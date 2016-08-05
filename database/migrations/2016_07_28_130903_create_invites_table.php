<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Invite;

class CreateInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = with(new Invite())->getTable();
        Schema::create($table,  function(Blueprint $table) {
            $table->increments('id');
            $table->integer('goal_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('reference_id')->unsigned();
            $table->enum('status', Invite::getStatuses())->default(Invite::STATUS_PENDING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table = with(new Invite())->getTable();
        Schema::drop($table);
    }
}
