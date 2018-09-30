<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFildArusKas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('arus_kas', function (Blueprint $table) {
            $table->string('no_bukti');
            $table->date('tgl_kas');
            $table->string('sumber_dana');
            $table->text('keterangan');
            $table->integer('jumlah_kas');
            $table->string('tipe_kas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('arus_kas', function (Blueprint $table) {
            $table->dropColumn('no_bukti');
            $table->dropColumn('tgl_kas');
            $table->dropColumn('sumber_dana');
            $table->dropColumn('keterangan');
            $table->dropColumn('jumlah_kas');
            $table->dropColumn('tipe_kas');
        });
    }
}
