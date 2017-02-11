<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddUnsupportedLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unsupported_location_cache', function (Blueprint $table) {
            $table->increments('id');
            $table->double('lat');
            $table->double('lng');
            $table->json('locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('unsupported_location_cache');
    }
}
