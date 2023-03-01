<?php
namespace App\Http\Controllers\Expenditure;
ini_set('memory_limit', '-1');
ini_set("pcre.backtrack_limit", "2000000");
ini_set("max_execution_time", "600");
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

class EciExpenditureController extends Controller {

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
          $m_election_history = DB::connection("mysql_database_history")->table("m_election_history")->where("db_name", $DB_DATABASE)->first();
		  ################Add by niraj for exp_alter DB ###########
	    Session::put('DB_ELECTION_ID',$m_election_history->election_id);
        Session::put('DB_MONTH',$m_election_history->month);
        Session::put('DB_YEAR',$m_election_history->year);
        Session::put('DB_CONS_TYPE',$m_election_history->const_type);
        Session::put('DB_ELE_TYPE',$m_election_history->elect_type);
		################end#####################################
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


    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return dashboard By ECI fuction     
     */
    public function dashboard(Request $request) {
      //dd(DB::connection()->getDatabaseName());
        //dd($request->all());
        //PC ECI dashboard TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
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
                   $statelist = $this->commonModel->getallstate();
                }
                if(!empty($st_code)){
                    $st_code=$st_code;
                }elseif(empty($st_code) && !empty($permitstate)){
                    $st_code=array_values($permitstate)[0];
                }else {
                    $st_code=0;
                }
               
             #########################Code For State Wise Access#####################

                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

               
                $cons_no = $request->input('pc');
               
               // $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
              //  print_r($st_code).'pc'.$cons_no; die('test');
                 //echo  $st_code.'pc'.$cons_no; die;
                if (!empty($st_code) &&  empty($cons_no)) {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                           // ->whereIn('candidate_nomination_detail.st_code', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->count();
                    $totalElectedCandidate=DB::table('winning_leading_candidate') 
                            ->where('winning_leading_candidate.st_code', '=', $st_code)
                            ->count();  
                    } else if (!empty($st_code) && $cons_no != '') {
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
                } else {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            //->where('candidate_nomination_detail.st_code','=',$st_code)
                            //->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->count();
                  $totalElectedCandidate=DB::table('winning_leading_candidate')              
                            ->count();
                }
                // dd($totalContestedCandidate);
                //dd($totalContestedCandidate);
                //Get Data entry Start Count 
                $startdatacount = $this->eciexpenditureModel->gettotaldataentryStart('PC', $st_code, $cons_no);
                // dd($startdatacount);
                //Get Data entry Start Count %
                $Percent_startdataentry = $this->get_percentage($totalContestedCandidate, $startdatacount);
                //dd($Percent_startdataentry);
                //Get Data entry finalize Count 
                $finaldatacount = $this->eciexpenditureModel->gettotaldataentryFinal('PC', $st_code, $cons_no);
                
                //Get Data entry finalize Count %
                $Percent_finaldatacount = $this->get_percentage($totalContestedCandidate, $finaldatacount);

                //Get Data entry finalize Count 
                $logedaccount = $this->eciexpenditureModel->gettotallogedAccount('PC', $st_code, $cons_no);
                //Get Data entry finalize Count %
                $Percent_logedaccount = $this->get_percentage($totalContestedCandidate, $logedaccount);

                //Get Data entry finalize Count 
                $notintimeaccount = $this->eciexpenditureModel->gettotalNotinTime('PC', $st_code, $cons_no);
                //Get Data entry finalize Count %
                $Percent_notintimeaccount = $this->get_percentage($totalContestedCandidate, $notintimeaccount);


                //Get Defects in format Count  
                $formateDefectscount = $this->eciexpenditureModel->gettotalDefectformats('PC', $st_code, $cons_no);
                //Get Defects in format Count %
                $Percent_formateDefectscount = $this->get_percentage($totalContestedCandidate, $formateDefectscount);

                //Get Defects in format Count 
                $expenseunderstated = $this->eciexpenditureModel->gettotalexpenseUnderStated('PC', $st_code, $cons_no);
                //Get Defects in format Count %
                $Percent_expenseunderstated = $this->get_percentage($totalContestedCandidate, $expenseunderstated);

                //Get total fund from party
                $partyFund = $this->eciexpenditureModel->gettotalPartyfund('PC', $st_code, $cons_no);
                $otherSourcesFund = $this->eciexpenditureModel->gettotalOtherSourcesfund('PC', $st_code, $cons_no);

                $totalFund = ($partyFund->total_partyfund + $otherSourcesFund->total_otherSourcesfund);
                //Get party fund %
                $Percent_partyFund = $this->get_percentage($totalFund, $partyFund->total_partyfund);
                //Get OtherSources fund %
                $Percent_OthersourcesFund = $this->get_percentage($totalFund, $otherSourcesFund->total_otherSourcesfund);
                $all_pc = '';
             
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
                
                 //Getpartywise expenditure by eci Count %
                 $contestedpartiescount = $this->expenditureModel->gettotalcontestedparties('PC', $st_code, $cons_no);
                 $partiescountwhichgreaterthanzero = $this->expenditureModel->partieswhichexpendisgetterthanzero('PC', $st_code, $cons_no);
              

                 $contestedcandidatecount = $this->expenditureModel->getcontestedcandidate('PC', $st_code, $cons_no);

                 $candidatewisecountwhichgreaterthanzero = $this->expenditureModel->candidatewhichexpendisgetterthanzero('PC', $st_code, $cons_no);
                
                 // end here Getpartywise

