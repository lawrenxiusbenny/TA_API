<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaftarKuponDiskonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daftar_kupon_diskons', function (Blueprint $table) {
            $table->bigIncrements('id_kupon_diskon');
            $table->string('nama_kupon');
            $table->integer('persentase_potongan');
            $table->integer('jumlah_point_tukar');
            $table->string('deskripsi_kupon');
            $table->integer('status_hapus')->default(0);
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
        Schema::dropIfExists('daftar_kupon_diskons');
    }
}
