<?php

namespace App\Http\Controllers\pdf;

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

use PDF;

class FinanceController extends Controller
{
    public function BukuBesar(Request $request)
	{
		$start			= $request->start;
		$end			= $request->end;
		
		$det						= MDetailJurnal::join('jurnal_umum', 'jurnal_detail.jurnal_id', 'jurnal_umum.id')
									->join('perkiraan','jurnal_detail.id_perkiraan', 'perkiraan.id')
									->whereBetween('jurnal_umum.date_added',[$start,$end])
									->select('jurnal_umum.created_at as date_jurnal','jurnal_umum.id as jur_id','jurnal_umum.info','jurnal_detail.jumlah_debet', 'jurnal_detail.jumlah_kredit','jurnal_detail.id_perkiraan as idakun','jurnal_detail.created_at as date_detail','perkiraan.perkiraan_akun as nama_akun', 'perkiraan.kode_akun')
									->paginate(100);
		$debets					= new JurnalClass();
		$perkiaraan				= new PerkiraanClass();
		$jurnal = $det->groupBy(function ($akun) {
			return $akun->idakun;
		})->all();
		
		
		$html	= '';
		$html			.= '<h3 class="text-center">Buku besar priode '.date('d M Y', strtotime($start)).' - '.date('d M Y', strtotime($end)).'</h3><br /><br />';
		foreach($jurnal as $name => $akun){
			
			$nama_akuns		= $debets->GetnameAkunId($name) ;
			$kode			= $debets->GetKodeAkun($name) ;
			$klasifikasi	= $perkiaraan->GetKlasifikasi($name);
			if($klasifikasi == 'Kewajiban' || $klasifikasi == 'Pendapatan' || $klasifikasi == 'Modal'){
				$klasi		= 'Surplus Pada Kredit';
			}else{
				$klasi		= 'Surplus Pada Debet';
			}
			$html	.= '<div class="form-group"><label>Nama Akun  : ' . $nama_akuns.'</label><label class="pull-right">Kode Akun : '.$kode.'</label>';
				$jun			= MDetailJurnal::join('jurnal_umum', 'jurnal_detail.jurnal_id', 'jurnal_umum.id')->select('jurnal_detail.jumlah_debet','jurnal_detail.id as iddetail', 'jurnal_detail.jumlah_kredit','jurnal_detail.id_perkiraan', 'jurnal_umum.date_added as jun_date','jurnal_umum.info')
								->whereBetween('jurnal_umum.date_added',[$start,$end])
								->where('id_perkiraan',$name)->get();
					$html	.= '<table class="table">';
					$html	.= '<thead style="color:#001a66;"><tr>';	
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
					$html	.= '<th colspan="2">Total</th>';
					$html	.= '<th class="text-center">Rp. '. number_format($jmdebet, 2).'</th>';
					$html	.= '<th class="text-center">Rp. '. number_format($jmkredit, 2).'</th>';
					$html	.= '</tr>';
					$html	.= '<tr style="background-color:#001a66;color:#fff;">';
					$html	.= '<th colspan="2">'.$klasi.'</th>';
					$html	.= '<th colspan="2" class="text-center">Rp. '. number_format(str_slug($total, 2)).'</th>';
					$html	.= '</tr></tfoot>';
					$html	.= '</table>';
			$html	.= '<div class="clearfix"></div></div>';
			
		}
		//return json_encode($html);
		return view('finance.print.buku_besar', compact('html'));
	}
}
