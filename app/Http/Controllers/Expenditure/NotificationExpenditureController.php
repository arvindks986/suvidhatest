<?php

namespace App\Http\Controllers\Expenditure;

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
use App\models\Expenditure\ExpenditureModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\Classes\xssClean;
use Illuminate\Support\Facades\URL;
use App\models\Expenditure\DeoexpenditureModel;
use MPDF;

class NotificationExpenditureController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
	 public  $expdb;
    public function __construct() {
		##############Connect with Expenditure DataBase#############
      // $expdb='exp_pc_2019_5_general';
        $this->middleware(function ($request, $next){
             $DB_DATABASE = strtolower(Session::get('DB_DATABASE'));
          $m_election_history = DB::connection("mysql_database_history")->table("m_election_history")->select('m_election_history.exp_db_name')->where("db_name", $DB_DATABASE)->first();
		    $this->expdb=$m_election_history->exp_db_name; 
           config(['database.connections.mysql.host' => '10.247.219.232']);
          
           config(['database.connections.mysql.database' => $this->expdb]);
             config(['database.connections.mysql.username' => 'etsuser']);
             config(['database.connections.mysql.password' => 'Ets@123#']);
			config(['database.connections.mysql.options' =>[\PDO::ATTR_EMULATE_PREPARES =>true]]);
           DB::purge('mysql');
            DB::connection('mysql');
            return $next($request); 
       });
        ############################################################

         $this->middleware('adminsession');
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ceo');
        $this->commonModel = new commonModel();
        $this->expenditureModel = new ExpenditureModel();
        $this->xssClean = new xssClean;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected function guard() {
        return Auth::guard();
    }

// end MasterData function  
    /**
     * @author Devloped By : Shishir Sharma
     * @author Devloped Date : 22-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return notificationmessage By DEO fuction     
     */
	 
	  public function scrutiny(Request $request) { //dd($request->all());
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
		//echo "dsd";die;
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $st_code = $d->st_code;

                $scrutinycandidate = DB::table('expenditure_notification')->select('expenditure_reports.created_at','expenditure_reports.finalized_status','expenditure_reports.final_by_ro','expenditure_reports.final_by_eci','expenditure_reports.final_by_ceo','expenditure_reports.candidate_id','expenditure_reports.ST_CODE as st_code','expenditure_reports.constituency_no as pc_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
					        ->leftjoin('candidate_nomination_detail', 'expenditure_notification.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->leftjoin('expenditure_reports','expenditure_reports.candidate_id','=','candidate_nomination_detail.candidate_id')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('expenditure_reports.final_by_ro', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
							->Where('expenditure_notification.ceo_read_status', '=', '0')
                            ->Where('expenditure_notification.st_code', '=',$st_code)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
				$request->session()->put('countscrutiny', '0');
				$data=array("ceo_read_status"=>1);
				$this->commonModel->updatedata('expenditure_notification','st_code',$st_code,$data);
				
				return view('admin.pc.ceo.Expenditure.scrutiny',['user_data' => $d,'scrutinycandidate' => $scrutinycandidate,"check_filter"=>"0"]); 
          
         //   DB::enableQueryLog(); 
                //echo "<pre>";
				//print_r($totalContestedCandidate);
				//die;
              } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBydataentryStart TRY CATCH ENDS HERE   
    }
	
	
    public function allscrutiny(Request $request) { //dd($request->all());
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $st_code = $d->st_code;

                $conditions = "";
                if (!empty($_GET['state'])) {
                    $state = $_GET['state'];
                    $conditions .= "AND candidate_nomination_detail.st_code='$state' ";
                }

                if (!empty($_GET['pc'])) {
                    $pc = $_GET['pc'];
                    $conditions .= "AND candidate_nomination_detail.pc_no='$pc' ";
                    $cons_no = $pc;
                }
                $receivefilter=!empty($_GET['receivefilter'])?trim($_GET['receivefilter']):'';
                 if ($receivefilter=='y'  ) {                     
                    $conditions .= " AND date_of_receipt !=''";                     
                    }
                 else if ($receivefilter=='n') {                     
                    $conditions .= " AND date_of_receipt =''";                     
                    }
                     else{
                        $conditions .= "";  
                     }  
                    

                $cons_no = !empty($cons_no) ? $cons_no : '0';
                if (!empty($conditions)) {
                      $scrutinycandidate = DB::select("select `candidate_personal_detail`.`cand_name`, `candidate_personal_detail`.`candidate_id`, `m_party`.`PARTYNAME`, `candidate_nomination_detail`.`st_code`, `candidate_nomination_detail`.`pc_no`, `expenditure_reports`.`created_at`, `expenditure_reports`.`final_by_ro`,`expenditure_reports`.`date_of_receipt`,`expenditure_reports`.`final_action`, `expenditure_reports`.`report_submitted_date`, `expenditure_reports`.`finalized_status`, `expenditure_reports`.`final_by_ceo`, `expenditure_reports`.`final_action` from `expenditure_notification` inner join `candidate_nomination_detail` on `expenditure_notification`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` inner join `candidate_personal_detail` on `candidate_personal_detail`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` inner join `m_party` on `candidate_nomination_detail`.`party_id` = `m_party`.`CCODE` inner join `m_symbol` on `candidate_nomination_detail`.`symbol_id` = `m_symbol`.`SYMBOL_NO` inner join `expenditure_reports` on `expenditure_reports`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` where `candidate_nomination_detail`.`st_code` = '$st_code' and `candidate_nomination_detail`.`application_status` = '6' and `candidate_nomination_detail`.`finalaccepted` = '1' and `candidate_nomination_detail`.`symbol_id` <> '200' and `candidate_personal_detail`.`cand_name` <> 'NOTA' and (`expenditure_reports`.`final_action` = 'Notice Issued' or `expenditure_reports`.`final_action` = 'Reply Issued' or `expenditure_reports`.`final_action` = 'Hearing Done' or `expenditure_reports`.`final_by_ro` = '1') $conditions group by `expenditure_reports`.`candidate_id`");
                } else {
                    $scrutinycandidate = DB::table('expenditure_notification')
                            ->select('candidate_personal_detail.cand_name',
                                    'candidate_personal_detail.candidate_id',
                                    'm_party.PARTYNAME',
                                    'candidate_nomination_detail.st_code',
                                    'candidate_nomination_detail.pc_no',
                                    'expenditure_reports.created_at',
                                    'expenditure_reports.final_by_ro',
                                    'expenditure_reports.date_of_receipt',
                                    'expenditure_reports.report_submitted_date',
                                    'expenditure_reports.finalized_status',
                                    'expenditure_reports.final_by_ceo',
                                    'expenditure_reports.final_action'
                            )
                            ->join('candidate_nomination_detail', 'expenditure_notification.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->join('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')                                                      
                            ->where(function($q) { 
                                $q->where('expenditure_reports.final_action', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', 'Hearing Done')
                                ->orWhere('expenditure_reports.final_by_ro', '1');
                            })
                            ->groupBY('expenditure_reports.candidate_id')
                            ->get();
                           
                }



                //dd($scrutinycandidate);
                //
                //DB::enableQueryLog();
                return view('admin.pc.ceo.Expenditure.scrutiny', ['user_data' => $d, 'scrutinycandidate' => $scrutinycandidate, 'cons_no' => $cons_no, "check_filter" => '1']);

                //echo "<pre>";
                //print_r($totalContestedCandidate);
                //die;
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBydataentryStart TRY CATCH ENDS HERE   
    }

	
    public function proceedprofile(Request $request) {
		
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $stcode = $d->st_code;
               
                $candidate_id = $_GET['candidate_id'];
                $profileData = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                        ->join("m_election_details", function($join) {
                            $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                            ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                        })
                        ->where('candidate_nomination_detail.st_code', $stcode)
                         
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.party_id', '<>', '1180')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
                        ->where('m_election_details.CONST_TYPE', '=', 'PC')
                        ->get();               
                         return view('admin.expenditure.proceedprofile', compact('profileData'));
                  
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }
	
	 public function updatecomment(Request $request) {
		 $candidateid=$request->candidateid;
		 $ceo_message=$request->comment;
		
		 $this->commonModel->updatedata('expenditure_notification', 'candidate_id', $candidateid, array("ceo_message" => $ceo_message));
		 $request->session()->flash('success', 'Comment done successfully');
		 return Redirect('/pcceo/allscrutiny');
	 }
	 
	 public function printScrutinyReport($candidateId) {

       if (Auth::check()) {
           $user = Auth::user();

           $candidate_id = base64_decode($candidateId);
//           $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
//        $candiatePcName =  !empty($candiatePcName)? $candiatePcName->PC_NAME:'---';

           $scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidate_id);


           $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidate_id);
           $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidate_id);
           $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidate_id);

           $pdf = MPDF::loadView('admin.expenditure.pdf_ro', compact('expensesourecefundbyitem', 'scrutinyReportData', 'expenseunderstated', 'expenseunderstatedbyitem'));
           return $pdf->stream('Ro.scrunity-report.pdf');

           //return view('admin.expenditure.pdf_ro');
       } else {
           return redirect('/officer-login');
       }
   }


