<?php

namespace App\Http\Controllers\jurnal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

class BukuBesarController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
   	public function BukuBesar()
	{
		$datenow 						= \Carbon\Carbon::today()->subDays(30);

		$det							= MDetailJurnal::join('jurnal_umum', 'jurnal_detail.jurnal_id', 'jurnal_umum.id')
										->join('perkiraan','jurnal_detail.id_perkiraan', 'perkiraan.id')
										->where('jurnal_umum.date_added', '>=', $datenow)
										->select('jurnal_umum.created_at as date_jurnal','jurnal_umum.id as jur_id','jurnal_umum.info','jurnal_detail.jumlah_debet', 'jurnal_detail.jumlah_kredit','jurnal_detail.id_perkiraan as idakun','jurnal_detail.created_at as date_detail','perkiraan.perkiraan_akun as nama_akun', 'perkiraan.kode_akun')
										->paginate(100);

		$debets							= new JurnalClass();

		$jurnal = $det->groupBy(function ($akun) {
			return $akun->idakun;
		})->all();

		$html	= '';

		foreach($jurnal as $name => $akun){

			$nama_akuns		= $debets->GetnameAkunId($name) ;
			$kode			= $debets->GetKodeAkun($name) ;
			$html	.= '<div class="form-group"><label>Nama Akun  : ' . $nama_akuns.'</label><label class="pull-right">Kode Akun : '.$kode.'</label>';

			$jun			= MDetailJurnal::join('jurnal_umum', 'jurnal_detail.jurnal_id', 'jurnal_umum.id')->select('jurnal_detail.jumlah_debet','jurnal_detail.id as iddetail', 'jurnal_detail.jumlah_kredit','jurnal_detail.id_perkiraan', 'jurnal_umum.date_added as jun_date','jurnal_umum.info')
								->where('jurnal_umum.date_added', '>=', $datenow)
								->where('id_perkiraan',$name)->get();
					$html	.= '<table class="table table-condensed">';
					$html	.= '<thead style="color:#ff0000;"><tr>';
					$html	.= '<th width="150" class="text-center">Tanggal</th><th width="200" class="text-center">Keterangan</th><th class="text-center" width="200">Debet</th><th width="200" class="text-center">Kredit</th>';
					$html	.= '</tr></thead>';
					$html	.= '<tbody>';
				$jmdebet		= 0;
				$jmkredit		= 0;
				foreach($jun as $d){
					$jmdebet	= $jmdebet + $d->jumlah_debet;
					$jmkredit	= $jmkredit + $d->jumlah_kredit;
					$total		= $jmdebet - $jmkredit;
					$html	.= '<tr>';

					$html	.= '<td>'.date('d M Y', strtotime($d->jun_date)).'</td>';
					$html	.= '<td>'.$d->info.'</td>';
					if($d->jumlah_debet === 0){
					$html	.= '<td>-</td>';
					}else{
					$html	.= '<td>Rp. '.number_format($d->jumlah_debet, 2).'</td>';
					}
					if($d->jumlah_kredit === 0){
						$html	.= '<td>-</td>';
					}else{
						$html	.= '<td>Rp. '.number_format($d->jumlah_kredit, 2).'</td>';
					}
				}
					$html	.= '</tr></tbody>';
					$html	.= '<tfoot><tr>';
					$html	.= '<th>'. $jmdebet.'';
					$html	.= '</tr></tfoot>';
					$html	.= '</table>';
			$html	.= '<div class="clearfix"></div></div>';

		}

		return view('finance.jurnal.buku_besar', compact('html'));
	}

	public function SearchBukuBesar(Request $request)
	{
		$start_date = date('Y-m-d', strtotime($request->start));
		$end_date = date('Y-m-d', strtotime($request->end));

		$startMonth				= $start_date;
		$endMonth				= $end_date."+1 day";
		$det						= MDetailJurnal::join('jurnal_umum', 'jurnal_detail.jurnal_id', 'jurnal_umum.id')
									->join('perkiraan','jurnal_detail.id_perkiraan', 'perkiraan.id')
									->whereBetween('jurnal_umum.date_added',[$startMonth,$endMonth])
									->select('jurnal_umum.created_at as date_jurnal','jurnal_umum.id as jur_id','jurnal_umum.info','jurnal_detail.jumlah_debet', 'jurnal_detail.jumlah_kredit','jurnal_detail.id_perkiraan as idakun','jurnal_detail.created_at as date_detail','perkiraan.perkiraan_akun as nama_akun', 'perkiraan.kode_akun')
									->paginate(100);
		$debets					= new JurnalClass();
		$perkiaraan				= new PerkiraanClass();

		$html					= '';

		$jurnal = $det->groupBy(function ($akun) {
			return $akun->idakun;
		})->all();

		$date_a					= date('d M Y', strtotime($startMonth));
		$date_b					= date('d M Y', strtotime($end_date));

		foreach($jurnal as $name => $akun){

			$nama_akuns		= $debets->GetnameAkunId($name) ;
			$kode			= $debets->GetKodeAkun($name) ;
			$klasifikasi	= $perkiaraan->GetKlasifikasi($name);
			if($klasifikasi == 'Kewajiban' || $klasifikasi == 'Pendapatan' || $klasifikasi == 'Modal'){
				$klasi		= 'Surplus Pada Kredit';
			}else{
				$klasi		= 'Surplus Pada Debet';
			}
			$html	.= '<div class="row"><div class="col-md-6"><label><strong>Nama Akun  : ' . $nama_akuns.'</strong></label></div><div class="col-md-6"><label class="float-right"><strong>Kode Akun : '.$kode.'</strong></label></div></div>';
				$jun			= MDetailJurnal::join('jurnal_umum', 'jurnal_detail.jurnal_id', 'jurnal_umum.id')->select('jurnal_detail.jumlah_debet','jurnal_detail.id as iddetail', 'jurnal_detail.jumlah_kredit','jurnal_detail.id_perkiraan', 'jurnal_umum.date_added as jun_date','jurnal_umum.info')
								->whereBetween('jurnal_umum.date_added',[$startMonth,$endMonth])
								->where('id_perkiraan',$name)->get();
					$html	.= '<table class="table">';
					$html	.= '<thead style="color:#001a66;"><tr>';
					$html	.= '<th width="150" class="text-center">Tanggal</th><th width="200" class="text-center">Keterangan</th><th class="text-center" width="200">Debet</th><th width="200" class="text-center">Kredit</th>';
					$html	.= '</tr></thead>';
					$html	.= '<tbody>';
				$jmdebet		= 0;
				$jmkredit		= 0;
				foreach($jun as $d){
					$jmdebet	= $jmdebet + str_slug($d->jumlah_debet);
					$jmkredit	= $jmkredit + str_slug($d->jumlah_kredit);
					$total		= str_slug($jmdebet) - str_slug($jmkredit);
					$total		= str_slug($total);
					$html	.= '<tr>';

					$html	.= '<td class="text-center">'.date('d M Y', strtotime($d->jun_date)).'</td>';
					$html	.= '<td>'. str_limit(strip_tags($d->info), $limit = 50, $end = '...').'</td>';
					if($d->jumlah_debet === 0){
					$html	.= '<td class="text-center">-</td>';
					}else{
					$html	.= '<td class="text-center">Rp. '.number_format($d->jumlah_debet, 2).'</td>';
					}
					if($d->jumlah_kredit === 0){
						$html	.= '<td class="text-center">-</td>';
					}else{
						$html	.= '<td class="text-center">Rp. '.number_format($d->jumlah_kredit, 2).'</td>';
					}
				}
					$html	.= '</tr></tbody>';
					$html	.= '<tfoot><tr>';
					$html	.= '<th colspan="2">Total</th>';
					$html	.= '<th class="text-center">Rp. '. number_format($jmdebet, 2).'</th>';
					$html	.= '<th class="text-center">Rp. '. number_format($jmkredit, 2).'</th>';
					$html	.= '</tr>';
					$html	.= '<tr style="background-color:#001a66;color:#fff;">';
					$html	.= '<th colspan="3">'.$klasi.'</th>';
					$html	.= '<th class="text-center">Rp. '. number_format($total, 2).'</th>';
					$html	.= '</tr></tfoot>';
					$html	.= '</table>';
			$html	.= '<div class="clearfix"></div></div><br />';

		}
		$data	=[
			'date_a'			=> $date_a,
			'date_b'			=> $date_b,
			'html'				=> $html

		];

		return response()->json($data);
		return view('finance.jurnal.search_buku_besar', compact('html'));
	}
}
