<?php

namespace App\Http\Controllers\report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\MReport;
use App\Models\AdsOffer;
use App\Models\MSallesOrder;

use App\Classes\CUser;
use App\Classes\OfferClass;
use App\Classes\CurencyClass;
use App\Classes\EstimatedOfferClass;
use App\Classes\CBrand;
use App\Classes\CContact;
use App\Classes\ReportClass;
use App\Classes\SallesOrderClass;
use App\Classes\PackageClass;
use App\Classes\AttachmentClass;

use DB;

class ReportController extends Controller
{
	protected $brands;
	protected $pic_brand;
	protected $packages;
	protected $att;
    public function __construct()
    {
        $this->middleware('auth');
		$this->brands		= new CBrand;
		$this->pic_brand	= new CContact;
		$this->packages		= new PackageClass;
		$this->att			= new AttachmentClass;
    }
    public function index()
    {
        //
    }

    
    public function create()
    {
        //
    }
	public function store(Request $request)
    {
        $report							= new MReport;
		$report->user_id				= $request->user_id;
		$report->date_activities		= $request->date_activitites;
		$report->status					= 1;
		$report->progress				= $request->progress;
		$report->info					= $request->info;
		$report->offer_id				= $request->offer_id;
		$report->company_id				= $request->company_id;
		$report->brand_id				= $request->brand_id;
		$report->attacthment_id			= $request->attacthment_id;
		$report->totals					= $request->omset_estimated;
		
		
		$report->save();
		
		$id								= $request->offer_id;
		$status	= 1;
		DB::update('update detail_ads_offer set status_estimated = ? where id = ?',[$status,$id]);
		
		return redirect()->route('mail.sendestimated', $request->offer_id);
    }

   
    public function show($id)
    {
        //
    }

    
    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
	public function ReportDetailOffer($id)
	{
		$iduser 		= \Auth::user()->id;
		$user			= new CUser();
		$users			= $user->Users($iduser);
		$dept			= $user->GetDetp($iduser);
		$title			= $user->TitleName($dept);
		$usename		= $user->NameUser($iduser);
		
		$offers			= new OfferClass();
		$offer			= $offers->detailsOffersBYid($id);
		
		
		return view('modal.modal_report_offer', compact('users','title','offer','usename','id','iduser'));
	}

	public function ReportOffers(Request $request)
	{
		$iduser 		= \Auth::user()->id;
		$user			= new CUser();
		$users			= $user->Users($iduser);
		$dept			= $user->GetDetp($iduser);
		$title			= $user->TitleName($dept);
		$usename		= $user->NameUser($iduser);
		
		$offers			= new OfferClass();
		$offer			= $offers->DetailOfferBYuser($iduser);
		$idoffer		= $offers->GetIdOffers($iduser);
		$totals			= $offers->sumdetailofferbyid($idoffer);
		$currency		= new CurencyClass();
		$estimat		= new EstimatedOfferClass();
		$brand			= $this->brands;
		$pic			= $this->pic_brand;
		$package		= $this->packages;
		$attach			= $this->att;

		$data	= [
			'data'			=> $offer,
			'pagination' => [
 
				'total' => $offer->total(),
  
				'per_page' => $offer->perPage(),
  
				'current_page' => $offer->currentPage(),
  
				'last_page' => $offer->lastPage(),
  
				'from' => $offer->firstItem(),
  
				'to' => $offer->lastItem()
  
			]
		];

		return response()->json($data);

		return view('report.report_offer', compact('users','title','offer','usename','currency', 'estimat', 'brand','pic', 'package', 'attach'));
	}
	public function SearchReportOffer(Request $request)
	{
		$iduser 		= \Auth::user()->id;
		$user			= new CUser();
		$users			= $user->Users($iduser);
		$dept			= $user->GetDetp($iduser);
		$title			= $user->TitleName($dept);
		$usename		= $user->NameUser($iduser);
		
		$offers			= new OfferClass();
		$offer			= $offers->detailsOffers1($iduser);
		$idoffer		= $offers->GetIdOffers($iduser);
		$totals			= $offers->sumdetailofferbyid($idoffer);
		$currency		= new CurencyClass();
		$date1 			= $request->date1;
		$date2 			= $request->date2;
		
		$report			= new ReportClass();
		$offer			= $report->FilterDateOffer($request);

		return view('report.table_report_offer', compact('users','title','offer','usename','currency'));
	}
	public function Estimated(Request $request)
	{
		$estimat		= new EstimatedOfferClass();
		$estimated		= $estimat->EstimatedOffer();
		
		$currency		= new CurencyClass();
	
		if($request->ajax()){
			$report		= new ReportClass();
			$estimated	= $report->FilterEstimate($request);

			return view('report.search_estimated', compact('estimated','currency'))->render();
		}
		return view('report.estimated_offer',compact('estimated','currency '));
	}
	public function ReportOmset(Request $request)
	{
		$sales			= new SallesOrderClass();
		$omset			= $sales->SalesOrderDetail();
		if($request->ajax()){
			$report		= new ReportClass();
			$omset		= $report->FilterDateEstimate($request);
			
			return view('bu.table_omset', compact('omset','start','end'));
		}
		return view('report.report_omset', compact('omset'));
	}
}
