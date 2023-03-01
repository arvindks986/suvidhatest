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
		use App\adminmodel\ROPCModel;
		use App\adminmodel\ROPCReportModel;
		use App\Classes\xssClean;
		use App\adminmodel\SymbolMaster;
		//use Spatie\MixedContentScanner\MixedContentScanner;
class RoPCReportController extends Controller
{
    //
   public function __construct()
        {   
			$this->middleware('adminsession');
			$this->middleware(['auth:admin','auth']);
			$this->middleware('ro');
			$this->commonModel = new commonModel();
			$this->CandidateModel = new CandidateModel();
			$this->romodel = new ROPCModel();
			$this->ropcreportmodel = new ROPCReportModel();
			$this->xssClean = new xssClean;
			$this->sym = new SymbolMaster();	
		}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
 	protected function guard(){
        return Auth::guard('admin');
    	}
   
/**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 16-02-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return electors-pollingstation List By State fuction     
   */
	
   public function electorsropollingstationList(Request $request){ 
	 if(Auth::check()){
	  $user = Auth::user();
	  $d=$this->commonModel->getunewserbyuserid($user->id);
	  $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
	 			$check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			    $seched=getschedulebyid($ele_details->ScheduleID);
                $sechdul=checkscheduledetails($seched);
	 $election_id= $ele_details->ELECTION_ID;
	 $pc_no= $ele_details->CONST_NO;
	 $st_code= $ele_details->ST_CODE;
	 $acdata = $this->ropcreportmodel->getAcByPC($st_code,$pc_no,$election_id);
	//dd($acdata);
	  return view('admin.pc.ro.electors-ropollingstationlist',['user_data' => $d,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'ele_details' => $ele_details,'acdata'=> $acdata]);
	  }else {
	   return redirect('/officer-login');
	 }   
	}   // end electorspollingstation List function
	
	/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 14-01-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return electors-pollingstation Store By State fuction     
  */
   
