<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixCategoryName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $category = \App\Models\Category::where('name', 'Intertainment')->first();
        if ($category) {
            $category->name = 'Entertainment';
            $category->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $category = \App\Models\Category::where('name', 'Entertainment')->first();
        if ($category) {
            $category->name = 'Intertainment';
            $category->save();
        }
    }
}
