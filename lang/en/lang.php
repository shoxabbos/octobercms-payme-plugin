<?php 

return [
    'plugin' => [
        'name' => 'Payme',
        'description' => 'Integration with Payme payment system'
    ],

    'settings' => [
        'timeout' => 'Timeout (In seconds)',
        'timeout_desc' => 'Ignore payment after the specified time',
        'code' => 'Events (init.php)',
        'code_desc' => 'You can expand the functionality of the plug-in using events or customize it for you.',
        'accounts' => 'Payment details (order_id, phone)',
        'handler' => 'Handler (web-hook)',
    	'can_cancel' => 'Cancel transactions',
    	'can_cancel_desc' => 'Enable the ability to undo transactions',
    	'type' => 'Type of account',
    	'type_single' => 'One-time account',
    	'type_multi' => 'Savings account',
    	'label' => 'Settings',
    	'description' => 'Manage settings PAYME',
    	'tab_keys' => 'Keys',
        'tab_main' => 'Main',
    	'tab_code' => 'Business logic',
    	'min_amount' => 'Minimal amount (UZS)',
    	'min_amount_desc' => 'The minimum allowable amount of payment in Uzbek sums',
    	'max_amount' => 'Maximal amount (UZS)',
    	'max_amount_desc' => 'The maximum allowable amount of payment in Uzbek sums',
    	'field_name' => 'Name',
    	'field_name_desc' => 'A business name under which the value of the payment requisition is stored on the Paycom side',
    	'field_reg' => 'Regular expression',
    	'field_reg_desc' => 'A regular expression that verifies the value of the payment requisite on the Paycom side',
    ],

    'transactions' => [
    	'title' => 'Transactions',
    	'description' => 'List of transactions'
    ],
];