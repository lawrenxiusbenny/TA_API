<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaftarKuponCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daftar_kupon_customers', function (Blueprint $table) {
            $table->bigIncrements('id_kupon_customer');
            $table->bigInteger('id_customer');
            $table->bigInteger('id_status_kupon')->default(1);
            $table->bigInteger('id_kupon_diskon');
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
        Schema::dropIfExists('daftar_kupon_customers');
    }
}
