<?php

namespace App\Http\Controllers\Expenditure;
ini_set('memory_limit', '-1');
ini_set("pcre.backtrack_limit", "2000000");
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
use App\adminmodel\ECIModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Expenditure\EciExpenditureModel;
use App\models\Expenditure\ExpenditureModel;
use Maatwebsite\Excel\Excel;
//INCLUDING CLASSES
use App\Classes\xssClean;
//INCLUDING CLASSES
use DateTime;
use App\models\Expenditure\DeoexpenditureModel;

class EciExpenditureController2014 extends Controller {
/**
* Create a new controller instance.
*
* @return void 
*/
public  $expdb;
public  $DB_CONS_TYPE;
public  $DB_YEAR;
public  $DB_MONTH;
public  $DB_ELE_TYPE;
public function __construct() {
##############Connect with Expenditure DataBase#############
// $expdb='exp_pc_2019_5_general';

$this->middleware(function ($request, $next){
$olddata =request()->route('old'); // For 2014 PC Data
$DB_DATABASE = strtolower(Session::get('DB_DATABASE'));
$m_election_history = DB::connection("mysql_database_history")->table("m_election_history")->select('m_election_history.exp_db_name')->where("db_name", $DB_DATABASE)->first();
// $this->expdb=$m_election_history->exp_db_name; 
$this->expdb = 'eems_pc_2014_may';

 config(['database.connections.mysql.host' => '10.247.137.15']);
config(['database.connections.mysql.database' => $this->expdb]);
/* config(['database.connections.mysql.username' => 'gotosuvidha']);
config(['database.connections.mysql.password' => 'asbhi%supqwe!@1234']); */
config(['database.connections.mysql.username' => 'suvidhaapp']);
config(['database.connections.mysql.password' => 'P7$b&n#367BYaRt91']);
config(['database.connections.mysql.options' => [
  \PDO::ATTR_EMULATE_PREPARES => true
]]);
// DB::reconnect('mysql');
DB::purge('mysql');
DB::connection('mysql');
return $next($request); 
});
############################################################
$this->accessstate='';
$this->middleware(['auth:admin', 'auth']);
//$this->middleware('eci');
$this->middleware(function (Request $request, $next) {
if (!\Auth::check()) {
return redirect('login')->with(Auth::logout());
}

$user = Auth::user();

switch ($user->role_id) {
case '7':
    $this->middleware('eci');
    break;
case '4':
    $this->middleware('ceo');
    break;
case '18':
    $this->middleware('ro');
    break;
case '27':
    $this->middleware('eci_index');
    break;
case '28':
$this->middleware('eci_expenditure');
break;
    
default:
    $this->middleware('eci');
}
return $next($request);
});

$this->middleware('adminsession');
$this->ECIModel = new ECIModel();
$this->commonModel = new commonModel();
$this->eciexpenditureModel = new EciExpenditureModel();
$this->expenditureModel = new ExpenditureModel();
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
* Calculate percetage between the numbers
*/
function get_percentage($total, $number) {
if ($total > 0) {
return round($number / ($total / 100), 2);
} else {
return 0;
}
}

//end number

#################################Start MIS Report By Niraj 28-05-2019#####################################

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 28-05-19
* @author Modified By : 
* @author Modified Date : 
* @author param return getOfficersmis By ECI fuction     
*/  
public function getOfficersmis(Request $request) {   
// Get the current URL without the query string...
$namePrefix = \Route::current()->action['prefix'];
$segments = explode('/', $_SERVER['REQUEST_URI']);
$nameSuffix = $segments['2'];

//dd($request->all());
//PC ECI getOfficersmis TRY CATCH STARTS HERE
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  //$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
//dd($d);
#############Code For State Wise Access By Niraj date 26-11-2019#################
$username=$user->officername;
$st_code = $request->input('state');
$zonestate = $this->eciexpenditureModel->getzonestate($username);

if($zonestate->isEmpty()){
  $permitstates = '';
}else{
  $permitstates = explode(',',$zonestate[0]->assign_state);
}

$permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;

  if(!empty($permitstate)){
      $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
  }else{
     $statelist = getallstate();
  }
  if($permitstates !='') {  $permitstates[] = "All"; }
 
  if(!empty($st_code)){
      $st_code=$st_code;
  }elseif(empty($st_code) && !empty($permitstate)){
     // $st_code=array_values($permitstate)[0];
      $st_code = end($permitstates);
      $allstate= array_pop($permitstates);
  }else {
      $st_code=0;
  }
  
 //pop the last element off



#########################Code For State Wise Access#####################
  $cons_no = $request->input('pc');
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 0;
//  echo  $st_code.'cons_no=>'.$cons_no; die;
   DB::enableQueryLog();
  if (!empty($st_code) && $cons_no == '' &&  $st_code !='All') { 
      $totalContestedCandidatedata = DB::table('expenditure_report')
              ->where('expenditure_report.ST_CODE', '=', $st_code)
              ->select("expenditure_report.CAND_SL_NO as candidate_id", "expenditure_report.ST_CODE as st_code", "expenditure_report.PC_NO as pc_no", DB::raw("COUNT(expenditure_report.C_CODE) as totalcandidate"))
              ->groupBy("expenditure_report.ST_CODE")
              ->get();
  } else if (!empty($st_code) && $cons_no != '' &&  $st_code !='All') {
      $totalContestedCandidatedata = DB::table('expenditure_report')
              ->where('expenditure_report.ST_CODE', '=', $st_code)
              ->where('expenditure_report.PC_NO', '=', $cons_no)
              ->select("expenditure_report.CAND_SL_NO as candidate_id", "expenditure_report.ST_CODE as st_code", "expenditure_report.PC_NO as pc_no", DB::raw("COUNT(expenditure_report.C_CODE) as totalcandidate"))
              ->groupBy("expenditure_report.ST_CODE")
              ->get();
  }else if ( $st_code == '' && $cons_no == '' ) {
      $totalContestedCandidatedata = DB::table('expenditure_report')
             ->select("expenditure_report.CAND_SL_NO as candidate_id", "expenditure_report.ST_CODE as st_code", "expenditure_report.PC_NO as pc_no", DB::raw("COUNT(expenditure_report.C_CODE) as totalcandidate"))
              ->groupBy("expenditure_report.ST_CODE")
              ->get();
  }
  //dd(DB::getQueryLog());
   //dd($totalContestedCandidatedata);
      if($nameSuffix=='mis-officer2014'){ 
          return view('admin.pc.eci.Expenditure.2014.mis-officer', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist,  'count' => count($totalContestedCandidatedata)]);

      }
} else {
  return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ECI getOfficersmis TRY CATCH ENDS HERE    
}

// end getOfficersmis function

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 28-05-19
* @author Modified By : 
* @author Modified Date : 
* @author param return getOfficersmis By ECI fuction     
*/
//ECI getOfficersmis EXCEL REPORT STARTS
public function getOfficersmisEXL(Request $request, $state, $pc) {
//ECI ACTIVE USERS EXCEL REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  $xss = new xssClean;
  $st_code = base64_decode($xss->clean_input($state));
#########################Code For State Wise Access By Niraj date 23-07-2019#####################
$username=$user->officername;
// $st_code = $request->input('state');
$zonestate = $this->eciexpenditureModel->getzonestate($username);

if($zonestate->isEmpty()){
  $permitstates = '';
}else{
  $permitstates = explode(',',$zonestate[0]->assign_state);
}

$permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;

  if(!empty($permitstate)){
      $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
  }else{
     $statelist = getallstate();
  }
  if($permitstates !='') {  $permitstates[] = "All"; }
 
  if(!empty($st_code)){
      $st_code=$st_code;
  }elseif(empty($st_code) && !empty($permitstate)){
     // $st_code=array_values($permitstate)[0];
      $st_code = end($permitstates);
      $allstate= array_pop($permitstates);
  }else {
      $st_code=0;
  }

#########################Code For State Wise Access#####################
  $cons_no = base64_decode($xss->clean_input($pc));
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 0;
   //echo  $st_code.'pc'.$cons_no; die;

  $cur_time = Carbon::now();
  DB::enableQueryLog();
  \Excel::create('EciMIS2014ExpReport_' . '_' . $cur_time, function($excel) use($st_code, $cons_no,$permitstates) {
      $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no,$permitstates) {
    if (!empty($st_code) && $cons_no == '' &&  $st_code !='All') { 
      $totalContestedCandidatedata = DB::table('expenditure_report')
              ->where('expenditure_report.ST_CODE', '=', $st_code)
              ->select("expenditure_report.CAND_SL_NO as candidate_id", "expenditure_report.ST_CODE as st_code", "expenditure_report.PC_NO as pc_no", DB::raw("COUNT(expenditure_report.C_CODE) as totalcandidate"))
              ->groupBy("expenditure_report.ST_CODE")
              ->get();
  } else if (!empty($st_code) && $cons_no != '' &&  $st_code !='All') {
      $totalContestedCandidatedata = DB::table('expenditure_report')
              ->where('expenditure_report.ST_CODE', '=', $st_code)
              ->where('expenditure_report.PC_NO', '=', $cons_no)
              ->select("expenditure_report.CAND_SL_NO as candidate_id", "expenditure_report.ST_CODE as st_code", "expenditure_report.PC_NO as pc_no", DB::raw("COUNT(expenditure_report.C_CODE) as totalcandidate"))
              ->groupBy("expenditure_report.ST_CODE")
              ->get();
  }else if ( $st_code == '' && $cons_no == '' ) {
      $totalContestedCandidatedata = DB::table('expenditure_report')
             ->select("expenditure_report.CAND_SL_NO as candidate_id", "expenditure_report.ST_CODE as st_code", "expenditure_report.PC_NO as pc_no", DB::raw("COUNT(expenditure_report.C_CODE) as totalcandidate"))
              ->groupBy("expenditure_report.ST_CODE")
              ->get();
  }

//dd(DB::getQueryLog());
$arr = array();
$TotalUsers = 0;
$TotalPendingatRO = 0;
$TotalPendingatCEO = 0;
$TotalPendingatECI = 0;
$TotalfiledData = 0;
$TotalnotfiledData = 0;
$Totalpc = 0;
$TotalDEONotice = 0;
$TotalCEONotice = 0;
$Totalfinalcompletedcount = 0;
$TotalFinalByDEO = 0;
//dd($totalContestedCandidatedata);

$user = Auth::user();
$count = 1;

foreach( $totalContestedCandidatedata as $listdata) { 

//get finalby DEO count
$finalbyDEO= $this->eciexpenditureModel->gettotalfinalbyDEO2014('PC',$listdata->st_code,$cons_no);
// $TotalFinalByDEO += $finalbyDEO;

//Get pendingatECI Count 
//$pendingatECI = $this->eciexpenditureModel->gettotalfinalbyeci('PC', $listdata->st_code, $cons_no);

//Get filedcount Count 
$filedcount = $this->eciexpenditureModel->gettotaldataentryStart2014('PC', $listdata->st_code, $cons_no);

// Get Pending Data Count 
$notfiledcount= $listdata->totalcandidate - $filedcount;


//Get noticeatDEOCount Count 
$noticeatDEOCount = $this->eciexpenditureModel->gettotalnoticeatCEO2014('PC', $listdata->st_code, $cons_no);

//Get noticeatCEOCount Count 
$noticeatCEOCount = $this->eciexpenditureModel->gettotalnoticeatCEO2014('PC', $listdata->st_code, $cons_no);

//Get finalcompletedcount at ECI Count 
$finalcompletedcount = $this->eciexpenditureModel->gettotalfinalbyDEO2014('PC', $listdata->st_code, $cons_no);
 if($finalcompletedcount>0){
           $pendingatECI= $finalcompletedcount-$finalbyDEO;
         }
        


$st = getstatebystatecode($listdata->st_code);
$pcbystate=getpcbystate($listdata->st_code);
$pccount=count($pcbystate);
$Totalpc += $pccount;  

 //pending at DEO
  if($finalbyDEO >= 0 ){
	$pendingatRO =$listdata->totalcandidate-($finalbyDEO);
	if($pendingatRO >= 0 ){$TotalPendingatRO += $pendingatRO;}
	}  
 //pending at CEO	
 if($finalbyDEO >= 0 && $pendingatECI >=0 && $finalcompletedcount >=0){
 $pendingatCEO = $finalbyDEO-($pendingatECI + $finalcompletedcount);
 if($pendingatCEO >= 0) {
 // $TotalPendingatCEO += $pendingatCEO; 
 }
}

              
              $filedcount = !empty($filedcount) ? $filedcount : '0';
              $finalbyDEO = !empty($finalbyDEO) ? $finalbyDEO : '0';
              $pendingatRO = !empty($pendingatRO) ? $pendingatRO : '0';
              $pendingatCEO = !empty($pendingatCEO) ? $pendingatCEO : '0';
              $pendingatECI = !empty($pendingatECI) ? $pendingatECI : '0';
              $noticeatDEOCount = !empty($noticeatDEOCount) ? $noticeatDEOCount : '0';
              $noticeatCEOCount = !empty($noticeatCEOCount) ? $noticeatCEOCount : '0';
              $finalcompletedcount = !empty($finalcompletedcount) ? $finalcompletedcount : '0';
              $pccount = !empty($pccount) ? $pccount : '0';
              $notfiledcount = !empty($notfiledcount) ? $notfiledcount : '0';


              $data = array(
                  $st->ST_NAME,
                  $pccount,
                  $listdata->totalcandidate,
                  $finalbyDEO,
                  $pendingatRO,
                  $pendingatCEO,
                  $pendingatECI,
                  $finalcompletedcount,
                  $noticeatDEOCount,
                  $noticeatCEOCount
              );
              $TotalUsers += $listdata->totalcandidate;
              $TotalFinalByDEO += $finalbyDEO;
              $TotalPendingatCEO += $pendingatCEO;
              $TotalPendingatECI += $pendingatECI;
              $TotalDEONotice += $noticeatDEOCount;
              $TotalCEONotice += $noticeatCEOCount;
              $Totalfinalcompletedcount += $finalcompletedcount;
              $TotalnotfiledData += $notfiledcount;
              $TotalfiledData += $filedcount;
              array_push($arr, $data);
              // }
              $count++;
          }
          $totalvalues = array('Total',$Totalpc, $TotalUsers, $TotalFinalByDEO, $TotalPendingatRO,$TotalDEONotice, $TotalPendingatCEO,$TotalCEONotice,$TotalPendingatECI,$Totalfinalcompletedcount);
          // print_r($totalvalues);die;
          array_push($arr, $totalvalues);
          $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
              'State Name', 'Total PC','Total Candidate','Finalise By DEO', 'Pending At DEO','Notice At DEO', 'Pending At CEO','Notice At CEO','Pending At ECI','Closed/Disqualified/Case Dropped'
                  )
          );
      });
  })->export('csv');
} else {
  return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI getOfficersmisEXL EXCEL REPORT TRY CATCH BLOCK ENDS
}

//ECI ACTIVE USERS EXCEL REPORT FUNCTION ENDS
//ECI getOfficersmis PDF REPORT STARTS
public function getOfficersmisPDF(Request $request, $state, $pc) {
//ECI getOfficersmisPdf PDF REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  $xss = new xssClean;
  $st_code = base64_decode($xss->clean_input($state));
#########################Code For State Wise Access By Niraj date 23-07-2019#####################
$username=$user->officername;
// $st_code = $request->input('state');
$zonestate = $this->eciexpenditureModel->getzonestate($username);

if($zonestate->isEmpty()){
  $permitstates = '';
}else{
  $permitstates = explode(',',$zonestate[0]->assign_state);
}

$permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;

  if(!empty($permitstate)){
      $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
  }else{
     $statelist = getallstate();
  }
  if($permitstates !='') {  $permitstates[] = "All"; }
 
  if(!empty($st_code)){
      $st_code=$st_code;
  }elseif(empty($st_code) && !empty($permitstate)){
     // $st_code=array_values($permitstate)[0];
      $st_code = end($permitstates);
      $allstate= array_pop($permitstates);
  }else {
      $st_code=0;
  }

#########################Code For State Wise Access#####################
  $cons_no = base64_decode($xss->clean_input($pc));
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 0;

  $cur_time = Carbon::now();
  if (!empty($st_code) && $cons_no == '' &&  $st_code !='All') { 
      $totalContestedCandidatedata = DB::table('expenditure_report')
              ->where('expenditure_report.ST_CODE', '=', $st_code)
              ->select("expenditure_report.CAND_SL_NO as candidate_id", "expenditure_report.ST_CODE as st_code", "expenditure_report.PC_NO as pc_no", DB::raw("COUNT(expenditure_report.C_CODE) as totalcandidate"))
              ->groupBy("expenditure_report.ST_CODE")
              ->get();
  } else if (!empty($st_code) && $cons_no != '' &&  $st_code !='All') {
      $totalContestedCandidatedata = DB::table('expenditure_report')
              ->where('expenditure_report.ST_CODE', '=', $st_code)
              ->where('expenditure_report.PC_NO', '=', $cons_no)
              ->select("expenditure_report.CAND_SL_NO as candidate_id", "expenditure_report.ST_CODE as st_code", "expenditure_report.PC_NO as pc_no", DB::raw("COUNT(expenditure_report.C_CODE) as totalcandidate"))
              ->groupBy("expenditure_report.ST_CODE")
              ->get();
  }else if ( $st_code == '' && $cons_no == '' ) {
      $totalContestedCandidatedata = DB::table('expenditure_report')
             ->select("expenditure_report.CAND_SL_NO as candidate_id", "expenditure_report.ST_CODE as st_code", "expenditure_report.PC_NO as pc_no", DB::raw("COUNT(expenditure_report.C_CODE) as totalcandidate"))
              ->groupBy("expenditure_report.ST_CODE")
              ->get();
  }

  //dd($totalContestedCandidatedata);

  $pdf = PDF::loadView('admin.pc.eci.Expenditure.2014.mis-officerPDFhtml', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata,'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist]);    
  return $pdf->download('EciPCMIS2014Pdf_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
  return view('admin.pc.eci.Expenditure.mis-officerPDFhtml');
} else {
  return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI mis-officerPDFhtml PDF REPORT TRY CATCH BLOCK ENDS
}
//ECI ACTIVE USERS PDF REPORT FUNCTION ENDS

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 29-11-19
* @author Modified By : 
* @author Modified Date : 
* @author param return finalCandidateList By ECI fuction     
*/
public function finalCandidateList(Request $request, $state, $pc) {
//dd($request->all());
//PC ECI finalCandidateList TRY CATCH STARTS HERE
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

$xss = new xssClean;
$st_code = base64_decode($xss->clean_input($state));
$cons_no = base64_decode($xss->clean_input($pc));
$st_code = !empty($st_code) ? $st_code : '';
$cons_no = !empty($cons_no) ? $cons_no : 'ALL';

DB::enableQueryLog();


###########Code For State Wise Access By Niraj date 23-07-2019#####################
$username=$user->officername;
//$st_code = base64_decode($request->input('state'));
$zonestate = $this->eciexpenditureModel->getzonestate($username);
if($zonestate->isEmpty()){
$permitstates = '';
}else{
$permitstates = explode(',',$zonestate[0]->assign_state);
}

$permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;

if(!empty($permitstate)){
$statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
}else{
$statelist = getallstate();
}
if(!empty($st_code)){
$st_code=$st_code;
}elseif(empty($st_code) && !empty($permitstate)){
$st_code=array_values($permitstate)[0];
}else {
$st_code=0;
}

#########################Code For State Wise Access#####################

if (!empty($st_code) && !is_numeric($cons_no)) { 
// echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
} else if (!empty($st_code) && is_numeric($cons_no)) {
$totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
} else {
$totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
/* $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
        //->where('candidate_nomination_detail.st_code','=',$st_code)
        //->where('candidate_nomination_detail.pc_no','=',$cons_no) 
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        // ->count();
        ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
        //->groupBy("candidate_nomination_detail.st_code")
        ->get();*/
}
//dd(DB::getQueryLog());
//  dd($totalContestedCandidatedata);

return view('admin.pc.eci.Expenditure.2014.candidate-report', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist,'count' => count($totalContestedCandidatedata)]);
} else {
return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ECI getOfficersmis TRY CATCH ENDS HERE    
}

// end getOfficersmis function

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 28-05-19
* @author Modified By : 
* @author Modified Date : 
* @author param return getOfficersmis By ECI fuction     
*/
//ECI getOfficersmis EXCEL REPORT STARTS
public function finalCandidateListEXL(Request $request, $state, $pc) {
//ECI ACTIVE USERS EXCEL REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$xss = new xssClean;
$st_code = base64_decode($xss->clean_input($state));
$cons_no = base64_decode($xss->clean_input($pc));
$st_code = !empty($st_code) ? $st_code : 0;
$cons_no = !empty($cons_no) ? $cons_no : 'ALL';
// echo  $st_code.'pc'.$cons_no; die;

$cur_time = Carbon::now();
DB::enableQueryLog();
\Excel::create('ECICandidateMIS2014Excel_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
$excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

if (!empty($st_code) && !is_numeric($cons_no)) { 
// echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));

} else if (!empty($st_code) && is_numeric($cons_no)) { 
// echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
} else {
// echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
}

// dd(DB::getQueryLog());

$arr = array();
$TotalUsers = 0;
$user = Auth::user();
$count = 1;
foreach ($totalContestedCandidatedata as $candDetails) {
  $st = getstatebystatecode($candDetails->st_code);
  //dd($candDetails);
  $pcDetails = getpcbypcno($candDetails->st_code, $candDetails->pc_no);
  $date = new DateTime($candDetails->created_at);
  //echo $date->format('d.m.Y'); // 31.07.2012
  $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
  $data = array(
      $st->ST_NAME,
      $pcDetails->PC_NO . '-' . $pcDetails->PC_NAME,
      $candDetails->cand_name,
      $candDetails->PARTYNAME
  );
  $TotalUsers = count($totalContestedCandidatedata);
  array_push($arr, $data);
  // }
  $count++;
}
$totalvalues = array('Total', $TotalUsers);
// print_r($totalvalues);die;
array_push($arr, $totalvalues);
$sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
  'State','PC No & Name', 'Candidate Name', 'Party Name'
      )
);
});
})->export('csv');
} else {
return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI finalCandidateList EXCEL REPORT TRY CATCH BLOCK ENDS
}