                /////////////////////////////-------start notification -------------------////////          
          //shishir
           $eciscrutinycandidatecount = DB::table('expenditure_notification')
           ->leftjoin('candidate_nomination_detail', 'expenditure_notification.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
          ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
          ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
          ->where('candidate_nomination_detail.application_status', '=', '6')
          ->where('candidate_nomination_detail.finalaccepted', '=', '1')
          ->where('candidate_nomination_detail.symbol_id', '<>', '200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
           ->Where('expenditure_notification.eci_read_status', '=', '0')
          ->count();
          $request->session()->put('ecicountscrutiny', $eciscrutinycandidatecount);
         //shishir

/////////////////-----------end notification -----------------//////////////
return view('admin.pc.eci.Expenditure.dashboard', ['user_data' => $d, 'startdatacount' => $startdatacount, 'Percent_startdataentry' => $Percent_startdataentry,
'finaldatacount' => $finaldatacount, 
'Percent_finaldatacount' => $Percent_finaldatacount, 'formateDefectscount' => $formateDefectscount,
'Percent_formateDefectscount' => $Percent_formateDefectscount, 'expenseunderstated' => $expenseunderstated, 
'Percent_expenseunderstated' => $Percent_expenseunderstated, 'Percent_partyFund' => $Percent_partyFund, 
'Percent_OthersourcesFund' => $Percent_OthersourcesFund, 'edetails' => $ele_details, 'logedaccount' => $logedaccount,
'Percent_logedaccount' => $Percent_logedaccount, 'notintimeaccount' => $notintimeaccount, 'Percent_notintimeaccount' => $Percent_notintimeaccount,
'returncount'=>$returncount,
'Percent_returncount'=>$Percent_returncount,
'nonreturncount'=>$nonreturncount,
'Percent_nonreturncount'=>$Percent_nonreturncount,
'totalContestedCandidate'=>$totalContestedCandidate,
'contestedpartiescount'=>$contestedpartiescount,
'partiescountwhichgreaterthanzero'=>$partiescountwhichgreaterthanzero,
'contestedcandidatecount'=>$contestedcandidatecount,
'candidatewisecountwhichgreaterthanzero'=>$candidatewisecountwhichgreaterthanzero,
'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI dashboard TRY CATCH ENDS HERE    
    }

// end dashboard function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBydataentryStart By ECI fuction     
     */
    public function candidateListBydataentryStart(Request $request, $state, $pc) {

        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
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

                DB::enableQueryLog();
                if ($st_code == '0' && $cons_no == '0') {
                    $DataentryStartCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $DataentryStartCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get(); 
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $DataentryStartCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                // dd(DB::getQueryLog());
                //dd($DataentryStartCandList);
                return view('admin.pc.eci.Expenditure.dataentrystart-report', ['user_data' => $d, 'DataentryStartCandList' => $DataentryStartCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($DataentryStartCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListBydataentryStart TRY CATCH ENDS HERE   
    }

// end dataentry start function

    public function candidateListBydataentryStartgraph(Request $request, $state, $pc) {
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $st_code = $request->input('state');
                $cons_no = $request->input('pc');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;

                $totalState = $this->eciexpenditureModel->gettotalState('PC');


                // dd($totalcontestingcandidate);
                $data = [
                    ['Oveall summary', 'Data entry started'],
                ];

                if (count($totalState) > 0) {
                    foreach ($totalState as $item) {


                        $datestart = $this->eciexpenditureModel->gettotaldataentryStartdataeci('PC',
                                $item->state_code);




                        if (!empty($datestart[0]) && $datestart[0]->state_code == $item->state_code) {

                            $totalcontestingcandidate = $this->eciexpenditureModel->getTotalContestingcandidateeci('PC', $item->state_code);

                            $data[] = [$item->state_name, $this->get_percentage($totalcontestingcandidate, $datestart[0]->total)];
                        } else {
                            $data[] = [$item->state_name, 0];
                        }
                    }
                }
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListBydataentryStart TRY CATCH ENDS HERE   
    }

// end dataentry start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByfinalizeData By ECI fuction     
     */
    public function candidateListByfinalizeData(Request $request, $state, $pc) {

        //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

              DB::enableQueryLog();
                $xss = new xssClean;
                $st_code=base64_decode($xss->clean_input($state));
                $cons_no=base64_decode($xss->clean_input($pc));
                $st_code=!empty($st_code) ? $st_code : 0;
                $cons_no=!empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                if ($st_code == '0' && $cons_no == '0') {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->join('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->join('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->join('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
            // dd(DB::getQueryLog());
               // dd($finalCandList);
                return view('admin.pc.eci.Expenditure.finalize-report', ['user_data' => $d, 'finalCandList' => $finalCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($finalCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByfinalizeData TRY CATCH ENDS HERE   
    }

// end candidateListByfinalizeData start function

    public function candidateListByfinalizeDatagraph(Request $request, $state, $pc) {
        //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {

                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $st_code = $request->input('state');
                $cons_no = $request->input('pc');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $totalState = $this->eciexpenditureModel->gettotalState('PC');

                // dd($totalcontestingcandidate);
                $data = [
                    ['Oveall summary', 'Report Finalised'],
                ];

                if (count($totalState) > 0) {
                    foreach ($totalState as $item) {


                        $datestart = $this->eciexpenditureModel->gettotaldataentryFinaldataeci('PC',
                                $item->state_code);
                        if (!empty($datestart[0]) && $datestart[0]->state_code == $item->state_code) {

                            $totalcontestingcandidate = $this->eciexpenditureModel->getTotalContestingcandidateeci('PC', $item->state_code);

                            $data[] = [$item->state_name, $this->get_percentage($totalcontestingcandidate, $datestart[0]->total)];
                        } else {
                            $data[] = [$item->state_name, 0];
                        }
                    }
                }
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByfinalizeData TRY CATCH ENDS HERE   
    }

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBylogedaccount By ECI fuction     
     */
    public function candidateListBylogedaccount(Request $request, $state, $pc) {

        //PC ECI candidateListBylogedaccount TRY CATCH STARTS HERE
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

                if ($st_code == '0' && $cons_no == '0') {
                    $logedAccount = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.candidate_lodged_acct', '=', 'Yes')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           // ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $logedAccount = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.candidate_lodged_acct', '=', 'Yes')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           // ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $logedAccount = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.candidate_lodged_acct', '=', 'Yes')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                //dd($DataentryStartCandList);
                return view('admin.pc.eci.Expenditure.logedaccount-report', ['user_data' => $d, 'logedAccount' => $logedAccount, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($logedAccount)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBylogedaccount TRY CATCH ENDS HERE   
    }

// end candidateListBylogedaccount start function

    public function candidateListBylogedaccountgraph(Request $request, $state, $pc) {
        //PC ECI candidateListBylogedaccount TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $st_code = $request->input('state');
                $cons_no = $request->input('pc');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $totalState = $this->eciexpenditureModel->gettotalState('PC');

                // dd($totalcontestingcandidate);
                $data = [
                    ['Oveall summary', 'Account Lodged'],
                ];

                if (count($totalState) > 0) {
                    foreach ($totalState as $item) {


                        $datestart = $this->eciexpenditureModel->gettotaldataentryFinaldataeci('PC',
                                $item->state_code);
                        if (!empty($datestart[0]) && $datestart[0]->state_code == $item->state_code) {

                            $totalcontestingcandidate = $this->eciexpenditureModel->getTotalContestingcandidateeci('PC', $item->state_code);

                            $data[] = [$item->state_name, $this->get_percentage($totalcontestingcandidate, $datestart[0]->total)];
                        } else {
                            $data[] = [$item->state_name, 0];
                        }
                    }
                }
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
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBynotintime By ECI fuction     
     */
    public function candidateListBynotintime(Request $request, $state, $pc) {

        //PC ECI candidateListBynotintime TRY CATCH STARTS HERE
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
                $notinTime = [];
                if ($st_code == '0' && $cons_no == '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.account_lodged_time', '=', 'No')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('expenditure_reports.account_lodged_time', '=', 'No')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.account_lodged_time', '=', 'No')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                
                //dd($DataentryStartCandList);
                return view('admin.pc.eci.Expenditure.notintime-report', ['user_data' => $d, 'notinTime' => $notinTime, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($notinTime)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListBynotintime TRY CATCH ENDS HERE   
    }

// end candidateListBynotintime start function

    public function candidateListBynotintimegraph(Request $request, $state, $pc) {
        //PC ECI candidateListBynotintime TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $xss = new xssClean;
                $st_code=base64_decode($xss->clean_input($state));
                $cons_no=base64_decode($xss->clean_input($pc));
                $st_code=!empty($st_code) ? $st_code : 0;
                $cons_no=!empty($cons_no) ? $cons_no : 0;

                $totalState = $this->eciexpenditureModel->gettotalState('PC');

                // dd($totalcontestingcandidate);
                $data = [
                    ['Oveall summary', 'Not in time'],
                ];

                if (count($totalState) > 0) {
                    foreach ($totalState as $item) {


                        $datestart = $this->eciexpenditureModel->gettotalNotinTimeeci('PC',
                                $item->state_code);
                        if (!empty($datestart[0]) && $datestart[0]->state_code == $item->state_code) {

                            $totalcontestingcandidate = $this->eciexpenditureModel->getTotalContestingcandidateeci('PC', $item->state_code);

                            $data[] = [$item->state_name, $this->get_percentage($totalcontestingcandidate, $datestart[0]->total)];
                        } else {
                            $data[] = [$item->state_name, 0];
                        }
                    }
                }
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListBynotintime TRY CATCH ENDS HERE   
    }

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBydataentryStart By ECI fuction     
     */
    public function candidateListByformatedefects(Request $request, $state, $pc) {

        //PC ECI candidateListByformatedefects TRY CATCH STARTS HERE
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
                $formateDefects=0;

                if ($st_code == '0' && $cons_no == '0') {
                    $formateDefects = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.rp_act', '=', 'No')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->groupBy('candidate_nomination_detail.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $formateDefects = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.rp_act', '=', 'No')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           // ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code == '0' && $cons_no != '0') {
                    $formateDefects = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.rp_act', '=', 'No')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->get();
                }

                //dd($DataentryStartCandList);
                return view('admin.pc.eci.Expenditure.formatedefects-report', ['user_data' => $d, 'formateDefects' => $formateDefects, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($formateDefects)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByformatedefects TRY CATCH ENDS HERE   
    }

// end candidateListByformatedefects start function

    public function candidateListByformatedefectsgraph(Request $request, $state, $pc) {
        //PC ECI candidateListByformatedefects TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $st_code = $request->input('state');
                $cons_no = $request->input('pc');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;

                $totalState = $this->eciexpenditureModel->gettotalState('PC');

                // dd($totalcontestingcandidate);
                $data = [
                    ['Oveall summary', 'Defects in Format'],
                ];

                if (count($totalState) > 0) {
                    foreach ($totalState as $item) {


                        $datestart = $this->eciexpenditureModel->gettotalformatedefectsdata('PC',
                                $item->state_code);
                        if (!empty($datestart[0]) && $datestart[0]->state_code == $item->state_code) {

                            $totalcontestingcandidate = $this->eciexpenditureModel->getTotalContestingcandidateeci('PC', $item->state_code);

                            $data[] = [$item->state_name, $this->get_percentage($totalcontestingcandidate, $datestart[0]->total)];
                        } else {
                            $data[] = [$item->state_name, 0];
                        }
                    }
                }
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
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByronotagree By ECI fuction     
     */
    public function candidateListByronotagree(Request $request, $state, $pc) {
        //PC ECI candidateListByronotagree TRY CATCH STARTS HERE
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

                $DataentryStartCandList = DB::table('expenditure_reports')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        // ->where('expenditure_reports.ST_CODE','=',$st_code)
                        // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                //dd($DataentryStartCandList);
                return view('admin.pc.eci.Expenditure.ronotagree-report', ['user_data' => $d, 'DataentryStartCandList' => $DataentryStartCandList, 'edetails' => $ele_details]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByronotagree TRY CATCH ENDS HERE   
    }

// end candidateListByronotagree start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByunderstatedexpense By ECI fuction     
     */
    public function candidateListByunderstatedexpense(Request $request, $state, $pc) {

        //PC ECI candidateListByunderstatedexpense TRY CATCH STARTS HERE
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

                if ($st_code == '0' && $cons_no == '0') {
                    $expenseunderstated = DB::table('expenditure_understated')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            // ->where('expenditure_understated.ST_CODE','=',$st_code)
                            // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                            ->where('expenditure_understated.page_no_observation', '=', "No")
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $expenseunderstated = DB::table('expenditure_understated')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->where('expenditure_understated.ST_CODE', '=', $st_code)
                            ->where('expenditure_understated.page_no_observation', '=', "No")
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $expenseunderstated = DB::table('expenditure_understated')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->where('expenditure_understated.ST_CODE', '=', $st_code)
                            ->where('expenditure_understated.constituency_no', '=', $cons_no)
                            ->where('expenditure_understated.page_no_observation', '=', "No")
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                }

                //dd($DataentryStartCandList);
                return view('admin.pc.eci.Expenditure.expenseunderstated-report', ['user_data' => $d, 'expenseunderstated' => $expenseunderstated, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($expenseunderstated)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByunderstatedexpense TRY CATCH ENDS HERE   
    }

// end candidateListByunderstatedexpense start function

    public function candidateListByunderstatedexpensegraph(Request $request, $state, $pc) {
        //PC ECI candidateListByunderstatedexpense TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $st_code = $request->input('state');
                $cons_no = $request->input('pc');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;

                $totalState = $this->eciexpenditureModel->gettotalState('PC');

                // dd($totalcontestingcandidate);
                $data = [
                    ['Oveall summary', 'Expense understated'],
                ];

                if (count($totalState) > 0) {
                    foreach ($totalState as $item) {


                        $datestart = $this->eciexpenditureModel->gettotalexpenseUnderStateddataeci('PC',
                                $item->state_code);
                        if (!empty($datestart[0]) && $datestart[0]->state_code == $item->state_code) {

                            $totalcontestingcandidate = $this->eciexpenditureModel->getTotalContestingcandidateeci('PC', $item->state_code);

                            $data[] = [$item->state_name, $this->get_percentage($totalcontestingcandidate, $datestart[0]->total)];
                        } else {
                            $data[] = [$item->state_name, 0];
                        }
                    }
                }
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByunderstatedexpense TRY CATCH ENDS HERE   
    }

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBydataentrydefects By ECI fuction     
     */
    public function candidateListBydataentrydefects(Request $request, $state, $pc) {

        //PC ECI candidateListBydataentrydefects TRY CATCH STARTS HERE
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

                if ($st_code == '0' && $cons_no == '0') {
                    $dataentrydefectsCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $dataentrydefectsCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $dataentrydefectsCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                //dd($DataentryStartCandList);
                return view('admin.pc.eci.Expenditure.dataentrydefect-report', ['user_data' => $d, 'dataentrydefectsCandList' => $dataentrydefectsCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($dataentrydefectsCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListBydataentrydefects TRY CATCH ENDS HERE   
    }

// end candidateListBydataentrydefects start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBypartyfund By ECI fuction     
     */
    public function candidateListBypartyfund(Request $request, $state, $pc) {

        //PC ROPC candidateListBypartyfund TRY CATCH STARTS HERE
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

                if ($st_code == '0' && $cons_no == '0') {
                    $partyfund = DB::table('expenditure_fund_parties')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id')
                            //->select(DB::raw('IFNULL((political_fund_cash + political_fund_checque + political_fund_kind),0) AS partyfund'))
                            ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'expenditure_fund_parties.candidate_id')
                            ->select('candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_parties.*', 'm_party.PARTYNAME','expenditure_reports.final_by_ro')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_fund_parties.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $partyfund = DB::table('expenditure_fund_parties')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id')
                            //->select(DB::raw('IFNULL((political_fund_cash + political_fund_checque + political_fund_kind),0) AS partyfund'))
                            ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'expenditure_fund_parties.candidate_id')
                            ->select('candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_parties.*', 'm_party.PARTYNAME','expenditure_reports.final_by_ro')
                            ->where('expenditure_fund_parties.ST_CODE', '=', $st_code)
                            // ->where('expenditure_fund_parties.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_fund_parties.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $partyfund = DB::table('expenditure_fund_parties')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id')
                            //->select(DB::raw('IFNULL((political_fund_cash + political_fund_checque + political_fund_kind),0) AS partyfund'))
                            ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'expenditure_fund_parties.candidate_id')
                            ->select('candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_parties.*', 'm_party.PARTYNAME','expenditure_reports.final_by_ro')
                            ->where('expenditure_fund_parties.ST_CODE', '=', $st_code)
                            ->where('expenditure_fund_parties.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_fund_parties.candidate_id')
                            ->get();
                }
                // dd($partyfund);
                return view('admin.pc.eci.Expenditure.partyfund-report', ['user_data' => $d, 'partyfund' => $partyfund, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($partyfund)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBypartyfund TRY CATCH ENDS HERE   
    }

// end candidateListBypartyfund start function

    public function candidateListBypartyfundgraph(Request $request, $state, $pc) {
        //PC ROPC candidateListBypartyfund TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $st_code = $request->input('state');
                $cons_no = $request->input('pc');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $totalState = $this->eciexpenditureModel->gettotalState('PC');

                // dd($totalcontestingcandidate);
                $data = [
                    ['Oveall summary', 'Fund From Party'],
                ];

                if (count($totalState) > 0) {
                    foreach ($totalState as $item) {


                        $datestart = $this->eciexpenditureModel->gettotalPartyfunddataeci('PC',
                                $item->state_code);
                        $otherfund = $this->eciexpenditureModel->gettotalOtherSourcesfunddata('PC',
                                $item->state_code);
                        if (!empty($datestart[0]) && $datestart[0]->state_code == $item->state_code) {

                            $totalcontestingcandidate = $this->eciexpenditureModel->getTotalContestingcandidateeci('PC', $item->state_code);

                            $data[] = [$item->state_name, $this->get_percentage($totalcontestingcandidate, $datestart[0]->total)];
                        } else {
                            $data[] = [$item->state_name, 0];
                        }
                    }
                }
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBypartyfund TRY CATCH ENDS HERE   
    }

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByothersfund By ECI fuction     
     */
    public function candidateListByothersfund(Request $request, $state, $pc) {

        //dd($request->all());
        //PC ECI candidateListByothersfund TRY CATCH STARTS HERE
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

                
                  $query = DB::table('expenditure_fund_source')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                           ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'expenditure_fund_source.candidate_id')
              
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA');
                            
                
                if ($st_code != '0' && $cons_no == '0') {
                
                           
                            $query->where('expenditure_fund_source.ST_CODE', '=', $st_code);
                
                } elseif ($st_code != '0' && $cons_no != '0') {
                            $query->where('expenditure_fund_source.ST_CODE', '=', $st_code);
                            $query->where('expenditure_fund_source.constituency_no', '=', $cons_no);
                
                }
                 $query->select('candidate_personal_detail.cand_name',
                         'candidate_personal_detail.candidate_id',
                         'candidate_personal_detail.candidate_father_name',
                         DB::raw('IFNULL(sum(expenditure_fund_source.other_source_amount),0 )as other_source_amount'),
                         'm_party.CCODE','m_party.PARTYNAME','expenditure_fund_source.ST_CODE',
                         'expenditure_fund_source.constituency_no',
                         'expenditure_reports.final_by_ro');
                $query->groupBy('expenditure_fund_source.candidate_id');
                $otherfund=$query->get();              
              
                return view('admin.pc.eci.Expenditure.otherfund-report', ['user_data' => $d, 'otherfund' => $otherfund, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($otherfund)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByothersfund TRY CATCH ENDS HERE   
    }

// end candidateListByothersfund start function

    public function candidateListByothersfundgraph(Request $request, $state, $pc) { //dd($request->all());
        //PC ECI candidateListByothersfund TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $st_code = $request->input('state');
                $cons_no = $request->input('pc');
                $st_code = !empty($st_code) ? $st_code : 0;
                $totalState = $this->eciexpenditureModel->gettotalState('PC');

                // dd($totalcontestingcandidate);
                $data = [
                    ['Oveall summary', 'Taken funds from other sources'],
                ];

                if (count($totalState) > 0) {
                    foreach ($totalState as $item) {


                        $datestart = $this->eciexpenditureModel->gettotalPartyfunddataeci('PC',
                                $item->state_code);
                        $otherfund = $this->eciexpenditureModel->gettotalOtherSourcesfunddata('PC',
                                $item->state_code);
                        if (!empty($datestart[0]) && $datestart[0]->state_code == $item->state_code) {

                            $totalcontestingcandidate = $this->eciexpenditureModel->getTotalContestingcandidateeci('PC', $item->state_code);

                            $data[] = [$item->state_name, $this->get_percentage($totalcontestingcandidate, $datestart[0]->total)];
                        } else {
                            $data[] = [$item->state_name, 0];
                        }
                    }
                }
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByothersfund TRY CATCH ENDS HERE   
    }

// end candidateListByothersfund start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByexeedceiling By ECI fuction     
     */
    public function candidateListByexeedceiling(Request $request, $state, $pc) {
        //PC ECI candidateListByexeedceiling TRY CATCH STARTS HERE
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

                $DataentryStartCandList = DB::table('expenditure_reports')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        //->where('expenditure_reports.ST_CODE','=',$st_code)
                        //->where('expenditure_reports.constituency_no','=',$cons_no) 
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                //dd($DataentryStartCandList);
                return view('admin.pc.eci.Expenditure.exceedceiling-report', ['user_data' => $d, 'DataentryStartCandList' => $DataentryStartCandList, 'edetails' => $ele_details]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByexeedceiling TRY CATCH ENDS HERE   
    }

// end candidateListByexeedceiling start function
#########################Start status dashboard by Niraj 16-05-2019###################

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 16-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return statusdashboard By ECI fuction     
     */
    public function statusdashboard(Request $request) {
        //dd($request->all());
//PC ECI statusdashboard TRY CATCH STARTS HERE
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
                   $statelist = $this->commonModel->getallstate();
                }
                if(!empty($st_code)){
                    $st_code=$st_code;
                }elseif(empty($st_code) && !empty($permitstate)){
                    $st_code=array_values($permitstate)[0];
                }else {
                    $st_code=0;
                }
               
             #########################Code For State Wise Access#####################
			 
		$cons_no = $request->input('pc');
		$st_code = !empty($st_code) ? $st_code : 0;
		$cons_no = !empty($cons_no) ? $cons_no : 0;
		// echo  $st_code.'pc'.$cons_no; die;
		$query=DB::table('candidate_nomination_detail')
					->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
					->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
					->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
					->where('candidate_nomination_detail.application_status', '=', '6')
					->where('candidate_nomination_detail.finalaccepted', '=', '1')
					->where('candidate_nomination_detail.symbol_id', '<>', '200')
					->Where('candidate_personal_detail.cand_name', '<>', 'NOTA');
					if (!empty($st_code && $cons_no == '')) {
						$query->where('candidate_nomination_detail.st_code', '=', $st_code);
						$totalContestedCandidate = $query->count();
					} else if (!empty($st_code) && $cons_no != '') {
						$query->where('candidate_nomination_detail.st_code', '=', $st_code);
						$query->where('candidate_nomination_detail.pc_no', '=', $cons_no);
						$totalContestedCandidate =  $query->count();
					} else {
						$totalContestedCandidate = $query->count();
					}

		//Get Data entry Start Count 
		$startdatacount = $this->eciexpenditureModel->gettotaldataentryStart('PC', $st_code, $cons_no);
		//Get Data entry Start Count %
		$Percent_startdatacount = $this->get_percentage($totalContestedCandidate, $startdatacount);

		// Get Pending Data Count 
		$pendingdatacount = $totalContestedCandidate - $startdatacount;
		//dd($pendingdatacount);
		//Get Data entry Start Count %
		$Percent_pendingdatacount = $this->get_percentage($totalContestedCandidate, $pendingdatacount);

		//get partially pending data count
		$partiallypending = $this->eciexpenditureModel->gettotalfinalbyDEO('PC', $st_code, $cons_no);
		if($partiallypending >= 0){
		$partiallypendingcount = ($totalContestedCandidate-$partiallypending);  }
		//Get Data entry Start Count %
		$Percent_partiallypendingcount = $this->get_percentage($totalContestedCandidate, $partiallypendingcount);

		//Get Data entry finalize Count 
		$finaldatacount = $this->eciexpenditureModel->gettotaldataentryFinal('PC', $st_code, $cons_no);
		//Get Data entry finalize Count %
		$Percent_finaldatacount = $this->get_percentage($totalContestedCandidate, $finaldatacount);

		//get partially pending data count
		$defaulter = $this->eciexpenditureModel->getdefaulter('PC', $st_code, $cons_no);
		if (empty($defaulter))
			$defaulter = [];
		//dd($defaulter);
		$defaultercount = count($defaulter);
		//Get Data entry Start Count %
		$Percent_defaultercount = $this->get_percentage($totalContestedCandidate, $defaultercount);

		

		//Get Data entry finalize Count 
		$finalbyecicount = $this->eciexpenditureModel->gettotalfinalbyeci('PC', $st_code, $cons_no);
		//Get Data entry finalize Count %
		$Percent_finalbyecicount = $this->get_percentage($totalContestedCandidate, $finalbyecicount);
	  //Get noticeatceocount Count 
	 $noticeatceocount = $this->eciexpenditureModel->gettotalnoticeatCEO('PC', $st_code, $cons_no);
	
	 //Get noticeatceocount  %
	 $Percent_noticeatceocount = $this->get_percentage($totalContestedCandidate, $noticeatceocount);
	
	  //Get noticeatdeocount Count 
	 $noticeatdeocount = $this->eciexpenditureModel->gettotalnoticeatDEO('PC', $st_code, $cons_no);
	
	 //Get noticeatdeocount Count %
	 $Percent_noticeatdeocount = $this->get_percentage($totalContestedCandidate, $noticeatdeocount);
	 
	  $finalbyDEO=$this->eciexpenditureModel->gettotalfinalbyDEO('PC',$st_code,$cons_no);
     $finalcompletedcount=$this->eciexpenditureModel->gettotalCompletedbyEci('PC',$st_code,$cons_no);
	 $disqualifiedcount=$this->eciexpenditureModel->gettotalDisqualifiedbyEci('PC',$st_code,$cons_no);
       
	   //pending at CEO	
		if($finalbyDEO >=  0 && $finalbyecicount >=0 && $finalcompletedcount >=0){
		 $finalbyceocount = $finalbyDEO-($finalbyecicount + $finalcompletedcount + $disqualifiedcount);
		}
 
		//Get Data entry finalize Count 
	//	$finalbyceocount = $this->eciexpenditureModel->gettotalfinalbyceo('PC', $st_code, $cons_no);
		   //dd($finalbyceocount);
		//Get Data entry finalize Count %
		$Percent_finalbyceocount = $this->get_percentage($totalContestedCandidate, $finalbyceocount);

  return view('admin.pc.eci.Expenditure.statusdashboard', ['user_data' => $d, 'startdatacount' => $startdatacount, 'Percent_startdatacount' => $Percent_startdatacount, 'pendingdatacount' => $pendingdatacount, 'Percent_finaldatacount' => $Percent_finaldatacount, 'finaldatacount' => $finaldatacount, 'Percent_pendingdatacount' => $Percent_pendingdatacount, 'partiallypendingcount' => $partiallypendingcount, 'Percent_partiallypendingcount' => $Percent_partiallypendingcount, 'defaultercount' => $defaultercount, 'Percent_defaultercount' => $Percent_defaultercount, 'finalbyceocount' => $finalbyceocount, 'Percent_finalbyceocount' => $Percent_finalbyceocount, 'finalbyecicount' => $finalbyecicount, 'Percent_finalbyecicount' => $Percent_finalbyecicount, 'noticeatceocount' => $noticeatceocount, 'Percent_noticeatceocount' => $Percent_noticeatceocount, 'noticeatdeocount' => $noticeatdeocount, 'Percent_noticeatdeocount' => $Percent_noticeatdeocount, 'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist]);

	} else {
		return redirect('/officer-login');
	}
} catch (Exception $ex) {
	return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ECI dashboard TRY CATCH ENDS HERE         
    }

// end dashboard function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 16-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getpendingcandidateList By ECI fuction     
     */
    public function getpendingcandidateList(Request $request, $state, $pc) {
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
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

                DB::enableQueryLog();
                if ($st_code == '0' && $cons_no == '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            //->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $pendingCandList = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    // ->where('candidate_nomination_detail.st_code','=',$st_code)
                                    // ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
									// ->groupBy('candidate_nomination_detail.candidate_id')
									->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $pendingCandList = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
									// ->groupBy('candidate_nomination_detail.candidate_id')
									->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                           // ->groupBy('expenditure_reports.candidate_id')
                            ->get();
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
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
									//->groupBy('candidate_nomination_detail.candidate_id')
									->get();
                }
                // dd(DB::getQueryLog());
                return view('admin.pc.eci.Expenditure.pending-report', ['user_data' => $d, 'pendingCandList' => $pendingCandList, 'edetails' => $ele_details, 'count' => count($pendingCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI pending candidate list TRY CATCH ENDS HERE   
    }

// end pending dataentry function

    public function getpendingcandidateListgraph(Request $request, $state, $pc) {
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
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

                $pending = $this->eciexpenditureModel->getTotalContestingcandidateecibystate('PC', $st_code);

                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : 'All';
                $candiatePcName = "All";
                $data = [
                    ['Oveall summary', 'Pending / Not filed'],
                ];
                if (count($pending) > 0) {
                    foreach ($pending as $item) {
                        $entryreport = $this->eciexpenditureModel->getTotalreprotcandidateecibystate('PC', $item->state_code);


                        if (count($entryreport) > 0) {

                            foreach ($entryreport as $item2) {
                                if (!empty($item2) && $item2->state_id == $item->state_id) {
                                    $data[] = [$item->state_name, $this->get_percentage($item->total, $item2->total)];
                                }
                            }
                        } else {
                            $data[] = [$item->state_name, 0];
                        }
                    }
                }
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI pending candidate list TRY CATCH ENDS HERE   
    }

// end pending dataentry function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 16-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getpartiallypendingcandidateList By ECI fuction     
     */
    public function getpartiallypendingcandidateList(Request $request, $state, $pc) {
        //PC ECI candidateListBydataentryStart TRY CATCH STARTS HERE
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

                DB::enableQueryLog();
                if ($st_code == '0' && $cons_no == '0') {
					$EcifinalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*','candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                  
                     $partiallyCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->select('candidate_personal_detail.candidate_id','candidate_nomination_detail.ST_CODE as ST_CODE','candidate_nomination_detail.pc_no as constituency_no','candidate_nomination_detail.created_at', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('candidate_nomination_detail.candidate_id')
                        ->get();
							
                   
                } elseif ($st_code != '0' && $cons_no == '0') {
                   $EcifinalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
							 ->where('candidate_nomination_detail.st_code', '=', $st_code)
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
                  
                     $partiallyCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
							 ->select('candidate_personal_detail.candidate_id','candidate_nomination_detail.ST_CODE as ST_CODE','candidate_nomination_detail.pc_no as constituency_no','candidate_nomination_detail.created_at',  'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                           // ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('candidate_nomination_detail.candidate_id')
                        ->get();
							
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $EcifinalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
							  ->where('candidate_nomination_detail.st_code', '=', $st_code)
                              ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
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
                  
                     $partiallyCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
							 ->select('candidate_personal_detail.candidate_id','candidate_nomination_detail.ST_CODE as ST_CODE','candidate_nomination_detail.pc_no as constituency_no','candidate_nomination_detail.created_at',  'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('candidate_nomination_detail.candidate_id')
                        ->get();
							
                }
                // dd(DB::getQueryLog());
                return view('admin.pc.eci.Expenditure.partiallypending-report', ['user_data' => $d, 'partiallyCandList' => $partiallyCandList, 'edetails' => $ele_details, 'count' => count($partiallyCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI pending candidate list TRY CATCH ENDS HERE   
    }

// end dataentry start function

    public function getpartiallypendingcandidateListgraph(Request $request, $state, $pc) {
        //PC ECI candidateListBydataentryStart TRY CATCH STARTS HERE ->where('finalized_status', '0')

        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                $st_code = $state;
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = $pc;
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                $pending = $this->eciexpenditureModel->getTotalContestingcandidateecibystate('PC', $st_code);

                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : 'All';
                $candiatePcName = "All";
                $data = [
                    ['Oveall summary', 'Partially Pending'],
                ];


                if (count($pending) > 0) {
                    foreach ($pending as $item) {
                        $entryreport = $this->eciexpenditureModel->getTotalpartialcandidateecibystate('PC', $item->state_code);


                        if (count($entryreport) > 0) {

                            foreach ($entryreport as $item2) {
                                if (!empty($item2) && $item2->state_id == $item->state_id) {
                                    $data[] = [$item->state_name, $this->get_percentage($item->total, $item2->total)];
                                }
                            }
                        } else {
                            $data[] = [$item->state_name, 0];
                        }
                    }
                }
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI pending candidate list TRY CATCH ENDS HERE     
    }

// end dataentry start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 16-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getpendingcandidateList By ECI fuction     
     */
    public function getdefaultercandidateList(Request $request, $state, $pc) {

        //PC ECI defaulter TRY CATCH STARTS HERE
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

                DB::enableQueryLog();
                if ($st_code == '0' && $cons_no == '0') {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<', 'totalcandamnt')
                            //->where('expenditure_understated.ST_CODE','=',$st_code)
                            // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<', 'totalcandamnt')
                            ->where('expenditure_understated.ST_CODE', '=', $st_code)
                            // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<', 'totalcandamnt')
                            ->where('expenditure_understated.ST_CODE', '=', $st_code)
                            ->where('expenditure_understated.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                }
                // dd(DB::getQueryLog());
                return view('admin.pc.eci.Expenditure.defaulter-report', ['user_data' => $d, 'defaulterCandList' => $defaulterCandList, 'edetails' => $ele_details, 'count' => count($defaulterCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI defaulter list TRY CATCH ENDS HERE   
    }

// end defaulter start function

    public function getdefaultercandidateListgraph(Request $request, $state, $pc) {

        //PC ECI defaulter TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                $st_code = $state;
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = $pc;
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                $totalcontestingcandidate = $this->eciexpenditureModel->getTotalContestingcandidateecibystate('PC', $st_code);

                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : 'All';
                $candiatePcName = "All";
                $data = [
                    ['Oveall summary', 'Partially Pending'],
                ];


                if (count($totalcontestingcandidate) > 0) {
                    foreach ($totalcontestingcandidate as $item) {
                        $defualtreport = $this->eciexpenditureModel->gettotalDefaulterreports('PC', $item->state_code);


                        if (count($defualtreport) > 0) {

                            foreach ($defualtreport as $item2) {
                                if (!empty($item2) && $item2->state_id == $item->state_id) {
                                    $data[] = [$item->state_name, $this->get_percentage($item->total, $item2->total)];
                                }
                            }
                        } else {
                            $data[] = [$item->state_name, 0];
                        }
                    }
                }
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI defaulter list TRY CATCH ENDS HERE   
    }

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 18-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByfiledData By ECI fuction     
     */
    public function candidateListByfiledData(Request $request, $state, $pc) {
        //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
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

                if ($st_code == '0' && $cons_no == '0') {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            // ->where('expenditure_reports.finalized_status','=','1') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                //dd($DataentryStartCandList);
                return view('admin.pc.eci.Expenditure.filed-report', ['user_data' => $d, 'finalCandList' => $finalCandList, 'edetails' => $ele_details, 'count' => count($finalCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByfiledData TRY CATCH ENDS HERE   
    }

// end candidateListByfiledData start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListfinalbyCEO By ECI fuction     
     */
    public function candidateListfinalbyCEO(Request $request, $state, $pc) {
        //PC ROPC candidateListfinalbyCEO TRY CATCH STARTS HERE
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

                 $candidate_id=[];
                if ($st_code == '0' && $cons_no == '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
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
                             //->where('expenditure_reports.ST_CODE', '=', $st_code)
                             //->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                          //  ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            // ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            // ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
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
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                            ->get();
                }

                //dd($finalbyceoCandList);
                return view('admin.pc.eci.Expenditure.finalbyceo-report', ['user_data' => $d, 'finalbyceoCandList' => $finalbyceoCandList, 'edetails' => $ele_details, 'count' => count($finalbyceoCandList)]);
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
     * @author Devloped Date : 21-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListfinalbyECI By ECI fuction     
     */
    public function candidateListfinalbyECI(Request $request, $state, $pc) {
        //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
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

                if ($st_code == '0' && $cons_no == '0') {
                    $finalbyeciCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where(function($query) {
							 $query->whereNull('expenditure_reports.final_action');
							  $query->orwhere('expenditure_reports.final_action', '=','');
							   })
                           // ->where('expenditure_reports.final_by_eci', '1')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $finalbyeciCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                            // ->where('expenditure_notification.eci_action','0')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                             ->where(function($query) {
							 $query->whereNull('expenditure_reports.final_action');
							  $query->orwhere('expenditure_reports.final_action', '=','');
							   })
                           // ->where('expenditure_reports.final_by_eci', '1')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $finalbyeciCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no','expenditure_reports.last_date_prescribed_acct_lodge', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            //->where('expenditure_notification.eci_action','0')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->where('expenditure_reports.final_by_eci', '1')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($query) {
							 $query->whereNull('expenditure_reports.final_action');
							  $query->orwhere('expenditure_reports.final_action', '=','');
							   })
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                //dd($DataentryStartCandList);
                return view('admin.pc.eci.Expenditure.finalbyeci-report', ['user_data' => $d, 'finalbyeciCandList' => $finalbyeciCandList, 'edetails' => $ele_details, 'count' => count($finalbyeciCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListfinalbyECI TRY CATCH ENDS HERE   
    }

// end candidateListfinalbyECI start function
########################End status dashboard by Niraj 16-05-2019 #####################
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
                   $statelist = $this->commonModel->getallstate();
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
                } else if ( $st_code == '' && $cons_no == '' ) {
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
                    if($nameSuffix=='mis-officer'){
                        return view('admin.pc.eci.Expenditure.mis-officer', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist,  'count' => count($totalContestedCandidatedata)]);

                    }else{
                        return view('admin.pc.eci.Expenditure.officer-report', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist,  'count' => count($totalContestedCandidatedata)]);
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
                   $statelist = $this->commonModel->getallstate();
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
                // echo  $st_code.'pc'.$cons_no; die;
                // dd($totalContestedCandidate);

                $cur_time = Carbon::now();

                \Excel::create('EciMISReportExcel_' . '_' . $cur_time, function($excel) use($st_code, $cons_no,$permitstates) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no,$permitstates) {

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
                } else if ( $st_code == '' && $cons_no == '' ) {
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
                        foreach ($totalContestedCandidatedata as $key => $listdata) {
                           
                             //get finalby DEO count
                            $finalbyDEO= $this->eciexpenditureModel->gettotalfinalbyDEO('PC',$listdata->st_code,$cons_no);
                           // $TotalFinalByDEO += $finalbyDEO;
                           
                            //get partially pending data count
                          //  $pendingatRO = $this->eciexpenditureModel->gettotalpartiallypending('PC', $listdata->st_code, $cons_no);
                            //Get pendingatCEO Count 
                            //$pendingatCEO = $this->eciexpenditureModel->gettotalfinalbyceo('PC', $listdata->st_code, $cons_no);
							
							
                            //Get pendingatECI Count 
                            $pendingatECI = $this->eciexpenditureModel->gettotalfinalbyeci('PC', $listdata->st_code, $cons_no);
                           
                            //Get filedcount Count 
                            $filedcount = $this->eciexpenditureModel->gettotaldataentryStart('PC', $listdata->st_code, $cons_no);
                           
                            // Get Pending Data Count 
                            $notfiledcount= $listdata->totalcandidate - $filedcount;
                           

                            //Get noticeatDEOCount Count 
                            $noticeatDEOCount = $this->eciexpenditureModel->gettotalnoticeatDEO('PC', $listdata->st_code, $cons_no);

                            //Get noticeatCEOCount Count 
                            $noticeatCEOCount = $this->eciexpenditureModel->gettotalnoticeatCEO('PC', $listdata->st_code, $cons_no);

                            //Get finalcompletedcount at ECI Count 
                            $finalcompletedcount = $this->eciexpenditureModel->gettotalCompletedbyEci('PC', $listdata->st_code, $cons_no);

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
							 if($pendingatCEO >= 0) { $TotalPendingatCEO += $pendingatCEO; }
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
                        $totalvalues = array('Total',$Totalpc, $TotalUsers, $TotalFinalByDEO, $TotalPendingatRO, $TotalPendingatCEO,$TotalPendingatECI,$Totalfinalcompletedcount,$TotalDEONotice,$TotalCEONotice);
                        // print_r($totalvalues);die;
                        array_push($arr, $totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'State Name', 'Total PC','Total Candidate','Finalise By DEO', 'Pending At DEO', 'Pending At CEO','Pending At ECI','Closed/Disqualified/Case Dropped','Notice At DEO','Notice At CEO'
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
                   $statelist = $this->commonModel->getallstate();
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
                } else if ( $st_code == '' && $cons_no == '' ) {
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

                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.pc.eci.Expenditure.mis-officerPDFhtml', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata,'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist]);    
                return $pdf->download('EciOfficerMISPdf_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
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
     * @author Devloped Date : 28-05-19
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
                $cons_no = !empty($cons_no) ? $cons_no : '';
                 //echo $st_code.'pc'.$cons_no; die;
                DB::enableQueryLog();


                 #########################Code For State Wise Access By Niraj date 23-07-2019#####################
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
                   $statelist = $this->commonModel->getallstate();
                }
                if(!empty($st_code)){
                    $st_code=$st_code;
                }elseif(empty($st_code) && !empty($permitstate)){
                    $st_code=array_values($permitstate)[0];
                }else {
                    $st_code=0;
                }
               
             #########################Code For State Wise Access#####################

                if (!empty($st_code) && $cons_no == '') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            //->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->count();
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                            //->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no != '') {
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
                            // ->count();
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                            //->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
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
                            ->get();
                }
                //dd(DB::getQueryLog());
                // dd($totalContestedCandidate);
				 
                return view('admin.pc.eci.Expenditure.candidate-report', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist,'count' => count($totalContestedCandidatedata)]);
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
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
				
                $cur_time = Carbon::now();
				DB::enableQueryLog();
                \Excel::create('ECICandidateMISExcel_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                    if (!empty($st_code) && $cons_no == '') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            //->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->count();
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                            //->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                     } else if (!empty($st_code) && $cons_no != '') {
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
                            // ->count();
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                            //->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
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
                            ->get();
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
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                if (!empty($st_code && $cons_no == '')) {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            //->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->count();
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                            ->orderBy("candidate_nomination_detail.pc_no")
                            ->get();
                } else if (!empty($st_code) && $cons_no != '') {
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
                            // ->count();
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                            ->orderBy("candidate_nomination_detail.pc_no")
                            ->get();
                } else {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
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
                            ->orderBy("candidate_nomination_detail.pc_no")
                            ->get();
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
     * @author Devloped Date : 28-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getcandidateListpendingatRO By ECI fuction     
     */
    public function getcandidateListpendingatRO(Request $request, $state, $pc) {
        //PC ECI candidateListBydataentryStart TRY CATCH STARTS HERE
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
                DB::enableQueryLog();
                $candidate_id=array();
     if($st_code == '0' && $cons_no == '0') { 
       /* $getcandidateListfinalbyECI = DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->select('expenditure_reports.candidate_id')
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('expenditure_reports.date_of_receipt_eci', '!=', 'null : 0000-00-00')
        ->where('expenditure_reports.final_by_eci','1')
        ->where(function($q) {
          $q->where('expenditure_reports.final_action', 'Closed')
            ->orWhere('expenditure_reports.final_action','Disqualified')
            ->orWhere('expenditure_reports.final_action', 'Case Dropped');
          })
        ->whereNotNull('expenditure_reports.date_of_receipt_eci')
       // ->groupBy('expenditure_reports.candidate_id')
        ->get();

                $pendingatceo=DB::table('expenditure_reports')
                ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
               // ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
               ->select('expenditure_reports.candidate_id')
               ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->where('expenditure_reports.final_by_ceo', '1')
                ->whereNotNull('expenditure_reports.date_of_receipt')
                ->whereNull('expenditure_reports.date_of_receipt_eci')
                ->get();

                $pendingateciCandlist = DB::table('expenditure_reports')
                ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->select('expenditure_reports.candidate_id')
               // ->where('expenditure_reports.ST_CODE', '=', $st_code)
                //->where('expenditure_reports.constituency_no', '=', $cons_no)
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                ->where(function($query) {
                    $query->whereNull('expenditure_reports.final_action');
                    $query->orwhere('expenditure_reports.final_action', '=','');
                  }) 
                ->groupBy('expenditure_reports.candidate_id')
                ->get();
                foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                    $candidate_id[] = $pendingateciCandlistData->candidate_id;
                }
                foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                    $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                }
                foreach ($pendingatceo as $pendingatceoListData) {
                    $candidate_id[] = $pendingatceoListData->candidate_id;
                }*/
                $EcifinalbyDEO = DB::table('expenditure_reports')
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

    } elseif ($st_code != '0' && $cons_no == '0') {
        $EcifinalbyDEO = DB::table('expenditure_reports')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->select('expenditure_reports.candidate_id')
                ->where('expenditure_reports.ST_CODE', '=', $st_code)
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
                ->where('candidate_nomination_detail.st_code', '=', $st_code)
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                ->get();
       
         } elseif ($st_code != '0' && $cons_no != '0') {
            $EcifinalbyDEO = DB::table('expenditure_reports')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->select('expenditure_reports.candidate_id')
                ->where('expenditure_reports.ST_CODE', '=', $st_code)
                ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
     //  echo '<pre>'; print_r( $candidate_id);
        
        $partiallyCandList = DB::table('candidate_nomination_detail')
                ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                ->where('candidate_nomination_detail.st_code', '=', $st_code)
                ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                ->get();
             }
  
                // dd(DB::getQueryLog());
                return view('admin.pc.eci.Expenditure.pendingatdeo-mis', ['user_data' => $d, 'partiallyCandList' => $partiallyCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($partiallyCandList)]);
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
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                //echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('ECIPendingatDEOCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        $candidate_id=array();
                        if($st_code == '0' && $cons_no == '0') { 
                            /* $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                             ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                             ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->select('expenditure_reports.candidate_id')
                             ->where('candidate_nomination_detail.application_status', '=', '6')
                             ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                             ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                             ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                             ->where('expenditure_reports.date_of_receipt_eci', '!=', 'null : 0000-00-00')
                             ->where('expenditure_reports.final_by_eci','1')
                             ->where(function($q) {
                               $q->where('expenditure_reports.final_action', 'Closed')
                                 ->orWhere('expenditure_reports.final_action','Disqualified')
                                 ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                               })
                             ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            // ->groupBy('expenditure_reports.candidate_id')
                             ->get();
                     
                                     $pendingatceo=DB::table('expenditure_reports')
                                     ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                     ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                     ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    // ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->select('expenditure_reports.candidate_id')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                     ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                     ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                     ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                     ->where('expenditure_reports.final_by_ceo', '1')
                                     ->whereNotNull('expenditure_reports.date_of_receipt')
                                     ->whereNull('expenditure_reports.date_of_receipt_eci')
                                     ->get();
                     
                                     $pendingateciCandlist = DB::table('expenditure_reports')
                                     ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                     ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                     ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                     ->select('expenditure_reports.candidate_id')
                                    // ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                     //->where('expenditure_reports.constituency_no', '=', $cons_no)
                                     ->where('candidate_nomination_detail.application_status', '=', '6')
                                     ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                     ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                     ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                     ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                                     ->where(function($query) {
                                         $query->whereNull('expenditure_reports.final_action');
                                         $query->orwhere('expenditure_reports.final_action', '=','');
                                       }) 
                                     ->groupBy('expenditure_reports.candidate_id')
                                     ->get();
                                     foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                                         $candidate_id[] = $pendingateciCandlistData->candidate_id;
                                     }
                                     foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                                         $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                                     }
                                     foreach ($pendingatceo as $pendingatceoListData) {
                                         $candidate_id[] = $pendingatceoListData->candidate_id;
                                     }*/
                                     $EcifinalbyDEO = DB::table('expenditure_reports')
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
                     
                         } elseif ($st_code != '0' && $cons_no == '0') {
                             $EcifinalbyDEO = DB::table('expenditure_reports')
                                     ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                     ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                     ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                     ->select('expenditure_reports.candidate_id')
                                     ->where('expenditure_reports.ST_CODE', '=', $st_code)
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
                                     ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                     ->where('candidate_nomination_detail.application_status', '=', '6')
                                     ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                     ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                     ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                     ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                                     ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                     ->get();
                            
                              } elseif ($st_code != '0' && $cons_no != '0') {
                                 $EcifinalbyDEO = DB::table('expenditure_reports')
                                     ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                     ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                     ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                     ->select('expenditure_reports.candidate_id')
                                     ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                     ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                          //  echo '<pre>'; print_r( $candidate_id);
                             
                             $partiallyCandList = DB::table('candidate_nomination_detail')
                                     ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                     ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                     ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                     ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                     ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                     ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                                     ->where('candidate_nomination_detail.application_status', '=', '6')
                                     ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                     ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                     ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                     ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                                     ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                     ->get();
                                  }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($partiallyCandList as $candDetails) { 
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            $pcDetails = getpcbypcno($candDetails->ST_CODE, $candDetails->constituency_no);
                           
							$pcno=!empty($pcDetails->PC_NO) ?  $pcDetails->PC_NO : '';
                            $pcname=!empty($pcDetails->PC_NAME) ?  $pcDetails->PC_NAME : '';
							
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
							  
                           // $lodgingDate=!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
                            $data = array(
                                $st->ST_NAME,
                                $pcno . '-' . $pcname,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate,
								$scrutinyreportsubmitdate,
								$candidatelodgingdate,
								$ceosendingdate,
								$ceoreceivedate
								
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
                            'State','PC No & Name','Candidate Name', 'Party Name', 'Last Date Of Lodging','Date of Scrutiny Report Submission','Date of Lodging A/C By Candidate','Date of Sending to the CEO','Date of Receipt By CEO'
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
$cons_no = !empty($cons_no) ? $cons_no : 0;
$cur_time = Carbon::now();
 $candidate_id=array();
 if($st_code == '0' && $cons_no == '0') { 
    /* $getcandidateListfinalbyECI = DB::table('expenditure_reports')
     ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
     ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
     ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
     ->select('expenditure_reports.candidate_id')
     ->where('candidate_nomination_detail.application_status', '=', '6')
     ->where('candidate_nomination_detail.finalaccepted', '=', '1')
     ->where('candidate_nomination_detail.symbol_id', '<>', '200')
     ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
     ->where('expenditure_reports.date_of_receipt_eci', '!=', 'null : 0000-00-00')
     ->where('expenditure_reports.final_by_eci','1')
     ->where(function($q) {
       $q->where('expenditure_reports.final_action', 'Closed')
         ->orWhere('expenditure_reports.final_action','Disqualified')
         ->orWhere('expenditure_reports.final_action', 'Case Dropped');
       })
     ->whereNotNull('expenditure_reports.date_of_receipt_eci')
    // ->groupBy('expenditure_reports.candidate_id')
     ->get();

             $pendingatceo=DB::table('expenditure_reports')
             ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
             ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
             ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
            // ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
            ->select('expenditure_reports.candidate_id')
            ->where('candidate_nomination_detail.application_status', '=', '6')
             ->where('candidate_nomination_detail.finalaccepted', '=', '1')
             ->where('candidate_nomination_detail.symbol_id', '<>', '200')
             ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
             ->where('expenditure_reports.final_by_ceo', '1')
             ->whereNotNull('expenditure_reports.date_of_receipt')
             ->whereNull('expenditure_reports.date_of_receipt_eci')
             ->get();

             $pendingateciCandlist = DB::table('expenditure_reports')
             ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
             ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
             ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
             ->select('expenditure_reports.candidate_id')
            // ->where('expenditure_reports.ST_CODE', '=', $st_code)
             //->where('expenditure_reports.constituency_no', '=', $cons_no)
             ->where('candidate_nomination_detail.application_status', '=', '6')
             ->where('candidate_nomination_detail.finalaccepted', '=', '1')
             ->where('candidate_nomination_detail.symbol_id', '<>', '200')
             ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
             ->whereNotNull('expenditure_reports.date_of_receipt_eci')
             ->where(function($query) {
                 $query->whereNull('expenditure_reports.final_action');
                 $query->orwhere('expenditure_reports.final_action', '=','');
               }) 
             ->groupBy('expenditure_reports.candidate_id')
             ->get();
             foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                 $candidate_id[] = $pendingateciCandlistData->candidate_id;
             }
             foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                 $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
             }
             foreach ($pendingatceo as $pendingatceoListData) {
                 $candidate_id[] = $pendingatceoListData->candidate_id;
             }*/
             $EcifinalbyDEO = DB::table('expenditure_reports')
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

 } elseif ($st_code != '0' && $cons_no == '0') {
     $EcifinalbyDEO = DB::table('expenditure_reports')
             ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
             ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
             ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
             ->select('expenditure_reports.candidate_id')
             ->where('expenditure_reports.ST_CODE', '=', $st_code)
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
             ->where('candidate_nomination_detail.st_code', '=', $st_code)
             ->where('candidate_nomination_detail.application_status', '=', '6')
             ->where('candidate_nomination_detail.finalaccepted', '=', '1')
             ->where('candidate_nomination_detail.symbol_id', '<>', '200')
             ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
             ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
             ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
             ->get();
    
      } elseif ($st_code != '0' && $cons_no != '0') {
         $EcifinalbyDEO = DB::table('expenditure_reports')
             ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
             ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
             ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
             ->select('expenditure_reports.candidate_id')
             ->where('expenditure_reports.ST_CODE', '=', $st_code)
             ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
  //  echo '<pre>'; print_r( $candidate_id);
     
     $partiallyCandList = DB::table('candidate_nomination_detail')
             ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
             ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
             ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
             ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
             ->where('candidate_nomination_detail.st_code', '=', $st_code)
             ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
             ->where('candidate_nomination_detail.application_status', '=', '6')
             ->where('candidate_nomination_detail.finalaccepted', '=', '1')
             ->where('candidate_nomination_detail.symbol_id', '<>', '200')
             ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
             ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
             ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
             ->get();
          }
$pdf = PDF::loadView('admin.pc.eci.Expenditure.candidatePendingatDEOPDFhtml', ['user_data' => $d, 'pendingatDEOCandList' => $partiallyCandList]);
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
     * @author Devloped Date : 01-07-19
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
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                if ($st_code == '0' && $cons_no == '0') {
                    $EcifinalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $EcifinalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $EcifinalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                // dd(DB::getQueryLog());
                return view('admin.pc.eci.Expenditure.finalbydeo-mis', ['user_data' => $d, 'EcifinalbyDEO' => $EcifinalbyDEO, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($EcifinalbyDEO)]);
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
     * @author Devloped Date : 01-07-19
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
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                //echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('ECIPendingatDEOCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        if ($st_code == '0' && $cons_no == '0') {
                            $EcifinalbyDEOMISEXL = DB::table('expenditure_reports')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->where('expenditure_reports.final_by_ro', '1')
                                    ->where('expenditure_reports.finalized_status', '1')
                                    ->whereNotNull('expenditure_reports.date_of_sending_deo')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $EcifinalbyDEOMISEXL = DB::table('expenditure_reports')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->where('expenditure_reports.final_by_ro', '1')
                                    ->where('expenditure_reports.finalized_status', '1')
                                    ->whereNotNull('expenditure_reports.date_of_sending_deo')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
                            $EcifinalbyDEOMISEXL = DB::table('expenditure_reports')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                                    //->where('expenditure_notification.deo_action','0')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->where('expenditure_reports.final_by_ro', '1')
                                    ->where('expenditure_reports.finalized_status', '1')
                                    ->whereNotNull('expenditure_reports.date_of_sending_deo')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($EcifinalbyDEOMISEXL as $candDetails) {
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            //dd($candDetails);
                            $pcDetails = getpcbypcno($candDetails->ST_CODE, $candDetails->constituency_no);
                            $date = new DateTime($candDetails->last_date_prescribed_acct_lodge);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
							$scrutinyreportsubmitdate= date('d-m-Y',strtotime($candDetails->report_submitted_date));
							$candidatelodgingdate= date('d-m-Y',strtotime($candDetails->date_orginal_acct));
							$ceosendingdate= date('d-m-Y',strtotime($candDetails->date_of_sending_deo));
							$ceoreceivedate= date('d-m-Y',strtotime($candDetails->date_of_receipt));
							
                           // $lodgingDate =!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
							
							  $lodgingDate =$lodgingDate ??  '22-06-2019';
							  $scrutinyreportsubmitdate =$scrutinyreportsubmitdate ??  'N/A';
							  $candidatelodgingdate = (!empty($candidatelodgingdate) && $candidatelodgingdate !='30-11--0001' ) ?  $candidatelodgingdate : 'N/A';
							  $ceosendingdate =$ceosendingdate ??  'N/A';
							  $ceoreceivedate =$ceoreceivedate ??  'N/A';
							 
                            $data = array(
                                $pcDetails->PC_NO . '-' . $pcDetails->PC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate,
								$scrutinyreportsubmitdate,
								$candidatelodgingdate,
								$ceosendingdate,
								$ceoreceivedate
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
                            'PC No & Name', 'Candidate Name', 'Party Name', 'Last Date Of Lodging','Date of Scrutiny Report Submission','Date of Lodging A/C By Candidate','Date of Sending to the CEO','Date of Receipt By CEO'
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
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                 if ($st_code == '0' && $cons_no == '0') {
                            $EcifinalbyDEOMISPDF = DB::table('expenditure_reports')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->where('expenditure_reports.final_by_ro', '1')
                                    ->where('expenditure_reports.finalized_status', '1')
                                    ->whereNotNull('expenditure_reports.date_of_sending_deo')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $EcifinalbyDEOMISPDF = DB::table('expenditure_reports')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->where('expenditure_reports.final_by_ro', '1')
                                    ->where('expenditure_reports.finalized_status', '1')
                                    ->whereNotNull('expenditure_reports.date_of_sending_deo')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
                            $EcifinalbyDEOMISPDF = DB::table('expenditure_reports')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                                    //->where('expenditure_notification.deo_action','0')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->where('expenditure_reports.final_by_ro', '1')
                                    ->where('expenditure_reports.finalized_status', '1')
                                    ->whereNotNull('expenditure_reports.date_of_sending_deo')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        }
                        
                $pdf = PDF::loadView('admin.pc.eci.Expenditure.finalbyDEOPDFhtml', ['user_data' => $d, 'EcifinalbyDEOMISPDF' => $EcifinalbyDEOMISPDF]);
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
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
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
                             //->where('expenditure_reports.ST_CODE', '=', $st_code)
                             //->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                          //  ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            // ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            // ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
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
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                            ->get();
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
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();

                \Excel::create('ECIPendingatCEOCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                       $candidate_id=[];
                if ($st_code == '0' && $cons_no == '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
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
                             //->where('expenditure_reports.ST_CODE', '=', $st_code)
                             //->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                          //  ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            // ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            // ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
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
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.pc_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                            ->get();
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
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
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
                             //->where('expenditure_reports.ST_CODE', '=', $st_code)
                             //->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                          //  ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            // ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            // ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                            // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
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
                            ->get();
                }
                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.pc.eci.Expenditure.candidatePendingatCEOPDFhtml', ['user_data' => $d, 'pendingatCEOCandList' => $finalbyceoCandList]);
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
                    $pendingateciCandlist = DB::table('expenditure_reports')
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
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
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
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
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
                            $pendingatECICandList = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    //->join('expenditure_notification', 'expenditure_notification.candidate_id', '=', 'expenditure_reports.candidate_id') 
                                    //->where('expenditure_reports.ST_CODE','=',$st_code)
                                    // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                                    // ->where('expenditure_notification.eci_action','0')
                                    ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    // ->where('expenditure_reports.final_action','==','Closed')
                                    // ->where('expenditure_reports.final_by_eci','1')
                                    ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                               ->where(function($query) {
								$query->whereNull('expenditure_reports.final_action');
								$query->orwhere('expenditure_reports.final_action', '=','');
							  })                                  
							  ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $pendingatECICandList = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    // ->join('expenditure_notification', 'expenditure_notification.candidate_id', '=', 'expenditure_reports.candidate_id') 
                                    ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                                    // ->where('expenditure_notification.eci_action','0')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    // ->where('expenditure_reports.final_action','==','Closed')
                                    //->where('expenditure_reports.final_by_eci','1')
                                    ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                                      ->where(function($query) {
								$query->whereNull('expenditure_reports.final_action');
								$query->orwhere('expenditure_reports.final_action', '=','');
							  })  
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
                            $pendingatECICandList = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    //->join('expenditure_notification', 'expenditure_notification.candidate_id', '=', 'expenditure_reports.candidate_id') 
                                    ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                                    // ->where('expenditure_notification.eci_action','0')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    //->where('expenditure_reports.final_action','==','Closed')
                                    //->where('expenditure_reports.final_by_eci','1')
                                    ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                                      ->where(function($query) {
								      $query->whereNull('expenditure_reports.final_action');
								      $query->orwhere('expenditure_reports.final_action', '=','');
							           })  
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
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
				
				  $lodgingDate =$lodgingDate ??  '22-06-2019';
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
                    $pendingatECICandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            // ->join('expenditure_notification', 'expenditure_notification.candidate_id', '=', 'expenditure_reports.candidate_id') 
                            //->where('expenditure_reports.ST_CODE','=',$st_code)
                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                            // ->where('expenditure_notification.eci_action','0')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($query) {
								      $query->whereNull('expenditure_reports.final_action');
								      $query->orwhere('expenditure_reports.final_action', '=','');
							           }) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $pendingatECICandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            //->join('expenditure_notification', 'expenditure_notification.candidate_id', '=', 'expenditure_reports.candidate_id') 
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                            //->where('expenditure_notification.eci_action','0')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($query) {
								      $query->whereNull('expenditure_reports.final_action');
								      $query->orwhere('expenditure_reports.final_action', '=','');
							           }) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $pendingatECICandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            // ->join('expenditure_notification', 'expenditure_notification.candidate_id', '=', 'expenditure_reports.candidate_id') 
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            //->where('expenditure_notification.eci_action','0')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                             ->where(function($query) {
								      $query->whereNull('expenditure_reports.final_action');
								      $query->orwhere('expenditure_reports.final_action', '=','');
							           }) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
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
     * @author Devloped Date : 29-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getOfficersmis By ECI fuction     
     */
    public function getCandidatemis(Request $request) {
        //dd($request->all());
        //PC ECI getOfficersmis TRY CATCH STARTS HERE
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
               $statelist = $this->commonModel->getallstate();
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
                $cons_no = $request->input('pc');
                $st_code = !empty($st_code) ? $st_code : '';
                $cons_no = !empty($cons_no) ? $cons_no : '';
                // echo  $st_code.'pc'.$cons_no; die;
                DB::enableQueryLog();

                if (!empty($st_code) && $cons_no == '' &&  $st_code !='All') {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            //->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no != '' &&  $st_code !='All') {
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
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                }else if (!empty($st_code) && $cons_no == '' &&  $st_code =='All') {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                }else if ( $st_code == '' && $cons_no == '' ) {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
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
                // dd(DB::getQueryLog());
                return view('admin.pc.eci.Expenditure.mis-candidate', ['user_data' => $d, 'totalContestedCandidate' => $totalContestedCandidate, 'cons_no' => $cons_no, 'st_code' => $st_code, 'statelist' => $statelist, 'count' => count($totalContestedCandidate)]);
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
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getCandidatesmisEXL By ECI fuction     
     */
//ECI getCandidatesmisEXL EXCEL REPORT STARTS
    public function getCandidatesmisEXL(Request $request, $state, $pc) {
        //ECI getCandidatesmisEXL EXCEL REPORT TRY CATCH BLOCK STARTS
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
                  $statelist = $this->commonModel->getallstate();
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
                // echo  $st_code.'pc'.$cons_no; die;
                // dd($totalContestedCandidate);

                $cur_time = Carbon::now();

                \Excel::create('ECICandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no,$permitstates) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no,$permitstates) {

                        if (!empty($st_code) && $cons_no == '' &&  $st_code !='All') {
                            $totalContestedCandidate = DB::table('candidate_nomination_detail')
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
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                    ->groupBy("candidate_nomination_detail.st_code")
                                    ->get();
                        }else if (!empty($st_code) && $cons_no == '' &&  $st_code =='All') {
                            $totalContestedCandidate = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                    ->groupBy("candidate_nomination_detail.st_code")
                                    ->get();
                        }else if ( $st_code == '' && $cons_no == '' ) {
                            $totalContestedCandidate = DB::table('candidate_nomination_detail')
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

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($totalContestedCandidate as $candDetails) {
                            $st = getstatebystatecode($candDetails->st_code);
                            // dd($candDetails);
                            $pcDetails = getpcbypcno($candDetails->st_code, $candDetails->pc_no);
                           // $date = new DateTime($candDetails->created_at);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                           // $lodgingDate = $date->format('d-m-Y'); // 31-07-2012

                             $TotalUsers =$candDetails->totalcandidate;
                            $stdetails = getstatebystatecode($candDetails->st_code);
                            $filedcount = $this->eciexpenditureModel->gettotaldataentryStart('PC', $candDetails->st_code, $cons_no);

                            // Get Pending Data Count 
                            $notfiledcount = $TotalUsers - $filedcount;

                            $defaulter = $this->eciexpenditureModel->getdefaulter('PC', $candDetails->st_code, $cons_no);
                            //dd($defaulter);
                            $defaultercount = !empty($defaulter) ? count($defaulter) : '0';
                            $notinTime = $this->eciexpenditureModel->gettotalNotinTime('PC', $candDetails->st_code, $cons_no);
                            if (empty($filedcount))
                                $filedcount = '0';
                            if (empty($notfiledcount) || $notfiledcount <=0)
                                $notfiledcount = '0';
                            if (empty($notinTime))
                                $notinTime = '0';
                            if (empty($defaultercount))
                                $defaultercount = '0';
                            $data = array(
                                $st->ST_NAME,
                                $filedcount,
                                $notfiledcount,
                                $notinTime,
                                $defaultercount
                            );

                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        // $totalvalues = array('Total',$TotalUsers);
                        // print_r($totalvalues);die;
                        // array_push($arr,$totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'State Name', 'Total Filed Candidate', 'Not Filed Candidate', 'Not In Time Candidate', 'Defaulter Candidate'
                                )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI getCandidatesmisEXL EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI getCandidatesmisEXL EXCEL REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getCandidatemisPDF By ECI fuction     
     */
    //ECI getCandidatemisPDF PDF REPORT STARTS

    public function getCandidatemisPDF(Request $request, $state, $pc) {
        //ECI getcandidateListpendingatECIPDF PDF REPORT TRY CATCH BLOCK STARTS
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
                   $statelist = $this->commonModel->getallstate();
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
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            //->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no != '' &&  $st_code !='All') {
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
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                }else if (!empty($st_code) && $cons_no == '' &&  $st_code =='All') {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                }else if ( $st_code == '' && $cons_no == '' ) {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
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

                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.pc.eci.Expenditure.mis-candidatePdfhtml', ['user_data' => $d, 'totalContestedCandidate' => $totalContestedCandidate,'cons_no' => $cons_no, 'st_code' => $st_code, 'statelist' => $statelist]);
                return $pdf->download('EcimiscandidatePdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.pc.eci.Expenditure.mis-candidatePdfhtml');
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
     * @author Devloped Date : 01-07-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return Ecistartedcandidate By ECI fuction     
     */
    public function Ecistartedcandidate(Request $request, $state, $pc) {
        //PC ECI Ecistartedcandidate TRY CATCH STARTS HERE
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
                        if ($st_code == '0' && $cons_no == '0') {
                            $Ecistartedcandidate = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $Ecistartedcandidate = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
                            $Ecistartedcandidate = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                               }
        
                        // dd($filedData);
                        return view('admin.pc.eci.Expenditure.mis-startedcandidate', ['user_data' => $d, 'Ecistartedcandidate' => $Ecistartedcandidate, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($Ecistartedcandidate)]);
                    } else {
                        return redirect('/officer-login');
                    }
                } catch (Exception $ex) {
                    return Redirect('/internalerror')->with('error', 'Internal Server Error');
                }//PC ECI Ecistartedcandidate TRY CATCH ENDS HERE   
            }
        
        // end Ecistartedcandidate start function
        
            /**
             * @author Devloped By : Niraj Kumar
             * @author Devloped Date : 01-07-19
             * @author Modified By : 
             * @author Modified Date : 
             * @author param return EcistartedcandidateMISEXL By ECI fuction     
             */
        //ECI EcistartedcandidateMISEXL EXCEL REPORT STARTS
            public function EcistartedcandidateMISEXL(Request $request, $state, $pc) {
                //ECI filedcandidateDataEXL EXCEL REPORT TRY CATCH BLOCK STARTS
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
                        $cur_time = Carbon::now();
                        \Excel::create('ECIStartedCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                            $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {
        
                                if ($st_code == '0' && $cons_no == '0') {
                                    $EcistartedcandidateMISEXL = DB::table('expenditure_reports')
                                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                            ->where('candidate_nomination_detail.application_status', '=', '6')
                                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                            ->groupBy('expenditure_reports.candidate_id')
                                            ->get();
                                } elseif ($st_code != '0' && $cons_no == '0') {
                                    $EcistartedcandidateMISEXL = DB::table('expenditure_reports')
                                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                                            ->where('candidate_nomination_detail.application_status', '=', '6')
                                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                            ->groupBy('expenditure_reports.candidate_id')
                                            ->get();
                                } elseif ($st_code != '0' && $cons_no != '0') {
                                    $EcistartedcandidateMISEXL = DB::table('expenditure_reports')
                                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                                            ->where('candidate_nomination_detail.application_status', '=', '6')
                                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                            ->groupBy('expenditure_reports.candidate_id')
                                            ->get();
                                }
        
                                $arr = array();
                                $TotalUsers = 0;
                                $user = Auth::user();
                                $count = 1;
                                foreach ($EcistartedcandidateMISEXL as $candDetails) {
                                    $st = getstatebystatecode($candDetails->ST_CODE);
                                    // dd($candDetails);
                                    $pcDetails = getpcbypcno($candDetails->ST_CODE, $candDetails->constituency_no);
                                    $date = new DateTime($candDetails->last_date_prescribed_acct_lodge);
                                    //echo $date->format('d.m.Y'); // 31.07.2012
                                    $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
                                    $lodgingDate=!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
                                    $TotalUsers = count($EcistartedcandidateMISEXL);
                                    $data = array(
                                        $st->ST_NAME,
                                        $pcDetails->PC_NO . '-' . $pcDetails->PC_NAME,
                                        $candDetails->cand_name,
                                        $candDetails->PARTYNAME,
                                        $lodgingDate
                                    );
        
                                    array_push($arr, $data);
                                    // }
                                    $count++;
                                 }
                                // $totalvalues = array('Total',$TotalUsers);
                                // print_r($totalvalues);die;
                                // array_push($arr,$totalvalues);
                                $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                                    'State','PC No & Name', 'Candidate Name', 'Party Name', 'Last Date Of Lodging'
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
                //ECI EcistartedcandidateMISEXL EXCEL REPORT TRY CATCH BLOCK ENDS
            }
        
            //ECI EcistartedcandidateMISEXL EXCEL REPORT FUNCTION ENDS
        
/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 01-07-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return EcistartedcandidateMISPDF By ECI fuction     
 */
//ECI filedcandidateDataPDF PDF REPORT STARTS

public function EcistartedcandidateMISPDF(Request $request, $state, $pc) {
//ECI filedcandidateDataPDF PDF REPORT TRY CATCH BLOCK STARTS
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
            $EcistartedcandidateMISPDF = DB::table('expenditure_reports')
                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->groupBy('expenditure_reports.candidate_id')
                    ->orderBy('expenditure_reports.constituency_no')
                    ->get();
        } elseif ($st_code != '0' && $cons_no == '0') {
            $EcistartedcandidateMISPDF = DB::table('expenditure_reports')
                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                    // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->groupBy('expenditure_reports.candidate_id')
                    ->orderBy('expenditure_reports.constituency_no')
                    ->get();
        } elseif ($st_code != '0' && $cons_no != '0') {
            $EcistartedcandidateMISPDF = DB::table('expenditure_reports')
                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->groupBy('expenditure_reports.candidate_id')
                    ->orderBy('expenditure_reports.constituency_no')
                    ->get();
        }

        //dd($totalContestedCandidatedata);

        $pdf = PDF::loadView('admin.pc.eci.Expenditure.mis-startedcandidatePdfhtml', ['user_data' => $d, 'filedData' => $filedData]);
        return $pdf->download('EcimiscandidatePdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
        return view('admin.pc.eci.Expenditure.mis-startedcandidatePdfhtml');
    } else {
        return redirect('/admin-login');
    }
} catch (Exception $ex) {
    return Redirect('/internalerror')->with('error', 'Internal Server Error');
} //ECI EcistartedcandidateMISPDF PDF REPORT TRY CATCH BLOCK ENDS
}

//ECI EcistartedcandidateMISPDF PDF REPORT FUNCTION ENDS

/**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 01-07-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return Ecinotstarted By ECI fuction     
     */
    public function Ecinotstarted(Request $request, $state, $pc) {
        //PC ECI notfiledcandidateData TRY CATCH STARTS HERE
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
                DB::enableQueryLog();
                $candidate_id = [];
                if ($st_code == '0' && $cons_no == '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            //->where('expenditure_reports.ST_CODE','=',$st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $Ecinotstarted = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    // ->where('candidate_nomination_detail.st_code','=',$st_code)
                                    // ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $Ecinotstarted = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    // ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $Ecinotstarted = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->get();
                }
                //  dd(DB::getQueryLog());
                return view('admin.pc.eci.Expenditure.mis-notstartedcandidate', ['user_data' => $d, 'Ecinotstarted' => $Ecinotstarted, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($Ecinotstarted)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC Ecinotstarted list TRY CATCH ENDS HERE   
    }

// end Ecinotstarted function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 29-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return filedcandidateData By ECI fuction     
     */
    public function filedcandidateData(Request $request, $state, $pc) {
//PC ECI candidateListByfinalizeData TRY CATCH STARTS HERE
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
                if ($st_code == '0' && $cons_no == '0') {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                // dd($filedData);
                return view('admin.pc.eci.Expenditure.mis-filedcandidate', ['user_data' => $d, 'filedData' => $filedData, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($filedData)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI filedcandidateData TRY CATCH ENDS HERE   
    }

// end filedcandidateData start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return filedcandidateDataEXL By ECI fuction     
     */
//ECI getCandidatesmisEXL EXCEL REPORT STARTS
    public function filedcandidateDataEXL(Request $request, $state, $pc) {
        //ECI filedcandidateDataEXL EXCEL REPORT TRY CATCH BLOCK STARTS
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
                $cur_time = Carbon::now();
                \Excel::create('ECIFiledCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        if ($st_code == '0' && $cons_no == '0') {
                            $filedData = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $filedData = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
                            $filedData = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($filedData as $candDetails) {
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            // dd($candDetails);
                            $pcDetails = getpcbypcno($candDetails->ST_CODE, $candDetails->constituency_no);
                            $date = new DateTime($candDetails->last_date_prescribed_acct_lodge);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012

                            $TotalUsers = count($filedData);
                            $data = array(
                                $pcDetails->PC_NO . '-' . $pcDetails->PC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate
                            );

                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        // $totalvalues = array('Total',$TotalUsers);
                        // print_r($totalvalues);die;
                        // array_push($arr,$totalvalues);
						
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'PC No & Name', 'Candidate Name', 'Party Name', 'Last Date Of Lodging'
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
        //ECI filedcandidateData EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI filedcandidateData EXCEL REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return filedcandidateDataPDF By ECI fuction     
     */
    //ECI filedcandidateDataPDF PDF REPORT STARTS

    public function filedcandidateDataPDF(Request $request, $state, $pc) {
        //ECI filedcandidateDataPDF PDF REPORT TRY CATCH BLOCK STARTS
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
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->orderBy('expenditure_reports.constituency_no')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->orderBy('expenditure_reports.constituency_no')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->orderBy('expenditure_reports.constituency_no')
                            ->get();
                }

                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.pc.eci.Expenditure.mis-filedcandidatePdfhtml', ['user_data' => $d, 'filedData' => $filedData]);
                return $pdf->download('EcimiscandidatePdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.pc.eci.Expenditure.mis-filedcandidatePdfhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        } //ECI filedcandidateDataPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//ECI filedcandidateDataPDF PDF REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 29-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return notfiledcandidateData By ECI fuction     
     */
    public function notfiledcandidateData(Request $request, $state, $pc) {
        //PC ECI notfiledcandidateData TRY CATCH STARTS HERE
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
                DB::enableQueryLog();
                $candidate_id = [];
                if ($st_code == '0' && $cons_no == '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            //->where('expenditure_reports.ST_CODE','=',$st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $pendingCandList = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    // ->where('candidate_nomination_detail.st_code','=',$st_code)
                                    // ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $pendingCandList = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    // ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
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
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->get();
                }
                //  dd(DB::getQueryLog());
                return view('admin.pc.eci.Expenditure.mis-notfiledcandidate', ['user_data' => $d, 'pendingCandList' => $pendingCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($pendingCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECInotfiledcandidateData list TRY CATCH ENDS HERE   
    }

// end notfiledcandidateData function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return notfiledcandidateDataEXL By ECI fuction     
     */
//ECI notfiledCandidatesmisEXL EXCEL REPORT STARTS
    public function notfiledcandidateDataEXL(Request $request, $state, $pc) {
        //ECI filedcandidateDataEXL EXCEL REPORT TRY CATCH BLOCK STARTS
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
                $cur_time = Carbon::now();
                \Excel::create('ECInotfiledCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {
                        if ($st_code == '0' && $cons_no == '0') {
                            $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                                    //->where('expenditure_reports.ST_CODE','=',$st_code)
                                    //->where('expenditure_reports.constituency_no','=',$cons_no) 
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                            foreach ($startCandList as $startCandListData) {
                                $candidate_id[] = $startCandListData->candidate_id;
                            }
                            $pendingCandList = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    // ->where('candidate_nomination_detail.st_code','=',$st_code)
                                    // ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                                    ->orderBy('candidate_nomination_detail.pc_no')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    //->where('expenditure_reports.constituency_no','=',$cons_no) 
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                            foreach ($startCandList as $startCandListData) {
                                $candidate_id[] = $startCandListData->candidate_id;
                            }
                            $pendingCandList = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    // ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                                    ->orderBy('candidate_nomination_detail.pc_no')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
                            $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
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
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                                    ->orderBy('candidate_nomination_detail.pc_no')
                                    ->get();
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($pendingCandList as $candDetails) {
                            $st = getstatebystatecode($candDetails->st_code);
                            // dd($candDetails);
                            $pcDetails = getpcbypcno($candDetails->st_code, $candDetails->pc_no);
                            $date = new DateTime($candDetails->created_at);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012

                            $TotalUsers = count($pendingCandList);
                            $data = array(
                                $st->ST_NAME,
                                $pcDetails->PC_NO . '-' . $pcDetails->PC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME
                               
                            );

                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        // $totalvalues = array('Total',$TotalUsers);
                        // print_r($totalvalues);die;
                        // array_push($arr,$totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                           'State', 'PC No & Name', 'Candidate Name', 'Party Name'
                                )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI notfiledcandidateData EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI notfiledcandidateData EXCEL REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return notfiledcandidateDataPDF By ECI fuction     
     */
    //ECI notfiledcandidateDataPDF PDF REPORT STARTS

    public function notfiledcandidateDataPDF(Request $request, $state, $pc) {
        //ECI notfiledcandidateDataPDF PDF REPORT TRY CATCH BLOCK STARTS
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
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            //->where('expenditure_reports.ST_CODE','=',$st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $pendingCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            // ->where('candidate_nomination_detail.st_code','=',$st_code)
                            // ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->orderBy('candidate_nomination_detail.pc_no')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $pendingCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            // ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->orderBy('candidate_nomination_detail.pc_no')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
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
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->orderBy('candidate_nomination_detail.pc_no')
                            ->get();
                }
                $pdf = PDF::loadView('admin.pc.eci.Expenditure.mis-notfiledcandidatePdfhtml', ['user_data' => $d, 'pendingCandList' => $pendingCandList]);
                return $pdf->download('EcimisnotfiledcandidatePdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.pc.eci.Expenditure.mis-notfiledcandidatePdfhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        } //ECI notfiledcandidateDataPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//ECI notfiledcandidateDataPDF PDF REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 29-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return notintimecandidateData By ECI fuction     
     */
    public function notintimecandidateData(Request $request, $state, $pc) {

        //PC ECI notintimecandidateData TRY CATCH STARTS HERE
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
                $notinTime = [];
                if ($st_code == '0' && $cons_no == '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.account_lodged_time','No') 
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.account_lodged_time','No') 
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code == '0' && $cons_no != '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.account_lodged_time','No') 
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                //dd($DataentryStartCandList);
                return view('admin.pc.eci.Expenditure.mis-notintimecandidate', ['user_data' => $d, 'notinTime' => $notinTime, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($notinTime)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI notintimecandidateData TRY CATCH ENDS HERE   
    }

// end notintimecandidateData start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return notintimecandidateDataEXL By ECI fuction     
     */
//ECI notintimeCandidatesmisEXL EXCEL REPORT STARTS
    public function notintimecandidateDataEXL(Request $request, $state, $pc) {
        //ECI filedcandidateDataEXL EXCEL REPORT TRY CATCH BLOCK STARTS
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
                $cur_time = Carbon::now();
                \Excel::create('ECIFiledCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        if ($st_code == '0' && $cons_no == '0') {
                            $notinTime = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->where('expenditure_reports.account_lodged_time','No') 
                                    ->where('expenditure_reports.finalized_status', '=', '1')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $notinTime = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('expenditure_reports.account_lodged_time','No') 
                                    ->where('expenditure_reports.finalized_status', '=', '1')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code == '0' && $cons_no != '0') {
                            $notinTime = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                                    ->where('expenditure_reports.account_lodged_time','No') 
                                    ->where('expenditure_reports.finalized_status', '=', '1')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($notinTime as $candDetails) {
                            $st = getstatebystatecode($candDetails->st_code);
                            // dd($candDetails);
                            $pcDetails = getpcbypcno($candDetails->st_code, $candDetails->pc_no);
                            $date = new DateTime($candDetails->last_date_prescribed_acct_lodge);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
                            $lodgingDate=!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
                            $TotalUsers = count($notinTime);
                            $data = array(
                                $st->ST_NAME,
                                $pcDetails->PC_NO . '-' . $pcDetails->PC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate
                            );

                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        // $totalvalues = array('Total',$TotalUsers);
                        // print_r($totalvalues);die;
                        // array_push($arr,$totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'State','PC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging'
                                )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI filedcandidateData EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI filedcandidateData EXCEL REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return filedcandidateDataPDF By ECI fuction     
     */
    //ECI notintimecandidateDataPDF PDF REPORT STARTS

    public function notintimecandidateDataPDF(Request $request, $state, $pc) {
        //ECI filedcandidateDataPDF PDF REPORT TRY CATCH BLOCK STARTS
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
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.account_lodged_time','No') 
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.account_lodged_time','No') 
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code == '0' && $cons_no != '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.account_lodged_time','No') 
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.pc.eci.Expenditure.mis-notintimecandidatePdfhtml', ['user_data' => $d, 'notinTime' => $notinTime]);
                return $pdf->download('EcimisnotintimecandidatePdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.pc.eci.Expenditure.mis-notintimecandidatePdfhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        } //ECI notintimecandidateDataPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//ECI notintimecandidateDataPDF PDF REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 29-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return defaultercandidateData By ECI fuction     
     */
    public function defaultercandidateData(Request $request, $state, $pc) {

        //PC ECI defaulter TRY CATCH STARTS HERE
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
                // echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                if ($st_code == '0' && $cons_no == '0') {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<=', 'totalcandamnt')
                            //->where('expenditure_understated.ST_CODE','=',$st_code)
                            // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<=', 'totalcandamnt')
                            ->where('expenditure_understated.ST_CODE', '=', $st_code)
                            // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
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
                }
                // dd(DB::getQueryLog());
                return view('admin.pc.eci.Expenditure.mis-defaultercandidate', ['user_data' => $d, 'defaulterCandList' => $defaulterCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($defaulterCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI defaultercandidateData list TRY CATCH ENDS HERE   
    }

// end defaultercandidateData start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return defaultercandidateDataEXL By ECI fuction     
     */
//ECI defaulterCandidatesmisEXL EXCEL REPORT STARTS
    public function defaultercandidateDataEXL(Request $request, $state, $pc) {
        //ECI filedcandidateDataEXL EXCEL REPORT TRY CATCH BLOCK STARTS
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
                $cur_time = Carbon::now();
                \Excel::create('ECIdefaulterCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {
                        if ($st_code == '0' && $cons_no == '0') {
                            $defaulterCandList = DB::table('expenditure_understated')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                            DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                            DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                                    ->having('totalobseramnt', '<=', 'totalcandamnt')
                                    //->where('expenditure_understated.ST_CODE','=',$st_code)
                                    // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_understated.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $defaulterCandList = DB::table('expenditure_understated')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                            DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                            DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                                    ->having('totalobseramnt', '<=', 'totalcandamnt')
                                    ->where('expenditure_understated.ST_CODE', '=', $st_code)
                                    // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_understated.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
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
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($defaulterCandList as $candDetails) {
                            $st = getstatebystatecode($candDetails->st_code);
                            // dd($candDetails);
                            $pcDetails = getpcbypcno($candDetails->st_code, $candDetails->pc_no);
                            $date = new DateTime($candDetails->created_at);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012

                            $TotalUsers = count($defaulterCandList);
                            $data = array(
                                $pcDetails->PC_NO . '-' . $pcDetails->PC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate
                            );

                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        // $totalvalues = array('Total',$TotalUsers);
                        // print_r($totalvalues);die;
                        // array_push($arr,$totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'PC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging'
                                )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI defaultercandidateData EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI defaultercandidateData EXCEL REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return defaultercandidateDataPDF By ECI fuction     
     */
    //ECI defaultercandidateDataPDF PDF REPORT STARTS

    public function defaultercandidateDataPDF(Request $request, $state, $pc) {
        //ECI filedcandidateDataPDF PDF REPORT TRY CATCH BLOCK STARTS
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
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<=', 'totalcandamnt')
                            //->where('expenditure_understated.ST_CODE','=',$st_code)
                            // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<=', 'totalcandamnt')
                            ->where('expenditure_understated.ST_CODE', '=', $st_code)
                            // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
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
                }
                $pdf = PDF::loadView('admin.pc.eci.Expenditure.mis-defaultercandidatePdfhtml', ['user_data' => $d, 'defaulterCandList' => $defaulterCandList]);
                return $pdf->download('EcimisdefaultercandidatePdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.pc.eci.Expenditure.mis-defaultercandidatePdfhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        } //ECI defaultercandidateDataPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//ECI defaultercandidateDataPDF PDF REPORT FUNCTION ENDS

    ///////Tracking Status by Niraj 15-06-2019////////////////////////
    public function getCandTracking(request $request, $candidate_id) {
         // Get the full URL for the previous request...
         $routesegment=array_slice(explode('/', url()->previous()), -3, 1);
 
        $html = '';
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            DB::enableQueryLog();
            $CandidatStatus = DB::table('expenditure_reports')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->where('expenditure_reports.candidate_id', $candidate_id)
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();
            // dd(DB::getQueryLog());
            // print_r( $CandidatStatus);
			
            if (($CandidatStatus[0]->date_orginal_acct == '0000-00-00') || empty($CandidatStatus[0]->date_orginal_acct)) {
                $candlogedAcc = 'N/A';
            } else {
                $candlogedAcc = date('d-m-Y', strtotime($CandidatStatus[0]->date_orginal_acct));
            }
            if (($CandidatStatus[0]->date_of_receipt == '0000-00-00') || empty($CandidatStatus[0]->date_of_receipt)) {
                $recieptbyceo = 'N/A';
            } else {
                $recieptbyceo = date('d-m-Y', strtotime($CandidatStatus[0]->date_of_receipt));
            }

            if (($CandidatStatus[0]->final_by_ceo == 1)) {
                $finalbyceo = 'Finalize';
            } else {
                $finalbyceo = 'Not Finalize';
            }
            if (($CandidatStatus[0]->final_by_eci == 1)) {
                $finalbyeci = 'Finalize';
            } else {
                $finalbyeci = 'Not Finalize';
            }
            if ((strtotime($CandidatStatus[0]->date_of_receipt_eci) == 0 || empty($CandidatStatus[0]->date_of_receipt_eci))) {
                $recieptbyeci = 'N/A';
            } else {
                $recieptbyeci = date('d-m-Y', strtotime($CandidatStatus[0]->date_of_receipt_eci));
            }


            ################################Notice Section By Niraj 13-09-2019##################
            if ((strtotime($CandidatStatus[0]->date_of_issuance_notice) == 0 || empty($CandidatStatus[0]->date_of_issuance_notice))) {
                $noticeissuedatebyeci = 'N/A';
            } else {
                $noticeissuedatebyeci = date('d-m-Y', strtotime($CandidatStatus[0]->date_of_issuance_notice));
            }

            if ((strtotime($CandidatStatus[0]->date_of_receipt_notice_service) == 0 || empty($CandidatStatus[0]->date_of_receipt_notice_service))) {
                $noticereceiveddatebyceo = 'N/A';
            } else {
                $noticereceiveddatebyceo = date('d-m-Y', strtotime($CandidatStatus[0]->date_of_receipt_notice_service));
            }

            if ((strtotime($CandidatStatus[0]->date_sending_notice_service_to_deo) == 0 || empty($CandidatStatus[0]->date_sending_notice_service_to_deo))) {
                $noticesendingdateceotodeo = 'N/A';
            } else {
                $noticesendingdateceotodeo = date('d-m-Y', strtotime($CandidatStatus[0]->date_sending_notice_service_to_deo));
            }

            if ((strtotime($CandidatStatus[0]->date_of_receipt_represetation) == 0 || empty($CandidatStatus[0]->date_of_receipt_represetation))) {
                $noticereceiveddatebydeo = 'N/A';
            } else {
                $noticereceiveddatebydeo = date('d-m-Y', strtotime($CandidatStatus[0]->date_of_receipt_represetation));
            }

            
            if ((strtotime($CandidatStatus[0]->date_sending_supplimentary) == 0 || empty($CandidatStatus[0]->date_sending_supplimentary))) {
                $noticereplieddatebydeo = 'N/A';
            } else {
                $noticereplieddatebydeo = date('d-m-Y', strtotime($CandidatStatus[0]->date_sending_supplimentary));
            }

            ################################End Notice Section By Niraj 13-09-2019##################

            $html .= '<div class="scroll-tracks">
           <div class="bs-vertical-wizard">
           <p class="text-left h6 pb-3 pt-4 Orange_text" style="margin-left: -50px;"><strong>Tracking Status :' . $CandidatStatus[0]->cand_name . '</strong></p>
           <div class="clearfix"></div>
               <ul>
                   <li class="complete">
                       <a href="#">
                       <i class="ico ico-green">RO</i> 									
                       <span>
                           <div class="contentBox">
                               <div class="date h6 text-success"><strong>Finalize:' . date('d-m-Y', strtotime($CandidatStatus[0]->created_at)) . ' </strong></div>
                               <p class="graySquire"> Account Loged By Candidate :' . $candlogedAcc . ' </p>
                               <p class="greenSquire">Scrutiny submit :' . date('d-m-Y', strtotime($CandidatStatus[0]->created_at)) . ' </p>
                               <p class="yellowSquire">Send To CEO :' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_deo)) . ' </p>';	
                              if($routesegment[0]=='noticeatdeo') { 
                                $html .='<p class="yellowSquire">Notice Send by CEO : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>	
                               <p class="yellowSquire">Notice Send by ECI : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>	
                               <p class="yellowSquire">Notice Received : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>	
                               <p class="yellowSquire">Notice Reply : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>';
                              }
                              $html .= '</div>							
                       </span>
                       </a>
                       <p class="dateleft">0 - 38&nbspDays</p>									
                       <div class="clearfix"></div>	
                   </li>
   
                   <li class="complete prev-step">
                       <a href="#"> 
                       <i class="ico ico-green">CEO</i>
                           <span class="desc">	
                           <div class="contentBox">
                               <div class="date h6 text-success"><strong>Finalize: ' . $recieptbyeci . '</strong></div>
                               <p class="graySquire"> Received: ' . $recieptbyceo . '</p>
                               <p class="greenSquire">Action : ' . $finalbyceo . '</p>
                               <p class="yellowSquire">Send to ECI : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>';		
                               if($routesegment[0]=='noticeatceo') { 
                                $html .='<p class="yellowSquire">Notice Received : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>	
                               <p class="yellowSquire">Notice Send to DEO : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>';	
                               }
                               $html .='</div>
                           </span>
                       </a>
                       <p class="dateleft">0 - 45&nbspDays</p>
                   </li>								
                   <li class="current">
                       <a href="#">
                       <i class="ico ico-green">ECI</i> 
                           <span class="desc">										
                               <div class="contentBox">
                               <div class="date h6 text-warning"><strong>Finalize : ' . $recieptbyeci . '</strong></div>
                               <p class="graySquire"> Received: ' . $recieptbyeci . '</p>
                               <p class="greenSquire">Action :' . $finalbyeci . '</p>
                               <p class="yellowSquire">Action Date : ' . $recieptbyeci . '</p>';	
                              if($noticeissuedatebyeci !='N/A'){
                                $html .='<p class="yellowSquire">Notice Issued : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>';	
                              }
                              $html .='</div>								
                           </span>										
                       </a>
                   </li>
               </ul>
           </div>
           </div>
           </div>
       </div>';
        }

        return $html;
    }

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 18-06-19
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
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                if ($st_code == '0' && $cons_no == '0') {
                    $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.date_of_receipt_eci', '!=', 'null : 0000-00-00')
                            ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                                //->orWhere('expenditure_reports.final_action','Disqualified')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                             ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                               // ->orWhere('expenditure_reports.final_action','Disqualified')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                               // ->orWhere('expenditure_reports.final_action','Disqualified')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
               // dd($getcandidateListfinalbyECI);
                return view('admin.pc.eci.Expenditure.finalbyeci-mis', ['user_data' => $d, 'getcandidateListfinalbyECI' => $getcandidateListfinalbyECI, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($getcandidateListfinalbyECI)]);
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
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                // dd($totalContestedCandidate);

                $cur_time = Carbon::now();

                \Excel::create('ECIFinalCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                       if ($st_code == '0' && $cons_no == '0') {
                    $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.date_of_receipt_eci', '!=', 'null : 0000-00-00')
                            ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                               // ->orWhere('expenditure_reports.final_action','Disqualified')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                             ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                               // ->orWhere('expenditure_reports.final_action','Disqualified')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                                //->orWhere('expenditure_reports.final_action','Disqualified')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($getcandidateListfinalbyECI as $candDetails) {
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
                            $TotalUsers = count($getcandidateListfinalbyECI);
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
    }//ECI getcandidateListfinalclosedECIEXL EXCEL REPORT FUNCTION ENDS


    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 06-09-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return disqualified By ECI fuction     
     */
    public function getdisqualifiedcandidateListbyECI(Request $request, $state, $pc) {
        //PC ECI getdisqualifiedcandidateListbyECI TRY CATCH STARTS HERE
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
                    $getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.date_of_receipt_eci', '!=', 'null : 0000-00-00')
                            ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
                            ->where('expenditure_reports.final_action', 'Disqualified')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
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
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
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
                }
               // dd($getdisqualifiedcandidateListbyECI);
                return view('admin.pc.eci.Expenditure.disqualifiedbyeci-mis', ['user_data' => $d, 'getdisqualifiedcandidateListbyECI' => $getdisqualifiedcandidateListbyECI, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($getdisqualifiedcandidateListbyECI)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getdisqualifiedcandidateListbyECI TRY CATCH ENDS HERE   
    }

// end getdisqualifiedcandidateListbyECI start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 06-09-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param getdisqualifiedcandidateListbyECIEXL By ECI fuction     
     */
    //ECI getdisqualifiedcandidateListbyECI EXCEL REPORT STARTS
    public function getdisqualifiedcandidateListbyECIEXL(Request $request, $state, $pc) {
        //ECI getdisqualifiedcandidateListbyECI EXCEL REPORT TRY CATCH BLOCK STARTS
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

                \Excel::create('ECIDisqualifiedCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                       if ($st_code == '0' && $cons_no == '0') {
                    $getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.date_of_receipt_eci', '!=', 'null : 0000-00-00')
                             ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
                            ->where('expenditure_reports.final_action', 'Disqualified')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
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
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.PARTYNAME')
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
                }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($getdisqualifiedcandidateListbyECI as $candDetails) {
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
                            $TotalUsers = count($getdisqualifiedcandidateListbyECI);
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
    }//ECI getcandidateListfinalclosedECIEXL EXCEL REPORT FUNCTION ENDS
    
#################################End MIS Report by Niraj##############################
    #################################Start Report Section By Niraj 13-06-2019#####################################

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 13-06-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getOfficersreport By ECI fuction     
     */
    public function getOfficersreport(Request $request) {
        //dd($request->all());
        //PC ECI getOfficersmis TRY CATCH STARTS HERE
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
            $statelist = $this->commonModel->getallstate();
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
                $cons_no = $request->input('pc');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                // DB::enableQueryLog();
                if (!empty($st_code) && $cons_no == '' &&  $st_code !='All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            //->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->count();
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
                            // ->count();
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
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            // ->count();
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if ( $st_code == '' && $cons_no == '' ) {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
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
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                }
                // dd(DB::getQueryLog());
                // dd($totalContestedCandidatedata);
                return view('admin.pc.eci.Expenditure.report-officer', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist, 'count' => count($totalContestedCandidatedata)]);
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
     * @author Devloped Date : 14-06--19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getOfficers report By ECI fuction     
     */
//ECI getOfficers EXCEL REPORT STARTS
    public function getOfficersreportEXL(Request $request, $state, $pc) {
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
            $statelist = $this->commonModel->getallstate();
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
                        // echo  $st_code.'pc'.$cons_no; die;
                        $cur_time = Carbon::now();
                        \Excel::create('EciOfficerReportExcel_' . '_' . $cur_time, function($excel) use($st_code, $cons_no,$permitstates) {
                            $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no,$permitstates) {
                                if (!empty($st_code) && $cons_no == '' &&  $st_code !='All') {
                                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                            //->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                                            ->where('candidate_nomination_detail.application_status', '=', '6')
                                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                            //->count();
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
                                            // ->count();
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
                                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                            // ->count();
                                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                            ->groupBy("candidate_nomination_detail.st_code")
                                            ->get();
                                } else if ( $st_code == '' && $cons_no == '' ) {
                                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
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
                                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                            ->groupBy("candidate_nomination_detail.st_code")
                                            ->get();
                                }
                                $arr = array();
                                $TotalUsers = 0;
                                $TotalfiledData = 0;
                                $TotalnotfiledData = 0;
                                $Totalfinalcompletedcount = 0;
                                $Totalpc = 0;
        
                                $user = Auth::user();
                                $count = 1;
                                foreach ($totalContestedCandidatedata as $key => $listdata) {
                                    //get filedcount data entry start data count
                                    $filedcount = $this->eciexpenditureModel->gettotaldataentryStart('PC', $listdata->st_code, $cons_no);
                                    // Get Pending Data Count 
                                    $notfiledcount = $listdata->totalcandidate - $filedcount;
        
                                    //Get Data entry finalize Count 
                                    $finalcompletedcount = $this->eciexpenditureModel->gettotalCompletedbyEci('PC', $listdata->st_code, $cons_no);
        
                                    $stdetails = getstatebystatecode($listdata->st_code);
                                    $pcbystate = getpcbystate($listdata->st_code);
                                    $pccount = count($pcbystate);
                                    $Totalpc += $pccount;
        
                                    $TotalUsers += $listdata->totalcandidate;
                                    $TotalfiledData += $filedcount;
                                    $TotalnotfiledData += $notfiledcount;
                                    $Totalfinalcompletedcount += $finalcompletedcount;
        
                                    $filedcount = !empty($filedcount) ? $filedcount : '0';
                                    $notfiledcount = !empty($notfiledcount) ? $notfiledcount : '0';
                                    $finalcompletedcount = !empty($finalcompletedcount) ? $finalcompletedcount : '0';
        
                                    $data = array(
                                        $stdetails->ST_NAME,
                                        $pccount,
                                        $listdata->totalcandidate,
                                        $notfiledcount,
                                        $filedcount,
                                        $finalcompletedcount
                                    );
                                    array_push($arr, $data);
                                    // }
                                    $count++;
                                }
                                $totalvalues = array('Total', $Totalpc, $TotalUsers, $TotalnotfiledData, $TotalfiledData, $Totalfinalcompletedcount);
                                // print_r($totalvalues);die;
                                array_push($arr, $totalvalues);
                                $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                                    'State Name', 'Total PC', 'Total Candidate', 'NotStarted', 'InProgress', 'Completed'
                                        )
                                );
                            });
                        })->export('xls');
                    } else {
                        return redirect('/admin-login');
                    }
                } catch (Exception $ex) {
                    return Redirect('/internalerror')->with('error', 'Internal Server Error');
                }
                //ECI getOfficersmisEXL EXCEL REPORT TRY CATCH BLOCK ENDS
            }

//ECI getOfficersreport USERS EXCEL REPORT FUNCTION ENDS
    //ECI getOfficersmis PDF REPORT STARTS
    public function getOfficersreportPDF(Request $request, $state, $pc) {
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
          $statelist = $this->commonModel->getallstate();
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
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            //->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->count();
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
                            // ->count();
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
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            // ->count();
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy('candidate_nomination_detail.st_code','candidate_nomination_detail.pc_no')
                            ->get();
                } else if ( $st_code == '' && $cons_no == '' ) {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
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
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                }

                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.pc.eci.Expenditure.report-officerPDFhtml', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata,'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist]);
                return $pdf->download('EciOfficerReportPdf_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.pc.eci.Expenditure.report-officerPDFhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECIgetOfficersreportPDF PDF REPORT TRY CATCH BLOCK ENDS
   }

//ECIgetOfficersreportPDF PDF REPORT FUNCTION ENDS



public function getNationlPartyWiseExpenditure(Request $request)
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
                   $statelist = $this->commonModel->getallstate();
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


            if(!empty($_GET['pdf']) && $_GET['pdf']="yes"){
                      ////// code for pdf generation//////
               $pdf = PDF::loadView('admin.pc.eci.Expenditure.fund-nationalpartiesPDF', ['user_data' => $d, 'partylist' => $partylist]);
                return $pdf->download('NationalPartyWiseFundPdf_' . trim($_GET['pdf']) . '_Today_' . $cur_time . '.pdf'); 
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
                   return view('admin.pc.eci.Expenditure.fund-nationalparties', ['user_data' => $d, 'ele_details' => $ele_details, 'partylist' => $partylist,"statelist"=>$statelist,"st_code"=>$st_code]);
                 }

        } else {
            return redirect('/officer-login');
        } 
   }
   
     
/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 30-12-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return getbreachAmntMis on expenditure By ECI fuction     
 */
public function getbreachAmntMis(Request $request)
   {  
    //dd($request->all());
      DB::enableQueryLog();
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
             $cur_time = Carbon::now();
            $conditions="";
            if(!empty($_GET['state'])){
            $st_code = $_GET['state'];
           // $conditions .=" and cnd.st_code='$st_code' ";
              }
       
            if(!empty($_GET['pc'])){
            $cons_no = $_GET['pc'];
           // $conditions .=" and cnd.pc_no='$pc' ";
              }  

            $returnType=$request->input('returnType');
          if($returnType=='return'){
            $returnType = 'Returned';
            }elseif ($returnType=='non-return') {
              $returnType = 'Non-Returned';
            } 

             ##########Code For State Wise Access By Niraj date 23-07-2019################
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
                   $statelist = $this->commonModel->getallstate();
                }
                if(!empty($st_code)){
                    $st_code=$st_code;
                }elseif(empty($st_code) && !empty($permitstate)){
                    $st_code=array_values($permitstate)[0];
                }else {
                    $st_code=0;
                }
                $cons_no = $request->input('pc');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $returnType = !empty($returnType) ?  $returnType : 0;
               // echo 'st_code'.$st_code.'cons_no'.$cons_no.'returnType'.$returnType; die('test');
             ###################Code For State Wise Access#####################
//  dd($conditions);

$query = DB::table('candidate_nomination_detail')
->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
->select('candidate_nomination_detail.pc_no','candidate_nomination_detail.st_code','candidate_personal_detail.cand_hname','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_id','candidate_nomination_detail.party_id', DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
->where('candidate_nomination_detail.application_status', '=', '6')
->where('candidate_nomination_detail.finalaccepted', '=', '1')
->where('candidate_nomination_detail.symbol_id', '<>', '200')
->Where('candidate_personal_detail.cand_name', '<>', 'NOTA');

if(!empty($st_code) && empty($cons_no)) {
  $query->where('candidate_nomination_detail.st_code', '=', $st_code);
  $query->groupBy('candidate_nomination_detail.st_code');  
  }else if (!empty($st_code) && !empty($cons_no)) {
  $query->where('candidate_nomination_detail.st_code', '=', $st_code);
  $query->where('candidate_nomination_detail.pc_no', '=', $cons_no);
   $query->groupBy('candidate_nomination_detail.st_code');  
}else if (empty($st_code) && empty($cons_no)) {
 $query->groupBy('candidate_nomination_detail.st_code');  
} 
$candList=$query->get();
//dd(DB::getQueryLog());
//$count=!empty($candList)?count($candList):0;

//  dd(DB::getQueryLog());
if(!empty($_GET['pdf']) && $_GET['pdf']="yes"){ 
    ////// code for pdf generation//////
$pdf = PDF::loadView('admin.pc.eci.Expenditure.misbreach-reportPDFhtml', ['user_data' => $d, 'candList' => $candList,'st_code' => $st_code,'cons_no' => $cons_no]);
return $pdf->download('BreachingAmntReportPdf_' . trim($_GET['pdf']) . '_Today_' . $cur_time . '.pdf'); 
return view('admin.ac.eci.Expenditure.misbreach-reportPDFhtml');  
}
elseif (!empty($_GET['exl']) && $_GET['exl']="yes") {
  //////////export exel //////////////
// Initialize the array which will be passed into the Excel
// generator.
$candidateArray = []; 

// Define the Excel spreadsheet headers
$candidateArray[] = ['S.NO','STATE NAME','TOTAL PC','Total Candidates','Total Candidates Whos Expenditure is Breaching','Total Candidates Without Breaching Amount'];

// Convert each member of the returned collection into an array,
// and append it to the payments array.
$i=1;
foreach ($candList as $canwise) { 
    $breachcount=$this->expenditureModel->gettotalbreaching('PC',$canwise->st_code,$cons_no);
    $breachcount=$breachcount[0]->breachcount;
	 //without breaching amount
		  if($breachcount >= 0 ){
			$withoutBreach=$canwise->totalcandidate-($breachcount);
			}  
    
$pcdetails=getpcbypcno($canwise->st_code,$canwise->pc_no);  
$st=getstatebystatecode($canwise->st_code);
  $pcbystate=getpcbystate($canwise->st_code);
   $pccount=count($pcbystate);
   $total_candidate=!empty($canwise->totalcandidate) ? $canwise->totalcandidate : '0';
  $candidateArr[$i]['S.no'] = $i;
  $candidateArr[$i]['state_name'] = $st->ST_NAME;
  $candidateArr[$i]['pc_no'] = $pccount;
  $candidateArr[$i]['total_candidate'] = $total_candidate;
  $candidateArr[$i]['breachCandidate'] =!empty($breachcount) ? $breachcount : '0';
  $candidateArr[$i]['withoutBreach'] =!empty($withoutBreach) ? $withoutBreach : '0';
  $i++;
}

foreach ($candidateArr as $candidate) {
       $candidateArray[] = $candidate;
       }

               // Generate and return the spreadsheet
                \Excel::create('MisBreachingReport', function($excel) use ($candidateArray) {
                    // Set the spreadsheet title, creator, and description
                    $excel->setTitle('Mis Breaching Report');
                    $excel->setCreator('Eci')->setCompany('Election Commission Of India');
                    // Build the spreadsheet, passing in the payments array
                    $excel->sheet('MisBreachingReport', function($sheet) use ($candidateArray) {
                        $sheet->fromArray($candidateArray, null, 'A1', false, false);
                    });
                    })->download('csv');
                 }
                 else
                 {
                   return view('admin.pc.eci.Expenditure.misbreach-report', ['user_data' => $d, 'ele_details' => $ele_details, 'candList' => $candList,'statelist'=>$statelist,'st_code' => $st_code,'cons_no' => $cons_no]);
                 }
            // dd(DB::getQueryLog());
          // dd($candList);
            
        } else {
            return redirect('/officer-login');
        }
  } // end breaching amount mis
/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 23-12-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return getbreachAmnt on expenditure By ECI fuction     
 */
public function getbreachAmnt(Request $request ,$state, $pc) {
        //ECI getbreachAmnt TRY CATCH BLOCK STARTS
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
    //dd($request->all());
     
            $returnType=$request->input('returnType');
          if($returnType=='return'){
            $returnType = 'Returned';
            }elseif ($returnType=='non-return') {
              $returnType = 'Non-Returned';
            } 
 $cur_time = Carbon::now();
            
//  dd($conditions);
$query = DB::table('expenditure_reports')
->join(DB::raw('(SELECT * FROM expenditure_understated  GROUP BY date_understated, expenditure_type,amt_understated_by_candidate,candidate_id ORDER BY candidate_id)
               resultunderstated'), 
        function($join)
        {
           $join->on('resultunderstated.candidate_id', '=', 'expenditure_reports.candidate_id');
        })
->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
->select(DB::raw('YEAR(STR_TO_DATE(resultunderstated.date_understated, "%m/%d/%Y")) AS YEAR'),'expenditure_reports.election_type','resultunderstated.constituency_no','resultunderstated.ST_CODE','candidate_personal_detail.cand_hname','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_id','expenditure_reports.finalized_status','expenditure_reports.updated_at as finalized_date','expenditure_reports.final_by_ro','expenditure_reports.date_of_declaration','expenditure_reports.grand_total_election_exp_by_cadidate',DB::raw('SUM(resultunderstated.amt_as_per_observation) as amt_as_per_observation'),DB::raw('SUM(resultunderstated.amt_understated_by_candidate) as amt_understated_by_candidate'))
->Where('candidate_personal_detail.cand_name', '<>', 'NOTA');
//->where('expenditure_reports.finalized_status', '=', '1')
//->where('expenditure_reports.final_by_ro', '=', '1');

if(!empty($st_code) && empty($cons_no) && empty($returnType)) {
$query->where('expenditure_reports.ST_CODE', '=', $st_code);
 
  }else if (!empty($st_code) && empty($cons_no) && !empty($returnType)) {
$query->where('expenditure_reports.return_status', '=', $returnType); 
$query->where('expenditure_reports.ST_CODE', '=', $st_code);
 
} else if (!empty($st_code) && !empty($cons_no) && empty($returnType)) {
  $query->where('expenditure_reports.ST_CODE', '=', $st_code);
  $query->where('expenditure_reports.constituency_no', '=', $cons_no);
  
}else if (!empty($st_code) && !empty($cons_no) && !empty($returnType)) {
  $query->where('expenditure_reports.return_status', '=', $returnType) ;
  $query->where('expenditure_reports.ST_CODE', '=', $st_code);
  $query->where('expenditure_reports.constituency_no', '=', $cons_no); 
}  
$query->groupBy('resultunderstated.candidate_id');
$candList=$query->get();
//dd(DB::getQueryLog());
//$count=!empty($candList)?count($candList):0;

//  dd(DB::getQueryLog());
if(!empty($_GET['pdf']) && $_GET['pdf']="yes"){
    ////// code for pdf generation//////
$pdf = PDF::loadView('admin.pc.eci.Expenditure.breach-reportPdfhtml', ['user_data' => $d, 'candList' => $candList]);
return $pdf->download('BreachingAmntReportPdf_' . trim($_GET['pdf']) . '_Today_' . $cur_time . '.pdf'); 
return view('admin.pc.eci.Expenditure.breach-reportPdfhtml');  
}
elseif (!empty($_GET['exl']) && $_GET['exl']="yes") {
  //////////export exel //////////////
// Initialize the array which will be passed into the Excel
// generator.
$candidateArray = []; 

// Define the Excel spreadsheet headers
$candidateArray[] = ['S.NO','CANDIDATE NAME', 'STATE NAME','PC NO & PC NAME','YEAR','ELECTION TYPE','TOTAL EXPENDITURE DECLARED BY CANDIDATE(Rs.)','TOTAL EXPENDITURE ASSESSED BY DEO(Rs.)','TOTAL BREACHING AMOUNT(Rs.)'];

// Convert each member of the returned collection into an array,
// and append it to the payments array.
$i=1;
$grandTotal = 0;
$grandTotalAssessbyDEO=0;
$avgTotalbycand=0;
$avgbyAssessbyDEO=0;
$grandTotalBreachAmnt=0;
$count=1;
foreach ($candList as $canwise) {
		$candidate_id=$canwise->candidate_id;
		//$candUnderStatasDetails=$this->expenditureModel->GetScrutinyUnderExpByitemData($candidate_id);
		$totalamntassesbyDEO=$canwise->amt_as_per_observation;
		 $grandTotalAssessbyDEO += $totalamntassesbyDEO;
		$totalamount = !empty($canwise->grand_total_election_exp_by_cadidate)? $canwise->grand_total_election_exp_by_cadidate : 0; 
		$grandTotal += $totalamount;
		$BreachAmnt=0;
		if(!empty($totalamntassesbyDEO) && ($totalamount != $totalamntassesbyDEO)){ 
		$BreachAmnt=$totalamntassesbyDEO-$totalamount;
		}
		if(!empty($BreachAmnt) && $BreachAmnt > 0){
		$BreachAmnt = '+'.$BreachAmnt;
		}elseif(!empty($BreachAmnt) && $BreachAmnt < 0){
		$BreachAmnt = $BreachAmnt;
		}else{
		$BreachAmnt = 0;
		}


$pcdetails=getpcbypcno($canwise->ST_CODE,$canwise->constituency_no); 
$st=getstatebystatecode($canwise->ST_CODE);
  $candidateArr[$i]['S.no'] = $i;
  $candidateArr[$i]['cand_name'] = $canwise->cand_name;
  $candidateArr[$i]['state_name'] = $st->ST_NAME;
  $candidateArr[$i]['pc_no'] = $pcdetails->PC_NO.' - '.$pcdetails->PC_NAME;
  $candidateArr[$i]['year'] = $canwise->YEAR;
  $candidateArr[$i]['election_type'] = $canwise->election_type;
  $candidateArr[$i]['grand_total_election_exp_by_cadidate'] =!empty($canwise->grand_total_election_exp_by_cadidate) ? $canwise->grand_total_election_exp_by_cadidate : 0;
  $candidateArr[$i]['grand_total_assessed_by_deo'] =!empty($totalamntassesbyDEO) ? $totalamntassesbyDEO : '0';
  $candidateArr[$i]['BreachAmnt'] =!empty($BreachAmnt) ? $BreachAmnt : '0';
 // $candidateArr[$i]['total_expenditure'] = $this->expenditureModel->getcandidatetotalexpenditure($canwise->candidate_id);
 // $candidateArr[$i]['total_expenditure'] = !empty($candidateArr[$i]['total_expenditure']) ? 'Rs. '.$candidateArr[$i]['total_expenditure']:0;
  $i++;
}

foreach ($candidateArr as $candidate) {
       $candidateArray[] = $candidate;
       }

               // Generate and return the spreadsheet
                \Excel::create('CandidateWiseBreachingReport', function($excel) use ($candidateArray) {
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
                   return view('admin.pc.eci.Expenditure.breach-report', ['user_data' => $d, 'ele_details' => $ele_details, 'candList' => $candList,"cons_no"=>$cons_no,"st_code"=>$st_code]);
                 }
            // dd(DB::getQueryLog());
          // dd($candList);
            
        } else {
            return redirect('/officer-login');
        }
      } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//PC ECI breaching report TRY CATCH ENDS HERE   
  } // end breaching report
    ############################################End Report Section ######################
	
	###############################Notice CEO & DEO 23-06-2019 Start By Niraj######################################
  /**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 23-06--19
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
        $cons_no=!empty($cons_no) ? $cons_no : 0;
        // echo $st_code.'cons_no'.$cons_no; die;
    
        if($st_code =='0' && $cons_no=='0'){
        $noticeatCEO = DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
         ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
         ->where('expenditure_reports.final_by_ceo','0')
         ->where('expenditure_reports.final_by_ro','0')
        ->whereNotNull('expenditure_reports.date_of_issuance_notice')
        ->where(function($q) {
            $q->where('expenditure_reports.final_action','=','Notice Issued')
              ->orWhere('expenditure_reports.final_action','=','Reply Issued')
              ->orWhere('expenditure_reports.final_action','=','Hearing Done');
            })
        ->groupBy('expenditure_reports.candidate_id')
        ->get(); 
    }elseif($st_code !='0' && $cons_no=='0'){
    $noticeatCEO = DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
        ->where('expenditure_reports.ST_CODE','=',$st_code)
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('expenditure_reports.final_by_ceo','0')
         ->where('expenditure_reports.final_by_ro','0')
        ->whereNotNull('expenditure_reports.date_of_issuance_notice')
        ->where(function($q) {
            $q->where('expenditure_reports.final_action','=','Notice Issued')
              ->orWhere('expenditure_reports.final_action','=','Reply Issued')
              ->orWhere('expenditure_reports.final_action','=','Hearing Done');
            })
         ->groupBy('expenditure_reports.candidate_id')
        ->get(); 
    }elseif($st_code !='0' && $cons_no !='0'){
    $noticeatCEO = DB::table('expenditure_reports')
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
        ->whereNotNull('expenditure_reports.date_of_issuance_notice')
        ->where(function($q) {
            $q->where('expenditure_reports.final_action','=','Notice Issued')
              ->orWhere('expenditure_reports.final_action','=','Reply Issued')
              ->orWhere('expenditure_reports.final_action','=','Hearing Done');
            })
        ->groupBy('expenditure_reports.candidate_id')
        ->get(); 
    }
        //dd($DataentryStartCandList);
        return view('admin.pc.eci.Expenditure.noticeatceo',['user_data' => $d,'noticeatCEO' => $noticeatCEO,'edetails'=>$ele_details,'st_code'=>$st_code,'cons_no'=>$cons_no,'count'=>count($noticeatCEO)]); 
        
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
        $cons_no=!empty($cons_no) ? $cons_no : 0;
        // echo  $st_code.'pc'.$cons_no; die;
       $cur_time    = Carbon::now();
    
       \Excel::create('EciNoticeAtCEO_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
    $excel->sheet('Sheet1', function($sheet) use($st_code,$cons_no) {
    
        if($st_code =='0' && $cons_no=='0'){
            $noticeatCEO = DB::table('expenditure_reports')
            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
             ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
             ->where('expenditure_reports.final_by_ceo','0')
             ->where('expenditure_reports.final_by_ro','0')
            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
            ->where(function($q) {
                $q->where('expenditure_reports.final_action','=','Notice Issued')
                  ->orWhere('expenditure_reports.final_action','=','Reply Issued')
                  ->orWhere('expenditure_reports.final_action','=','Hearing Done');
                })
            ->groupBy('expenditure_reports.candidate_id')
            ->get(); 
        }elseif($st_code !='0' && $cons_no=='0'){
        $noticeatCEO = DB::table('expenditure_reports')
            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
            ->where('expenditure_reports.ST_CODE','=',$st_code)
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('expenditure_reports.final_by_ceo','0')
             ->where('expenditure_reports.final_by_ro','0')
            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
            ->where(function($q) {
                $q->where('expenditure_reports.final_action','=','Notice Issued')
                  ->orWhere('expenditure_reports.final_action','=','Reply Issued')
                  ->orWhere('expenditure_reports.final_action','=','Hearing Done');
                })
             ->groupBy('expenditure_reports.candidate_id')
            ->get(); 
        }elseif($st_code !='0' && $cons_no !='0'){
        $noticeatCEO = DB::table('expenditure_reports')
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
            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
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
            foreach ($noticeatCEO as $candDetails) {
                $st=getstatebystatecode($candDetails->st_code);
                //dd($candDetails);
                $pcDetails=getpcbypcno($candDetails->st_code,$candDetails->pc_no);
                $date = new DateTime($candDetails->finalized_date);
                //echo $date->format('d.m.Y'); // 31.07.2012
                $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
                $data =  array(
				$st->ST_NAME,
                $pcDetails->PC_NO.'-'.$pcDetails->PC_NAME,
                $candDetails->cand_name,
                $candDetails->PARTYNAME,
                $lodgingDate
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
                                'State','PC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging'
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
        //ECI getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK ENDS
        
    }

    /**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 23-06-19
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
        $cons_no=!empty($cons_no) ? $cons_no : 0;
        // echo $st_code.'cons_no'.$cons_no; die;
    
        if($st_code =='0' && $cons_no=='0'){
        $noticeatDEO = DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
         ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
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
    }elseif($st_code !='0' && $cons_no=='0'){
    $noticeatDEO = DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
        ->where('expenditure_reports.ST_CODE','=',$st_code)
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
    }elseif($st_code !='0' && $cons_no !='0'){
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
        return view('admin.pc.eci.Expenditure.noticeatdeo',['user_data' => $d,'noticeatDEO' => $noticeatDEO,'edetails'=>$ele_details,'st_code'=>$st_code,'cons_no'=>$cons_no,'count'=>count($noticeatDEO)]); 
        
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
        $cons_no=!empty($cons_no) ? $cons_no : 0;
        // echo  $st_code.'pc'.$cons_no; die;
       $cur_time    = Carbon::now();
    
    \Excel::create('ECINoticeatDEOCandidate_'.'_'.$cur_time, function($excel) use($st_code,$cons_no) { 
    $excel->sheet('Sheet1', function($sheet) use($st_code,$cons_no) {
    
        if($st_code =='0' && $cons_no=='0'){
            $noticeatDEO = DB::table('expenditure_reports')
            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
             ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
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
        }elseif($st_code !='0' && $cons_no=='0'){
        $noticeatDEO = DB::table('expenditure_reports')
            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
            ->where('expenditure_reports.ST_CODE','=',$st_code)
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
        }elseif($st_code !='0' && $cons_no !='0'){
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
				  
                $data =  array(
				$st->ST_NAME,
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
                               'State','PC No & Name', 'Candidate Name', 'Party Name', 'Date Of Submit Scrutiny Report'
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
        //ECI getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK ENDS
        
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
                return view('admin.expenditure.createmisexpensereport', ['user_data' => $d, 'ele_details' => $ele_details, "cand_finalize_ro" => array(), "electionType" => $electionType, "ReportSingleData" => $ReportSingleData, "nature_of_default_ac" => $nature_of_default_ac, "current_status" => $current_status, "candidate_data" => (array) $candidate_data,'countElectedCandidate'=>$countElectedCandidate,'resultDeclarationDate'=>$resultDeclarationDate]);
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
		$date_of_disqualified = $request['date_of_disqualified'];
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
               $data_action=array("candidate_id"=>$candidate_id,"deo_action"=>$final_action,"ceo_action"=>$final_action,"eci_action"=>$final_action,"eci_action_date"=>$cdate,"eci_comment"=>$comment_by_eci,"created_by"=>$uid,"eci_action_sending_date"=>$cdate,"eci_action_receive_date"=>$date_of_receipt_eci,"eci_action_disqualified_date"=>$date_of_disqualified);

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
                   $statelist = $this->commonModel->getallstate();
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
                   $statelist = $this->commonModel->getallstate();
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
					if (!empty($_GET['exl']) && $_GET['exl']="yes") {
//////////export exel //////////////
// Initialize the array which will be passed into the Excel
// generator.
$candidateArray = []; 

// Define the Excel spreadsheet headers
$candidateArray[] = ['S.NO', 'STATE NAME','PC NO & PC NAME','CANDIDATE NAME','PARTYNAME','LAST LODGING DATE','TOTAL RECEIVED FUND(Rs.)','TOTAL EXPENDITURE DECLARED BY CANDIDATE(Rs.)'];

// Convert each member of the returned collection into an array,
// and append it to the payments array.
$i=1;
foreach ($returnCandList as $canwise) { // dd($canwise);

$totalexpen= !empty($canwise->grand_total_election_exp_by_cadidate) ? $canwise->grand_total_election_exp_by_cadidate : '0';

$candreceieved = $this->expenditureModel->getcandidatetotalexpenditure($canwise->candidate_id);
$pcdetails=getpcbypcno($canwise->ST_CODE,$canwise->constituency_no); 
$st=getstatebystatecode($canwise->ST_CODE);
$candidateArr[$i]['S.no'] = $i;
$candidateArr[$i]['state_name'] = $st->ST_NAME;
$candidateArr[$i]['pc_no'] = $pcdetails->PC_NO.' - '.$pcdetails->PC_NAME;
$candidateArr[$i]['cand_name'] = $canwise->cand_name;
$candidateArr[$i]['partyname'] = $canwise->PARTYNAME;
$candidateArr[$i]['lastlodgingdate'] = !empty($canwise->last_date_prescribed_acct_lodge)  ? date('d-m-Y',strtotime($canwise->last_date_prescribed_acct_lodge)) : 'N/A';
$candidateArr[$i]['candreceieved'] =!empty($candreceieved) ? $candreceieved : '0';
$candidateArr[$i]['$totalexpen'] =!empty($totalexpen) ? $totalexpen : '0';

$i++;
}

            foreach ($candidateArr as $candidate) {
            $candidateArray[] = $candidate;
            }
       // Generate and return the spreadsheet
        \Excel::create('ReturnACCandidateReport', function($excel) use ($candidateArray) {
            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Elected Candidate Wise Expenditure');
            $excel->setCreator('Eci')->setCompany('Election Commission Of India');
            // Build the spreadsheet, passing in the payments array
            $excel->sheet('ReturnACCandidateReport', function($sheet) use ($candidateArray) {
                $sheet->fromArray($candidateArray, null, 'A1', false, false);
            });
           })->download('csv');
         }
         else
            {   
                return view('admin.pc.eci.Expenditure.return-report', ['user_data' => $d, 'returnCandList' => $returnCandList ,
                    'edetails' => $ele_details, "count" => $count,
                    'st_code'=>$st_code,
                    'cons_no'=>$cons_no
                        ]);
		 } } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByfiledData TRY CATCH ENDS HERE   
    }
	
		
public function getElectedcand(Request $request,$state, $pc) {
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
        
        
    #####Code For State Wise Access By Niraj date 23-07-2019#####################
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
           $statelist = $this->commonModel->getallstate();
        }
        if(!empty($st_code)){
            $st_code=$st_code;
        }elseif(empty($st_code) && !empty($permitstate)){
            $st_code=array_values($permitstate)[0];
        }else {
            $st_code=0;
        }
                   
        #########################Code For State Wise Access#####################
$query = DB::table('winning_leading_candidate')
  ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'winning_leading_candidate.candidate_id')
  ->join('m_party', 'winning_leading_candidate.lead_cand_partyid', '=', 'm_party.CCODE')
  ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'winning_leading_candidate.candidate_id')
  ->select('winning_leading_candidate.candidate_id','winning_leading_candidate.st_code','winning_leading_candidate.pc_no as constituency_no','candidate_personal_detail.cand_name','expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.grand_total_election_exp_by_cadidate','expenditure_reports.created_at','expenditure_reports.final_by_ro','m_party.PARTYNAME');

if(!empty($st_code) && empty($cons_no)) {
   $query->where('winning_leading_candidate.st_code', '=', $st_code);
 } else if (!empty($st_code) && !empty($cons_no)) {
  $query->where('winning_leading_candidate.st_code', '=', $st_code);
  $query->where('winning_leading_candidate.pc_no', '=', $cons_no);
}
$query->groupBy('winning_leading_candidate.candidate_id');

$electedCandList=$query->get();
                
$count=!empty($electedCandList) ? count($electedCandList): '0';

if (!empty($_GET['exl']) && $_GET['exl']="yes") {
  //////////export exel //////////////
// Initialize the array which will be passed into the Excel
// generator.
$candidateArray = []; 

// Define the Excel spreadsheet headers
$candidateArray[] = ['S.NO', 'STATE NAME','PC NO & PC NAME','CANDIDATE NAME','PARTYNAME','LAST LODGING DATE','TOTAL RECEIVED FUND(Rs.)','TOTAL EXPENDITURE DECLARED BY CANDIDATE(Rs.)'];

// Convert each member of the returned collection into an array,
// and append it to the payments array.
$i=1;
foreach ($electedCandList as $canwise) {
   $candidate_id=$canwise->candidate_id;
   $totalexpen=$this->expenditureModel->getcandidatetotalexpenditure($candidate_id);
    
$pcdetails=getpcbypcno($canwise->st_code,$canwise->constituency_no); 
$st=getstatebystatecode($canwise->st_code);
  $candidateArr[$i]['S.no'] = $i;
  $candidateArr[$i]['state_name'] = $st->ST_NAME;
  $candidateArr[$i]['pc_no'] = $pcdetails->PC_NO.' - '.$pcdetails->PC_NAME;
  $candidateArr[$i]['cand_name'] = $canwise->cand_name;
  $candidateArr[$i]['partyname'] = $canwise->PARTYNAME;
  $candidateArr[$i]['lastlodgingdate'] = !empty($canwise->last_date_prescribed_acct_lodge)  ? date('d-m-Y',strtotime($canwise->last_date_prescribed_acct_lodge)) : 'N/A';
  $candidateArr[$i]['totalexpen'] =!empty($totalexpen) ? $totalexpen : '0';
  $candidateArr[$i]['grand_total_election_exp_by_cadidate'] =!empty($canwise->grand_total_election_exp_by_cadidate) ? $canwise->grand_total_election_exp_by_cadidate : '0';
  $i++;
}

				foreach ($candidateArr as $candidate) {
					   $candidateArray[] = $candidate;
				}
               // Generate and return the spreadsheet
                \Excel::create('ElectedCandidateReport', function($excel) use ($candidateArray) {

                    // Set the spreadsheet title, creator, and description
                    $excel->setTitle('Elected Candidate Wise Expenditure');
                    $excel->setCreator('Eci')->setCompany('Election Commission Of India');
                    // Build the spreadsheet, passing in the payments array
                    $excel->sheet('ElectedCandidateReport', function($sheet) use ($candidateArray) {
                        $sheet->fromArray($candidateArray, null, 'A1', false, false);
                    });
                   })->download('csv');
                 }
                 else
                 {
              return view('admin.pc.eci.Expenditure.electedcandidate-report', ['user_data' => $d, 'electedCandList' => $electedCandList ,
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
    } // end Function Elected Candidate
	
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
                   $statelist = $this->commonModel->getallstate();
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
                   $statelist = $this->commonModel->getallstate();
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
               
              $candList = DB::select("select TEMP.YEAR,ELECTION_TYPE,cpd.cand_hname,TEMP.pc_no,TEMP.st_code,TEMP.district_no,
              TEMP.party_id,cpd.cand_name,cpd.candidate_id,TEMP.finalized_status,TEMP.finalized_date,TEMP.final_by_ro,
              TEMP.date_of_declaration,TEMP.grand_total_election_exp_by_cadidate
              from(
              select med.YEAR,med.ELECTION_TYPE,cnd.pc_no,
              cnd.st_code,cnd.district_no,cnd.candidate_id,
              cnd.party_id,er.finalized_status,
              er.updated_at as finalized_date, er.final_by_ro,
              er.date_of_declaration,er.grand_total_election_exp_by_cadidate
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
              order by TEMP.st_code, TEMP.pc_no asc");
           
            }
            else{ 

           

                    $candList = DB::select("select TEMP.YEAR,ELECTION_TYPE,cpd.cand_hname,TEMP.pc_no,TEMP.st_code,TEMP.district_no,
                    TEMP.party_id,cpd.cand_name,cpd.candidate_id,TEMP.finalized_status,TEMP.finalized_date,TEMP.final_by_ro,
                    TEMP.date_of_declaration,TEMP.grand_total_election_exp_by_cadidate
                    from(
                    select med.YEAR,med.ELECTION_TYPE,cnd.pc_no,
                    cnd.st_code,cnd.district_no,cnd.candidate_id,
                    cnd.party_id,er.finalized_status,
                    er.updated_at as finalized_date, er.final_by_ro,
                    er.date_of_declaration,er.grand_total_election_exp_by_cadidate
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
                    order by TEMP.st_code, TEMP.pc_no asc");
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
                $candidateArray[] = ['S.NO','CANDIDATE NAME', 'STATE NAME','PC NO & PC NAME','YEAR','ELECTION TYPE','TOTAL EXPENDITURE DECLARED BY CANDIDATE(Rs.)'];

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
                    $candidateArr[$i]['grand_total_election_exp_by_cadidate'] =!empty($canwise->grand_total_election_exp_by_cadidate) ? $canwise->grand_total_election_exp_by_cadidate : 0;
                   // $candidateArr[$i]['total_expenditure'] = $this->expenditureModel->getcandidatetotalexpenditure($canwise->candidate_id);
                   // $candidateArr[$i]['total_expenditure'] = !empty($candidateArr[$i]['total_expenditure']) ? 'Rs. '.$candidateArr[$i]['total_expenditure']:0;

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
                   $statelist = $this->commonModel->getallstate();
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
                    //$partyArr[$i]['S.no'] = $i;
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
				//Re-indexing arraylist 
				$partyArray=array_combine(range(1, count($partyArray)), array_values($partyArray)); 
                $headingpartyArray[] = ['State','PC Name','Party Name','Total Expenditure'];
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
                   $statelist = $this->commonModel->getallstate();
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
          

         
// add 24/10/2019 manoj
        $resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
        // end 24/10/2019 manoj
          
        return view('admin.expenditure.summary_report_eci', ['user_data' => $d, 
            'ele_details' => $ele_details, "cand_finalize_ro" => array(),
            'candList' => $candList,
            'Pcdetail' => $Pcdetail, 'stateDetail' =>$stateDetail,
            'winn_data' => $winn_data,
            'statelist'=>$statelist,
            'st_code'=>$st_code,
            'pc_no'=>$pc_no,'resultDeclarationDate'=>$resultDeclarationDate
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
          $statelist = $this->commonModel->getallstate();              
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
// add 24/10/2019 manoj
        $resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
        // end 24/10/2019 manoj


        $pdf = view('admin.expenditure.pdf_tracking_report', compact('candList','stateDetail' ,'Pcdetail', 'winn_data','resultDeclarationDate'));
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
                    $statelist = $this->commonModel->getallstate();
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
                    $statelist = $this->commonModel->getallstate();
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
                    $statelist = $this->commonModel->getallstate();
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
                   $statelist = $this->commonModel->getallstate();
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
                   $statelist = $this->commonModel->getallstate();
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
                   $statelist = $this->commonModel->getallstate();
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
                }
               
                else {
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
                   $statelist = $this->commonModel->getallstate();
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
                } else if ( $st_code == '' && $cons_no == '' ) {
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

####################end Summary Analytical Dashboard #####################################


}

// end class