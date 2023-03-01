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
    use App\adminmodel\PCRoreportModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;

    use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class PCRoreportController extends Controller {
  /**
  * Create a new controller instance.
  *
  * @return void
  */
  public function __construct(){
        $this->middleware(['auth:admin','auth']);
        $this->middleware('adminsession');
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ro');
        $this->commonModel = new commonModel();
        $this->ropcreport = new PCRoreportModel();
     }

  /**
  * Show the application dashboard.
  *
  * @return \Illuminate\Http\Response
  */

   protected function guard(){
        return Auth::guard();
    }

      public function datewisereport(){      
	    if(Auth::check()){
		    $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		    // dd($d);
		    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
                $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
                $seched=getschedulebyid($ele_details->ScheduleID);
                $sechdul=checkscheduledetails($seched);  

           if(isset($ele_details->ScheduleID)) {
              $sched=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
              $const_type=$ele_details->CONST_TYPE;
           }
              else {
                $sched='';
              }
                $list=$this->ropcreport->electiondetailsbystatecode($d->st_code,$const_type,$d->pc_no);
                // dd($list);
                // $fromdate = date('d-m-Y');
                // $todate = date('d-m-Y');    
                // $timeInterval = $fromdate.'~'.$todate;
                // $fromdate = date('Y-m-d'); 
                // $todate = date('Y-m-d');  
                if(!empty($list)){  $i=0;
                  $allTypeCountArr = array();
                    foreach ($list as $lis) {   $i++;
                    //   dd($lis);
                            if($lis->CONST_TYPE=='AC') {
                              $const=$this->commonModel->getacbyacno($lis->ST_CODE,$lis->CONST_NO);
                              $const_name=$const->AC_NAME;
                            }
                          if($lis->CONST_TYPE=='PC') {
                              $const=$this->commonModel->getpcname($lis->ST_CODE,$lis->CONST_NO);
                              $const_name=trim($const->PC_NAME);
                            }

                    $total =$this->ropcreport->gettotalnominationcnt($lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate='', $todate=''); // ALL list
                    $totw=$this->ropcreport->gettotalnominationcntbystatus('5', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate='', $todate=''); 
                    $totr=$this->ropcreport->gettotalnominationcntbystatus('4', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate='', $todate=''); 
                    $totacc=$this->ropcreport->gettotalnominationcntbystatus('6', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate='', $todate=''); 
                    $totv=$this->ropcreport->gettotalnominationcntbystatus('2', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate='', $todate=''); 
                    $totrec=$this->ropcreport->gettotalnominationcntbystatus('3', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate='', $todate=''); 
                    $tota=$this->ropcreport->gettotalnominationcntbystatus('1', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate='', $todate=''); 
                    //  dd($total);
                    // $totfor=$this->ropcreport->gettotalnominationcntbystatus('formsubmited', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate='', $todate=''); 
                            
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
                    //   dd($list);
            return view('admin.pc.ro.datewisereport', ['user_data' => $d,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'list_const' => $list,'ele_details'=>$ele_details,'sched' => $sched,'allTypeCountArr' =>$allTypeCountArr]);
                       
            }
            else {
                  return redirect('/officer-login');
                }
      }

      public function datewisereport_range(Request $request){
        if(Auth::check()){
		    $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		    // dd($d);
		$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
          $seched=getschedulebyid($ele_details->ScheduleID);
                $sechdul=checkscheduledetails($seched);    

           if(isset($ele_details->ScheduleID)) {
              $sched=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
              $const_type=$ele_details->CONST_TYPE;
           }
              else {
                $sched='';
              }
              $from_date = ($request->from_date);
              $to_date = ($request->to_date); 
              $const = trim($request->const);

              if(isset($from_date)){
                if($from_date=='all' && $to_date=='all'){
                  $from_date='';
                  $to_date='';
                }
              }
              
              $timeInterval = $from_date.'~'.$to_date;
              
              $fromdate = date('Y-m-d',strtotime($from_date));
              $todate = date('Y-m-d',strtotime($to_date));  

              $list=$this->ropcreport->electiondetailsbystatecode($d->st_code,$const_type,$const);

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
                    $total =$this->ropcreport->gettotalnominationcnt($lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); // ALL list
                    $totw=$this->ropcreport->gettotalnominationcntbystatus('5', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                    $totr=$this->ropcreport->gettotalnominationcntbystatus('4', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                    $totacc=$this->ropcreport->gettotalnominationcntbystatus('6', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                    $totv=$this->ropcreport->gettotalnominationcntbystatus('2', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                    $totrec=$this->ropcreport->gettotalnominationcntbystatus('3', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                    $tota=$this->ropcreport->gettotalnominationcntbystatus('1', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                    // $totfor=$this->ropcreport->gettotalnominationcntbystatus('formsubmited', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                        $allTypeCountArr[$i]['const_no'] = $lis->CONST_NO;
                        $allTypeCountArr[$i]['const_name'] = $const_name;
                        $allTypeCountArr[$i]['total'] = $total;      
                        $allTypeCountArr[$i]['totalw'] = $totw;                   
                        $allTypeCountArr[$i]['totalr'] = $totr;
                        $allTypeCountArr[$i]['totalacc'] = $totacc;
                        $allTypeCountArr[$i]['totalv'] = $totv;
                        $allTypeCountArr[$i]['totalrec'] =$totrec; 
                        $allTypeCountArr[$i]['totala'] =$tota;  
                            // dd(count($allTypeCountArr));
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
        //   dd($consti);  
          if(Auth::check()){
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id);
            // dd($d);
            $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
              // dd($ele_details);
              $state =$this->commonModel->getstatebystatecode($d->st_code);
              $const_name=trim($this->commonModel->getpcname($d->st_code,$d->pc_no)->PC_NAME);
            //   dd($const);
             if(isset($ele_details->ScheduleID)) {
                $sched=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
                $const_type=$ele_details->CONST_TYPE;
             }
                else {
                  $sched='';
                }
                $list=$this->ropcreport->electiondetailsbystatecode($d->st_code,$const_type,$ele_details->CONST_NO);
                // dd($date);
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
                                $const_name=trim($lis->PC_NAME);
                              }
                              // dd($fromdate);
                      $total =$this->ropcreport->gettotalnominationcnt($lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); // ALL list
                      $totw=$this->ropcreport->gettotalnominationcntbystatus('5', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                      $totr=$this->ropcreport->gettotalnominationcntbystatus('4', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                      $totacc=$this->ropcreport->gettotalnominationcntbystatus('6', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                      $totv=$this->ropcreport->gettotalnominationcntbystatus('2', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                      $totrec=$this->ropcreport->gettotalnominationcntbystatus('3', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                      $tota=$this->ropcreport->gettotalnominationcntbystatus('1', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                      // $totfor=$this->ropcreport->gettotalnominationcntbystatus('formsubmited', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                            //   dd($total);
                          $allTypeCountArr[$i]['const_no'] = $lis->CONST_NO;
                          $allTypeCountArr[$i]['const_name'] = $const_name;
                          $allTypeCountArr[$i]['total'] = $total;      
                          $allTypeCountArr[$i]['totalw'] = $totw;                   
                          $allTypeCountArr[$i]['totalr'] = $totr;
                          $allTypeCountArr[$i]['totalacc'] = $totacc;
                          $allTypeCountArr[$i]['totalv'] = $totv;
                          $allTypeCountArr[$i]['totalrec'] =$totrec; 
                          $allTypeCountArr[$i]['totala'] =$tota;  
                          }   
                        }
                            // dd($allTypeCountArr);
                        $pdf = PDF::loadView('admin.pc.ro.roreportpdf',compact('date',$date,'allTypeCountArr',$allTypeCountArr,'state',$state,'const_name',$const_name));
		                    return $pdf->download('roreportpdf.pdf');
		                    return view('admin.pc.ro.deoreportpdf');
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
            //   dd($ele_details);
              if(isset($ele_details)) {$i=0;
                  $sched=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
                  $const_type=$ele_details->CONST_TYPE;
              }
              $state =$this->commonModel->getstatebystatecode($d->st_code);
              $const_name=trim($this->commonModel->getpcname($d->st_code,$d->pc_no)->PC_NAME);
             if(isset($ele_details->ScheduleID)) {
                $sched=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
                $const_type=$ele_details->CONST_TYPE;
             }
                else {
                  $sched='';
                }
                $list=$this->ropcreport->electiondetailsbystatecode($d->st_code,$const_type,$d->pc_no);
                // dd($list);
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
                        $stateId = $d->st_code;
                        $cur_time    = Carbon::now();
						$consti = $lis->PC_NO;
                //         dd($d);
      // \Excel::create('nomination_report_excel_'.trim($d->st_code).'_'.$cur_time, function($excel) use($d,$consti,$const_type,$fromdate, $todate,$stateId) {
                          
               //   $excel->sheet('Sheet1', function($sheet) use($d,$consti,$const_type,$fromdate, $todate,$stateId) {
                    $list=$this->ropcreport->electiondetailsbystatecode($d->st_code,$const_type,$consti);
                    // dd($list);
                    if(!empty($list)){  $i=0; $arr = array();
                      $allTypeCountArr = array();
                      $export_data[] = ['SN', 'Constituency Name', 'Applied','Withdrawn', 'Rejected','Accepted','Total'];
                      $headings[]=[];
                        foreach ($list as $lis) {   $i++; 
                          if($lis->CONST_TYPE=='AC') {
                            $const=$this->commonModel->getacbyacno($lis->ST_CODE,$lis->CONST_NO);
                            $const_name=$const->AC_NAME;
                          }
                        if($lis->CONST_TYPE=='PC') {
                            // dd($lis);
                            $const_name=trim($lis->PC_NAME);
                          }
                                $total =$this->ropcreport->gettotalnominationcnt($lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); // ALL list
                                $totw=$this->ropcreport->gettotalnominationcntbystatus('5', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                                $totr=$this->ropcreport->gettotalnominationcntbystatus('4', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                                $totacc=$this->ropcreport->gettotalnominationcntbystatus('6', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                                $totv=$this->ropcreport->gettotalnominationcntbystatus('2', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                                $totrec=$this->ropcreport->gettotalnominationcntbystatus('3', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                                $tota=$this->ropcreport->gettotalnominationcntbystatus('1', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
        // dd($total);
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

                                // $data =  array(
                                //       $i,
                                //       $const_name,
                                //       $total,
                                //       $totw,
                                //       $totr,
                                //       $totacc,
                                //       $total
                                //      );
                                //       array_push($arr, $data);
                            } 
                            
        $name_excel = 'nomination_report_excel_'.trim($d->st_code).'_'.$cur_time;
        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
                                }








                          //    $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                           //     'SN', 'Constituency Name', 'Applied','Withdrawn', 'Rejected','Accepted','Total'));
                  //  });
            //  })->export('xls');
                  } }
                  else {
                        return redirect('/officer-login');
                      }
          }
    }
         
}  // end class