<?php

namespace App\Http\Controllers\Expenditure;
ini_set('memory_limit', '-1');
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
use MPDF;
use App\commonModel;
use App\models\Expenditure\ExpenditureModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\Classes\xssClean;
use Illuminate\Support\Facades\URL;
use App\models\Expenditure\DeoexpenditureModel;
use DateTime;
class ROPCExpenditureController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
   public static $fileLocation;
   public static $fileName;
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

        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('ro');
        $this->commonModel = new commonModel();
        $this->expenditureModel = new ExpenditureModel();
        $this->xssClean = new xssClean;
		self::$fileLocation=public_path() . '/uploads1/ExpenditureReportPC/';
        self::$fileName='/uploads1/ExpenditureReportPC/';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected function guard() {
        return Auth::guard();
    }

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 02-06-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return  expenditure By RO fuction     
     */
    public function expenditure(request $request) {
        //PC ROPC dashboard TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->count();
                // dd($totalContestedCandidate);
                //Get Data entry Start Count 
                $startdatacount = $this->expenditureModel->gettotaldataentryStart('PC', $st_code, $cons_no);
                // dd($startdatacount);
                //Get Data entry Start Count %
                $Percent_startdatacount = $this->get_percentage($totalContestedCandidate, $startdatacount);

                //Get Data entry finalize Count 
                $finaldatacount = $this->expenditureModel->gettotaldataentryFinal('PC', $st_code, $cons_no);
                //Get Data entry finalize Count %
                $Percent_finaldatacount = $this->get_percentage($totalContestedCandidate, $finaldatacount);

                //Get pending Count 
                $pendingdataentrycount = $totalContestedCandidate - $startdatacount;

                //Get pending Count %
                $Percent_pendingdataentrycount = $this->get_percentage($totalContestedCandidate, $pendingdataentrycount);

                //Get Data entry finalize Count 
                $partiallypendingcount = $this->expenditureModel->gettotalpartiallypending('PC', $st_code, $cons_no);

                //Get Data entry finalize Count %
                $Percent_partiallypendingcount = $this->get_percentage($totalContestedCandidate, $partiallypendingcount);

                //Get Data entry defaultercount Count 
                $defaulter = $this->expenditureModel->getdefaulter('PC', $st_code, $cons_no);
                $defaultercount = count($defaulter);

                //Get Data entry defaultercount Count %
                $Percent_defaultercount = $this->get_percentage($totalContestedCandidate, $defaultercount);
                //Get final by ceo Count 
                $finalbyceocount = $this->expenditureModel->gettotalfinalbyceo('PC', $st_code, $cons_no);
                // dd($finalbyceocount);
                //Get Data entry final by ceo %
                $Percent_finalbyceocount = $this->get_percentage($totalContestedCandidate, $finalbyceocount);

                //Get final by eci Count 
                $finalbyecicount = $this->expenditureModel->gettotalfinalbyeci('PC', $st_code, $cons_no);

                //Getfinal by eci Count %
                $Percent_finalbyecicount = $this->get_percentage($totalContestedCandidate, $finalbyecicount);


                //dd($Percent_startdataentry);
                return view('admin.pc.ro.Expenditure.statusdashboard', ['user_data' => $d, 'startdatacount' => $startdatacount, 'Percent_startdatacount' => $Percent_startdatacount, 'totalContestedCandidatecount' => $totalContestedCandidate, 'pendingdataentrycount' => $pendingdataentrycount, 'Percent_pendingdataentrycount' => $Percent_pendingdataentrycount, 'Percent_finaldatacount' => $Percent_finaldatacount, 'partiallypendingcount' => $partiallypendingcount, 'Percent_partiallypendingcount' => $Percent_partiallypendingcount, 'defaultercount' => $defaultercount, 'Percent_defaultercount' => $Percent_defaultercount, 'finalbyceocount' => $finalbyceocount, 'Percent_finalbyceocount' => $Percent_finalbyceocount, 'finalbyecicount' => $finalbyecicount, 'Percent_finalbyecicount' => $Percent_finalbyecicount, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC CEO dashboard TRY CATCH ENDS HERE    
    }

// end dashboard function
// end MasterData function  

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 02-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getcandidateListbyDeo By DEO fuction     
     */
    public function getcandidateList(request $request) {
        //dd($request->all());
       // DB::enableQueryLog();
        if (Auth::check()) {
            $user = Auth::user();
            $uid = $user->id;
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
            if ($check_finalize == '') {
                $cand_finalize_ceo = 0;
                $cand_finalize_ro = 0;
            } else {
                $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                $cand_finalize_ro = $check_finalize->finalized_ac;
            }
            $seched = getschedulebyid($ele_details->ScheduleID);
            $sechdul = checkscheduledetails($seched);
            //dd($d);
            $stcode = $d->st_code;
            $pc_no = $d->pc_no;

            $candList = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                    })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'm_election_details.*', 'expenditure_reports.finalized_status', 'expenditure_reports.created_at as form_fill_start','expenditure_reports.updated_at as finalized_date', 'expenditure_reports.final_by_ro', 
                    'expenditure_reports.last_date_prescribed_acct_lodge', 
                    'expenditure_reports.date_of_declaration', 'expenditure_reports.date_orginal_acct', 'expenditure_reports.date_of_receipt', 'expenditure_reports.date_of_receipt_eci', 'expenditure_reports.date_of_sending_deo', 'expenditure_reports.report_submitted_date', 'expenditure_reports.final_action')
                    ->where('candidate_nomination_detail.st_code', $stcode)
                    ->where('candidate_nomination_detail.pc_no', $pc_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('m_election_details.CONST_TYPE', '=', 'PC')
					->groupBy('candidate_nomination_detail.candidate_id')
                    ->get();
            // dd(DB::getQueryLog());
            // dd($candList);

                     if(!empty($candList))
                       {
                        foreach ($candList as $value) {
                                $getLog = DB::table('expenditure_logs')->where('created_by',$uid)->where('candidate_id',$value->candidate_id)->count();   
                                $value->count_by_ro = $getLog;
                        }
                       }
        // add 24/10/2019 manoj
             $resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
        // end 24/10/2019 manoj 

            return view('admin.pc.ro.Expenditure.scrutinyExpenditure', ['user_data' => $d, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'ele_details' => $ele_details, 'candList' => $candList, 'pc_no' => $pc_no,'resultDeclarationDate'=>$resultDeclarationDate]);
        } else {
            return redirect('/officer-login');
        }
    }

