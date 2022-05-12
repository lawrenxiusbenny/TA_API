<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id_menu');
            $table->bigInteger('id_status_menu')->default(1);
            $table->bigInteger('id_jenis_menu');
            $table->string('nama_menu');
            $table->double('harga_menu');
            $table->string('deskripsi_menu');
            $table->string('gambar_menu');
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
        Schema::dropIfExists('menus');
    }
}
