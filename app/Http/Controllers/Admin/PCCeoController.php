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
    use MPDF;
    use App\commonModel;  
    use App\adminmodel\CEOModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\adminmodel\CEOPCModel;
    use App\adminmodel\PCCeoReportModel;
    use App\Classes\xssClean;
    use Illuminate\Support\Facades\URL;
    use Illuminate\Support\Facades\Crypt;
 
class PCCeoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){    
        $this->middleware('adminsession');
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ceo');
        $this->commonModel = new commonModel();
        $this->ceomodel = new CEOPCModel();
        $this->pcceoreportModel = new PCCeoReportModel();
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

    
    public function dashboard(Request $request){
        if(Auth::check()){
              $user = Auth::user();
               $uid=$user->id;
              $d=$this->commonModel->getunewserbyuserid($user->id);
              $d=$this->commonModel->getunewserbyuserid($uid);
              $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

            $sched=''; $search='';
            $status=$this->commonModel->allstatus(); 
            if(isset($ele_details)) {  $i=0;
              foreach($ele_details as $ed) {  
                 $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
                 $const_type=$ed->CONST_TYPE;
               }
            }
			//shishir
            $scrutinycandidatecount = DB::table('expenditure_notification')
                            ->leftjoin('candidate_nomination_detail', 'expenditure_notification.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                           ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                           ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                           ->where('candidate_nomination_detail.st_code', '=', $d->st_code)
                           ->where('candidate_nomination_detail.application_status', '=', '6')
                           ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                           ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                           ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->Where('expenditure_notification.ceo_read_status', '=', '0')
                           ->count();
            $request->session()->put('countscrutiny', $scrutinycandidatecount);
             //shishir
            // $pc=$this->ceomodel->electiondetailsbystatecode($d->st_code);
            return view('admin.pc.ceo.dashboard',['user_data' => $d,'sched' =>$sched]);
             
          }
          else {
              return redirect('/officer-login');
          }    
  
        }   // end dashboard function
  public function edituser($eid='', Request $request){
        if(Auth::check()){
              $user = Auth::user();
               $uid=$user->id;
              $d=$this->commonModel->getunewserbyuserid($user->id);
              $d=$this->commonModel->getunewserbyuserid($uid);
              $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
               $eid   = Crypt::decrypt($eid);
              $rec=getById('officer_login','id',$eid);
              //dd($rec);   
               return view('admin.pc.ceo.officer-profile',['user_data' => $d,'offrecords'=>$rec]);
          }
          else {
              return redirect('/officer-login');
          }    
  
        }   // end dashboard function
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
            $mob_message = "Dear Sir/Madam, your OTP is ".$mobile_otp." and Login ID: ".$rec->officername." for SUVIDHA Portal.Activation link has been sent on your email.".$passcreaturl." Please enter that link and enter OTP to proceed. Do not share this OTP Team ECI";
              $response = SmsgatewayHelper::gupshup($mobile,$mob_message);
            }  
 
