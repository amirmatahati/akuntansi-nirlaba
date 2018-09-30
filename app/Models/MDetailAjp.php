<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MDetailAjp extends Model
{
    protected $table		= 'detail_ajp';
	protected $fillable		= ['id', 'ajp_id', 'id_akun', 'ajp_debet', 'ajp_kredit', 'created_at', 'updated_at', 'ajp_date'];
	
	public function GetAJP()
	{
		return $this->belongsTo('App\Models\MAJP','ajp_id','id');
	}
	public function GetPerkiraan()
	{
		return $this->belongsTo('App\Models\MPerkiraan','id_akun','id');
	}
}
