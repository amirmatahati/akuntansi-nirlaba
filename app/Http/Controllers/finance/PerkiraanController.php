<?php

namespace App\Http\Controllers\finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\MPerkiraan;
use App\Models\MDetailJurnal;

use App\Classes\PerkiraanClass;
use App\Classes\JurnalClass;

class PerkiraanController extends Controller
{
	protected $akun;
	public function __construct()
    {
        $this->akun = new PerkiraanClass;
		$this->middleware('auth');
    }
	public function index()
	{
		$perkiraan						= MPerkiraan::orderBy('kode_akun', 'ASC')->paginate(20);

		$data	= [
			'data'			=> $perkiraan,
			'pagination' => [

				'total' => $perkiraan->total(),

				'per_page' => $perkiraan->perPage(),

				'current_page' => $perkiraan->currentPage(),

				'last_page' => $perkiraan->lastPage(),

				'from' => $perkiraan->firstItem(),

				'to' => $perkiraan->lastItem()

			]
		];
		return response()->json($data);

		return view('finance.perkiraan.index', compact('perkiraan') );
	}
    public function create()
	{
		return view('finance.perkiraan.add');
	}
	public function store(Request $request)
	{
		$perkiraan						= new MPerkiraan;

		$perkiraan->kode_akun			= $request->kode_akun;
		$perkiraan->perkiraan_akun		= $request->perkiraan_akun;
		$perkiraan->klasifikasi			= $request->klasifikasi;
		$perkiraan->sub_klasifikasi		= $request->sub_klasifikasi;

		$perkiraan->save();

		$request->session()->flash('alert-success', 'was successful add!');
		return redirect()->route('perkiraan.list');
	}
	public function edit($id)
	{
		$ref							= MPerkiraan::find($id);

		$idperkiraan					= MPerkiraan::where('id', $id)->pluck('klasifikasi');
		return view('finance.perkiraan.edit', compact('id', 'ref', 'idperkiraan'));
	}
	public function update(Request $request, $id)
	{
		$ref							= MPerkiraan::find($id);

		$ref->kode_akun								= $request->kode_akun;
		$ref->perkiraan_akun					= $request->perkiraan_akun;
		$ref->klasifikasi							= $request->klasifikasi;
		$ref->sub_klasifikasi					= $request->sub_klasifikasi;

		$ref->save();

		$request->session()->flash('alert-success', 'was successful Update!');
		return response()->json($ref);
		return redirect()->route('perkiraan.list');
	}