//ECI ACTIVE USERS EXCEL REPORT FUNCTION ENDS
//ECI finalCandidateList PDF REPORT STARTS
public function finalCandidateListPDF(Request $request, $state, $pc) {
//ECI finalCandidateList PDF REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$xss = new xssClean;
$st_code = base64_decode($xss->clean_input($state));
$cons_no = base64_decode($xss->clean_input($pc));
$st_code = !empty($st_code) ? $st_code : 0;
$cons_no = !empty($cons_no) ? $cons_no : 'ALL';
$cur_time = Carbon::now();
if (!empty($st_code && $cons_no == '')) {
// echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
} else if (!empty($st_code) && $cons_no != '') {
// echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
} else {
// echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
}
$pdf = PDF::loadView('admin.pc.eci.Expenditure.candidatePDFhtml', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata,'st_code' => $st_code,'cons_no' => $cons_no]);
return $pdf->download('EciCandidateMISPdf_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
return view('admin.pc.eci.Expenditure.candidatePDFhtml');
} else {
return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI mis-officerPDFhtml PDF REPORT TRY CATCH BLOCK ENDS
}

//ECI candidate PDF REPORT FUNCTION ENDS

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 29-11-19
* @author Modified By : 
* @author Modified Date : 
* @author param return getcandidateListpendingatRO By ECI fuction     
*/
public function getcandidateListpendingatRO(Request $request, $state, $pc) {
//PC ECI getcandidateListpendingatRO TRY CATCH STARTS HERE
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  $xss = new xssClean;
  $st_code = base64_decode($xss->clean_input($state));
  $cons_no = base64_decode($xss->clean_input($pc));
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 'ALL';
  // echo $st_code.'cons_no'.$cons_no; die;
  DB::enableQueryLog();
  $candidate_id=array();
if($st_code == '0' && !is_numeric($cons_no)) { 
   $partiallyCandList=DB::select('call Exp_Pending_Reports(?,?)',array($st_code,$cons_no));
} elseif ($st_code != '0' && !is_numeric($cons_no)) {
$partiallyCandList=DB::select('call Exp_Pending_Reports(?,?)',array($st_code,$cons_no));

} elseif ($st_code != '0' && is_numeric($cons_no)) {
$partiallyCandList=DB::select('call Exp_Pending_Reports(?,?)',array($st_code,$cons_no));
}

  // dd(DB::getQueryLog());
  return view('admin.pc.eci.Expenditure.2014.pendingatdeo-mis', ['user_data' => $d, 'partiallyCandList' => $partiallyCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($partiallyCandList)]);
} else {
  return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ECI getcandidateListpendingatRO TRY CATCH ENDS HERE   
}

// end getcandidateListpendingatRO function

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 28-05-19
* @author Modified By : 
* @author Modified Date : 
* @author param return getcandidateListpendingatROEXL By ECI fuction     
*/
//ECI getcandidateListpendingatROEXL EXCEL REPORT STARTS
public function getcandidateListpendingatROEXL(Request $request, $state, $pc) {
//ECI getcandidateListpendingatROEXL EXCEL REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$xss = new xssClean;
$st_code = base64_decode($xss->clean_input($state));
$cons_no = base64_decode($xss->clean_input($pc));
$st_code = !empty($st_code) ? $st_code : 0;
$cons_no = !empty($cons_no) ? $cons_no : 'ALL';
//echo  $st_code.'pc'.$cons_no; die;
$cur_time = Carbon::now();
\Excel::create('ECIPendingatDEOCandidateMIS2014_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
$excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

$candidate_id=array();
if($st_code == '0' && !is_numeric($cons_no)) { 
   $partiallyCandList=DB::select('call Exp_Pending_Reports(?,?)',array($st_code,$cons_no));
} elseif ($st_code != '0' && !is_numeric($cons_no)) {
$partiallyCandList=DB::select('call Exp_Pending_Reports(?,?)',array($st_code,$cons_no));

} elseif ($st_code != '0' && is_numeric($cons_no)) {
$partiallyCandList=DB::select('call Exp_Pending_Reports(?,?)',array($st_code,$cons_no));
}

$arr = array();
$TotalUsers = 0;
$user = Auth::user();
$count = 1;
foreach ($partiallyCandList as $candDetails) { 
$st = getstatebystatecode($candDetails->st_code);
$pcDetails = getpcbypcno($candDetails->st_code, $candDetails->pc_no);

$pcno=!empty($pcDetails->PC_NO) ?  $pcDetails->PC_NO : '';
$pcname=!empty($pcDetails->PC_NAME) ?  $pcDetails->PC_NAME : '';

$lastdate = new DateTime($candDetails->created_at);
//echo $date->format('d.m.Y'); // 31.07.2012
$lodgingDate = $lastdate->format('d-m-Y'); // 31-07-2012
/*
$scrutinysubmit = new DateTime($candDetails->report_submitted_date);
$scrutinyreportsubmitdate = $scrutinysubmit->format('d-m-Y'); // 31-07-2012
//$scrutinyreportsubmitdate= date('d-m-Y',strtotime($candDetails->report_submitted_date));
$candidatelodgingdate= date('d-m-Y',strtotime($candDetails->date_orginal_acct));

$sendingdatetoceo = new DateTime($candDetails->date_of_sending_deo);
$ceosendingdate = $sendingdatetoceo->format('d-m-Y'); // 31-07-2012

$ceoreceiveddate = new DateTime($candDetails->date_of_receipt);
$ceoreceivedate = $ceoreceiveddate->format('d-m-Y'); // 31-07-2012

// $lodgingDate =!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';

$lodgingDate =$lodgingDate ??  '22-06-2019';
$scrutinyreportsubmitdate =$scrutinyreportsubmitdate ??  'N/A';
$candidatelodgingdate =$candidatelodgingdate ??  'N/A';
$ceosendingdate =$ceosendingdate ??  'N/A';
$ceoreceivedate =$ceoreceivedate ??  'N/A';
*/
// $lodgingDate=!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
$data = array(
$st->ST_NAME,
$pcno . '-' . $pcname,
$candDetails->cand_name,
$candDetails->PARTYNAME
);
$TotalUsers = count($partiallyCandList);
array_push($arr, $data);
// }
$count++;
}
$totalvalues = array('Total', $TotalUsers);
// print_r($totalvalues);die;
array_push($arr, $totalvalues);
$sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
'State','PC No & Name','Candidate Name', 'Party Name'
)
);
});
})->export('csv');
} else {
return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI getcandidateListpendingatROPDF EXCEL REPORT TRY CATCH BLOCK ENDS
}

//ECI getcandidateListpendingatROPDF EXCEL REPORT FUNCTION ENDS
//ECI getcandidateListpendingatROPDF PDF REPORT STARTS
public function getcandidateListpendingatROPDF(Request $request, $state, $pc) {
//ECI getcandidateListpendingatROPDF PDF REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$xss = new xssClean;
$st_code = base64_decode($xss->clean_input($state));
$cons_no = base64_decode($xss->clean_input($pc));
$st_code = !empty($st_code) ? $st_code : 0;
$cons_no = !empty($cons_no) ? $cons_no : 'ALL';
$cur_time = Carbon::now();
$candidate_id=array();
if($st_code == '0' && !is_numeric($cons_no)) { 
   $partiallyCandList=DB::select('call Exp_Pending_Reports(?,?)',array($st_code,$cons_no));
  /*$EcifinalbyDEO = DB::table('expenditure_reports')
  ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
  ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
  ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
  ->select('expenditure_reports.candidate_id')
  //->where('expenditure_reports.ST_CODE', '=', $st_code)
 // ->where('expenditure_reports.constituency_no', '=', $cons_no)
  ->where('candidate_nomination_detail.application_status', '=', '6')
  ->where('candidate_nomination_detail.finalaccepted', '=', '1')
  ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
  ->where('expenditure_reports.final_by_ro', '1')
  ->where('expenditure_reports.finalized_status', '1')
  ->whereNotNull('expenditure_reports.date_of_sending_deo')
  ->groupBy('expenditure_reports.candidate_id')
  ->get();
  foreach ($EcifinalbyDEO as $EcifinalbyDEOData) {
      $candidate_id[] = $EcifinalbyDEOData->candidate_id;
  }
 // echo '<pre>'; print_r( $candidate_id);
  $partiallyCandList = DB::table('candidate_nomination_detail')
          ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
          ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
          ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
          ->where('candidate_nomination_detail.application_status', '=', '6')
          ->where('candidate_nomination_detail.finalaccepted', '=', '1')
          ->where('candidate_nomination_detail.symbol_id', '<>', '200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
          ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
           ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
          ->get();
          */

} elseif ($st_code != '0' && !is_numeric($cons_no)) {
$partiallyCandList=DB::select('call Exp_Pending_Reports(?,?)',array($st_code,$cons_no));

} elseif ($st_code != '0' && is_numeric($cons_no)) {
$partiallyCandList=DB::select('call Exp_Pending_Reports(?,?)',array($st_code,$cons_no));
}
$pdf = PDF::loadView('admin.pc.eci.Expenditure.2014.candidatePendingatDEOPDFhtml', ['user_data' => $d, 'pendingatDEOCandList' => $partiallyCandList]);
return $pdf->download('EcipendingatDEOCandidateMIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
return view('admin.pc.eci.Expenditure.candidatePendingatDEOPDFhtml');
} else {
return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI getcandidateListpendingatROPDF PDF REPORT TRY CATCH BLOCK ENDS
}

//ECI getcandidateListpendingatROPDF PDF REPORT FUNCTION ENDS


/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 19-11-19
* @author Modified By : 
* @author Modified Date : 
* @author param return EcifinalbyDEO By ECI fuction     
*/
public function EcifinalbyDEO(Request $request, $state, $pc) {
//PC ECI EcifinalbyDEO TRY CATCH STARTS HERE
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$xss = new xssClean;
$st_code = base64_decode($xss->clean_input($state));
$cons_no = base64_decode($xss->clean_input($pc));
$st_code = !empty($st_code) ? $st_code : 0;
$cons_no = !empty($cons_no) ? $cons_no : 'ALL';
// echo $st_code.'cons_no'.$cons_no; die;
DB::enableQueryLog();
if ($st_code == '0' && $cons_no == 'ALL') {
// echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$EcifinalbyDEO= DB::select('call Final_exp_report(?,?)',array($st_code,$cons_no));

} elseif ($st_code != '0' && $cons_no == '0') {
// echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$EcifinalbyDEO= DB::select('call Final_exp_report(?,?)',array($st_code,$cons_no));
} elseif ($st_code != '0' && $cons_no != '0') {
  // echo $st_code.'---pc--->'.$cons_no; 
$EcifinalbyDEO= DB::select('call Final_exp_report(?,?)',array($st_code,$cons_no));
}
// dd(DB::getQueryLog());
return view('admin.pc.eci.Expenditure.2014.finalbydeo-mis', ['user_data' => $d, 'EcifinalbyDEO' => $EcifinalbyDEO, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($EcifinalbyDEO)]);
} else {
return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ECI EcifinalbyDEO TRY CATCH ENDS HERE   
}

// end getcandidateListpendingatRO function

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 19-11-19
* @author Modified By : 
* @author Modified Date : 
* @author param return EcifinalbyDEOMISEXL By ECI fuction     
*/
//ECI EcifinalbyDEOMISEXL EXCEL REPORT STARTS
public function EcifinalbyDEOMISEXL(Request $request, $state, $pc) {
//ECI getcandidateListpendingatROEXL EXCEL REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$xss = new xssClean;
$st_code = base64_decode($xss->clean_input($state));
$cons_no = base64_decode($xss->clean_input($pc));
$st_code = !empty($st_code) ? $st_code : 0;
$cons_no = !empty($cons_no) ? $cons_no : 'ALL';
//echo  $st_code.'pc'.$cons_no; die;
$cur_time = Carbon::now();
\Excel::create('ECIFinalbyDEOMISPC2014_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
$excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

if ($st_code == '0' && !is_numeric($cons_no)) {
  // echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$EcifinalbyDEOMISEXL= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));

} elseif ($st_code != '0' && is_numeric($cons_no)) {
  // echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$EcifinalbyDEOMISEXL= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));

} elseif ($st_code != '0' && !is_numeric($cons_no)) {
// echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$EcifinalbyDEOMISEXL= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
}

$arr = array();
$TotalUsers = 0;
$user = Auth::user();
$count = 1;
foreach ($EcifinalbyDEOMISEXL as $candDetails) {
$st = getstatebystatecode($candDetails->st_code);
//dd($candDetails);
$pcDetails = getpcbypcno($candDetails->st_code, $candDetails->pc_no);


$report_submitted_data = new DateTime($candDetails->report_submitted_date);
$scrutinyreportsubmitdate=$report_submitted_data->format('d-m-Y'); // 31-07-2012


$date_orginal_acct = new DateTime($candDetails->created_at);
$candidatelodgingdate=$date_orginal_acct->format('d-m-Y'); // 31-07-2012


$lastlodgingDate ='16-06-2014';
$scrutinyreportsubmitdate =$scrutinyreportsubmitdate ??  'N/A';
$candidatelodgingdate = (!empty($candidatelodgingdate) && $candidatelodgingdate !='30-11--0001' && $candidatelodgingdate > '05-12-2019') ?  $candidatelodgingdate : 'N/A';

$data = array(
$pcDetails->PC_NO . '-' . $pcDetails->PC_NAME,
$candDetails->cand_name,
$candDetails->PARTYNAME,
$lastlodgingDate,
$scrutinyreportsubmitdate,
$candidatelodgingdate
);
$TotalUsers = count($EcifinalbyDEOMISEXL);
array_push($arr, $data);
// }
$count++;
}
$totalvalues = array('Total', $TotalUsers);
// print_r($totalvalues);die;
array_push($arr, $totalvalues);
$sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
'PC No & Name', 'Candidate Name', 'Party Name', 'Last Date Of Lodging','Date of Scrutiny Report Submission','Date of Lodging A/C By Candidate'
)
);
});
})->export('csv');
} else {
return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI getcandidateListpendingatROPDF EXCEL REPORT TRY CATCH BLOCK ENDS
}

//ECI EcifinalbyDEOMISPDF EXCEL REPORT FUNCTION ENDS
//ECI EcifinalbyDEOMISPDF PDF REPORT STARTS
public function EcifinalbyDEOMISPDF(Request $request, $state, $pc) {
//ECI getcandidateListpendingatROPDF PDF REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  $xss = new xssClean;
  $st_code = base64_decode($xss->clean_input($state));
  $cons_no = base64_decode($xss->clean_input($pc));
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 'ALL';
  $cur_time = Carbon::now();
   if ($st_code == '0' && $cons_no == 'ALL') {
    // echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$EcifinalbyDEOMISPDF= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
              
          } elseif ($st_code != '0' && $cons_no == 'ALL') {
// echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$EcifinalbyDEOMISPDF= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
          } elseif ($st_code != '0' && $cons_no != '0') {
             // echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
 $EcifinalbyDEOMISPDF= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
          }
          
  $pdf = PDF::loadView('admin.pc.eci.Expenditure.2014.finalbyDEOPDFhtml', ['user_data' => $d, 'EcifinalbyDEOMISPDF' => $EcifinalbyDEOMISPDF]);
  return $pdf->download('EcifinalbyDEOCandidateMIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
  return view('admin.pc.eci.Expenditure.finalbyDEOPDFhtml');
} else {
  return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI EcifinalbyDEOMISPDF PDF REPORT TRY CATCH BLOCK ENDS
}

//ECI EcifinalbyDEOMISPDF PDF REPORT FUNCTION ENDS

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 28-05-19
* @author Modified By : 
* @author Modified Date : 
* @author param return getcandidateListpendingatCEO By ECI fuction     
*/
public function getcandidateListpendingatCEO(Request $request, $state, $pc) {
//PC ECI getcandidateListpendingatCEO TRY CATCH STARTS HERE
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  $xss = new xssClean;
  $st_code = base64_decode($xss->clean_input($state));
  $cons_no = base64_decode($xss->clean_input($pc));
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 0;
  // echo $st_code.'cons_no'.$cons_no; die;
  $candidate_id=[];
  if ($st_code == '0' && $cons_no == '0') {
// echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$pendingateciCandlist= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
      
  
  } elseif ($st_code != '0' && $cons_no == '0') {
     // echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$pendingateciCandlist= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && $cons_no != '0') {
$pendingateciCandlist= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
    
  }
  //dd($DataentryStartCandList);
  return view('admin.pc.eci.Expenditure.pendingatceo-mis', ['user_data' => $d, 'finalbyceoCandList' => $finalbyceoCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($finalbyceoCandList)]);
} else {
  return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ECI candidateListByfinalizeData TRY CATCH ENDS HERE   
}

// end candidateListByfinalizeData start function

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 28-05-19
* @author Modified By : 
* @author Modified Date : 
* @author param return getcandidateListpendingatROEXL By ECI fuction     
*/
//ECI getcandidateListpendingatCEOEXL EXCEL REPORT STARTS
public function getcandidateListpendingatCEOEXL(Request $request, $state, $pc) {
//ECI getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  $xss = new xssClean;
  $st_code = base64_decode($xss->clean_input($state));
  $cons_no = base64_decode($xss->clean_input($pc));
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 'ALL';
  // echo  $st_code.'pc'.$cons_no; die;
  $cur_time = Carbon::now();

  \Excel::create('ECIPendingatCEOCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
      $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

         $candidate_id=[];
  if ($st_code == '0' && $cons_no == '0') {
    // echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$pendingateciCandlist= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
     
  
  } elseif ($st_code != '0' && $cons_no == '0') {
          // echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$pendingateciCandlist= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && $cons_no != '0') {    // echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$pendingateciCandlist= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
  }

$arr = array();
$TotalUsers = 0;
$user = Auth::user();
$count = 1;
foreach ($finalbyceoCandList as $candDetails) {
$st = getstatebystatecode($candDetails->ST_CODE);
//dd($candDetails);
$pcDetails = getpcbypcno($candDetails->ST_CODE, $candDetails->constituency_no);
$lastdate = new DateTime($candDetails->last_date_prescribed_acct_lodge);
//echo $date->format('d.m.Y'); // 31.07.2012
$lodgingDate = $lastdate->format('d-m-Y'); // 31-07-2012

$scrutinysubmit = new DateTime($candDetails->report_submitted_date);
$scrutinyreportsubmitdate = $scrutinysubmit->format('d-m-Y'); // 31-07-2012
//$scrutinyreportsubmitdate= date('d-m-Y',strtotime($candDetails->report_submitted_date));
$candidatelodgingdate= date('d-m-Y',strtotime($candDetails->date_orginal_acct));

$sendingdatetoceo = new DateTime($candDetails->date_of_sending_deo);
$ceosendingdate = $sendingdatetoceo->format('d-m-Y'); // 31-07-2012

$ceoreceiveddate = new DateTime($candDetails->date_of_receipt);
$ceoreceivedate = $ceoreceiveddate->format('d-m-Y'); // 31-07-2012

// $lodgingDate =!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';

$lodgingDate =$lodgingDate ??  '22-06-2019';
$scrutinyreportsubmitdate =$scrutinyreportsubmitdate ??  'N/A';
$candidatelodgingdate =$candidatelodgingdate ??  'N/A';
$ceosendingdate =$ceosendingdate ??  'N/A';
$ceoreceivedate =$ceoreceivedate ??  'N/A';
$data = array(
  $st->ST_NAME,
  $pcDetails->PC_NO . '-' . $pcDetails->PC_NAME,
  $candDetails->cand_name,
  $candDetails->PARTYNAME,
  $lodgingDate,
	$scrutinyreportsubmitdate,
	$candidatelodgingdate,
	$ceosendingdate,
	$ceoreceivedate
              );
              $TotalUsers = count($finalbyceoCandList);
              array_push($arr, $data);
              // }
              $count++;
          }
          $totalvalues = array('Total', $TotalUsers);
          // print_r($totalvalues);die;
          array_push($arr, $totalvalues);
          $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
              'State','PC No & Name', 'Candidate Name', 'Party Name', 'Last Date Of Lodging','Date Of Scrutiny Report Submission','Date Of Lodging A/C By Candidates','Date Of Sending To CEO','Date Of Received By CEO'
                  )
          );
      });
  })->export('csv');
} else {
  return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK ENDS
}

