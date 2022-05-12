<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //create table
        Schema::create('status_menus', function (Blueprint $table) {
            $table->bigIncrements('id_status_menu');
            $table->string('nama_status');
        });

        //insert data
        DB::table('status_menus')->insert(
            array(
                ['nama_status'=> 'Tersedia'],
                ['nama_status'=> 'Tidak Tersedia'],
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
        Schema::dropIfExists('status_menus');
    }
}
