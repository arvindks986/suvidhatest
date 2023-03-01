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
use Excel;
use App\commonModel;  
use App\models\Admin\ReportModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;

class FormDownloadController extends Controller {
	
	public $view_path     = "admin.countingReport.form21c";
	public $aro           = "aro";
	public $ropc          = "admin.countingReport.form21c";
	public $eci           = "eci";
	public $ceo           = "admin.countingReport.form21c";
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
  
	public function form21Download(Request $request)
	{
	  $user_data = Auth::user();
	  $heading_title = 'Form 21 C/D Download';
	  $data['m_state']=DB::table('m_state')->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
        ])->where('m_election_details.CONST_TYPE','PC')
	 ->where('election_status','1')->orderBy('m_state.ST_NAME','ASC')->get();
	  
	  $result=DB::select(DB::raw("SELECT PC.st_code AS STATE,PC.pc_no AS PC_NO,FRM.form21_path AS FROM21C 
				FROM winning_leading_candidate AS PC LEFT JOIN counting_form21_detail AS FRM ON 
				PC.st_code=FRM.st_code AND PC.pc_no=FRM.pc_no ORDER BY PC.st_code,PC.pc_no"));
				
	  $state=strip_tags(trim($request->state_code));
	  $pc_no=strip_tags(trim($request->pc_no));
	  $state_query="";
	  $pc="";
	  if(isset($state) && isset($pc_no) && $state!="")
	  {	  
		    if($state) 
			 {
				$state_query=" WHERE PC.ST_CODE='$state'";  
		     }
		     if($pc_no!=0)
			{
			  $pc=" AND PC.PC_NO=$pc_no";
		    }  
			$result=DB::select(DB::raw("SELECT PC.st_code AS STATE,PC.pc_no AS PC_NO,FRM.form21_path AS FROM21C 
				FROM winning_leading_candidate AS PC LEFT JOIN counting_form21_detail AS FRM ON 
				PC.st_code=FRM.st_code AND PC.pc_no=FRM.pc_no $state_query$pc ORDER BY PC.st_code,PC.pc_no"));				       
	      }
	  return view($this->view_path.'.eci-form21c-download-report', ['user_data'=>$user_data,'data'=>$data,'result'=>$result,'state'=>$state,'pc_no'=>$pc_no,'heading_title'=>$heading_title]);
	}
	
	public function pcList($s_code="")
	{
		$loggedin_userid = Auth::user()->user_id;
		if($s_code)
		{
		$pc=DB::table('m_pc as pc')
		->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','pc.ST_CODE'],
          ['m_election_details.CONST_NO', '=','pc.PC_NO'],
        ])
        ->where('election_status','1')
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