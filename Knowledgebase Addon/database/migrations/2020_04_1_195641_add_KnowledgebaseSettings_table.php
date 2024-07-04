<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKnowledgebaseSettingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('knowledgebasesettings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category')->default(1);
            $table->integer('author')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('knowledgebasesettings');
    }
}
