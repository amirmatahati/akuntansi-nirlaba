<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MAJP extends Model
{
    protected $table		= 'ayat_jurnal_penyesuaian';
	protected $fillable		= ['id', 'no_ajp', 'info_ajp', 'so_id', 'user_id', 'created_at', 'updated_at', 'date_ajp'];
}
