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
		use App\adminmodel\ROPCNomModel;
		use App\Classes\xssClean;
    use Illuminate\Support\Facades\Crypt;

		//use Spatie\MixedContentScanner\MixedContentScanner;
class RoPCnomController extends Controller
{
    //
   public function __construct()
        {   

			$this->middleware('adminsession');
			$this->middleware('ro');
			$this->middleware('clean_url');
			$this->commonModel = new commonModel();
			$this->CandidateModel = new CandidateModel();
			$this->romodel = new ROPCModel();
			$this->ropcmodel = new ROPCNomModel();
			$this->xssClean = new xssClean;
			if(!Auth::check()){ 
        		return redirect('/officer-login');
        	}
        }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
 	protected function guard(){
        return Auth::guard('admin');
    	}
    
    public function createnomination()
				{      
				if(Auth::check()){ 
				    $user = Auth::user();
				    $d=$this->commonModel->getunewserbyuserid($user->id); 
				    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');

				    $st=$this->commonModel->getstatebystatecode($ele_details->ST_CODE);
				    $pc=$this->commonModel->getallacbypcno($ele_details->ST_CODE,$ele_details->CONST_NO);
				    $dist=$this->commonModel->getdistrictbydistrictno($ele_details->ST_CODE,$d->dist_no);
				    $all_state=$this->commonModel->getallstate();
				    $all_dist=$this->commonModel->getalldistrictbystate($ele_details->ST_CODE);
				    $all_ac=$this->commonModel->getacbystate($ele_details->ST_CODE);

                    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['stcode']=$ele_details->ST_CODE;
                    $data['constno']=$ele_details->CONST_NO;
                    $data['distno']=$d->dist_no;
				            $data['getStateDetail']=$st;
                    $data['getDetails']=$pc;
                    $data['disttDetails']=$dist;
                    $data['all_state']=$all_state;
                    $data['all_dist']=$all_dist;
                    $data['all_ac']=$all_ac;
 						return view('admin.pc.ro.createnomination',$data);	
 						           
					}
					else {
		              return redirect('/officer-login');
		        	  }
				}  
     
