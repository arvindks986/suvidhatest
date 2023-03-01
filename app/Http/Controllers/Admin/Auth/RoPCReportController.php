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
	 /* echo '<pre>'; print_r($d);  echo '<pre>'; echo '<br>';
	  echo '<pre>'; print_r($ele_details);  die;*/
	 $election_id= $ele_details->ELECTION_ID;
	 $pc_no= $ele_details->CONST_NO;
	 $st_code= $ele_details->ST_CODE;
	 $acdata = $this->ropcreportmodel->getAcByPC($st_code,$pc_no,$election_id);
	//dd($acdata);
	  return view('admin.pc.ro.electors-ropollingstationlist',['user_data' => $d,'ele_details' => $ele_details,'acdata'=> $acdata]);
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
	 //echo '<pre>'; print_r($request->all()); exit;
	 //dd($request->all());
	 if(Auth::check()){
	  $user = Auth::user();
	  $d=$this->commonModel->getunewserbyuserid($user->id);
	  $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
	
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
		 $gen_t = $gen_total[$ac_no];
		 $ser_m = $ser_male[$ac_no];
		 $ser_f = $ser_female[$ac_no];
		 $ser_o = $ser_third[$ac_no];
		 $ser_t = $ser_total[$ac_no];
		 $polling_reg = $regular_arr[$ac_no];
		 $polling_auxillary = $auxillary_arr[$ac_no];
		 $polling_total = $polling_total_arr[$ac_no];
		
		  $elector_data = array(
			'election_id'=>$election_id,
			'const_no'=>$request->ac_no[$i],
			'const_type'=>$CONST_TYPE,
			'st_code'=>$request->st_code,
			'ac_no'=>$ac_no,
			'pc_no'=>$request->pc_no,
			'gen_m'=>$gen_m,
			'gen_f'=>$gen_f,
			'gen_o'=>$gen_o,
			'gen_t'=>$gen_t,
			'ser_m'=>$ser_m,
			'ser_f'=>$ser_f,
			'ser_o'=>$ser_o,
			'ser_t'=>$ser_t,
			'polling_reg'=>$polling_reg,
			'polling_auxillary'=>$polling_auxillary,
			'polling_total'=>$polling_total,
			'poll_date'=>$added_create_at,
			'added_create_at'=>$added_create_at,
			'created_at'=>$created_at,
			'created_by'=>$user->id
			 );
		  //dd($elector_data);
		 $checkelectorData = DB::table('elector_details')->where('ac_no', $ac_no)->first();
		 //dd($checkelectorData);
		 if(!empty($checkelectorData)){
			$n = DB::table('elector_details')->where('ac_no', $ac_no)->update($elector_data);
		   }else{
           $n = DB::table('elector_details')->insert($elector_data);
		}
		//dd(DB::getQueryLog());
	 }
	 \Session::flash('success_admin', 'You have Successfully Added Schedule. '); 
	 return redirect()->back();
	 /*if($n=="true"){
	   \Session::flash('success_admin', 'You have Successfully Added Schedule. '); 
		// return Redirect::to('/admin/pc/ceo/electors-pollingstationlist');
		return redirect()->back();
		 }else {
		 \Session::flash('error_mes', 'Data Insertion/Updation Unsuccessfull. '); 
		 return redirect()->back();
	  }  */
	 } 
	}   // end electorspollingstationData function


    public function index()
	    {      
	    if(Auth::check()){
		    $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		    //dd($d);
		$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
		 
            $module=$this->commonModel->getallmodule();
            
         if(isset($ele_details->ScheduleID))
            $sched=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
            else 
            	$sched='';
        
            return view('admin.pc.ro.dashboard', ['user_data' => $d,'module' => $module,'sched' => $sched,'showpage'=>'candidate','edetails'=>$ele_details]);	           
	        }
	        else {
	              return redirect('/officer-login');
	        	  }
	    }  // end index function

		 
	public function listallcandidate(Request $request)
		    {
		     if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		   		$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
		         
		        $val=$this->romodel->checkfinalize_acbyro($d->st_code,$d->ac_no,$d->officerlevel);
		     	$cand_status = $request->input('cand_status');
		     	$list=$this->romodel->Allcandidatelist($ele_details,$cand_status);
		     	$status=allstatus();
		     	//dd($list);
	        return view('admin.pc.ro.listallcandidate', ['user_data' => $d,'lists'=>$list,'status'=>$cand_status,'checkval'=>$val, 'status_list'=>$status,'edetails'=>$ele_details]);
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
		     
		    }  // end  function   
    public function withdrawn_candidates(Request $request)
		    {
		     if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		   		$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
		         
		        $val=$this->romodel->checkfinalize_acbyro($d->st_code,$d->ac_no,$d->officerlevel);
		     	$cand_status = $request->input('cand_status');
		     	$list=$this->romodel->withdrawn($ele_details,$cand_status);
		     	$status=allstatus();
		      
	        return view('admin.pc.ro.withdrawn_candidates', ['user_data' => $d,'lists'=>$list,'status'=>$cand_status,'checkval'=>$val, 'status_list'=>$status,'edetails'=>$ele_details]);
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
		     
		    }  // end  function   withdrawn_candidates

	public function marksvip($id,$val)
		    {   //dd("hello");
		      if(Auth::check()){
		          $user = Auth::user();
		          $d=$this->commonModel->getunewserbyuserid($user->id);
		         
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
		           return Redirect::to('ropc/scrutiny-candidates');
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
                     //'verifyotp' => 'required|numeric',
                     //'affidavit' => 'required',
                      'rejection_message' => 'required',
                     ],
                    [
                    // 'verifyotp.required' => 'Please enter your valid Otp', 
                     //'verifyotp.numeric' => 'Please enter your valid Otp',
                     //'affidavit.required' => 'Please check the affidavit',
                     'rejection_message.required' => 'Please enter Message',
                     ]);
		 		//$verifyotp = $this->xssClean->clean_input($request->input('verifyotp'));
                $candidate_id = $this->xssClean->clean_input($request->input('candidate_id')); 
                $nom_id = $this->xssClean->clean_input($request->input('nom_id')); 
                $marks = $this->xssClean->clean_input($request->input('marks'));
                $rejection_message = $this->xssClean->clean_input($request->input('rejection_message'));
                //$affidavit = $this->xssClean->clean_input($request->input('affidavit'));  
                $st = array('rejection_message'=>$rejection_message,'application_status'=>$marks,'affidavit_public'=>'yes'); 
			    $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);
			    \Session::flash('ro_admin', 'Action successfully Change' ); 
				 
				$this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$nom_id,'application_status','receipt_generated',$marks,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));
		      	 
		    		return Redirect::to('ropc/scrutiny-candidates');
		     
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
			}
	public function withstatusvalidation(Request $request)
			{
				if(Auth::check()){
		          $user = Auth::user();
		          $d=$this->commonModel->getunewserbyuserid($user->id);
		       

		     $this->validate(
                $request, 
                    [
                     //'verifyotp' => 'required|numeric',
                     //'affidavit' => 'required',
                      'rejection_message' => 'required',
                     ],
                    [
                    // 'verifyotp.required' => 'Please enter your valid Otp', 
                     //'verifyotp.numeric' => 'Please enter your valid Otp',
                     //'affidavit.required' => 'Please check the affidavit',
                     'rejection_message.required' => 'Please enter Message',
                     ]);
		 		//$verifyotp = $this->xssClean->clean_input($request->input('verifyotp'));
                $candidate_id = $this->xssClean->clean_input($request->input('candidate_id')); 
                $nom_id = $this->xssClean->clean_input($request->input('nom_id')); 
                $marks = $this->xssClean->clean_input($request->input('marks'));
                $rejection_message = $this->xssClean->clean_input($request->input('rejection_message'));
                //$affidavit = $this->xssClean->clean_input($request->input('affidavit'));  
                $st = array('rejection_message'=>$rejection_message,'application_status'=>$marks,'affidavit_public'=>'yes'); 
			    $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);
			    \Session::flash('ro_admin', 'Action successfully Change' ); 
				 
				$this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$nom_id,'application_status','receipt_generated',$marks,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));
		      	 
		    		return Redirect::to('ropc/withdrawn-candidates');
		     
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
				$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

		   		$val=$this->romodel->checkfinalize_acbyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		       
		    	$list=$this->romodel->acceptedcandidate($ele_details);
		    	return view('admin.pc.ro.listaccepted',['user_data' => $d,'lists'=>$list,'checkval'=>$val,'showpage'=>'candidate','edetails'=>$ele_details]);
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
                return Redirect::to('/ropc/contested-application');
		      	}
		       if($s==$s1) 
		      	{
		      	\Session::flash('error_mes1', 'Dublicate Sr. number ');
                return Redirect::to('/ropc/contested-application');
		      	}
		       if($s==0) 
		      	{
		      	\Session::flash('error_mes1', 'please not entry zero');
                return Redirect::to('/ropc/contested-application');
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
                return Redirect::to('/ropc/contested-application');
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
		    
		   $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

		    if($ele_details->CONST_TYPE=="AC") { 
		    			$v= 'candidate_nomination_detail.ac_no'; $m=$ele_details->CONST_NO; 
		    		}
  			elseif($ele_details->CONST_TYPE=="PC") {  
  						$v= 'candidate_nomination_detail.pc_no'; $m=$ele_details->CONST_NO;   
  					}

		    $candn = DB::table('candidate_nomination_detail')
		   	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
		    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
		    ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
		    ->where('candidate_nomination_detail.st_code','=',$d->st_code)->where($v,'=',$m) 
		    ->where('candidate_nomination_detail.application_status','=','6')
		    ->where('m_party.PARTYTYPE','=','N')
		    ->orderBy('candidate_nomination_detail.new_srno', 'asc')
    		->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES')->get(); 
			$cands = DB::table('candidate_nomination_detail')
		   	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
		    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
		    ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
		    ->where('candidate_nomination_detail.st_code','=',$d->st_code)->where($v,'=',$m) 
		    ->where('candidate_nomination_detail.application_status','=','6')
		    ->where('m_party.PARTYTYPE','=','S')
		    ->orderBy('candidate_nomination_detail.new_srno', 'asc')
    		->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES')->get();

			$candu = DB::table('candidate_nomination_detail')
		   	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
		    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
		    ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
		    ->where('candidate_nomination_detail.st_code','=',$d->st_code)->where($v,'=',$m) 
		    ->where('candidate_nomination_detail.application_status','=','6')
		    ->where('m_party.PARTYTYPE','=','U')->orderBy('candidate_nomination_detail.new_srno', 'asc')
    		->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES')->get();

    		$candz = DB::table('candidate_nomination_detail')
		   	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
		    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
		    ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
		    ->where('candidate_nomination_detail.st_code','=',$d->st_code)->where($v,'=',$m) 
		    ->where('candidate_nomination_detail.application_status','=','6')
		    ->where('m_party.PARTYTYPE','=','Z')->orderBy('candidate_nomination_detail.new_srno', 'asc')
    		->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES')->get();


		     $pc=''; $ac='';
		          if(!empty($d->ac_no))
		        	$ac=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
		        if(!empty($d->pc_no))
    				$pc=$this->commonModel->getpcbypcno($d->st_code,$d->pc_no);
				
				$state=$this->commonModel->getstatebystatecode($d->st_code);
	    	    $ac=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
		    
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
		 		$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
		        $val=$this->romodel->checkfinalize_acbyro($d->st_code,$d->ac_no,$d->officerlevel);
		        $list=$this->romodel->Symbolcandidate($ele_details);
		        $sym=getsymbollist();
               
		    	return view('admin.pc.ro.symboldetails',['user_data' => $d,'lists'=>$list,'checkval'=>$val,'showpage'=>'candidate','edetails'=>$ele_details,'sym'=>$sym]);
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
		    
		    	return view('admin.pc.ro.symbolassign',['user_data' => $d,'lists'=>$list,'showpage'=>'candidate']);
		    	}
		    else {
		    	 return Redirect::to('/ropc');
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
		         
		         
		          \Session::flash('success_mes', 'Symbol successfully Assign');
                  return Redirect::to('/ropc/symbol-upload');
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
		     $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);  
        $check_ac = DB::table('candidate_finalized_ac')->where('st_code',$d->st_code)
        		->where('const_no',$ele_details->CONST_NO)
        		->where('const_type',$ele_details->CONST_TYPE)
        		->where('election_id',$ele_details->ELECTION_ID)->first();

        	$date = Carbon::now();
        	$currentTime = $date->format('Y-m-d H:i:s');
		 
		      $otp="123456"; //rand(100000,999999);
		      $mob_message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Candidate Portal for finalized AC . Please enter the OTP to proceed.Your OTP will be valid till 30 minutes.Do not share this OTP,  Team ECI";
		 
		if(!isset($check_ac)) {
			$st = array('st_code'=>$d->st_code,'const_no'=>$ele_details->CONST_NO,'const_type'=>$ele_details->CONST_TYPE,'election_id'=>$ele_details->ELECTION_ID,'finalized_ac'=>'0','mobile_otp'=>$otp,'otp_time' => $currentTime,'created_at'=>date("Y-m-d H:i:s"),'created_by'=>$d->officername); 
			$r=$this->commonModel->insertData('candidate_finalized_ac',$st);
			$check_ac = DB::table('candidate_finalized_ac')->where('st_code',$d->st_code)->where('const_no',$ele_details->CONST_NO)->where('const_type',$ele_details->CONST_TYPE)->where('election_id',$ele_details->ELECTION_ID)->first();
            }
        else{  
        	$st = array('mobile_otp'=>$otp,'otp_time' => $currentTime);
        	$i = DB::table('candidate_finalized_ac')->where('id',$check_ac->id)->update($st);
         	}
          
		   $html =$otp; 
		    if(!empty($d->email)) {  
						//sendotpmail($d->email,$d->name,$html);  
						}
				// $response = SmsgatewayHelper::sendOtpSMS($mob_message,$d->Phone_no); 
				return view('admin.pc.ro.finalize-ac',['user_data' => $d,'lists'=>$check_ac,'otp'=>$otp,'showpage'=>'candidate','otp_time'=>$currentTime]);
		         }
	        else {
	         		return Redirect::to('/officer-login');
	        	 }		
			}
	function finalize_candidate(Request $request)
			{  
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
                  return Redirect::to('/ropc/finalize-ac');
		       }
		      if($otp_time<$currentTime) {
		      	 \Session::flash('ro_opt_messsage', 'Your Otp time Expair');
                  return Redirect::to('/ropc/finalize-ac');
		       }
		       $ins_data = array('finalized_ac'=>'1','finalized_message'=>$finalized_message);
				$state=$this->commonModel->getstatebystatecode($d->st_code);
	    		$ac=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
				$ddeo = DB::table('officer_login')->where('st_code',$d->st_code)
						->where('dist_no',$d->dist_no)->where('officerlevel','DEO')->first();
				$cceo = DB::table('officer_login')->where('st_code',$d->st_code)
						->where('officerlevel','CEO')->first();

		$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

                $list=$this->romodel->finalize_candidate_ac($st_code,$cons_no,$ele_details->CONST_TYPE,$ins_data); 
               
		        $html =$state->ST_NAME; 
				 
				sendlevelmail($ddeo->email,$d->name,$ac->AC_NAME,$html);
				sendlevelmail($cceo->email,$d->name,$ac->AC_NAME,$html);

		          \Session::flash('success_mes', 'Finalize AC successfully Assign');
                  return Redirect::to('/ropc/contested-application');
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
		         
                $list=$this->romodel->public_affidavit_ac($d->st_code,$d->ac_no);
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
		      
		    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

		    if($ele_details->CONST_TYPE=="AC") { 
		    			$v= 'candidate_nomination_detail.ac_no'; $m=$ele_details->CONST_NO; 
		    		}
  			elseif($ele_details->CONST_TYPE=="PC") {  
  						$v= 'candidate_nomination_detail.pc_no'; $m=$ele_details->CONST_NO;   
  					}

		    $cand = DB::table('candidate_nomination_detail')
		   	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
		    ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
		    ->where('candidate_nomination_detail.st_code','=',$d->st_code)->where($v,'=',$m) 
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
		    
    public function listnomination(request $request){
        if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
            $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
            $val=$this->romodel->checkfinalize_acbyro($ele_details->ST_CODE,$ele_details->CONST_NO,'PC');
            
            $cand_status='';
            $cand_status = $request->input('cand_status');
            $list=$this->romodel->Allcandidatelist($ele_details,$cand_status);
            $status=allstatus();
            
            return view('admin.pc.ro.listnomination', ['user_data' => $d,'lists'=>$list,'status'=>$cand_status,'checkval'=>$val,'showpage'=>'candidate','status_list'=>$status,'edetails'=>$ele_details]);
        }
        else {
            return redirect('/officer-login');
        }
	} 

	public function roOfficerLogindetailsList(request $request){
        if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
            $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          // dd($d);
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

}  // end class  
