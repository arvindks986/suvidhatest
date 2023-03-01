<?php

namespace App\Http\Controllers\IndexCardReports\StatisticalReportsCurrent;

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


class StatisticalReportsCurrentVolumeTwoController extends Controller
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
	//statewise seat won
	  public function getStatewiseSeatWon(Request $request)
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
		//echo "<pre>"; print_r($session); die;
		$election_detail = $session['election_detail'];
		$user_data = $d;
		//end session data
		//dd($session);
		DB::enableQueryLog();
		//$year=$session['election_detail']['year'];
		$stcode=$session['election_detail']['st_code'];
		$stname=$session['election_detail']['st_name'];
		/*SELECT A.PARTYABBRE, A.PARTYTYPE, C.total_valid_votes, sum(C.e_all_t) as totalelectors, Count(B.leading_id) as wonseat from `m_party` AS A join winning_leading_candidate as B on A.CCODE = B.lead_cand_partyid join `t_pc_ic` AS C on C.`st_code` = B.`st_code` WHERE C.st_code='s01' GROUP by A.PARTYABBRE*/
		$rows = array(
		            'C.PARTYABBRE',
                'C.PARTYNAME',
                'C.PARTYTYPE',
                //'D.st_name',  		          
		          	//DB::raw('SUM(C.e_all_t) as totalelectors'),
		          	DB::raw('COUNT(E.leading_id) as wonseat'),
		          	DB::raw('SUM(F.total_valid_votes) as totalvotes')
               		);
       		$data = DB::table('cand_cont_ic AS A')
               ->select($rows)
              ->join('candidate_nomination_detail AS B','A.con_cand_id','B.candidate_id')
              ->join('m_party AS C', 'B.party_id',
              	'C.CCODE')
              ->join('m_state AS D','A.st_code','D.st_code')
              ->join('winning_leading_candidate AS E', 'B.candidate_id','E.candidate_id')
              ->join('t_pc_ic AS F',function($join){
                                $join->on('B.pc_no','F.pc_no')
                                    ->on('B.st_code','F.st_code');
                            })
               ->where('F.st_code',$stcode)
               ->groupBy('C.CCODE')
               
               ->get();
               	$queue = DB::getQueryLog();
				//echo '<pre>'; print_r($queue);die;
				 //dd($data);

         //total electors
         $totalelectors=DB::table('t_pc_ic')
                          ->select(DB::raw('SUM(e_all_t) as totalelectors'),DB::raw('SUM(total_valid_votes) as totalvalidvotes'))
                          ->where('st_code',$stcode)
                          ->first();

            //dd($totalelectors);

				 if($request->path() == 'pcceo/StatewiseSeatWon'){
					 return view('IndexCardReports.StatisticalReportsCurrent.Vol2.statewise-seat-won',compact('data','totalelectors','stname','user_data','sched'));

				}elseif($request->path() == 'pcceo/StatewiseSeatWonPDF'){
				 //return view('IndexCardReports.StatisticalReportsCurrent.Vol2.statewise-seat-won-pdf',compact('data','totalelectors','stname','user_data','sched'));
				$pdf=PDF::loadView('IndexCardReports.StatisticalReportsCurrent.Vol2.statewise-seat-won-pdf',[
				'totalelectors'=>$totalelectors,
				'data'=>$data,
				'stname'=>$stname,
				'user_data'=>$user_data,
				'sched'=>$sched]);
			return $pdf->download('statewise-seat-won.pdf');
						}

    }
    //PCWiseDistributionVotesPolled
    public function getPCWiseDistributionVotesPolled(Request $request)
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
		//echo "<pre>"; print_r($session); die;
		$election_detail = $session['election_detail'];
		$user_data = $d;
		//end session data
		DB::enableQueryLog();
        //$year=$session['election_detail']['year'];
        $stcode=$session['election_detail']['st_code'];
        $stname=$session['election_detail']['st_name'];
       $vselect = array(
                'A.PC_NO',
                'A.PC_NAME',           
                'e_gen_t',
                'B.e_ser_t',   
                'B.v_votes_evm_all',  
                'B.postal_valid_votes',              
                'B.total_votes_nota',              
                'B.r_votes_evm',              
                'B.postal_vote_rejected',              
                'B.e_all_t',              
                'B.tendered_votes',              
                'B.mock_poll_evm',   
                'C.lead_total_vote'           
                //DB::raw('SUM(C.e_all_t) as totalelectors'),
               // DB::raw('COUNT(B.leading_id) as wonseat'),
                //DB::raw('COUNT(E.total_valid_vote) as totalvotes')
                  );
       $vWhere=array(
              'B.st_code'=>$stcode
              //'C.status'=>'1'
              );
          $data = DB::table('m_pc AS A')
                     ->select($vselect)              
                    ->join('t_pc_ic AS B', function($join){
                                    $join->on('B.st_code','A.ST_CODE')
                                        ->on('B.pc_no','A.PC_NO');
                                })
                    ->join('winning_leading_candidate AS C', function($join){
                          $join->on('C.st_code','A.ST_CODE')
                              ->on('C.pc_no','A.PC_NO');
                    })
               
                     ->where($vWhere)                     
                     ->groupby ('A.PC_NO', 'A.ST_CODE')
                      ->get()->toArray();
                $queue = DB::getQueryLog();
        //echo '<pre>'; print_r($queue);die;
        //dd($data);

				if($request->path() == 'pcceo/PCWiseDistributionVotesPolled'){
					 return view('IndexCardReports.StatisticalReportsCurrent.Vol2.pcwise-distribution-of-valid-votes',compact('data','stname','user_data','sched'));
				}elseif($request->path() == 'pcceo/PCWiseDistributionVotesPolledPDF'){
					//return view('IndexCardReports.StatisticalReportsCurrent.Vol2.pcwise-distribution-of-valid-votes-pdf',compact('data','stname','year'));
					 $pdf=PDF::loadView('IndexCardReports.StatisticalReportsCurrent.Vol2.pcwise-distribution-of-valid-votes-pdf',[
					'stname'=>$stname,
					'data'=>$data,
					'user_data'=>$user_data,
					'sched'=>$sched]);
				return $pdf->download('pcwise-distribution-votes-polled.pdf');
				}

    }
}

