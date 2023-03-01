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

//END OF POLL FINALISE MODAL
use App\models\Admin\EndOfPollFinaliseModel;
use App\models\Admin\PhaseModel;

//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;

date_default_timezone_set('Asia/Kolkata');
    

class EciEndOfPollFinalisedController extends Controller
{   

    public $folder        = 'eci';
    public $action        = 'eci/EciEndOfPollFinalised';
    public $view_path     = "admin.pc.eci";


    //USING TRAIT FOR COMMON FUNCTIONS
    use CommonTraits;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){   
        $this->middleware(['auth:admin','auth']);
        //$this->middleware('clean_url');
        //$this->middleware('clean_request');
        $this->middleware('eci');
        $this->commonModel = new commonModel();
        $this->ECIModel = new ECIModel();
        $this->voting_model = new PollDayModel();
        $this->EopFinalisedModal = new EndOfPollFinaliseModel();

        if(!Auth::user()){
          return redirect('/officer-login');
        }
       
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
    */

    protected function guard(){
        return Auth::guard();
    }

    


    //ECI END OF POLL FINALSED REPORT STARTS
    public function EciEndOfPollFinalised(Request $request){  
      //ECI END OF POLL FINALSED REPORT TRY CATCH BLOCK STARTS
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

              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;
              //dd($AllPartyList);

              $default_phase = PhaseModel::get_current_phase();

              $request_array = []; 
              $data['phases'] = PhaseModel::get_phases();
              $data['phase'] = NULL;

              if($request->has('phase')){
                if($request->phase != 'all'){
                  $data['phase'] = $request->phase;
                }
                $request_array[] =  'phase='.$request->phase;
              }else{
                $data['phase']    = $default_phase;
                $request_array[]  =  'phase='.$default_phase; 
              }
            
              if($data['phase']==1){      
              $data['phase']    = 1;
              $data['phases'] =  [];
              }

              //set title
              $title_array  = [];
              $data['heading_title'] = 'End of Poll PC Finalised';

              if($data['phase']){
                $title_array[] = "Phase: ".$data['phase'];
              }

              $data['filter_buttons'] = $title_array;

              $data['filter']   = implode('&', array_merge($request_array));
              //end set title

              //buttons
              $data['buttons']    = [];
              $data['buttons'][]  = [
                'name' => 'Export Excel',
                'href' =>  url($this->action.'/excel').'?'.implode('&', $request_array),
                'target' => true
              ];
              $data['buttons'][]  = [
                'name' => 'Export Pdf',
                'href' =>  url($this->action.'/pdf').'?'.implode('&', $request_array),
                'target' => true
              ];


              $data['action']         = url($this->action);

              $results                = [];

              $filter_election = [
                'phase'         => $data['phase'],
              ];


              $object_states = EndOfPollFinaliseModel::get_eop_finalise_data($filter_election);


              foreach ($object_states as $result) {

                  $filter_data = [
                    'phase'         => $data['phase'],
                  ];

                  $individual_filter_array = [];
                  if($data['phase']){
                    $individual_filter_array['phase'] = 'phase='.$data['phase'];
                  }
                
                  $individual_filter    = implode('&', $individual_filter_array);


                  $results[] = [
                    'label'               => $result->state_name,
                    "total_pc"            => $result->total_pc,
                    "pc_finalised"        => $result->pc_finalised,
                    "href"                => 'javascript:void(0)'
                  ];   

              }

              $data['user_data']  =   Auth::user();
              $data['results']    =   $results;

              $data['heading_title_with_all'] = $data['heading_title'];

             // dd($data);
               return view($this->view_path.'.EciEndOfPollFinalised', $data);

/*
            return view('admin.pc.eci.EciEndOfPollFinalised',['user_data' => $user_data,'EciEndOfPollFinalised' => $EciEndOfPollFinalised]);*/
                            
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI END OF POLL FINALSED REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI END OF POLL FINALSED REPORT FUNCTION ENDS

    //ECI END OF POLL FINALSED EXCEL REPORT STARTS
    public function EciEndOfPollFinalisedExcel(Request $request){  
      //ECI END OF POLL FINALSED EXCEL REPORT TRY CATCH BLOCK STARTS
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

               set_time_limit(6000);
               $data = $this->EciEndOfPollFinalised($request->merge(['is_excel' => 1]));                         

              $headings[]=[];
              $export_data[]=['State', 'Total Pcs', 'Pc Finalised'];
              $TotalPc = 0;
              $TotalFinalisePc = 0;

              foreach ($data['results'] as $result) {
                 
                if($result['label'] ==''){
                  
                  $result['label'] = '0';

                }

                if($result['total_pc'] ==''){
                  
                   $result['total_pc'] = '0';

                }

                if($result['pc_finalised'] ==''){
                  
                   $result['pc_finalised'] = '0';

                }
                $export_data[] = [
                  $result['label'],
                         $result['total_pc'],
                         $result['pc_finalised'],
                 ];
                

               $TotalPc             +=   $result['total_pc'];
               $TotalFinalisePc     +=   $result['pc_finalised'];

                         
                         }

               $name_excel = 'EciEndOfPollFinalisedExcel_'.'_'.$cur_time;
               return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 


            //   \Excel::create('EciEndOfPollFinalisedExcel_'.'_'.$cur_time, function($excel) use($data) { 
            //   $excel->sheet('Sheet1', function($sheet) use($data) {

            //    $arr  = array();
            //    $TotalPc = 0;
            //    $TotalFinalisePc = 0;

             
            //   $user = Auth::user();
            //   $$data['results'] = ['', 'End of Poll PC Finalised','','','','','','','',''];
            //   foreach ($data['results'] as $result) {
                 
            //      if($result['label'] ==''){
                   
            //        $result['label'] = '0';

            //      }

            //      if($result['total_pc'] ==''){
                   
            //         $result['total_pc'] = '0';

            //      }

            //      if($result['pc_finalised'] ==''){
                   
            //         $result['pc_finalised'] = '0';

            //      }

            //      $exceldata =  array(
                          
            //               $result['label'],
            //               $result['total_pc'],
            //               $result['pc_finalised'],

            //                     );

            //     $TotalPc             +=   $result['total_pc'];
            //     $TotalFinalisePc     +=   $result['pc_finalised'];

            //               array_push($arr, $exceldata);
            //                // }
            //               }

            //    $totalvalues = array('Total',$TotalPc,$TotalFinalisePc);
            //     // print_r($totalvalues);die;
            //       array_push($arr,$totalvalues);
            //       $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'State', 'Total Pcs', 'Pc Finalised'
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
        //ECI END OF POLL FINALSED EXCEL REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI END OF POLL FINALSED EXCEL REPORT FUNCTION ENDS

    //ECI ACTIVE USERSEND OF POLL FINALSED PDF REPORT STARTS
    public function EciEndOfPollFinalisedPdf(Request $request){  
      //ECI END OF POLL FINALSED PDF REPORT TRY CATCH BLOCK STARTS
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

             
             $cur_time  = Carbon::now();
             $st_code = $user_data->st_code;
             $st_name = $user_data->placename;

             $data = $this->EciEndOfPollFinalised($request->merge(['is_excel' => 1])); 

              /*$pdf = \PDF::loadView($this->view_path.'.EciEndOfPollFinalisedPdf',$data);
              return $pdf->download($EciEndOfPollFinalisedPdf.'_'.date('d-m-Y').'_'.time().'.pdf');*/

              $pdf = PDF::loadView($this->view_path.'.EciEndOfPollFinalisedPdf',['user_data'=>$data['user_data'],'data' =>$data['results'],'heading_title'=>$data['heading_title'],'phase'=>$data['phase']]);
              return $pdf->download('EciEndOfPollFinalisedPdf'.trim($st_name).'_Today_'.$cur_time.'.pdf');
              return view($this->view_path.'.EciEndOfPollFinalisedPdf');  

                            
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI END OF POLL FINALSED PDF REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI END OF POLL FINALSED PDF REPORT FUNCTION ENDS






}  // end class