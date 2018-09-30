<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArusKas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arus_kas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no_bukti');
            $table->date('tgl_kas');
            $table->string('sumber_dana');
            $table->text('keterangan');
            $table->integer('jumlah_kas');
            $table->string('tipe_kas');
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
        Schema::dropIfExists('arus_kas');
    }
}
