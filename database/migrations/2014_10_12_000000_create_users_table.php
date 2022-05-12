<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id_karyawan');
            $table->bigInteger('id_role');
            $table->bigInteger('id_status_karyawan')->default(1);
            $table->string('nama_karyawan');
            $table->string('jenis_kelamin_karyawan');
            $table->string('telepon_karyawan');
            $table->string('email_karyawan')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('tanggal_bergabung');
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
        Schema::dropIfExists('users');
    }
}