		public function getSymbol(request $request){
        if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
            $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
           
            
        $partyid = $request->input('partyid');
        $party=getpartybyid($partyid);
        if($party->PARTYTYPE=="N")
          {
                //$partyDetails = $this->commonModel->getparty($partyid);
                $symData =$this->commonModel->getsymbol($party->PARTYSYM);
          
           $st='';
          if($symData != ''){
            $st .='<option value="'.$symData->SYMBOL_NO.'">'.$symData->SYMBOL_NO."-".$symData->SYMBOL_DES.'-'.$symData->SYMBOL_HDES.'</option>'; 
              return $st;
              }
            else{
              $newsym =getsymbolbyid('200');
              $st .='<option value="'.$newsym->SYMBOL_NO.'">'.$newsym->SYMBOL_NO."-".$newsym->SYMBOL_DES.'-'.$newsym->SYMBOL_HDES.'</option>'; 
              return $st;
             } 
          }
        elseif($party->PARTYTYPE=="S")
          {    
           $partyDetails = DB::table('m_party')
                    ->leftjoin('d_party', 'm_party.PARTYABBRE', '=', 'd_party.PARTYABBRE') 
                    ->where('m_party.PARTYTYPE','=','S')
                    ->where('d_party.ST_CODE','=',$ele_details->ST_CODE)
                    ->where('m_party.CCODE','=',$partyid)
                    ->select('m_party.*')->first();
          $symData='';
          if(isset($partyDetails))
              {
               $symData =$this->commonModel->getsymbol($partyDetails->PARTYSYM); 
              }         
          
          $st='';
          if($symData != ''){
            $st .='<option value="'.$symData->SYMBOL_NO.'">'.$symData->SYMBOL_NO."-".$symData->SYMBOL_DES.'-'.$symData->SYMBOL_HDES.'</option>'; 
              return $st;
              }
            else{
              $newsym =getsymbolbyid('200');
              $st .='<option value="'.$newsym->SYMBOL_NO.'">'.$newsym->SYMBOL_NO."-".$newsym->SYMBOL_DES.'-'.$newsym->SYMBOL_HDES.'</option>'; 
              return $st;
             } 
          }
         elseif($party->PARTYTYPE=="U" )
          {    
          $partyDetails = DB::table('m_party')
                    ->leftjoin('d_party', 'm_party.PARTYABBRE', '=', 'd_party.PARTYABBRE') 
                    ->where('m_party.PARTYTYPE','=','S')
                    ->where('d_party.ST_CODE','=',$ele_details->ST_CODE)
                    ->where('m_party.CCODE','=',$partyid)
                    ->select('m_party.*')->first();
          $symData='';
          if(isset($partyDetails))
              {
               $symData =$this->commonModel->getsymbol($partyDetails->PARTYSYM); 
              }    
           $st='';
          if($symData != ''){
            $st .='<option value="'.$symData->SYMBOL_NO.'">'.$symData->SYMBOL_NO."-".$symData->SYMBOL_DES.'-'.$symData->SYMBOL_HDES.'</option>'; 
              return $st;
              }
            else{
              $newsym =getsymbolbyid('200');
              $st .='<option value="'.$newsym->SYMBOL_NO.'">'.$newsym->SYMBOL_NO."-".$newsym->SYMBOL_DES.'-'.$newsym->SYMBOL_HDES.'</option>'; 
              return $st;
             } 
          }
        elseif($party->PARTYTYPE=="Z" || $party->PARTYTYPE=="0")
          { 
           $partyDetails = DB::table('m_party')
                    ->leftjoin('d_party', 'm_party.PARTYABBRE', '=', 'd_party.PARTYABBRE') 
                    ->where('m_party.PARTYTYPE','=','S')
                    ->where('d_party.ST_CODE','=',$ele_details->ST_CODE)
                    ->where('m_party.CCODE','=',$partyid)
                    ->select('m_party.*')->first();
          $symData='';
          if(isset($partyDetails))
              {
               $symData =$this->commonModel->getsymbol($partyDetails->PARTYSYM); 
              }    
           $st='';
          if($symData != ''){
            $st .='<option value="'.$symData->SYMBOL_NO.'">'.$symData->SYMBOL_NO."-".$symData->SYMBOL_DES.'-'.$symData->SYMBOL_HDES.'</option>'; 
              return $st;
              }
            else{
              $newsym =getsymbolbyid('200');
              $st .='<option value="'.$newsym->SYMBOL_NO.'">'.$newsym->SYMBOL_NO."-".$newsym->SYMBOL_DES.'-'.$newsym->SYMBOL_HDES.'</option>'; 
              return $st;
             } 
             
          }
          
            }
            else {
                  return redirect('/officer-login');
               }
    
  }
	public function getSymboltype(request $request){
				$r = getsymboltypelist('T');
				return $r;	 
		
			}
	public function getDistricts(request $request){
		$stcode = $request->input('stcode');
		$districtData = $this->commonModel->getalldistrictbystate($stcode);
		return $districtData; 
	}
	public function getaclist(request $request){  
		$district = $request->input('district');
		$stcode = $request->input('stcode');
		$acdata = $this->commonModel->getAcByst($stcode,$district);

		return $acdata; 
	}   
    public function insertnomination(request $request)
        {     
        if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
             $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
             
           
            $this->validate(  
                $request, 
                [
                  'party_id' => 'required',
                  'symbol_id' => 'required',
                  'profileimg'=>'image|mimes:jpeg,png,jpg|max:200',
                  'name'=>'required',
                  'hname'=>'required',
                  'cand_vname'=>'required',
                  'fname'=>'required',
                  //'fhname'=>'required',
                  'age'=>'required|numeric',
                 // 'cand_mobile'=>'regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                  'gender' => 'required|string|in:male,female,third',
                  'addressline1'=>'required',
                 // 'addresshline1'=>'required',
                  'state'=>'required',
                  'district'=>'required',
                  'ac'=>'required',
                  'cand_category'=>'required',
                ],
                [ 
                  'party_id.required' => 'Please select party',
                  'symbol_id.required' => 'Please select symbol', 
                  //'profileimg.required'=>'Please enter profile image',
                  'profileimg.image'=>'Please only jpg, jpeg, png format',
                  'profileimg.max'=>'image size maximum 200kb',
                  'name.required'=>'Please enter name in english',
                  'hname.required'=>'Please enter name in hindi',
                  'cand_vname.required'=>'Please enter name in vernacular',
                  'fname.required'=>'Please enter father/husband name in english',
                 // 'fhname.required'=>'Please enter father/husband name in hindi',
                  'age.required'=>'Please enter candidate age',
                  'age.numeric'=>'Please enter valid age',
                  //'age.digits'=>'Please enter valid age',
                 // 'cand_mobile.required'=>'Please enter validate mobileno',
                  //'cand_mobile.min'=>'Mobile Number minimum 10 digit',
                  //'cand_mobile.unique'=>'Mobile number All ready Exists',
                  'addressline1.required'=>'Please enter address',
                  //'addresshline1.required'=>'Please enter hindi address',
                  'state.required'=>'Please select state',
                  'district.required'=>'Please select district',
                  'ac.required'=>'Please select ac',
                  'cand_category.required'=>'Please select candidate category',
                ]
            ); 
            
           $cand_mob=trim($this->xssClean->clean_input(Check_Input($request->input('cand_mobile'))));
           $cand_name=trim($this->xssClean->clean_input(Check_Input($request->input('name')))) ;
           $cand_fname=trim($this->xssClean->clean_input(Check_Input($request->input('fname'))));
           $dob = $request->input('dob');
           $age = $request->input('age'); 
           
           
            
           $shares = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id') 
                ->where('candidate_nomination_detail.st_code', $ele_details->ST_CODE)
                ->where('candidate_nomination_detail.pc_no', $ele_details->CONST_NO)
                ->where('candidate_personal_detail.cand_name', ucwords($cand_name))
                ->where('candidate_personal_detail.candidate_father_name', ucwords($cand_fname))
                ->where('candidate_personal_detail.cand_age',$age)
                ->where('candidate_nomination_detail.application_status','<>','11')
                ->first();
                
            if(isset($shares)) {
              \Session::flash('error_mes', 'Nomination with this candidate details are already entered. If this is the multiple nominations case. Go to multiple nominations menu and select the candidate');
                return Redirect::to('/ropc/multiplenomination');
            }  
            
            $party=$this->commonModel->getparty($request->input('party_id'));
             
            if($party->PARTYTYPE=="S"){ 
            $partyDetails = DB::table('m_party')
                    ->leftjoin('d_party', 'm_party.PARTYABBRE', '=', 'd_party.PARTYABBRE') 
                    ->where('m_party.PARTYTYPE','=','S')
                    ->where('d_party.ST_CODE','=',$ele_details->ST_CODE)
                    ->where('m_party.CCODE','=',$party->CCODE)
                    ->select('m_party.*')->first();
            if(isset($partyDetails)){
                  $partytype = $party->PARTYTYPE;
             }
             else{
                 $partytype ='U';
             }
           } 
           else {
                 $partytype = $party->PARTYTYPE;
           }
            
            $candImage = '';
            $request->file('profileimg') ;
           
            $candName = $request->input('name') ;
             
            if($request->input('addressline2') != ''){
                $addressEnglish = $this->xssClean->clean_input(Check_Input($request->input('addressline1'))).' , '.$this->xssClean->clean_input(Check_Input($request->input('addressline2')));
            }else{
                $addressEnglish = $this->xssClean->clean_input(Check_Input($request->input('addressline1'))) ;
            }
            if($request->input('addresshline2') != ''){
             $addressHindi =$this->xssClean->clean_input(Check_Input($request->input('addresshline1'))). ' , ' .$this->xssClean->clean_input(Check_Input($request->input('addresshline2')));
            }else{
                $addressHindi = $this->xssClean->clean_input(Check_Input($request->input('addresshline1')));
            }
           if($ele_details->CONST_TYPE=="PC")
             $g = DB::table('candidate_nomination_detail')->where('st_code',$ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)->get();

            $mslno=$g->max('cand_sl_no'); $mslno++;
               //
            $candPersonalData = array(
                'cand_name'=>ucwords($this->xssClean->clean_input(Check_Input($request->input('name')))),
                'cand_hname'=>$this->xssClean->clean_input(Check_Input($request->input('hname'))),
                'cand_vname'=>$this->xssClean->clean_input(Check_Input($request->input('cand_vname'))),
                'cand_alias_name'=>$this->xssClean->clean_input(Check_Input($request->input('aliasname'))),
                'cand_alias_hname'=>$this->xssClean->clean_input(Check_Input($request->input('aliashname'))),

                'candidate_father_name'=>ucwords($this->xssClean->clean_input(Check_Input($request->input('fname')))),
                'cand_email'=>$this->xssClean->clean_input(Check_Input($request->input('email'))),
                'cand_mobile'=>$this->xssClean->clean_input(Check_Input($request->input('cand_mobile'))),
                'cand_fhname'=>ucwords($this->xssClean->clean_input(Check_Input($request->input('fhname')))),
                'cand_gender'=>$this->xssClean->clean_input(Check_Input($request->input('gender'))),
                'candidate_residence_address'=>$addressEnglish,
                'candidate_residence_addressh'=>$addressHindi,
                'candidate_residence_stcode'=>$this->xssClean->clean_input(Check_Input($request->input('state'))),
                'candidate_residence_districtno'=>$this->xssClean->clean_input(Check_Input($request->input('district'))),
                'candidate_residence_acno'=>$this->xssClean->clean_input(Check_Input($request->input('ac'))),
                'cand_category'=>$this->xssClean->clean_input(Check_Input($request->input('cand_category'))),
                'cand_age'=>$age,
                'cand_panno'=>$this->xssClean->clean_input(Check_Input($request->input('panno'))),
                
                'created_by'=>$d->officername,
                'created_at'=>date('Y-m-d h:i:s'),
                'added_create_at'=>date('Y-m-d'),
            );
             //print_r($candPersonalData);exit;
            $randno = rand(1000,9999);
             
            $n = DB::table('candidate_personal_detail')->insert($candPersonalData);  
            $cid = DB::getPdo()->lastInsertId();
            if($cid != ''){
                    $ccode = $d->st_code . $cid . $randno . date('Ymd');
                $candNomData = array(
                    'election_id'=>$ele_details->ELECTION_ID,
                    'party_id'=>$request->input('party_id'),
                    'cand_sl_no'=>$mslno,
                    'new_srno'=>$mslno,
                    'symbol_id'=>$request->input('symbol_id'),
                    'pc_no'=>$ele_details->CONST_NO,
                    'st_code'=>$ele_details->ST_CODE,
                    'candidate_id'=>$cid,
                    'district_no'=>$d->dist_no,
                    'date_of_submit'=>date('Y-m-d'),
                    'qrcode'=>$ccode,
                    'created_by'=>$d->officername,
                    'created_at'=>date('Y-m-d h:i:s'),
                    'added_create_at'=>date('Y-m-d'),
                    'application_status'=>'1',
                    'cand_party_type'=> $partytype,
                    'scheduleid'=>$ele_details->ScheduleID,
                    'election_type_id'=>$ele_details->ELECTION_TYPEID,
                    'state_phase_no'=>$ele_details->StatePHASE_NO,
                    'm_election_detail_ccode'=>$ele_details->CCODE
                );     
                $n = DB::table('candidate_nomination_detail')->insert($candNomData);
                $lastid=DB::getPdo()->lastInsertId();

                if(!empty($request->file('profileimg'))){
                    $file = $request->file('profileimg');
                    $cyear = date('Y');
                    
                    //Move Uploaded File
                    $newfile = $candName . '-' . $cyear . '-' . $cid;
                    $fileNewName =$newfile.'.'.$request->file('profileimg')->getClientOriginalExtension();
                    $destinationPath ='uploads/candprofile/'.$cyear.'/PC/E'.$ele_details->ELECTION_ID.'/'.$ele_details->ST_CODE.'/';
                    $file->move($destinationPath,$fileNewName);
                  
                    $candImage = $destinationPath . $fileNewName ;
                } 
                $updateCandData = DB::update('update candidate_personal_detail set cand_image = ? where candidate_id = ?',[$candImage,$cid]);
                if($cand_mob!='') {
                  $mob_message="Now you can check your nomination/ permission status through suvidha candidate android app. Download from here https://goo.gl/YGoMmM and login using this mobile number.";
                  $response = SmsgatewayHelper::gupshup($cand_mob,$mob_message);
                }
                $lid=base64_encode($lastid);
                \Session::flash('success_mes', 'Candidate personal details added please upload affidavit');
                return Redirect::to('/ropc/candidateaffidavit/'.$lid);
            }
        }
        else{
            return redirect('/officer-login');
        }
    }  
 
	 
   public function updatenominationform($nomid1)
			{      
				if(Auth::check()){ 
		            $user = Auth::user();
		            $d=$this->commonModel->getunewserbyuserid($user->id); 
		             $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
           
               
				    $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
				    $nomid   = Crypt::decrypt($nomid1);  
		         if(isset($ele_details))
			            $seched=getschedulebyid($ele_details->ScheduleID);
			            else 
			            	$seched='';
			           $sechdul=checkscheduledetails($seched);
		          if($check_finalize->finalized_ac=='1')
                    {
                     \Session::flash('finalize_mes', 'Candidate Nomination is Finalize');
                      return Redirect::to('/ropc/listnomination');  
                    }  
		            $nom=getById('candidate_nomination_detail','nom_id',$nomid); 
					$cand=getById('candidate_personal_detail','candidate_id',$nom->candidate_id); 
					$st_code=$ele_details->ST_CODE;
				    $const_no=$ele_details->CONST_NO;
				    $distno=$d->dist_no;

				    $st=getallstate($st_code);
				    $pc=getpcbypcno($st_code,$const_no);
				    $dist=getdistrictbydistrictno($st_code,$distno);
				    $all_state=getallstate();
				    $all_dist=getalldistrictbystate($st_code);
				    $all_ac=getacbystate($st_code);
				     
				 
    		    
					return view('admin.pc.ro.updatenomination', ['user_data' => $d,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details,'nomid'=>$nomid,'nomDetails'=>$nom,'persoanlDetails'=>$cand, 'ele_details'=>$ele_details,'all_state'=>$all_state,'all_dist'=>$all_dist,'all_ac'=>$all_ac,'pc'=>$pc,'nomid1'=>$nomid1,]);	           
				}
				else{
					return redirect('/officer-login');
				}
			}  
