<?php

namespace App\Http\Controllers\jurnal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\MPerkiraan;

use App\Classes\JurnalClass;
use App\Classes\PerkiraanClass;
use App\Models\MDetailJurnal;

use Illuminate\Support\Str;

class NeracaSaldoController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    public function NeracaSaldo()
	{
		return view('finance.jurnal.neraca_saldo', compact('neraca','totaldebet','sumdebet','sumkredit', 'nsd','datenow'));
	}
	public function SearchNeracaSaldo(Request $request)
	{
		$start_date = date('Y-m-d', strtotime($request->start));
		$end_date = date('Y-m-d', strtotime($request->end));

		$startMonth						= $start_date;
		$endMonth						= $end_date."+1 day";
		$Month							= $end_date;

		$nsd							= new JurnalClass();

		$neraca							= $nsd->NeracaSaldo($startMonth,$endMonth);
		$sumdebet						= $neraca->sum('jumlah_debet');
		$sumkredit						= $neraca->sum('jumlah_kredit');

		$html							= '';
		$periode						= date('d M Y', strtotime($Month));
		$totalb = 0;
		$jurnal = $neraca->groupBy(function ($akun) {
			return $akun->idakun;
		})->all();
		foreach($jurnal as $name => $akun){
			$nama_akuns		= $nsd->GetnameAkunId($name) ;
			$html										.= '<tr>';
			$html										.= '<td>'.$nama_akuns.'<td>';

			$nama_akuns							= $nsd->GetnameAkunId($name) ;
			$jun										= MDetailJurnal::join('jurnal_umum', 'jurnal_detail.jurnal_id', 'jurnal_umum.id')
															->join('perkiraan', 'jurnal_detail.id_perkiraan', 'perkiraan.id')
															->select('jurnal_detail.jumlah_debet','jurnal_detail.id as iddetail', 'jurnal_detail.jumlah_kredit','jurnal_detail.id_perkiraan', 'jurnal_umum.date_added as jun_date','jurnal_umum.info','perkiraan.perkiraan_akun as namanya_akun')
															->whereBetween('jurnal_umum.date_added',[$startMonth,$endMonth])
															->where('id_perkiraan',$name)->get();
			$jmdebet		= 0;
			$jmkredit		= 0;
			foreach($jun as $d){
				$jmdebet	= $jmdebet + $d->jumlah_debet;
				$jmkredit	= $jmkredit + $d->jumlah_kredit;
				$total		= $jmdebet - $jmkredit;
				$total1		= $jmdebet - $jmkredit;
				$akun		= $d->namanya_akun;

				$nostring	= str_slug($total);
			}
			if($total == $nostring){
				$html		.= '<td colspan="2">Rp. '.number_format($nostring, 2) .'<input type="hidden" value="'.$nostring .'"></td>';
				$html		.= '<td><input type="hidden" value="0"></td>';
			}else{
				$html		.= '<td><input type="hidden" value="0"></td>';
				$html		.= '<td colspan="2">Rp. '.number_format($nostring, 2) . '<input type="hidden" value="'. $nostring . '"></td>';
				$totalb	= $totalb + $nostring;
			}
			$html			.= '</tr>';
		}
		$total_b			= number_format($totalb);

		$data		= [
			'periode'			=> $periode,
			'html'				=> $html,
			'totalb'			=> $totalb
		];

		return response()->json($data);
		return view('finance.jurnal.search_neraca_saldo', compact('neraca','totaldebet','sumdebet','sumkredit', 'nsd', 'startMonth', 'endMonth', 'Month'));
	}
	public function AktivitasNew(Request $request)
	{
		$start_date = date('Y-m-d', strtotime($request->start));
		$end_date = date('Y-m-d', strtotime($request->end));

		$startMonth						= $start_date;
		$endMonth						= $end_date."+1 day";
		$Month							= $end_date;

		$nsd									= new JurnalClass();

		$neraca								= $nsd->GetAktivitas('pendapatan tidak terikat',$startMonth,$endMonth);

		$html									= '';
		$periode							= date('d M Y', strtotime($Month));
		$totalb 							= 0;
		$jurnal = $neraca->groupBy(function ($akun) {
			return $akun->idakun;
		})->all();
		$html									.= '<ul class="no_list_built">';
		$html									.= '<li><strong>Pendapatan</strong><ul>';
		foreach($jurnal as $name => $akun){
			$nama_akuns					= $nsd->GetnameAkunId($name) ;
			$html								.= '<li><span class="float-left">'.$nama_akuns.'</span>';

			$nama_akuns					= $nsd->GetnameAkunId($name) ;
			$jun								= $nsd->JurnalByIdPerkiraan($name,$startMonth,$endMonth);
			$jmdebet						= 0;
			$jmkredit						= 0;
			foreach($jun as $d){
				$jmdebet					= $jmdebet + $d->jumlah_debet;
				$jmkredit					= $jmkredit + $d->jumlah_kredit;
				$total						= $jmdebet - $jmkredit;
				$total1						= $jmdebet - $jmkredit;
				$akun							= $d->namanya_akun;

				$nostring					= str_slug($total);
			}

				$html		.= '<span class="float-right">Rp. '.number_format($nostring, 2) . '</span>';
				$totalb	= $totalb + $nostring;
				$html							.= '</li><div style="clear:both;"></div>';

		}
		$html			.= '</ul></li></ul>';
		$total_b			= number_format($totalb);

		/* ------------------------------------- 	BEBAN TIDAK TERIKAT KANTOR ------------------------- */

		$neraca								= $nsd->GetAktivitas('beban kantor',$startMonth,$endMonth);

		$bebantdkTerikat			= '';
		$periode							= date('d M Y', strtotime($Month));
		$totalbKantor 							= 0;
		$jurnal = $neraca->groupBy(function ($akun) {
			return $akun->idakun;
		})->all();
		$bebantdkTerikat		.= '<div style="clear:both;"></div><ul class="no_list_built">';
		$bebantdkTerikat		.= '<li><strong>Beban Kantor</strong><ul>';
		foreach($jurnal as $name => $akun){
			$nama_akuns					= $nsd->GetnameAkunId($name) ;
			$bebantdkTerikat		.= '<li><span class="float-left">'.$nama_akuns.'</span>';

			$nama_akuns					= $nsd->GetnameAkunId($name) ;
			$jun								= $nsd->JurnalByIdPerkiraan($name,$startMonth,$endMonth);
			$jmdebet						= 0;
			$jmkredit						= 0;
			foreach($jun as $d){
				$jmdebet					= $jmdebet + $d->jumlah_debet;
				$jmkredit					= $jmkredit + $d->jumlah_kredit;
				$total2						= $jmdebet - $jmkredit;
				$akun							= $d->namanya_akun;

				$jmlBeKantor					= str_slug($total2);
			}

				$bebantdkTerikat							.= '<span class="float-right">Rp. '.number_format($jmlBeKantor, 2) . '</span>';
				$totalbKantor									= $totalbKantor + $jmlBeKantor;
				$bebantdkTerikat							.= '</li><div style="clear:both;"></div>';

		}
		$bebantdkTerikat									.= '</ul></li></ul>';
		$total_beKantor			= number_format($totalbKantor, 2);

		/* ------------------------------------- 	BEBAN TIDAK TERIKAT PROGRAM ------------------------- */

		$neraca								= $nsd->GetAktivitas('beban program',$startMonth,$endMonth);

		$programtdkTerikat			= '';
		$periode							= date('d M Y', strtotime($Month));
		$total_beProgram 							= 0;
		$jurnal = $neraca->groupBy(function ($akun) {
			return $akun->idakun;
		})->all();
		$programtdkTerikat		.= '<ul class="no_list_built">';
		$programtdkTerikat		.= '<li><strong>Beban Kantor</strong><ul>';
		$totalbProgram			= 0;
		foreach($jurnal as $name => $akun){
			$nama_akuns					= $nsd->GetnameAkunId($name) ;
			$programtdkTerikat		.= '<li><span class="float-left">'.$nama_akuns.'</span>';

			$nama_akuns					= $nsd->GetnameAkunId($name) ;
			$jun								= $nsd->JurnalByIdPerkiraan($name,$startMonth,$endMonth);
			$jmdebet						= 0;
			$jmkredit						= 0;

			foreach($jun as $d){
				$jmdebet					= $jmdebet + $d->jumlah_debet;
				$jmkredit					= $jmkredit + $d->jumlah_kredit;
				$total2						= $jmdebet - $jmkredit;

				$jmlBeProgram					= str_slug($total2);
			}

				$programtdkTerikat							.= '<span class="float-right">Rp. '.number_format($jmlBeProgram, 2) . '</span>';
				$totalbProgram									= $totalbProgram + $jmlBeProgram;
				$programtdkTerikat							.= '</li><div style="clear:both;"></div>';

		}
		$programtdkTerikat									.= '</ul></li></ul><div style="clear:both;"></div>';
		$total_beProgram			= number_format($totalbProgram, 2);

		$total_tidak_terikat				=  ($totalbKantor) + str_slug($totalbProgram);
		$totalPendTdkTerikat				= str_slug($totalb) - $total_tidak_terikat;
		$AsetTidakTerikat						= number_format($totalPendTdkTerikat);


		/* ------------------------------------- 	PENDAPATAN TERIKAT ------------------------- */

		$neraca								= $nsd->GetAktivitas('Pendapatan Terikat Temporer',$startMonth,$endMonth);

		$pendTerikat					= '';
		$periode							= date('d M Y', strtotime($Month));
		$totalPendTerikat 							= 0;
		$jurnal = $neraca->groupBy(function ($akun) {
			return $akun->idakun;
		})->all();
		$pendTerikat				.= '<div style="clear:both;"></div><ul class="no_list_built">';
		$pendTerikat				.= '<li><strong>Pendapatan</strong><ul>';
		foreach($jurnal as $name => $akun){
			$nama_akuns					= $nsd->GetnameAkunId($name) ;
			$pendTerikat		.= '<li><span class="float-left">'.$nama_akuns.'</span>';

			$nama_akuns					= $nsd->GetnameAkunId($name) ;
			$jun								= $nsd->JurnalByIdPerkiraan($name,$startMonth,$endMonth);
			$jmdebet						= 0;
			$jmkredit						= 0;
			foreach($jun as $d){
				$jmdebet					= $jmdebet + $d->jumlah_debet;
				$jmkredit					= $jmkredit + $d->jumlah_kredit;
				$total2						= $jmdebet - $jmkredit;

				$jmlPendTerikat								= str_slug($total2);
			}

				$pendTerikat									.= '<span class="float-right">Rp. '.number_format($jmlPendTerikat, 2) . '</span>';
				$totalPendTerikat							= $totalPendTerikat + $jmlPendTerikat;
				$pendTerikat									.= '</li><div style="clear:both;"></div>';

		}
		$pendTerikat											.= '</ul></li></ul>';
		$total_PendTerikat								= number_format($totalPendTerikat, 2);

		/* ------------------------------------- 	BEBAN TERIKAT ------------------------- */

		$neraca								= $nsd->GetAktivitas('beban titipan',$startMonth,$endMonth);

		$bebanTemporer									= '';
		$periode							= date('d M Y', strtotime($Month));
		$totalbebanTemporer 							= 0;
		$jurnal = $neraca->groupBy(function ($akun) {
			return $akun->idakun;
		})->all();
		$bebanTemporer									.= '<ul class="no_list_built">';
		$bebanTemporer									.= '<li><strong>Beban</strong><ul>';
		foreach($jurnal as $name => $akun){
			$nama_akuns					= $nsd->GetnameAkunId($name) ;
			$bebanTemporer								.= '<li><span class="float-left">'.$nama_akuns.'</span>';

			$nama_akuns					= $nsd->GetnameAkunId($name) ;
			$jun								= $nsd->JurnalByIdPerkiraan($name,$startMonth,$endMonth);
			$jmdebet						= 0;
			$jmkredit						= 0;
			foreach($jun as $d){
				$jmdebet					= $jmdebet + $d->jumlah_debet;
				$jmkredit					= $jmkredit + $d->jumlah_kredit;
				$total						= $jmdebet - $jmkredit;

				$hasilDebetBeban					= str_slug($total);
			}

				$bebanTemporer		.= '<span class="float-right">Rp. '.number_format($hasilDebetBeban, 2) . '</span>';
				$totalbebanTemporer						= $totalbebanTemporer + $hasilDebetBeban;
				$bebanTemporer							.= '</li><div style="clear:both;"></div>';

		}
		$bebanTemporer			.= '</ul></li></ul>';
		$total_beban_tempore			= number_format($totalbebanTemporer, 2);

		$total_temporer						= str_slug($totalPendTerikat) - str_slug($totalbebanTemporer);
		$total_temporer						= str_slug($total_temporer);

		/* ------------------------------------- 	WAKAF ------------------------- */

		$neraca								= $nsd->GetAktivitas('beban wakaf',$startMonth,$endMonth);

		$wakaf									= '';
		$periode							= date('d M Y', strtotime($Month));
		$totalWakaf 							= 0;
		$jurnal = $neraca->groupBy(function ($akun) {
			return $akun->idakun;
		})->all();
		$wakaf									.= '<ul class="no_list_built">';
		$wakaf									.= '<li><strong>Beban</strong><ul>';
		foreach($jurnal as $name => $akun){
			$nama_akuns					= $nsd->GetnameAkunId($name) ;
			$wakaf								.= '<li><span class="float-left">'.$nama_akuns.'</span>';

			$nama_akuns					= $nsd->GetnameAkunId($name) ;
			$jun								= $nsd->JurnalByIdPerkiraan($name,$startMonth,$endMonth);
			$jmdebet						= 0;
			$jmkredit						= 0;
			foreach($jun as $d){
				$jmdebet					= $jmdebet + $d->jumlah_debet;
				$jmkredit					= $jmkredit + $d->jumlah_kredit;
				$total						= $jmdebet - $jmkredit;

				$hasilWakaf					= str_slug($total);
			}

				$wakaf		.= '<span class="float-right">Rp. '.number_format($hasilWakaf, 2) . '</span>';
				$totalWakaf						= $totalWakaf + $hasilWakaf;
				$wakaf							.= '</li><div style="clear:both;"></div>';

		}
		$wakaf			.= '</ul></li></ul>';
		$total_wakaf_sub			= number_format($totalWakaf, 2);

		$saldo_AsetNeto				= str_slug($AsetTidakTerikat) + str_slug($total_temporer) + str_slug($totalWakaf);
//		$saldo_AsetNeto				= str_slug($saldo_AsetNeto);


		$data		= [
			'periode'			=> $periode,
			'html'				=> $html,
			'totalb'			=> $totalb,
			'beban'		=> $bebantdkTerikat,
			'total_beKantor'	=> $total_beKantor,
			'programtdkTerikat'		=> $programtdkTerikat,
			'total_beProgram'			=> $total_beProgram,
			'AsetTidakTerikat'		=> $AsetTidakTerikat,
			'pendTerikat'					=> $pendTerikat,
			'total_PendTerikat'		=> $total_PendTerikat,
			'bebanTemporer'				=> $bebanTemporer,
			'total_beban_tempore'	=> $total_beban_tempore,
			'total_temporer'			=> number_format($total_temporer, 2),
			'wakaf'								=> $wakaf,
			'total_wakaf_sub'			=> $total_wakaf_sub,
			'saldo_AsetNeto'			=> number_format($saldo_AsetNeto, 2)
		];

		return response()->json($data);
	}
  public function Aktivitas(Request $request)
	{
		$start_date = date('Y-m-d', strtotime($request->start));
		$end_date = date('Y-m-d', strtotime($request->end));

		$startMonth						= $start_date;
		$endMonth						= $end_date."+1 day";
		$Month							= $end_date;

		$nsd							  = new JurnalClass();

		$neraca						= MDetailJurnal::join('perkiraan','jurnal_detail.id_perkiraan', 'perkiraan.id')
    										->whereBetween('jurnal_detail.date_jurnal',[$startMonth,$endMonth])
    										->select('jurnal_detail.jumlah_debet', 'jurnal_detail.jumlah_kredit','jurnal_detail.id_perkiraan as idakun','jurnal_detail.created_at as date_detail','perkiraan.perkiraan_akun as nama_akun', 'perkiraan.kode_akun')
    										->groupBy('perkiraan.sub_klasifikasi')
    										->orderBy('perkiraan.kode_akun','asc')
    										->get();
    $tidak_Terikat      = $nsd->Aktivitas('pendapatan tidak terikat');
		$sumdebet						= $neraca->sum('jumlah_debet');
		$sumkredit					= $neraca->sum('jumlah_kredit');

		$html							  = '';
		$periode						= date('d M Y', strtotime($Month));

		$jurnal = $neraca->groupBy(function ($akun) {
			return $akun->idakun;
		})->all();

    $html               .= '<ul class="no_list_built">';
	  foreach($tidak_Terikat as $ner){
			$totalb = 0;
			//if($ner->jumlah_debet > '0' || $ner->jumlah_kredit > '0'){
	      $html             .= '<li><strong>'.$ner->sub_klasifikasi.'</strong>';
	      $detail_jurnal     = MDetailJurnal::join('perkiraan','jurnal_detail.id_perkiraan', 'perkiraan.id')
	                        ->whereBetween('jurnal_detail.date_jurnal', [$startMonth,$endMonth])
	                        ->where('perkiraan.sub_klasifikasi', $ner->sub_klasifikasi)->groupBy('perkiraan.id')->get();
				$jmdebet					= 0;
				$jmkredit					= 0;

	      foreach($detail_jurnal as $j_detail){

					$jmdebet				= $jmdebet + $j_detail->jumlah_debet;
					$jmkredit				= $jmkredit + $j_detail->jumlah_kredit;
					$total					= $jmdebet - $jmkredit;
					$total1					= $jmdebet - $jmkredit;
					$nostring				= str_slug($total);
					$totalb							= $totalb + $nostring;

	        $html           .= '<ul>';
	        $html           .= '<li>'.$j_detail->perkiraan_akun.'<span class="float-right">Rp. '.number_format($nostring, 2).'</li>';
	        $html           .= '</ul>';
	      }

	      $html             .= '</li>';
				$html								.= '<div style="clear:both;"></div><li><span class="float-left"><strong>Jumlah '.$ner->sub_klasifikasi.'</strong></span><span class="float-right"><strong>Rp .'.number_format($totalb, 2).'</strong></span></li><div style="clear:both;"></div>' ;
    	//}
		}

    $html                 .= '</ul>';
		/* -------------------------------------------- BEBAN KANTOR --------------------------------------- */
		$bebanKantor					= '';
		$beban								= $nsd->Aktivitas('beban kantor');

		$bebanKantor         	.= '<ul class="no_list_built">';
	  foreach($beban as $ner1){
			$totalbeban = 0;
			if($ner1->jumlah_debet > '0' || $ner1->jumlah_kredit > '0'){
	      $bebanKantor      .= '<li><strong>'.$ner1->sub_klasifikasi.'</strong>';
	      $detail_jurnal1    = MDetailJurnal::join('perkiraan','jurnal_detail.id_perkiraan', 'perkiraan.id')
	                        ->whereBetween('jurnal_detail.date_jurnal', [$startMonth,$endMonth])
	                        ->where('perkiraan.sub_klasifikasi', $ner1->sub_klasifikasi)->groupBy('perkiraan.id')->get();
			//	return json_encode($detail_jurnal1);
				$jmdebetBeban			= 0;
				$jmkreditBeban		= 0;
				$totalbebanK			= 0;

	      foreach($detail_jurnal1 as $bebanDetail){

					$jmdebetBeban		= $jmdebetBeban + $bebanDetail->jumlah_debet;
					$jmkreditBeban	= $jmkreditBeban + $bebanDetail->jumlah_kredit;
					$totalsBeban		= $jmdebetBeban - $jmkreditBeban;
					$total1					= $jmdebetBeban - $jmkreditBeban;
					$sumbebanK			= str_slug($totalsBeban);
					$totalbebanK		= $totalbebanK + $sumbebanK;

	        $bebanKantor   .= '<ul>';
	        $bebanKantor   .= '<li>'.$bebanDetail->perkiraan_akun.'<span class="float-right">Rp. '.number_format($sumbebanK, 2).'</li>';
	        $bebanKantor   .= '</ul>';
	      }

	      $bebanKantor             .= '</li>';
				$bebanKantor								.= '<div style="clear:both;"></div><li><span class="float-left"><strong>Jumlah '.$ner1->sub_klasifikasi.'</strong></span><span class="float-right"><strong>Rp .'.number_format($totalbebanK, 2).'</strong></span></li><div style="clear:both;"></div>' ;
    	}
		}

		/* -------------------------------------------- BEBAN PROGGRAM --------------------------------------- */
		$bebanProgram					= '';
		$totalbebanKP					= 0;
		$totalbebanK					= 0;
		$bebanProgramD				= $nsd->Aktivitas('beban program');

		$bebanProgram        	.= '<ul class="no_list_built">';
		foreach($bebanProgramD as $ner1){
			$totalbeban = 0;
			if($ner1->jumlah_debet > '0' || $ner1->jumlah_kredit > '0'){
				$bebanProgram      .= '<li><strong>'.$ner1->sub_klasifikasi.'</strong>';
				$detail_jurnal1    = MDetailJurnal::join('perkiraan','jurnal_detail.id_perkiraan', 'perkiraan.id')
													->whereBetween('jurnal_detail.date_jurnal', [$startMonth,$endMonth])
													->where('perkiraan.sub_klasifikasi', $ner1->sub_klasifikasi)->groupBy('perkiraan.id')->get();

				$jmdebetBebanP			= 0;
				$jmkreditBebanP		= 0;
				$totalbebanKP			= 0;

				foreach($detail_jurnal1 as $bebanDetail){

					$jmdebetBebanP		= $jmdebetBebanP + $bebanDetail->jumlah_debet;
					$jmkreditBebanP		= $jmkreditBebanP + $bebanDetail->jumlah_kredit;
					$totalsBebanP			= $jmdebetBebanP - $jmkreditBebanP;

					$sumbebanP				= str_slug($totalsBebanP);
					$totalbebanKP			= $totalbebanKP + $sumbebanP;

					$bebanProgram  .= '<ul>';
					$bebanProgram  .= '<li>'.$bebanDetail->perkiraan_akun.'<span class="float-right">Rp. '.number_format($sumbebanP, 2).'</li>';
					$bebanProgram  .= '</ul>';
				}

				$bebanProgram    .= '</li>';
				$bebanProgram		.= '<div style="clear:both;"></div><li><span class="float-left"><strong>Jumlah '.$ner1->sub_klasifikasi.'</strong></span><span class="float-right"><strong>Rp .'.number_format($totalbebanKP, 2).'</strong></span></li><div style="clear:both;"></div>' ;
			}
		}
		$totalBebanya					= str_slug($totalbebanKP) + str_slug($totalbebanK);
		$total_pendapatan_tidak_terikat		= $totalBebanya - str_slug($totalb) ;
		$total_tdk_terikat		= str_slug($total_pendapatan_tidak_terikat);


		/* -------------------------------------------- PENDAPATAN TERIKAT TEMPORER --------------------------------------- */
		$p_permanen						= $nsd->Aktivitas('Pendapatan Terikat Temporer');

		$permanen							= '';
		$totalPpermanen				= 0;
		$totalPb_permanen			= 0;

		$permanen		        	.= '<ul class="no_list_built">';
		foreach($p_permanen as $ner1){
			$totalbeban = 0;
			if($ner1->jumlah_debet > '0' || $ner1->jumlah_kredit > '0'){
				$permanen		      .= '<li><strong>'.$ner1->sub_klasifikasi.'</strong>';
				$detail_jurnal1    = MDetailJurnal::join('perkiraan','jurnal_detail.id_perkiraan', 'perkiraan.id')
													->whereBetween('jurnal_detail.date_jurnal', [$startMonth,$endMonth])
													->where('perkiraan.sub_klasifikasi', $ner1->sub_klasifikasi)->groupBy('perkiraan.id')->get();
			//	return json_encode($detail_jurnal1);
				$jmdebetpermanen					= 0;
				$jmkreditpermanen					= 0;
				$totalsBebanPermanen			= 0;
				$totalPpermanen						= 0;

				foreach($detail_jurnal1 as $bebanDetail){

					$jmdebetpermanen				= $jmdebetpermanen + $bebanDetail->jumlah_debet;
					$jmkreditpermanen				= $jmkreditpermanen + $bebanDetail->jumlah_kredit;
					$totalsBebanPermanen		= $jmdebetpermanen - $jmkreditpermanen;

					$sumPermanen						= str_slug($totalsBebanPermanen);
					$totalPpermanen					= $totalPpermanen + $sumPermanen;

					$permanen			  .= '<ul>';
					$permanen			  .= '<li>'.$bebanDetail->perkiraan_akun.'<span class="float-right">Rp. '.number_format($sumPermanen, 2).'</li>';
					$permanen			  .= '</ul>';
				}

				$permanen			    .= '</li>';
				$permanen					.= '<div style="clear:both;"></div><li><span class="float-left"><strong>Jumlah '.$ner1->sub_klasifikasi.'</strong></span><span class="float-right"><strong>Rp .'.number_format($totalPpermanen, 2).'</strong></span></li><div style="clear:both;"></div>' ;
			}
		}

		/* -------------------------------------------- BEBAN WAKAF --------------------------------------- */
		$b_permanen										= $nsd->Aktivitas('beban titipan');

		$b_permanen_t									= '';

		$b_permanen_t				        	.= '<ul class="no_list_built">';
		foreach($b_permanen as $ner1){
			$totalbeban = 0;
			if($ner1->jumlah_debet > '0' || $ner1->jumlah_kredit > '0'){
				$b_permanen_t				      .= '<li><strong>'.$ner1->sub_klasifikasi.'</strong>';
				$detail_jurnal1    				= MDetailJurnal::join('perkiraan','jurnal_detail.id_perkiraan', 'perkiraan.id')
																	->whereBetween('jurnal_detail.date_jurnal', [$startMonth,$endMonth])
																	->where('perkiraan.sub_klasifikasi', $ner1->sub_klasifikasi)->groupBy('perkiraan.id')->get();

				$jmdebetb_permanen					= 0;
				$jmkreditb_permanen					= 0;
				$totalsB_Permanen						= 0;
				$totalPb_permanen						= 0;

				foreach($detail_jurnal1 as $bebanDetail){

					$jmdebetb_permanen			= $jmdebetb_permanen + $bebanDetail->jumlah_debet;
					$jmkreditb_permanen			= $jmkreditb_permanen + $bebanDetail->jumlah_kredit;
					$totalsB_Permanen				= $jmdebetb_permanen - $jmkreditb_permanen;

					$sumb_Permanen					= str_slug($totalsB_Permanen);
					$totalPb_permanen				= $totalPb_permanen + $sumb_Permanen;

					$b_permanen_t					  .= '<ul>';
					$b_permanen_t					  .= '<li>'.$bebanDetail->perkiraan_akun.'<span class="float-right">Rp. '.number_format($sumb_Permanen, 2).'</li>';
					$b_permanen_t					  .= '</ul>';
				}

				$b_permanen_t					    .= '</li>';
				$b_permanen_t							.= '<div style="clear:both;"></div><li><span class="float-left"><strong>Jumlah '.$ner1->sub_klasifikasi.'</strong></span><span class="float-right"><strong>Rp .'.number_format($totalPb_permanen, 2).'</strong></span></li><div style="clear:both;"></div>' ;
			}
		}
		$totalpendTemporer						= str_slug($totalPpermanen) - str_slug($totalPb_permanen);
		$totalTemporer								= number_format($totalpendTemporer);

		/* -------------------------------------------- BEBAN WAKAF --------------------------------------- */
		$p_wakaf											= $nsd->Aktivitas('beban wakaf');

		$wakaf_t											= '';
		$totals_wakaf2								= 0;
		$wakaf_t						        	.= '<div style="clear:both;"></div><ul class="no_list_built">';
		foreach($p_wakaf as $ner1){
			$totalbeban = 0;
			if($ner1->jumlah_debet > '0' || $ner1->jumlah_kredit > '0'){
				$wakaf_t						      .= '<li><strong>'.$ner1->sub_klasifikasi.'</strong>';
				$detail_jurnal1    				= MDetailJurnal::join('perkiraan','jurnal_detail.id_perkiraan', 'perkiraan.id')
																	->whereBetween('jurnal_detail.date_jurnal', [$startMonth,$endMonth])
																	->where('perkiraan.sub_klasifikasi', $ner1->sub_klasifikasi)->groupBy('perkiraan.id')->get();

				$debetWakaf									= 0;
				$kreditWakaf								= 0;
				$totalsB_Permanen						= 0;
				$totals_wakaf								= 0;
				$totals_wakaf2							= 0;

				foreach($detail_jurnal1 as $bebanDetail){

					$debetWakaf							= $debetWakaf + $bebanDetail->jumlah_debet;
					$kreditWakaf						= $kreditWakaf + $bebanDetail->jumlah_kredit;
					$totals_wakaf						= $debetWakaf - $kreditWakaf;

					$sum_wakaf							= str_slug($totals_wakaf);
					$totals_wakaf2					= $totals_wakaf2 + $sum_wakaf;

					$wakaf_t							  .= '<ul>';
					$wakaf_t							  .= '<li>'.$bebanDetail->perkiraan_akun.'<span class="float-right">Rp. '.number_format($sum_wakaf, 2).'</li>';
					$wakaf_t							  .= '</ul>';
				}

				$wakaf_t							    .= '</li>';
				$wakaf_t									.= '<div style="clear:both;"></div><li><span class="float-left"><strong>Jumlah '.$ner1->sub_klasifikasi.'</strong></span><span class="float-right"><strong>Rp .'.number_format($totals_wakaf2, 2).'</strong></span></li><div style="clear:both;"></div>' ;
			}
		}

		$total_kenaikan								= str_slug($total_tdk_terikat) + str_slug($totalTemporer) + str_slug($totals_wakaf2);

		$data		= [
			'periode'							=> $periode,
			'html'								=> $html,
			'bebanKantor'					=> $bebanKantor,
			'bebanProgram'				=> $bebanProgram,
			'total_tdk_terikat'		=> number_format($total_tdk_terikat,2),
			'permanen'						=> $permanen,
			'b_permanen_t'				=> $b_permanen_t,
			'totalTemporer'				=> $totalTemporer,
			'wakaf_t'							=> $wakaf_t,
			'total_kenaikan'			=> number_format($total_kenaikan,2),
		];

		return response()->json($data);
		return view('finance.jurnal.search_neraca_saldo', compact('neraca','totaldebet','sumdebet','sumkredit', 'nsd', 'startMonth', 'endMonth', 'Month'));
	}
}
