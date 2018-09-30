<?php

namespace App\Http\Controllers\jurnal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\MDetailJurnal;
use App\Classes\AutoNumber;
use App\Classes\JurnalClass;
use App\Classes\PerkiraanClass;

class NeracaPenutupController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    public function posisiAsset()
	{
		return view('finance.jurnal.posisi_keuangan');
	}
	public function SearchposisiAsset(Request $request)
	{
		$start_date = date('Y-m-d', strtotime($request->start));
		$end_date = date('Y-m-d', strtotime($request->end));

		$startMonth				= $start_date;
		$endMonth				= $end_date."+1 day";
		$lapEnd					= $request->end;
		$periode				= date('d M Y', strtotime($lapEnd));

		$perkiraan				= new PerkiraanClass();
		$nsd					= new JurnalClass();
		$h_lancar				= 'harta lancar';
		$h_tetap				= 'harta tetap';
		$neraca					= $nsd->NotHartaTetap($h_lancar, $startMonth,$endMonth);
		//$assetTetap				= $nsd->NotHartaTetap($h_tetap,$startMonth,$endMonth);
		$assetTetap				= $nsd->ActivaBiayaPenyusutan($startMonth,$endMonth);

		/* 	modal Total */
		$idprive	 			= $perkiraan->GetAkunByid('modal');
		$idperkiraan 			= $perkiraan->GetAkunByid('modal');
		$modal					= $nsd->Modal($idperkiraan, $startMonth,$endMonth);
		$beban					= $perkiraan->BiayaBeban($startMonth,$endMonth);
		$getBebanPluck			= $beban->pluck('id');
		$prive					= $nsd->GetPrive($idprive, $startMonth,$endMonth);
		$GetModal				= new JurnalClass();

		$Hutang					= $nsd->GetKewajiban($startMonth,$endMonth);

		$html								= '';
		$table1								= '';
		$name_asset_lancar[]				= '';
		$totalAssetTetap					= 0;
		$table2								= '';
		$total_asset						= 0;
		$table3								= '';
		$totalLibilitas						= 0;
		$modal_akhir						= 0;
		$totalLaba							= 0;
		$penambahan_modalakhir				= 0;

		$jurnal 						= $neraca->groupBy(function ($akun) {
			return $akun->idakun;
		})->all();
		$totalAtetap 					= 0;
		$sum_bAtetap							= 0;
		foreach($jurnal as $name => $akun){
			$jmdebet					= 0;
			$jmkredit					= 0;
			$ajpDebetJ					= 0;

			$getPerkiraan				= $nsd->GetPerkiraan($name,$startMonth, $endMonth);
			$idper						= $getPerkiraan->pluck('id_perkiraan');

			$ajpbeban					= 0;
			$jmlajp1					= 0;
			$Stotal						= 0;
			//$ajpDebetJ					= 0;

			/* get data ajp untuk dikurangi */
			$ajpData					= $nsd->GetPerkriaanAjp($idper, $startMonth, $endMonth);
			foreach($ajpData as $ajpD){
					$ajpDebetJ			= str_slug($ajpDebetJ) + str_slug($ajpD->ajp_kredit);
			}

			foreach($getPerkiraan as $d){
				$jmdebet				= str_slug($jmdebet) + str_slug($d->jumlah_debet);
				$jmkredit				= str_slug($jmkredit) + str_slug($d->jumlah_kredit);
				$totalAtetapsum	= str_slug($jmdebet) - str_slug($jmkredit) - str_slug($ajpDebetJ);
				$akun					= $d->namanya_akun;
				$nostring				= str_slug($totalAtetapsum);
			}

			$bebantotaldebet			= 0;
			$bebantotalkredit			= 0;
			$ajpkred					= 0;
			$ajpdebet					= 0;
			$assetAjpDebet				= 0;

			$ajpPerkiraan				= $nsd->GetPerkriaanAjp($idper, $startMonth,$endMonth);
			foreach($ajpPerkiraan as $bebans){
				$ajpbeban				= $ajpbeban + $bebans->ajp_debet;
				$bebantotaldebet		= $d->jumlah_debet + $bebans->ajp_debet;
				$bebantotalkredit		= $d->jumlah_debet - $bebans->ajp_kredit;
				$jmlajp1				= $jmlajp1 + $bebans->ajp_debet;
				$ajpkred				= $bebans->ajp_kredit;
				$ajpdebet				= $bebans->ajp_debet;
			}


			$sum_bAtetap						= $sum_bAtetap + str_slug($totalAtetapsum);
			$name_asset_lancar[]		= $nsd->GetnameAkunId($name);
			$table1						.= '<tr><td>'.$nsd->GetnameAkunId($name) .'</td>';
			if($ajpkred > 0){
					$assetAjpKredit			= $bebantotalkredit;
				$table1						.= '<td>Rp. '.number_format(str_slug($bebantotalkredit), 2) .'</td></tr>';
			}elseif($bebantotaldebet > 0){
					$assetAjpDebet			= $bebantotaldebet;
				$table1						.= '<td>Rp. '.number_format(str_slug($bebantotaldebet), 2) .'</td></tr>';
			}else{
					$assetDebet				= $totalAtetapsum;
					$totalAtetap			= $totalAtetap + str_slug($totalAtetapsum);
				$table1						.= '<td>Rp. '.number_format(str_slug($totalAtetapsum), 2) .'</td></tr>';
			}
			//$table1						.= '<td>Rp. '.number_format(str_slug($totalAtetapsum), 2) .'</td></tr>';
		}
		//return json_encode($totalAtetap);
		$table2								= '';
		$jurnal21 							= $assetTetap->groupBy(function ($akun) {
			return $akun->id;
		})->all();
		$totalAtetap 						= 0;
		$totalTetap							= 0;
		$jmkredit							= 0;
		$totalAsetTetapNonAjp				= 0;
		$totalAsetTetapAjp					= 0;
		$totalAssetTetap					= 0;
		foreach($jurnal21 as $nameK => $akun){
				$GetAssetTtp						= $nsd->GetPerkiraan($nameK, $startMonth,$endMonth);
			$jmdebet							= 0;
			$jmkredit							= 0;
			$akunss								= 0;
			foreach($GetAssetTtp as $d){
				$jmdebet						= $jmdebet + $d->jumlah_debet;
				$jmkredit						= $jmkredit + $d->jumlah_kredit;
				$total							= $jmdebet - $jmkredit;
				$total1							= $jmdebet - $jmkredit;
				$akunss							= $d->jumlah_debet;

				$nostring						= str_slug($total);
			}
			$ajpTetap							= $nsd->GetPerkriaanAjp($nameK, $startMonth,$endMonth);
			$jmajpD								= 0;
			$idp								= 0;
			foreach($ajpTetap as $atp){
				$jmajpD							= $jmajpD + $atp->ajp_kredit;
				$idp							= $atp->ajp_kredit;
			}
			$ajpTp								= $jmdebet - $jmajpD;

			$table2								.= '<tr>';

			if($ajpTp > 0){
			$table2								.= '<td>'. $nsd->GetnameAkunId($nameK) .'</td>';
				$table2								.= '<td >Rp. '. number_format(str_slug($ajpTp), 2).'</td>';
			}
			$table2								.= '</tr>';

			$totalAsetTetapNonAjp				= $totalAsetTetapNonAjp + str_slug($jmdebet);
			$totalAsetTetapAjp					= $totalAsetTetapAjp + str_slug($jmajpD);
			$totalAssetTetap					= $totalAsetTetapNonAjp - $totalAsetTetapAjp;

		}
	//	return json_encode($ajpTetap);
		//return $totalAssetTetap + $sum_b;
		$total_asset							= $sum_bAtetap + $totalAssetTetap;


		/* ------------------------------ UTANG ---------------------------------- */
		$table3									= '';
		$sumHutangJ								= $Hutang->sum('jumlah_kredit');
		$pluckHutang							= $Hutang->pluck('id');
		$groupHutang 							= $Hutang->groupBy(function ($utangLeabil) {
			return $utangLeabil->id;
		})->all();
		$totalLibilitas 						= 0;
		foreach($groupHutang as $utang => $idutang){
			$jmld								= 0;
			$debetH								= 0;
			$kreditHut							= 0;
			$ajpdebet							= 0;
			$ajpkredit							= 0;
			foreach($idutang as $fg){
				$debetH							+= $fg->jumlah_debet;
				$kreditHut						+= $fg->jumlah_kredit;
				$ajpdebet						+= $fg->ajp_debet;
				$ajpkredit						+= $fg->ajp_kredit;
			}
			$sds								= $kreditHut - $debetH;
			$sds2								= $ajpdebet - $ajpkredit;
			$ok									= str_slug($sds) - str_slug($sds2);

			$table3								.= '<tr>';
			$table3								.= '<td>'. $nsd->GetnameAkunId($utang).'</td>';
			$table3								.= '<td>Rp. '. number_format($ok,2) .'</td>';
			$table3								.= '</tr>';

			$totalLibilitas						+= str_slug($ok);

		}

		/* ---------------------------------------------- GEt MODAL ------------------------------ */

		$Mkredit								= 0;
		foreach($modal as $d){
			$nama_akun								= $d->GetPerkiraan->perkiraan_akun;
			$Mkredit								= $Mkredit + $d->jumlah_kredit;
		}

		$Jkredit								= 0;
		$total_pendatan						= 0;
		foreach($perkiraan->SeacrhName2($startMonth, $endMonth) as $ddd){
			$Jkredit								= $Jkredit + $ddd->jumlah_kredit;
			$total_pendatan					=  $total_pendatan + $ddd->total;
		}

		$penyesuainTotal						= 0;
		foreach($perkiraan->SeacrhPendapatanAJP($startMonth, $endMonth) as $ajpp){
			$penyesuainTotal					= $penyesuainTotal + $ajpp->ajp_kredit;
		}
		$totalPendapatan1						= $penyesuainTotal + $total_pendatan;
		$jml_beban								= 0;
		$ajpbeban								= 0;
		$jmlkredit								= 0;
		$namakunajp 							= '';
		$jmlajp1								= 0;

		foreach($beban as $e){
			$namakunajp								.= $e->perkiraan_akun ;
			$jmlkredit								= $jmlkredit + $e->jumlah_debet;
			$jml_beban								= $jml_beban + $e->total_beban;
			$debet									= $e->jumlah_debet;
			$bebantotaldebet						= 0;
			foreach($GetModal->ajpbyakun($e->id, $startMonth, $endMonth) as $bebans){
				$ajpbeban							= $ajpbeban + $bebans->ajp_debet;
				$bebantotaldebet					= $e->jumlah_debet + $bebans->ajp_debet;
				$jmlajp1							= $jmlajp1 + $bebans->ajp_debet;
			}
		}

		$jmlajp									= 0;
		$sublaba								= 0;
		$totalpenyesuaian			= 0;
		foreach($GetModal->OnlyAjp($getBebanPluck,$startMonth, $endMonth) as $detajp){
			$ss									= $jmlajp1;
			$jmlajp								= $jmlajp + $detajp->ajp_debet;
			$totalpenyesuaian					= $ss + $jmlajp;
		}
		$sublaba							= $totalpenyesuaian + $jml_beban;
		$laba									= str_slug($totalPendapatan1) - $sublaba;

		$jmdebet 								= 0;
		$jmkredit 								= 0;

		if($prive->count() > 0){
			foreach($prive as $prives){
				$prive_name							= $prives->GetPerkiraan->perkiraan_akun;
				$jmdebet							= $jmdebet + $prives->jumlah_debet;
				$jmkredit							= $jmkredit + $prives->jumlah_kredit;
				$total								= $jmdebet - $jmkredit;
				$nostring							= str_slug($total);
			}
		}else{
			$nostring = 0;
		}

		$penambahan_modal						= str_slug($laba) - $nostring;
		$modal_akhir							= $Mkredit + str_slug($penambahan_modal);
		$totalLaba								= 0;
		$totalLaba								+= $modal_akhir;
		$penambahan_modalakhir					= 0;
		$penambahan_modalakhir					= str_slug($totalLaba) + str_slug($totalLibilitas);
		//echo json_encode($sublaba);
			$data	= [
				'name_asset_lancar'		=> $name_asset_lancar,
				'table1'				=> $table1,
				'periode'				=> $periode,
				'jmlh'					=> $sum_bAtetap,
				'totalAssetTetap'		=> str_slug($totalAssetTetap),
				'table2'				=> $table2,
				'total_asset'			=> str_slug($total_asset),
				'table3'				=> $table3,
				'totalLibilitas'		=> str_slug($totalLibilitas),
				'modal_akhir'			=> str_slug($modal_akhir),
				'totalLaba'			 	=> str_slug($totalLaba),
				'penambahan_modalakhir'	=> str_slug($penambahan_modalakhir)
			];

		return response()->json($data);
		//return view('finance.jurnal.search_posisi_keuangan', compact('startMonth', 'endMonth', 'perkiraan','nsd', 'neraca', 'assetTetap', 'modal', 'idperkiraan', 'beban', 'GetModal','getBebanPluck', 'prive', 'idprive', 'Hutang', 'lapEnd'));

	}
}