public function updatenomination(request $request, $nomid1){
	 
	if(Auth::check()){ 
		            $user = Auth::user();
		            $d=$this->commonModel->getunewserbyuserid($user->id); 
		             $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');

       $nomid=Crypt::decrypt($nomid1);       
		$this->validate(
    			$request,
    			[ 
    			  'party_id' => 'required',
    			  'symbol_id' => 'required',
    			  'name'=>'required',
    			  'hname'=>'required',
    			  'fname'=>'required',
            'cand_vname'=>'required',
    			  //'fhname'=>'required',
            'gender' => 'required|string|in:male,female,third',
            'age'=>'required|numeric',
            'addressline1'=>'required',
            'state'=>'required',
            'district'=>'required',
            'ac'=>'required',
            'cand_category'=>'required',
    			  ],
    			[ 
    			  'party_id.required' => 'Please select party',
    			  'symbol_id.required' => 'Please select symbol',
    			  'name.required'=>'Please enter name in english',
            'hname.required'=>'Please enter name in hindi',
            'cand_vname.required'=>'Please enter name in vernacular',
    			  'fname.required'=>'Please enter father/husband name in english',
    			  //'fhname.required'=>'Please enter father/husband name in hindi',
    			  'addressline1.required'=>'Please enter address',
            'state.required'=>'Please select state',
            'district.required'=>'Please select district',
            'ac.required'=>'Please select ac',
            'cand_category.required'=>'Please select candidate category',
            'age.required'=>'Please enter candidate age',
            'age.numeric'=>'Please enter valid age',
            //'age.digits'=>'Please enter valid age',
    			   
    			]
    		);
	    
		      	$dob = $request->input('dob');
            $age = $request->input('age');;
            $party=getpartybyid($request->input('party_id'));
            $nom=getById('candidate_nomination_detail','nom_id',$nomid); 
          
            $getPersonalDetails = getById('candidate_personal_detail','candidate_id',$nom->candidate_id); ;
			      $candimg = $getPersonalDetails->cand_image ;

              if($party->PARTYTYPE=="S"){ 
            $partyDetails = DB::table('m_party')
                    ->leftjoin('d_party', 'm_party.PARTYABBRE', '=', 'd_party.PARTYABBRE') 
                    ->where('m_party.PARTYTYPE','=','S')
                    ->where('d_party.ST_CODE','=',$ele_details->ST_CODE)
                    ->where('m_party.CCODE','=',$party->CCODE)
                    ->select('m_party.*')->first();
            if(isset($partyDetails)){
                  $partytype = $party->PARTYTYPE;
             }
             else{
                 $partytype ='U';
             }
           } 
           else {
                 $partytype = $party->PARTYTYPE;
           }
            $candImage = '';
            $request->file('profileimg') ;

            $constType = $ele_details->CONST_TYPE ;
			      $stcode = $ele_details->ST_CODE ;
			      $electionType = $ele_details->CONST_TYPE;
		

            $candName = $request->input('name') ;
             
            if($request->input('addressline2') != ''){
                $addressEnglish = $this->xssClean->clean_input(Check_Input($request->input('addressline1'))).','.$this->xssClean->clean_input(Check_Input($request->input('addressline2')));
            }else{
                $addressEnglish = $this->xssClean->clean_input(Check_Input($request->input('addressline1'))) ;
            }
            if($request->input('addresshline2') != ''){
             $addressHindi =$this->xssClean->clean_input(Check_Input($request->input('addresshline1'))). ', ' .$this->xssClean->clean_input(Check_Input($request->input('addresshline2')));
            }else{
                $addressHindi = $this->xssClean->clean_input(Check_Input($request->input('addresshline1')));
            }
         //
		$candPersonalData = array(
				'cand_name'=>$this->xssClean->clean_input(Check_Input($request->input('name'))),
                'cand_hname'=>$this->xssClean->clean_input(Check_Input($request->input('hname'))),
                'cand_vname'=>$this->xssClean->clean_input(Check_Input($request->input('cand_vname'))),
                'cand_alias_name'=>$this->xssClean->clean_input(Check_Input($request->input('aliasname'))),
                'cand_alias_hname'=>$this->xssClean->clean_input(Check_Input($request->input('aliashname'))),
                'candidate_father_name'=>$this->xssClean->clean_input(Check_Input($request->input('fname'))),
                'cand_email'=>$this->xssClean->clean_input(Check_Input($request->input('email'))),
                'cand_mobile'=>$this->xssClean->clean_input(Check_Input($request->input('cand_mobile'))),
                'cand_fhname'=>$this->xssClean->clean_input(Check_Input($request->input('fhname'))),
                'cand_gender'=>$this->xssClean->clean_input(Check_Input($request->input('gender'))),
                'candidate_residence_address'=>$addressEnglish,
                'candidate_residence_addressh'=>$addressHindi,
                'candidate_residence_stcode'=>$this->xssClean->clean_input(Check_Input($request->input('state'))),
                'candidate_residence_districtno'=>$this->xssClean->clean_input(Check_Input($request->input('district'))),
                'candidate_residence_acno'=>$this->xssClean->clean_input(Check_Input($request->input('ac'))),
                'cand_category'=>$this->xssClean->clean_input(Check_Input($request->input('cand_category'))),
                'cand_age'=>$age,
                'cand_panno'=>$this->xssClean->clean_input(Check_Input($request->input('panno'))),
                //'cand_dob'=>date('Y-m-d', strtotime($request->input('dob'))),
                'updated_by'=>$d->officername,
                'updated_at'=>date('Y-m-d h:i:s'),
                'added_update_at'=>date('Y-m-d'),
			  
		);
	     $n = DB::table('candidate_personal_detail')->where('candidate_id', $nom->candidate_id)->update($candPersonalData);
		
		if($nom->candidate_id != ''){
			$candNomData = array(
				 
				'party_id'=>$request->input('party_id'),
				'symbol_id'=>$request->input('symbol_id'),
				'added_update_at'=>date('Y-m-d'), 
				'updated_by'=>$d->officername,
				'updated_at'=>date('Y-m-d'),
				'cand_party_type'=> $partytype
			);
			
			if(!empty($request->file('profileimg'))){
				$file = $request->file('profileimg');
				$cyear = date('Y');
				 
				$newfile = $candName . '-' . $cyear . '-' .  date('Ymdhis');
				$fileNewName = $newfile . '.' . $request->file('profileimg')->getClientOriginalExtension();
         $destinationPath ='uploads/candprofile/'.$cyear.'/PC/E'.$ele_details->ELECTION_ID.'/'.$ele_details->ST_CODE.'/';
				 
				$file->move($destinationPath,$fileNewName);
			  
				$candImage = $destinationPath . $fileNewName ;
			} else{
				$candImage = $candimg;
			}
			$n = DB::table('candidate_nomination_detail')->where('candidate_id', $nom->candidate_id)->where('nom_id',$nomid)->update($candNomData);

			$updateCandData = DB::update('update candidate_personal_detail set cand_image = ? where candidate_id = ?',[$candImage,$nom->candidate_id]);
		  
			\Session::flash('success_mes', 'Candidate profile has been successfully Updated');
			return Redirect::to('/ropc/listnomination');
		}
	}
	else{
		return redirect('/officer-login');
	}
}
 
