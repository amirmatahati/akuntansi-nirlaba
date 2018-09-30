<?php

namespace App\Classes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Models\MPerkiraan;
use DB;
class PerkiraanClass {

	public function AkunAll($search)
	{
		if(!$search){
			$akun		= MPerkiraan::paginate(5);
		}else{
			$akun		= MPerkiraan::where('perkiraan_akun', 'LIKE', '%'.$search. '%')->paginate(10);
		}
		return $akun;
	}
	public function GetPerkiraanByiD($id)
	{
		return MPerkiraan::where('id', $id)->get();
	}
	public function GetAkunByIdLoop($id)
	{
		return MPerkiraan::whereIn('id', $id)->get();
	}
	public function GetKlasifikasi($id)
	{
		return MPerkiraan::where('id', $id)->first()->klasifikasi;
	}
	public function SeacrhName()
	{
		$perkiraan				= MPerkiraan::join('jurnal_detail', 'perkiraan.id', 'jurnal_detail.id_perkiraan')
								->where('perkiraan_akun', 'LIKE', '%Pendapatan Jasa%')
								->select('perkiraan.id', 'perkiraan.perkiraan_akun', 'jurnal_detail.jumlah_kredit')
								->orderBy('perkiraan.id', 'asc')
								->get();
		return $perkiraan;
	}
	public function SeacrhName2($startMonth,$endMonth)
	{
			$perkiraan			= MPerkiraan::join('jurnal_detail', 'perkiraan.id', 'jurnal_detail.id_perkiraan')
								->where('klasifikasi', 'LIKE', 'Pendapatan')
								->whereBetween('jurnal_detail.date_jurnal',[$startMonth,$endMonth])
								//->orWhere('perkiraan.id', 0)
								->select('perkiraan.id', 'perkiraan.kode_akun', 'perkiraan.klasifikasi', 'perkiraan.perkiraan_akun', 'jurnal_detail.jumlah_kredit', DB::raw('SUM(jurnal_detail.jumlah_kredit) as total'))
                ->groupBy('jurnal_detail.id_perkiraan')
								->orderBy('perkiraan.id', 'asc')
								->get();
		return $perkiraan;
	}
	public function SeacrhPendapatanAJP($startMonth,$endMonth)
	{
		$perkiraan				= MPerkiraan::join('detail_ajp', 'perkiraan.id', 'detail_ajp.id_akun')
								->select('perkiraan.id', 'perkiraan.perkiraan_akun', 'detail_ajp.ajp_kredit')
								->where('perkiraan_akun', 'LIKE', '%pendapatan%')
								->whereBetween('detail_ajp.ajp_date',[$startMonth,$endMonth])
								->get();
		return $perkiraan;
	}
	public function PluckName()
	{
		return MPerkiraan::pluck('perkiraan_akun', 'id');
	}
	public function PluckBeban()
	{
		return MPerkiraan::where('perkiraan_akun', 'LIKE', '%beban%')->pluck('id');
	}
	public function SearchBeban()
	{
		return MPerkiraan::where('perkiraan_akun', 'LIKE', '%beban%')->get();
	}
	public function GetName()
	{
		return MPerkiraan::where('perkiraan_akun', 'LIKE', '%pendapatan%')->groupBy('id')->get();
	}
	public function GetAkunByid($name)
	{
		return MPerkiraan::where('sub_klasifikasi', 'LIKE', '%'.$name.'%')->pluck('id');
	}
	public function ByID($id, $startMonth, $endMonth)
	{
		$perkiraan			= MPerkiraan::join('jurnal_detail', 'perkiraan.id', 'jurnal_detail.id_perkiraan')
							->where('perkiraan.id', $id)
							->whereBetween('jurnal_detail.date_jurnal',[$startMonth,$endMonth])
							->select('perkiraan.id', 'perkiraan.perkiraan_akun', 'jurnal_detail.jumlah_debet')
							->groupBy('perkiraan.id')
							->get();
		return $perkiraan;
	}

	public function GetNameAkun($id)
	{
		return MPerkiraan::where('id', $id)->first()->perkiraan_akun;
	}
	public function GetKodeakun($id)
	{
		return MPerkiraan::where('id', $id)->first()->kode_akun;
	}
	public function GetiDakunByName($id)
	{
		return MPerkiraan::where('perkiraan_akun','LIKE', '%'.$id.'%')->first()->id;
	}
	public function GetKodeByName($id)
	{
		return MPerkiraan::where('perkiraan_akun','LIKE', '%'.$id.'%')->first()->kode_akun;
	}
	public function GetByName($id)
	{
		return MPerkiraan::where('perkiraan_akun','LIKE', '%'.$id.'%')->first()->perkiraan_akun;
	}
	public function GetKlasifikasiByName($id)
	{
		return MPerkiraan::where('perkiraan_akun','LIKE', '%'.$id.'%')->first()->klasifikasi;
	}
	public function BiayaBeban($startMonth,$endMonth)
	{
    /*
		return MPerkiraan::join('jurnal_detail', 'perkiraan.id', 'jurnal_detail.id_perkiraan')
							->whereBetween('jurnal_detail.date_jurnal',[$startMonth,$endMonth])
							->where('perkiraan.klasifikasi', 'LIKE', '%beban%')
							->orWhere('perkiraan.klasifikasi', 'LIKE', '%Pengeluaran Operasional')
							->orWhere('perkiraan.klasifikasi', 'LIKE', '%biaya%')
							->select('perkiraan.id', 'perkiraan.perkiraan_akun','jurnal_detail.jumlah_debet', 'jurnal_detail.jumlah_kredit')
							->groupBy('perkiraan.id')
							->get();
    */
    $perkiraan			= MPerkiraan::join('jurnal_detail', 'perkiraan.id', 'jurnal_detail.id_perkiraan')
              ->where('klasifikasi', 'LIKE', '%beban%')
              ->whereBetween('jurnal_detail.date_jurnal',[$startMonth,$endMonth])
              //->orWhere('perkiraan.id', 0)
              ->select('perkiraan.id', 'perkiraan.kode_akun', 'perkiraan.klasifikasi', 'perkiraan.perkiraan_akun', 'jurnal_detail.jumlah_kredit', DB::raw('SUM(jurnal_detail.jumlah_debet) as total_beban'))
              ->groupBy('jurnal_detail.id_perkiraan')
              ->orderBy('perkiraan.id', 'asc')
              ->get();
    return $perkiraan;
	}
	public function analisaBisnis($years)
	{
		return MPerkiraan::join('jurnal_detail', 'perkiraan.id', 'jurnal_detail.id_perkiraan')
							->where('date_jurnal','LIKE', '%'.date('M Y', strtotime($years)).'%')
							->orWhere('perkiraan.perkiraan_akun', 'LIKE', 'kas')
							->orWhere('perkiraan.perkiraan_akun', 'LIKE', 'bank')
							->orWhere('perkiraan.perkiraan_akun', 'LIKE', 'piutang usaha')
							->orWhere('perkiraan.perkiraan_akun', 'LIKE', 'persediaan')
							->select('perkiraan.id', 'perkiraan.perkiraan_akun','jurnal_detail.jumlah_debet', 'jurnal_detail.jumlah_kredit')
							->orWhere('perkiraan.perkiraan_akun', 'LIKE', 'harta lancar')
							->groupBy('perkiraan.id')
							->get();
	}
}
