<?php

namespace App\Classes;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

use App\Models\MDetailJurnal;
use App\Models\MJurnal;
use App\Models\MPerkiraan;
use App\Models\MDetailAjp;

use DB;
class JurnalClass {

	public function GetDebet($id)
	{
		return MDetailJurnal::where('jurnal_id', $id)->first()->jumlah_debet;
	}
	public function JurnalDetail($q)
	{
		if($q > 0){
				$j_detail = MDetailJurnal::join('jurnal_umum', 'jurnal_detail.jurnal_id', 'jurnal_umum.id')
									->join('perkiraan', 'jurnal_detail.id_perkiraan', 'perkiraan.id')
									->where('jurnal_detail.date_jurnal', 'LIKE', '%' . $q .'%')
									->select('jurnal_detail.id', 'jurnal_detail.jumlah_debet', 'jurnal_detail.jumlah_kredit', 'jurnal_detail.jurnal_id',
											'jurnal_umum.id as id_jurnal', 'jurnal_umum.date_added', 'jurnal_umum.no_jurnal', 'jurnal_umum.info', 'perkiraan.perkiraan_akun',
		                  'perkiraan.kode_akun', 'perkiraan.id as id_perkiraan')
									->paginate(10);
		}else{
			$j_detail = MDetailJurnal::join('jurnal_umum', 'jurnal_detail.jurnal_id', 'jurnal_umum.id')
								->join('perkiraan', 'jurnal_detail.id_perkiraan', 'perkiraan.id')
								->select('jurnal_detail.id', 'jurnal_detail.jumlah_debet', 'jurnal_detail.jumlah_kredit', 'jurnal_detail.jurnal_id',
										'jurnal_umum.id as id_jurnal', 'jurnal_umum.date_added', 'jurnal_umum.no_jurnal', 'jurnal_umum.info', 'perkiraan.perkiraan_akun',
										'perkiraan.kode_akun', 'perkiraan.id as id_perkiraan')
								->paginate(10);
		}
		return $j_detail;
	}
	public function GetKredit($id)
	{
		return MDetailJurnal::where('jurnal_id', $id)->first()->jumlah_kredit;
	}
	public function GetTglByiD($id)
	{
		return MJurnal::where('id', $id)->first()->created_at;
	}
	public function GetInfoByiD($id)
	{
		return MJurnal::where('id', $id)->first()->info;
	}
	public function GetnameAkunId($id)
	{
		$akun	= MPerkiraan::where('id', $id)->first();
		if(!empty($akun)){
			return $akun->perkiraan_akun;
		}
	}
	public function klasifikasiBySub($sub)
	{
		$akun	= MPerkiraan::where('sub_klasifikasi', $sub)->first();
		if(!empty($akun)){
			return $akun->perkiraan_akun;
		}
	}

