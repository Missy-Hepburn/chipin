<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendshipsTable extends Migration
{
    public function up()
    {
        Schema::create('friendships', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('sender');
            $table->morphs('recipient');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });

        Schema::dropIfExists('friends');
    }

    public function down()
    {
        Schema::dropIfExists('friendships');

        Schema::create('friends', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('profile_id')->index();
            $table->integer('friend_profile_id')->index();
            $table->unique(array('profile_id', 'friend_profile_id'));
            $table->boolean('accepted')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }
}
