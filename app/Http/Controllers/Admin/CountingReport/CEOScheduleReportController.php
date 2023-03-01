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

class CEOScheduleReportController extends Controller {

	public $view_path     = "admin.countingReport.scheduleReport.ceo";
	public $aro           = "aro";
	public $ropc          = "ropc";
	public $eci           = "eci";
	public $ceo           = "ceo";
    protected $userId;
	
    public function __construct() {
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('ceo');		
	    $this->commonModel = new commonModel();
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
	  $state = $user_data->st_code;
	  $pc_no= strip_tags(trim($request->pc_no));
	  $ac_no= strip_tags(trim($request->ac_no));
	  //SELECT * FROM `m_election_details` WHERE `ST_CODE`='S22' AND `CONST_TYPE`='PC'

	  //$data['m_pc']=DB::table('m_pc')->where('ST_CODE',$state)->orderBy('PC_NO','ASC')->get();

	  $data['m_pc']=DB::table('m_election_details')->where('ST_CODE',$state)->where('CONST_TYPE','PC')->orderBy('CONST_NO','ASC')->get();

	  $pc_name=DB::table('m_pc')->where('ST_CODE',$state)->where('PC_NO',$pc_no)->first();  
	  $heading_title = 'Scheduled Rounds Report';	  
	  $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master WHERE st_code='$state' order by pc_no,ac_no"));	
	  $ac="";
	  $urlpdf='0/0';
	  $urlexcel='0/0';	
	  $get_ac=[];
	  if(isset($pc_no) && $pc_no!="" && $pc_no!=0)
	  { 
		   if(isset($ac_no) && $ac_no!=0)
		   {
			  $ac=" AND ac_no=$ac_no";
		   }
        $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master WHERE st_code='$state' AND pc_no=$pc_no$ac order by pc_no,ac_no"));
        $urlpdf=$pc_no.'/'.$ac_no;
	    $urlexcel=$pc_no.'/'.$ac_no;
	    }
	   // dd($data);
	  return view($this->view_path.'.ceo-schedule-report', ['user_data'=>$user_data,'data'=>$data,'result'=>$result,'state'=>$state,'urlpdf'=>$urlpdf,'urlexcel'=>$urlexcel,'pc_no'=>$pc_no,'ac_no'=>$ac_no,'heading_title'=>$heading_title]);
	}

	public function scheduleReportPDF($pc_no="",$ac_no)
	{
	  $user_data =   Auth::user();
	  $state = $user_data->st_code;
	  $heading_title = 'Scheduled Rounds Report PDF';
	  $data['m_state']=DB::table('m_state')->where('ST_CODE',$state)->first();	
      $pc_name=DB::table('m_pc')->where('ST_CODE',$state)->where('PC_NO',$pc_no)->first();
      $ac_name=DB::table('m_ac')->where('ST_CODE',$state)->where('AC_NO',$ac_no)->first();	
      $ac="";
	  if($pc_no!=0 && $pc_no!="")
	  {
		   if($ac_no!="" && $ac_no!=0)
		   {
			  $ac=" AND ac_no=$ac_no";
		   }
        $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master WHERE st_code='$state' AND pc_no=$pc_no$ac order by pc_no,ac_no"));
	  }
	  else
	  {
		  $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master WHERE st_code='$state' order by pc_no,ac_no")); 
	  }
	   $date = date('Y-m-d-H:i:s');
	  $pdf = PDF::loadView($this->view_path.'.ceo-schedule-report-pdf', compact('result','data','pc_no','ac_no','state','pc_name','ac_name','user_data','heading_title'));
	  return $pdf->download($date.'-'.$state.'-ceo-schedule-report.pdf');
	}
	
	public function scheduleReportExcel($pc_no="",$ac_no="")
	{
	  $user_data = Auth::user();
	  $state = $user_data->st_code;
	  $data['heading_title'] = 'Schedule Report Excel';
	  $ac="";
	  if($pc_no!=0 && $pc_no!="")
	  {
		   if($ac_no!="" && $ac_no!=0)
		   {
			  $ac=" AND ac_no=$ac_no";
		   }
        $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master WHERE st_code='$state' AND pc_no=$pc_no$ac order by pc_no,ac_no"));
	  }
	  else
	  {
		  $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master WHERE st_code='$state' order by pc_no,ac_no")); 
	  }
	  $dataResult=[];
	  $export_data[] = ['Schedule Report Excel'];
      $headings[]=[];
	  $i=0;
	  $dataResult[$i]['S.No']='S.No';				
		$dataResult[$i]['AC Name']='AC Name';
		$dataResult[$i]['AC No']='AC No';
		$dataResult[$i]['Scheduled Rounds']='Scheduled Rounds';
		$dataResult[$i]['Completed Rounds']='Completed Rounds';
		$dataResult[$i]['Pending Rounds']='Pending Rounds';
	  
	  //dd($dataResult);
	  
	  $j=0;
	  for($i=1; $i<= count($result);$i++)
	{
			
			$export_data[] = [
				$completed_round=completeRound($result[$j]->STATE,$result[$j]->PC_NO,$result[$j]->AC_NO),
				$pending=$result[$j]->S_ROUND-$completed_round,
				$val=($completed_round)?($completed_round):'0',
				$result_pending=($pending)?($pending):'0',
				
				$dataResult[$i]['S.No']=$i,				
				$dataResult[$i]['AC Name']=getacbyacno($result[$j]->STATE,$result[$j]->AC_NO)->AC_NAME,
				$dataResult[$i]['AC No']=$result[$j]->AC_NO,
				$dataResult[$i]['Scheduled Rounds']=$result[$j]->S_ROUND,
				$dataResult[$i]['Completed Rounds']=$val,
				$dataResult[$i]['Pending Rounds']=$result_pending,
			   ];

$j++;
		}
	  
	  $data= json_decode(json_encode($dataResult), true);
      $date = date('Y-m-d-H:i:s');
	  $type='csv';

	  $name_excel = $date.'-'.$state.'-eci-schedule-report';

	  return Excel::download(new ExcelExport($headings, $dataResult), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');        

    //   return Excel::create($date.'-'.$state.'-eci-schedule-report', function($excel) use ($data) {
    //         $excel->sheet('mySheet', function($sheet) use ($data)
    //         {
    //             $sheet->fromArray($data);
    //         });
    //     })->download($type);


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
        $myData.='<select name="ac_no" id="acno" class="form-control" required>';
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
		$myData='<select name="ac_no" id="acno" class="form-control" required>
							<option value="">---Please Select---</option>
							<option value="0">Select All</option>
				</select>';
		return $myData;
		}
	}

	
}  // end class