<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultKnowledgebaseCategoryTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::table('knowledgebasecategory')->insert(
            array(
                'name' => 'Default',
                'description' => 'This is a default table edit this in your admin side.',
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