                  \Session::flash('success_mes', 'officer profile updated successfully');   
                  return Redirect::to('/pcceo/officer-details');
          }
          else {
              return redirect('/officer-login');
          }    
  
        }   // end dashboard function
  public function showdashboard($cand_status='',$constituency='',$search=''){
             //dd($request);
              $users=Session::get('admin_login_details');
              $user = Auth::user();   
        if(session()->has('admin_login')){  
            $uid=$user->id;
            $d=$this->commonModel->getunewserbyuserid($uid);
            $edetails=$this->commonModel->election_details_cons($d->st_code,'','','CEO'); 
            $sched='';  
             if($cand_status=='null') $cand_status=''; if($constituency=='null') $constituency=''; if($search=='null') $search='';
            if(isset($edetails)) {  $i=0;
              foreach($edetails as $ed) {  
                 $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
                 $const_type=$ed->ConstType;
               }
            } 
            
            $list1=$this->ceomodel->Allcandidatelist($d,$cand_status,$search,$constituency,$const_type);
           // dd( $list1);
        $str = '';
           if(count($list1)>0)
            {       
                 
            foreach($list1 as $lis) {
                $s= $this->commonModel->getnameBystatusid($lis->application_status); 
                  if($const_type=='AC') {
                            $const=$this->commonModel->getacbyacno($lis->st_code,$lis->ac_no);
                            $const_name=$const->AC_NAME;
                           
                          }
                        elseif($const_type=='PC') {
                            $const=$this->commonModel->getallacbypcno($lis->st_code,$lis->pc_no);
                         $const_name=$const->PC_NAME;  
                         } 
            echo  $str .= "<tr><td>".$lis->qrcode."</td><td>".$lis->cand_name."</td> <td>".$const_name."</td><td>".$s."</td> </tr>";
             }
                
              }else{
                
                echo $str .= '<tr><td colspan="4" style="color:red; text-align:center;"><b>No Record Found.</b></td></tr>';
            }
             
          }
          else {
              return redirect('/officer-login');
          }    
  
        }   // end dashboard function
  public function datewisereport(Request $request){
     
              $users=Session::get('admin_login_details');
              $user = Auth::user();   
        if(session()->has('admin_login')){  
            $uid=$user->id;
            $d=$this->commonModel->getunewserbyuserid($uid);
            $edetails=$this->commonModel->election_details_cons($d->st_code,'','','CEO'); 
            $sched=''; $search='';
             
            if(isset($edetails)) {$i=0;
              foreach($edetails as $ed) {  
                 $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               }
            }
           $list=$this->ceomodel->electiondetailsbystatecode($d->st_code);
            $fromdate = date('d-m-Y');
            $todate = date('d-m-Y');    
            $timeInterval = $fromdate.'~'.$todate;
            $fromdate = date('Y-m-d'); 
            $todate = date('Y-m-d');  
            if(!empty($list)){  $i=0;
               $allTypeCountArr = array();
                 foreach ($list as $lis) {   $i++; 
                        if($lis->CONST_TYPE=='AC') {
                          $const=$this->commonModel->getacbyacno($lis->st_code,$lis->CONST_NO);
                          $const_name=$const->AC_NAME;
                        }
                      if($lis->CONST_TYPE=='PC') {
                          $const=$this->commonModel->getallacbypcno($lis->st_code,$lis->CONST_NO);
                          $const_name=$const->PC_NAME;
                        }
              
                     
                $total =$this->ceomodel->gettotalnominationcnt($lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); // ALL list
                $totw=$this->ceomodel->gettotalnominationcntbystatus('5', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                $totr=$this->ceomodel->gettotalnominationcntbystatus('4', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                $totacc=$this->ceomodel->gettotalnominationcntbystatus('6', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                $totv=$this->ceomodel->gettotalnominationcntbystatus('2', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                $totrec=$this->ceomodel->gettotalnominationcntbystatus('3', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                $tota=$this->ceomodel->gettotalnominationcntbystatus('1', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                // $totfor=$this->ceomodel->gettotalnominationcntbystatus('formsubmited', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 

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
          return view('admin.ceo.datewisereport',['user_data' => $d,'list_const' => $list,'sched' => $sched,'allTypeCountArr'=>$allTypeCountArr,'timeInterval'=>$timeInterval]);
             
          }
          else {
              return redirect('/officer-login');
          }    
  
        }   // end dashboard function
  public function datewisereport_range(Request $request){
     
              $users=Session::get('admin_login_details');
              $user = Auth::user();   
        if(session()->has('admin_login')){  
            $uid=$user->id;
            $d=$this->commonModel->getunewserbyuserid($uid);
            $edetails=$this->commonModel->election_details_cons($d->ST_CODE,'','','CEO'); 
            $sched=''; $search='';
             
            if(isset($edetails)) {$i=0;
              foreach($edetails as $ed) {  
                 $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               }
            }
            $from_date = ($request->from_date);
            $to_date = ($request->to_date); 
            $const = trim($request->const);
           $list=$this->ceomodel->electiondetailsbystatecode($d->ST_CODE,$const);

            $timeInterval = $from_date.'~'.$to_date;  
             
            $fromdate = date('Y-m-d',strtotime($from_date));
            $todate = date('Y-m-d',strtotime($to_date));  

            if(!empty($list)){  $i=0;
               $allTypeCountArr = array();
                 foreach ($list as $lis) {   $i++; 
                        if($lis->CONST_TYPE=='AC') {
                          $const=$this->commonModel->getacbyacno($lis->ST_CODE,$lis->CONST_NO);
                          $const_name=$const->AC_NAME;
                        }
                      if($lis->CONST_TYPE=='PC') {
                          $const=$this->commonModel->getallacbypcno($lis->ST_CODE,$lis->CONST_NO);
                          $const_name=$const->PC_NAME;
                        }
              
                     
                $total =$this->ceomodel->gettotalnominationcnt($lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); // ALL list
                $totw=$this->ceomodel->gettotalnominationcntbystatus('5', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                $totr=$this->ceomodel->gettotalnominationcntbystatus('4', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                $totacc=$this->ceomodel->gettotalnominationcntbystatus('6', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                $totv=$this->ceomodel->gettotalnominationcntbystatus('2', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                $totrec=$this->ceomodel->gettotalnominationcntbystatus('3', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                $tota=$this->ceomodel->gettotalnominationcntbystatus('1', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 
                 //$totfor=$this->ceomodel->gettotalnominationcntbystatus('formsubmited', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 

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
               
          $str = '';
           if(count($allTypeCountArr)>0)
            {    $i=0;   $totalag=0;  $totalvg=0; $totalrecg=0; $totalwg=0; $totalaccg=0; $totalrg=0; $totalg=0;
                 
            foreach($allTypeCountArr as $list) {
               
                  $totalag=$totalag+$list['totala'];  $totalvg=$totalvg+$list['totalv']; $totalrecg=$totalrecg+$list['totalrec']; 
                  $totalwg=$totalwg+$list['totalw']; $totalrg=$totalrg+$list['totalr']; 
                  $totalaccg=$totalaccg+$list['totalacc']; $totalg=$totalg+$list['total'];          
              
              $str .= "<tr><td>".$list['const_no']."-".$list['const_name']."</td><td>".$list['totala']."</td> <td>".$list['totalv']."</td><td>".$list['totalrec']."</td><td>".$list['totalw']."</td><td>".$list['totalr']."</td><td>".$list['totalacc']."</td><td>".$list['total']."</td> </tr>";
             }
              echo $str .= "<tr><td>Total:- </td><td>".$totalag."</td> <td>".$totalvg."</td><td>".$totalrecg."</td><td>".$totalwg."</td><td>".$totalrg."</td><td>".$totalaccg."</td><td>".$totalg."</td> </tr>";     
              }else{
                
                echo $str .= '<tr><td colspan="7" style="color:red; text-align:center;"><b>No Record Found.</b></td></tr>';
            }
             
          }
          else {
              return redirect('/officer-login');
          }    
  
        }   // end dashboard function 
   public function candidate_finalize(Request $request){
           if(Auth::check()){
              $user = Auth::user();
               $uid=$user->id;
              $d=$this->commonModel->getunewserbyuserid($user->id);
              $d=$this->commonModel->getunewserbyuserid($uid);
              $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
              $st_code=$ele_details[0]->ST_CODE;
              $list=$this->ceomodel->Allcandidate_finaliselist($st_code);
             
            return view('admin.pc.ceo.candidate-finalise',['user_data' => $d,'lists' => $list,'st_code'=>$st_code]);
             
          }
          else {
              return redirect('/officer-login');
          }    
  
        }   // end candidate_finalize function
   public function candidate_definalize($ac_no,$actype){
            
            if(Auth::check()){
              $user = Auth::user();
               $uid=$user->id;
              $d=$this->commonModel->getunewserbyuserid($user->id);
              $d=$this->commonModel->getunewserbyuserid($uid);
              $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
              $st_code=$ele_details[0]->ST_CODE;   
             
            $list=$this->ceomodel->get_candidate_finalizeac($st_code,$ac_no,$actype);
             
            $date = Carbon::now();
            $currentTime = $date->format('Y-m-d H:i:s');
        
            $otp="123456"; //rand(100000,999999);
            $mob_message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Candidate Portal for de-finalized AC . Please enter the OTP to proceed.Your OTP will be valid till 30 minutes.Do not share this OTP,  Team ECI";
     
            $st = array('mobile_otp'=>$otp,'otp_time' => $currentTime);
            $i = DB::table('candidate_finalized_ac')->where('id',$list->id)->update($st);
            //$response = SmsgatewayHelper::sendOtpSMS($mob_message,$d->Phone_no); 

          return view('admin.pc.ceo.candidate-definalise',['user_data'=>$d,'ac_no'=>$ac_no,'st_code'=>$st_code,'actype'=>$actype,'list'=>$list,'otp'=>$otp,'otp_time'=>$currentTime]);
             
          }
          else {
              return redirect('/officer-login');
          }    
  
        }   // end candidate_finalize function
  function definalizevalidation(Request $request)
      {    
      if(Auth::check()){
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id);
            $definalized_message = $this->xssClean->clean_input(Check_Input($request->input('definalized_message')));
            if(empty($definalized_message)) {
             \Session::flash('error_messsage', 'Message empty');
                  return Redirect::to('/pcceo/candidate-finalize');
              }
              $id = $this->xssClean->clean_input(Check_Input($request->input('id')));
              $cons_no =$this->xssClean->clean_input(Check_Input($request->input('ac_no')));
              $st_code =$this->xssClean->clean_input(Check_Input($request->input('st_code')));
              $actype = $this->xssClean->clean_input(Check_Input($request->input('actype')));

          //   $this->validate(
          //         $request, 
          //             [
          //              //'verifyotp' => 'required|numeric',
          //              'definalized_message' => 'required',
          //              ],
          //             [
          //              'verifyotp.required' => 'Please enter your valid Otp', 
          //              'verifyotp.numeric' => 'Please enter your valid Otp',
          //              'definalized_message.required' => 'Please enter message',
          //              ]);
          //  $verifyotp = Check_Input($request->input('verifyotp'));
          //  $definalized_message = Check_Input($request->input('definalized_message'));
          //  $id = Check_Input($request->input('id'));
          //  $cons_no = Check_Input($request->input('ac_no'));
          //  $st_code = Check_Input($request->input('st_code'));
          //  $actype = Check_Input($request->input('actype'));
          // // $ELECTION_ID = Check_Input($request->input('ELECTION_ID'));
          //  $otp = Check_Input($request->input('otp'));
          //  $otp_time = Check_Input($request->input('otp_time'));
            
          //  $date = Carbon::now()->subMinutes(30);
          //       $currentTime = $date->format('Y-m-d H:i:s');
           
          //  if($otp!=$verifyotp) {
          //    \Session::flash('ro_opt_messsage', 'Your Otp Message Invalide');
          //         return Redirect::to('/ceo/candidate-definalize/'.$cons_no.'/'.$actype);
          //  }
          // if($otp_time<$currentTime) {
          //    \Session::flash('ro_opt_messsage', 'Your Otp time Expair');
          //          return Redirect::to('/ceo/candidate-definalize/'.$cons_no.'/'.$actype);
          //  }
           $ins_data = array('finalized_ac'=>'0','definalized_message'=>$definalized_message,'definalize_date'=>date('Y-m-d'));
            $this->ceomodel->definalize_candidate_ac($st_code,$cons_no,$actype,$ins_data);
           // dd('hello');
             \Session::flash('success_mes', 'De-finalize Successfully');
                 return Redirect::to('/pcceo/candidate-finalize');
             }
          else {
            return Redirect::to('/officer-login');
              } 
      }
    
   
  public function electorspollingstationList(Request $request){ 
     //dd($request->all());
     if(Auth::check()){
      $user = Auth::user();
      $d=$this->commonModel->getunewserbyuserid($user->id);
      $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
      $all_pc=$this->commonModel->getpcbystate($d->st_code);
      return view('admin.pc.ceo.electors-pollingstationlist',['user_data' => $d,'ele_details' => $ele_details,'all_pc' => $all_pc]);
      }else {
       return redirect('/officer-login');
     }   
    }   // end electorspollingstation List function
  
 public function getaclistbyPC(request $request){ 
   //dd($request->all()); 
  
    $pc_no = $request['pc_no'];
 
  if(Auth::check()){ 
    $user = Auth::user();
    $d=$this->commonModel->getunewserbyuserid($user->id);
    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
  $election_id=$ele_details[0]->ELECTION_ID;
  // dd($election_id);
   //dd($ele_details);
   if($pc_no!=90){ 
      // $election_id=$ele_details[$request->pc_no-1]->ELECTION_ID;
      // $CONST_TYPE=$ele_details[$request->pc_no-1]->CONST_TYPE;
      $acdata = $this->pcceoreportModel->getAcByPC($d->st_code,$pc_no,$election_id);     
     
     }else{ 
      foreach($ele_details as $ele_detailsList){
     // $election_id=$ele_detailsList->ELECTION_ID;
     }
     $electorSummary = $this->pcceoreportModel->getelectorssummarybyState($d->st_code,$election_id);     
        
     }
//dd($acdata);
     $html='';
     $j=0;
     if(!empty($acdata)){
     $html.='<thead>
       <tr>
        <th colspan="3"> AC No & AC Name </th>
        <th colspan="4">General Electors</th>
        <th colspan="4">Service Electors</th>
        <th colspan="3">Polling Stations</th>
       </tr>

        <tr>
        <th size="2">S.No.</th>
        <th>AC No</th>
        <th>AC Name</th>
        <th size="2">Male</th>
        <th size="2">Female</th>
        <th size="2">Third Gender</th>
        <th size="2">Total</th>

        <th size="2">Male</th>
        <th size="2">Female</th>
        <th size="2">Third Gender</th>
        <th size="2">Total</th>

        <th size="2">Regular</th>
        <th size="2">Auxillary</th>
        <th size="2">Total</th>
        </tr>
    </thead>';
      
       foreach($acdata as $acdataList){ 
        $j++;  
      $html.='<input type="hidden" name="pc_no" value="'.$acdataList->pc_no.'">
              <input type="hidden" name="st_code" value="'.$acdataList->st_code.'">';
      $html.='<tr>
        <td><input type="hidden"   name=""  value="'.$j.'"  maxlength="5" readonly="readonly" size="2"><span>'.$j.'</span></td> 
        <td><input type="hidden"   name="ac_no[]"  value="'.$acdataList->ac_no.'"  maxlength="8" readonly="readonly"><span>'.$acdataList->AC_NO.'</span></td> 
        <td><input type="hidden"  name="ac_name[]"  value="'.$acdataList->AC_NAME.'" maxlength="8"  readonly="readonly"><span>'.$acdataList->AC_NAME.'</span></td> 
        <td><input type="text"    name="gen_male[]" id="gen_male" value="'.$acdataList->gen_m.'"   size="7" readonly="readonly"></td> 
        <td><input type="text"    name="gen_female[]" id="gen_female" value="'.$acdataList->gen_f.'"  size="7" readonly="readonly"> </td>         
        <td><input type="text"    name="gen_third[]" id="gen_third" value="'.$acdataList->gen_o.'" size="7"  readonly="readonly"> </td>          
        <td><input type="text"   name="gen_total[]" id="gen_total" value="'.$acdataList->gen_t.'" size="7"  readonly="readonly"> </td>  

        <td><input type="text" name="ser_male[]" id="ser_male" value="'.$acdataList->ser_m.'" size="7"   readonly="readonly"> </td> 
        <td><input type="text" name="ser_female[]" id="ser_female" value="'.$acdataList->ser_f.'" size="7"   readonly="readonly"> </td>          
        <td><input type="text" name="ser_third[]" id="ser_third" value="'.$acdataList->ser_o.'" size="7" readonly="readonly"> </td> 
        <td><input type="text" name="ser_total[]" id="ser_total" value="'.$acdataList->ser_t.'" size="7" readonly="readonly"> </td> 
        
        <td><input type="text" name="regular[]" id="regular" value="'.$acdataList->polling_reg.'" size="7" readonly="readonly"> </td> 
        <td><input type="text" name="auxillary[]" id="auxillary" value="'.$acdataList->polling_auxillary.'" size="7"   readonly="readonly"> </td> 
        <td><input type="text" name="polling_total[]" id="polling_total" value="'.$acdataList->polling_total.'" size="7"  readonly="readonly"></span> </td> 
         </tr>';
        }
       }elseif (!empty($electorSummary)) {
         # code...
         $html.='<thead>
         <tr>
          <th colspan="3"> PCNo & PC Name </th>
          <th colspan="4">General Electors</th>
          <th colspan="4">Service Electors</th>
          <th colspan="3">Polling Stations</th>
         </tr>

          <tr>
          <th size="2">S.No.</th>
          <th>PC No</th>
          <th>PC Name</th>
          <th size="2">Male</th>
          <th size="2">Female</th>
          <th size="2">Third Gender</th>
          <th size="2">Total</th>

          <th size="2">Male</th>
          <th size="2">Female</th>
          <th size="2">Third Gender</th>
          <th size="2">Total</th>

          <th size="2">Regular</th>
          <th size="2">Auxillary</th>
          <th size="2">Total</th>
          </tr>
      </thead>';
         foreach($electorSummary as $acdataSummary){ 
          $j++;  
         $html.='<input type="hidden" name="pc_no" value="'.$acdataSummary->PC_NO.'">
                <input type="hidden" name="st_code" value="">';
         $html.='<tr>
          <td><input type="hidden"   name=""  value="'.$j.'"  maxlength="5" readonly="readonly" size="2"><span>'.$j.'</span></td> 
          <td><input type="hidden"   name="pc_no[]"  value="'.$acdataSummary->PC_NO.'"  readonly="readonly"><span>'.$acdataSummary->PC_NO.'</span></td> 
          <td><input type="hidden"  name="pc_name[]"  value="'.$acdataSummary->PC_NAME.'"   readonly="readonly"><span>'.$acdataSummary->PC_NAME.'</span></td> 
          <td><input type="text"    name="gen_male[]" id="gen_male" value="'.$acdataSummary->total_gen_m.'" size="7" readonly="readonly"></td> 
          <td><input type="text"    name="gen_female[]" id="gen_female" value="'.$acdataSummary->total_gen_f.'" size="7" readonly="readonly"> </td>         
          <td><input type="text"    name="gen_third[]" id="gen_third" value="'.$acdataSummary->total_gen_o.'" size="7"  readonly="readonly"> </td>          
          <td><input type="text"   name="gen_total[]" id="gen_total" value="'.$acdataSummary->total_gen_t.'" size="7"  readonly="readonly"> </td>  
  
          <td><input type="text" name="ser_male[]" id="ser_male" value="'.$acdataSummary->total_ser_m.'" size="7"   readonly="readonly"> </td> 
          <td><input type="text" name="ser_female[]" id="ser_female" value="'.$acdataSummary->total_ser_f.'" size="7"  readonly="readonly"> </td>          
          <td><input type="text" name="ser_third[]" id="ser_third" value="'.$acdataSummary->total_ser_o.'" size="7"   readonly="readonly"> </td> 
          <td><input type="text" name="ser_total[]" id="ser_total" value="'.$acdataSummary->total_ser_t.'" size="7"  readonly="readonly"> </td> 
          
          <td><input type="text" name="regular[]" id="regular" value="'.$acdataSummary->total_polling_reg.'" size="7"  readonly="readonly"> </td> 
          <td><input type="text" name="auxillary[]" id="auxillary" value="'.$acdataSummary->total_polling_auxillary.'" size="7"   readonly="readonly"> </td> 
          <td><input type="text" name="polling_total[]" id="polling_total" value="'.$acdataSummary->total_polling_total.'" size="7" readonly="readonly"></span> </td> 
           </tr>';
          }
       }
        return $html;
   }   
} 
      public function changepassword(request $request){ 
        if(Auth::check()){ 
          $user = Auth::user();
          $d=$this->commonModel->getunewserbyuserid($user->id); 
          $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
         
          return view('admin.pc.ceo.change-password', ['user_data' => $d]);
        }
      } //@end changepassword function

 
  public function changePasswordStore(request $request){ 
    if(Auth::check()){ 
          $user = Auth::user();
          $d=$this->commonModel->getunewserbyuserid($user->id); 
          //$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          //dd($user);
          if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
        }
        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
            //Current password and new password are same
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
  
    
  
  public function officerList(Request $request){
    if(Auth::check()){
     $user = Auth::user();
     $d=$this->commonModel->getunewserbyuserid($user->id);
     $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
     
     $officerlist =DB::table('officer_login')->where('st_code',$d->st_code)->whereIn('role_id', [5,18,20])->get();
        return view('admin.pc.ceo.officer-details',['user_data' => $d,'ele_details' => $ele_details,'officerlist' => $officerlist]);
  }
  else {
      return redirect('/officer-login');
    }   
  }   // end candidateListbyPC function  
   
 
  public function officerProfileUpdate(Request $request,$id='') {
    
  //  dd($request->all());
 
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
                  'Phone_no' => 'required|string|min:10|numeric|digits:10|unique:officer_login',

                 ],
                [
                 'name.required' => 'Please enter your name', 
                 'email.required' => 'Please enter your email',
                 'Phone_no.required' => 'Please enter mobile number',
                 'Phone_no.digits' => 'Please enter 10 digit mobile number',
                 'Phone_no.unique' => 'This mobile number already exist',
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
                   return redirect('/pcceo/officer-details');
                      }
             } else {
              \Session::flash('success_error', 'You have some Error!. '); 
              return redirect('/pcceo/officer-details');
              //  return redirect()->back()->withErrors($validator, 'error');
            }
        } else {
            $decryptedid = decrypt($id);
            $rec=getById('officer_login','id',$decryptedid);
            return view('admin.pc.ceo.officer-profile')->with(array('user_data' => $d,'getofficerdetails' => $rec));
        }
    } else {
        return redirect('/officer-login');
    }
}


  
   
  public function psinfoList(Request $request){
    //dd($request->all());
    
    if(Auth::check()){
     $user = Auth::user();
     $d=$this->commonModel->getunewserbyuserid($user->id);
     $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
     $all_state=$this->commonModel->getallstate();
     $all_dist=$this->commonModel->getalldistrictbystate($d->st_code);
     $all_ac=$this->commonModel->getacbystate($d->st_code);
    // $officerlist =DB::table('officer_login')->where('st_code',$d->st_code)->get();
    // print_r($officerlist);  die;
    return view('admin.pc.ceo.psinfo',['user_data' => $d,'ele_details' => $ele_details,'all_state' => $all_state,'all_dist' => $all_dist,'all_ac' => $all_ac]);
  }
  else {
      return redirect('/officer-login');
    }   
  }   // end candidateListbyPC function  
   
 
  public function getaclist(request $request){
    //dd($request->all());
    if(Auth::check()){
      $user = Auth::user();
      $d=$this->commonModel->getunewserbyuserid($user->id);
 
    $district = $request->input('district');
    $stcode = $d->st_code;
    $acdata = $this->commonModel->getAcByst_test($stcode,$district);
    }
    return $acdata; 
  }  
  
  public function psresultList(Request $request){
    //dd($request->all());
    $url = 'http://eronetservices.ecinet.in/api/ERONet/GetPSDetailsAcWise';
    $st_code=$request->st_code;
    $ac_no=$request->ac;
   // $st_code='S11';
    //$ac_no='2';
    //$secureKey = "ABCD1234#123521GISTECIKEY";
    $method='POST';
    $resultData=$this->pcceoreportModel->ComputeSha512Hash($st_code,$ac_no);
    // dd($resultData);
    $data=array(
      "ST_CODE"=> $st_code,
      "ac_no"=> $ac_no,
      "Client_HASHCode"=> $resultData,
      );
      $data_string = json_encode($data);
      $jsonResult = $this->pcceoreportModel->callAPI($method, $url, $data_string);
      $dist_no=$request->district;
    if(Auth::check()){ 
     $user = Auth::user();
     $d=$this->commonModel->getunewserbyuserid($user->id);
     $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
     
    
    return view('admin.pc.ceo.psinfo',['user_data' => $d,'dist_no' => $dist_no,'ac_no' => $ac_no,'st_code' => $st_code,'jsonResult' => $jsonResult]);
  }
  else {
      return redirect('/officer-login');
    }  
  }   // end candidateListbyPC function  
   
  
  // download cantesting candidate
  public function download_contesting_candidate($cons_no)
          {
          if(Auth::check()){
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id);
            $st=$d->st_code;
          
        $candn = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
        ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
        ->where('candidate_nomination_detail.st_code','=',$st)->where('candidate_nomination_detail.pc_no','=',$cons_no) 
        ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
        //->where('candidate_nomination_detail.finalize','=','1')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        //->where('m_party.PARTYTYPE','=','N')
        ->orderBy('candidate_nomination_detail.new_srno', 'asc')
        ->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES','candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode','candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.candidate_residence_acno')->get();
      $a='N'; $a1='S';
      $cands = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
        ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
        ->where('candidate_nomination_detail.st_code','=',$st)->where('candidate_nomination_detail.pc_no','=',$cons_no) 
        ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
        //->where('candidate_nomination_detail.finalize','=','1')
         ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where(function($query1) use ($a,$a1){
                        $query1->where('m_party.PARTYTYPE','=',$a)
                        ->orWhere('m_party.PARTYTYPE','=',$a1);
                    })
        ->orderBy('candidate_nomination_detail.new_srno', 'asc')
        ->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES','candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode','candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.candidate_residence_acno')->get();


      $candu = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
        ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
        ->where('candidate_nomination_detail.st_code','=',$st)->where('candidate_nomination_detail.pc_no','=',$cons_no) 
        ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
        //->where('candidate_nomination_detail.finalize','=','1')
         ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('m_party.PARTYTYPE','=','U')->orderBy('candidate_nomination_detail.new_srno', 'asc')
        ->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES','candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode','candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.candidate_residence_acno')->get();

        $candz = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
        ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
        ->where('candidate_nomination_detail.st_code','=',$st)->where('candidate_nomination_detail.pc_no','=',$cons_no) 
        ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
       // ->where('candidate_nomination_detail.finalize','=','1')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('m_party.PARTYTYPE','=','Z')
        ->orderBy('candidate_nomination_detail.new_srno', 'asc')
        ->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES','candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode','candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.candidate_residence_acno')->get();


         $pc=''; $ac='';
           $pc=getpcbypcno($st,$cons_no);
           $state=getstatebystatecode($st);
         
          view()->share('candn',$candn,'cands',$cands,'candu',$candu,'candz',$candz,'st',$state,'pc',$pc);
           $pdf = MPDF::loadView('admin.pc.nomination.cantesting-candidate',compact('candn',$candn,'cands',$cands,'candu',$candu,'candz',$candz,'state',$state,'pc',$pc));
           return $pdf->download('cantesting-candidate.pdf');
     
           return view('cantesting-candidate');
              
            
       }
          else {
                return redirect('/officer-login');
              }
        }
 
  //end 
}  // end class