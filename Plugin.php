<?php namespace Shohabbos\Payme;

use Backend;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents() {

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
