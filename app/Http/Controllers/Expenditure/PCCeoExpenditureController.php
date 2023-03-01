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
use App\adminmodel\CEOModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Expenditure\ExpenditureModel;
use App\Classes\xssClean;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use App\models\Expenditure\DeoexpenditureModel;
use App\models\Expenditure\EciExpenditureModel;
use DateTime;

class PCCeoExpenditureController extends Controller {

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
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('ceo');
        $this->commonModel = new commonModel();
        $this->expenditureModel = new ExpenditureModel();
		$this->eciexpenditureModel = new EciExpenditureModel();
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
     * @author Devloped Date : 07-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return dashboard By CEO fuction     
     */
    public function dashboard(Request $request) {
        //PC CEO dashboard TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                $st_code = $d->st_code;
                $cons_no = $request->input('pc');
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'PC'.$cons_no; 
                if ($cons_no == '0') {
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
                            ->count();
                } else {
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
                }

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
                return view('admin.pc.ceo.Expenditure.dashboard', ['user_data' => $d, 'startdatacount' => $startdatacount, 'Percent_startdataentry' => $Percent_startdataentry, 'finaldatacount' => $finaldatacount, 'Percent_finaldatacount' => $Percent_finaldatacount, 'formateDefectscount' => $formateDefectscount, 'Percent_formateDefectscount' => $Percent_formateDefectscount, 'expenseunderstated' => $expenseunderstated, 'Percent_expenseunderstated' => $Percent_expenseunderstated, 'Percent_partyFund' => $Percent_partyFund, 'Percent_OthersourcesFund' => $Percent_OthersourcesFund, 'edetails' => $ele_details, 'logedaccount' => $logedaccount, 'Percent_logedaccount' => $Percent_logedaccount, 'notintimeaccount' => $notintimeaccount, 'Percent_notintimeaccount' => $Percent_notintimeaccount, 'cons_no' => $cons_no]);
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
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBydataentryStart By ROPC fuction     
     */
    public function candidateListBydataentryStart(Request $request, $pc) { //dd($request->all());
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $st_code=$d->st_code;
                $xss = new xssClean;
                $cons_no=base64_decode($xss->clean_input($pc));
                $cons_no=!empty($cons_no) ? $cons_no : 0;
                $st_code=!empty($st_code) ? $st_code : 0;
                // echo $st_code.'PC'.$cons_no;
                DB::enableQueryLog();
                if ($cons_no != '0') {
                    $DataentryStartCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } else {
                    $DataentryStartCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                // dd(DB::getQueryLog());
                //dd($DataentryStartCandList);
                return view('admin.pc.ceo.Expenditure.dataentrystart-report', ['user_data' => $d, 'DataentryStartCandList' => $DataentryStartCandList, 'edetails' => $ele_details, 'cons_no' => $cons_no, 'count' => count($DataentryStartCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBydataentryStart TRY CATCH ENDS HERE   
    }

// end dataentry start function

    public function candidateListBydataentryStartgraph(Request $request, $pc) { //dd($request->all());
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $st_code=$d->st_code;
                $xss = new xssClean;
                $cons_no=base64_decode($xss->clean_input($pc));
                $cons_no=!empty($cons_no) ? $cons_no : 0;
                $st_code=!empty($st_code) ? $st_code : 0;

                if ($cons_no == '0') {
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
                            ->count();
                } else {
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
                }
                $entryStartdata = $this->expenditureModel->gettotaldataentryStartdata('PC', $st_code, $cons_no);




                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : 'All';
                $candiatePcName = "All";
                $data = [
                    ['Oveall summary', 'Data entry started'],
                ];
                if (count($entryStartdata) > 0) {
                    foreach ($entryStartdata as $item) {
                        $totalcontestingcandidate = $this->expenditureModel->gettotalContestedCandidate('PC', $d->st_code, $item->pc_no);


                        $data[] = [$item->pc_name, $this->get_percentage($totalcontestingcandidate, $item->total)
                        ];
                    }
                }

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
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByfinalizeData By CEO fuction     
     */
    public function candidateListByfinalizeData(Request $request, $pc) {
        //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                $st_code=$d->st_code;
                $xss = new xssClean;
                $cons_no=base64_decode($xss->clean_input($pc));
                $cons_no=!empty($cons_no) ? $cons_no : 0;
                $st_code=!empty($st_code) ? $st_code : 0;
                if ($cons_no != '0') {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } else {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                //dd($DataentryStartCandList);
                return view('admin.pc.ceo.Expenditure.finalize-report', ['user_data' => $d, 'finalCandList' => $finalCandList, 'edetails' => $ele_details, 'cons_no' => $cons_no, "count" => count($finalCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByfinalizeData TRY CATCH ENDS HERE   
    }

// end candidateListByfinalizeData start function

    public function candidateListByfinalizeDatagraph(Request $request, $pc) {
        //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                $st_code=$d->st_code;
                $xss = new xssClean;
                $cons_no=base64_decode($xss->clean_input($pc));
                $cons_no=!empty($cons_no) ? $cons_no : 0;
                $st_code=!empty($st_code) ? $st_code : 0;

                $totalpc = $this->expenditureModel->gettotalpc('PC', $st_code, $cons_no);


                // dd($totalcontestingcandidate);
                $data = [
                    ['Oveall summary', 'Report Finalised'],
                ];

                if (count($totalpc) > 0) {
                    foreach ($totalpc as $item) {


                        $finaldatacountdata = $this->expenditureModel->gettotaldataentryFinaldata('PC', $st_code, $item->pc_no);




                        if (!empty($finaldatacountdata[0]) && $finaldatacountdata[0]->pc_no == $item->pc_no) {

                            $totalcontestingcandidate = $this->expenditureModel->gettotalContestedCandidate('PC', $d->st_code, $item->pc_no);

                            $data[] = [$item->pc_name, $this->get_percentage($totalcontestingcandidate, $finaldatacountdata[0]->total)];
                        } else {
                            $data[] = [$item->pc_name, 0];
                        }
                    }
                }
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
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBylogedaccount By CEO fuction     
     */
    public function candidateListBylogedaccount(Request $request, $pc) {
        //PC ROPC candidateListBylogedaccount TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $st_code=$d->st_code;
                $xss = new xssClean;
                $cons_no=base64_decode($xss->clean_input($pc));
                $cons_no=!empty($cons_no) ? $cons_no : 0;
                $st_code=!empty($st_code) ? $st_code : 0;
                if ($cons_no != '0') {
                    $logedAccount = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.candidate_lodged_acct', '=', 'Yes')
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } else {
                    $logedAccount = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('expenditure_reports.candidate_lodged_acct', '=', 'Yes')
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                if (empty($logedAccount) || $logedAccount == '')
                    $logedAccount = array();
                //dd($logedAccount);
                return view('admin.pc.ceo.Expenditure.logedaccount-report', ['user_data' => $d, 'logedAccount' => $logedAccount, 'edetails' => $ele_details, 'cons_no' => $cons_no, 'count' => count($logedAccount)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBylogedaccount TRY CATCH ENDS HERE   
    }

// end candidateListBylogedaccount start function

    public function candidateListBylogedaccountgraph(Request $request, $pc) {
        //PC ROPC candidateListBylogedaccount TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                $st_code=$d->st_code;
                $xss = new xssClean;
                $cons_no=base64_decode($xss->clean_input($pc));
                $cons_no=!empty($cons_no) ? $cons_no : 0;
                $st_code=!empty($st_code) ? $st_code : 0;

                $totalpc = $this->expenditureModel->gettotalpc('PC', $st_code, $cons_no);

                $data = [
                    ['Oveall summary', 'Account Lodged'],
                ];

                if (count($totalpc) > 0) {
                    foreach ($totalpc as $item) {


                        $countdata = $this->expenditureModel->gettotallogedaccountdata('PC', $st_code, $item->pc_no);




                        if (!empty($countdata[0]) && $countdata[0]->pc_no == $item->pc_no) {

                            $totalcontestingcandidate = $this->expenditureModel->gettotalContestedCandidate('PC', $d->st_code, $item->pc_no);

                            $data[] = [$item->pc_name, $this->get_percentage($totalcontestingcandidate, $countdata[0]->total)];
                        } else {
                            $data[] = [$item->pc_name, 0];
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
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBynotintime By PCCEO fuction     
     */
    public function candidateListBynotintime(Request $request, $pc) {
        //PC ROPC candidateListBynotintime TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                $st_code=$d->st_code;
                $xss = new xssClean;
                $cons_no=base64_decode($xss->clean_input($pc));
                $cons_no=!empty($cons_no) ? $cons_no : 0;
                $st_code=!empty($st_code) ? $st_code : 0;
                if ($cons_no != '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_personal_detail.cand_name', 'expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.account_lodged_time','=','No') 
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } else {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_personal_detail.cand_name', 'expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.account_lodged_time','=','No') 
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                //dd($DataentryStartCandList);
                return view('admin.pc.ceo.Expenditure.notintime-report', ['user_data' => $d, 'notinTime' => $notinTime, 'edetails' => $ele_details, 'cons_no' => $cons_no, "count" => count($notinTime)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC PCCEO candidateListBynotintime TRY CATCH ENDS HERE   
   }

// end candidateListBynotintime start function

    public function candidateListBynotintimegraph(Request $request, $pc) {
        //PC ROPC candidateListBynotintime TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $st_code=$d->st_code;
                $xss = new xssClean;
                $cons_no=base64_decode($xss->clean_input($pc));
                $cons_no=!empty($cons_no) ? $cons_no : 0;
                $st_code=!empty($st_code) ? $st_code : 0;

                $totalpc = $this->expenditureModel->gettotalpc('PC', $st_code, $cons_no);


                // dd($totalcontestingcandidate);
                $data = [
                    ['Oveall summary', 'Not in Time'],
                ];

                if (count($totalpc) > 0) {
                    foreach ($totalpc as $item) {


                        $acountdata = $this->expenditureModel->gettotaldataentryFinaldata('PC', $st_code, $item->pc_no);




                        if (!empty($acountdata[0]) && $acountdata[0]->pc_no == $item->pc_no) {

                            $totalcontestingcandidate = $this->expenditureModel->gettotalContestedCandidate('PC', $d->st_code, $item->pc_no);

                            $data[] = [$item->pc_name, $this->get_percentage($totalcontestingcandidate, $acountdata[0]->total)];
                        } else {
                            $data[] = [$item->pc_name, 0];
                        }
                    }
                }
                return json_encode($data);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBynotintime TRY CATCH ENDS HERE   
    }

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBydataentryStart By ROPC fuction     
     */
    public function candidateListByformatedefects(Request $request, $pc) {
        //PC ROPC candidateListByformatedefects TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $st_code=$d->st_code;
                $xss = new xssClean;
                $cons_no=base64_decode($xss->clean_input($pc));
                $cons_no=!empty($cons_no) ? $cons_no : 0;
                $st_code=!empty($st_code) ? $st_code : 0;
                if ($cons_no != '0') {
                    $formateDefects = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_personal_detail.cand_name', 'expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.rp_act', 'No')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } else {
                    $formateDefects = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_personal_detail.cand_name', 'expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('expenditure_reports.rp_act', 'No')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                //dd($DataentryStartCandList);
                return view('admin.pc.ceo.Expenditure.formatedefects-report', ['user_data' => $d, 'formateDefects' => $formateDefects, 'edetails' => $ele_details, 'cons_no' => $cons_no, 'count' => count($formateDefects)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByformatedefects TRY CATCH ENDS HERE   
    }

// end candidateListByformatedefects start function

public function candidateListByformatedefectsgraph(Request $request, $pc) {
//PC ROPC candidateListByformatedefects TRY CATCH STARTS HERE
try {
    if (Auth::check()) {
        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);

        $st_code=$d->st_code;
        $xss = new xssClean;
        $cons_no=base64_decode($xss->clean_input($pc));
        $cons_no=!empty($cons_no) ? $cons_no : 0;
        $st_code=!empty($st_code) ? $st_code : 0;

        $totalpc = $this->expenditureModel->gettotalpc('PC', $st_code, $cons_no);


        // dd($totalcontestingcandidate);
        $data = [
            ['Oveall summary', 'Defects in format'],
        ];

        if (count($totalpc) > 0) {
            foreach ($totalpc as $item) {


                $acountdata = $this->expenditureModel->gettotalformatedefectsdata('PC', $st_code, $item->pc_no);




                if (!empty($acountdata[0]) && $acountdata[0]->pc_no == $item->pc_no) {

                    $totalcontestingcandidate = $this->expenditureModel->gettotalContestedCandidate('PC', $d->st_code, $item->pc_no);

                    $data[] = [$item->pc_name, $this->get_percentage($totalcontestingcandidate, $acountdata[0]->total)];
                } else {
                    $data[] = [$item->pc_name, 0];
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

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByronotagree By ROPC fuction     
     */
    public function candidateListByronotagree(Request $request, $pc) {
        //PC ROPC candidateListByronotagree TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                  
   
                $st_code=$d->st_code;
                $xss = new xssClean;
                $cons_no=base64_decode($xss->clean_input($pc));
                $cons_no=!empty($cons_no) ? $cons_no : 0;
                $st_code=!empty($st_code) ? $st_code : 0;
               
                if ($cons_no != '0') {
                    $DataentryStartCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->get();
                } else {
                    $DataentryStartCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                //dd($DataentryStartCandList);
                return view('admin.pc.ro.Expenditure.ronotagree-report', ['user_data' => $d, 'DataentryStartCandList' => $DataentryStartCandList, 'edetails' => $ele_details, 'st_code' => $st_code,'cons_no' => $cons_no]);
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
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByunderstatedexpense By CEO fuction     
     */
public function candidateListByunderstatedexpense(Request $request, $pc) {
//PC ROPC candidateListByunderstatedexpense TRY CATCH STARTS HERE
try {
if (Auth::check()) {
    $user = Auth::user();
    $uid = $user->id;
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


        
$st_code=$d->st_code;
$xss = new xssClean;
$cons_no=base64_decode($xss->clean_input($pc));
$cons_no=!empty($cons_no) ? $cons_no : 0;
$st_code=!empty($st_code) ? $st_code : 0;
    if ($cons_no != '0') {
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
    } else {
        $expenseunderstated = DB::table('expenditure_understated')
                ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                ->where('expenditure_understated.ST_CODE', '=', $st_code)
                //->where('expenditure_understated.constituency_no','=',$cons_no) 
                ->where('expenditure_understated.page_no_observation', '=', "No")
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->groupBy('expenditure_understated.candidate_id')
                ->get();
    }
    //dd($DataentryStartCandList);
    return view('admin.pc.ceo.Expenditure.expenseunderstated-report', ['user_data' => $d, 'expenseunderstated' => $expenseunderstated, 'edetails' => $ele_details, 'cons_no' => $cons_no,
        'count' => count($expenseunderstated)]);
} else {
    return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ROPC candidateListByunderstatedexpense TRY CATCH ENDS HERE   
}

// end candidateListByunderstatedexpense start function

public function candidateListByunderstatedexpensegraph(Request $request, $pc) {
//PC ROPC candidateListByunderstatedexpense TRY CATCH STARTS HERE
try {
    if (Auth::check()) {
        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


            
$st_code=$d->st_code;
$xss = new xssClean;
$cons_no=base64_decode($xss->clean_input($pc));
$cons_no=!empty($cons_no) ? $cons_no : 0;
$st_code=!empty($st_code) ? $st_code : 0;

        $totalpc = $this->expenditureModel->gettotalpc('PC', $st_code, $cons_no);


        // dd($totalcontestingcandidate);
        $data = [
            ['Oveall summary', 'Expenses understated'],
        ];

        if (count($totalpc) > 0) {
            foreach ($totalpc as $item) {


                $finaldatacountdata = $this->expenditureModel->gettotalexpenseUnderStateddata('PC', $st_code, $item->pc_no);

                if (!empty($finaldatacountdata[0]) && $finaldatacountdata[0]->pc_no == $item->pc_no) {

                    $totalcontestingcandidate = $this->expenditureModel->gettotalContestedCandidate('PC', $d->st_code, $item->pc_no);

                    $data[] = [$item->pc_name, $this->get_percentage($totalcontestingcandidate, $finaldatacountdata[0]->total)];
                } else {
                    $data[] = [$item->pc_name, 0];
                }
            }
        }
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
    public function candidateListBydataentrydefects(Request $request, $pc) {
        //PC ROPC candidateListBydataentrydefects TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
               
                $st_code=$d->st_code;
                $xss = new xssClean;
                $cons_no=base64_decode($xss->clean_input($pc));
                $cons_no=!empty($cons_no) ? $cons_no : 0;
                $st_code=!empty($st_code) ? $st_code : 0;

                $cons_no = !empty($cons_no) ? $cons_no : '0';
                if ($cons_no != '') {
                    $DataentryStartCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } else {
                    $DataentryStartCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                //dd($DataentryStartCandList);
                return view('admin.pc.ro.Expenditure.dataentrydefect-report', ['user_data' => $d, 'DataentryStartCandList' => $DataentryStartCandList, 'edetails' => $ele_details, 'cons_no' => $cons_no]);
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
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBypartyfund By CEO fuction     
     */
public function candidateListBypartyfund(Request $request, $pc) {
//PC ROPC candidateListBypartyfund TRY CATCH STARTS HERE
try {
if (Auth::check()) {
    $user = Auth::user();
    $uid = $user->id;
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

        
$st_code=$d->st_code;
$xss = new xssClean;
$cons_no=base64_decode($xss->clean_input($pc));
$cons_no=!empty($cons_no) ? $cons_no : 0;
$st_code=!empty($st_code) ? $st_code : 0;
    if ($cons_no != '0') {
        $partyfund = DB::table('expenditure_fund_parties')
                ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id')
                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id')
                //->select(DB::raw('IFNULL((political_fund_cash + political_fund_checque + political_fund_kind),0) AS partyfund'))
                ->select('candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_parties.*', 'm_party.*')
                ->where('expenditure_fund_parties.ST_CODE', '=', $st_code)
                ->where('expenditure_fund_parties.constituency_no', '=', $cons_no)
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->groupBy('expenditure_fund_parties.candidate_id')
                ->get();
    } else {
        $partyfund = DB::table('expenditure_fund_parties')
                ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id')
                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id')
                //->select(DB::raw('IFNULL((political_fund_cash + political_fund_checque + political_fund_kind),0) AS partyfund'))
                ->select('candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_parties.*', 'm_party.*')
                ->where('expenditure_fund_parties.ST_CODE', '=', $st_code)
                //->where('expenditure_fund_parties.constituency_no','=',$cons_no) 
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->groupBy('expenditure_fund_parties.candidate_id')
                ->get();
    }
    // dd($partyfund);
    return view('admin.pc.ceo.Expenditure.partyfund-report', ['user_data' => $d, 'partyfund' => $partyfund, 'edetails' => $ele_details, 'cons_no' => $cons_no, "count" => count($partyfund)]);
} else {
    return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ROPC candidateListBypartyfund TRY CATCH ENDS HERE   
}

// end candidateListBypartyfund start function

public function candidateListBypartyfundgraph(Request $request, $pc) {
//PC ROPC candidateListBypartyfund TRY CATCH STARTS HERE
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);

            
$st_code=$d->st_code;
$xss = new xssClean;
$cons_no=base64_decode($xss->clean_input($pc));
$cons_no=!empty($cons_no) ? $cons_no : 0;
$st_code=!empty($st_code) ? $st_code : 0;
        $totalpc = $this->expenditureModel->gettotalpc('PC', $st_code, $cons_no);


        // dd($totalcontestingcandidate);
        $data = [
            ['Oveall summary', 'Taken funds from party'],
        ];

        if (count($totalpc) > 0) {
            foreach ($totalpc as $item) {
                $totalPartyfunddata = $this->expenditureModel->gettotalPartyfunddata('PC', $st_code, $item->pc_no);
                $totalOtherSourcesfunddata = $this->expenditureModel->gettotalOtherSourcesfunddata('PC', $st_code, $item->pc_no);


                if (!empty($totalPartyfunddata[0]) && $totalPartyfunddata[0]->pc_no == $item->pc_no) {

                    $totalcontestingcandidate = $this->expenditureModel->gettotalContestedCandidate('PC', $d->st_code, $item->pc_no);

                    $data[] = [$item->pc_name, $this->get_percentage($totalcontestingcandidate, $totalPartyfunddata[0]->total)];
                } else {
                    $data[] = [$item->pc_name, 0];
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
 * @author param return candidateListByothersfund By CEO fuction     
 */
public function candidateListByothersfund(Request $request, $pc) {
//PC ROPC candidateListByothersfund TRY CATCH STARTS HERE
try {
    if (Auth::check()) {
        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

        
$st_code=$d->st_code;
$xss = new xssClean;
$cons_no=base64_decode($xss->clean_input($pc));
$cons_no=!empty($cons_no) ? $cons_no : 0;
$st_code=!empty($st_code) ? $st_code : 0;
        if ($cons_no != '0') {
            $otherfund = DB::table('expenditure_fund_source')
                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                    //->select(DB::raw('IFNULL((other_source_amount),0) AS otherSourcesfund'))
                    ->select('candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_source.*', 'm_party.*')
                    ->where('expenditure_fund_source.ST_CODE', '=', $st_code)
                    ->where('expenditure_fund_source.constituency_no', '=', $cons_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->groupBy('expenditure_fund_source.candidate_id')
                    ->get();
        } else {
            $otherfund = DB::table('expenditure_fund_source')
                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                    // ->select(DB::raw('IFNULL((other_source_amount),0) AS otherSourcesfund'))
                    ->select('candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_source.*', 'm_party.*')
                    ->where('expenditure_fund_source.ST_CODE', '=', $st_code)
                    //->where('expenditure_fund_source.constituency_no','=',$cons_no) 
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->groupBy('expenditure_fund_source.candidate_id')
                    ->get();
        }
        //dd($DataentryStartCandList);
        return view('admin.pc.ceo.Expenditure.otherfund-report', ['user_data' => $d, 'otherfund' => $otherfund, 'edetails' => $ele_details, 'cons_no' => $cons_no, "count" => count($otherfund)]);
    } else {
        return redirect('/officer-login');
    }
} catch (Exception $ex) {
    return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ROPC candidateListByothersfund TRY CATCH ENDS HERE   
}

// end candidateListByothersfund start function

public function candidateListByothersfundgraph(Request $request, $pc) {
//PC ROPC candidateListByothersfund TRY CATCH STARTS HERE
try {
if (Auth::check()) {
    $user = Auth::user();
    $uid = $user->id;
    $d = $this->commonModel->getunewserbyuserid($user->id);

        
$st_code=$d->st_code;
$xss = new xssClean;
$cons_no=base64_decode($xss->clean_input($pc));
$cons_no=!empty($cons_no) ? $cons_no : 0;
$st_code=!empty($st_code) ? $st_code : 0;

    $totalpc = $this->expenditureModel->gettotalpc('PC', $st_code, $cons_no);


    // dd($totalcontestingcandidate);
    $data = [
        ['Oveall summary', 'Taken funds from other sources'],
    ];

    if (count($totalpc) > 0) {
        foreach ($totalpc as $item) {
            $totalPartyfunddata = $this->expenditureModel->gettotalPartyfunddata('PC', $st_code, $item->pc_no);
            $totalOtherSourcesfunddata = $this->expenditureModel->gettotalOtherSourcesfunddata('PC', $st_code, $item->pc_no);


            if (!empty($totalPartyfunddata[0]) && $totalPartyfunddata[0]->pc_no == $item->pc_no) {

                $totalcontestingcandidate = $this->expenditureModel->gettotalContestedCandidate('PC', $d->st_code, $item->pc_no);

                $data[] = [$item->pc_name, $this->get_percentage($totalcontestingcandidate, $totalPartyfunddata[0]->total)];
            } else {
                $data[] = [$item->pc_name, 0];
            }
        }
    }
    return json_encode($data);
} else {
    return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ROPC candidateListByothersfund TRY CATCH ENDS HERE   
}

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByexeedceiling By ROPC fuction     
     */
public function candidateListByexeedceiling(Request $request, $pc) {
//PC ROPC candidateListByexeedceiling TRY CATCH STARTS HERE
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                  
$st_code=$d->st_code;
$xss = new xssClean;
$cons_no=base64_decode($xss->clean_input($pc));
$cons_no=!empty($cons_no) ? $cons_no : 0;
$st_code=!empty($st_code) ? $st_code : 0;
        if ($cons_no != '0') {
            $DataentryStartCandList = DB::table('expenditure_reports')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();
        } else {
            $DataentryStartCandList = DB::table('expenditure_reports')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                    //->where('expenditure_reports.constituency_no','=',$cons_no) 
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();
        }
        //dd($DataentryStartCandList);
        return view('admin.pc.ro.Expenditure.exceedceiling-report', ['user_data' => $d, 'DataentryStartCandList' => $DataentryStartCandList,  'edetails' => $ele_details, 'cons_no' => $cons_no]);
    } else {
        return redirect('/officer-login');
    }
} catch (Exception $ex) {
    return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ROPC candidateListByexeedceiling TRY CATCH ENDS HERE   
}

// end candidateListByexeedceiling start function

    public function edituser($eid = '', Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $uid = $user->id;
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $d = $this->commonModel->getunewserbyuserid($uid);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $eid = Crypt::decrypt($eid);
            $rec = getById('officer_login', 'id', $eid);
            //dd($rec);   
            return view('admin.pc.ceo.officer-profile', ['user_data' => $d, 'offrecords' => $rec]);
        } else {
            return redirect('/officer-login');
        }
    }

// end dashboard function
    ########################status dashboard by Niraj 16-05-2019###################

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 07-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return statusdashboard By CEO fuction     
     */
    public function statusdashboard(Request $request) {
        //PC CEO dashboard TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                //shishir
             $scrutinycandidatecount = DB::table('expenditure_notification')
                            ->leftjoin('candidate_nomination_detail', 'expenditure_notification.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                           ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                           ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->leftjoin('expenditure_reports','expenditure_reports.candidate_id','=','expenditure_notification.candidate_id')
                           ->where('candidate_nomination_detail.st_code', '=', $d->st_code)
                           ->where('candidate_nomination_detail.application_status', '=', '6')
                           ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                           ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                           ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->Where('expenditure_notification.ceo_read_status', '=', '0')
                            ->Where('expenditure_notification.st_code', '=',$d->st_code)
                            ->Where('expenditure_reports.final_by_ro','=','1')
                           ->count();
            $request->session()->put('countscrutiny', $scrutinycandidatecount);
             //shishir


                $st_code = $d->st_code;
                $cons_no = $request->input('pc');
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'PC'.$cons_no; 
                if ($cons_no == '0') {
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
                            ->count();
                   $totalElectedCandidate=DB::table('winning_leading_candidate')
                            ->where('winning_leading_candidate.st_code','=',$st_code)                         
                            ->count();
                } else {
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
                }

                //Get Data entry Start Count 
                  $startdatacount = $this->eciexpenditureModel->gettotaldataentryStart('PC', $st_code, $cons_no);
				//dd($startdatacount);
             
                //Get Data entry Start Count %
                $Percent_startdatacount = $this->get_percentage($totalContestedCandidate, $startdatacount);

                //get pending data count
                $pendingdatacount = $totalContestedCandidate - $startdatacount;
                //Get Data entry pendingdatacount Count %
                $Percent_pendingdatacount = $this->get_percentage($totalContestedCandidate, $pendingdatacount);

                //get partially pending data count
                $finalbyDEO = $this->eciexpenditureModel->gettotalfinalbyDEO('PC', $st_code, $cons_no);
                 // dd($totalContestedCandidate.'=>'.$finalbyDEO);
              //pending at DEO
             if($finalbyDEO >= 0 ){
               $partiallypendingcount= $totalContestedCandidate -($finalbyDEO);
               }
                //Get Data entry Start Count %
                $Percent_partiallypendingcount = $this->get_percentage($totalContestedCandidate, $partiallypendingcount);

                //Get Data entry finalize Count 
                $finaldata = $this->expenditureModel->gettotaldataentryFinal('PC', $st_code, $cons_no);
				
				$finaldatacount=count($finaldata);
                //Get Data entry finalize Count %
                $Percent_finaldatacount = $this->get_percentage($totalContestedCandidate, $finaldatacount);

                //get partially pending data count
                $defaulter = $this->expenditureModel->getdefaulter('PC', $st_code, $cons_no);
                if (empty($defaulter))
                    $defaulter = [];
                $defaultercount = count($defaulter);
                //Get Data entry Start Count %
                $Percent_defaultercount = $this->get_percentage($totalContestedCandidate, $defaultercount);

             
		

                //Get final by eci Count 
              $finalbyecicount = $this->eciexpenditureModel->gettotalfinalbyeci('PC', $st_code, $cons_no);
                //Getfinal by eci Count %
                $Percent_finalbyecicount = $this->get_percentage($totalContestedCandidate, $finalbyecicount);
				$finalcompletedcount=$this->eciexpenditureModel->gettotalCompletedbyEci('PC',$st_code,$cons_no);
	            $disqualifiedcount=$this->eciexpenditureModel->gettotalDisqualifiedbyEci('PC',$st_code,$cons_no);
				
						 //pending at CEO	
				if($startdatacount >=  0 && $finalbyecicount >=0 && $finalcompletedcount >=0){
				 $finalbyceocount = $startdatacount-($finalbyecicount + $finalcompletedcount + $disqualifiedcount);
				}
                // dd($finalbyceocount);
                //Get Data entry final by ceo %
                $Percent_finalbyceocount = $this->get_percentage($totalContestedCandidate, $finalbyceocount);

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

                 //Get noticeatceocount Count 
	             $noticeatceocount = $this->expenditureModel->gettotalnoticeatCEO('PC', $st_code, $cons_no);
                 //Get noticeatceocount  %
                 $Percent_noticeatceocount = $this->get_percentage($totalContestedCandidate, $noticeatceocount);
	

               //dd($Percent_startdataentry);
               return view('admin.pc.ceo.Expenditure.statusdashboard', ['user_data' => $d,
                   'startdatacount' => $startdatacount, 'Percent_startdatacount' => $Percent_startdatacount,
                   'pendingdatacount' => $pendingdatacount, 'Percent_finaldatacount' => $Percent_finaldatacount,
                   'finaldatacount' => $finaldatacount, 'Percent_pendingdatacount' => $Percent_pendingdatacount,
                   'partiallypendingcount' => $partiallypendingcount,
                   'Percent_partiallypendingcount' => $Percent_partiallypendingcount, 
                   'defaultercount' => $defaultercount, 'Percent_defaultercount' => $Percent_defaultercount,
                   'finalbyceocount' => $finalbyceocount, 'Percent_finalbyceocount' => $Percent_finalbyceocount,
                   'finalbyecicount' => $finalbyecicount, 'Percent_finalbyecicount' => $Percent_finalbyecicount,
                   'noticeatceocount' => $noticeatceocount, 'Percent_noticeatceocount' => $Percent_noticeatceocount,
                   'edetails' => $ele_details, 
                   'returncount'=>$returncount,
                   'Percent_returncount'=>$Percent_returncount,
                   'nonreturncount'=>$nonreturncount,
                   'Percent_nonreturncount'=>$Percent_nonreturncount,
                   'cons_no' => $cons_no]);
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
     * @author param return getpendingcandidateList By PCCEO fuction     
     */
    public function getpendingcandidateList(Request $request, $pc) { //dd($request->all());
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                  
$st_code=$d->st_code;
$xss = new xssClean;
$cons_no=base64_decode($xss->clean_input($pc));
$cons_no=!empty($cons_no) ? $cons_no : 0;
$st_code=!empty($st_code) ? $st_code : 0;
                DB::enableQueryLog();
                if ($cons_no != '0') {
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
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();
                } else {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
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
                }
                // dd(DB::getQueryLog());
                //dd($DataentryStartCandList);
                return view('admin.pc.ceo.Expenditure.pending-report', ['user_data' => $d, 'pendingCandList' => $pendingCandList, 'edetails' => $ele_details, 'cons_no' => $cons_no, 'count' => count($pendingCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBydataentryStart TRY CATCH ENDS HERE   
    }

// end dataentry start function

    public function getpendingcandidateListgraph(Request $request, $pc) { //dd($request->all());
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                 
$st_code=$d->st_code;
$xss = new xssClean;
$cons_no=base64_decode($xss->clean_input($pc));
$cons_no=!empty($cons_no) ? $cons_no : 0;
$st_code=!empty($st_code) ? $st_code : 0;

                $totalcontestingcandidate = DB::table('candidate_nomination_detail')
                        ->select('candidate_nomination_detail.pc_no as pc_no', 'm_pc.PC_NAME as pc_name', DB::raw('count(*) as total'))
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->join('m_pc', 'candidate_nomination_detail.pc_no', '=', 'm_pc.PC_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('m_pc.ST_CODE', '=', $st_code)
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->groupBy('candidate_nomination_detail.pc_no')
                        ->get();





                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : 'All';
                $candiatePcName = "All";
                $data = [
                    ['Oveall summary', 'Pending / Not filed'],
                ];


                if (count($totalcontestingcandidate) > 0) {
                    foreach ($totalcontestingcandidate as $item) {
                        $Totalcandidatereports = $this->expenditureModel->getTotalcandidatereports('PC', $st_code, $item->pc_no);
                        if (count($Totalcandidatereports) > 0) {

                            foreach ($Totalcandidatereports as $item2) {
                                if (!empty($item2) && $item2->pc_no == $item->pc_no) {



                                    $data[] = [$item->pc_name, $this->get_percentage($item->total, $item2->total)];
                                }
                            }
                        } else {
                            $data[] = [$item->pc_name, 0];
                        }
                    }
                }
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
     * @author Devloped Date : 16-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getpartiallypendingcandidateList By PCCEO fuction     
     */
    public function getpartiallypendingcandidateList(Request $request, $pc) { //dd($request->all());
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                 
$st_code=$d->st_code;
$xss = new xssClean;
$cons_no=base64_decode($xss->clean_input($pc));
$cons_no=!empty($cons_no) ? $cons_no : 0;
$st_code=!empty($st_code) ? $st_code : 0;
                // echo $st_code.'PC'.$cons_no;
                DB::enableQueryLog();
                if ($cons_no != '0') {
                     $finalbyDEO = DB::table('expenditure_reports')
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
                             foreach ($finalbyDEO as $finalbyDEOData) {
                        $candidate_id[] = $finalbyDEOData->candidate_id;
                    }
                  
                     $partiallyCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('candidate_nomination_detail.candidate_id')
                        ->get();

                    
                   
                } else {
                    $finalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                             foreach ($finalbyDEO as $finalbyDEOData) {
                        $candidate_id[] = $finalbyDEOData->candidate_id;
                    }
                     //dd(count($candidate_id));
                     $partiallyCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                             ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                             ->groupBy('candidate_nomination_detail.candidate_id')
                        ->get();

                
                }
                // dd(DB::getQueryLog());
                //dd($DataentryStartCandList);
                return view('admin.pc.ceo.Expenditure.partiallypending-report', ['user_data' => $d, 'partiallyCandList' => $partiallyCandList, 'edetails' => $ele_details, 'cons_no' => $cons_no, 'count' => count($partiallyCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC CEO getpartiallypendingcandidateList TRY CATCH ENDS HERE   
    }

// end getpartiallypendingcandidateList start function

    public function getpartiallypendingcandidateListgraph(Request $request, $pc) { //dd($request->all());
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

   
                $st_code=$d->st_code;
                $xss = new xssClean;
                $cons_no=base64_decode($xss->clean_input($pc));
                $cons_no=!empty($cons_no) ? $cons_no : 0;
                $st_code=!empty($st_code) ? $st_code : 0;
                //============
                $totalcontestingcandidate = DB::table('candidate_nomination_detail')
                        ->select('candidate_nomination_detail.pc_no as pc_no', 'm_pc.PC_NAME as pc_name', DB::raw('count(*) as total'))
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->join('m_pc', 'candidate_nomination_detail.pc_no', '=', 'm_pc.PC_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('m_pc.ST_CODE', '=', $st_code)
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->groupBy('candidate_nomination_detail.pc_no')
                        ->get();




                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : 'All';
                $candiatePcName = "All";
                $data = [
                    ['Oveall summary', 'Partially Pending'],
                ];


                if (count($totalcontestingcandidate) > 0) {
                    foreach ($totalcontestingcandidate as $item) {
                        $Totalcandidatereports = $this->expenditureModel->getpartialTotalcandidatereports('PC', $st_code, $item->pc_no);
                        if (count($Totalcandidatereports) > 0) {

                            foreach ($Totalcandidatereports as $item2) {
                                if (!empty($item2) && $item2->pc_no == $item->pc_no) {



                                    $data[] = [$item->pc_name, $this->get_percentage($item->total, $item2->total)];
                                }
                            }
                        } else {
                            $data[] = [$item->pc_name, 0];
                        }
                    }
                }

                $data = array_unique($data, SORT_REGULAR);

                return json_encode(array_values($data));
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC CEO getpartiallypendingcandidateList TRY CATCH ENDS HERE   
    }

// end getpartiallypendingcandidateList start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 16-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getdefaultercandidateList By PCCEO fuction     
     */
    public function getdefaultercandidateList(Request $request, $pc) {
        //dd($request->all());
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                
$st_code=$d->st_code;
$xss = new xssClean;
$cons_no=base64_decode($xss->clean_input($pc));
$cons_no=!empty($cons_no) ? $cons_no : 0;
$st_code=!empty($st_code) ? $st_code : 0;
                // echo $st_code.'PC'.$cons_no;
                DB::enableQueryLog();
                if ($cons_no != '0') {
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
                } else {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<=', 'totalcandamnt')
                            ->where('expenditure_understated.ST_CODE', '=', $st_code)
                            //->where('expenditure_understated.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                }
                // dd(DB::getQueryLog());
                //dd($DataentryStartCandList);
                return view('admin.pc.ceo.Expenditure.defaulter-report', ['user_data' => $d, 'defaulterCandList' => $defaulterCandList, 'edetails' => $ele_details, 'cons_no' => $cons_no, 'count' => count($defaulterCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC CEO getdefaultercandidateList TRY CATCH ENDS HERE   
    }

// end getdefaultercandidateList start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 18-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByfiledData By CEO fuction     
     */
    public function candidateListByfiledData(Request $request, $pc) {
        //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
   
                $st_code=$d->st_code;
                $xss = new xssClean;
                $cons_no=base64_decode($xss->clean_input($pc));
                $cons_no=!empty($cons_no) ? $cons_no : 0;
                $st_code=!empty($st_code) ? $st_code : 0;
				
                if ($cons_no != '0') {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            // ->where('expenditure_reports.finalized_status','=','1') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } else {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            // ->where('expenditure_reports.finalized_status','=','1') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                //dd($DataentryStartCandList);
                return view('admin.pc.ceo.Expenditure.filed-report', ['user_data' => $d, 'finalCandList' => $finalCandList, 'edetails' => $ele_details, 'cons_no' => $cons_no, "count" => count($finalCandList)]);
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
 * @author param return candidateListfinalbyCEO By CEO fuction     
 */
public function candidateListfinalbyCEO(Request $request, $pc) {
//PC CEO candidateListfinalbyCEO TRY CATCH STARTS HERE
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
      
$st_code=$d->st_code;
$xss = new xssClean;
$cons_no=base64_decode($xss->clean_input($pc));
$cons_no=!empty($cons_no) ? $cons_no : 0;
$st_code=!empty($st_code) ? $st_code : 0;
$candidate_id=[];
    if ($cons_no != '0') {
		
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
        
    } else {
       
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
    }
    //dd($DataentryStartCandList);
    return view('admin.pc.ceo.Expenditure.finalbyceo-report', ['user_data' => $d, 'finalbyceoCandList' => $finalbyceoCandList, 'edetails' => $ele_details, 'cons_no' => $cons_no, "count" => count($finalbyceoCandList)]);
} else {
    return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ROPC candidateListByfinalizeData TRY CATCH ENDS HERE 
}

// end candidateListfinalbyECI start function
    ########################end status dashboard by Niraj 16-05-2019##############
    ###############################Notice CEO  09-07-2019 Start By Niraj######################################
  /**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 09-07-2019
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return getnoticeatCEO By ECI fuction     
 */
public function getnoticeatCEO(Request $request,$pc){
    //PC ECI getnoticeatCEO TRY CATCH STARTS HERE
    try{
    if(Auth::check()){
        $user = Auth::user();
        $uid=$user->id;
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        $st_code=$d->st_code;
        $xss = new xssClean;
        $cons_no=base64_decode($xss->clean_input($pc));
        $cons_no=!empty($cons_no) ? $cons_no : 0;
        $st_code=!empty($st_code) ? $st_code : 0;
        // echo $st_code.'cons_no'.$cons_no; die;
      
        if($cons_no=='0'){
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
    }elseif($cons_no !='0'){
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
        return view('admin.pc.ceo.Expenditure.noticeatceo',['user_data' => $d,'noticeatCEO' => $noticeatCEO,'edetails'=>$ele_details,'st_code'=>$st_code,'cons_no'=>$cons_no,'count'=>count($noticeatCEO)]); 
        
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
    public function getnoticeatCEOEXL(Request $request,$pc){  
    //ECI getnoticeatCEOEXL EXCEL REPORT TRY CATCH BLOCK STARTS
    try{
        if(Auth::check()){
        $user = Auth::user();
        $uid=$user->id;
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        $st_code=$d->st_code;
        $xss = new xssClean;
        $cons_no=base64_decode($xss->clean_input($pc));
        $cons_no=!empty($cons_no) ? $cons_no : 0;
        $st_code=!empty($st_code) ? $st_code : 0;
        // echo  $st_code.'pc'.$cons_no; die;
       $cur_time    = Carbon::now();
    
       \Excel::create('CEONoticeAtCEO_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
    $excel->sheet('Sheet1', function($sheet) use($st_code,$cons_no) {
    
       if($st_code !='0' && $cons_no=='0'){
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
                                'PC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging'
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
        //PCCEO getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK ENDS
        
    }
#########################################End Notice Section by Niraj##########################


    public function getdefaultercandidateListgraph(Request $request, $pc) {
        //dd($request->all());
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                $st_code = $d->st_code;
                $cons_no = $pc;
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                $totalcontestingcandidate = DB::table('candidate_nomination_detail')
                        ->select('candidate_nomination_detail.pc_no as pc_no', 'm_pc.PC_NAME as pc_name', DB::raw('count(*) as total'))
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->join('m_pc', 'candidate_nomination_detail.pc_no', '=', 'm_pc.PC_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('m_pc.ST_CODE', '=', $st_code)
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->groupBy('candidate_nomination_detail.pc_no')
                        ->get();
                $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
                $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : 'All';
                $candiatePcName = "All";
                $data = [
                    ['Oveall summary', 'Defaulter case'],
                ];
                if (count($totalcontestingcandidate) > 0) {
                    foreach ($totalcontestingcandidate as $item) {
                        $Totalcandidatereports = $this->expenditureModel->getTotalDefaultreports('PC', $st_code, $item->pc_no);
                        if (count($Totalcandidatereports) > 0) {

                            foreach ($Totalcandidatereports as $item2) {
                                if (!empty($item2) && $item2->pc_no == $item->pc_no) {



                                    $data[] = [$item->pc_name, $this->get_percentage($item->total, $item2->total)];
                                }
                            }
                        } else {
                            $data[] = [$item->pc_name, 0];
                        }
                    }
                }

                $data = array_unique($data, SORT_REGULAR);

                return json_encode(array_values($data));
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC CEO getdefaultercandidateList TRY CATCH ENDS HERE   
    }

    // manoj start
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

    public function getprofile(Request $request) {
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
                return view('admin.expenditure.GetProfile', compact('profileData'));
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function getTrackingByCEOUserId(Request $request) {

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
            $data = DB::select(" SELECT
                                C.candidate_id,
                                C.cand_name,
                                C.cand_email,
                                P.PC_NAME ,
                                R.date_of_declaration
                              FROM
                                `expenditure_reports` R 
                              INNER JOIN
                                candidate_personal_detail C ON C.candidate_id = R.candidate_id
                                 INNER JOIN m_pc P ON
                                     P.PC_NO = R.constituency_no AND P.ST_CODE =R.ST_CODE
                              WHERE
                                R.ST_CODE = '$d->st_code' $condtition");
            $total_rec = count($data);
            $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();

            return view('admin.pc.ceo.Expenditure.tracking', ['user_data' => $d, 'ele_details' => $ele_details, "total_rec" => $total_rec, "cand_finalize_ro" => array(), "electionType" => $electionType, "expenditureData" => $data]);
        } else {
            return redirect('/officer-login');
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

    // manoj end
//////////////manish start////////////////
/////////////////manish////////////
    public function GetTrackingReportData(Request $request) {

        if (Auth::check()) {
            $request = (array) $request->all();
            $user = Auth::user();
            $uid = $user->id;
            $namePrefix = \Route::current()->action['prefix'];
            $d = $this->expenditureModel->getunewserbyuserid($user->id, $user->role_id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
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

                if (!empty($_GET['pcname'])) {
                    $pcname = $_GET['pcname'];
                    $condtition .= " AND er.constituency_no='$pcname'";
                }



                $ReportData = $this->expenditureModel->GetExpeditureData($user->role_id, $user->pc_no, $user->st_code, $condtition);
                $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();
                $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray();
                $current_status = DB::table('expenditure_mis_current_sataus')->get()->toArray();


                return view('admin.expenditure.tracking_pceo', ['user_data' => $d, 'ele_details' => $ele_details, "cand_finalize_ro" => array(), "electionType" => $electionType, "expenditureData" => $ReportData, "total_rec" => count($ReportData), "nature_of_default_ac" => $nature_of_default_ac, "current_status" => $current_status]);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        } else {
            return redirect('/officer-login');
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
            return view('admin.pc.ceo.Expenditure.GetScrutinyReport', compact('expensesourecefundbyitem', 'scrutinyReportData', 'expenseunderstated', 'expenseunderstatedbyitem'));
        } else {
            
        }
    }
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

            $candidate_id = base64_decode($_GET['candidate_id']);
            $ReportId = !empty($_GET['candidate_id']) ? $_GET['candidate_id'] : "";
            $namePrefix = \Route::current()->action['prefix'];
            $candidate_data = $this->expenditureModel->getunewserbyuserid_uid_ceo($candidate_id);
            $d = $this->expenditureModel->getunewserbyuserid($user->id, $user->role_id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

            $current_status = DB::table('expenditure_mis_current_sataus')->get()->toArray();
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


                return view('admin.expenditure.createmisexpensereport', ['user_data' => $d, 'ele_details' => $ele_details, "cand_finalize_ro" => array(), "electionType" => $electionType, "ReportSingleData" => $ReportSingleData, "current_status" => $current_status, "nature_of_default_ac" => $nature_of_default_ac, "candidate_data" => (array) $candidate_data,'countElectedCandidate'=>$countElectedCandidate,'resultDeclarationDate'=>$resultDeclarationDate]);
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

        $candidate_id = $request['candidate_id'];

        $request['user_id'] = $uid;
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);
        $send_notice_deo = !empty($request['send_notice_deo']) ? $request['send_notice_deo'] : '';
        $comment_by_ceo = !empty($request['comment_by_ceo']) ? $request['comment_by_ceo'] : '';
        $date_sending_notice_service_to_deo = !empty($request['date_sending_notice_service_to_deo'])?$request['date_sending_notice_service_to_deo']:"";

        unset($request['send_notice_deo']);

        try {
            $data_arr = array();
            foreach ($request as $key => $req_data) {
                $xss = new xssClean;
                $data_arr[$key] = $xss->clean_input($req_data);
            }



            $unsetItems = ['candidate_id', 'constituency_no', 'constituency_nos', 'contensting_candiate',
                'date_of_declaration', 'user_id'];
            $dataUpdate = array_diff_key($data_arr, array_flip($unsetItems));

            if ($send_notice_deo == "deo") {
                $dataUpdate['final_by_ro'] = '0';
            }


            $updateStatus = DB::table('expenditure_reports')->where('candidate_id', $candidate_id)->update($dataUpdate);

             ////////////////////////////////// add entry in expenditure action logs/////////////////
               $cdate = date('Y-m-d h:i:s');
               $data_action=array("candidate_id"=>$candidate_id,"ceo_action_date"=>$cdate,"ceo_comment"=>$comment_by_ceo,"ceo_action_sending_date"=>$date_sending_notice_service_to_deo);
               $data_arr_action = array();
                foreach ($data_action as $key => $req_data_action) {
                    $xss = new xssClean;
                    $data_arr_action[$key] = $xss->clean_input($req_data_action);
                }
              // print_r($data_action);die;
               $data_actionInserted = $this->commonModel->updatedata('expenditure_action_logs', 'candidate_id', $candidate_id, $data_arr_action);

               // $data_arr_action = array();
               //  foreach ($data_action as $key => $req_data_action) {
               //      $xss = new xssClean;
               //      $data_arr_action[$key] = $xss->clean_input($req_data_action);
               //  }

               // $data_actionInserted = $this->commonModel->insertData('expenditure_action_logs', $data_arr_action);
              ///////////////////////////////////////// end entry in expenditure logs///////////////////
//DB::enableQueryLog();

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

    public function updateData(Request $request) {
        $request = (array) $request->all();
        // print_r($request);die;
        if (!empty($request)) {
            $updateTrackData = $this->commonModel->updatedata('expenditure_reports', 'id', $request['tbid'], array($request['column'] => $request['value']));
            if ($updateTrackData) {
                return 1;
            } else {
                return 0;
            }
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

    public function saveComment(Request $request) {
        $request = (array) $request->all();
        $comment_by_ceo = !empty($request['comment']) ? $request['comment'] : "";
        if (!empty($request)) {
            $insertComment = $this->commonModel->updatedata('expenditure_reports', 'candidate_id', $request['candidate_id'], array("comment_by_ceo" => $comment_by_ceo));
            if ($insertComment) {
                return 1;
            } else {
                return 0;
            }
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
            $insertdata = ['candidate_id' => $candidate_id, 'st_code' => $st_code, 'constituency_no' => $pc_no, 'ceo_action' => '1'];
             
       

        $insertComment = $this->commonModel->updatedata('expenditure_reports', 'candidate_id', $candidate_id, array("final_by_ceo" => '1'));
        $update = $this->commonModel->updatedata('expenditure_notification', 'candidate_id', $candidate_id, array("ceo_action" => '1'));
        
        if ($insertComment) {
           // $this->commonModel->insertData('expenditure_notification', $insertdata);

            return 1;
        } else {
            return 0;
        }
         }else{
             echo 0;
         }
    }

    public function GetProfileCEO(Request $request) {
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
    
    public function getReturn(Request $request, $pc) {
        
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);               
                $xss = new xssClean;                 
                $cons_no=base64_decode($xss->clean_input($pc));
                $st_code = $d->st_code;               
                $cons_no = !empty($cons_no) ? $cons_no : '0';             
              
                if ($cons_no != '0') {
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
                } else {
                     $returnCandList = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
			->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
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
                }                  
              
                    $count=!empty($returnCandList)?count($returnCandList):0;
              
                
                return view('admin.pc.ceo.Expenditure.return-report', ['user_data' => $d, 'returnCandList' => $returnCandList ,
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
     public function getNonReturn(Request $request,$pc) {
        
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);              $xss = new xssClean;
                 $st_code = $d->st_code;
                                
                 $cons_no=base64_decode($xss->clean_input($pc));
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                
                if ($cons_no != '0') {
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
                } else {
                     $nonreturnCandList = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
			->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
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
                }            
                
                 
              
                    $count=!empty($nonreturnCandList)?count($nonreturnCandList):0;
                
                return view('admin.pc.ceo.Expenditure.non-return-report', ['user_data' => $d, 'nonreturnCandList' => $nonreturnCandList ,
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


    public function updateStatusReport(Request $request) {
         if (Auth::check()) {
         $user = Auth::user();
         $uid = $user->id;

        $candidateId = $_GET['candidate_id'];
        $reason = $_GET['reason'];

       // $getLog = DB::table('expenditure_logs')->where('created_by',$uid)->where('candidate_id',$candidateId)->first();
        // $countByCEO = !empty($getLog)?$getLog->count_by_ceo:0;
        // $count_by_ceo = $countByCEO + 1;
        $data_definalization = array('candidate_id'=>$candidateId,'created_by'=>$uid,'updated_by'=>$uid,'comment'=>$reason,"count_by_ceo"=>'1','log_type'=>'DEFINALIZATION','officer_level'=>'CEO');

        if ($candidateId){
            $updateStatus = $this->commonModel->updatedata('expenditure_reports', 'candidate_id', $candidateId, array("finalized_status" => "0","final_by_ro"=>'0'));
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



    public function getcandidateList(request $request) {
        //dd($request->all());
        DB::enableQueryLog();
        if (Auth::check()) {
            $user = Auth::user();
            $uid = $user->id;
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $state = $user->st_code;
            $conditions="";
            
            if(!empty($_GET['pc'])){
            $pc = $_GET['pc'];
            $conditions .=" and candidate_nomination_detail.pc_no='$pc' ";
              }  

            if(!empty($conditions)){
                         $candList = DB::select("select `candidate_nomination_detail`.*, `candidate_personal_detail`.*, `m_election_details`.*, `expenditure_reports`.`finalized_status`, `expenditure_reports`.`updated_at` as `finalized_date`, `expenditure_reports`.`final_by_ro`,`expenditure_reports`.`date_of_declaration` ,`expenditure_reports`.`last_date_prescribed_acct_lodge` from `candidate_nomination_detail` left join `candidate_personal_detail` on `candidate_nomination_detail`.`candidate_id` = `candidate_personal_detail`.`candidate_id` inner join `m_election_details` on `m_election_details`.`st_code` = `candidate_nomination_detail`.`st_code` and `m_election_details`.`CONST_NO` = `candidate_nomination_detail`.`pc_no` left join `expenditure_reports` on `expenditure_reports`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` where `candidate_nomination_detail`.`application_status` = 6 and `candidate_nomination_detail`.`party_id` <> 1180 and `candidate_nomination_detail`.`finalaccepted` = '1' and `m_election_details`.`CONST_TYPE` = 'PC' and `expenditure_reports`.`finalized_status` = '1' and candidate_nomination_detail.st_code='$state' $conditions");
            }
            else{ 
            $candList = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                    })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'm_election_details.*', 'expenditure_reports.finalized_status', 'expenditure_reports.updated_at as finalized_date', 'expenditure_reports.final_by_ro', 'expenditure_reports.date_of_declaration', 'expenditure_reports.last_date_prescribed_acct_lodge')
                     ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                      ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('m_election_details.CONST_TYPE', '=', 'PC')
                    ->where('expenditure_reports.finalized_status', '=', '1')
                    ->get();
               }

               if(!empty($candList))
               {
                foreach ($candList as $value) {
                        $getLog = DB::table('expenditure_logs')->where('created_by',$uid)->where('candidate_id',$value->candidate_id)->count();   
                        $value->count_by_ceo = $getLog;
                }
               }
            // dd(DB::getQueryLog());
            // dd($candList);
            return view('admin.pc.ceo.Expenditure.FinalizedcandidateList', ['user_data' => $d, 'ele_details' => $ele_details, 'candList' => $candList]);
        } else {
            return redirect('/officer-login');
        }
    }
  
  #################################Start MIS Report By Niraj 21-08-2019#####################################

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getOfficersmis By CEO fuction     
     */  
    public function getOfficersmis(Request $request) { 
        //dd($request->all());
        //PC ECI getOfficersmis TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $st_code=$d->st_code;
                $cons_no = $request->input('pc');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
             // echo  $st_code.'cons_no=>'.$cons_no; die;
                 DB::enableQueryLog();
                if (empty($cons_no)) { 
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
                            ->groupBy("candidate_nomination_detail.pc_no")
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
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.pc_no")
                            ->get();
                }
                //dd(DB::getQueryLog());
                // dd($totalContestedCandidatedata);
                return view('admin.pc.ceo.Expenditure.mis-officer', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'st_code' => $st_code,'cons_no' => $cons_no, 'count' => count($totalContestedCandidatedata)]);
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
public function getOfficersmisEXL(Request $request,$pc) {
//ECI ACTIVE USERS EXCEL REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
    $user = Auth::user();
    $uid = $user->id;
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
    $xss = new xssClean;
    $st_code = $d->st_code;
    
    $cons_no = base64_decode($xss->clean_input($pc));
    $st_code = !empty($st_code) ? $st_code : 0;
    $cons_no = !empty($cons_no) ? $cons_no : 0;
    // echo  $st_code.'pc'.$cons_no; die;
    // dd($totalContestedCandidate);

    $cur_time = Carbon::now();

    \Excel::create('CEOPCMISExcel_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
        $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

            if (empty($cons_no)) { 
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
                        ->groupBy("candidate_nomination_detail.pc_no")
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
                        ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                        ->groupBy("candidate_nomination_detail.pc_no")
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
			$TotalNotinTime=0;
             $disqualifiedcount;

            $user = Auth::user();
            $count = 1;
            foreach ($totalContestedCandidatedata as $key => $listdata) {
                $cons_no=$listdata->pc_no;
                    //get finalby DEO count 
                $finalbyDEO= $this->eciexpenditureModel->gettotalfinalbyDEO('PC',$listdata->st_code,$cons_no);
               
                
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

                //Get finalcompletedcount at CEO Count 
                $finalcompletedcount = $this->eciexpenditureModel->gettotalCompletedbyEci('PC', $listdata->st_code, $cons_no);
				
				//Get disqualifiedcount at CEO Count 
                $disqualifiedcount = $this->eciexpenditureModel->gettotalDisqualifiedbyEci('PC', $listdata->st_code, $cons_no);
				
				//Get notinTime at CEO Count 
			   $notinTime= $this->eciexpenditureModel->gettotalNotinTime('PC',$listdata->st_code,$cons_no);
		      

                $st = getstatebystatecode($listdata->st_code);
                $pcbystate=getpcbystate($listdata->st_code);
                $pccount=count($pcbystate);
                $pcdetails=getpcbypcno($listdata->st_code,$listdata->pc_no);
                $Totalpc += $pccount;  
                
                //pending at DEO
                if($finalbyDEO >= 0 ){
			     $pendingatRO=$listdata->totalcandidate-($finalbyDEO);
			    }  
				
				//pending at CEO	
		if($finalbyDEO >=  0 && $pendingatECI >=0 && $finalcompletedcount >=0){
		 $pendingatCEO = $finalbyDEO-($pendingatECI + $finalcompletedcount + $disqualifiedcount);
		// if($pendingatCEO >= 0) { $TotalPendingatCEO += $pendingatCEO; }
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
				$notinTime = !empty($notinTime) ? $notinTime : '0';


                $data = array(
				    $st->ST_NAME,
                    $pcdetails->PC_NAME,
                    $listdata->totalcandidate,
                    $filedcount,
                    $notfiledcount,
					$notinTime,
                    $finalbyDEO,
                    $pendingatRO,
                    $pendingatCEO,
					$noticeatCEOCount
                );
                $TotalUsers += $listdata->totalcandidate;
                $TotalPendingatRO += $pendingatRO;
                $TotalPendingatCEO += $pendingatCEO;
                $TotalPendingatECI += $pendingatECI;
                $TotalDEONotice += $noticeatDEOCount;
                $TotalCEONotice += $noticeatCEOCount;
                $Totalfinalcompletedcount += $finalcompletedcount;
                $TotalnotfiledData += $notfiledcount;
                $TotalfiledData += $filedcount;
                $TotalNotinTime += $notinTime;
				 $TotalFinalByDEO += $finalbyDEO;
                array_push($arr, $data);
                // }
                $count++;
            }
            $totalvalues = array('Total','', $TotalUsers, $TotalfiledData, $TotalnotfiledData,$TotalNotinTime,$TotalFinalByDEO, $TotalPendingatRO, $TotalPendingatCEO,$TotalCEONotice);
            // print_r($totalvalues);die;
            array_push($arr, $totalvalues);
            $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                'State','PC Name','Total Candidate','Started', 'Not Started','Not In Time','Finalise By DEO', 'Pending At DEO', 'Pending At CEO', 'Notice At CEO'
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
    public function getOfficersmisPDF(Request $request,$pc) {
        //ECI getOfficersmisPdf PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code =  $d->st_code;
			
                $cons_no = base64_decode($xss->clean_input($pc));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();

                if (empty($cons_no)) { 
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
                            ->groupBy("candidate_nomination_detail.pc_no")
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
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.pc_no")
                            ->get();
                }
                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.pc.ceo.Expenditure.mis-officerPDFhtml', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata,'cons_no' => $cons_no, 'st_code' => $st_code]);    
                return $pdf->download('CEOPCMISPdf_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.pc.ceo.Expenditure.mis-officerPDFhtml');
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
     * @author Devloped Date : 21-08-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return finalCandidateList By CEO fuction     
     */
    public function finalCandidateList(Request $request,$pc) {
        //dd($request->all());
        //PC CEO finalCandidateList TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($pc));
                $st_code = !empty($st_code) ? $st_code : '';
                $cons_no = !empty($cons_no) ? $cons_no : '';
                // echo $st_code.'pc'.$cons_no; die;
                DB::enableQueryLog();

                if (empty($cons_no)) {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
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
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                            ->get();
                } 
                //dd(DB::getQueryLog());
                // dd($totalContestedCandidate);
                return view('admin.pc.ceo.Expenditure.candidate-report', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code,'count' => count($totalContestedCandidatedata)]);
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
    public function finalCandidateListEXL(Request $request, $pc) {
        //ECI ACTIVE USERS EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($pc));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('CeoCandidateMISExcel_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        if (empty($cons_no)) {
                            $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
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
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.pc_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                                    //->groupBy("candidate_nomination_detail.st_code")
                                    ->orderBy("candidate_nomination_detail.pc_no")
                                    ->get();
                        }

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
    public function finalCandidateListPDF(Request $request, $pc) {
        //ECI finalCandidateList PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($pc));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                if (empty($cons_no)) {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
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
                } 
                $pdf = PDF::loadView('admin.pc.ceo.Expenditure.candidatePDFhtml', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata]);
                return $pdf->download('CeoCandidateMISPdf_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.pc.ceo.Expenditure.candidatePDFhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //CEO All Contested candidates PDF REPORT TRY CATCH BLOCK ENDS
    }
    //CEO candidate PDF REPORT FUNCTION ENDS

/**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return startedcandidate By CEO fuction     
     */
    public function getStartedcandidateMIS(Request $request, $pc) {
        //PC CEO Ecistartedcandidate TRY CATCH STARTS HERE
                try {
                    if (Auth::check()) {
                        $user = Auth::user();
                        $uid = $user->id;
                        $d = $this->commonModel->getunewserbyuserid($user->id);
                        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
        
                        $xss = new xssClean;
                        $st_code = $d->st_code;
                        $cons_no = base64_decode($xss->clean_input($pc));
                        $st_code = !empty($st_code) ? $st_code : 0;
                        $cons_no = !empty($cons_no) ? $cons_no : 0;
                        // echo  $st_code.'pc'.$cons_no; die;
                       
                        if (empty($cons_no)) {
                            $startedcandidate = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
                            $startedcandidate = DB::table('expenditure_reports')
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
        
                         //dd($startedcandidate);
                        return view('admin.pc.ceo.Expenditure.mis-startedcandidate', ['user_data' => $d, 'startedcandidate' => $startedcandidate, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($startedcandidate)]);
                    } else {
                        return redirect('/officer-login');
                    }
                } catch (Exception $ex) {
                    return Redirect('/internalerror')->with('error', 'Internal Server Error');
                }//PC CEO Ecistartedcandidate TRY CATCH ENDS HERE   
            }
        
        // end getstartedcandidateMIS start function
       
        /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return filedcandidateDataEXL By CEO fuction     
     */
//CEO getStartedcandidateEXL EXCEL REPORT STARTS
    public function getStartedcandidateMISEXL(Request $request, $pc) {
        //ECI filedcandidateDataEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code =$d->st_code;
                $cons_no = base64_decode($xss->clean_input($pc));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('CEOFiledCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                       if (empty($cons_no)) {
                            $startedcandidate = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                             } elseif ($st_code != '0' && $cons_no != '0') {
                            $startedcandidate = DB::table('expenditure_reports')
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
                        foreach ($startedcandidate as $candDetails) {
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            // dd($candDetails);
                            $pcDetails = getpcbypcno($candDetails->ST_CODE, $candDetails->constituency_no);
                            $date = new DateTime($candDetails->last_date_prescribed_acct_lodge);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
                            $lodgingDate=!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';

                            $TotalUsers = count($startedcandidate);
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
                })->export('csv');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //CEO startedcandidate EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //CEO startedcandidate EXCEL REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return startedcandidatePDF By CEO fuction     
     */
    //ECI filedcandidateDataPDF PDF REPORT STARTS

    public function getStartedcandidateMISPDF(Request $request,$pc) {
        //CEO filedcandidateDataPDF PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($pc));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
               if (empty($cons_no)) {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
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

                $pdf = PDF::loadView('admin.pc.ceo.Expenditure.mis-startedcandidatePdfhtml', ['user_data' => $d, 'filedData' => $filedData]);
                return $pdf->download('CeomiscandidatePdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.pc.ceo.Expenditure.mis-startedcandidatePdfhtml');
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
     * @author Devloped Date : 01-07-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return Ecinotstarted By ECI fuction     
     */
    public function getNotstartedMIS(Request $request, $pc) {
        //PC ECI notfiledcandidateData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($pc));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                $candidate_id = [];
                if(empty($cons_no)) {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $notstarted = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
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
                    $notstarted = DB::table('candidate_nomination_detail')
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
                return view('admin.pc.ceo.Expenditure.mis-notstartedcandidate', ['user_data' => $d, 'notstarted' => $notstarted, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($notstarted)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC Ecinotstarted list TRY CATCH ENDS HERE   
    }
     // end CEO notstarted function

  

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getNotstartedMISEXL By CEO fuction     
     */
//CEO getNotstartedMISEXL EXCEL REPORT STARTS
    public function getNotstartedMISEXL(Request $request, $pc) {
        //CEO filedcandidateDataEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($pc));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('CEOnotfiledCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {
                        $candidate_id = [];
                        if(empty($cons_no)) {
                            $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                            foreach ($startCandList as $startCandListData) {
                                $candidate_id[] = $startCandListData->candidate_id;
                            }
                            $notstarted = DB::table('candidate_nomination_detail')
                                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
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
                            $notstarted = DB::table('candidate_nomination_detail')
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

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($notstarted as $candDetails) {
                            $st = getstatebystatecode($candDetails->st_code);
                            // dd($candDetails);
                            $pcDetails = getpcbypcno($candDetails->st_code, $candDetails->pc_no);
                            $date = new DateTime($candDetails->created_at);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012

                            $TotalUsers = count($notstarted);
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
                })->export('csv');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //CEO notfiledcandidateData EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //CEO notfiledcandidateData EXCEL REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 23-08-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getNotstartedMISPDF By ECI fuction     
     */
    //CEO getNotstartedMISPDF PDF REPORT STARTS

    public function getNotstartedMISPDF(Request $request,$pc) {
        //ECI notfiledcandidateDataPDF PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($pc));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                $candidate_id = [];
                        if(empty($cons_no)) {
                            $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                            foreach ($startCandList as $startCandListData) {
                                $candidate_id[] = $startCandListData->candidate_id;
                            }
                            $notstarted = DB::table('candidate_nomination_detail')
                                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
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
                            $notstarted = DB::table('candidate_nomination_detail')
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
                $pdf = PDF::loadView('admin.pc.ceo.Expenditure.mis-notstartedPdfhtml', ['user_data' => $d, 'notstarted' => $notstarted]);
                return $pdf->download('CeomisnotstartedPdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.pc.ceo.Expenditure.mis-notstartedPdfhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        } //CEO notfiledcandidateDataPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//CEO notstartedDataPDF PDF REPORT FUNCTION ENDS

/**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getfinalbyDEO By CEO fuction     
     */
    public function getfinalbyDEO(Request $request, $pc) {
        //PC ECI EcifinalbyDEO TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($pc));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                if (empty($cons_no)) {
                    $finalbyDEO = DB::table('expenditure_reports')
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
                    $finalbyDEO = DB::table('expenditure_reports')
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
                return view('admin.pc.ceo.Expenditure.finalbydeo-mis', ['user_data' => $d, 'finalbyDEO' => $finalbyDEO, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($finalbyDEO)]);
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
     * @author Devloped Date : 21-08-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getfinalbyDEOMISEXL By CEO fuction     
     */
//CEO EcifinalbyDEOMISEXL EXCEL REPORT STARTS
    public function getfinalbyDEOMISEXL(Request $request, $pc) {
//CEO getfinalbyDEOMISEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($pc));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                //echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('CEOPendingatDEOCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        if (empty($cons_no)) {
                            $finalbyDEOMISEXL = DB::table('expenditure_reports')
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
                            $finalbyDEOMISEXL = DB::table('expenditure_reports')
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
                        foreach ($finalbyDEOMISEXL as $candDetails) {
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            //dd($candDetails);
                            $pcDetails = getpcbypcno($candDetails->ST_CODE, $candDetails->constituency_no);
                            $date = new DateTime($candDetails->last_date_prescribed_acct_lodge);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
                            $lodgingDate =!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
                            $data = array(
                                $pcDetails->PC_NO . '-' . $pcDetails->PC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate
                            );
                            $TotalUsers = count($finalbyDEOMISEXL);
                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        $totalvalues = array('Total', $TotalUsers);
                        // print_r($totalvalues);die;
                        array_push($arr, $totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'PC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging'
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
        //CEO getfinalbyDEOMIS EXCEL REPORT TRY CATCH BLOCK ENDS
    }//CEO EcifinalbyDEOMIS EXCEL REPORT FUNCTION ENDS

    //CEO EcifinalbyDEOMISPDF PDF REPORT STARTS
    public function getfinalbyDEOMISPDF(Request $request, $pc) {
//CEO getfinalbyDEOMISPDF PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($pc));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                if (empty($cons_no)) {
                    $finalbyDEO = DB::table('expenditure_reports')
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
                    $finalbyDEO = DB::table('expenditure_reports')
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
                $pdf = PDF::loadView('admin.pc.ceo.Expenditure.finalbyDEOPDFhtml', ['user_data' => $d, 'finalbyDEO' => $finalbyDEO]);
                return $pdf->download('CEOfinalbyDEOCandidateMIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.pc.ceo.Expenditure.finalbyDEOPDFhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
//CEO getfinalbyDEOMISPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//CEO getfinalbyDEOMISPDF PDF REPORT FUNCTION ENDS

/**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getcandidateListpendingatRO By CEO fuction     
     */
    public function getcandidateListpendingatRO(Request $request, $pc) {
        //PC CEO candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($pc));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                $candidate_id=array();
                $getcandidateListfinalbyECI=[];
                $pendingatceo=[];
    if (empty($cons_no)) {
        $getcandidateListfinalbyECI = DB::table('expenditure_reports')
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
        ->where('expenditure_reports.ST_CODE', '=', $st_code)
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
        ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
        ->where('expenditure_reports.ST_CODE', '=', $st_code)
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('expenditure_reports.final_by_ceo', '1')
        ->whereNotNull('expenditure_reports.date_of_receipt')
        ->whereNull('expenditure_reports.date_of_receipt_eci')
        ->get();
        foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
            $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
        }
        foreach ($pendingatceo as $pendingatceoListData) {
            $candidate_id[] = $pendingatceoListData->candidate_id;
        }
       
        $partiallyCandList = DB::table('candidate_nomination_detail')
                ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                ->where('candidate_nomination_detail.st_code', '=', $st_code)
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                ->select('expenditure_reports.created_at','expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                ->get();
       
         } elseif ($st_code != '0' && $cons_no != '0') {
            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
            ->select('expenditure_reports.candidate_id')
            ->where('candidate_nomination_detail.application_status', '=', '6')
            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('expenditure_reports.date_of_receipt_eci', '!=', 'null : 0000-00-00')
            ->where('final_by_eci','1')
            ->where('expenditure_reports.ST_CODE', '=', $st_code)
            ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
        ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
        ->where('expenditure_reports.ST_CODE', '=', $st_code)
        ->where('expenditure_reports.constituency_no', '=', $cons_no)
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('expenditure_reports.final_by_ceo', '1')
        ->whereNotNull('expenditure_reports.date_of_receipt')
        ->whereNull('expenditure_reports.date_of_receipt_eci')
        ->get();
        foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
            $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
        }
        foreach ($pendingatceo as $pendingatceoListData) {
            $candidate_id[] = $pendingatceoListData->candidate_id;
        }
        $partiallyCandList = DB::table('candidate_nomination_detail')
                ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
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
                ->select('expenditure_reports.created_at','expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                ->get();
             }
  
                // dd(DB::getQueryLog());
                return view('admin.pc.ceo.Expenditure.pendingatdeo-mis', ['user_data' => $d, 'partiallyCandList' => $partiallyCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($partiallyCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ceo getcandidateListpendingatRO TRY CATCH ENDS HERE   
    }

// end getcandidateListpendingatRO function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getcandidateListpendingatROEXL By ECI fuction     
     */
//CEO getcandidateListpendingatROEXL EXCEL REPORT STARTS
    public function getcandidateListpendingatROEXL(Request $request, $pc) {
//CEO getcandidateListpendingatROEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($pc));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                //echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('CeoPendingatDEOCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {
                    $candidate_id=array();

                    if (empty($cons_no)) {
                        $getcandidateListfinalbyECI = DB::table('expenditure_reports')
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
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
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
                        ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->where('expenditure_reports.final_by_ceo', '1')
                        ->whereNotNull('expenditure_reports.date_of_receipt')
                        ->whereNull('expenditure_reports.date_of_receipt_eci')
                        ->get();
                        foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                            $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                        }
                        foreach ($pendingatceo as $pendingatceoListData) {
                            $candidate_id[] = $pendingatceoListData->candidate_id;
                        }
                       
                        $partiallyCandList = DB::table('candidate_nomination_detail')
                                ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                ->where('candidate_nomination_detail.application_status', '=', '6')
                                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                                ->select('expenditure_reports.created_at','expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                ->get();
                       
                         } elseif ($st_code != '0' && $cons_no != '0') {
                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.date_of_receipt_eci', '!=', 'null : 0000-00-00')
                            ->where('final_by_eci','1')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
                        ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->where('expenditure_reports.final_by_ceo', '1')
                        ->whereNotNull('expenditure_reports.date_of_receipt')
                        ->whereNull('expenditure_reports.date_of_receipt_eci')
                        ->get();
                        foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                            $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                        }
                        foreach ($pendingatceo as $pendingatceoListData) {
                            $candidate_id[] = $pendingatceoListData->candidate_id;
                        }
                        $partiallyCandList = DB::table('candidate_nomination_detail')
                                ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
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
                                ->select('expenditure_reports.created_at','expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                ->get();
                             }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($partiallyCandList as $candDetails) { 
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            $pcDetails = getpcbypcno($candDetails->ST_CODE, $candDetails->constituency_no);
                            $date = new DateTime($candDetails->last_date_prescribed_acct_lodge);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
							$pcno=!empty($pcDetails->PC_NO) ?  $pcDetails->PC_NO : '';
                            $pcname=!empty($pcDetails->PC_NAME) ?  $pcDetails->PC_NAME : '';
                            $lodgingDate=!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
                            $data = array(
                                $st->ST_NAME,
                                $pcno . '-' . $pcname,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate
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
                            'State','PC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging Scrutiny Form'
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
public function getcandidateListpendingatROPDF(Request $request,$pc) {
//ECI getcandidateListpendingatROPDF PDF REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$xss = new xssClean;
$st_code = $d->st_code;
$cons_no = base64_decode($xss->clean_input($pc));
$st_code = !empty($st_code) ? $st_code : 0;
$cons_no = !empty($cons_no) ? $cons_no : 0;
$cur_time = Carbon::now();
 $candidate_id=array();
 if (empty($cons_no)) {
    $getcandidateListfinalbyECI = DB::table('expenditure_reports')
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
    ->where('expenditure_reports.ST_CODE', '=', $st_code)
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
    ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
    ->where('expenditure_reports.ST_CODE', '=', $st_code)
    ->where('candidate_nomination_detail.application_status', '=', '6')
    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
    ->where('expenditure_reports.final_by_ceo', '1')
    ->whereNotNull('expenditure_reports.date_of_receipt')
    ->whereNull('expenditure_reports.date_of_receipt_eci')
    ->get();
    foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
        $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
    }
    foreach ($pendingatceo as $pendingatceoListData) {
        $candidate_id[] = $pendingatceoListData->candidate_id;
    }
   
    $pendingatDEOCandList = DB::table('candidate_nomination_detail')
            ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
            ->where('candidate_nomination_detail.st_code', '=', $st_code)
            ->where('candidate_nomination_detail.application_status', '=', '6')
            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
            ->select('expenditure_reports.created_at','expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
            ->get();
   
     } elseif ($st_code != '0' && $cons_no != '0') {
        $getcandidateListfinalbyECI = DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->select('expenditure_reports.candidate_id')
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('expenditure_reports.date_of_receipt_eci', '!=', 'null : 0000-00-00')
        ->where('final_by_eci','1')
        ->where('expenditure_reports.ST_CODE', '=', $st_code)
        ->where('expenditure_reports.constituency_no', '=', $cons_no)
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
    ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
    ->where('expenditure_reports.ST_CODE', '=', $st_code)
    ->where('expenditure_reports.constituency_no', '=', $cons_no)
    ->where('candidate_nomination_detail.application_status', '=', '6')
    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
    ->where('expenditure_reports.final_by_ceo', '1')
    ->whereNotNull('expenditure_reports.date_of_receipt')
    ->whereNull('expenditure_reports.date_of_receipt_eci')
    ->get();
    foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
        $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
    }
    foreach ($pendingatceo as $pendingatceoListData) {
        $candidate_id[] = $pendingatceoListData->candidate_id;
    }
    $pendingatDEOCandList = DB::table('candidate_nomination_detail')
            ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
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
            ->select('expenditure_reports.created_at','expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
            ->get();
         }
$pdf = PDF::loadView('admin.pc.ceo.Expenditure.candidatePendingatDEOPDFhtml', ['user_data' => $d, 'pendingatDEOCandList' => $pendingatDEOCandList]);
return $pdf->download('CeopendingatDEOCandidateMIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
return view('admin.pc.ceo.Expenditure.candidatePendingatDEOPDFhtml');
} else {
return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//CEO getcandidateListpendingatROPDF PDF REPORT TRY CATCH BLOCK ENDS
}

//CEO getcandidateListpendingatROPDF PDF REPORT FUNCTION ENDS

/**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 23-08-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getcandidateListpendingatCEO By CEO fuction     
     */
    public function getcandidateListpendingatCEO(Request $request, $pc) {
        //PC CEO getcandidateListpendingatCEO TRY CATCH STARTS HERE
try {
if (Auth::check()) {
    $user = Auth::user();
    $uid = $user->id;
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
    $xss = new xssClean;
    $st_code = $d->st_code;
    $cons_no = base64_decode($xss->clean_input($pc));
    $st_code = !empty($st_code) ? $st_code : 0;
    $cons_no = !empty($cons_no) ? $cons_no : 0;
    // echo $st_code.'cons_no'.$cons_no; die;
    $candidate_id=[];
if(empty($cons_no)) {
/*$pendingatceoCandList = DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
        ->where('expenditure_reports.ST_CODE', '=', $st_code)
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('expenditure_reports.final_by_ceo', '1')
        ->whereNotNull('expenditure_reports.date_of_receipt')
        ->whereNull('expenditure_reports.date_of_receipt_eci')
        ->groupBy('expenditure_reports.candidate_id')
        ->get();*/
		
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
                            $pendingatceoCandList = DB::table('expenditure_reports')
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
                            $pendingatceoCandList = DB::table('expenditure_reports')
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
    //dd($pendingatceoCandList);
    return view('admin.pc.ceo.Expenditure.pendingatceo-mis', ['user_data' => $d, 'pendingatceoCandList' => $pendingatceoCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($pendingatceoCandList)]);
} else {
    return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC CEO candidateListByfinalizeData TRY CATCH ENDS HERE
}

// end candidateListByfinalizeData start function
        
/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 28-05-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return getcandidateListpendingatROEXL By ECI fuction     
 */
//CEO getcandidateListpendingatCEOEXL EXCEL REPORT STARTS
public function getcandidateListpendingatCEOEXL(Request $request, $pc) {
//CEO getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK STARTS
                try {
                    if (Auth::check()) {
                        $user = Auth::user();
                        $uid = $user->id;
                        $d = $this->commonModel->getunewserbyuserid($user->id);
                        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                        $xss = new xssClean;
                        $st_code = $d->st_code;
                        $cons_no = base64_decode($xss->clean_input($pc));
                        $st_code = !empty($st_code) ? $st_code : 0;
                        $cons_no = !empty($cons_no) ? $cons_no : 0;
                        // echo  $st_code.'pc'.$cons_no; die;
						
                        $cur_time = Carbon::now();
        
                        \Excel::create('CeoPendingatCEOCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                            $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {
        $candidate_id=[];
        if(empty($cons_no)) {
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
                            $pendingatceoCandList = DB::table('expenditure_reports')
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
                            $pendingatceoCandList = DB::table('expenditure_reports')
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
        foreach ($pendingatceoCandList as $candDetails) {
			//dd($candDetails);
            $st = getstatebystatecode($candDetails->ST_CODE);
            $pcDetails = getpcbypcno($candDetails->ST_CODE, $candDetails->constituency_no);
            $date = new DateTime($candDetails->created_at);
            //echo $date->format('d.m.Y'); // 31.07.2012
            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
            $data = array(
                $pcDetails->PC_NO . '-' . $pcDetails->PC_NAME,
                $candDetails->cand_name,
                $candDetails->PARTYNAME,
                $lodgingDate
            );
            $TotalUsers = count($pendingatceoCandList);
            array_push($arr, $data);
            // }
            $count++;
        }
        $totalvalues = array('Total', $TotalUsers);
        // print_r($totalvalues);die;
        array_push($arr, $totalvalues);
        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
            'PC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging'
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
//CEO getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK ENDS
}
        
//CEO getcandidateListpendingatROPDF EXCEL REPORT FUNCTION ENDS
//CEO getcandidateListpendingatCEOPDF PDF REPORT STARTS
public function getcandidateListpendingatCEOPDF(Request $request, $pc) {
//CEO getcandidateListpendingatCEOPDF PDF REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
$xss = new xssClean;
$st_code = $d->st_code;
$cons_no = base64_decode($xss->clean_input($pc));
$st_code = !empty($st_code) ? $st_code : 0;
$cons_no = !empty($cons_no) ? $cons_no : 0;
$cur_time = Carbon::now();
$candidate_id=[];
if(empty($cons_no)) {
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
                            $pendingatCEOCandList = DB::table('expenditure_reports')
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
                            $pendingatCEOCandList = DB::table('expenditure_reports')
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
//dd($totalContestedCandidatedata);
$pdf = PDF::loadView('admin.pc.ceo.Expenditure.candidatePendingatCEOPDFhtml', ['user_data' => $d, 'pendingatCEOCandList' => $pendingatCEOCandList]);
return $pdf->download('CeopendingatCEOCandidateMIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
return view('admin.pc.ceo.Expenditure.candidatePendingatCEOPDFhtml');
} else {
return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//CEO getcandidateListpendingatCEOPDF PDF REPORT TRY CATCH BLOCK ENDS
}//CEO getcandidateListpendingatCEOPDF PDF REPORT FUNCTION ENDS


/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 23-08-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return notintimecandidateData By CEO fuction     
 */
public function getnotintimecandidateData(Request $request, $pc) {

//PC CEO notintimecandidateData TRY CATCH STARTS HERE

try {
if (Auth::check()) {
    $user = Auth::user();
    $uid = $user->id;
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
    $xss = new xssClean;
    $st_code = $d->st_code;
    $cons_no = base64_decode($xss->clean_input($pc));
    $st_code = !empty($st_code) ? $st_code : 0;
    $cons_no = !empty($cons_no) ? $cons_no : 0;
    // echo $st_code.'cons_no'.$cons_no; die;
    DB::enableQueryLog();
    $notinTime = [];
    if (empty($cons_no)) {
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
    } elseif ($st_code != '0' && $cons_no != '0') {
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
//dd(DB::getQueryLog());
    //dd($notinTime);
    return view('admin.pc.ceo.Expenditure.mis-notintimecandidate', ['user_data' => $d, 'notinTime' => $notinTime, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($notinTime)]);
} else {
    return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC CEO notintimecandidateData TRY CATCH ENDS HERE   
}

// end notintimecandidateData start function

/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 23-08-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return notintimecandidateDataEXL By CEO fuction     
 */
//CEO notintimeCandidatesmisEXL EXCEL REPORT STARTS
public function getnotintimecandidateDataEXL(Request $request, $pc) {
//CEO filedcandidateDataEXL EXCEL REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
    $user = Auth::user();
    $uid = $user->id;
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
    $xss = new xssClean;
    $st_code = $d->st_code;
    $cons_no = base64_decode($xss->clean_input($pc));
    $st_code = !empty($st_code) ? $st_code : 0;
    $cons_no = !empty($cons_no) ? $cons_no : 0;
    // echo  $st_code.'pc'.$cons_no; die;
    $cur_time = Carbon::now();
    \Excel::create('CeoNotinTimeCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
        $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

if(empty($cons_no)) {
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
   }elseif ($st_code != '0' && $cons_no != '0') {
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
})->export('csv');
} else {
    return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//ECI filedcandidateData EXCEL REPORT TRY CATCH BLOCK ENDS
}

//CEO filedcandidateData EXCEL REPORT FUNCTION ENDS

/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 23-08-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return filedcandidateDataPDF By ECI fuction     
 */
//CEO notintimecandidateDataPDF PDF REPORT STARTS

public function getnotintimecandidateDataPDF(Request $request, $pc) {
//CEO filedcandidateDataPDF PDF REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
    $user = Auth::user();
    $uid = $user->id;
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
    $xss = new xssClean;
    $st_code = $d->st_code;
    $cons_no = base64_decode($xss->clean_input($pc));
    $st_code = !empty($st_code) ? $st_code : 0;
    $cons_no = !empty($cons_no) ? $cons_no : 0;
    $cur_time = Carbon::now();

    if (empty($cons_no)) {
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
    } elseif ($st_code != '0' && $cons_no != '0') {
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

    $pdf = PDF::loadView('admin.pc.ceo.Expenditure.mis-notintimecandidatePdfhtml', ['user_data' => $d, 'notinTime' => $notinTime]);
    return $pdf->download('CeomisnotintimecandidatePdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
    return view('admin.pc.ceo.Expenditure.mis-notintimecandidatePdfhtml');
} else {
    return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
} //CEO notintimecandidateDataPDF PDF REPORT TRY CATCH BLOCK ENDS
}

//CEO notintimecandidateDataPDF PDF REPORT FUNCTION ENDS


############################End CEO MIS by Niraj ##############################################


}

// end class