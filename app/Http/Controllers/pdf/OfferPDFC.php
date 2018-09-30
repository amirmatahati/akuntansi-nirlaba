<?php

namespace App\Http\Controllers\pdf;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

use App\Models\Brands;
use App\Models\MContactPerson;
use App\Models\MCompanies;
use App\Models\AdsOffer;
use App\Models\DetailAdsOffer;
use App\Models\CompanyAddress;

use App\Classes\OfferClass;
use App\Classes\CContact;
use App\Classes\PdfOfferClass;
use App\Classes\UserClass;
use App\Classes\SallesOrderClass;
use App\Classes\AddressClass;
use App\Classes\CUser;
use App\Classes\AttachmentClass;

use DB;
use Carbon;
use PDF;

class OfferPDFC extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $dompdf = new Dompdf();
		$dompdf->loadHtml('hello world');
		$dompdf->setPaper('A4', 'landscape');

		$dompdf->render();
		$dompdf->stream();
    }
    public function prints($id)
    {
	    $now = \Carbon\Carbon::now();
		$tgl = date('d M Y', strtotime($now));
		
		$pdfoffer			= new OfferClass();
		$pdfofferdDetail	= $pdfoffer->DetailOffer($id);

		$col_ads			= "";
		$col_package		= "";
		$col_no				= "";
		$col_price			= "";
		$col_priode			= "";
		$n					= 0;

		foreach($pdfofferdDetail as $b){
			$n++;
			$nomor_offer	= $b->offer_number;
			$brand_id		= $b->brand_id;
			$company_id		= $b->company_id;
			$ads			= $b->OfferPackage->type;
			$package		= $b->OfferPackage->package;
			$price			= $b->price;
			$periode		= $b->ads_period;
			$user_id		= $b->user_id;
//			$price			= "Rp." .number_format($prices);
			
			$col_ads 		= $col_ads.$ads."\n";
			$col_package 	= $col_package.$package."\n";
			$col_price	 	= $col_price.$price."\n";
			$col_priode	 	= $col_priode.$periode."\n";
			$col_no		 	= $col_no.$n."\n";
		}
		
		$pdfoffer	= new PdfOfferClass();
		$offer = $pdfoffer->GetBrandOffer($brand_id);
		foreach($offer as $c){
			$attOffer		= $c->AttachmentName->name;
			$subject		= $c->GetSubject->perihal;
			$brand			= $c->BrandName->name; 
			$company		= $c->GetCompanyPenwaran->name;
			$b_name			= $c->BrandName->brand;
		}
		
		$contact			= new CContact();
		$getContact			= $contact->GetName($brand_id);
		
		foreach($getContact as $d){
			$pics			= $d->name;
			$position		= $d->position;
		}
		
		$adres				= new PdfOfferClass();
		$address			= $adres->GetAddresOffer($company_id);
		foreach($address as $e){
			$address_o		= $e->address;
		}
		
		$user				= new UserClass();
		$user_detail		= $user->GetByIdUser($user_id);
		$ttd				= $user->GetTtd($user_id);
		
		foreach($user_detail as $users){
			$Duser			= $users->name;
			$department_id	= $users->department_id;
			
		}
		
		$user_cat			= $user->GetUserCat($department_id);
		foreach($user_cat as $catuser){
			$Department		= $catuser->name;
			
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
		
		PDF::SetLineWidth(1);
		PDF::Line(10,30,206,30);
		PDF::SetLineWidth(0);
		PDF::Line(10,31,206,31);
		
		$Y_Table_Position = 35;		
		
		PDF::SetY($Y_Table_Position);
		PDF::SetFont('Times','','12');
		PDF::Cell(20, 0, 'Tanggal', 0, 'L');
		
		PDF::Cell(3);
		PDF::Cell(2, 0, ':', 0, 'L');
		
		PDF::SetX(40);
		PDF::Cell(60, 2, ''.$tgl.'', 0, 0, 'L');
		
		PDF::ln(6);
		PDF::SetFont('Times','','12');
		PDF::Cell(20, 1, 'Nomor', 0, 'L');
		
		PDF::Cell(3);
		PDF::Cell(2, 4, ':', 0, 'L');
		

		PDF::SetX(40);
		PDF::Cell(60, 2, ''. $nomor_offer .'', 0, 0, 'L');


		PDF::ln(6);

		PDF::SetFont('Times','','12');
		PDF::Cell(20, 4, 'Lamp.', 0, 'L');

		PDF::Cell(3);
		PDF::Cell(2, 4, ':', 0, 'L');
		
		PDF::SetX(40);
		PDF::Cell(60, 2, ''. $attOffer .'', 0, 0, 'L');

		PDF::ln(6);

		PDF::SetFont('Times','','12');
		PDF::Cell(20, 4, 'Perihal.', 0, 'L');

		PDF::Cell(3);
		PDF::Cell(2, 4, ':', 0, 'L');
		
		PDF::SetX(40);
		PDF::Cell(60, 2, ''. $subject .'', 0, 0, 'L');

		PDF::ln(10);
		
		PDF::SetFont('Times','','12');
		PDF::Cell(0,5,'Kepada Yth, ',0,1,'L');
		
		PDF::SetFont('Times','B','12');
		PDF::Cell(0,5,'Bapak/Ibu. '. $pics .'',0,1,'L');

		PDF::Cell(0,5,''. $position .'',0,1,'L');
		
		
		PDF::Cell(0,5,''. $company .'',0,1,'L');
		
		PDF::Cell(0,5,''. $b_name .'',0,1,'L');
		
		PDF::MultiCell(70,1,$address_o,0,'L');
		
		PDF::ln(6);
		PDF::SetFont('Times','','12');
		PDF::Cell(0,5,'Dengan Hormat,',0,1,'L');
		
		$txt_long1 = 'www.franchiseglobal.com adalah portal media online Franchise No. 1 di Indonesia, yang menghubungkan calon pembeli peluang Franchise/Kemitraan/License dengan Franchisor.';
		$txt_long2 = 'Melalui surat ini, kami bermaksud menawarkan kerjasama pemasangan iklan peluang Franchise/Kemitraan/License Bapak/Ibu di media kami (FranchiseGlobal.com) dengan penawaran sbb.';
		
	
		PDF::MultiCell(190,1,$txt_long1,0,'L');
		PDF::MultiCell(190,1,$txt_long2,0,'L');

		PDF::ln();

		PDF::setFillColor(230,230,230);
		PDF::Cell(10,10.5,'No.',1,0,'C',1);
		PDF::Cell(50,10.5,'Type Ads',1,0,'C',1);
		PDF::Cell(40,10.5,'Package',1,0,'C',1);
		PDF::Cell(40,10.5,'Price',1,0,'C',1);
		PDF::Cell(45,10.5,'Period',1,0,'C',1);
		
		$Y_Table_Position = 153;
		
		PDF::SetFont('Times','',12);
		PDF::ln();
		
		PDF::MultiCell(10, 10, $col_no, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(50, 10, $col_ads, 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(40, 10, $col_package, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(40, 10, $col_price, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(45, 10, $col_priode, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::SetFont('Times','','12');
		PDF::writeHTML('*Terlampir keterangan paket iklan pada Media Kit',130);
		
		$text3	= 'Jika ada pertanyaan atau informasi lebih lanjut dapat menghubungi telp. 021-29832172 atau email: iklan.franchiseglobal@gmail.com.';
		
		
		$text4	= 'Demikian penawaran ini kami sampaikan. Besar harapan kami, kita dapat bekerjasama dalam waktu dekat ini. Atas perhatian dan kerjasamanya kami ucapkan terima kasih';
		PDF::MultiCell(190,1,$text3,0,'L');

		PDF::ln(3);
		PDF::MultiCell(190,1,$text4,0,'L');
		
		PDF::ln(18);
		
		PDF::Cell(0,5,'Hormat Kami,',0,'L');
		PDF::Image(asset($ttd),15,222,30,'');
		PDF::ln(27);
		PDF::SetFont('','U');
		PDF::SetFont('Times','B.U','12');
		PDF::Cell(0,5,$Duser,0,'L');
		
		
		
		PDF::ln();
		PDF::SetFont('Times','','12');
		PDF::Cell(0,5,$Department,0,'L');
		PDF::Output(public_path('create').'/'.$id .'.pdf', 'F');
		PDF::Output(''.$b_name.'.pdf', 'I');
    }
	public function SendMail(Request $request,$id)
    {
		$pdfoffer			= new PdfOfferClass();
		$offersclass		= new OfferClass();
		$idoffers			= $offersclass->GetIdOffersByid($id);
		$att				= new AttachmentClass();
		$getAtth			= $att->FilePDFid($idoffers);
		
		//return json_encode($getAtth);
		if($getAtth == 0 ){
			$now = \Carbon\Carbon::now();
			$tgl = date('d M Y', strtotime($now));
			
			$pdfoffer			= new OfferClass();
			$pdfofferdDetail	= $pdfoffer->DetailOffer($id);

			$col_ads			= "";
			$col_package		= "";
			$col_no				= "";
			$col_price			= "";
			$col_priode			= "";
			$n					= 0;

			foreach($pdfofferdDetail as $b){
				$n++;
				$nomor_offer	= $b->offer_number;
				$brand_id		= $b->brand_id;
				$company_id		= $b->company_id;
				$ads			= $b->OfferPackage->type;
				$package		= $b->OfferPackage->package;
				$price			= $b->price;
				$periode		= $b->ads_period;
				$user_id		= $b->user_id;
	//			$price			= "Rp." .number_format($prices);
				
				$col_ads 		= $col_ads.$ads."\n";
				$col_package 	= $col_package.$package."\n";
				$col_price	 	= $col_price.$price."\n";
				$col_priode	 	= $col_priode.$periode."\n";
				$col_no		 	= $col_no.$n."\n";
			}
			
			
			$offer = $pdfoffer->GetBrandOffer($brand_id);
			foreach($offer as $c){
				$attOffer		= $c->AttachmentName->name;
				$subject		= $c->GetSubject->perihal;
				$brand			= $c->BrandName->name; 
				$company		= $c->GetCompanyPenwaran->name;
				$b_name			= $c->BrandName->brand;
			}
			
			$contact			= new CContact();
			$getContact			= $contact->GetName($brand_id);
			
			foreach($getContact as $d){
				$pics			= $d->name;
				$position		= $d->position;
			}
			
			$adres				= new PdfOfferClass();
			$address			= $adres->GetAddresOffer($company_id);
			foreach($address as $e){
				$address_o		= $e->address;
			}
			
			$user				= new UserClass();
			$user_detail		= $user->GetByIdUser($user_id);
			foreach($user_detail as $users){
				$Duser			= $users->name;
				$department_id	= $users->department_id;
			}
			
			$user_cat			= $user->GetUserCat($department_id);
			foreach($user_cat as $catuser){
				$Department		= $catuser->name;
				
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
			
			PDF::SetLineWidth(1);
			PDF::Line(10,30,206,30);
			PDF::SetLineWidth(0);
			PDF::Line(10,31,206,31);
			
			$Y_Table_Position = 35;		
			
			PDF::SetY($Y_Table_Position);
			PDF::SetFont('Times','','12');
			PDF::Cell(20, 0, 'Tanggal', 0, 'L');
			
			PDF::Cell(3);
			PDF::Cell(2, 0, ':', 0, 'L');
			
			PDF::SetX(40);
			PDF::Cell(60, 2, ''.$tgl.'', 0, 0, 'L');
			
			PDF::ln(6);
			PDF::SetFont('Times','','12');
			PDF::Cell(20, 1, 'Nomor', 0, 'L');
			
			PDF::Cell(3);
			PDF::Cell(2, 4, ':', 0, 'L');
			

			PDF::SetX(40);
			PDF::Cell(60, 2, ''. $nomor_offer .'', 0, 0, 'L');


			PDF::ln(6);

			PDF::SetFont('Times','','12');
			PDF::Cell(20, 4, 'Lamp.', 0, 'L');

			PDF::Cell(3);
			PDF::Cell(2, 4, ':', 0, 'L');
			
			PDF::SetX(40);
			PDF::Cell(60, 2, ''. $attOffer .'', 0, 0, 'L');

			PDF::ln(6);

			PDF::SetFont('Times','','12');
			PDF::Cell(20, 4, 'Perihal.', 0, 'L');

			PDF::Cell(3);
			PDF::Cell(2, 4, ':', 0, 'L');
			
			PDF::SetX(40);
			PDF::Cell(60, 2, ''. $subject .'', 0, 0, 'L');

			PDF::ln(10);
			
			PDF::SetFont('Times','','12');
			PDF::Cell(0,5,'Kepada Yth, ',0,1,'L');
			
			PDF::SetFont('Times','B','12');
			PDF::Cell(0,5,'Bapak/Ibu. '. $pics .'',0,1,'L');

			PDF::Cell(0,5,''. $position .'',0,1,'L');
			
			
			PDF::Cell(0,5,''. $company .'',0,1,'L');
			
			PDF::Cell(0,5,''. $b_name .'',0,1,'L');
			
			PDF::MultiCell(70,1,$address_o,0,'L');
			
			PDF::ln(6);
			PDF::SetFont('Times','','12');
			PDF::Cell(0,5,'Dengan Hormat,',0,1,'L');
			
			$txt_long1 = 'www.franchiseglobal.com adalah portal media online Franchise No. 1 di Indonesia, yang menghubungkan calon pembeli peluang Franchise/Kemitraan/License dengan Franchisor.';
			$txt_long2 = 'Melalui surat ini, kami bermaksud menawarkan kerjasama pemasangan iklan peluang Franchise/Kemitraan/License Bapak/Ibu di media kami (FranchiseGlobal.com) dengan penawaran sbb.';
			
		
			PDF::MultiCell(190,1,$txt_long1,0,'L');
			PDF::MultiCell(190,1,$txt_long2,0,'L');

			PDF::ln();

			PDF::setFillColor(230,230,230);
			PDF::Cell(10,10.5,'No.',1,0,'C',1);
			PDF::Cell(50,10.5,'Type Ads',1,0,'C',1);
			PDF::Cell(40,10.5,'Package',1,0,'C',1);
			PDF::Cell(40,10.5,'Price',1,0,'C',1);
			PDF::Cell(45,10.5,'Period',1,0,'C',1);
			
			$Y_Table_Position = 153;
			
			PDF::SetFont('Times','',12);
			PDF::ln();
			
			PDF::MultiCell(10, 10, $col_no, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
			PDF::MultiCell(50, 10, $col_ads, 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
			PDF::MultiCell(40, 10, $col_package, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
			PDF::MultiCell(40, 10, $col_price, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
			PDF::MultiCell(45, 10, $col_priode, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
			
			PDF::SetFont('Times','','12');
			PDF::writeHTML('*Terlampir keterangan paket iklan pada Media Kit',130);
			
			$text3	= 'Jika ada pertanyaan atau informasi lebih lanjut dapat menghubungi telp. 021-29832172 atau email: iklan.franchiseglobal@gmail.com.';
			$text4	= 'Demikian penawaran ini kami sampaikan. Besar harapan kami, kita dapat bekerjasama dalam waktu dekat ini. Atas perhatian dan kerjasamanya kami ucapkan terima kasih';
			PDF::MultiCell(190,1,$text3,0,'L');
			
			PDF::MultiCell(190,1,$text4,0,'L');
			
			PDF::ln(18);
			
			PDF::Cell(0,5,'Hormat Kami,',0,'L');
			
			PDF::ln(15);
			PDF::SetFont('','U');
			PDF::SetFont('Times','B.U','12');
			PDF::Cell(0,5,$Duser,0,'L');

			PDF::ln();
			PDF::SetFont('Times','','12');
			PDF::Cell(0,5,$Department,0,'L');
			PDF::Output(public_path('create').'/'.$id, 'F');
		}
		return redirect()->route('details.offers_views', $id);
    }
	public function PDFSODownload(Request $request,$id)
	{
		$salles		= new SallesOrderClass();
		$so			= $salles->BrandById($id);
		$sodetail	= $salles->SallesOrderDetail($id);
		
		
		
		
		foreach($so as $so_d){
			$no_so		= $so_d->no_so;
			$pic		= $so_d->GetPICTable->name;
			$brand		= $so_d->BrandSO->brand;
			$jabatan	= $so_d->GetPICTable->position;
			$company	= $so_d->GetCompanySO->name;
			$companyid	= $so_d->company_id;
			$phone		= $so_d->GetPICTable->phone;
			$total		= $so_d->total;
			$user_id	= $so_d->user_id;
		}
		
		$col_ads			= "";
		$col_package		= "";
		$col_no				= "";
		$col_price			= "";
		$col_priode			= "";
		$n					= 0;
		foreach($sodetail as $detail_so){
			$n++;
			
			$ads			= $detail_so->PackageSO->type;
			$package		= $detail_so->PackageSO->package;
			$startads		= $detail_so->start_ads;
			$endads			= $detail_so->end_ads;
			$price			= 'Rp. ' .number_format($detail_so->price, 2);
			
			
			$col_ads 		= $col_ads.$ads."\n";
			$col_package 	= $col_package.$package."\n";
			$col_price	 	= $col_price.$price."\n";
			$col_startads 	= $col_priode.$startads."\n";
			$col_endads 	= $col_priode.$endads."\n";
			$col_no		 	= $col_no.$n."\n";
		}
		$address		= new AddressClass();
		$getAddress		= $address->GetAddressByid($companyid);
		$email			= $address->GetMailByid($companyid);
		$website		= $address->WebAddress($companyid);
		$fax			= $address->FaxAddress($companyid);
		
		$usertable		= new CUser();
		$usermember		= $usertable->Users($user_id);
		
		foreach($usermember as $users){
			$nameuser	= $users->name;
			$catuser	= $users->department_id;
		}
		$department		= $usertable->DepartmentName($catuser);

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
		
		PDF::ln(10);
		PDF::SetX(9);
		PDF::SetFont('Times','B.U','12');
		PDF::Cell(0,5,'Form Media Order',0,'L');
		
		PDF::ln();
		PDF::SetX(9);
		PDF::SetFont('Times','B','12');
		PDF::Cell(0,5,'Sales Agreement',0,'L');
		
		
		PDF::SetXY(130, 43);
		PDF::Cell(0,5,'No. SO : ' .$no_so.' RD',0);
		
		PDF::ln();
		PDF::SetLineWidth(1);
		PDF::Line(10,50,206,50);
		PDF::SetLineWidth(0);
		PDF::Line(10,51,206,51);
		
		PDF::ln();
		PDF::SetX(9);
		PDF::SetFont('Times','','12');
		PDF::Cell(40, 0, 'Name', 0, 'L');
		PDF::Cell(2, 0, ':', 0, 'L');
		
		PDF::MultiCell(60, 2, ''. $pic .'', 0, 'L');
		
		PDF::ln(1);
		PDF::SetX(9);
		PDF::Cell(40, 0, 'Title', 0, 'L');
		PDF::Cell(2, 0, ':', 0, 'L');
		PDF::MultiCell(60, 2, ''. $jabatan .'', 0, 'L');
		
		
		PDF::ln(1);
		PDF::SetX(9);
		PDF::Cell(40, 0, 'Brand', 0, 'L');
		PDF::Cell(2, 0, ':', 0, 'L');
		PDF::MultiCell(60, 2, ''. $brand .'', 0, 'L');
		
		PDF::ln(1);
		PDF::SetX(9);
		PDF::Cell(40, 0, 'Company (PT)', 0, 'L');
		PDF::Cell(2, 0, ':', 0, 'L');
		PDF::MultiCell(60, 2, ''. $company .'', 0, 'L');
		
		PDF::ln(1);
		PDF::SetX(9);
		PDF::Cell(40, 0, 'Address', 0, 'L');
		PDF::Cell(2, 0, ':', 0, 'L');
		PDF::MultiCell(60, 2, ''. $getAddress .'', 0, 'L');
		
		PDF::ln(1);
		PDF::SetX(9);
		PDF::Cell(40, 0, 'City', 0, 'L');
		PDF::Cell(2, 0, ':', 0, 'L');
		PDF::MultiCell(60, 2, 'Bogor', 0, 'L');
		
		PDF::ln(1);
		PDF::SetX(9);
		PDF::Cell(40, 0, 'Phone', 0, 'L');
		PDF::Cell(2, 0, ':', 0, 'L');
		PDF::MultiCell(60, 2, $phone, 0, 'L');
		
		PDF::ln(1);
		PDF::SetX(9);
		PDF::Cell(40, 0, 'Hand phone', 0, 'L');
		PDF::Cell(2, 0, ':', 0, 'L');
		PDF::MultiCell(60, 2, $phone, 0, 'L');
		
		PDF::ln(1);
		PDF::SetX(9);
		PDF::Cell(40, 0, 'E-mail', 0, 'L');
		PDF::Cell(2, 0, ':', 0, 'L');
		PDF::MultiCell(60, 2, $email, 0, 'L');
		
		PDF::ln(1);
		PDF::SetX(9);
		PDF::Cell(40, 0, 'Faxsimile', 0, 'L');
		PDF::Cell(2, 0, ':', 0, 'L');
		PDF::MultiCell(60, 2, $fax, 0, 'L');
		
		PDF::ln(1);
		PDF::SetX(9);
		PDF::Cell(40, 0, 'Website', 0, 'L');
		PDF::Cell(2, 0, ':', 0, 'L');
		PDF::MultiCell(60, 2, $website, 0, 'L');
		
		PDF::ln(6);
		PDF::SetX(9);
		PDF::SetFont('Times','','11');
		
		$txt_long1 = 'We agree to cooperate with FranchiseGlobal.com, with the following price agreements:';
		$txt_long2 = 'Melalui surat ini, kami bermaksud menawarkan kerjasama pemasangan iklan peluang Franchise/Kemitraan/License Bapak/Ibu di media kami (FranchiseGlobal.com) dengan penawaran sbb.';
		
	
		PDF::MultiCell(190,1,$txt_long1,0,'L');
		
		PDF::ln();
		
		PDF::SetX(9);
		PDF::setFillColor(230,230,230);
		PDF::Cell(10,10.5,'No.',1,0,'C',1);
		PDF::Cell(50,10.5,'Description Package ',1,0,'C',1);
		PDF::Cell(40,10.5,'Selected Package',1,0,'C',1);
		PDF::Cell(45,10.5,'Period',1,0,'C',1);
		PDF::Cell(40,10.5,'Price',1,0,'C',1);
		
		PDF::ln();
		
		PDF::SetX(9);
		PDF::MultiCell(10, 10, $col_no, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(50, 10, $col_ads, 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(40, 10, $col_package, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(45, 10, $col_startads .' s/d ' . $col_endads, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(40, 10, $col_price, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::ln();
		PDF::SetX(9);
		PDF::MultiCell(145, 8, 'Total', 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(40, 8,'Rp. '. number_format($total, 2), 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		
		
		
		PDF::ln(10);
		PDF::SetX(9);
		PDF::SetFont('Times','B.U','12');
		PDF::Cell(0,5,'Term & Condition:',0,'L');
		
		PDF::ln();
		PDF::SetX(9);
		PDF::SetFont('Times','','12');
		PDF::Cell(0,5,'-  This agreement can not be unilaterally canceled.',0,'L');
		
		PDF::ln();
		PDF::SetX(9);
		PDF::MultiCell(0,5,'-  Due to the cancellation of the agreement, it will be subject to a cancellation fee of 20% of the value of this agreement.',0,'L');
		
		PDF::ln();
		PDF::SetX(9);
		PDF::SetFont('Times','B.U','12');
		PDF::Cell(0,5,'Payment Method:',0,'L');
		
		PDF::ln();
		PDF::SetX(9);
		PDF::SetFont('Times','','12');
		PDF::Cell(0,5,'-  Please make a payment before ads are enabled.',0,'L');
		
		PDF::ln();
		PDF::SetX(9);
		PDF::SetFont('Times','','12');
		PDF::Cell(0,5,'-  Payment can be transferred to BCA account: 164.300.7995 a/n PT. Tras Mediacom, KCP Kalimalang.',0,'L');
		
		PDF::ln();
		PDF::SetX(9);
		PDF::SetFont('Times','','12');
		PDF::Cell(0,5,'-  Please send proof of payment to: finance.plasafranchise@gmail.com.',0,'L');
		
		PDF::ln(50);
		PDF::SetX(9);
		PDF::setFillColor(230,230,230);
		PDF::Cell(90,10.5,'Pihak Pertama, PT. Tras Mediacom  ',0,0,'C',1);
		PDF::Cell(90,10.5,'Pihak Kedua,Perusahaan & Materi ',0,0,'C',1);

		PDF::ln(11);
		PDF::SetX(9);
		
		PDF::Cell(45,10.5,' ',0,1,'L',1);
		PDF::SetX(9);

		PDF::Cell(45,11,' ',0,1,'L',1);
		PDF::SetX(9);
		PDF::Cell(45,10.5,$nameuser,0,0,'C',1);
		PDF::ln();
		PDF::SetX(9);
		PDF::Cell(45,10.5,$department,0,0,'C',1);
		PDF::ln();
		PDF::SetXY(55,38);
		PDF::SetXY(55,38);
		PDF::Cell(43,42,'',0,0,'C',1);
		PDF::SetX(99);
		PDF::Cell(45,10.5,'Name',0,0,'L',2);
		PDF::SetX(145);
		PDF::Cell(44,10.5,$pic,0,0,'L',1);
		PDF::ln();
		PDF::SetX(99);
		PDF::Cell(45,10.5,'Title  ',0,0,'L',1);
		PDF::SetX(145);
		PDF::Cell(44,10.5,$jabatan,0,0,'L',1);
		PDF::ln();
		PDF::SetX(99);
		PDF::Cell(45,10.5,'e-mail',0,0,'L',1);
		PDF::SetX(145);
		PDF::Cell(44,10.5,$email,0,0,'L',1);
		PDF::ln();
		PDF::SetX(99);
		PDF::Cell(45,10.5,'Hand Phone ',0,0,'L',1);
		PDF::SetX(145);
		PDF::Cell(44,10.5,$phone,0,0,'L',1);
		
		PDF::Output(public_path('create/so').'/'.$id, 'F');
		
		return redirect()->route('details.so_views', $id);
	}
}
