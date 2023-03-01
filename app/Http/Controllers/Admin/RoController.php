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
		use App\adminmodel\CandidateModel;
		use App\adminmodel\PartyMaster;
		use App\adminmodel\CandidateNomination;
		use App\Helpers\SmsgatewayHelper;
		use App\adminmodel\ROModel;
		use App\Classes\xssClean;
		//use Spatie\MixedContentScanner\MixedContentScanner;
class RoController extends Controller
{
    //
   public function __construct()
        {   
			$this->middleware('adminsession');
			$this->middleware(['auth:admin','auth']);
			$this->middleware('ro');
			$this->commonModel = new commonModel();
			$this->CandidateModel = new CandidateModel();
			$this->romodel = new ROModel();
			$this->xssClean = new xssClean;

		}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
 	protected function guard(){
        return Auth::guard('admin');
    	}

    public function index()
	    {      
	     $users=Session::get('admin_login_details');
	     $user = Auth::user();
	     if(session()->has('admin_login')){  
            $uid=$users->id;
            $d=$this->commonModel->getunewserbyuserid($uid);
            $module=$this->commonModel->getallmodule();
             
            if($d->officerlevel=="AC")
            	$edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,$d->officerlevel); 
            elseif($d->officerlevel=="PC")
            	$edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->PC_NO,$d->officerlevel);
            if(isset($edetails->ScheduleID))
             	$sched=$this->commonModel->getschedulebyid($edetails->ScheduleID);
            else 
            	$sched='';
            // dd($sched);
            
 			    return view('admin.ro.dashboard', ['user_data' => $d,'module' => $module,'sched' => $sched,'showpage'=>'candidate','edetails'=>$edetails]);	           
	        }
	        else {
	              return redirect('/officer-login');
	        	  }
	    }  // end index function

		 //=================================================25/07/18
        public function qrscanfunction($qr='')
		    { 
		     if(Auth::check()){
		            $user = Auth::user();
		            $d=$this->commonModel->getunewserbyuserid($user->id); 
		            //if($qr=='') $qr='123456';
		            return view('admin.ro.qrscan', ['user_data' => $d,'qr' => $qr,'showpage'=>'candidate']);
	             }
	        else {
	              return redirect('/officer-login');
	        	  }
		     
		    }  // end  function 
        public function candidateinformation(Request $request)
		    {
		     if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		        $qrcode = $this->xssClean->clean_input($request->input('qrcode'));
                
		        $validator = Validator::make($request->all(), ['qrcode' => 'required',],
			                    [
			                      'qrcode.required' => 'Please enter QR code ',
			                    ]);

        	if ($validator->fails()) {
            		return redirect('/ro/qrscan/'. $qrcode)
                        ->withErrors($validator)
                        ->withInput();
        			}
	            
		    $shares = DB::table('candidate_nomination_detail')
		    		->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id') 
		    ->where('candidate_nomination_detail.qrcode', $qrcode)
    		->select('candidate_personal_detail.candidate_id','candidate_personal_detail.*', 'candidate_nomination_detail.*')->first();  
            if(empty($shares)){
            	\Session::flash('error_mes', 'invalid QR Code');
            	return redirect('/ro/qrscan/'. $qrcode);
            }
    		$pc=''; $ac='';
	    	$state=$this->commonModel->getstatebystatecode($shares->ST_CODE);
	    	$dist=$this->commonModel->getdistrictbydistrictno($shares->ST_CODE,$shares->district_no);
	    	 
	    			$ac=$this->commonModel->getacbyacno($shares->ST_CODE,$shares->ac_no);
	    	if($shares->pc_no!=NULL)
	    			$pc=$this->commonModel->getpcbypcno($shares->ST_CODE,$shares->pc_no);
			 
			if(!empty($pc))
					return view('admin.ro.candidateinformation', ['user_data' => $d,'caddata'=>$shares,'state'=>$state,'dist'=>$dist,'ac'=>$ac,'pc'=>$pc,'showpage'=>'candidate']);
			elseif(!empty($ac))
					return view('admin.ro.candidateinformationac', ['user_data' => $d,'caddata'=>$shares,'state'=>$state,'dist'=>$dist,'ac'=>$ac,'showpage'=>'candidate']);

		         }
	        else {
	              return redirect('/officer-login');
	        	  }
		     
		    }  // end  function   
public function candidatevalidation(Request $request)
		    {  //dd($request);
		     if(Auth::check()){
		            $user = Auth::user();
		           $d=$this->commonModel->getunewserbyuserid($user->id);
				    
		      if(empty($request->input('qrcode')))
		      	   {
		      		$qrcode = $this->xssClean->clean_input($request->old('qrcode'));
		    		$candidate_id = $this->xssClean->clean_input($request->old('candidate_id'));
		    	   }
		      else { 
		    		$qrcode = $this->xssClean->clean_input($request->input('qrcode'));
		    		$candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
		    		}
	       if(!empty($qrcode) and !empty($candidate_id)) {      
	        $shares = DB::table('candidate_personal_detail')
    				->leftjoin('candidate_nomination_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
    				->where('candidate_nomination_detail.qrcode', $qrcode)
    				->select('candidate_personal_detail.*','candidate_nomination_detail.*')->first();

		          $nom_stsus = array('application_status'=>'2'); 
		          $i = DB::table('candidate_nomination_detail')->where('candidate_id', $candidate_id)->where('qrcode', $qrcode)->update($nom_stsus);
		          //dd($shares);
                if(!empty($shares)){
				$this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$shares->nom_id,'application_status','1','2',request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));
                  }
				$state=$this->commonModel->getstatebystatecode($shares->ST_CODE);
	    	    $ac=$this->commonModel->getacbyacno($shares->ST_CODE,$shares->ac_no);
				 
				return view('admin.ro.decisionbyro', ['user_data' => $d,'caddata'=>$shares,'showpage'=>'candidate']);
		         }
		       else{
		       	return redirect('/ro/applicant');
		        }
		     }
	        else {
	              return redirect('/officer-login');
	        	  }
		     
		    }  // end  function 
