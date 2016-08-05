<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameProfilePhotoToImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = with(new \App\Models\Profile())->getTable();
        Schema::table($table, function (Blueprint $table) {
            $table->renameColumn('photo_id', 'image_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table = with(new \App\Models\Profile())->getTable();
        Schema::table($table, function (Blueprint $table) {
            $table->renameColumn('image_id', 'photo_id');
        });
    }
}
