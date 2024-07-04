<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGatewayTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
   public function up()
   {
         Schema::create('gateways', function (Blueprint $table) {
             $table->increments('id');
             $table->string('gateway');
             $table->unsignedInteger('enabled')->nullable();
             $table->text('api')->nullable();
             $table->text('private_key')->nullable();
             $table->text('mode')->nullable();
             $table->text('name')->nullable();
             $table->float('description')->nullable();
             $table->text('service_id')->nullable();
         });
   }
   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
         Schema::dropIfExists('gateways');
   }
}
