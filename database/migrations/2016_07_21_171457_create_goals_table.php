<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \App\Models\Goal;

class CreateGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = with(new Goal())->getTable();
        Schema::create($table,  function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('name');
            $table->integer('image_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned();
            $table->dateTime('start_date');
            $table->dateTime('due_date');
            $table->decimal('amount', 12, 2)->unsigned();
            $table->string('wallet');
            $table->enum('type', Goal::getTypes())->default(Goal::DEFAULT_TYPE);
            $table->enum('timer', Goal::getTimers())->default(Goal::DEFAULT_TIMER);
            $table->enum('status', Goal::getStatuses())->default(Goal::STATUS_ACTIVE);
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
        $table = with(new Goal())->getTable();
        Schema::drop($table);
    }
}
