<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJurnalDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurnal_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jurnal_id');
            $table->integer('id_perkiraan');
            $table->integer('jumlah_debet');
            $table->integer('jumlah_kredit');
            $table->date('date_jurnal');
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
        Schema::dropIfExists('jurnal_detail');
    }
}
