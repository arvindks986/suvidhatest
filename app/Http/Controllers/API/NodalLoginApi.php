<?php
namespace App\Http\Controllers\API;
use Laravel\Passport\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;
use App\commonModel;
use App\adminmodel\NodalUser;
use Session;
use App\models\{States, Districts, AC};
use Mail;
use App\Helpers\SmsgatewayHelper;
use Illuminate\Support\Facades\Input;
use Redirect;
use Carbon\Carbon;
use App\Helpers\SendNotification;
use Notification;
use Illuminate\Notifications\Notifiable;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Classes\xssClean;
//INCLUDING TRAIT FOR COMMON FUNCTIONS
//use App\Http\Traits\CommonTraits;

class NodalLoginApi extends Controller
{
    public function __construct() {
        $this->xssClean = new xssClean;
        $this->commonModel = new commonModel();
        $this->ResponseMethod = new ResponseController;
        $this->bad_response = $this->ResponseMethod::HTTP_BAD_REQUEST;
        $this->ok_response = $this->ResponseMethod::HTTP_ACCEPTED; 
        $this->okStatus = "success";
        $this->errStatus = "error";
    }
	
	    //USING TRAIT FOR COMMON FUNCTIONS
   //use CommonTraits;

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
                'mobile' => 'required|string|max:10|min:10',
                'deviceId' => 'required|string',
                'fcm_id' => 'required',
            ]);
         
         if($validator->fails()){
//                return response()->json(['success' => false,'message'=>'Mobile number must be of 10 digits']);
              return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors(), $this->bad_response);
            } 

            $userInputs = $request->all();
            $mobile = trim($userInputs['mobile']);
            $device_id = trim($userInputs['deviceId']);
            $fcm_id = trim($userInputs['fcm_id']);
			
            $app_id = "NodalApp";
            $type = 'Permission';

			//SendNotification::send_notification_fcm('Permission Assigned','You Have Assigned a Permission',$fcm_id,$type,'1');

            $loginDb = DB::table('authority_masters')->where('mobile', '=', $mobile)->first();
            
            if(isset($loginDb)){
                    $user = NodalUser::firstOrNew(['mobile' => $loginDb->mobile]);

                    if(!is_null($user->otp_time)){
                        $currentTime = Carbon::now();
                        $diff=$currentTime->diffInSeconds($user->otp_time);
                        // dd($diff);
                        }else{ 
                            $diff=61; 
                        }

                    if($diff>60){
                       $user->name= $loginDb->name;
                       $user->authority_id =$loginDb->id;
                       $user->email= $loginDb->email;
                       $user->mobile= $loginDb->mobile;
                       $user->device_id= $device_id;
                       $user->fcm_id= $fcm_id;
                       $user->device_type='Mobile';
                       $user->otp_time= Carbon::now();
                       $user->role_id='4';
                       $user->otp_attempt='0';
                       $user->created_at= date('Y-m-d H:m:s');
                       $user->password= bcrypt($loginDb->mobile);
                       $user->verify_otp= '0';
                       $user->app_id= $app_id;
                       $user->save();
                       $logid=$user->id;
                       $id= $loginDb->id;
                       $this->sendOtp($loginDb->mobile, $id);
      
                    $success['success'] =  true;
                    $success['message'] = 'Login Successfully';
                    $success['Id'] = (string)$id;
                    $success['mobile_otp'] = 'OTP has been send to registered mobile number please enter to verify mobile number of Nodal officer';
//                  return response()->json($success, $this->successStatus);
                    return $this->ResponseMethod->get_http_response($this->successStatus, $success, $this->ok_response);
            }
            else{
                $success['success'] =  true;
                $success['message'] = 'Please wait for 1 minute to resend OTP';
                $success['Id'] = (string)$loginDb->id;
                $success['mobile_otp'] = 'Can Send only 1 OTP per minute';
//                return response()->json($success, $this->successStatus);
                return $this->ResponseMethod->get_http_response($this->successStatus, $success, $this->ok_response);
            }
        }
            else{
//                $error['success'] =  false;
                $error['message'] = 'Nodal officer with this mobile number does not exist!';
                
//                return response()->json($error, $this->successStatus);
                return $this->ResponseMethod->get_http_response($this->successStatus, $error, $this->ok_response);
            }
        } catch (Exception $ex) {
//            return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
            return response()->json(encrypt(['success' => false,'error'=>'Internal Server Error']), $this->intservererrorStatus);
        }
}
    
    
public function sendOtp($mobno, $userId)
    {
        $otp = rand(100000, 999999);
	    //$otp = '123456';
        $datamob = array('OTP'=>$otp);
        DB::table('authority_login')->where([['authority_id', $userId],['mobile',$mobno],['role_id', '4']])->update($datamob);
        $mobile_message = 'Your OTP is ' .$otp. ' for ECI Nodal App. Please enter the OTP to proceed. Do not share this OTP';
		//$msgstatus = $this->sendmessage($mobno, $mobile_message);
		$msgstatus = SmsgatewayHelper::gupshup($mobno, $mobile_message);
        //$msgstatus = SmsgatewayHelper::sendOtpSMS($mobile_message, $mobno);
        
    }
    
    