///////candidate list for abstract statement //////////////

    public function candidateList_abstract(request $request) {
        //dd($request->all());
        DB::enableQueryLog();
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
            if ($check_finalize == '') {
                $cand_finalize_ceo = 0;
                $cand_finalize_ro = 0;
            } else {
                $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                $cand_finalize_ro = $check_finalize->finalized_ac;
            }
            $seched = getschedulebyid($ele_details->ScheduleID);
            $sechdul = checkscheduledetails($seched);
            //dd($d);
            $stcode = $d->st_code;
            $pc_no = $d->pc_no;

            $candList = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                    })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'm_election_details.*', 'expenditure_reports.finalized_status')
                    ->where('candidate_nomination_detail.st_code', $stcode)
                    ->where('candidate_nomination_detail.pc_no', $pc_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('m_election_details.CONST_TYPE', '=', 'PC')
                    ->get();
            // dd(DB::getQueryLog());
            // dd($candList);
            return view('admin.pc.ro.Expenditure.candidate_list', ['user_data' => $d, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'ele_details' => $ele_details, 'candList' => $candList, 'pc_no' => $pc_no]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function getStatus($stcode, $pc_no) {
        return DB::select("select candidate_id,finalized_status from expenditure_reports  where expenditure_reports.ST_CODE ='$stcode' and expenditure_reports.constituency_no='$pc_no'");
    }

//end getcandidateListbyDeo
########################Current Status Dashboard  Start By Niraj 16-05-19########################

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 16-05-19
* @author Modified By : 
* @author Modified Date : 
* @author param return status dashboard By ROPC fuction     
*/
public function statusdashboard(Request $request) {
//PC ROPC dashboard TRY CATCH STARTS HERE
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
if ($check_finalize == '') {
	$cand_finalize_ceo = 0;
	$cand_finalize_ro = 0;
} else {
	$cand_finalize_ceo = $check_finalize->finalize_by_ceo;
	$cand_finalize_ro = $check_finalize->finalized_ac;
}
$seched = getschedulebyid($ele_details->ScheduleID);
$sechdul = checkscheduledetails($seched);

$st_code = $d->st_code;
$cons_no = $d->pc_no;
//echo $st_code.'cons_no=>'.$cons_no;
$totalContestedCandidate = DB::table('candidate_nomination_detail')
		->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
		->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
		->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
		->where('candidate_nomination_detail.st_code', '=', $st_code)
		->where('candidate_nomination_detail.pc_no', '=', $cons_no)
		->where('candidate_nomination_detail.application_status', '=', '6')
		->where('candidate_nomination_detail.finalaccepted', '=', '1')
		->where('candidate_nomination_detail.symbol_id', '<>', '200')
		->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->count();
$totalElectedCandidate=DB::table('winning_leading_candidate')
        ->where('winning_leading_candidate.st_code','=',$st_code)
        ->where('winning_leading_candidate.pc_no','=',$cons_no)
        ->count();
// dd($totalContestedCandidate);
//Get Data entry Start Count 
$startdata = $this->expenditureModel->gettotaldataentryStart('PC', $st_code, $cons_no);
$startdatacount=count($startdata);
// dd($startdatacount);
//Get Data entry Start Count %
$Percent_startdatacount = $this->get_percentage($totalContestedCandidate, $startdatacount);

//Get Data entry finalize Count 
$finaldata = $this->expenditureModel->gettotaldataentryFinal('PC', $st_code, $cons_no);
$finaldatacount=count($finaldata);
//Get Data entry finalize Count %
$Percent_finaldatacount = $this->get_percentage($totalContestedCandidate, $finaldatacount);

//Get pending Count 
$pendingdataentrycount = $totalContestedCandidate - $startdatacount;
 
//Get pending Count %
$Percent_pendingdataentrycount = $this->get_percentage($totalContestedCandidate, $pendingdataentrycount);

//Get Data entry finalize Count 
$partiallypending = $this->expenditureModel->gettotalpartiallypending('PC', $st_code, $cons_no);
 $partiallypendingcount=count($partiallypending);
//Get Data entry finalize Count %
$Percent_partiallypendingcount = $this->get_percentage($totalContestedCandidate, $partiallypendingcount);
$defaulter=array();
//Get Data entry defaultercount Count 
$defaulter = $this->expenditureModel->getdefaulter('PC', $st_code, $cons_no);
 $defaulter=(is_array($defaulter)) ? $defaulter : [] ;
      //  dd($defaulter);
        $defaultercount=count($defaulter);
//Get Data entry defaultercount Count %
$Percent_defaultercount = $this->get_percentage($totalContestedCandidate, $defaultercount);

//Get final by ceo Count 
$finalbyceodata = $this->expenditureModel->gettotalfinalbyceo('PC', $st_code, $cons_no);
$finalbyceocount=count($finalbyceodata);
// dd($finalbyceocount);
//Get Data entry final by ceo %
$Percent_finalbyceocount = $this->get_percentage($totalContestedCandidate, $finalbyceocount);

//Get final by eci Count 
$finalbyecidata = $this->expenditureModel->gettotalfinalbyeci('PC', $st_code, $cons_no);
$finalbyecicount=count($finalbyecidata);
//Getfinal by eci Count %
$Percent_finalbyecicount = $this->get_percentage($totalContestedCandidate, $finalbyecicount);
 // return /non return start here
 $totalElectedCandidate=!empty($totalElectedCandidate)?$totalElectedCandidate:0;
 $returncount = $this->expenditureModel->gettotalreturn('PC', $st_code, $cons_no,'Returned');
             
 $totalNominationCandiate=$totalContestedCandidate-$totalElectedCandidate;
 
 $nonreturncount = $this->expenditureModel->gettotalreturn('PC', $st_code, $cons_no,'Non-Returned');
 
  $returncount=!empty($returncount)?count($returncount):0;
  $nonreturncount=!empty($nonreturncount)?count($nonreturncount):0; 

 //Getfinal by eci Count %
 $Percent_returncount = $this->get_percentage($totalElectedCandidate, $returncount);
 $Percent_nonreturncount = $this->get_percentage($totalNominationCandiate, $nonreturncount);
 // end here return /non return

  //Get noticeatdeocount Count 
	$noticeatdeocount = $this->expenditureModel->gettotalnoticeatDEO('PC', $st_code, $cons_no);
	//Get noticeatdeocount Count %
	$Percent_noticeatdeocount = $this->get_percentage($totalContestedCandidate, $noticeatdeocount);

//dd($Percent_startdataentry);
return view('admin.pc.ro.Expenditure.statusdashboard', ['user_data' => $d, 'startdatacount' => $startdatacount, 'Percent_startdatacount' => $Percent_startdatacount, 'totalContestedCandidatecount' => $totalContestedCandidate, 'pendingdataentrycount' => $pendingdataentrycount, 'Percent_pendingdataentrycount' => $Percent_pendingdataentrycount, 'finaldatacount' => $finaldatacount, 'Percent_finaldatacount' => $Percent_finaldatacount, 'partiallypendingcount' => $partiallypendingcount, 'Percent_partiallypendingcount' => $Percent_partiallypendingcount, 'defaultercount' => $defaultercount, 'Percent_defaultercount' => $Percent_defaultercount, 'finalbyceocount' => $finalbyceocount, 'Percent_finalbyceocount' => $Percent_finalbyceocount, 'finalbyecicount' => $finalbyecicount, 'Percent_finalbyecicount' => $Percent_finalbyecicount,'returncount'=>$returncount,'Percent_returncount'=>$Percent_returncount,'nonreturncount'=>$nonreturncount,'Percent_nonreturncount'=>$Percent_nonreturncount,'noticeatdeocount' => $noticeatdeocount, 'Percent_noticeatdeocount' => $Percent_noticeatdeocount,  'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details]);

} else {
return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC CEO dashboard TRY CATCH ENDS HERE    
}

// end dashboard function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 16-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getpendingcandidateList  By ROPC fuction     
     */
    public function getpendingcandidateList(Request $request) {
        //PC ROPC getpendingcandidateList  TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;


                $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();

                $candidate_id=[];

                foreach ($startCandList as $startCandListData) {
                    $candidate_id[] = $startCandListData->candidate_id;
                }
                $pendingCandList = DB::table('candidate_nomination_detail')
                                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                                ->where('candidate_nomination_detail.application_status', '=', '6')
                                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();
                // dd($pendingCandList);
                return view('admin.pc.ro.Expenditure.pending-report', ['user_data' => $d, 'pendingCandList' => $pendingCandList, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details, 'count' => count($pendingCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBydataentryStart TRY CATCH ENDS HERE  
    }

// end dataentry start function

    public function getpendingcandidateListgraph(Request $request) {
        //PC ROPC getpendingcandidateList  TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->count();
                // dd($totalContestedCandidate);
                //Get Data entry Start Count 
                $startdatacount = $this->expenditureModel->gettotaldataentryStart('PC', $st_code, $cons_no);

                //Get pending Count 
                $pendingdataentrycount = $totalContestedCandidate - $startdatacount;

                //Get pending Count %
                $Percent_pendingdataentrycount = $this->get_percentage($totalContestedCandidate, $pendingdataentrycount);

                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';
                $data = [
                    ['Oveall summary', 'Pending / Not Filed'],
                    [$candiatePcName, $Percent_pendingdataentrycount]
                ];
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBydataentryStart TRY CATCH ENDS HERE  
    }

// end dataentry start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 16-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getpartiallypendingcandidateList  By ROPC fuction     
     */
    public function getpartiallypendingcandidateList(Request $request) {

        //PC ROPC getdefaultercandidateList  TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                DB::enableQueryLog();
                $partiallyCandList = DB::table('expenditure_reports')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)
                        // ->where('expenditure_notification.deo_action','0')
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->where('expenditure_reports.final_by_ro', '1')
                        ->whereNotNull('expenditure_reports.date_of_sending_deo')
                        ->where(function($query) {
                            $query->whereNull('expenditure_reports.date_of_receipt');
                            $query->orwhere('expenditure_reports.date_of_receipt', '=', '');
                        })
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();

                // dd($defaulterCandList);
                //dd(DB::getQueryLog());
                return view('admin.pc.ro.Expenditure.partiallypending-report', ['user_data' => $d, 'partiallyCandList' => $partiallyCandList, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details, 'count' => count($partiallyCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC CEO getdefaultercandidateList TRY CATCH ENDS HERE  
    }

// end getpartiallypendingcandidateList start function

    public function getpartiallypendingcandidateListgraph(Request $request) {
        //PC ROPC getpartiallypendingcandidateList  TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->count();

                //Get Data entry finalize Count 
                $partiallypendingcount = $this->expenditureModel->gettotalpartiallypending('PC', $st_code, $cons_no);
                //dd($totalContestedCandidate);
                //Get Data entry finalize Count %
                $Percent_partiallypendingcount = $this->get_percentage($totalContestedCandidate, $partiallypendingcount);


                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';
                $data = [
                    ['Oveall summary', 'Partially Pending'],
                    [$candiatePcName, $Percent_partiallypendingcount]
                ];
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC getpartiallypendingcandidateList TRY CATCH ENDS HERE  
    }

// end getpartiallypendingcandidateList start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 16-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getdefaultercandidateList  By ROPC fuction     
     */
    public function getdefaultercandidateList(Request $request) {
        //PC ROPC getdefaultercandidateList  TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                DB::enableQueryLog();
                $defaulterCandList = DB::table('expenditure_understated')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                        ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                        ->having('totalobseramnt', '<=', 'totalcandamnt')
                        ->where('expenditure_understated.ST_CODE', '=', $st_code)
                        ->where('expenditure_understated.constituency_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->groupBy('expenditure_understated.candidate_id')
                        ->get();

                // dd($defaulterCandList);
                //dd(DB::getQueryLog());
                return view('admin.pc.ro.Expenditure.defaulter-report', ['user_data' => $d, 'defaulterCandList' => $defaulterCandList, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details, 'count' => count($defaulterCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC CEO getdefaultercandidateList TRY CATCH ENDS HERE  
    }

// end getpartiallypendingcandidateList start function

    public function getdefaultercandidateListgraph(Request $request) {
        //PC ROPC getdefaultercandidateList  TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->count();
                //Get Data entry defaultercount Count 
                $defaulter = $this->expenditureModel->getdefaulter('PC', $st_code, $cons_no);
                $defaultercount = count($defaulter);

                //Get Data entry defaultercount Count %
                $Percent_defaultercount = $this->get_percentage($totalContestedCandidate, $defaultercount);

                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';
                $data = [
                    ['Oveall summary', 'Defaulter Case'],
                    [$candiatePcName, $Percent_defaultercount]
                ];
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC getpartiallypendingcandidateList TRY CATCH ENDS HERE  
    }

// end getpartiallypendingcandidateList start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 18-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByfinalizeData By ROPC fuction     
     */
    public function candidateListByfiledData(Request $request) {
        //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $finalCandList = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
						->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)
                        //->where('expenditure_reports.finalized_status','=','1') 
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                //dd($DataentryStartCandList);
                return view('admin.pc.ro.Expenditure.filed-report', ['user_data' => $d, 'finalCandList' => $finalCandList, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details, "count" => count($finalCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByfiledData TRY CATCH ENDS HERE   
    }

// end candidateListByfiledData start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListfinalbyCEO By ROPC fuction     
     */
    public function candidateListfinalbyCEO(Request $request) {
        //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $finalbyceoCandList = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                      ->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)
                        //->where('expenditure_notification.ceo_action','0')
                        ->where('expenditure_reports.final_by_ceo', '1')
                        ->whereNotNull('expenditure_reports.date_of_receipt')
                        ->whereNull('expenditure_reports.date_of_receipt_eci')
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                //dd($DataentryStartCandList);
                return view('admin.pc.ro.Expenditure.finalbyceo-report', ['user_data' => $d, 'finalbyceoCandList' => $finalbyceoCandList, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details, "count" => count($finalbyceoCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListfinalbyCEO TRY CATCH ENDS HERE   
    }

// end candidateListfinalbyCEO start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListfinalbyECI By ROPC fuction     
     */
    public function candidateListfinalbyECI(Request $request) {
        //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $finalbyeciCandList = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)
                        //->where('expenditure_notification.eci_action','0')
                        ->where('expenditure_reports.final_by_eci', '1')
                        ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                        //->where('expenditure_reports.final_action','==','Closed')
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                //dd($DataentryStartCandList);
                return view('admin.pc.ro.Expenditure.finalbyeci-report', ['user_data' => $d, 'finalbyeciCandList' => $finalbyeciCandList, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details, "count" => count($finalbyeciCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListfinalbyECI TRY CATCH ENDS HERE   
    }

// end candidateListfinalbyECI start function


/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 23-06-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return getnoticeatDEO By ECI fuction     
 */
public function getnoticeatDEO(Request $request){
    //ROPC getnoticeatDEO TRY CATCH STARTS HERE
    try{
    if(Auth::check()){
      $user = Auth::user();
      $uid=$user->id;
      $d=$this->commonModel->getunewserbyuserid($user->id);
      $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
     
     $st_code=$d->st_code;
     $cons_no=$d->pc_no;
     $cons_no=!empty($cons_no) ? $cons_no : 0;
     $st_code=!empty($st_code) ? $st_code : 0;
       // echo $st_code.'cons_no'.$cons_no; die;
       if($st_code !='0' && $cons_no !='0'){
    $noticeatDEO = DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
        ->where('expenditure_reports.ST_CODE','=',$st_code)
        ->where('expenditure_reports.constituency_no','=',$cons_no) 
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('expenditure_reports.final_by_ceo','0')
        ->where('expenditure_reports.final_by_ro','0')
       ->whereNotNull('expenditure_reports.date_sending_notice_service_to_deo')
       ->where(function($q) {
           $q->where('expenditure_reports.final_action','=','Notice Issued')
             ->orWhere('expenditure_reports.final_action','=','Reply Issued')
             ->orWhere('expenditure_reports.final_action','=','Hearing Done');
           })
        ->groupBy('expenditure_reports.candidate_id')
        ->get(); 
    }
        //dd($DataentryStartCandList);
        return view('admin.pc.ro.Expenditure.noticeatdeo',['user_data' => $d,'noticeatDEO' => $noticeatDEO,'edetails'=>$ele_details,'st_code'=>$st_code,'cons_no'=>$cons_no,'count'=>count($noticeatDEO)]); 
        
    }
    else {
        return redirect('/officer-login');
    }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//ROPC getnoticeatDEO  TRY CATCH ENDS HERE   
    }   // end //ROPC getnoticeatDEO  function
    
         /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-07-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getnoticeatDEOEXL By ECI fuction     
     */
     //ROPC getnoticeatDEO  EXCEL REPORT STARTS
    public function getnoticeatDEOEXL(Request $request){  
    ////ROPC getnoticeatDEO  EXCEL REPORT TRY CATCH BLOCK STARTS
    try{
        if(Auth::check()){
        $user = Auth::user();
        $uid=$user->id;
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        $st_code=$d->st_code;
        $cons_no=$d->pc_no;
        $cons_no=!empty($cons_no) ? $cons_no : 0;
        $st_code=!empty($st_code) ? $st_code : 0;
          // echo $st_code.'cons_no'.$cons_no; die;
        $cur_time    = Carbon::now();
    
    \Excel::create('NoticeatDEOCandidate_'.'_'.$cur_time, function($excel) use($st_code,$cons_no) { 
    $excel->sheet('Sheet1', function($sheet) use($st_code,$cons_no) {
    
        if($st_code !='0' && $cons_no !='0'){
        $noticeatDEO = DB::table('expenditure_reports')
            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
            ->where('expenditure_reports.ST_CODE','=',$st_code)
            ->where('expenditure_reports.constituency_no','=',$cons_no) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('expenditure_reports.final_by_ceo','0')
            ->where('expenditure_reports.final_by_ro','0')
           ->whereNotNull('expenditure_reports.date_sending_notice_service_to_deo')
           ->where(function($q) {
               $q->where('expenditure_reports.final_action','=','Notice Issued')
                 ->orWhere('expenditure_reports.final_action','=','Reply Issued')
                 ->orWhere('expenditure_reports.final_action','=','Hearing Done');
               })
            ->groupBy('expenditure_reports.candidate_id')
            ->get(); 
        }
    
            $arr  = array();
            $TotalUsers = 0;
            $user = Auth::user();
            $count = 1;
            foreach ($noticeatDEO as $candDetails) {
                $st=getstatebystatecode($candDetails->st_code);
                //dd($candDetails);
                $pcDetails=getpcbypcno($candDetails->st_code,$candDetails->pc_no);
                $date = new DateTime($candDetails->finalized_date);
                //echo $date->format('d.m.Y'); // 31.07.2012
                $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
                $data =  array(
                $pcDetails->PC_NO.'-'.$pcDetails->PC_NAME,
                $candDetails->cand_name,
                $candDetails->PARTYNAME,
                $lodgingDate
                    );
                    $TotalUsers =count($noticeatDEO);
                    array_push($arr, $data);
                            // }
                            $count++;
                        }
                $totalvalues = array('Total',$TotalUsers);
                // print_r($totalvalues);die;
                array_push($arr,$totalvalues);
                    $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                                'PC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging'
                        )
                    );
                });
            })->export('xls');
            }else {
                return redirect('/admin-login');
            } 
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
    
        }
        ////ROPC getnoticeatDEO EXCEL REPORT TRY CATCH BLOCK ENDS
        
    }
######################end Current Status Dashboard ##############################################
########################MIS  Start By Niraj 27-05-19########################

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 16-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return status dashboard By ROPC fuction     
     */
    public function getOfficersmis(Request $request) {
        //PC ROPC dashboard TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->count();
                // dd($totalContestedCandidate);
                //Get Data entry Start Count 
                $startdatacount = $this->expenditureModel->gettotaldataentryStart('PC', $st_code, $cons_no);
                // dd($startdatacount);
                //Get Data entry Start Count %
                $Percent_startdataentry = $this->get_percentage($totalContestedCandidate, $startdatacount);

                //Get Data entry finalize Count 
                $finaldatacount = $this->expenditureModel->gettotaldataentryFinal('PC', $st_code, $cons_no);
                //Get Data entry finalize Count %
                $Percent_finaldatacount = $this->get_percentage($totalContestedCandidate, $finaldatacount);

                //Get pending Count 
                $pendingdataentrycount = $totalContestedCandidate - $startdatacount;

                //Get pending Count %
                $Percent_pendingdataentrycount = $this->get_percentage($totalContestedCandidate, $pendingdataentrycount);

                //Get Data entry finalize Count 
                $partiallypendingcount = $this->expenditureModel->gettotalpartiallypending('PC', $st_code, $cons_no);

                //Get Data entry finalize Count %
                $Percent_partiallypendingcount = $this->get_percentage($totalContestedCandidate, $partiallypendingcount);

                //Get Data entry defaultercount Count 
                $defaulter = $this->expenditureModel->getdefaulter('PC', $st_code, $cons_no);
                $defaultercount = count($defaulter);
                //Get Data entry defaultercount Count %
                $Percent_defaultercount = $this->get_percentage($totalContestedCandidate, $defaultercount);
                //Get final by ceo Count 
                $finalbyceocount = $this->expenditureModel->gettotalfinalbyceo('PC', $st_code, $cons_no);
                // dd($finalbyceocount);
                //Get Data entry final by ceo %
                $Percent_finalbyceocount = $this->get_percentage($totalContestedCandidate, $finalbyceocount);

                //Get final by eci Count 
                $finalbyecicount = $this->expenditureModel->gettotalfinalbyeci('PC', $st_code, $cons_no);

                //Getfinal by eci Count %
                $Percent_finalbyecicount = $this->get_percentage($totalContestedCandidate, $finalbyecicount);


                //dd($Percent_startdataentry);
                return view('admin.pc.ro.Expenditure.mis-officer', ['user_data' => $d, 'totalContestedCandidatecount' => $totalContestedCandidate, 'pendingdataentrycount' => $pendingdataentrycount, 'Percent_pendingdataentrycount' => $Percent_pendingdataentrycount, 'Percent_finaldatacount' => $Percent_finaldatacount, 'partiallypendingcount' => $partiallypendingcount, 'Percent_partiallypendingcount' => $Percent_partiallypendingcount, 'defaultercount' => $defaultercount, 'Percent_defaultercount' => $Percent_defaultercount, 'finalbyceocount' => $finalbyceocount, 'Percent_finalbyceocount' => $Percent_finalbyceocount, 'finalbyecicount' => $finalbyecicount, 'Percent_finalbyecicount' => $Percent_finalbyecicount, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC CEO dashboard TRY CATCH ENDS HERE    
    }

// end dashboard function
######################end MIS REPORT ##############################################

    public function deoForm(Request $request) {
        $request = (array) $request->all();
        try {
            $namePrefix = \Route::current()->action['prefix'];
            $checkArrayData = [];

            foreach ($request as $key => $req_data) {
                $xss = new xssClean;
                $checkArrayData[$key] = $xss->clean_input($req_data);
            }

            $candidateId = !empty($checkArrayData['candidate_id']) ? $checkArrayData['candidate_id'] : 0;
            $isexist = DeoexpenditureModel::isCandidate($candidateId);
            unset($checkArrayData['_token']);
//                // check result exist or not 
            if ($isexist) { // update new record
                unset($checkArrayData['candidate_id']);
                unset($checkArrayData['_token']);
                $actionStatus = DeoexpenditureModel::updateData($checkArrayData, $candidateId);
            } else {// add new record
                $actionStatus = DeoexpenditureModel::add($checkArrayData);
            }
            Session::put('message', "Record updated successfully.");
            return redirect($namePrefix . '/scrutinyExpenditure');
//            if ($actionStatus) {   
//                Session::put('message', "Record updated successfully.");
//                 
//                return redirect($namePrefix . '/scrutinyExpenditure');
//            } else {
//                return Redirect('/internalerror')->with('error', 'Internal Server Error');
//            }
        } catch (\Exception $e) {

            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        // }
    }

    public function viewById($candidateId) {
        $data = DeoexpenditureModel::viewById($candidateId);
        return $response = !empty($data) ? json_encode($data) : [];
    }

    public function defectdeoform(Request $request) {
//        $candidateId = "3";
//        $request = $_POST;
//        $user = Auth::user();
//        $uid = $user->id;
//        $namePrefix = \Route::current()->action['prefix'];
//        unset($request['_token']);
//        $countdata = count($request);
//        try {
//            $datas = [];
//            for ($i = 1; $i <= 9; $i++) {
//
//
//                $req['candidate_id'] = "2";
//                $req['defect_type_id'] = $i;
//                $req['status'] = !empty($request[$i]['understated']['status']) ? $xss->clean_input($request[$i]['understated']['status']) : "";
//                $req['comment'] = !empty($request[$i]['understated']['comment']) ? $xss->clean_input($request[$i]['understated']['comment']) : "";
//                $dataInserted = $this->commonModel->insertData('expenditure_defects', $req);
//            }
//
//            return 1;
//        } catch (\Exception $e) {
//
//            return 0;
//        }
    }

    public function tracking_status(Request $request) {

        $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
        return view('admin.expenditure.tracking-status', ['user_data' => $d, 'ele_details' => $ele_details]);
    }

    public function deoformview($candidateId) {
        $candidateId = base64_decode($candidateId);
        $candidateData = DeoexpenditureModel::viewById($candidateId);
        //dd($candidateData);
        $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);
// add 24/10/2019 manoj
        $resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
        
        // end 24/10/2019 manoj 
 


         $candidateData = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                    })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->join('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
                    ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*','candidate_personal_detail.candidate_id as c_id', 'm_election_details.*', 'expenditure_reports.*', 'm_party.PARTYNAME')
                    ->where('candidate_nomination_detail.st_code', $d->st_code)
                    ->where('candidate_nomination_detail.pc_no', $d->pc_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                     ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.candidate_id', '=', $candidateId)
                    ->where('m_election_details.CONST_TYPE', '=', 'PC')
                    ->first();

                   // dd($candList);

        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
        $winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
        $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
        if ($check_finalize == '') {
            $cand_finalize_ceo = 0;
            $cand_finalize_ro = 0;
        } else {
            $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
            $cand_finalize_ro = $check_finalize->finalized_ac;
        }
        $seched = getschedulebyid($ele_details->ScheduleID);
        $sechdul = checkscheduledetails($seched);
        $scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidateId);
        // not form entry get pc and pc name        
        $pcdetails =  getpcbypcno($d->st_code, $d->pc_no);
        
       
        $gexExpReport = DB::table('expenditure_reports')->where('candidate_id', $candidateId)->get()->toArray();
        $getCandidateExpData = DB::table('expenditure_understates')->where('candidate_id', $candidateId)->get()->toArray();
        $expenditure_fund_parties = DB::table('expenditure_fund_parties')->where('candidate_id', $candidateId)->get()->toArray();
        $expenditure_fund_source = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)->get()->toArray();
        $getSourceFundData = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)->get()->toArray();
        $getExpData = DB::table('expenditure_understated')->where('candidate_id', $candidateId)->get()->toArray();
        $getExpItem = DB::table('expenditure_items')->get();
        
        
            
        $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
        $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';
        // for file start
        $scrutiny_data=DB::table('expenditure_reports')->select('expenditure_reports.noticefile')
                    ->where('expenditure_reports.candidate_id', '=', $candidateId)->first();
       // $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidateId);
               $expenseunderstated= DB::table('expenditure_understates')->where('candidate_id', $candidateId)->get()->toArray();

             ////////////// file path start ///////
     
             $download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : '';
             if(strpos($download_link1,'ExpenditureReportPC') !==false) { 
                        
                   $download_link1= url($download_link1);              
            }            
            else if(!empty($download_link1) && strpos($download_link1,'ExpenditureReportPC') ==false) {
               
               $download_link1 = url('/uploads/ExpenditureReportPC').'/'.$download_link1;

            } 

             $download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : '';

              if(strpos($download_link2,'ExpenditureReportPC') !==false) { 
                        
                   $download_link2= url($download_link2);              
            }            
            else if(!empty($download_link2) && strpos($download_link2,'ExpenditureReportPC') ==false) {
               
               $download_link2 = url('/uploads/ExpenditureReportPC').'/'.$download_link2;

            } 

            $download_link3=!empty($scrutiny_data->noticefile)? $scrutiny_data->noticefile:'';
              if(strpos($download_link3,'ExpenditureReportPC') !==false) { 
                        
                   $download_link3= url($download_link3);              
            }            
            else if(!empty($download_link3) && strpos($download_link3,'ExpenditureReportPC') ==false) {
               
               $download_link3 = url('/uploads/ExpenditureReportPC').'/'.$download_link3;

            } 


               $download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : ''; 
            if(strpos($download_link4,'ExpenditureReportPC') !==false) { 
                        
                   $download_link4= url($download_link4);              
            }            
            else if(!empty($download_link4) && strpos($download_link4,'ExpenditureReportPC') ==false) {
               
               $download_link4 = url('/uploads/ExpenditureReportPC').'/'.$download_link4;

            } 
            ////////////// file path end ///////
 
          
            // for file end
        // print_r($getCandidateExpData);die;
        return view('admin.pc.ro.Expenditure.deoForm', ['user_data' => $d, 'candidateData' => $candidateData, 'candiatePcName' => $candiatePcName,
            "getCandidateExpData" => $getCandidateExpData, "expenditure_fund_source" => $expenditure_fund_source,
            "expenditure_fund_parties" => $expenditure_fund_parties, 'cand_finalize_ceo' => $cand_finalize_ceo,
            'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched,
            'ele_details' => $ele_details, "getSourceFundData" => $getSourceFundData, "getExpData" => $getExpData,
            "getExpItem" => $getExpItem, "gexExpReport" => $gexExpReport,'scrutinyReportData'=>$scrutinyReportData, 'winn_data' => $winn_data,'pcdetails'=>$pcdetails,
            'download_link1'=>$download_link1 , 'download_link2'=>$download_link2, 'download_link3'=>$download_link3,
            'download_link4'=>$download_link4,'resultDeclarationDate'=>$resultDeclarationDate]);
    }

    public function getPCName($object) {
        $statecode = !empty($object->st_code) ? $object->st_code : 0;
        $districtId = !empty($object->district_no) ? $object->district_no : 0;
        $pcNo = !empty($object->pc_no) ? $object->pc_no : 0;
        return DeoexpenditureModel::getPCName($pcNo, $districtId, $statecode);
    }

   public function updateAccountDeoForm(Request $request) {
        $response = [];
        $request = (array) $request->all();
        $response = [
            'status' => false,
            'message' => false,
            'data' => []
        ];
        $user = Auth::user();
        $uid = $user->id;
        $namePrefix = \Route::current()->action['prefix'];
        $checkArrayData = [];

        foreach ($request as $key => $req_data) {
            $xss = new xssClean;
            $checkArrayData[$key] = $xss->clean_input($req_data);
        }

        //
        $candidateId = !empty($checkArrayData['candidate_id']) ? $checkArrayData['candidate_id'] : 0;
         
        $candidateDetail = $this->commonModel->selectone('candidate_nomination_detail', 'candidate_id', $candidateId);
        $checkArrayData['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
        
        $checkArrayData['constituency_no'] = !empty($candidateDetail->pc_no) ? $candidateDetail->pc_no : "";
        $checkArrayData['created_by'] = $uid;
        $checkArrayData['updated_by'] = $uid;
        $checkArrayData['election_type'] = "General";
		$checkArrayData['election_id'] = !empty($user->election_id)?$user->election_id:0;
       
        $isexist = DeoexpenditureModel::isCandidate($candidateId);
        unset($checkArrayData['_token']);
        unset($checkArrayData['example_length']);
        unset($checkArrayData['candidate_id_base']);


//                // check result exist or not 
        if ($isexist) { // update new record
            unset($checkArrayData['candidate_id']);
            unset($checkArrayData['_token']);
            //print_r($checkArrayData);die;
            $actionStatus = DeoexpenditureModel::updateData($checkArrayData, $candidateId);
            $response = [
                'status' => true,
                'message' => "Account Details updated successfully.",
                'data' => $checkArrayData
            ];
        } else { // add new record               
            $actionStatus = DeoexpenditureModel::add($checkArrayData);
            $response = [
                'status' => true,
                'message' => "Account Details saved successfully.",
                'data' => $checkArrayData
            ];
        }
        echo json_encode($response);
    }
    public function updateDefectDeoForm(Request $request) {
        $response = [];
        $request = (array) $request->all();
        $response = [
            'status' => false,
            'message' => false,
            'data' => []
        ];
        $user = Auth::user();
        $uid = $user->id;

        $namePrefix = \Route::current()->action['prefix'];
        $checkArrayData = [];

        foreach ($request as $key => $req_data) {
            $xss = new xssClean;
            $checkArrayData[$key] = $xss->clean_input($req_data);
        }

        $candidateId = !empty($checkArrayData['candidate_id']) ? $checkArrayData['candidate_id'] : 0;
        $candidateDetail = $this->commonModel->selectone('candidate_nomination_detail', 'candidate_id', $candidateId);
        $checkArrayData['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
        $checkArrayData['constituency_no'] = !empty($candidateDetail->pc_no) ? $candidateDetail->pc_no : "";
        $checkArrayData['created_by'] = $uid;
        $checkArrayData['updated_by'] = $uid;
		$checkArrayData['election_id'] = !empty($user->election_id)?$user->election_id:0;
        $checkArrayData['election_type'] = "General";
        $checkArrayData['noticefile'] = Session::get('noticefile');
        $isexist = DeoexpenditureModel::isCandidate($candidateId);
        unset($checkArrayData['_token']);
//                // check result exist or not 
        if ($isexist) { // update new record
            unset($checkArrayData['candidate_id']);
            unset($checkArrayData['_token']);
            $actionStatus = DeoexpenditureModel::updateData($checkArrayData, $candidateId);
            $response = [
                'status' => true,
                'message' => "Defect Details updated successfully.",
                'data' => $checkArrayData
            ];
        } else { // add new record               
            $actionStatus = DeoexpenditureModel::add($checkArrayData);
            $response = [
                'status' => true,
                'message' => "Defect Details saved successfully.",
                'data' => $checkArrayData
            ];
        }
        echo json_encode($response);
    }

    public function ExpDataEntrySummaryReport(Request $request) {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $cand_finalize_ro = [];
            return view('admin.pc.ro.Expenditure.ExpDataEntrySummaryReport', ['user_data' => $d, 'ele_details' => $ele_details, 'cand_finalize_ro' => $cand_finalize_ro]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function getSummaryGraphData($id) {

        $data = [
            ['State', 'started', 'Finalized'],
            ['Delhi-c', 111, 200],
            ['Punjab-c', 170, 160],
            ['UP-c', 60, 110],
        ];

        $data = json_encode($data);
        return $data;
    }

    public function printScrutinyReport($candidate_id) {

        if (Auth::check()) {
            $user = Auth::user();
            $candidate_id = base64_decode($candidate_id);
            $d = $this->expenditureModel->getunewserbyuserid($user->id, $user->role_id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');

            ////===================
             $pcdetail = DB::table('candidate_nomination_detail')->where('candidate_nomination_detail.candidate_id', $candidate_id)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->first();
        
            $pcNo = !empty($pcdetail->pc_no) ? $pcdetail->pc_no : 0;
            $st_code = !empty($pcdetail->st_code) ? $pcdetail->st_code : 0;           
            $pcData =  getpcbypcno($st_code, $pcNo);


            $district_no = !empty($pcdetail->district_no) ? $pcdetail->district_no : 0;
            $districtDetails = getdistrictbydistrictno($st_code, $district_no);
         
            $electionTypeId = !empty($pcdetail->election_type_id) ? $pcdetail->election_type_id : 0;
           
        
        $candidateName = !empty($ReportSingleData['contensting_candiate']) ? $ReportSingleData['contensting_candiate'] : '';
        // $ELECTION_TYPE = !empty($ReportSingleData['election_type']) ? $ReportSingleData['election_type'] : '';
       // $ELECTION_TYPE="PC";
        $party_id = !empty($pcdetail->party_id) ? $pcdetail->party_id : 0;
        $partyname = getpartybyid($party_id);
        $partyname = !empty($partyname->PARTYNAME) ? $partyname->PARTYNAME : '';
        
        // $ELECTION_ID = !empty($pcdetail->election_id) ? $pcdetail->election_id : 0;

        // echo $pcNO, $ELECTION_ID, $st_code;die;
        $winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $st_code)->where('pc_no', $pcNo)->where('election_id', $user->election_id)->first();
 
            
            
            ///////////////////////
             
            $mpdf = new \Mpdf\Mpdf();

            $candiatePcName = getpcbypcno($st_code, $pcNo);
            $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '';
            $districtDetails = getdistrictbydistrictno($st_code, $district_no);
            
            
            

            $date = date('d-m-Y');
            // $profileData = DB::table('candidate_nomination_detail')
            //         ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            //         ->join("m_election_details", function($join) {
            //             $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
            //             ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
            //         })
            //          ->where('candidate_nomination_detail.application_status', '=', '6')
            //         ->where('candidate_nomination_detail.party_id', '<>', '1180')
            //         ->where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            //          ->where('candidate_nomination_detail.finalaccepted', '=', '1')
            //         ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
            //         ->where('m_election_details.CONST_TYPE', '=', 'PC')
            //         ->get();
            // get CEO status cand_name ELECTION_TYPE
            $candidateprofile=DB::table('candidate_personal_detail')
            ->select('cand_name')
            ->where('candidate_id','=',$candidate_id)
            ->first();
                   // dd($candidate_id);
            $candidateName = !empty($candidateprofile->cand_name) ? $candidateprofile->cand_name : '';
           // $electionType = !empty($profileData[0]) ? $profileData[0]->ELECTION_TYPE : '';
              $electionType =  'PC';
            // $party_id = !empty($profileData[0]) ? $profileData[0]->party_id : '';
            // $partyname = getpartybyid($party_id);
            // $partyname = !empty($partyname) ? $partyname->PARTYNAME : '';
 
           
             

            $date = date('d-m-Y');
            $year = date('Y');
            $title = $date . '_' . "Election Commission of India";
             
            $mpdf->setHeader($candidateName . ' | ' . $electionType . ' | ' . $partyname);

            $mpdf->SetFooter($date . '|' . "Election Commission of India" . '|{PAGENO}');

            $mpdf->SetProtection(array('print'));
            $mpdf->SetTitle($title);
            $mpdf->SetAuthor("Election Commission of India");
            $mpdf->SetWatermarkText("Election Commission of India");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->SetDisplayMode('fullpage');
           // $scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidate_id);
             $scrutinyReportData = DB::table('candidate_nomination_detail')
                    ->join('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                    })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    // ->join('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')

///
                    ->leftjoin('expenditure_fund_parties', 'expenditure_fund_parties.candidate_id', '=', 'candidate_nomination_detail.candidate_id')

                    ->leftjoin('expenditure_understates', 'expenditure_fund_parties.candidate_id', '=', 'candidate_nomination_detail.candidate_id')

                      /*->leftjoin('m_state', 'm_state.ST_CODE', '=', 'expenditure_reports.ST_CODE')*/
                       

                       /*->join("m_pc", function($join) {
                        $join->on("m_pc.PC_NO", "=", "expenditure_reports.constituency_no")
                        ->on("m_pc.ST_CODE", "=", "expenditure_reports.st_code");
                    })*/

////


                     
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
                    /*->where('m_election_details.CONST_TYPE', '=', 'PC')*/
                    ->first();
                     
                    //$scrutinyReportData=['0'=>$scrutinyReportData];
              

                    $scrutiny_data=DB::table('expenditure_reports')
                    ->select('expenditure_reports.report_submitted_date as updated_at')
                    ->where('expenditure_reports.candidate_id', '=', $candidate_id)
                    ->first();
                  
                    $submitedData=!empty( $scrutiny_data)? $scrutiny_data->updated_at:0;
             
            //$expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidate_id);
            $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidate_id);
             
            $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidate_id);

            //dd($scrutinyReportData);
            // $download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : 'N/A';
            // $download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : 'N/A';
            // $download_link3 = !empty($scrutinyReportData[0]->noticefile) ?  $scrutinyReportData[0]->noticefile : 'N/A';
            //  $download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : '';
            $scrutiny_data=DB::table('expenditure_reports')->select('expenditure_reports.updated_at','expenditure_reports.noticefile')
                    ->where('expenditure_reports.candidate_id', '=', $candidate_id)

                    ->first();
             $expenseunderstated= DB::table('expenditure_understates')->where('candidate_id', $candidate_id)->get()->toArray();

              ////////////// file path start ///////
   
             $download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : '';
             if(strpos($download_link1,'ExpenditureReportPC') !==false) { 
                        
                   $download_link1= url($download_link1);              
            }            
            else if(!empty($download_link1) && strpos($download_link1,'ExpenditureReportPC') ==false) {
               
               $download_link1 = url('/uploads/ExpenditureReportPC').'/'.$download_link1;

            } 

             $download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : '';

              if(strpos($download_link2,'ExpenditureReportPC') !==false) { 
                        
                   $download_link2= url($download_link2);              
            }            
            else if(!empty($download_link2) && strpos($download_link2,'ExpenditureReportPC') ==false) {
               
               $download_link2 = url('/uploads/ExpenditureReportPC').'/'.$download_link2;

            } 

            $download_link3=!empty($scrutiny_data->noticefile)? $scrutiny_data->noticefile:'';
              if(strpos($download_link3,'ExpenditureReportPC') !==false) { 
                        
                   $download_link3= url($download_link3);              
            }            
            else if(!empty($download_link3) && strpos($download_link3,'ExpenditureReportPC') ==false) {
               
               $download_link3 = url('/uploads/ExpenditureReportPC').'/'.$download_link3;

            } 


               $download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : ''; 
            if(strpos($download_link4,'ExpenditureReportPC') !==false) { 
                        
                   $download_link4= url($download_link4);              
            }            
            else if(!empty($download_link4) && strpos($download_link4,'ExpenditureReportPC') ==false) {
               
               $download_link4 = url('/uploads/ExpenditureReportPC').'/'.$download_link4;

            } 
            ////////////// file path end ///////
           
            $pdf = view('admin.expenditure.pdf_ro', compact('expensesourecefundbyitem', 'scrutinyReportData', 'districtDetails', 'expenseunderstated', 'expenseunderstatedbyitem', 'submitedData','electionType','download_link1','electionType' ,
                    'download_link2', 'download_link3','download_link4', 'winn_data','partyname'));
            $mpdf->WriteHTML($pdf);
            $mpdf->Output();
 
        } else {
            return redirect('/officer-login');
        }

 
    }

/////////////////manish////////////
    public function GetTrackingReportData(Request $request) {
        if (Auth::check()) {
            $request = (array) $request->all();
            $user = Auth::user();
            $uid = $user->id;
            $namePrefix = \Route::current()->action['prefix'];
            $d = $this->expenditureModel->getunewserbyuserid($user->id, $user->role_id);
            $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray();

            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
            if ($check_finalize == '') {
                $cand_finalize_ceo = 0;
                $cand_finalize_ro = 0;
            } else {
                $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                $cand_finalize_ro = $check_finalize->finalized_ac;
            }
            $seched = getschedulebyid($ele_details->ScheduleID);
            $sechdul = checkscheduledetails($seched);
            try {
                $condtition = "";
                if (!empty($_GET['year'])) {
                    $year = $_GET['year'];
                    $condtition .= " AND YEAR(er.date_of_declaration)='$year'";
                }

                if (!empty($_GET['electionType'])) {
                    $electype = $_GET['electionType'];
                    $condtition .= " AND er.election_type='$electype'";
                }

                $ReportData = $this->expenditureModel->GetExpeditureData($user->role_id, $user->pc_no, $user->st_code, $condtition);
                $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();

                return view('admin.expenditure.tracking_ro', ['user_data' => $d, 'ele_details' => $ele_details, "cand_finalize_ro" => array(), "electionType" => $electionType, "expenditureData" => $ReportData, "total_rec" => count($ReportData), "nature_of_default_ac" => $nature_of_default_ac]);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function editExpenditureData(Request $request, $ReportID) {
        if (Auth::check()) {
            $request = (array) $request->all();
            $user = Auth::user();
            $uid = $user->id;
            $namePrefix = \Route::current()->action['prefix'];
            $d = $this->expenditureModel->getunewserbyuserid($user->id);

            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
            if ($check_finalize == '') {
                $cand_finalize_ceo = 0;
                $cand_finalize_ro = 0;
            } else {
                $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                $cand_finalize_ro = $check_finalize->finalized_ac;
            }
            $seched = getschedulebyid($ele_details->ScheduleID);
            $sechdul = checkscheduledetails($seched);
            $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();
            $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray();

            try {



                $ReportSingleData = $this->expenditureModel->GetExpeditureSingleData(base64_decode($ReportID));
                // print_r($ReportSingleData);die;
                /////check last data inserted for preview/////
                $PreviewData[0] = array();
                if (isset($_GET['id']) && !empty($_GET['id'])) {
                    $lastInserted = base64_decode($_GET['id']);
                    $PreviewData = $this->expenditureModel->singledata($lastInserted);
                }
                return view('admin.expenditure.createmisexpensereport', ['user_data' => $d, 'ele_details' => $ele_details, "cand_finalize_ro" => array(), "electionType" => $electionType, "ReportSingleData" => $ReportSingleData[0], "nature_of_default_ac" => $nature_of_default_ac, "PreviewData" => $PreviewData[0]]);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function confirmReport() {

        $candidate_id = !empty($_GET['candidate_id']) ? $_GET['candidate_id'] : "";
        if (Auth::check()) {
            $user = Auth::user();
            $uid = $user->id;
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $st_code = $d->st_code;
            $pc_no = $d->pc_no;
            $insertdata = ['candidate_id' => $candidate_id, 'st_code' => $st_code, 'constituency_no' => $pc_no, 'deo_action' => '1'];
        }
        $insertComment = $this->commonModel->updatedata('expenditure_reports', 'candidate_id', $candidate_id, array("final_by_ro" => '1', 'signed_file' => Session::get('uploadsigned')));
        if ($insertComment) {
            $this->commonModel->insertData('expenditure_notification', $insertdata);

            return 1;
        } else {
            return 0;
        }
    }

    public function editExpenditureReport(Request $request) {
        if (Auth::check()) {
            $request = (array) $request->all();
            $user = Auth::user();
            $uid = $user->id;
			// add 24/10/2019 manoj
        $resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
        // end 24/10/2019 manoj 
            $candidate_id = base64_decode($_GET['candidate_id']);
            $ReportId = !empty($_GET['candidate_id']) ? $_GET['candidate_id'] : "";
            $namePrefix = \Route::current()->action['prefix'];
            $candidate_data = $this->expenditureModel->getunewserbyuserid_uid_ceo($candidate_id);
            $d = $this->expenditureModel->getunewserbyuserid($user->id, $user->role_id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
            if ($check_finalize == '') {
                $cand_finalize_ceo = 0;
                $cand_finalize_ro = 0;
            } else {
                $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                $cand_finalize_ro = $check_finalize->finalized_ac;
            }
            $seched = getschedulebyid($ele_details->ScheduleID);
            $sechdul = checkscheduledetails($seched);
            $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();
            $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray();

            try {

                $ReportSingleData = $this->expenditureModel->GetExpeditureSingleData(base64_decode($ReportId));
                if (!empty($ReportSingleData)) {
                    $ReportSingleData = (array) $ReportSingleData[0];
                } else {
                    $ReportSingleData = array();
                }
                $countElectedCandidate=$this->getElectedCandidate($candidate_id);


                return view('admin.expenditure.createmisexpensereport', ['user_data' => $d, 'ele_details' => $ele_details, "cand_finalize_ro" => array(), "electionType" => $electionType, "ReportSingleData" => $ReportSingleData, "nature_of_default_ac" => $nature_of_default_ac, "candidate_data" => (array) $candidate_data,'countElectedCandidate'=>$countElectedCandidate,'resultDeclarationDate'=>$resultDeclarationDate]);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        } else {
            return redirect('/officer-login');
        }
    }

    // get elected candidate
    public function getElectedCandidate($candidate_id){
        $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
        $countElectedCandidate=DB::table('winning_leading_candidate')->where('st_code', $ele_details->ST_CODE)
                              ->where('pc_no', $ele_details->CONST_NO)
                              ->where('election_id', $ele_details->ELECTION_ID)
                              ->where('candidate_id', $candidate_id)
                              ->count();
        return $countElectedCandidate;
    }
    public function StoreMisExpenseReport(Request $request) {

        $request = (array) $request->all();
        $users = Session::get('admin_login_details');
        $user = Auth::user();
        $uid = $user->id;
        $role_id = $user->role_id;
        $candidate_id = $request['candidate_id'];
        $comment_by_ro = $request['comment_by_ro'];

        $request['user_id'] = $uid;
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);
        unset($request['signedfile']);
   // validate for return type  start here
   $isElectedCandidate=$this->getElectedCandidate($candidate_id);
   if($isElectedCandidate>0){
       $request['return_status']=='Returned';

       
   }else{
       $request['return_status']=='Non-Returned';        
       
   }
   // validate for return type  end here
        try {
            $data_arr = array();
            foreach ($request as $key => $req_data) {
                $xss = new xssClean;
                $data_arr[$key] = $xss->clean_input($req_data);
            }

            $isexistData = DB::table('expenditure_reports')->where('candidate_id', $candidate_id)->count();
            if ($isexistData > 0) {
                $unsetItems = ['candidate_id', 'constituency_no', 'constituency_nos', 'contensting_candiate',
                    'date_of_declaration', 'user_id'];
                $dataUpdate = array_diff_key($data_arr, array_flip($unsetItems));
                $status = DB::table('expenditure_reports')->where('candidate_id', $candidate_id)->update($dataUpdate);

              ////////////////////////////////// add entry in expenditure action logs/////////////////
               $cdate = date('Y-m-d h:i:s');
               $data_action=array("candidate_id"=>$candidate_id,"deo_action_date"=>$cdate,"deo_comment"=>$comment_by_ro);

               $data_arr_action = array();
                foreach ($data_action as $key => $req_data_action) {
                    $xss = new xssClean;
                    $data_arr_action[$key] = $xss->clean_input($req_data_action);
                }

                $data_actionInserted = $this->commonModel->updatedata('expenditure_action_logs', 'candidate_id', $candidate_id, $data_arr_action);
               


            } else {
                $unsetItems = ['constituency_nos', 'user_id'];
                $dataUpdate = array_diff_key($data_arr, array_flip($unsetItems));
                $status = 1;
                //$status = DB::table('expenditure_reports')->insert($dataUpdate);
            }


            if ($status > 0) {

                Session::put('message', "Saved successfully");
                return redirect($namePrefix . '/editExpenditureReport?candidate_id=' . base64_encode($candidate_id));
            } else {
                Session::put('message', "No change");
                return redirect($namePrefix . '/editExpenditureReport?candidate_id=' . base64_encode($candidate_id));
            }
        } catch (\Exception $e) {

            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

//////////////////////manish////////


    public function getTrackingByROUserId(Request $request) {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

            $filterrequest = $request->all();
            $year = !empty($filterrequest['year']) ? $filterrequest['year'] : '';
            $condtition = "";
            if (!empty($year)) {
                $condtition .= " AND YEAR(date_of_declaration)='$year'";
            }
            $data = DB::select("SELECT
                                   C.candidate_id,
                                   C.cand_name,
                                   C.cand_email,
                                   P.PC_NAME,
                                   R.date_of_declaration
                               FROM
                                   `expenditure_reports` R
                               INNER JOIN candidate_personal_detail C ON
                                   C.candidate_id = R.candidate_id
                               INNER JOIN m_pc P ON
                                    P.PC_NO = R.constituency_no AND P.ST_CODE =R.ST_CODE
                               WHERE
                                   R.constituency_no IN($d->pc_no) $condtition");
            $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();
            $total_rec = count($data);
            return view('admin.expenditure.tracking', ['user_data' => $d, 'ele_details' => $ele_details, "cand_finalize_ro" => array(), "electionType" => $electionType, "expenditureData" => $data, "total_rec" => $total_rec]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function trackingReport(Request $request) {
        try {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
            $stcode = $d->st_code;
            $pc_no = $d->pc_no;
            $stateDetail = getstatebystatecode($stcode);
            $Pcdetail = getpcbypcno($d->st_code, $d->pc_no);

            $PcName = !empty($Pcdetail) ? $Pcdetail->PC_NAME : '';
            $PcNo = !empty($Pcdetail->PC_NO) ? $Pcdetail->PC_NO : '';
             

              
            $districtDetails = getdistrictbydistrictno($stcode, $d->dist_no);
             
            $profileData = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                    })
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('m_election_details.CONST_TYPE', '=', 'PC')
                    ->get();

            $candList = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                    })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('expenditure_fund_parties', 'expenditure_fund_parties.candidate_id', '=', 'candidate_nomination_detail.candidate_id')      
                    ->join('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
                       ->leftjoin("expenditure_understates", function($join) {
                        $join->on("expenditure_understates.candidate_id", "=", "candidate_nomination_detail.candidate_id")
                        ->where("expenditure_understates.understated_type_id", "=", "8");
                })->select('expenditure_fund_parties.*','expenditure_understates.*','candidate_nomination_detail.*', 'candidate_personal_detail.*', 'm_election_details.*', 'expenditure_reports.*', 'm_party.PARTYNAME')
                    ->where('candidate_nomination_detail.st_code', $stcode)
                    ->where('candidate_nomination_detail.pc_no', $pc_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('m_election_details.CONST_TYPE', '=', 'PC')
					->groupBy('candidate_nomination_detail.candidate_id')
                    ->get();
                    
              if(!empty($candList)){
                  $i=0;
               foreach($candList as $cand){
                  $expenditure_understates = DB::table('expenditure_understates')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$stcode)->where('constituency_no',$PcNo)->where('understated_type_id','9')->first();
                  $other_source_cc = DB::table('expenditure_fund_source')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$stcode)->where('constituency_no',$PcNo)
                          ->whereIn('other_source_payment_mode',array('Cheque','Cash'))->sum('other_source_amount');
                  $other_source_kind = DB::table('expenditure_fund_source')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$stcode)->where('constituency_no',$PcNo)
                          ->whereIn('other_source_payment_mode',array('In Kind'))->sum('other_source_amount');
                  $candList[$i]->comment_9 = !empty($expenditure_understates->comment)?$expenditure_understates->comment:"";
                  $candList[$i]->understated_type_id_9 = !empty($expenditure_understates->understated_type_id)?$expenditure_understates->understated_type_id:"";
                  $candList[$i]->other_source_amt_cc = !empty($other_source_cc)?$other_source_cc:"0";
                  $candList[$i]->other_source_amt_kind = !empty($other_source_kind)?$other_source_kind:"0";
                  $i++;
               }
              }
                    
            $profileData = count($profileData) > 0 ? $profileData[0] : [];
           // add 24/10/2019 manoj
              $resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
           // end 24/10/2019 manoj 
            return view('admin.expenditure.tracking_report', ['user_data' => $d, 
                'ele_details' => $ele_details, "cand_finalize_ro" => array(),
                'profileData' => $profileData, 'candList' => $candList,
                'Pcdetail' => $Pcdetail, 'stateDetail' =>$stateDetail, "districtDetails"=>$districtDetails,'winn_data' => $winn_data,'resultDeclarationDate'=>$resultDeclarationDate]);
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function trackingReportprint(Request $request) {
        try {
            $mpdf = new \Mpdf\Mpdf();
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
            $stcode = $d->st_code;
            $pc_no = $d->pc_no;
            $stateDetail = getstatebystatecode($stcode);
            $PcName = getpcbypcno($d->st_code, $d->pc_no);
 
            $Pcdetail = getpcbypcno($d->st_code, $d->pc_no);
 
            
             

              
            $districtDetails = getdistrictbydistrictno($stcode, $d->dist_no);
            $PcName = !empty($PcName) ? $PcName->PC_NAME : '';
            $stateName = !empty($stateDetail->ST_NAME) ? $stateDetail->ST_NAME : '';
            $date = date('d-m-Y');

            /* $ELECTION_TYPE = !empty($ele_details->ELECTION_TYPE) ? $ele_details->ELECTION_TYPE : ''; */
			$ELECTION_TYPE="General PC";
            $date = date('d-m-Y');
            $year = $ele_details->YEAR;
            $title = $date . '_' . "Election Commission of India";
            $mpdf->setHeader($PcName . ' | ' . $ELECTION_TYPE . ' ' . $year . ' | ' . $stateName);

            $mpdf->SetFooter($date . '|' . "Election Commission of India" . '|{PAGENO}');

            $mpdf->SetProtection(array('print'));
            $mpdf->SetTitle($title);
            $mpdf->SetAuthor("Election Commission of India");
            $mpdf->SetWatermarkText("Election Commission of India");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->SetDisplayMode('fullpage');

           $candList = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                    })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('expenditure_fund_parties', 'expenditure_fund_parties.candidate_id', '=', 'candidate_nomination_detail.candidate_id')      
                    ->join('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
                       ->leftjoin("expenditure_understates", function($join) {
                        $join->on("expenditure_understates.candidate_id", "=", "candidate_nomination_detail.candidate_id")
                        ->where("expenditure_understates.understated_type_id", "=", "8");
                })->select('expenditure_fund_parties.*','expenditure_understates.*','candidate_nomination_detail.*', 'candidate_personal_detail.*', 'm_election_details.*', 'expenditure_reports.*', 'm_party.PARTYNAME')
                    ->where('candidate_nomination_detail.st_code', $stcode)
                    ->where('candidate_nomination_detail.pc_no', $pc_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('m_election_details.CONST_TYPE', '=', 'PC')
					->groupBy('candidate_nomination_detail.candidate_id')
                    ->get();
                    if(!empty($candList)){
                  $i=0;
               foreach($candList as $cand){
                  $expenditure_understates = DB::table('expenditure_understates')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$stcode)->where('constituency_no',$pc_no)->where('understated_type_id','9')->first();
                  $other_source_cc = DB::table('expenditure_fund_source')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$stcode)->where('constituency_no',$pc_no)
                          ->whereIn('other_source_payment_mode',array('Cheque','Cash'))->sum('other_source_amount');
                  $other_source_kind = DB::table('expenditure_fund_source')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$stcode)->where('constituency_no',$pc_no)
                          ->whereIn('other_source_payment_mode',array('In Kind'))->sum('other_source_amount');
                  $candList[$i]->comment_9 = !empty($expenditure_understates->comment)?$expenditure_understates->comment:"";
                  $candList[$i]->understated_type_id_9 = !empty($expenditure_understates->understated_type_id)?$expenditure_understates->understated_type_id:"";
                  $candList[$i]->other_source_amt_cc = !empty($other_source_cc)?$other_source_cc:"0";
                  $candList[$i]->other_source_amt_kind = !empty($other_source_kind)?$other_source_kind:"0";
                  $i++;
               }
              }

            // add 24/10/2019 manoj
              $resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
            // end 24/10/2019 manoj 

            $pdf = view('admin.expenditure.pdf_tracking_report', compact('candList','stateDetail','districtDetails' ,'Pcdetail', 'winn_data','resultDeclarationDate'));
            $mpdf->WriteHTML($pdf);
            $mpdf->Output();
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function viewByCandidateId($candidateId) {
        $candidateId = base64_decode($candidateId);
         $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);
        
        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
        ////////////////////////////////////////
        
               $pcdetail = DB::table('candidate_nomination_detail')->where('candidate_nomination_detail.candidate_id', $candidateId)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->first();
        
            $pcNo = !empty($pcdetail->pc_no) ? $pcdetail->pc_no : 0;
            $st_code = !empty($pcdetail->st_code) ? $pcdetail->st_code : 0;
            
            $pcData =  getpcbypcno($st_code, $pcNo);


            $district_no = !empty($pcdetail->district_no) ? $pcdetail->district_no : 0;

        $districtDetails = getdistrictbydistrictno($st_code, $district_no);
         
            $electionTypeId = !empty($pcdetail->election_type_id) ? $pcdetail->election_type_id : 0;
            $electionType = DB::table('expenditure_election_type')->where('expenditure_election_type.status', 1)
                            ->where('expenditure_election_type.id', $electionTypeId)->first();
        // get CEO status cand_name ELECTION_TYPE
            
        $party_id = !empty($pcdetail->party_id) ? $pcdetail->party_id : 0;
        $partyname = getpartybyid($party_id);
        $partyname = !empty($partyname) ? $partyname->PARTYNAME : '';
        
        $ELECTION_ID = !empty($pcdetail->election_id) ? $pcdetail->election_id : 0;

        // echo $pcNO, $ELECTION_ID, $st_code;die;
        $winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $st_code)->where('pc_no', $pcNo)->where('election_id', $ELECTION_ID)->first();
 
        $gexExpReport = DB::table('expenditure_reports')->where('candidate_id', $candidateId)->get()->toArray();
        $getCandidateExpData = DB::table('expenditure_understates')->where('candidate_id', $candidateId)->get()->toArray();
        $expenditure_fund_parties = DB::table('expenditure_fund_parties')->where('candidate_id', $candidateId)->get()->toArray();
        $expenditure_fund_source = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)->get()->toArray();
        $getSourceFundData = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)->get()->toArray();
        $getExpData = DB::table('expenditure_understated')->where('candidate_id', $candidateId)->get()->toArray();
        $getExpItem = DB::table('expenditure_items')->get();
         $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidateId);
            $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidateId);
            $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidateId);
            
			 // add new 
          $scrutiny_data=DB::table('expenditure_reports')->select('expenditure_reports.noticefile')
                    ->where('expenditure_reports.candidate_id', '=', $candidateId)
                    ->first();
                   
                   
              $expenseunderstated= DB::table('expenditure_understates')->where('candidate_id', $candidateId)->get()->toArray();

            
            //  $download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : '';
            // $download_link1= !empty($download_link1) && strpos($download_link1,'ExpenditureReportPC') !==false? url($download_link1):!empty($download_link1) ? url('/uploads/ExpenditureReportPC').'/'.$download_link1:'';

            //   $download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : '';
            //  $download_link2= !empty($download_link2) && strpos($download_link2,'ExpenditureReportPC') !==false? url($download_link2):!empty($download_link2) ? url('/uploads/ExpenditureReportPC').'/'.$download_link2:'';

            // $download_link3=!empty($scrutiny_data->noticefile)? $scrutiny_data->noticefile:'';
            //  $download_link3= !empty($download_link3) && strpos($download_link3,'ExpenditureReportPC') !==false? url($download_link3):!empty($download_link3) ? url('/uploads/ExpenditureReportPC').'/'.$download_link3:'';
            //  $download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : '';
            //  $download_link4= !empty($download_link4) && strpos($download_link4,'ExpenditureReportPC') !==false? url($download_link4):!empty($download_link4) ? url('/uploads/ExpenditureReportPC').'/'.$download_link4:'';
              ////////////// file path start ///////
     
             $download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : '';
             if(strpos($download_link1,'ExpenditureReportPC') !==false) { 
                        
                   $download_link1= url($download_link1);              
            }            
            else if(!empty($download_link1) && strpos($download_link1,'ExpenditureReportPC') ==false) {
               
               $download_link1 = url('/uploads/ExpenditureReportPC').'/'.$download_link1;

            } 

             $download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : '';

              if(strpos($download_link2,'ExpenditureReportPC') !==false) { 
                        
                   $download_link2= url($download_link2);              
            }            
            else if(!empty($download_link2) && strpos($download_link2,'ExpenditureReportPC') ==false) {
               
               $download_link2 = url('/uploads/ExpenditureReportPC').'/'.$download_link2;

            } 

            $download_link3=!empty($scrutiny_data->noticefile)? $scrutiny_data->noticefile:'';
              if(strpos($download_link3,'ExpenditureReportPC') !==false) { 
                        
                   $download_link3= url($download_link3);              
            }            
            else if(!empty($download_link3) && strpos($download_link3,'ExpenditureReportPC') ==false) {
               
               $download_link3 = url('/uploads/ExpenditureReportPC').'/'.$download_link3;

            } 


               $download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : ''; 
            if(strpos($download_link4,'ExpenditureReportPC') !==false) { 
                        
                   $download_link4= url($download_link4);              
            }            
            else if(!empty($download_link4) && strpos($download_link4,'ExpenditureReportPC') ==false) {
               
               $download_link4 = url('/uploads/ExpenditureReportPC').'/'.$download_link4;

            } 
            ////////////// file path end ///////
 
 
        

         $candidateData = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                    })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->join('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
                    ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*','candidate_personal_detail.candidate_id as c_id', 'm_election_details.*', 'expenditure_reports.*', 'm_party.PARTYNAME')
                    ->where('candidate_nomination_detail.st_code', $st_code)
                    ->where('candidate_nomination_detail.pc_no', $pcNo)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                     ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                 
                    ->where('candidate_nomination_detail.candidate_id', '=', $candidateId)
                    ->where('m_election_details.CONST_TYPE', '=', 'PC')
                    ->first();
                    
 
        return view('admin.expenditure.viewdeoForm',['user_data' => $d, 'candidateData' => $candidateData,
            "getCandidateExpData" => $getCandidateExpData, 
            "expenditure_fund_source" => $expenditure_fund_source,
            "expenditure_fund_parties" => $expenditure_fund_parties, 
              'ele_details' => $ele_details, 
            "getSourceFundData" => $getSourceFundData, "getExpData" => $getExpData,
            "getExpItem" => $getExpItem, "gexExpReport" => $gexExpReport,'winn_data'=>$winn_data,
             
            'pcdetail'=>$pcData,
            'download_link1'=>$download_link1, 
            'download_link2'=>$download_link2, 
            'download_link3'=>$download_link3,
            'download_link4'=>$download_link4,]);
       
       
      
        
       
        
    }

    // end here Manoj 
    ////////////////////// code by manish /////////////////////
    /*
      SOTORE UNDERSTAED EXPENSES DATA FOR PARTICULAR CANDIDATE
     */

    // (ii) If Yes, then Annexe copies of all the notices issued relating to Discrepancies with English Translation (If it is in regional language) and mention Date of Notice.
    public function update_understated_file1(Request $request) {



        if (!empty($_FILES)) {

            $file_name = $_FILES[4]['name']['understated']['comment'];
            $file_size = $_FILES[4]['size']['understated']['comment'];
            $file_tmp = $_FILES[4]['tmp_name']['understated']['comment'];
            $file_type = $_FILES[4]['type']['understated']['comment'];
            $name = rand(100000, 999999) . '_' . $file_name;

            if (move_uploaded_file($file_tmp, self::$fileLocation.$name)) {
                Session::put("comment17ii", self::$fileName.$name);
                 return 1;
            } else {
                Session::put("comment17ii", "");
                 return 0;
            }
        } else {
            Session::put("comment17ii", "");
            return 0;
        }
    }

    // (ii) If Yes, then Annexe copies of all the notices issued relating to Discrepancies with English Translation (If it is in regional language) and mention Date of Notice.
    public function update_understated_file2(Request $request) {
        if (!empty($_FILES)) {

            $file_name = $_FILES[6]['name']['understated']['comment'];
            $file_size = $_FILES[6]['size']['understated']['comment'];
            $file_tmp = $_FILES[6]['tmp_name']['understated']['comment'];
            $file_type = $_FILES[6]['type']['understated']['comment'];
            $name = rand(100000, 999999) . '_' . $file_name;

            if (move_uploaded_file($file_tmp, self::$fileLocation.$name)) {
                Session::put("comment17iv", self::$fileName.$name);
                return 1;
            } else {
                Session::put("comment17iv", "");
                 return 0;
            }
        } else {
            Session::put("comment17iv", "");
            return 0;
        }
    }

    public function update_understated_file4(Request $request) {

        if (!empty($_FILES)) {

            $file_name = $_FILES[9]['name']['understated']['comment'];
            $file_size = $_FILES[9]['size']['understated']['comment'];
            $file_tmp = $_FILES[9]['tmp_name']['understated']['comment'];
            $file_type = $_FILES[9]['type']['understated']['comment'];
            $name = rand(100000, 999999) . '_' . $file_name;

            if (move_uploaded_file($file_tmp, self::$fileLocation.$name)) {
                Session::put("comment23iv", self::$fileName.$name);
                 return 1;
            } else {
                Session::put("comment23iv", "");
                 return 0;
            }
        } else {
            Session::put("comment23iv", "");
            return 0;
        }
    }

    public function uploadsigned(Request $request) {
        if (!empty($_FILES)) {

            $file_name = $_FILES['signedfileupload']['name'];
            $file_size = $_FILES['signedfileupload']['size'];
            $file_tmp = $_FILES['signedfileupload']['tmp_name'];
            $file_type = $_FILES['signedfileupload']['type'];
            $name = rand(100000, 999999) . '_' . $file_name;

            if (move_uploaded_file($file_tmp, self::$fileLocation.$name)) {
                Session::put("uploadsigned", self::$fileName.$name);
                 return 1;
            } else {
                Session::put("uploadsigned", "");
                 return 0;
            }
        } else {
            Session::put("uploadsigned", "");
            return 0;
        }
    }

    public function updateNoticeFile(Request $request) {
        if (!empty($_FILES)) {

            $file_name = $_FILES[6]['name']['understated']['comment'];
            $file_size = $_FILES[6]['size']['understated']['comment'];
            $file_tmp = $_FILES[6]['tmp_name']['understated']['comment'];
            $file_type = $_FILES[6]['type']['understated']['comment'];
            $name = rand(100000, 999999) . '_' . $file_name;

            if (move_uploaded_file($file_tmp, self::$fileLocation.$name)) {
                Session::put("noticefile", self::$fileName.$name);
                return 1;
            } else {
                Session::put("noticefile", "");
                 return 0;
            }
        } else {
            Session::put("noticefile", "");
            return 0;
        }
    }

     public function updateUnderstatedDetail(Request $request) {
        $request = (array) $request->all();
        $candidateId = $request['candidate_id'];
        $count_data =!empty($request['datas']['expenditure_type'])? count($request['datas']['expenditure_type']):0;
        //$request = $_POST;
        $user = Auth::user();

        $uid = $user->id;
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);
        $countdata = count($request);
         $candidateDetail=DB::table('candidate_nomination_detail')
                    ->select('st_code','pc_no','district_no')
                    ->where('candidate_nomination_detail.candidate_id','=', $candidateId)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->first();

        $filePath=DB::table('expenditure_understates')
                       ->select('understated_type_id','comment','extra_data')
                       ->where('candidate_id', $candidateId)
                       ->get()->toArray(); 

        $filePath1= !empty($filePath[3]->comment)? $filePath[3]->comment:'';
        $filePath2= !empty($filePath[5]->comment)?$filePath[5]->comment:'';
        $filePath3= !empty($filePath[8]->extra_data)?$filePath[8]->extra_data:'';
        $comment17ii = !empty(Session::get("comment17ii"))?Session::get("comment17ii"):$filePath1;    
        $comment17iv = !empty(Session::get("comment17iv"))?Session::get("comment17iv"):$filePath2;    
        $comment23iv = !empty(Session::get("comment23iv"))?Session::get("comment23iv"):$filePath3;    
          
       
         
        try {
            $datas = [];
            $rules = []; 
               
              $getCandidateExpData = DB::table('expenditure_understates')->where('candidate_id','=', $candidateId)->get()->toArray();

            
            for ($i = 1; $i <= 9; $i++) {
                $xss = new xssClean;

                $req['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
                $req['constituency_no'] = !empty($candidateDetail->pc_no) ? $candidateDetail->pc_no : "";
                $req['district_no'] = !empty($candidateDetail->district_no) ? $candidateDetail->district_no : "";
                $req['created_by'] = $uid;
                $req['updated_by'] = $uid;
                $req['election_type'] = Session::get('DB_ELE_TYPE');
                $req['candidate_id'] = $candidateId;
                $req['election_id'] = Session::get('DB_ELECTION_ID');
                $req['understated_type_id'] = $i;
                $req['status'] = !empty($request[$i]['understated']['status']) ? $xss->clean_input($request[$i]['understated']['status']) : "";
                $req['comment'] = !empty($request[$i]['understated']['comment']) ? $xss->clean_input($request[$i]['understated']['comment']) : "";
                if ($i == 4) {
                    $req['comment'] = !empty($comment17ii) ? $comment17ii : "";
                    $req['extra_data'] = !empty($request[$i]['understated']['extra_data']) ? $xss->clean_input($request[$i]['understated']['extra_data']) : "";
                    Session::forget("comment17ii");
                }
                if ($i == 6) {
                    $req['comment'] = !empty($comment17iv) ? $comment17iv : "";
                    Session::forget("comment17iv");
                }
                if ($i == 9) {
                    $req['extra_data'] = !empty($comment23iv) ? $comment23iv : "";
                    Session::forget("comment23iv");
                }

                  if (!empty($getCandidateExpData) && count($getCandidateExpData)>0) {
                         
 
               $updatedata=[];
                $updatedata['updated_by'] = $uid; 
                $updatedata['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
                $updatedata['constituency_no'] = !empty($candidateDetail->pc_no) ? $candidateDetail->pc_no : "";
                $updatedata['district_no'] = !empty($candidateDetail->district_no) ? $candidateDetail->district_no : "";             
                 
                $updatedata['status'] = !empty($request[$i]['understated']['status']) ? $xss->clean_input($request[$i]['understated']['status']) : $getCandidateExpData[$i-1]->status;
                $updatedata['comment'] = !empty($request[$i]['understated']['comment']) ? $xss->clean_input($request[$i]['understated']['comment']) : $getCandidateExpData[$i-1]->status;
                if ($i == 4) {
                    $updatedata['comment'] = !empty($comment17ii) ? $comment17ii : $getCandidateExpData[$i-1]->comment;
                    $updatedata['extra_data'] = !empty($request[$i]['understated']['extra_data']) ? $xss->clean_input($request[$i]['understated']['extra_data']) : $getCandidateExpData[$i-1]->extra_data;
                    Session::forget("comment17ii");
                }
                if ($i == 6) {
                    $updatedata['comment'] = !empty($comment17iv) ? $comment17iv : "";
                    Session::forget("comment17iv");
                }
                if ($i == 9) {
                    $updatedata['extra_data'] = !empty($comment23iv) ? $comment23iv : "";
                    Session::forget("comment23iv");
                }
                   
                     $updatunderstates = DB::table('expenditure_understates')
                     ->where('candidate_id','=', $candidateId)
                      ->where('id','=', $getCandidateExpData[$i-1]->id)
                     ->update($updatedata);
                     }else{
                             
                      $dataInserted = $this->commonModel->insertData('expenditure_understates', $req);
                     }
               
                
            }


            if ($count_data > 0) {
              $sno19Data = DB::table('expenditure_understated')->where('candidate_id','=', $candidateId)
               ->get()->toArray();
                  
                $requestData = array();
                for ($i = 0; $i < $count_data; $i++) {                     
                        
                        if(!empty($sno19Data) && count($sno19Data)>0){


                              $updatedata=[];      
                       $updatedata['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
                       $updatedata['constituency_no'] = !empty($candidateDetail->pc_no) ? $candidateDetail->pc_no : "";
                       $updatedata['district_no'] = !empty($candidateDetail->district_no) ? $candidateDetail->district_no : "";
                        $updatedata['election_id'] = Session::get('DB_ELECTION_ID');                     
                        $updatedata['updated_by'] = $uid;           
                         $updatedata['updated_by'] = $uid;             
                        $requestData['expenditure_type'] = !empty($request['datas']['expenditure_type'][$i])? $request['datas']['expenditure_type'][$i]:"";
                        $updatedata['date_understated'] = !empty($request['datas']['date_understated'][$i])?$request['datas']['date_understated'][$i]:$sno19Data[$i]->date_understated;
                        $updatedata['page_no_observation'] = !empty($request['datas']['page_no_observation'][$i])?$request['datas']['page_no_observation'][$i]:$sno19Data[$i]->page_no_observation;
                        $updatedata['amt_as_per_observation'] =!empty($request['datas']['amt_as_per_observation'][$i])?$request['datas']['amt_as_per_observation'][$i]:$sno19Data[$i]->amt_as_per_observation;
                        $updatedata['amt_as_per_candidate'] = !empty($request['datas']['amt_as_per_candidate'][$i])?$request['datas']['amt_as_per_candidate'][$i]:$sno19Data[$i]->amt_as_per_candidate;
                        $updatedata['amt_understated_by_candidate'] = !empty($request['datas']['amt_understated_by_candidate'][$i])?$request['datas']['amt_understated_by_candidate'][$i]:$sno19Data[$i]->amt_understated_by_candidate;
                        $updatedata['description'] = !empty($request['datas']['description'][$i])?$request['datas']['description'][$i]:$sno19Data[$i]->description;
                          $updatesno19 = DB::table('expenditure_understated')
                            
                                       ->where('id','=', $sno19Data[$i]->id)
                                       ->where('candidate_id','=', $candidateId)
                                       ->update($updatedata);
                            }else{
                                  $requestData['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code :"";
                        $requestData['constituency_no'] = !empty($candidateDetail->pc_no) ? $candidateDetail->pc_no : "";
                        $requestData['district_no'] = !empty($candidateDetail->district_no) ? $candidateDetail->district_no :"";
                        $requestData['election_id'] = Session::get('DB_ELECTION_ID');  
                        $requestData['created_by'] = $uid;
                        $requestData['updated_by'] = $uid;
                        $requestData['updated_at'] = date('Y-m-d');
                        $requestData['election_type'] = Session::get('DB_ELE_TYPE');
                        $requestData['expenditure_type'] = !empty($request['datas']['expenditure_type'][$i])? $request['datas']['expenditure_type'][$i]:"";
                        $requestData['date_understated'] = !empty($request['datas']['date_understated'][$i])?$request['datas']['date_understated'][$i]:"";
                        $requestData['page_no_observation'] = !empty($request['datas']['page_no_observation'][$i])?$request['datas']['page_no_observation'][$i]:"";
                        $requestData['amt_as_per_observation'] =!empty($request['datas']['amt_as_per_observation'][$i])?$request['datas']['amt_as_per_observation'][$i]:"";
                        $requestData['amt_as_per_candidate'] = !empty($request['datas']['amt_as_per_candidate'][$i])?$request['datas']['amt_as_per_candidate'][$i]:"";
                        $requestData['amt_understated_by_candidate'] = !empty($request['datas']['amt_understated_by_candidate'][$i])?$request['datas']['amt_understated_by_candidate'][$i]:"";
                        $requestData['description'] = !empty($request['datas']['description'][$i])?$request['datas']['description'][$i]:"";
                        $requestData['candidate_id'] = $candidateId;
                          $dataInserted = $this->commonModel->insertData('expenditure_understated', $requestData);
                        }

                            
                    
                }
            }
            return 1;
        } catch (\Exception $e) {
          return $e->getMessage();

            return 0;
        }
    }
 public function UpdateSourceFundData(Request $request) {
        $request = (array) $request->all();
        //$count_data =  !empty($request['data']['other_souce_name'])? count($request['data']['other_souce_name']):'';
        $candidateId = $request['candidate_id'];
        $request = $_POST;
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
        $uid = $user->id;
 
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);

         
         
        $candidateDetail=DB::table('candidate_nomination_detail')
                    ->select('st_code','pc_no','district_no')
                    ->where('candidate_nomination_detail.candidate_id','=', $candidateId)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->first();
         

        try {
            $getSourceFundData = DB::table('expenditure_fund_source')->where('candidate_id','=' ,$candidateId)->get()->toArray();

                                 $cashinsertData=['ST_CODE'=>!empty($candidateDetail->st_code) ? $candidateDetail->st_code : "",
                                                  'constituency_no'=>!empty($candidateDetail->pc_no) ? $candidateDetail->pc_no : "",
                                                  'district_no'=>!empty($candidateDetail->district_no) ? $candidateDetail->district_no : "",
                                                  'created_by'=>$uid,
                                                  'updated_by'=>$uid,
                                                  'election_type'=>Session::get('DB_ELE_TYPE'),         
                                                  'other_souce_name'=>$request['other_souce_name_cash'],
                                                  'other_source_payment_mode'=>'Cash',
                                                  'other_source_amount'=>$request['other_source_amount_cash'],
                                                  'candidate_id'=>$candidateId,
                                                  'election_id'=> !empty($user->election_id)?$user->election_id:0
                                                  ];
                                $chequeinsertData = ['ST_CODE'=>!empty($candidateDetail->st_code) ? $candidateDetail->st_code : "",
                                                  'constituency_no'=>!empty($candidateDetail->pc_no) ? $candidateDetail->pc_no : "",
                                                  'district_no'=>!empty($candidateDetail->district_no) ? $candidateDetail->district_no : "",
                                                  'created_by'=>$uid,
                                                  'updated_by'=>$uid,
                                                  'election_type'=>Session::get('DB_ELE_TYPE'),
                                                  'other_souce_name'=>$request['other_souce_name_cheque'],
                                                  'other_source_payment_mode'=>'Cheque',
                                                  'other_source_amount'=>$request['other_source_amount_cheque'],
                                                  'candidate_id'=>$candidateId,
                                                  'election_id'=> !empty($user->election_id)?$user->election_id:0
                                                  ];
                                $kindinsertData= ['ST_CODE'=>!empty($candidateDetail->st_code) ? $candidateDetail->st_code : "",
                                                  'constituency_no'=>!empty($candidateDetail->pc_no) ? $candidateDetail->pc_no : "",
                                                  'district_no'=>!empty($candidateDetail->district_no) ? $candidateDetail->district_no : "",
                                                  'created_by'=>$uid,
                                                  'updated_by'=>$uid,
                                                  'election_type'=>Session::get('DB_ELE_TYPE'),
                                                  'other_souce_name'=>$request['other_souce_name_kind'],
                                                  'other_source_payment_mode'=>'In Kind',
                                                  'other_source_amount'=>$request['other_source_amount_kind'],
                                                  'candidate_id'=>$candidateId,
                                                  'election_id'=> !empty($user->election_id)?$user->election_id:0
                                                  ];
                                            


                          
                                if(!empty($getSourceFundData) &&count($getSourceFundData)>0){
                                     
                                             $cashDataUpdate=
                                                  [ 
                                                  'updated_by'=>$uid,
                                                  'other_souce_name'=>$request['other_souce_name_cash'],
                                                  'other_source_payment_mode'=>'Cash',
                                                  'other_source_amount'=>$request['other_source_amount_cash']
                                                  ];
                                  $chequeDataUpdate=[ 
                                                  'updated_by'=>$uid,
                                                  'other_souce_name'=>$request['other_souce_name_cheque'],
                                                  'other_source_payment_mode'=>'Cheque',
                                                  'other_source_amount'=>$request['other_source_amount_cheque']
                                                  ];
                                 $kindDataUpdate= [
                                                  'updated_by'=>$uid,
                                                  'other_souce_name'=>$request['other_souce_name_kind'],
                                                  'other_source_payment_mode'=>'In Kind',
                                                  'other_source_amount'=>$request['other_source_amount_kind']
                                                  ];
                          $cashrecord=DB::table('expenditure_fund_source')
                          ->where('candidate_id','=',$candidateId)
                          ->where('other_source_payment_mode','=','Cash')->first();

                              if(!empty($cashrecord)){
                                $updateFundstatus=DB::table('expenditure_fund_source')
                                ->where('candidate_id','=',$candidateId)
                                ->where('other_source_payment_mode','=','Cash')
                                ->update($cashDataUpdate); 
                                 
                              }else{
         
                                      $updateFundstatus=DB::table('expenditure_fund_source')->insert($cashinsertData);
 
                              }

                           $Chequerecord=DB::table('expenditure_fund_source')
                          ->where('candidate_id','=',$candidateId)
                          ->where('other_source_payment_mode','=','Cheque')->first();
                              if(!empty($Chequerecord)){
                                $updateFundstatus=DB::table('expenditure_fund_source')
                                ->where('candidate_id','=',$candidateId)
                                ->where('other_source_payment_mode','=','Cheque')
                                ->update($chequeDataUpdate); 
                              }else{
     
                                      $updateFundstatus=DB::table('expenditure_fund_source')->insert($chequeinsertData);

                              }
                           $kindrecord=DB::table('expenditure_fund_source')
                          ->where('candidate_id','=',$candidateId)
                          ->where('other_source_payment_mode','=','In Kind')->first();
                            if(!empty($kindrecord)){
                              $updateFundstatus=DB::table('expenditure_fund_source')
                              ->where('candidate_id','=',$candidateId)
                              ->where('other_source_payment_mode','=','In Kind')
                              ->update($kindDataUpdate); 
                            }else{

                                    $updateFundstatus=DB::table('expenditure_fund_source')->insert($kindinsertData);

                            } 


                  }else{       
                        $updateFundstatus=DB::table('expenditure_fund_source')->insert([$cashinsertData,$chequeinsertData,$kindinsertData]); 
                  }     
                  return 1;        

             
        } catch (\Exception $e) {

            return 0;
        }
    }

     public function UpdatePartyFundData(Request $request) {

        $request = (array) $request->all();
        $candidateId = $request['candidate_id'];
        $request = $_POST;
        $user = Auth::user();
        $uid = $user->id;
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);
        unset($request['overallsum_source_political']);
        $request['candidate_id'] = $candidateId;

        $candidateDetail=DB::table('candidate_nomination_detail')
                    ->select('st_code','pc_no','district_no')
                    ->where('candidate_nomination_detail.candidate_id','=', $candidateId)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->first();

        $request['district_no'] = !empty($candidateDetail->district_no) ? $candidateDetail->district_no : "";
        $request['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
        $request['constituency_no'] = !empty($candidateDetail->pc_no) ? $candidateDetail->pc_no : "";
        $request['created_by'] = $uid;
        $request['updated_by'] = $uid;
        $request['election_id'] = !empty($user->election_id)?$user->election_id:0;
        $request['election_type'] =Session::get('DB_ELE_TYPE');
        $request['candidate_id'] = $candidateId;
        try {
            $getSourceFundData = DB::table('expenditure_fund_parties')->where('candidate_id','=', $candidateId)->get()->toArray();
            if (!empty($getSourceFundData) && count($getSourceFundData)>0) {
                $dataUpdate=[
                    'political_fund_cash'=>$request['political_fund_cash'],
                    'political_fund_checque'=>$request['political_fund_checque'],
                    'political_fund_checque_date'=>$request['political_fund_checque_date'],
                    'political_fund_bank_name'=>$request['political_fund_bank_name'],
                    'political_fund_acct_no'=>$request['political_fund_acct_no'],
                    'political_fund_ifsc'=>$request['political_fund_ifsc'],
                    'political_fund_checque_num'=>$request['political_fund_checque_num'],
                    'political_fund_kind'=>$request['political_fund_kind'],
                    'political_fund_kind_text'=>$request['political_fund_kind_text'],
                    'updated_by'=>$uid
                  ];
                $updateData = DB::table('expenditure_fund_parties')
                ->where('candidate_id','=', $candidateId)
                ->update($dataUpdate);

            }else{
                 $dataInserted = $this->commonModel->insertData('expenditure_fund_parties', $request);
            }

             return 1;
             
        } catch (\Exception $e) {

            return 0;
        }
    }

    public function SaveExpenseData(Request $request) {

        $request = (array) $request->all();
        $count_data = count($request['datas']['expenditure_type']);
        $candidateId = $request['candidate_id'];
        $user = Auth::user();
        $uid = $user->id;
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);
        $candidateDetail = $this->commonModel->selectone('candidate_nomination_detail', 'candidate_id', $candidateId);

        try {
            $getExpData = DB::table('expenditure_understated')->where('candidate_id', $candidateId)->get()->toArray();

            if ($count_data > 0) {
                $requestData = array();
                for ($i = 0; $i < $count_data; $i++) {
                    if (!empty($request['datas']['page_no_observation'][$i])) {
                        $requestData['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
                        $requestData['constituency_no'] = !empty($candidateDetail->pc_no) ? $candidateDetail->pc_no : "";
                        $requestData['created_by'] = $uid;
                        $requestData['updated_by'] = $uid;
						$requestData['election_id'] = !empty($user->election_id)?$user->election_id:0;
                        $requestData['election_type'] = "General";
                        $requestData['expenditure_type'] = $request['datas']['expenditure_type'][$i];
                        $requestData['date_understated'] = $request['datas']['date_understated'][$i];
                        $requestData['page_no_observation'] = $request['datas']['page_no_observation'][$i];
                        $requestData['amt_as_per_observation'] = $request['datas']['amt_as_per_observation'][$i];
                        $requestData['amt_as_per_candidate'] = $request['datas']['amt_as_per_candidate'][$i];
                        $requestData['amt_understated_by_candidate'] = $request['datas']['amt_understated_by_candidate'][$i];
                        $requestData['description'] = $request['datas']['description'][$i];
                        $requestData['candidate_id'] = $candidateId;
                        $dataInserted = $this->commonModel->insertData('expenditure_understated', $requestData);
                    }
                }
            }

            if (!empty($dataInserted)) {
                if ($dataInserted) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } catch (\Exception $e) {

            return 0;
        }
    }

    public function updateData(Request $request) {
        $request = (array) $request->all();
        if (!empty($request)) {
            $updateTrackData = $this->commonModel->updatedata('expenditure_reports', 'id', $request['tbid'], array($request['column'] => $request['value']));
            if ($updateTrackData) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    public function DeleteSourceFundData(Request $request) {
        try {
            $delId = $_POST['delID'];
            if (!empty($delId)) {
                $deleteRecord = $this->commonModel->removerecord('expenditure_fund_source', 'id', $delId);
                if ($deleteRecord) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function FinalizedData(Request $request) {

        $candidateId = $_POST['candidate_id'];

        try {

            $updateFinalized = $this->commonModel->updatedata('expenditure_reports', 'candidate_id', $candidateId, array("finalized_status" => '1'));
            if ($updateFinalized) {
                return 1;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function DeleteUnderStatedData(Request $request) {
        try {
            $delId = $_POST['delID'];
            if (!empty($delId)) {
                $deleteRecord = $this->commonModel->removerecord('expenditure_understated', 'id', $delId);
                if ($deleteRecord) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

    // end here Manish

    /**
     * Calculate percetage between the numbers
     *    
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 07-05-19
     */
    public function get_percentage($total, $number) {
        if ($total > 0) {
            return round($number / ($total / 100), 2);
        } else {
            return 0;
        }
    }

//end number

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 07-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return dashboard By ROPC fuction     
     */
    public function dashboard(Request $request) {
        //PC ROPC dashboard TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->count();

                //Get Data entry Start Count 
                $startdata = $this->expenditureModel->gettotaldataentryStart('PC', $st_code, $cons_no);
				$startdatacount=count($startdata);
                //dd($startdatacount);
                //Get Data entry Start Count %
                $Percent_startdataentry = $this->get_percentage($totalContestedCandidate, $startdatacount);

                //Get Data entry finalize Count 
                $finaldata = $this->expenditureModel->gettotaldataentryFinal('PC', $st_code, $cons_no);
			    $finaldatacount=count($finaldata);
                //Get Data entry finalize Count %
                $Percent_finaldatacount = $this->get_percentage($totalContestedCandidate, $finaldatacount);

                //Get Data entry finalize Count 
                $logedaccdata = $this->expenditureModel->gettotallogedAccount('PC', $st_code, $cons_no);
				$logedaccount=count($logedaccdata);
                //Get Data entry finalize Count %
                $Percent_logedaccount = $this->get_percentage($totalContestedCandidate, $logedaccount);

                //Get Data entry finalize Count 
                $notintimedata = $this->expenditureModel->gettotalNotinTime('PC', $st_code, $cons_no);
				$notintimeaccount=count($notintimedata);
                //Get Data entry finalize Count %
                $Percent_notintimeaccount = $this->get_percentage($totalContestedCandidate, $notintimeaccount);


                //Get Defects in format Count 
                $formateDefectsdata = $this->expenditureModel->gettotalDefectformats('PC', $st_code, $cons_no);
                $formateDefectscount=count($formateDefectsdata);
				//Get Defects in format Count %
                $Percent_formateDefectscount = $this->get_percentage($totalContestedCandidate, $formateDefectscount);

                //Get Defects in format Count 
                $expenseunderstated = $this->expenditureModel->gettotalexpenseUnderStated('PC', $st_code, $cons_no);
             
                //Get Defects in format Count %
                $Percent_expenseunderstated = $this->get_percentage($totalContestedCandidate, $expenseunderstated);

                //Get total fund from party
                $partyFund = $this->expenditureModel->gettotalPartyfund('PC', $st_code, $cons_no);
                $otherSourcesFund = $this->expenditureModel->gettotalOtherSourcesfund('PC', $st_code, $cons_no);

                $totalFund = ($partyFund->total_partyfund + $otherSourcesFund->total_otherSourcesfund);
                //Get party fund %
                $Percent_partyFund = $this->get_percentage($totalFund, $partyFund->total_partyfund);
                //Get OtherSources fund %
                $Percent_OthersourcesFund = $this->get_percentage($totalFund, $otherSourcesFund->total_otherSourcesfund);

                //dd($Percent_startdataentry);
                return view('admin.pc.ro.Expenditure.dashboard', ['user_data' => $d, 'startdatacount' => $startdatacount, 'Percent_startdataentry' => $Percent_startdataentry, 'finaldatacount' => $finaldatacount, 'Percent_finaldatacount' => $Percent_finaldatacount, 'formateDefectscount' => $formateDefectscount, 'Percent_formateDefectscount' => $Percent_formateDefectscount, 'expenseunderstated' => $expenseunderstated, 'Percent_expenseunderstated' => $Percent_expenseunderstated, 'Percent_partyFund' => $Percent_partyFund, 'Percent_OthersourcesFund' => $Percent_OthersourcesFund, 'edetails' => $ele_details, 'logedaccount' => $logedaccount, 'Percent_logedaccount' => $Percent_logedaccount, 'notintimeaccount' => $notintimeaccount, 'Percent_notintimeaccount' => $Percent_notintimeaccount, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC CEROO dashboard TRY CATCH ENDS HERE    
    }

// end dashboard function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBydataentryStart By ROPC fuction     
     */
    public function candidateListBydataentryStart(Request $request) {
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $DataentryStartCandList = DB::table('expenditure_reports')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
						->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                // dd($DataentryStartCandList);
                return view('admin.pc.ro.Expenditure.dataentrystart-report', ['user_data' => $d, 'DataentryStartCandList' => $DataentryStartCandList, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details, 'count' => count($DataentryStartCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBydataentryStart TRY CATCH ENDS HERE   
    }

// end dataentry start function

    public function candidateListBydataentryStartGraph(Request $request) {
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->count();

                //Get Data entry Start Count 
                $startdatacount = $this->expenditureModel->gettotaldataentryStart('PC', $st_code, $cons_no);
                //dd($startdatacount);
                //Get Data entry Start Count %
                $Percent_startdataentry = $this->get_percentage($totalContestedCandidate, $startdatacount);
                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';
                $data = [
                    ['Oveall summary', 'Data Entry stated'],
                    [$candiatePcName, $Percent_startdataentry]
                ];
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBydataentryStart TRY CATCH ENDS HERE   
    }

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByfinalizeData By ROPC fuction     
     */
    public function candidateListByfinalizeData(Request $request) {
        //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $finalCandList = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
						->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)
                        ->where('expenditure_reports.finalized_status', '=', '1')
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                //dd($DataentryStartCandList);
                return view('admin.pc.ro.Expenditure.finalize-report', ['user_data' => $d, 'finalCandList' => $finalCandList, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details, "count" => count($finalCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByfinalizeData TRY CATCH ENDS HERE   
    }

// end candidateListByfinalizeData start function

    public function candidateListByfinalizeDatagraph(Request $request) {
        //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->count();


                //Get Data entry finalize Count 
                $finaldatacount = $this->expenditureModel->gettotaldataentryFinal('PC', $st_code, $cons_no);
                //Get Data entry finalize Count %
                $Percent_finaldatacount = $this->get_percentage($totalContestedCandidate, $finaldatacount);


                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';
                $data = [
                    ['Oveall summary', 'Data finalized'],
                    [$candiatePcName, $Percent_finaldatacount]
                ];
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByfinalizeData TRY CATCH ENDS HERE   
    }

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBylogedaccount By ROPC fuction     
     */
    public function candidateListBylogedaccount(Request $request) {
        //PC ROPC candidateListBylogedaccount TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $logedAccount = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
						->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)
                        ->where('expenditure_reports.candidate_lodged_acct', '=', 'Yes')
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                //dd($DataentryStartCandList);
                return view('admin.pc.ro.Expenditure.logedaccount-report', ['user_data' => $d, 'logedAccount' => $logedAccount, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details, 'count' => count($logedAccount)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBylogedaccount TRY CATCH ENDS HERE   
    }

// end candidateListBylogedaccount start function

    public function candidateListBylogedaccountgraph(Request $request) {
        //PC ROPC candidateListBylogedaccount TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {

                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->count();



                //Get Data entry finalize Count 
                $logedaccount = $this->expenditureModel->gettotallogedAccount('PC', $st_code, $cons_no);
                //Get Data entry finalize Count %
                $Percent_logedaccount = $this->get_percentage($totalContestedCandidate, $logedaccount);


                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';
                $data = [
                    ['Oveall summary', 'Loged Account'],
                    [$candiatePcName, $Percent_logedaccount]
                ];
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBylogedaccount TRY CATCH ENDS HERE   
    }

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBynotintime By ROPC fuction     
     */
    public function candidateListBynotintime(Request $request) {
        //PC ROPC candidateListBynotintime TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $notinTime = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
						->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)
                        ->where('expenditure_reports.account_lodged_time', '=', 'No')
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                //dd($DataentryStartCandList);
                return view('admin.pc.ro.Expenditure.notintime-report', ['user_data' => $d, 'notinTime' => $notinTime, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details, 'count' => count($notinTime)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBynotintime TRY CATCH ENDS HERE   
    }

// end candidateListBynotintime start function

    public function candidateListBynotintimegraph(Request $request) {
        //PC ROPC candidateListBynotintime TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->count();

                //Get Data entry finalize Count 
                $notintimeaccount = $this->expenditureModel->gettotalNotinTime('PC', $st_code, $cons_no);
                //Get Data entry finalize Count %
                $Percent_notintimeaccount = $this->get_percentage($totalContestedCandidate, $notintimeaccount);


                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';
                $data = [
                    ['Oveall summary', 'Not in Time'],
                    [$candiatePcName, $Percent_notintimeaccount]
                ];
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBynotintime TRY CATCH ENDS HERE   
    }

// end candidateListBynotintime start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBydataentryStart By ROPC fuction     
     */
    public function candidateListByformatedefects(Request $request) {
        //PC ROPC candidateListByformatedefects TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;
              
                $formateDefects = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                         ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
						->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)
                        ->where('expenditure_reports.rp_act','No')
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                  //dd($formateDefects);
                return view('admin.pc.ro.Expenditure.formatedefects-report', ['user_data' => $d, 'formateDefects' => $formateDefects, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details, 'count' => count($formateDefects)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByformatedefects TRY CATCH ENDS HERE   
    }

// end candidateListByformatedefects start function

    public function candidateListByformatedefectsgraph(Request $request) {
        //PC ROPC candidateListByformatedefects TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->count();


                //Get Defects in format Count 
                $formateDefectscount = $this->expenditureModel->gettotalDefectformats('PC', $st_code, $cons_no);
                //Get Defects in format Count %
                $Percent_formateDefectscount = $this->get_percentage($totalContestedCandidate, $formateDefectscount);


                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';
                $data = [
                    ['Oveall summary', 'Defects in format'],
                    [$candiatePcName, $Percent_formateDefectscount]
                ];
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByformatedefects TRY CATCH ENDS HERE   
    }

// end candidateListByformatedefects start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByronotagree By ROPC fuction     
     */
    public function candidateListByronotagree(Request $request) {
        //PC ROPC candidateListByronotagree TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $DataentryStartCandList = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)
                        ->get();
                //dd($DataentryStartCandList);
                return view('admin.pc.ro.Expenditure.ronotagree-report', ['user_data' => $d, 'DataentryStartCandList' => $DataentryStartCandList, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByronotagree TRY CATCH ENDS HERE   
    }

// end candidateListByronotagree start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByunderstatedexpense By ROPC fuction     
     */
    public function candidateListByunderstatedexpense(Request $request) {
        //PC ROPC candidateListByunderstatedexpense TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $expenseunderstated = DB::table('expenditure_understated')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                        ->where('expenditure_understated.ST_CODE', '=', $st_code)
                        ->where('expenditure_understated.constituency_no', '=', $cons_no)
                        ->where('expenditure_understated.page_no_observation', '=', "No")
                        ->groupBy('expenditure_understated.candidate_id')
                        ->get();
                //dd($expenseunderstated);
                return view('admin.pc.ro.Expenditure.expenseunderstated-report', ['user_data' => $d, 'expenseunderstated' => $expenseunderstated, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details, 'count' => count($expenseunderstated)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByunderstatedexpense TRY CATCH ENDS HERE   
    }

// end candidateListByunderstatedexpense start function

    public function candidateListByunderstatedexpensegraph(Request $request) {
        //PC ROPC candidateListByunderstatedexpense TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->count();


                //Get Defects in format Count 
                $expenseunderstated = $this->expenditureModel->gettotalexpenseUnderStated('PC', $st_code, $cons_no);

                //Get Defects in format Count %
                $Percent_expenseunderstated = $this->get_percentage($totalContestedCandidate, $expenseunderstated);



                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';
                $data = [
                    ['Oveall summary', 'Expenses understated'],
                    [$candiatePcName, $Percent_expenseunderstated]
                ];
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByunderstatedexpense TRY CATCH ENDS HERE   
    }

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBydataentrydefects By ROPC fuction     
     */
    public function candidateListBydataentrydefects(Request $request) {
        //PC ROPC candidateListBydataentrydefects TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $DataentryStartCandList = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                //dd($DataentryStartCandList);
                return view('admin.pc.ro.Expenditure.dataentrydefect-report', ['user_data' => $d, 'DataentryStartCandList' => $DataentryStartCandList, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBydataentrydefects TRY CATCH ENDS HERE   
    }

// end candidateListBydataentrydefects start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBypartyfund By ROPC fuction     
     */
    public function candidateListBypartyfund(Request $request) {
        //PC ROPC candidateListBypartyfund TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $partyfund = DB::table('expenditure_fund_parties')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id')
                        //->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.candidate_father_name',DB::raw('IFNULL((political_fund_cash + political_fund_checque + political_fund_kind),0) AS partyfund'))

                        ->select('candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_parties.*')
                        ->where('expenditure_fund_parties.ST_CODE', '=', $st_code)
                        ->where('expenditure_fund_parties.constituency_no', '=', $cons_no)
                        ->groupBy('expenditure_fund_parties.candidate_id')
                        ->get();
                // dd($partyfund);
                return view('admin.pc.ro.Expenditure.partyfund-report', ['user_data' => $d, 'partyfund' => $partyfund, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details
                    , 'count' => count($partyfund)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBypartyfund TRY CATCH ENDS HERE   
    }

// end candidateListBypartyfund start function

    public function candidateListBypartyfundgraph(Request $request) {
        //PC ROPC candidateListBypartyfund TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->count();


                //Get total fund from party
                $partyFund = $this->expenditureModel->gettotalPartyfund('PC', $st_code, $cons_no);

                $otherSourcesFund = $this->expenditureModel->gettotalOtherSourcesfund('PC', $st_code, $cons_no);
                $totalFund = ($partyFund->total_partyfund + $otherSourcesFund->total_otherSourcesfund);
                //Get party fund %
                $Percent_partyFund = $this->get_percentage($totalFund, $partyFund->total_partyfund);


                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';
                $data = [
                    ['Oveall summary', 'Taken funds from party'],
                    [$candiatePcName, $Percent_partyFund]
                ];
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBypartyfund TRY CATCH ENDS HERE   
    }

// end candidateListBypartyfund start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByothersfund By ROPC fuction     
     */
    public function candidateListByothersfund(Request $request) {
        //PC ROPC candidateListByothersfund TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $otherfund = DB::table('expenditure_fund_source')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                        // ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.candidate_father_name',DB::raw('IFNULL((other_source_amount),0) AS otherSourcesfund'))
                        ->select('candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_source.*')
                        ->where('expenditure_fund_source.ST_CODE', '=', $st_code)
                        ->where('expenditure_fund_source.constituency_no', '=', $cons_no)
                        ->groupBy('expenditure_fund_source.candidate_id')
                        ->get();
                //dd($otherfund);
                return view('admin.pc.ro.Expenditure.otherfund-report', ['user_data' => $d, 'otherfund' => $otherfund, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details,
                    'count' => count($otherfund)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByothersfund TRY CATCH ENDS HERE   
    }

// end candidateListByothersfund start function

    public function candidateListByothersfundgraph(Request $request) {
        //PC ROPC candidateListByothersfund TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {

                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->count();



                //Get total fund from party
                $partyFund = $this->expenditureModel->gettotalPartyfund('PC', $st_code, $cons_no);
                $otherSourcesFund = $this->expenditureModel->gettotalOtherSourcesfund('PC', $st_code, $cons_no);

                $totalFund = ($partyFund->total_partyfund + $otherSourcesFund->total_otherSourcesfund);
                //Get party fund %
                $Percent_partyFund = $this->get_percentage($totalFund, $partyFund->total_partyfund);
                //Get OtherSources fund %
                $Percent_OthersourcesFund = $this->get_percentage($totalFund, $otherSourcesFund->total_otherSourcesfund);

                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';
                $data = [
                    ['Oveall summary', 'Taken funds from other sources'],
                    [$candiatePcName, $Percent_OthersourcesFund]
                ];
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByothersfund TRY CATCH ENDS HERE   
    }

// end candidateListByothersfund start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByexeedceiling By ROPC fuction     
     */
    public function candidateListByexeedceiling(Request $request) {
        //PC ROPC candidateListByexeedceiling TRY CATCH STARTS HERE 
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                $DataentryStartCandList = DB::table('expenditure_reports')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                //dd($DataentryStartCandList);
                return view('admin.pc.ro.Expenditure.exceedceiling-report', ['user_data' => $d, 'DataentryStartCandList' => $DataentryStartCandList, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByexeedceiling TRY CATCH ENDS HERE   
    }

// end candidateListByexeedceiling start function

    public function annuxure($candidateId) {
        $candidateId = base64_decode($candidateId);
        $candidateData = DeoexpenditureModel::viewById($candidateId);
        //dd($candidateData);
        $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);

        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
        $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
        if ($check_finalize == '') {
            $cand_finalize_ceo = 0;
            $cand_finalize_ro = 0;
        } else {
            $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
            $cand_finalize_ro = $check_finalize->finalized_ac;
        }
        $seched = getschedulebyid($ele_details->ScheduleID);
        $sechdul = checkscheduledetails($seched);

        $GetAbstractData = DB::table('expenditure_annexure_e2')->where('candidate_id', $candidateId)->get()->toArray();
        //  print_r($GetAbstractData);die;
        $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
        $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';

//dd($GetAbstractData);
        return view('admin.pc.ro.Expenditure.annuxure', ['user_data' => $d, 'candidateData' => $candidateData, 'candiatePcName' => $candiatePcName,
            "GetAbstractData" => $GetAbstractData, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'ele_details' => $ele_details]);
    }

    public function SaveAnnuxureData(Request $request) {

        $request = (array) $request->all();
        $candidateId = $request['candidate_id'];
        $user = Auth::user();
        $uid = $user->id;
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);
        $candidateDetail = $this->commonModel->selectone('candidate_nomination_detail', 'candidate_id', $candidateId);

        $candidate_id = !empty($request['candidate_id_update']) ? $request['candidate_id_update'] : "";

        try {

            $data_arr = array();
            foreach ($request as $key => $req_data) {
                $xss = new xssClean;
                $data_arr[$key] = $xss->clean_input($req_data);
            }

            $data_arr['created_by'] = $uid;
            $data_arr['updated_by'] = $uid;



            $data_arr['grand_total_source_funds'] = 
                    !empty($data_arr['amt_own_funds_election_compaign']) ?

                    $data_arr['amt_own_funds_election_compaign'] : 

                            (0 + !empty($data_arr['lump_sum_amt_from_party']) ? 

                            $data_arr['lump_sum_amt_from_party'] : 

                                (0 + !empty($data_arr['lump_sum_amt_from_other']) ? 

                                $data_arr['lump_sum_amt_from_other'] : 
                                0));
            //$dataInserted = $this->commonModel->insertData('expenditure_annexure_e2', $data_arr);
//dd($data_arr);
            if (empty($data_arr['candidate_id_update'])) {
                $dataInserted = $this->commonModel->insertData('expenditure_annexure_e2', $data_arr);
            } else {
                unset($data_arr['candidate_id_update']);
                unset($data_arr['candidate_id']);

                $dataInserted = $this->commonModel->updatedata('expenditure_annexure_e2', 'candidate_id', $candidate_id, $data_arr);
            }

            if ($dataInserted) {
                return 1;
            } else {
                return 0;
            }
        } catch (\Exception $e) {

            return 0;
        }
    }

    public function getscrutinyreport(Request $request) {
        $htmlData = '';
        ////get scrutiny report data ///////
        $candidate_id = $_GET['candidate_id'];
        $scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidate_id);
        $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidate_id);
        $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidate_id);
        $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidate_id);

        if (!empty($scrutinyReportData)) {
            return view('admin.pc.ro.Expenditure.GetScrutinyReport', compact('expensesourecefundbyitem', 'scrutinyReportData', 'expenseunderstated', 'expenseunderstatedbyitem'));
        } else {
            
        }
    }

    public function getprofile(Request $request) {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $stcode = $d->st_code;
                $pc_no = $d->pc_no;
                $candidate_id = $_GET['candidate_id'];
                $profileData = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                        ->join("m_election_details", function($join) {
                            $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                            ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                        })
                        ->where('candidate_nomination_detail.st_code', $stcode)
                        ->where('candidate_nomination_detail.pc_no', $pc_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.party_id', '<>', '1180')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
                        ->where('m_election_details.CONST_TYPE', '=', 'PC')
                        ->get();
                return view('admin.expenditure.GetProfile', compact('profileData'));
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function GetProfileRO(Request $request) {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $d = $this->commonModel->getunewserbyuserid($user->id);


                $candidate_id = $_GET['candidate_id'];
                $profileData = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                        ->join("m_election_details", function($join) {
                            $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                            ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                        })
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.party_id', '<>', '1180')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
                        ->where('m_election_details.CONST_TYPE', '=', 'PC')
                        ->get();
                // get CEO status

                $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();
                $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray();
                $current_status = DB::table('expenditure_mis_current_sataus')->get()->toArray();
                $ReportSingleData = $this->expenditureModel->GetExpeditureSingleData($candidate_id);
                if (!empty($ReportSingleData)) {

                    $ReportSingleData = (array) $ReportSingleData[0];
                }

                return view('admin.expenditure.GetProfileRO', compact('profileData',
                                'ReportSingleData', 'electionType', 'nature_of_default_ac', 'current_status'));
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function printTrackingStatus($candidateId) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->expenditureModel->getunewserbyuserid($user->id, $user->role_id);
            $mpdf = new \Mpdf\Mpdf();

            $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
            $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';

            $candidate_id = base64_decode($candidateId);
            $profileData = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                    })
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
                    ->where('m_election_details.CONST_TYPE', '=', 'PC')
                    ->get();
            // get CEO status cand_name ELECTION_TYPE
            $candidateName = !empty($profileData[0]) ? $profileData[0]->cand_name : '';
            $ELECTION_TYPE = !empty($profileData[0]) ? $profileData[0]->ELECTION_TYPE : '';
            $party_id = !empty($profileData[0]) ? $profileData[0]->party_id : '';
            $partyname = getpartybyid($party_id);
            $partyname = !empty($partyname) ? $partyname->PARTYNAME : '---';

            $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();
            $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray();
            $current_status = DB::table('expenditure_mis_current_sataus')->get()->toArray();
            $ReportSingleData = $this->expenditureModel->GetExpeditureSingleData($candidate_id);
            if (!empty($ReportSingleData)) {

                $ReportSingleData = (array) $ReportSingleData[0];
            }

            $date = date('d-m-Y');
            $title = $date . '_' . "Election Commission of India";
            $mpdf->setHeader($candidateName . ' | ' . $ELECTION_TYPE . ' | ' . $partyname);

            $mpdf->SetFooter($date . '|' . "Election Commission of India" . '|{PAGENO}');
            $mpdf->SetProtection(array('print'));
            $mpdf->SetTitle($title);
            $mpdf->SetAuthor("Election Commission of India");
            $mpdf->SetWatermarkText("Election Commission of India");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->SetDisplayMode('fullpage');

            $pdf = view('admin.expenditure.pdf_ro_tracking', compact('profileData',
                            'ReportSingleData', 'electionType', 'nature_of_default_ac', 'current_status'));
            $mpdf->WriteHTML($pdf);
            $mpdf->Output();
            // return view('admin.expenditure.pdf_eci_tracking', compact('profileData',
            //                 'ReportSingleData', 'electionType', 'nature_of_default_ac', 'current_status'));
        } else {
            return redirect('/officer-login');
        }
    }

    public function generatePDF($candidate_id) {

        $scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidate_id);
        $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidate_id);
        $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidate_id);
        $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidate_id);

        $pdf = MPDF::loadView('admin.pc.ro.Expenditure.ReportPdf', compact('scrutinyReportData', 'expenseunderstated', 'expenseunderstatedbyitem', 'expensesourecefundbyitem'));
        return $pdf->stream('Ro.scrunity-report.pdf');
    }

    function convert_customer_data_to_html($canID = NULL) {

        $candidate_id = $canID;
        $scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidate_id);
        $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidate_id);
        $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidate_id);
        $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidate_id);
        $htmlData = "";
        // $htmlData1 ="<table border='1' style='background-color:red;'><tr><td>mamsh</td></tr></table>";
        $htmlData .= "<div class='col'><center><h5>Candidate Accoussnt Detail</h5></center></div>
                    <br>
                     <div class='row mis_gap'>
                        <div class='col'>Name : </div>
                        <div class='col'>" . $scrutinyReportData[0]->contensting_candiate . "</div>
                    </div>
 
                    <div class='row mis_gap'>
                        <div class='col'>Address of the Candidate  : </div>
                        <div class='col'>" . $scrutinyReportData[0]->candidate_residence_address . "</div>
                    </div>

                    <div class='row mis_gap'>
                        <div class='col'>Political Party Affliation, If Any  : </div>
                        <div class='col'>Independent</div>
                    </div>

                    
                    <div class='row mis_gap'>
                        <div class='col'>Date of Declaration of Result : </div>
                        <div class='col'>" . $scrutinyReportData[0]->date_of_declaration . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Date of Account Reconciliation Meeting   : </div>
                        <div class='col'>" . $scrutinyReportData[0]->date_of_account_rec_meetng . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(i) Whether the Candidate or his Agent had been informed about the Date of Account Reconciliation Meeting in writing  : </div>
                        <div class='col'>" . $scrutinyReportData[0]->reconciliation_meeting_writing . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(ii) Whether he or his Agent has attended the Meeting  : </div>
                        <div class='col'>" . $scrutinyReportData[0]->agent_attend_meeting . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Whether all the defects Reconciled by the Candidate after Account Reconciliation Meeting (Yes or No). (If not, defects that could not be reconciled be shown in Column No. 15) : </div>
                        <div class='col'>" . $scrutinyReportData[0]->defect_reconciliation_meeting . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Last Date Prescribed for Lodging Account : </div>
                        <div class='col'>" . $scrutinyReportData[0]->last_date_prescribed_acct_lodge . "</div>
                    </div>

                    <div class='row mis_gap'>
                        <div class='col'>Whether the Candidate has Lodged the Account : </div>
                        <div class='col'>" . $scrutinyReportData[0]->candidate_lodged_acct . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>If the Candidate has Lodged the Account, Date of Lodging of Account by the Candidate <br> (i) Original Account </div>
                        <div class='col'>" . $scrutinyReportData[0]->date_orginal_acct . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(ii) Revised Account after the Account Reconciliation Meeting : </div>
                        <div class='col'>" . $scrutinyReportData[0]->date_revised_acct . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Whether Account Lodged in Time : </div>
                        <div class='col'>" . $scrutinyReportData[0]->account_lodged_time . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>If Account not Lodged or not Lodged in Time, Whether DEO called for Explanation from the Candidate. If not, reason thereof. : </div>
                        <div class='col'>" . $scrutinyReportData[0]->reason_lodged_not_lodged . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Explanation, if any, given by the Candidate :</div>
                        <div class='col'>" . $scrutinyReportData[0]->explaination_by_candidate . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Comments of the DEO on the Explanation if any, of the Candidate : </div>
                        <div class='col'>" . $scrutinyReportData[0]->comment_by_deo . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Grand Total of all Election Expenses Reported by the Candidate in Part-II of the Abstract Statement :</div>
                        <div class='col'>" . $scrutinyReportData[0]->grand_total_election_exp_by_cadidate . "</div>
                    </div>
                    <br><br>
                    <div class='col'><center><h5>Defects In Formats</h5></center></div>
                    <br>
                    <div class='row mis_gap'>
                        <div class='col'>Whether in the RO's Opinion, the Account of Election Expenses of the Candidate has been Lodged 
in the manner required by the R.P. Act 1951 and C.E. Rules, 1961. : </div>
                        <div class='col'>" . $scrutinyReportData[0]->rp_act . "</div>
                    </div>
 
                    <div class='row mis_gap'>
                        <div class='col'> If No, then please mention the following defects with details <br>
                        (i) Whether Election Expenditure Register Comprising of the Day to Day Account Register,
                                                <br />Cash Register, Bank Register, Abstract Statement has been Lodged </div>
                        <div class='col'>" . $scrutinyReportData[0]->comprising . " <br> " . $scrutinyReportData[0]->comprising_comment . "</div>
                    </div>

                    <div class='row mis_gap'>
                        <div class='col'>(ii) Whether duly sworn in affidavit has been submitted by the Candidate : </div>
                        <div class='col'>" . $scrutinyReportData[0]->duly_sworn . " <br>" . $scrutinyReportData[0]->duly_sworn_comment . "</div>
                    </div>

                    
                    <div class='row mis_gap'>
                        <div class='col'>(iii) Whether requisite Vouchers in respect of items of Election Expenditure Submited :</div>
                        <div class='col'>" . $scrutinyReportData[0]->Vouchers . " <br> " . $scrutinyReportData[0]->Vouchers_comment . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'> (iv) Whether  seprate Bank Account Opened by for Election : </div>
                        <div class='col'>" . $scrutinyReportData[0]->seprate . " <br> " . $scrutinyReportData[0]->seprate_comment . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(v) Whether all Expenditure (Except petty Expenditure) routed through bank Account : </div>
                        <div class='col'>" . $scrutinyReportData[0]->routed . " <Br> " . $scrutinyReportData[0]->routed_comment . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(i) Whether the RO had issued a notice to the Candidate for Rectifying the Defect : </div>
                        <div class='col'>" . $scrutinyReportData[0]->rectifying . " <br>" . $scrutinyReportData[0]->rectifying_comment . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(ii) Whether the Candidate Rectified the Defect :  </div>
                        <div class='col'>" . $scrutinyReportData[0]->rectified . "

                                        <br> " . $scrutinyReportData[0]->rectified_comment . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(iii) Comments of the RO on the above, i.e. whether the defect was rectified or not. : </div>
                        <div class='col'>" . $scrutinyReportData[0]->comment_of_deo . "</div>
                    </div>";

        if (!empty($expenseunderstated)) {

            $htmlData .= "<br><br>
                    <div class='col'><center><h5>Expenses Understated</h5></center></div>
                    <br>
                    <div class='row mis_gap'>
                        <div class='col'>Whether the items of Election Expenses Reported by the Candidate correspond with the Expenses shown in the Shadow Observation Register and Folder of Evidance. If no then mention the following.     </div>
                        <div class='col'>" . $expenseunderstated[0]->status . "</div>
                    </div>";

            $htmlData .= "<table class='table' width='100%' cellpadding='0' id='tblEntAttributes'>
                                <thead>
                                    <tr>
                                        <th width='200'>Item of Expenditure</th>
                                        <th width='140'>Date</th>   
                                        <th width='100'>Page no of Shadow Observation Register / folder of evidence</th>  
                                        <th width='130'>Mention amount as per the shadow observation register/ folder of evidence</th>
                                        <th width='130'>Amount as per the account submitted by the candidate</th>   
                                        <th width='130'>Amount understated by the Candidate </th> 
                                        <th>Description</th>
                                    </tr>
                                </thead>
                            <tbody>";

            if (!empty($expenseunderstatedbyitem)) {
                foreach ($expenseunderstatedbyitem as $item) {

                    $htmlData .= "<tr>
                                    <td>" . $item->expenditure_type . "</td>
                                    <td>" . $item->date_understated . "</td>
                                    <td>" . $item->page_no_observation . "</td>
                                    <td>" . $item->amt_as_per_observation . "</td>
                                    <td>" . $item->amt_as_per_candidate . "</td>
                                    <td>" . $item->amt_understated_by_candidate . "</td>
                                    <td>" . $item->description . "</td>

                                    </tr>";
                }
            }


            $htmlData .= "</tbody>
                        </table>";


            $htmlData .= "<div class='row mis_gap'>
                        <div class='col'> Did the Candidate produce his Register of the Accounting Election Expenditure Register for Inspection by the Observer/RO/Authorized persons 3 times during Campaign Period    </div>
                        <div class='col'>" . $expenseunderstated[1]->status . "</div>
                    </div>

                    <div class='row mis_gap'>
                        <div class='col'>If RO does not agree with the facts Mentioned aginast Row No. 15 referred to above, give the following Details : <Br> (i) Were the defects notice by the RO brought to the notice of the Candidate during Campaign Period or during the Account Reconcialation Meeting </div>
                        <div class='col'>" . $expenseunderstated[2]->status . "</div>
                    </div>

                    
                    <div class='row mis_gap'>
                        <div class='col'>(ii) If Yes, then Annexe copies of all the notices issued relating to Discrepancies with English Translation (If it is in regional language) and mention Date of Notice.  :</div>
                        <div class='col'>PDF FILE</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'> (iii) Did the Candidate give any reply to the Notice ?  : </div>
                        <div class='col'>" . $expenseunderstated[4]->status . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(iv) If Yes, please Annex copies of such Explanation received, (With the English translation of the same, if it is in regional language) and mention Date of Reply : </div>
                        <div class='col'>PDF FILE</div>
                    </div>
                   
                    <div class='row mis_gap'>
                        <div class='col'>(V) RO's Comments/Observations on the Candidate's Explanation :</div>
                        <div class='col'>" . $expenseunderstated[6]->comment . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Whether the RO Agrees that the Expenses are correctly Reported by the Candidate. should be similar to Column no. 8 of Summary Repods of RO : </div>
                        <div class='col'>" . $expenseunderstated[7]->status . " <br>" . $expenseunderstated[7]->comment . "</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'> Comments, If Any by the Expenditure Observer : </div>
                        <div class='col'>" . $expenseunderstated[8]->comment . "</div>
                    </div>";
        }

        $htmlData .= "<br><br><div class='col'><center><h5>Fund Given by Political Party</h5></center></div>
                    <br>
                    
                    <table id='fundParty' class='table table-striped table-bordered' style='width:100%'>
                <thead>
                    <tr>
                        <th colspan='7' class='text-center' color='#ffffff'>Fund Given By Political Party</th>
                    </tr><tr>    
                </tr></thead>
                    <tbody>
                    <tr>
                        <td width='190'><label>By Cash</label></td>
                        <td>" . $scrutinyReportData[0]->political_fund_cash . "</td>
                    </tr>
                    <tr>
                        <td width='190'><label>By Cheque</label></td>
                        <td width='120'>
                           " . $scrutinyReportData[0]->political_fund_checque . " 
                        </td>
                        <td>
                           " . $scrutinyReportData[0]->political_fund_checque_date . "
                        </td>
                        <td>
                            " . $scrutinyReportData[0]->political_fund_bank_name . "
                        </td>
                        <td>
                            " . $scrutinyReportData[0]->political_fund_acct_no . "
                        </td>
                        <td>
                        " . $scrutinyReportData[0]->political_fund_ifsc . "
                        </td>
                        <td>    
                        " . $scrutinyReportData[0]->political_fund_checque_num . "

                        </td>
                    </tr>
                    <tr>
                        <td width='190'><label>In Kind</label></td>
                      <td>" . $scrutinyReportData[0]->political_fund_kind . "</td>
                    </tr>
                    
                    </tbody>
                </table>";
        if (!empty($expensesourecefundbyitem)) {

            $htmlData .= "<br><br><div class='col'><center><h5>Fund Given by Political Party</h5></center></div>
                    <br>
                    
                    <table class='table table-bordered'>
                    <thead>                                                
                        <tr>
                            <th>Name</th>
                            <th>Mode of Payment</th>
                            <th>Amount</th>
                        </tr>    
                    </thead>
                    <tbody>";

            foreach ($expensesourecefundbyitem as $items) {

                $htmlData .= "<tr>
                                    <td>" . $items->other_souce_name . "</td>
                                    <td>" . $items->other_source_payment_mode . "</td>
                                    <td>" . $items->other_source_amount . "</td>
                                    </tr>";
            }


            $htmlData .= "</tbody>    
                            </table>";
        }
        return $htmlData;
    }

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 24-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return tarcking By RO/DEO fuction     
     */
    public function tarcking(request $request) {
        //PC ROPC candidateListByexeedceiling TRY CATCH STARTS HERE 
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                //dd($DataentryStartCandList);
                return view('admin.pc.ro.Expenditure.tracking-status', ['user_data' => $d, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByexeedceiling TRY CATCH ENDS HERE   
    }

    // public function ecrpRegistration(Request $request) {
    //     try {
    //         if (Auth::check()) {
    //             $user = Auth::user();
    //             $uid = $user->id;
    //             $d = $this->commonModel->getunewserbyuserid($user->id);
    //             $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
    //             $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
    //             if ($check_finalize == '') {
    //                 $cand_finalize_ceo = 0;
    //                 $cand_finalize_ro = 0;
    //             } else {
    //                 $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
    //                 $cand_finalize_ro = $check_finalize->finalized_ac;
    //             }
    //             $seched = getschedulebyid($ele_details->ScheduleID);
    //             $sechdul = checkscheduledetails($seched);

    //             $st_code = $d->st_code;
    //             $cons_no = $d->pc_no;

    //             //dd($DataentryStartCandList);
    //             return view('admin.expenditure.ecrp_registration_form', ['user_data' => $d, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details]);
    //         } else {
    //             return redirect('/officer-login');
    //         }
    //     } catch (Exception $ex) {
    //         return Redirect('/internalerror')->with('error', 'Internal Server Error');
    //     }//PC ROPC candidateListByexeedceiling TRY CATCH ENDS HERE   
    // }
    // public function saveEcrpRegistration(Request $request){
    //     return'sdf';
    //     $request = (array) $request->all();         
    //     $user = Auth::user();
    //     $uid = $user->id;
    //     $namePrefix = \Route::current()->action['prefix'];
    //     unset($request['_token']);      

    //     try {

    //         $data_arr = array();
    //         foreach ($request as $key => $req_data) {
    //             $xss = new xssClean;
    //             $data_arr[$key] = $xss->clean_input($req_data);
    //         }

    //         $data_arr['created_by'] = $uid;
    //         $data_arr['updated_by'] = $uid;           
    //         if (empty($data_arr['candidate_id_update'])) {
    //             $dataInserted = $this->commonModel->insertData('ecrp_registrations', $data_arr);
    //         } else {               

    //             $dataInserted = $this->commonModel->updatedata('ecrp_registrations');
    //         }

    //         if ($dataInserted) {
    //             return 1;
    //         } else {
    //             return 0;
    //         }
    //     } catch (\Exception $e) {

    //         return 0;
    //     }
    // }
    // ECRP

    
    public function ecrpRegistration(Request $request) {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }
                $seched = getschedulebyid($ele_details->ScheduleID);
                $sechdul = checkscheduledetails($seched);

                $st_code = $d->st_code;
                $cons_no = $d->pc_no;

                //dd($DataentryStartCandList);
                return view('admin.expenditure.ecrp_registration_form', ['user_data' => $d, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'edetails' => $ele_details]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByexeedceiling TRY CATCH ENDS HERE   
    }

    public function saveEcrpRegistration(Request $request) {
        try {
            if (Auth::check()) {
                $request = (array) $request->all();
                $user = Auth::user();
                $uid = $user->id;
                $namePrefix = \Route::current()->action['prefix'];
                 $lastid =  DB::table('ecrp_registrations')->select('id')->latest('id')->first();                
                unset($request['_token']);
                $data_arr = array();
                foreach ($request as $key => $req_data) {
                    $xss = new xssClean;
                    $data_arr[$key] = $xss->clean_input($req_data);
                }
                $data_arr['created_by'] = $uid;
                $data_arr['updated_by'] = $uid;                
     
                $refId=!empty($lastid->id) && $lastid->id >0 ? $lastid->id:1;
                     
                $data_arr['reference_no']='UAF' . $data_arr['ST_CODE'] . $data_arr['district_no'] . $data_arr['election_type'].$refId;

                $dataInserted = $this->commonModel->insertData('ecrp_registrations', $data_arr);
//                if (empty($data_arr['candidate_id_update'])) {
//                    $dataInserted = $this->commonModel->insertData('ecrp_registrations', $data_arr);
//                } else {
//
//                    //$dataInserted = $this->commonModel->updatedata('ecrp_registrations');
//                }

                if ($dataInserted) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function getdistrictsbystate(Request $request) {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $xss = new xssClean;
                $stcode = $xss->clean_input($request->input('state'));
                $Alldistrict = getalldistrictbystate($stcode);
                return $Alldistrict;
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function assignEcrpRegistration(Request $request) {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $request = (array) $request->all();
                $data_arr = array();
                foreach ($request as $key => $req_data) {
                    $xss = new xssClean;
                    $data_arr[$key] = $xss->clean_input($req_data);
                }
                $dataArray['ecrp_id'] = $data_arr['ecrp_id'];
                $dataArray['ST_CODE'] = $data_arr['stateAssign'];
                $dataArray['district_no'] = $data_arr['districtassign'];
                $dataArray['candidate_id'] = $data_arr['candidate_id'];
                $dataArray['party_id'] = $data_arr['party_id'];
                $dataArray['election_type'] = $data_arr['election_typeassign'];
                $dataArray['created_by'] = $uid;
                $dataArray['updated_by'] = $uid;

                $dataInserted = $this->commonModel->insertData('ecrp_assigns', $dataArray);
                //stateAssign districtassign election_typeassign party_id candidate_id ecrp_id
//                if (empty($data_arr['candidate_id_update'])) {
//                    $dataInserted = $this->commonModel->insertData('ecrp_registrations', $data_arr);
//                } else {
//
//                    $dataInserted = $this->commonModel->updatedata('ecrp_registrations');
//                }

                if ($dataInserted) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function getEcrpList(Request $request) {

        try {
            if (Auth::check()) {
                //  if($request->ajax()) {             
                $data = DB::table('ecrp_registrations')->select('id', 'name','reference_no')->get();


                // declare an empty array for output
                $output = '';
                // if searched countries count is larager than zero
                if (count($data) > 0) {
                    // concatenate output to the array
                    $output = '<select name="ecrp_id" id="ecrp_id" class="form-control" >'
                            . '<option value="" selected>Select ECRP Name </option>';
                    // loop through the result array
                    foreach ($data as $row) {
                        // concatenate output to the array
                        $output .= '<option value="' . $row->id . '">' . $row->name .'-'.$row->reference_no .'</option> ';
                    }
                    // end of output
                    $output .= '</select>';
                } else {
                    // if there's no matching results according to the input
                    $output .= '<option value="">' . 'No results' . '</option> ';
                }
                // return output result array
                return $output;
                //}
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function getParty(Request $request) {
        try {
            if (Auth::check()) {
                if ($request->ajax()) {

                    $data = DB::table('m_party')->select('CCODE', 'PARTYNAME')->get();


                    // declare an empty array for output
                    $output = '';
                    // if searched countries count is larager than zero
                    if (count($data) > 0) {
                        // concatenate output to the array
                        $output = '<select name="party_id" id="party_id" class="form-control" >'
                                . '<option value="" selected>Select Political Party </option>';
                        // loop through the result array
                        foreach ($data as $row) {
                            // concatenate output to the array
                            $output .= '<option value="' . $row->CCODE . '" class="party">' . $row->PARTYNAME . '</option> ';
                        }
                        // end of output
                        $output .= '</select>';
                    } else {
                        // if there's no matching results according to the input
                        $output .= '<option value="">' . 'No results' . '</option> ';
                    }
                    // return output result array
                    return $output;
                }
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function getECRPCandidateList($stcode) {

        try {
            if (Auth::check()) {
                // if($request->ajax()) {
                // return $request->stcode;
                $data = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                        ->join("m_election_details", function($join) {
                            $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                            ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                        })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'm_election_details.*', 'expenditure_reports.finalized_status', 'expenditure_reports.updated_at as finalized_date', 'expenditure_reports.final_by_ro','expenditure_reports.date_of_declaration', 'expenditure_reports.date_orginal_acct', 'expenditure_reports.date_of_receipt', 'expenditure_reports.date_of_receipt_eci', 'expenditure_reports.date_of_sending_deo')
                        ->where('candidate_nomination_detail.st_code', $stcode)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.party_id', '<>', '1180')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('m_election_details.CONST_TYPE', '=', 'PC')
                        ->get();

                // print_r($data);  die;              
                // declare an empty array for output
                $output = '';
                // if searched countries count is larager than zero
                if (count($data) > 0) {
                    // concatenate output to the array
                    $output = '<select name="candidate_id" id="candidate_id" class="form-control" >'
                            . '<option value="" selected>Select Candidate Name </option>';
                    // loop through the result array
                    foreach ($data as $row) {
                        // concatenate output to the array
                        $output .= '<option value="' . $row->candidate_id . '" >' . $row->cand_name . '</option> ';
                    }
                    // end of output
                    $output .= '</select>';
                } else {
                    // if there's no matching results according to the input
                    $output .= '<option value="">' . 'No results' . '</option> ';
                }
                // return output result array
                return $output;
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function getFiledStatementList(Request $request) {
        try {
            if (Auth::check()) {
                 $filedData = DB::table('ecrp_registrations')
                        ->leftjoin('ecrp_assigns', 'ecrp_assigns.ecrp_id', '=', 'ecrp_registrations.id')
                        ->leftjoin('candidate_personal_detail','candidate_personal_detail.candidate_id','=','ecrp_assigns.candidate_id')                        
                        ->select('ecrp_registrations.*', 'ecrp_assigns.*')                         
                        ->get();
                 
                 dd($filedData);
                  return view('admin.expenditure.ecrp_registration_form', compact('filedData' ));            
                
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    //end ECRP

///////Tracking Status End //////////////


///////Tracking Status End //////////////
public function getReturn(Request $request) {
        
    try {
        if (Auth::check()) {
            $user = Auth::user();
            $uid = $user->id;
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);               

            $st_code = $d->st_code;
            $cons_no = $d->pc_no;
            $returnCandList = DB::table('expenditure_reports')
                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                    ->where('expenditure_reports.constituency_no', '=', $cons_no)                     
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->where('expenditure_reports.return_status', '=', 'Returned')
                    ->where('expenditure_reports.finalized_status', '=', '1')
                    ->where('expenditure_reports.final_by_ro', '=', '1') 
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();
                $count=!empty($returnCandList)?count($returnCandList):0;
            
            return view('admin.pc.ro.Expenditure.return-report', ['user_data' => $d, 'returnCandList' => $returnCandList , 'edetails' => $ele_details, "count" => $count]);
        } else {
            return redirect('/officer-login');
        }
    } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }//PC ROPC candidateListByfiledData TRY CATCH ENDS HERE   
}
 public function getNonReturn(Request $request) {
    
    try {
        if (Auth::check()) {
            $user = Auth::user();
            $uid = $user->id;
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);               

            $st_code = $d->st_code;
            $cons_no = $d->pc_no;
            $nonreturnCandList = DB::table('expenditure_reports')
                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
           ->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                    ->where('expenditure_reports.constituency_no', '=', $cons_no)                         
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->where('expenditure_reports.return_status', '=', 'Non-Returned')
                    ->where('expenditure_reports.finalized_status', '=', '1')
                    ->where('expenditure_reports.final_by_ro', '=', '1')
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();
                $count=!empty($nonreturnCandList)?count($nonreturnCandList):0;
            
            return view('admin.pc.ro.Expenditure.non-return-report', ['user_data' => $d, 'nonreturnCandList' => $nonreturnCandList , 'edetails' => $ele_details, "count" => $count]);
        } else {
            return redirect('/officer-login');
        }
    } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }//PC ROPC candidateListByfiledData TRY CATCH ENDS HERE   
}


public function updateStatusReport(Request $request) {
         if (Auth::check()) {
         $user = Auth::user();
         $uid = $user->id;

        $candidateId = $_GET['candidate_id'];
        $reason = $_GET['reason'];

       // $getLog = DB::table('expenditure_logs')->where('created_by',$uid)->where('candidate_id',$candidateId)->first();
        // $countByCEO = !empty($getLog)?$getLog->count_by_ceo:0;
        // $count_by_ceo = $countByCEO + 1;
        $data_definalization = array('candidate_id'=>$candidateId,'created_by'=>$uid,'updated_by'=>$uid,'comment'=>$reason,"count_by_ro"=>'1','log_type'=>'DEFINALIZATION','officer_level'=>'RO');

        if ($candidateId){
            $updateStatus = $this->commonModel->updatedata('expenditure_reports', 'candidate_id', $candidateId, array("finalized_status" => "0",'final_by_ro'=>"0"));
            $insertLog = $this->commonModel->insertData('expenditure_logs', $data_definalization);

            if ($updateStatus){
                Session::put('message', "Permission sent for the updation of scrutiny report successfully.");
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
        }
        else
        {
            return 0;
        }
    }


}

// end class