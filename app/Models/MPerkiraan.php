<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MPerkiraan extends Model
{
    protected $table		= 'perkiraan';
	protected $fillable		= ['id', 'kode_akun', 'perkiraan_akun', 'created_at', 'updated_at', 'klasifikasi', 'sub_klasifikasi'];

	public function GetJurnaDetail()
	{
		return $this->hasMany('App\Models\MDetailJurnal','id_perkiraan','id');
	}
}
