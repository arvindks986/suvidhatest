<?php
namespace App\Http\Controllers\Expenditure;

use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
//INCLUDING CLASSES AND HELPERS
use App\Helpers\SmsgatewayHelper;
use Illuminate\Support\Facades\Auth;
use App\commonModel;
use Session;
use mpdf;
use App\Classes\xssClean;
use Illuminate\Http\Request;
use App\Classes\secureCode;
use App\models\Expenditure\NatureofExpModel;
use App\models\Expenditure\ExpSchModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;
use Validator;

class CandidateController extends Controller
{
    

    //CALLING AUTH MIDDLEWARE STARTS
    public function __construct()
    {
        $this->commonModel = new commonModel();
        $this->expschmodel = new ExpSchModel();
    }
    //CALLING AUTH MIDDLEWARE ENDS
    
######################################ECRP LOGIN BY NIRAJ#########################################
    public function index()
    {  
      $users=Session::get('admin_login_details');
        $user = Auth::user();
       if(session()->has('admin_login')){ 
         
        return Redirect::to('/adminhome');
      }
      else{  
            return view('auth/ecrp-login');
          }
        
    }
	 //USING TRAIT FOR COMMON FUNCTIONS
    use CommonTraits;
    
    //LOGIN FUNCTION STARTS HERE
    public function postlogin(Request $request){
      //REGISTRATION TRY CATCH STARTS HERE
        try{
         
           $this->validate($request,[
                    'mobile'          => 'required|regex:/^\S*$/u|numeric|digits:10',
                    'captcha'         => 'required|captcha',
                  ],[
                    'mobile.required' => 'Please enter your valid mobile number', 
                    'mobile.min'      => 'Mobile number must be 10 digits',
                    'mobile.numeric'  => 'Please enter your valid mobile number',
                  ]);

           $xss = new xssClean;
        
           // Get user record
           $mobile = $xss->clean_input($request['mobile']);
          // dd($mobile);
          //CHECKING USER EXIST OR NOT STARTS
		  
          //CHECKING MOBILE NUMBER
          //$mobile_exist = UserLogin::where('mobile','=',$mobile)->first();
          $mobile_exist = DB::table('candidate_nomination_detail')
          ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->where('candidate_nomination_detail.party_id', '<>', '1180')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
          ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();
          //dd($mobile_exist);
         

          if($mobile_exist){
               //CHECK USER EXIST ON USER LOGIN FOR OTP SEND STARTS
              // $usermobile_exist = UserLogin::where('mobile','=',$mobile)->first();
              $usermobile_exist =  DB::table('user_login')->where('mobile','=',$mobile)->first();
             
              if($usermobile_exist){
              //EXIST USER STARTS
              $user_where = ['mobile'=>$mobile];
              $userexist = DB::table('user_login')->where($user_where)
                         //->whereNull('deleted_at')
                         ->first();

              //CHECKING MAXIMUM ATTEMPT FOR OTP STARTS
              $attempts = $userexist->otp_attempt;
              //SETTING OTP TO NULL AFTER 3 FAILED ATTEMPTS STARTS
              if($attempts > 2){
                //UserLogin::where($user_where)
                DB::table('user_login')->where($user_where)
                ->update([
                          'otp_attempt'             =>  '0',
                          'otp'                     =>  '',
                          //'ipaddress'               =>  request()->ip(),
                          //'request_resource_type'   =>  $request->server('HTTP_USER_AGENT'),//$request->header('User-Agent');

                      ]);
                return Redirect('/')->with('error', 'Reached maximum attempts');

              }else{
                  $this->otp_attempt($userexist->id, $attempts+1);
              }
            //SETTING OTP TO NULL AFTER 3 FAILED ATTEMPTS ENDS

            if($userexist->mobile != ""){

                $user2 = DB::table('user_login')->where($user_where)
                 //->whereNull('deleted_at')
                 ->first();

                //CHECKING OTP TIME DIFFRENCE STARTS
                if(!is_null($user2->otp_time)){
                    $currentTime = Carbon::now();
                    $diff=$currentTime->diffInSeconds($user2->otp_time);

                }else{
                        $diff=61; 
                }
                //CHECKING OTP TIME DIFFRENCE ENDS

            if($diff>60){
                $otp = $this->generate_otp();
               // $otp = 123456;
                //SAVING OTP INTO DATABASE STARTS

                DB::table('user_login')->where($user_where)
                //->whereNull('deleted_at')
                ->update([
                    'otp'                     => $otp,
                    'otp_time'                => Carbon::now(),
                    'otp_attempt'             => '0',
                    //'ipaddress'               => request()->ip(),
                    //'request_resource_type'   => $request->server('HTTP_USER_AGENT'),//$request->header('User-Agent');

                ]);

                //SAVING OTP INTO DATABASE ENDS

                 $message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Candidate Portal. Please enter the OTP to proceed.Do not share this OTP Team ECI.";
                //$this->sendmessage($mobile,$message);
                SmsgatewayHelper::gupshup($mobile,$message);
                return Redirect('/ecrpmobileotp/'.base64_encode($mobile))->with('success', 'OTP send on your mobile number.');
                }else{
                    //return 'Can Send only 1 OTP per minute.';
                    return Redirect('/ecrpmobileotp/'.base64_encode($mobile))->with('success', 'Can Send only 1 OTP per minute');
              }

              }
              }else{
              //IF USER COMES FIRST TIME OTP SEND STARTS
              $values = array(
                            'candidate_id' => $mobile_exist->candidate_id,
                            'mobile' => $mobile,
                            'password' =>bcrypt($mobile),
                            'registration_type'=>'1',
                            'permission_request_status'=>'0',
                            'login_access'=>'1'
                          );

              $LastInsertId = DB::table('user_login')->create($values);
              $LastInsertId = $LastInsertId->id;

              $code        = Hash::make(str_random(10));
              $date        = Carbon::now();
              $currentTime = $date->format('Y-m-d H:i:s');
              $otp         = $this->generate_otp();
             // $otp = 123456;
              //SAVING OTP & OTP TIME INTO DB STARTS
              $datas = array(
                          'otp'            => $otp,
                          'remember_token' => $code,
                          'otp_time'       => Carbon::now(),
                          'otp_attempt'    => '1',
                      );

              DB::table('user_login')->where('id',$LastInsertId)->update($datas);
              //SAVING OTP INTO & OTP TIME DB ENDS 

              $message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Candidate Portal. Please enter the OTP to proceed.Do not share this OTP Team ECI.";
              //$this->sendmessage($mobile,$message);
              SmsgatewayHelper::gupshup($mobile,$message);
            
             // return view('otp',['mobile' => $mobile]);

             return Redirect('/ecrpmobileotp/'.base64_encode($mobile))->with('success', 'OTP send on your mobile number.');
              //USER COMES FIRST TIME OTP SEND ENDS
            }
          }else{
			  
            return Redirect('/ecrp-login')->with('error', 'Candidate Not Register As Contested');
          }          

        }catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
      }
      //LOGIN TRY CATCH ENDS HERE       

    }
    //LOGIN FUNCTION ENDS HERE     

    //OTP PAGE FUNCTION STARTS HERE
    public function mobileotp(Request $request, $mobile){
      //OTP PAGE TRY CATCH STARTS HERE
        try{
          $mobile = base64_decode($request->mobile);
          
          return view('auth/ecrp-otp',['mobile'=>$mobile]);

          }catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
      }
      //OTP PAGE TRY CATCH ENDS HERE       

    }
    //OTP PAGE FUNCTION ENDS HERE 


    //LOGIN STARTS HERE
    public function customlogin(Request $request)
    {
        // Check validation
        try{

            $validator = Validator::make($request->all(), [ 
                'mobile' => 'required|regex:/^\S*$/u|numeric|digits:10',
                'otp'    => 'required|regex:/^\S*$/u|numeric|digits:6',
            ]);

           if ($validator->fails()) {
               return Redirect::back()
               ->withErrors($validator)
               ->withInput();          
            }

        $xss = new xssClean;
        // Get user record
        $mobile        = $xss->clean_input($request['mobile']);
        $otp           = $xss->clean_input($request['otp']);

        //CLIENT IP ADDRESS
        //$user_ipaddress = $request->getClientIp();
        
        //$user = UserLogin::where('mobile', $request->get('mobile'))->first();
        
        ///DB::connection()->enableQueryLog();
        $user_where = ['mobile'=>$mobile];
        $otpuser = DB::table('user_login')->where($user_where)
                   ->first();
        
        //MATCHING OTP WITH DB STARTS
        if($otpuser->otp != $otp){

            //CHECKING MAXIMUM ATTEMPT FOR OTP STARTS
            $attempts = $otpuser->otp_attempt;
            //SETTING OTP TO NULL AFTER 3 FAILED ATTEMPTS STARTS
            if($attempts > 2){
               
                DB::table('user_login')->where($user_where)
              ->update([
                        //'is_login'                =>  '0',
                        'otp_attempt'             =>  '0',
                         //'is_verified'             =>  '1',
                        'otp'                     =>  '',
                        //'ipaddress'               =>  request()->ip(),
                        //'request_resource_type'   =>  $request->server('HTTP_USER_AGENT'),//$request->header('User-Agent');

                    ]);
              return Redirect('/ecrp-login')->with('success', 'Reached maximum OTP attempts. Request for new OTP.');
            }else{

                $this->otp_attempt($otpuser->id, $attempts+1);
                return Redirect('/ecrpmobileotp/'.base64_encode($mobile))->with('error', 'Invalid OTP');
            }
            //SETTING OTP TO NULL AFTER 3 FAILED ATTEMPTS ENDS
            //CHECKING MAXIMUM ATTEMPT FOR OTP ENDS

        }
        //MATCHING OTP WITH DB ENDS
        
        //SETTING IS_LOGIN FILED IN USERS TABLE TO 1 STARTS
        DB::table('user_login')->where($user_where)
              ->update([
                        //'is_login'                =>  '1',
                        'otp_attempt'             =>  '0',
                        //'is_verified'             =>  '1',
                        'otp'                     =>  '',
                        //'ipaddress'               =>  request()->ip(),
                        //'request_resource_type'   =>  $request->server('HTTP_USER_AGENT'),//$request->header('User-Agent');
                    ]);
        //SETTING IS_LOGIN FILED IN USERS TABLE TO 1 ENDS
        
        //IF ELSE CONDITION FOR OTP MATCH STARTS
        if ($otpuser->otp == $otp) {
            
            $user =  DB::table('user_login')->where('mobile',$request->mobile)->first();
            
            //LOGIN AS AUTH OF LARAVEL
            $sessiondata = Auth::loginUsingId($user->id);
            //dd(Auth::loginUsingId()->id);

            if($sessiondata){

                $user_data=Auth()->user();
                Auth::guard('web')->setUser($user_data);
                // change stop

                //dd($user_data);
                Session::flash('sucess_message', 'You Are Successfully Logged In'); 
                
                $login_history = array(
                                       'session_id'    =>$user_data->remember_token,
                                       'user_login_id' =>$user_data->id,
                                       'ipaddress'     =>request()->ip(),
                                       'updated_at'=>Date('Y-m-d H:i:s'),
                                       'login_time'=>Date('Y-m-d H:i:s'),
                                       'login_date'=>Date('Y-m-d H:i:s')
                                     );

               $this->commonModel->insertData('user_history', $login_history); 

               Session::put('login_details', $user_data);

               Session::put('logged_id', $user_data->id);

               Session::put('user_login',true);

               return Redirect::to('/ecrphome');
            }

         } else {
                 
                return Redirect('/ecrmobileotp/'.base64_encode($mobile))->with('error', 'Invalid OTP');
                 //return view('welcome',['mobile_number' =>$mobile_number,'otperror' => "Invalid OTP"]);
                }//IF ELSE CONDITION FOR OTP MATCH ENDS

        }catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
         }
            
        
    }
    //LOGIN ENDS HERE

    public function userhome(Request $request)
            {  
              $users=Session::get('login_details');
              $user = Auth()->user();
              if(session()->has('user_login')){ 
                $getid=$user->id;
                return Redirect::to('candidateprofile');
                /*if($user->role_id == NULL)
                {
                    return Redirect::to('/roletype');
                }else
                {
                  $result=DB::table('user_login')->get();
                  $re=$result[0]->permission_request_status;
                  // dd($re);
                  if($re == 0)
                  {
                    return Redirect::to('/update profile');
                  }else
                  {
                    return Redirect::to('/create');
                  } 
                } */  
            }
            else {    
                 return redirect('/ecrp-login');
                }  
            }
    public function updateprofile(Request $request)
    {  
        Auth::guard('web');
        if(!Auth::check()){
            return Redirect::back();
        }

        $users=Session::get('login_details');
        $user = Auth()->user();
        $mobile=$user->mobile;
        $id=$user->id;
        //dd($user);
        $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
		    ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();
        
        return view('admin.expenditure.update_profile', ['user_data' => $user,'candidateData' => $candData]);

    }
            
            public function update(Request $request)
            {
                Auth::guard('web');
                if(Auth::check()){
                $data = $request->all();
                $users=Session::get('login_details');
                $user = Auth()->user();
                $mobile=$user->mobile;
                $user_id=$users->id;
				$ecrp = !empty($request->ecrp)?$request->ecrp:"";
				$candidate_id = !empty($request->candidate_id)?$request->candidate_id:"";
				$nom_id = !empty($request->nom_id)?$request->nom_id:"";
				$ecrp_data = DB::table('ecrp_user_data')->where('candidate_id',$candidate_id)->where('nom_id',$nom_id)->first();
				$data_arr = array("nom_id"=>$nom_id,"candidate_id"=>$candidate_id,"ecrp_reg_no"=>$ecrp);	
				if(empty($ecrp_data->id)){
					$dataInserted = $this->commonModel->insertData('ecrp_user_data', $data_arr);
				 }else{
					$updateEcrp = $this->commonModel->updatedata('ecrp_user_data','id',$ecrp_data->id,$data_arr);
					}
                return redirect()->back()->with('message', 'Updated Successfully!');
                
                }else{
                    return Redirect::back();
                }
            }
	##########################################################################################

    //CANDIDATE ANNUXURE FUNCTION STARTS
    public function annuxure() {

        if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;

        $mobile=$user->mobile;
        $id=$user->id;
        //dd($user);
        $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
		->join('m_party','candidate_nomination_detail.party_id', '=','m_party.CCODE')
		->join('m_state','candidate_nomination_detail.st_code', '=','m_state.ST_CODE')
		->join('m_election_details','candidate_nomination_detail.m_election_detail_ccode', '=','m_election_details.CCODE')
		->leftjoin('ecrp_cand_scanned_file','candidate_nomination_detail.candidate_id', '=','ecrp_cand_scanned_file.candidate_id')
		->leftjoin('ecrp_cand_aff_scanned_file','candidate_nomination_detail.candidate_id', '=','ecrp_cand_aff_scanned_file.candidate_id')
		->leftjoin('ecrp_cand_ack_scanned_file','candidate_nomination_detail.candidate_id', '=','ecrp_cand_ack_scanned_file.candidate_id')
		->leftjoin('m_pc', function($query) {
			$query->on('m_pc.PC_NO','=','candidate_nomination_detail.pc_no')->on('m_pc.ST_CODE','=','candidate_nomination_detail.st_code');
		})
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('ecrp_cand_ack_scanned_file.filename as acknowledgement','ecrp_cand_aff_scanned_file.filename as affidavit','ecrp_cand_scanned_file.filename','m_election_details.ELECTION_TYPE','m_state.ST_NAME','m_pc.PC_NAME','candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids','m_party.PARTYNAME')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();
       
	  	

            $natureofExp    =  NatureofExpModel::get()->toArray();

            $electionSch    =   DB::table('m_schedule')
                                      ->get()
                                      ->toArray();

            if(!empty($candData)){
                $GetAbstractData    = DB::table('expenditure_annexure_e2')
                                      ->where('candidate_id',$candData->candidate_id)
                                      ->get()
                                      ->toArray();

                $expSch1          = DB::table('expenditure_schedule_1')
                                    ->where('candidate_id',$candData->candidate_id)
                                    ->get()->toArray();

                $expSch2          = DB::table('expenditure_schedule_2')
                                    ->where('candidate_id',$candData->candidate_id)
                                    ->get()->toArray();

                $expSch3          = DB::table('expenditure_schedule_3')
                                    ->where('candidate_id',$candData->candidate_id)
                                    ->get()->toArray();

                $expSch4          = DB::table('expenditure_schedule_4')
                                    ->where('candidate_id',$candData->candidate_id)
                                    ->get()->toArray(); 

                $expSch4a          = DB::table('expenditure_schedule_4a')
                                    ->where('candidate_id',$candData->candidate_id)
                                    ->get()->toArray();           

                $expSch5          = DB::table('expenditure_schedule_5')
                                    ->where('candidate_id',$candData->candidate_id)
                                    ->get()->toArray();    

                $expSch6          = DB::table('expenditure_schedule_6')
                                    ->where('candidate_id',$candData->candidate_id)
                                    ->get()->toArray();

                $expSch7          = DB::table('expenditure_schedule_7')
                                    ->where('candidate_id',$candData->candidate_id)
                                    ->get()->toArray();        

                $expSch8          = DB::table('expenditure_schedule_8')
                                    ->where('candidate_id',$candData->candidate_id)
                                    ->get()->toArray();

                $expSch9          = DB::table('expenditure_schedule_9')
                                    ->where('candidate_id',$candData->candidate_id)
                                    ->get()->toArray(); 

                $expSch10         = DB::table('expenditure_schedule_10')
                                    ->where('candidate_id',$candData->candidate_id)
                                    ->get()->toArray(); 

                $candiatePcName   = getpcbypcno($candData->st_code, $candData->pc_no);       
                $candiatePcName   =  !empty($candiatePcName)? $candiatePcName->PC_NAME:'---'; 

                return view('expenditure.pages.candidate.annuxure', [
                    'user_data'         => $user, 
                    'candidateData'     => $candData,
                    'candiatePcName'    => $candiatePcName,
                    'natureofExp'       => $natureofExp,
                    'GetAbstractData'   => $GetAbstractData,
                    'getSch1'           => $expSch1,
                    'getSch2'           => $expSch2,
                    'getSch3'           => $expSch3,
                    'getSch4'           => $expSch4,
                    'getSch4a'          => $expSch4a,
                    'getSch5'           => $expSch5,
                    'getSch6'           => $expSch6,
                    'getSch7'           => $expSch7,
                    'getSch8'           => $expSch8,
                    'getSch9'           => $expSch9,
                    'getSch10'          => $expSch10,
                    'electionSch'       => $electionSch,
                ]);
            }
            
        
        }
		else
		{
			return redirect('/ecrp-login');
		}
    }
    //CANDIDATE ANNUXURE FUNCTION ENDS

    //CANDIDATE SAVE ANNUXURE DATA FUNCTION STARTS
    public function SaveAnnuxureData(Request $request)
    {

        $request            = (array) $request->all();
        $candidateId        = $request['candidate_id'];
        $user               = Auth::user();
        $uid                = $user->id;
        $namePrefix         = \Route::current()->action['prefix'];
        unset($request['_token']);
        $candidateDetail    = $this->commonModel->selectone('candidate_nomination_detail', 'candidate_id', $candidateId);

        $candidate_id       = !empty($request['candidate_id_update'])?$request['candidate_id_update']:"";
        
        //try{
            $data_arr=array();
            foreach($request as $key=>$req_data)
            {       
                $xss                    =   new xssClean;
                $data_arr[$key]         =   $xss->clean_input($req_data);
            }

            $data_arr['created_by']     =   $uid;
            $data_arr['updated_by']     =   $uid;

            $data_arr['grand_total_source_funds']=!empty($data_arr['amt_own_funds_election_compaign'])?$data_arr['amt_own_funds_election_compaign']:0 + !empty($data_arr['lump_sum_amt_from_party'])?$data_arr['lump_sum_amt_from_party']:0 + !empty($data_arr['lump_sum_amt_from_other'])?$data_arr['lump_sum_amt_from_other']:0;
                
					
            if(empty($data_arr['candidate_id_update'])){
				    unset($data_arr['candidate_id_update']);
                $dataInserted = $this->commonModel->insertData('expenditure_annexure_e2', $data_arr);
            }
            else
            {
               
                unset($data_arr['candidate_id']);
				      unset($data_arr['candidate_id_update']);
                $dataInserted = $this->commonModel->updatedata('expenditure_annexure_e2','candidate_id',$candidate_id,$data_arr);
            }

            if($dataInserted)
            {
                return 1;
            }
            else
            {
                return 0;
            }
            
            
        // }
        // catch (\Exception $e) {
        //     return 0;
        // }
    }

    
    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM DATA FUNCTION STARTS
    public function saveAckformSch1(Request $request){
        if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;

           

        $mobile=$user->mobile;
        $id=$user->id;
        //dd($user);
        $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();

            //try{
                $inputs = $request->all();

                $xss    = new xssClean;
                $secure = new secureCode;
                $data_arr = array();

                unset($inputs['_token']);
             
                $result    = DB::table('expenditure_schedule_1')->where('candidate_id', $candData->candidate_id)->delete();
                foreach($inputs as $expSch){
                    foreach($expSch as $key => $value ) {                      

                        $constituencyno         = '';
                        if($candData->ac_no != ''){
                            $constituencyno     = $candData->ac_no;
                        }
                        if($candData->pc_no != ''){
                            $constituencyno     = $candData->pc_no;
                        }
                        $dataVal = array(
                            'candidate_id'          => $candData->candidate_id,
                            'st_code'               => $candData->st_code,
                            'district_no'           => $candData->district_no,
                            'constituency_no'       => $constituencyno,
                            'nature_of_exp_id'      => $value['nature_of_exp_id'],
                            'nature_of_exp'         => $value['nature_of_exp'],
                            'total_amt'             => $value['total_amt'],
                            'src_amt_incurred_cand' => $value['src_amt_incurred_cand'],
                            'src_amt_incurred_pp'   => $value['src_amt_incurred_pp'],
                            'src_amt_incurred_other'=> $value['src_amt_incurred_other'],
                            'created_by'            => $candData->candidate_id,
                            'updated_by'            => $candData->candidate_id,
                            'created_at'            => date('Y-m-d h:i:s'),
                            'updated_at'            => date('Y-m-d h:i:s')
                        );

                        $result2    = DB::table('expenditure_schedule_1')->insert($dataVal);
                           
                    }
                }
                return 'Record has been successfully inserted' ;
            /*}
            catch (\Exception $e) {
                return 0;
            }*/
        }
    }
    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 1 DATA FUNCTION ENDS


    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 2 DATA FUNCTION STARTS
    public function saveAckformSch2(Request $request){
        if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;

         $mobile=$user->mobile;
        $id=$user->id;

            $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();


            //try{
                $inputs = $request->all();
                $xss    = new xssClean;
                $secure = new secureCode;
                $data_arr = array();

                unset($inputs['_token']);
                $result    = DB::table('expenditure_schedule_2')->where('candidate_id', $candData->candidate_id)->delete();

                foreach($inputs as $expSchd){
                    foreach($expSchd as $key => $value ) {

                        $constituencyno         = '';
                        if($candData->ac_no != ''){
                            $constituencyno     = $candData->ac_no;
                        }
                        if($candData->pc_no != ''){
                            $constituencyno     = $candData->pc_no;
                        }

                        $dataVal = array(
                            'candidate_id'            => $candData->candidate_id,
                            'st_code'                 => $candData->st_code,
                            'district_no'             => $candData->district_no,
                            'constituency_no'         => $constituencyno,
                            'meetingdate'             => $value['meetingdate'],
                            'venue'                   => $value['venue'],
                            'name_of_start_and_party' => $value['name_of_start_and_party'],
                            'src_amt_by_cand'         => $value['src_amt_by_cand'],
                            'src_amt_by_pp'           => $value['src_amt_by_pp'],
                            'src_amt_by_other'        => $value['src_amt_by_other'],
                            'remarks'                 => $value['remarks'],
                            'created_by'              => $candData->candidate_id,
                            'updated_by'              => $candData->candidate_id,
                            'created_at'              => date('Y-m-d h:i:s'),
                            'updated_at'              => date('Y-m-d h:i:s')
                        );

                        $result2    = DB::table('expenditure_schedule_2')->insert($dataVal);
                       
                    }
                } 
                return 'Record has been successfully inserted' ;
            /*}
            catch (\Exception $e) {
                return 0;
            }*/
        }
    }
    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 2 DATA FUNCTION ENDS

    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 3 DATA FUNCTION STARTS
    public function saveAckformSch3(Request $request){
        if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;

             $mobile=$user->mobile;
        $id=$user->id;

            $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();
           try{
                $inputs = $request->all();
                $xss    = new xssClean;
                $secure = new secureCode;
                $data_arr = array();

                unset($inputs['_token']);
                $result    = DB::table('expenditure_schedule_3')->where('candidate_id', $candData->candidate_id)->delete();

                foreach($inputs as $expSch){
                    foreach($expSch as $key => $value ) {

                        
                        $constituencyno         = '';
                        if($candData->ac_no != ''){
                            $constituencyno     = $candData->ac_no;
                        }
                        if($candData->pc_no != ''){
                            $constituencyno     = $candData->pc_no;
                        }

                            $dataVal = array(
                                'candidate_id'            => $candData->candidate_id,
                                'st_code'                 => $candData->st_code,
                                'district_no'             => $candData->district_no,
                                'constituency_no'         => $constituencyno,
                                'district_no'             => $candData->district_no,
                                'nature_of_expense'       => $value['nature_of_expense'],
                                'total_amt'               => $value['total_amt'],
                                'src_amt_by_cand'         => $value['src_amt_by_cand'],
                                'src_amt_by_pp'           => $value['src_amt_by_pp'],
                                'src_amt_by_other'        => $value['src_amt_by_other'],
                                'remarks'                 =>$value['remarks'],
                                'created_by'              => $candData->candidate_id,
                                'updated_by'              => $candData->candidate_id,
                                'created_at'              => date('Y-m-d h:i:s'),
                                'updated_at'              => date('Y-m-d h:i:s')
                            );

                            $result2    = DB::table('expenditure_schedule_3')->insert($dataVal);
                        
                    }
                }
            }
            catch (\Exception $e) {
                return 0;
            }
        }
    }
    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 3 DATA FUNCTION ENDS

    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 4 DATA FUNCTION STARTS
    public function saveAckformSch4(Request $request){
        if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;

           $mobile=$user->mobile;
        $id=$user->id;

            $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();
            //try{
                $inputs = $request->all();

                $xss    = new xssClean;
                $secure = new secureCode;
                $data_arr = array();

                unset($inputs['_token']);
                $result    = DB::table('expenditure_schedule_4')->where('candidate_id', $candData->candidate_id)->delete();
                foreach($inputs as $expSch){
                    //print_r($expSch);
                    foreach($expSch as $key => $value ) {
                        
                        $constituencyno         = '';
                        if($candData->ac_no != ''){
                            $constituencyno     = $candData->ac_no;
                        }
                        if($candData->pc_no != ''){
                            $constituencyno     = $candData->pc_no;
                        }

                            $dataVal = array(
                                'candidate_id'               => $candData->candidate_id,
                                'st_code'                    => $candData->st_code,
                                'district_no'                => $candData->district_no,
                                'constituency_no'            => $constituencyno,
                                'nature_of_medium'           => $value['nature_of_medium'],
                                'name_of_media'              => $value['name_of_media'],
                                'address_of_media'           => $value['address_of_media'],
                                'name_address_of_agency'     => $value['name_address_of_agency'],
                                'price_of_the_media'         => $value['price_of_the_media'],
                                'commission_of_agency'       => $value['commission_of_agency'],
                                'total_amt'                  => $value['total_amt'],
                                'src_amt_by_cand'            => $value['src_amt_by_cand'],
                                'src_amt_by_pp'              => $value['src_amt_by_pp'],
                                'src_amt_by_other'           => $value['src_amt_by_other'],
                                'created_by'                 => $candData->candidate_id,
                                'updated_by'                 => $candData->candidate_id,
                                'created_at'                 => date('Y-m-d h:i:s'),
                                'updated_at'                 => date('Y-m-d h:i:s')
                            );

                            $result2    = DB::table('expenditure_schedule_4')->insert($dataVal);
                        
                    }
                }
            /*}
            catch (\Exception $e) {
                return 0;
            }*/
        }
    }
    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 4 DATA FUNCTION ENDS

    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 4a DATA FUNCTION STARTS
    public function saveAckformSch5(Request $request){
        if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;

             $mobile=$user->mobile;
        $id=$user->id;

            $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();
            //try{
                $inputs = $request->all();

                $xss    = new xssClean;
                $secure = new secureCode;
                $data_arr = array();

                unset($inputs['_token']);
                $result    = DB::table('expenditure_schedule_4a')->where('candidate_id', $candData->candidate_id)->delete();
                foreach($inputs as $expSch){

                    foreach($expSch as $key => $value ) {

                        $constituencyno         = '';
                        if($candData->ac_no != ''){
                            $constituencyno     = $candData->ac_no;
                        }
                        if($candData->pc_no != ''){
                            $constituencyno     = $candData->pc_no;
                        }

                       
                        $dataVal = array(
                            'candidate_id'               => $candData->candidate_id,
                            'st_code'                    => $candData->st_code,
                            'district_no'                => $candData->district_no,
                            'constituency_no'            => $constituencyno,
                            'nature_of_media'            => $value['nature_of_media'],
                            'name_of_media'              => $value['name_of_media'],
                            'address_of_media'           => $value['address_of_media'],
                            'name_address_of_agency'     => $value['name_address_of_agency'],
                            'price_of_the_media'         => $value['price_of_the_media'],
                            'commission_of_agency'       => $value['commission_of_agency'],
                            'total_amt'                  => $value['total_amt'],
                            'src_amt_by_cand'            => $value['src_amt_by_cand'],
                            'src_amt_by_pp'              => $value['src_amt_by_pp'],
                            'src_amt_by_other'           => $value['src_amt_by_other'],
                            'created_by'                 => $candData->candidate_id,
                            'updated_by'                 => $candData->candidate_id,
                            'created_at'                 => date('Y-m-d h:i:s'),
                            'updated_at'                 => date('Y-m-d h:i:s')
                        );

                        $result2    = DB::table('expenditure_schedule_4a')->insert($dataVal);
                    }
                }
            // }
            // catch (\Exception $e) {
            //     return 0;
            // }
        }
    }
    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 4a DATA FUNCTION ENDS

    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 5 DATA FUNCTION STARTS
    public function saveAckformSch6(Request $request){
        if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;

             $mobile=$user->mobile;
        $id=$user->id;

            $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first(); 
            try{
                $inputs = $request->all();

                $xss    = new xssClean;
                $secure = new secureCode;
                $data_arr = array();

                unset($inputs['_token']);
                $result    = DB::table('expenditure_schedule_5')->where('candidate_id', $candData->candidate_id)->delete();

                foreach($inputs as $expSch){
                    foreach($expSch as $key => $value ) {
                        
                        $constituencyno         = '';
                        if($candData->ac_no != ''){
                            $constituencyno     = $candData->ac_no;
                        }
                        if($candData->pc_no != ''){
                            $constituencyno     = $candData->pc_no;
                        }

                            $dataVal = array(
                                'candidate_id'               => $candData->candidate_id,
                                'st_code'                    => $candData->st_code,
                                'district_no'                => $candData->district_no,
                                'constituency_no'            => $constituencyno,
                                'regn_no_of_vehicle'         => $value['regn_no_of_vehicle'],
                                'hir_rate_for_vehicle'       => $value['hir_rate_for_vehicle'],
                                'hir_fuel_charges'           => $value['hir_fuel_charges'],
                                'hir_driver_charges'         => $value['hir_driver_charges'],
                                'no_of_days'                 => $value['no_of_days'],
                                'total_amt_incurred'         => $value['total_amt_incurred'],
                                'src_amt_by_cand'            => $value['src_amt_by_cand'],
                                'src_amt_by_pp'              => $value['src_amt_by_pp'],
                                'src_amt_by_other'           => $value['src_amt_by_other'],
                                'created_by'                 => $candData->candidate_id,
                                'updated_by'                 => $candData->candidate_id,
                                'created_at'                 => date('Y-m-d h:i:s'),
                                'updated_at'                 => date('Y-m-d h:i:s')
                            );

                        $result2    = DB::table('expenditure_schedule_5')->insert($dataVal);
                        
                    }
                }
            }
            catch (\Exception $e) {
                return 0;
            }
        }
    }
    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 5 DATA FUNCTION ENDS

    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 6 DATA FUNCTION STARTS
    public function saveAckformSch7(Request $request){
        if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;
 $mobile=$user->mobile;
        $id=$user->id;

            $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();
            try{
                $inputs = $request->all();

                $xss    = new xssClean;
                $secure = new secureCode;
                $data_arr = array();

                unset($inputs['_token']);
                $result    = DB::table('expenditure_schedule_6')->where('candidate_id', $candData->candidate_id)->delete();

                foreach($inputs as $expSch){

                    foreach($expSch as $key => $value ) {

                        $constituencyno         = '';
                        if($candData->ac_no != ''){
                            $constituencyno     = $candData->ac_no;
                        }
                        if($candData->pc_no != ''){
                            $constituencyno     = $candData->pc_no;
                        }

                        
                        $dataVal = array(
                            'candidate_id'               => $candData->candidate_id,
                            'st_code'                    => $candData->st_code,
                            'district_no'                => $candData->district_no,
                            'constituency_no'            => $constituencyno,
                            'venu_date'                  => $value['venu_date'],
                            'venu_details'               => $value['venu_details'],
                            'expense_nature'             => $value['expense_nature'],
                            'expense_nature_rate'        => $value['expense_nature_rate'],
                            'worker_agents_count'        => $value['worker_agents_count'],
                            'total_amnt'                 => $value['total_amnt'],
                            'source_amnt_by_cand'        => $value['source_amnt_by_cand'],
                            'source_amnt_by_polparty'    => $value['source_amnt_by_polparty'],
                            'source_amnt_by_others'      => $value['source_amnt_by_others'],
                            'created_by'                 => $candData->candidate_id,
                            'updated_by'                 => $candData->candidate_id,
                            'created_at'                 => date('Y-m-d h:i:s'),
                            'updated_at'                 => date('Y-m-d h:i:s')
                        );

                        $result2    = DB::table('expenditure_schedule_6')->insert($dataVal);
                    
                    }
                }
            }
            catch (\Exception $e) {
                return 0;
            }
        }
    }
    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 6 DATA FUNCTION ENDS

    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 7 DATA FUNCTION STARTS
    public function saveAckformSch8(Request $request){
        if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;
            $mobile=$user->mobile;
        $id=$user->id;

            $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();
            try{
                $inputs = $request->all();

                $xss    = new xssClean;
                $secure = new secureCode;
                $data_arr = array();

                unset($inputs['_token']);
                $result    = DB::table('expenditure_schedule_7')->where('candidate_id', $candData->candidate_id)->delete();
                foreach($inputs as $expSch){
                    
                    foreach($expSch as $key => $value ) {
                        $constituencyno         = '';
                        if($candData->ac_no != ''){
                            $constituencyno     = $candData->ac_no;
                        }
                        if($candData->pc_no != ''){
                            $constituencyno     = $candData->pc_no;
                        }

                        $cheque_date        = '' ;
                        $cheque_bank        = '' ;
                        $cheque_ifsc        = '' ;
                        $cheque_number      = '' ;

                       
                        if($value['payment_type'] == 'cash'){
                            $cheque_date        = '' ;
                            $cheque_bank        = '' ;
                            $cheque_ifsc        = '' ;
                            $cheque_number      = '' ;
                        }
                        elseif($value['payment_type'] == 'dd'){
                            $cheque_date        = '' ;
                            $cheque_bank        = '' ;
                            $cheque_ifsc        = '' ;
                            $cheque_number      = '' ;
                        }
                        elseif($value['payment_type'] == 'cheque'){
                            $cheque_date        = '' ;
                            $cheque_bank        = '' ;
                            $cheque_ifsc        = '' ;
                            $cheque_number      = '' ;
                        }

                        
                        $dataVal = array(
                            'candidate_id'               => $candData->candidate_id,
                            'st_code'                    => $candData->st_code,
                            'district_no'                => $candData->district_no,
                            'constituency_no'            => $constituencyno,
                            'submit_date'                => $value['submit_date'],
                            'payment_type'               => $value['payment_type'],
                            'amount'                     => $value['amount'],
                            'cheque_date'                => $cheque_date,
                            'cheque_bank'                => $cheque_bank,
                            'cheque_ifsc'                => $cheque_ifsc,
                            'cheque_number'              => $cheque_number,
                            'remarks'                    => $value['remarks'],
                            'created_by'                 => $candData->candidate_id,
                            'updated_by'                 => $candData->candidate_id,
                            'created_at'                 => date('Y-m-d h:i:s'),
                            'updated_at'                 => date('Y-m-d h:i:s')
                        );

                        $result2    = DB::table('expenditure_schedule_7')->insert($dataVal);
                        
                    }
                }
            }
            catch (\Exception $e) {
                return 0;
            }
        }
    }
    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 7 DATA FUNCTION ENDS

    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 8 DATA FUNCTION STARTS
    public function saveAckformSch9(Request $request){
        if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;

             $mobile=$user->mobile;
        $id=$user->id;

            $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();
            
            //try{
                $inputs = $request->all();

                $xss    = new xssClean;
                $secure = new secureCode;
                $data_arr = array();

                unset($inputs['_token']);
                $result    = DB::table('expenditure_schedule_8')->where('candidate_id', $candData->candidate_id)->delete();

                foreach($inputs as $expSch){
                    //print_r($expSch);
                    foreach($expSch as $key => $value ) {

                        $constituencyno         = '';
                        if($candData->ac_no != ''){
                            $constituencyno     = $candData->ac_no;
                        }
                        if($candData->pc_no != ''){
                            $constituencyno     = $candData->pc_no;
                        }

                        $cheque_date        = '' ;
                        $cheque_bank        = '' ;
                        $cheque_ifsc        = '' ;
                        $cheque_number      = '' ;

                        if($value['payment_type'] == 'cash'){
                            $cheque_date        = '' ;
                            $cheque_bank        = '' ;
                            $cheque_ifsc        = '' ;
                            $cheque_number      = '' ;
                        }
                        elseif($value['payment_type'] == 'dd'){
                            $cheque_date        = '' ;
                            $cheque_bank        = '' ;
                            $cheque_ifsc        = '' ;
                            $cheque_number      = '' ;
                        }
                        elseif($value['payment_type'] == 'cheque'){
                            $cheque_date        = '' ;
                            $cheque_bank        = '' ;
                            $cheque_ifsc        = '' ;
                            $cheque_number      = '' ;
                        }

                        $dataVal = array(
                            'candidate_id'               => $candData->candidate_id,
                            'st_code'                    => $candData->st_code,
                            'district_no'                => $candData->district_no,
                            'constituency_no'            => $constituencyno,
                            'party_id'                   => $value['party_id'],
                            'payment_type'               => $value['payment_type'],
                            'amount'                     => $value['amount'],
                            'cheque_date'                => $cheque_date,
                            'cheque_bank'                => $cheque_bank,
                            'cheque_ifsc'                => $cheque_ifsc,
                            'cheque_number'              => $cheque_number,
                            'remarks'                    => $value['remarks'],
                            'submit_date'                => $value['submit_date'],
                            'created_by'                 => $candData->candidate_id,
                            'updated_by'                 => $candData->candidate_id,
                            'created_at'                 => date('Y-m-d h:i:s'),
                            'updated_at'                 => date('Y-m-d h:i:s')
                        );

                        $result2    = DB::table('expenditure_schedule_8')->insert($dataVal);
                    }
                }
            /*}
            catch (\Exception $e) {
                return 0;
            }*/
        }
    }
    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 8 DATA FUNCTION ENDS

    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 9 DATA FUNCTION STARTS
    public function saveAckformSch10(Request $request){
        if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;

             $mobile=$user->mobile;
        $id=$user->id;

            $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first(); 
            try{
                $inputs = $request->all();
                $result    = DB::table('expenditure_schedule_9')->where('candidate_id', $candData->candidate_id)->delete();

                $xss    = new xssClean;
                $secure = new secureCode;
                $data_arr = array();

                unset($inputs['_token']);
                foreach($inputs as $expSch){
                   
                    foreach($expSch as $key => $value ) {

                        $constituencyno         = '';
                        if($candData->ac_no != ''){
                            $constituencyno     = $candData->ac_no;
                        }
                        if($candData->pc_no != ''){
                            $constituencyno     = $candData->pc_no;
                        }

                        $cheque_date        = '' ;
                        $cheque_bank        = '' ;
                        $cheque_ifsc        = '' ;
                        $cheque_number      = '' ;

                        if($value['payment_type'] == 'cash'){
                            $cheque_date        = '' ;
                            $cheque_bank        = '' ;
                            $cheque_ifsc        = '' ;
                            $cheque_number      = '' ;
                        }
                        elseif($value['payment_type'] == 'cheque'){
                            $cheque_date        = '' ;
                            $cheque_bank        = '' ;
                            $cheque_ifsc        = '' ;
                            $cheque_number      = '' ;
                        }
                        elseif($value['payment_type'] == 'dd'){
                            $cheque_date        = '' ;
                            $cheque_bank        = '' ;
                            $cheque_ifsc        = '' ;
                            $cheque_number      = '' ;
                        }

                        $dataVal = array(
                            'candidate_id'               => $candData->candidate_id,
                            'st_code'                    => $candData->st_code,
                            'district_no'                => $candData->district_no,
                            'constituency_no'            => $constituencyno,
                            'name'                       => $value['name'],
                            'address'                    => $value['address'],
                            'payment_type'               => $value['payment_type'],
                            'amount'                     => $value['amount'],
                            'amount_details'             => $value['amount_details'],
                            'cheque_date'                => $cheque_date,
                            'cheque_bank'                => $cheque_bank,
                            'cheque_ifsc'                => $cheque_ifsc,
                            'cheque_number'              => $cheque_number,
                            'remarks'                    => $value['remarks'],
                            'submit_date'                => $value['submit_date'],
                            'created_by'                 => $candData->candidate_id,
                            'updated_by'                 => $candData->candidate_id,
                            'created_at'                 => date('Y-m-d h:i:s'),
                            'updated_at'                 => date('Y-m-d h:i:s')
                        );

                        $result2    = DB::table('expenditure_schedule_9')->insert($dataVal);
                    }
                }
                return 1;
            }
            catch (\Exception $e) {
                return 0;
            }
        }
    }
    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 9 DATA FUNCTION ENDS

    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 10 DATA FUNCTION STARTS
    public function saveAckformSch11(Request $request){
        if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;

             $mobile=$user->mobile;
        $id=$user->id;

            $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();
            //try{
                $inputs = $request->all();
                $result    = DB::table('expenditure_schedule_10')->where('candidate_id', $candData->candidate_id)->delete();

                $xss    = new xssClean;
                $secure = new secureCode;
                $data_arr = array();

                unset($inputs['_token']);
				//print_r($inputs);
                foreach($inputs as $expSch){
                    //print_r($expSch);
                    foreach($expSch as $key => $value ) {

                        $constituencyno         = '';
                        if($candData->ac_no != ''){
                            $constituencyno     = $candData->ac_no;
                        }
                        if($candData->pc_no != ''){
                            $constituencyno     = $candData->pc_no;
                        }
                        $cheque_date        = '' ;
                        $cheque_bank        = '' ;
                        $cheque_ifsc        = '' ;
                        $cheque_number      = '' ;

                        if($value['payment_type'] == 'cash'){
                            $cheque_date        = '' ;
                            $cheque_bank        = '' ;
                            $cheque_ifsc        = '' ;
                            $cheque_number      = '' ;
                        }
                        elseif($value['payment_type'] == 'cheque'){
                            $cheque_date        = '' ;
                            $cheque_bank        = '' ;
                            $cheque_ifsc        = '' ;
                            $cheque_number      = '' ;
                        }
                        elseif($value['payment_type'] == 'dd'){
                            $cheque_date        = '' ;
                            $cheque_bank        = '' ;
                            $cheque_ifsc        = '' ;
                            $cheque_number      = '' ;
                        }

                        $dataVal = array(
                            'candidate_id'               => $candData->candidate_id,
                            'st_code'                    => $candData->st_code,
                            'district_no'                => $candData->district_no,
                            'constituency_no'            => $constituencyno,
                            'newspaper_name'             => $value['newspaper_name'],
                            'news_publishing_date'       => $value['news_publishing_date'],
                            'expense_on_news'            => $value['expense_on_news'],
                            'channel_name'               => $value['channel_name'],
                            'telecost_dateTime'          => $value['telecost_dateTime'],
                            'expense_on_channel'         => $value['expense_on_channel'],
                            'payment_type'               => $value['payment_type'],
                            'cheque_date'                => $cheque_date,
                            'cheque_bank'                => $cheque_bank,
                            'cheque_ifsc'                => $cheque_ifsc,
                            'cheque_number'              => $cheque_number,
                            'created_by'                 => $candData->candidate_id,
                            'updated_by'                 => $candData->candidate_id,
                            'updated_at'                 => date('Y-m-d h:i:s')
                        );

                        $result2    = DB::table('expenditure_schedule_10')->insert($dataVal);
                        if($result2 == 1){
                            $formid = $candData->st_code.$candData->district_no.date('Ymd').rand(100,999);
                            //echo $formid ; exit;
                            $expUpdate1    = DB::table('expenditure_schedule_1')
                                            ->where('candidate_id',$candData->candidate_id)
                                            ->update(['finalize_by_candidate' => 1, 'formid'=>$formid]);

                            $expUpdate2    = DB::table('expenditure_schedule_2')
                                          ->where('candidate_id',$candData->candidate_id)
                                          ->update(['finalize_by_candidate' => 1, 'formid'=>$formid]);

                            $expUpdate3    = DB::table('expenditure_schedule_3')
                                          ->where('candidate_id',$candData->candidate_id)
                                          ->update(['finalize_by_candidate' => 1, 'formid'=>$formid]);

                            $expUpdate4    = DB::table('expenditure_schedule_4')
                                          ->where('candidate_id',$candData->candidate_id)
                                          ->update(['finalize_by_candidate' => 1, 'formid'=>$formid]);

                            $expUpdate4a    = DB::table('expenditure_schedule_4a')
                                            ->where('candidate_id',$candData->candidate_id)
                                            ->update(['finalize_by_candidate' => 1, 'formid'=>$formid]);

                            $expUpdate5    = DB::table('expenditure_schedule_5')
                                          ->where('candidate_id',$candData->candidate_id)
                                          ->update(['finalize_by_candidate' => 1, 'formid'=>$formid]);

                            $expUpdate6    = DB::table('expenditure_schedule_6')
                                          ->where('candidate_id',$candData->candidate_id)
                                          ->update(['finalize_by_candidate' => 1, 'formid'=>$formid]);

                            $expUpdate7    = DB::table('expenditure_schedule_7')
                                          ->where('candidate_id',$candData->candidate_id)
                                          ->update(['finalize_by_candidate' => 1, 'formid'=>$formid]);

                            $expUpdate8    = DB::table('expenditure_schedule_8')
                                          ->where('candidate_id',$candData->candidate_id)
                                          ->update(['finalize_by_candidate' => 1, 'formid'=>$formid]);

                            $expUpdate9    = DB::table('expenditure_schedule_9')
                                          ->where('candidate_id',$candData->candidate_id)
                                          ->update(['finalize_by_candidate' => 1, 'formid'=>$formid]);

                            $expUpdate10  = DB::table('expenditure_schedule_10')
                                          ->where('candidate_id',$candData->candidate_id)
                                          ->update(['finalize_by_candidate' => 1, 'formid'=>$formid]);

                        } 
                    }
                }
             //}
            // catch (\Exception $e) {
            //     return 0;
            // }
        }
    }
    //CANDIDATE SAVE ACKNOWLEDGEMENT FORM 10 DATA FUNCTION ENDS

    //CANDIDATE ACKNOWLEDGEMENT FINALIZE PDF FUNCTION STARTS
    public function ackSubmittion($candidate_id) {
    }
    //CANDIDATE ACKNOWLEDGEMENT FINALIZE PDF FUNCTION ENDS

     //CANDIDATE ACKNOWLEDGEMENT FINALIZE PDF FUNCTION STARTS
    public function printAcknowledgementReport($candidate_id) {
      
        if (Auth::check()) {
            $user = Auth::user();            
            $candidate_id= base64_decode($candidate_id);           
            $d = $this->expenditureModel->getunewserbyuserid($user->id, $user->role_id);
          
            $mpdf = new \Mpdf\Mpdf();
            
            $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
            $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';
            
            

            $date = date('d-m-Y');
            $profileData = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                    })
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
                    ->where('m_election_details.CONST_TYPE', '=', 'PC')
                    ->get();
            // get CEO status cand_name ELECTION_TYPE
            $candidateName = !empty($profileData[0]) ? $profileData[0]->cand_name : '';
            $ELECTION_TYPE = !empty($profileData[0]) ? $profileData[0]->ELECTION_TYPE : '';
            $party_id = !empty($profileData[0]) ? $profileData[0]->party_id : '';
            $partyname = getpartybyid($party_id);
            $partyname = !empty($partyname) ? $partyname->PARTYNAME : '';
            


            $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();
            $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray();
            $current_status = DB::table('expenditure_mis_current_sataus')->get()->toArray();
            $ReportSingleData = $this->expenditureModel->GetExpeditureSingleData($candidate_id);
            if (!empty($ReportSingleData)) {

                $ReportSingleData = (array) $ReportSingleData[0];
            }

            $date = date('d-m-Y');
            $year=date('Y');
            $title = $date . '_' . "Election Commission of India";
            $mpdf->setHeader($candidateName . ' | ' . $ELECTION_TYPE . ' '.$year.' | ' . $partyname);

            $mpdf->SetFooter($date . '|' . "Election Commission of India" . '|{PAGENO}');
            
            $mpdf->SetProtection(array('print'));
            $mpdf->SetTitle($title);
            $mpdf->SetAuthor("Election Commission of India");
            $mpdf->SetWatermarkText("Election Commission of India");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->SetDisplayMode('fullpage');
            $scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidate_id);
            $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidate_id);
            $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidate_id);
            $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidate_id);