public function getpclist(request $request) {
        //dd($request->all());
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $stcode = $request->input('state');
            // $all_pc = $this->commonModel->getpcbystate($stcode);
            $all_pc = DB::table('m_pc')
                            ->where('ST_CODE', $stcode)->orderBy('PC_NO', 'asc')->get();
        }
        return $all_pc;
    }
    public function updateReceived(Request $request) {

        $request = (array) $request->all();
        $checkArrayData = [];
        $actionStatus = '';
        unset($request["_token"]);
        $final_action = !empty($request['final_action']) ? $request['final_action'] : '';
        $checkArrayDateids = "";
        if (!empty($request['received'])) {
            foreach ($request['received'] as $candidateId) {

                if ($final_action == 'Received') {
                    $check_data_recieved = DB::table('expenditure_reports')->where('candidate_id', $candidateId)
                            ->where(function($query) {
                                $query->whereNull('expenditure_reports.date_of_receipt');
                                $query->orwhere('expenditure_reports.date_of_receipt', '=', '');
                            })
                            ->first();
                    if (!empty($check_data_recieved)) {
                        $checkArrayDateids = $candidateId;
                        $checkArrayData['date_of_receipt'] = date('Y-m-d');
                    }
                } else {
                    $check_data_recieved = DB::table('expenditure_reports')->where('candidate_id', $candidateId)->whereNotNull('expenditure_reports.date_of_receipt')->first();
                    //print_r($check_data_recieved);
                    if (!empty($check_data_recieved)) {
                        $checkArrayDateids = $candidateId;
                        $checkArrayData['final_action'] = $final_action;
                        $checkArrayData['final_by_ceo'] = '1';
                    }
                }


                if (!empty($checkArrayData)) {
                    $actionStatus = DeoexpenditureModel::updateData($checkArrayData, $checkArrayDateids);
                }
            }
        } else {

            echo 'Please Checked At Least One.';
            die;
        }

        if (!empty($actionStatus)) {
            echo'Saved successfully';
            die;
        } else {
            echo'Already action done.';
            die;
        }
    }

}