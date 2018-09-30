<?php

namespace App\Http\Controllers\jurnal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Classes\AutoNumber;

class AlurKasController extends Controller
{
    public function InputPenerimaan()
	{
		$table					= "arus_kas";
        $primary				= "no_bukti";
        $prefix					= "KM";
        $nojurnal				= Autonumber::autonumber($table,$primary,$prefix);
		
		return view('finance.transaksi.penerimaan', compact('nojurnal'));
	}
}
