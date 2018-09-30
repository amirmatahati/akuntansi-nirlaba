<?php

namespace App\Http\Controllers\pdf;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;


use App\Models\Brands;
use App\Models\MContactPerson;
use App\Models\MCompanies;

use App\Classes\OfferClass;
use App\Classes\SallesOrderClass;
use App\Classes\AddressClass;
use App\Classes\CUser;
use App\Classes\PdfOfferClass;
use App\Classes\CurencyClass;

use PDF;

class PdfController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request,$id)
    {
		$items 		= Brands::where('id', $id)->get();
		$brand_id 	= Brands::where('company_id', $id)->pluck('id');
		$company_id = Brands::pluck('company_id');

		$dcontact	= MContactPerson::where('brand_id', $id)->get();
		$vcompany	= MCompanies::where('id', $company_id)->get();

        return view('pdf.company_pdf',compact('items','dcontact','vcompany','id'));
    }
	public function downloadPDF(Request $request, $id){
		$items 		= Brands::where('id', $id)->get();
		$brand_id 	= Brands::where('company_id', $id)->pluck('id');
		$company_id = Brands::pluck('company_id');

		$dcontact	= MContactPerson::where('brand_id', $id)->get();
		$vcompany	= MCompanies::where('id', $company_id)->get();

		$column_pic = "";
		$column_position = "";
		$column_phone = "";
		$column_email = "";
		PDF::SetTitle('Sample PDF');

		PDF::SetHeaderData(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 034', PDF_HEADER_STRING);
		
		// set header and footer fonts
		PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
		PDF::SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);

        PDF::AddPage();
		
		PDF::Ln();
		
		
		$Y_pt = 10;
		foreach($items as $a){
		PDF::SetY($Y_pt);
		PDF::SetX(25);
		PDF::Cell(150, 1, $a->brand, 0, 0, 'C'); 
		
		}
		PDF::Ln();
		PDF::SetFont('helveticaI','',10);
		PDF::setColor('fill', 220, 220, 220);

		PDF::Ln();
		$Y_pt = 16;
		foreach($vcompany as $d){
		PDF::SetY($Y_pt);
		PDF::SetX(18);
		PDF::Cell(160, 1, $d->name, 0, 0, 'C'); 
		
		}
		PDF::Ln();
		PDF::Ln();
		
		$Y_Fields_Name_position = 35;
		PDF::SetFont('helveticaI','B',10);
		PDF::SetY($Y_Fields_Name_position);
		PDF::Cell(50,8,'PIC',1,0,'C',1);
		PDF::SetX(55);
		PDF::Cell(50,8,'Position',1,0,'C',1);
		PDF::SetX(100);
		PDF::Cell(45,8,'Phone',1,0,'C',1);
		PDF::SetX(145);
		PDF::Cell(50,8,'Email',1,0,'C',1);
		PDF::Ln();
		
		
		foreach($dcontact as $c){
			$pic 				= $c->name;
			$position 			= $c->position;
			$phone 				= $c->phone;
			$email 				= $c->phone;
			
			$column_pic 		= $column_pic.$pic."\n";
			$column_position 	= $column_position.$position."\n";
			$column_phone 		= $column_phone.$phone."\n";
			$column_email 		= $column_email.$email."\n";

		}
		$Y_Table_Position = 43;
		PDF::Ln();
		
		PDF::SetFont('helveticaI','',10);
		
		PDF::SetY($Y_Table_Position);
		PDF::SetX(15);
		PDF::MultiCell(40,6,$column_pic,1,'L');

		PDF::SetY($Y_Table_Position);
		PDF::SetX(55);
		PDF::MultiCell(45,6,$column_position,1,'L');

		PDF::SetY($Y_Table_Position);
		PDF::SetX(100);
		PDF::MultiCell(45,6,$column_phone,1,'L');

		PDF::SetY($Y_Table_Position);
		PDF::SetX(145);
		PDF::MultiCell(50,6,$column_email,1,'L');

        PDF::Output('SamplePDF.pdf', 'I');
    }
	public function invoice(Request $request,$ids)
	{ 
		$id				= $request->id;
		$now 			= \Carbon\Carbon::now();
		$thn 			= date('Y', strtotime($now));
		$tgl 			= date('d M Y', strtotime($now));
		
		$salles			= new SallesOrderClass();
		$so				= $salles->BrandById($id);
		$sodetail		= $salles->SallesOrderDetail($id);
		
		$jatuh_tempo	= date('d M Y', strtotime($request->jatuh_tempo));
		$lampiran		= $request->lamp;
		$companyid = 0;
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
			$brand_id	= $so_d->brand_id;
		}
		//return json_encode($id);
		$col_ads			= "";
		$col_package		= "";
		$col_no				= "";
		$col_price			= "";
		$col_startads		= "";
		$col_endads			= "";
		$list				= "";
		$n					= 0;
		$price_total		= 0;
		$currency			= new CurencyClass();
		foreach($sodetail as $detail_so){
			$n++;
			
			$ads			= $detail_so->PackageSO->type;
			$package		= $detail_so->PackageSO->package;
			$startads		= date('d M Y', strtotime($detail_so->start_ads));
			$endads			= date('d M Y', strtotime($detail_so->end_ads));
			$price			= 'Rp. ' .number_format($detail_so->price, 2);
			$price_total 	= $detail_so->price + $price_total ;
			
			
			$col_ads 		= $col_ads.$ads."\n";
			$col_package 	= $col_package.$package."\n";
			$col_price	 	= $col_price.$price."\n";
			$col_startads 	= $col_startads.$startads."\n";
			$col_endads 	= $col_endads.$endads."\n";
			$list 			= $list."- \n";
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
		
		$inv			= 'INV/AC-AWI/XII/'.$thn.'/'.$no_so;
		/*
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
		
		*/
		
		
		$pdfoffer	= new PdfOfferClass();
		$offer = $pdfoffer->GetBrandOffer($brand_id);

		
		//return json_encode($brand);
		PDF::SetTitle('Sample PDF');
		//PDF::setMargins(10,6,10);
		//PDF::AddPage('L','mm','A5');
		
		PDF::SetHeaderMargin(5);
		PDF::SetFooterMargin(18);
		PDF::setMargins(10,6,10);
		PDF::SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
		PDF::AddPage();
		
		PDF::Image(asset('img/logo2.jpg'),155,10,50,'');
		PDF::Cell(2);
		PDF::SetFont('Times','B','12');
		PDF::Cell(0,5,'PT. TRAS MEDIACOM ',0,1,'L');
		PDF::Cell(2);
		PDF::SetFont('Times','','12');
		PDF::Cell(0,5,'Ruko Waterland,Jl.Menteng Utama Blok F1 No.23 ',0,1,'L');
		PDF::Cell(2);
		PDF::Cell(0,5,'Sektor Utama - Menteng Metropolitan,Cakung-Jakarta Timur',0,1,'L');
		PDF::Cell(2);
		PDF::Cell(0,5,'Telp. 021-29832172-74 / Fax. 021-22860753',0,1,'L');
		
		PDF::SetLineWidth(1);
		PDF::Line(10,30,205,30);
		PDF::SetLineWidth(0);
		PDF::Line(10,31,205,31);
		
		$Y_Table_Position = 35;		
		
		PDF::SetY($Y_Table_Position);
		PDF::Cell(2);
		PDF::SetFont('Times','B','18');
		PDF::Cell(0,5,'INVOICE',0,1,'C');
		
		PDF::Cell(2);
		PDF::SetFont('Times','','10');
		PDF::Cell(0,5,'Kepada Yth, ',0,1,'L');
		
		PDF::Cell(2);
		PDF::Cell(20, 4, 'Perusahaan', 0, 'L');
		
		PDF::Cell(5);
		PDF::Cell(2, 4, ':', 0, 'L');
		
		$Y_Table_Position = 48;		
		PDF::SetY($Y_Table_Position);
		PDF::SetX(40);
		PDF::Cell(60, 2, $brand, 0, 0, 'L');

		PDF::Cell(20);
		PDF::Cell(25, 4, 'No. Invoice ', 0,  'L'); 
		PDF::Cell(10);
		PDF::MultiCell(50, 4, ': INV/AC-AWI/XII/'.$thn.'/'.$no_so, 0, 'L');

		$get_xxx = PDF::GetX();
		$get_yyy = PDF::GetY();
		
		$width_cell = 65; 
		$height_cell = 7; 
		
		PDF::Cell(2);
		PDF::Cell(20, 5, 'Alamat', 0, 0, 'L'); 

		PDF::Cell(5);
		PDF::Cell(2, 5, ':', 0, 0, 'L'); 

		PDF::SetX(40);
		PDF::MultiCell(75,1,$getAddress,0,'L');
//		PDF::MultiCell($width_cell,$height_cell,$getAddress,0);
		$get_xxx+=$width_cell;
		PDF::SetXY($get_xxx, $get_yyy);
	
		PDF::SetY(53);
		PDF::Cell(110);
		PDF::Cell(25, 5, 'Tgl. Invoice ', 0,  'L'); 
		PDF::Cell(10);
		PDF::MultiCell(50, 5, ': ' .$tgl, 0, 'L');
	
		$Y_Table_Position = 66;		
		
		PDF::SetY($Y_Table_Position);
		PDF::Cell(2);
		PDF::Cell(20,5,'No. Telp.',0,0,'L');
		
		PDF::Cell(5);
		PDF::Cell(2,5,':',0,0,'L');
		
		PDF::Cell(1);
		PDF::Cell(20, 5, $phone, 0, 0, 'L');
		
		PDF::SetY(58);
		PDF::Cell(110);
		PDF::Cell(25, 5, 'Tgl. Jatuh Tempo ', 0,  'L'); 
		PDF::Cell(10);
		PDF::MultiCell(50, 5, ': ' .$jatuh_tempo, 0, 'L');
		
		PDF::SetY(71);
		PDF::Cell(2);
		PDF::Cell(20,5,'No. Fax. ',0,0,'L');
		
		PDF::Cell(5);
		PDF::Cell(2,5,':',0,0,'L');
		
		PDF::Cell(1);
		PDF::Cell(20, 5,$fax, 0, 0, 'L');
		
		PDF::SetY(63);
		PDF::Cell(110);
		PDF::Cell(25,5,'Lampiran ',0,'L');
		
		PDF::Cell(10);
		PDF::Cell(20, 5, ': '. $lampiran, 0, 'L');
		
		PDF::Ln();
		PDF::SetY(76);
		PDF::Cell(2);
		PDF::Cell(20,5,'Attn',0,0,'L');
		
		PDF::Cell(5);
		PDF::Cell(2,5,':',0,0,'L');
		
		PDF::Cell(1);
		PDF::Cell(20, 4, $pic, 0, 0, 'L');
		
		
		
		PDF::ln(20);
		PDF::setFillColor(230,230,230);
		PDF::Cell(10,10.5,'No.',1,0,'C',1);
		PDF::Cell(50,10.5,'Type Ads',1,0,'C',1);
		PDF::Cell(30,10.5,'Package',1,0,'C',1);
		PDF::Cell(55,10.5,'Period',1,0,'C',1);
		PDF::Cell(40,10.5,'Price',1,0,'C',1);
		
		$Y_Table_Position = 153;
		
		PDF::SetFont('Times','',12);
		PDF::ln();
		
		PDF::MultiCell(10, 10, $col_no, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(50, 10, $col_ads, 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(30, 10, $col_package, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(25, 10.5, $col_startads , 0, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(5, 10.5, $list , 0, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(25, 10.5, $col_endads, 0, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(40, 10.5, $col_price, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);

		PDF::ln(10);
		PDF::Cell(145,10.5,'Total.',1,0,'L',1);
		PDF::Cell(40, 10.5,'Rp. '. number_format($price_total, 2), 1,0,'C',1);
		
		PDF::ln(10);
		PDF::Cell(55,10.5,'Terbilang',0,'C');		
		PDF::Cell(55,10.5,ucwords($currency->TerbilangRupiah($price_total)). 'Rupiah',0,'C');		
		
		PDF::ln(25);
		PDF::Cell(145);
		PDF::Cell(20,6,'Hormat Kami,',0,1,'C');
		PDF::Cell(145);
		PDF::Cell(20,6,'PT. TRAS MEDIACOM',0,1,'C');
		PDF::Cell(145);
		PDF::Cell(20,6,'www.trasnco.com',0,1,'C');
		
		PDF::ln(10);
		PDF::SetY(149);
		PDF::Cell(80,6,'Pembayaran dapat dilakukan melalui : ',1,1,'C');
		PDF::Cell(25, 6, 'No. Rekening', 1, 0, 'L');
		PDF::Cell(55, 6, ': 164-301-1381', 1, 1, 'L');
		PDF::Cell(25, 6, 'A/N  Pemilik', 1, 0, 'L'); 
		PDF::Cell(55, 6, ': PT. TRAS  MEDIACOM', 1, 1, 'L');
		PDF::Cell(25, 6, 'Nama Bank ', 1, 0, 'L'); 
		PDF::Cell(55, 6, ': BCA Cabang Kali Malang 1', 1, 1, 'L');
		
		PDF::SetY(175);
		PDF::Cell(70, 4, 'Mohon Bukti Pembayaran di fax ke : ', 0, 1, 'C'); 
		PDF::Cell(70, 4, '(021) 22860753 ', 0, 1, 'C'); 
		PDF::Cell(70, 4, 'Up : Bag.Finance ', 0, 1, 'C');		
		
		PDF::SetY(187);
		PDF::Cell(135);
		PDF::SetFont('Times','B','10');
		PDF::Cell(45, 4, 'Susilowati Ningsih', 0,1,'C'); 
		PDF::SetFont('Times','','10');
		PDF::Cell(135);
		PDF::Cell(45, 4, 'Director',0, 0,'C'); 

		PDF::ln(10);
		PDF::Cell(25, 4, 'Catatan: ', 0, 1, 'L');
		PDF::Cell(25, 4, '1. Mohon diperhatikan tanggal jatuh tempo pembayaran.  ', 0, 1, 'L');
		PDF::Cell(25, 4, '2. Apabila terjadi pembatalan order secara sepihak akan dikirimkan invoice cancel fee sebesar 20% dari nilai media order.', 0, 1, 'L');
		
		

		PDF::Cell(2);
		PDF::SetFont('Times','B','12');
		

		PDF::Output(public_path('create').'/so/'.$id, 'F');
		if($request->send_mail){
			$post_mail	= [
				'id' => $id,
				'jatuh_tempo' => $request->jatuh_tempo,
				'inv' => $inv,
				'company_name'		=> $request->company_name,
				'brand_name'		=> $request->brand_name,
				'no_so'				=> $request->no_so,
				'address'		=> $request->address,
				'tanggal'		=> $request->tanggal,
				'text'		=> $request->text,
				'email'		=> $request->email,
				'type'		=> $request->type,
				'package'		=> $request->package,
				'publish'		=> $request->publish,
				'price'		=> $request->price,
				'ccto'		=> $request->ccto,
			];
			return redirect()->route('mail.so', $post_mail);
		}
		PDF::Output('SO.pdf', 'I');
	}
	
	
	public function invoice__backup()
	{
		
		PDF::SetTitle('Sample PDF');
		PDF::setMargins(10,6,10);
		PDF::AddPage('L','mm','A5');
		PDF::Image('asset("assets/img/logo2.jpg")',150,10,50,'');
		
		PDF::Cell(2);
		PDF::SetFont('Times','B','12');
		PDF::Cell(0,5,'PT. TRAS MEDIACOM ',0,1,'L');
		PDF::Cell(2);
		PDF::SetFont('Times','','10');
		PDF::Cell(0,5,'Ruko Waterland,Jl.Menteng Utama Blok F1 No.23 ',0,1,'L');
		PDF::Cell(2);
		PDF::Cell(0,5,'Sektor Utama - Menteng Metropolitan,Cakung-Jakarta Timur',0,1,'L');
		PDF::Cell(2);
		PDF::Cell(0,5,'Telp. 021-29832172-74 / Fax. 021-22860753',0,1,'L');
		
		PDF::SetLineWidth(1);
		PDF::Line(10,30,198,30);
		PDF::SetLineWidth(0);
		PDF::Line(10,31,198,31);
		
		$Y_Table_Position = 35;		
		
		PDF::SetY($Y_Table_Position);
		PDF::Cell(2);
		PDF::SetFont('Times','B','18');
		PDF::Cell(0,5,'INVOICE',0,1,'C');
		
		PDF::Cell(2);
		PDF::SetFont('Times','','8');
		PDF::Cell(0,5,'Kepada Yth, ',0,1,'L');
		
		PDF::Cell(2);
		PDF::Cell(20, 4, 'Perusahaan', 0, 'L');
		
		PDF::Cell(5);
		PDF::Cell(2, 4, ':', 0, 'L');
		
		$Y_Table_Position = 48;		
		PDF::SetY($Y_Table_Position);
		PDF::SetX(40);
		PDF::Cell(60, 2, 'PT. TRANS PASIFIC GLOBAL', 0, 0, 'L');

		PDF::Cell(15);
		PDF::Cell(25, 4, 'No. Invoice ', 0,  'L'); 
		PDF::Cell(10);
		PDF::MultiCell(50, 4, ': INV/AC-AWI/XII/2017/216', 0, 'L');

		$get_xxx = PDF::GetX();
		$get_yyy = PDF::GetY();
		
		$width_cell = 65; 
		$height_cell = 7; 
		
		PDF::Cell(2);
		PDF::Cell(20, 5, 'Alamat', 0, 0, 'L'); 

		PDF::Cell(5);
		PDF::Cell(2, 5, ':', 0, 0, 'L'); 

		PDF::SetX(40);
		PDF::MultiCell($width_cell,$height_cell,'Ged.Cartisan Lt.3 Jl. RS Fatmawati No.53 RT.007/RW.005 Kel. Cilandak Barat , Kec. Cilandak, Jakarta Selatan',0);
		$get_xxx+=$width_cell;
		PDF::SetXY($get_xxx, $get_yyy);
	
		PDF::SetY(53);
		PDF::Cell(105);
		PDF::Cell(25, 5, 'Tgl. Invoice ', 0,  'L'); 
		PDF::Cell(10);
		PDF::MultiCell(50, 5, ': 12/15/2017', 0, 'L');
	
		$Y_Table_Position = 66;		
		
		PDF::SetY($Y_Table_Position);
		PDF::Cell(2);
		PDF::Cell(20,5,'No. Telp.',0,0,'L');
		
		PDF::Cell(5);
		PDF::Cell(2,5,':',0,0,'L');
		
		PDF::Cell(1);
		PDF::Cell(20, 5, '21-75918596', 0, 0, 'L');
		
		PDF::SetY(58);
		PDF::Cell(105);
		PDF::Cell(25, 5, 'Tgl. Jatuh Tempo ', 0,  'L'); 
		PDF::Cell(10);
		PDF::MultiCell(50, 5, ': 12/19/2017', 0, 'L');
		
		PDF::SetY(71);
		PDF::Cell(2);
		PDF::Cell(20,5,'No. Fax. ',0,0,'L');
		
		PDF::Cell(5);
		PDF::Cell(2,5,':',0,0,'L');
		
		PDF::Cell(1);
		PDF::Cell(20, 5,'021-75918596', 0, 0, 'L');
		
		PDF::SetY(63);
		PDF::Cell(105);
		PDF::Cell(25,5,'Lampiran ',0,'L');
		
		PDF::Cell(10);
		PDF::Cell(20, 5, ': Formulir Konfirmasi', 0, 'L');
		
		PDF::Ln();
		PDF::SetY(76);
		PDF::Cell(2);
		PDF::Cell(20,5,'Attn',0,0,'L');
		
		PDF::Cell(5);
		PDF::Cell(2,5,':',0,0,'L');
		
		PDF::Cell(1);
		PDF::Cell(20, 4, 'Ibu Verawati Basri', 0, 0, 'L');
		
		/* Tabel */
		$dcontacts		= MContactPerson::all();
		$dcontacts		= $dcontacts->sortBy('count')->take(3); 
		
		
		$col_1 = "";
		$col_2 = "";
		$col_3 = "";
		$col_4 = "";
		foreach($dcontacts as $c){
			$pic 				= $c->name;
			$position 			= $c->position;
			$phone 				= $c->phone;
			$email 				= $c->phone;
			
			$col_1		 		= $col_1.$pic."\n";
			$col_2			 	= $col_2.$position."\n";
			$col_3		 		= $col_3.$phone."\n";
			$col_4		 		= $col_4.$email."\n";

		}
		
		PDF::ln(10);
		PDF::Cell(150,10.5,'Keterangan',1,0,'C');
		PDF::Cell(38,10.5,'Harga',1,0,'C');
		
		PDF::ln();
		PDF::MultiCell(150,1,'Tagihan Atas : '.$col_1.'',1,'L');
		PDF::SetY(96.7);
		PDF::Cell(150);
		PDF::MultiCell(38,1,$col_2,1,'L');
		
		
		PDF::Cell(2);
		PDF::SetFont('Times','B','12');
		
		PDF::Output('SamplePDF.pdf', 'I');
	}
	public function PdfOffer($id)
	{
		$offerClass	= new OfferClass();
		$offer = $offerClass->GetPdfOffer($id);
		return view('pdf.offer_pdf', compact('offer'));
		
	}
}
