<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
   
    public function up()
    {
        //create table
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id_role');
            $table->string('nama_role');
        });

        //insert data
        DB::table('roles')->insert(
            array(
                ['nama_role'=> 'Owner'],
                ['nama_role'=> 'Waiter'],
                ['nama_role'=> 'Chef'],
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
        Schema::dropIfExists('roles');
    }
}