public function verifyOtp(Request $request) {
        try{
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|string|max:10|min:10',
                'otp' => 'required',
                'nodalid' => 'required|numeric'
            ]);

            if ($validator->fails()) {
//                        return response()->json(['success' => false,'error'=>'Please Check Your Input Details'], $this->successStatus);  
                 return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors(), $this->bad_response);
                    }
             $inputs = $request->all();
              
             $otp = trim($inputs['otp']);
             $id = trim($inputs['nodalid']);
             $mobile = trim($inputs['mobile']);
            $newuser = NodalUser::where('mobile', '=', $mobile)->where('authority_id', '=',$id)->where('role_id',4)->first();

            if(isset($newuser) > 0){
				
			$attempts=$newuser->otp_attempt;
            $this->otp_attempt($newuser->id, $attempts+1);

            if($attempts>2){
//                return response()->json(['success' => false, 'message' => "reached maximum attempts Please resend otp!"], 200);
                return $this->ResponseMethod->get_http_response($this->errStatus,$data,$this->bad_response);
            }
			
             $mobileOTP = $newuser->otp;
             $mobile = $newuser->mobile;

             if($mobileOTP == $otp)
             {
              $token = $newuser->createToken('MyApp')->accessToken;

              $logdata = array('remember_token'=>$token,'login_flag'=>1,
                            'isActive'=>1,'verify_otp'=>1);

            DB::table('authority_login')->where([['mobile' , $mobile],['authority_id' , $id],['role_id',4]])->update($logdata); 
            
            $login_history = array(
            'nodal_login_id'=>$id,
            'role_id'=>'4',
            'ipaddress'=>request()->ip(),
            'app_id'=>$newuser->app_id,
            'user_device_id'=>$newuser->device_id,
            'device_type'=>'Mobile',
            'Login_time'=>Date('Y-m-d H:i:s'),
            'session_id'=>$token,
            'user_activity'=>'login on Mobile',
            'Login_date'=>Date('Y-m-d'));
            $a=DB::table('nodal_history')->insert($login_history);

           $checkvalue = DB::table('authority_masters')->where([['id' , $id]])->first();
		  if(!empty($checkvalue->auth_type_id))
		  {
		   $authority_type = DB::table('authority_type')
          ->RightJoin('authority_masters', 'authority_type.id', '=', 'authority_masters.auth_type_id')
          ->where('authority_masters.id',$id)
          ->select('authority_type.name as authority_name')
          ->get();
          foreach($authority_type as $t) { 
			  if(!empty($t)){
              $at=array("Name"=>$t->authority_name);
			  }else{
				  $at = array();
			  }
          }  
			 $k = DB::table('authority_masters')->where([['id' , $id]])->first();  
		  }
		  else
		  {
			 $k = DB::table('authority_masters')
			 ->Join('authority_masters_mapping', 'authority_masters.id', '=', 'authority_masters_mapping.authority_masters_id')
			 ->select('authority_masters.st_code','authority_masters_mapping.dist_no', 'authority_masters_mapping.ac_no', 'authority_masters_mapping.pc_no', 'authority_masters.name', 'authority_masters.department', 'authority_masters.designation')
			 ->where([['authority_masters.id' , $id]])->first();  
		  $authority_type = DB::table('authority_type')
          ->RightJoin('authority_masters_mapping', 'authority_type.id', '=', 'authority_masters_mapping.auth_type_id')
          ->where('authority_masters_mapping.authority_masters_id',$id)
          ->select('authority_type.name as authority_name')
          ->get();
          foreach($authority_type as $t) { 
			  if(!empty($t)){
              $at=array("Name"=>$t->authority_name);
			  }else{
				  $at = array();
			  }
          }  
            		 
		  }
          
		  //,"AC Name"=>trim($this->commonModel->getacbyacno($k->st_code,$k->ac_no)->AC_NAME)

          if(!empty($k)) {
			  
			  if(!empty($k->pc_no) || ($k->pc_no != 0 && isset($k) ) ){
				$acname = trim($this->commonModel->getpcbypcno($k->st_code,$k->pc_no)->PC_NAME);
			}else{ 
				$acname = "";
			}
			
			if(!empty($k->dist_no) || ($k->dist_no != 0)){
				$distname = trim($this->commonModel->getdistrictbydistrictno($k->st_code,$k->dist_no)->DIST_NAME);
			}else{
				$distname = "";
			}
			  
            $dat=array("state"=>trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME),'District Name'=>$distname,"Name"=>$k->name,"Department"=>$k->department,"Designation"=>$k->designation, "AC Name"=>$acname);
          }else{
			  $dat = "";
		  }
            $success['success'] = true;
            $success['message'] = 'OTP verified';
            $success['NodalId'] = $id;
            $success['accessToken'] = (string)$token;
            $success['NodalDetails'] =$dat;
            $success['Authority_Type'] =$at;
            
//                return response()->json($success, $this->successStatus);  
            return $this->ResponseMethod->get_http_response($this->okStatus,$success,$this->ok_response);  
             } else {
                  $error['success'] = false;
                  $error['message'] = 'Entered OTP is wrong, please enter correct OTP';
//                  return response()->json($error, $this->successStatus);  
                   return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response); 
             }
            }else{
               $error['success'] = false;
               $error['message'] = 'Entered data does not exist please check Mobile or Nodal ID';
   
//               return response()->json($error, $this->successStatus);   
               return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response); 
            }
        } catch (Exception $ex) {
//            return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
            return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
    }
}

