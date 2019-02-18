<?php namespace Shohabbos\Payme\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosPaymeTransactions extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_payme_transactions', function($table)
        {
            $table->string('transaction', 32)->change();
            $table->smallInteger('state')->nullable()->unsigned(false)->default(null)->change();
            $table->bigInteger('payme_time')->nullable()->unsigned(false)->default(0)->change();
            $table->bigInteger('cancel_time')->nullable()->unsigned(false)->default(0)->change();
            $table->bigInteger('create_time')->nullable()->unsigned(false)->default(0)->change();
            $table->bigInteger('perform_time')->nullable()->unsigned(false)->default(0)->change();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_payme_transactions', function($table)
        {
            $table->string('transaction', 191)->change();
            $table->string('state', 191)->nullable()->unsigned(false)->default(null)->change();
            $table->string('payme_time', 191)->nullable()->unsigned(false)->default(null)->change();
            $table->string('cancel_time', 191)->nullable()->unsigned(false)->default(null)->change();
            $table->string('create_time', 191)->nullable()->unsigned(false)->default(null)->change();
            $table->string('perform_time', 191)->nullable()->unsigned(false)->default(null)->change();
        });
    }
}
