<?php namespace Shohabbos\Payme\Controllers;

use Event;
use Illuminate\Routing\Controller;
use Shohabbos\Payme\Models\Settings;
use Shohabbos\Payme\Models\Transaction;
use Shohabbos\Payme\Classes\JsonRpcServer;
use Shohabbos\Payme\Classes\JsonRpc;

class Payme extends Controller
{
    const JSON_RPC_VERSION = '2.0';



    private function CheckTransaction($params) {
        // validate params
        $this->validateParams(['id'], $params);

        
        // find transaction by payme id
        $transaction = Transaction::where('transaction', $params['id'])->first();
        if (!$transaction) {
            throw new \Exception( 'Transaction not found', -31003);
        }


        return [
            'create_time' => $transaction->create_time,
            'perform_time' => $transaction->perform_time,
            'cancel_time' => $transaction->cancel_time,
            'transaction' => $transaction->id,
            'state' => $transaction->state,
            'reason' => $transaction->reason
        ];
    }

    private function CancelTransaction($params) {
        // validate params
        $this->validateParams(['id', 'reason'], $params);


        // fetch vars
        $id       = $params['id'];
        $reason   = $params['reason'];


        // find transaction by payme id
        $transaction = Transaction::where('transaction', $id)->first();
        if (!$transaction) {
            throw new \Exception( 'Transaction not found', -31003);
        }

        // check state
        if ($transaction->state == 1) {
            $transaction->state = -1;
            $transaction->reason = $reason;
            $transaction->cancel_time = time() * 1000;
            $transaction->save();

            return [
                'cancel_time' => (int) $transaction->cancel_time,
                'state' => (int) $transaction->state,
                'transaction' => (string) $transaction->id
            ];
        } elseif ($transaction->state != 2) {
            return [
                'cancel_time' => (int) $transaction->cancel_time,
                'state' => (int) $transaction->state,
                'transaction' => (string) $transaction->id
            ];
        }


        // check settings
        if (!Settings::get('can_cancel', 0)) {
            throw new \Exception( 'Can not cancel transaction', -31007);
        }

        // take away balance or update status order
        $result = false;
        $message = 'Can not cancel transaction';
        Event::fire('shohabbos.payme.cancelTransaction', [$transaction, &$result, &$message]);

        if (!$result) {
            throw new \Exception($message, -31007);
        }

        $transaction->state = -2;
        $transaction->reason = $reason;
        $transaction->cancel_time = time() * 1000;
        $transaction->save();

        return [
            'cancel_time' => (int) $transaction->cancel_time,
            'state' => (int) $transaction->state,
            'transaction' => (string) $transaction->id
        ];
    }

    private function PerformTransaction($params) {
        // validate params
        $this->validateParams(['id'], $params);
        

        // fetch vars
        $id       = $params['id'];


        // find transaction by payme id
        $transaction = Transaction::where('transaction', $id)->first();
        if (!$transaction) {
            throw new \Exception( 'Transaction not found', -31003);
        }


        // check state
        if ($transaction->state != 1) {
            if ($transaction->state != 2) {
                throw new \Exception( 'Invalid transaction state', -31008);
            }

            return [
                'state' => $transaction->state,
                'perform_time' => $transaction->perform_time,
                'transaction' => $transaction->id
            ];
        }


        // check timeout
        $currentTime = time() * 1000;
        $timeoutTime = (Settings::get('timeout') * 1000) + $transaction->create_time;
        if ($timeoutTime < $currentTime) {
            $transaction->state = -1;
            $transaction->reason = 4;
            $transaction->save();

            throw new \Exception( 'Timeout', -31008);
        }

        // fill balance or update status order
        $result = false;
        $message = 'Unknown error';
        Event::fire('shohabbos.payme.performTransaction', [$transaction, &$result, &$message]);

        if (!$result) {
            throw new \Exception($message, -31008);
        }

        $transaction->perform_time = time() * 1000;
        $transaction->state = 2;
        $transaction->save();

        return [
            'state' => (int) $transaction->state,
            'perform_time' => (int) $transaction->perform_time,
            'transaction' => (string) $transaction->id
        ];
    }


