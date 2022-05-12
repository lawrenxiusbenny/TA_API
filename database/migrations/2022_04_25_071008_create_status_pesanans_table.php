<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusPesanansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //create table
        Schema::create('status_pesanans', function (Blueprint $table) {
            $table->bigIncrements('id_status_pesanan');
            $table->string('nama_status');
        });

        //insert data
        DB::table('status_pesanans')->insert(
            array(
                ['nama_status'=> 'Belum Disajikan'],
                ['nama_status'=> 'Sudah Disajikan'],
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
        Schema::dropIfExists('status_pesanans');
    }
}
