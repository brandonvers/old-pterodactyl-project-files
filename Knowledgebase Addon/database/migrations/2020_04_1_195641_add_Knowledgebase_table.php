<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKnowledgebaseTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('knowledgebase', function (Blueprint $table) {
            $table->increments('id');
            $table->string('author', 50000000);
            $table->string('subject', 50000000);
            $table->string('information', 50000000);
            $table->integer('category');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('knowledgebase');
    }
}
