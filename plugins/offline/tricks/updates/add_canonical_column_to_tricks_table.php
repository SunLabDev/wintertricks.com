<?php namespace OFFLINE\Tricks\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddCanonicalColumnToTricksTable extends Migration
{
    public function up()
    {
        Schema::table('offline_tricks_tricks', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('canonical')->nullable();
        });
    }

    public function down()
    {
        Schema::table('offline_tricks_tricks', function ($table) {
            $table->engine = 'InnoDB';
            $table->dropColumn('canonical');
        });
    }
}
