<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAyatJurnalPenyesuaian extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('ayat_jurnal_penyesuaian', function (Blueprint $table) {
          $table->increments('id');
          $table->string('no_ajp');
          $table->string('info_ajp');
          $table->integer('so_id')->nullable();
          $table->integer('user_id');
          $table->date('date_ajp');
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
