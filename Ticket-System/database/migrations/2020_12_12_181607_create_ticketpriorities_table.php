<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketPrioritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_priorities', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->timestamps();
        });
		
        DB::table('ticket_priorities')->insert(
            array(
                'name' => 'Low',
            )
        );
        DB::table('ticket_priorities')->insert(
            array(
                'name' => 'Medium',
            )
        );
        DB::table('ticket_priorities')->insert(
            array(
                'name' => 'High',
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_priorities');
    }
}