//ECI getcandidateListpendingatROPDF EXCEL REPORT FUNCTION ENDS
//ECI getcandidateListpendingatCEOPDF PDF REPORT STARTS
public function getcandidateListpendingatCEOPDF(Request $request, $state, $pc) {
//ECI getcandidateListpendingatCEOPDF PDF REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  $xss = new xssClean;
  $st_code = base64_decode($xss->clean_input($state));
  $cons_no = base64_decode($xss->clean_input($pc));
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 0;
  $cur_time = Carbon::now();
  $candidate_id=[];
  if ($st_code == '0' && $cons_no == '0') {
          // echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$pendingateciCandlist= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));

  
  } elseif ($st_code != '0' && $cons_no == '0') {
          // echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$pendingateciCandlist= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && $cons_no != '0') {
// echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$pendingateciCandlist= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
     /* $pendingateciCandlist = DB::table('expenditure_reports')
              ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
              ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->select('expenditure_reports.candidate_id')
              ->where('expenditure_reports.ST_CODE', '=', $st_code)
             ->where('expenditure_reports.constituency_no', '=', $cons_no)
              ->where('candidate_nomination_detail.application_status', '=', '6')
              ->where('candidate_nomination_detail.finalaccepted', '=', '1')
              ->where('candidate_nomination_detail.symbol_id', '<>', '200')
              ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
              ->whereNotNull('expenditure_reports.date_of_receipt_eci')
              ->where(function($query) {
	$query->whereNull('expenditure_reports.final_action');
	$query->orwhere('expenditure_reports.final_action', '=','');
  }) 
             // ->groupBy('expenditure_reports.candidate_id')
              ->get();

              $getcandidateListfinalbyECI = DB::table('expenditure_reports')
              ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
              ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              // ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
               ->where('expenditure_reports.ST_CODE', '=', $st_code)
               ->where('expenditure_reports.constituency_no', '=', $cons_no)
              ->select('expenditure_reports.candidate_id')
              ->where('candidate_nomination_detail.application_status', '=', '6')
              ->where('candidate_nomination_detail.finalaccepted', '=', '1')
              ->where('candidate_nomination_detail.symbol_id', '<>', '200')
              ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
             ->where('expenditure_reports.final_by_eci','1')
->where('expenditure_reports.finalized_status','1')
              ->where(function($q) {
                $q->where('expenditure_reports.final_action', 'Closed')
                  ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                })
              ->whereNotNull('expenditure_reports.date_of_receipt_eci')
              ->groupBy('expenditure_reports.candidate_id')
              ->get();

 $getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
              ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
              ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
               ->select('expenditure_reports.candidate_id')
               ->where('expenditure_reports.ST_CODE', '=', $st_code)
               ->where('expenditure_reports.constituency_no', '=', $cons_no)
              ->where('candidate_nomination_detail.application_status', '=', '6')
              ->where('candidate_nomination_detail.finalaccepted', '=', '1')
              ->where('candidate_nomination_detail.symbol_id', '<>', '200')
              ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
             ->where('expenditure_reports.final_by_eci','1')
->where('expenditure_reports.finalized_status','1')
->where('expenditure_reports.final_action', 'Disqualified')
              ->whereNotNull('expenditure_reports.date_of_receipt_eci')
              ->groupBy('expenditure_reports.candidate_id')
              ->get();

              foreach ($getdisqualifiedcandidateListbyECI as $getdisqualifiedcandidateListbyECIData) {
                  $candidate_id[] = $getdisqualifiedcandidateListbyECIData->candidate_id;
              }

              foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                  $candidate_id[] = $pendingateciCandlistData->candidate_id;
              }
              foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                  $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
              }
             // echo '<pre>'; print_r( $candidate_id);
              $finalbyceoCandList = DB::table('expenditure_reports')
              ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
              ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code','candidate_nomination_detail.pc_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
               ->where('expenditure_reports.ST_CODE', '=', $st_code)
               ->where('expenditure_reports.constituency_no', '=', $cons_no)
              ->where('candidate_nomination_detail.application_status', '=', '6')
              ->where('candidate_nomination_detail.finalaccepted', '=', '1')
              ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
              ->where('expenditure_reports.final_by_ro', '1')
              ->where('expenditure_reports.finalized_status', '1')
              ->whereNotNull('expenditure_reports.date_of_sending_deo')
              ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
              ->groupBy('expenditure_reports.candidate_id')
              ->get();*/
  }
  //dd($totalContestedCandidatedata);

  $pdf = PDF::loadView('admin.pc.eci.Expenditure.2014.candidatePendingatCEOPDFhtml', ['user_data' => $d, 'pendingatCEOCandList' => $finalbyceoCandList]);
  return $pdf->download('EcipendingatCEOCandidateMIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
  return view('admin.pc.eci.Expenditure.candidatePendingatCEOPDFhtml');
} else {
  return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI getcandidateListpendingatCEOPDF PDF REPORT TRY CATCH BLOCK ENDS
}

//ECI getcandidateListpendingatCEOPDF PDF REPORT FUNCTION ENDS

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 21-05-19
* @author Modified By : 
* @author Modified Date : 
* @author param return candidateListfinalbyECI By ECI fuction     
*/
public function getcandidateListpendingatECI(Request $request, $state, $pc) {
//PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  $xss = new xssClean;
  $st_code = base64_decode($xss->clean_input($state));
  $cons_no = base64_decode($xss->clean_input($pc));
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 0;
  // echo $st_code.'cons_no'.$cons_no; die;
  if ($st_code == '0' && $cons_no == '0') {
        // echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$pendingateciCandlist= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
     /* $pendingateciCandlist = DB::table('expenditure_reports')
              ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
              ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
              ->where('candidate_nomination_detail.application_status', '=', '6')
              ->where('candidate_nomination_detail.finalaccepted', '=', '1')
              ->where('candidate_nomination_detail.symbol_id', '<>', '200')
              ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
              //->where('expenditure_reports.final_by_eci','1')
              ->whereNotNull('expenditure_reports.date_of_receipt_eci')
->where(function($query) {
	$query->whereNull('expenditure_reports.final_action');
	$query->orwhere('expenditure_reports.final_action', '=','');
	  }) 
              ->groupBy('expenditure_reports.candidate_id')
              ->get();
  } elseif ($st_code != '0' && $cons_no == '0') {
      $pendingateciCandlist = DB::table('expenditure_reports')
              ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
              ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
              ->where('expenditure_reports.ST_CODE', '=', $st_code)
              ->where('candidate_nomination_detail.application_status', '=', '6')
              ->where('candidate_nomination_detail.finalaccepted', '=', '1')
              ->where('candidate_nomination_detail.symbol_id', '<>', '200')
              ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
              // ->where('expenditure_reports.final_by_eci','1')
              ->whereNotNull('expenditure_reports.date_of_receipt_eci')
               ->where(function($query) {
	$query->whereNull('expenditure_reports.final_action');
	$query->orwhere('expenditure_reports.final_action', '=','');
	  })                             
	  ->groupBy('expenditure_reports.candidate_id')
              ->get();*/
  } elseif ($st_code != '0' && $cons_no != '0') {
// echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$pendingateciCandlist= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
  }
  //dd($DataentryStartCandList);
  return view('admin.pc.eci.Expenditure.pendingateci-mis', ['user_data' => $d, 'pendingateciCandlist' => $pendingateciCandlist, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($pendingateciCandlist)]);
} else {
  return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ECI getcandidateListpendingatECI TRY CATCH ENDS HERE   
}

// end getcandidateListpendingatECI start function

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 28-05-19
* @author Modified By : 
* @author Modified Date : 
* @author param return getcandidateListpendingatECIEXL By ECI fuction     
*/
//ECI getcandidateListpendingatECIEXL EXCEL REPORT STARTS
public function getcandidateListpendingatECIEXL(Request $request, $state, $pc) {
//ECI getcandidateListpendingatECIEXL EXCEL REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  $xss = new xssClean;
  $st_code = base64_decode($xss->clean_input($state));
  $cons_no = base64_decode($xss->clean_input($pc));
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 0;
  // echo  $st_code.'pc'.$cons_no; die;
  // dd($totalContestedCandidate);

  $cur_time = Carbon::now();

  \Excel::create('ECIPendingatECICandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
      $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

if ($st_code == '0' && $cons_no == '0') {
// echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$pendingatECICandList= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
             
  } elseif ($st_code != '0' && $cons_no == '0') {
$pendingatECICandList= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && $cons_no != '0') {
$pendingatECICandList= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
          }

          $arr = array();
          $TotalUsers = 0;
          $user = Auth::user();
          $count = 1;
          foreach ($pendingatECICandList as $candDetails) {
              $st = getstatebystatecode($candDetails->st_code);
              //dd($candDetails);
              $pcDetails = getpcbypcno($candDetails->st_code, $candDetails->pc_no);
              $ecireceiveddate = new DateTime($candDetails->date_of_receipt_eci);
              //echo $date->format('d.m.Y'); // 31.07.2012
              $ecireceiveddate = $ecireceiveddate->format('d-m-Y'); // 31-07-2012
 $lastdate = new DateTime($candDetails->last_date_prescribed_acct_lodge);
//echo $date->format('d.m.Y'); // 31.07.2012
$lodgingDate = $lastdate->format('d-m-Y'); // 31-07-2012

$scrutinysubmit = new DateTime($candDetails->report_submitted_date);
$scrutinyreportsubmitdate = $scrutinysubmit->format('d-m-Y'); // 31-07-2012

if( !empty($candDetails->date_orginal_acct) && isset($candDetails->date_orginal_acct) && strtotime($candDetails->date_orginal_acct) > 0){
        $candidatelodging = new DateTime($candDetails->date_orginal_acct);
$candidatelodgingdate = $candidatelodging->format('d-m-Y'); // 31-07-2012

}else { echo 'N/A'; }


$sendingdatetoceo = new DateTime($candDetails->date_of_sending_deo);
$ceosendingdate = $sendingdatetoceo->format('d-m-Y'); // 31-07-2012

$ceoreceiveddate = new DateTime($candDetails->date_of_receipt);
$ceoreceivedate = $ceoreceiveddate->format('d-m-Y'); // 31-07-2012

// $lodgingDate =!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';

$lodgingDate =$lodgingDate ??  '16-06-2014';
$scrutinyreportsubmitdate =$scrutinyreportsubmitdate ??  'N/A';
$candidatelodgingdate =$candidatelodgingdate ??  'N/A';
$ceosendingdate =$ceosendingdate ??  'N/A';
$ceoreceivedate =$ceoreceivedate ??  'N/A';
$ecireceiveddate =$ecireceiveddate ??  'N/A';

              $data = array(
    $st->ST_NAME,
                  $pcDetails->PC_NO . '-' . $pcDetails->PC_NAME,
                  $candDetails->cand_name,
                  $candDetails->PARTYNAME,
                  $lodgingDate,
	$scrutinyreportsubmitdate,
	$candidatelodgingdate,
	$ceosendingdate,
	$ceoreceivedate,
	$ecireceiveddate
              );
              $TotalUsers = count($pendingatECICandList);
              array_push($arr, $data);
              // }
              $count++;
          }
          $totalvalues = array('Total', $TotalUsers);
          // print_r($totalvalues);die;
          array_push($arr, $totalvalues);
          $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
              'State','PC No & Name', 'Candidate Name', 'Party Name', 'Last Date Of Lodging','Date Of Scrutiny Report Submission','Date Of Lodging A/C By Candidates','Date Of Sending To CEO','Date Of Received By CEO','Date Of Received by ECI'
                  )
          );
      });
  })->export('csv');
} else {
  return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI getcandidateListpendingatECIEXL EXCEL REPORT TRY CATCH BLOCK ENDS
}

//ECI getcandidateListpendingatECIEXL EXCEL REPORT FUNCTION ENDS
//ECI getcandidateListpendingatECIPDF PDF REPORT STARTS
public function getcandidateListpendingatECIPDF(Request $request, $state, $pc) {
//ECI getcandidateListpendingatECIPDF PDF REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  $xss = new xssClean;
  $st_code = base64_decode($xss->clean_input($state));
  $cons_no = base64_decode($xss->clean_input($pc));
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 0;
  $cur_time = Carbon::now();
  if ($st_code == '0' && $cons_no == '0') {
    // echo $st_code.'---pc--->'.$cons_no; 
 // $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
 $pendingatECICandList= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
      
 } elseif ($st_code != '0' && $cons_no == '0') {
          // echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$pendingatECICandList= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && $cons_no != '0') {
          // echo $st_code.'---pc--->'.$cons_no; 
// $totalContestedCandidatedata= DB::select('call All_Exp_Candidate(?,?)',array('S02','ALL'));
$pendingatECICandList= DB::select('call All_Exp_Candidate(?,?)',array($st_code,$cons_no));
  }

  //dd($totalContestedCandidatedata);

  $pdf = PDF::loadView('admin.pc.eci.Expenditure.candidatePendingatECIPDFhtml', ['user_data' => $d, 'pendingatECICandList' => $pendingatECICandList]);
  return $pdf->download('EcipendingatECICandidateMIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
  return view('admin.pc.eci.Expenditure.candidatePendingatECIPDFhtml');
} else {
  return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
} //ECI getcandidateListpendingatECIPDF PDF REPORT TRY CATCH BLOCK ENDS
}
//ECI getcandidateListpendingatECIPDF PDF REPORT FUNCTION ENDS


/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 29-11-19
* @author Modified By : 
* @author Modified Date : 
* @author param return finalcandlistECI By ECI fuction     
*/
public function getcandidateListfinalbyECI(Request $request, $state, $pc) {
//PC ROPC getcandidateListfinalbyECI TRY CATCH STARTS HERE
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  $xss = new xssClean;
  $st_code = base64_decode($xss->clean_input($state));
  $cons_no = base64_decode($xss->clean_input($pc));
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 'ALL';
  // echo $st_code.'cons_no'.$cons_no; die;
  if ($st_code == '0' && !is_numeric($cons_no)) {
    $getcandidateListfinalbyECI= DB::select('call Final_exp_report(?,?)',array($st_code,$cons_no));
     
  } elseif ($st_code != '0' && !is_numeric($cons_no)) {
      $getcandidateListfinalbyECI= DB::select('call Final_exp_report(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && is_numeric($cons_no)) {
       $getcandidateListfinalbyECI= DB::select('call Final_exp_report(?,?)',array($st_code,$cons_no));
  }
 // dd($getcandidateListfinalbyECI);
  return view('admin.pc.eci.Expenditure.2014.finalbyeci-mis', ['user_data' => $d, 'getcandidateListfinalbyECI' => $getcandidateListfinalbyECI, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($getcandidateListfinalbyECI)]);
} else {
  return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ECI getcandidateListfinalbyECI TRY CATCH ENDS HERE   
}

// end getcandidateListfinalbyECI start function

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 18-06-19
* @author Modified By : 
* @author Modified Date : 
* @author param returngetcandidateListfinalbyECIEXL By ECI fuction     
*/
//ECI getcandidateListpendingatECIEXL EXCEL REPORT STARTS
public function getcandidateListfinalbyECIEXL(Request $request, $state, $pc) {
//ECI getcandidateListpendingatECIEXL EXCEL REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  $xss = new xssClean;
  $st_code = base64_decode($xss->clean_input($state));
  $cons_no = base64_decode($xss->clean_input($pc));
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 'ALL';
  // echo  $st_code.'pc'.$cons_no; die;
  // dd($totalContestedCandidate);

  $cur_time = Carbon::now();

  \Excel::create('ECIFinalCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
      $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

  if ($st_code == '0' && !is_numeric($cons_no)) {
    $getcandidateListfinalbyECI= DB::select('call Final_exp_report(?,?)',array($st_code,$cons_no));
     
  } elseif ($st_code != '0' && !is_numeric($cons_no)) {
      $getcandidateListfinalbyECI= DB::select('call Final_exp_report(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && is_numeric($cons_no)) {
       $getcandidateListfinalbyECI= DB::select('call Final_exp_report(?,?)',array($st_code,$cons_no));
  }

          $arr = array();
          $TotalUsers = 0;
          $user = Auth::user();
          $count = 1;
          foreach ($getcandidateListfinalbyECI as $candDetails) {
              $st = getstatebystatecode($candDetails->st_code);
              //dd($candDetails);
              $pcDetails = getpcbypcno($candDetails->st_code, $candDetails->pc_no);
             $scrutinysubmit = new DateTime($candDetails->report_submitted_date);
$scrutinyreportsubmitdate = $scrutinysubmit->format('d-m-Y'); // 31-07-2012

$date_orginal_acct = new DateTime($candDetails->created_at);
$candidatelodgingdate=$date_orginal_acct->format('d-m-Y'); // 31-07-2012
  /*$ecireceiveddate = new DateTime($candDetails->date_of_receipt_eci);
              //echo $date->format('d.m.Y'); // 31.07.2012
              $ecireceiveddate = $ecireceiveddate->format('d-m-Y'); // 31-07-2012
 $lastdate = new DateTime($candDetails->last_date_prescribed_acct_lodge);
//echo $date->format('d.m.Y'); // 31.07.2012
$lodgingDate = $lastdate->format('d-m-Y'); // 31-07-2012


//$scrutinyreportsubmitdate= date('d-m-Y',strtotime($candDetails->report_submitted_date));
$candidatelodgingdate= date('d-m-Y',strtotime($candDetails->date_orginal_acct));

$sendingdatetoceo = new DateTime($candDetails->date_of_sending_deo);
$ceosendingdate = $sendingdatetoceo->format('d-m-Y'); // 31-07-2012

$ceoreceiveddate = new DateTime($candDetails->date_of_receipt);
$ceoreceivedate = $ceoreceiveddate->format('d-m-Y'); // 31-07-2012

// $lodgingDate =!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';


$ceosendingdate =$ceosendingdate ??  'N/A';
$ceoreceivedate =$ceoreceivedate ??  'N/A';
$ecireceiveddate =$ecireceiveddate ??  'N/A';
*/
$scrutinyreportsubmitdate =$scrutinyreportsubmitdate ??  'N/A';
$candidatelodgingdate =(!empty($candidatelodgingdate) && $candidatelodgingdate > '04-12-2019') ? $candidatelodgingdate : 'N/A';
              $data = array(
                  $st->ST_NAME,
                  $pcDetails->PC_NO . '-' . $pcDetails->PC_NAME,
                  $candDetails->cand_name,
                  $candDetails->PARTYNAME,
				  '16-06-2014',
				  $scrutinyreportsubmitdate,
				  $candidatelodgingdate 
              );
              $TotalUsers = count($getcandidateListfinalbyECI);
              array_push($arr, $data);
              // }
              $count++;
          }
          $totalvalues = array('Total', $TotalUsers);
          // print_r($totalvalues);die;
          array_push($arr, $totalvalues);
          $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
              'State','PC No & Name', 'Candidate Name', 'Party Name','Last Date Of Lodging', 'Date Of Scrutiny Report Submission','Date Of Lodging A/C By Candidate')
          );
      });
  })->export('csv');
} else {
  return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI getcandidateListpendingatECIEXL EXCEL REPORT TRY CATCH BLOCK ENDS
}//ECI getcandidateListfinalclosedECIEXL EXCEL REPORT FUNCTION ENDS


