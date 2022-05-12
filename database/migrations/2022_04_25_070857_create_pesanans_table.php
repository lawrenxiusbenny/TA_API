<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePesanansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->bigIncrements('id_pesanan');
            $table->bigInteger('id_status_pesanan')->default(1);
            $table->string('id_transaksi',100)->nullable();
            $table->bigInteger('id_menu');
            $table->bigInteger('id_customer');
            $table->integer('jumlah_pesanan');
            $table->double('sub_total');
            $table->string('catatan')>nullable();;
            $table->integer('status_selesai')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pesanans');
    }
}
