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
class ROScheduleReportController extends Controller {

	public $view_path     = "admin.countingReport.scheduleReport.ropc";
	public $aro           = "aro";
	public $ropc          = "ropc";
	public $eci           = "eci";
	public $ceo           = "admin.countingReport.scheduleReport.eco";
    protected $userId;
	
    public function __construct() {		
		$this->middleware('adminsession');
		$this->middleware(['auth:admin','auth']);
		$this->middleware('ro');
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
	  $pc_no = $user_data->pc_no;
	  $ac_no= strip_tags(trim($request->ac_no));
	  $data['m_ac']=DB::table('m_ac')->where('ST_CODE',$state)->where('PC_NO',$pc_no)->orderBy('AC_NO','ASC')->get();
	  $ac_name=DB::table('m_ac')->where('ST_CODE',$state)->where('AC_NO',$ac_no)->first();
	  //ADD
      $d=$this->commonModel->getunewserbyuserid($user_data->id);	  
      $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
      //	  
	  $heading_title = 'Scheduled Round Report';	  
	  $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master WHERE st_code='$state' AND pc_no=$pc_no order by ac_no"));	
	  $ac="";
	  $urlpdf='0';
	  $urlexcel='0';		
	  if(isset($ac_no) && $ac_no!=0 && $ac_no!="")
	  { 
        $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master WHERE st_code='$state' AND pc_no=$pc_no AND ac_no=$ac_no order by ac_no"));
        $urlpdf=$ac_no;
	    $urlexcel=$ac_no;	
	    }
	  return view($this->view_path.'.ropc-schedule-report', ['user_data'=>$user_data,'data'=>$data,'result'=>$result,'state'=>$state,'urlpdf'=>$urlpdf,'urlexcel'=>$urlexcel,'ele_details'=>$ele_details,'ac_no'=>$ac_no,'heading_title'=>$heading_title ]);
	}
	
	public function scheduleReportPDF($ac_no)
	{
	  $user_data =   Auth::user();
	  $data = array();
	  $state = $user_data->st_code;
	  $pc_no = $user_data->pc_no; 
	  $heading_title = 'Scheduled Rounds Report PDF';
	  $state_name=DB::table('m_state')->where('ST_CODE',$state)->first();	
      $pc_name=DB::table('m_pc')->where('ST_CODE',$state)->where('PC_NO',$pc_no)->first();
	  if($ac_no) {
      $m_ac=DB::table('m_ac')->where('ST_CODE',$state)->where('AC_NO',$ac_no)->first();	
	  } else
	  {
		$m_ac=""; 
	  }	
      if($ac_no==0 && $ac_no!="")	
	  {		  
	  $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master WHERE st_code='$state' AND pc_no=$pc_no order by ac_no"));	  
	  } 
	  else 
	  {
        $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master WHERE st_code='$state' AND pc_no=$pc_no AND ac_no=$ac_no order by ac_no"));	
	  }
	  $date = date('Y-m-d-H:i:s');
	  $pdf = PDF::loadView($this->view_path.'.ropc-schedule-report-pdf', compact('result','data','pc_no','ac_no','state','user_data','heading_title'));
	  return $pdf->download($date.'-'.$state.'-'.$pc_no.'-ro-pc-schedule-report.pdf');
	}
	
	public function scheduleReportExcel($ac_no="")
	{
	  $user_data =   Auth::user();
	  $state = $user_data->st_code;
	  $pc_no = $user_data->pc_no;
	  $data['heading_title'] = 'Schedule Round Report EXCEL';
      if($ac_no==0 && $ac_no!="")	
	  {		  
	  $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master WHERE st_code='$state' AND pc_no=$pc_no order by ac_no"));	  
	  } 
	  else 
	  {
        $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master WHERE st_code='$state' AND pc_no=$pc_no AND ac_no=$ac_no order by ac_no"));	
	  }
	  
	  $dataResult=[];
	  $export_data[]=['Schedule Round Report EXCEL'];
	  $headings[]=[];
	  
	  //dd($result);
	  
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
		
		
		//dd($export_data);
	  
	  $data= json_decode(json_encode($dataResult), true);
      $date = date('Y-m-d-H:i:s');
	  $type='csv';

	  $name_excel = $date.'-'.$state.'-'.$pc_no.'-ro-pc-schedule-report';
    return Excel::download(new ExcelExport($headings, $dataResult), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 

    //   return Excel::create($date.'-'.$state.'-'.$pc_no.'-ro-pc-schedule-report', function($excel) use ($data) {
    //         $excel->sheet('mySheet', function($sheet) use ($data)
    //         {
    //             $sheet->fromArray($data);
    //         });
    //     })->download($type);


	}

	
}  // end class
