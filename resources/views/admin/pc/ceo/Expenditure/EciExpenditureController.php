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
    public function __construct() {
        $this->middleware(['auth:admin', 'auth']);
        //$this->middleware('eci');
        ini_set("pcre.backtrack_limit", "500000000");
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

 public function expdashboard(Request $request){ 
        
        $users=Session::get('admin_login_details');
        $user = Auth::user();   
        if(session()->has('admin_login')){  
            $uid=$user->id;
            $d=$this->commonModel->getunewserbyuserid($uid);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
           /* $list_record=$this->ECIModel->getallelectionphasewise();
            $list_state=$this->ECIModel->listcurrentelectionstate();
            $list_phase=$this->ECIModel->listcurrentelectionphase();
            $list_electionid=$this->ECIModel->getallelectionbyid();
            $list=$this->ECIModel->listelectiontype();
           
            $module=$this->commonModel->getallmodule();*/


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

             return view('admin.pc.eci.Expenditure.expdashboard', ['user_data' => $d,'edetails' => $ele_details]);
             
          }
          else {
              return redirect('/admin-login');
          }    
  
        }   // end expdashboard function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return dashboard By ECI fuction     
     */
    public function dashboard(Request $request) {
        //dd($request->all());
        //PC ECI dashboard TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $st_code = $request->input('state');
                $cons_no = $request->input('pc');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                if (!empty($st_code && $cons_no == '')) {
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
                $returncount = $this->expenditureModel->gettotalreturn('PC', $st_code, $cons_no,'Returned');
                $nonreturncount = $this->expenditureModel->gettotalreturn('PC', $st_code, $cons_no,'Non-Returned');
                
                $returncount=!empty($returncount)?count($returncount):0;
                $nonreturncount=!empty($nonreturncount)?count($nonreturncount):0;
                //Getfinal by eci Count %
                $Percent_returncount = $this->get_percentage($totalContestedCandidate, $returncount);
                $Percent_nonreturncount = $this->get_percentage($totalContestedCandidate, $nonreturncount);
                // end here
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
'cons_no' => $cons_no, 'st_code' => $st_code]);
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
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.finalized_status', '=', '1')
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
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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

                //dd($DataentryStartCandList);
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
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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

                if ($st_code == '0' && $cons_no == '0') {
                    $formateDefects = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
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
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.rp_act', '=', 'No')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           // ->groupBy('expenditure_reports.candidate_id')
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
                            // ->where('expenditure_understated.constituency_no','=',$cons_no) 
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
                            //->where('expenditure_reports.ST_CODE','=',$st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
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
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
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
                           // ->select(DB::raw('IFNULL((political_fund_cash + political_fund_checque + political_fund_kind),0) AS partyfund'))
                            ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'expenditure_fund_parties.candidate_id')
                            ->select('expenditure_reports.final_by_ro','candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_parties.*', 'm_party.PARTYNAME')
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
                            ->select('expenditure_reports.final_by_ro','candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_parties.*', 'm_party.PARTYNAME')
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
                            ->select('expenditure_reports.final_by_ro','candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_parties.*', 'm_party.PARTYNAME')
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

                if ($st_code == '0' && $cons_no == '0') {
                    $otherfund = DB::table('expenditure_fund_source')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                            ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                            ->select('expenditure_reports.final_by_ro','candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_source.*', 'm_party.PARTYNAME')
                            //->where('expenditure_fund_source.ST_CODE','=',$st_code)
                            // ->where('expenditure_fund_source.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_fund_source.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $otherfund = DB::table('expenditure_fund_source')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                            //->select(DB::raw('IFNULL((other_source_amount),0) AS otherSourcesfund'))
                            ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                            ->select('expenditure_reports.final_by_ro','candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_source.*', 'm_party.PARTYNAME')
                            ->where('expenditure_fund_source.ST_CODE', '=', $st_code)
                            // ->where('expenditure_fund_source.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_fund_source.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $otherfund = DB::table('expenditure_fund_source')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                            //->select(DB::raw('IFNULL((other_source_amount),0) AS otherSourcesfund'))
                            ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                            ->select('expenditure_reports.final_by_ro','candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_source.*', 'm_party.PARTYNAME')
                            ->where('expenditure_fund_source.ST_CODE', '=', $st_code)
                            ->where('expenditure_fund_source.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_fund_source.candidate_id')
                            ->get();
                }
                //dd($DataentryStartCandList);
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
		$st_code = $request->input('state');
		$cons_no = $request->input('pc');
		$st_code = !empty($st_code) ? $st_code : 0;
		$cons_no = !empty($cons_no) ? $cons_no : 0;
		// echo  $st_code.'pc'.$cons_no; die;
		if (!empty($st_code && $cons_no == '')) {
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
		$partiallypendingcount = $this->eciexpenditureModel->gettotalpartiallypending('PC', $st_code, $cons_no);
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
		$finalbyceocount = $this->eciexpenditureModel->gettotalfinalbyceo('PC', $st_code, $cons_no);
		//Get Data entry finalize Count %
		$Percent_finalbyceocount = $this->get_percentage($totalContestedCandidate, $finalbyceocount);

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



  return view('admin.pc.eci.Expenditure.statusdashboard', ['user_data' => $d, 'startdatacount' => $startdatacount, 'Percent_startdatacount' => $Percent_startdatacount, 'pendingdatacount' => $pendingdatacount, 'Percent_finaldatacount' => $Percent_finaldatacount, 'finaldatacount' => $finaldatacount, 'Percent_pendingdatacount' => $Percent_pendingdatacount, 'partiallypendingcount' => $partiallypendingcount, 'Percent_partiallypendingcount' => $Percent_partiallypendingcount, 'defaultercount' => $defaultercount, 'Percent_defaultercount' => $Percent_defaultercount, 'finalbyceocount' => $finalbyceocount, 'Percent_finalbyceocount' => $Percent_finalbyceocount, 'finalbyecicount' => $finalbyecicount, 'Percent_finalbyecicount' => $Percent_finalbyecicount, 'noticeatceocount' => $noticeatceocount, 'Percent_noticeatceocount' => $Percent_noticeatceocount, 'noticeatdeocount' => $noticeatdeocount, 'Percent_noticeatdeocount' => $Percent_noticeatdeocount, 'cons_no' => $cons_no, 'st_code' => $st_code]);

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
                    $partiallyCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->where(function($query) {
                                $query->whereNull('expenditure_reports.date_of_receipt');
                                $query->orwhere('expenditure_reports.date_of_receipt', '=', '');
                            })
                          //  ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $partiallyCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                            //->where('expenditure_notification.deo_action','0')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->where(function($query) {
                                $query->whereNull('expenditure_reports.date_of_receipt');
                                $query->orwhere('expenditure_reports.date_of_receipt', '=', '');
                            })
                          //  ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $partiallyCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.created_at','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                      