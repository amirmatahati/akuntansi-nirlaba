<?php

namespace App\Http\Controllers\finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\MSallesOrderDetail;
use App\Models\MSallesOrder;
use App\Models\MPerkiraan;
use App\Models\MDetailJurnal;
use App\Models\package;
use App\Models\MReport;

use App\Classes\SallesOrderClass;
use App\Classes\AddressClass;
use App\Classes\PerkiraanClass;
use App\Classes\JurnalClass;
use App\Classes\PackageClass;
use App\Classes\CBrand;

use DB;
use Carbon;
class SaleController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
		$this->packages					= new PackageClass;
		$this->sale						= new SallesOrderClass;
		$this->akun_asset				= new PerkiraanClass;
		$this->addressC					= new AddressClass;
		$this->brand					= new CBrand;
    }
	public function index()
	{
		return view('finance.home');
	}
    public function listSO()
	{
		$dateS 							= Carbon\Carbon::now()->startOfMonth()->subMonth(12);
		$dateE 							= Carbon\Carbon::now()->startOfMonth(); 
		
		$thisYears						= $mytime = \Carbon\Carbon::now();
		$so								= new SallesOrderClass();
		$query							= $so->JoinOrder();
		$analisso						= $this->packages->PackageSo();
		$analisso1						= $this->sale->SumSale($thisYears);
		$data	= [
			'data'			=> $query,
			'pagination' => [
 
				'total' => $query->total(),
  
				'per_page' => $query->perPage(),
  
				'current_page' => $query->currentPage(),
  
				'last_page' => $query->lastPage(),
  
				'from' => $query->firstItem(),
  
				'to' => $query->lastItem()
  
			]
		];
		return response()->json($data);
		return view('finance.sale', compact('query', 'analisso', 'thisYears'));
	}
	public function Invoice($id)
	{
		$sale							= MSallesOrderDetail::where('salles_order_id', $id)->get();
		$so								= new SallesOrderClass();
		$addressC						= new AddressClass();
		return view('modal.finance.invoice', compact('sale','so','id', 'addressC') );
	}
	public function Analisis(Request $request)
	{
		$thisYears						= \Carbon\Carbon::now();
		$asset							= $this->akun_asset->analisaBisnis($thisYears);
		$akunGet						= new PerkiraanClass();
		$GetAkun						= $akunGet->PluckName();
		
		$analisso						= $this->packages->PackageSo();
		
		
		
		
		/* GRAFIK HARTA */
		
		$chrt							= '';
		$item_name						= '';
		$jurnal 						= $asset->groupBy(function ($akun) {
			return $akun->id;
		})->all();
		$totalAtetap 					= 0;
		$subTdebet 						= 0;
		$subTkredit 					= 0;
		if($request->has('search_harta')){
			$thn			= $request->grafik_s;
			echo json_encode($thn);
		}else{
			$thn			= date('Y', strtotime($thisYears));
		}						
		foreach($jurnal as $idakun => $akun){
			$perkiraan			= MDetailJurnal::where('date_jurnal','LIKE', '%'.$thn.'%')
								->where('id_perkiraan',$idakun)
								->select('id_perkiraan', 'jumlah_debet', 'jumlah_kredit', 'date_jurnal')
								->get();
									
			$Tdebet			= 0;
			$Tkredit		= 0;
			$hasilAsset		= 0;
			$SumTdebet		= 0;
			foreach($perkiraan as $b){
				$Tdebet		+= $b->jumlah_debet;
				$Tkredit	+= $b->jumlah_kredit;
											
				$SumTdebet  	= str_slug($Tdebet) - str_slug($Tkredit);
				$hasilAsset		+= str_slug($SumTdebet);
			}
			$data 			= $SumTdebet;
			$namanya 		= $akunGet->GetNameAkun($idakun);
										 
			//echo json_encode($data);
			$chrt			.="{
				name: '".$namanya ."',
				y: ".str_slug($data) ."
				},";
			$item_name		= $namanya;
		}
		
		
		/* PROGRESS PENAWARAN */
		$progres_report		= MReport::all();
		
		
		$chrt_progress		= '';
		$item_progress		= '';
		$nm_progress		= '';
		$prog_stat 			= $progres_report->groupBy(function ($status_progress) {
			return $status_progress->progress;
		})->all();
		$totalAtetap 		= 0;
		$subTdebet 			= 0;
		$subTkredit 		= 0;
		if($request->has('progress_s')){
			$thn_progress	= $request->progress_s;
		}else{
			$thn_progress	= date('Y', strtotime($thisYears));
		}						
		foreach($prog_stat as $nm_prog => $status_progress){
			$get_progress	= MReport::where('date_activities','LIKE', '%'.$thn_progress.'%')
								->where('progress',$nm_prog)
								->get();
									
			$T_prog			= 0;
			$Tkredit		= 0;
			$hasilProgress	= 0;
			$SumTdebet		= 0;
			foreach($get_progress as $b_progress){
				$T_prog		+= $b_progress->totals;
				$nm_progress= $b_progress->progress;
			}
			$data_progress	= $T_prog;
										 
			//echo json_encode($data);
			$chrt_progress			.="{
				name: '".$nm_progress ."',
				y: ".str_slug($data_progress) ."
				},";
			$item_progress		= $nm_progress;
		}
		
		
		
		return view('finance.analisa_bisnis', compact('thn_progress','chrt_progress','chrt','thn','item_name','asset', 'akunGet', 'GetAkun', 'thisYears', 'analisso', 'chrt21','hotsprospek','close','rejected','processing'));
	}
	public function GetMailAddres($id)
	{
		$brands				= $this->brand->GetIdBrandByCompanies($id);
		$datas				= $this->addressC->GetMailByid($id);
		$datamail			= $this->brand->GetmaiBYBrand($brands);

		$data	= [
			'to'			=> $datas,
			'cc'			=> $datamail
		];
		return response()->json($data);
	}
	
}

