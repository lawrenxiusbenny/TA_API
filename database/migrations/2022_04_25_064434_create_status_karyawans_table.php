<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusKaryawansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //create table
        Schema::create('status_karyawans', function (Blueprint $table) {
            $table->bigIncrements('id_status_karyawan');
            $table->string('nama_status');
        });

        //insert data
        DB::table('status_karyawans')->insert(
            array(
                ['nama_status'=> 'aktif'],
                ['nama_status'=> 'tidak aktif'],
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
        Schema::dropIfExists('status_karyawans');
    }
}
