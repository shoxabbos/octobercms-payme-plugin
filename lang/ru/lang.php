<?php return [
    'plugin' => [
        'name' => 'Payme',
        'description' => 'Payme payment system for October'
    ],

    'settings' => [
        'handler' => 'Обработчик запросов PAYME',
    	'can_cancel' => 'Отмена транзакции',
    	'can_cancel_desc' => 'Включить возможность отменять транзакции',
    	'type' => 'Тип счета',
    	'type_single' => 'Одноразовый счет',
    	'type_multi' => 'Накопительный счет',
    	'label' => 'Настройки',
    	'description' => 'Управление настройками PAYME',
    	'form_keys_tab' => 'Ключи',
    	'form_keys_main' => 'Основные',
    	'min_amount' => 'Минимальная сумма (UZS)',
    	'min_amount_desc' => 'Наименьшая допустимая сумма платежа в узбекских сумах',
    	'max_amount' => 'Максимальная сумма (UZS)',
    	'max_amount_desc' => 'Наибольшая допустимая сумма платежа в узбекских сумах',
    	'field_name' => 'Имя',
    	'field_name_desc' => 'Служебное имя, под которым значение реквизита платежа сохраняется на стороне Paycom',
    	'field_reg' => 'Регулярное выражение',
    	'field_reg_desc' => 'Регулярное выражение, с помощью которого проверяется значение реквизита платежа на стороне Paycom',
    ],

    'transactions' => [
    	'title' => 'Транзакции',
    	'description' => 'Список транзакций'
    ],


];