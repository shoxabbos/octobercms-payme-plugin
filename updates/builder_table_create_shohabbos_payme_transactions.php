<?php namespace Shohabbos\Payme\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateShohabbosPaymeTransactions extends Migration
{
    public function up()
    {
        Schema::create('shohabbos_payme_transactions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('transaction')->nullable();
            $table->string('code')->nullable();
            $table->string('state')->nullable();
            $table->string('owner_id')->nullable();
            $table->string('amount')->nullable();
            $table->string('reason')->nullable();
            $table->string('payme_time')->nullable();
            $table->string('cancel_time')->nullable();
            $table->string('create_time')->nullable();
            $table->string('perform_time')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_payme_transactions');
    }
}
