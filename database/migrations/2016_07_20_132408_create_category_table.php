<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = with(new \App\Models\Category())->getTable();
        Schema::create($table,  function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('image_id')->unsigned()->nullable();
            $table->timestamps();
        });

        /* Predefined categories */
        $list = [
            'Beauty\wellness',
            'Sports',
            'Education',
            'Retirement',
            'Emergency',
            'Wedding',
            'Clothes',
            'Gifts',
            'Vacation',
            'Intertainment',
            'Car',
            'House',
        ];

        foreach ($list as $item) {
            App\Models\Category::create(['name' => $item]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table = with(new \App\Models\Category())->getTable();
        Schema::drop($table);
    }
}
