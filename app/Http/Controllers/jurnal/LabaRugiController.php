<?php

namespace App\Http\Controllers\jurnal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Classes\JurnalClass;
use App\Classes\PerkiraanClass;

class LabaRugiController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    public function LabaRugi()
	{
		$startMonth					= 0;
		$endMonth					= 0;

		$perkiraan					= new PerkiraanClass();
		$jurnal						= new JurnalClass();

		$lb							= $perkiraan->SeacrhName();
		$beban						= $jurnal->GetBebanBiaya($startMonth,$endMonth);
		$sumdebet					= $beban->sum('jumlah_debet');
		return view('finance.jurnal.lap_keuangan', compact('perkiraan','jurnal','beban', 'sumdebet'));
	}
	public function LabaRugiSearch(Request $request)
	{
		$start_date = date('Y-m-d', strtotime($request->start));
		$end_date = date('Y-m-d', strtotime($request->end));

		$startMonth					= $start_date;
		$endMonth					= $end_date."+1 day";
		$Month						= $end_date;
		$periode						= date('d M Y', strtotime($Month));

		$perkiraan				= new PerkiraanClass();
		$jurnal						= new JurnalClass();
		$beban						= $perkiraan->BiayaBeban($startMonth,$endMonth);
		$idperk						= $beban->pluck('id');
		$sumdebet					= $beban->sum('jumlah_debet');

		$Jkredit					= 0;
		$nama_akun					= '';
		$t_pendapatan			= '';
		$total_pendatan		= 0;
		foreach($perkiraan->SeacrhName2($startMonth, $endMonth) as $d){
			$Jkredit				= $Jkredit + $d->jumlah_kredit;
			$nama_akun			= $d->perkiraan_akun;
			$total_pendatan	=  $total_pendatan + $d->total;
			$t_pendapatan		.= '<tr style="border:none">';
			$t_pendapatan		.= '<td>'. $d->perkiraan_akun .'</td>';
			$t_pendapatan		.= '<td>'. number_format($d->total, 2) .'</td>';
			$t_pendapatan		.= '</tr>';

		}

		$penyesuainTotal		= 0;
		foreach($perkiraan->SeacrhPendapatanAJP($startMonth,$endMonth) as $ajpp){
			$penyesuainTotal	= $penyesuainTotal + $ajpp->ajp_kredit;
		}
		$totalPendapatan1		= $total_pendatan;

		$jml_beban					= 0;
		$ajpbeban					= 0;
		$ajpdebets					= 0;
		$jmlkredit					= 0;
		$perkiraanid 				= '';
		$namakunajp 				= '';
		$jmlajp1					= 0;

		$table2							= '';

		foreach($beban as $e){
			$perkiraanid				.= $e->id ;
			$namakunajp					.= $e->perkiraan_akun ;
			$jmlkredit					= $jmlkredit + $e->jumlah_debet;
			$jml_beban					= $jml_beban + $e->total_beban;
			$debet							= $e->jumlah_debet;

			$table2						.= '<tr style="border:none">';
			if($e->total_beban > 0){
				$table2					.= '<td>'.$e->perkiraan_akun .'</td>';
				$table2					.= '<td>';
				$bebantotaldebet		= 0;
				foreach($jurnal->ajpbyakun($e->id, $startMonth, $endMonth) as $bebans){
					$ajpbeban			= $ajpbeban + $bebans->ajp_debet;
					$bebantotaldebet	= $e->jumlah_debet + $bebans->ajp_debet;
					$jmlajp1			= $jmlajp1 + $bebans->ajp_debet;
				}
				if($bebantotaldebet > 0){
					$table2				.= 'Rp. '. number_format($bebantotaldebet, 2).'';
				}else{
					$table2				.= 'Rp. '. number_format($e->total_beban, 2) .'';
				}
				$table2					.= '</td>';
			}
			$table2						.= '</tr>';
		}

		$jmlajp							= 0;
		$table3							= '';
		if($jurnal->OnlyAjp($idperk,$startMonth, $endMonth)->count() > 0){
			foreach($jurnal->OnlyAjp($idperk,$startMonth, $endMonth) as $detajp){
				$ss						= $jmlajp1;
				$jmlajp					= $jmlajp + $detajp->ajp_debet;
				$totalpenyesuaian		= $ss + $jmlajp;

				$table3					.= '<tr style="border:none">';
				$table3					.= '<td>'.$detajp->perkiraan_akun .'</td>';
				$table3					.= '<td>Rp. ' . $detajp->ajp_debet .'</td>';
				$table3					.= '</tr>';
			}
			$sublaba					= $totalpenyesuaian + $jml_beban;
		}else{
			$sublaba					= $jml_beban + 0;
		}
		$laba		= $sublaba - $totalPendapatan1;
		$data	= [
			'nama_akun'				=> $nama_akun,
			'totalPendapatan1'		=> $totalPendapatan1,
			't_pendapatan'				=> $t_pendapatan,
			'periode'				=> $periode,
			'table2'				=> $table2,
			'sublaba'				=> $sublaba,
			'laba'					=> str_slug($laba),
			'table3'				=> $table3
		];
		return response()->json($data);
		return view('finance.jurnal.searching_laba', compact('perkiraan','jurnal','beban', 'sumdebet', 'startMonth', 'endMonth','idperk', 'Month'));
	}
}
