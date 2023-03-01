<?php
namespace App\Http\Controllers\API;
use Laravel\Passport\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\User;
use App\commonModel;
use DB;
use Session;
use App\models\{States, Districts, AC};
use Mail;
use App\Helpers\SmsgatewayHelper;
use Illuminate\Support\Facades\Input;
use Redirect;
use PDF;
use Carbon\Carbon;
//INCLUDING TRAIT FOR COMMON FUNCTIONS
//use App\Http\Traits\CommonTraits;
 
class UsersController extends Controller
{
    public function __construct()
    {
        $this->commonModel = new commonModel();
    }
	
	    //USING TRAIT FOR COMMON FUNCTIONS
  // use CommonTraits;

    public $successStatus = 200;
    public $createdStatus = 201;
    public $nocontentStatus = 204;
    public $notmodifiedStatus = 304;
    public $badrequestStatus = 400;
    public $unauthorizedStatus = 401;
    public $notfoundStatus = 404;
    public $intservererrorStatus = 500;

 
public function login(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|numeric|digits:10',
                'deviceId' => 'required'
            ]);
         
         if ($validator->fails()) {
						$error['success'] =  'false';
						$error['message'] = 'please input correct mobile no';
                        return response()->json($error,$this->successStatus);            
                    } 

            $userInputs = $request->all();
            $mobile = trim($userInputs['mobile']);
            $device_id = trim($userInputs['deviceId']);
            $app_id = "cadidateApp";

            $loginDb = DB::table('candidate_personal_detail')->where('cand_mobile', '=', $mobile)->first();
            $newlogin = DB::table('user_login')->where('mobile', '=', $mobile)->first();
            if(isset($loginDb)){
            $partyid = DB::table('candidate_nomination_detail')->where('candidate_id',$loginDb->candidate_id)->first();
            
			if(!empty($newlogin)){
			if(!is_null($newlogin->otp_time)){
                        $currentTime = Carbon::now();
                        $diff=$currentTime->diffInSeconds($newlogin->otp_time);
                        }else{ 
                            $diff=61; 
                        }
			}else{
				$diff = 61;
			}

                    if($diff>60){
			
             $user = user::updateOrCreate(
				['mobile'=> $loginDb->cand_mobile],
                [ 'name'=> $loginDb->cand_name,
				'candidate_id' => $loginDb->candidate_id,
                'authority_id' => "0",
                'role_id'=>'2',
				'otp_time'=>Carbon::now(),
                'party_id'=>$partyid->party_id,
                'mobile'=> $loginDb->cand_mobile,
                'device_id'=> $device_id,
				'otp_attempt'=>'0',
                'device_type'=>'Mobile',
                'created_at'=> date('Y-m-d H:m:s'),
                'password'=> bcrypt($loginDb->cand_mobile),
                'verify_otp'=> '0',
                'app_id'=> $app_id]
				);
                $logid=$user->id;
            
                    $candidate_id= $loginDb->candidate_id;
                    $this->sendOtp($loginDb->cand_mobile, $candidate_id);
      
                    $success['success'] =  'true';
                    $success['message'] = 'Login Successfully';
                    $success['candidateId'] = (string)$candidate_id;
                    $success['mobile_otp'] = 'OTP has been send to registered mobile no, please enter to verify mobile number,Candidate App ';
                  return response()->json($success, $this->successStatus);
            }
            else{
                $success['success'] =  'false';
                $success['message'] = 'Please wait for 1 minute to resend OTP';
                $success['mobile_otp'] = 'Can Send only 1 OTP per minute';
                return response()->json($success, $this->successStatus);
            }
			}
            else{
                $error['success'] =  'false';
                $error['message'] = 'Candidate with Input Mobile number does not exist';
                
                return response()->json($error, $this->successStatus);
            }
        } catch (Exception $ex) {
            return response()->json(['error'=>'Internal Server Error'], $this->intservererrorStatus);
        }
}
    
    
public function sendOtp($mobno, $userId)
    {
        $otp = rand(100000, 999999);
		//$otp = '123456';
        $datamob = array('OTP'=>$otp);
        DB::table('user_login')->where('candidate_id', $userId)->update($datamob);

        $mobile_message = 'Your OTP is ' .$otp. ' for ECI Candidate App. Please enter the OTP to proceed. Do not share this OTP';
		//$msgstatus = $this->sendmessage($mobno, $mobile_message);
		$msgstatus = SmsgatewayHelper::gupshup($mobno, $mobile_message);
        //$msgstatus = SmsgatewayHelper::sendOtpSMS($mobile_message, $mobno);
		
        
    }
    
    