/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 04-12-19
* @author Modified By : 
* @author Modified Date : 
* @author param returngetcandidateListfinalbyECIPDF By ECI fuction     
*/
//ECI getcandidateListpendingatECIPDF PDF REPORT STARTS
public function getcandidateListfinalbyECIPDF(Request $request, $state, $pc) {
//ECI getcandidateListpendingatROPDF PDF REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  $xss = new xssClean;
  $st_code = base64_decode($xss->clean_input($state));
  $cons_no = base64_decode($xss->clean_input($pc));
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 'ALL';
  $cur_time = Carbon::now();
  
   if ($st_code == '0' && !is_numeric($cons_no)) {
    $getcandidateListfinalbyECI= DB::select('call Final_exp_report(?,?)',array($st_code,$cons_no));
     
  } elseif ($st_code != '0' && !is_numeric($cons_no)) {
      $getcandidateListfinalbyECI= DB::select('call Final_exp_report(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && is_numeric($cons_no)) {
       $getcandidateListfinalbyECI= DB::select('call Final_exp_report(?,?)',array($st_code,$cons_no));
  }

          
  $pdf = PDF::loadView('admin.pc.eci.Expenditure.2014.finalbyeciPDFhtml', ['user_data' => $d, 'getcandidateListfinalbyECI' => $getcandidateListfinalbyECI]);
  return $pdf->download('Final_Close_PC2014MIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
  return view('admin.pc.eci.Expenditure.2014.finalbyeciPDFhtml');
} else {
  return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI EcifinalbyECIMISPDF PDF REPORT TRY CATCH BLOCK ENDS
}//ECI EcifinalbyECIMISPDF PDF REPORT FUNCTION ENDS
#################################End MIS Report by Niraj##############################


###############################Notice CEO & DEO 23-06-2019 Start By Niraj######################################
/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 29-11-19
* @author Modified By : 
* @author Modified Date : 
* @author param return getnoticeatCEO By ECI fuction     
*/
public function getnoticeatCEO(Request $request,$state,$pc){
//PC ECI getnoticeatCEO TRY CATCH STARTS HERE
try{
if(Auth::check()){
$user = Auth::user();
$uid=$user->id;
$d=$this->commonModel->getunewserbyuserid($user->id);
  $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
$xss = new xssClean;
$st_code=base64_decode($xss->clean_input($state));
$cons_no=base64_decode($xss->clean_input($pc));
$st_code=!empty($st_code) ? $st_code : 0;
$cons_no=!empty($cons_no) ? $cons_no : 'ALL';
// echo $st_code.'cons_no'.$cons_no; die;

if ($st_code == '0' && !is_numeric($cons_no)) {
    $noticeatCEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && !is_numeric($cons_no)) {
      $noticeatCEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && is_numeric($cons_no)) {
       $noticeatCEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  }
//dd($DataentryStartCandList);
return view('admin.pc.eci.Expenditure.2014.noticeatceo',['user_data' => $d,'noticeatCEO' => $noticeatCEO,'edetails'=>$ele_details,'st_code'=>$st_code,'cons_no'=>$cons_no,'count'=>count($noticeatCEO)]); 

}
else {
return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');

}//PC ECI candidateListByfinalizeData TRY CATCH ENDS HERE   
}   // end candidateListByfinalizeData start function

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 23-06-19
* @author Modified By : 
* @author Modified Date : 
* @author param return getnoticeatCEOEXL By ECI fuction     
*/
//ECI getnoticeatCEOEXL EXCEL REPORT STARTS
public function getnoticeatCEOEXL(Request $request,$state,$pc){  
//ECI getnoticeatCEOEXL EXCEL REPORT TRY CATCH BLOCK STARTS
try{
if(Auth::check()){
$user = Auth::user();
$uid=$user->id;
$d=$this->commonModel->getunewserbyuserid($user->id);
  $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
$xss = new xssClean;
$st_code=base64_decode($xss->clean_input($state));
$cons_no=base64_decode($xss->clean_input($pc));
$st_code=!empty($st_code) ? $st_code : 0;
$cons_no=!empty($cons_no) ? $cons_no : 'ALL';
// echo  $st_code.'pc'.$cons_no; die;
$cur_time    = Carbon::now();

\Excel::create('EciNoticeAtCEO2014_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
$excel->sheet('Sheet1', function($sheet) use($st_code,$cons_no) {

if ($st_code == '0' && !is_numeric($cons_no)) {
    $noticeatCEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && !is_numeric($cons_no)) {
      $noticeatCEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && is_numeric($cons_no)) {
       $noticeatCEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  }

$arr  = array();
$TotalUsers = 0;
$user = Auth::user();
$count = 1;
foreach ($noticeatCEO as $candDetails) {
  $st=getstatebystatecode($candDetails->ST_CODE);
  //dd($candDetails);
  $pcDetails=getpcbypcno($candDetails->ST_CODE,$candDetails->pc_NO);
  /*$date = new DateTime($candDetails->finalized_date);
  //echo $date->format('d.m.Y'); // 31.07.2012
  $lodgingDate=$date->format('d-m-Y'); // 31-07-2012*/
  $data =  array(
  $st->ST_NAME,
  $pcDetails->PC_NO.'-'.$pcDetails->PC_NAME,
  $candDetails->cand_name,
  $candDetails->PARTYNAME
      );
      $TotalUsers =count($noticeatCEO);
      array_push($arr, $data);
              // }
              $count++;
          }
  $totalvalues = array('Total',$TotalUsers);
  // print_r($totalvalues);die;
  array_push($arr,$totalvalues);
      $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                  'State','PC No & Name', 'Candidate Name', 'Party Name'
          )
      );
  });
})->export('csv');
}else {
  return redirect('/admin-login');
} 
}catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');

}
//ECI getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK ENDS

}


/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 04-12-19
* @author Modified By : 
* @author Modified Date : 
* @author param return getnoticeatCEOPDF By ECI fuction     
*/
//ECI getnoticeatCEOEXL EXCEL REPORT STARTS
public function getnoticeatCEOPDF(Request $request,$state,$pc){
//ECI getcandidateListpendingatROPDF PDF REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  $xss = new xssClean;
  $st_code = base64_decode($xss->clean_input($state));
  $cons_no = base64_decode($xss->clean_input($pc));
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 'ALL';
  $cur_time = Carbon::now();
  
   if ($st_code == '0' && !is_numeric($cons_no)) {
    $noticeatCEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && !is_numeric($cons_no)) {
      $noticeatCEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && is_numeric($cons_no)) {
       $noticeatCEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  }

          
  $pdf = PDF::loadView('admin.pc.eci.Expenditure.2014.noticeatCEOPDFhtml', ['user_data' => $d, 'noticeatCEO' => $noticeatCEO]);
  return $pdf->download('NoticeatCEOPC2014MIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
  return view('admin.pc.eci.Expenditure.2014.noticeatCEOPDFhtml');
} else {
  return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI EcifinalbyDEOMISPDF PDF REPORT TRY CATCH BLOCK ENDS
}

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 29-11-19
* @author Modified By : 
* @author Modified Date : 
* @author param return getnoticeatDEO By ECI fuction     
*/
public function getnoticeatDEO(Request $request,$state,$pc){
//PC ECI getcandidateListpendingatCEO TRY CATCH STARTS HERE
try{
if(Auth::check()){
$user = Auth::user();
$uid=$user->id;
$d=$this->commonModel->getunewserbyuserid($user->id);
  $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
$xss = new xssClean;
$st_code=base64_decode($xss->clean_input($state));
$cons_no=base64_decode($xss->clean_input($pc));
$st_code=!empty($st_code) ? $st_code : 0;
$cons_no=!empty($cons_no) ? $cons_no : 'ALL';
// echo $st_code.'cons_no'.$cons_no; die;
 if ($st_code == '0' && !is_numeric($cons_no)) {
    $noticeatDEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && !is_numeric($cons_no)) {
      $noticeatDEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && is_numeric($cons_no)) {
       $noticeatDEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  }


//dd($DataentryStartCandList);
return view('admin.pc.eci.Expenditure.2014.noticeatdeo',['user_data' => $d,'noticeatDEO' => $noticeatDEO,'edetails'=>$ele_details,'st_code'=>$st_code,'cons_no'=>$cons_no,'count'=>count($noticeatDEO)]); 

}
else {
return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');

}//PC ECI candidateListByfinalizeData TRY CATCH ENDS HERE   
}   // end candidateListByfinalizeData start function

/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 29-11-19
* @author Modified By : 
* @author Modified Date : 
* @author param return getnoticeatDEOEXL By ECI fuction     
*/
//ECI getnoticeatDEOEXL EXCEL REPORT STARTS
public function getnoticeatDEOEXL(Request $request,$state,$pc){  
//ECI getnoticeatDEOEXL EXCEL REPORT TRY CATCH BLOCK STARTS
try{
if(Auth::check()){
$user = Auth::user();
$uid=$user->id;
$d=$this->commonModel->getunewserbyuserid($user->id);
$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
$xss = new xssClean;
$st_code=base64_decode($xss->clean_input($state));
$cons_no=base64_decode($xss->clean_input($pc));
$st_code=!empty($st_code) ? $st_code : 0;
$cons_no=!empty($cons_no) ? $cons_no : 'ALL';
// echo  $st_code.'pc'.$cons_no; die;
$cur_time    = Carbon::now();

\Excel::create('ECINoticeatDEOCandidate2014_'.'_'.$cur_time, function($excel) use($st_code,$cons_no) { 
$excel->sheet('Sheet1', function($sheet) use($st_code,$cons_no) {

if ($st_code == '0' && !is_numeric($cons_no)) {
    $noticeatDEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && !is_numeric($cons_no)) {
      $noticeatDEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && is_numeric($cons_no)) {
       $noticeatDEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  }

$arr  = array();
$TotalUsers = 0;
$user = Auth::user();
$count = 1;
foreach ($noticeatDEO as $candDetails) {
  $st=getstatebystatecode($candDetails->ST_CODE);
  //dd($candDetails);
  $pcDetails=getpcbypcno($candDetails->ST_CODE,$candDetails->pc_NO);
 /*
$lastdate = new DateTime($candDetails->last_date_prescribed_acct_lodge);
//echo $date->format('d.m.Y'); // 31.07.2012
$lodgingDate = $lastdate->format('d-m-Y'); // 31-07-2012

$scrutinysubmit = new DateTime($candDetails->report_submitted_date);
$scrutinyreportsubmitdate = $scrutinysubmit->format('d-m-Y'); // 31-07-2012
//$scrutinyreportsubmitdate= date('d-m-Y',strtotime($candDetails->report_submitted_date));
$candidatelodgingdate= date('d-m-Y',strtotime($candDetails->date_orginal_acct));

$sendingdatetoceo = new DateTime($candDetails->date_of_sending_deo);
$ceosendingdate = $sendingdatetoceo->format('d-m-Y'); // 31-07-2012

$ceoreceiveddate = new DateTime($candDetails->date_of_receipt);
$ceoreceivedate = $ceoreceiveddate->format('d-m-Y'); // 31-07-2012
// $lodgingDate =!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';

$lodgingDate =$lodgingDate ??  '22-06-2019';
$scrutinyreportsubmitdate =$scrutinyreportsubmitdate ??  'N/A';
$candidatelodgingdate =$candidatelodgingdate ??  'N/A';
$ceosendingdate =$ceosendingdate ??  'N/A';
$ceoreceivedate =$ceoreceivedate ??  'N/A';
*/
  $data =  array(
   $st->ST_NAME,
  $pcDetails->PC_NO.'-'.$pcDetails->PC_NAME,
  $candDetails->cand_name,
  $candDetails->PARTYNAME
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
                 'State','PC No & Name', 'Candidate Name', 'Party Name'
          )
      );
  });
})->export('csv');
}else {
  return redirect('/admin-login');
} 
}catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');

}
//ECI getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK ENDS

}


/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 04-12-19
* @author Modified By : 
* @author Modified Date : 
* @author param return getnoticeatDEOPDF By ECI fuction     
*/
//ECI getnoticeatDEOPDF REPORT STARTS
public function getnoticeatDEOPDF(Request $request,$state,$pc){
//ECI getcandidateListpendingatROPDF PDF REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  $xss = new xssClean;
  $st_code = base64_decode($xss->clean_input($state));
  $cons_no = base64_decode($xss->clean_input($pc));
  $st_code = !empty($st_code) ? $st_code : 0;
  $cons_no = !empty($cons_no) ? $cons_no : 'ALL';
  $cur_time = Carbon::now();
   if ($st_code == '0' && !is_numeric($cons_no)) {
    $noticeatDEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && !is_numeric($cons_no)) {
      $noticeatDEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  } elseif ($st_code != '0' && is_numeric($cons_no)) {
       $noticeatDEO= DB::select('call Cand_Exp_Notice(?,?)',array($st_code,$cons_no));
  }
          
  $pdf = PDF::loadView('admin.pc.eci.Expenditure.2014.noticeatDEOPDFhtml', ['user_data' => $d, 'noticeatDEO' => $noticeatDEO]);
  return $pdf->download('NoticeatDEOPC2014MIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
  return view('admin.pc.eci.Expenditure.2014.noticeatDEOPDFhtml');
} else {
  return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI EcifinalbyDEOMISPDF PDF REPORT TRY CATCH BLOCK ENDS
}

###############################End Notice CEO & DEO ###########################################################

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

/////by manish
public function getscrutinyreport(Request $request) {
$htmlData = '';
////get scrutiny report data ///////
$candidate_id = $_GET['candidate_id'];
$scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidate_id);
$expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidate_id);
$expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidate_id);
$expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidate_id);

if (!empty($scrutinyReportData)) {
return view('admin.pc.eci.Expenditure.GetScrutinyReport', compact('expensesourecefundbyitem', 'scrutinyReportData', 'expenseunderstated', 'expenseunderstatedbyitem'));
} else {

}
}

public function saveComment(Request $request) {
$request = (array) $request->all();
$comment_by_ceo = !empty($request['comment']) ? $request['comment'] : "";
if (!empty($request)) {
$insertComment = $this->commonModel->updatedata('expenditure_reports', 'candidate_id', $request['candidate_id'], array("comment_by_eci" => $comment_by_ceo));
if ($insertComment) {
  return 1;
} else {
  return 0;
}
}
}

public function confirmReport() {
$candidate_id = !empty($_GET['candidate_id']) ? $_GET['candidate_id'] : "";
$insertComment = $this->commonModel->updatedata('expenditure_reports', 'candidate_id', $candidate_id, array("final_by_eci" => '1'));
$update = $this->commonModel->updatedata('expenditure_notification', 'candidate_id', $candidate_id, array("eci_action" => '1'));

if ($insertComment) {
return 1;
} else {
return 0;
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

// start manoj here
public function getprofile(Request $request) {
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
  return view('admin.expenditure.GetProfile', compact('profileData'));
} else {
  return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
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
$ELECTION_TYPE = !empty($ReportSingleData['election_type']) ? $ReportSingleData['election_type'] : '';
$party_id = !empty($pcdetail->party_id) ? $pcdetail->party_id : 0;
$partyname = getpartybyid($party_id);
$partyname = !empty($partyname) ? $partyname->PARTYNAME : '';

$ELECTION_ID = !empty($pcdetail->election_id) ? $pcdetail->election_id : 0;

// echo $pcNO, $ELECTION_ID, $st_code;die;
$winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $st_code)->where('pc_no', $pcNo)->where('election_id', $ELECTION_ID)->first();



///////////////////////

$mpdf = new \Mpdf\Mpdf();

$candiatePcName = getpcbypcno($st_code, $pcNo);
$candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '';
$districtDetails = getdistrictbydistrictno($st_code, $district_no);




$date = date('d-m-Y');
$profileData = DB::table('candidate_nomination_detail')
      ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
      ->join("m_election_details", function($join) {
          $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
          ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
      })
       ->where('candidate_nomination_detail.application_status', '=', '6')
      ->where('candidate_nomination_detail.party_id', '<>', '1180')
      ->where('candidate_personal_detail.cand_name', '<>', 'NOTA')
       ->where('candidate_nomination_detail.finalaccepted', '=', '1')
      ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
      ->where('m_election_details.CONST_TYPE', '=', 'PC')
      ->get();
// get CEO status cand_name ELECTION_TYPE
      
$candidateName = !empty($profileData[0]) ? $profileData[0]->cand_name : '';
$electionType = !empty($profileData[0]) ? $profileData[0]->ELECTION_TYPE : '';
$party_id = !empty($profileData[0]) ? $profileData[0]->party_id : '';
$partyname = getpartybyid($party_id);
$partyname = !empty($partyname) ? $partyname->PARTYNAME : '';




$date = date('d-m-Y');
$year = date('Y');
$title = $date . '_' . "Election Commission of India";

$mpdf->setHeader($candidateName . ' | ' . $electionType . ' ' . $year . ' | ' . $partyname);

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
      ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
      ->join("m_election_details", function($join) {
          $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
          ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
      })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
      ->join('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')

///
      ->leftjoin('expenditure_fund_parties', 'expenditure_fund_parties.candidate_id', '=', 'candidate_nomination_detail.candidate_id')

      ->leftjoin('expenditure_understates', 'expenditure_fund_parties.candidate_id', '=', 'candidate_nomination_detail.candidate_id')

        ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'expenditure_reports.ST_CODE')
         

         ->join("m_pc", function($join) {
          $join->on("m_pc.PC_NO", "=", "expenditure_reports.constituency_no")
          ->on("m_pc.ST_CODE", "=", "expenditure_reports.st_code");
      })

////

      
      ->where('candidate_nomination_detail.st_code', $st_code)
      ->where('candidate_nomination_detail.pc_no', $pcNo)
      ->where('candidate_nomination_detail.application_status', '=', '6')
      ->where('candidate_nomination_detail.party_id', '<>', '1180')
      ->where('candidate_personal_detail.cand_name', '<>', 'NOTA')
       ->where('candidate_personal_detail.cand_name', '<>', 'NOTA')
      ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
      ->where('m_election_details.CONST_TYPE', '=', 'PC')
      ->get();


      $scrutiny_data=DB::table('expenditure_reports')->select('expenditure_reports.updated_at')
      ->where('expenditure_reports.candidate_id', '=', $candidate_id)

      ->first();
    
      $submitedData=!empty( $scrutiny_data)? $scrutiny_data->updated_at:0;

$expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidate_id);
$expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidate_id);
$expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidate_id);
//dd($scrutinyReportData);
$download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : 'N/A';
$download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : 'N/A';
$download_link3 = !empty($scrutinyReportData[0]->noticefile) ?  $scrutinyReportData[0]->noticefile : 'N/A';
$download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : '';

$pdf = view('admin.expenditure.pdf_ro', compact('expensesourecefundbyitem', 'scrutinyReportData', 'districtDetails', 'profileData', 'expenseunderstated', 'expenseunderstatedbyitem', 'submitedData','electionType','download_link1','electionType' ,
      'download_link2', 'download_link3','download_link4', 'winn_data','partyname'));
$mpdf->WriteHTML($pdf);
$mpdf->Output();

} else {
return redirect('/officer-login');
}


}

//end manoj


public function MasterDataListing(Request $request) {
$users = Session::get('admin_login_details');
$user = Auth::user();
if (session()->has('admin_login')) {
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($uid);
$list_record = $this->ECIModel->getallelectionphasewise();
$list_state = $this->ECIModel->listcurrentelectionstate();
$list_phase = $this->ECIModel->listcurrentelectionphase();
$list_electionid = $this->ECIModel->getallelectionbyid();
$list = $this->ECIModel->listelectiontype();
$MasterData = $this->expenditureModel->GetMasterEntry();
$module = $this->commonModel->getallmodule();
return view('admin.pc.eci.Expenditure.MasterDataListing', ['user_data' => $d, 'module' => $module, 'list_record' => $list_record, 'list_state' => $list_state, 'list_phase' => $list_phase, 'list_electionid' => $list_electionid, 'list' => $list, "MasterData" => $MasterData]);
} else {
return redirect('/admin-login');
}
}

public function masterEntry(Request $request) {
$users = Session::get('admin_login_details');
$user = Auth::user();
if (session()->has('admin_login')) {
$uid = $user->id;
$MID = base64_decode(!empty($_GET['id']) ? $_GET['id'] : "");
$d = $this->commonModel->getunewserbyuserid($uid);
$list_record = $this->ECIModel->getallelectionphasewise();
$list_state = $this->ECIModel->listcurrentelectionstate();
$list_phase = $this->ECIModel->listcurrentelectionphase();
$list_electionid = $this->ECIModel->getallelectionbyid();
$list = $this->ECIModel->listelectiontype();
$singleMaster = $this->commonModel->selectone('expenditure_master_entry', 'id', $MID);

$module = $this->commonModel->getallmodule();
return view('admin.pc.eci.Expenditure.entryform', ['user_data' => $d, 'module' => $module, 'list_record' => $list_record, 'list_state' => $list_state, 'list_phase' => $list_phase, 'list_electionid' => $list_electionid, 'list' => $list, "singleMaster" => $singleMaster]);
} else {
return redirect('/admin-login');
}
}

