<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MJurnal extends Model
{
    protected $table		= 'jurnal_umum';
	protected $fillable		= ['id', 'no_jurnal', 'info', 'so_id', 'user_id', 'created_at', 'updated_at', 'date_added'];
}
