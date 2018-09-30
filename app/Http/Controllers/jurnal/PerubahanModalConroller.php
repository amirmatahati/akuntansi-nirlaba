<?php

namespace App\Http\Controllers\jurnal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Classes\JurnalClass;
use App\Classes\PerkiraanClass;

class PerubahanModalConroller extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    public function perubahanModal()
	{
		return view('finance.jurnal.perubahan_modal');
	}
	public function perubahanModalSearch(Request $request)
	{
		$start_date = date('Y-m-d', strtotime($request->start));
		$end_date = date('Y-m-d', strtotime($request->end));

		$startMonth					= $start_date;
		$endMonth					= $end_date."+1 day";
		$lapEnd						= $request->end;
		$periode					= date('d M Y', strtotime($lapEnd));

		$jurnal						= new JurnalClass();

		$perkiraan					= new PerkiraanClass();
		$idpendapatan 				= $perkiraan->SeacrhName();
		$idperkiraan 				= $perkiraan->GetAkunByid('modal');

		//$idprive	 				= $perkiraan->GetAkunByid('modal');
		$idprive	 				= $perkiraan->GetAkunByid('asset neto');

		$modal						= $jurnal->Modal($idperkiraan, $startMonth,$endMonth);
		//return json_encode($modal);
		$beban						= $perkiraan->BiayaBeban($startMonth,$endMonth);
		$prive						= $jurnal->GetPrive($idprive, $startMonth,$endMonth);

		//echo json_encode($prive);

		$idperk						= $beban->pluck('id');

		$Mkredit					= 0;
		$nama_akun					= '';
		foreach($modal as $d){
			$nama_akun				= $d->GetPerkiraan->perkiraan_akun;
			$Mkredit				= $Mkredit + $d->jumlah_kredit;
		}

		$Jkredit						= 0;
		$total_pendatan			= 0;
		foreach($perkiraan->SeacrhName2($startMonth, $endMonth) as $ddd){
			$Jkredit					= $Jkredit + $ddd->jumlah_kredit;
			$total_pendatan		=  $total_pendatan + $ddd->total;
		}
		$penyesuainTotal			= 0;
		foreach($perkiraan->SeacrhPendapatanAJP($startMonth, $endMonth) as $ajpp){
			$penyesuainTotal		= $penyesuainTotal + $ajpp->ajp_kredit;
		}
		$totalPendapatan1			= $penyesuainTotal + $total_pendatan;
		$jml_beban					= 0;
		$ajpbeban					= 0;
		$jmlkredit					= 0;
		$namakunajp 				='';
		$jmlajp1					= 0;
		foreach($beban as $e){
			$namakunajp					.= $e->perkiraan_akun ;
			$jmlkredit					= $jmlkredit + $e->jumlah_debet;
			$jml_beban					= $jml_beban + $e->total_beban;
			$debet						= $e->jumlah_debet;

			$bebantotaldebet			= 0;
			foreach($jurnal->ajpbyakun($e->id, $startMonth, $endMonth) as $bebans){
				$ajpbeban				= $ajpbeban + $bebans->ajp_debet;
				$bebantotaldebet		= $e->jumlah_debet + $bebans->ajp_debet;
				$jmlajp1				= $jmlajp1 + $bebans->ajp_debet;
			}
		}
		$jmlajp						= 0;
		$totalpenyesuaian						= 0;

		foreach($jurnal->OnlyAjp($idperk,$startMonth, $endMonth) as $detajp){
			$ss						= $jmlajp1;
			$jmlajp					= $jmlajp + $detajp->ajp_debet;
			$totalpenyesuaian		= $ss + $jmlajp;
		}
		$sublaba					= $totalpenyesuaian + $jml_beban;
		$laba						= $sublaba - $totalPendapatan1;

		$jmdebet 					= 0;
		$jmkredit 					= 0;
		$nostring					= 0;
		$prive_name					= '';
		$total						= 0;
		if($prive->count() > 0){
			foreach($prive as $prives){
				$prive_name			= $prives->GetPerkiraan->perkiraan_akun;
				$jmdebet			= $jmdebet + $prives->jumlah_debet;
				$jmkredit			= $jmkredit + $prives->jumlah_kredit;
				$total				= $jmdebet - $jmkredit;
				$nostring			= str_slug($total);
			}
		}

		$penambahan_modal			= str_slug($laba) - str_slug($nostring);
		$modal_akhir				= $Mkredit + str_slug($penambahan_modal);
		//echo json_encode($jml_beban);
		$data	= [
			'periode'				=> $periode,
			'nama_akun'				=> $nama_akun,
			'Mkredit'				=> str_slug($Mkredit),
			'laba'					=> str_slug($laba),
			'prive_name'			=> $prive_name,
			'total'					=> str_slug($total),
			'nostring'				=> $nostring,
			'penambahan_modal'		=> $penambahan_modal,
			'modal_akhir'			=> $modal_akhir

		];

		return response()->json($data);
		return view('finance.jurnal.search_perubahan_modal', compact('startMonth', 'endMonth', 'modal', 'beban', 'prive','perkiraan', 'jurnal','idperk', 'lapEnd'));
	}
}
