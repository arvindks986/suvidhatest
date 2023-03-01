<?php
namespace App\Http\Controllers\Admin\CountingReport;

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
use PDF;
// use Excel;
use App\commonModel;  
use App\models\Admin\ReportModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class ScheduleReportController extends Controller {

	public $view_path     = "admin.countingReport.scheduleReport.eci";
	public $aro           = "aro";
	public $ropc            = "admin.countingReport.scheduleReport.ropc";
	public $eci           = "eci";
	public $ceo           = "admin.countingReport.scheduleReport.eco";
    protected $userId;
	
    public function __construct() {
		$this->middleware(['auth:admin','auth']);
        $this->middleware('eci');
        $this->middleware(function (Request $request, $next) {
            if (!\Auth::check()) {
               return redirect('login')->with(Auth::logout());
            }
            $this->userId = \Auth::id(); // you can access user id here

            return $next($request);
        });
    }
  
	public function scheduleReport(Request $request)
	{
	  $user_data = Auth::user();
	  $heading_title = 'Scheduled Rounds Report';
	  $data['m_state']=DB::table('m_state')->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
        ])->where('m_election_details.CONST_TYPE','PC')->where('election_status','1')->select("m_state.*")->orderBy('ST_NAME','ASC')->get();	  
	  $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master ORDER BY st_code,pc_no,ac_no"));	
	  $state=strip_tags(trim($request->state_code));
	  $pc_no=strip_tags(trim($request->pc_no));
	  $ac_no=strip_tags(trim($request->ac_no)); 
	  $pc="";
	  $ac="";
	  $state_query="";
	  $urlpdf='0/0/0';
	  $urlexcel='0/0/0';  
	  if(isset($state) && isset($pc_no)  && isset($ac_no) && $state!="")
	  {			
           if($state)  
		   {
			 //echo $state; die;
			 $state_query=" WHERE st_code='$state'"; 
		   }
		   if($pc_no!=0)
		   {
			 $pc=" AND pc_no=$pc_no";
		   }
		   if(isset($ac_no) && $ac_no!=0)
		   {
			  $ac=" AND ac_no=$ac_no";
		   }
        $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master$state_query$pc$ac ORDER BY st_code,pc_no,ac_no"));
        $urlpdf=$state.'/'.$pc_no.'/'.$ac_no;
	    $urlexcel=$state.'/'.$pc_no.'/'.$ac_no;	
	    }
	  return view($this->view_path.'.eci-schedule-report', ['user_data'=>$user_data,'data'=>$data,'result'=>$result,'state'=>$state,'urlpdf'=>$urlpdf,'urlexcel'=>$urlexcel,'pc_no'=>$pc_no,'ac_no'=>$ac_no,'heading_title'=>$heading_title]);
	}
	
	public function scheduleReportPDF($state="",$pc_no="",$ac_no="")
	{
		
	  $user_data =   Auth::user();	 
	  $heading_title = 'Schedule Round Report PDF';
	  $state_name=DB::table('m_state')->where('ST_CODE',$state)->first();	
      $pc_name=DB::table('m_pc')->where('ST_CODE',$state)->where('PC_NO',$pc_no)->first();
      $ac_name=DB::table('m_ac')->where('ST_CODE',$state)->where('AC_NO',$ac_no)->first();
	  $state_query="";
	  $ac="";
	  $pc="";	  
	      if($state)
		   {
			   $state_query=" WHERE st_code='$state'";
		   }
		   if($pc_no!="" && $pc_no!=0)
		   {
			$pc=" AND pc_no=$pc_no";
		   }
		   if($ac_no!="" && $ac_no!=0)
		   {
			  $ac=" AND ac_no=$ac_no";
		   }
      $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master$state_query$pc$ac ORDER BY st_code,pc_no,ac_no"));	
	  $date = date('Y-m-d-H:i:s');
	  $pdf = PDF::loadView($this->view_path.'.eci-schedule-report-pdf', compact('result','date','pc_no','ac_no','state','user_data','heading_title'));
	  return $pdf->download($date.'-eci-schedule-report.pdf');
	}
	
	public function scheduleReportExcel($state="",$pc_no="",$ac_no="")
	{
		
	  $user_data =   Auth::user();
	  $heading_title = 'Schedule Report Excel';  
      $state_query="";
	  $ac="";
	  $pc="";	  
	      if($state)
		   {
			   $state_query=" WHERE st_code='$state'";
		   }
		   if($pc_no!="" && $pc_no!=0)
		   {
			$pc=" AND pc_no=$pc_no";
		   }
		   if($ac_no!="" && $ac_no!=0)
		   {
			  $ac=" AND ac_no=$ac_no";
		   }
      $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master$state_query$pc$ac ORDER BY st_code,pc_no,ac_no"));	
	  $dataResult=[];
	  $headings[]=['S.No','State Name','PC Name','PC No','AC Name','AC No','Scheduled Rounds','Completed Rounds','Pending Rounds'];
	  $export_data[]=[];
	  for($i=0;$i<count($result);$i++)
		{
			$completed_round=completeRound($result[$i]->STATE,$result[$i]->PC_NO,$result[$i]->AC_NO);
			$pending=$result[$i]->S_ROUND-$completed_round;
			$val=($completed_round)?($completed_round):'0';
			$result_pending=($pending)?($pending):'0';

			$export_data[] = [
							$i+1,
							getstatebystatecode($result[$i]->STATE)->ST_NAME,
							getpcbypcno($result[$i]->STATE,$result[$i]->PC_NO)->PC_NAME,
							$result[$i]->PC_NO,
							getacbyacno($result[$i]->STATE,$result[$i]->AC_NO)->AC_NAME,
							$result[$i]->AC_NO,
							$result[$i]->S_ROUND,
							$val,
							$result_pending,
			   ];

			
		}
	  
	  $data= json_decode(json_encode($dataResult), true);
      $date = date('Y-m-d-H:i:s');
	  $type='csv';

	  $name_excel = $date.'-eci-schedule-report';
      return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 

    //   return Excel::create($date.'-eci-schedule-report', function($excel) use ($data) {
    //         $excel->sheet('mySheet', function($sheet) use ($data)
    //         {
    //             $sheet->fromArray($data);
    //         });
    //     })->download($type);
	}
	
	
	public function pcList($s_code="")
	{
		$loggedin_userid = Auth::user()->user_id;
		if($s_code)
		{
		$pc=DB::table('m_pc as pc')->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','pc.ST_CODE'],
          ['m_election_details.CONST_NO', '=','pc.PC_NO'],
        ])->where('m_election_details.CONST_TYPE','PC')->where('election_status','1')
                ->select('pc.PC_NO AS pc_no','pc.PC_NAME AS pc_name')
				->where('pc.ST_CODE', '=', $s_code)
				->orderByRaw('pc.PC_NO','ASC')
				->get();
		$myData='';
        $myData.='<select name="pc_no" id="pcno" class="form-control"  required onchange="getACList(this.value);">';
		$myData.='<option value="">--Please Select--</option>'; 
        $myData.='<option value="0">Select All</option>';   
        foreach($pc as $data)
		{
        $myData.='<option value="'.$data->pc_no.'">'.$data->pc_no.' -'.$data->pc_name.'</option>';    
        }
        $myData.='</select>';
        return $myData;
	    }
		else
		{  
		$myData='';
		$myData='<select name="pc_no" id="pcno" class=" form-control" required onchange="getACList(this.value);">
		                    <option value="">---Select PC---</option>
							<option value="0">Select All</option>
				</select>';
		return $myData;
		}
	}
	
	public function acList($s_code="",$pc_no="")
	{
		$loggedin_userid = Auth::user()->user_id;
		if($pc_no)
		{
		$ac=DB::table('m_ac as ac')
                ->select('ac.AC_NO AS ac_no','ac.AC_NAME AS ac_name')
				->where('ac.ST_CODE', '=', $s_code)
				->where('ac.PC_NO', '=', $pc_no)
				->orderByRaw('ac.AC_NO','ASC')
				->get();
		$myData='';
        $myData.='<select name="ac_no" id="acno" class="form-control" required >';
        $myData.='<option value="">---Please Select---</option>';   
		$myData.='<option value="0">Select All</option>';   
        foreach($ac as $data)
		{
        $myData.='<option value="'.$data->ac_no.'">'.$data->ac_no.' -'.$data->ac_name.'</option>';    
        }
        $myData.='</select>';
        return $myData;
	    }
		else
		{
		$myData='';
		$myData='<select name="ac_no" id="acno" class=" form-control" required>
							<option value="">---Please Select---</option>
							<option value="0">Select All</option>
				</select>';
		return $myData;
		}
	}

	
}  // end class