public function storeMasterEntry(Request $request) {
$request = (array) $request->all();
$users = Session::get('admin_login_details');
$user = Auth::user();
$uid = $user->id;
$role_id = $user->role_id;
$master_id = !empty($request['master_id']) ? $request['master_id'] : "";
$namePrefix = \Route::current()->action['prefix'];
unset($request['_token']);

$st_code = $request['st_code'];

$GetMasterEntry = DB::select("select id from expenditure_master_entry where id='$master_id' and st_code='$st_code'");
if (empty($GetMasterEntry)) {
$GetMasterEntrys = DB::select("select id from expenditure_master_entry where st_code='$st_code'");

if (!empty($GetMasterEntrys)) {
  Session::put('message', "You have already added record from this state");
  return redirect($namePrefix . '/masterEntry?id=' . base64_encode($master_id));
}
}

try {
$datas = [];

$data_arr = array();
foreach ($request as $key => $req_data) {
  $xss = new xssClean;
  $data_arr[$key] = $xss->clean_input($req_data);
}

// print_r($request);die;

if (empty($request['master_id'])) {
  unset($request['master_id']);
  $dataInserted = $this->commonModel->insertData('expenditure_master_entry', $request);
} else {

  //  echo $dataInserted = $this->commonModel->updatedata('expenditure_master_entry','id',$master_id,$request); 
  $dataInserted = DB::table('expenditure_master_entry')->where('id', $master_id)->update(array('result_declaration_date' => $request['result_declaration_date'], "type_of_election" => $request['type_of_election'], "st_code" => trim($request['st_code']), "ceiling_amt" => $request['ceiling_amt'], "lodged_date" => $request['lodged_date']));
}


if ($dataInserted) {
  Session::put('message', "Record Add successfully.");
  return redirect($namePrefix . '/MasterDataListing');
} else {
  Session::put('message', " Internal Server Error");
  return redirect($namePrefix . '/masterEntry?id=' . base64_encode($master_id));
}
} catch (\Exception $e) {

Session::put('message', "Internal Server Error");
return redirect($namePrefix . '/masterEntry?id=' . base64_encode($master_id));
}
}

/////manish
public function getElectedCandidate($candidate_id){
$pcdetail = DB::table('candidate_nomination_detail')->where('candidate_nomination_detail.candidate_id', $candidate_id)
      ->where('candidate_nomination_detail.application_status', '=', '6')
      ->where('candidate_nomination_detail.party_id', '<>', '1180')
      ->where('candidate_nomination_detail.finalaccepted', '=', '1')
      ->first();        
$pcNo = !empty($pcdetail->pc_no) ? $pcdetail->pc_no : 0;
$st_code = !empty($pcdetail->st_code) ? $pcdetail->st_code : 0;          
$ELECTION_ID = !empty($pcdetail->election_id) ? $pcdetail->election_id : 0;
$countElectedCandidate=DB::table('winning_leading_candidate')->where('st_code', $st_code)
                ->where('pc_no', $pcNo)
                ->where('election_id', $ELECTION_ID)
                ->where('candidate_id', $candidate_id)
                ->count();
return $countElectedCandidate;
}
public function editExpenditureReport(Request $request) {
if (Auth::check()) {
$request = (array) $request->all();
$user = Auth::user();
$uid = $user->id;
// add 24/10/2019 manoj
$resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
// end 24/10/2019 manoj 

$namePrefix = \Route::current()->action['prefix'];
$candidate_id = !empty($_GET['candidate_id']) ? $_GET['candidate_id'] : "";
$candidate_id = base64_decode($candidate_id);

$candidate_data = $this->expenditureModel->getunewserbyuserid_uid_ceo($candidate_id);

$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();
$nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray();
$current_status = DB::table('expenditure_mis_current_sataus')->get()->toArray();

try {

  $ReportSingleData = $this->expenditureModel->GetExpeditureSingleData($candidate_id);
  if (!empty($ReportSingleData)) {

      $ReportSingleData = (array) $ReportSingleData[0];
  } else {
      $ReportSingleData = array();
  }
   $countElectedCandidate=$this->getElectedCandidate($candidate_id);
  return view('admin.expenditure.createmisexpensereport', ['user_data' => $d, 'ele_details' => $ele_details, "cand_finalize_ro" => array(), "electionType" => $electionType, "ReportSingleData" => $ReportSingleData, "nature_of_default_ac" => $nature_of_default_ac, "current_status" => $current_status, "candidate_data" => (array) $candidate_data,'countElectedCandidate'=>$countElectedCandidate ,'resultDeclarationDate'=>$resultDeclarationDate]);
} catch (\Exception $e) {
  return $e->getMessage();
}
} else {
return redirect('/officer-login');
}
}