public function candidateaffidavit($lastid=''){
    if(Auth::check()){ 

            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
             $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
        $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
         $lastid=base64_decode($lastid);
         if(isset($ele_details))
              $seched=getschedulebyid($ele_details->ScheduleID);
              else 
                $seched='';
             $sechdul=checkscheduledetails($seched);
             //print_r($seched);
             //dd($sechdul);
           // if($check_finalize->finalized_ac=='1')
           //          {
           //           \Session::flash('finalize_mes', 'Candidate Nomination is Finalize');
           //            return Redirect::to('/ropc/listnomination');  
           //          }   
      $list = DB::table('candidate_nomination_detail')
              ->where('candidate_nomination_detail.st_code','=',$ele_details->ST_CODE)
              ->where('pc_no','=',$ele_details->CONST_NO)->where('application_status','<>','11') 
            ->orderBy('candidate_nomination_detail.nom_id', 'ASC')->get(); 
           
      $affidavitlist = DB::table('candidate_affidavit_detail')
          ->where('st_code','=',$ele_details->ST_CODE)->where('pc_no','=',$ele_details->CONST_NO) 
            ->orderBy('added_create_at', 'ASC')->get();  
     
    return view('admin.pc.ro.candidateaffidavit',['user_data' => $d,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details,'affidavitlist'=>$affidavitlist,'cand_data' => $list,'lastid'=>$lastid]);
}
  else {
    return redirect('/officer-login');
  }
 }
 
