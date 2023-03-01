<?php 
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Validator;
use Config;
use \PDF,Excel;
use App\models\Admin\ReportModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\StateModel;
use App\adminmodel\ECIModel;
use App\Classes\xssClean;
use App\Exports\ResultSheetReport;

class ResultSheetController extends Controller {
  
	public $action     = 'result-sheet-report';
    
    public function __construct(){
		$this->ECIModel = new ECIModel();        
    }
  
public function index(Request $request, $st_code=''){
	 
	$user_type=null;
	$ST_CODE = '';
	$semi_chart_data = '[]';
	$semi_chart_labels = '[]';
	$semi_chart_bgcolor = '[]';
	$sum=''; $sumData=''; $result=array();
	$state_name='';
	$total_win=0;
	$total_lead=0;
	$grand_total_win_lead=0;
	$Constituencies_count = 0;
	$Constituencies_out_of_count = 0;
	
	$Constituencies_count_chart = array();
	$voteshare = array();
	$votesharedata = '[]';
	$votesharecolor = '[]';
	$resultPartywisedata='';
	$list_desc = array();
	$elec_name = '';
	$LeadWinCount = array();
	$pwdata = array();
	$total_win_lead = 0;
	
	

	$list_details = $this->ECIModel->getstatebyelectionid(13);
	
	//dd($st_code);
	
	
	$state_list = explode(",",$st_code);
	
	//dd(count($state_list));
	
	
	$elec_name = array();
	$elec_type = array();
	$state_name = array();
	$resultPartywisedata = array();
	$resultPartywisedata_total = array();
	$Constituencies_count = array();
	$Constituencies_out_of_count = array();
	$result = array();
	$st_name = '';
	
	foreach($state_list as $key => $raw){
		
	//foreach($data['states'] as $raw){
	$data['state'] = $raw;
	
	
	
	
	if($list_details){
		if($data['state']){
			$ST_CODE = $data['state'];
		}

		
		//dd($ST_CODE);
		
		$list_desc = DB::table('m_election_history')->select('description')->where('election_id','=',13)->where('election_type_id','=',$list_details->ELECTION_TYPEID)->first(); 
		$list_de = DB::table('m_election_details')->select('ELECTION_TYPE')->where('ST_CODE','=',$ST_CODE)->first(); 
		if($list_desc){
			$elec_name[$key] = strtoupper(str_replace('-',' ',$list_desc->description));
			$elec_type[$key] = strtoupper(str_replace('-',' ',$list_de->ELECTION_TYPE));
		}		  	  
	}
	//dd($list_details);
	
	
	if(isset($ST_CODE) && $ST_CODE <>''){
		
		
		
		$query = "SELECT * from  winning_leading_candidate where st_code='$ST_CODE'";
		//$query .= " order by leading_id asc"; 
		$query .= " order by status desc"; 
		$result[$key] = DB::select($query);
		
		$state_name[$key] = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $ST_CODE)->first()->ST_NAME;
		$st_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $ST_CODE)->first()->ST_NAME;


		//dd($state_name);


		$LeadWinCount = DB::table('winning_leading_candidate')
		 ->select('lead_cand_party','lead_cand_hparty', DB::raw('sum(CASE WHEN STATUS = "1" THEN "1" ELSE 0 END) as win'),DB::raw('sum(CASE WHEN STATUS = "0" THEN "1" ELSE 0 END) as lead'))
		 ->where('lead_cand_party', '!='," ")
		 ->where('ST_CODE',$ST_CODE)
		 ->groupBy('lead_cand_party','lead_cand_hparty')
		 ->get()->toArray();
 
		$pwdata = json_decode( json_encode($LeadWinCount), true);
		
		//dd($pwdata);
		$total_win=0;
		$total_lead=0;
		$grand_total_win_lead=0;
		
		foreach ($pwdata as $k)
		{
			$total_win=$total_win+$k['win'];
			$total_lead=$total_lead+$k['lead'];
			$grand_total_win_lead=$total_win+$total_lead;
			$total_win_lead=$k['win']+$k['lead'];
		   $resultPartywisedata[$key][]="<tr style='font-size:12px;'>
			<td align='left' style='font-weight:bold;'>".$k['lead_cand_party']."</td><td align='center' style='font-weight:bold; background-color: #c3e9c0;'>".$k['win']."</td><td align='center' style='font-weight:bold; background-color: #f8d79e;'>".$k['lead']."</td>
			<td align='center' style='font-weight:bold; background-color: #a5edde; color: #000;'>".$total_win_lead."</td>
			</tr>"; 
		}
		$resultPartywisedata[$key][]="
			<tr style='font-size:12px;font-weight:bold;color:#FFF;text-align:center;'><td style='color:#d53858; background-color: #fce0e6; font-weight: bold;'  align='left'>Total</td><td style='color:#000; background-color: #54c752;' align='center'>".$total_win."</td><td style='color:#000; background-color: #ecb241;' align='center'>".$total_lead."</td><td style='color:#000; background-color: #3accae; color: #000;' align='center'>".$grand_total_win_lead."</td></tr>"; 
		 
		$Constituencies_count[$key] = DB::table('m_election_details')
				 ->where('ST_CODE',$ST_CODE)
				 ->where('CONST_TYPE','AC')
				 ->where('CURRENTELECTION','Y')
				 ->get()
				->count();
		$Constituencies_out_of_count[$key] = DB::table('winning_leading_candidate')
				 ->where('ST_CODE',$ST_CODE)
				 ->where('lead_cand_party', '!='," ")
				 ->count();
		
	}
	}
	
	//$data['action']         = url($this->action);
	
	//dd($resultPartywisedata);
	
	if(count($state_list) > 1){
		$st_name ='All-States-Report';
	}
	
	
	$pdf = PDF::loadView('admin.ResultSheet.result-sheet-report-pdf',$data, compact('data','state_name','user_type','Constituencies_out_of_count', 'Constituencies_count','result','resultPartywisedata','votesharedata','votesharecolor','elec_name','elec_type','state_list'));
     return $pdf->download($st_name.'.pdf');

	
	
	//return view('admin.ResultSheet.result-sheet-report-pdf',$data, compact('data','state_name','user_type','Constituencies_out_of_count', 'Constituencies_count','result','resultPartywisedata','votesharedata','votesharecolor','elec_name','elec_type','state_list'));
  }
  
  
  
  public function result_report(Request $request, $st_code=''){
	  
	  
	  	//dd(123);
	 
		$user_type=null;

		$data = array();

		//dd($request->all());
		
		$data['m_state']= $request->state_code;
		$data['state']= $request->state_code;
		$data['pc_no']= $request->pc_no;
		$data['party']= $request->party;
		$data['party_type']= $request->party_type;
		$data['pdf']= $request->pdf;
		$data['excel']= $request->excel;

		$data['m_state']= $statevalue = StateModel::get_states();

		$sql = DB::table('winning_leading_candidate as wlc')->select('wlc.st_code','st_name','wlc.pc_no','pc_name','lead_cand_party','lead_cand_name','trail_cand_party','trail_cand_name','margin','wlc.status', DB::raw("(SELECT sum(scheduled_round) as scheduled_round FROM round_master as rm WHERE rm.st_code = wlc.st_code and rm.pc_no = wlc.pc_no group by rm.st_code,rm.pc_no) as scheduled_round" ));
		 
		 if(isset($data['state']) && $data['state']){
			 $sql->where('wlc.st_code',$data['state']);
		 }
		 
		if(isset($data['pc_no']) && $data['pc_no']){
			 $sql->where('wlc.pc_no',$data['pc_no']);
		}
		
		if(isset($data['party_type']) && ($data['party_type'] == '1') && isset($data['party'])){
			$sql->where('wlc.lead_cand_partyid',$data['party']);
		}else if(isset($data['party_type']) && ($data['party_type'] == '2') && isset($data['party'])){
			$sql->where('wlc.trail_cand_partyid',$data['party']);
		}else if(isset($data['party']) && $data['party']){
			$sql->where(function($query) use ($data){
                    $query->where('wlc.lead_cand_partyid',$data['party'])
                          ->orWhere('wlc.trail_cand_partyid',$data['party']);
                });
		}
		 
		 
		 $result = $sql->orderBy('wlc.st_code','asc')
		 ->orderBy('pc_name','asc')
		 ->get()
		 ->toArray();
		 
		 $party_data = DB::select('SELECT * FROM (SELECT lead_cand_partyid AS party,lead_cand_party AS party_name FROM `winning_leading_candidate` UNION  SELECT trail_cand_partyid AS party, trail_cand_party AS party_name FROM `winning_leading_candidate` WHERE trail_cand_partyid IS NOT NULL) AS temp ORDER BY party_name');

		 $data['party_data']= $party_data;
				$user_data = Auth::user();
		//
 
	
		if(isset($data['pdf']) && $data['pdf']){				
			$pdf = PDF::loadView('admin.ResultSheet.result-report-pdf',compact('result','user_data'));
			return $pdf->download('Results Report.pdf');
		}
		
		if(isset($data['excel']) && $data['excel']){				
			return Excel::download(new ResultSheetReport($result), 'Results Report.xlsx');	
		}
	



	//dd($result);
	return view('admin.ResultSheet.result-report',compact('result','user_data','data'));
  }
  
  public function party_wise(Request $request, $st_code=''){
	
	
	$data = array();
	 $request->pdf;
	
	$state_list = DB::table('m_election_details')
	->select('ST_CODE')
	->where('ELECTION_TYPEID','=','3')
	//->where('ST_CODE','=','u07')
	->groupBy('ST_CODE')
	->get();
	
	//dd($state_list);
	

	
	foreach($state_list as $key => $raw){
		
		$ST_CODE  = $raw->ST_CODE;

	//dd($raw);
		
	if(isset($ST_CODE) && $ST_CODE <>''){
		

		$topten = DB::table('winning_leading_candidate')
		 ->select('lead_cand_party','lead_cand_hparty', DB::raw('sum(CASE WHEN STATUS = "1" THEN "1" ELSE 0 END) as win'),DB::raw('sum(CASE WHEN STATUS = "0" THEN "1" ELSE 0 END) as lead'))
		 ->where('lead_cand_party', '!='," ")
		 ->where('lead_cand_partyid','!=','743')
		 ->where('ST_CODE',$ST_CODE)
		 ->groupBy('lead_cand_party','lead_cand_hparty')
		 ->orderBy('win','DESC')
		 ->orderBy('lead','DESC')
		 ->limit(10)
		 ->get();
		 
		 $independent = DB::table('winning_leading_candidate')
		 ->select('lead_cand_party','lead_cand_hparty', DB::raw('sum(CASE WHEN STATUS = "1" THEN "1" ELSE 0 END) as win'),DB::raw('sum(CASE WHEN STATUS = "0" THEN "1" ELSE 0 END) as lead'))
		 ->where('ST_CODE',$ST_CODE)
		 ->where('lead_cand_partyid','743')
		 ->groupBy('lead_cand_party','lead_cand_hparty')
		 ->first();
		 
		 $others_data = DB::table('winning_leading_candidate')
		 ->select( DB::raw('sum(CASE WHEN STATUS = "1" THEN "1" ELSE 0 END) as win'),DB::raw('sum(CASE WHEN STATUS = "0" THEN "1" ELSE 0 END) as lead'))
		 ->where('lead_cand_party', '!='," ")
		 ->where('lead_cand_partyid','!=','743')
		 ->where('ST_CODE',$ST_CODE)
		 ->groupBy('lead_cand_party','lead_cand_hparty')
		 ->orderBy('win','DESC')
		 ->orderBy('lead','DESC')
		 ->skip(10)->take(100)
		 ->get()->toArray();
		 
		 
		 $win = 0;
		 $lead = 0;
		 $others = array();
		 
		 if($others_data){
			 foreach($others_data as $raw){			 
				$win+= $raw->win;
				$lead+= $raw->lead;			 
			 }

			 $others['lead_cand_party'] ='Others';
			 $others['lead_cand_hparty'] ='Others';
			 $others['win'] =  $win;
			 $others['lead'] = $lead;
		 }
		 $data[$ST_CODE]['topten'] = $topten;
		 $data[$ST_CODE]['independent'] = $independent;
		 $data[$ST_CODE]['others'] = $others;
 		
		}	
	}
	
	//dd($data);
	$user_data = Auth::user();
	
	if(isset($request->pdf) && ($request->pdf=='yes')){				
	
	
			//return view('admin.ResultSheet.party-wise-pdf', compact('data','user_data'));
			$pdf = PDF::loadView('admin.ResultSheet.party-wise-pdf',compact('data','user_data'));
			return $pdf->download('Party Wise Result Report.pdf');
		}
	
	return view('admin.ResultSheet.party-wise', compact('data','user_data'));
  }
  
  
}  