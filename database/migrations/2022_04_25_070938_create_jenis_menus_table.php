<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJenisMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //create table
        Schema::create('jenis_menus', function (Blueprint $table) {
            $table->bigIncrements('id_jenis_menu');
            $table->string('jenis_menu');
        });

        //insert data
        DB::table('jenis_menus')->insert(
            array(
                ['jenis_menu'=> 'Makanan'],
                ['jenis_menu'=> 'Minuman'],
                ['jenis_menu'=> 'Lain'],
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
        Schema::dropIfExists('jenis_menus');
    }
}