public function candstoreaffidavit(request $request){  
       if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
             $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
	   $this->validate(
                		$request,
                		[ 
                			"candidate_id" => "required",
                			'affidavit' => 'required',
                			'affidavit' => 'mimes:pdf|max:10000',
                     
                		],
                		[ 
                      'candidate_id.required' => 'Please select candidate',
                		  'affidavit.required' => 'Please upload the valid pdf affidavit',
                      'affidavit.mimes' => 'Please upload the valid pdf affidavit',
                      'affidavit.max' => 'Please upload the maximum size 10mb',
                		]
                	);
	    $ext=$request->file('affidavit')->getClientOriginalExtension();
  	    if($ext!="pdf")
    	      {
    	      	    \Session::flash('error_mes', 'Only Pdf File uploaded');
    			         return Redirect::to('/ropc/candidateaffidavit/'.$request->input('candidate_id'));
    	      }
       // dd($ele_details);
    		$stateid = $ele_details->ST_CODE;
    		$electionid = $ele_details->ELECTION_ID;
    		$cons_no = $ele_details->CONST_NO;
    		$electionName = $ele_details->CONST_TYPE ;
    		$electionType= $ele_details->CONST_TYPE ;
    		$candidate_id  = $request->input('candidate_id');
    		$affidavit_name  = $request->input('affidavit_name');
    		$cdate = date('d-M-Y'); $acdate = date('Y-m-d');
    		$created_at = date('Y-m-d h:i:s');
    		$updated_at = date('Y-m-d h:i:s');
		
    		$nom=getById('candidate_nomination_detail','nom_id',$candidate_id); 
    		$cand=getById('candidate_personal_detail','candidate_id',$nom->candidate_id); 
    		 
    		$candName = $cand->cand_name ;
    		$nom_id=$nom->nom_id;
        $cid=$nom->candidate_id;
     
		//Get Affidavit Details
		$getAffidavitDetails = getById('candidate_affidavit_detail','nom_id',$nom_id); 
		//Get State Details
		$st=getstatebystatecode($ele_details->ST_CODE);  
		$stateName =$st->ST_NAME;  
		$file = $request->file('affidavit');
		$cyear = date('Y');
		if($request->file('affidavit')){
			//Move Uploaded File
			$newfile = $stateid.date('Ymdhis');  
			$fileNewName = $newfile.'.'.$request->file('affidavit')->getClientOriginalExtension();
			//edited by waseem paste it before move function
         if(!validate_pdf_file($request->file('affidavit'))){
           \Session::flash('error_mes', 'Only Pdf File uploaded');
           return Redirect::back()->withInput($request->all());
         }
      
			$destinationPath = 'uploads/affidavit/'.$cyear.'/'.$electionName.'/E'.$ele_details->ELECTION_ID.'/'.$stateid .'/'. $cons_no;
			$file->move($destinationPath,$fileNewName);
		  
			$affidavitName = "Form 26";
			$affidavit_path = $destinationPath .'/'.$fileNewName ;
    			 if(!file_exists($affidavit_path)){
           \Session::flash('error_mes', 'File is not uploaded. Please try again.');
           return Redirect::back()->withInput($request->all());
         }
     //end by waseem
			if(!empty($getAffidavitDetails) ){
				if($request->file('affidavit') != ''){
					   $updateNomDetail = DB::update('update candidate_affidavit_detail set affidavit_path ="'.$affidavit_path . '" where nom_id = ' .$nom_id);
				}
			}else{
				DB::table('candidate_affidavit_detail')->insert([
					 ['candidate_id' => $cid, 'nom_id' => $nom_id, 'affidavit_name' => $affidavitName, 'affidavit_path' => $affidavit_path, 'created_by' => $d->officername, 'updated_by' => $d->officername, 'created_at' => $created_at, 'updated_at' => $updated_at,'st_code'=>$stateid,'pc_no'=>$cons_no,'added_create_at'=>$acdate,'added_update_at'=>$acdate]
				]);
			 
			}
		}
		 
		\Session::flash('success_mes', 'Your files has been successfully added');
			return Redirect::to('/ropc/listnomination');
	}
}
 
