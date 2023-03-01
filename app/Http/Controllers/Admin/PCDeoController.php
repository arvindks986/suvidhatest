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
    use App\adminmodel\PCDEOModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\Classes\xssClean;
    use Illuminate\Support\Facades\URL;
    use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class PCDeoController extends Controller {
  /**
  * Create a new controller instance.
  *
  * @return void
  */
  public function __construct(){
    $this->middleware(['auth:admin','auth']);
        $this->middleware('deo');
        $this->commonModel = new commonModel();
        $this->deomodel = new PCDEOModel();
        $this->xssClean = new xssClean;
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
        if(Auth::check()){
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);
        // dd($d);
         $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          // dd($ele_details->ScheduleID);
         if(isset($ele_details->ScheduleID))
            $sched=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
            else 
              $sched='';

              // dd($ele_details);
          return view('admin.pc.deo.dashboard', ['user_data' => $d,'ele_details'=>$ele_details,'sched' => $sched]);
                     
          }
          else {
                return redirect('/officer-login');
              }
      }  // end index function

      public function datewisereport(){
        if(Auth::check()){
          $user = Auth::user();
          $d=$this->commonModel->getunewserbyuserid($user->id);
          // dd($d);
          $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
            // dd($ele_details);
            if(isset($ele_details)) {$i=0;
              foreach($ele_details as $ed) {  
                $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
                $const_type=$ed->CONST_TYPE;
              }
            }

           if(isset($ele_details->ScheduleID)) {
              $sched=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
              $const_type=$ele_details->ConstType;
           }
              else {
                $sched='';
              }
                $list=$this->deomodel->electiondetailsbystatecode($d->st_code,$const_type,$d->dist_no);
                $fromdate = date('d-m-Y');
                $todate = date('d-m-Y');    
                $timeInterval = $fromdate.'~'.$todate;
                $fromdate = date('Y-m-d'); 
                $todate = date('Y-m-d');  
                if(!empty($list)){  $i=0;
                  $allTypeCountArr = array();
                    foreach ($list as $lis) {   $i++;
                      // dd($lis);
                            if($lis->CONST_TYPE=='AC') {
                              $const=$this->commonModel->getacbyacno($lis->ST_CODE,$lis->CONST_NO);
                              $const_name=$const->AC_NAME;
                            }
                          if($lis->CONST_TYPE=='PC') {
                              $const=$this->commonModel->getpcname($lis->ST_CODE,$lis->PC_NO);
                              $const_name=trim($const->PC_NAME);
                            }

                    $total =$this->deomodel->gettotalnominationcnt($lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate='', $todate=''); // ALL list
                    $totw=$this->deomodel->gettotalnominationcntbystatus('5', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate='', $todate=''); 
                    $totr=$this->deomodel->gettotalnominationcntbystatus('4', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate='', $todate=''); 
                    $totacc=$this->deomodel->gettotalnominationcntbystatus('6', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate='', $todate=''); 
                    $totv=$this->deomodel->gettotalnominationcntbystatus('2', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate='', $todate=''); 
                    $totrec=$this->deomodel->gettotalnominationcntbystatus('3', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate='', $todate=''); 
                    $tota=$this->deomodel->gettotalnominationcntbystatus('1', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate='', $todate=''); 
                    // $totfor=$this->deomodel->gettotalnominationcntbystatus('formsubmited', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate='', $todate=''); 
                            
                        $allTypeCountArr[$i]['const_no'] = $lis->CONST_NO;
                        $allTypeCountArr[$i]['const_name'] = $const_name;
                        $allTypeCountArr[$i]['total'] = $total;      
                        $allTypeCountArr[$i]['totalw'] = $totw;                   
                        $allTypeCountArr[$i]['totalr'] = $totr;
                        $allTypeCountArr[$i]['totalacc'] = $totacc;
                        $allTypeCountArr[$i]['totalv'] = $totv;
                        $allTypeCountArr[$i]['totalrec'] =$totrec; 
                        $allTypeCountArr[$i]['totala'] =$tota;  
                            // dd($allTypeCountArr);
                        }   
                      }
            return view('admin.pc.deo.datewisereport', ['user_data' => $d,'list_const' => $list,'ele_details'=>$ele_details,'sched' => $sched,'allTypeCountArr' =>$allTypeCountArr]);
                       
            }
            else {
                  return redirect('/officer-login');
                }
      }

      public function datewisereport_range(Request $request){
        if(Auth::check()){
          $user = Auth::user();
          $d=$this->commonModel->getunewserbyuserid($user->id);
          
          $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
            // dd($ele_details);
            if(isset($ele_details)) {$i=0;
              foreach($ele_details as $ed) {  
                $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
                $const_type=$ed->CONST_TYPE;
              }
            }

           if(isset($ele_details->ScheduleID)) {
              $sched=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
              $const_type=$ele_details->ConstType;
           }
              else {
                $sched='';
              }
              $from_date = ($request->from_date);
              $to_date = ($request->to_date); 
              $const = trim($request->const);
              // dd($const);
              $list=$this->deomodel->electiondetailsbystatecode($d->st_code,$const_type,$d->dist_no,$const);
                
              // dd($list);  

              $timeInterval = $from_date.'~'.$to_date;
              
              $fromdate = date('Y-m-d',strtotime($from_date));
              $todate = date('Y-m-d',strtotime($to_date));  

                if(!empty($list)){  $i=0;
                  $allTypeCountArr = array();
                    foreach ($list as $lis) {   $i++;
                      // dd($lis);
                            if($lis->CONST_TYPE=='AC') {
                              $const=$this->commonModel->getacbyacno($lis->ST_CODE,$lis->CONST_NO);
                              $const_name=$const->AC_NAME;
                            }
                          if($lis->CONST_TYPE=='PC') {
                              $const=$this->commonModel->getpcname($lis->ST_CODE,$lis->PC_NO);
                              $const_name=trim($const->PC_NAME);
                            }

                    $total =$this->deomodel->gettotalnominationcnt($lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); // ALL list
                    $totw=$this->deomodel->gettotalnominationcntbystatus('5', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                    $totr=$this->deomodel->gettotalnominationcntbystatus('4', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                    $totacc=$this->deomodel->gettotalnominationcntbystatus('6', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                    $totv=$this->deomodel->gettotalnominationcntbystatus('2', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                    $totrec=$this->deomodel->gettotalnominationcntbystatus('3', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                    $tota=$this->deomodel->gettotalnominationcntbystatus('1', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                    // $totfor=$this->deomodel->gettotalnominationcntbystatus('formsubmited', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                            
                        $allTypeCountArr[$i]['const_no'] = $lis->CONST_NO;
                        $allTypeCountArr[$i]['const_name'] = $const_name;
                        $allTypeCountArr[$i]['total'] = $total;      
                        $allTypeCountArr[$i]['totalw'] = $totw;                   
                        $allTypeCountArr[$i]['totalr'] = $totr;
                        $allTypeCountArr[$i]['totalacc'] = $totacc;
                        $allTypeCountArr[$i]['totalv'] = $totv;
                        $allTypeCountArr[$i]['totalrec'] =$totrec; 
                        $allTypeCountArr[$i]['totala'] =$tota;  
                            // dd($allTypeCountArr);
                        }   
                      }
                      $str = '';
                      if(count($allTypeCountArr)>0)
                        {    $i=0;   $totalag=0;  $totalvg=0; $totalrecg=0; $totalwg=0; $totalaccg=0; $totalrg=0; $totalg=0;
                            
                        foreach($allTypeCountArr as $list) {
                          
                              $totalag=$totalag+$list['totala'];  $totalvg=$totalvg+$list['totalv']; $totalrecg=$totalrecg+$list['totalrec']; 
                              $totalwg=$totalwg+$list['totalw']; $totalrg=$totalrg+$list['totalr']; 
                              $totalaccg=$totalaccg+$list['totalacc']; $totalg=$totalg+$list['total'];          
                          
                          $str .= "<tr><td>".$list['const_name']."</td><td>".$list['total']."</td><td>".$list['totalw']."</td><td>".$list['totalr']."</td><td>".$list['totalacc']."</td><td>".$list['total']."</td> </tr>";
                        }
                          echo $str .= "<tr><td>Total:- </td><td>".$totalg."</td><td>".$totalwg."</td><td>".$totalrg."</td><td>".$totalaccg."</td><td>".$totalg."</td> </tr>";     
                          }else{
                              echo $str .= '<tr><td colspan="8" style="color:red; text-align:center;"><b>No Record Found.</b></td></tr>';
                        }
            }
            else {
                  return redirect('/officer-login');
                }
      }

      public function reportspdfview(Request $request) {
        set_time_limit(6000);
          $date=trim(base64_decode($request->date));
          $consti=trim(base64_decode($request->consti));
          
          if(Auth::check()){
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id);
            // dd($d);
            $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
              // dd($ele_details);
              if(isset($ele_details)) {$i=0;
                foreach($ele_details as $ed) {  
                  $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
                  $const_type=$ed->CONST_TYPE;
                }
              }
              $state =$this->commonModel->getstatebystatecode($d->st_code);
            $distname = $this->commonModel->getdistrictbydistrictno($d->st_code,$d->dist_no);
             if(isset($ele_details->ScheduleID)) {
                $sched=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
                $const_type=$ele_details->ConstType;
             }
                else {
                  $sched='';
                }
                $list=$this->deomodel->electiondetailsbystatecode($d->st_code,$const_type,$d->dist_no,$consti);
                
                if($date=='all') {
                  $fromdate='';
                  $todate='';
                }else{
                  $date_range = explode('~', $date);
                  $from_date=$date_range[0];
                  $to_date=$date_range[1];
                  $fromdate = date('Y-m-d',strtotime($from_date));
                  $todate = date('Y-m-d',strtotime($to_date));
                }
  
                  if(!empty($list)){  $i=0;
                    $allTypeCountArr = array();
                      foreach ($list as $lis) {   $i++;
                        // dd($lis);
                              if($lis->CONST_TYPE=='AC') {
                                $const=$this->commonModel->getacbyacno($lis->ST_CODE,$lis->CONST_NO);
                                $const_name=$const->AC_NAME;
                              }
                            if($lis->CONST_TYPE=='PC') {
                                $const=$this->commonModel->getpcname($lis->ST_CODE,$lis->PC_NO);
                                $const_name=trim($lis->PC_NAME_EN);
                              }
                      $total =$this->deomodel->gettotalnominationcnt($lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); // ALL list
                      $totw=$this->deomodel->gettotalnominationcntbystatus('5', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                      $totr=$this->deomodel->gettotalnominationcntbystatus('4', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                      $totacc=$this->deomodel->gettotalnominationcntbystatus('6', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                      $totv=$this->deomodel->gettotalnominationcntbystatus('2', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                      $totrec=$this->deomodel->gettotalnominationcntbystatus('3', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                      $tota=$this->deomodel->gettotalnominationcntbystatus('1', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                      // $totfor=$this->deomodel->gettotalnominationcntbystatus('formsubmited', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                              
                          $allTypeCountArr[$i]['const_no'] = $lis->CONST_NO;
                          $allTypeCountArr[$i]['const_name'] = $const_name;
                          $allTypeCountArr[$i]['total'] = $total;      
                          $allTypeCountArr[$i]['totalw'] = $totw;                   
                          $allTypeCountArr[$i]['totalr'] = $totr;
                          $allTypeCountArr[$i]['totalacc'] = $totacc;
                          $allTypeCountArr[$i]['totalv'] = $totv;
                          $allTypeCountArr[$i]['totalrec'] =$totrec; 
                          $allTypeCountArr[$i]['totala'] =$tota;  
                              // dd($allTypeCountArr);
                          }   
                        }

                        $pdf = PDF::loadView('admin.pc.deo.deoreportpdf',compact('date',$date,'allTypeCountArr',$allTypeCountArr,'state',$state,'distname',$distname));
		                    return $pdf->download('deoreportpdf.pdf');
		                    return view('admin.pc.deo.deoreportpdf');
              }
              else {
                    return redirect('/officer-login');
                  }

      }

      public function reportexcelview(Request $request) {

        set_time_limit(6000);
          $date=trim(base64_decode($request->date));
          $consti=trim(base64_decode($request->consti));
          
          if(Auth::check()){
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id);
            // dd($d);
            $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
              // dd($ele_details);
              if(isset($ele_details)) {$i=0;
                foreach($ele_details as $ed) {  
                  $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
                  $const_type=$ed->CONST_TYPE;
                }
              }
              $state =$this->commonModel->getstatebystatecode($d->st_code);
            $distname = $this->commonModel->getdistrictbydistrictno($d->st_code,$d->dist_no);
             if(isset($ele_details->ScheduleID)) {
                $sched=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
                $const_type=$ele_details->ConstType;
             }
                else {
                  $sched='';
                }

                $list=$this->deomodel->electiondetailsbystatecode($d->st_code,$const_type,$d->dist_no);
                
                if($date=='all') {
                  $fromdate='';
                  $todate='';
                }else{
                  $date_range = explode('~', $date);
                  $from_date=$date_range[0];
                  $to_date=$date_range[1];
                  $fromdate = date('Y-m-d',strtotime($from_date));
                  $todate = date('Y-m-d',strtotime($to_date));
                }
                // dd($list);
                  if(!empty($list)){  $i=0;
                    $allTypeCountArr = array();
                      foreach ($list as $lis) {
                           $i++;
                        // dd($lis);
                        $stateId = $d->st_code;
                        $cur_time    = Carbon::now();
                        // dd($d);

                        if(!empty($list)){  
                          $i=0; $arr = array();
                          $list=$this->deomodel->electiondetailsbystatecode($d->st_code,$const_type,$d->dist_no,$consti);
                          $export_data[] = ['SN', 'Constituency Name', 'Applied','Withdrawn', 'Rejected','Accepted','Total'];
                          $headings[]=[];
                          $allTypeCountArr = array();
                            foreach ($list as $lis) {   $i++; 
                              if($lis->CONST_TYPE=='AC') {
                                $const=$this->commonModel->getacbyacno($lis->ST_CODE,$lis->CONST_NO);
                                $const_name=$const->AC_NAME;
                              }
                            if($lis->CONST_TYPE=='PC') {
                                // dd($lis);
                                $const_name=trim($lis->PC_NAME_EN);
                              }
                                    $total =$this->deomodel->gettotalnominationcnt($lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); // ALL list
                                    $totw=$this->deomodel->gettotalnominationcntbystatus('5', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                                    $totr=$this->deomodel->gettotalnominationcntbystatus('4', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                                    $totacc=$this->deomodel->gettotalnominationcntbystatus('6', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                                    $totv=$this->deomodel->gettotalnominationcntbystatus('2', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                                    $totrec=$this->deomodel->gettotalnominationcntbystatus('3', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                                    $tota=$this->deomodel->gettotalnominationcntbystatus('1', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
            
                                    if($total==0){
                                      $total ='0';
                                    }
                                    if($tota ==0){
                                      $tota ='0';
                                    }
                                    if($totacc ==0){
                                        $totacc ='0';
                                    }
                                    if($totrec ==0){
                                      $totrec ='0';
                                    }
                                    if($totr ==0){
                                      $totr ='0';
                                    }
                                    if($totv==0){
                                      $totv='0';
                                    }
                                    if($totw==0){
                                      $totw='0';
                                    }
                                    $export_data[] = [
                                      $i,
                                          $const_name,
                                          $total,
                                          $totw,
                                          $totr,
                                          $totacc,
                                          $total
                                     ];

                                   
                                }  
                                
                        $name_excel = 'nomination_report_excel_'.trim($d->st_code).'_'.$cur_time;
                        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');        
                                    }

                  //       \Excel::create('nomination_report_excel_'.trim($d->st_code).'_'.$cur_time, function($excel) use($d,$consti,$const_type,$fromdate, $todate,$stateId) {
                          
                  // $excel->sheet('Sheet1', function($sheet) use($d,$consti,$const_type,$fromdate, $todate,$stateId) {
                    // $list=$this->deomodel->electiondetailsbystatecode($d->st_code,$const_type,$d->dist_no,$consti);
                    
                    // if(!empty($list)){  
                    //   $i=0; $arr = array();
                    //   $allTypeCountArr = array();
                    //     foreach ($list as $lis) {   $i++; 
                    //       if($lis->CONST_TYPE=='AC') {
                    //         $const=$this->commonModel->getacbyacno($lis->ST_CODE,$lis->CONST_NO);
                    //         $const_name=$const->AC_NAME;
                    //       }
                    //     if($lis->CONST_TYPE=='PC') {
                    //         // dd($lis);
                    //         $const_name=trim($lis->PC_NAME_EN);
                    //       }
                    //             $total =$this->deomodel->gettotalnominationcnt($lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); // ALL list
                    //             $totw=$this->deomodel->gettotalnominationcntbystatus('5', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                    //             $totr=$this->deomodel->gettotalnominationcntbystatus('4', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                    //             $totacc=$this->deomodel->gettotalnominationcntbystatus('6', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                    //             $totv=$this->deomodel->gettotalnominationcntbystatus('2', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                    //             $totrec=$this->deomodel->gettotalnominationcntbystatus('3', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
                    //             $tota=$this->deomodel->gettotalnominationcntbystatus('1', $lis->CONST_TYPE,$lis->ST_CODE,$lis->DIST_NO,$lis->CONST_NO, $fromdate, $todate); 
        
                    //             if($total==0){
                    //               $total ='0';
                    //             }
                    //             if($tota ==0){
                    //               $tota ='0';
                    //             }
                    //             if($totacc ==0){
                    //                 $totacc ='0';
                    //             }
                    //             if($totrec ==0){
                    //               $totrec ='0';
                    //             }
                    //             if($totr ==0){
                    //               $totr ='0';
                    //             }
                    //             if($totv==0){
                    //               $totv='0';
                    //             }
                    //             if($totw==0){
                    //               $totw='0';
                    //             }
                    //             $data =  array(
                    //                   $i,
                    //                   $const_name,
                    //                   $total,
                    //                   $totw,
                    //                   $totr,
                    //                   $totacc,
                    //                   $total );
                    //                   array_push($arr, $data);
                    //         }  
                            
                            
                    //             }
              //                 $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
              //                   'SN', 'Constituency Name', 'Applied','Withdrawn', 'Rejected','Accepted','Total'));
              //       });
              // })->export('xls');
              
                  } }
                  else {
                        return redirect('/officer-login');
                      }
          }
    }
     public function changepassword(request $request){
         if(Auth::check()){
           $user = Auth::user();
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

           return view('admin.pc.deo.changepassword', ['user_data' => $d]);
         }
       } //@end changepassword function
      public function changePasswordStore(request $request){
             if(Auth::check()){
               $user = Auth::user();
               $d=$this->commonModel->getunewserbyuserid($user->id);
               
               if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
                 // The passwords matches
                 return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
             }
             if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
                 
                 return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
             }
             $validatedData = $request->validate([
                 'current-password' => 'required',
                 'new-password' => 'required|string|min:8|required_with:new-password-confirm|same:new-password-confirm',
				 'new-password-confirm' => 'required|string|min:8',
             ]);
             //Change Password
             $user = Auth::user();
             $user->password = bcrypt($request->get('new-password'));
             $user->save();
              return redirect()->back()->with("success","Password changed successfully !");
              }//@end Auth::check()

          } //@end changePasswordStore function     
		  
		      
  /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 28-02-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return officerList By DEO fuction     
  */
   
  public function officerList(Request $request){
    if(Auth::check()){
     $user = Auth::user();
     $d=$this->commonModel->getunewserbyuserid($user->id);
     $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
     
     $officerlist =DB::table('officer_login')
     ->where('st_code',$d->st_code)
	 ->where('dist_no',$d->dist_no)
      ->whereIn('role_id', [18,20])->get();
    // print_r($officerlist);  die;
    return view('admin.pc.deo.officer-details',['user_data' => $d,'ele_details' => $ele_details,'officerlist' => $officerlist]);
  }
  else {
      return redirect('/officer-login');
    }   
  }   // end officerList function  
   
/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 28-02-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return officerProfileUpdate By ID fuction     
  */
  public function officerProfileUpdate(Request $request,$id='') {
   if(Auth::check()){
      $user = Auth::user();
      $d=$this->commonModel->getunewserbyuserid($user->id);
      $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
 
        if (!empty($_POST['profileUpdate'])) {
             $validator = $this->validate(
            $request, 
                [
                  'name' => 'required',
                  'email' => 'required',
                  'Phone_no' => 'required|string|min:10|numeric|digits:10',

                 ],
                [
                 'name.required' => 'Please enter your name', 
                 'email.required' => 'Please enter your email',
                 'Phone_no.required' => 'Please enter mobile number',
                 'Phone_no.digits' => 'Please enter 10 digit mobile number',
                 ]);
           
           // if ($validator->passes()) {
            if ($validator) { 
                if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['Phone_no'])) {
                   
                    $name =  strip_tags($_POST['name']);
                    $email =  strip_tags($_POST['email']);
                    $Phone_no = strip_tags($_POST['Phone_no']);
                   // $Phone_no = $this->xssClean($_POST['profileUpdate']);
                    $officerdata = array(
                      'name' => $name,
                       'email' => $email, 
                       'Phone_no' => $Phone_no,
                       /*'modified_by' => $d->id,*/
                       'added_update_at' => date('Y-m-d'), 
                       'updated_at' => date('Y-m-d H:i:s')
                      );
                     // dd($officerdata);
                    $where = array('id' => $_POST['profileUpdate']);
                    $result = DB::table('officer_login')->where($where)->update($officerdata);
                   
                    \Session::flash('success_success', 'You have Successfully Updated!. '); 
                   // return redirect()->back();
                   return redirect('/pcdeo/officer-details');
                      }
             } else {
              \Session::flash('success_error', 'You have some Error!. '); 
              return redirect('/pcdeo/officer-details');
              //  return redirect()->back()->withErrors($validator, 'error');
            }
        } else {
            $decryptedid = decrypt($id);
            $getofficerdetails =DB::table('officer_login')->where('id',$decryptedid)->get();
            return view('admin.pc.deo.officer-profile')->with(array('user_data' => $d, 'showpage' => 'officer-profile', 'getofficerdetails' => $getofficerdetails));
        }
    } else {
        return redirect('/officer-login');
    }
} // end officerProfileUpdate function  
    
    // sachchidnand
  public function updateuser(Request $request){  
        if(Auth::check()){
              $user = Auth::user();
              $uid=$user->id;
              $d=$this->commonModel->getunewserbyuserid($uid);
              $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
               $this->validate(
                $request, 
                    [
                     'name' => 'required',
                      'email' => 'required|email',
                      'Phone_no'=>'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|numeric|digits:10',
                     ],
                    [
                    'name.required' => 'Please enter name', 
                     'email.required' => 'Please enter your email',
                     'email.email' => 'Please enter valid email',
                      'Phone_no.required'=>'Please enter validate mobileno',
                      'Phone_no.min'=>'Mobile Number minimum 10 digit',
                      'Phone_no.digits'=>'Mobile Number minimum 10 digit',
                      'Phone_no.numeric'=>'Please enter validate mobileno',
                     ]);
              
               $id=$this->xssClean->clean_input(Check_Input($request->input('profileUpdate')));
               $name=$this->xssClean->clean_input(Check_Input($request->input('name')));
               $mobile=$this->xssClean->clean_input(Check_Input($request->input('Phone_no')));
               $email=$this->xssClean->clean_input(Check_Input($request->input('email')));
                $date = Carbon::now();
                $currentTime = $date->format('Y-m-d H:i:s'); 
                $code = Hash::make(str_random(10));
                $mobile_otp =rand(100000,999999);
                $rec=getById('officer_login','id',$id);   
              $record = array(
                'name'=>$name,
                //'password'=>'',
                'Phone_no'=>$mobile,
                'email'=>$email,
                'mobile_otp' => $mobile_otp,
                'otp_time' => $currentTime,
                'auth_token' => $code,
             );
              $n = DB::table('officer_login')->where('id', $id)->update($record);
              $encodeid=base64_encode($id);
              $passcreaturl = URL::to("/updateprofile/$encodeid");
              $html = "Dear $name,\n\n";
                                  $html .= "Your account has been updated in Suvidha Portal"
                                      . "Your account must be activated before you use it. For activating your account and updating your particular, please click on the following link. Alternatively, you could copy and paste the link in your browser.\n\n";
                                  $html .= "$passcreaturl\n\n";
                                  $html .= "OTP: $mobile_otp\n\n";
                                  $html .= "Login ID:  $rec->officername\n\n";
                                  $html .= "For verifying  your account,  kindly enter OTP $mobile_otp and this OTP has also sent on your registered mobile no.:\n\n";
                                  
                                  $html .= "Thanks & Regards,\n\n";
                                  $html .= "Suvidha Team,\n\n";

                                $html = strip_tags($html);
                                //sendotpmail($email,'UserLogin Credential',$html);  
                                 mail ($email, 'UserLogin Credential',$html,'suvidha.eci.gov.in');
                          
                
          if($mobile!=""){
            $mob_message = "Dear Sir/Madam, your OTP is ".$mobile_otp." and Login ID: ".$rec->officername." for SUVIDHA Portal.Activation link has been sent on your email. ".$passcreaturl." Please enter that link and enter OTP to proceed. Do not share this OTP Team ECI";
              $response = SmsgatewayHelper::gupshup($mobile,$mob_message);
            }  
 
                  \Session::flash('success_mes', 'officer profile updated successfully');   
                  return Redirect::to('/pcdeo/officer-details');
          }
          else {
              return redirect('/officer-login');
          }    
  
        }   // end dashboard function
}  // end class