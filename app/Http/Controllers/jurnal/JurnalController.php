<?php

namespace App\Http\Controllers\jurnal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Collection;

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
Use Redirect;

class JurnalController extends Controller
{
	public function __construct()
    {
		$this->middleware('auth');
		$this->jurnal					= new JurnalClass;
    }
	public function index(Request $request)
	{
		if(isset($request->q)){
			$q									= date('Y-m-d', strtotime($request->q));
		}else{
			$q = 0;
		}
		$detail							= $this->jurnal->JurnalDetail($q);
		$jurnal							= MDetailJurnal::all();
		$perkiraan					= MPerkiraan::pluck('perkiraan_akun', 'id');
		$sumdebet						= $detail->sum('jumlah_debet');
		$sumkredit						= $detail->sum('jumlah_kredit');
		$data	= [
			'data'			=> $detail,
			'sumdebet'	=> $sumdebet,
			'sumkredit'	=>$sumkredit,
			'perkiraan'	=> $perkiraan,
			'pagination' => [

				'total' => $detail->total(),

				'per_page' => $detail->perPage(),

				'current_page' => $detail->currentPage(),

				'last_page' => $detail->lastPage(),

				'from' => $detail->firstItem(),

				'to' => $detail->lastItem()

			]
		];
		return response()->json($data);

		return view('finance.jurnal.index', compact('detail', 'sumdebet', 'sumkredit'));
	}


    public function create(Request $request)
	{
		$table					= "jurnal_umum";
        $primary				= "no_jurnal";
        $prefix					= "JU";
        $nojurnal				= Autonumber::autonumber($table,$primary,$prefix);

		$so						= MSallesOrder::pluck('no_so', 'id');
		return response()->json($nojurnal);
		return view('finance.jurnal.add', compact('so', 'nojurnal'));
	}
	public function store(Request $request)
	{
		$jurnal					= new MJurnal;

		$jurnal->no_jurnal		= $request->no_jurnal;
		$jurnal->info			= $request->info;
		$jurnal->so_id			= 0;
		$jurnal->user_id		= \Auth::user()->id;
		$jurnal->date_added		= $request->date_added;

		$jurnal->save();

		$id_jurnal				= $jurnal->id;
		$tgl_jurnal				= $jurnal->date_added;

		foreach ($request->input('data') as $key => $v){
			$detailjurnal []  = [
						   'jurnal_id' => $id_jurnal,
						   'id_perkiraan' => $v['id'],
						   'jumlah_debet'		=> $v['jumlah_debet'],
						   'jumlah_kredit' => $v['jumlah_kredit'],
						   'date_jurnal' => $tgl_jurnal
			  ];
	   }
	   MDetailJurnal::insert($detailjurnal);

	   return response()->json($detailjurnal);

		$request->session()->flash('alert-success', 'was successful update!');
		return redirect()->route('list.jurnal');
	}
	public function PackSuggest(Request $request)
	{
		$query 			= $request->get('term','');

		$queries 				= MPerkiraan::where('kode_akun', 'LIKE', '%'.$query.'%')
								->orWhere('perkiraan_akun', 'LIKE', '%'.$query.'%')
								->take(5)->get();

        $data			= array();
        foreach ($queries as $pack) {
                $data[]	= array('id'=>$pack->id,'type'=>$pack->kode_akun,'package'=>$pack->perkiraan_akun);
        }
        if(count($data))
             return $data;
        else
            return ['id'=>'','type'=>'','package'=>''];
	}
	public function edit($id)
	{
		$jurnal						= MJurnal::join('jurnal_detail', 'jurnal_umum.id', 'jurnal_detail.jurnal_id')
											->find($id);
		$nojurnal 				= $jurnal->no_jurnal;
		$so 							= $jurnal->no_so;

		$detailjurnal			= MDetailJurnal::join('perkiraan', 'jurnal_detail.id_perkiraan', 'perkiraan.id')
											->where('jurnal_detail.jurnal_id', $id)
											->get();
		$data			= [
				'jurnal'				=> $jurnal,
				'detailJurnal'	=>$detailjurnal
		];
		return response()->json($data);
		return view('finance.jurnal.edit_jurnal', compact('data','nojurnal', 'so'));
	}
	public function update(Request $request, $id)
	{
		DB::update('update jurnal_umum set date_added = ? where id = ?',[$request->date_added,$request->jurnal_id]);

		$detail					= MDetailJurnal::find($id);

		$detail->jurnal_id		= $request->jurnal_id;
		if($request->perkiraan_id1 > 0){
			$detail->id_perkiraan	= $request->perkiraan_id1;
		}else{
			$detail->id_perkiraan	= $request->perkiraan_id;
		}
		$detail->jumlah_debet	= $request->jumlah_debet;
		$detail->jumlah_kredit	= $request->jumlah_kredit;
		$detail->date_jurnal	= $request->date_added;

		$detail->save();
	   return response()->json($detail);

		$request->session()->flash('alert-success', 'was successful Update!');
		return redirect()->route('list.jurnal');
	}
	public function destroy($id)
	{
		$jurnal				= MJurnal::FindOrFail($id);
		$dJurnal			= MDetailJurnal::where('jurnal_id', $id)->get();

		foreach($dJurnal as $b){
			$id_detail		= $b->id;
		}
		$Dsjurnal			= MDetailJurnal::FindOrFail($id_detail);
		$Dsjurnal->delete();
		$jurnal->delete();
		return redirect()->route('list.jurnal')->with('success','Article created successfully');
	}
	public function GetAkunAll(Request $request)
	{
		$search					= $request->q;
		if(!$search){
			$akun		= MPerkiraan::select('id as id_akun', 'perkiraan_akun', 'klasifikasi', 'kode_akun')->paginate(5);
		}else{
			$akun		= MPerkiraan::where('perkiraan_akun', 'LIKE', '%'.$search. '%')
							->select('id as id_akun', 'perkiraan_akun', 'klasifikasi', 'kode_akun')
							->paginate(10);
		}

		$data	= [
			'data'			=> $akun,
			'pagination' => [

				'total' => $akun->total(),

				'per_page' => $akun->perPage(),

				'current_page' => $akun->currentPage(),

				'last_page' => $akun->lastPage(),

				'from' => $akun->firstItem(),

				'to' => $akun->lastItem()
			]
			];

		return response()->json($data);
	}
}
