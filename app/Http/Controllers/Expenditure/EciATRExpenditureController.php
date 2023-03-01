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
use App\Exports\TeamExport;
//INCLUDING CLASSES
use App\Classes\xssClean;
//INCLUDING CLASSES
use DateTime;
use App\models\Expenditure\DeoexpenditureModel;

class EciATRExpenditureController extends Controller {

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
      $m_election_history = DB::connection("mysql_database_historys")->table("m_election_history")->where("db_name", $DB_DATABASE)->first();
		
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
    }//end number


    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return dashboard By ECI fuction     
     */
    public function atrdashboard(Request $request) {
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

                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
               
                $cons_no = $request->input('pc');
               
               // $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                 DB::enableQueryLog();
                 //echo  $st_code.'pc'.$cons_no; die;
                 $sql= DB::table('expenditure_reports')->where('expenditure_reports.final_action', '=', 'Notice Issued');
                 $sql1= DB::table('expenditure_reports')->where('expenditure_reports.final_action', '=', 'Notice Issued');
                 $sql2= DB::table('expenditure_reports')->where('expenditure_reports.final_action', '=', 'Notice Issued');

                if (!empty($st_code) &&  empty($cons_no)) {
                    $sql = $sql->where('expenditure_reports.ST_CODE', '=', $st_code);
                    $sql1 = $sql1->where('expenditure_reports.ST_CODE', '=', $st_code);
                    $sql2 = $sql2->where('expenditure_reports.ST_CODE', '=', $st_code);
                    $sql1= $sql1->where('expenditure_reports.final_by_eci',  '=', '1');
                    $sql2= $sql2->where('expenditure_reports.final_by_eci',  '=', '0');     
                 
                    } else if (!empty($st_code) && $cons_no != '') {
                    $sql =$sql->where('expenditure_reports.ST_CODE', '=', $st_code);
                    $sql = $sql->where('expenditure_reports.constituency_no', '=', $cons_no);
                    $sql1 =$sql1->where('expenditure_reports.ST_CODE', '=', $st_code);
                    $sql1 = $sql1->where('expenditure_reports.constituency_no', '=', $cons_no);
                    $sql2 =$sql2->where('expenditure_reports.ST_CODE', '=', $st_code);
                    $sql2 = $sql2->where('expenditure_reports.constituency_no', '=', $cons_no);
                    $sql1= $sql1->where('expenditure_reports.final_by_eci',  '=', '1');
                    $sql2= $sql2->where('expenditure_reports.final_by_eci',  '=', '0');   
                  } else{
                    $sql1= $sql1->where('expenditure_reports.final_by_eci',  '=', '1');
                    $sql2= $sql2->where('expenditure_reports.final_by_eci',  '=', '0');
                  }
               $totalatr=$sql->count();
               $closedatr=$sql1->count();
               $liveatr=$sql2->count();
            // dd(DB::getQueryLog());
         // echo $totalatr.'closedatr'. $closedatr.'liveatr'. $liveatr; die('test');
            

/////////////////-----------end notification -----------------//////////////
return view('admin.pc.eci.Expenditure.atr-dashboard', ['user_data' => $d, 'totalatr' => $totalatr, 'closedatr' => $closedatr,
'liveatr' => $liveatr, 'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI dashboard TRY CATCH ENDS HERE    
    }

// end dashboard function



  
#################################Start MIS Report By Niraj 12-07-2020#####################################

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 12-07-2020
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getOfficersmis for ATR By ECI fuction     
     */  
    public function getatrmis(Request $request) {  
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

         #########################Code For State Wise Access By Niraj date 12-07-2020#####################
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
                 //echo  $st_code.'cons_no=>'.$cons_no; die;
                 DB::enableQueryLog();
                if (!empty($st_code) && $cons_no == '' &&  $st_code !='All') { 

                  $totalContestedCandidatedata = $this->eciexpenditureModel->gettotalcontestedcanndidate($st_code,$cons_no); 
                } else if (!empty($st_code) && $cons_no != '' &&  $st_code !='All') { 

                  $totalContestedCandidatedata = $this->eciexpenditureModel->gettotalcontestedcanndidate($st_code,$cons_no);

                }else if (!empty($st_code) && $cons_no == '' &&  $st_code =='All') {  

                  $totalContestedCandidatedata = $this->eciexpenditureModel->gettotalcontestedcanndidate($st_code,$cons_no);

                } else if ( $st_code == '' && $cons_no == '' ) {

                 $totalContestedCandidatedata = $this->eciexpenditureModel->gettotalcontestedcanndidate($st_code,$cons_no);

                }
                //dd(DB::getQueryLog());
               //  dd($totalContestedCandidatedata);
                        return view('admin.pc.eci.Expenditure.mis-atr', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist,  'count' => count($totalContestedCandidatedata)]);
                  
                   } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getOfficersmis TRY CATCH ENDS HERE    
    } // end getOfficersmis function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 28-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getOfficersmis By ECI fuction     
     */
    //ECI getOfficersmis EXCEL REPORT STARTS
    public function getatrmisEXL(Request $request, $state, $pc) {
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
                // echo  $st_code.'pc'.$cons_no; die;
                // dd($totalContestedCandidate);

                $cur_time = Carbon::now();

                \Excel::create('EciMISReportExcel_' . '_' . $cur_time, function($excel) use($st_code, $cons_no,$permitstates) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no,$permitstates) {

                        if (!empty($st_code) && $cons_no == '' &&  $st_code !='All') { 

                  $totalContestedCandidatedata = $this->eciexpenditureModel->gettotalcontestedcanndidate($st_code='',$cons_no=''); 
                } else if (!empty($st_code) && $cons_no != '' &&  $st_code !='All') {
                    $totalContestedCandidatedata = $this->eciexpenditureModel->gettotalcontestedcanndidate($st_code='',$cons_no='');
                }else if (!empty($st_code) && $cons_no == '' &&  $st_code =='All') {  
                  $totalContestedCandidatedata = $this->eciexpenditureModel->gettotalcontestedcanndidate($st_code='',$cons_no='');
                } else if ( $st_code == '' && $cons_no == '' ) {
                 $totalContestedCandidatedata = $this->eciexpenditureModel->gettotalcontestedcanndidate($st_code='',$cons_no='');
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
    public function getatrmisPDF(Request $request, $state, $pc) {
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

                  $totalContestedCandidatedata = $this->eciexpenditureModel->gettotalcontestedcanndidate($st_code,$cons_no); 
                } else if (!empty($st_code) && $cons_no != '' &&  $st_code !='All') { 

                  $totalContestedCandidatedata = $this->eciexpenditureModel->gettotalcontestedcanndidate($st_code,$cons_no);

                }else if (!empty($st_code) && $cons_no == '' &&  $st_code =='All') {  

                  $totalContestedCandidatedata = $this->eciexpenditureModel->gettotalcontestedcanndidate($st_code,$cons_no);

                } else if ( $st_code == '' && $cons_no == '' ) {

                 $totalContestedCandidatedata = $this->eciexpenditureModel->gettotalcontestedcanndidate($st_code,$cons_no);

                }

                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.pc.eci.Expenditure.mis-atrPDFhtml', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata,'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist]);    
                return $pdf->download('EciOfficerATRMISPdf_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.pc.eci.Expenditure.mis-atrPDFhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI mis-officerPDFhtml PDF REPORT TRY CATCH BLOCK ENDS
    }


     /**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 23-06-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return getnoticeatDEO By ECI fuction     
 */
public function gettotalatr(Request $request,$state,$pc){
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
         DB::enableQueryLog();
        $sql= DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
         ->leftjoin('expenditure_action_logs', 'expenditure_action_logs.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->select('candidate_nomination_detail.party_id','candidate_nomination_detail.candidate_id','candidate_personal_detail.cand_name','expenditure_action_logs.ST_CODE', 'expenditure_action_logs.constituency_no as pc_no','expenditure_reports.updated_at as finalized_date','expenditure_reports.final_by_eci','expenditure_reports.final_action','expenditure_reports.created_at','m_party.CCODE','m_party.PARTYNAME');
     
    
      if($st_code =='0' && $cons_no=='0'){
        $sql = $sql ->groupBy('expenditure_action_logs.candidate_id');
      }elseif($st_code !='0' && $cons_no=='0'){
        $sql = $sql->where('expenditure_action_logs.ST_CODE','=',$st_code);
        $sql = $sql->groupBy('expenditure_action_logs.candidate_id');

      }elseif($st_code !='0' && $cons_no !='0'){
        $sql = $sql->where('expenditure_action_logs.ST_CODE','=',$st_code);
        $sql = $sql->where('expenditure_action_logs.constituency_no','=',$cons_no) ;
        $sql = $sql->groupBy('expenditure_action_logs.candidate_id');
      }
      $totalATR=$sql->get();
    // dd(DB::getQueryLog());
        return view('admin.pc.eci.Expenditure.totalatr',['user_data' => $d,'totalATR' => $totalATR,'edetails'=>$ele_details,'st_code'=>$st_code,'cons_no'=>$cons_no,'count'=>count($totalATR)]); 
        
    }
    else {
        return redirect('/officer-login');
    }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//PC ECI totalatr TRY CATCH ENDS HERE   
    }   // end totalatr start function

/**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-09-2020
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getReplybyDEO By ECI fuction     
     */
    public function getReplybyDEO(Request $request, $state, $pc) {
        //PC ECI getReplybyDEO TRY CATCH STARTS HERE
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
                $sql=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('expenditure_action_logs', 'expenditure_action_logs.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.party_id','candidate_personal_detail.cand_name', 'expenditure_reports.ST_CODE','expenditure_reports.constituency_no as pc_no','expenditure_reports.candidate_id','expenditure_reports.created_at','expenditure_reports.report_submitted_date as finalized_date', 'expenditure_reports.last_date_prescribed_acct_lodge',
                              'expenditure_reports.date_orginal_acct',
                              'expenditure_reports.final_by_ro','expenditure_reports.final_by_eci',
                              'expenditure_reports.date_sending_notice_service_to_deo',
                              'expenditure_reports.date_of_issuance_notice','expenditure_reports.date_of_sending_deo',
                              'expenditure_reports.date_of_issuance_notice','m_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.final_by_eci', '<>',1)
                            ->where('expenditure_reports.final_by_ro','1')
                            ->whereNotNull('expenditure_reports.date_sending_notice_service_to_deo')
                           ->where(function($q) {
                                $q->where('expenditure_action_logs.final_action_taken', '=', 'Reply Issued');
                               // ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                               // ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            })
                            ->where('expenditure_action_logs.role_id', 18)
                            ->groupBy('expenditure_action_logs.candidate_id');

                     if($st_code == '0' && $cons_no == '0'){

                     $replybydeo = $sql->get();

                     }elseif ($st_code != '0' && $cons_no == '0') {

                     $sql=$sql->where('expenditure_action_logs.ST_CODE', '=', $st_code);
                     $replybydeo = $sql->get();

                     }elseif ($st_code != '0' && $cons_no != '0') {

                     $sql=$sql->where('expenditure_action_logs.ST_CODE', '=', $st_code);
                     $sql=$sql->where('expenditure_action_logs.constituency_no', '=', $cons_no);
                     $replybydeo = $sql->get();

                     }
                //dd(DB::getQueryLog());
               // dd($replybydeo);
                return view('admin.pc.eci.Expenditure.replybydeo', ['user_data' => $d, 'replybydeo' => $replybydeo, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($replybydeo)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getReplybyDEO TRY CATCH ENDS HERE   
    }// end getReplybyDEO start function

     /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-09-2020
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getReplybyCEO By ECI fuction     
     */
    public function getReplybyCEO(Request $request, $state, $pc) {
        //PC ECI getReplybyCEO TRY CATCH STARTS HERE
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

                $sql=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('expenditure_action_logs', 'expenditure_action_logs.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.party_id','candidate_personal_detail.cand_name', 'expenditure_reports.ST_CODE','expenditure_reports.constituency_no as pc_no','expenditure_reports.candidate_id','expenditure_reports.created_at','expenditure_reports.report_submitted_date as finalized_date', 'expenditure_reports.last_date_prescribed_acct_lodge',
                              'expenditure_reports.date_orginal_acct',
                              'expenditure_reports.final_by_ro','expenditure_reports.final_by_eci',
                              'expenditure_reports.date_sending_notice_service_to_deo',
                              'expenditure_reports.date_of_issuance_notice','expenditure_reports.date_of_sending_deo',
                              'expenditure_reports.date_of_issuance_notice','m_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.final_by_eci', '<>',1)
                            ->where('expenditure_reports.final_by_ro','1')
                           ->where(function($q) {
                                $q->where('expenditure_action_logs.final_action_taken', '=', 'Reply Issued');
                               // ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                               // ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            })
                            ->where('expenditure_action_logs.role_id', 4)
                            ->groupBy('expenditure_action_logs.candidate_id');

                     if($st_code == '0' && $cons_no == '0'){

                     $replybyceo = $sql->get();

                     }elseif ($st_code != '0' && $cons_no == '0') {

                     $sql=$sql->where('expenditure_action_logs.ST_CODE', '=', $st_code);
                     $replybyceo = $sql->get();

                     }elseif ($st_code != '0' && $cons_no != '0') {

                     $sql=$sql->where('expenditure_action_logs.ST_CODE', '=', $st_code);
                     $sql=$sql->where('expenditure_action_logs.constituency_no', '=', $cons_no);
                     $replybyceo = $sql->get();

                     }
                //dd($DataentryStartCandList);
                return view('admin.pc.eci.Expenditure.replybyceo', ['user_data' => $d, 'replybyceo' => $replybyceo, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($replybyceo)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getReplybyCEO TRY CATCH ENDS HERE   
    }// end getReplybyCEO start function

     /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-09-2020
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getclosedATR By ECI fuction     
     */
    public function getclosedATR(Request $request, $state, $pc) {
        //PC ECI getclosedATR TRY CATCH STARTS HERE
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

                $sql=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->leftjoin('expenditure_action_logs', 'expenditure_action_logs.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.party_id','candidate_personal_detail.cand_name', 'expenditure_reports.ST_CODE','expenditure_reports.constituency_no as pc_no','expenditure_reports.candidate_id','expenditure_reports.created_at','expenditure_reports.report_submitted_date as finalized_date', 'expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_issuance_notice','expenditure_reports.date_sending_notice_service_to_deo','expenditure_reports.date_of_sending_deo','m_party.CCODE', 'm_party.PARTYNAME')
                             ->where('expenditure_reports.final_by_ro', '1')
                             ->where('expenditure_reports.final_by_ceo','1')
                            ->where('expenditure_reports.final_by_eci','1')
                            ->whereNotNull('expenditure_reports.date_sending_notice_service_to_deo')
                            ->where(function($q) {
                                $q->where('expenditure_action_logs.final_action_taken', '=', 'Closed')
                                ->orWhere('expenditure_action_logs.final_action_taken', '=', 'Disqualified');
                            })
                            ->where('expenditure_action_logs.role_id',28) 
                            ->groupBy('expenditure_reports.candidate_id');

                if ($st_code == '0' && $cons_no == '0') {
                    $getclosedATR = $sql->get();
                  } elseif ($st_code != '0' && $cons_no == '0') {

                   $sql=$sql->where('expenditure_reports.ST_CODE', '=', $st_code);
                    $getclosedATR = $sql->get();

                } elseif ($st_code != '0' && $cons_no != '0') {
                     $sql=$sql->where('expenditure_reports.ST_CODE', '=', $st_code);
                    $sql=$sql->where('expenditure_reports.constituency_no', '=', $cons_no);
                    $getclosedATR = $sql->get();
                }
                //dd($DataentryStartCandList);
                return view('admin.pc.eci.Expenditure.closedatr', ['user_data' => $d, 'getclosedATR' => $getclosedATR, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($getclosedATR)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getclosedATR TRY CATCH ENDS HERE   
    }// end getclosedATR start function   

/**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 07-09-2020
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getCandATRTracking By ECI fuction     
     */
    public function getCandATRTracking(request $request, $candidate_id) {
        // Get the full URL for the previous request...
        $routesegment = array_slice(explode('/', url()->previous()), -3, 1);

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
                    ->where('expenditure_reports.candidate_id', $candidate_id)
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();
            // dd(DB::getQueryLog());
            // dd($CandidatStatus);

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

            $html .='<div class="scroll-tracks">
           <div class="bs-vertical-wizard">
           <p class="text-left h6 pb-3 pt-4 Orange_text" style="margin-left: -50px;"><strong>ATR Status :' . $CandidatStatus[0]->cand_name . '</strong></p>
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
            if ($routesegment[0] == 'noticeatdeo') {
                $html .= '<p class="yellowSquire">Notice Send by CEO : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>  
                               <p class="yellowSquire">Notice Send by ECI : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p> 
                               <p class="yellowSquire">Notice Received : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>  
                               <p class="yellowSquire">Notice Reply : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>';
            }
             $html .= '</div></span></a>';

             $html .= '<p class="dateleft">0 - 38&nbspDays</p>                  
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
            if ($routesegment[0] == 'noticeatceo') {
                $html .= '<p class="yellowSquire">Notice Received : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p> 
                               <p class="yellowSquire">Notice Send to DEO : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>';
            }
             $html .= '</div></span></a>';

             $html .= '<p class="dateleft">0 - 45&nbspDays</p>
                   </li>                
                   <li class="pending">
                       <a href="#">
                       <i class="ico ico-green">ECI</i> 
                           <span class="desc">                    
                               <div class="contentBox">
                               <div class="date h6 text-warning"><strong>Finalize : ' . $recieptbyeci . '</strong></div>
                               <p class="graySquire"> Received: ' . $recieptbyeci . '</p>
                               <p class="greenSquire">Action :' . $finalbyeci . '</p>
                               <p class="yellowSquire">Action Date : ' . $recieptbyeci . '</p>';
            if ($noticeissuedatebyeci != 'N/A') {
                $html .= '<p class="yellowSquire">Notice Issued : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>';
            }
             $html .= '</div></span></a>';
          
       $atrArray=DB::table('expenditure_action_logs')->where('candidate_id',$candidate_id)->where('role_id',4)->get();

        
          if(count($atrArray) > 0 && $atrArray[0]->role_id==4){  
              $html .= '<p class="dateleft">4 &nbsp Month</p>
                   </li>                
                   <li class="current">
                    <a href="#">
                      <i class="ico ico-green">CEO</i> 
                       <span class="desc">                    
                           <div class="contentBox">';
                           foreach ($atrArray as $key => $Atrvalue) {
                             if($Atrvalue->final_action_taken=='Notice Issued') {
                              $noticeclass='yellowSquire';
                              $ActionbyCEO=$Atrvalue->final_action_taken.' To RO';
                              }
                             $date=date('d-m-Y', strtotime($Atrvalue->final_action_date));

                            $html .= '<p class="'.$noticeclass.'"> '.$ActionbyCEO.': '.$date.'</p>';
                          } //end foreach 
             $html .= '</div></span></a>';
             }  //end if 
             
             $atrROData=DB::table('expenditure_action_logs')->where('candidate_id',$candidate_id)->where('role_id',18)->get();
             
             if(count($atrROData) > 0 && $atrROData[0]->role_id==18){
              $html .= '<p class="dateleft"></p>
                   </li>                
                   <li class="current">
                       <a href="#">
                       <i class="ico ico-green">RO</i> 
                           <span class="desc">                    
                               <div class="contentBox">';
                                foreach ($atrROData as $key => $Atrvalue) {
                                 if(!empty($Atrvalue->date_of_sending_ack_attachment)) {
                                # code...
                                 $ActionbyRO='Acknnowledgement Sent By RO';
                                $date=date('d-m-Y', strtotime($Atrvalue->date_of_sending_ack_eci));
                                 $noticeclass='yellowSquire';
                              }
                               else {
                                $noticeclass='greenSquire';
                                $ActionbyRO=$Atrvalue->final_action_taken.' To CEO/ECI';
                                 $date=date('d-m-Y', strtotime($Atrvalue->final_action_date));
                                 }
                            $html .= '<p class="'.$noticeclass.'"> '.$ActionbyRO.': '.$date.'</p>';
                          } //end foreach 
             $html .= '</div></span></a>';
            }  //end id
            
          $atrReplybyCEO=DB::table('expenditure_action_logs')->where('candidate_id',$candidate_id)
             ->where('role_id',4)->where('final_action_taken','Reply Issued')->get();
          
           if(count($atrReplybyCEO) > 0 && $atrReplybyCEO[0]->role_id==4){ 
              $html .= '<p class="dateleft">&nbsp </p>
                   </li>                
                   <li class="current">
                    <a href="#">
                      <i class="ico ico-green">CEO</i> 
                       <span class="desc">                    
                           <div class="contentBox">';
                           foreach ($atrReplybyCEO as $key => $Atrvalue) {
                             if($Atrvalue->final_action_taken=='Reply Issued') {
                              $noticeclass='yellowSquire';
                              $ActionbyCEO=$Atrvalue->final_action_taken.' To ECI';
                              }
                             $date=date('d-m-Y', strtotime($Atrvalue->final_action_date));

                            $html .= '<p class="'.$noticeclass.'"> '.$ActionbyCEO.': '.$date.'</p>';
                          } //end foreach 
             $html .= '</div></span></a>';
               }  //end if

         $atrECIData=DB::table('expenditure_action_logs')->where('candidate_id',$candidate_id)->where('role_id',28)->get();

             if(count($atrECIData) > 0 && $atrECIData[0]->role_id==28){
              $html .= '<p class="dateleft"></p>
                   </li>                
                   <li class="complete">
                       <a href="#">
                       <i class="ico ico-green">ECI</i> 
                           <span class="desc">                    
                               <div class="contentBox">';
                                foreach ($atrECIData as $key => $Atrvalue) {
                                 if(!empty($Atrvalue->final_action_taken=='Notice Issued')) {
                                # code...
                                $ActionbyECI=$Atrvalue->final_action_taken.' To CEO/RO';
                                $date=date('d-m-Y', strtotime($Atrvalue->date_of_sending_ack_eci));
                                 $noticeclass='yellowSquire';
                              } 
                              if(in_array($Atrvalue->final_action_taken, ['Closed','Disqualified'])) {
                                # code...
                                $ActionbyECI=$Atrvalue->final_action_taken.'By ECI';
                                $date=date('d-m-Y', strtotime($Atrvalue->date_of_sending_ack_eci));
                                 $noticeclass='yellowSquire';
                              }
                               if(!empty($Atrvalue->final_action_taken=='Reply Issued')) {
                                # code...{
                                $noticeclass='greenSquire';
                                $ActionbyECI=$Atrvalue->final_action_taken.' From CEO/RO';
                                 $date=date('d-m-Y', strtotime($Atrvalue->final_action_date));
                                 }
                            $html .= '<p class="'.$noticeclass.'"> '.$ActionbyECI.': '.$date.'</p>';
                          } //end foreach 
             $html .= '</div></span></a>';
               }  //end if
            
             $html .= '</li>
               </ul>
           </div>
           </div>
           </div>
       </div>';

        }

        return $html;
    }


####################end ATR Dashboard #####################################


}

// end class