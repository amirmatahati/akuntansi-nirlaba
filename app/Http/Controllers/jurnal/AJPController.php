<?php

namespace App\Http\Controllers\jurnal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

use App\Models\MPerkiraan;
use App\Models\package;
use App\Models\MSallesOrder;
use App\Models\MJurnal;
use App\Models\MDetailJurnal;
use App\Models\MAJP;
use App\Models\MDetailAjp;

use App\Classes\AutoNumber;
use App\Classes\JurnalClass;
use App\Classes\PerkiraanClass;

use DB;
use Carbon;

class AJPController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    public function createAJP(Request $request)
	{
		$table					= "ayat_jurnal_penyesuaian";
        $primary				= "no_ajp";
        $prefix					= "AJP";
        $nojurnal				= Autonumber::autonumber($table,$primary,$prefix);


		return response()->json($nojurnal);
		return view('finance.jurnal.add', compact('so', 'nojurnal'));
	}
	public function storeAJP(Request $request)
	{
		$ajp					= new MAJP;

		$ajp->no_ajp			= $request->no_jurnal;
		$ajp->info_ajp			= $request->info;
		$ajp->so_id				= 0;
		$ajp->user_id			= \Auth::user()->id;
		$ajp->date_ajp			= $request->date_added;

		$ajp->save();

		$id_ajp					= $ajp->id;
		$ajpdates				= $ajp->date_ajp;
		$perkiraan_id			= $request->perkiraan_id;

		foreach ($request->input('data') as $key => $v){
			$detailjurnal []  = [
						   'ajp_id' => $id_ajp,
						   'id_akun' => $v['id'],
						   'ajp_debet'		=> $v['jumlah_debet'],
						   'ajp_kredit' => $v['jumlah_kredit'],
						   'ajp_date' => $ajpdates
			  ];
	   }
	   MDetailAjp::insert($detailjurnal);

	   return response()->json($detailjurnal);

	}
	public function AJP()
	{
		$ajp						= MDetailAjp::join('ayat_jurnal_penyesuaian', 'detail_ajp.ajp_id', 'ayat_jurnal_penyesuaian.id')
									->join('perkiraan', 'detail_ajp.id_akun', 'perkiraan.id')
									->select('detail_ajp.id', 'detail_ajp.ajp_debet', 'detail_ajp.ajp_kredit',
									'ayat_jurnal_penyesuaian.id as id_ajp','ayat_jurnal_penyesuaian.date_ajp','ayat_jurnal_penyesuaian.no_ajp',
									'perkiraan.perkiraan_akun')
									->paginate(10);

		$data	= [
			'data'			=> $ajp,
			'pagination' => [

				'total' => $ajp->total(),

				'per_page' => $ajp->perPage(),

				'current_page' => $ajp->currentPage(),

				'last_page' => $ajp->lastPage(),

				'from' => $ajp->firstItem(),

				'to' => $ajp->lastItem()

			]
		];
		return response()->json($data);
		return view('finance.jurnal.ajp', compact('ajp'));
	}
}