	public function Sub_klasifikasi($id)
	{
		$akun	= MPerkiraan::where('id', $id)->first();
		if(!empty($akun)){
			return $akun->sub_klasifikasi;
		}
	}
	public function GetKodeAkun($id)
	{
		return MPerkiraan::where('id', $id)->first()->kode_akun;
	}
	public function GetKlasifikasi($id)
	{
		return MPerkiraan::where('id', $id)->first()->klasifikasi;
	}
	public function GetAJP($kode_akun)
	{
		$ajp			= MDetailAjp::where('id_akun', $kode_akun)->get();
		return $ajp;
	}
	public function GetDebets($id)
	{
		$os	= collect($id);
		$jurnal 		= MDetailJurnal::whereIn('id_perkiraan', [$id])->get();
		return $jurnal;
	}
	public function SumDebet($id, $startMonth, $endMonth)
	{
		$debet 			= MDetailJurnal::join('jurnal_umum', 'jurnal_detail.jurnal_id', 'jurnal_umum.id')->select('jurnal_detail.jumlah_debet','jurnal_detail.id as iddetail', 'jurnal_detail.jumlah_kredit','jurnal_detail.id_perkiraan', 'jurnal_umum.date_added as jun_date','jurnal_umum.info')
						->whereBetween('jurnal_umum.date_added',[$startMonth,$endMonth])
						->where('id_perkiraan',$id)->get();
		return $debet;
	}
	public function SumKredit($id)
	{
		$debet = MDetailJurnal::where('id_perkiraan', $id)->select(DB::raw('SUM(jumlah_debet) as debet'))->get();
		return $debet;
	}
	public function ajpbyakun($id, $startMonth, $endMonth)
	{
		$bebanajp					= MDetailAjp::join('perkiraan', 'detail_ajp.id_akun', 'perkiraan.id')
									->whereBetween('ajp_date',[$startMonth,$endMonth])
									->where('id_akun', 'LIKE', '%'.$id.'%')
									->select('perkiraan.id', 'perkiraan.perkiraan_akun', 'detail_ajp.ajp_debet')
									->groupBy('id_akun')
									->get();
		return $bebanajp;
	}
	public function ajpbyakun2($id, $startMonth, $endMonth)
	{
		$bebanajp					= MDetailAjp::join('perkiraan', 'detail_ajp.id_akun', 'perkiraan.id')
									->whereBetween('ajp_date',[$startMonth,$endMonth])
									->select('perkiraan.id', 'perkiraan.perkiraan_akun', 'detail_ajp.ajp_debet')
									->groupBy('id_akun')
									->get();
		return $bebanajp;
	}
	public function OnlyAjp($id,$startMonth, $endMonth)
	{
		$bebanajp					= MDetailAjp::join('perkiraan', 'detail_ajp.id_akun', 'perkiraan.id')
									->where('perkiraan.perkiraan_akun', 'LIKE', '%beban%')
									->whereNotIn('detail_ajp.id_akun', $id)
									->whereBetween('ajp_date',[$startMonth,$endMonth])
									->groupBy('perkiraan.id')
									->get();
		return $bebanajp;
	}
	public function GetPerkiraan($id, $startMonth, $endMonth)
	{
		$jun						= MDetailJurnal::join('jurnal_umum', 'jurnal_detail.jurnal_id', 'jurnal_umum.id')
									->join('perkiraan', 'jurnal_detail.id_perkiraan', 'perkiraan.id')
									->where('id_perkiraan',$id)
									->whereBetween('jurnal_umum.date_added',[$startMonth,$endMonth])
									->select('jurnal_detail.jumlah_debet','jurnal_detail.id as iddetail', 'jurnal_detail.jumlah_kredit','jurnal_detail.id_perkiraan', 'jurnal_umum.date_added as jun_date','jurnal_umum.info','perkiraan.perkiraan_akun as namanya_akun','perkiraan.kode_akun','perkiraan.klasifikasi')
									->get();
		return $jun;
	}
	public function GetPerkriaanAjp($id, $startMonth,$endMonth)
	{
		$ajp						= MDetailAjp::whereBetween('ajp_date',[$startMonth,$endMonth])
									->where('id_akun', $id)
									->select('ajp_debet','ajp_kredit', 'id_akun')
									->groupBy('id_akun')
									->get();
		return $ajp;
	}
	public function GetPerkriaanAjp2($id, $startMonth,$endMonth)
	{
		$ajp						= MDetailAjp::whereBetween('ajp_date',[$startMonth,$endMonth])
									->whereIn('id_akun', $id)
									->get();
		return $ajp;
	}
	public function GetBebanBiaya($startMonth,$endMonth)
	{
		$beban	= MPerkiraan::join('jurnal_detail', 'perkiraan.id', 'jurnal_detail.id_perkiraan')
				->whereBetween('jurnal_detail.date_jurnal',[$startMonth,$endMonth])
				->where('perkiraan.perkiraan_akun', 'LIKE', '%beban%')
				->select('perkiraan.id', 'perkiraan.perkiraan_akun', 'perkiraan.kode_akun as akun_kode', 'perkiraan.klasifikasi','jurnal_detail.jumlah_debet', 'jurnal_detail.jumlah_kredit')
				->orWhere('perkiraan.perkiraan_akun', 'LIKE', '%biaya%')
				->groupBy('perkiraan.id')
				->orderBy('perkiraan.id', 'asc')
				->get();
		return $beban;
	}
	public function NotHartaTetap($h_tetap,$startMonth,$endMonth)
	{
		$neraca	= MDetailJurnal::join('perkiraan','jurnal_detail.id_perkiraan', 'perkiraan.id')
				->where('.perkiraan.sub_klasifikasi', 'LIKE', '%'.$h_tetap.'%')
				->whereBetween('jurnal_detail.date_jurnal',[$startMonth,$endMonth])
				->select('jurnal_detail.jumlah_debet', 'jurnal_detail.jumlah_kredit','jurnal_detail.id_perkiraan as idakun','jurnal_detail.created_at as date_detail','perkiraan.perkiraan_akun as nama_akun', 'perkiraan.kode_akun')
				->orderBy('perkiraan.kode_akun','asc')
				->get();
		return $neraca;
	}
	public function ActivaBiayaPenyusutan($startMonth,$endMonth)
	{
		$assetTetap		= MPerkiraan::join('jurnal_detail', 'perkiraan.id','jurnal_detail.id_perkiraan')
						->leftjoin('detail_ajp', 'perkiraan.id','detail_ajp.id_akun')
						->whereBetween('jurnal_detail.date_jurnal',[$startMonth,$endMonth])
						->where('perkiraan.sub_klasifikasi', 'LIKE', '%Harta Tetap%')

						->select('jurnal_detail.jumlah_debet', 'jurnal_detail.jumlah_kredit','perkiraan.perkiraan_akun','perkiraan.id', 'detail_ajp.ajp_debet','detail_ajp.ajp_kredit')
						->groupBy('jurnal_detail.id_perkiraan')
						->orderBy('jurnal_detail.jumlah_debet', 'desc')
						->get();
		return $assetTetap;
	}
	public function Modal($id, $startMonth,$endMonth)
	{
		$modal			= MDetailJurnal::whereIn('id_perkiraan', $id)
						->whereBetween('jurnal_detail.date_jurnal',[$startMonth,$endMonth])
						->get();
		return $modal;
	}
	public function GetPrive($idprive, $startMonth,$endMonth)
	{
		$prive			= MDetailJurnal::whereIn('id_perkiraan', $idprive)
						->whereBetween('date_jurnal',[$startMonth,$endMonth])
						->get();
		return $prive;
	}
	public function GetKewajiban($startMonth,$endMonth)
	{
		$Hutang			= MPerkiraan::leftjoin('jurnal_detail', 'perkiraan.id','jurnal_detail.id_perkiraan')
						->leftjoin('detail_ajp', 'perkiraan.id','detail_ajp.id_akun')
						->where('perkiraan.klasifikasi', 'LIKE', 'kewajiban')
						//->where('perkiraan.klasifikasi', 'LIKE', 'Pengeluaran Operasional')
						->whereBetween('jurnal_detail.date_jurnal',[$startMonth,$endMonth])
						->orWhere(function ($query) use ($startMonth, $endMonth) {
						$query->whereBetween('detail_ajp.ajp_date', [$startMonth,$endMonth])
							->where('perkiraan.klasifikasi', 'LIKE', 'kewajiban');
							//->where('perkiraan.klasifikasi', 'LIKE', 'Pengeluaran Operasional');
						})
						->select('jurnal_detail.jumlah_debet', 'jurnal_detail.jumlah_kredit','perkiraan.perkiraan_akun','perkiraan.id','detail_ajp.ajp_debet', 'detail_ajp.ajp_kredit')
						->orderBy('jurnal_detail.jumlah_debet', 'desc')
						->get();
		return $Hutang;
	}
	public function NeracaSaldo($startMonth,$endMonth)
	{
		return MDetailJurnal::join('perkiraan','jurnal_detail.id_perkiraan', 'perkiraan.id')
										->whereBetween('jurnal_detail.date_jurnal',[$startMonth,$endMonth])
										->select('jurnal_detail.jumlah_debet', 'jurnal_detail.jumlah_kredit','jurnal_detail.id_perkiraan as idakun','jurnal_detail.created_at as date_detail','perkiraan.perkiraan_akun as nama_akun', 'perkiraan.kode_akun')
										->groupBy('perkiraan.perkiraan_akun')
										->orderBy('perkiraan.kode_akun','asc')
										->get();
	}
	public function GetAktivitas($sub,$startMonth,$endMonth)
	{
		return MDetailJurnal::join('perkiraan','jurnal_detail.id_perkiraan', 'perkiraan.id')
										->where('perkiraan.sub_klasifikasi', 'LIKE', $sub)
										->whereBetween('jurnal_detail.date_jurnal',[$startMonth,$endMonth])
										->select('jurnal_detail.jumlah_debet', 'jurnal_detail.jumlah_kredit','jurnal_detail.id_perkiraan as idakun','jurnal_detail.created_at as date_detail','perkiraan.perkiraan_akun as nama_akun', 'perkiraan.kode_akun')
										->groupBy('perkiraan.perkiraan_akun')
										->orderBy('perkiraan.kode_akun','asc')
										->get();
	}
	public function Aktivitas($sub)
	{
		$perkiraan          = MPerkiraan::join('jurnal_detail','perkiraan.id', 'jurnal_detail.id_perkiraan')->where('sub_klasifikasi', 'LIKE', $sub)->groupBy('sub_klasifikasi')->get();
		if(!empty($perkiraan)){
			return $perkiraan;
		}
	}
	public function JurnalByIdPerkiraan($id, $startMonth,$endMonth)
	{
		return MDetailJurnal::join('jurnal_umum', 'jurnal_detail.jurnal_id', 'jurnal_umum.id')
												->join('perkiraan', 'jurnal_detail.id_perkiraan', 'perkiraan.id')
												->select('jurnal_detail.jumlah_debet','jurnal_detail.id as iddetail', 'jurnal_detail.jumlah_kredit','jurnal_detail.id_perkiraan', 'jurnal_umum.date_added as jun_date','jurnal_umum.info','perkiraan.perkiraan_akun as namanya_akun')
												->whereBetween('jurnal_umum.date_added',[$startMonth,$endMonth])
												->where('id_perkiraan',$id)->get();
	}
}