public function verifyOtp(Request $request) {
        try{
            $validator = Validator::make($request->all(), [
                'otp' => 'required',
                'candidateId' => 'required|numeric',
				'mobile' => 'required|numeric|digits:10',
            ]);
            if ($validator->fails()) {
                        $error['success'] =  'false';
                        $error['message'] = 'please input correct mobile no';
                        return response()->json($error,$this->successStatus);              
                    }
             $inputs = $request->all();
              
             $otp = trim($inputs['otp']);
             $candidate_id = trim($inputs['candidateId']);
             $mobile = trim($inputs['mobile']);

            $newuser = User::where('mobile', $mobile)->where('candidate_id', '=',$candidate_id)->first();

             if(isset($newuser)){
				 
			$attempts=$newuser->otp_attempt;
            $this->otp_attempt($newuser->id, $attempts+1);

            if($attempts>2){
                return response()->json(['success' => false, 'message' => "reached maximum attempts Please resend otp!"], 200);
            }
				 
				 
             $token = $newuser->createToken('MyApp')->accessToken;
             $mobileOTP = $newuser->otp;
             $mobile = $newuser->mobile;
             if($mobileOTP == $otp)
             { 
              $logdata = array('access_token'=>$token,'login_flag'=>1,
                            'isActive'=>1,'verify_otp'=>1);

        DB::table('user_login')->where([['mobile' , $mobile],['candidate_id' , $candidate_id]])->update($logdata);
          $login_history = array(
                                'user_login_id'=>$newuser->id,
                                'ipaddress'=>request()->ip(),
                                'app_id'=>$newuser->app_id,
                                'user_device_id'=>$newuser->device_id,
                                'device_type'=>'Mobile',
                                'role_id'=>'2',
                                'Login_time'=>Date('Y-m-d H:i:s'),
                                'session_id'=>$token,
                                'user_activity'=>'login on Mobile',
                                'Login_date'=>Date('Y-m-d'));
            $a=DB::table('user_history')->insert($login_history); 
              
          $cand_d =DB::table('candidate_personal_detail')->where([['candidate_id' , $candidate_id]])->first();
          $nom_d = DB::table('candidate_nomination_detail')
          ->where([['candidate_id' , $candidate_id]])->get();
		  if(count($nom_d)>0){
          foreach($nom_d as $k) {
            $dat[]=array("nomId"=>$k->nom_id,"stateName"=>trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME),'districtName'=>trim($this->commonModel->getdistrictbydistrictno($k->st_code,$k->district_no)->DIST_NAME),"PCName"=>trim($this->commonModel->getpcbypcno($k->st_code,$k->pc_no)->PC_NAME),"application_status"=>trim($k->application_status));
          }}else{
			  $dat = array();
		  }
          
            $success['success'] = 'true';
            $success['message'] = 'OTP verified';
			$success['userloginid'] = $newuser->id;
            $success['candidateId'] = $candidate_id;
            $success['accessToken'] = (string)$token;
            $success['name'] =$cand_d->cand_name;
            $success['candImage'] =$cand_d->cand_image;
            
                return response()->json($success, $this->successStatus);  
             } else {
                  $error['success'] = 'false';
                  $error['message'] = 'Entered Otp is wrong, please enter correct otp';
                  return response()->json($error, $this->successStatus);  
             }
            }else{
               $error['success'] = 'false';
               $error['message'] = 'Entered data not exist ,may be wrong otp, mobile and candidateId';
   
               return response()->json($error, $this->successStatus);   
            }
        } catch (Exception $ex) {
            return response()->json(['error'=>'Internal Server Error'], $this->intservererrorStatus);
        }
}