public function StoreMisExpenseReport(Request $request) {
$request = (array) $request->all();
$users = Session::get('admin_login_details');
$user = Auth::user();
$uid = $user->id;
$role_id = $user->role_id;
//$report_id = $request['report_id'];
$candidate_id = $request['candidate_id'];
$request['user_id'] = $uid;
$final_action = $request['final_action'];
$notice_send_to = $request['notice_send_to'];
$comment_by_eci  = $request['comment_by_eci'];
$date_of_receipt_eci = $request['date_of_receipt_eci'];
$namePrefix = \Route::current()->action['prefix'];
unset($request['_token']);
try {
$data_arr = array();
foreach ($request as $key => $req_data) {
  $xss = new xssClean;
  $data_arr[$key] = $xss->clean_input($req_data);
}



$unsetItems = ['candidate_id', 'constituency_no', 'constituency_nos', 'contensting_candiate',
  'date_of_declaration', 'user_id', 'notice_send_to'];
$dataUpdate = array_diff_key($data_arr, array_flip($unsetItems));

//date_of_sending_deo

$updateStatus = DB::table('expenditure_reports')->where('candidate_id', $candidate_id)->update($dataUpdate);
//dd($updateStatus);
###############ECI NOTICE FINAL#########################
//echo $final_action.'notice_send_to'.$notice_send_to;
if ($final_action == 'Closed' || $final_action == 'Disqualified' || $final_action == 'Case Dropped') {
  $finalbyeci = DB::table('expenditure_reports')->where('candidate_id', $candidate_id)->update(['final_by_eci' => '1','final_by_ceo' => '1','final_by_ro' => '1']);
  Session::put('message', "Saved successfully");
  return redirect($namePrefix . '/eciallscrutiny');
 } elseif ($final_action == 'Notice Issued' || $final_action == 'Reply Issued' || $final_action == 'Hearing Done') {


   ////////////////////////////////// add entry in expenditure action logs/////////////////

 $cdate = date('Y-m-d h:i:s');
 $data_action=array("candidate_id"=>$candidate_id,"deo_action"=>$final_action,"ceo_action"=>$final_action,"eci_action"=>$final_action,"eci_action_date"=>$cdate,"eci_comment"=>$comment_by_eci,"created_by"=>$uid,"eci_action_sending_date"=>$cdate,"eci_action_receive_date"=>$date_of_receipt_eci);

 $data_arr_action = array();
  foreach ($data_action as $key => $req_data_action) {
      $xss = new xssClean;
      $data_arr_action[$key] = $xss->clean_input($req_data_action);
  }

 $check_exits_log = DB::table('expenditure_action_logs')->where('eci_action','!=',"")->where('candidate_id',$candidate_id)->first();
 if(!empty($check_exits_log) && is_array($check_exits_log) && count($check_exits_log)>0){
     $data_actionInserted = $this->commonModel->updatedata('expenditure_action_logs', 'candidate_id', $candidate_id, $data_arr_action);

  }
  else{
   $data_actionInserted = $this->commonModel->insertData('expenditure_action_logs', $data_arr_action);
  }
///////////////////////////////////////// end entry in expenditure logs///////////////////


  if ($notice_send_to == 'ceo') {
      $pendencybyceo = DB::table('expenditure_reports')->where('candidate_id', $candidate_id)->update(['final_by_ceo' => '0','final_by_ro' => '0']);
  }
}
// dd(DB::getQueryLog());
################ECI NOTICE ENDS########################
// dd($updateStatus);
if ($updateStatus > 0) {
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

public function GetProfileECI(Request $request) {
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

public function getcandidateList(request $request) {
//dd($request->all());
DB::enableQueryLog();
if (Auth::check()) {
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

$conditions="";
if(!empty($_GET['state'])){
$st_code = $_GET['state'];
$conditions .=" and candidate_nomination_detail.st_code='$st_code' ";
}

if(!empty($_GET['pc'])){
$pc = $_GET['pc'];
$conditions .=" and candidate_nomination_detail.pc_no='$pc' ";
}  


#########################Code For State Wise Access By Niraj date 23-07-2019#####################
$username=$user->officername;
$st_code = $request->input('state');
$zonestate = $this->eciexpenditureModel->getzonestate($username);
if($zonestate->isEmpty()){
  $permitstates = '';
}else{
  $permitstates = explode(',',$zonestate[0]->assign_state);
}

$permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;

  if(!empty($permitstate)){
      $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
  }else{
     $statelist = getallstate();
  }
  if(!empty($st_code)){
      $st_code=$st_code;
  }elseif(empty($st_code) && !empty($permitstate)){
      $st_code=array_values($permitstate)[0];
  }else {
      $st_code=0;
  }
 
#########################Code For State Wise Access#####################

if(!empty($conditions)){
	 $candList = DB::select("select `candidate_nomination_detail`.*, `candidate_personal_detail`.*, `m_election_details`.*, `expenditure_reports`.`finalized_status`, `expenditure_reports`.`updated_at` as `finalized_date`, `expenditure_reports`.`final_by_ro`, `expenditure_reports`.`date_of_declaration` from `candidate_nomination_detail` left join `candidate_personal_detail` on `candidate_nomination_detail`.`candidate_id` = `candidate_personal_detail`.`candidate_id` inner join `m_election_details` on `m_election_details`.`st_code` = `candidate_nomination_detail`.`st_code` and `m_election_details`.`CONST_NO` = `candidate_nomination_detail`.`pc_no` left join `expenditure_reports` on `expenditure_reports`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` where `candidate_nomination_detail`.`application_status` = '6' and `candidate_nomination_detail`.`party_id` <> 1180 and `candidate_nomination_detail`.`finalaccepted` = '1' and `m_election_details`.`CONST_TYPE` = 'PC' and `expenditure_reports`.`finalized_status` = '1' and expenditure_reports.final_by_eci='0' $conditions");
}
else{ 
$candList = DB::table('candidate_nomination_detail')
      ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
      ->join("m_election_details", function($join) {
          $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
          ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
      })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
      ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'm_election_details.*', 'expenditure_reports.finalized_status', 'expenditure_reports.updated_at as finalized_date', 'expenditure_reports.final_by_ro', 'expenditure_reports.date_of_declaration')
       ->where('candidate_nomination_detail.application_status', '=', '6')
      ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
      ->where('m_election_details.CONST_TYPE', '=', 'PC')
      ->where('expenditure_reports.finalized_status', '=', '1')
      ->where('expenditure_reports.final_by_eci', '=', '0')
      ->where('expenditure_reports.st_code', '=', $state)
      ->get();
 }
// dd(DB::getQueryLog());
// dd($candList);
return view('admin.pc.eci.Expenditure.FinalizedcandidateList', ['statelist' => $statelist,'st_code' => $state,'user_data' => $d, 'ele_details' => $ele_details, 'candList' => $candList]);
} else {
return redirect('/officer-login');
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

public function updateStatusReport(Request $request) {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;

$candidateId = $_GET['candidate_id'];
$reason = $_GET['reason'];

$data_definalization = array('candidate_id'=>$candidateId,'created_by'=>$uid,'updated_by'=>$uid,'comment'=>$reason,"count_by_eci"=>'1','log_type'=>'DEFINALIZATION','officer_level'=>'ECI');

  if ($candidateId) {
$updateStatus = $this->commonModel->updatedata('expenditure_reports', 'candidate_id', $candidateId, array("finalized_status" => "0","final_by_ro"=>'0'));
          $insertLog = $this->commonModel->insertData('expenditure_logs', $data_definalization);

if ($updateStatus) {
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

$scrutiny_data=DB::table('expenditure_reports')->select('expenditure_reports.noticefile')
      ->where('expenditure_reports.candidate_id', '=', $candidateId)->first();

$download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : '';
$download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : '';
$download_link3=!empty($scrutiny_data->noticefile)? $scrutiny_data->noticefile:'';
$download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : '';




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
public function getReturn(Request $request,$state, $pc) {

try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);               

  $xss = new xssClean;
  $st_code=base64_decode($xss->clean_input($state));
  $cons_no=base64_decode($xss->clean_input($pc));
   $st_code=!empty($st_code) ? $st_code : 0;
   $cons_no=!empty($cons_no) ? $cons_no : 0; 
  
  
   #########################Code For State Wise Access By Niraj date 23-07-2019#####################
$username=$user->officername;

$zonestate = $this->eciexpenditureModel->getzonestate($username);
if($zonestate->isEmpty()){
  $permitstates = '';
}else{
  $permitstates = explode(',',$zonestate[0]->assign_state);
}

$permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;

  if(!empty($permitstate)){
      $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
  }else{
     $statelist = getallstate();
  }
  if(!empty($st_code)){
      $st_code=$st_code;
  }elseif(empty($st_code) && !empty($permitstate)){
      $st_code=array_values($permitstate)[0];
  }else {
      $st_code=0;
  }
 
#########################Code For State Wise Access#####################

  if (!empty($st_code) && empty($cons_no)) {
        $returnCandList = DB::table('expenditure_reports')
              ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
              ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->where('expenditure_reports.ST_CODE', '=', $st_code)                        
               
              ->where('candidate_nomination_detail.application_status', '=', '6')
              ->where('candidate_nomination_detail.finalaccepted', '=', '1')
              ->where('candidate_nomination_detail.symbol_id', '<>', '200')
              ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
              ->where('expenditure_reports.return_status', '=', 'Returned')	
              ->where('expenditure_reports.finalized_status', '=', '1')
              ->where('expenditure_reports.final_by_ro', '=', '1')
              ->groupBy('expenditure_reports.candidate_id')
              ->get();
       
       
  } else if (!empty($st_code) && !empty($cons_no)) {              
       $returnCandList = DB::table('expenditure_reports')
              ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
              ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
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
  } else {             

         $returnCandList = DB::table('expenditure_reports')
              ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
              ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')

              ->where('candidate_nomination_detail.application_status', '=', '6')
              ->where('candidate_nomination_detail.finalaccepted', '=', '1')
              ->where('candidate_nomination_detail.symbol_id', '<>', '200')
              ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
              ->where('expenditure_reports.return_status', '=', 'Returned')	
              ->where('expenditure_reports.finalized_status', '=', '1')
              ->where('expenditure_reports.final_by_ro', '=', '1')
              ->groupBy('expenditure_reports.candidate_id')
              ->get();
      
  }                

      $count=!empty($returnCandList)?count($returnCandList):0;

  
  return view('admin.pc.eci.Expenditure.return-report', ['user_data' => $d, 'returnCandList' => $returnCandList ,
      'edetails' => $ele_details, "count" => $count,
      'st_code'=>$st_code,
      'cons_no'=>$cons_no
          ]);
} else {
  return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ROPC candidateListByfiledData TRY CATCH ENDS HERE   
}
public function getNonReturn(Request $request,$state, $pc) {

try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);               
  $xss = new xssClean;
  $st_code=base64_decode($xss->clean_input($state));
  $cons_no=base64_decode($xss->clean_input($pc));
  $st_code=!empty($st_code) ? $st_code : 0;
  $cons_no=!empty($cons_no) ? $cons_no : 0;               
   #########################Code For State Wise Access By Niraj date 23-07-2019#####################
  $username=$user->officername;

$zonestate = $this->eciexpenditureModel->getzonestate($username);
if($zonestate->isEmpty()){
  $permitstates = '';
}else{
  $permitstates = explode(',',$zonestate[0]->assign_state);
}

$permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;

  if(!empty($permitstate)){
      $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
  }else{
     $statelist = getallstate();
  }
  if(!empty($st_code)){
      $st_code=$st_code;
  }elseif(empty($st_code) && !empty($permitstate)){
      $st_code=array_values($permitstate)[0];
  }else {
      $st_code=0;
  }
   if (!empty($st_code) && empty($cons_no)) {
         $nonreturnCandList = DB::table('expenditure_reports')
              ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
              ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->where('expenditure_reports.ST_CODE', '=', $st_code)                        
               
              ->where('candidate_nomination_detail.application_status', '=', '6')
              ->where('candidate_nomination_detail.finalaccepted', '=', '1')
              ->where('candidate_nomination_detail.symbol_id', '<>', '200')
              ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
              ->where('expenditure_reports.return_status', '=', 'Non-Returned')	
              ->where('expenditure_reports.finalized_status', '=', '1')
              ->where('expenditure_reports.final_by_ro', '=', '1')
              ->groupBy('expenditure_reports.candidate_id')
              ->get();
  } else if (!empty($st_code) && !empty($cons_no)) {

       $nonreturnCandList = DB::table('expenditure_reports')
              ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
              ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
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
  } else {             
      
          $nonreturnCandList = DB::table('expenditure_reports')
              ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
              ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
              ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')

              ->where('candidate_nomination_detail.application_status', '=', '6')
              ->where('candidate_nomination_detail.finalaccepted', '=', '1')
              ->where('candidate_nomination_detail.symbol_id', '<>', '200')
              ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
              ->where('expenditure_reports.return_status', '=', 'Non-Returned')	
              ->where('expenditure_reports.finalized_status', '=', '1')
              ->where('expenditure_reports.final_by_ro', '=', '1')
              ->groupBy('expenditure_reports.candidate_id')
              ->get();
      
  }

      $count=!empty($nonreturnCandList)?count($nonreturnCandList):0;
  
  return view('admin.pc.eci.Expenditure.non-return-report', ['user_data' => $d, 'nonreturnCandList' => $nonreturnCandList ,
      'edetails' => $ele_details, "count" => $count,
       'st_code'=>$st_code,
      'cons_no'=>$cons_no
      ]);
} else {
  return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ROPC candidateListByfiledData TRY CATCH ENDS HERE   
}

public function candidate_wise_expenditure(Request $request)
{

// DB::enableQueryLog();
if (Auth::check()) {
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$cur_time = Carbon::now();
$conditions="";
if(!empty($_GET['state'])){
$st_code = $_GET['state'];
$conditions .=" and cnd.st_code='$st_code' ";
}

if(!empty($_GET['pc'])){
$pc = $_GET['pc'];
$conditions .=" and cnd.pc_no='$pc' ";
}  

#########################Code For State Wise Access By Niraj date 23-07-2019#####################
$username=$user->officername;
$st_code = $request->input('state');
$zonestate = $this->eciexpenditureModel->getzonestate($username);
if($zonestate->isEmpty()){
  $permitstates = '';
}else{
  $permitstates = explode(',',$zonestate[0]->assign_state);
}

$permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;

  if(!empty($permitstate)){
      $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
  }else{
     $statelist = getallstate();
  }
  if(!empty($st_code)){
      $st_code=$st_code;
  }elseif(empty($st_code) && !empty($permitstate)){
      $st_code=array_values($permitstate)[0];
  }else {
      $st_code=0;
  }
 
#########################Code For State Wise Access#####################

if(!empty($conditions)){
// $candList = DB::select("select m_election_details.YEAR,m_election_details.ELECTION_TYPE,candidate_personal_detail.cand_hname,candidate_nomination_detail.pc_no,candidate_nomination_detail.st_code,candidate_nomination_detail.district_no,candidate_nomination_detail.party_id,candidate_personal_detail.cand_name,candidate_personal_detail.candidate_id, `expenditure_reports`.`finalized_status`, `expenditure_reports`.`updated_at` as `finalized_date`, `expenditure_reports`.`final_by_ro`, `expenditure_reports`.`date_of_declaration` from `candidate_nomination_detail` left join `candidate_personal_detail` on `candidate_nomination_detail`.`candidate_id` = `candidate_personal_detail`.`candidate_id` inner join `m_election_details` on `m_election_details`.`st_code` = `candidate_nomination_detail`.`st_code` and `m_election_details`.`CONST_NO` = `candidate_nomination_detail`.`pc_no` left join `expenditure_reports` on `expenditure_reports`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` where `candidate_nomination_detail`.`application_status` = 6 and `candidate_nomination_detail`.`party_id` <> 1180 and `candidate_nomination_detail`.`finalaccepted` = '1' and `m_election_details`.`CONST_TYPE` = 'PC' and expenditure_reports.date_of_declaration !='' $conditions order by candidate_personal_detail.cand_name desc");
$candList = DB::select("select TEMP.YEAR,ELECTION_TYPE,cpd.cand_hname,TEMP.pc_no,TEMP.st_code,TEMP.district_no,
TEMP.party_id,cpd.cand_name,cpd.candidate_id,TEMP.finalized_status,TEMP.finalized_date,TEMP.final_by_ro,
TEMP.date_of_declaration
from(
select med.YEAR,med.ELECTION_TYPE,cnd.pc_no,
cnd.st_code,cnd.district_no,cnd.candidate_id,
cnd.party_id,er.finalized_status,
er.updated_at as finalized_date, er.final_by_ro,
er.date_of_declaration
from candidate_nomination_detail cnd,
m_election_details med ,expenditure_reports er
where cnd.application_status = 6
and cnd.party_id <> 1180
and cnd.finalaccepted= 1 $conditions
and med.CONST_TYPE = 'PC'
and er.date_of_declaration !=''
AND med.st_code = cnd.st_code
and med.CONST_NO = cnd.pc_no
and er.candidate_id =cnd.candidate_id
)TEMP left join candidate_personal_detail cpd on TEMP.candidate_id = cpd.candidate_id
group by TEMP.candidate_id
order by cpd.cand_name desc");

}
else{ 

/*  $candList = DB::table('candidate_nomination_detail')
      ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
      ->join("m_election_details", function($join) {
          $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
          ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
      })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
      ->select('m_election_details.ELECTION_TYPE','m_election_details.YEAR','candidate_personal_detail.cand_hname','candidate_nomination_detail.pc_no','candidate_nomination_detail.st_code','candidate_nomination_detail.district_no','candidate_nomination_detail.party_id', 'candidate_personal_detail.cand_name', 'expenditure_reports.finalized_status', 'expenditure_reports.updated_at as finalized_date', 'expenditure_reports.final_by_ro', 'expenditure_reports.date_of_declaration','candidate_personal_detail.candidate_id')
       ->where('candidate_nomination_detail.application_status', '=', '6')
      ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
      ->where('m_election_details.CONST_TYPE', '=', 'PC')
      ->where('candidate_nomination_detail.st_code', '=', $st_code)
      ->where('expenditure_reports.date_of_declaration', '!=', '')->orderBy('candidate_personal_detail.cand_name','desc')
      ->get();*/

      $candList = DB::select("select TEMP.YEAR,ELECTION_TYPE,cpd.cand_hname,TEMP.pc_no,TEMP.st_code,TEMP.district_no,
      TEMP.party_id,cpd.cand_name,cpd.candidate_id,TEMP.finalized_status,TEMP.finalized_date,TEMP.final_by_ro,
      TEMP.date_of_declaration
      from(
      select med.YEAR,med.ELECTION_TYPE,cnd.pc_no,
      cnd.st_code,cnd.district_no,cnd.candidate_id,
      cnd.party_id,er.finalized_status,
      er.updated_at as finalized_date, er.final_by_ro,
      er.date_of_declaration
      from candidate_nomination_detail cnd,
      m_election_details med ,expenditure_reports er
      where cnd.application_status = 6
      and cnd.party_id <> 1180
      and cnd.finalaccepted= 1
      and med.CONST_TYPE = 'PC'
      and er.date_of_declaration !=''
      AND med.st_code = cnd.st_code
      and med.CONST_NO = cnd.pc_no
      and er.candidate_id =cnd.candidate_id
      )TEMP left join candidate_personal_detail cpd on TEMP.candidate_id = cpd.candidate_id
      group by TEMP.candidate_id
      order by cpd.cand_name desc");
 }

 if(!empty($_GET['pdf']) && $_GET['pdf']="yes"){
        ////// code for pdf generation//////
 $pdf = PDF::loadView('admin.pc.eci.Expenditure.CandidateWisePdf', ['user_data' => $d, 'candList' => $candList]);
  return $pdf->download('CandidateWisePdf_' . trim($_GET['pdf']) . '_Today_' . $cur_time . '.pdf'); 
  return view('admin.pc.eci.Expenditure.CandidateWisePdf');  
   }
   elseif (!empty($_GET['exl']) && $_GET['exl']="yes") {
      //////////export exel //////////////
    // Initialize the array which will be passed into the Excel
  // generator.
  $candidateArray = []; 

  // Define the Excel spreadsheet headers
  $candidateArray[] = ['S.NO', 'STATE NAME','PC NO & PC NAME','YEAR','ELECTION TYPE','TOTAL EXPENDITURE'];

  // Convert each member of the returned collection into an array,
  // and append it to the payments array.
  $i=1;
  foreach ($candList as $canwise) {
    $pcdetails=getpcbypcno($canwise->st_code,$canwise->pc_no); 
    $st=getstatebystatecode($canwise->st_code);
      $candidateArr[$i]['S.no'] = $i;
$candidateArr[$i]['cand_name'] = $canwise->cand_name;
      $candidateArr[$i]['state_name'] = $st->ST_NAME;
      $candidateArr[$i]['pc_no'] = $pcdetails->PC_NO.' - '.$pcdetails->PC_NAME;
      $candidateArr[$i]['year'] = $canwise->YEAR;
      $candidateArr[$i]['election_type'] = $canwise->ELECTION_TYPE;
      $candidateArr[$i]['total_expenditure'] = $this->expenditureModel->getcandidatetotalexpenditure($canwise->candidate_id);
      $candidateArr[$i]['total_expenditure'] = !empty($candidateArr[$i]['total_expenditure']) ? 'Rs. '.$candidateArr[$i]['total_expenditure']:0;

      $i++;
  }

  foreach ($candidateArr as $candidate) {
           $candidateArray[] = $candidate;
  }

 // Generate and return the spreadsheet
  \Excel::create('CandidateWiseExpenditure', function($excel) use ($candidateArray) {

      // Set the spreadsheet title, creator, and description
      $excel->setTitle('Candidate Wise Expenditure');
      $excel->setCreator('Eci')->setCompany('Election Commission Of India');
      // Build the spreadsheet, passing in the payments array
      $excel->sheet('CandidateWiseExpenditure', function($sheet) use ($candidateArray) {
          $sheet->fromArray($candidateArray, null, 'A1', false, false);
      });

  })->download('csv');


   }
   else
   {
     return view('admin.pc.eci.Expenditure.candidate_wise_expenditure', ['user_data' => $d, 'ele_details' => $ele_details, 'candList' => $candList,"statelist"=>$statelist,"st_code"=>$st_code]);
   }
// dd(DB::getQueryLog());
// dd($candList);

} else {
return redirect('/officer-login');
}
}


public function getPartyWiseExpenditure(Request $request)
{
// DB::enableQueryLog();
if (Auth::check()) {
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$cur_time = Carbon::now();
$conditions="";
if(!empty($_GET['party'])){
$party = $_GET['party'];
$conditions .=" and candidate_nomination_detail.party_id='$party' ";
}

if(!empty($_GET['state'])){
$state = $_GET['state'];
$conditions .=" and candidate_nomination_detail.st_code='$state' ";
}

if(!empty($_GET['pc'])){ 
$pc = $_GET['pc'];
$conditions .=" and candidate_nomination_detail.pc_no='$pc' ";
}

#########################Code For State Wise Access By Niraj date 23-07-2019#####################
$username=$user->officername;
$st_code = $request->input('state');
$zonestate = $this->eciexpenditureModel->getzonestate($username);
if($zonestate->isEmpty()){
  $permitstates = '';
}else{
  $permitstates = explode(',',$zonestate[0]->assign_state);
}

$permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;

  if(!empty($permitstate)){
      $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
  }else{
     $statelist = getallstate();
  }
  if(!empty($st_code)){
      $st_code=$st_code;
  }elseif(empty($st_code) && !empty($permitstate)){
      $st_code=array_values($permitstate)[0];
  }else {
      $st_code=0;
  }
 
#########################Code For State Wise Access#####################


if(!empty($conditions)){
 $partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail WHERE 1 $conditions");
if(!empty($partyids))
 {
  foreach ($partyids as  $value) {
    $partyID[] = $value->party_id;
  }

  $partyids = implode(',', $partyID);
 } 

 //print_r($partyids);die; 
  $partyids = !empty($partyids)?$partyids:0;           
  $partyids = rtrim(implode(',',array_unique(explode(',',$partyids))), ',');
  
 $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
}
else{

$partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail");
 if(!empty($partyids))
 {
  foreach ($partyids as  $value) {
    $partyID[] = $value->party_id;
  }

  $partyids = implode(',', $partyID);
 } 

 //print_r($partyids);die; 
  $partyids = !empty($partyids)?$partyids:0;
  $partyids = rtrim(implode(',',array_unique(explode(',',$partyids))), ',');
 // print_r($partyids);die;
  $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE !='Z' and PARTYTYPE !='Z1'");

//$partylist = DB::select("SELECT * FROM m_party WHERE 1 and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
 }


if(!empty($_GET['pdf']) && $_GET['pdf']="yes"){
        ////// code for pdf generation//////
 $pdf = PDF::loadView('admin.pc.eci.Expenditure.getPartyWisePDF', ['user_data' => $d, 'partylist' => $partylist]);
  return $pdf->download('PartyWisePdf_' . trim($_GET['pdf']) . '_Today_' . $cur_time . '.pdf'); 
  return view('admin.pc.eci.Expenditure.getPartyWisePDF');  
   }
   elseif (!empty($_GET['exl']) && $_GET['exl']=="yes") {
      
   if(!empty($state)){   
    $st=getstatebystatecode($state);
    $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';  
      }
      else{
         $stateName = "ALL"; 
    $state="";
      }

  if(!empty($pc)){
    $pcdetails=getpcbypcno($state,$pc); 
    $pcName=!empty($pcdetails->PC_NAME) ? $pcdetails->PC_NAME : 'ALL';
    }
  else
  {
    $pcName="ALL";
    $pc="";
  }
  
      // Initialize the array which will be passed into the Excel
  // generator.
  $partyArray = []; 

  
  // Define the Excel spreadsheet headers
//  $partyArray[] = ['S.no','State','AC Name','Party Name','Total Expenditure'];

  // Convert each member of the returned collection into an array,
  // and append it to the payments array.
  $i=1;
  foreach ($partylist as $party) {
      $partyArr[$i]['S.no'] = $i;
      $partyArr[$i]['state'] = $stateName;
      $partyArr[$i]['pc_name'] = $pcName;
      $partyArr[$i]['party_name'] = $party->PARTYABBRE.' - '.$party->PARTYNAME;
      $partyArr[$i]['total_expenditure'] = $this->expenditureModel->getpartytotalexpenditure($party->CCODE,$state,$pc);
      $partyArr[$i]['total_expenditure'] = !empty($partyArr[$i]['total_expenditure'])?$partyArr[$i]['total_expenditure']:0;
      $i++;
  }

  foreach ($partyArr as $pay) {
           $partyArray[] = $pay;
  }
  $amount=array_column($partyArray,'total_expenditure');              
  array_multisort($amount, SORT_DESC,$partyArray);
  $headingpartyArray[] = ['S.no','State','AC Name','Party Name','Total Expenditure'];
 // array_shift($partyArray,array('S.no','State','AC Name','Party Name','Total Expenditure'));
  $partyArray2=$headingpartyArray+$partyArray;
 // Generate and return the spreadsheet
  \Excel::create('PartyWiseExpenditure', function($excel) use ($partyArray2) {

      // Set the spreadsheet title, creator, and description
      $excel->setTitle('Party Wise Expenditure');
      $excel->setCreator('Eci')->setCompany('Election Commission Of India');
      // Build the spreadsheet, passing in the payments array
      $excel->sheet('PartyWiseExpenditure', function($sheet) use ($partyArray2) {
          $sheet->fromArray($partyArray2, null, 'A1', false, false);
      });

  })->download('csv');

   }
   else
   {
     return view('admin.pc.eci.Expenditure.party_wise_expenditure', ['user_data' => $d, 'ele_details' => $ele_details, 'partylist' => $partylist,"statelist"=>$statelist,"st_code"=>$st_code]);
   }

} else {
return redirect('/officer-login');
} 
}


public function trackingReport(Request $request) {
try {          
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
$uid = $user->id;
#########################Code For State Wise Access By Niraj date 23-07-2019#####################
$username=$user->officername;

$st_code = $request->input('state');
$pc_no = $request->input('pc'); 
$zonestate = $this->eciexpenditureModel->getzonestate($username);
if($zonestate->isEmpty()){
  $permitstates = '';
}else{
  $permitstates = explode(',',$zonestate[0]->assign_state);
}

$permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;

  if(!empty($permitstate)){
      $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
  }else{
     $statelist = getallstate();
  }              
if(!empty($st_code)){
  $st_code=$st_code;
  $pclist=getpcbystate($st_code);
   
}elseif(empty($st_code)){
  $st_code=!empty($statelist[0]->ST_CODE) ? $statelist[0]->ST_CODE:'';                    
  $pclist=getpcbystate($st_code);
  $pc_no=!empty($pclist[0]->PC_NO) ? $pclist[0]->PC_NO:'';

}else {
  $st_code=0;
}

$election=  getelectiondetailbystcode($st_code,$pc_no,'PC');
$ELECTION_ID=!empty($election->ELECTION_ID)? $election->ELECTION_ID:0;
$ele_details = $this->commonModel->election_details($st_code, $d->ac_no, $pc_no, $d->id, $d->officerlevel);


$winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $st_code)->where('pc_no', $pc_no)->where('election_id', $ELECTION_ID)->first();



$stateDetail = getstatebystatecode($st_code);
$Pcdetail = getpcbypcno($st_code, $pc_no);

$PcName = !empty($Pcdetail) ? $Pcdetail->PC_NAME : '';
$PcNo = !empty($Pcdetail->PC_NO) ? $Pcdetail->PC_NO : '';


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
  ->where('candidate_nomination_detail.st_code', $st_code)
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
$expenditure_understates = DB::table('expenditure_understates')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$st_code)->where('constituency_no',$PcNo)->where('understated_type_id','9')->first();
$other_source_cc = DB::table('expenditure_fund_source')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$st_code)->where('constituency_no',$PcNo)
        ->whereIn('other_source_payment_mode',array('Cheque','Cash'))->sum('other_source_amount');
$other_source_kind = DB::table('expenditure_fund_source')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$st_code)->where('constituency_no',$PcNo)
        ->whereIn('other_source_payment_mode',array('In Kind'))->sum('other_source_amount');
$candList[$i]->comment_9 = !empty($expenditure_understates->comment)?$expenditure_understates->comment:"";
$candList[$i]->understated_type_id_9 = !empty($expenditure_understates->understated_type_id)?$expenditure_understates->understated_type_id:"";
$candList[$i]->other_source_amt_cc = !empty($other_source_cc)?$other_source_cc:"0";
$candList[$i]->other_source_amt_kind = !empty($other_source_kind)?$other_source_kind:"0";
$i++;
}
}





return view('admin.expenditure.summary_report_eci', ['user_data' => $d, 
'ele_details' => $ele_details, "cand_finalize_ro" => array(),
'candList' => $candList,
'Pcdetail' => $Pcdetail, 'stateDetail' =>$stateDetail,
'winn_data' => $winn_data,
'statelist'=>$statelist,
'st_code'=>$st_code,
'pc_no'=>$pc_no
]);
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
}

public function trackingReportprint(Request $request,$state,$pc) {
try {
$mpdf = new \Mpdf\Mpdf();
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
$uid = $user->id;
$username=$user->officername; 
$st_code = !empty($state)? base64_decode($state):0;
$pc_no = !empty($pc)? base64_decode($pc):0;              
$statelist = getallstate();              
if(!empty($st_code)){
  $st_code=$st_code;
}elseif(empty($st_code)){
  $st_code=!empty($statelist[0]->ST_CODE) ? $statelist[0]->ST_CODE:'';                    
  $pclist=getpcbystate($st_code);
  $pc_no=!empty($pclist[0]->PC_NO) ? $pclist[0]->PC_NO:'';

}else {
  $st_code=0;
}

//  echo'-'.$st_code.'-'.$pc_no;die;
$election=  getelectiondetailbystcode($st_code,$pc_no,'PC');
$ELECTION_ID=!empty($election->ELECTION_ID)? $election->ELECTION_ID:0;
$ele_details = $this->commonModel->election_details($st_code, $d->ac_no, $pc_no, $d->id, $d->officerlevel);


$winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $st_code)->where('pc_no', $pc_no)->where('election_id', $ELECTION_ID)->first();



$stateDetail = getstatebystatecode($st_code);
$stateName =!empty($stateDetail->ST_NAME)?$stateDetail->ST_NAME:'';
$Pcdetail = getpcbypcno($st_code, $pc_no);

$PcName = !empty($Pcdetail) ? $Pcdetail->PC_NAME : '';
$PcNo = !empty($Pcdetail->PC_NO) ? $Pcdetail->PC_NO : '';
$date = date('d-m-Y');

/* $ELECTION_TYPE = !empty($ele_details->ELECTION_TYPE) ? $ele_details->ELECTION_TYPE : ''; */
$ELECTION_TYPE="General PC";
$date = date('d-m-Y');
$year = '2019';
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
  ->where('candidate_nomination_detail.st_code', $st_code)
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
$expenditure_understates = DB::table('expenditure_understates')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$st_code)->where('constituency_no',$PcNo)->where('understated_type_id','9')->first();
$other_source_cc = DB::table('expenditure_fund_source')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$st_code)->where('constituency_no',$PcNo)
        ->whereIn('other_source_payment_mode',array('Cheque','Cash'))->sum('other_source_amount');
$other_source_kind = DB::table('expenditure_fund_source')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$st_code)->where('constituency_no',$PcNo)
        ->whereIn('other_source_payment_mode',array('In Kind'))->sum('other_source_amount');
$candList[$i]->comment_9 = !empty($expenditure_understates->comment)?$expenditure_understates->comment:"";
$candList[$i]->understated_type_id_9 = !empty($expenditure_understates->understated_type_id)?$expenditure_understates->understated_type_id:"";
$candList[$i]->other_source_amt_cc = !empty($other_source_cc)?$other_source_cc:"0";
$candList[$i]->other_source_amt_kind = !empty($other_source_kind)?$other_source_kind:"0";
$i++;
}
}