public function logout(Request $request) {
    $validator = Validator::make($request->all(), [
        'nodalid' => 'required|numeric',
        'mobile' => 'required|string|max:10|min:10',
    ]);
    if ($validator->fails()) {
//        return response()->json(['success' => false, 'error' => "Invalid data provided!"], 200);
        return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors(), $this->bad_response);
    }
    
    $id = trim($request->nodalid);
    $mobile = trim($request->mobile);
    
    $newuser = NodalUser::where('authority_id', '=',$id)->where('mobile', $mobile)->where('role_id',4)->first();
    if(isset($newuser)){
        $token = '';
        $otp= '';           
        $logdata = array('remember_token'=>$token,'login_flag'=>0,'otp'=>$otp,
        'isActive'=>0,'verify_otp'=>0);
        DB::table('authority_login')->where([['authority_id' , $id],['role_id',4]])->update($logdata);

        $json = [
            'success' => true,
            'code' => 200,
            'message' => 'Logged out successfully!',
        ];
//        return response()->json($json, '200');
        return $this->ResponseMethod->get_http_response($this->okStatus,$json,$this->ok_response);
    }else{
//        return response()->json(['success' => false, 'error' => "ID entered is not correct!"]);
        $error = 'ID entered is not correct!';
      return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
    }
}

        public function permissionlist(Request $request){ 
        $base_url = (url('/'));
        $validator = Validator::make($request->all(),[
            'accessToken' => 'required',
            'nodalid' => 'required|numeric',
        ]);

        if($validator->fails()){
//            return response()->json(['success' => false, 'error' => "Invalid data provided!"], 200);
            return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors(), $this->bad_response);
        }

        $inputs = $request->all();
        $accessToken = trim($inputs['accessToken']);
        $id = trim($inputs['nodalid']);
       
       $newuser = NodalUser::where([['remember_token', '=', $accessToken]])->where('authority_id', '=',$id)->where('role_id',4)->first();
        if(isset($newuser)){
        if($newuser->remember_token == $accessToken)
        {
		$perm_req =DB::table('permission_request')
                ->leftJoin('permission_assigned_auth','permission_request.id','=', 'permission_assigned_auth.permission_request_id')
                ->leftJoin('permission_type','permission_request.permission_type_id','=','permission_type.id')
                // ->leftjoin('permission_request_comment', 'permission_request.id', '=', 'permission_request_comment.permission_request_id')
                ->leftJoin('user_data','permission_request.user_id','=','user_data.user_login_id')
                ->leftJoin('location_master', 'permission_request.location_id', '=', 'location_master.id')
                ->leftJoin('user_login','permission_request.user_id','=','user_login.id')
                ->leftJoin('user_role','user_login.role_id','=','user_role.role_id')
                ->leftjoin('permission_master', 'permission_type.permission_type_id', '=', 'permission_master.id')
                ->select('user_role.role_name','permission_request.required_files','permission_request.location_id','permission_request.Other_location','location_master.location_name','permission_request.updated_at as roupdatedate',
                'permission_assigned_auth.comment','permission_assigned_auth.file as nodal_file','permission_assigned_auth.permission_request_id as permission_id','user_login.candidate_id as candid',
                'permission_request.approved_status as approved_status','permission_assigned_auth.created_at as createdon','permission_assigned_auth.updated_at as updatedat','date_time_start as datefrom','date_time_end as datetill',
                'permission_master.permission_name as pername','permission_assigned_auth.accept_status','user_data.fathers_name as father_name','permission_request.st_code as stcode','permission_request.dist_no as distno',
                'permission_request.ac_no as ac','permission_request.cancel_status','user_data.name as candname','user_data.address as canaddress')
                ->where([['permission_assigned_auth.authority_id',$id]])->orderBy('createdon', 'desc')->get(); 
		
          $permission_data = array();	
        //   $permission_data = array_merge(json_decode($perm_req, true),json_decode($perm_req1, true));		
		  $permission_data = array_merge(json_decode($perm_req, true));	
		 
        if(count($permission_data)>0){
                foreach($permission_data as $p){
                    
                     if($p["required_files"] != 'null' && $p["required_files"] != 'NULL'){
                        
                         $docdata=explode(',',$p["required_files"]);
                         $doc = array();
                     for($i=0;$i < count($docdata); $i++){
					 	if(!empty($docdata[$i])){
					 	$doc[] = array("doc_by_candidate"=>url('uploads/userdoc/permission-document').'/'.$docdata[$i]) ;}
                     }
                     }else{
                         $doc = array();
                     }
					 
					 
					$nodalfile_path = ($p["nodal_file"]);
                    if($nodalfile_path != null) { $nodalfile_path = $base_url."/uploads/Nodal-Uploaddocument/".$p["permission_id"].'/'.$nodalfile_path; }
             
                    if($p["ac"] != 0){
                           $acname = trim($this->commonModel->getacbyacno($p["stcode"],$p["ac"])->AC_NAME);
                    }else{
                           $acname = "Not Found";
                    }
                    
                    if($p["distno"] != 0){
                        $distname = trim($this->commonModel->getdistrictbydistrictno($p["stcode"],$p["distno"])->DIST_NAME);
                    }else{
                        $distname = "Not Found";
                    }
						
                     if($p["cancel_status"] == 0){
                         $is_canceled = 0;
                         }else{
                         $is_canceled = 1;
                         }

                        $perm_req_comment =DB::table('permission_request_comment')->where('permission_request_id',$p["permission_id"])->orderBy('id','DESC')->first();
						if(!empty($perm_req_comment)){
						$rofile_path = ($perm_req_comment->file);
						if(!empty($rofile_path) && strtolower($rofile_path) != 'null') { $rofile_path = $base_url."/uploads/RO-Uploaddocument/".$p["permission_id"].'/'.$rofile_path; }
						else{$rofile_path="";}
						}else{$rofile_path="";}

                     $can[]=array("CandidateName"=>$p["candname"],"CandidateAddress"=>$p["canaddress"],"PermissionFrom"=>$p["datefrom"],"PermissionTill"=>$p["datetill"],
                     "StateName"=>trim($this->commonModel->getstatebystatecode($p["stcode"])->ST_NAME),'DistrictName'=>$distname,
                     "ACName"=>$acname,"Accept_Status"=>$p["accept_status"],"Approve_Status"=>$p["approved_status"],"permission_name"=>$p["pername"],
                     "permission_id"=>$p["permission_id"],"created_on"=>$p["createdon"],"updated_at"=>$p["updatedat"],"comment"=>$p["comment"],"nodal_file"=>$nodalfile_path,"ro_file"=>$rofile_path,
                     "roupdatedate"=>$p["roupdatedate"],"location_name"=>$p["location_name"],"location_id"=>$p["location_id"],"location_other_name"=>$p["Other_location"],"doc_upload"=>$doc,"cand_role_name"=>$p["role_name"],
                     "is_canceled"=>$is_canceled);
            }
            }else{
                $can = array();
            }
           $success['success'] = true;
           $success['name'] =$newuser->name;
           $success['Candidatedata'] =$can;
//			return response()->json($success, $this->successStatus);
           return $this->ResponseMethod->get_http_response($this->okStatus,$success,$this->ok_response);
			
        } else {
//             $error['success'] = false;
             $error['message'] = 'Access Token you are given is invalid!';
//             return response()->json($error, $this->successStatus);  
              return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
        }
       }else{
//          $error['success'] = false;
          $error['message'] = 'Access Token or NodalId You are given is invalid!';
//          return response()->json($error, $this->successStatus);
          return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
       }
    }

   public function permissionupdate(Request $request){
        $validator = Validator::make($request->all(),[
            'accessToken' => 'required',
            'nodalid' => 'required|string',
            'permissionid' => 'required|string',
            'nodalstatus' => 'required|string',
            'comment' => 'required|string',
            'file' => 'mimes:jpeg,bmp,png,pdf|max:500000',
        ]);

        if($validator->fails()){
//            return response()->json(['success' => false, 'error' => "Invalid data provided!"], 200);
            return $this->ResponseMethod->get_http_response($this->errStatus, ['errorMessage'=> $validator->errors()->first()], $this->bad_response);
        }

        $inputs = $request->all();
        $accessToken = trim($inputs['accessToken']);
        $id = trim($inputs['nodalid']);
        $permid = trim($inputs['permissionid']);
        $nodalstatus = trim($inputs['nodalstatus']);
        $comment = $this->xssClean->clean_input($inputs['comment']);
       
       $newuser = NodalUser::where([['remember_token', '=', $accessToken]])->where('authority_id', '=',$id)->where('role_id',4)->first();
        if(isset($newuser)){
        if($newuser->remember_token == $accessToken)
        {
            if($request->hasFile('file'))
            {
                $file  = $request->file;
                $file_new_name = time().$file->getClientOriginalName();
				
				$des =public_path('/uploads/Nodal-Uploaddocument/'.$permid.'/'); 

                $file->move($des,$file_new_name);
			   $file = $file_new_name ;
            }else{
                $file='';
            }

            $u = array('accept_status'=>$nodalstatus,'approved_status'=>'1','updated_by_nodal'=>$id,'permission_assigned_auth.comment'=>$comment,'permission_assigned_auth.file'=>$file);
            $x = DB::table('permission_assigned_auth')
            ->leftjoin('permission_request', 'permission_assigned_auth.permission_request_id', '=', 'permission_request.id')
            ->where([['permission_assigned_auth.authority_id',$id],['permission_request.id',$permid]])->update($u);  

            if($x > 0){
            $msg="Data Successfully Updated";
           }else{
            $msg ="Please check your Permission Id or you want to update the previous Records";
           }
            
           $success['success'] = true;
           $success['message'] =$msg;
//			return response()->json($success, $this->successStatus);
           return $this->ResponseMethod->get_http_response($this->okStatus,$success,$this->ok_response);
			
        } else {
//             $error['success'] = false;
             $error['message'] = 'Access Token you are given is invalid!';
//             return response()->json($error, $this->successStatus);  
             return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
        }
       }else{
//          $error['success'] = false;
          $error['message'] = 'Access Token or NodalId You are given is invalid!';
//          return response()->json($error, $this->successStatus);
          return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
       }
    }
	
	 public function notificationlist(Request $request){ 
        $validator = Validator::make($request->all(),[
            'accessToken' => 'required',
            'nodalid' => 'required|numeric',
        ]);

        if($validator->fails()){
//            return response()->json(['success' => false, 'error' => "Invalid data provided!"], 200);
            return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors(), $this->bad_response);
        }

        $inputs = $request->all();
        $accessToken = trim($inputs['accessToken']);
        $id = trim($inputs['nodalid']);
       
       $newuser = NodalUser::where([['remember_token', '=', $accessToken]])->where('authority_id', '=',$id)->where('role_id',4)->first();
        if(isset($newuser)){
        if($newuser->remember_token == $accessToken)
        {
            $notification = DB::table('notifications')->where('authority_login_id', '=', $id)
                            ->whereNull('deleted_at')
                            ->get();
			
            if(count($notification)>0){
                foreach($notification as $notifi){
					$success = false;
                    $noti[] = array("title"=>$notifi->title,"msg_data"=>$notifi->text,"created"=>$notifi->created_at);
                }
            }else{
				$success = false;
                $noti = array();
            }
			
           $success['success'] = $success;
           $success['name'] =$newuser->name;
           $success['Candidatedata'] =$noti;
//			return response()->json($success, $this->successStatus);
           return $this->ResponseMethod->get_http_response($this->okStatus,$success,$this->ok_response);
			
        } else {
//             $error['success'] = false;
             $error['message'] = 'Access Token you are given is invalid!';
//             return response()->json($error, $this->successStatus);  
             return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
        }
       }else{
//          $error['success'] = false;
          $error['message'] = 'Access Token or NodalId You are given is invalid!';
//          return response()->json($error, $this->successStatus);
          return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
       }
    }

    public function clearnotificationlist(Request $request){
        $validator = Validator::make($request->all(),[
            'accessToken' => 'required',
            'nodalid' => 'required|numeric',
        ]);

        if($validator->fails()){
//            return response()->json(['success' => false, 'error' => "Invalid data provided!"], 200);
            return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors(), $this->bad_response);
        }

        $inputs = $request->all();
        $accessToken = trim($inputs['accessToken']);
        $id = trim($inputs['nodalid']);
       
       $newuser = NodalUser::where([['remember_token', '=', $accessToken]])->where('authority_id', '=',$id)->where('role_id',4)->first();
        if(isset($newuser)){
        if($newuser->remember_token == $accessToken)
        {
           $now = Carbon::now();
		   $update = DB::table('notifications')->where('authority_login_id', '=', $id)
                            ->whereNull('deleted_at')
                            ->get();
            if(count($update) > 0){
				DB::table('notifications')->where('authority_login_id', '=',$id)->whereNull('deleted_at')->update(array('deleted_at' => $now ));
				$succes = true;
				$msg = "notification cleared !";
			}else{
				$succes = false;
				$msg = "nothing to cleared !";
			}
//           $success['success'] = $succes;
           $success['name'] =$newuser->name;
           $success['message'] = $msg;
//			     return response()->json($success, $this->successStatus);
           return $this->ResponseMethod->get_http_response($this->okStatus,$success,$this->ok_response);
			
        } else {
//             $error['success'] = false;
             $error['message'] = 'Access Token you are given is invalid!';
//             return response()->json($error, $this->successStatus);  
             return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
        }
       }else{
//          $error['success'] = false;
          $error['message'] = 'Access Token or NodalId You are given is invalid!';
//          return response()->json($error, $this->successStatus);
          return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response); 
       }
    }

    public function otp_attempt($userid,$attempt_value)
    {
        NodalUser::where('id', $userid)->update(array('OTP_attempt' => $attempt_value));
    }
}