public function nominationlisting(Request $request) {
	         try{
            $validator = Validator::make($request->all(), [
                'accessToken' => 'required',
                'candidateId' => 'required|numeric'
            ]);
            if ($validator->fails()) {
                    return response()->json(['error'=>'please check the input details'], $this->successStatus);            
                    }
             $inputs = $request->all();
             // dd($inputs);
             $accessToken = trim($inputs['accessToken']);
             $candidate_id = trim($inputs['candidateId']);
            
            $newuser = User::where([['access_token', '=', $accessToken]])->where('candidate_id', '=',$candidate_id)->first();
            
             if(isset($newuser)){
             if($newuser->access_token == $accessToken)
             { 
                $nom_d = DB::table('candidate_nomination_detail')
				->where([['candidate_id' , $candidate_id]])->get();
	
			  if(count($nom_d)>0){
			  foreach($nom_d as $k) {
				$dat[]=array("nomId"=>$k->nom_id,"stateName"=>trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME),'districtName'=>trim($this->commonModel->getdistrictbydistrictno($k->st_code,$k->district_no)->DIST_NAME),"PCName"=>trim($this->commonModel->getpcbypcno($k->st_code,$k->pc_no)->PC_NAME),"application_status"=>trim($this->commonModel->getnameBystatusid($k->application_status)),"application_status_data"=>$k->application_status);
			  }}else{
				  $dat = array();
				}
                $success['success'] = 'true';
                $success['candidateId'] = $candidate_id;
                $success['nominitationdata'] =$dat;
                return response()->json($success, $this->successStatus);  
             } else {
                  $error['success'] = 'false';
                  $error['message'] = 'Please Check your Access Token';
                  return response()->json($error, $this->successStatus);  
             }
            }else{
               $error['success'] = 'false';
               $error['message'] = 'Entered Access Token or candidate Wrong';
   
               return response()->json($error, $this->successStatus);   
            }
        } catch (Exception $ex) {
            return response()->json(['error'=>'Internal Server Error'], $this->intservererrorStatus);
        }
}

public function nominationstatus(Request $request) {
        try{
            $validator = Validator::make($request->all(), [
                'accessToken' => 'required',
                'candidateId' => 'required|numeric',
                'nomId' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                    return response()->json(['error'=>'please check the input details'], $this->successStatus);            
                    }
             $inputs = $request->all();
             $accessToken = trim($inputs['accessToken']);
             $candidate_id = trim($inputs['candidateId']);
             $nom_id = trim($inputs['nomId']);
            
            $newuser = User::where([['access_token', '=', $accessToken]])->where('candidate_id', '=',$candidate_id)->first();
             if(isset($newuser)){
             if($newuser->access_token == $accessToken)
             { 
                $cand_d =DB::table('candidate_personal_detail')->where([['candidate_id' , $candidate_id]])->first();  
                $nom_d = DB::table('candidate_nomination_detail')->where([['nom_id',$nom_id],['candidate_id' , $candidate_id]])->get();
                $afidav = DB::table('candidate_affidavit_detail')->where([['nom_id',$nom_id],['candidate_id' , $candidate_id]])->first();
                if(!empty($afidav)) {
                        $afid=array("affidavitLink"=>url($afidav->affidavit_path));
                }else{
                    $afid = array("affidavitLink"=>"");
                }

				if(count($nom_d)>0){ $msg = 'true';
                foreach($nom_d as $k) {
                    // dd($k);
                    $ac = ""; $pc="";$nominationaccept="";
                    if($k->ac_no != null){ $ac = trim($this->commonModel->getacbyacno($k->st_code,$k->ac_no)->AC_NAME); }
                    if($k->pc_no != null){ $pc = trim($this->commonModel->getpcbypcno($k->st_code,$k->pc_no)->PC_NAME); }
                    if($k->scrutiny_date != null){ $nominationaccept=$k->scrutiny_date; }
                    $dat=array("nomId"=>$k->nom_id,"stateName"=>trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME),'districtName'=>trim($this->commonModel->getdistrictbydistrictno($k->st_code,$k->district_no)->DIST_NAME),"ACName"=>$ac,"PCName"=>$pc,"application_status"=>trim($this->commonModel->getnameBystatusid($k->application_status)),"application_status_data"=>$k->application_status,"date_of_submit_nomination"=>$k->date_of_submit,"accept_nomination_date"=>$nominationaccept);   
                }
				}else { $msg = 'false';
					$dat = "data not present may be your nomId wrong";
				}
                $success['success'] = $msg;
                $success['candidateId'] = $candidate_id;
                $success['name'] =$cand_d->cand_name;
                $success['candImage'] =$cand_d->cand_image;
                $success['nominitationdata'] =$dat;
                $success['affidavit'] =$afid;
                return response()->json($success, $this->successStatus);  
             } else {
                  $error['success'] = 'false';
                  $error['message'] = 'Wrong Access Token Entered';
                  return response()->json($error, $this->successStatus);  
             }
            }else{
               $error['success'] = 'false';
               $error['message'] = 'Entered Access Token or candidate id Wrong';
   
               return response()->json($error, $this->successStatus);   
            }
        } catch (Exception $ex) {
            return response()->json(['error'=>'Internal Server Error'], $this->intservererrorStatus);
        }
}

