<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultBillingSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::table('billing')->insert(
          array(
              'currency' => 'euro',
              'currency_code' => 'EUR',
              'use_categories' => '1',
              'categories_img' => '1',
              'categories_img_rounded' => '1',
              'categories_img_width' => '100px',
              'categories_img_height' => '100px',
              'use_products' => '1',
              'products_img' => '1',
              'products_img_rounded' => '1',
              'products_img_width' => '100px',
              'products_img_height' => '100px',
              'use_deploy' => '1',
              'tos' => 'This is the example TOS, please change this.',
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
      DB::table('billing')->where('id', '=', '1')->delete();
    }
}
