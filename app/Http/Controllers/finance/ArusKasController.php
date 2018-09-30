<?php

namespace App\Http\Controllers\finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\MJurnal;
use App\Models\MDetailJurnal;
use App\Models\MAlurKas;
use App\Models\MPerkiraan;
use App\Models\TmpJurnal;

use App\Classes\AutoNumber;

use DB;
use Carbon;

class ArusKasController extends Controller
{
    public function GetSaldo($id)
    {
        //$total_saldo            = 0;
        $saldos                 = MPerkiraan::join('jurnal_detail','perkiraan.id', 'jurnal_detail.id_perkiraan')
                                ->where('perkiraan.id',$id)->get();
        $total_saldo            = $saldos->sum('jumlah_debet') - $saldos->sum('jumlah_kredit');


        return response()->json($total_saldo);
    }
    public function create()
    {
        $table					= "arus_kas";
        $primary				= "no_bukti";
        $prefix					= "KAS";
        $nokas  				= Autonumber::autonumber($table,$primary,$prefix);

        $table1					= "jurnal_umum";
        $primary1				= "no_jurnal";
        $prefix1				= "JU";
        $nojurnal			= Autonumber::autonumber($table1,$primary1,$prefix1);

        $akun                   = MPerkiraan::where('perkiraan_akun', 'LIKE','%Bank%')->orWhere('perkiraan_akun','LIKE','%kas%')
                                ->select('id','perkiraan_akun')
                                ->get();

        $data     = [
            'akun'              => $akun,
            'nokas'             => $nokas,
            'nojurnal'          => $nojurnal
        ];
        return response()->json($data);
    }
    public function store(Request $request)
	{
        $aruskas                = new MAlurKas;
        $aruskas->no_bukti      = $request->no_bukti;
        $aruskas->tgl_kas       = $request->tgl_kas;
        $aruskas->sumber_dana   = $request->sumber_dana;
        $aruskas->keterangan    = $request->keterangan;
        $aruskas->jumlah_kas    = $request->jumlah_kas;
        $aruskas->tipe_kas      = $request->tipe_kas;

        $aruskas->save();

		$jurnal					= new MJurnal;

		$jurnal->no_jurnal		= $request->nojurnal;
		$jurnal->info			= $request->keterangan;
		$jurnal->so_id			= 0;
		$jurnal->user_id		= \Auth::user()->id;
		$jurnal->date_added		= $request->tgl_kas;

		$jurnal->save();

        $no_jurnals             = $jurnal->no_jurnal;
		$id_jurnal				= $jurnal->id;
        $tgl_jurnal				= $request->tgl_kas;
        $akun_type              = $request->akun_type;

        if($request->tipe_kas == 'Kas Masuk'){
            /* insert to debet */

            /* insert to kredit */
            foreach ($request->input('data') as $key => $v){
                $detailjurnal []  = [
                            'jurnal_id' => $id_jurnal,
                            'id_perkiraan' => $v['id'],
                            'jumlah_debet'		=>0 ,
                            'jumlah_kredit' =>$v['jumlah_debet'],
                            'date_jurnal' => $tgl_jurnal
                ];
            }

            MDetailJurnal::insert($detailjurnal);
            $detail_jurnal2                 = new MDetailJurnal;
            $detail_jurnal2->jurnal_id      = $id_jurnal;
            $detail_jurnal2->id_perkiraan   = $request->akun_type;
            $detail_jurnal2->jumlah_debet   = $request->jumlah_kas;
            $detail_jurnal2->jumlah_kredit  = 0;
            $detail_jurnal2->date_jurnal    = $request->tgl_kas;

            $detail_jurnal2->save();


        }elseif($request->tipe_kas == 'Kas Keluar'){
            foreach ($request->input('data') as $key => $v){
                $detailjurnal []  = [
                            'jurnal_id' => $id_jurnal,
                            'id_perkiraan' => $v['id'],
                            'jumlah_debet'		=>$v['jumlah_debet'] ,
                            'jumlah_kredit' =>0,
                            'date_jurnal' => $tgl_jurnal
                ];
            }

            MDetailJurnal::insert($detailjurnal);

            $detail_jurnal2                 = new MDetailJurnal;
            $detail_jurnal2->jurnal_id      = $id_jurnal;
            $detail_jurnal2->id_perkiraan   = $request->akun_type;
            $detail_jurnal2->jumlah_debet   = 0;
            $detail_jurnal2->jumlah_kredit  = $request->jumlah_kas;
            $detail_jurnal2->date_jurnal    = $request->tgl_kas;

            $detail_jurnal2->save();

             /* insert to debet */
             /*
             $tmp                    = new TmpJurnal;

             $tmp->kode_akun                     = $request->akun_type;
             $tmp->debet                         = $request->jumlah_kas;
             $tmp->kredit                        = 0;
             $tmp->no_jurnal                     = $id_jurnal;

             $tmp->save();


             // if($tmp->no_jurnal = $no_jurnals){
                  $detail_jurnal2                 = new MDetailJurnal;
                  $detail_jurnal2->jurnal_id      = $id_jurnal;
                  $detail_jurnal2->id_perkiraan   = $tmp->kode_akun;
                  $detail_jurnal2->jumlah_debet   = $tmp->debet;
                  $detail_jurnal2->jumlah_kredit  = 0;
                  $detail_jurnal2->date_jurnal    = $tmp->created_at;

                  $detail_jurnal2->save();
                  $d_delte       = TmpJurnal::FindOrFail($tmp->id);
                  $d_delte->delete();
             // }
             */


        }





       return response()->json($detailjurnal);
    }
}
