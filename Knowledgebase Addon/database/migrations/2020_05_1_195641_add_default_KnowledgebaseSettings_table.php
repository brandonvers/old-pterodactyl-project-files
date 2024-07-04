<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultKnowledgebaseSettingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::table('knowledgebasesettings')->insert(
            array(
                'category' => 1,
                'author' => 1,
            )
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
      DB::table('knowledgebasesettings')->where('id', '=', '1')->delete();
    }
}