public function decisionvalidate(Request $request)
		    {
		     if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		     	$input = $request->all(); 
		  		$validator = Validator::make($request->all(), 
		    		[
                      'nomination_srno' => 'required',
                      'nomination_submittedby' => 'required',
                    ],
                    [
                      'nomination_srno.required' => 'Please enter valid nomination serial number ',
                      'nomination_submittedby.required' => 'Please enter candidate / properser name', 
                    ]);
		     
        	if ($validator->fails()) { 
            				return redirect('/ro/candidatevalidation')
                        					->withErrors($validator)
                        					->withInput();
        					}
		          $candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
                  $qrcode = $this->xssClean->clean_input($request->input('qrcode')); 
                  $nomination_srno = $this->xssClean->clean_input($request->input('nomination_srno'));
                  $nomination_hour = $this->xssClean->clean_input($request->input('nomination_hour'));
                  $nomination_date = $this->xssClean->clean_input($request->input('nomination_date'));  
                  $nomination_submittedby = $this->xssClean->clean_input($request->input('nomination_submittedby'));
                  
		  $nom_data = array('nomination_papersrno'=>$nomination_srno,'rosubmit_time'=>date("H:m:s",strtotime($nomination_hour)),'rosubmit_date'=>date("Y-m-d",strtotime($nomination_date)),'nomination_submittedby'=>$nomination_submittedby,'updated_at'=>date("Y-m-d",strtotime($nomination_date)) ." ".date("H:m:s",strtotime($nomination_hour)),'updated_by'=>$d->officername); 
		      
		           
		$i = DB::table('candidate_nomination_detail')->where('candidate_id', $candidate_id)->where('qrcode', $qrcode)->update($nom_data);
		          // $i = DB::table('candidate_personal_detail')->where('candidate_id', $candidate_id)->update($nom_stsus);
         
		    $qrcode = $this->xssClean->clean_input($request->input('qrcode'));
		    $candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
	        
	   if($d->officerlevel=="AC")
            	$edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,$d->officerlevel,''); 
        elseif($d->officerlevel=="PC")
            	$edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->PC_NO,$d->officerlevel,'');
           //print_r($edetails); 
            if(isset($edetails->ScheduleID))
             	$sched=$this->commonModel->getschedulebyid($edetails->ScheduleID);
            else 
            	$sched='';
	    $shares = DB::table('candidate_nomination_detail')
    			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
    			->where('candidate_nomination_detail.qrcode', $qrcode)
    			->select('candidate_personal_detail.cand_name','candidate_nomination_detail.*')->first();
    	$cons='';
    	 //dd($sched);
		   if(!empty($shares->ac_no)) {
		        	$cons=$this->commonModel->getacbyacno($shares->ST_CODE,$shares->ac_no);
		            $con_name=$cons->AC_NAME; $ac="Assembly";
		        }
		   if(!empty($shares->pc_no)) {
    				$cons=$this->commonModel->getpcbypcno($shares->ST_CODE,$shares->pc_no);
		 			$con_name=$cons->PC_NAME; $ac="Parliamentary";
		 	 }
	          	
    	$this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$shares->nom_id,'nomination_papersrno','NA',$nomination_srno,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));
        
	    return view('admin.ro.finalreceipt',['user_data'=>$d,'caddata'=>$shares,'con_name'=>$con_name,'ac'=>$ac,'showpage'=>'candidate','scrutiny_date'=>$sched->DT_SCR_NOM]);
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
		     
	}  // end  function  print_receipt

 public function print_receipt(Request $request)
		    {
		     if(Auth::check()){
		            $user = Auth::user();
		            $d=$this->commonModel->getunewserbyuserid($user->id);
		      $this->validate(
                $request, 
                    [
                      'scrutiny_time' => 'required',
                      'scrutiny_date' => 'required',
                     // 'affidavit'=>'required',
                      //'place'=>'required',
                    ],
                    [
                      'scrutiny_time.required' => 'Please enter valid time ',
                      'scrutiny_date.required' => 'Please enter valid date', 
                     // 'place.required' => 'Please enter Scutiny Place', 
                      //'affidavit.required' => 'Please check', 
                    ]);
		          $candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
                  $scrutiny_time = $this->xssClean->clean_input($request->input('scrutiny_time'));
                  $scrutiny_date = $this->xssClean->clean_input($request->input('scrutiny_date'));
                  $qrcode = $this->xssClean->clean_input($request->input('qrcode'));

                  $place = $this->xssClean->clean_input($request->input('place'));
                  $fdate = $this->xssClean->clean_input($request->input('fdate'));
                 

		           $nom_data = array('scrutiny_time'=>$scrutiny_time,'scrutiny_date'=>$scrutiny_date,'place'=>$place,'fdate'=>$fdate,'application_status'=>'3'); 
		           
		           $i = DB::table('candidate_nomination_detail')->where('candidate_id', $candidate_id)->where('qrcode',$qrcode)->update($nom_data);
		            
		          
		          
	              $cand = DB::table('candidate_personal_detail')->where('candidate_id',$candidate_id)->first();
	              $nom = DB::table('candidate_nomination_detail')->where('candidate_id',$candidate_id)->where('qrcode',$qrcode)->first();
	               $this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$nom->nom_id,'scrutiny_date','NA',$scrutiny_date,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));
	              $pc=''; $ac='';
		          if(!empty($nom->ac_no))
		        	$ac=$this->commonModel->getacbyacno($nom->ST_CODE,$nom->ac_no);
		        if(!empty($nom->pc_no))
    				$pc=$this->commonModel->getpcbypcno($nom->ST_CODE,$nom->pc_no);
				
				$state=$this->commonModel->getstatebystatecode($nom->ST_CODE);
	    	    $ac=$this->commonModel->getacbyacno($nom->ST_CODE,$nom->ac_no);
				//$sub="Your Nomination Application Status change";
				 
			/*$Mob_otp="Dear ".$cand->cand_name." your Nomination Application of the constituency ".$ac->AC_NAME." of state ".$state->ST_NAME." is Receipt Generated by returning officer";
 
		 $html ="<html>
					<body>
					<p>Dear ".$cand->cand_name.",<br/><br/></p>
					<p>Your Nomination Application Serial No. of nomination paper <b> ".$nom->nomination_papersrno."</b> ,  Form is Submitted on date ".date("d/m/Y")." for the General / Bye elections of the constituency <b> ".$ac->AC_NAME."</b> of state  <b> ".$state->ST_NAME."</b>. Your Scutiny Date <b> ".date("d-m-Y",strtotime($nom->scrutiny_date))."</b> and time <b> ".$nom->scrutiny_time."</b> is <b>Receipt Generated </b>by returning officer</p>
					  <p><br/><br/>Regards,<br/>
						Returning Officer. <br>
						Election Commission of India<br/>
					  </p>
					</body>
					</html>";  
				CandidateECIMail($cand->cand_email,$cand->cand_name,$html);
				$response = SmsgatewayHelper::sendOtpSMS($Mob_otp,$cand->cand_mobile); 
				*/
			dd("hello");
	          return view('admin.ro.printreceipt', ['user_data' => $d,'caddata'=>$cand,'nomination'=>$nom,'ac'=>$ac,'pc'=>$pc,'showpage'=>'candidate']);
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
		     
		    }  // end  function   
    public function reprint_receipt($nom_id)
		    {  
		     if(Auth::check()){
		            $user = Auth::user();
		            $d=$this->commonModel->getunewserbyuserid($user->id);
		       $n = DB::table('candidate_nomination_detail')->where('nom_id',$nom_id)->first();
		       $c = DB::table('candidate_personal_detail')->where('candidate_id',$n->candidate_id)->first();
	            
	              $pc=''; $ac='';
		          if(!empty($n->ac_no))
		        	$ac=$this->commonModel->getacbyacno($n->ST_CODE,$n->ac_no);
		        if(!empty($n->pc_no))
    				$pc=$this->commonModel->getpcbypcno($n->ST_CODE,$n->pc_no);
				 
	    	    $ac=$this->commonModel->getacbyacno($n->ST_CODE,$n->ac_no);
				 
			return view('admin.ro.printreceipt', ['user_data' => $d,'caddata'=>$c,'nomination'=>$n,'ac'=>$ac,'pc'=>$pc,'showpage'=>'candidate']);
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
		     
		    }  // end  function 
	 public function finalized_application(Request $request)
		    {
		     if(Auth::check()){
		            $user = Auth::user();
		            $d=$this->commonModel->getunewserbyuserid($user->id);
		      $this->validate(
                $request, 
                    [
                      'scrutiny_time' => 'required',
                      'scrutiny_date' => 'required',
                    ],
                    [
                      'scrutiny_time.required' => 'Please enter valid time ',
                      'scrutiny_date.required' => 'Please enter valid date', 
                    ]);
		          $candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
                  $scrutiny_time = $this->xssClean->clean_input($request->input('scrutiny_time'));
                  $scrutiny_date = $this->xssClean->clean_input($request->input('scrutiny_date'));
                  $qrcode = $this->xssClean->clean_input($request->input('qrcode'));
               
                  $place = $this->xssClean->clean_input($request->input('place'));
                  $fdate = $this->xssClean->clean_input($request->input('fdate'));
                 

		           $nom_data = array('scrutiny_time'=>$scrutiny_time,'scrutiny_date'=>$scrutiny_date,'place'=>$place,'fdate'=>$fdate); 
		           
		           $i = DB::table('candidate_nomination_detail')->where('candidate_id', $candidate_id)->where('qrcode',$qrcode)->update($nom_data);
		            
		          
		          
	              $cand = DB::table('candidate_personal_detail')->where('candidate_id',$candidate_id)->first();
	              $nom = DB::table('candidate_nomination_detail')->where('candidate_id',$candidate_id)->where('qrcode',$qrcode)->first();
	               $this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$nom->nom_id,'scrutiny_date','NA',$scrutiny_date,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));
	               Session::put('candidate_id', $candidate_id);
                   Session::put('nom_id', $nom->nom_id);
	              $pc=''; $ac='';
		          if(!empty($nom->ac_no))
		        	$ac=$this->commonModel->getacbyacno($nom->ST_CODE,$nom->ac_no);
		        if(!empty($nom->pc_no))
    				$pc=$this->commonModel->getpcbypcno($nom->ST_CODE,$nom->pc_no);
				
				$state=$this->commonModel->getstatebystatecode($nom->ST_CODE);
	    	    $ac=$this->commonModel->getacbyacno($nom->ST_CODE,$nom->ac_no);
			 
                /*$date = Carbon::now();
                $currentTime = $date->format('Y-m-d H:i:s');
			$mobile_otp = rand(100000,999999);
            		$dat = array('otpvalue' => $mobile_otp,'otp_time' => $currentTime,); 
            $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom->nom_id)->update($dat);

              if($d->Phone_no!=""){
                 
                $mob_message = "Dear Sir/Madam, your OTP is ".$mobile_otp." for ECI Candidate Portal. Please enter the OTP to proceed.Your OTP will be valid till 10 minutes.Do not share this OTP Team ECI";
                      // $response = SmsgatewayHelper::sendOtpSMS($mob_message, $d->Phone_no);
                }	 
			   */
	          return view('admin.ro.finalizedapplication', ['user_data' => $d,'caddata'=>$cand,'nomination'=>$nom,'ac'=>$ac,'pc'=>$pc,'showpage'=>'candidate']);
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
		    }  // end  function 

     public function roverify_otp(Request $request)
		    { 
		    	//print_r($request->input());
		     if(Auth::check()){
		            $user = Auth::user();
		            $d=$this->commonModel->getunewserbyuserid($user->id);
		       
		          $candidate_id = $request->input('candidate_id');
                  $nom_id = $request->input('nom_id');
                 //Session::put('candidate_id', $candidate_id);
                 //Session::put('nom_id', $nom_id);
                $date = Carbon::now();
                $currentTime = $date->format('Y-m-d H:i:s');
				$mobile_otp = rand(100000,999999);
            $dat = array('otpvalue' => $mobile_otp,'otp_time' => $currentTime,); 
            $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($dat);

              if($d->Phone_no!=""){  
                $mob_message = "Dear Sir/Madam, your OTP is ".$mobile_otp." for ECI Candidate Portal. Please enter the OTP to proceed.Your OTP will be valid till 10 minutes.Do not share this OTP Team ECI";
                //echo $mob_message; echo $d->Phone_no; echo $nom_id;
                $response = SmsgatewayHelper::sendOtpSMS($mob_message,$d->Phone_no); 
                 
                }	 
			  //dd($response);
	          return view('admin.ro.roverify-otp',['user_data' => $d,'candidate_id'=>$candidate_id,'nom_id'=>$nom_id,'mobile_otp'=>$mobile_otp,'showpage'=>'candidate']);
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
		    }  // end  function 
	public function verify_finalize_otp(Request $request)
		    {  
		     if(Auth::check()){
		            $user = Auth::user();
		            $d=$this->commonModel->getunewserbyuserid($user->id);
		            $candidate_id =Session::get('candidate_id');
                  $nom_id =Session::get('nom_id');
                 // echo $candidate_id; echo $nom_id;
                  //dd("hello");
		       		$this->validate(
                $request, 
                    [
                      'otpvalue' => 'required_with:mobile_otp|same:mobile_otp',
                    ],
                    [
                      'otpvalue.required_with' => 'Please enter valid OTP ',
                      'otpvalue.same' => 'Please enter valid Mobile OTP ',
                    ]);
		          
                  
               $n_data = array('application_status'=>'3'); 
		       // echo $nom_id;  echo $candidate_id;   
		        $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($n_data); 
		      $n = DB::table('candidate_nomination_detail')->where('nom_id',$nom_id)->first();
		      $c = DB::table('candidate_personal_detail')->where('candidate_id',$candidate_id)->first();
	         //dd($n);   
	          $pc=''; $ac='';
		      if(!empty($n->ac_no))
		        	$ac=$this->commonModel->getacbyacno($n->ST_CODE,$n->ac_no);
		        if(!empty($n->pc_no))
    				$pc=$this->commonModel->getpcbypcno($n->ST_CODE,$n->pc_no);
				 
	    	    $ac=$this->commonModel->getacbyacno($n->ST_CODE,$n->ac_no);
				 
			return view('admin.ro.printreceipt', ['user_data' => $d,'caddata'=>$c,'nomination'=>$n,'ac'=>$ac,'pc'=>$pc,'showpage'=>'candidate']);
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
		    }  // end  function 
	public function listallcandidate(Request $request)
		    {
		     if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		        if($d->officerlevel=="AC")
            	$edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,$d->officerlevel,''); 
        elseif($d->officerlevel=="PC")
            	$edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->PC_NO,$d->officerlevel,'');
		         
		        $val=$this->romodel->checkfinalize_acbyro($d->ST_CODE,$d->AC_NO,$d->officerlevel);
		     	$cand_status = $request->input('cand_status');
		     	$list=$this->romodel->Allcandidatelist($edetails,$cand_status);
		     	$status=$this->commonModel->allstatus();
		     	//dd($list);
	        return view('admin.ro.listallcandidate', ['user_data' => $d,'lists'=>$list,'status'=>$cand_status,'checkval'=>$val,'showpage'=>'candidate','status_list'=>$status,'edetails'=>$edetails]);
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
		     
		    }  // end  function   

	public function marksvip($id,$val)
		    {   //dd("hello");
		      if(Auth::check()){
		          $user = Auth::user();
		          $d=$this->commonModel->getunewserbyuserid($user->id);
		         // $list1 = PartyMaster::with('symboldetails')->get();
		         // if($val=='1') $val=0; else $val=1;
		        if($id!='' and $val!='') {
		        	if($val==0) {
		         	 $stsus = array('is_candidate_vip'=>'1');
		        	}
		         	else {
		         	 $stsus = array('is_candidate_vip'=>'0');	
		         	} 
		         	 $i = DB::table('candidate_personal_detail')->where('candidate_id', $id)->update($stsus);
		        	}
		       $this->commonModel->Audit_log_data('0',$d->id,'candidate_personal_detail',$id,'is_candidate_vip','NA',1,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));
		           return Redirect::to('ro/scrutiny-candidates');
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
		     
		    } 
	public function change_status($id,$val)
		    { 
		      if(Auth::check()){
		          $user = Auth::user();
		          $d=$this->commonModel->getunewserbyuserid($user->id);
		          $otp=rand(100000,999999);
		          $mob_message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Candidate Portal. Please enter the OTP to proceed.Your OTP will be valid till 10 minutes.Do not share this OTP Team ECI";
		         if($id!='') {
		         	 $st = array('otpvalue'=>$otp); 
		         	 $i = DB::table('candidate_nomination_detail')->where('nom_id', $id)->update($st);
		        	}
		         $html =$otp; 
				 
			    if(!empty($d->email)) {  
						sendotpmail($d->email,$d->name,$html);  
						}

		     	$response = SmsgatewayHelper::sendOtpSMS($mob_message,$d->Phone_no); 
		     	    
		   		 $list = DB::table('candidate_nomination_detail')->where('candidate_nomination_detail.nom_id','=',$id)->first();
     			return view('admin.ro.changestatus', ['user_data' => $d,'lists'=>$list,'id'=>$id,'val'=>$val,'otp'=>$otp,'showpage'=>'candidate']);
				}
	        else {
	              return redirect('/officer-login');
	        	  }
		    } 
	public function statusvalidation(Request $request)
			{
				if(Auth::check()){
		          $user = Auth::user();
		          $d=$this->commonModel->getunewserbyuserid($user->id);
		     $this->validate(
                $request, 
                    [
                     'verifyotp' => 'required|numeric',
                     'affidavit' => 'required',
                      'rejection_message' => 'required',
                     ],
                    [
                     'verifyotp.required' => 'Please enter your valid Otp', 
                     'verifyotp.numeric' => 'Please enter your valid Otp',
                     'affidavit.required' => 'Please check the affidavit',
                     'rejection_message.required' => 'Please enter Message',
                     ]);
		 		$verifyotp = $this->xssClean->clean_input($request->input('verifyotp'));
                $candidate_id = $this->xssClean->clean_input($request->input('candidate_id')); 
                $nom_id = $this->xssClean->clean_input($request->input('nom_id')); 
                $marks = $this->xssClean->clean_input($request->input('marks'));
                $rejection_message = $this->xssClean->clean_input($request->input('rejection_message'));
                $affidavit = $this->xssClean->clean_input($request->input('affidavit'));  
                $list = DB::table('candidate_nomination_detail')->where('candidate_nomination_detail.nom_id','=',$nom_id)->first();
				$cand = DB::table('candidate_personal_detail')->where('candidate_id','=',$list->candidate_id)->first();
                //dd($list->otpvalue);
                if($list->otpvalue==$verifyotp) {

			     	$st = array('rejection_message'=>$rejection_message,'application_status'=>$marks,'affidavit_public'=>'yes'); 
			     	$i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);
			     	 
			     	\Session::flash('ro_admin', 'Action successfully Change' ); 
				$state=$this->commonModel->getstatebystatecode($list->ST_CODE);
	    	    $ac=$this->commonModel->getacbyacno($list->ST_CODE,$list->ac_no);
                $otp="Dear ".$cand->cand_name.",  your nomination is ".$marks." for constituency ".$ac->AC_NAME." of state  ".$state->ST_NAME." By Election Commission of India. ";				
			    $html =$state->ST_NAME;  
				CandidateECIMail($cand->cand_email,$cand->cand_name,$ac->AC_NAME,$marks,$html);
				$response = SmsgatewayHelper::sendOtpSMS($otp,$cand->cand_mobile); 

				$this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$nom_id,'application_status','receipt_generated',$marks,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));

			      }
			     else {
			     	\Session::flash('ro_opt_messsage', 'OTP not match'); 
			     	
			     	return Redirect::to('ro/change-status/'.$nom_id.'/'.$marks);
			     }  	 
		    	return Redirect::to('ro/scrutiny-candidates');
		     
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
			}
	public function accepted_application()
			{
			if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		if($d->officerlevel=="AC")
          $edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,$d->officerlevel,''); 
        elseif($d->officerlevel=="PC")
           $edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->PC_NO,$d->officerlevel,'');
		        $val=$this->romodel->checkfinalize_acbyro($d->ST_CODE,$d->AC_NO,$d->officerlevel);
		        //dd($val);
		    	$list=$this->romodel->acceptedcandidate($edetails);
		    	return view('admin.ro.listaccepted',['user_data' => $d,'lists'=>$list,'checkval'=>$val,'showpage'=>'candidate','edetails'=>$edetails]);
		         }
	        else {
	         	return Redirect::to('/officer-login');
	        	  }	
			}
	public function change_sequence(Request $request)
			{
			if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		       // dd($request);
		        $noval = $this->xssClean->clean_input($request->input('noval'));
		        $v = $this->xssClean->clean_input($request->input('totalvalue'));
				$input = $request->all();
				$rules = ['Please enter all new serial number'];
		for ($i=1; $i<$noval;$i++)
		    {
		    $this->validate($request, ['newsrno'.$i => 'required|integer',],
                [
                'newsrno'.$i.'required' => 'Please enter all new serial number ',
                ]);	
		    }
		for ($i=1; $i<$noval;$i++)
		    { $k=$i+1;
		      $s=$this->xssClean->clean_input($request->input('newsrno'.$i));
		      $s1=$this->xssClean->clean_input($request->input('newsrno'.$k));
		      if($s>$v) 
		      	{
		      	\Session::flash('error_mes', 'Enter valid new serial number ');
                return Redirect::to('/ro/contested-application');
		      	}
		       if($s==$s1) 
		      	{
		      	\Session::flash('error_mes1', 'Dublicate Sr. number ');
                return Redirect::to('/ro/contested-application');
		      	}
		    }	
		  for ($i=1; $i<$noval;$i++)
		       	{
		       	$s=trim($request->input('newsrno'.$i));
		       	$candidate_id=trim($request->input('nom_id'.$i));
		       	$no = array('new_srno'=>$s); 
		        DB::table('candidate_nomination_detail')->where('nom_id', $candidate_id)->update($no);	
		         $this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$candidate_id,'new_srno','NO',$s,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));
		       	}

		       \Session::flash('success_mes', 'Candidate New sr.no successfully Update');
                return Redirect::to('/ro/contested-application');
		         }
	        else {
	              return Redirect::to('/officer-login');
	        	  }	
			}
	public function pdfview(Request $request)
		    {
			if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		    
		    if($d->officerlevel=="AC")
            	$edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,$d->officerlevel); 
            elseif($d->officerlevel=="PC")
            	$edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->PC_NO,$d->officerlevel);
            // dd($edetails);

		    if($edetails->CONST_TYPE=="AC") { 
		    			$v= 'candidate_nomination_detail.ac_no'; $m=$edetails->CONST_NO; 
		    		}
  			elseif($edetails->CONST_TYPE=="PC") {  
  						$v= 'candidate_nomination_detail.pc_no'; $edetails->CONST_NO;   
  					}

		    $candn = DB::table('candidate_nomination_detail')
		   	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
		    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
		    ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
		    ->where('candidate_nomination_detail.ST_CODE','=',$d->ST_CODE)->where($v,'=',$m) 
		    ->where('candidate_nomination_detail.application_status','=','6')
		    ->where('m_party.PARTYTYPE','=','N')
		    ->orderBy('candidate_nomination_detail.new_srno', 'asc')
    		->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES')->get(); 
			$cands = DB::table('candidate_nomination_detail')
		   	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
		    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
		    ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
		    ->where('candidate_nomination_detail.ST_CODE','=',$d->ST_CODE)->where($v,'=',$m) 
		    ->where('candidate_nomination_detail.application_status','=','6')
		    ->where('m_party.PARTYTYPE','=','S')
		    ->orderBy('candidate_nomination_detail.new_srno', 'asc')
    		->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES')->get();

			$candu = DB::table('candidate_nomination_detail')
		   	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
		    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
		    ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
		    ->where('candidate_nomination_detail.ST_CODE','=',$d->ST_CODE)->where($v,'=',$m) 
		    ->where('candidate_nomination_detail.application_status','=','6')
		    ->where('m_party.PARTYTYPE','=','U')->orderBy('candidate_nomination_detail.new_srno', 'asc')
    		->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES')->get();

    		$candz = DB::table('candidate_nomination_detail')
		   	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
		    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
		    ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
		    ->where('candidate_nomination_detail.ST_CODE','=',$d->ST_CODE)->where($v,'=',$m) 
		    ->where('candidate_nomination_detail.application_status','=','6')
		    ->where('m_party.PARTYTYPE','=','Z')->orderBy('candidate_nomination_detail.new_srno', 'asc')
    		->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES')->get();


		     $pc=''; $ac='';
		          if(!empty($d->AC_NO))
		        	$ac=$this->commonModel->getacbyacno($d->ST_CODE,$d->AC_NO);
		        if(!empty($d->PC_NO))
    				$pc=$this->commonModel->getpcbypcno($d->ST_CODE,$d->PC_NO);
				
				$state=$this->commonModel->getstatebystatecode($d->ST_CODE);
	    	    $ac=$this->commonModel->getacbyacno($d->ST_CODE,$d->AC_NO);
		     	//print_r($candn);print_r($cands);print_r($candu);dd($candz);
		        view()->share('candn',$candn,'cands',$cands,'candu',$candu,'candz',$candz,'st',$state,'ac',$ac);

 
		        if($request->has('download')){
		            $pdf = PDF::loadView('admin.pdfview',compact('candn',$candn,'cands',$cands,'candu',$candu,'candz',$candz,'state',$state,'ac',$ac));
		            return $pdf->download('admin.pdfview.pdf');
		        }


		        return view('admin.pdfview');
			 }
	        else {
	              return redirect('/officer-login');
	        	  }
		    }
    public function symbol_upload()  
			{
			if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		if($d->officerlevel=="AC")
           $edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,$d->officerlevel,''); 
        elseif($d->officerlevel=="PC")
           $edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->PC_NO,$d->officerlevel,'');
		        $val=$this->romodel->checkfinalize_acbyro($d->ST_CODE,$d->AC_NO,$d->officerlevel);
		        $list=$this->romodel->Symbolcandidate($edetails);
		       //  dd($list);
		    	return view('admin.ro.symboldetails',['user_data' => $d,'lists'=>$list,'checkval'=>$val,'showpage'=>'candidate','edetails'=>$edetails]);
		         }
	        else {
	         	return Redirect::to('/officer-login');
	        	  }	
			}    
    public function assign_symbol($nom_id) 
			{
			if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
            if(!empty($nom_id)) {    
		        $list=$this->romodel->Symbolassign($nom_id);
		        //dd($list);

		    	return view('admin.ro.symbolassign',['user_data' => $d,'lists'=>$list,'showpage'=>'candidate']);
		    	}
		    else {
		    	 return Redirect::to('/ro');
		        }
		         }
	        else {
	         	return Redirect::to('/officer-login');
	        	  }	
			}
	public function updatesymbol(Request $request) 
			{
			if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
                 // dd($request);
		        $this->validate(
                $request, 
                    [
                     'symbol' => 'required',
                    ],
                    [
                     'symbol.required' => 'Please select symbol', 
                    ]);
                  $candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
                  $nom_id = $this->xssClean->clean_input($request->input('nom_id'));
                  $symbol = $this->xssClean->clean_input($request->input('symbol'));
                  $udata = array('symbol_id'=> $symbol); 
                  //echo $candidate_id;  echo "=".$nom_id;
		          // dd($udata); 
		          $n=$this->commonModel->updatedata('candidate_nomination_detail','nom_id',$nom_id,$udata);
		           $this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$nom_id,'symbol_id','NO',$symbol,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));
		        //  dd($n);
		         // $i = DB::table('candidate_nomination_detail')->where('candidate_id', $candidate_id)->where('nom_id',$nom_id)->update($udata);
		          \Session::flash('success_mes', 'Symbol successfully Assign');
                  return Redirect::to('/ro/symbol-upload');
		         }
	        else {
	         	return Redirect::to('/officer-login');
	        	  }	
			}  
	function finalize_ac()
			{
			 if(Auth::check()){
		      $user = Auth::user();
		      $d=$this->commonModel->getunewserbyuserid($user->id);
		       if($d->officerlevel=="AC")
		        	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,'AC');   
		        elseif($d->officerlevel=="PC")	
		        	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->PC_NO,'PC');  
           // dd($ele_details);  
        $check_ac = DB::table('candidate_finalized_ac')->where('ST_CODE',$d->ST_CODE)->where('CONS_NO',$ele_details->CONST_NO)->where('CONS_TYPE',$ele_details->CONST_TYPE)->where('ELECTION_ID',$ele_details->ELECTION_ID)->first();
        	$date = Carbon::now();
        	$currentTime = $date->format('Y-m-d H:i:s');
		 
		      $otp=rand(100000,999999);
		      $mob_message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Candidate Portal for finalized AC . Please enter the OTP to proceed.Your OTP will be valid till 30 minutes.Do not share this OTP,  Team ECI";
		 
		if(!isset($check_ac)) {
			$st = array('ST_CODE'=>$d->ST_CODE,'CONS_NO'=>$ele_details->CONST_NO,'CONS_TYPE'=>$ele_details->CONST_TYPE,'ELECTION_ID'=>$ele_details->ELECTION_ID,'finalized_ac'=>'0','mobile_otp'=>$otp,'otp_time' => $currentTime,'created_at'=>date("Y-m-d H:i:s"),'created_by'=>$d->officername); 
			$r=$this->commonModel->insertData('candidate_finalized_ac',$st);
			$check_ac = DB::table('candidate_finalized_ac')->where('ST_CODE',$d->ST_CODE)->where('CONS_NO',$ele_details->CONST_NO)->where('CONS_TYPE',$ele_details->CONST_TYPE)->where('ELECTION_ID',$ele_details->ELECTION_ID)->first();
            }
        else{  
        	$st = array('mobile_otp'=>$otp,'otp_time' => $currentTime);
        	$i = DB::table('candidate_finalized_ac')->where('id',$check_ac->id)->update($st);
         	}
         
		   $html =$otp; 
				 
			    if(!empty($d->email)) {  
						sendotpmail($d->email,$d->name,$html);  
						}
				 $response = SmsgatewayHelper::sendOtpSMS($mob_message,$d->Phone_no); 
				return view('admin.ro.finalize-ac',['user_data' => $d,'lists'=>$check_ac,'otp'=>$otp,'showpage'=>'candidate','otp_time'=>$currentTime]);
		         }
	        else {
	         		return Redirect::to('/officer-login');
	        	 }		
			}
	function finalize_candidate(Request $request)
			{  
				//set_time_limit(0);
			if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		        $this->validate(
	                $request, 
	                    [
	                     'verifyotp' => 'required|numeric',
	                     //'finalized_message' => 'required',
	                     ],
	                    [
	                     'verifyotp.required' => 'Please enter your valid Otp', 
	                     'verifyotp.numeric' => 'Please enter your valid Otp',
	                     //'finalized_message.required' => 'Please check the affidavit',
	                     ]);
		       $verifyotp = $this->xssClean->clean_input($request->input('verifyotp'));
		       $finalized_message = $this->xssClean->clean_input($request->input('finalized_message'));
		       $id = $this->xssClean->clean_input($request->input('id'));
		       $cons_no = $this->xssClean->clean_input($request->input('cons_no'));
		       $st_code = $this->xssClean->clean_input($request->input('st_code'));
		       $CONS_TYPE = $this->xssClean->clean_input($request->input('CONS_TYPE'));
		       $ELECTION_ID = $this->xssClean->clean_input($request->input('ELECTION_ID'));
		       $otp = $this->xssClean->clean_input($request->input('otp'));
		       $otp_time = $this->xssClean->clean_input($request->input('otp_time'));
		        
		       $date = Carbon::now()->subMinutes(10);
                $currentTime = $date->format('Y-m-d H:i:s');
                //echo $currentTime; echo $otp_time;
		       if($otp!=$verifyotp) {
		       	 \Session::flash('ro_opt_messsage', 'Your Otp Message Invalide');
                  return Redirect::to('/ro/finalize-ac');
		       }
		      if($otp_time<$currentTime) {
		      	 \Session::flash('ro_opt_messsage', 'Your Otp time Expair');
                  return Redirect::to('/ro/finalize-ac');
		       }
		       $ins_data = array('finalized_ac'=>'1','finalized_message'=>$finalized_message);
				$state=$this->commonModel->getstatebystatecode($d->ST_CODE);
	    		$ac=$this->commonModel->getacbyacno($d->ST_CODE,$d->AC_NO);
		$ddeo = DB::table('officer_login')->where('ST_CODE',$d->ST_CODE)->where('DIST_NO',$d->DIST_NO)->where('officerlevel','DEO')->first();
		$cceo = DB::table('officer_login')->where('ST_CODE',$d->ST_CODE)->where('officerlevel','CEO')->first();

		  
		  
		        // dd($request);
		        if($d->officerlevel=="AC")
		        	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,'AC');   
		        elseif($d->officerlevel=="PC")	
		        	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->PC_NO,'PC');  
		         //dd($ele_details);
                $list=$this->romodel->finalize_candidate_ac($st_code,$cons_no,$ele_details->CONST_TYPE,$ins_data); 
		        $html =$state->ST_NAME; 
				 
				sendlevelmail($ddeo->email,$d->name,$ac->AC_NAME,$html);
				sendlevelmail($cceo->email,$d->name,$ac->AC_NAME,$html);

		          \Session::flash('success_mes', 'Finalize AC successfully Assign');
                  return Redirect::to('/ro/contested-application');
		         }
	        else {
	         	return Redirect::to('/officer-login');
	        	  }	
			}	    
	function public_affidavit()
			{  
			if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		         
                $list=$this->romodel->public_affidavit_ac($d->ST_CODE,$d->AC_NO);
		          \Session::flash('success_mes', 'After Scrutiny All affidavit Public');
                  return Redirect::to('/ro/contested-application');
		         }
	        else {
	         	return Redirect::to('/officer-login');
	        	  }	
				
			}
	public function ballotpaperpdfview(Request $request)
		    {
			if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		      
		     if($d->officerlevel=="AC")
            	$edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,$d->officerlevel); 
            elseif($d->officerlevel=="PC")
            	$edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->PC_NO,$d->officerlevel);
            // dd($edetails);

		    if($edetails->CONST_TYPE=="AC") { 
		    			$v= 'candidate_nomination_detail.ac_no'; $m=$edetails->CONST_NO; 
		    		}
  			elseif($edetails->CONST_TYPE=="PC") {  
  						$v= 'candidate_nomination_detail.pc_no'; $m=$edetails->CONST_NO;   
  					}

		    $cand = DB::table('candidate_nomination_detail')
		   	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
		    ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
		    ->where('candidate_nomination_detail.ST_CODE','=',$d->ST_CODE)->where($v,'=',$m) 
		    ->where('candidate_nomination_detail.application_status','=','6')
		    ->orderBy('candidate_nomination_detail.new_srno', 'asc')
    		->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_image','candidate_nomination_detail.new_srno','m_symbol.*')->get(); 
			
		    view()->share('cand',$cand);

 			//if($request->has('download')){
		    $pdf = MPDF::loadView('admin.ballotview',compact('cand',$cand));
			return $pdf->download('dadmin.ballotview.pdf');
		    //$pdf = PDF::loadView('admin.ballotview',compact('cand',$cand));
		    //return $pdf->download('admin.ballotview.pdf');
		    ///}
			  return view('admin.ballotview');
			 }
	        else {
	              return redirect('/officer-login');
	        	  }
		    }	
		    
     	 
}  // end class  
