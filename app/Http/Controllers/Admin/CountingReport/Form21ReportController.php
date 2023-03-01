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

class Form21ReportController extends Controller {
	
	public $view_path     = "admin.countingReport.formReport";
	public $aro           = "aro";
	public $ropc          = "admin.countingReport.formReport";
	public $eci           = "eci";
	public $ceo           = "admin.countingReport.formReport";
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
  
	public function form21Report(Request $request)
	{
	  $user_data = Auth::user();
	  $heading_title = 'Form 21 C/D Download';
	  $state=strip_tags(trim($request->state_code));
	  $totalPC=DB::select(DB::raw("SELECT COUNT(FRM.st_code) AS TOTALPC FROM winning_leading_candidate AS FRM"));
	  $result=DB::select(DB::raw("SELECT COUNT(PC.pc_no) AS TOTALPC, PC.st_code AS STATE,PC.pc_no AS PC_NO,PC.pc_name AS PC_NAME,FRM.form21_path AS FROM21C 
				FROM winning_leading_candidate AS PC LEFT JOIN counting_form21_detail AS FRM ON 
				PC.st_code=FRM.st_code and PC.pc_no=FRM.pc_no GROUP BY PC.st_code ORDER BY PC.st_code"));		
	   $totalUploaded=DB::select(DB::raw("SELECT COUNT(FRM.st_code) AS TOTALPC FROM counting_form21_detail AS FRM"));
	  return view($this->view_path.'.eci-form21-report', ['user_data'=>$user_data,
	  'totalPC'=>$totalPC,'result'=>$result,'totalUploaded'=>$totalUploaded,'state'=>$state,'heading_title'=>$heading_title]);
	}
	

	
}  // end class