print_r($expensesourecefundbyitem);exit;
           $download_link1= !empty($expenseunderstated[3]->comment)? link_to_asset('ExpenditureReport/'.$expenseunderstated[3]->comment):'';
           $download_link2= !empty($expenseunderstated[5]->comment)? link_to_asset('ExpenditureReport/'.$expenseunderstated[5]->comment):'';
           $download_link3= !empty($scrutinyReportData[0]->noticefile)? link_to_asset('ExpenditureReport/'.$scrutinyReportData[0]->noticefile):'';

            $electionSch    =   DB::table('m_schedule')->get()->toArray();
            $pdf = view('expenditure.pages.candidate.pdf_acknowledgement', compact('expensesourecefundbyitem', 'scrutinyReportData', 'expenseunderstated', 'expenseunderstatedbyitem','download_link1','download_link2','download_link3'));

            
            $mpdf->WriteHTML($pdf);
            $mpdf->Output();


        } else {
            return redirect('/officer-login');
        }
   }
   //CANDIDATE ACKNOWLEDGEMENT FINALIZE PDF FUNCTION ENDS

    //////////pdf generation///////////////////
    public function printEcrpStatusReport(Request $request) {
		ini_set('max_execution_time', 99999999);
        if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;

           
            $mpdf = new \Mpdf\Mpdf();
            $mobile=$user->mobile;
            $id=$user->id;

            $candData = DB::table('candidate_nomination_detail')
                        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
			                   ->join('m_party','candidate_nomination_detail.party_id', '=','m_party.CCODE')
                         ->join("m_election_details", function($join) {
                                          $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                                          ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no"); 
                                      })
                                      ->join("m_pc", function($joins) {
                                          $joins->on("m_pc.ST_CODE", "=", "candidate_nomination_detail.st_code")
                                          ->on("m_pc.PC_NO", "=", "candidate_nomination_detail.pc_no");
                                           })
                                ->join('m_state','m_state.ST_CODE','=','candidate_nomination_detail.st_code') 
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->leftjoin('expenditure_reports','expenditure_reports.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('m_party.PARTYNAME','candidate_nomination_detail.*','m_state.ST_NAME','m_pc.*','candidate_personal_detail.*','m_election_details.*','expenditure_reports.finalized_status','expenditure_reports.updated_at as finalized_date') 
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();


            $election_id        = $candData->election_id ;
            $electionDetails    = DB::table('m_cur_elec')->where('ELECTION_ID',$election_id)->first();

            // get CEO status cand_name ELECTION_TYPE
            $candidateName = !empty($candData->cand_name)?$candData->cand_name:"N/A";
		      	$candidateParty = !empty($candData->PARTYNAME)?$candData->PARTYNAME:"N/A";
			      $candidateElectioType = !empty($candData->ELECTION_TYPE)?$candData->ELECTION_TYPE:"N/A";
            ////annuxure-e2 form data///
            $getannuxureData = $this->expschmodel->GetAnnuxureData($candData->candidate_id);
            ///end annuxure-e2 form data///
            ///akcnowldge form data///
            $getSch1Data = $this->expschmodel->GetSch1Data($candData->candidate_id);
            $getSch2Data = $this->expschmodel->GetSch2Data($candData->candidate_id);
            $getSch3Data =$this->expschmodel->GetSch3Data($candData->candidate_id);
            $getSch4Data =$this->expschmodel->GetSch4Data($candData->candidate_id);
            $getSch4aData =$this->expschmodel->GetSch4aData($candData->candidate_id);
            $getSch5Data =$this->expschmodel->GetSch5Data($candData->candidate_id);
            $getSch6Data =$this->expschmodel->GetSch6Data($candData->candidate_id);
            $getSch7Data =$this->expschmodel->GetSch7Data($candData->candidate_id);
            $getSch8Data =$this->expschmodel->GetSch8Data($candData->candidate_id);
            $getSch9Data =$this->expschmodel->GetSch9Data($candData->candidate_id);
            $getSch10Data =$this->expschmodel->GetSch10Data($candData->candidate_id);

            $electionSch    =   DB::table('m_schedule')->get()->toArray();
            
            //print_r($getannuxureData);die;
            $date = date('d-m-Y');
            $title = $date . '_' . "Election Commission of India";
            $mpdf->setHeader($candidateName . ' | ' . $candidateElectioType . ' | ' . $candidateParty);

            $mpdf->SetFooter($date . '|' . "Election Commission of India" . '|{PAGENO}');
            $mpdf->SetProtection(array('print'));
            $mpdf->SetTitle($title);
            $mpdf->SetAuthor("Election Commission of India");
            $mpdf->SetWatermarkText("Election Commission of India");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->SetDisplayMode('fullpage');

            $pdf = view('expenditure.pages.candidate.pdf_acknowledgement', compact('candData','getannuxureData','getSch8Data','getSch9Data','getSch10Data','getSch1Data', 'getSch2Data','getSch3Data','getSch4Data', 'getSch4aData', 'getSch5Data','getSch6Data','getSch7Data','electionSch','electionDetails'));
            $mpdf->WriteHTML($pdf);
			$mpdf->Output();
            
        } else {
            return redirect('/ecrp-login');
        }
    }
	
	
	public function downloadEcrpStatusReport(Request $request) {
        if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;

           
            $mpdf = new \Mpdf\Mpdf();
            $mobile=$user->mobile;
            $id=$user->id;

            $candData = DB::table('candidate_nomination_detail')
                        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
                         ->join("m_election_details", function($join) {
                                $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                                ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no"); 
                          })
                          ->join("m_pc", function($joins) {
                              $joins->on("m_pc.ST_CODE", "=", "candidate_nomination_detail.st_code")
                              ->on("m_pc.PC_NO", "=", "candidate_nomination_detail.pc_no");
                          })
                         ->join('m_state','m_state.ST_CODE','=','candidate_nomination_detail.st_code') 
                          ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
                          ->leftjoin('expenditure_reports','expenditure_reports.candidate_id','=','candidate_nomination_detail.candidate_id')
                          ->select('candidate_nomination_detail.*','m_state.ST_NAME','m_pc.*','candidate_personal_detail.*','m_election_details.*','expenditure_reports.finalized_status','expenditure_reports.updated_at as finalized_date') 
                          ->where('candidate_nomination_detail.application_status','=','6')
                          ->where('candidate_nomination_detail.finalaccepted','=','1')
                          ->where('candidate_nomination_detail.symbol_id','<>','200')
                          ->where('candidate_nomination_detail.party_id', '<>', '1180')
                          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                          ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();


            // get CEO status cand_name ELECTION_TYPE
            $candidateName        = !empty($candData->cand_name)?$candData->cand_name:"N/A";
            ////annuxure-e2 form data///
            $getannuxureData      = $this->expschmodel->GetAnnuxureData($candData->candidate_id);
            ///end annuxure-e2 form data///
            ///akcnowldge form data///
            $getSch1Data          = $this->expschmodel->GetSch1Data($candData->candidate_id);
            $getSch2Data          = $this->expschmodel->GetSch2Data($candData->candidate_id);
            $getSch3Data          = $this->expschmodel->GetSch3Data($candData->candidate_id);
            $getSch4Data          = $this->expschmodel->GetSch4Data($candData->candidate_id);
            $getSch4aData         = $this->expschmodel->GetSch4aData($candData->candidate_id);
            $getSch5Data          = $this->expschmodel->GetSch5Data($candData->candidate_id);
            $getSch6Data          = $this->expschmodel->GetSch6Data($candData->candidate_id);
            $getSch7Data          = $this->expschmodel->GetSch7Data($candData->candidate_id);
            $getSch8Data          = $this->expschmodel->GetSch8Data($candData->candidate_id);
            $getSch9Data          = $this->expschmodel->GetSch9Data($candData->candidate_id);
            $getSch10Data         = $this->expschmodel->GetSch10Data($candData->candidate_id);
            


            //print_r($getannuxureData);die;
            $date                 = date('d-m-Y');
            $title                = $date . '_' . "Election Commission of India";
            $mpdf->setHeader($candidateName . ' | ' . "General" . ' | ' . "Bjp");

            $mpdf->SetFooter($date . '|' . "Election Commission of India" . '|{PAGENO}');
            $mpdf->SetProtection(array('print'));
            $mpdf->SetTitle($title);
            $mpdf->SetAuthor("Election Commission of India");
            $mpdf->SetWatermarkText("Election Commission of India");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->SetDisplayMode('fullpage');

            $pdf                = view('expenditure.pages.candidate.pdf_acknowledgement', compact('candData',' getannuxureData','getSch8Data','getSch9Data','getSch10Data','getSch1Data', 'getSch2Data','getSch3Data','getSch4Data', 'getSch4aData', 'getSch5Data','getSch6Data','getSch7Data'));
            $mpdf->WriteHTML($pdf);
			      $mpdf->Output('ECRP_FILED_REPORT.pdf','D');

        } else {
            return redirect('/ecrp-login');
        }
    }
    
	public function UploadEcrpFile(Request $request)
	{
		 if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;
		$mobile=$user->mobile;
        $id=$user->id;

            $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();
			$candidate_id = $candData->candidate_id;
			$file = $request->file;  
			 $filesize = $file->getSize();
			$filerealpath = $file->getRealPath();
			 $fileExt = $file->getClientOriginalExtension();
			$fileName = rand().$file->getClientOriginalName();
			
			if($filesize>3000000 || $fileExt !="pdf"){
				return 0;
				}
				else
				{
				//Move Uploaded File
				 $destinationPath = public_path().'/ExpenditureReport';
				 $file->move($destinationPath,$fileName);
				 ///insert ecrp_cand_scanned_file data
				 $data = array("candidate_id"=>$candidate_id,"filename"=>$fileName,"status"=>"1","created_by"=>$candidate_id,"updated_by"=>$candidate_id);
				 $InsertScannedFile = $this->commonModel->insertData('ecrp_cand_scanned_file',$data);
				 return 1;
				}
				
			
		 }
		 else
		 {
			 return redirect('/ecrp-login');
		 }

	}
	
	
	
	////////////////upload affidavit form////////////////////////////
	public function UploadAffFile(Request $request)
	{
		 if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;
		$mobile=$user->mobile;
        $id=$user->id;

            $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();
			$candidate_id = $candData->candidate_id;
			$file = $request->file;  
			 $filesize = $file->getSize();
			$filerealpath = $file->getRealPath();
			 $fileExt = $file->getClientOriginalExtension();
			$fileName = rand().$file->getClientOriginalName();
			
			if($filesize>3000000 || $fileExt !="pdf"){
				return 0;
				}
				else
				{
				//Move Uploaded File
				 $destinationPath = public_path().'/ExpenditureReport';
				 $file->move($destinationPath,$fileName);
				 ///insert ecrp_cand_scanned_file data
				 $data = array("candidate_id"=>$candidate_id,"filename"=>$fileName,"status"=>"1","created_by"=>$candidate_id,"updated_by"=>$candidate_id);
				 $InsertScannedFile = $this->commonModel->insertData('ecrp_cand_aff_scanned_file',$data);
				 return 1;
				}
				
			
		 }
		 else
		 {
			 return redirect('/ecrp-login');
		 }

	}
	//////////////////end upload/////////////////////////////////////
	
	
	
	
	/////////////////////////////////upload acknolegement form////////////////////////////
	public function UploadAckFile(Request $request)
	{
		 if (Auth::check()) {
            $users          =   Session::get('login_details');
            $user           =   Auth()->user();
            $userid         =   $user->id;
		$mobile=$user->mobile;
        $id=$user->id;

            $candData = DB::table('candidate_nomination_detail')
        ->join('candidate_personal_detail','candidate_personal_detail.candidate_id', '=','candidate_nomination_detail.candidate_id')
        ->leftjoin('ecrp_user_data','ecrp_user_data.candidate_id','=','candidate_nomination_detail.candidate_id')
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','ecrp_user_data.*','candidate_nomination_detail.nom_id as nom_ids','candidate_nomination_detail.candidate_id as candidate_ids')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_personal_detail.cand_mobile', '=',  $mobile)->first();
			$candidate_id = $candData->candidate_id;
			$file = $request->file;  
			 $filesize = $file->getSize();
			$filerealpath = $file->getRealPath();
			 $fileExt = $file->getClientOriginalExtension();
			$fileName = rand().$file->getClientOriginalName();
			
			if($filesize>3000000 || $fileExt !="pdf"){
				return 0;
				}
				else
				{
				//Move Uploaded File
				 $destinationPath = public_path().'/ExpenditureReport';
				 $file->move($destinationPath,$fileName);
				 ///insert ecrp_cand_scanned_file data
				 $data = array("candidate_id"=>$candidate_id,"filename"=>$fileName,"status"=>"1","created_by"=>$candidate_id,"updated_by"=>$candidate_id);
				 $InsertScannedFile = $this->commonModel->insertData('ecrp_cand_ack_scanned_file',$data);
				 return 1;
				}
				
			
		 }
		 else
		 {
			 return redirect('/ecrp-login');
		 }

	}
	///////////////////////////////////end acknolegement form/////////////////////////
	public function logout(Request $request){  
             // Remove an item from the session
                 Session::forget('key');
                // Remove all of the items from the session
                 Session::flush();
                // Generate a new session identifier
                 Session::regenerate();
                // Flash a key / value pair to the session
                 Session::flash('key', 'value');
                // Reflash all of the session flash data
                 Session::reflash();
                 
               Auth::logout();
               Auth::guard('admin')->logout();
               return Redirect::to('/ecrp-login');              
           
    }
    //////////////////////end pdf generation///////////////////
}
