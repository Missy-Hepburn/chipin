<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Profile;

class CreatePrfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = with(new Profile)->getTable();
        Schema::create($table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->string('first_name');
            $table->string('last_name');
            $table->unique(array('first_name', 'last_name'));
            $table->char('nationality', 2);
            $table->char('country', 2);
            $table->date('birthday');
            $table->string('photo_id')->nullable();
            $table->string('address')->nullable();
            $table->string('occupation')->nullable();
            $table->string('income')->nullable();
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
        $table = with(new Profile)->getTable();
        Schema::drop($table);
    }
}
