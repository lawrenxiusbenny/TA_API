<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusKuponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //create table
        Schema::create('status_kupons', function (Blueprint $table) {
            $table->bigIncrements('id_status_kupon');
            $table->string('nama_status');
        });

        //insert data
        DB::table('status_kupons')->insert(
            array(
                ['nama_status'=> 'Belum Dipakai'],
                ['nama_status'=> 'Sudah Dipakai'],
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
        Schema::dropIfExists('status_kupons');
    }
}
