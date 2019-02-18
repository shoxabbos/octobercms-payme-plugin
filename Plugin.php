<?php namespace Shohabbos\Payme;

use Backend;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails() {
    	return [
    		'name' => 'shohabbos.payme::lang.plugin.name',
    		'description' => 'shohabbos.payme::lang.plugin.description',
    		'author' => 'Shohabbos Olimjonov',
            'icon' => 'oc-icon-paypal',
            'homepage' => 'https://itmaker.uz',
    	];
    }

    public function registerReportWidgets()
    {
        return [
            'Shohabbos\Payme\ReportWidgets\Payment' => [
                'label' => 'Transactions of Payme',
                'context' => 'dashboard'
            ],
        ];
    }

    public function registerSettings() {
	    return [
	    	'transactions' => [
                'label'       => 'shohabbos.payme::lang.transactions.title',
                'description' => 'shohabbos.payme::lang.transactions.description',
                'icon'        => 'icon-list-alt',
                'url'         => Backend::url('shohabbos/payme/transactions'),
                'order'       => 500,
                'category'    => 'shohabbos.payme::lang.plugin.name',
                'permissions' => ['shohabbos.payme.manage_messages']
            ],
	        'settings' => [
	        	'category'    => 'shohabbos.payme::lang.plugin.name',
	            'label'       => 'shohabbos.payme::lang.settings.label',
	            'description' => 'shohabbos.payme::lang.settings.description',
	            'icon'        => 'icon-cog',
	            'class'       => 'Shohabbos\Payme\Models\Settings',
	            'order'       => 501,
	            'keywords'    => 'payme paymets',
	        ]
	    ];
	}

}
