<?php

namespace App\Http\Controllers\IndexCardReports\IndexCardReportPC;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth AS Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Config;
use \PDF;
use MPDF;
use App\commonModel;  
use App\adminmodel\CEOModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\adminmodel\CEOPCModel;
use App\adminmodel\PCCeoReportModel;
use App\Classes\xssClean;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;

ini_set("memory_limit","850M");
set_time_limit('120');
ini_set("pcre.backtrack_limit", "5000000");

class IndexCardReportPCController extends Controller
{
	public function __construct(){
       $this->middleware('adminsession');
       $this->middleware(['auth:admin','auth']);
       $this->middleware('ceo');
       $this->commonModel = new commonModel();
       $this->ceomodel = new CEOPCModel();
            $this->pcceoreportModel = new PCCeoReportModel();
       $this->xssClean = new xssClean;
	}
	 public function getIndexCardReportPC(Request $request)
    {
    	// session data
		$user = Auth::user();
		$uid=$user->id;
		$d=$this->commonModel->getunewserbyuserid($user->id);
		$d=$this->commonModel->getunewserbyuserid($uid);
		$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          $sched=''; $search='';
          $status=$this->commonModel->allstatus();
          if(isset($ele_details)) {  $i=0;
            foreach($ele_details as $ed) {
               $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               $const_type=$ed->CONST_TYPE;
             }
          }
          $session['election_detail'] = array();
       // echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
	   //end session data
		//$session = $request->session()->all();
    	//dd($session);
    	
    		//$year=$session['election_detail']['year'];
    		$state=$session['election_detail']['st_name'];
			DB::enableQueryLog();

    		$tvdata = DB::table('t_pc_ic AS tpi')
					->join(DB::raw('(SELECT MAX(id) AS id,MAX(created_at) AS created_at FROM t_pc_ic GROUP BY st_code, pc_no) AS B'),'tpi.id','B.id')
    				->join('m_pc as mpc', function($join){
    					$join->on('tpi.st_code','mpc.ST_CODE')
    						->on('tpi.pc_no','mpc.PC_NO');
    				})
    				->where(['tpi.st_code' => $session['election_detail']['st_code'], 'tpi.ac_no' => null])
    				
					->get()->toArray();
					
			$query = DB::getQueryLog();

		//echo "<pre>";	print_r($query); die;

			
			$update_id=DB::table('cand_cont_ic')
							->select(DB::raw('MAX(update_id) AS max'))
							->where('st_code',$session['election_detail']['st_code'])
							->first();
           		// echo '<pre>'; print_r(($update_id->max)?($update_id->max+1):0);die;

			foreach ($tvdata as $key) {
           		// echo '<pre>'; print_r($key);die;
           		$sWhere = array(
           			'cci.st_code' 		=> $key->st_code,
           			'cci.pc_no'			=> $key->pc_no,
           			'cci.schedule_id'	=> $key->schedule_id
           		);

				$candidateData = DB::table('cand_cont_ic AS cci')
									->join('candidate_personal_detail AS cpd','cci.con_cand_id','cpd.candidate_id')
									->join('candidate_nomination_detail AS cnd','cnd.candidate_id','cci.con_cand_id')
									->join('m_party AS mp','mp.CCODE','cnd.party_id')
									->where($sWhere)
									->groupBy('cci.con_cand_id')
									->get()->toArray();
				$queue = DB::getQueryLog();
				//echo '<pre>'; print_r($queue);die;
				$key->candidate_data = (object)$candidateData;
			}
			// echo "<pre>"; print_r($tvdata); die;
			
			if($request->path() == 'pcceo/indexCardBriefReport'){
				return view('IndexCardReports.IndexCardReportPC.indexcard-brief-report',compact('tvdata','user_data','sched'));
			}elseif($request->path() == 'pcceo/indexCardBriefReportPDF'){
				//return view('IndexCardReports.IndexCardReportPC.indexcard-brief-report-pdf',compact('tvdata','user_data','sched'));
				$pdf=PDF::loadView('IndexCardReports.IndexCardReportPC.indexcard-brief-report-pdf',[
				'tvdata'=>$tvdata,
				'state'=>$state
				]);
        
				return $pdf->download('index-card-brief-report.pdf');				
			
			}
    }
	
}

    