public function countercandaffidavit(){
	if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
             $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
		    $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		     
         if(isset($ele_details))
	            $seched=getschedulebyid($ele_details->ScheduleID);
	            else 
	            	$seched='';
	           $sechdul=checkscheduledetails($seched);
          // if($check_finalize->finalized_ac=='1')
          //           {
          //            \Session::flash('finalize_mes', 'Candidate Nomination is Finalize');
          //             return Redirect::to('/ropc/listnomination');  
          //           }    // application_status
        $list = DB::table('candidate_nomination_detail')
				 	->where('candidate_nomination_detail.st_code','=',$ele_details->ST_CODE)->where('pc_no','=',$ele_details->CONST_NO)->where('application_status','<>','11') 
		    		->orderBy('candidate_nomination_detail.nom_id', 'ASC')->get(); 
    		$affidavitlist = DB::table('candidate_counteraffidavit_detail')
				 	->where('st_code','=',$ele_details->ST_CODE)->where('pc_no','=',$ele_details->CONST_NO) 
		    		->orderBy('added_create_at', 'ASC')->get();     
			return view('admin.pc.ro.counteraffidavit', ['user_data' => $d,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details,'CounterAffidavitDetails'=>$affidavitlist,'cand_data' => $list]);
		}
	    else {
	        return redirect('/officer-login');
	    }
	}
 
	public function storecountercandaffidavit(request $request){
		if(Auth::check()){ 
			$user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
             $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
		$this->validate(
      			$request,
      			[ 
      				"candidate_id" => "required",
      				'counteraffidavit' => 'required',
      				'counteraffidavit' => 'mimes:pdf|max:10000',

      			],
      			[ 
      			  'candidate_id.required' => 'Please select candidate',
      			  'counteraffidavit.required' => 'Please upload the valid pdf affidavit',
      			  'counteraffidavit.mimes' => 'Please upload only pdf files',
      			]
      		);
		$ext=$request->file('counteraffidavit')->getClientOriginalExtension();
    	    if($ext!="pdf")
    	      {
    	      	\Session::flash('error_mes', 'Only Pdf File uploaded');
    			  return Redirect::to('/ropc/counteraffidavit');
    	      } 
         
      		$stateid = $ele_details->ST_CODE;
      		$electionid = $ele_details->ELECTION_ID;
      		$cons_no = $ele_details->CONST_NO;
      		$electionName = $ele_details->CONST_TYPE ;
      		$electionType= $ele_details->CONST_TYPE ;
      		$candidate_id  = $request->input('candidate_id');
      		$affidavit_name  = $request->input('affidavit_name');
      		$cdate = date('d-M-Y'); $acdate = date('Y-m-d');
      		$created_at = date('Y-m-d h:i:s');
      		$updated_at = date('Y-m-d h:i:s');
      		
      		$nom=getById('candidate_nomination_detail','nom_id',$candidate_id); 
      		$cand=getById('candidate_personal_detail','candidate_id',$nom->candidate_id); 
      		 
      		$candName = $cand->cand_name ;
          $nom_id=$nom->nom_id;
          $cid=$nom->candidate_id;
      		//Get Affidavit Details
      		$getAffidavitDetails = getById('candidate_counteraffidavit_detail','nom_id',$nom_id); 
      		//Get State Details
      		$st=getstatebystatecode($ele_details->ST_CODE);  
      		$stateName =$st->ST_NAME; 
      		$file = $request->file('counteraffidavit');
      		$cyear = date('Y');
  		
  		if($request->file('counteraffidavit')){
			//Move Uploaded File
			$newfile = $stateid.date('Ymdhis');  
			$fileNewName = $newfile.'.'.$request->file('counteraffidavit')->getClientOriginalExtension();
			//edited by waseem paste it before move function
         if(!validate_pdf_file($request->file('counteraffidavit'))){
           \Session::flash('error_mes', 'Only Pdf File uploaded');
           return Redirect::back()->withInput($request->all());
         }
       
			$destinationPath = 'uploads/counteraffidavit/'.$cyear.'/'.$electionName.'/E'.$ele_details->ELECTION_ID.'/'.$stateid .'/'. $cons_no;
			$file->move($destinationPath,$fileNewName);
		  
			$affidavitName = "Counter Affidavit";
			$affidavit_path = $destinationPath .'/'.$fileNewName ;
      if(!file_exists($affidavit_path)){
           \Session::flash('error_mes', 'File is not uploaded. Please try again.');
           return Redirect::back()->withInput($request->all());
         }
      
			DB::table('candidate_counteraffidavit_detail')->insert([
								['candidate_id' => $cid, 'nom_id' => $nom_id, 'affidavit_name' => $affidavitName, 'affidavit_path' => $affidavit_path, 'created_by' => $d->officername, 'updated_by' => $d->officername, 'created_at' => $created_at, 'updated_at' => $updated_at,'st_code'=>$stateid,'pc_no'=>$cons_no,'added_create_at'=>$acdate,'added_update_at'=>$acdate]
							]);
			 
			 
		}

		 	\Session::flash('success', 'Your files has been successfully added');
			  return Redirect::to('/ropc/counteraffidavit');
			 
			 
		}
	} 
    
   public function counteraffidavitdetails($nom_id){
	if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
             $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
		    $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		     $nom_id=base64_decode($nom_id);
         if(isset($ele_details))
	            $seched=getschedulebyid($ele_details->ScheduleID);
	            else 
	            	$seched='';
	           $sechdul=checkscheduledetails($seched);
          if($check_finalize->finalized_ac=='1')
                    {
                     \Session::flash('finalize_mes', 'Candidate Nomination is Finalize');
                      return Redirect::to('/ropc/listnomination');  
                    } 
    		$list = DB::table('candidate_counteraffidavit_detail')
				 	->where('st_code','=',$ele_details->ST_CODE)->where('pc_no','=',$ele_details->CONST_NO)->where('nom_id','=',$nom_id)->orderBy('added_create_at', 'ASC')->get(); 

			return view('admin.pc.ro.counteraffidavitdetails', ['user_data' => $d,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details,'list'=>$list]);
		}
	    else {
	        return redirect('/officer-login');
	    }
	}
    public function multiplenomination()
        {      
          if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 

             $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
              $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
           // dd( $check_finalize);  
               if(isset($ele_details))
                  $seched=getschedulebyid($ele_details->ScheduleID);
                  else 
                    $seched='';
                 $sechdul=checkscheduledetails($seched);
            if($check_finalize->finalized_ac=='1')
                {
                 \Session::flash('finalize_mes', 'Candidate Nomination is Finalize');
                  return Redirect::to('/ropc/listnomination');  
                }
            
             $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
             ->where('candidate_nomination_detail.st_code','=',$ele_details->ST_CODE) 
             ->where('candidate_nomination_detail.pc_no','=',$ele_details->CONST_NO)
             ->where('candidate_nomination_detail.application_status','<>','11')
             ->groupby('candidate_personal_detail.candidate_id') 
             ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_mobile','candidate_personal_detail.candidate_father_name','candidate_personal_detail.candidate_residence_acno','candidate_personal_detail.candidate_id')->get(); 
             //$list=$this->romodel->Allcandidatelist($ele_details,'','');
             
            $st_code=$ele_details->ST_CODE;
            $const_no=$ele_details->CONST_NO;
            $distno=$d->dist_no;
 
             
           return view('admin.pc.ro.multiplenomination', ['user_data' => $d,'ele_details'=>$ele_details, 'lists'=>$list,'stcode'=>$st_code,'constno'=>$const_no]);             
          }
          else {
                  return redirect('/officer-login');
                }
        } 
  public function insertmultiplenomination(request $request)
        {      
        if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
             $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
             
            $this->validate( 
                $request, 
                [
                  'party_id' => 'required',
                  'symbol_id' => 'required',
                  'candidate_name'=>'required',
                 ],
                [ 
                  'party_id.required' => 'Please select party',
                  'symbol_id.required' => 'Please select symbol', 
                  'candidate_name.required'=>'Please select candidate name',
                ]
            ); 
           $cid = $request->input('candidate_name');
           $cnt = DB::table('candidate_nomination_detail')
              ->where('candidate_id','=',$cid)->where('application_status','<>','11')->get()->count();
       
            $totalnom=$cnt;
            if($totalnom>=4)
              {
                \Session::flash('error_mes', 'Candidate multiple nominations can not be more than 4 ');
                return Redirect::to('/ropc/multiplenomination'); 
              }
            $party=$this->commonModel->getparty($request->input('party_id'));
         
            if($party->PARTYTYPE=="S"){ 
             $partyDetails = DB::table('m_party')
                    ->leftjoin('d_party', 'm_party.PARTYABBRE', '=', 'd_party.PARTYABBRE') 
                    ->where('m_party.PARTYTYPE','=','S')
                    ->where('d_party.ST_CODE','=',$ele_details->ST_CODE)
                    ->where('m_party.CCODE','=',$party->CCODE)
                    ->select('m_party.*')->first();
            if(isset($partyDetails)){
                  $partytype = $party->PARTYTYPE;
             }
             else{
                 $partytype ='U';
             }
           } 
           else {
                 $partytype = $party->PARTYTYPE;
           }
            
           if($ele_details->CONST_TYPE=="PC")
             $g = DB::table('candidate_nomination_detail')->where('st_code',$ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)->get();

            $mslno=$g->max('cand_sl_no'); $mslno++;
             
            $randno = rand(1000,9999);
            
            if($cid != ''){
                    $ccode = $d->st_code . $cid . $randno . date('Ymd');
                $candNomData = array(
                    'election_id'=>$ele_details->ELECTION_ID,
                    'party_id'=>$request->input('party_id'),
                    'cand_sl_no'=>$mslno,
                    'new_srno'=>$mslno,
                    'symbol_id'=>$request->input('symbol_id'),
                    'pc_no'=>$ele_details->CONST_NO,
                    'ST_CODE'=>$ele_details->ST_CODE,
                    'candidate_id'=>$cid,
                    'district_no'=>$d->dist_no,
                    'date_of_submit'=>date('Y-m-d'),
                    'qrcode'=>$ccode,
                    'created_by'=>$d->officername,
                    'created_at'=>date('Y-m-d h:i:s'),
                    'added_create_at'=>date('Y-m-d'),
                    'application_status'=>'1',
                    'cand_party_type'=> $partytype,
                    'scheduleid'=>$ele_details->ScheduleID,
                    'election_type_id'=>$ele_details->ELECTION_TYPEID,
                    'state_phase_no'=>$ele_details->StatePHASE_NO,
                    'm_election_detail_ccode'=>$ele_details->CCODE
                );
                $n = DB::table('candidate_nomination_detail')->insert($candNomData);
                $lastid=DB::getPdo()->lastInsertId();
                 $lid=base64_encode($lastid);
                \Session::flash('success_mes', 'Candidate nomination successfully added');
                return Redirect::to('/ropc/candidateaffidavit/'.$lid);
            }
        }
        else{
            return redirect('/officer-login');
        }
    }
  

}  // end class  
