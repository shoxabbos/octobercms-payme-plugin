<?php
use RainLab\User\Models\User;

Event::listen('shohabbos.payme.existsAccount', function ($accounts, &$result, &$message) {
    // find order or account
	$result = User::find($accounts['user_id']);
});

// $result param by default is true
Event::listen('shohabbos.payme.checkAmount', function ($accounts, $amount, &$result, &$message) {
	
});

Event::listen('shohabbos.payme.performTransaction', function ($transaction, &$result, &$message) {
    // check order as paid or fill user balance
	$user = User::find($transaction->owner_id);
	$user->balance += ($transaction->amount / 100);
	$result = $user->save();
});

Event::listen('shohabbos.payme.cancelTransaction', function ($transaction, &$result, &$message) {
    // check order as paid or fill user balance
	$user = User::find($transaction->owner_id);
	$user->balance -= ($transaction->amount / 100);
	$result = $user->save();
});