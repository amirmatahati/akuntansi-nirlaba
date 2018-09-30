<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MDetailJurnal extends Model
{
    protected $table	= 'jurnal_detail';
	protected $fillable	= ['id', 'jurnal_id', 'id_perkiraan', 'jumlah_debet', 'jumlah_kredit', 'created_at', 'updated_at', 'date_jurnal'];
	
	public function GetJurnal()
	{
		return $this->belongsTo('App\Models\MJurnal','jurnal_id','id');
	}
	public function GetPerkiraan()
	{
		return $this->belongsTo('App\Models\MPerkiraan','id_perkiraan','id');
	}
}