$pdf = view('admin.expenditure.pdf_tracking_report', compact('candList','stateDetail' ,'Pcdetail', 'winn_data'));
$mpdf->WriteHTML($pdf);
$mpdf->Output();
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
}
//district wise filter start here
public function getDistrictReport(Request $request) {
//dd($request->all());
//PC ECI getOfficersmis TRY CATCH STARTS HERE
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);   
  $xss = new xssClean;
  $st_code = $xss->clean_input($request->input('state'));
  $cons_no = $xss->clean_input($request->input('pc'));
  $district = $xss->clean_input($request->input('district'));
  $ele_details = $this->commonModel->election_details($st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
  #########################Code For State Wise Access By Niraj date 23-07-2019#####################
  $username = $user->officername;
 
  $zonestate = $this->eciexpenditureModel->getzonestate($username);

  if ($zonestate->isEmpty()) {
      $permitstates = '';
  } else {
      $permitstates = explode(',', $zonestate[0]->assign_state);
  }

  $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

  if (!empty($permitstate)) {
      $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
  } else {
      $statelist = getallstate();
  }
  if ($permitstates != '') {
      $permitstates[] = "All";
  }

  if (!empty($st_code)) {
      $st_code = $st_code;
  } elseif (empty($st_code) && !empty($permitstate)) {
      // $st_code=array_values($permitstate)[0];
      $st_code = end($permitstates);
      $allstate = array_pop($permitstates);
  } else {
      $st_code = 0;
  }
  #########################Code For State Wise Access#####################

  $st_code = !empty($st_code) ? $st_code : '';
  $cons_no = !empty($cons_no) ? $cons_no : '';
  $district = !empty($district) ? $district : '';
  $districts = DB::table('m_district')->select('DIST_NAME', 'DIST_NO')->where('ST_CODE', $st_code)->get();


  // DB::enableQueryLog();
  $totalContestedCandidate = DB::table('candidate_nomination_detail')
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
          ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
          ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
          ->where('candidate_nomination_detail.application_status', '=', '6')
          ->where('candidate_nomination_detail.finalaccepted', '=', '1')
          ->where('candidate_nomination_detail.symbol_id', '<>', '200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA');

  if (!empty($st_code) && empty($cons_no) && $st_code != 'All' && empty($district)) {
      $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
  }if (!empty($st_code) && !empty($cons_no) && $st_code != 'All' && empty($district)) {
      $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
      $totalContestedCandidate->where('candidate_nomination_detail.pc_no', '=', $cons_no);
  } else if (!empty($st_code) && !empty($district) && empty($cons_no) && $st_code != 'All') {
      $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);

      $totalContestedCandidate->join("m_ac", function($join) {
          $join->on("m_ac.ST_CODE", "=", "candidate_nomination_detail.st_code")
                  ->on("m_ac.PC_NO", "=", "candidate_nomination_detail.pc_no");
      });

      $totalContestedCandidate->where('m_ac.DIST_NO_HDQTR', '=', $district);
  } else if (!empty($st_code) && !empty($district) && !empty($cons_no) && $st_code != 'All') {

      $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
      $totalContestedCandidate->join("m_ac", function($join) {
          $join->on("m_ac.ST_CODE", "=", "candidate_nomination_detail.st_code")
                  ->on("m_ac.PC_NO", "=", "candidate_nomination_detail.pc_no");
      });
      $totalContestedCandidate->where('m_ac.DIST_NO_HDQTR', '=', $district);
      $totalContestedCandidate->where('candidate_nomination_detail.pc_no', '=', $cons_no);
  } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {

      $totalContestedCandidate->whereIn('candidate_nomination_detail.st_code', $permitstates);
  }
  //dd(DB::getQueryLog());
  $result = $totalContestedCandidate->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
          ->groupBy("candidate_nomination_detail.st_code", 'candidate_nomination_detail.pc_no')
          ->get();
  if (!empty($district)) {
       $all_pc = DB::table('m_pc')
           ->select('m_pc.PC_NO','m_pc.PC_NAME')
          ->join('m_ac',function($join){
              $join->on('m_ac.ST_CODE','=','m_pc.ST_CODE');
              $join->on('m_ac.PC_NO','=','m_pc.PC_NO');
          })
          ->where('m_pc.ST_CODE', $st_code)
          ->where('m_ac.DIST_NO_HDQTR', $district)
          ->orderBy('m_pc.PC_NAME')
          ->groupBy('m_pc.ST_CODE','m_pc.PC_NAME')
          ->get();
  } else {
      $all_pc = DB::table('m_pc')
          ->select('m_pc.PC_NO','m_pc.PC_NAME')
          ->join('m_ac',function($join){
              $join->on('m_ac.ST_CODE','=','m_pc.ST_CODE');
              $join->on('m_ac.PC_NO','=','m_pc.PC_NO');
          })
          ->where('m_pc.ST_CODE', $st_code)

          ->orderBy('m_pc.PC_NAME')
          ->groupBy('m_pc.ST_CODE','m_pc.PC_NAME')
          ->get();
  }



  return view('admin.pc.eci.Expenditure.district-report', ['user_data' => $d,
      'totalContestedCandidate' => $result,
      'cons_no' => $cons_no,
      'st_code' => $st_code,
      'statelist' => $statelist,
      'district' => $district,
      'districts' => $districts,
      'all_pc' => $all_pc,
      'permitstates' => $permitstates,
      'count' => count($result)]);
} else {
  return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ECI getOfficersmis TRY CATCH ENDS HERE    
}

public function getDistrictReportPdf(Request $request, $state, $district, $pc) {
//dd($request->all());
//PC ECI getOfficersmis TRY CATCH STARTS HERE
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $xss = new xssClean;
  $st_code = $xss->clean_input(base64_decode($state));
  $cons_no = $xss->clean_input(base64_decode($pc));
  $district = $xss->clean_input(base64_decode($district));
  $ele_details = $this->commonModel->election_details($st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

  #########################Code For State Wise Access By Niraj date 23-07-2019#####################
  $username = $user->officername;
  
  $zonestate = $this->eciexpenditureModel->getzonestate($username);

  if ($zonestate->isEmpty()) {
      $permitstates = '';
  } else {
      $permitstates = explode(',', $zonestate[0]->assign_state);
  }

  $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

  if (!empty($permitstate)) {
      $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
  } else {
      $statelist = getallstate();
  }
  if ($permitstates != '') {
      $permitstates[] = "All";
  }

  if (!empty($st_code)) {
      $st_code = $st_code;
  } elseif (empty($st_code) && !empty($permitstate)) {
      // $st_code=array_values($permitstate)[0];
      $st_code = end($permitstates);
      $allstate = array_pop($permitstates);
  } else {
      $st_code = 0;
  }
  #########################Code For State Wise Access#####################

  $st_code = !empty($st_code) ? $st_code : '';
  $cons_no = !empty($cons_no) ? $cons_no : '';
  $district = !empty($district) ? $district : '';
  $districts = DB::table('m_district')->select('DIST_NAME', 'DIST_NO')->where('ST_CODE', $st_code)->get();


  // DB::enableQueryLog();
  $totalContestedCandidate = DB::table('candidate_nomination_detail')
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
          ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
          ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
          ->where('candidate_nomination_detail.application_status', '=', '6')
          ->where('candidate_nomination_detail.finalaccepted', '=', '1')
          ->where('candidate_nomination_detail.symbol_id', '<>', '200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA');

  if (!empty($st_code) && empty($cons_no) && $st_code != 'All' && empty($district)) {
      $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
  }if (!empty($st_code) && !empty($cons_no) && $st_code != 'All' && empty($district)) {
      $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
      $totalContestedCandidate->where('candidate_nomination_detail.pc_no', '=', $cons_no);
  } else if (!empty($st_code) && !empty($district) && empty($cons_no) && $st_code != 'All') {

      
      $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);

      $totalContestedCandidate->join("m_ac", function($join) {
          $join->on("m_ac.ST_CODE", "=", "candidate_nomination_detail.st_code")
                  ->on("m_ac.PC_NO", "=", "candidate_nomination_detail.pc_no");
      });

      $totalContestedCandidate->where('m_ac.DIST_NO_HDQTR', '=', $district);
  } else if (!empty($st_code) && !empty($district) && !empty($cons_no) && $st_code != 'All') {

      $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
      $totalContestedCandidate->join("m_ac", function($join) {
          $join->on("m_ac.ST_CODE", "=", "candidate_nomination_detail.st_code")
                  ->on("m_ac.PC_NO", "=", "candidate_nomination_detail.pc_no");
      });
      $totalContestedCandidate->where('m_ac.DIST_NO_HDQTR', '=', $district);
      $totalContestedCandidate->where('candidate_nomination_detail.pc_no', '=', $cons_no);
  } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {

      $totalContestedCandidate->whereIn('candidate_nomination_detail.st_code', $permitstates);
  }
  //dd(DB::getQueryLog());
  $result = $totalContestedCandidate->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
          ->groupBy("candidate_nomination_detail.st_code", 'candidate_nomination_detail.pc_no')
          ->get();
  if (!empty($district)) {
      $all_pc = DB::table('m_pc')
           ->select('m_pc.PC_NO','m_pc.PC_NAME')
          ->join('m_ac',function($join){
              $join->on('m_ac.ST_CODE','=','m_pc.ST_CODE');
              $join->on('m_ac.PC_NO','=','m_pc.PC_NO');
          })
          ->where('m_pc.ST_CODE', $st_code)
          ->where('m_ac.DIST_NO_HDQTR', $district)
          ->orderBy('m_pc.PC_NAME')
          ->groupBy('m_pc.ST_CODE','m_pc.PC_NAME')
          ->get();
  } else {
     $all_pc = DB::table('m_pc')
           ->select('m_pc.PC_NO','m_pc.PC_NAME')
          ->join('m_ac',function($join){
              $join->on('m_ac.ST_CODE','=','m_pc.ST_CODE');
              $join->on('m_ac.PC_NO','=','m_pc.PC_NO');
          })
          ->where('m_pc.ST_CODE', $st_code)

          ->orderBy('m_pc.PC_NAME')
          ->groupBy('m_pc.ST_CODE','m_pc.PC_NAME')
          ->get();
  }


  $pdf = PDF::loadView('admin.pc.eci.Expenditure.district-reportPDFhtml', ['user_data' => $d,
              'totalContestedCandidate' => $result,
              'cons_no' => $cons_no,
              'st_code' => $st_code,
              'statelist' => $statelist,
              'district' => $district,
              'districts' => $districts,
              'all_pc' => $all_pc,
              'permitstates' => $permitstates,
              'count' => count($result)
  ]);
  $cur_time = Carbon::now();
  return $pdf->download('DistrictreportPdf_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');

} else {
  return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ECI getOfficersmis TRY CATCH ENDS HERE    
}

public function getDistrictReportExl(Request $request, $state, $district, $pc) {
//dd($request->all());
//PC ECI getOfficersmis TRY CATCH STARTS HERE
try {
if (Auth::check()) {
  $user = Auth::user();
  $uid = $user->id;
  $d = $this->commonModel->getunewserbyuserid($user->id);
  $xss = new xssClean;
  $st_code = $xss->clean_input(base64_decode($state));
  $cons_no = $xss->clean_input(base64_decode($pc));
  $district = $xss->clean_input(base64_decode($district));
 $ele_details = $this->commonModel->election_details($st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

  #########################Code For State Wise Access By Niraj date 23-07-2019#####################
  $username = $user->officername;
  
  $zonestate = $this->eciexpenditureModel->getzonestate($username);

  if ($zonestate->isEmpty()) {
      $permitstates = '';
  } else {
      $permitstates = explode(',', $zonestate[0]->assign_state);
  }

  $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

  if (!empty($permitstate)) {
      $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
  } else {
      $statelist = getallstate();
  }
  if ($permitstates != '') {
      $permitstates[] = "All";
  }

  if (!empty($st_code)) {
      $st_code = $st_code;
  } elseif (empty($st_code) && !empty($permitstate)) {
      // $st_code=array_values($permitstate)[0];
      $st_code = end($permitstates);
      $allstate = array_pop($permitstates);
  } else {
      $st_code = 0;
  }
  #########################Code For State Wise Access#####################

  $st_code = !empty($st_code) ? $st_code : '';
  $cons_no = !empty($cons_no) ? $cons_no : '';
  $district = !empty($district) ? $district : '';
  $districts = DB::table('m_district')->select('DIST_NAME', 'DIST_NO')->where('ST_CODE', $st_code)->get();


  // DB::enableQueryLog();


  $cur_time = Carbon::now();

  \Excel::create('DistrictActiveUsersReportExcel_' . '_' . $cur_time, function($excel) use($st_code, $district, $cons_no, $permitstates) {
      $excel->sheet('Sheet1', function($sheet) use($st_code, $district, $cons_no, $permitstates) {
          $totalContestedCandidate = DB::table('candidate_nomination_detail')
                  ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                  ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                  ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                  ->where('candidate_nomination_detail.application_status', '=', '6')
                  ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                  ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                  ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA');

          if (!empty($st_code) && empty($cons_no) && $st_code != 'All' && empty($district)) {
              $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
          }if (!empty($st_code) && !empty($cons_no) && $st_code != 'All' && empty($district)) {
              $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
              $totalContestedCandidate->where('candidate_nomination_detail.pc_no', '=', $cons_no);
          } else if (!empty($st_code) && !empty($district) && empty($cons_no) && $st_code != 'All') {
              $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);

              $totalContestedCandidate->join("m_ac", function($join) {
                  $join->on("m_ac.ST_CODE", "=", "candidate_nomination_detail.st_code")
                          ->on("m_ac.PC_NO", "=", "candidate_nomination_detail.pc_no");
              });

              $totalContestedCandidate->where('m_ac.DIST_NO_HDQTR', '=', $district);
          } else if (!empty($st_code) && !empty($district) && !empty($cons_no) && $st_code != 'All') {

              $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
              $totalContestedCandidate->join("m_ac", function($join) {
                  $join->on("m_ac.ST_CODE", "=", "candidate_nomination_detail.st_code")
                          ->on("m_ac.PC_NO", "=", "candidate_nomination_detail.pc_no");
              });
              $totalContestedCandidate->where('m_ac.DIST_NO_HDQTR', '=', $district);
              $totalContestedCandidate->where('candidate_nomination_detail.pc_no', '=', $cons_no);
          } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {

              $totalContestedCandidate->whereIn('candidate_nomination_detail.st_code', $permitstates);
          }
          //dd(DB::getQueryLog());
          $result = $totalContestedCandidate->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                  ->groupBy("candidate_nomination_detail.st_code", 'candidate_nomination_detail.pc_no')
                  ->get();


          $arr = array();
          $TotalUsers = 0;
          $TotalPendingatRO = 0;
          $TotalPendingatCEO = 0;
          $TotalPendingatECI = 0;
          $TotalfiledData = 0;
          $TotalnotfiledData = 0;
          $Totalpc = 0;
          $TotalDEONotice = 0;
          $TotalCEONotice = 0;
          $Totalfinalcompletedcount = 0;
          $TotalFinalByDEO = 0;


          $user = Auth::user();
          $count = 1;
          foreach ($result as $key => $listdata) {
              $cons_no = $listdata->pc_no;
              //get finalby DEO count
              $finalbyDEO = $this->eciexpenditureModel->gettotalfinalbyDEO('PC', $listdata->st_code, $cons_no);
              $TotalFinalByDEO += $finalbyDEO;
              //get partially pending data count
              $pendingatROold = $this->eciexpenditureModel->gettotalpartiallypending('PC', $listdata->st_code, $cons_no);
              //Get Data entry finalize Count 
              $pendingatCEO = $this->eciexpenditureModel->gettotalfinalbyceo('PC', $listdata->st_code, $cons_no);

              //Get pendingatDEO Count 
              $pendingatRO = $listdata->totalcandidate - $pendingatCEO;

              //Get Data entry finalize Count 
              $pendingatECI = $this->eciexpenditureModel->gettotalfinalbyeci('PC', $listdata->st_code, $cons_no);

              //Get filedcount Count 
              $filedcount = $this->eciexpenditureModel->gettotaldataentryStart('PC', $listdata->st_code, $cons_no);

              // Get Pending Data Count 
              $notfiledcount = $listdata->totalcandidate - $filedcount;
             // $TotalnotfiledData += $notfiledcount;

              //Get noticeatDEOCount Count 
              $noticeatDEOCount = $this->eciexpenditureModel->gettotalnoticeatDEO('PC', $listdata->st_code, $cons_no);

              //Get noticeatCEOCount Count 
              $noticeatCEOCount = $this->eciexpenditureModel->gettotalnoticeatCEO('PC', $listdata->st_code, $cons_no);

              //Get finalcompletedcount Count 
              $finalcompletedcount = $this->eciexpenditureModel->gettotalCompletedbyEci('PC', $listdata->st_code, $cons_no);

              $st = getstatebystatecode($listdata->st_code);
              $acbystate = getacbystate($listdata->st_code);
              $account = count($acbystate);
              // $Totalac += $account;
              $pcdetails = getpcbypcno($listdata->st_code, $listdata->pc_no);
              $pcnoname = $pcdetails->PC_NO . '-' . $pcdetails->PC_NAME;

              $st_code = !empty($st_code) ? $st_code : $listdata->st_code;
              $allStates[] = [
                  'st_code' => $st_code,
                  'pc_no' => $listdata->pc_no,
              ];

              // get district start here
              $detriectdetails = DB::table('m_ac')
                      ->where('ST_CODE', $listdata->st_code)
                      ->where('PC_NO', $listdata->pc_no)
                      ->groupBy('m_ac.DIST_NO_HDQTR')
                      ->get();
              $districtids = [];
              if (!empty($detriectdetails)) {
                  foreach ($detriectdetails as $item) {
                      $districtids[] = $item->DIST_NO_HDQTR;
                  }
              }

              $allDistrict = '';
              if (!empty($districtids)) {
                  foreach ($districtids as $id) {
                      $district = getdistrictbydistrictno($listdata->st_code, $id);
                      $allDistrict .= $district->DIST_NAME . ' ,';
                  }
              }
              $alldistricts1 = rtrim($allDistrict, ',');
              if (empty($alldistricts1) && $alldistricts1 == '') {
                  $districtName = 'N/A';
              } else {
                  $districtName = $alldistricts1;
              }


              // get district end here 



              $filedcount = !empty($filedcount) ? $filedcount : '0';
              $finalbyDEO = !empty($finalbyDEO) ? $finalbyDEO : '0';
              $pendingatRO = !empty($pendingatRO) ? $pendingatRO : '0';
              $pendingatCEO = !empty($pendingatCEO) ? $pendingatCEO : '0';
              $pendingatECI = !empty($pendingatECI) ? $pendingatECI : '0';
              $noticeatDEOCount = !empty($noticeatDEOCount) ? $noticeatDEOCount : '0';
              $noticeatCEOCount = !empty($noticeatCEOCount) ? $noticeatCEOCount : '0';
              $finalcompletedcount = !empty($finalcompletedcount) ? $finalcompletedcount : '0';
              $account = !empty($account) ? $account : '0';
              $notfiledcount = (!empty($notfiledcount) || $notfiledcount <= 0) ? $notfiledcount : '0';

              $data = array($count,
                  $st->ST_NAME,
                  $districtName,
                  $pcnoname,
                  $listdata->totalcandidate,
                  $filedcount,
                  $notfiledcount,
                  $finalbyDEO,
                  $pendingatRO,
                  $pendingatCEO,
                  $pendingatECI,
                  $finalcompletedcount
              );
              $TotalUsers += $listdata->totalcandidate;
               if ($pendingatECI > 0 || $pendingatCEO >= 0 || $finalcompletedcount > 0) {
              $pendingatRO = $listdata->totalcandidate - ($pendingatCEO + $pendingatECI + $finalcompletedcount);
              $TotalPendingatRO += $pendingatRO;
          }
             
              $TotalPendingatCEO += $pendingatCEO;
              $TotalPendingatECI += $pendingatECI;
              $TotalDEONotice += $noticeatDEOCount;
              $TotalCEONotice += $noticeatCEOCount;
              $Totalfinalcompletedcount += $finalcompletedcount;
              $TotalnotfiledData += $notfiledcount;
              $TotalfiledData += $filedcount;
              array_push($arr, $data);
              // }
              $count++;
          }
           
         


          // all state list here
          if (!empty($allStates)) {

              if (!empty($allStates[0]['st_code']) && $allStates[0]['st_code'] == "All") {
                  foreach ($permitstates as $item) {
                      $Totalpc += DB::table('m_pc')
                              ->where('ST_CODE', $item)
                              ->count();
                  }
              } else {
                  foreach ($allStates as $item) {
                      $Totalpc += DB::table('m_pc')
                              ->where('ST_CODE', $item['st_code'])
                              ->where('PC_NO', $item['pc_no'])
                              ->count();
                  }
              }
          }

          // end all state here

          $totalvalues = array(
              'Total',
              '',
              '',
              $Totalpc,
              $TotalUsers,
              $TotalfiledData,
              $TotalnotfiledData,
              $TotalFinalByDEO, 
              $TotalPendingatRO,
              $TotalPendingatCEO,
              $TotalPendingatECI,
              $Totalfinalcompletedcount);
          
          array_push($arr, $totalvalues);
          $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
              'S.No.:',
              'State Name',
              'District Name',
              'PC NO AND PC NAME',
              'Total Candidate',
              'Started',
              'Not Started',
              'Finalise By DEO',
              'Pending At DEO',
              'Pending At CEO',
              'Pending At ECI',
              'Closed/Disqualified/Case Dropped')
          );
      });
  })->export('csv');
} else {
  return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ECI getOfficersmis TRY CATCH ENDS HERE    
}

// end getOfficersmis function

public function Alldistrict($stcode) {

$districts = DB::table('m_district')
  ->select('DIST_NAME', 'DIST_NO')
  ->where('ST_CODE', $stcode)
  ->orderBy('DIST_NAME')
  ->get();

return $districts;
}

// get all ac by state code and district no Start

function getAllPCs(Request $request) {
if (Auth::check()) {
$xss = new xssClean;
$stcode = $xss->clean_input($request->input('state'));
$district = $xss->clean_input($request->input('district'));
if (!empty($district)) {
  $all_pc = DB::table('m_pc')
          ->select('m_pc.PC_NO','m_pc.PC_NAME')
          ->join('m_ac',function($join){
              $join->on('m_ac.ST_CODE','=','m_pc.ST_CODE');
              $join->on('m_ac.PC_NO','=','m_pc.PC_NO');
          })
          ->where('m_pc.ST_CODE', $stcode)
          ->where('m_ac.DIST_NO_HDQTR', $district)
          ->orderBy('m_pc.PC_NAME')
          ->groupBy('m_pc.ST_CODE','m_pc.PC_NAME')
          ->get();
} else {
   $all_pc = DB::table('m_pc')
           ->select('m_pc.PC_NO','m_pc.PC_NAME')
          ->join('m_ac',function($join){
              $join->on('m_ac.ST_CODE','=','m_pc.ST_CODE');
              $join->on('m_ac.PC_NO','=','m_pc.PC_NO');
          })
          ->where('m_pc.ST_CODE', $stcode)              
          ->orderBy('m_pc.PC_NAME')
          ->groupBy('m_pc.ST_CODE','m_pc.PC_NAME')
          ->get();
}
}
return $all_pc;
}

