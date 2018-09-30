<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MAlurKas extends Model
{
    protected $table	= 'arus_kas';
	protected $fillable	= ['id', 'no_bukti', 'tgl_kas', 'sumber_dana', 'keterangan', 'jumlah_kas', 'tipe_kas', 'created_at', 'updated_at'];
}
