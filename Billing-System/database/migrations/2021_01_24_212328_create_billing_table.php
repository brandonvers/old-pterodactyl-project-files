<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing', function (Blueprint $table) {
            $table->increments('id');
            $table->text('currency');
            $table->text('currency_code');
            $table->integer('use_categories');
            $table->integer('categories_img');
            $table->integer('categories_img_rounded');
            $table->text('categories_img_width');
            $table->text('categories_img_height');
            $table->integer('use_products');
            $table->integer('products_img');
            $table->integer('products_img_rounded');
            $table->text('products_img_width');
            $table->text('products_img_height');
            $table->integer('use_deploy');
            $table->text('tos');
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
        Schema::dropIfExists('billing');
    }
}
