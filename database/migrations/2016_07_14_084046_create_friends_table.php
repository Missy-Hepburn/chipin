<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Friend;

class CreateFriendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = with(new Friend())->getTable();
        Schema::create($table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('profile_id')->index();
            $table->integer('friend_profile_id')->index();
            $table->unique(array('profile_id', 'friend_profile_id'));
            $table->boolean('accepted')->default(false);
            $table->boolean('active')->default(true);
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
        $table = with(new Friend())->getTable();
        Schema::drop($table);
    }
}