	public function AssetList()
	{
		$asset							= MPerkiraan::join('jurnal_detail', 'perkiraan.id','jurnal_detail.id_perkiraan')
										->leftjoin('detail_ajp', 'perkiraan.id','detail_ajp.id_akun')
										->select('jurnal_detail.jumlah_debet', 'jurnal_detail.jumlah_kredit','perkiraan.perkiraan_akun','perkiraan.id as idperk', 'detail_ajp.ajp_debet','detail_ajp.ajp_kredit')
										->get();
		$akunGet						= new PerkiraanClass();
		$GetAkun						= $akunGet->PluckName();

		$html	= '';
		$chrt	= '';
		$chrt	.='[';
		$jurnal = $asset->groupBy(function ($akun) {
			return $akun->idperk;
		})->all();
		$totalAtetap = 0;
		$subTdebet = 0;
		$subTkredit = 0;
		foreach($jurnal as $idakun => $akun){
			$perkiraan			= MDetailJurnal::where('id_perkiraan',$idakun)->get();
			$Tdebet				= 0;
			$Tkredit			= 0;
			$hasilAsset			= 0;
			$SumTdebet			= 0;
			foreach($perkiraan as $b){
				$Tdebet		+= $b->jumlah_debet;
				$Tkredit	+= $b->jumlah_kredit;

				$SumTdebet  	= str_slug($Tdebet) - str_slug($Tkredit);
				$hasilAsset		+= str_slug($SumTdebet);
			}
			$data1[] = $SumTdebet;
			$namanya[] = $akunGet->GetNameAkun($idakun);

			//$chrt	.="['".$namanya ."',".str_slug($data1) ."],";

		}
		$chrt	.= ']';
		$datanya	=[
			'name_chrt'			=> $namanya,
			'data_chart'		=> $data1

		];

		return response()->json($datanya);
		return view('finance.perkiraan.list_asset', compact('asset', 'akunGet', 'GetAkun'));
	}
	public function SearchAsset(Request $request)
	{
		$start_date = date('Y-m-d', strtotime($request->start));
		$end_date = date('Y-m-d', strtotime($request->end));


		$startMonth					= $start_date;
		$endMonth					= $end_date."+1 day";



		$perkiraan					= new PerkiraanClass();
		$jurnal						= new JurnalClass();
		$nsd							= new JurnalClass();

		$beban						= $jurnal->GetBebanBiaya($startMonth,$endMonth);
		$idperk						= $beban->pluck('id');
		$sumdebet					= $beban->sum('jumlah_debet');

		/* Ambil data dari posisi Keuangan */


		$neraca						= $jurnal->NotHartaTetap( $startMonth,$endMonth);

		$assetTetap					= $jurnal->ActivaBiayaPenyusutan($startMonth,$endMonth);
		/* 	modal Total */
		$idprive 					= $perkiraan->GetAkunByid('prive');
		$idperkiraan				= $perkiraan->GetAkunByid('modal');
		$modal						= $jurnal->Modal($idperkiraan, $startMonth,$endMonth);

		$getBebanPluck				= $beban->pluck('id');
		$prive						= $jurnal->GetPrive($idprive, $startMonth,$endMonth);
		$GetModal					= new JurnalClass();

		$Hutang						= $jurnal->GetKewajiban($startMonth,$endMonth);

		$search_name				= $perkiraan->SeacrhName2($startMonth,$endMonth);

		$Jkredit	= 0;
		$chrt	= '';
		foreach($search_name as $d){
			$Jkredit			= $Jkredit + $d->jumlah_kredit;
			$nama_akun			= $d->perkiraan_akun;
			$kode_akun			= $d->kode_akun;
			$klasifikasi		= $d->klasifikasi;
		}
		$penyesuainTotal		= 0;
		foreach($perkiraan->SeacrhPendapatanAJP($startMonth,$endMonth) as $ajpp){
			$penyesuainTotal	= $penyesuainTotal + $ajpp->ajp_kredit;
		}
		$totalPendapatan1		= $penyesuainTotal + $Jkredit;
		$data[] = $totalPendapatan1;
		$namanya[] = $nama_akun;

		$jml_beban					= 0;
		$ajpbeban					= 0;
		$ajpdebets					= 0;
		$jmlkredit					= 0;
		$perkiraanid 				= '';
		$namakunajp 				= '';
		$jmlajp1					= 0;

		foreach($beban as $e){
			$perkiraanid				.= $e->id ;
			$namakunajp					.= $e->perkiraan_akun ;
			$jmlkredit					= $jmlkredit + $e->jumlah_debet;
			$jml_beban					= $jml_beban + $e->jumlah_debet;
			$debet						=  $e->jumlah_debet;
			$kodeAkun					= $e->akun_kode;

			if($e->jumlah_debet > 1){
				$bebantotaldebet			= 0;
				foreach($jurnal->ajpbyakun($e->id, $startMonth, $endMonth) as $bebans){
					$ajpbeban				= $ajpbeban + $bebans->ajp_debet;
					$bebantotaldebet		= $e->jumlah_debet + $bebans->ajp_debet;
					$jmlajp1				= $jmlajp1 + $bebans->ajp_debet;
				}
			}
			if($bebantotaldebet > 0){
				$data[] = $bebantotaldebet;
			}else{
				$data[] = $e->jumlah_debet;
			}
			$namanya[] = $e->perkiraan_akun ;
		}
		$jmlajp							= 0;
		if($jurnal->OnlyAjp($idperk,$startMonth, $endMonth)->count() > 0){
			foreach($jurnal->OnlyAjp($idperk,$startMonth, $endMonth) as $detajp){
				$ss							= $jmlajp1;
				$jmlajp						= $jmlajp + $detajp->ajp_debet;
				$totalpenyesuaian			= $ss + $jmlajp;

				$data[] 		= $detajp->ajp_debet;
				$namanya[] 	= $detajp->perkiraan_akun ;
			}
			$sublaba						= $totalpenyesuaian + $jml_beban;
		}else{
			$sublaba						= $jml_beban + 0;
		}
		$laba							= $sublaba - $totalPendapatan1;
		$jurnal 						= $neraca->groupBy(function ($akun) {
			return $akun->idakun;
		})->all();
		$totalAtetap 					= 0;
		foreach($jurnal as $name => $akun){
			$jmdebet						= 0;
			$jmkredit						= 0;

			$getPerkiraan					= $nsd->GetPerkiraan($name,$startMonth, $endMonth);
			$idper							= $getPerkiraan->pluck('id_perkiraan');

			$ajpbeban						= 0;
			$jmlajp1						= 0;
			$Stotal							= 0;

			foreach($getPerkiraan as $d){
				$jmdebet					= $jmdebet + $d->jumlah_debet;
				$jmkredit					= $jmkredit + $d->jumlah_kredit;
				$total						= $jmdebet - $jmkredit;
				$akun						= $d->namanya_akun;
				$nostring					= str_slug($total);
			}
			$bebantotaldebet			= 0;
			$bebantotalkredit			= 0;
			$ajpkred					= 0;
			$ajpdebet					= 0;
			$assetAjpDebet				= 0;

			$ajpPerkiraan		= $nsd->GetPerkriaanAjp($idper, $startMonth,$endMonth);

			foreach($ajpPerkiraan as $bebans){
				$ajpbeban			= $ajpbeban + $bebans->ajp_debet;
				$bebantotaldebet	= $d->jumlah_debet + $bebans->ajp_debet;
				$bebantotalkredit	= $d->jumlah_debet - $bebans->ajp_kredit;
				$jmlajp1			= $jmlajp1 + $bebans->ajp_debet;
				$ajpkred			= $bebans->ajp_kredit;
				$ajpdebet			= $bebans->ajp_debet;
			}
			if($ajpkred > 0){
				$assetAjpKredit			= $bebantotalkredit;
			}elseif($bebantotaldebet > 0){
				$assetAjpDebet			= $bebantotaldebet;
			}else{
				$assetDebet				= $total;
				$totalAtetap			= $totalAtetap + $total;
			}

		}
		if($ajpkred > 0){
			$data[]			= $bebantotalkredit;
		}elseif($bebantotaldebet > 0){
			$data[]			= $bebantotaldebet;
		}else{
			$data[]				= $total;
		}
		$namanya[] 	= $nsd->GetnameAkunId($name) ;
		/* Get dari asset lancar */
		$jurnal21 				= $assetTetap->groupBy(function ($akun) {
			return $akun->id;
		})->all();
		$totalAtetap 			= 0;
		$totalTetap				= 0;
		$jmkredit				= 0;
		$totalAsetTetapNonAjp	= 0;
		$totalAsetTetapAjp		= 0;
		foreach($jurnal21 as $nameK => $akun){
			$GetAssetTtp			= $nsd->GetPerkiraan($nameK, $startMonth,$endMonth);
				$jmdebet				= 0;
				$jmkredit				= 0;
				$akunss					= 0;
				foreach($GetAssetTtp as $d){
					$jmdebet			= $jmdebet + $d->jumlah_debet;
					$jmkredit			= $jmkredit + $d->jumlah_kredit;
					$total				= $jmdebet - $jmkredit;
					$total1				= $jmdebet - $jmkredit;
					$akunss				= $d->jumlah_debet;
					$nostring			= str_slug($total);
				}

				$ajpTetap				= $nsd->GetPerkriaanAjp($nameK, $startMonth,$endMonth);
				$jmajpD					= 0;
				$idp					= 0;

				foreach($ajpTetap as $atp){
					$jmajpD				= $jmajpD + $atp->ajp_kredit;
					$idp				= $atp->ajp_kredit;
				}
				$ajpTp					= $jmdebet - $jmajpD;
				$data[]		= $ajpTp;
				$namanya[] 	= $nsd->GetnameAkunId($nameK) ;
		}
		/* Get dari leabilitas */
		$sumHutangJ				= $Hutang->sum('jumlah_kredit');
		$pluckHutang			= $Hutang->pluck('id');
		$groupHutang 			= $Hutang->groupBy(function ($utangLeabil) {
			return $utangLeabil->id;
		})->all();
		$totalLibilitas 		= 0;
		foreach($groupHutang as $utang => $idutang){
			$jmld				= 0;
			$debetH				= 0;
			$kreditHut			= 0;
			$ajpdebet			= 0;
			$ajpkredit			= 0;

			foreach($idutang as $fg){
				$debetH			+= $fg->jumlah_debet;
				$kreditHut		+= $fg->jumlah_kredit;
				$ajpdebet		+= $fg->ajp_debet;
				$ajpkredit		+= $fg->ajp_kredit;
			}
			$sds					=  $kreditHut - $debetH;
			$sds2					= $ajpdebet - $ajpkredit;
			$ok						= str_slug($sds) - str_slug($sds2);

			$data[]		= $ok;
			$namanya[] 	= $nsd->GetnameAkunId($utang) ;
		}

		/* Get Modal Total */

		$Mkredit				= 0;
		foreach($modal as $d){
			$nama_akun				= $d->GetPerkiraan->perkiraan_akun;
			$Mkredit				= $Mkredit + $d->jumlah_kredit;
		}
		$Jkredit				= 0;
		foreach($perkiraan->SeacrhName2($startMonth, $endMonth) as $ddd){
			$Jkredit				= $Jkredit + $ddd->jumlah_kredit;
		}
		$penyesuainTotal		= 0;
		foreach($perkiraan->SeacrhPendapatanAJP($startMonth, $endMonth) as $ajpp){
			$penyesuainTotal	= $penyesuainTotal + $ajpp->ajp_kredit;
		}
		$totalPendapatan1		= $penyesuainTotal + $Jkredit;

		$jml_beban				= 0;
		$ajpbeban				= 0;
		$jmlkredit				= 0;
		$namakunajp 			= '';
		$jmlajp1				= 0;

		foreach($beban as $e){
			$namakunajp				.= $e->perkiraan_akun ;
			$jmlkredit				= $jmlkredit + $e->jumlah_debet;
			$jml_beban				= $jml_beban + $e->jumlah_debet;
			$debet					=  $e->jumlah_debet;

			$bebantotaldebet		= 0;
			foreach($GetModal->ajpbyakun($e->id, $startMonth, $endMonth) as $bebans){
				$ajpbeban			= $ajpbeban + $bebans->ajp_debet;
				$bebantotaldebet	= $e->jumlah_debet + $bebans->ajp_debet;
				$jmlajp1			= $jmlajp1 + $bebans->ajp_debet;
			}
		}
		$jmlajp					= 0;

		foreach($GetModal->OnlyAjp($getBebanPluck,$startMonth, $endMonth) as $detajp){
			$ss					= $jmlajp1;
			$jmlajp				= $jmlajp + $detajp->ajp_debet;
			$totalpenyesuaian	= $ss + $jmlajp;
			$sublaba			= $totalpenyesuaian + $jml_beban;
		}
		$laba					= $sublaba - $totalPendapatan1;

		$jmdebet 				= 0;
		$jmkredit 				= 0;
		if($prive->count() > 0){
			foreach($prive as $prives){
				$prive_name			= $prives->GetPerkiraan->perkiraan_akun;
				$jmdebet			= $jmdebet + $prives->jumlah_debet;
				$jmkredit			= $jmkredit + $prives->jumlah_kredit;
				$total				= $jmdebet - $jmkredit;
				$nostring			= str_slug($total);
			}
		}else{
			$nostring 		= 0;
		}
		$penambahan_modal		= str_slug($laba) - $nostring;
		$modal_akhir			= $Mkredit + $penambahan_modal;
		$totalLaba				= 0;
		$totalLaba				+= $modal_akhir;
		$penambahan_modalakhir	= 0;
		$penambahan_modalakhir	= str_slug($totalLaba) + str_slug($totalLibilitas);

		$data[]		= $modal_akhir;
		$namanya[] 	= $perkiraan->GetByName('modal') ;

		$years_asset			= date('M Y', strtotime($startMonth));
		$datanya	=[
			'name_chrt'			=> $namanya,
			'data_chart'		=> $data,
			'years_asset'		=> $years_asset

		];

		return response()->json($datanya);
		return view('finance.perkiraan.search_asset', compact('perkiraan','jurnal','beban', 'sumdebet', 'startMonth', 'endMonth','idperk', 'perkiraan','nsd', 'neraca', 'assetTetap', 'modal', 'idperkiraan', 'beban', 'GetModal','getBebanPluck', 'prive', 'idprive', 'Hutang', 'search_name'));

	}
	public function GetAkunAll(Request $request)
	{
		$search					= $request->q;
		$pakun					= $this->akun->AkunAll($search);

		$data	= [
			'data'			=> $pakun,
			'pagination' => [

				'total' => $pakun->total(),

				'per_page' => $pakun->perPage(),

				'current_page' => $pakun->currentPage(),

				'last_page' => $pakun->lastPage(),

				'from' => $pakun->firstItem(),

				'to' => $pakun->lastItem()
			]
			];

		return response()->json($data);
	}
	public function AkunByiD(Request $request)
	{
		$id				= $request->data;
		$akundata			= $this->akun->GetAkunByIdLoop($id);

		return response()->json($akundata);
	}
}
