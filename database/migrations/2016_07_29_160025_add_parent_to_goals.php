<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentToGoals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = with(new \App\Models\Goal())->getTable();
        Schema::table($table,  function(Blueprint $table) {
            $table->integer('parent_id')->after('id')->nullable()->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table = with(new \App\Models\Goal())->getTable();
        Schema::table($table,  function(Blueprint $table) {
            $table->dropColumn('parent_id');
        });
    }
}
