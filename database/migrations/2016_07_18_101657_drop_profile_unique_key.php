<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \App\Models\Profile;

class DropProfileUniqueKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = with(new Profile)->getTable();
        Schema::table($table, function(Blueprint $table)
            {
                $table->dropUnique('profiles_first_name_last_name_unique');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table = with(new Profile)->getTable();
        Schema::table($table, function(Blueprint $table)
            {
                $table->unique(array('first_name', 'last_name'));
            }
        );
    }
}