public function permissionlistview(Request $request) {
	try{
            $validator = Validator::make($request->all(), [
                'accessToken' => 'required',
                'candidateId' => 'required|numeric',
                'userloginId' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                    return response()->json(['error'=>'Please check the input details'], $this->successStatus);            
                    }
             $inputs = $request->all();
             $accessToken = trim($inputs['accessToken']);
             $candidate_id = trim($inputs['candidateId']);
             $login_id = trim($inputs['userloginId']);
            
            $newuser = User::where([['access_token', '=', $accessToken]])->where('candidate_id', '=',$candidate_id)->first();
             if(isset($newuser)){
             if($newuser->access_token == $accessToken)
             { 
               
                 $permis = DB::table('user_login')
                 ->join('permission_request','user_login.id','=','permission_request.user_id')
				 ->Join('permission_type', 'permission_request.permission_type_id', '=', 'permission_type.id')
				 ->join('permission_master', 'permission_type.permission_type_id', '=', 'permission_master.id')
				 ->leftJoin('location_master', 'permission_request.location_id', '=','location_master.id')
                 ->select('permission_request.location_id','permission_request.Other_location','location_master.location_name',
                 'permission_request.id','permission_master.permission_name','permission_request.added_at',
                 'permission_request.date_time_start','permission_request.date_time_end','permission_request.approved_status',
                 'permission_request.cancel_status','permission_request.action_date as roupdatedate','location_master.latitude','location_master.longitude')
                 ->where('user_login.id' , $login_id)->get();
                
                if(count($permis)>0) {
					$msg = 'true';
                    foreach($permis as $f) {
						if($f->approved_status == 0){ $status = 'Pending'; }
						elseif($f->approved_status == 1){ $status = 'Inprocess'; }
						elseif($f->approved_status == 2){ $status = 'Accept'; }
						elseif($f->approved_status == 3){ $status = 'Reject'; }
						
                        $perm[]=array("permission_id"=>$f->id,'Permission_longitude'=>$f->longitude,'Permission_latitude'=>$f->latitude,
                        "permission"=>$f->permission_name,"permission_registerd_date"=>$f->added_at,"permission_action_date"=>$f->roupdatedate,
                        "permission_from"=>$f->date_time_start,"permission_till"=>$f->date_time_end,"permission_approved_status"=>$f->approved_status,
                        "permission_approved_status_detail"=>$status,"location_name"=>$f->location_name,"location_id"=>$f->location_id,
                        "location_other_name"=>$f->Other_location,"is_canceled"=>$f->cancel_status);
                    }
                }else{ 
					$msg = 'false';
                    $perm = array();
                }
                $success['success'] = $msg;
				$success['userloginid'] = $newuser->id;
                $success['candidateId'] = $candidate_id;
                $success['permission'] = $perm;
                return response()->json($success, $this->successStatus);  
             } else {
                  $error['success'] = 'false';
                  $error['message'] = 'Entered Access Token wrong';
                  return response()->json($error, $this->successStatus);  
             }
            }else{
               $error['success'] = 'false';
               $error['message'] = 'Entered Access Token or candidate id wrong';
   
               return response()->json($error, $this->successStatus);   
            }
        } catch (Exception $ex) {
            return response()->json(['error'=>'Internal Server Error'], $this->intservererrorStatus);
        }
}

public function permissionpreview(Request $request) {
	try{
            $validator = Validator::make($request->all(), [
                'accessToken' => 'required',
                'candidateId' => 'required|numeric',
                'userloginId' => 'required|numeric',
				'permissionId' => 'required|numeric',
				'location_id' => 'required',
            ]);
            if ($validator->fails()) {
                    return response()->json(['error'=>'Please check the input details'], $this->successStatus);            
                    }
             $inputs = $request->all();
             $accessToken = trim($inputs['accessToken']);
             $candidate_id = trim($inputs['candidateId']);
             $login_id = trim($inputs['userloginId']);
			 $permission_id = trim($inputs['permissionId']);
			 $location_id = trim($inputs['location_id']);
            
            $newuser = User::where([['access_token', '=', $accessToken]])->where('candidate_id', '=',$candidate_id)->first();
             if(isset($newuser)){
             if($newuser->access_token == $accessToken)
             {
                $result = DB::table('permission_request')
                 ->Join('permission_type', 'permission_request.permission_type_id', '=', 'permission_type.id')
				 ->join('permission_master', 'permission_type.permission_type_id', '=', 'permission_master.id')
				 ->rightJoin('user_login', 'permission_request.user_id', '=','user_login.id')
				 ->rightJoin('user_data','user_login.id', '=','user_data.user_login_id')
				 ->leftJoin('user_role','user_login.role_id','=','user_role.role_id');
				 if($location_id != 'other' && $location_id != '0')
				 {
					$result->rightJoin('location_master', 'permission_request.location_id', '=', 'location_master.id');
				 }
				 
				 if($location_id != 'other' && $location_id != '0')
				 {
                 $result->select('user_role.role_name','permission_request.required_files','permission_request.location_id',
                 'permission_request.Other_location','location_master.location_name','user_data.name','user_data.fathers_name',
                 'user_data.email','user_data.mobileno','user_data.gender','user_data.dob','user_data.address'
                 ,'user_data.added_at as form filled date','permission_master.permission_name',
                 'permission_request.date_time_start','permission_request.date_time_end','permission_request.approved_status',
                 'permission_request.added_at','location_master.latitude','location_master.longitude',
                 'permission_request.cancel_status','permission_request.dist_no','permission_request.ac_no','permission_request.st_code');
				 }
				 else{
                     $result->select('user_role.role_name','permission_request.required_files','permission_request.location_id',
                     'permission_request.Other_location','user_data.name','user_data.fathers_name','user_data.email',
                     'user_data.mobileno','user_data.gender','user_data.dob','user_data.address','user_data.added_at as form filled date',
                     'permission_master.permission_name','permission_request.date_time_start','permission_request.date_time_end',
                     'permission_request.approved_status','permission_request.added_at',
                     'permission_request.cancel_status','permission_request.dist_no','permission_request.ac_no','permission_request.st_code');
				 }
                 $result->where([['user_login.id' , $login_id],['permission_request.id', $permission_id]]);
				 $permis =$result->first();
				
                if(!empty($permis)) {
						if($permis->approved_status == 0){ $status = 'Pending'; }
						elseif($permis->approved_status == 1){ $status = 'Inprocess'; }
						elseif($permis->approved_status == 2){ $status = 'Accept'; }
                        elseif($permis->approved_status == 3){ $status = 'Reject'; }
                        $ac="not found";$pc="not found";

                        if($permis->ac_no != 0){ $ac = trim($this->commonModel->getacbyacno($permis->st_code,$permis->ac_no)->AC_NAME); }
                        
                        if($permis->dist_no != 0){$dist = trim($this->commonModel->getdistrictbydistrictno($permis->st_code,$permis->dist_no)->DIST_NAME);}

						if($permis->required_files != 'null' && $permis->required_files != 'NULL'){
                        
                        $docdata=explode(',',$permis->required_files);

                        $doc = array();
						for($i=0;$i < count($docdata); $i++){
							if(!empty($docdata[$i])){
							$doc[] = array("doc_by_candidate"=>url('uploads/userdoc/permission-document').'/'.$docdata[$i]) ;}
						}
						}else{
							$doc = array();
						}
                        
                        
						if(!empty($permis->location_id) && $permis->location_id !=0)
						{
							if(empty($permis->location_name)){
								return response()->json(['success' => false, 'message' => "Please Check location_id"], 200);
							}
							
							$location_id=$permis->location_id;
							$longitude=$permis->longitude;
							$latitude=$permis->latitude;
							$location_name=$permis->location_name;
							
						}else{
							$location_id=0;
							$longitude=0;
							$latitude=0;
							$location_name="Location Name Not Defiend";
						}
						$msg = 'true';
                        $perm=array("name"=>$permis->name,"father_name"=>$permis->fathers_name,"email"=>$permis->email,"mobile"=>$permis->mobileno,
                            "gender"=>$permis->gender,"dob"=>$permis->dob,"address"=>$permis->address,"state"=>trim($this->commonModel->getstatebystatecode($permis->st_code)->ST_NAME),
                            'DistrictName'=>$dist,"ACName"=>$ac,"PCName"=>$pc,
                            "permission"=>$permis->permission_name,"permission_registerd_date"=>$permis->added_at,"permission_from"=>$permis->date_time_start,
                            "permission_till"=>$permis->date_time_end,"permission_approved_status"=>$permis->approved_status,"permission_approved_status_detail"=>$status,
                            'Permission_longitude'=>$longitude,'Permission_latitude'=>$latitude,"location_name"=>$location_name,"location_id"=>$location_id,
                            "location_other_name"=>$permis->Other_location,"cand_role_name"=>$permis->role_name,"doc_upload"=>$doc,"is_canceled"=>$permis->cancel_status);
                    }
                else{
					$msg = 'false';
                    $perm = (object)array();
                }
                $success['success'] = $msg;
				$success['userloginid'] = $newuser->id;
                $success['candidateId'] = $candidate_id;
                $success['permission'] = $perm;
                return response()->json($success, $this->successStatus);  
             } else {
                  $error['success'] = 'false';
                  $error['message'] = 'Entered Access Token wrong';
                  return response()->json($error, $this->successStatus);  
             }
            }else{
               $error['success'] = 'false';
               $error['message'] = 'Entered Access Token or candidate Id wrong';
   
               return response()->json($error, $this->successStatus);   
            }
        } catch (Exception $ex) {
            return response()->json(['error'=>'Internal Server Error'], $this->intservererrorStatus);
        }

}


public function logout(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'accessToken' => 'required',
            'candidateId' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>'Please check the input details'], $this->successStatus);
        }
        $accessToken = trim($request->accessToken);
        $candidate_id = trim($request->candidateId);
        $newuser = User::where('access_token',$accessToken)->where('candidate_id', '=',$candidate_id)->first();
        if(isset($newuser)){
            if($newuser->access_token == $accessToken)
            {      
            $token = '';
			$otp = '';
            $logdata = array('access_token'=>$token,'login_flag'=>0,'otp'=>$otp,
            'isActive'=>0,'verify_otp'=>0);
            DB::table('user_login')->where([['access_token' , $accessToken],['candidate_id' , $candidate_id]])->update($logdata);
            
            $json = [
                'success' => true,
                'message' => 'You are Logged out.',
            ];
            return response()->json($json);
            }else{
                return response()->json(['success' => false, 'error' => "Wrong Access Token Entered!"]);
            }
        }else{
            return response()->json(['success' => false, 'error' => "Please Check Access Token or candidate id!"]);
        }
    }
  
      public function otp_attempt($userid,$attempt_value)
    {
        User::where('id', $userid)->update(array('OTP_attempt' => $attempt_value));
    }
}