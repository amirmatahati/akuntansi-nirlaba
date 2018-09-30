<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailAjap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_ajp', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ajp_id');
            $table->integer('id_akun');
            $table->integer('ajp_debet');
            $table->integer('ajp_kredit');
            $table->date('ajp_date');
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
        Schema::dropIfExists('detail_ajp');
    }
}
