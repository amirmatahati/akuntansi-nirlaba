<?php

namespace App\Http\Controllers\pdf;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\MBrokersMember;
use App\Models\MDetailBroker;
use App\Models\MCostBrokerMember;
use App\Models\MComitSuccessFeeBroker;
use App\Models\MOpen_listing;
use App\Models\MBusinnesEntityBroker;
use App\Models\MDetailComiitFee;
use App\User;

use PDF;
use Carbon;

class PDFBrokerMember extends Controller
{
    public function index(Request $request,$id)
	{
		$data				= MDetailBroker::where('broker_id', $id)->get();
		$iduser				= MBrokersMember::where('id', $id)->first()->user_id;
		$listing			= MOpen_listing::where('broker_id', $id)->first()->name;
		$users				= User::where('id', $iduser)->get();
		$bussines_entity	= MBusinnesEntityBroker::where('broker_id', $id)->get();
		$commitfee			= MDetailComiitFee::where('broker_id', $id)->get();
		$now 				= Carbon\Carbon::now();
		
		foreach($data as $b){
			$company_name		= $b->BrokerCompany->name;
			$brand				= $b->BrokerBrand->brand;
			$pic_name			= $b->BrokerPIC->name;
			$pic_title			= $b->BrokerPIC->position;
			$pic_phone			= $b->BrokerPIC->phone;
			$pic_email			= $b->BrokerPIC->email;
			$modal_awal			= number_format($b->modal_awal);
			$type				= number_format($b->package_type);
			$royalty		 	= number_format($b->royalty_fee);
			$advers			 	= number_format($b->adverstising_fee);
			$roi				= $b->roi;
			$skala				= $b->BrokerCost->skala_nilai_investasi;
			$annual				= number_format($b->BrokerCost->annual_member_free);
			$surv				= number_format($b->BrokerCost->survey_fee);
			$no_broker			= $b->BrokerDetail->no_listing;
		}
		
		foreach($users as $user){
			$name_user			= $user->name;
			$user_title			= $user->PositionUser->name;
			$user_hp			= $user->hp;
			$user_email			= $user->email;
			$user_image			= $user->ttd;
		}
		
		$col_1 = "";
		foreach($bussines_entity as $bus){
			$bussines			= strtoupper($bus->name);
			$col_1		 		= $col_1.$bussines." ,";
		}
		foreach($commitfee as $commit){
			$commit1			= $commit->CommitDetail->name;
			$commit2			= $commit->nilai;
			$commit3			= $commit->CommitDetail->info;
		}
		
		PDF::SetHeaderData(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 034', PDF_HEADER_STRING);
		
		PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
		PDF::SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
		PDF::AddPage();
		
		PDF::Image(asset('img/logo2.jpg'),156,10,50,'');
			PDF::Cell(1);
			PDF::SetFont('Times','B','12');
			PDF::Image(asset('img/logo1.jpg'),5,10,60,'L');
			
		
		PDF::ln();
		PDF::SetFont('Times','B','10');
		PDF::Cell(25, 5, 'MEMBER LIST BROKER', 0,  'L');
		
		PDF::Cell(110);
		PDF::MultiCell(20, 5, 'Tgl.', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(50, 5, ': ' .$now->format('d, M Y'), 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln();
		PDF::Cell(25, 5, '(JASA PEMASARAN FRANCHISE, KEMITRAAN/LICENSE)', 0,  'L');
		
		PDF::Cell(110);
		PDF::MultiCell(20, 5, 'No. Listing', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(50, 5, ': ' .$no_broker, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln();
		PDF::SetLineWidth(1);
		PDF::Line(10,44,206,44);
		PDF::SetLineWidth(0);
		PDF::Line(10,45,206,45);
		
		PDF::ln(5);
		PDF::Cell(2);
		PDF::SetFont('Times','','10');
		PDF::Cell(25, 5, 'Open Listing ', 0,  'L');
		PDF::Cell(22);
		PDF::MultiCell(50, 5, ': ' . ucwords($listing), 0, 'L');
		
		
		//PDF::ln();
		PDF::Cell(2);
		PDF::Cell(25, 5, 'Nama PT/CV ', 0,  'L');
		
		//PDF::MultiCell(50, 5, ': ' . $company_name, 0, 'L');
		PDF::Cell(22);
		PDF::MultiCell(50, 5, ': ' . $company_name, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		//PDF::Cell(1);
		PDF::MultiCell(30, 10, 'Brand ', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		PDF::Cell(5);
		PDF::MultiCell(50, 5, ': ' . $brand, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln(10);
		PDF::Cell(2);
		PDF::SetFont('Times','B.U','12');
		PDF::Cell(25, 5, 'Pihak Pertama', 0,  'L');
		
		PDF::Cell(56);
		PDF::MultiCell(50, 5, 'Pihak Kedua', 0, 'C', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln(7);
		PDF::SetFont('Times','','10');
		PDF::Cell(2);
		PDF::Cell(10, 5, 'Nama', 0,  'L');
		PDF::Cell(37);
		PDF::MultiCell(30, 5, ': ' . $pic_name, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);

		PDF::Cell(16);
		PDF::MultiCell(20, 5, 'Nama', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::Cell(14);
		PDF::MultiCell(30, 5, ': ' .$name_user, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		
		PDF::ln();
		PDF::Cell(2);
		PDF::Cell(10, 5, 'Jabatan', 0,  'L');
		PDF::Cell(37);
		PDF::MultiCell(30, 5, ': ' . $pic_title, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::Cell(16);
		PDF::MultiCell(30, 5, 'Jabatan', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::Cell(4);
		PDF::MultiCell(30, 5, ': ' .$user_title, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln();
		PDF::Cell(2);
		PDF::Cell(25, 5, 'Telp/HP.', 0,  'L');
		PDF::Cell(22);
		PDF::MultiCell(30, 5, ': ' . $pic_phone, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::Cell(16);
		PDF::MultiCell(30, 5, 'HP.', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::Cell(4);
		PDF::MultiCell(30, 5, ': ' .$user_hp, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln();
		PDF::Cell(2);
		PDF::Cell(15, 5, 'Email', 0,  'L');
		PDF::Cell(32);
		PDF::MultiCell(30, 5, ': ' . $pic_email, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::Cell(16);
		PDF::MultiCell(30, 5, 'Email', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::Cell(4);
		PDF::MultiCell(50, 5, ': ' .$user_email, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln(13);
		PDF::Cell(2);
		PDF::Cell(25, 5, 'Kelengkapan Badan Usaha', 0,  'L');
		
		PDF::Cell(47);
		PDF::MultiCell(100, 5, ': ' . $col_1, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln(8);
		PDF::Cell(2);
		PDF::SetFont('Times','B.U','12');
		PDF::Cell(25, 5, 'Keterangan Paket Investasi', 0,  'L');
		
		PDF::ln(8);
		PDF::Cell(2);
		PDF::SetFont('Times','','10');
		PDF::Cell(25, 5, 'Total Nilai Investasi', 0,  'L');
		
		PDF::Cell(47);
		PDF::MultiCell(100, 5, ': Rp. ' . $modal_awal, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln();
		PDF::Cell(2);
		PDF::Cell(25, 5, 'Fanchise/Joining/Lisence Fee', 0,  'L');
		
		PDF::Cell(47);
		PDF::MultiCell(100, 5, ': Rp. ' . $type, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln();
		PDF::Cell(2);
		PDF::Cell(25, 5, 'Royalty Fee', 0,  'L');
		
		PDF::Cell(47);
		PDF::MultiCell(100, 5, ': Rp. ' . $royalty, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln();
		PDF::Cell(2);
		PDF::Cell(25, 5, 'Advertising Fee', 0,  'L');
		
		PDF::Cell(47);
		PDF::MultiCell(100, 5, ': Rp. ' . $advers, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln();
		PDF::Cell(2);
		PDF::Cell(25, 5, 'Return On Investment (ROI)', 0,  'L');
		
		PDF::Cell(47);
		PDF::MultiCell(100, 5, ':  ' . $roi, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln(10);
		PDF::Cell(2);
		PDF::SetFont('Times','B.U','12');
		PDF::Cell(25, 5, 'Biaya Broker Listing Member :', 0,  'L');
		
		PDF::ln(8);
		PDF::Cell(2);
		PDF::SetFont('Times','','10');
		PDF::Cell(25, 5, 'Skala Nilai Investasi', 0,  'L');
		
		PDF::Cell(47);
		PDF::MultiCell(100, 5, ':  ' . $skala, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln();
		PDF::Cell(2);
		PDF::SetFont('Times','','10');
		PDF::Cell(25, 5, 'Annual Member Fee', 0,  'L');
		
		PDF::Cell(47);
		PDF::MultiCell(100, 5, ':  Rp. ' . $annual, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln();
		PDF::Cell(2);
		PDF::SetFont('Times','','10');
		PDF::Cell(25, 5, 'Commitment Survey Fee', 0,  'L');
		
		PDF::Cell(47);
		PDF::MultiCell(100, 5, ':  Rp. ' . $surv, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);

		PDF::ln(10);
		PDF::Cell(2);
		PDF::SetFont('Times','','10');
		PDF::Cell(5, 5, '1.', 0,  'L');
		PDF::MultiCell(180, 5, 'Jika pihak FranchiseGlobal.com berhasil menjual paket peluang bisnis franchise, kemitraan atau lisensi tersebut, saya setuju untuk membayar jasa penjualan, sbb:', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);

		
		PDF::ln(10);
		PDF::Cell(2);
		PDF::SetFont('Times','B.U','12');
		PDF::Cell(25, 5, 'Commitment Success Fee :', 0,  'L');
		
		PDF::ln();
		PDF::Cell(2);
		PDF::SetFont('Times','','10');
		PDF::Cell(25, 5, $commit1, 0,  'L');
		
		
		PDF::Cell(47);
		PDF::MultiCell(30, 5, $commit2 .'%', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);

		PDF::Cell(7);
		PDF::MultiCell(70, 5, $commit3, 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln(8);
		PDF::Cell(2);
		PDF::SetFont('Times','','10');
		PDF::Cell(5, 5, '2.', 0,  'L');
		PDF::MultiCell(180, 5, 'Setelah ditandatangan perjanjian ini Pihak Pertama tidak diperkannkan untuk membuat perjanjian jasa sejenis dengan pihak lainnya.', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln();
		PDF::Cell(2);
		PDF::Cell(5, 5, '3.', 0,  'L');
		PDF::MultiCell(180, 5, 'Pihak Pertama bersedia memberikan training berupa business knowledge, pendampingan 2x pertemuan dengan calon mitra potensial dan meng-update perubahan nilai investasi (bila ada perubahan).', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln();
		PDF::Cell(2);
		PDF::Cell(5, 5, '4.', 0,  'L');
		PDF::MultiCell(180, 5, 'Pihak Pertama bersedia memberikan testimoni atas penjualan Pihak Kedua yang telah berhasil.', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln();
		PDF::Cell(2);
		PDF::Cell(5, 5, '5.', 0,  'L');
		PDF::MultiCell(180, 5, 'Pihak Kedua tidak bertanggungjawab atas kerja sama antara Pihak Pertama dengan Franchisee/Mitra/Lisencee.', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln(10);
		PDF::Cell(2);
		PDF::SetFont('Times','','10');
		PDF::Cell(25, 5, 'Jakarta, '.$now->format('d, M Y'), 0,  'L');
		
		PDF::ln(7);
		PDF::Cell(2);
		PDF::SetFont('Times','B','10');
		PDF::Cell(25, 5, 'Pihak Pertama', 0,  'L');

		PDF::Cell(92);
		PDF::MultiCell(25, 5, 'Pihak Kedua', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln();
		PDF::Cell(2);
		PDF::SetFont('Times','B','10');
		PDF::Cell(25, 5, 'Pemilik Franchise/Kemitraan/Lisensi', 0,  'L');

				
		PDF::Cell(92);
		PDF::MultiCell(40, 5, 'FranchiseGlobal.com', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln(27);
		PDF::Cell(2);
		PDF::SetFont('Times','B','10');
		PDF::Cell(25, 5, '( --------------------------------------------- )', 0,  'L');
		
		PDF::Cell(92);
		PDF::MultiCell(50, 5, '( '.$name_user.' )', 0, 'L', 0, 0, '', '', true, 0, false, true, 0);
		
		
		PDF::Image(asset($user_image),135,245,30,'');
		
		PDF::Output(public_path('create').'/broker/'.$id, 'F');
		PDF::Output('broker-member-'.$id.'.pdf', 'I');
	}
}
