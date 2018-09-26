<?php namespace Shohabbos\Payme\Controllers;

use Illuminate\Routing\Controller;
use Shohabbos\Payme\Models\Settings;
use Shohabbos\Payme\Classes\JsonRpcServer;
use Shohabbos\Payme\Classes\JsonRpc;

class Payme extends Controller
{
    const JSON_RPC_VERSION = '2.0';


    public function CheckPerformTransaction($params) {
        return $params;
    }










    public function index() {
        $json = file_get_contents('php://input');
        $response = [];
        $response['jsonrpc'] = self::JSON_RPC_VERSION;
        $request =  json_decode($json, true, 32);

        try {
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
            $response['id'] = null;
        }

        return response()->json($response);
    }


}