    private function CreateTransaction($params) {
        // validate params
        $this->validateParams(['id', 'time', 'amount', 'account'], $params);


        // fetch vars
        $id       = $params['id'];
        $time     = $params['time'];
        $amount   = $params['amount'];
        $accounts = $this->getValidAccounts($params['account']);

        // find transaction by payme id
        $transaction = Transaction::where('transaction', $id)->first();
        if ($transaction) {

            // check state
            if ($transaction->state == 1) {
                
                // check timeout
                $currentTime = time() * 1000;
                $timeoutTime = (Settings::get('timeout') * 1000) + $transaction->create_time;
                if ($timeoutTime < $currentTime) {
                    $transaction->state = -1;
                    $transaction->reason = 4;
                    $transaction->save();

                    throw new \Exception( 'Timeout', -31008);
                }

                return [
                    'state' => (int) $transaction->state,
                    'create_time' => (int) $transaction->create_time,
                    'transaction' => (string) $transaction->id
                ];   
            } else {
                throw new \Exception( 'Invalid state', -31008);
            }
        }


        // check for Repeated payment
        if (Settings::get('type', 'single') == 'single') {
            $transaction = Transaction::where('owner_id', implode(',', $accounts))->first();
            if ($transaction) {
                throw new \Exception('Waiting for payment', -31050);   
            }
        }


        // transaction not found
        // check perform transaction 
        $this->CheckPerformTransaction(['amount' => $amount, 'account' => $accounts]);


        // create new transaction
        $transaction = new Transaction();
        $transaction->state = 1;
        $transaction->transaction = $id;
        $transaction->payme_time = $time;
        $transaction->amount = $amount;
        $transaction->create_time = time() * 1000;
        $transaction->owner_id = implode('-', $accounts);
        $transaction->save();

        return [
            'state' => (int) $transaction->state,
            'create_time' => (int) $transaction->create_time,
            'transaction' => (string) $transaction->id
        ];
    }


    private function CheckPerformTransaction($params) {
        // validate params
        $this->validateParams(['account', 'amount'], $params);

        // fetch vars
        $accounts = $this->getValidAccounts($params['account']);
        $amount = $params['amount'];


        // Is exists order or account
        $result = false;
        $message = 'Account or order not found';
        Event::fire('shohabbos.payme.existsAccount', [$accounts, &$result, &$message]);

        if (!$result) {
            throw new \Exception($message, -31050);
        }


        // Validate state of account or order
        $result = true;
        $message = 'Account or order state is not true';
        Event::fire('shohabbos.payme.stateAccount', [$accounts, &$result, &$message]);

        if (!$result) {
            throw new \Exception($message, -31050);
        }


        // validate amount
        // TO DO: get currency params from backend
        $amount /= 100;
        $result = $amount >= Settings::get('min_amount', 1) && $amount <= Settings::get('max_amount', 100);
        $message = 'Invalid amount';
        Event::fire('shohabbos.payme.checkAmount', [$accounts, $amount, &$result, &$message]);

        if (!$result) {
            throw new \Exception($message, -31001);
        }

        
        return ['allow' => true];
    }





    // validate params
    private function validateParams($params, $data) {
        foreach ($params as $key => $value) {
            if (!isset($data[$value])) {
                throw new \Exception( 'Invalid Request', -32600);
            }
        }
    }

    private function getValidAccounts($accounts) {
        $res = [];

        foreach (Settings::get('accounts', []) as $key => $value) {
            if (isset($accounts[$value['name']])) {
                $res[$value['name']] = $accounts[$value['name']];
            }
        }

        return $res;
    }

    public function index(\Illuminate\Http\Request $mainRequest) {
        // get vars 
        $mode = Settings::get('mode', 'test');
        $key = Settings::get($mode);
        $login = Settings::get("login");
        $header = "Basic ".base64_encode($login.":".$key);

        $json = file_get_contents('php://input');
        $response = [];
        $response['jsonrpc'] = self::JSON_RPC_VERSION;
        $request =  json_decode($json, true, 32);

        try {
            if ($header != $mainRequest->header('Authorization')) {
                throw new \Exception( 'Access denied', -32504);
            }

            if ( 
                !isset($request['jsonrpc'] ) || 
                !isset($request['method']) || 
                !isset($request['params']) || 
                !isset($request['id']) 
            ) {
                throw new \Exception( 'Invalid Request', -32600);
            }

            if (!method_exists($this, $request['method'])) {
                throw new \Exception( 'Method not found', -32601);
            }

            if (is_null($request['params'])) {
                $request['params'] = [];
            }

            $response['result'] = $this->{$request['method']}($request['params']);
            $response['id'] = $request['id'];
        } catch ( \Exception $ex ) {
            $response['error'] = new \stdClass();
            $response['error']->code = $ex->getCode();
            $response['error']->message = $ex->getMessage();
            $response['id'] = isset($request['id']) ? $request['id'] : null;
        }

        return response()->json($response);
    }


}
