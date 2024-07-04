<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->string('img')->default('/uploads/default.png');
            $table->float('priority');
            $table->float('price');
            $table->text('description');
            $table->integer('category');
            $table->integer('egg_id');
            $table->integer('visible');
            $table->text('node_id');
            $table->integer('memory');
            $table->integer('swap');
            $table->integer('cpu');
            $table->integer('io');
            $table->integer('disk');
            $table->integer('database_limit');
            $table->integer('allocation_limit');
            $table->integer('backup_limit');
            $table->string('docker_image');
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
        Schema::dropIfExists('products');
    }
}