  public function electorsropollingstationStore(Request $request){
	 if(Auth::check()){
	  $user = Auth::user();
	  $d=$this->commonModel->getunewserbyuserid($user->id);
	 $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
			    $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			    $seched=getschedulebyid($ele_details->ScheduleID);
                $sechdul=checkscheduledetails($seched);
       	    $byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
	
	 $election_id=$ele_details->ELECTION_ID;
	 $CONST_TYPE=$ele_details->CONST_TYPE;
	 $pc_no= $ele_details->CONST_NO;
	 $st_code= $ele_details->ST_CODE;
 
	 $added_create_at = date('Y-m-d');
	 $created_at = date('Y-m-d H:i:s');
	 $totalchecked = count($request->checkbox);

	 //Ac No
	 $acno_arr = $request->ac_no;
	 $acname_arr = $request->ac_name;
	 $gen_male =   $request->gen_male;
	 $gen_female = $request->gen_female;
	 $gen_third = $request->gen_third;
	 $gen_total = $request->gen_total;

	 $ser_male =   $request->ser_male;
	 $ser_female = $request->ser_female;
	 $ser_third = $request->ser_third;
	 $ser_total = $request->ser_total;

	 $regular_arr =   $request->regular;
	 $auxillary_arr = $request->auxillary;
	 $polling_total_arr = $request->polling_total;
	 
	 DB::enableQueryLog();

	 for($i=0;$i<$totalchecked;$i++)
	 {  
		 $ac_no=$request->checkbox[$i];
		 $gen_m = $gen_male[$ac_no];
		 $gen_f = $gen_female[$ac_no];
		 $gen_o = $gen_third[$ac_no];
		// $gen_t = $gen_total[$ac_no];
			$gen_t = $gen_m+$gen_f+$gen_o;
			
		 $ser_m = $ser_male[$ac_no];
		 $ser_f = $ser_female[$ac_no];
		 $ser_o = $ser_third[$ac_no];
		// $ser_t = $ser_total[$ac_no];
		 $ser_t = $ser_m+$ser_f+$ser_o;
		 $polling_reg = $regular_arr[$ac_no];
		 $polling_auxillary = $auxillary_arr[$ac_no];
		// $polling_total = $polling_total_arr[$ac_no];
		 $polling_total = $polling_reg+$polling_auxillary;
		
		  $elector_data = array(
			'election_id'=>$election_id,
			'const_no'=>$request->ac_no[$i],
			'const_type'=>$CONST_TYPE,
			'st_code'=>$request->st_code,
			'ac_no'=>$ac_no,
			'pc_no'=>$request->pc_no,  
			'gen_m'=>$this->xssClean->clean_input($gen_m),
			'gen_f'=>$this->xssClean->clean_input($gen_f),
			'gen_o'=>$this->xssClean->clean_input($gen_o),
			'gen_t'=>$this->xssClean->clean_input($gen_t),
			'ser_m'=>$this->xssClean->clean_input($ser_m),
			'ser_f'=>$this->xssClean->clean_input($ser_f),
			'ser_o'=>$this->xssClean->clean_input($ser_o),
			'ser_t'=>$this->xssClean->clean_input($ser_t),
			'polling_reg'=>$this->xssClean->clean_input($polling_reg),
			'polling_auxillary'=>$this->xssClean->clean_input($polling_auxillary),
			'polling_total'=>$this->xssClean->clean_input($polling_total),
			'poll_date'=>$added_create_at,
			'added_create_at'=>$added_create_at,
			'created_at'=>$created_at,
			'created_by'=>$user->id
			 );
		 $checkelectorData = DB::table('elector_details')->where('st_code', $request->st_code)->where('ac_no', $ac_no)->first();
		 if(!empty($checkelectorData)){
			$n = DB::table('elector_details')->where('st_code', $request->st_code)->where('ac_no', $ac_no)->update($elector_data);
		   }else{
           $n = DB::table('elector_details')->insert($elector_data);
		}
	 }
	 \Session::flash('success_admin', 'You have Successfully saved Schedule. '); 
	 return redirect()->back();
	 } 
     }   // end electorspollingstationData function
	
         
	function public_affidavit()
			{  
			if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		         
                $list=$this->romodel->public_affidavit_ac($d->st_code,$d->ac_no);
		          \Session::flash('success_mes', 'After Scrutiny All affidavit Public');
                  return Redirect::to('/ro/contested-application');
		         }
	        else {
	         	return Redirect::to('/officer-login');
	        	  }	
				
			}
	 	
		    
     

	public function roOfficerLogindetailsList(request $request){
        if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
            $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
			    $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			    $seched=getschedulebyid($ele_details->ScheduleID);
                $sechdul=checkscheduledetails($seched);
            $officerDetails=$this->ropcreportmodel->getOfficerlistByROPC($d->st_code,$d->pc_no);
           
            
            return view('admin.pc.ro.roofficer-logindetails', ['user_data' => $d,'officerDetails'=>$officerDetails,'ele_details'=>$ele_details]);
        }
        else {
            return redirect('/officer-login');
        }
	} 

	public function logindetailpdf(request $request){
		//echo "test";die;
        if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
            $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          $st_code=$d->st_code;
            $officerDetails=$this->ropcreportmodel->getOfficerlistByROPC($d->st_code,$d->pc_no);
           $allUsers =DB::table('officer_login')->where('st_code',$d->st_code)->get();
$pdf = PDF::loadView('admin.pc.ro.ropcOfficerDetailHtml', compact('st_code','officerDetails'));
            return $pdf->download($st_code."-user-login-detail-report".".pdf");
            
            //return view('admin.pc.ro.ropcOfficerDetailHtml', ['user_data' => $d,'officerDetails'=>$officerDetails,'ele_details'=>$ele_details]);
        }
        else {
            return redirect('/officer-login');
        }
	} 
		public function loginDetailExcel(request $request){
		//echo "test";die;
        if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id);
            $st_code =$d->st_code;
                    $cur_time    = Carbon::now(); 
            \Excel::create('officer-login-detail'.trim($st_code).'_'.$cur_time, function($excel) use($st_code) { 
      $excel->sheet('Sheet1', function($sheet) use($st_code) {
      $arr  = array();
      //$cand_party_type='Z'; 
      $finalize='1';
      $user = Auth::user();
      $d=$this->commonModel->getunewserbyuserid($user->id);
         $officerDetails=$this->ropcreportmodel->getOfficerlistByROPC($d->st_code,$d->pc_no);
         $j=0;
      foreach ($officerDetails as $officerDetailsList) {
        $j++;
          $data =  array(
                  $j,
                  $officerDetailsList->name,
                  $officerDetailsList->designation,
                  $officerDetailsList->officername,
                  'demo@1234'
                        );
                  array_push($arr, $data); 
                  }
   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                       'Sr. No.', 'Officer Name', 'Designation', 'User Id','Password'
               )

           );

         });

    })->export('xls');
        }
        else {
            return redirect('/officer-login');
        }
	} 
    public function changepassword(request $request){ 
	
			if(Auth::check()){ 
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id); 
			$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
			$check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			$seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
			if($user->designation!='ARO'){
					return view('admin.pc.ro.changepassword', ['user_data' => $d,'ele_details' => $ele_details,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul]);
			} else {
					return view('admin.pc.ro.changepassword', ['user_data' => $d,'ele_details' => $ele_details]);
				}
				}
			} //@end changepassword function

	/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 20-02-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return changePasswordStore By RO     
  */
  public function changePasswordStore(request $request){ 
		if(Auth::check()){ 
					$user = Auth::user();
					$d=$this->commonModel->getunewserbyuserid($user->id); 
					 
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
	
	
	/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 06-03-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return form 3a report By PC wise     
  */
	public function form3areport(request $request){    
		if(Auth::check()){
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);
			// dd($d);
			$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
		//	dd($ele_details);
			$check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			$seched=getschedulebyid($ele_details->ScheduleID);
			$sechdul=checkscheduledetails($seched);  
			//dd($check_finalize);
			$form3alist=$this->ropcreportmodel->getnominationByROPC($d->st_code,$d->pc_no,'','');
						
					     return view('admin.pc.ro.form-3A', ['user_data' => $d,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'form3alist' => $form3alist,'ele_details'=>$ele_details]);
				    	}	else {
								return redirect('/officer-login');
							}
		} // end electorspollingstation List function
	
	/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 06-03-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return electors-pollingstation Store By State fuction     
  */
		public function form3adatewisereport(Request $request){  
			//dd($request->all());
			if(Auth::check()){ 
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);
			// dd($d);
    	$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
		//	dd($ele_details);
		
			$check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			$seched=getschedulebyid($ele_details->ScheduleID);
			$sechdul=checkscheduledetails($seched);    

				 if(isset($ele_details->ScheduleID)) {
						$sched=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
						$const_type=$ele_details->CONST_TYPE;
				 }	else {
							$sched='';
				}
						$from_date = ($request->from_date);
						$to_date = ($request->to_date); 
						$st_code = $request->st_code;
						$pc_no = $request->pc_no;

						if(isset($from_date)){
							if($from_date=='all' && $to_date=='all'){
								$from_date='';
								$to_date='';
							}
						}
						
						$timeInterval = $from_date.'~'.$to_date;
						
						$fromdate = date('Y-m-d',strtotime($from_date));
						$todate = date('Y-m-d',strtotime($to_date));  

						$datewiseform3alist=$this->ropcreportmodel->getnominationByROPC($d->st_code,$d->pc_no,$fromdate,$todate);
          // dd($datewiseform3alist);
							if(!empty($datewiseform3alist)){  $j=1;
								$canddetailsArray = array();
								$html='';
									foreach ($datewiseform3alist as $listdata) { 
										 //dd($lis);
										 $canddetailsArray=CandidateModel::where(['candidate_id' =>$listdata->candidate_id])->get();
										 $nominationArray=CandidateNomination::where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['candidate_id' =>$listdata->candidate_id])->get();
							
										 $html.='<tr>
											 <td>'.$j++.'</td>
											 <td>'.$canddetailsArray[0]->cand_name.'</td>
											 <td>'.$canddetailsArray[0]->candidate_father_name.'</td>
											 <td>'.$canddetailsArray[0]->cand_age.'</td>
											 <td>'.$canddetailsArray[0]->candidate_residence_address.'</td>
											 <td>'.$listdata->party_id.'</td>
											 <td>'.$canddetailsArray[0]->cand_category.'</td>
											 <td>'.$listdata->party_id.'</td>
											 <td>'.$nominationArray[0]->proposer_name.'</td>
											 <td>'.$nominationArray[0]->proposer_partno.'</td>
										 </tr>';
											}   
										}	else{
														 $html .= '<tr><td colspan="10" style="color:red; text-align:center;"><b>No Record Found.</b></td></tr>';
										 
														}
														return $html;
					}else {
								return redirect('/officer-login');
							}
		 }// end electorspollingstation List function
	
	/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 06-03-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return electors-pollingstation Store By State fuction     
  */
		public function form3apdfview(Request $request) { 
			//set_time_limit(6000);
				$date=trim(base64_decode($request->date));
				$pc_no=trim(base64_decode($request->pc_no));
			  // dd($date);  
				if(Auth::check()){
					$user = Auth::user();
					$d=$this->commonModel->getunewserbyuserid($user->id);
					// dd($d);
					$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
						// dd($ele_details);
						$state =$this->commonModel->getstatebystatecode($d->st_code);
						$const_name=trim($this->commonModel->getpcname($d->st_code,$d->pc_no)->PC_NAME);
					   //dd($const_name);
					   $statename=$state->ST_NAME;
					 if(isset($ele_details->ScheduleID)) {
							$sched=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
							$const_type=$ele_details->CONST_TYPE;
					 }
							else {
								$sched='';
							}
							
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
							$datewiseform3alist=$this->ropcreportmodel->getnominationByROPC($d->st_code,$d->pc_no,$fromdate,$todate);

								// dd($datewiseform3alist);
						$pdf = MPDF::loadView('admin.pc.ro.form3Apdf',compact('date',$date,'datewiseform3alist',$datewiseform3alist,'state',$state,'const_name',$const_name,'ele_details',$ele_details));
						//return $pdf->download('form3Apdf.pdf');
					//	return view('admin.pc.ro.form3Apdf');

						//$pdf = PDF::loadView('admin.pc.ro.form3Apdf', compact('date' ,'datewiseform3alist','state','const_name','ele_details'));
           return $pdf->download($statename.'/'.$const_name."-form-3A-distwiseReport-".".pdf");
						}
						else {
									return redirect('/officer-login');
								}

		} //end form 3A pdf view

		
	/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 06-03-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return form 4a report By PC wise     
  */
	public function form4areport(request $request){    
		if(Auth::check()){
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);
			// dd($d);
			$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
		//	dd($ele_details);
			$check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			$seched=getschedulebyid($ele_details->ScheduleID);
			$sechdul=checkscheduledetails($seched);  
			//dd($check_finalize);
			$form3alist=$this->ropcreportmodel->getform4AByROPC($d->st_code,$d->pc_no,'','');
							
					     return view('admin.pc.ro.form4a', ['user_data' => $d,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'form3alist' => $form3alist,'ele_details'=>$ele_details]);
				    	}	else {
								return redirect('/officer-login');
							}
		} // end form4areport List function
	
	/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 06-03-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return form4adatewisereport Store By State fuction     
  */
		public function form4adatewisereport(Request $request){  
			//dd($request->all());
			if(Auth::check()){ 
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);
			// dd($d);
    	$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
		//	dd($ele_details);
		
			$check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			$seched=getschedulebyid($ele_details->ScheduleID);
			$sechdul=checkscheduledetails($seched);    

				 if(isset($ele_details->ScheduleID)) {
						$sched=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
						$const_type=$ele_details->CONST_TYPE;
				 }	else {
							$sched='';
				}
						$from_date = ($request->from_date);
						$to_date = ($request->to_date); 
						$st_code = $request->st_code;
						$pc_no = $request->pc_no;

						if(isset($from_date)){
							if($from_date=='all' && $to_date=='all'){
								$from_date='';
								$to_date='';
							}
						}
						
						$timeInterval = $from_date.'~'.$to_date;
						
						$fromdate = date('Y-m-d',strtotime($from_date));
						$todate = date('Y-m-d',strtotime($to_date));  

						$datewiseform3alist=$this->ropcreportmodel->getform4AByROPC($d->st_code,$d->pc_no,$fromdate,$todate);
          // dd($datewiseform3alist);
							if(!empty($datewiseform3alist)){  $j=1;
								$canddetailsArray = array();
								$html='';
									foreach ($datewiseform3alist as $listdata) { 
										 //dd($lis);
										 $canddetailsArray=CandidateModel::where(['candidate_id' =>$listdata->candidate_id])->get();
										 $nominationArray=CandidateNomination::where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['candidate_id' =>$listdata->candidate_id])->get();
							
										 $html.='<tr>
											 <td>'.$j++.'</td>
											 <td>'.$canddetailsArray[0]->cand_name.'</td>
											 <td>'.$canddetailsArray[0]->candidate_father_name.'</td>
											 <td>'.$canddetailsArray[0]->cand_age.'</td>
											 <td>'.$canddetailsArray[0]->candidate_residence_address.'</td>
											 <td>'.$listdata->party_id.'</td>
										 </tr>';
											}   
										}	else{
														 $html .= '<tr><td colspan="10" style="color:red; text-align:center;"><b>No Record Found.</b></td></tr>';
										 
														}
														return $html;
					}else {
								return redirect('/officer-login');
							}
		 }// end form4adatewisereport List function
	
	/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 06-03-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return form4apdfview Store By State fuction     
  */
		public function form4apdfview(Request $request) { 
			//set_time_limit(6000);
				$date=trim(base64_decode($request->date));
				$pc_no=trim(base64_decode($request->pc_no));
			  // dd($date);  
				if(Auth::check()){
					$user = Auth::user();
					$d=$this->commonModel->getunewserbyuserid($user->id);
					// dd($d);
					$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
						// dd($ele_details);
						$state =$this->commonModel->getstatebystatecode($d->st_code);
						$const_name=trim($this->commonModel->getpcname($d->st_code,$d->pc_no)->PC_NAME);
					  // dd($const_name);
					   $statename=$state->ST_NAME;
					 if(isset($ele_details->ScheduleID)) {
							$sched=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
							$const_type=$ele_details->CONST_TYPE;
					 }
							else {
								$sched='';
							}
							
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
							$datewiseform4alist=$this->ropcreportmodel->getform4AByROPC($d->st_code,$d->pc_no,$fromdate,$todate);

					 //dd($datewiseform3alist);
						$pdf = PDF::loadView('admin.pc.ro.form4Apdf',compact('date',$date,'datewiseform4alist',$datewiseform4alist,'state',$state,'const_name',$const_name,'ele_details',$ele_details));
						//return $pdf->download('form4Apdf.pdf');
					//	return view('admin.pc.ro.form4Apdf');

						//$pdf = MPDF::loadView('admin.pc.ro.form4Apdf', compact('date' ,'datewiseform3alist','state','const_name','ele_details'));
                         return $pdf->download($statename.'/'.$const_name."-form-4A-distwiseReport-".".pdf");
						}
						else {
									return redirect('/officer-login');
								}

		} //end form 4A pdf view
}  // end class  
