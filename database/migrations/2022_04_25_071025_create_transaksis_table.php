<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->string('id_transaksi', 100);
            $table->bigInteger('id_customer');
            $table->bigInteger('id_karyawan')->nullable();
            $table->bigInteger('id_kupon_customer')->nullable();
            $table->double('total_harga');
            $table->string('metode_pembayaran');  
            $table->string('nama_metode')->nullable();  
            $table->string('status_transaksi')->default("Belum Lunas");
            $table->timestamps();
            $table->primary('id_transaksi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksis');
    }
}