// get all ac by state code and district no end
// start fund graph  

public function getNationlPartyWiseExpendituregraph(Request $request)
{
// DB::enableQueryLog();
if (Auth::check()) {
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$cur_time = Carbon::now();
$conditions="";
if(!empty($_GET['party'])){
$party = $_GET['party'];
$conditions .=" and candidate_nomination_detail.party_id='$party' ";
}

if(!empty($_GET['state'])){
$state = $_GET['state'];
$conditions .=" and candidate_nomination_detail.st_code='$state' ";
}

if(!empty($_GET['pc'])){ 
$pc = $_GET['pc'];
$conditions .=" and candidate_nomination_detail.pc_no='$pc' ";
}

#########################Code For State Wise Access By Niraj date 23-07-2019#####################
$username=$user->officername;
$st_code = $request->input('state');
$zonestate = $this->eciexpenditureModel->getzonestate($username);
if($zonestate->isEmpty()){
  $permitstates = '';
}else{
  $permitstates = explode(',',$zonestate[0]->assign_state);
}

$permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;

  if(!empty($permitstate)){
      $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
  }else{
     $statelist = getallstate();
  }
  if(!empty($st_code)){
      $st_code=$st_code;
  }elseif(empty($st_code) && !empty($permitstate)){
      $st_code=array_values($permitstate)[0];
  }else {
      $st_code=0;
  }
 
#########################Code For State Wise Access#####################


if(!empty($conditions)){
 $partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail WHERE 1 $conditions");
if(!empty($partyids))
 {
  foreach ($partyids as  $value) {
    $partyID[] = $value->party_id;
  }

  $partyids = implode(',', $partyID);
 } 

 //print_r($partyids);die; 
  $partyids = !empty($partyids)?$partyids:0;             
  $partyids = rtrim(implode(',',array_unique(explode(',',$partyids))), ',');
  
 $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
}
else{

$partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail");
 if(!empty($partyids))
 {
  foreach ($partyids as  $value) {
    $partyID[] = $value->party_id;
  }

  $partyids = implode(',', $partyID);
 } 

 //print_r($partyids);die; 
  $partyids = !empty($partyids)?$partyids:0;
  $partyids = rtrim(implode(',',array_unique(explode(',',$partyids))), ',');
 // print_r($partyids);die;
  $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE ='N'");

 }




    return view('admin.pc.eci.Expenditure.fund-nationalpartiesGraph', ['user_data' => $d, 'ele_details' => $ele_details, 'partylist' => $partylist,"statelist"=>$statelist,"st_code"=>$st_code]);
  



} else {
return redirect('/officer-login');
} 
}

public function getNationlPartyWiseExpenditureNationGraph(Request $request)
{
// DB::enableQueryLog();
if (Auth::check()) {
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$cur_time = Carbon::now();
$conditions="";
if(!empty($_GET['party'])){
$party = $_GET['party'];
$conditions .=" and candidate_nomination_detail.party_id='$party' ";
}

if(!empty($_GET['state'])){
$state = $_GET['state'];
$conditions .=" and candidate_nomination_detail.st_code='$state' ";
}
$pc='';
if(!empty($_GET['pc'])){ 
$pc = $_GET['pc'];
$conditions .=" and candidate_nomination_detail.pc_no='$pc' ";
}

#########################Code For State Wise Access By Niraj date 23-07-2019#####################
$username=$user->officername;
$st_code = $request->input('state');
$zonestate = $this->eciexpenditureModel->getzonestate($username);
if($zonestate->isEmpty()){
  $permitstates = '';
}else{
  $permitstates = explode(',',$zonestate[0]->assign_state);
}

$permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;

  if(!empty($permitstate)){
      $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
  }else{
     $statelist = getallstate();
  }
  if(!empty($st_code)){
      $st_code=$st_code;
  }elseif(empty($st_code) && !empty($permitstate)){
      $st_code=array_values($permitstate)[0];
  }else {
      $st_code=0;
  }
 
#########################Code For State Wise Access#####################


if(!empty($conditions)){
 $partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail WHERE 1 $conditions");
if(!empty($partyids))
 {
  foreach ($partyids as  $value) {
    $partyID[] = $value->party_id;
  }

  $partyids = implode(',', $partyID);
 } 

 //print_r($partyids);die; 
  $partyids = !empty($partyids)?$partyids:0;             
  $partyids = rtrim(implode(',',array_unique(explode(',',$partyids))), ',');
  
 $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
}
else{

$partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail");
 if(!empty($partyids))
 {
  foreach ($partyids as  $value) {
    $partyID[] = $value->party_id;
  }

  $partyids = implode(',', $partyID);
 } 

 //print_r($partyids);die; 
  $partyids = !empty($partyids)?$partyids:0;
  $partyids = rtrim(implode(',',array_unique(explode(',',$partyids))), ',');
 // print_r($partyids);die;
  $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE ='N'");

//$partylist = DB::select("SELECT * FROM m_party WHERE 1 and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
 }


    
      

  

  $data = [
      ['National Parties funds', 'No. of candidate to Whom National Parties gave funds'],
  ];
  $i=1;
  if(count($partylist)>0){
  foreach ($partylist as $party) {
      

      $totalcandidates=$this->expenditureModel->getcandidatesbyparties($party->CCODE,$st_code,$pc);
      $countPartywiseCandidate = count(explode(',',$totalcandidates));
      $data[] = [$party->PARTYABBRE,$countPartywiseCandidate];
       
      
  }
  }
  else {
      $data[] = ['No Data', 0];
  }              
  return json_encode($data);


 




   

} else {
return redirect('/officer-login');
} 
}
public function getNationlPartyWiseExpenditureAvgGraph(Request $request)
{
// DB::enableQueryLog();
if (Auth::check()) {
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$cur_time = Carbon::now();
$conditions="";
if(!empty($_GET['party'])){
$party = $_GET['party'];
$conditions .=" and candidate_nomination_detail.party_id='$party' ";
}
$pc='';
if(!empty($_GET['state'])){
$state = $_GET['state'];
$conditions .=" and candidate_nomination_detail.st_code='$state' ";
}

if(!empty($_GET['pc'])){ 
$pc = $_GET['pc'];
$conditions .=" and candidate_nomination_detail.pc_no='$pc' ";
}

#########################Code For State Wise Access By Niraj date 23-07-2019#####################
$username=$user->officername;
$st_code = $request->input('state');
$zonestate = $this->eciexpenditureModel->getzonestate($username);
if($zonestate->isEmpty()){
  $permitstates = '';
}else{
  $permitstates = explode(',',$zonestate[0]->assign_state);
}

$permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;

  if(!empty($permitstate)){
      $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
  }else{
     $statelist = getallstate();
  }
  if(!empty($st_code)){
      $st_code=$st_code;
  }elseif(empty($st_code) && !empty($permitstate)){
      $st_code=array_values($permitstate)[0];
  }else {
      $st_code=0;
  }
 
#########################Code For State Wise Access#####################


if(!empty($conditions)){
 $partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail WHERE 1 $conditions");
if(!empty($partyids))
 {
  foreach ($partyids as  $value) {
    $partyID[] = $value->party_id;
  }

  $partyids = implode(',', $partyID);
 } 

 //print_r($partyids);die; 
  $partyids = !empty($partyids)?$partyids:0;             
  $partyids = rtrim(implode(',',array_unique(explode(',',$partyids))), ',');
  
 $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
}
else{

$partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail");
 if(!empty($partyids))
 {
  foreach ($partyids as  $value) {
    $partyID[] = $value->party_id;
  }

  $partyids = implode(',', $partyID);
 } 

 //print_r($partyids);die; 
  $partyids = !empty($partyids)?$partyids:0;
  $partyids = rtrim(implode(',',array_unique(explode(',',$partyids))), ',');
 // print_r($partyids);die;
  $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE ='N'");

//$partylist = DB::select("SELECT * FROM m_party WHERE 1 and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
 }

  $data = [
      ['National Parties funds', 'Average funds given to a candidate by national parties'],
  ];
 

   
if(count($partylist)>0){
  foreach ($partylist as $party) {
       $totalcandidates=$this->expenditureModel->getcandidatesbyparties($party->CCODE,$st_code,$pc);
$countPartywiseCandidate = count(explode(',',$totalcandidates));

$totalpartyexpen=$this->expenditureModel->getPoliticalpartyExp($totalcandidates);
$avgpartyexpencandidatewise= round($totalpartyexpen/$countPartywiseCandidate,2);
$data[] = [$party->PARTYABBRE,$avgpartyexpencandidatewise];
  }
  }else {
      $data[] = ['No Data', 0];
  }   

  return json_encode($data);

} else {
return redirect('/officer-login');
} 
}

// end fund graph


###############Start Summary Analytical Dash Board Date 16-09-2019 by Niraj ####################
/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 16-09-19
* @author Modified By : 
* @author Modified Date : 
* @author param return getanalyticsummary By ECI fuction     
*/  
public function getanalyticsummary(Request $request) {  
// Get the current URL without the query string...
$namePrefix = \Route::current()->action['prefix'];
$segments = explode('/', $_SERVER['REQUEST_URI']);
$nameSuffix = $segments['3'];
// Get the full URL for the previous request...
$routesegment=array_slice(explode('/', url()->previous()), -2, 2);

//PC ECI getanalyticsummary TRY CATCH STARTS HERE
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

#########################Code For State Wise Access By Niraj date 23-07-2019#####################
$username=$user->officername;
$st_code = $request->input('state');
$zonestate = $this->eciexpenditureModel->getzonestate($username);

if($zonestate->isEmpty()){
$permitstates = '';
}else{
$permitstates = explode(',',$zonestate[0]->assign_state);
}

$permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;

if(!empty($permitstate)){
$statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
}else{
$statelist = getallstate();
}
if($permitstates !='') {  $permitstates[] = "All"; }

if(!empty($st_code)){
$st_code=$st_code;
}elseif(empty($st_code) && !empty($permitstate)){
// $st_code=array_values($permitstate)[0];
$st_code = end($permitstates);
$allstate= array_pop($permitstates);
}else {
$st_code=0;
}
//pop the last element off
#########################Code For State Wise Access#####################
$cons_no = $request->input('pc');
$st_code = !empty($st_code) ? $st_code : 0;
$cons_no = !empty($cons_no) ? $cons_no : 0;
//  echo  $st_code.'cons_no=>'.$cons_no; die;
DB::enableQueryLog();
if (!empty($st_code) && $cons_no == '' &&  $st_code !='All') { 
$totalContestedCandidatedata = DB::table('candidate_nomination_detail')
      ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
      ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
      ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
      ->where('candidate_nomination_detail.st_code', '=', $st_code)
      ->where('candidate_nomination_detail.application_status', '=', '6')
      ->where('candidate_nomination_detail.finalaccepted', '=', '1')
      ->where('candidate_nomination_detail.symbol_id', '<>', '200')
      ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
      ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
      ->groupBy("candidate_nomination_detail.st_code")
      ->get();
} else if (!empty($st_code) && $cons_no != '' &&  $st_code !='All') {
$totalContestedCandidatedata = DB::table('candidate_nomination_detail')
      ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
      ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
      ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
      ->where('candidate_nomination_detail.st_code', '=', $st_code)
      ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
      ->where('candidate_nomination_detail.application_status', '=', '6')
      ->where('candidate_nomination_detail.finalaccepted', '=', '1')
      ->where('candidate_nomination_detail.symbol_id', '<>', '200')
      ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
      ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
      ->groupBy("candidate_nomination_detail.st_code")
      ->get();
}else if (!empty($st_code) && $cons_no == '' &&  $st_code =='All') {  
$totalContestedCandidatedata = DB::table('candidate_nomination_detail')
      ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
      ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
      ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
      ->whereIn('candidate_nomination_detail.st_code', $permitstates)
      ->where('candidate_nomination_detail.application_status', '=', '6')
      ->where('candidate_nomination_detail.finalaccepted', '=', '1')
      ->where('candidate_nomination_detail.symbol_id', '<>', '200')
      ->where('candidate_personal_detail.cand_name', '<>', 'NOTA')
      ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
      ->groupBy("candidate_nomination_detail.st_code")
      ->get();
} else if( $st_code == '' && $cons_no == '' ) {
$totalContestedCandidatedata = DB::table('candidate_nomination_detail')
      ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
      ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
      ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
      ->where('candidate_nomination_detail.application_status', '=', '6')
      ->where('candidate_nomination_detail.finalaccepted', '=', '1')
      ->where('candidate_nomination_detail.symbol_id', '<>', '200')
      ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
      ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
      ->groupBy("candidate_nomination_detail.st_code")
      ->get();
}
//dd(DB::getQueryLog());
// dd($totalContestedCandidatedata);
  return view('admin.pc.eci.Expenditure.summary-analytical', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist,'nameSuffix' => $nameSuffix, 'count' => count($totalContestedCandidatedata)]);
} else {
return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ECI getanalyticsummary TRY CATCH ENDS HERE    
}

// end getanalyticsummary function


/**
* @author Devloped By : Niraj Kumar
* @author Devloped Date : 20-01-2020
* @author Modified By : 
* @author Modified Date : 
* @author param return getReturn candidates details By ECI fuction     
*/ 
public function getElectedcand2014(Request $request,$state, $pc) { 
try {
    if (Auth::check()) {
        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);               
        $xss = new xssClean;
        $st_code=base64_decode($xss->clean_input($state));
        $cons_no=base64_decode($xss->clean_input($pc));
        $st_code=!empty($st_code) ? $st_code : 0;
        $cons_no=!empty($cons_no) ? $cons_no : 0; 
        
        
########Code For State Wise Access By Niraj date 23-07-2019#####################
      $username=$user->officername;
      $zonestate = $this->eciexpenditureModel->getzonestate($username);
      if($zonestate->isEmpty()){
        $permitstates = '';
      }else{
        $permitstates = explode(',',$zonestate[0]->assign_state);
      }
    
      $permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;
    
        if(!empty($permitstate)){
            $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
        }else{
           $statelist = getallstate();
        }
        if(!empty($st_code)){
            $st_code=$st_code;
        }elseif(empty($st_code) && !empty($permitstate)){
            $st_code=array_values($permitstate)[0];
        }else {
            $st_code=0;
        }
     #########################Code For State Wise Access#####################
      DB::enableQueryLog();
$conditions ="";
if(!empty($st_code) && empty($cons_no)) {
 $conditions .="temp.st_code='$st_code' ";
} else if (!empty($st_code) && !empty($cons_no)) {
 $conditions .="temp.st_code='$st_code' AND temp.pc_no='$pc' ";
} else{
  $conditions ="1=1";
}
$query="SELECT temp.C_CODE,temp.st_code,temp.pc_no,temp.DATE_OF_LODGING,temp.pc_name,temp.CAND_NAME,temp.PARTYNAME,temp.understated_by_candidate,
temp.understated_by_observer,aa.source_amount,aaa.candidate_total_expense
FROM
(
SELECT m_pc.pc_no,cand.c_code,cand.DATE_OF_LODGING,win.st_code,m_pc.pc_name,cand.CAND_NAME,vm.PARTYNAME,
SUM(ex.amount_by_cand) AS 'understated_by_candidate',
SUM(ex.amount_observed) AS 'understated_by_observer'
FROM vw_vw_pcwinner win LEFT JOIN vw_vw_allcanddetails cand
ON win.st_code =cand.st_code AND win.pc_no = cand.pc_no
AND win.cand_sl_no = cand.cand_sl_no
LEFT JOIN exp_understated ex ON ex.c_code = cand.c_code
JOIN m_pc ON m_pc.st_code = win.st_code
AND m_pc.pc_no = win.pc_no
LEFT JOIN vm_m_party vm ON vm.PARTYABBRE=cand.PARTYABBRE
GROUP BY cand.c_code
)temp LEFT JOIN
(SELECT fund.c_code,
SUM(fund.amount) AS 'source_amount'
FROM vw_vw_pcwinner win LEFT JOIN vw_vw_allcanddetails cand
ON win.st_code =cand.st_code AND win.pc_no = cand.pc_no
AND win.cand_sl_no = cand.cand_sl_no
LEFT JOIN fund_source fund
ON fund.c_code = cand.c_code JOIN m_pc
ON m_pc.st_code = win.st_code
AND m_pc.pc_no = win.pc_no
GROUP BY cand.c_code
)aa ON aa.c_code = temp.c_code
LEFT JOIN
(
SELECT cand.c_code,
SUM(exp_amount) AS candidate_total_expense
FROM vw_vw_pcwinner win LEFT JOIN vw_vw_allcanddetails cand
ON win.st_code =cand.st_code AND win.pc_no = cand.pc_no
AND win.cand_sl_no = cand.cand_sl_no
LEFT JOIN cand_expense ex ON ex.c_code = cand.c_code
JOIN m_pc ON m_pc.st_code = win.st_code
AND m_pc.pc_no = win.pc_no
GROUP BY cand.c_code)aaa
ON aaa.c_code = temp.c_code where $conditions GROUP BY temp.c_code";

$electedCandList=DB::select($query);

//$electedCandList=$query->get();
//dd(DB::getQueryLog());
//dd($electedCandList);          
$count=!empty($electedCandList) ? count($electedCandList): '0';

if (!empty($_GET['exl']) && $_GET['exl']="yes") {
//////////export exel //////////////
// Initialize the array which will be passed into the Excel
// generator.
$candidateArray = []; 

// Define the Excel spreadsheet headers
$candidateArray[] = ['S.NO', 'STATE NAME','PC NO & PC NAME','CANDIDATE NAME','PARTYNAME','LODGING DATE','TOTAL RECEIVED FUND(Rs.)','TOTAL EXPENDITURE DECLARED BY CANDIDATE(Rs.)'];

// Convert each member of the returned collection into an array,
// and append it to the payments array.
$i=1;
foreach ($electedCandList as $canwise) { 
$totalexpen= !empty($canwise->candidate_total_expense) ? $canwise->candidate_total_expense : '0';
$candreceieved= !empty($canwise->source_amount) ? $canwise->source_amount : '0';

$pcdetails=getpcbypcno($canwise->st_code,$canwise->pc_no); 
$st=getstatebystatecode($canwise->st_code);
$candidateArr[$i]['S.no'] = $i;
$candidateArr[$i]['state_name'] = $st->ST_NAME;
$candidateArr[$i]['pc_no'] = $pcdetails->PC_NO.' - '.$pcdetails->PC_NAME;
$candidateArr[$i]['cand_name'] = $canwise->CAND_NAME;
$candidateArr[$i]['partyname'] = $canwise->PARTYNAME;
$candidateArr[$i]['lastlodgingdate'] = !empty($canwise->DATE_OF_LODGING)  ? date('d-m-Y',strtotime(str_replace('/', '-', $canwise->DATE_OF_LODGING))) : 'N/A';
$candidateArr[$i]['candreceieved'] =!empty($candreceieved) ? $candreceieved : '0';
$candidateArr[$i]['$totalexpen'] =$totalexpen;
$i++;
}

foreach ($candidateArr as $candidate) {
$candidateArray[] = $candidate;
}

       // Generate and return the spreadsheet
        \Excel::create('ElectedCandidateReport2014', function($excel) use ($candidateArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Elected Candidate Wise Expenditure');
            $excel->setCreator('Eci')->setCompany('Election Commission Of India');
            // Build the spreadsheet, passing in the payments array
            $excel->sheet('ElectedCandidateReport2014', function($sheet) use ($candidateArray) {
                $sheet->fromArray($candidateArray, null, 'A1', false, false);
            });
           })->download('csv');
         }
         else
         {
return view('admin.pc.eci.Expenditure.2014.electedcandidate-report', ['user_data' => $d, 'electedCandList' => $electedCandList ,
  'edetails' => $ele_details, "count" => $count,
  'st_code'=>$st_code,
  'cons_no'=>$cons_no
                ]);
    }
    } else {
        return redirect('/officer-login');
    }
} catch (Exception $ex) {
    return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC Elected Candidate TRY CATCH ENDS HERE   
}

####################end Summary Analytical Dashboard #####################################

}

// end class