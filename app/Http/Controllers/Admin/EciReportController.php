<?php
namespace App\Http\Controllers\Admin;
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
use App\adminmodel\ECIModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\ReportModel;
use App\models\Admin\PollDayModel;
use App\models\Admin\StateModel;

// use Maatwebsite\Excel\Excel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

//INCLUDING CLASSES
use App\Classes\xssClean;
use App\Classes\secureCode;

use App\Http\Controllers\Admin\Voting\EciPollDayController;

//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;

date_default_timezone_set('Asia/Kolkata');
    

class EciReportController extends Controller
{   

    //USING TRAIT FOR COMMON FUNCTIONS
    use CommonTraits;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){   
        $this->middleware(['auth:admin','auth']);
        $this->middleware('clean_url');
        $this->middleware('eci');
        $this->commonModel = new commonModel();
        $this->ECIModel = new ECIModel();
        $this->EciPollDayController = new EciPollDayController();

       
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
    */

    protected function guard(){
        return Auth::guard();
    }

   public function dashboard(){ 
        
        $users=Session::get('admin_login_details');
        $user = Auth::user();   
        if(session()->has('admin_login')){  
            $uid=$user->id;
            $d=$this->commonModel->getunewserbyuserid($uid);
            $list_record=$this->ECIModel->getallelectionphasewise();
            $list_state=$this->ECIModel->listcurrentelectionstate();
            $list_phase=$this->ECIModel->listcurrentelectionphase();
            $list_electionid=$this->ECIModel->getallelectionbyid();
            $list=$this->ECIModel->listelectiontype();
           
            $module=$this->commonModel->getallmodule();
             return view('admin.pc.eci.dashboard', ['user_data' => $d,'module' => $module,'list_record' => $list_record,'list_state'=>$list_state,'list_phase'=>$list_phase,'list_electionid'=>$list_electionid,'list'=>$list]);
             
          }
          else {
              return redirect('/admin-login');
          }    
  
        }   // end dashboard function

/*    public function dashboard(){ 
        
        $users=Session::get('admin_login_details');
        $user = Auth::user();   
        if(session()->has('admin_login')){  
            $uid=$user->id;
            $d=$this->commonModel->getunewserbyuserid($uid);
            $list_record=$this->ECIModel->getallelectionphasewise();
            $list_state=$this->ECIModel->listcurrentelectionstate();
            $list_phase=$this->ECIModel->listcurrentelectionphase();
            $list_electionid=$this->ECIModel->getallelectionbyid();
            $list=$this->ECIModel->listelectiontype();
           
            $module=$this->commonModel->getallmodule();
             return view('admin.pc.eci.dashboard', ['user_data' => $d,'module' => $module,'list_record' => $list_record,'list_state'=>$list_state,'list_phase'=>$list_phase,'list_electionid'=>$list_electionid,'list'=>$list]);
             
          }
          else {
              return redirect('/admin-login');
          }    
  
        }   // end dashboard function*/
    

    //ECI ACTIVE USERS REPORT STARTS
    public function EciActiveUsers(Request $request){  
      //ECI ACTIVE USERS REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();

              $EciActiveUsersSelectData = "SELECT ST_NAME,total_user,active_users,ROUND(( active_users/total_user *100) ,2)AS                           percentage FROM (SELECT m.ST_NAME ,COUNT(*) total_user,
                                           COUNT(IF(PASSWORD!='' ,PASSWORD,NULL)) AS active_users
                                           FROM `officer_login` o JOIN m_state m ON m.ST_CODE=o.st_code
                                           WHERE `role_id` NOT IN ('4','21','23','24')  GROUP BY 1) result";
            
             $EciActiveUsers = DB::select($EciActiveUsersSelectData);

             $cur_time  = Carbon::now();
             $st_code = $user_data->st_code;
             $st_name = $user_data->placename;
              //dd($AllPartyList);

            return view('admin.pc.eci.EciActiveUsers',['user_data' => $user_data,'EciActiveUsers' => $EciActiveUsers]);
                            
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI ACTIVE USERS REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI ACTIVE USERS REPORT FUNCTION ENDS

    //ECI ACTIVE USERS EXCEL REPORT STARTS
    public function EciActiveUsersReportExcel(Request $request){  
      //ECI ACTIVE USERS EXCEL REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
             
              $EciActiveUsersSelectData = "SELECT ST_NAME,total_user,active_users,ROUND(( active_users/total_user *100) ,2)AS                           percentage FROM (SELECT m.ST_NAME ,COUNT(*) total_user,
              COUNT(IF(PASSWORD!='' ,PASSWORD,NULL)) AS active_users
              FROM `officer_login` o JOIN m_state m ON m.ST_CODE=o.st_code
              WHERE `role_id` NOT IN ('4','21','23','24')  GROUP BY 1) result";

$EciActiveUsersReportExcel = DB::select($EciActiveUsersSelectData);
//dd($EciActiveUsers);  

$arr  = array();
$TotalUser = 0;
$ActiveUser = 0;


$user = Auth::user();
$export_data[] = ['State Name', 'Total Users', 'Active Users', '% Of Users'];
$headings[]=[];

foreach ($EciActiveUsersReportExcel as $ActiveUsers) {

if($ActiveUsers->total_user ==''){

$ActiveUsers->total_user = '0';

}

if($ActiveUsers->active_users ==''){

$ActiveUsers->active_users = '0';

}

if($ActiveUsers->percentage ==''){

$ActiveUsers->percentage = '0';

}

$export_data[] = [
  $ActiveUsers->ST_NAME,
  $ActiveUsers->total_user,
  $ActiveUsers->active_users,
  $ActiveUsers->percentage,
 ];



$TotalUser             +=   $ActiveUsers->total_user;
$ActiveUser            +=   $ActiveUsers->active_users;


}


              $name_excel = 'EciActiveUsersReportExcel_'.'_'.$cur_time;
              return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 


            //   \Excel::create('EciActiveUsersReportExcel_'.'_'.$cur_time, function($excel)  { 
            //   $excel->sheet('Sheet1', function($sheet)  {

            //   $EciActiveUsersSelectData = "SELECT ST_NAME,total_user,active_users,ROUND(( active_users/total_user *100) ,2)AS                           percentage FROM (SELECT m.ST_NAME ,COUNT(*) total_user,
            //                                COUNT(IF(PASSWORD!='' ,PASSWORD,NULL)) AS active_users
            //                                FROM `officer_login` o JOIN m_state m ON m.ST_CODE=o.st_code
            //                                WHERE `role_id` NOT IN ('4','21','23','24')  GROUP BY 1) result";
            
            //  $EciActiveUsersReportExcel = DB::select($EciActiveUsersSelectData);
            // //dd($EciActiveUsers);  

            //   $arr  = array();
            //    $TotalUser = 0;
            //    $ActiveUser = 0;

            
            //   $user = Auth::user();
            //   foreach ($EciActiveUsersReportExcel as $ActiveUsers) {
                 
            //      if($ActiveUsers->total_user ==''){
                   
            //         $ActiveUsers->total_user = '0';

            //      }

            //      if($ActiveUsers->active_users ==''){
                   
            //         $ActiveUsers->active_users = '0';

            //      }

            //      if($ActiveUsers->percentage ==''){
                   
            //         $ActiveUsers->percentage = '0';

            //      }

            //      $data =  array(
            //               $ActiveUsers->ST_NAME,
            //               $ActiveUsers->total_user,
            //               $ActiveUsers->active_users,
            //               $ActiveUsers->percentage,
            //                     );

            //     $TotalUser             +=   $ActiveUsers->total_user;
            //     $ActiveUser            +=   $ActiveUsers->active_users;

            //               array_push($arr, $data);
            //                // }
            //               }

            //    $totalvalues = array('Total',$TotalUser,$ActiveUser);
            //     // print_r($totalvalues);die;
            //       array_push($arr,$totalvalues);
            //   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'State Name', 'Total Users', 'Active Users', '% Of Users'
            //            )

            //        );

            //      });

            // })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI ACTIVE USERS EXCEL REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI ACTIVE USERS EXCEL REPORT FUNCTION ENDS

    //ECI ACTIVE USERS PDF REPORT STARTS
    public function EciActiveUsersPdf(Request $request){  
      //ECI ACTIVE USERS PDF REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();

              $EciActiveUsersSelectData = "SELECT ST_NAME,total_user,active_users,ROUND(( active_users/total_user *100) ,2)AS percentage FROM (SELECT m.ST_NAME ,COUNT(*) total_user,COUNT(IF(PASSWORD!='' ,PASSWORD,NULL)) AS active_users  FROM `officer_login` o JOIN m_state m ON m.ST_CODE=o.st_code WHERE `role_id` NOT IN ('4','21','23','24')  GROUP BY 1) result";
            
             $EciActiveUsersPdf = DB::select($EciActiveUsersSelectData);

             $cur_time  = Carbon::now();
             $st_code = $user_data->st_code;
             $st_name = $user_data->placename;
              //dd($EciActiveUsersPdf);

             $pdf = PDF::loadView('admin.pc.eci.EciActiveUsersPdf',['user_data' => $user_data,'EciActiveUsersPdf' =>$EciActiveUsersPdf]);
                        return $pdf->download('EciActiveUsersPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.pc.eci.EciActiveUsersPdf');  
                            
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI ACTIVE USERS PDF REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI ACTIVE USERS PDF REPORT FUNCTION ENDS


    //ECI NOMINATION DATA REPORT STARTS
    public function EciNominationReport(Request $request){  
      //ECI NOMINATION DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();

              //SETTING SCHEDULE LIST IN SESSION FOR FILTER STARTS
              $GetAllElectionSchedule = $this->GetAllElectionSchedule();
              Session::put('ScheduleList', $GetAllElectionSchedule);
              //SETTING SCHEDULE LIST IN SESSION FOR FILTER ENDS
              //dd($GetAllElectionSchedule);
             
                          
              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;

             $EciNominationSelectData = "SELECT s.ST_CODE, s.ST_NAME, COUNT(candidate_id) AS total_nomination,
                                         COUNT(IF(application_status=6,nom_id,NULL)) AS accepted_status
                                         FROM candidate_nomination_detail d RIGHT JOIN m_state s ON s.ST_CODE=d.st_code AND `application_status` != 11 AND `party_id` != 1180
                                         AND `pc_no` !=''
                                         GROUP BY 1";
            
             $EciNominationReport = DB::select($EciNominationSelectData);
          

            return view('admin.pc.eci.EciNominationReport',['user_data' => $user_data,'EciNominationReport' => $EciNominationReport]);

            
            }else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI NOMINATION DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI NOMINATION DATA REPORT FUNCTION ENDS

    //ECI NOMINATION EXCEL DATA REPORT STARTS
    public function EciNominationExcelReport(Request $request){  
      //ECI NOMINATION EXCEL DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
             
                          

              \Excel::create('EciNominationsReport'.'_'.$cur_time, function($excel)  { 
              $excel->sheet('Sheet1', function($sheet)  {

              $EciNominationSelectData = "SELECT s.ST_CODE,s.ST_NAME, COUNT(candidate_id) AS total_nomination,
                                         COUNT(IF(application_status=6,nom_id,NULL)) AS accepted_status
                                         FROM candidate_nomination_detail d RIGHT JOIN m_state s ON s.ST_CODE=d.st_code AND `application_status` != 11 AND `party_id` != 1180
                                         AND `pc_no` !=''
                                         GROUP BY 1";
            
             $EciNominations = DB::select($EciNominationSelectData);
            //dd($EciActiveUsers);  

              $arr  = array();
              $TotalNomination = 0;
              $TotalAccepted = 0;
            
              $user = Auth::user();
              foreach ($EciNominations as $NominationsData) {
                
                 if($NominationsData->total_nomination ==''){
                   
                    $NominationsData->total_nomination = '0';

                 }

                 if($NominationsData->accepted_status ==''){
                   
                    $NominationsData->accepted_status = '0';

                 }

                 $data =  array(
                          $NominationsData->ST_NAME,
                          $NominationsData->total_nomination,
                          $NominationsData->accepted_status,
                                ); 

                $TotalNomination             +=   $NominationsData->total_nomination;
                $TotalAccepted               +=   $NominationsData->accepted_status;
                          array_push($arr, $data);
                           // }
                          }

                $totalvalues = array('Total',$TotalNomination,$TotalAccepted);
                // print_r($totalvalues);die;
                  array_push($arr,$totalvalues);

               $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'State Name.', 'Total Nomination Applied', 'Total Accepted'
                             )

                   );

                 });

            })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI NOMINATION EXCEL DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI NOMINATION EXCEL DATA REPORT FUNCTION ENDS

    //ECI NOMINATION DATA PDF REPORT STARTS
    public function EciNominationReportPdf(Request $request){  
      //ECI NOMINATION DATA PDF REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();

              //SETTING SCHEDULE LIST IN SESSION FOR FILTER STARTS
              $GetAllElectionSchedule = $this->GetAllElectionSchedule();
              Session::put('ScheduleList', $GetAllElectionSchedule);
              //SETTING SCHEDULE LIST IN SESSION FOR FILTER ENDS
              //dd($GetAllElectionSchedule);
             
                          
              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;

             $EciNominationSelectData = "SELECT s.ST_CODE, s.ST_NAME, COUNT(candidate_id) AS total_nomination,
                                         COUNT(IF(application_status=6,nom_id,NULL)) AS accepted_status
                                         FROM candidate_nomination_detail d RIGHT JOIN m_state s ON s.ST_CODE=d.st_code AND `application_status` != 11 AND `party_id` != 1180
                                         AND `pc_no` !=''
                                         GROUP BY 1";
            
             $EciNominationReportPdf = DB::select($EciNominationSelectData);


             $pdf = PDF::loadView('admin.pc.eci.EciNominationReportPdf',['user_data' => $user_data,'EciNominationReportPdf' =>$EciNominationReportPdf]);
                        return $pdf->download('EciNominationReportPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.pc.eci.EciNominationReportPdf');  

            
            }else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI NOMINATION DATA PDF REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI NOMINATION DATA PDF REPORT FUNCTION ENDS

    //ECI COUNTING RESULT DATA REPORT STARTS
    public function EciCountingStatusReport(Request $request){  
      //ECI COUNTING RESULT DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;
                          
              $EciCountingSelectData = "SELECT ST_NAME,TOTAL_PC,COUNTING_STARTED,RESULT_DECLARED,
                                        CONCAT(ROUND((RESULT_DECLARED/TOTAL_PC*100),2),'%') AS PERCENTAGE FROM(
                                        SELECT
                                        s.ST_NAME,
                                        COUNT(p.PC_NAME)TOTAL_PC,
                                        COUNT(IF(lead_cand_name!='null' and lead_cand_name!='' ,p.PC_NAME,NULL))COUNTING_STARTED,
                                        COUNT(IF(STATUS='1',p.PC_NAME,NULL))RESULT_DECLARED
                                        FROM winning_leading_candidate w JOIN m_pc p ON w.st_code=p.ST_CODE AND w.pc_no=p.PC_NO
                                        JOIN m_state s ON s.ST_CODE=p.ST_CODE GROUP BY 1) result order by ST_NAME";
            
             $EciCountingStatusReport = DB::select($EciCountingSelectData);

             return view('admin.pc.eci.EciCountingStatusReport',['user_data' => $user_data,'EciCountingStatusReport' => $EciCountingStatusReport]);             
               
            }else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI COUNTING RESULT DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI COUNTING RESULT DATA REPORT FUNCTION ENDS


    //ECI COUNTING RESULT EXCEL DATA REPORT STARTS
    public function EciCountingExcelStatus(Request $request){  
      //ECI COUNTING RESULT EXCEL DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $cur_time    = Carbon::now();
              $st_code = $d->st_code;
              $st_name = $d->placename;
              

              $EciCountingSelectData = "SELECT ST_NAME,TOTAL_PC,COUNTING_STARTED,RESULT_DECLARED,
              CONCAT(ROUND((RESULT_DECLARED/TOTAL_PC*100),2),'%') AS PERCENTAGE FROM(
              SELECT
              s.ST_NAME,
              COUNT(p.PC_NAME)TOTAL_PC,
              COUNT(IF(lead_cand_name!='null' and lead_cand_name!='' ,p.PC_NAME,NULL))COUNTING_STARTED,
              COUNT(IF(STATUS='1',p.PC_NAME,NULL))RESULT_DECLARED
              FROM winning_leading_candidate w JOIN m_pc p ON w.st_code=p.ST_CODE AND w.pc_no=p.PC_NO
              JOIN m_state s ON s.ST_CODE=p.ST_CODE GROUP BY 1) result order by ST_NAME";

                $EciCountingData = DB::select($EciCountingSelectData);
                //dd($PcCeoCountingData);  

                $arr  = array();
                $TotalPc = 0;
                $TotalCountingStarted = 0;
                $TotalDeclared = 0;

                $user = Auth::user();
                $export_data[]=['State', 'No Of PCs','Counting Started in PCs', 'Result Declared in PCs', '% Of Results'];
                $headings[]=[];
                foreach ($EciCountingData as $CountingData) {

                if($CountingData->COUNTING_STARTED ==''){

                $CountingData->COUNTING_STARTED = '0';

                }

                if($CountingData->RESULT_DECLARED ==''){

                $CountingData->RESULT_DECLARED = '0';

                }

                $export_data[] = [
                  $CountingData->ST_NAME,
                        $CountingData->TOTAL_PC,
                        $CountingData->COUNTING_STARTED,
                        $CountingData->RESULT_DECLARED,
                        $CountingData->PERCENTAGE,
                ];




                $TotalPc              += $CountingData->TOTAL_PC;
                $TotalCountingStarted += $CountingData->COUNTING_STARTED;
                $TotalDeclared        += $CountingData->RESULT_DECLARED;

                }

              $name_excel = 'CountingStatusExcel_'.trim($st_name).'_'.$cur_time;
              return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 


            //   \Excel::create('CountingStatusExcel_'.trim($st_name).'_'.$cur_time, function($excel) { 
            //   $excel->sheet('Sheet1', function($sheet) {

            //   $EciCountingSelectData = "SELECT ST_NAME,TOTAL_PC,COUNTING_STARTED,RESULT_DECLARED,
            //                             CONCAT(ROUND((RESULT_DECLARED/TOTAL_PC*100),2),'%') AS PERCENTAGE FROM(
            //                             SELECT
            //                             s.ST_NAME,
            //                             COUNT(p.PC_NAME)TOTAL_PC,
            //                             COUNT(IF(lead_cand_name!='null' and lead_cand_name!='' ,p.PC_NAME,NULL))COUNTING_STARTED,
            //                             COUNT(IF(STATUS='1',p.PC_NAME,NULL))RESULT_DECLARED
            //                             FROM winning_leading_candidate w JOIN m_pc p ON w.st_code=p.ST_CODE AND w.pc_no=p.PC_NO
            //                             JOIN m_state s ON s.ST_CODE=p.ST_CODE GROUP BY 1) result order by ST_NAME";
            
            //  $EciCountingData = DB::select($EciCountingSelectData);
            // //dd($PcCeoCountingData);  

            //   $arr  = array();
            //   $TotalPc = 0;
            //   $TotalCountingStarted = 0;
            //   $TotalDeclared = 0;
            
            //   $user = Auth::user();
            //   foreach ($EciCountingData as $CountingData) {

            //     if($CountingData->COUNTING_STARTED ==''){
                   
            //         $CountingData->COUNTING_STARTED = '0';

            //      }

            //      if($CountingData->RESULT_DECLARED ==''){
                   
            //         $CountingData->RESULT_DECLARED = '0';

            //      }

            //      $data =  array(
            //                       $CountingData->ST_NAME,
            //                       $CountingData->TOTAL_PC,
            //                       $CountingData->COUNTING_STARTED,
            //                       $CountingData->RESULT_DECLARED,
            //                       $CountingData->PERCENTAGE,
            //                     );
                
            //     $TotalPc              += $CountingData->TOTAL_PC;
            //     $TotalCountingStarted += $CountingData->COUNTING_STARTED;
            //     $TotalDeclared        += $CountingData->RESULT_DECLARED;
            //               array_push($arr, $data);
            //                // }
            //               }

            //      $totalvalues = array('Total',$TotalPc,$TotalCountingStarted,$TotalDeclared);
            //     // print_r($totalvalues);die;
            //       array_push($arr,$totalvalues);

            //   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'State', 'No Of PCs','Counting Started in PCs', 'Result Declared in PCs', '% Of Results'
            //                  )

            //        );

            //      });

            // })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI COUNTING RESULT EXCEL DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI COUNTING RESULT EXCEL DATA REPORT FUNCTION ENDS


    //ECI COUNTING RESULT DATA PDF REPORT STARTS
    public function EciCountingStatusReportPdf(Request $request){  
      //ECI COUNTING RESULT DATA PDF REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;
                          
              $EciCountingSelectData = "SELECT ST_NAME,TOTAL_PC,COUNTING_STARTED,RESULT_DECLARED,
                                        CONCAT(ROUND((RESULT_DECLARED/TOTAL_PC*100),2),'%') AS PERCENTAGE FROM(
                                        SELECT
                                        s.ST_NAME,
                                        COUNT(p.PC_NAME)TOTAL_PC,
                                        COUNT(IF(lead_cand_name!='null' and lead_cand_name!='' ,p.PC_NAME,NULL))COUNTING_STARTED,
                                        COUNT(IF(STATUS='1',p.PC_NAME,NULL))RESULT_DECLARED
                                        FROM winning_leading_candidate w JOIN m_pc p ON w.st_code=p.ST_CODE AND w.pc_no=p.PC_NO
                                        JOIN m_state s ON s.ST_CODE=p.ST_CODE GROUP BY 1) result order by ST_NAME";
            
             $EciCountingStatusReportPdf = DB::select($EciCountingSelectData);

             $pdf = PDF::loadView('admin.pc.eci.EciCountingStatusReportPdf',['user_data' => $user_data,'EciCountingStatusReportPdf' =>$EciCountingStatusReportPdf]);
                        return $pdf->download('EciCountingStatusReportPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.pc.eci.EciCountingStatusReportPdf');             
               
            }else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI COUNTING RESULT DATA PDF REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI COUNTING RESULT DATA PDF REPORT FUNCTION ENDS

    //ECI PARTY RESULT DATA REPORT STARTS
    public function EciPartyData(Request $request){  
      //ECI PARTY DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   

          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data =$this->commonModel->getunewserbyuserid($uid);

              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;

              $AllPartyList = $this->GetAllPartyListWithType();
              //dd($AllPartyList);

            return view('admin.pc.eci.EciPartyData',['user_data' => $user_data,'AllPartyList' => $AllPartyList]);
            //return view('admin.pc.ceo.pclist',['user_data' => $d,'ele_details' => $ele_details,'allPcList' => $allTypeCountArr]);
               
          }else {
                  return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI PARTY DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI PARTY DATA REPORT FUNCTION ENDS

    //ECI PARTY EXCEL DATA REPORT STARTS
    public function EciPartyDataExcel(Request $request){  
      //ECI PARTYL EXCEL DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $cur_time    = Carbon::now();
              $st_code = $d->st_code;
              $st_name = $d->placename;
                 
              $AllPartyList = $this->GetAllPartyListWithType();

              $arr  = array();
            
              $user = Auth::user();
              $export_data[] = ['Party Abbreviation', 'Party Name','Party Type'];
              $headings[]=[];
              foreach ($AllPartyList as $listdata) {

                $export_data[] = [
                  $listdata->PARTYABBRE,
                  $listdata->PARTYNAME,
                  $listdata->PARTYTYPE,
                 ];

                //  $data =  array(
                //                   $listdata->PARTYABBRE,
                //                   $listdata->PARTYNAME,
                //                   $listdata->PARTYTYPE,
                //                 );
                //           array_push($arr, $data);
                //            // }
                          }


              $name_excel = 'EciPartyData_'.trim($st_name).'_'.$cur_time;
              return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 


            //   \Excel::create('EciPartyData_'.trim($st_name).'_'.$cur_time, function($excel) { 
            //   $excel->sheet('Sheet1', function($sheet) {

            //  $AllPartyList = $this->GetAllPartyListWithType();

            //   $arr  = array();
            
            //   $user = Auth::user();
            //   foreach ($AllPartyList as $listdata) {

            //      $data =  array(
            //                       $listdata->PARTYABBRE,
            //                       $listdata->PARTYNAME,
            //                       $listdata->PARTYTYPE,
            //                     );
            //               array_push($arr, $data);
            //                // }
            //               }
            //   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'Party Abbreviation', 'Party Name','Party Type'
            //                  )

            //        );

            //      });

            // })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI PARTY EXCEL DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI PARTY EXCEL DATA REPORT FUNCTION ENDS


    //ECI PARTY RESULT DATA PDF REPORT STARTS
    public function EciPartyDataPdf(Request $request){  
      //ECI PARTY DATA PDF REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   

          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data =$this->commonModel->getunewserbyuserid($uid);

              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;

              $EciPartyDataPdf = $this->GetAllPartyListWithType();

              //dd($EciPartyDataPdf);
            $pdf = PDF::loadView('admin.pc.eci.EciPartyDataPdf',['user_data' => $user_data,'EciPartyDataPdf' =>$EciPartyDataPdf]);
                        return $pdf->download('EciPartyDataPdf'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.pc.eci.EciPartyDataPdf'); 
               
          }else {
                  return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI PARTY DATA PDF REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI PARTY DATA PDF REPORT FUNCTION ENDS


    //ECI SYMBOL DATA REPORT STARTS
    public function EciSymbolData(Request $request){  
      //ECI SYMBOL DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   

          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data =$this->commonModel->getunewserbyuserid($uid);

              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;

              $AllSymbolList = DB::table('m_symbol')->orderBy('SYMBOL_NO', 'ASC')->get();
              //dd($AllSymbolList);

            return view('admin.pc.eci.EciSymbolData',['user_data' => $user_data,'AllSymbolList' => $AllSymbolList]);
               
          }else {
                  return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI SYMBOL DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI SYMBOL DATA REPORT FUNCTION ENDS

    //ECI SYMBOL EXCEL DATA REPORT STARTS
    public function EciSymbolDataExcel(Request $request){  
      //ECI PARTY AND SYMBOL EXCEL DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $cur_time    = Carbon::now();
              $st_code = $d->st_code;
              $st_name = $d->placename;
                          
              $export_data[] = ['Symbol No', 'Symbol Name'];
              $headings[]=[];

              $AllSymbolList = DB::table('m_symbol')->orderBy('SYMBOL_NO', 'ASC')->get();

              $arr  = array();
            
              $user = Auth::user();
              foreach ($AllSymbolList as $listdata) {

                $export_data[] = [
                  $listdata->SYMBOL_NO,
                  $listdata->SYMBOL_DES,
                 ];

                
                          }

              $name_excel = 'nomination_report_excel_'.trim($d->st_code).'_'.$cur_time;
              return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 


            //   \Excel::create('EciSymbolData_'.trim($st_name).'_'.$cur_time, function($excel) { 
            //   $excel->sheet('Sheet1', function($sheet) {

            //  $AllSymbolList = DB::table('m_symbol')->orderBy('SYMBOL_NO', 'ASC')->get();

            //   $arr  = array();
            
            //   $user = Auth::user();
            //   foreach ($AllSymbolList as $listdata) {

            //      $data =  array(
            //                       $listdata->SYMBOL_NO,
            //                       $listdata->SYMBOL_DES,
            //                     );
            //               array_push($arr, $data);
            //                // }
            //               }
            //   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'Symbol No', 'Symbol Name'
            //                  )

            //        );

            //      });

            // })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI SYMBOL EXCEL DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI SYMBOL EXCEL DATA REPORT FUNCTION ENDS


    //ECI SYMBOL DATA PDF REPORT STARTS
    public function EciSymbolDataPdf(Request $request){  
      //ECI SYMBOL DATA PDF REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   

          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data =$this->commonModel->getunewserbyuserid($uid);

              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;

              $EciSymbolDataPdf = DB::table('m_symbol')->orderBy('SYMBOL_NO', 'ASC')->get();
              //dd($EciSymbolDataPdf);
             $pdf = PDF::loadView('admin.pc.eci.EciSymbolDataPdf',['user_data' => $user_data,'EciSymbolDataPdf' =>$EciSymbolDataPdf]);
            return $pdf->download('EciSymbolDataPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
            return view('admin.pc.eci.EciSymbolDataPdf'); 
               
          }else {
                  return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI SYMBOL DATA PDF REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI SYMBOL DATA PDF REPORT FUNCTION ENDS


    //ECI PC ELECTION SCHEDULE DATA REPORT STARTS
    public function EciElectionSchedule(Request $request){  
      //ECI PC ELECTION SCHEDULE DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $cur_time    = Carbon::now();
              $st_code     = $user_data->st_code;
              $st_name     = $user_data->placename;

              //SETTING SCHEDULE LIST IN SESSION FOR FILTER STARTS
              $GetAllElectionSchedule = $this->GetAllElectionSchedule();
              Session::put('ScheduleList', $GetAllElectionSchedule);
              //SETTING SCHEDULE LIST IN SESSION FOR FILTER ENDS
              //dd($GetAllElectionSchedule);
              
              
              $ScheduleData =   "SELECT  ms.ST_NAME AS state_name,e.ST_CODE AS state, e.ScheduleID AS sid, 
                                      e.CONST_NO AS cno,  e.CONST_TYPE AS ctype, p.PC_NO AS pcno , p.PC_NAME AS npc, s.DT_ISS_NOM AS start_nomi_date, s.LDT_IS_NOM AS last_nomi_date, 
                                      s.DT_SCR_NOM AS dt_nomi_scr, 
                                     s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                     FROM m_election_details e 
                                     RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                     RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID 
                                     RIGHT JOIN m_state ms ON ms.ST_CODE= e.ST_CODE 
                                     WHERE e.CONST_TYPE= 'PC' ORDER BY sid, state";

              $ScheduleSelectData = DB::select($ScheduleData);
                  
              
              return view('admin.pc.eci.EciElectionSchedule',['user_data' => $user_data,'ScheduleSelectData' =>$ScheduleSelectData]);        
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI PC ELECTION SCHEDULE DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI PC ELECTION SCHEDULE DATA REPORT FUNCTION ENDS


    //ECI PC ELECTION SCHEDULE EXCEL DATA REPORT STARTS
    public function EciElectionScheduleExcel(Request $request){  
      //ECI PC ELECTION SCHEDULE EXCEL DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;
                  
              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $cur_time    = Carbon::now();
              $st_code     = $user_data->st_code;
              $st_name     = $user_data->placename;             


              $ScheduleExcelData =   "SELECT  ms.ST_NAME AS state_name,e.ST_CODE AS state, e.ScheduleID AS sid, 
              e.CONST_NO AS cno,  e.CONST_TYPE AS ctype, p.PC_NO AS pcno , p.PC_NAME AS npc, s.DT_ISS_NOM AS start_nomi_date, s.LDT_IS_NOM AS last_nomi_date, 
              s.DT_SCR_NOM AS dt_nomi_scr, 
             s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
             FROM m_election_details e 
             RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
             RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID 
             RIGHT JOIN m_state ms ON ms.ST_CODE= e.ST_CODE 
             WHERE e.CONST_TYPE= 'PC' ORDER BY sid, state";

              $ScheduleSelectExcelData = DB::select($ScheduleExcelData);
              //dd($ScheduleSelectExcelData);  

              $arr  = array();

              $user = Auth::user();
              $export_data[] = ['Phase No','State', 'PC Name','PC No', 'Issue of Notification', 'Last Date For Filing Nominations', 'Scrutiny Date', 'Last Date For Withdrawl', 'Date Of Poll'];
               $headings[]=[];

              foreach ($ScheduleSelectExcelData as $ScheduleData) {

                $export_data[] = [
                  $ScheduleData->sid,
                  $ScheduleData->state_name,
                  $ScheduleData->npc,
                  $ScheduleData->cno,
                  $ScheduleData->start_nomi_date,
                  $ScheduleData->last_nomi_date,
                  $ScheduleData->dt_nomi_scr,
                  $ScheduleData->last_wid_date,
                  $ScheduleData->poll_date,
                 ];

            
                }


              $name_excel = 'EciElectionScheduleExcelData_'.trim($st_name).'_'.$cur_time;
              return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


            //    \Excel::create('EciElectionScheduleExcelData_'.trim($st_name).'_'.$cur_time, function($excel) use($st_code) { 
            //   $excel->sheet('Sheet1', function($sheet) use($st_code) {

            //   $ScheduleExcelData =   "SELECT  ms.ST_NAME AS state_name,e.ST_CODE AS state, e.ScheduleID AS sid, 
            //                           e.CONST_NO AS cno,  e.CONST_TYPE AS ctype, p.PC_NO AS pcno , p.PC_NAME AS npc, s.DT_ISS_NOM AS start_nomi_date, s.LDT_IS_NOM AS last_nomi_date, 
            //                           s.DT_SCR_NOM AS dt_nomi_scr, 
            //                          s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
            //                          FROM m_election_details e 
            //                          RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
            //                          RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID 
            //                          RIGHT JOIN m_state ms ON ms.ST_CODE= e.ST_CODE 
            //                          WHERE e.CONST_TYPE= 'PC' ORDER BY sid, state";
            
            //  $ScheduleSelectExcelData = DB::select($ScheduleExcelData);
            // //dd($ScheduleSelectExcelData);  

            //   $arr  = array();
            
            //   $user = Auth::user();
            //   foreach ($ScheduleSelectExcelData as $ScheduleData) {

            //      $data =  array(
            //                       $ScheduleData->sid,
            //                       $ScheduleData->state_name,
            //                       $ScheduleData->npc,
            //                       $ScheduleData->cno,
            //                       $ScheduleData->start_nomi_date,
            //                       $ScheduleData->last_nomi_date,
            //                       $ScheduleData->dt_nomi_scr,
            //                       $ScheduleData->last_wid_date,
            //                       $ScheduleData->poll_date,
            //                     );
            //               array_push($arr, $data);
            //                // }
            //               }
            //   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'Phase No','State', 'PC Name','PC No', 'Issue of Notification', 'Last Date For Filing Nominations', 'Scrutiny Date', 'Last Date For Withdrawl', 'Date Of Poll'
            //                  )

            //        );

            //      });

            // })->export('xls');

            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI PC ELECTION SCHEDULE EXCEL DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI PC ELECTION SCHEDULE EXCEL DATA REPORT FUNCTION ENDS

    //PC ECI ELECTION SCHEDULE DATA PDF REPORT FUNCTION STARTS
    public function EciElectionSchedulePdf(Request $request){ 
      //PC ECI ELECTION SCHEDULE DATA PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


              $EciElectionSchedulePdfSelect = "SELECT  ms.ST_NAME AS state_name,e.ST_CODE AS state, e.ScheduleID AS sid,e.CONST_NO AS cno,  e.CONST_TYPE AS ctype, p.PC_NO AS pcno , p.PC_NAME AS npc, s.DT_ISS_NOM AS start_nomi_date, s.LDT_IS_NOM AS last_nomi_date,s.DT_SCR_NOM AS dt_nomi_scr,s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date FROM m_election_details e RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID RIGHT JOIN m_state ms ON ms.ST_CODE= e.ST_CODE WHERE e.CONST_TYPE= 'PC' ORDER BY sid, state";

          
            // dd($EciPhaseNominationSelectData);
            
             $EciElectionSchedulePdf = DB::select($EciElectionSchedulePdfSelect);
                  

             $pdf = PDF::loadView('admin.pc.eci.EciElectionSchedulePdf',['user_data' => $user_data,'EciElectionSchedulePdf' =>$EciElectionSchedulePdf]);
                        return $pdf->download('EciElectionSchedulePdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.pc.eci.EciElectionSchedulePdf');   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ELECTION SCHEDULE  DATA PDF REPORT TRY CATCH ENDS HERE
    }
    //PC ELECTION SCHEDULE DATA PDF REPORT FUNCTION ENDS

    


    //PC ECI ELECTION FILTER FUNCTION STARTS
    public function EciCustomReportFilter(Request $request){ 
      //PC ECI ELECTION FILTER TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $validator = Validator::make($request->all(), [ 
                    'ScheduleList'   => 'nullable|numeric|regex:/^\S*$/u',
                    'state'          => 'nullable|regex:/^\S*$/u',
                    /*'startDate'    => 'required|date',
                    'endDate'        => 'required|date|after_or_equal:startDate',*/
                    
                ]);

            if ($validator->fails()) {
                   return Redirect::back()
                   ->withErrors($validator)
                   ->withInput();          
                }

            $xss = new xssClean;

            $ScheduleList        = $xss->clean_input($request['ScheduleList']);
            $state_code          = $xss->clean_input($request['state']);
            
            if (!$ScheduleList) {
                 $ScheduleList = "";
            }else{
                $ScheduleList = $ScheduleList;
            }
            //STATE CODE
            if (!$state_code) {
                 $state_code = NULL;
            }else{
                $state_code = $state_code;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
                  
        
             return redirect('/eci/EciCustomReportFilterGet/'.base64_encode($state_code).'/'.base64_encode($ScheduleList));         
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI ELECTION FILTER TRY CATCH ENDS HERE
    }
    //PC ECI ELECTION FILTER FUNCTION ENDS
  
  //PC ECI ELECTION FILTER FUNCTION STARTS
    public function EciCustomReportFilterGet(Request $request ,$state_code, $ScheduleList= null){ 
      //AC ECI ELECTION FILTER TRY CATCH STARTS HERE
      try{
          
           //$input = $request->all();
            //echo '<pre>'.print_r(base64_decode($ScheduleList));die;


          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 
      
            $xss    = new xssClean;
            $secure = new secureCode;

           
            $ScheduleList      = base64_decode($ScheduleList);
            $state_code        = base64_decode($state_code);

            //CHECKING URL VARIABLES FOR VALUES STARTS
            //PHASE NO
            if (!$ScheduleList) {
                 $ScheduleList = "";
            }else{
                $ScheduleList = $ScheduleList;
            }
            //STATE CODE
            if (!$state_code) {
                 $state_code = NULL;
            }else{
                $state_code = $state_code;
            }
           //CHECKING URL VARIABLES FOR VALUES ENDS
        
            
            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            if(empty($ScheduleList)){
              
              //CHECKING STATE CODE STARTS
              if($state_code == 'all'){
                 // dd('allstate');
                   $FilterData =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,
                                   e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                                   p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                                   s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                                   s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                   FROM m_election_details e 
                                   RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                   RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                                   RIGHT JOIN m_state st ON st.ST_CODE = p.ST_CODE
                                   WHERE e.CONST_TYPE = 'PC'
                                   ORDER BY state, sid, cno";

                }else{
                   // dd('onestate');
                  $FilterData =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,
                                 e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                                 p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                                 s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                                 s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                 FROM m_election_details e 
                                 RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                 RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                                 RIGHT JOIN m_state st ON st.ST_CODE = p.ST_CODE
                                 WHERE e.CONST_TYPE = 'PC' AND st.ST_CODE = '".$state_code."'
                                 ORDER BY state, sid, cno";
                }
                //CHECKING STATE CODE ENDS

            }else{
                 // dd('both');

                  if($state_code == 'all'){

                    $FilterData = "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,
                                   e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                                   p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                                   s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                                   s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                   FROM m_election_details e 
                                   RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                   RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                                   RIGHT JOIN m_state st ON st.ST_CODE = p.ST_CODE
                                   WHERE e.CONST_TYPE = 'PC' AND e.ScheduleID='".$ScheduleList."'
                                   ORDER BY state, sid, cno"; 

                  }else{

                     $FilterData = "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,
                                   e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                                   p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                                   s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                                   s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                   FROM m_election_details e 
                                   RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                   RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                                   RIGHT JOIN m_state st ON st.ST_CODE = p.ST_CODE
                                   WHERE e.CONST_TYPE = 'PC' AND e.ScheduleID='".$ScheduleList."'
                                   AND st.ST_CODE = '".$state_code."'
                                   ORDER BY state, sid, cno"; 

                  }
                                      

            }
           //dd($FilterData);
            $FilterSelectData = DB::select($FilterData);

             if($state_code != '' &&  $state_code != 'all'){

              $statelist = getstatebystatecode($state_code);
              $state     = $statelist->ST_NAME;

            }else{ $state = "";} 


              
              //dd($FilterSelectData);       
              return view('admin.pc.eci.EciCustomReportFilterGet',['user_data' => $user_data,'FilterSelectData' =>$FilterSelectData,'ScheduleList'=>$ScheduleList,'state_code'=>$state_code,'state'=>$state,'phaseid'=>$ScheduleList]);
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI ELECTION FILTER TRY CATCH ENDS HERE
    }
    //PC ECI ELECTION FILTER FUNCTION ENDS

  
    //PC ECI ELECTION FILTER FUNCTION STARTS
    public function EciCustomReportFilterGetExcel(Request $request, $state_code, $ScheduleList= null){ 
      //PC ECI ELECTION FILTER TRY CATCH STARTS HERE
      try{
          
           //$input = $request->all();
            //echo '<pre>'.print_r(base64_decode($ScheduleList));die;


          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 
      
            $xss    = new xssClean;
            $secure = new secureCode;

          
            $ScheduleList      = base64_decode($ScheduleList);
            $state_code        = base64_decode($state_code);

            //CHECKING URL VARIABLES FOR VALUES STARTS
            //PHASE NO
            if (!$ScheduleList) {
                 $ScheduleList = "";
            }else{
                $ScheduleList = $ScheduleList;
            }
            //STATE CODE
            if (!$state_code) {
                 $state_code = NULL;
            }else{
                $state_code = $state_code;
            }
           //CHECKING URL VARIABLES FOR VALUES ENDS
          
          
            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);
  
            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
              
            $ScheduleList = Session::put('ScheduleList',$ScheduleList);  
            $state_code   = Session::put('state_code',$state_code);    

            \Excel::create('EciElectionScheduleFilterExcelData_'.$cur_time, function($excel) use($st_code) { 
              $excel->sheet('Sheet1', function($sheet) {
              

             $ScheduleList = Session::get('ScheduleList');
             $state_code   = Session::get('state_code');

            if(empty($ScheduleList)){
              
              //CHECKING STATE CODE STARTS
              if($state_code == 'all'){
                 // dd('allstate');
                   $FilterDataExcel =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,
                                   e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                                   p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                                   s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                                   s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                   FROM m_election_details e 
                                   RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                   RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                                   RIGHT JOIN m_state st ON st.ST_CODE = p.ST_CODE
                                   WHERE e.CONST_TYPE = 'PC'
                                   ORDER BY state, sid, cno";

                }else{
                   // dd('onestate');
                  $FilterDataExcel =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,
                                 e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                                 p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                                 s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                                 s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                 FROM m_election_details e 
                                 RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                 RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                                 RIGHT JOIN m_state st ON st.ST_CODE = p.ST_CODE
                                 WHERE e.CONST_TYPE = 'PC' AND st.ST_CODE = '".$state_code."'
                                 ORDER BY state, sid, cno";
                }
                //CHECKING STATE CODE ENDS

            }else{
                 // dd('both');

                  if($state_code == 'all'){

                    $FilterDataExcel = "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,
                                   e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                                   p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                                   s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                                   s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                   FROM m_election_details e 
                                   RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                   RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                                   RIGHT JOIN m_state st ON st.ST_CODE = p.ST_CODE
                                   WHERE e.CONST_TYPE = 'PC' AND e.ScheduleID='".$ScheduleList."'
                                   ORDER BY state, sid, cno"; 

                  }else{

                     $FilterDataExcel = "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,
                                   e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                                   p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                                   s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                                   s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                   FROM m_election_details e 
                                   RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                   RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                                   RIGHT JOIN m_state st ON st.ST_CODE = p.ST_CODE
                                   WHERE e.CONST_TYPE = 'PC' AND e.ScheduleID='".$ScheduleList."'
                                   AND st.ST_CODE = '".$state_code."'
                                   ORDER BY state, sid, cno"; 

                  }
                                      

            }

            
             $ScheduleSelectExcelData = DB::select($FilterDataExcel);
             //dd($ScheduleSelectExcelData);  

              $arr  = array();
            
              $user = Auth::user();
              foreach ($ScheduleSelectExcelData as $ScheduleData) {

                 $data =  array(
                                  $ScheduleData->sid,
                                  $ScheduleData->state,
                                  $ScheduleData->npc,
                                  $ScheduleData->cno,
                                  GetReadableDate($ScheduleData->start_nomi_date),
                                  GetReadableDate($ScheduleData->last_nomi_date),
                                  GetReadableDate($ScheduleData->dt_nomi_scr),
                                  GetReadableDate($ScheduleData->last_wid_date),
                                  GetReadableDate($ScheduleData->poll_date),
                                );
                          array_push($arr, $data);
                           // }
                          }
              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'Phase No', 'State','PC Name','PC No', 'Issue of Notification', 'Last Date For Filing Nominations', 'Scrutiny Date', 'Last Date For Withdrawl', 'Date Of Poll'
                             )

                   );

                 });

            })->export('xls');

            }else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI ELECTION FILTER TRY CATCH ENDS HERE
    }
    //PC ECI ELECTION FILTER FUNCTION ENDS


    //PC ECI ELECTION FILTER PDF REPORT FUNCTION STARTS
    public function EciCustomReportFilterGetPdf(Request $request, $state_code, $ScheduleList= null){ 
      //PC ECI ELECTION FILTER PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 
      
            $xss    = new xssClean;
            $secure = new secureCode;

           
            $ScheduleList      = base64_decode($ScheduleList);
            $state_code        = base64_decode($state_code);

            //CHECKING URL VARIABLES FOR VALUES STARTS
            //PHASE NO
            if (!$ScheduleList) {
                 $ScheduleList = "";
            }else{
                $ScheduleList = $ScheduleList;
            }
            //STATE CODE
            if (!$state_code) {
                 $state_code = NULL;
            }else{
                $state_code = $state_code;
            }
           //CHECKING URL VARIABLES FOR VALUES ENDS
         
            
            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
      
      if(empty($ScheduleList)){
        
        //CHECKING FOR ALL STATE STARTS
                if($state_code == 'all'){
           
                      $FilterData =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,
                                   e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                                   p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                                   s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                                   s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                   FROM m_election_details e 
                                   RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                   RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                                   RIGHT JOIN m_state st ON st.ST_CODE = p.ST_CODE
                                   WHERE e.CONST_TYPE = 'PC'
                                   ORDER BY state, sid, cno";

                }else{
          
                         $FilterData =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,
                                 e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                                 p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                                 s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                                 s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                 FROM m_election_details e 
                                 RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                 RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                                 RIGHT JOIN m_state st ON st.ST_CODE = p.ST_CODE
                                 WHERE e.CONST_TYPE = 'PC' AND st.ST_CODE = '".$state_code."'
                                 ORDER BY state, sid, cno";
          
                }
                //CHECKING FOR ALL STATE ENDS
        
        
      }else{ 
      
          if($state_code == 'all'){
          
                        $FilterData =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,
                                   e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                                   p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                                   s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                                   s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                   FROM m_election_details e 
                                   RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                   RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                                   RIGHT JOIN m_state st ON st.ST_CODE = p.ST_CODE
                                   WHERE e.CONST_TYPE = 'PC' AND e.ScheduleID='".$ScheduleList."'
                                   ORDER BY state, sid, cno";
          
           }else{
          
               $FilterData =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,
                                   e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                                   p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                                   s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                                   s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                   FROM m_election_details e 
                                   RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                   RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                                   RIGHT JOIN m_state st ON st.ST_CODE = p.ST_CODE
                                   WHERE e.CONST_TYPE = 'PC' AND e.ScheduleID='".$ScheduleList."'
                                   AND st.ST_CODE = '".$state_code."'
                                   ORDER BY state, sid, cno";
          
        }
                
      }
      
            //dd($FilterData);

              $FilterSelectData = DB::select($FilterData);
              
              //STATE NAME
        if($state_code != '' &&  $state_code != 'all'){

              $statelist = getstatebystatecode($state_code);
              $state     = $statelist->ST_NAME;

            }else{ $state = "";} 
        
       //PHASE DATES
      if($ScheduleList != ''){

        $PhaseInfo = getschedulebyid($ScheduleList);
      }else{ $PhaseInfo = "";} 
        
         $pdf = PDF::loadView('admin.pc.eci.EciCustomReportFilterGetPdf',['user_data' => $user_data,'FilterSelectData' =>$FilterSelectData,'PhaseInfo'=>$PhaseInfo,'state'=>$state,'phaseid'=>$ScheduleList]);
                        return $pdf->download('EciCustomReportFilterGetPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.pc.eci.EciCustomReportFilterGetPdf');  
        
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI ELECTION FILTER PDF REPORT TRY CATCH ENDS HERE
    }
    //PC ECI ELECTION FILTER PDF REPORT FUNCTION ENDS


    //PC ECI NOMINATION STATE WISE REPORT FUNCTION STARTS
    public function EciNominationStateWiseReport(Request $request, $stcode,$ScheduleList= null){ 
      //PC ECI NOMINATION STATE WISE REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $stcode        = base64_decode($xss->clean_input($request['stcode']));
            $phase         = base64_decode($xss->clean_input($request['ScheduleList']));

            //STATE CODE
            if (!$stcode) {
                 $stcode = NULL;
            }else{
                $stcode = $stcode;
            }
            //PHASE CODE
            if (!$phase) {
                 $phase = NULL;
            }else{
                $phase = $phase;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            if(empty($phase)){

              $EciNominationStateWiseData = "SELECT p.ST_CODE,p.PC_NO,p.PC_NAME,COUNT(c.nom_id) AS totalnomination, COUNT(IF(application_status=6,nom_id,NULL)) AS accepted_status FROM candidate_nomination_detail  c JOIN m_election_details e ON c.ST_CODE = e.ST_CODE AND c.PC_NO = e.CONST_NO AND e.CONST_TYPE = 'PC' RIGHT JOIN m_pc p ON c.pc_no = p.PC_NO AND p.ST_CODE = c.st_code AND party_id != 1180 AND application_status != 11 WHERE p.st_code = '".$stcode."' GROUP BY 1,2";

            }else{
              

              $EciNominationStateWiseData = "SELECT p.ST_CODE,p.PC_NO,p.PC_NAME,COUNT(c.nom_id) AS totalnomination, COUNT(IF(application_status=6,nom_id,NULL)) AS accepted_status FROM candidate_nomination_detail  c JOIN m_election_details e ON c.ST_CODE = e.ST_CODE AND c.PC_NO = e.CONST_NO AND e.CONST_TYPE = 'PC' AND c.`scheduleid`='".$phase."' JOIN m_pc p ON c.pc_no = p.PC_NO AND p.ST_CODE = c.st_code AND party_id != 1180 AND application_status != 11 WHERE p.st_code = '".$stcode."' GROUP BY 1,2";

              /*$EciNominationStateWiseData = "SELECT p.ST_CODE,p.PC_NO,p.PC_NAME, COUNT(c.nom_id) AS totalnomination FROM candidate_nomination_detail AS c 
              RIGHT JOIN m_pc p ON c.pc_no = p.PC_NO AND p.ST_CODE = c.st_code 
              RIGHT JOIN m_state s ON p.ST_CODE = s.ST_CODE 
              RIGHT JOIN m_election_details e ON s.ST_CODE = e.ST_CODE AND p.PC_NO = e.CONST_NO AND CONST_TYPE = 'PC' RIGHT JOIN m_schedule sh ON e.ScheduleID = sh.SCHEDULEID WHERE c.st_code = '".$stcode."' AND party_id != 1180 AND application_status != 11 AND sh.SCHEDULEID = '".$phase."' GROUP BY c.pc_no ORDER BY p.PC_NO";*/

            }
             
            //dd($EciNominationStateWiseData);
             $EciNominationStateWiseReport = DB::select($EciNominationStateWiseData);
                  
            //dd($EciNominationStateWiseReport);      

             return view('admin.pc.eci.EciNominationStateWiseReport',['user_data' => $user_data,'EciNominationStateWiseReport' =>$EciNominationStateWiseReport,'stcode' =>$stcode,'phase' =>$phase]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI NOMINATION STATE WISE REPORT TRY CATCH ENDS HERE
    }
    //PC ECI NOMINATION STATE WISE REPORT FUNCTION ENDS


    //PC ECI NOMINATION STATE WISE REPORT FUNCTION STARTS
    public function EciNominationStateWiseExcelReport(Request $request, $stcode,$phase= null){ 
      //PC ECI NOMINATION STATE WISE REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $stcode        = base64_decode($xss->clean_input($request['stcode']));
            $phase         = base64_decode($xss->clean_input($request['phase']));

            //STATE CODE
            if (!$stcode) {
                 $stcode = NULL;
            }else{
                $stcode = $stcode;
            }
             //PHASE CODE
            if (!$phase) {
                 $phase = NULL;
            }else{
                $phase = $phase;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


            $stcode   = Session::put('stcode',$stcode); 
            $phase   = Session::put('phase',$phase);  
                  
            \Excel::create('EciNominationStateWiseExcelReport'.'_'.$cur_time, function($excel)  { 
              $excel->sheet('Sheet1', function($sheet)  {

            $stcode   = Session::get('stcode');
            $phase   = Session::get('phase');

              if(empty($phase)){

              $EciNominationStateWiseExcel = "SELECT p.ST_CODE,p.PC_NO,p.PC_NAME,COUNT(c.nom_id) AS totalnomination, COUNT(IF(application_status=6,nom_id,NULL)) AS accepted_status FROM candidate_nomination_detail  c JOIN m_election_details e ON c.ST_CODE = e.ST_CODE AND c.PC_NO = e.CONST_NO AND e.CONST_TYPE = 'PC' RIGHT JOIN m_pc p ON c.pc_no = p.PC_NO AND p.ST_CODE = c.st_code AND party_id != 1180 AND application_status != 11 WHERE p.st_code = '".$stcode."' GROUP BY 1,2";
            }else{

              $EciNominationStateWiseExcel = "SELECT p.ST_CODE,p.PC_NO,p.PC_NAME,COUNT(c.nom_id) AS totalnomination, COUNT(IF(application_status=6,nom_id,NULL)) AS accepted_status FROM candidate_nomination_detail  c JOIN m_election_details e ON c.ST_CODE = e.ST_CODE AND c.PC_NO = e.CONST_NO AND e.CONST_TYPE = 'PC' AND c.`scheduleid`='".$phase."' JOIN m_pc p ON c.pc_no = p.PC_NO AND p.ST_CODE = c.st_code AND party_id != 1180 AND application_status != 11 WHERE p.st_code = '".$stcode."' GROUP BY 1,2";

            }
            
             $EciNominationStateWiseExcelReport = DB::select($EciNominationStateWiseExcel);
            
          

              $arr  = array();
              $TotalNomination = 0;
              $TotalAccepted   = 0;
            
              $user = Auth::user();
              foreach ($EciNominationStateWiseExcelReport as $EciNominationStateWise) {
                 
                 if($EciNominationStateWise->PC_NO ==''){
                   
                    $EciNominationStateWise->PC_NO = '0';

                 }

                 if($EciNominationStateWise->PC_NAME ==''){
                   
                    $EciNominationStateWise->PC_NAME = '0';

                 }

                 if($EciNominationStateWise->totalnomination ==''){
                   
                    $EciNominationStateWise->totalnomination = '0';

                 }

                 if($EciNominationStateWise->accepted_status ==''){
                   
                    $EciNominationStateWise->accepted_status = '0';

                 }

                 $data =  array(

                         $EciNominationStateWise->PC_NO,
                         $EciNominationStateWise->PC_NAME,
                         $EciNominationStateWise->totalnomination,
                         $EciNominationStateWise->accepted_status,
                          
                          );

                $TotalNomination    +=   $EciNominationStateWise->totalnomination;
                $TotalAccepted      +=   $EciNominationStateWise->accepted_status;

                          array_push($arr, $data);
                           // }
                          }

                $totalvalues = array('Total','',$TotalNomination,$TotalAccepted);
                // print_r($totalvalues);die;
                  array_push($arr,$totalvalues);
              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'PC No', 'PC Name', 'Total Nomination','Accepted Status'
                       )

                   );

                 });

            })->export('xls');    

             
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI NOMINATION STATE WISE REPORT TRY CATCH ENDS HERE
    }
    //PC ECI NOMINATION STATE WISE REPORT FUNCTION ENDS


    //PC ECI NOMINATION STATE WISE PDF REPORT FUNCTION STARTS
    public function EciNominationStateWisePdf(Request $request, $stcode,$phase= null){ 
      //PC ECI NOMINATION STATE WISE PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $stcode        = base64_decode($xss->clean_input($request['stcode']));
            $phase         = base64_decode($xss->clean_input($request['phase']));

            //STATE CODE
            if (!$stcode) {
                 $stcode = NULL;
            }else{
                $stcode = $stcode;
            }
            //PHASE CODE
            if (!$phase) {
                 $phase = NULL;
            }else{
                $phase = $phase;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            if(empty($phase)){

              $EciNominationStateWiseData = "SELECT p.ST_CODE,p.PC_NO,p.PC_NAME,COUNT(c.nom_id) AS totalnomination, COUNT(IF(application_status=6,nom_id,NULL)) AS accepted_status FROM candidate_nomination_detail  c JOIN m_election_details e ON c.ST_CODE = e.ST_CODE AND c.PC_NO = e.CONST_NO AND e.CONST_TYPE = 'PC' RIGHT JOIN m_pc p ON c.pc_no = p.PC_NO AND p.ST_CODE = c.st_code AND party_id != 1180 AND application_status != 11 WHERE p.st_code = '".$stcode."' GROUP BY 1,2";

            }else{
              

              $EciNominationStateWiseData = "SELECT p.ST_CODE,p.PC_NO,p.PC_NAME,COUNT(c.nom_id) AS totalnomination, COUNT(IF(application_status=6,nom_id,NULL)) AS accepted_status FROM candidate_nomination_detail  c JOIN m_election_details e ON c.ST_CODE = e.ST_CODE AND c.PC_NO = e.CONST_NO AND e.CONST_TYPE = 'PC' AND c.`scheduleid`='".$phase."' JOIN m_pc p ON c.pc_no = p.PC_NO AND p.ST_CODE = c.st_code AND party_id != 1180 AND application_status != 11 WHERE p.st_code = '".$stcode."' GROUP BY 1,2";

              /*$EciNominationStateWiseData = "SELECT p.ST_CODE,p.PC_NO,p.PC_NAME, COUNT(c.nom_id) AS totalnomination FROM candidate_nomination_detail AS c 
              RIGHT JOIN m_pc p ON c.pc_no = p.PC_NO AND p.ST_CODE = c.st_code 
              RIGHT JOIN m_state s ON p.ST_CODE = s.ST_CODE 
              RIGHT JOIN m_election_details e ON s.ST_CODE = e.ST_CODE AND p.PC_NO = e.CONST_NO AND CONST_TYPE = 'PC' RIGHT JOIN m_schedule sh ON e.ScheduleID = sh.SCHEDULEID WHERE c.st_code = '".$stcode."' AND party_id != 1180 AND application_status != 11 AND sh.SCHEDULEID = '".$phase."' GROUP BY c.pc_no ORDER BY p.PC_NO";*/

            }
             
            //dd($EciNominationStateWiseData);
             $EciNominationStateWisePdf = DB::select($EciNominationStateWiseData);
                  
            //dd($EciNominationStateWiseReport); 

            if($stcode != ''){

              $statelist = getstatebystatecode($stcode);
              $state     = $statelist->ST_NAME;

            }else{ $state = "";}   


            //PHASE DATES
            if($phase != ''){

              $PhaseInfo = getschedulebyid($phase);
            }else{ $PhaseInfo = "";}  


             $pdf = PDF::loadView('admin.pc.eci.EciNominationStateWisePdf',['user_data' => $user_data,'EciNominationStateWisePdf' =>$EciNominationStateWisePdf,'state' =>$state,'phaseid' =>$phase,'PhaseInfo'=>$PhaseInfo]);
              return $pdf->download('EciNominationStateWisePdf'.trim($st_name).'_Today_'.$cur_time.'.pdf');
              return view('admin.pc.eci.EciPhaseInfoDataPdf');     
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI NOMINATION STATE WISE PDF REPORT TRY CATCH ENDS HERE
    }
    //PC ECI NOMINATION STATE WISE PDF REPORT FUNCTION ENDS


     //PC ECI NOMINATION PC WISE REPORT FUNCTION STARTS
    public function EciNominationPcWiseReport(Request $request, $stcode, $pcno){ 
      //PC ECI NOMINATION PC WISE REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $pcno        = base64_decode($xss->clean_input($request['pcno']));
            $stcode      = base64_decode($xss->clean_input($request['stcode']));

            //STATE CODE
            if (!$stcode) {
                 $stcode = NULL;
            }else{
                $stcode = $stcode;
            }
            //PC CODE
            if (!$pcno) {
                 $pcno = NULL;
            }else{
                $pcno = $pcno;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


             $EciNominationPcWiseData = "SELECT cn.nom_id,cd.candidate_id,cd.cand_name,p.PC_NAME,mp.PARTYNAME,
             sy.SYMBOL_DES FROM candidate_nomination_detail AS cn 
             RIGHT JOIN `candidate_personal_detail` cd ON cd.candidate_id = cn.candidate_id 
             LEFT JOIN m_pc p ON cn.pc_no = p.PC_NO AND p.ST_CODE = cn.st_code
             LEFT JOIN m_state s ON p.ST_CODE = s.ST_CODE 
             LEFT JOIN m_party mp ON cn.party_id = mp.CCODE 
             LEFT JOIN m_symbol sy ON cn.symbol_id = sy.SYMBOL_NO 
             WHERE cn.st_code ='".$stcode."' AND cn.pc_no  = '".$pcno."'
             AND cn.party_id != 1180 
             AND cn.application_status != 11 
             ORDER BY cn.nom_id";
            
             $EciNominationPcWiseReport = DB::select($EciNominationPcWiseData);
                  
            //dd($EciNominationStateWiseReport);      

             return view('admin.pc.eci.EciNominationPcWiseReport',['user_data' => $user_data,'EciNominationPcWiseReport' =>$EciNominationPcWiseReport,'stcode' =>$stcode,'pcno' =>$pcno]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI NOMINATION PC WISE REPORT TRY CATCH ENDS HERE
    }
    //PC ECI NOMINATION PC WISE REPORT FUNCTION ENDS

    //PC ECI NOMINATION STATE WISE REPORT FUNCTION STARTS
    public function EciNominationPcWiseExcelReport(Request $request, $stcode, $pcno){ 
      //PC ECI NOMINATION STATE WISE REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $pcno        = base64_decode($xss->clean_input($request['pcno']));
            $stcode      = base64_decode($xss->clean_input($request['stcode']));

            //STATE CODE
            if (!$stcode) {
                 $stcode = NULL;
            }else{
                $stcode = $stcode;
            }
            //PC CODE
            if (!$pcno) {
                 $pcno = NULL;
            }else{
                $pcno = $pcno;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


            $pcno   = Session::put('pcno',$pcno); 
            $stcode   = Session::put('stcode',$stcode);  
                  
            \Excel::create('EciNominationPcWiseExcelReport'.'_'.$cur_time, function($excel)  { 
              $excel->sheet('Sheet1', function($sheet)  {

            $stcode   = Session::get('stcode');
            $pcno   = Session::get('pcno');

              $EciNominationPcWiseExcel = "SELECT cn.nom_id,cd.candidate_id,cd.cand_name,p.PC_NAME,mp.PARTYNAME,
             sy.SYMBOL_DES FROM candidate_nomination_detail AS cn 
             RIGHT JOIN `candidate_personal_detail` cd ON cd.candidate_id = cn.candidate_id 
             LEFT JOIN m_pc p ON cn.pc_no = p.PC_NO AND p.ST_CODE = cn.st_code
             LEFT JOIN m_state s ON p.ST_CODE = s.ST_CODE 
             LEFT JOIN m_party mp ON cn.party_id = mp.CCODE 
             LEFT JOIN m_symbol sy ON cn.symbol_id = sy.SYMBOL_NO 
             WHERE cn.st_code ='".$stcode."' AND cn.pc_no  = '".$pcno."'
             AND cn.party_id != 1180 
             AND cn.application_status != 11 
             ORDER BY cn.nom_id";
            
             $EciNominationPcWiseExcelReport = DB::select($EciNominationPcWiseExcel);
            
          

              $arr  = array();
            
              $user = Auth::user();
              foreach ($EciNominationPcWiseExcelReport as $EciNominationPcWise) {
                 
                 if($EciNominationPcWise->cand_name ==''){
                   
                    $EciNominationPcWise->cand_name = '0';

                 }

                 if($EciNominationPcWise->PC_NAME ==''){
                   
                    $EciNominationPcWise->PC_NAME = '0';

                 }

                 if($EciNominationPcWise->PARTYNAME ==''){
                   
                    $EciNominationPcWise->PARTYNAME = '0';

                 }

                if($EciNominationPcWise->SYMBOL_DES ==''){
                   
                    $EciNominationPcWise->SYMBOL_DES = '0';

                 }

                 $data =  array(

                         $EciNominationPcWise->cand_name,
                         $EciNominationPcWise->PC_NAME,
                         $EciNominationPcWise->PARTYNAME,
                         $EciNominationPcWise->SYMBOL_DES,
                          
                          );

                          array_push($arr, $data);
                           // }
                          }
              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'Candidate Name', 'PC Name', 'Party Name', 'Symbol'
                       )

                   );

                 });

            })->export('xls');    

             
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI NOMINATION STATE WISE REPORT TRY CATCH ENDS HERE
    }
    //PC ECI NOMINATION STATE WISE REPORT FUNCTION ENDS


     //PC ECI NOMINATION PC WISE PDF REPORT FUNCTION STARTS
    public function EciNominationPcWisePdf(Request $request, $stcode, $pcno){ 
      //PC ECI NOMINATION PC WISE PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $pcno        = base64_decode($xss->clean_input($request['pcno']));
            $stcode      = base64_decode($xss->clean_input($request['stcode']));

            //STATE CODE
            if (!$stcode) {
                 $stcode = NULL;
            }else{
                $stcode = $stcode;
            }
            //PC CODE
            if (!$pcno) {
                 $pcno = NULL;
            }else{
                $pcno = $pcno;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


             $EciNominationPcWiseData = "SELECT cn.nom_id,cd.candidate_id,cd.cand_name,p.PC_NAME,mp.PARTYNAME,
             sy.SYMBOL_DES FROM candidate_nomination_detail AS cn 
             RIGHT JOIN `candidate_personal_detail` cd ON cd.candidate_id = cn.candidate_id 
             LEFT JOIN m_pc p ON cn.pc_no = p.PC_NO AND p.ST_CODE = cn.st_code
             LEFT JOIN m_state s ON p.ST_CODE = s.ST_CODE 
             LEFT JOIN m_party mp ON cn.party_id = mp.CCODE 
             LEFT JOIN m_symbol sy ON cn.symbol_id = sy.SYMBOL_NO 
             WHERE cn.st_code ='".$stcode."' AND cn.pc_no  = '".$pcno."'
             AND cn.party_id != 1180 
             AND cn.application_status != 11 
             ORDER BY cn.nom_id";
            
             $EciNominationPcWisePdf = DB::select($EciNominationPcWiseData);
                  
            //dd($EciNominationStateWiseReport); 

            if($stcode != ''){

              $statelist = getstatebystatecode($stcode);
              $state     = $statelist->ST_NAME;

            }else{ $state = "";}     


             $pdf = PDF::loadView('admin.pc.eci.EciNominationPcWisePdf',['user_data' => $user_data,'EciNominationPcWisePdf' =>$EciNominationPcWisePdf,'state' =>$state,'pcno' =>$pcno]);
                        return $pdf->download('EciNominationPcWisePdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.pc.eci.EciNominationPcWisePdf');  

                
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI NOMINATION PC WISE PDF REPORT TRY CATCH ENDS HERE
    }
    //PC ECI NOMINATION PC WISE PDF REPORT FUNCTION ENDS


    //PC ECI VIEW NOMINATION FUNCTION STARTS
    public function EciViewNomination(Request $request, $nom_id, $cand_id){ 
      //PC ECI VIEW NOMINATION TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $nom_id        = base64_decode($xss->clean_input($request['nom_id']));
            $cand_id       = base64_decode($xss->clean_input($request['cand_id']));

            //STATE CODE
            if (!$nom_id) {
                 $nom_id = NULL;
            }else{
                $nom_id = $nom_id;
            }
            //PC CODE
            if (!$cand_id) {
                 $cand_id = NULL;
            }else{
                $cand_id = $cand_id;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


             $EciViewNominationData = "SELECT cd.*,cn.*,mp.PARTYNAME,sy.SYMBOL_DES,s.ST_NAME,p.PC_NAME
             FROM `candidate_nomination_detail` AS cn 
             RIGHT JOIN `candidate_personal_detail` cd ON cd.candidate_id = cn.candidate_id 
             LEFT JOIN m_party mp ON cn.party_id = mp.CCODE 
             LEFT JOIN m_symbol sy ON cn.symbol_id = sy.SYMBOL_NO 
             LEFT JOIN m_pc p ON cn.pc_no = p.PC_NO AND p.ST_CODE = cn.st_code
             LEFT JOIN m_state s ON p.ST_CODE = s.ST_CODE 
             WHERE cn.candidate_id='".$cand_id."' AND cn.nom_id= '".$nom_id."'
             AND cn.party_id != 1180 
             AND cn.application_status != 11";
            
             $EciViewNomination = DB::select($EciViewNominationData);
                  
            //dd($EciNominationStateWiseReport);      

             return view('admin.pc.eci.EciViewNomination',['user_data' => $user_data,'EciViewNomination' =>$EciViewNomination,'cand_id'=>$cand_id,'nom_id'=>$nom_id]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI VIEW NOMINATION TRY CATCH ENDS HERE
    }
    //PC ECI VIEW NOMINATION FUNCTION ENDS

    //PC ECI VIEW NOMINATION PDF DOWNLOAD FUNCTION STARTS
    public function EciViewNominationPdf(Request $request, $nom_id, $cand_id){ 
      //PC ECI VIEW NOMINATION PDF DOWNLOAD TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $nom_id        = base64_decode($xss->clean_input($request['nom_id']));
            $cand_id       = base64_decode($xss->clean_input($request['cand_id']));

            //STATE CODE
            if (!$nom_id) {
                 $nom_id = NULL;
            }else{
                $nom_id = $nom_id;
            }
            //PC CODE
            if (!$cand_id) {
                 $cand_id = NULL;
            }else{
                $cand_id = $cand_id;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


             $EciViewNominationPdfData = "SELECT cd.*,cn.*,mp.PARTYNAME,sy.SYMBOL_DES,s.ST_NAME,p.PC_NAME
             FROM `candidate_nomination_detail` AS cn 
             RIGHT JOIN `candidate_personal_detail` cd ON cd.candidate_id = cn.candidate_id 
             LEFT JOIN m_party mp ON cn.party_id = mp.CCODE 
             LEFT JOIN m_symbol sy ON cn.symbol_id = sy.SYMBOL_NO 
             LEFT JOIN m_pc p ON cn.pc_no = p.PC_NO AND p.ST_CODE = cn.st_code
             LEFT JOIN m_state s ON p.ST_CODE = s.ST_CODE 
             WHERE cn.candidate_id='".$cand_id."' AND cn.nom_id= '".$nom_id."'
             AND cn.party_id != 1180 
             AND cn.application_status != 11";
            
             $EciViewNominationPdf = DB::select($EciViewNominationPdfData);
                  
            //dd($EciNominationStateWiseReport);      

             /*return view('admin.pc.eci.EciViewNomination',['user_data' => $user_data,'EciViewNomination' =>$EciViewNomination]);  */ 

             $pdf = PDF::loadView('admin.pc.eci.EciViewNominationPdf',['user_data' => $user_data,'EciViewNominationPdf' =>$EciViewNominationPdf]);
                        return $pdf->download('EciViewNominationPdf_CandId'.trim($cand_id).'_Nomid'.trim($nom_id).'_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.pc.eci.EciViewNominationPdf');
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI VIEW NOMINATION PDF DOWNLOAD TRY CATCH ENDS HERE
    }
    //PC ECI VIEW NOMINATION PDF DOWNLOAD FUNCTION ENDS


    //PC ECI STATE PHASE WISE DATA STATE NAME FUNCTION STARTS
    public function EciNominationStatePhase(Request $request){ 
      //PC ECI STATE PHASE WISE DATA STATE NAME TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $data = $request->all();

            $validator = Validator::make($data, [
              'ScheduleList'      =>'required|numeric',
              ],
              [
                'ScheduleList'=>'Please Select Phase!',
                'ScheduleList.numeric'=>'Phase Should be Numeric',
              ]);

            $ScheduleList       = $xss->clean_input($request['ScheduleList']);

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


             $EciNominationStatePhaseData = "SELECT  ms.ST_NAME AS state_name,e.ST_CODE AS state, e.ScheduleID AS sid, e.CONST_TYPE AS ctype, s.DT_ISS_NOM AS start_nomi_date, s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
               FROM m_election_details e 
               RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID 
               RIGHT JOIN m_state ms ON ms.ST_CODE= e.ST_CODE 
               WHERE e.CONST_TYPE= 'PC' AND e.ScheduleID= '".$ScheduleList."'
               GROUP BY e.ST_CODE ORDER BY sid, state";
            
             $EciNominationStatePhase = DB::select($EciNominationStatePhaseData);
                  
            //dd($EciNominationStateWiseReport);      

             return view('admin.pc.eci.EciNominationStatePhase',['user_data' => $user_data,'EciNominationStatePhase' =>$EciNominationStatePhase,'ScheduleList'=>$ScheduleList]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI STATE PHASE WISE DATA STATE NAME TRY CATCH ENDS HERE
    }
    //PC ECI STATE PHASE WISE DATA STATE NAME FUNCTION ENDS

    //PC ECI STATE PHASE WISE EXCEL DATA STATE NAME FUNCTION STARTS
    public function EciNominationStatePhaseExcel(Request $request,$ScheduleList){ 
      //PC ECI STATE PHASE WISE EXCEL DATA STATE NAME TRY CATCH STARTS HERE

      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $ScheduleList        = base64_decode($xss->clean_input($request['ScheduleList']));
           

            //STATE CODE
            if (!$ScheduleList) {
                 $ScheduleList = NULL;
            }else{
                $ScheduleList = $ScheduleList;
            }
          

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


            $phase   = Session::put('phase',$ScheduleList); 
           
                  
            \Excel::create('EciNominationStatePhaseExcelReport'.'_'.$cur_time, function($excel)  { 
              $excel->sheet('Sheet1', function($sheet)  {

            $phase   = Session::get('phase');
          

              $EciNominationStatePhaseExcel = "SELECT  ms.ST_NAME AS state_name,e.ST_CODE AS state, e.ScheduleID AS sid, e.CONST_TYPE AS ctype, s.DT_ISS_NOM AS start_nomi_date, s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
               FROM m_election_details e 
               RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID 
               RIGHT JOIN m_state ms ON ms.ST_CODE= e.ST_CODE 
               WHERE e.CONST_TYPE= 'PC' AND e.ScheduleID= '".$phase."'
               GROUP BY e.ST_CODE ORDER BY sid, state";
            
             $EciNominationStatePhaseData = DB::select($EciNominationStatePhaseExcel);
           

              $arr  = array();
            
              $user = Auth::user();
              foreach ($EciNominationStatePhaseData as $ScheduleData) {

                 $data =  array(
                                  $ScheduleData->sid,
                                  $ScheduleData->state_name,
                                  $ScheduleData->state,
                                  $ScheduleData->start_nomi_date,
                                  $ScheduleData->last_nomi_date,
                                  $ScheduleData->dt_nomi_scr,
                                  $ScheduleData->last_wid_date,
                                  $ScheduleData->poll_date,
                                );
                          array_push($arr, $data);
                           // }
                          }
              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'Phase','State','State Code','Issue of Notification', 'Last Date For Filing Nominations', 'Scrutiny Date', 'Last Date For Withdrawl', 'Date Of Poll'
                             )

                   );

                 });

            })->export('xls');  

             
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI STATE PHASE WISE EXCEL DATA STATE NAME TRY CATCH ENDS HERE
    }
    //PC ECI STATE PHASE WISE EXCEL DATA STATE NAME FUNCTION ENDS


     //PC ECI NOMINATION PC WISE REPORT FUNCTION STARTS
    public function EciNominationPcPhaseFilter(Request $request){ 
      //PC ECI NOMINATION PC WISE REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $data = $request->all();

            $validator = Validator::make($data, [
              'ScheduleList'      =>'required|numeric',
              ],
              [
                'ScheduleList'=>'Please Select Phase!',
                'ScheduleList.numeric'=>'Phase Should be Numeric',
              ]);

            $stcode        = base64_decode($xss->clean_input($request['stcode']));
            $phase         = $xss->clean_input($request['ScheduleList']);

            //STATE CODE
            if (!$stcode) {
                 $stcode = NULL;
            }else{
                $stcode = $stcode;
            }
            //PHASE CODE
            if (!$phase) {
                 $phase = NULL;
            }else{
                $phase = $phase;
            }

            $uid=$user->id;
//dd($stcode);
            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


              $EciNominationPcPhaseWiseData = "SELECT p.ST_CODE,p.PC_NO,p.PC_NAME, COUNT(c.nom_id) AS totalnomination, COUNT(IF(application_status=6,nom_id,NULL)) AS accepted_status FROM candidate_nomination_detail AS c 
              RIGHT JOIN m_pc p ON c.pc_no = p.PC_NO AND p.ST_CODE = c.st_code 
              RIGHT JOIN m_state s ON p.ST_CODE = s.ST_CODE 
              RIGHT JOIN m_election_details e ON s.ST_CODE = e.ST_CODE AND p.PC_NO = e.CONST_NO AND CONST_TYPE = 'PC' RIGHT JOIN m_schedule sh ON e.ScheduleID = sh.SCHEDULEID WHERE c.st_code = '".$stcode."' AND party_id != 1180 AND application_status != 11 AND sh.SCHEDULEID = '".$phase."' GROUP BY c.pc_no ORDER BY p.PC_NO";

          
            // dd($EciNominationPcPhaseWiseData);
            
             $EciNominationPcPhaseFilter = DB::select($EciNominationPcPhaseWiseData);
                  
            //dd($EciNominationPcPhaseData);      

             return view('admin.pc.eci.EciNominationPcPhaseFilter',['user_data' => $user_data,'EciNominationPcPhaseFilter' =>$EciNominationPcPhaseFilter,'stcode' =>$stcode,'phase' =>$phase]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI NOMINATION PC WISE REPORT TRY CATCH ENDS HERE
    }
    //PC ECI NOMINATION PC WISE REPORT FUNCTION ENDS


    //PC ECI NOMINATION PC WISE REPORT FUNCTION STARTS
    public function EciNominationPcPhaseFilterExcel(Request $request, $stcode,$phase){ 
      //PC ECI NOMINATION PC WISE REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $stcode        = base64_decode($xss->clean_input($request['stcode']));
            $phase         = base64_decode($xss->clean_input($request['phase']));

            //STATE CODE
            if (!$stcode) {
                 $stcode = NULL;
            }else{
                $stcode = $stcode;
            }
             //PHASE CODE
            if (!$phase) {
                 $phase = NULL;
            }else{
                $phase = $phase;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


            $stcode   = Session::put('stcode',$stcode); 
            $phase   = Session::put('phase',$phase);  
                  
            \Excel::create('EciNominationPcPhaseWise'.'_'.$cur_time, function($excel)  { 
              $excel->sheet('Sheet1', function($sheet)  {

            $stcode   = Session::get('stcode');
            $phase   = Session::get('phase');

             $EciNominationPcPhaseWiseData = "SELECT p.ST_CODE,p.PC_NO,p.PC_NAME, COUNT(c.nom_id) AS totalnomination, COUNT(IF(application_status=6,nom_id,NULL)) AS accepted_status FROM candidate_nomination_detail AS c 
              RIGHT JOIN m_pc p ON c.pc_no = p.PC_NO AND p.ST_CODE = c.st_code 
              RIGHT JOIN m_state s ON p.ST_CODE = s.ST_CODE 
              RIGHT JOIN m_election_details e ON s.ST_CODE = e.ST_CODE AND p.PC_NO = e.CONST_NO AND CONST_TYPE = 'PC' RIGHT JOIN m_schedule sh ON e.ScheduleID = sh.SCHEDULEID WHERE c.st_code = '".$stcode."' AND party_id != 1180 AND application_status != 11 AND sh.SCHEDULEID = '".$phase."' GROUP BY c.pc_no ORDER BY p.PC_NO";
            
             $EciNominationPcPhaseWiseExcelReport = DB::select($EciNominationPcPhaseWiseData);
            
          

              $arr  = array();
              $TotalNomination = 0;
              $TotalAcc = 0;
            
              $user = Auth::user();
              foreach ($EciNominationPcPhaseWiseExcelReport as $EciNominationPcPhaseWise) {
                 
                 if($EciNominationPcPhaseWise->PC_NO ==''){
                   
                    $EciNominationPcPhaseWise->PC_NO = '0';

                 }

                 if($EciNominationPcPhaseWise->PC_NAME ==''){
                   
                    $EciNominationPcPhaseWise->PC_NAME = '0';

                 }

                 if($EciNominationPcPhaseWise->totalnomination ==''){
                   
                    $EciNominationPcPhaseWise->totalnomination = '0';

                 }

                 if($EciNominationPcPhaseWise->accepted_status ==''){
                   
                    $EciNominationPcPhaseWise->accepted_status = '0';

                 }

                 $data =  array(

                         $EciNominationPcPhaseWise->PC_NO,
                         $EciNominationPcPhaseWise->PC_NAME,
                         $EciNominationPcPhaseWise->totalnomination,
                         $EciNominationPcPhaseWise->accepted_status,
                          
                          );

                 $TotalNomination    +=   $EciNominationPcPhaseWise->totalnomination;
                 $TotalAcc           +=   $EciNominationPcPhaseWise->accepted_status;

                          array_push($arr, $data);
                           // }
                          }

                $totalvalues = array('Total','',$TotalNomination,$TotalAcc);
                // print_r($totalvalues);die;
                  array_push($arr,$totalvalues);

              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'PC No', 'PC Name', 'Total Nomination','Accepted Status'
                       )

                   );

                 });

            })->export('xls');    

             
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI NOMINATION PC WISE REPORT TRY CATCH ENDS HERE
    }
    //PC ECI NOMINATION PC WISE REPORT FUNCTION ENDS


     //PC ECI NOMINATION PC WISE PDF REPORT FUNCTION STARTS
    public function EciNominationPcPhaseFilterPdf(Request $request, $stcode,$phase){ 
      //PC ECI NOMINATION PC WISE PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $stcode        = base64_decode($xss->clean_input($request['stcode']));
            $phase         = base64_decode($xss->clean_input($request['phase']));

            //STATE CODE
            if (!$stcode) {
                 $stcode = NULL;
            }else{
                $stcode = $stcode;
            }
             //PHASE CODE
            if (!$phase) {
                 $phase = NULL;
            }else{
                $phase = $phase;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
                  
      

             $EciNominationPcPhaseWiseData = "SELECT p.ST_CODE,p.PC_NO,p.PC_NAME, COUNT(c.nom_id) AS totalnomination, COUNT(IF(application_status=6,nom_id,NULL)) AS accepted_status FROM candidate_nomination_detail AS c 
              RIGHT JOIN m_pc p ON c.pc_no = p.PC_NO AND p.ST_CODE = c.st_code 
              RIGHT JOIN m_state s ON p.ST_CODE = s.ST_CODE 
              RIGHT JOIN m_election_details e ON s.ST_CODE = e.ST_CODE AND p.PC_NO = e.CONST_NO AND CONST_TYPE = 'PC' RIGHT JOIN m_schedule sh ON e.ScheduleID = sh.SCHEDULEID WHERE c.st_code = '".$stcode."' AND party_id != 1180 AND application_status != 11 AND sh.SCHEDULEID = '".$phase."' GROUP BY c.pc_no ORDER BY p.PC_NO";
            
             $EciNominationPcPhaseFilterPdf = DB::select($EciNominationPcPhaseWiseData);

              if($stcode != ''){

              $statelist = getstatebystatecode($stcode);
              $state     = $statelist->ST_NAME;

              }else{ $state = "";}


             $pdf = PDF::loadView('admin.pc.eci.EciNominationPcPhaseFilterPdf',['user_data' => $user_data,'EciNominationPcPhaseFilterPdf' =>$EciNominationPcPhaseFilterPdf,'state'=>$state,'phaseid'=>$phase]);
                        return $pdf->download('EciNominationPcPhaseWisePdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.pc.eci.EciNominationPcPhaseWisePdf'); 
            
          

             
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI NOMINATION PC WISE PDF REPORT TRY CATCH ENDS HERE
    }
    //PC ECI NOMINATION PC WISE PDF REPORT FUNCTION ENDS


    //PC ECI PHASE INFO DATA REPORT FUNCTION STARTS
    public function EciPhaseInfoData(Request $request){ 
      //PC ECI PHASE INFO DATA REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            //SETTING SCHEDULE LIST IN SESSION FOR FILTER STARTS
            $GetAllElectionSchedule = $this->GetAllElectionSchedule();
            Session::put('ScheduleList', $GetAllElectionSchedule);
            //SETTING SCHEDULE LIST IN SESSION FOR FILTER ENDS
            //dd($GetAllElectionSchedule);


              $EciPhaseInfoDataSelect = "SELECT s.`ST_NAME`,s.`ST_CODE`, COUNT(IF(application_status!=11,c.`candidate_id`,NULL)) TOTAL_NOMINATION, COUNT(IF(`cand_party_type`='N' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) NATIONAL, COUNT(IF(`cand_party_type`='S' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) STATE, COUNT(IF(`cand_party_type` IN ('U','0') AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) OTHER, COUNT(IF(`cand_party_type`='Z' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) INDEPENDENT, COUNT(IF(`cand_gender`='male' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) male, COUNT(IF(`cand_gender`='female' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) female,COUNT(IF(`cand_gender`='third' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) others,COUNT(IF(`cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) total FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.ST_CODE=c.st_code AND c.pc_no=e.`CONST_NO` AND `party_id`!=1180 AND `CONST_TYPE`='PC' RIGHT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 1 ORDER BY 2";

          
            // dd($EciPhaseNominationSelectData);
            
             $EciPhaseInfoData = DB::select($EciPhaseInfoDataSelect);
                  
            //dd($EciNominationPcPhaseData);      

             return view('admin.pc.eci.EciPhaseInfoData',['user_data' => $user_data,'EciPhaseInfoData' =>$EciPhaseInfoData]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC PHASE INFO DATA REPORT TRY CATCH ENDS HERE
    }
    //PC PHASE INFO DATA REPORT FUNCTION ENDS

    //PC ECI PHASE INFO DATA REPORT EXCEL FUNCTION STARTS
    public function EciPhaseInfoDataExcel(Request $request){ 
      //PC ECI PHASE INFO DATA REPORT EXCEL TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

     

           
            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            $EciPhaseInfoDataCandWiseExcel = "SELECT s.`ST_NAME`,s.`ST_CODE`,COUNT(IF(application_status!=11,c.`candidate_id`,NULL)) TOTAL_NOMINATION, COUNT(IF(`cand_party_type`='N' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) NATIONAL, COUNT(IF(`cand_party_type`='S' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) STATE, COUNT(IF(`cand_party_type` IN ('U','0') AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) OTHER, COUNT(IF(`cand_party_type`='Z' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) INDEPENDENT, COUNT(IF(`cand_gender`='male' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) male, COUNT(IF(`cand_gender`='female' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) female,COUNT(IF(`cand_gender`='third' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) others, COUNT(IF(`cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) total
            FROM candidate_nomination_detail c
            JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id`
            JOIN `m_election_details` e ON e.ST_CODE=c.st_code AND c.pc_no=e.`CONST_NO` AND `party_id`!=1180 AND `CONST_TYPE`='PC'
            RIGHT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 1 ORDER BY 2";
       

         $EciPhaseInfoDataCandWiseExcelData = DB::select($EciPhaseInfoDataCandWiseExcel);
        
      

          $arr  = array();

          $TotalNomination = 0; 
          $TotalNational = 0;
          $TotalState = 0;
          $TotalOther= 0;
          $TotalIndependent = 0;
          $TotalMale = 0;
          $TotalFemale = 0;
          $TotalOthers= 0;
          $TotalValidNomination=0;
      
          $user = Auth::user();
          $export_data[] = [ 'State', 'Total Nominations Filed', 'National Parties', 'State Parties',
          'Other Parties','Independent ','Male','Female','Others','Total Valid Nominations',];
          $headings[]=[];

          foreach ($EciPhaseInfoDataCandWiseExcelData as $EciPhaseInfoDataCandWise) {
             
             if($EciPhaseInfoDataCandWise->ST_NAME ==''){
               
                $EciPhaseInfoDataCandWise->ST_NAME = '0';

             }

             if($EciPhaseInfoDataCandWise->TOTAL_NOMINATION ==''){
               
                $EciPhaseInfoDataCandWise->TOTAL_NOMINATION = '0';

             }

             if($EciPhaseInfoDataCandWise->NATIONAL ==''){
               
                $EciPhaseInfoDataCandWise->NATIONAL = '0';

             }

             if($EciPhaseInfoDataCandWise->STATE ==''){
               
                $EciPhaseInfoDataCandWise->STATE = '0';

             }

             if($EciPhaseInfoDataCandWise->OTHER ==''){
               
                $EciPhaseInfoDataCandWise->OTHER = '0';

             }

             if($EciPhaseInfoDataCandWise->INDEPENDENT ==''){
               
                $EciPhaseInfoDataCandWise->INDEPENDENT = '0';

             }

             if($EciPhaseInfoDataCandWise->male ==''){
               
                $EciPhaseInfoDataCandWise->male = '0';

             }

             if($EciPhaseInfoDataCandWise->female ==''){
               
                $EciPhaseInfoDataCandWise->female = '0';

             }

             if($EciPhaseInfoDataCandWise->others ==''){
               
                $EciPhaseInfoDataCandWise->others = '0';

             }

             if($EciPhaseInfoDataCandWise->total ==''){
               
                $EciPhaseInfoDataCandWise->total = '0';

             }

             $export_data[] = [
              $EciPhaseInfoDataCandWise->ST_NAME,
              $EciPhaseInfoDataCandWise->TOTAL_NOMINATION,
              $EciPhaseInfoDataCandWise->NATIONAL,
              $EciPhaseInfoDataCandWise->STATE,
              $EciPhaseInfoDataCandWise->OTHER,
              $EciPhaseInfoDataCandWise->INDEPENDENT,
              $EciPhaseInfoDataCandWise->male,
              $EciPhaseInfoDataCandWise->female,
              $EciPhaseInfoDataCandWise->others,
              $EciPhaseInfoDataCandWise->total,
             ];

           


            $TotalNomination             +=   $EciPhaseInfoDataCandWise->TOTAL_NOMINATION;
            $TotalNational               +=   $EciPhaseInfoDataCandWise->NATIONAL;
            $TotalState                  +=   $EciPhaseInfoDataCandWise->STATE;
            $TotalOther                  +=   $EciPhaseInfoDataCandWise->OTHER;
            $TotalIndependent            +=   $EciPhaseInfoDataCandWise->INDEPENDENT;
            $TotalMale                   +=   $EciPhaseInfoDataCandWise->male;
            $TotalFemale                 +=   $EciPhaseInfoDataCandWise->female;
            $TotalOthers                 +=   $EciPhaseInfoDataCandWise->others;
            $TotalValidNomination        +=   $EciPhaseInfoDataCandWise->total;

                      
                      }

            $name_excel = 'EciNominationExcel'.'_'.$cur_time;
            return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

                  
            // \Excel::create('EciNominationExcel'.'_'.$cur_time, function($excel)  { 
            //   $excel->sheet('Sheet1', function($sheet)  {

            //  $EciPhaseInfoDataCandWiseExcel = "SELECT s.`ST_NAME`,s.`ST_CODE`,COUNT(IF(application_status!=11,c.`candidate_id`,NULL)) TOTAL_NOMINATION, COUNT(IF(`cand_party_type`='N' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) NATIONAL, COUNT(IF(`cand_party_type`='S' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) STATE, COUNT(IF(`cand_party_type` IN ('U','0') AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) OTHER, COUNT(IF(`cand_party_type`='Z' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) INDEPENDENT, COUNT(IF(`cand_gender`='male' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) male, COUNT(IF(`cand_gender`='female' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) female,COUNT(IF(`cand_gender`='third' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) others, COUNT(IF(`cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) total
            //     FROM candidate_nomination_detail c
            //     JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id`
            //     JOIN `m_election_details` e ON e.ST_CODE=c.st_code AND c.pc_no=e.`CONST_NO` AND `party_id`!=1180 AND `CONST_TYPE`='PC'
            //     RIGHT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 1 ORDER BY 2";
           

            //  $EciPhaseInfoDataCandWiseExcelData = DB::select($EciPhaseInfoDataCandWiseExcel);
            
          

            //   $arr  = array();

            //   $TotalNomination = 0; 
            //   $TotalNational = 0;
            //   $TotalState = 0;
            //   $TotalOther= 0;
            //   $TotalIndependent = 0;
            //   $TotalMale = 0;
            //   $TotalFemale = 0;
            //   $TotalOthers= 0;
            //   $TotalValidNomination=0;
          
            //   $user = Auth::user();

            //   foreach ($EciPhaseInfoDataCandWiseExcelData as $EciPhaseInfoDataCandWise) {
                 
            //      if($EciPhaseInfoDataCandWise->ST_NAME ==''){
                   
            //         $EciPhaseInfoDataCandWise->ST_NAME = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->TOTAL_NOMINATION ==''){
                   
            //         $EciPhaseInfoDataCandWise->TOTAL_NOMINATION = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->NATIONAL ==''){
                   
            //         $EciPhaseInfoDataCandWise->NATIONAL = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->STATE ==''){
                   
            //         $EciPhaseInfoDataCandWise->STATE = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->OTHER ==''){
                   
            //         $EciPhaseInfoDataCandWise->OTHER = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->INDEPENDENT ==''){
                   
            //         $EciPhaseInfoDataCandWise->INDEPENDENT = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->male ==''){
                   
            //         $EciPhaseInfoDataCandWise->male = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->female ==''){
                   
            //         $EciPhaseInfoDataCandWise->female = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->others ==''){
                   
            //         $EciPhaseInfoDataCandWise->others = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->total ==''){
                   
            //         $EciPhaseInfoDataCandWise->total = '0';

            //      }


            //     $data =  array(

            //              $EciPhaseInfoDataCandWise->ST_NAME,
            //              $EciPhaseInfoDataCandWise->TOTAL_NOMINATION,
            //              $EciPhaseInfoDataCandWise->NATIONAL,
            //              $EciPhaseInfoDataCandWise->STATE,
            //              $EciPhaseInfoDataCandWise->OTHER,
            //              $EciPhaseInfoDataCandWise->INDEPENDENT,
            //              $EciPhaseInfoDataCandWise->male,
            //              $EciPhaseInfoDataCandWise->female,
            //              $EciPhaseInfoDataCandWise->others,
            //              $EciPhaseInfoDataCandWise->total,
                          
            //               );


            //     $TotalNomination             +=   $EciPhaseInfoDataCandWise->TOTAL_NOMINATION;
            //     $TotalNational               +=   $EciPhaseInfoDataCandWise->NATIONAL;
            //     $TotalState                  +=   $EciPhaseInfoDataCandWise->STATE;
            //     $TotalOther                  +=   $EciPhaseInfoDataCandWise->OTHER;
            //     $TotalIndependent            +=   $EciPhaseInfoDataCandWise->INDEPENDENT;
            //     $TotalMale                   +=   $EciPhaseInfoDataCandWise->male;
            //     $TotalFemale                 +=   $EciPhaseInfoDataCandWise->female;
            //     $TotalOthers                 +=   $EciPhaseInfoDataCandWise->others;
            //     $TotalValidNomination        +=   $EciPhaseInfoDataCandWise->total;

            //               array_push($arr, $data);
            //                // }
            //               }

            //     $totalvalues = array('Total',$TotalNomination,$TotalNational,$TotalState,$TotalOther, $TotalIndependent, $TotalMale,$TotalFemale,$TotalOthers,$TotalValidNomination);
            //     // print_r($totalvalues);die;
            //     array_push($arr,$totalvalues);

            //   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'State', 'Total Nominations Filed', 'National Parties', 'State Parties',
            //                    'Other Parties','Independent ','Male','Female','Others','Total Valid Nominations',
            //            )

            //        );

            //      });

            // })->export('xls');    

             
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI PHASE INFO DATA REPORT EXCEL REPORT TRY CATCH ENDS HERE
    }
    //PC ECI PHASE INFO DATA REPORT EXCEL REPORT FUNCTION ENDS

    //PC ECI PHASE INFO DATA PDF REPORT FUNCTION STARTS
    public function EciPhaseInfoDataPdf(Request $request){ 
      //PC ECI PHASE INFO DATA PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            //SETTING SCHEDULE LIST IN SESSION FOR FILTER STARTS
            $GetAllElectionSchedule = $this->GetAllElectionSchedule();
            Session::put('ScheduleList', $GetAllElectionSchedule);
            //SETTING SCHEDULE LIST IN SESSION FOR FILTER ENDS
            //dd($GetAllElectionSchedule);


              $EciPhaseInfoDataSelect = "SELECT s.`ST_NAME`,s.`ST_CODE`, COUNT(IF(application_status!=11,c.`candidate_id`,NULL)) TOTAL_NOMINATION, COUNT(IF(`cand_party_type`='N' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) NATIONAL, COUNT(IF(`cand_party_type`='S' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) STATE, COUNT(IF(`cand_party_type` IN ('U','0') AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) OTHER, COUNT(IF(`cand_party_type`='Z' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) INDEPENDENT, COUNT(IF(`cand_gender`='male' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) male, COUNT(IF(`cand_gender`='female' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) female,COUNT(IF(`cand_gender`='third' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) others,COUNT(IF(`cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) total FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.ST_CODE=c.st_code AND c.pc_no=e.`CONST_NO` AND `party_id`!=1180 AND `CONST_TYPE`='PC' RIGHT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 1 ORDER BY 2";

          
            // dd($EciPhaseNominationSelectData);
            
             $EciPhaseInfoDataPdf = DB::select($EciPhaseInfoDataSelect);
                  
            //dd($EciNominationPcPhaseData);      

            // return view('admin.pc.eci.EciPhaseInfoDataPdf',['user_data' => $user_data,'EciPhaseInfoDataPdf' =>$EciPhaseInfoDataPdf]);


             $pdf = PDF::loadView('admin.pc.eci.EciPhaseInfoDataPdf',['user_data' => $user_data,'EciPhaseInfoDataPdf' =>$EciPhaseInfoDataPdf]);
                        return $pdf->download('EciPhaseInfoDataPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.pc.eci.EciPhaseInfoDataPdf');   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC PHASE INFO DATA PDF REPORT TRY CATCH ENDS HERE
    }
    //PC PHASE INFO DATA PDF REPORT FUNCTION ENDS


    //PC ECI PHASE INFO REPORT DATA BY PHASE ID FORM FUNCTION STARTS
    public function EciPhaseInfoDataCandWiseForm(Request $request){ 
      //PC ECI PHASE INFO REPORT DATA BY PHASE ID FORM TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $validator = Validator::make($request->all(), [ 
                    'phaseid'   => 'nullable|numeric|regex:/^\S*$/u',
                ]);

            if ($validator->fails()) {
                   return Redirect::back()
                   ->withErrors($validator)
                   ->withInput();          
                }

            $xss = new xssClean;

            $phaseid        = $xss->clean_input($request['phaseid']);
         
            
            if (!$phaseid) {
                 $phaseid = "";
            }else{
                $phaseid = $phaseid;
            }


            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
                  
        
             return redirect('/eci/EciPhaseInfoDataCandWise/'.base64_encode($phaseid));         
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI PHASE INFO REPORT DATA BY PHASE ID FORM TRY CATCH ENDS HERE
    }
    //PC ECI PHASE INFO REPORT DATA BY PHASE ID FORM FUNCTION ENDS



    //PC ECI PHASE CANDIDATE WISE INFO DATA REPORT FUNCTION STARTS
    public function EciPhaseInfoDataCandWise(Request $request,$phaseid){ 
      //PC ECI PHASE CANDIDATE WISE INFO DATA REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            $phaseid         = base64_decode($xss->clean_input($request['phaseid']));

            //STATE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }


              $EciPhaseInfoDataCandWiseData = "SELECT s.`ST_NAME`,s.`ST_CODE`,COUNT(IF(application_status!=11,c.`candidate_id`,NULL)) TOTAL_NOMINATION, COUNT(IF(`cand_party_type`='N' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) NATIONAL, COUNT(IF(`cand_party_type`='S' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) STATE, COUNT(IF(`cand_party_type` IN ('U','0') AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) OTHER, COUNT(IF(`cand_party_type`='Z' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) INDEPENDENT, COUNT(IF(`cand_gender`='male' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) male, COUNT(IF(`cand_gender`='female' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) female,COUNT(IF(`cand_gender`='third' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) others,
                COUNT(IF(`cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) total
                FROM candidate_nomination_detail c
                JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id`
                JOIN `m_election_details` e ON e.ST_CODE=c.st_code AND c.pc_no=e.`CONST_NO` AND e.`ScheduleID`='".$phaseid."' AND `party_id`!=1180 AND `CONST_TYPE`='PC'
                RIGHT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 1 ORDER BY 2";

          
            // dd($EciPhaseNominationSelectData);
            
             $EciPhaseInfoDataCandWise = DB::select($EciPhaseInfoDataCandWiseData);
                  
            //dd($EciPhaseInfoDataCandWise);      

             return view('admin.pc.eci.EciPhaseInfoDataCandWise',['user_data' => $user_data,'EciPhaseInfoDataCandWise' =>$EciPhaseInfoDataCandWise,'phaseid'=>$phaseid]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC PHASE CANDIDATE WISE INFO DATA REPORT TRY CATCH ENDS HERE
    }
    //PC PHASE CANDIDATE WISE INFO DATA REPORT FUNCTION ENDS


     //PC PHASE CANDIDATE WISE INFO DATA EXCEL REPORT FUNCTION STARTS
    public function EciPhaseInfoDataCandWiseExcel(Request $request,$phaseid){ 
      //PCPHASE CANDIDATE WISE INFO DATA EXCEL REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $phaseid         = base64_decode($xss->clean_input($request['phaseid']));

             //PHASE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            $phaseid   = Session::put('phaseid',$phaseid);  
                  
            \Excel::create('EciNominationPcPhaseFilterExcel'.'_'.$cur_time, function($excel)  { 
              $excel->sheet('Sheet1', function($sheet)  {

            $phaseid   = Session::get('phaseid');

             $EciPhaseInfoDataCandWiseExcel = "SELECT s.`ST_NAME`,s.`ST_CODE`,COUNT(IF(application_status!=11,c.`candidate_id`,NULL)) TOTAL_NOMINATION, COUNT(IF(`cand_party_type`='N' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) NATIONAL, COUNT(IF(`cand_party_type`='S' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) STATE, COUNT(IF(`cand_party_type` IN ('U','0') AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) OTHER, COUNT(IF(`cand_party_type`='Z' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) INDEPENDENT, COUNT(IF(`cand_gender`='male' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) male, COUNT(IF(`cand_gender`='female' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) female,COUNT(IF(`cand_gender`='third' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) others, 
              COUNT(IF(`cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) total
                FROM candidate_nomination_detail c
                JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id`
                JOIN `m_election_details` e ON e.ST_CODE=c.st_code AND c.pc_no=e.`CONST_NO` AND e.`ScheduleID`='".$phaseid."' AND `party_id`!=1180 AND `CONST_TYPE`='PC'
                RIGHT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 1 ORDER BY 2";
           

             $EciPhaseInfoDataCandWiseExcelData = DB::select($EciPhaseInfoDataCandWiseExcel);
            
          

              $arr  = array();

        $TotalNomination = 0; 
        $TotalNational = 0;
        $TotalState = 0;
        $TotalOther= 0;
        $TotalIndependent = 0;
        $TotalMale = 0;
        $TotalFemale = 0;
        $TotalOthers = 0;
        $TotalValidNomination=0;
            
              $user = Auth::user();
              foreach ($EciPhaseInfoDataCandWiseExcelData as $EciPhaseInfoDataCandWise) {
                 
                 if($EciPhaseInfoDataCandWise->ST_NAME ==''){
                   
                    $EciPhaseInfoDataCandWise->ST_NAME = '0';

                 }

                 if($EciPhaseInfoDataCandWise->TOTAL_NOMINATION ==''){
                   
                    $EciPhaseInfoDataCandWise->TOTAL_NOMINATION = '0';

                 }

                 if($EciPhaseInfoDataCandWise->NATIONAL ==''){
                   
                    $EciPhaseInfoDataCandWise->NATIONAL = '0';

                 }

                 if($EciPhaseInfoDataCandWise->STATE ==''){
                   
                    $EciPhaseInfoDataCandWise->STATE = '0';

                 }

                 if($EciPhaseInfoDataCandWise->OTHER ==''){
                   
                    $EciPhaseInfoDataCandWise->OTHER = '0';

                 }

                 if($EciPhaseInfoDataCandWise->INDEPENDENT ==''){
                   
                    $EciPhaseInfoDataCandWise->INDEPENDENT = '0';

                 }

                 if($EciPhaseInfoDataCandWise->male ==''){
                   
                    $EciPhaseInfoDataCandWise->male = '0';

                 }

                 if($EciPhaseInfoDataCandWise->female ==''){
                   
                    $EciPhaseInfoDataCandWise->female = '0';

                 }

                 if($EciPhaseInfoDataCandWise->others ==''){
                   
                    $EciPhaseInfoDataCandWise->others = '0';

                 }

                 if($EciPhaseInfoDataCandWise->total ==''){
                   
                    $EciPhaseInfoDataCandWise->total = '0';

                 }


                 $data =  array(

                         $EciPhaseInfoDataCandWise->ST_NAME,
                         $EciPhaseInfoDataCandWise->TOTAL_NOMINATION,
                         $EciPhaseInfoDataCandWise->NATIONAL,
                         $EciPhaseInfoDataCandWise->STATE,
                         $EciPhaseInfoDataCandWise->OTHER,
                         $EciPhaseInfoDataCandWise->INDEPENDENT,
                         $EciPhaseInfoDataCandWise->male,
                         $EciPhaseInfoDataCandWise->female,
                         $EciPhaseInfoDataCandWise->others,
                         $EciPhaseInfoDataCandWise->total,
                          
                          );

                $TotalNomination             +=   $EciPhaseInfoDataCandWise->TOTAL_NOMINATION;
                $TotalNational               +=   $EciPhaseInfoDataCandWise->NATIONAL;
                $TotalState                  +=   $EciPhaseInfoDataCandWise->STATE;
                $TotalOther                  +=   $EciPhaseInfoDataCandWise->OTHER;
                $TotalIndependent            +=   $EciPhaseInfoDataCandWise->INDEPENDENT;
                $TotalMale                   +=   $EciPhaseInfoDataCandWise->male;
                $TotalFemale                 +=   $EciPhaseInfoDataCandWise->female;
                $TotalOthers                 +=   $EciPhaseInfoDataCandWise->others;
                $TotalValidNomination        +=   $EciPhaseInfoDataCandWise->total;

                          array_push($arr, $data);
                           // }
                          }
                $totalvalues = array('Total',$TotalNomination,$TotalNational,$TotalState,$TotalOther,$TotalIndependent,$TotalMale,$TotalFemale,$TotalOthers,$TotalValidNomination);
                // print_r($totalvalues);die;
                  array_push($arr,$totalvalues);
              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'State', 'Total Nominations Filed', 'National Parties', 'State Parties',
                               'Other Parties','Independent ','Male','Female','Others','Total Valid Nominations',
                       )

                   );

                 });

            })->export('xls');    

             
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC PHASE CANDIDATE WISE INFO DATA EXCEL REPORT TRY CATCH ENDS HERE
    }
    //PC PHASE CANDIDATE WISE INFO DATA EXCEL REPORT FUNCTION ENDS

     //PC PHASE CANDIDATE WISE INFO DATA PDF REPORT FUNCTION STARTS
    public function EciPhaseInfoDataCandWisePdf(Request $request,$phaseid){ 
      //PCPHASE CANDIDATE WISE INFO DATA PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $phaseid         = base64_decode($xss->clean_input($request['phaseid']));

             //PHASE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

             $EciPhaseInfoDataCandWiseData = "SELECT s.`ST_NAME`,s.`ST_CODE`,COUNT(IF(application_status!=11,c.`candidate_id`,NULL)) TOTAL_NOMINATION, COUNT(IF(`cand_party_type`='N' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) NATIONAL, COUNT(IF(`cand_party_type`='S' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) STATE, COUNT(IF(`cand_party_type` IN ('U','0') AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) OTHER, COUNT(IF(`cand_party_type`='Z' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) INDEPENDENT, COUNT(IF(`cand_gender`='male' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) male, COUNT(IF(`cand_gender`='female' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) female,COUNT(IF(`cand_gender`='third' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) others, 
              COUNT(IF(`cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) total
                FROM candidate_nomination_detail c
                JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id`
                JOIN `m_election_details` e ON e.ST_CODE=c.st_code AND c.pc_no=e.`CONST_NO` AND e.`ScheduleID`='".$phaseid."' AND `party_id`!=1180 AND `CONST_TYPE`='PC'
                RIGHT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 1 ORDER BY 2";
           

             $EciPhaseInfoDataCandWisePdf = DB::select($EciPhaseInfoDataCandWiseData);

             //PHASE DATES
            if($phaseid != ''){

              $PhaseInfo = getschedulebyid($phaseid);
            }else{ $PhaseInfo = "";}

             $pdf = PDF::loadView('admin.pc.eci.EciPhaseInfoDataCandWisePdf',['user_data' => $user_data,'EciPhaseInfoDataCandWisePdf' =>$EciPhaseInfoDataCandWisePdf,'phaseid'=>$phaseid,'PhaseInfo'=>$PhaseInfo]);
                        return $pdf->download('EciPhaseInfoDataCandWisePdf'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.pc.eci.EciPhaseInfoDataCandWisePdf');  
                   
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC PHASE CANDIDATE WISE INFO DATA PDF REPORT TRY CATCH ENDS HERE
    }
    //PC PHASE CANDIDATE WISE INFO DATA PDF REPORT FUNCTION ENDS


    //PC ECI NOMINATION FINALIZED REPORT FUNCTION STARTS
    public function EciNominationFinalized(Request $request){ 
      //PC ECI NOMINATION FINALIZED REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            //SETTING SCHEDULE LIST IN SESSION FOR FILTER STARTS
            $GetAllElectionSchedule = $this->GetAllElectionSchedule();
            Session::put('ScheduleList', $GetAllElectionSchedule);
            //SETTING SCHEDULE LIST IN SESSION FOR FILTER ENDS
            //dd($GetAllElectionSchedule);


           $EciNominationFinalizedSelect = "SELECT e.ScheduleID AS sid,COUNT(e.CONST_NO) AS total_pc,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_pc' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code = e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='PC' GROUP BY e.ScheduleID";

            
             $EciNominationFinalized = DB::select($EciNominationFinalizedSelect);
                  
            //dd($EciNominationFinalized);      

             return view('admin.pc.eci.EciNominationFinalized',['user_data' => $user_data,'EciNominationFinalized' =>$EciNominationFinalized]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PCNOMINATION FINALIZED REPORT TRY CATCH ENDS HERE
    }
    //PC NOMINATION FINALIZED REPORT FUNCTION ENDS


    //PC NOMINATION FINALIZED REPORT EXCEL REPORT STARTS
    public function EciNominationFinalizedExcel(Request $request){  
      //PC NOMINATION FINALIZED REPORT EXCEL REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
             
              $EciNominationFinalizedExcelSelect = "SELECT e.ScheduleID AS sid,COUNT(e.CONST_NO) AS total_pc,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_pc' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code = e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='PC' GROUP BY e.ScheduleID";

            
              $EciNominationFinalizedExcelData = DB::select($EciNominationFinalizedExcelSelect);
             //dd($EciActiveUsers);  
 
               $arr  = array();
               $TotalPc = 0; 
               $TotalFinalized = 0;
             
               $user = Auth::user();
               $export_data[] = ['Phase No.', 'No of Total PCs', 'Finalized PCs'];
               $headings[]=[];
               foreach ($EciNominationFinalizedExcelData as $FinalizedData) {
                 
                  if($FinalizedData->sid ==''){
                    
                     $FinalizedData->sid = '0';
 
                  }
 
                  if($FinalizedData->total_pc ==''){
                    
                     $FinalizedData->total_pc = '0';
 
                  }
 
                  if($FinalizedData->finalized_pc ==''){
                    
                     $FinalizedData->finalized_pc = '0';
 
                  }
 
                  $export_data[] = [
                    $FinalizedData->sid,
                    $FinalizedData->total_pc,
                    $FinalizedData->finalized_pc,
                   ];

                  
 
                 $TotalPc        += $FinalizedData->total_pc;
                 $TotalFinalized += $FinalizedData->finalized_pc;
 
                           
                           }


              $name_excel = 'EciNominationFinalizedExcel'.'_'.$cur_time;
              return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');  


            //   \Excel::create('EciNominationFinalizedExcel'.'_'.$cur_time, function($excel)  { 
            //   $excel->sheet('Sheet1', function($sheet)  {

            //   $EciNominationFinalizedExcelSelect = "SELECT e.ScheduleID AS sid,COUNT(e.CONST_NO) AS total_pc,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_pc' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code = e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='PC' GROUP BY e.ScheduleID";

            
            //  $EciNominationFinalizedExcelData = DB::select($EciNominationFinalizedExcelSelect);
            // //dd($EciActiveUsers);  

            //   $arr  = array();
            //   $TotalPc = 0; 
            //   $TotalFinalized = 0;
            
            //   $user = Auth::user();
            //   foreach ($EciNominationFinalizedExcelData as $FinalizedData) {
                
            //      if($FinalizedData->sid ==''){
                   
            //         $FinalizedData->sid = '0';

            //      }

            //      if($FinalizedData->total_pc ==''){
                   
            //         $FinalizedData->total_pc = '0';

            //      }

            //      if($FinalizedData->finalized_pc ==''){
                   
            //         $FinalizedData->finalized_pc = '0';

            //      }

            //      $data =  array(
            //               $FinalizedData->sid,
            //               $FinalizedData->total_pc,
            //               $FinalizedData->finalized_pc,
            //                     ); 

            //     $TotalPc        += $FinalizedData->total_pc;
            //     $TotalFinalized += $FinalizedData->finalized_pc;

            //               array_push($arr, $data);
            //                // }
            //               }

            //     $totalvalues = array('Total',$TotalPc,$TotalFinalized);
            //     // print_r($totalvalues);die;
            //       array_push($arr,$totalvalues);

            //   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'Phase No.', 'No of Total PCs', 'Finalized PCs'
            //                  )

            //        );

            //      });

            // })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //PC NOMINATION FINALIZED REPORT EXCEL REPORT TRY CATCH BLOCK ENDS
        
    }
    //PC NOMINATION FINALIZED REPORT EXCEL REPORT FUNCTION ENDS

    //PC ECI NOMINATION FINALIZED PDF REPORT FUNCTION STARTS
    public function EciNominationFinalizedPdf(Request $request){ 
      //PC ECI NOMINATION FINALIZED PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

           $EciNominationFinalizedSelect = "SELECT e.ScheduleID AS sid,COUNT(e.CONST_NO) AS total_pc,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_pc' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code = e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='PC' GROUP BY e.ScheduleID";

            
             $EciNominationFinalizedPdf = DB::select($EciNominationFinalizedSelect);
                  
            //dd($EciNominationFinalizedPdf);      

             $pdf = PDF::loadView('admin.pc.eci.EciNominationFinalizedPdf',['user_data' => $user_data,'EciNominationFinalizedPdf' =>$EciNominationFinalizedPdf]);
            return $pdf->download('EciNominationFinalizedPdf'.trim($st_name).'_Today_'.$cur_time.'.pdf');
            return view('admin.pc.eci.EciNominationFinalizedPdf');     
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC NOMINATION FINALIZED PDF REPORT TRY CATCH ENDS HERE
    }
    //PC NOMINATION FINALIZED PDF REPORT FUNCTION ENDS


    //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID FORMFUNCTION STARTS
    public function EciNominationFinalizedByPhaseIdForm(Request $request){ 
      //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID FORM TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $validator = Validator::make($request->all(), [ 
                    'phaseid'   => 'nullable|numeric|regex:/^\S*$/u',
                ]);

            if ($validator->fails()) {
                   return Redirect::back()
                   ->withErrors($validator)
                   ->withInput();          
                }

            $xss = new xssClean;

            $phaseid        = $xss->clean_input($request['phaseid']);
         
            
            if (!$phaseid) {
                 $phaseid = "";
            }else{
                $phaseid = $phaseid;
            }


            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
                  
        
             return redirect('/eci/EciNominationFinalizedByPhaseId/'.base64_encode($phaseid));         
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID FORM TRY CATCH ENDS HERE
    }
    //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID FORM FUNCTION ENDS


    //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID REPORT FUNCTION STARTS
    public function EciNominationFinalizedByPhaseId(Request $request,$phaseid){ 
      //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            $phaseid         = base64_decode($xss->clean_input($request['phaseid']));

            //STATE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }


           $EciNominationFinalizedByPhaseIdSelect = "SELECT s.ST_CODE,s.ST_NAME AS state_name,e.ScheduleID AS sid,COUNT(e.CONST_NO) AS total_pc,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_pc' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code =  e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_state s ON e.st_code =  s.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='PC' AND e.ScheduleID='".$phaseid."' GROUP BY s.ST_CODE";

            
             $EciNominationFinalizedByPhaseId = DB::select($EciNominationFinalizedByPhaseIdSelect);
                  
            //dd($EciNominationFinalizedByPhaseId);      

             return view('admin.pc.eci.EciNominationFinalizedByPhaseId',['user_data' => $user_data,'EciNominationFinalizedByPhaseId' =>$EciNominationFinalizedByPhaseId,'phaseid' => $phaseid]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID REPORT TRY CATCH ENDS HERE
    }
    //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID REPORT FUNCTION ENDS


    //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID EXCEL REPORT STARTS
    public function EciNominationFinalizedByPhaseIdExcel(Request $request,$phaseid){  
      //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID EXCEL REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
              
              $xss = new xssClean;
              $phaseid         = base64_decode($xss->clean_input($request['phaseid']));

              //STATE CODE
              if (!$phaseid) {
                   $phaseid = NULL;
              }else{
                  $phaseid = $phaseid;
              }

              $phaseid = Session::put('phaseid',$phaseid); 
               
              $export_data[] = ['State Name', 'No of Total PCs', 'Finalized PCs'];
              $headings[]=[];
              
              $phaseid = Session::get('phaseid');

              $EciNominationFinalizedByPhaseIdExcelSelect = "SELECT s.ST_CODE,s.ST_NAME AS state_name,e.ScheduleID AS sid,COUNT(e.CONST_NO) AS total_pc,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_pc' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code =  e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_state s ON e.st_code =  s.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='PC' AND e.ScheduleID='".$phaseid."' GROUP BY s.ST_CODE";

            
             $EciNominationFinalizedByPhaseIdExcelData = DB::select($EciNominationFinalizedByPhaseIdExcelSelect);
            //dd($EciActiveUsers);  

              $arr  = array();
              $TotalPc = 0; 
              $TotalFinalized = 0;
            
              $user = Auth::user();
              foreach ($EciNominationFinalizedByPhaseIdExcelData as $FinalizedData) {
                
                 if($FinalizedData->state_name ==''){
                   
                    $FinalizedData->state_name = '0';

                 }

                 if($FinalizedData->total_pc ==''){
                   
                    $FinalizedData->total_pc = '0';

                 }

                 if($FinalizedData->finalized_pc ==''){
                   
                    $FinalizedData->finalized_pc = '0';

                 }

                 $export_data[] = [
                  $FinalizedData->state_name,
                  $FinalizedData->total_pc,
                  $FinalizedData->finalized_pc,
                 ];

                  

                $TotalPc        += $FinalizedData->total_pc;
                $TotalFinalized += $FinalizedData->finalized_pc;

                          }


              $name_excel = 'EciNominationFinalizedByPhaseIdExcel'.'_'.$cur_time;
              return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 

              

            //   \Excel::create('EciNominationFinalizedByPhaseIdExcel'.'_'.$cur_time, function($excel)  { 
            //   $excel->sheet('Sheet1', function($sheet)  {

            //     $phaseid = Session::get('phaseid');

            //   $EciNominationFinalizedByPhaseIdExcelSelect = "SELECT s.ST_CODE,s.ST_NAME AS state_name,e.ScheduleID AS sid,COUNT(e.CONST_NO) AS total_pc,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_pc' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code =  e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_state s ON e.st_code =  s.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='PC' AND e.ScheduleID='".$phaseid."' GROUP BY s.ST_CODE";

            
            //  $EciNominationFinalizedByPhaseIdExcelData = DB::select($EciNominationFinalizedByPhaseIdExcelSelect);
            // //dd($EciActiveUsers);  

            //   $arr  = array();
            //   $TotalPc = 0; 
            //   $TotalFinalized = 0;
            
            //   $user = Auth::user();
            //   foreach ($EciNominationFinalizedByPhaseIdExcelData as $FinalizedData) {
                
            //      if($FinalizedData->state_name ==''){
                   
            //         $FinalizedData->state_name = '0';

            //      }

            //      if($FinalizedData->total_pc ==''){
                   
            //         $FinalizedData->total_pc = '0';

            //      }

            //      if($FinalizedData->finalized_pc ==''){
                   
            //         $FinalizedData->finalized_pc = '0';

            //      }

            //      $data =  array(
            //               $FinalizedData->state_name,
            //               $FinalizedData->total_pc,
            //               $FinalizedData->finalized_pc,
            //                     ); 

            //     $TotalPc        += $FinalizedData->total_pc;
            //     $TotalFinalized += $FinalizedData->finalized_pc;

            //               array_push($arr, $data);
            //                // }
            //               }

            //     $totalvalues = array('Total',$TotalPc,$TotalFinalized);
            //     // print_r($totalvalues);die;
            //       array_push($arr,$totalvalues);

            //   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'State Name', 'No of Total PCs', 'Finalized PCs'
            //                  )

            //        );

            //      });

            // })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID EXCEL REPORT TRY CATCH BLOCK ENDS
        
    }
    //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID EXCEL REPORT FUNCTION ENDS


     //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID PDF REPORT FUNCTION STARTS
    public function EciNominationFinalizedByPhaseIdPdf(Request $request,$phaseid){ 
      //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            $phaseid         = base64_decode($xss->clean_input($request['phaseid']));

            //STATE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }


           $EciNominationFinalizedByPhaseIdSelect = "SELECT s.ST_CODE,s.ST_NAME AS state_name,e.ScheduleID AS sid,COUNT(e.CONST_NO) AS total_pc,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_pc' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code =  e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_state s ON e.st_code =  s.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='PC' AND e.ScheduleID='".$phaseid."' GROUP BY s.ST_CODE";

            
             $EciNominationFinalizedByPhaseIdPdf = DB::select($EciNominationFinalizedByPhaseIdSelect);
                  
            //dd($EciNominationFinalizedByPhaseId); 

            //PHASE DATES
            if($phaseid != ''){

              $PhaseInfo = getschedulebyid($phaseid);
            }else{ $PhaseInfo = "";}     

             $pdf = PDF::loadView('admin.pc.eci.EciNominationFinalizedByPhaseIdPdf',['user_data' => $user_data,'EciNominationFinalizedByPhaseIdPdf' =>$EciNominationFinalizedByPhaseIdPdf,'phaseid' => $phaseid,'PhaseInfo'=>$PhaseInfo]);
                        return $pdf->download('EciNominationFinalizedByPhaseIdPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.pc.eci.EciNominationFinalizedByPhaseIdPdf');  
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID PDF REPORT TRY CATCH ENDS HERE
    }
    //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID PDF REPORT FUNCTION ENDS


    //ECI PC NOMINATION FINALIZED PC DATA BY PHASE ID AND STATE CODE REPORT FUNCTION STARTS
    public function EciNominationFinalizedByStatePhaseId(Request $request,$phaseid,$statecode){ 
      //ECI PC NOMINATION FINALIZED PC DATA BY PHASE ID AND STATE CODE REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            $phaseid         = base64_decode($xss->clean_input($request['phaseid']));
            $statecode         = base64_decode($xss->clean_input($request['statecode']));

            //PHASE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }
            //STATE CODE
            if (!$statecode) {
                 $statecode = NULL;
            }else{
                $statecode = $statecode;
            }


           $EciNominationFinalizedByPhaseIdState = "SELECT p.PC_NO,p.PC_NAME,s.ST_CODE,s.ST_NAME AS state_name,e.ScheduleID AS sid,IF(c.finalized_ac='1','Yes','No') AS finalized_pc FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code =  e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_state s ON e.st_code =  s.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_pc p ON e.st_code =  p.ST_CODE AND p.PC_NO=e.CONST_NO  WHERE c.CONST_TYPE='PC' AND e.ScheduleID='".$phaseid."' AND s.ST_CODE='".$statecode."' ORDER BY p.PC_NO";

            
             $EciNominationFinalizedByStatePhaseId = DB::select($EciNominationFinalizedByPhaseIdState);
                  
            //dd($EciNominationFinalizedByStatePhaseId);      

             return view('admin.pc.eci.EciNominationFinalizedByStatePhaseId',['user_data' => $user_data,'EciNominationFinalizedByStatePhaseId' =>$EciNominationFinalizedByStatePhaseId,'phaseid' => $phaseid,'statecode' => $statecode]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //ECI PC NOMINATION FINALIZED PC DATA BY PHASE ID AND STATE CODE REPORT TRY CATCH ENDS HERE
    }
    //ECI PC NOMINATION FINALIZED PC DATA BY PHASE ID AND STATE CODE REPORT FUNCTION ENDS


     //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID EXCEL REPORT STARTS
    public function EciNominationFinalizedByStatePhaseIdExcel(Request $request,$phaseid,$statecode){  
      //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID EXCEL REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
              
              $xss = new xssClean;

              $phaseid         = base64_decode($xss->clean_input($request['phaseid']));
              $statecode         = base64_decode($xss->clean_input($request['statecode']));

              //PHASE CODE
              if (!$phaseid) {
                   $phaseid = NULL;
              }else{
                  $phaseid = $phaseid;
              }
              //STATE CODE
              if (!$statecode) {
                   $statecode = NULL;
              }else{
                  $statecode = $statecode;
              }

              $phaseid = Session::put('phaseid',$phaseid); 
              $statecode = Session::put('statecode',$statecode); 
               
              $phaseid = Session::get('phaseid');
                $statecode = Session::get('statecode');

              $EciNominationFinalizedByStatePhaseIdExcelSelect = "SELECT p.PC_NO,p.PC_NAME,s.ST_CODE,s.ST_NAME AS state_name,e.ScheduleID AS sid,IF(c.finalized_ac='1','Yes','No') AS finalized_pc FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code =  e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_state s ON e.st_code =  s.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_pc p ON e.st_code =  p.ST_CODE AND p.PC_NO=e.CONST_NO  WHERE c.CONST_TYPE='PC' AND e.ScheduleID='".$phaseid."' AND s.ST_CODE='".$statecode."' ORDER BY p.PC_NO";

            
             $EciNominationFinalizedByStatePhaseIdExcelData = DB::select($EciNominationFinalizedByStatePhaseIdExcelSelect);
            //dd($EciActiveUsers);  

              $arr  = array();
            
              $user = Auth::user();
              $export_data[] = ['PC No', 'No of Total PCs', 'Finalized PCs'];
              $headings[]=[];

              foreach ($EciNominationFinalizedByStatePhaseIdExcelData as $FinalizedData) {
                
                 if($FinalizedData->PC_NO ==''){
                   
                    $FinalizedData->PC_NO = '0';

                 }

                 if($FinalizedData->PC_NAME ==''){
                   
                    $FinalizedData->PC_NAME = '0';

                 }

                 if($FinalizedData->finalized_pc ==''){
                   
                    $FinalizedData->finalized_pc = '0';

                 }

                 $export_data[] = [
                  $FinalizedData->PC_NO,
                  $FinalizedData->PC_NAME,
                  $FinalizedData->finalized_pc,
                 ];

                 
                          }

              $name_excel = 'EciNominationFinalizedByPhaseIdExcel'.'_'.$cur_time;
              return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


            //   \Excel::create('EciNominationFinalizedByPhaseIdExcel'.'_'.$cur_time, function($excel)  { 
            //   $excel->sheet('Sheet1', function($sheet)  {

            //     $phaseid = Session::get('phaseid');
            //     $statecode = Session::get('statecode');

            //   $EciNominationFinalizedByStatePhaseIdExcelSelect = "SELECT p.PC_NO,p.PC_NAME,s.ST_CODE,s.ST_NAME AS state_name,e.ScheduleID AS sid,IF(c.finalized_ac='1','Yes','No') AS finalized_pc FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code =  e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_state s ON e.st_code =  s.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_pc p ON e.st_code =  p.ST_CODE AND p.PC_NO=e.CONST_NO  WHERE c.CONST_TYPE='PC' AND e.ScheduleID='".$phaseid."' AND s.ST_CODE='".$statecode."' ORDER BY p.PC_NO";

            
            //  $EciNominationFinalizedByStatePhaseIdExcelData = DB::select($EciNominationFinalizedByStatePhaseIdExcelSelect);
            // //dd($EciActiveUsers);  

            //   $arr  = array();
            
            //   $user = Auth::user();
            //   foreach ($EciNominationFinalizedByStatePhaseIdExcelData as $FinalizedData) {
                
            //      if($FinalizedData->PC_NO ==''){
                   
            //         $FinalizedData->PC_NO = '0';

            //      }

            //      if($FinalizedData->PC_NAME ==''){
                   
            //         $FinalizedData->PC_NAME = '0';

            //      }

            //      if($FinalizedData->finalized_pc ==''){
                   
            //         $FinalizedData->finalized_pc = '0';

            //      }

            //      $data =  array(
            //                     $FinalizedData->PC_NO,
            //                     $FinalizedData->PC_NAME,
            //                     $FinalizedData->finalized_pc,
            //                     ); 
            //               array_push($arr, $data);
            //                // }
            //               }
            //   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'PC No', 'No of Total PCs', 'Finalized PCs'
            //                  )

            //        );

            //      });

            // })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID EXCEL REPORT TRY CATCH BLOCK ENDS
        
    }
    //PC ECI NOMINATION FINALIZED PC DATA BY PHASE ID EXCEL REPORT FUNCTION ENDS


    //ECI PC NOMINATION FINALIZED PC DATA BY PHASE ID AND STATE CODE PDF REPORT FUNCTION STARTS
    public function EciNominationFinalizedByStatePhaseIdPdf(Request $request,$phaseid,$statecode){ 
      //ECI PC NOMINATION FINALIZED PC DATA BY PHASE ID AND STATE CODE PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            $phaseid         = base64_decode($xss->clean_input($request['phaseid']));
            $statecode         = base64_decode($xss->clean_input($request['statecode']));

            //PHASE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }
            //STATE CODE
            if (!$statecode) {
                 $statecode = NULL;
            }else{
                $statecode = $statecode;
            }


           $EciNominationFinalizedByPhaseIdState = "SELECT p.PC_NO,p.PC_NAME,s.ST_CODE,s.ST_NAME AS state_name,e.ScheduleID AS sid,IF(c.finalized_ac='1','Yes','No') AS finalized_pc FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code =  e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_state s ON e.st_code =  s.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_pc p ON e.st_code =  p.ST_CODE AND p.PC_NO=e.CONST_NO  WHERE c.CONST_TYPE='PC' AND e.ScheduleID='".$phaseid."' AND s.ST_CODE='".$statecode."' ORDER BY p.PC_NO";

            
             $EciNominationFinalizedByStatePhaseIdPdf = DB::select($EciNominationFinalizedByPhaseIdState);
                  
            //dd($EciNominationFinalizedByStatePhaseId);  

            //STATE NAME 
            if($statecode != ''){

              $statelist = getstatebystatecode($statecode);
              $state     = $statelist->ST_NAME;

            }else{ $state = "";}

            //PHASE DATES
            if($phaseid != ''){

              $PhaseInfo = getschedulebyid($phaseid);
            }else{ $PhaseInfo = "";}



             $pdf = PDF::loadView('admin.pc.eci.EciNominationFinalizedByStatePhaseIdPdf',['user_data' => $user_data,'EciNominationFinalizedByStatePhaseIdPdf' =>$EciNominationFinalizedByStatePhaseIdPdf,'phaseid' => $phaseid,'state' => $state,'PhaseInfo'=>$PhaseInfo]);
                        return $pdf->download('EciNominationFinalizedByStatePhaseIdPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.pc.eci.EciNominationFinalizedByStatePhaseIdPdf');     
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //ECI PC NOMINATION FINALIZED PC DATA BY PHASE ID AND STATE CODE PDF REPORT TRY CATCH ENDS HERE
    }
    //ECI PC NOMINATION FINALIZED PC DATA BY PHASE ID AND STATE CODE PDF REPORT FUNCTION ENDS


     //ECI PC POLL PERCENT REPORT FUNCTION STARTS
    public function EciPollPercent(Request $request){ 
      //ECI PCPOLL PERCENT REPORT TRY CATCH STARTS HERE
      try{

        $request->merge(['is_excel' => 1]);
        $PercentState = $this->EciPollDayController->index($request);
        //dd($PercentState['results']);



          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

           

            //$EciPollPercent =  $PercentState['results'];
             $EciPollPercent =  $PercentState;
            // dd($PercentState);

             return view('admin.pc.eci.EciPollPercent',['user_data' => $user_data,'EciPollPercent' =>$EciPollPercent]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //ECI PC POLL PERCENT REPORT TRY CATCH ENDS HERE
    }
    //ECI PC POLL PERCENT REPORT FUNCTION ENDS


    //PC ECI PC POLL PERCENT EXCEL REPORT STARTS
    public function EciPollPercentExcel(Request $request){  
      //PC ECI PC POLL PERCENT EXCEL REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
              
              $xss = new xssClean;

               
               $request->merge(['is_excel' => 1]);
               $PercentStateExcel = $this->EciPollDayController->index($request); 

              $PercentStateExcel = Session::put('PercentStateExcel',$PercentStateExcel);  

                  

              \Excel::create('EciPollPercentExcel'.'_'.$cur_time, function($excel)   { 
              $excel->sheet('Sheet1', function($sheet)  {

                 $PercentData = Session::get('PercentStateExcel');

                $EciPollPercentData =  $PercentData['results'];
          

              $arr  = array();
            
              $user = Auth::user();
              foreach ($EciPollPercentData as $listdata) {
                
                 if($listdata['label']  ==''){
                   
                    $listdata['label']  = '0';

                 }

                if($listdata['gen_t'] != "" && $listdata['total'] > 0){

                  $TotalPercent =  round($listdata['total']/$listdata['gen_t']*100,2);

                }else{

                    $TotalPercent = '0';

                  } 

                 $data =  array(
                                $listdata['label'],
                                $TotalPercent,
                               
                                ); 
                          array_push($arr, $data);
                           // }
                          }

              
              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'State Name', 'Percentage', ''
                             )

                   );

                 });

            })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //PC ECI PC POLL PERCENT EXCEL REPORT TRY CATCH BLOCK ENDS
        
    }
    //PC ECIPC POLL PERCENT EXCEL REPORT FUNCTION ENDS


    //ECI PC POLL PERCENT PDF REPORT  FUNCTION STARTS
    public function EciPollPercentPdf(Request $request){ 
      //ECI PCPOLL PERCENT PDF REPORT TRY CATCH STARTS HERE
      try{

        $request->merge(['is_excel' => 1]);
        $PercentState = $this->EciPollDayController->index($request);
        //dd($PercentState['results']);



          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

           // dd($PercentState['results']);

            $EciPollPercentPdf =  $PercentState['results'];


           $pdf = PDF::loadView('admin.pc.eci.EciPollPercentPdf',['user_data' => $user_data,'EciPollPercentPdf' =>$EciPollPercentPdf]);
                    return $pdf->download('EciPollPercentPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                    return view('admin.pc.eci.EciPollPercentPdf');    
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //ECI PC POLL PERCENT PDF REPORT TRY CATCH ENDS HERE
    }
    //ECI PC POLL PERCENT PDF REPORT FUNCTION ENDS


    //ECI PC POLL PERCENT STATE WISE REPORT FUNCTION STARTS
    public function EciPollPercentPcWise(Request $request){ 
      //ECI PCPOLL PERCENT STATE WISE REPORT TRY CATCH STARTS HERE
      try{
      
        $request->merge(['is_excel' => 1]);
     
        $PercentState = $this->EciPollDayController->state($request);
                
        $users=Session::get('admin_login_details');
        $user = Auth::user();   
        
        if(session()->has('admin_login')){ 

          $xss = new xssClean;

          $uid=$user->id;

          $user_data=$this->commonModel->getunewserbyuserid($uid);

          $cur_time    = Carbon::now();
          $st_code     = $user_data->st_code;
          $st_name     = $user_data->placename;

          $EciPollPercentPcWise =  $PercentState['results'];
          //dd($EciPollPercentPcWise);

         return view('admin.pc.eci.EciPollPercentPcWise',['user_data' => $user_data,'EciPollPercentPcWise' =>$PercentState]);   
               
        }else {
                return redirect('/admin-login');
            }             

      
      } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //ECI PC POLL PERCENT STATE WISE REPORT TRY CATCH ENDS HERE
    }
    //ECI PC POLL PERCENT STATE WISE REPORT FUNCTION ENDS


    //PC ECI PC POLL PERCENT STATE WISE EXCEL REPORT STARTS
    public function EciPollPercentPcWiseExcel(Request $request){  
      //PC ECI PC POLL PERCENT STATE WISE EXCEL REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
              
              $xss = new xssClean;

               
              $request->merge(['is_excel' => 1]);
     
              $EciPollPercentPcWiseExcel = $this->EciPollDayController->state($request);


              $EciPollPercentPcWiseExcel = Session::put('EciPollPercentPcWiseExcel',$EciPollPercentPcWiseExcel);  

                  

              \Excel::create('EciPollPercentPcWiseExcel'.'_'.$cur_time, function($excel)   { 
              $excel->sheet('Sheet1', function($sheet)  {

                 $PercentData = Session::get('EciPollPercentPcWiseExcel');

                $EciPollPercentData =  $PercentData['results'];
          

              $arr  = array();
            
              $user = Auth::user();
              foreach ($EciPollPercentData as $listdata) {
                
                 if($listdata['label']  ==''){
                   
                    $listdata['label']  = '0';

                 }

                if($listdata['gen_t'] != "" && $listdata['total'] > 0){

                  $TotalPercent =  round($listdata['total']/$listdata['gen_t']*100,2);

                }else{

                    $TotalPercent = '0';

                  } 

                 $data =  array(
                                $listdata['label'],
                                $TotalPercent,
                               
                                ); 
                          array_push($arr, $data);
                           // }
                          }

              
              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               '>Name of Parliamentry Constituency', 'Polling Percent'
                             )

                   );

                 });

            })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //PC ECI PC POLL PERCENT STATE WISE EXCEL REPORT TRY CATCH BLOCK ENDS
        
    }
    //PC ECIPC POLL PERCENT STATE WISE EXCEL REPORT FUNCTION ENDS


    //ECI PC POLL PERCENT STATE WISE PDF REPORT  FUNCTION STARTS
    public function EciPollPercentPcWisePdf(Request $request){ 
      //ECI PCPOLL PERCENT STATE WISE PDF REPORT TRY CATCH STARTS HERE
      try{

        $request->merge(['is_excel' => 1]);
        $PercentState = $this->EciPollDayController->index($request);
        //dd($PercentState['results']);



          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

           // dd($PercentState['results']);

            $EciPollPercentPdf =  $PercentState['results'];


           $pdf = PDF::loadView('admin.pc.eci.EciPollPercentPcWisePdf',['user_data' => $user_data,'EciPollPercentPcWisePdf' =>$EciPollPercentPcWisePdf]);
                    return $pdf->download('EciPollPercentPcWisePdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                    return view('admin.pc.eci.EciPollPercentPcWisePdf');    
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //ECI PC POLL PERCENT STATE WISE PDF REPORT TRY CATCH ENDS HERE
    }
    //ECI PC POLL PERCENT STATE WISE PDF REPORT FUNCTION ENDS


    //ECI PC POLL TURN OUT PC WISE REPORT FUNCTION STARTS
    public function EciPollTurnOutPcWise(Request $request){ 
      //ECI PC POLL TURN OUT PC  WISE REPORT TRY CATCH STARTS HERE
      try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();

              $EciPollTurnOutPcWiseSelectData = "SELECT m.ST_NAME,p.pc_no,q.`PC_NAME`,SUM(`electors_total`) electors_total,SUM(total) AS Latest_total,CONCAT(ROUND(SUM(total)/SUM(electors_total)*100,2),'%') Percent FROM `pd_scheduledetail` p JOIN `electors_cdac` e ON p.st_code=e.st_code AND p.pc_no=e.pc_no AND p.ac_no=e.ac_no JOIN m_state m ON m.ST_CODE=p.st_code JOIN m_pc q ON q.`ST_CODE`=p.st_code AND q.`PC_NO`=e.pc_no GROUP BY 1,2";
            
             $EciPollTurnOutPcWise = DB::select($EciPollTurnOutPcWiseSelectData);

             $cur_time  = Carbon::now();
             $st_code = $user_data->st_code;
             $st_name = $user_data->placename;
              //dd($AllPartyList);

            return view('admin.pc.eci.EciPollTurnOutPcWise',['user_data' => $user_data,'EciPollTurnOutPcWise' => $EciPollTurnOutPcWise]);
                            
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI PC POLL TURN OUT PC  WISE REPORT TRY CATCH ENDS HERE
    }
    //ECI PC POLL TURN OUT PC  WISE REPORT FUNCTION ENDS


    //ECI PC POLL TURN OUT PC WISE EXCEL REPORT STARTS
    public function EciPollTurnOutPcWiseExcel(Request $request){  
      //ECI PC POLL TURN OUT PC WISE EXCEL TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
             
                          

              \Excel::create('EciPollTurnOutPcWiseExcel_'.'_'.$cur_time, function($excel)  { 
              $excel->sheet('Sheet1', function($sheet)  {

              $EciPollTurnOutPcWiseSelectData = "SELECT m.ST_NAME,p.pc_no,q.`PC_NAME`,SUM(`electors_total`) electors_total,SUM(total) AS Latest_total,CONCAT(ROUND(SUM(total)/SUM(electors_total)*100,2),'%') Percent FROM `pd_scheduledetail` p JOIN `electors_cdac` e ON p.st_code=e.st_code AND p.pc_no=e.pc_no AND p.ac_no=e.ac_no JOIN m_state m ON m.ST_CODE=p.st_code JOIN m_pc q ON q.`ST_CODE`=p.st_code AND q.`PC_NO`=e.pc_no GROUP BY 1,2";
            
             $EciPollTurnOutPcWiseExcel = DB::select($EciPollTurnOutPcWiseSelectData);
            //dd($EciActiveUsers);  

              $arr  = array();
            
              $user = Auth::user();
              foreach ($EciPollTurnOutPcWiseExcel as $EciPollTurnOutPcWise) {
                 
                 if($EciPollTurnOutPcWise->ST_NAME ==''){
                   
                    $EciPollTurnOutPcWise->ST_NAME = '0';

                 }

                 if($EciPollTurnOutPcWise->pc_no ==''){
                   
                    $EciPollTurnOutPcWise->pc_no = '0';

                 }

                 if($EciPollTurnOutPcWise->PC_NAME ==''){
                   
                    $EciPollTurnOutPcWise->PC_NAME = '0';

                 }

                 if($EciPollTurnOutPcWise->electors_total ==''){
                   
                    $EciPollTurnOutPcWise->electors_total = '0';

                 }

                 if($EciPollTurnOutPcWise->Latest_total ==''){
                   
                    $EciPollTurnOutPcWise->Latest_total = '0';

                 }

                 if($EciPollTurnOutPcWise->Percent ==''){
                   
                    $EciPollTurnOutPcWise->Percent = '0';

                 }

                 $data =  array(
                          $EciPollTurnOutPcWise->ST_NAME,
                          $EciPollTurnOutPcWise->pc_no,
                          $EciPollTurnOutPcWise->PC_NAME,
                          $EciPollTurnOutPcWise->electors_total,
                          $EciPollTurnOutPcWise->Latest_total,
                          $EciPollTurnOutPcWise->Percent,
                                );


                          array_push($arr, $data);
                           // }
                          }

              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'State Name', 'PC Number', 'PC Name', 'Electors Total','Latest Total','Percent'
                       )

                   );

                 });

            })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECIPC POLL TURN OUT PC WISE EXCEL TRY CATCH BLOCK ENDS
        
    }
    //ECI PC POLL TURN OUT PC WISE EXCEL FUNCTION ENDS



    //ECI AC POLL TURN OUT AC WISE REPORT FUNCTION STARTS
    public function EciPollTurnOutAcWise(Request $request){ 
      //ECI AC POLL TURN OUT AC  WISE REPORT TRY CATCH STARTS HERE
      try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();

              $EciPollTurnOutAcWiseSelectData = "SELECT m.ST_NAME,p.pc_no,q.`PC_NAME`,p.ac_no,e.ac_name,`electors_total`,total AS Latest_total,CONCAT(ROUND(total/`electors_total`*100,2),'%') Percent FROM `pd_scheduledetail` p JOIN `electors_cdac` e ON p.st_code=e.st_code AND p.pc_no=e.pc_no AND p.ac_no=e.ac_no JOIN m_state m ON m.ST_CODE=p.st_code JOIN m_pc q ON q.`ST_CODE`=p.st_code AND q.`PC_NO`=e.pc_no ORDER BY m.ST_NAME,p.pc_no,q.`PC_NAME`,p.ac_no,e.ac_name";
            
             $EciPollTurnOutAcWise = DB::select($EciPollTurnOutAcWiseSelectData);

             $cur_time  = Carbon::now();
             $st_code = $user_data->st_code;
             $st_name = $user_data->placename;
              //dd($AllPartyList);

            return view('admin.pc.eci.EciPollTurnOutAcWise',['user_data' => $user_data,'EciPollTurnOutAcWise' => $EciPollTurnOutAcWise]);
                            
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI AC POLL TURN OUT AC  WISE REPORT TRY CATCH ENDS HERE
    }
    //ECI AC POLL TURN OUT AC  WISE REPORT FUNCTION ENDS



    //ECI PC POLL TURN OUT PC WISE EXCEL REPORT STARTS
    public function EciPollTurnOutAcWiseExcel(Request $request){  
      //ECI PC POLL TURN OUT PC WISE EXCEL TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
             
                          

              \Excel::create('EciPollTurnOutAcWiseExcel_'.'_'.$cur_time, function($excel)  { 
              $excel->sheet('Sheet1', function($sheet)  {

              $EciPollTurnOutAcWiseSelectData = "SELECT m.ST_NAME,p.pc_no,q.`PC_NAME`,p.ac_no,e.ac_name,`electors_total`,total AS Latest_total,CONCAT(ROUND(total/`electors_total`*100,2),'%') Percent FROM `pd_scheduledetail` p JOIN `electors_cdac` e ON p.st_code=e.st_code AND p.pc_no=e.pc_no AND p.ac_no=e.ac_no JOIN m_state m ON m.ST_CODE=p.st_code JOIN m_pc q ON q.`ST_CODE`=p.st_code AND q.`PC_NO`=e.pc_no ORDER BY m.ST_NAME,p.pc_no,q.`PC_NAME`,p.ac_no,e.ac_name";
            
             $EciPollTurnOutAcWiseExcel = DB::select($EciPollTurnOutAcWiseSelectData);
            //dd($EciActiveUsers);  

              $arr  = array();
            
              $user = Auth::user();
              foreach ($EciPollTurnOutAcWiseExcel as $EciPollTurnOutAcWise) {
                 
                 if($EciPollTurnOutAcWise->ST_NAME ==''){
                   
                    $EciPollTurnOutAcWise->ST_NAME = '0';

                 }

                 if($EciPollTurnOutAcWise->pc_no ==''){
                   
                    $EciPollTurnOutAcWise->pc_no = '0';

                 }

                 if($EciPollTurnOutAcWise->PC_NAME ==''){
                   
                    $EciPollTurnOutAcWise->PC_NAME = '0';

                 }

                 if($EciPollTurnOutAcWise->ac_no ==''){
                   
                    $EciPollTurnOutAcWise->ac_no = '0';

                 }

                 if($EciPollTurnOutAcWise->ac_name ==''){
                   
                    $EciPollTurnOutAcWise->ac_name = '0';

                 }

                 if($EciPollTurnOutAcWise->electors_total ==''){
                   
                    $EciPollTurnOutAcWise->electors_total = '0';

                 }

                 if($EciPollTurnOutAcWise->Latest_total ==''){
                   
                    $EciPollTurnOutAcWise->Latest_total = '0';

                 }

                 if($EciPollTurnOutAcWise->Percent ==''){
                   
                    $EciPollTurnOutAcWise->Percent = '0';

                 }

                 $data =  array(
                          $EciPollTurnOutAcWise->ST_NAME,
                          $EciPollTurnOutAcWise->pc_no,
                          $EciPollTurnOutAcWise->PC_NAME,
                          $EciPollTurnOutAcWise->ac_no,
                          $EciPollTurnOutAcWise->ac_name,
                          $EciPollTurnOutAcWise->electors_total,
                          $EciPollTurnOutAcWise->Latest_total,
                          $EciPollTurnOutAcWise->Percent,
                                );


                          array_push($arr, $data);
                           // }
                          }

              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'State Name', 'PC Number', 'PC Name', 'AC Number', 'AC Name', 'Electors Total','Latest Total','Percent'
                       )

                   );

                 });

            })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECIPC POLL TURN OUT PC WISE EXCEL TRY CATCH BLOCK ENDS
        
    }
    //ECI PC POLL TURN OUT PC WISE EXCEL FUNCTION ENDS



    //ECI AC POLL TURN OUT ROUND REPORT AC WISE REPORT FUNCTION STARTS
    public function EciCompPollRoundReport(Request $request){ 
      //ECI AC POLL TURN OUT ROUND REPORT AC  WISE REPORT TRY CATCH STARTS HERE
      try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();

              $EciPollTurnRoundWiseAc = "SELECT m.ST_NAME,p.pc_no,q.`PC_NAME`,p.ac_no,e.ac_name,`round1_voter_male`,round1_voter_male, round1_voter_female,round1_voter_other ,round1_voter_total ,round2_voter_male, round2_voter_female,round2_voter_other ,round2_voter_total ,round3_voter_male, round3_voter_female,round3_voter_other ,round3_voter_total ,round4_voter_male, round4_voter_female,round4_voter_other ,round4_voter_total ,round5_voter_male, round5_voter_female,round5_voter_other ,round5_voter_total ,end_voter_male, end_voter_female   ,end_voter_other,end_voter_total,total_male,total_female,total_other,total AS Latest_total,`electors_male`,`electors_female`,`electors_other`,`electors_total`,CONCAT(ROUND(total/`electors_total`*100,2),'%') Percent FROM `pd_scheduledetail` p JOIN `electors_cdac` e ON p.st_code=e.st_code AND p.pc_no=e.pc_no AND p.ac_no=e.ac_no JOIN m_state m ON m.ST_CODE=p.st_code JOIN m_pc q ON q.`ST_CODE`=p.st_code AND q.`PC_NO`=e.pc_no ORDER BY 1,2,3,4";
            
             $EciPollTurnRoundWiseAcReport = DB::select($EciPollTurnRoundWiseAc);

             $cur_time  = Carbon::now();
             $st_code = $user_data->st_code;
             $st_name = $user_data->placename;
              //dd($AllPartyList);

            return view('admin.pc.eci.EciPollTurnRoundWiseAcReport',['user_data' => $user_data,'EciPollTurnRoundWiseAcReport' => $EciPollTurnRoundWiseAcReport]);
                            
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI AC POLL TURN OUT ROUND REPORT AC  WISE REPORT TRY CATCH ENDS HERE
    }
    //ECI AC POLL TURN OUT ROUND REPORT AC  WISE REPORT FUNCTION ENDS


    //ECI  AC POLL TURN OUT ROUND REPORT AC EXCEL REPORT STARTS
    public function EciCompPollRoundReportExcel(Request $request){  
      //ECI  AC POLL TURN OUT ROUND REPORT AC EXCEL TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
             
                          

              \Excel::create('EciCompPollRoundReportExcel_'.'_'.$cur_time, function($excel)  { 
              $excel->sheet('Sheet1', function($sheet)  {

              $EciCompPollRoundReportExcelData = "SELECT m.ST_NAME,p.pc_no,q.`PC_NAME`,p.ac_no,e.ac_name,round1_voter_male  ,round1_voter_female,round1_voter_other ,round1_voter_total ,round2_voter_male  ,round2_voter_female,round2_voter_other ,round2_voter_total ,round3_voter_male  ,round3_voter_female,round3_voter_other ,round3_voter_total ,round4_voter_male  ,round4_voter_female,round4_voter_other ,round4_voter_total ,round5_voter_male  ,round5_voter_female,round5_voter_other ,round5_voter_total ,end_voter_male     ,end_voter_female   ,end_voter_other,end_voter_total,total_male         ,total_female       ,total_other,total AS Latest_total,`electors_male`,`electors_female`,`electors_other`,`electors_total`,CONCAT(ROUND(total/`electors_total`*100,2),'%') Percent FROM `pd_scheduledetail` p JOIN `electors_cdac` e ON p.st_code=e.st_code AND p.pc_no=e.pc_no AND p.ac_no=e.ac_no JOIN m_state m ON m.ST_CODE=p.st_code JOIN m_pc q ON q.`ST_CODE`=p.st_code AND q.`PC_NO`=e.pc_no ORDER BY 1,2,3,4";
            
             $EciCompPollRoundReportExcel = DB::select($EciCompPollRoundReportExcelData);
            //dd($EciActiveUsers);  

              $arr  = array();
            
              $user = Auth::user();
              foreach ($EciCompPollRoundReportExcel as $EciPollTurnOutRoundWise) {
                 
                 if($EciPollTurnOutRoundWise->ST_NAME ==''){
                   
                    $EciPollTurnOutRoundWise->ST_NAME = '0';

                 }

                 if($EciPollTurnOutRoundWise->pc_no ==''){
                   
                    $EciPollTurnOutRoundWise->pc_no = '0';

                 }

                 if($EciPollTurnOutRoundWise->PC_NAME ==''){
                   
                    $EciPollTurnOutRoundWise->PC_NAME = '0';

                 }

                 if($EciPollTurnOutRoundWise->ac_no ==''){
                   
                    $EciPollTurnOutRoundWise->ac_no = '0';

                 }

                 if($EciPollTurnOutRoundWise->ac_name ==''){
                   
                    $EciPollTurnOutRoundWise->ac_name = '0';

                 }

                 if($EciPollTurnOutRoundWise->round1_voter_male ==''){
                   
                    $EciPollTurnOutRoundWise->round1_voter_male = '0';

                 }

                 if($EciPollTurnOutRoundWise->round1_voter_female ==''){
                   
                    $EciPollTurnOutRoundWise->round1_voter_female = '0';

                 }

                 if($EciPollTurnOutRoundWise->round1_voter_other ==''){
                   
                    $EciPollTurnOutRoundWise->round1_voter_other = '0';

                 }

                 if($EciPollTurnOutRoundWise->round1_voter_total ==''){
                   
                    $EciPollTurnOutRoundWise->round1_voter_total = '0';

                 }

                 if($EciPollTurnOutRoundWise->round2_voter_male ==''){
                   
                    $EciPollTurnOutRoundWise->round2_voter_male = '0';

                 }

                 if($EciPollTurnOutRoundWise->round2_voter_female ==''){
                   
                    $EciPollTurnOutRoundWise->round2_voter_female = '0';

                 }

                 if($EciPollTurnOutRoundWise->round2_voter_other ==''){
                   
                    $EciPollTurnOutRoundWise->round2_voter_other = '0';

                 }

                 if($EciPollTurnOutRoundWise->round2_voter_total ==''){
                   
                    $EciPollTurnOutRoundWise->round2_voter_total = '0';

                 }


                 if($EciPollTurnOutRoundWise->round3_voter_male ==''){
                   
                    $EciPollTurnOutRoundWise->round3_voter_male = '0';

                 }

                 if($EciPollTurnOutRoundWise->round3_voter_female ==''){
                   
                    $EciPollTurnOutRoundWise->round3_voter_female = '0';

                 }

                 if($EciPollTurnOutRoundWise->round3_voter_other ==''){
                   
                    $EciPollTurnOutRoundWise->round3_voter_other = '0';

                 }

                 if($EciPollTurnOutRoundWise->round3_voter_total ==''){
                   
                    $EciPollTurnOutRoundWise->round3_voter_total = '0';

                 }


                 if($EciPollTurnOutRoundWise->round4_voter_male ==''){
                   
                    $EciPollTurnOutRoundWise->round4_voter_male = '0';

                 }

                 if($EciPollTurnOutRoundWise->round4_voter_female ==''){
                   
                    $EciPollTurnOutRoundWise->round4_voter_female = '0';

                 }

                 if($EciPollTurnOutRoundWise->round4_voter_other ==''){
                   
                    $EciPollTurnOutRoundWise->round4_voter_other = '0';

                 }

                 if($EciPollTurnOutRoundWise->round4_voter_total ==''){
                   
                    $EciPollTurnOutRoundWise->round4_voter_total = '0';

                 }


                 if($EciPollTurnOutRoundWise->round5_voter_male ==''){
                   
                    $EciPollTurnOutRoundWise->round5_voter_male = '0';

                 }

                 if($EciPollTurnOutRoundWise->round5_voter_female ==''){
                   
                    $EciPollTurnOutRoundWise->round5_voter_female = '0';

                 }

                 if($EciPollTurnOutRoundWise->round5_voter_other ==''){
                   
                    $EciPollTurnOutRoundWise->round5_voter_other = '0';

                 }

                 if($EciPollTurnOutRoundWise->round5_voter_total ==''){
                   
                    $EciPollTurnOutRoundWise->round5_voter_total = '0';

                 }

                 if($EciPollTurnOutRoundWise->end_voter_male ==''){
                   
                    $EciPollTurnOutRoundWise->end_voter_male = '0';

                 }

                 if($EciPollTurnOutRoundWise->end_voter_female ==''){
                   
                    $EciPollTurnOutRoundWise->end_voter_female = '0';

                 }

                 if($EciPollTurnOutRoundWise->end_voter_other ==''){
                   
                    $EciPollTurnOutRoundWise->end_voter_other = '0';

                 }

                 if($EciPollTurnOutRoundWise->end_voter_total ==''){
                   
                    $EciPollTurnOutRoundWise->end_voter_total = '0';

                 }

                 if($EciPollTurnOutRoundWise->total_male ==''){
                   
                    $EciPollTurnOutRoundWise->total_male = '0';

                 }


                 if($EciPollTurnOutRoundWise->total_female ==''){
                   
                    $EciPollTurnOutRoundWise->total_female = '0';

                 }

                 if($EciPollTurnOutRoundWise->total_other ==''){
                   
                    $EciPollTurnOutRoundWise->total_other = '0';

                 }


                if($EciPollTurnOutRoundWise->Latest_total ==''){
                   
                    $EciPollTurnOutRoundWise->Latest_total = '0';

                 }

                 if($EciPollTurnOutRoundWise->electors_total ==''){
                   
                    $EciPollTurnOutRoundWise->electors_total = '0';

                 }

                

                 if($EciPollTurnOutRoundWise->Percent ==''){
                   
                    $EciPollTurnOutRoundWise->Percent = '0';

                 }

                 $data =  array(
                          $EciPollTurnOutRoundWise->ST_NAME,
                          $EciPollTurnOutRoundWise->pc_no,
                          $EciPollTurnOutRoundWise->PC_NAME,
                          $EciPollTurnOutRoundWise->ac_no,
                          $EciPollTurnOutRoundWise->ac_name,
                          $EciPollTurnOutRoundWise->round1_voter_male,
                          $EciPollTurnOutRoundWise->round1_voter_female, 
                          $EciPollTurnOutRoundWise->round1_voter_other ,
                          $EciPollTurnOutRoundWise->round1_voter_total ,
                          $EciPollTurnOutRoundWise->round2_voter_male ,
                          $EciPollTurnOutRoundWise->round2_voter_female ,
                          $EciPollTurnOutRoundWise->round2_voter_other ,
                          $EciPollTurnOutRoundWise->round2_voter_total ,
                          $EciPollTurnOutRoundWise->round3_voter_male ,
                          $EciPollTurnOutRoundWise->round3_voter_female ,
                          $EciPollTurnOutRoundWise->round3_voter_other ,
                          $EciPollTurnOutRoundWise->round3_voter_total ,
                          $EciPollTurnOutRoundWise->round4_voter_male,
                          $EciPollTurnOutRoundWise->round4_voter_female,
                          $EciPollTurnOutRoundWise->round4_voter_other,
                          $EciPollTurnOutRoundWise->round4_voter_total,
                          $EciPollTurnOutRoundWise->round5_voter_male,
                          $EciPollTurnOutRoundWise->round5_voter_female,
                          $EciPollTurnOutRoundWise->round5_voter_other,
                          $EciPollTurnOutRoundWise->round5_voter_total ,
                          $EciPollTurnOutRoundWise->end_voter_male ,
                          $EciPollTurnOutRoundWise->end_voter_female ,
                          $EciPollTurnOutRoundWise->end_voter_other ,
                          $EciPollTurnOutRoundWise->end_voter_total,
                          $EciPollTurnOutRoundWise->total_male ,
                          $EciPollTurnOutRoundWise->total_female ,
                          $EciPollTurnOutRoundWise->total_other ,
                          $EciPollTurnOutRoundWise->Latest_total ,
                          $EciPollTurnOutRoundWise->electors_male ,
                          $EciPollTurnOutRoundWise->electors_female ,
                          $EciPollTurnOutRoundWise->electors_other ,
                          $EciPollTurnOutRoundWise->electors_total ,
                          $EciPollTurnOutRoundWise->Percent ,
                                );


                          array_push($arr, $data);
                           // }
                          }

              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'State' ,
          'PC No' ,
          'PC Name' ,
          'AC No' ,
          'AC Name' ,
          '9 AM Male',
          '9 AM Female' ,
          '9 AM Other' ,
          '9 AM Total' ,
          '11 AM Male',
          '11 AM Female' ,
          '11 AM Other',
          '11 AM Total' ,
          '1 PM Male',
          '1 PM Female' ,
          '1 PM Other',
          '1 PM Total' ,
          '3 PM Male',
          '3 PM Female' ,
          '3 PM Other',
          '3 PM Total' ,
          '5 PM Male',
          '5 PM Female' ,
          '5 PM Other',
          '5 PM Total',
          'End Of Poll Male',
          'End Of Poll Female', 
          'End Of Poll Other',
          'End Of Poll Total',
          'Latest Male',
          'Latest Female',
          'Latest Other',
          'Latest Votes',
          'Electors Male',
          'Electors Female',
          'Electors Other',
          'Electors Total',
          'Voting Percent'
                       )

                   );

                 });

            })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI  AC POLL TURN OUT ROUND REPORT AC EXCEL TRY CATCH BLOCK ENDS
        
    }
    //ECI  AC POLL TURN OUT ROUND REPORT AC WISE EXCEL FUNCTION ENDS








}  // end class