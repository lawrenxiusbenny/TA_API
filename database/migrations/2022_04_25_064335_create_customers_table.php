<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id_customer');
            $table->string('id_royalty_point',100)->nullable();
            $table->string('nama_customer');
            $table->string('email_customer')->unique();
            $table->string('telepon_customer');
            $table->string('password_customer');
            $table->date('tanggal_lahir_customer');
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
        Schema::dropIfExists('customers');
    }
}
