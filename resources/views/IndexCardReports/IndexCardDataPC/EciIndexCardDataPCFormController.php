<?php namespace App\Http\Controllers\IndexCardReports\IndexCardDataPC;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Session;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Redirect;
    use Carbon\Carbon;
    use DB;
    use Illuminate\Support\Facades\Hash;
    use Validator;
    use Config;
    use \PDF;
    use App\commonModel;  
    use App\models\Admin\ReportModel;
    use App\models\Admin\PollDayModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\models\Admin\StateModel;

class EciIndexCardDataPCFormController extends Controller {
  

  public function __construct(){
    //$this->middleware('eci');
    $this->commonModel  = new commonModel();
    $this->report_model = new ReportModel();
    $this->voting_model = new PollDayModel();
    if(!Auth::user()){
      return redirect('/officer-login');
    }
  }

  public function responseeditrequest(Request $request){
		
	$user_data = Auth::user();
		
	$data = DB::table('feedback_request_ic')
		->select('feedback_request_ic.*','m_ac.ac_name','m_pc.PC_NAME','m_state.ST_NAME')
		->leftjoin('m_ac', function($join){
			$join->on('feedback_request_ic.for_ac_no','m_ac.AC_NO')
			->on('feedback_request_ic.st_code','m_ac.ST_CODE');
			})
		->leftjoin('m_pc', function($join2){
			$join2->on('feedback_request_ic.for_pc_no','m_pc.PC_NO')
			->on('feedback_request_ic.st_code','m_pc.ST_CODE');
			})
		->join('m_state','feedback_request_ic.st_code','m_state.st_code')
		->orderBy('feedback_request_ic.cr_id','DESC')
		->get()->toArray();
		
		
		//echo '<pre>'; print_r($data); die;
				
    	return view('IndexCardReports.IndexCardDataPCWise.responseeditrequest', compact('user_data','data'));
    }

  public function indexcardpc(Request $request){
      
        $user_data = Auth::user();
        $session = $request->session()->all();
        $uid = $user_data->id;
        $sched=''; $search='';
        $d = $this->commonModel->getunewserbyuserid($user_data->id);
        $d = $this->commonModel->getunewserbyuserid($uid);
     
        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$st_code = $d->st_code;
        //if($user_data->designation == 'CEO'){
        	$pcList = DB::table('m_pc')
        				->select(['PC_NO','PC_NAME'])
        				->where('ST_CODE',$d->st_code)
        				->get()->toArray();
        	// echo "<pre>"; print_r($pcList); die;

                
        	return view('IndexCardReports.IndexCardDataPCWise.acindexcardreportpcselect', compact('session','pcList','user_data','sched','st_code'));
                
//        }elseif($user_data->designation == 'ROPC'){
//            $rRequest = array(
//                '_token' => Session::token(),
//                'st_code' => $user->st_code,
//                'pc' => $user->pc_no
//            );
//            $request->request->add($rRequest);
//        // echo "<pre>"; print_r($user);
//            //return redirect()->route('getindexcarddata');
//            return $this->getindexcarddata($request);
//        // echo "<pre>"; print_r($request->all()); die;
//        }
    }
	
	
	public function approveddata(Request $request){
		
	$user_data = Auth::user();
	$data = DB::table('finalize_request_ic')
					->select('finalize_request_ic.*','m_pc.PC_NAME')
				->join('m_pc',function($join){
					$join->on('finalize_request_ic.st_code','=','m_pc.st_code')
						->on('finalize_request_ic.pc_no','=','m_pc.pc_no');
				})
				->where('finalize_request_ic.id',$request->id)
				->orderBy('finalize_request_ic.id','DESC')
				->first();
				
    	return json_encode($data);
    }
	
	public function submitindexcard(Request $request){
		
	$user = Auth::user();

	$issuearr = serialize($request->indexCard);
	
	DB::table('finalize_request_ic')
					->where('finalize_request_ic.id',$request->id)
					->update(['review_status' => $request->reviewstatus,'issue'=>$issuearr,'review_by'=>$user->officername,'review_comment'=>$request->comments,'review_at'=>date('Y-m-d H:i:s')]);
    	return Redirect::to('eci/responseindexcard');
    }
	
	public function responseindexcard(Request $request){
		
		$user_data = Auth::user();
	
		$data = DB::table('finalize_request_ic')
					->select('finalize_request_ic.*','m_state.ST_NAME','m_pc.PC_NAME')
				->join('m_pc',function($join){
					$join->on('finalize_request_ic.st_code','=','m_pc.st_code')
						->on('finalize_request_ic.pc_no','=','m_pc.pc_no');
				})
				->join('m_state','finalize_request_ic.st_code','m_state.st_code')
				->orderBy('finalize_request_ic.id','DESC')
				->get();
				
		//echo '<pre>';
		//print_r($data); die;
		
    	return view('IndexCardReports.IndexCardDataPCWise.responseindexcard', compact('user_data','data'));
    }
}  // end class