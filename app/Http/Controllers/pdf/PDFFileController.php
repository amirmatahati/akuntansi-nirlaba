<?php

namespace App\Http\Controllers\pdf;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\MPdfFiles;
use App\Models\MSallesOrder;

use App\Classes\StringClass;

use PDF;

class PDFFileController extends Controller
{
    public function index()
    {
		$pdf		= MPdfFiles::paginate(10);
        return view('pdf.pdf_list',compact('pdf'));
    }
	public function create()
    {
        return view('pdf.add');
    }
	public function store(Request $request)
    {
		$strings                = new StringClass();
		$fileNames              = $strings->str2alias($request->pdf_name);

		$bs                     = $request->file('pfd_file_storage')->getClientOriginalExtension();
		$filepdf                = $fileNames . '.' .$bs;
		$file					= 'create/pdf/' .$filepdf;
		$path                   = base_path() .'/public/create/pdf/';
		
        $pdf					= new MPdfFiles;
		$pdf->pdf_name			= $request->pdf_name;
		$pdf->pfd_file_storage	= $file;
		
		$pdf->save();
		
		$pdfnm                  = $strings->str2alias($pdf->pdf_name);
        $nmpdf                  = strtolower($pdfnm) . '.' .
        
        $request->file('pfd_file_storage')->getClientOriginalExtension();
        $request->file('pfd_file_storage')->move($path, $pdfnm);
		
		$request->session()->flash('alert-success', 'was successful add!');
		return redirect()->route('pdf.list', compact('idcompany'));
    }
	public function show($id)
    {
        //
    }
	public function edit($id)
    {
		$data			= MPdfFiles::find($id);
        return view('pdf.edit', compact('data'));
    }
	public function update(Request $request, $id)
    {
		 $strings                = new StringClass();
        $gallery_title          = $strings->str2alias($request->pdf_name);
        
        $b_title             = strtolower($gallery_title);
       
        if($request->hasFile('pfd_file_storage')) {
        $strings                        = new StringClass();
        $gallery_title                  = $strings->str2alias($request->pdf_name);
        
        $bs                             = $request->file('pfd_file_storage')->getClientOriginalExtension();
        $fileimg                        = $gallery_title . '.' .$bs;
        $b_image                        = 'create/pdf/' .$fileimg;
        $path                           = base_path() .'/public/create/pdf/';
        }else{
            $b_image                = $request->file_pdf;
        }
         
        $pdf                        = MPdfFiles::find($id);
        $pdf->pdf_name			= $request->pdf_name;
        $pdf->pfd_file_storage          = $b_image;
        
        
        
        if($request->hasFile('pfd_file_storage')) {
            $imageName = $strings->str2alias($request->pdf_name);
            $nmImg      = strtolower($imageName) . '.' . 
            $request->file('pfd_file_storage')->getClientOriginalExtension();
            $request->file('pfd_file_storage')->move($path, $nmImg);
        }
        $pdf->save();
		$request->session()->flash('alert-success', 'was successful Update!');
        return redirect()->route('pdf.list')->with('success','Successfully Update');
		
		
    }
	public function destroy($id)
    {
        //
    }
	public function omsetPDF(Request $request)
	{
		$start 		= date("Y-m-d",strtotime($request->start));
		$end 		= date("Y-m-d",strtotime($request->end."+1 day"));
		$omset		= MSallesOrder::join('salles_order_detail','salles_order.id','salles_order_detail.salles_order_id')
					->whereBetween('salles_order.created_at', [$start,$end])
					->paginate(10);
		
		$col_1 = "";
		$col_2 = "";
		$col_3 = "";
		$col_4 = "";
		$col_5 = "";
		$col_6 = "";
		
		foreach($omset as $c){
			$noso 				= $c->no_so;
			$brand	 			= $c->BrandSO->brand;
			
			$starts				= $c->start_ads;
			$ends				= $c->end_ads;
			
			$publish			= date('d M Y', strtotime($starts));
			$unpublish			= date('d M Y', strtotime($ends));
			$total 				= number_format($c->total, 2);
			$payment			= ucwords($c->type_payment);
			
			$col_1		 		= $col_1.$noso."\n";
			$col_2			 	= $col_2.$brand."\n";
			$col_3		 		= $col_3.$publish."\n";
			$col_4		 		= $col_4.$unpublish."\n";
			$col_5		 		= $col_5.$total."\n";
			$col_6		 		= $col_6.$payment."\n";

		}
		
		PDF::SetTitle('Sample PDF');
		PDF::setMargins(10,6,10);
		PDF::AddPage('L','mm','A5');
		PDF::Image(asset('img/logo2.jpg'),230,10,50,'');
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
		PDF::Line(10,30,276,30);
		PDF::SetLineWidth(0);
		PDF::Line(10,31,276,31);

		
		PDF::ln(10);
		PDF::SetFont('Times','B','25');
		PDF::Cell(0,5,'Omset Report ',0,1,'C');
		PDF::ln(10);

		PDF::SetFont('Times','','12');
		PDF::Cell(25, 5, 'Priode ', 0,  'L'); 
		PDF::Cell(10);
		PDF::MultiCell(50, 5, ':' .  date('d M Y', strtotime($start)) .' - ' . date('d M Y', strtotime($end)) , 0, 'L');
		PDF::ln(10);
		PDF::SetFont('Times','B','12');
		PDF::Cell(30,10.5,'No. SO',1,0,'C');
		PDF::Cell(55,10.5,'Brand',1,0,'C');
		PDF::Cell(40,10.5,'Publish',1,0,'C');
		PDF::Cell(55,10.5,'Unpublish',1,0,'C');
		PDF::Cell(55,10.5,'Total',1,0,'C');
		PDF::Cell(30,10.5,'Payment',1,0,'C');
		PDF::ln();
		PDF::SetFont('Times','','12');
		PDF::MultiCell(30, 10, $col_1, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(55, 10, $col_2, 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(40, 10, $col_3, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(55, 10, $col_4 , 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(55, 10, $col_5, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		PDF::MultiCell(30, 10, $col_6, 1, 'C', 0, 0, '', '', true, 0, false, true, 0);
		
		PDF::Output('SamplePDF.pdf', 'I');
	}
}
