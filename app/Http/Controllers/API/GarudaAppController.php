<?php

namespace App\Http\Controllers\API;

use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Controller;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Validator;
use App\User;
Use DB;
use Image;

class GarudaAppController extends Controller
{	
	public $secret = 'ECI@Booth825';
    public $successStatus = 200;
	public $UnsuccessStatus = 204;
	public $passStatus = 1;
	public $failsStatus = 0;
	public $sizeimage = '10000000';
	public $createdStatus = 201;
    public $nocontentStatus = 204;
    public $notmodifiedStatus = 304;
    public $badrequestStatus = 400;
    public $unauthorizedStatus = 401;
    public $notfoundStatus = 404;
    public $intservererrorStatus = 500;
	
	public function __construct(Request $request=null)
    {	
		$header   =   $request->headers->all();
		if(empty($header['secret'][0])){
			print_r(json_encode(array('code'=>$this->unauthorizedStatus, 'status'=>$this->failsStatus, 'message'=>'Unauthorized access not allowed!')));
			die;
		} else {
		$secret   =   $header['secret'][0];
		if($this->secret!=$secret){ 
			print_r(json_encode(array('code'=>$this->unauthorizedStatus, 'status'=>$this->failsStatus, 'message'=>'Unauthorized access not allowed!')));
			die;
			}
		}
    }
	
    public function validate_api(Request $request)
	{	
	header('Content-Type: application/json');
	try{	
	//header('content-type:text/html;charset=utf-8');
	$input=$request->All();
	//echo "<pre>"; print_r($input['data']); die;
	
	$en = $input['data'];
	
	//$en='ZwK09xv0e2J/B+rIBGNlTL5jMglZpgJjNAjOe7bahYD1hLdUELpCq6rEfoeS+rgjMWGdDR88pN2bbSzibcKSJqov/SsozLoHNFYoIoI2vxczx8XohYJkRt4Ve2P9UpooZ7h/jzh06RjMqwHNWEwLU7LMlnaemawmEOyoUCqjp7muKFNrUbuTMUFFUwdrsxPi';	


	//$key='k_1N@u$23aSwI.3T';
	$key='k_1N@u$23aSwI.3T';
	//$iv='l01(@ubs4aSwI.7D';		
	$iv='K01(@ubs4aSwI.7D';		
	$enc_method = 'AES-128-CBC';
	
	/*$str='{
	"path": "GetErollElectorList?st_code=S11&ac_no=1&part_no=2",
	"request": {"USER_ID":"8129415840","PASSWORD":"8129415840"},
	"method":"GET"
	}';*/


	$dec = openssl_decrypt($en, $enc_method, $key, $options=0, $iv);
	$decordata = json_decode($dec);
	//print_r($decordata); die;	

	//echo  $decor->path; die;
	
	//$decordata = json_decode($decordata);
	$path   =  $decordata->path;
	$uid='';
	$pass='';
	if(isset($decordata->request) && (!empty($decordata->request))){
	 $uid    =  $decordata->request->USER_ID;
	 $pass   = $decordata->request->PASSWORD;
	}
	
	$method = $decordata->method;
	$requestData    =  $decordata->request;


	/*echo $decordata->path;
	echo "<br>";
	echo $decordata->request->USER_ID;
	echo "<br>";
	echo $decordata->request->PASSWORD;
	echo "<br>";
	echo $decordata->method;*/
	//echo $path.'<br>'; 

	$method=$decordata->method;
	$exp = explode("?", $path);
	$ddd = explode("&", $exp[1]);
	
	$stc = explode("=", $ddd[0]);
	$st=$stc[1];
	$act = explode("=", $ddd[1]);
	$ac=$act[1];
	$prt = explode("=", $ddd[2]);
	$part=$prt[1];

	//echo $path."-".$method."-".$st."-".$ac."-".$part; die;
	

	$inputdd = $st.$ac.$part;
	$keypass = "AAAA1234#123456TESTECIKEY";
	$hashkey = hash('sha512', $inputdd.$keypass);
	$passkey = strtoupper($hashkey);
	$response='';
	
	
	$url = "http://117.239.183.245/blonet_service_sa/api/BLONET/";
	set_time_limit(0);
	//echo $method; die;
	if($method=='POST'){
			$request =$requestData;
			$ch = curl_init($url.$path);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						"pass_key:$passkey"
			));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
			$response = curl_exec($ch);
			curl_close($ch);
			//print_r($response);
	} else { 
			$getUrl= $url.$path;
			//echo $getUrl; die;
			$curl_handle=curl_init();
		    curl_setopt($curl_handle,CURLOPT_URL, $getUrl);
			curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
				"pass_key:$passkey"
			));
			curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
		    curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);			
		    $response = curl_exec($curl_handle);
			//print_r($response); die;
		    curl_close($curl_handle);
	}
	
	//header('Content-Type:text/plain;');	
    //header('Accept-Ranges: bytes');
    //header('Content-Length: ' . mb_strlen($response, 'UTF8'));
	
	
	
 	//$response = openssl_encrypt($response, $enc_method, $key, $options=0, $iv);
	
	$value=array();	
	$val=array();	
	$fnl=array();	
	$datasss = json_decode($response, TRUE);
	//echo "<pre>"; print_r($datasss); die("---ppp");
	if(count($datasss)>0){
				   
				     if(count($datasss)>0){
						 $fnl= $this->removeNullInResultArray($datasss);
					} 
				 	
	}	
	
	//$fnl = json_encode($fnl, TRUE);
	//echo "<pre>"; print_r($fnl); die("---ppp");
	//$fnl = openssl_encrypt($fnl, $enc_method, $key, $options=0, $iv);
	
	return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'Validated result', 'result'=>$fnl]);
	}
	catch (Exception $ex) {
    return response()->json(['code'=>204, 'status' =>10, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
    }
  }	
	
	 	
	
	
	

	
	function role_master(){
		$role = DB::connection('mysql2')->table('role_master') 
		->get(); 
		return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'Role Master', 'result'=>$role]);
	}	
	
	public function pollingstationdata(Request $request)
    {	 	
		try{	
		
				$validator = Validator::make($request->all(), [
				'st_code' => 'required',
                'ac_no' => 'required',
				'ps_no' => 'required'
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required.'
				]); }
				
				$input=$request->All();		
				$sid=$input['st_code'];
				$ac=$input['ac_no'];
				$psno=$input['ps_no'];
				$ps_id=$this->getPsIdBystateACPSNO($sid, $ac, $psno);	
				
				if($ps_id > 0 ){
				$img='';
				$result=array();				
				$state =$this->getState($sid); 
				$acName =$this->getAc($sid, $ac); 				
				$ps = DB::connection('mysql2')->table('polling_station')
				->select('polling_station.id', 'polling_station.ps_name_en', 'polling_station.ps_address', 'polling_station.lattitude', 'polling_station.longitude', 'polling_station_type.ps_type')
				->leftjoin('polling_station_type', 'polling_station_type.id', '=', 'polling_station.ps_type_id')
				->where('polling_station.id', '=', $ps_id)
				->take(1)
				->get();
				 
				
				$psd=array();
				if(isset($ps)){
					$psd= array( 		
					'ps_id'=>$ps[0]->id,	
					'ps_name'=>$ps[0]->ps_name_en,
					'ps_address'=>$ps[0]->ps_address,
					'ps_type'=>$ps[0]->ps_type,
					'lattitude'=>$ps[0]->lattitude,	
					'longitude'=>$ps[0]->longitude
					);
				}				
				if(count($psd)>0){
				$result['ps'] = $this->removeNullInResultArray($psd);
				}
				$blo = DB::connection('mysql2')->table('officer_login')
				->select('full_name', 'mobile_number')
				->where('st_code', '=', $sid)
				->where('ac_no', '=', $ac)
				->where('ps_no', '=', $psno)
				->take(1)
				->get();
				$blod=array();
				if(count($blo)>0){
					$blod= array( 		
					'full_name'=>$blo[0]->full_name,	
					'mobile_number'=>$blo[0]->mobile_number
					);
				}
				 
				$bloarray=array();
				$datam=array();
				if(count($blod)>0){
				 $bloarray= $this->removeNullInResultArray($blod);
				}
				$result['blo']=$bloarray;	
				$data = DB::connection('mysql2')->table('ps_facility_master')
				->select('ps_facility_master.id', 'facility_master.field_name', 'facility_master.title', 'ps_facility_master.image', 'ps_facility_master.approved_status', 'ps_facility_master.reverfication_status', 
				'ps_facility_master.rating', 'ps_facility_master.status', 'ps_facility_master.updated_by', 'ps_facility_master.updated_at', 'ps_facility_master.blo_current_status', 'ps_facility_master.lattitude', 'ps_facility_master.longitude')
				->join('facility_master', 'facility_master.id', '=', 'ps_facility_master.facility_master_id')
				->where('ps_facility_master.ps_id', $ps_id)
				->get();
				
				//echo "<pre>"; print_r($data); die;
				
				$myarray=array();
				$myarray2=array();
				if(count($data)>0){
					$datam = $this->removeNullInResult($data);
				}
				$ps_array_main=array();
				$ps_array_main_old=array();
				if(count($datam) > 0 ){
				  foreach($datam as $key=>$val){  
					$photo="";
					$thumb="";
					if($val['image']!=''){
					$photo=$this->imageUrl('thumbs', $sid, $ac).$val['image'];
					$thumb=$this->imageUrlThumb('thumbs', $sid, $ac).str_replace("img", "thumb", $val['image']);
					} else {
					$photo=$this->NoImage();
					$thumb=$this->NoImage();
					}
					$ps_array_main_old[]= array( 
							'id'=>$val['id'],
							'name'=>$val['field_name'],
							'title'=>$val['title'],
							'image'=>$photo,
							'thumb'=>$thumb,
							'status'=>$val['status'],	
							'lattitude'=>$val['lattitude'],	
							'longitude'=>$val['longitude']
					);	
				  }
				}
			$result['Amf_Emf'] = $ps_array_main_old;
			return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'EMF AMF Details', 'result'=>$result]);		
			} else {
			 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Polling station not found', 'Success'=>[]]);
		}		
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
	
	function getPsIdBystateACPSNO($sid, $ac, $psno){
		
		$data =   DB::connection('mysql2')->table('polling_station')->select('id')
		->where('st_code', '=', $sid)
		->where('ac_no', '=', $ac)
		->where('ps_no', '=', $psno)
		->value('id'); 
		
		if(!empty($data)){
			return $data;
		} else {
			return 0;
		}
	}
	
	
	function decrypt_token(Request $request){ 
		$header =   $request->headers->all(); 
		if(!isset($header['token'][0])) 
		{	
		 print_r(json_encode(array('code'=>$this->unauthorizedStatus, 'status'=>$this->failsStatus, 'message'=>'Invalid User', 'token'=>'')));
		 die;
		}
        
		$data   =   $header['token'][0]; 
		
		
		$checkdata = DB::connection('mysql2')->table('officer_login')
		->where('token', '=', $data)
		->first();	
		if(empty($checkdata)) 
		{	
		 print_r(json_encode(array('code'=>$this->unauthorizedStatus, 'status'=>$this->failsStatus, 'message'=>'Invalid Token', 'token'=>$data)));
		 die;
		}
		if(!is_numeric($checkdata->booth_officer_id)) 
		{	
		print_r(json_encode(array('code'=>$this->unauthorizedStatus, 'status'=>$this->failsStatus, 'message'=>'Invalid User', 'token'=>$data)));
		 die;
		}
		return $checkdata->booth_officer_id;		
		//echo "<pre>"; print_r($checkdata->booth_officer_id); die;
		



		if(preg_match("/^(.*)::(.*)$/", $data, $regs)) {
		list(, $crypted_token, $enc_iv) = $regs;
		$enc_method = 'AES-128-CBC'; 
		$enc_key = openssl_digest(gethostname() . "|" . ip2long($_SERVER['SERVER_ADDR']), 'SHA256', TRUE);
		$decrypted_token = openssl_decrypt($crypted_token, $enc_method, $enc_key, 0, hex2bin($enc_iv));		
		unset($crypted_token, $enc_method, $enc_key, $enc_iv, $regs);
		
			$check = DB::connection('mysql2')->table('officer_login')
			->where('booth_officer_id', '=', $decrypted_token)
			->where('token', '=', $data)
			->first();		
			
			if(empty($check)) 
			{	
			 print_r(json_encode(array('code'=>$this->unauthorizedStatus, 'status'=>$this->failsStatus, 'message'=>'Invalid Token', 'token'=>$data)));
			 die;
			}
		
			if(!is_numeric($decrypted_token)) 
			{	
			 print_r(json_encode(array('code'=>$this->unauthorizedStatus, 'status'=>$this->failsStatus, 'message'=>'Invalid User', 'token'=>$data)));
			 die;
			}
			return $decrypted_token;		
		} else {
			 print_r(json_encode(array('code'=>$this->unauthorizedStatus, 'status'=>$this->failsStatus, 'message'=>'Invalid User', 'token'=>$data)));
			die;
		}
		
	}
	
	public function login(Request $request){  
			$input = $request->All();
			try{
				$validator = Validator::make($request->all(), [
                'mobile_number' => 'required|digits:10',
				]);         
				if($validator->fails()){  return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Mobile number must be of 10 digits']); } 
				
				$validator = Validator::make($request->all(), [
                'role_id' => 'required|numeric',
				]);   
				
				if($validator->fails()){  return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Numeric role id is required']); }
				if($input['role_id']==1){ //echo $input['mobile_number']; die;
				$data = DB::connection('mysql')->table('polling_station_officer')->where('mobile_number', '=', $input['mobile_number'])->where('role_id', '=',$input['role_id'])->where('is_active', '=', '1')->first();
				} else {
				$data = DB::connection('mysql2')->table('supervisor_master')->where('mobile_number', '=', $input['mobile_number'])->where('status', '=', '1')->first();	
				}
				//echo "<pre>"; print_r($data); die;
				
				
				
				$psidget = 0;
				//return "Testing Speed";	
				
				
				
				if(isset($data)){ 
					 $token = $data->id;
					 $sssss = $data->id;
					 $d = User::find($sssss, ['id']);
                     $token_pass = $d->createToken('MyApp')->accessToken;
					  //echo Auth::user()->id; die;    
					
					 //echo $token; die("Data");
					 

						




					$enc_method = 'AES-128-CBC';
					$enc_key = openssl_digest(gethostname() . "|" . ip2long($_SERVER['SERVER_ADDR']), 'SHA256', TRUE);
					$enc_iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($enc_method));
					$crypted_token = openssl_encrypt($token, $enc_method, $enc_key, 0, $enc_iv) . "::" . bin2hex($enc_iv);
					unset($token, $enc_method, $enc_key, $enc_iv);
					$psPhoto='';
					$checkExist = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=', $data->id)->first();
					if(isset($checkExist->photo_name)){
					$psPhoto = $checkExist->photo_name;
					}
					$psidget =  $this->getPsIdOnLogin($data->st_code, $data->ac_no, $data->ps_no); 
					date_default_timezone_set('Asia/Kolkata'); 


					if(!empty($checkExist->id)){ 

							if($input['role_id']==1){ //echo $data->id; die("PPPsss");
								DB::connection('mysql')->table('polling_station_officer')
								 ->where('id', $data->id)
								->update([
								'api_token'=>$token_pass,
								'is_login'=>1,
								'is_active'=>1,
								'login_time'=>date('y-m-d h:i:s'),
								]);	

								DB::connection('mysql2')->table('officer_login')
								 ->where('booth_officer_id', $data->id)
								->update([
								'booth_officer_id'=>$data->id,
								'role_id'=>$input['role_id'],
								'ps_id'=>$psidget,
								'status'         =>$data->is_active, 
								'deleted'        =>0, 
								'otp_expire'     =>date('Y-m-d H:i:s', time()), 
								'created_at'     =>date('Y-m-d H:i:s', time()), 
								//'token'     	 =>$crypted_token, 
								'token'     	 =>$token_pass, 
								'created_by'     =>$data->created_by, 
								'updated_at'     =>date('Y-m-d H:i:s', time()), 
								'updated_by'     =>$data->id
								]);	
								
							} else {
								DB::connection('mysql2')->table('officer_login')
								 ->where('booth_officer_id', $data->id)
								->update([
								'booth_officer_id'=>$data->id,
								'ps_id'=>$psidget,
								'role_id'=>$input['role_id'],
								'status'         =>$data->status, 
								'deleted'        =>0, 
								'otp_expire'     =>date('Y-m-d H:i:s', time()), 
								'created_at'     =>date('Y-m-d H:i:s', time()), 
								//'token'     	 =>$crypted_token, 
								'token'     	 =>$token_pass, 
								'created_by'     =>$data->created_by, 
								'updated_at'     =>date('Y-m-d H:i:s', time()), 
								'updated_by'     =>$data->id
								]);
							}	
								
								
					} else { //die("PPP3333");
					       if($input['role_id']==1){
						    DB::connection('mysql2')->table('officer_login')->insert([ 
							'booth_officer_id'=>$data->id,
							'ps_id'=>$psidget,
							'role_id'=>$input['role_id'],
							'mobile_number'  =>$data->mobile_number,
							'designation'  	 =>$data->designation,
							'full_name'      =>$data->name,
							'address'  		 =>$data->address,
							'email'  		 =>$data->email,
							'st_code'     	 =>$data->st_code,
							'dist_code'      =>$data->district_no,
							'ac_no'     	 =>$data->ac_no,
							'ps_no'     	 =>$data->ps_no,
							'status'         =>$data->is_active, 
						    'otp_expire'     =>date('Y-m-d H:i:s', time()), 
							'deleted'        =>0, 
							'created_at'     =>date('Y-m-d H:i:s', time()), 
							//'token'     	 =>$crypted_token, 
							'token'     	 =>$token_pass, 
							'created_by'     =>$data->id, 
							'updated_at'     =>date('Y-m-d H:i:s', time()), 
							'updated_by'     =>date('Y-m-d H:i:s', time())
							]); 
						 }	else {
							DB::connection('mysql2')->table('officer_login')->insert([ 
							'booth_officer_id'=>$data->id,
							'ps_id'=>$psidget,
							'role_id'=>$input['role_id'],
							'mobile_number'  =>$data->mobile_number,
							'designation'  	 =>'Supervisor',
							'full_name'      =>$data->full_name,
							'address'  		 =>$data->address,
							'email'  		 =>$data->email_id,
							'st_code'     	 =>$data->st_code,
							'dist_code'      =>$data->dist_code,
							'ac_no'     	 =>$data->ac_no,
							'ps_no'     	 =>$data->ps_no,
							'photo_name'     	 =>$data->photo_name,
							'status'         =>$data->status, 
						    'otp_expire'     =>date('Y-m-d H:i:s', time()), 
							'deleted'        =>0, 
							'created_at'     =>date('Y-m-d H:i:s', time()), 
							//'token'     	 =>$crypted_token, 
							'token'     	 =>$token_pass, 
							'created_by'     =>$data->id, 
							'updated_at'     =>date('Y-m-d H:i:s', time()), 
							'updated_by'     =>date('Y-m-d H:i:s', time())
							]);  
						 }
					}	
					$this->sendOtp($data->mobile_number, $data->id);
					return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'token' => $token_pass, 'user_id'=>$data->id, 'booth_net_token'=>$token_pass,
					'role_id'=>$input['role_id'], 'message'=>'OTP has been send to registered mobile number please enter to verify mobile number.']);
           }else{
                return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'No record found1']);
            }
        } catch (Exception $ex) {
            return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
        }
   }
   
   
   
   public function resendotp(Request $request){
		
		$input = $request->All();
		$uId = $this->decrypt_token($request);
		$validator = Validator::make($request->all(), ['mobile_number' => 'required|digits:10',]);         
		if($validator->fails()){  return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Mobile number must be of 10 digits']); } 
		
		$this->sendOtp($input['mobile_number'], $uId);
		
		return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true,  
		'message'=>'OTP has been send to registered mobile number please enter to verify mobile number.']);
          
   }
   
    public function amfandemfview(Request $request)
    {	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){
			
				$ps_id=$this->getPsId($uId);
				if(empty($ps_id)){
					return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>"Officer's polling station not found" ]);
				}
				$result=array();
				$getData = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)
				->get();	
				
				$result=array();
				$state =$this->getState($getData[0]->st_code); 
				$distName =$this->getDist($getData[0]->st_code, $getData[0]->dist_no);
				$acName =$this->getAc($getData[0]->st_code, $getData[0]->ac_no); 
				
				$amf_details = DB::connection('mysql2')->table('ps_facility_master')
				->select('ps_facility_master.id', 'facility_master.field_name', 'facility_master.title', 'ps_facility_master.image', 'ps_facility_master.approved_status', 'ps_facility_master.reverfication_status', 
				'ps_facility_master.rating', 'ps_facility_master.status', 'ps_facility_master.updated_by', 'officer_login.full_name as updated_by_name', 'ps_facility_master.updated_at', 'ps_facility_master.blo_current_status')
				->join('facility_master', 'facility_master.id', '=', 'ps_facility_master.facility_master_id')
				->leftjoin('officer_login', 'officer_login.booth_officer_id', '=', 'ps_facility_master.updated_by')
				->where('ps_facility_master.ps_id', $ps_id)//862394 $ps_id
				->where('facility_master.type', 'amf')
				->orderBy('ps_facility_master.updated_at', "DESC")
				->get();
				
				//data
				$resultdata=array();
				if(count($amf_details)>0){
				$amf_details = $this->removeNullInResult($amf_details);
				foreach($amf_details as $data){ 
				$photo=$thumb='';	
				if($data['image']!=''){
				$photo=$this->imageUrl('thumbs', 'U05', '1').$data['image'];
				$thumb=$this->imageUrlThumb('thumbs', 'U05', '1').str_replace("img", "thumb", $data['image']);		
				} else {
				$photo=$this->NoImage();
				$thumb=$this->NoImage();
				}
				$resultdata[]= array( 					
				  'id'=>$data['id'],
				  'field_name'=>$data['field_name'],
				  'title'=>$data['title'],
				  'image'=>$photo,
				  'thumbnail'=>$thumb,
				  'approved_status'=>$data['approved_status'],
				  'reverfication_status'=>$data['reverfication_status'],
				  'rating'=>'"'.$data['rating'].'"',
				  'status'=>$data['status'],
				  'updated_by'=>$data['updated_by'],
				  'updated_by_name'=>$data['updated_by_name'],
				  'updated_at'=>$data['updated_at'],
				  'blo_current_status'=>$data['blo_current_status']
				  );
				}
				}

				$result['amf_details']=$resultdata;
				$emf_master = DB::connection('mysql2')->table('ps_facility_master')
				->select('ps_facility_master.id', 'facility_master.field_name', 'facility_master.title', 'ps_facility_master.image', 'ps_facility_master.approved_status', 'ps_facility_master.reverfication_status', 
				'ps_facility_master.rating', 'ps_facility_master.status', 'ps_facility_master.updated_by', 'officer_login.full_name as updated_by_name', 'ps_facility_master.updated_at', 'ps_facility_master.blo_current_status')
				->join('facility_master', 'facility_master.id', '=', 'ps_facility_master.facility_master_id')
				->leftjoin('officer_login', 'officer_login.booth_officer_id', '=', 'ps_facility_master.updated_by')
				->where('ps_facility_master.ps_id', $ps_id)//862394 $ps_id
				->where('facility_master.type', 'emf')
				->orderBy('ps_facility_master.updated_at', "DESC")
				->get();
				
				$resultdataEmf=array();
				if(count($emf_master)>0){
				$emf_master = $this->removeNullInResult($emf_master);
				$photo=$thumb='';	
				foreach($emf_master as $data){ 
				$photo=$thumb='';	
				if($data['image']!=''){
				$photo=$this->imageUrl('thumbs', 'U05', '1').$data['image'];
				$thumb=$this->imageUrlThumb('thumbs', 'U05', '1').str_replace("img", "thumb", $data['image']);		
				} else {
				$photo=$this->NoImage();
				$thumb=$this->NoImage();
				}
				$resultdataEmf[]= array( 					
				  'id'=>$data['id'],
				  'field_name'=>$data['field_name'],
				  'title'=>$data['title'],
				  'image'=>$photo,
				  'thumbnail'=>$thumb,
				  'approved_status'=>$data['approved_status'],
				  'reverfication_status'=>$data['reverfication_status'],
				  'rating'=>'"'.$data['rating'].'"',
				  'status'=>$data['status'],
				  'updated_by'=>$data['updated_by'],
				  'updated_by_name'=>$data['updated_by_name'],
				  'updated_at'=>$data['updated_at'],
				  'blo_current_status'=>$data['blo_current_status']
				  );
				}
				}
				$result['emf_master']=$resultdataEmf;
				
				return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'state' => $state, 'DistName' => $distName, 'Ac' => $acName,
				'message'=>'Polling Station Details', 'result' => $result]);
		 }
	   } catch (Exception $ex) {
       return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
	public function psdetails(Request $request)
    {	
	try{		
			$input=$request->All();				
			$validator = Validator::make($request->all(), [
			'st_code' => 'required',
			'ac_no' => 'required',
			]);      
			
			if($validator->fails()){  return response()->json([
				'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'State code and ac_no required.', 'result'=>[]
			]); }
			
			if(!is_numeric($input['ac_no'])){ 
				return response()->json([
				'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Ac No. should be numeric.', 'result'=>[]]);
			}
		if(!isset($input['ps_no'])){  // if ps id not set
			//	echo $input['st_code'].'-'.$input['ac_no']; die;
			$data2 = DB::connection('mysql2')->table('polling_station')
			->select('polling_station.id','polling_station.part_no','polling_station.part_name', 'polling_station.ps_no', 'polling_station.ps_loc_no', 'polling_station.ps_sr_no', 'polling_station.ps_address','polling_station.ps_building_type_code', 'polling_station.priority_choice','polling_station.ps_name_en', 'polling_station.ps_name_v1', 'polling_station.ps_name_v2','polling_station.ps_category','polling_station.locn_type', 'polling_station.psl_no', 'polling_station.is_located_in_same_polling_area', 'polling_station.is_more_convenient', 'polling_station.ps_building_distance_flag',
			'polling_station.ps_floor_info_flag', 'polling_station.is_ps_across_river', 'polling_station.is_ps_easy_accessible','polling_station.is_ps_has_proper_signage_of_building_name_n_address', 'polling_station.is_google_map_view_available',			
			'polling_station.is_google_satellite_view_available', 'polling_station.is_part_boundry_map_available', 'polling_station.is_building_front_view_available', 
			'polling_station.is_cad_view_available', 'polling_station.is_key_map_view_available'
			)
			->leftjoin('ps_facility_master', 'ps_facility_master.ps_id', '=', 'polling_station.id')
			->where('polling_station.st_code', $input['st_code'])
			->where('polling_station.ac_no', $input['ac_no'])
			->groupBy('polling_station.ps_no')
			->orderBy('polling_station.id', 'asc')
			->get();			
			$datam=0;
			$datamfine=array();
			if(count($data2)>0){
				foreach($data2 as $dataddddd){					
				$data = DB::connection('mysql2')->table('ps_facility_master')
				->select('ps_facility_master.status', 'facility_master.field_name')
				->leftjoin('facility_master', 'facility_master.id', '=', 'ps_facility_master.facility_master_id')
				->where('ps_facility_master.ps_id', $dataddddd->id) //$dataddddd->id 
				->where('ps_facility_master.deleted', '0')
				->get();
				
					$narray=array();
					$finalarray=array();
					foreach($data as $key=>$val){
						if($val->status==1){
						$finalarray[$val->field_name]="1";
						} else {
						$finalarray[$val->field_name]="0";
						}
					}
					$okarraydata=array();
					//echo "<pre>"; print_r($finalarray); die;
					if(isset($finalarray['bldg_quality_condition'])){
						$okarraydata['bldg_quality_condition']=$finalarray['bldg_quality_condition'];
					} else {
						$okarraydata['bldg_quality_condition']="0";
					}
					//echo $okarraydata['bldg_quality_condition']; die;
					if(isset($finalarray['ps_less_than_20_sqmtrs'])){
						$okarraydata['ps_less_than_20_sqmtrs']=$finalarray['ps_less_than_20_sqmtrs'];
					} else {
						$okarraydata['ps_less_than_20_sqmtrs']="0";
					}
					if(isset($finalarray['is_bldg_dangerous'])){
						$okarraydata['is_bldg_dangerous']=$finalarray['is_bldg_dangerous'];
					} else {
						$okarraydata['is_bldg_dangerous']="0";
					}
					if(isset($finalarray['is_govt_bldg'])){
						$okarraydata['is_govt_bldg']=$finalarray['is_govt_bldg'];
					} else {
						$okarraydata['is_govt_bldg']="0";
					}
					if(isset($finalarray['is_religious_inst'])){
						$okarraydata['is_religious_inst']=$finalarray['is_religious_inst'];
					} else {
						$okarraydata['is_religious_inst']="0";
					}
					if(isset($finalarray['is_school_college'])){
						$okarraydata['is_school_college']=$finalarray['is_school_college'];
					} else {
						$okarraydata['is_school_college']="0";
					}
					if(isset($finalarray['is_ground_floor'])){
						$okarraydata['is_ground_floor']=$finalarray['is_ground_floor'];
					} else {
						$okarraydata['is_ground_floor']="0";
					}
					if(isset($finalarray['is_separate_entry_exit'])){
						$okarraydata['is_separate_entry_exit']=$finalarray['is_separate_entry_exit'];
					} else {
						$okarraydata['is_separate_entry_exit']="0";
					}
					if(isset($finalarray['is_pol_party_office_within_200mtr'])){
						$okarraydata['is_pol_party_office_within_200mtr']=$finalarray['is_pol_party_office_within_200mtr'];
					} else {
						$okarraydata['is_pol_party_office_within_200mtr']="0";
					}
					if(isset($finalarray['is_electricity_available'])){
						$okarraydata['is_electricity_available']=$finalarray['is_electricity_available'];
					} else {
						$okarraydata['is_electricity_available']="0";
					}
					if(isset($finalarray['is_separate_toilet'])){
						$okarraydata['is_separate_toilet']=$finalarray['is_separate_toilet'];
					} else {
						$okarraydata['is_separate_toilet']="0";
					}
					if(isset($finalarray['is_shelter_available'])){
						$okarraydata['is_shelter_available']=$finalarray['is_shelter_available'];
					} else {
						$okarraydata['is_shelter_available']="0";
					}
					if(isset($finalarray['is_proper_road_connectivity'])){
						$okarraydata['is_proper_road_connectivity']=$finalarray['is_proper_road_connectivity'];
					} else {
						$okarraydata['is_proper_road_connectivity']="0";
					}
					if(isset($finalarray['is_any_obstacle_in_way'])){
						$okarraydata['is_any_obstacle_in_way']=$finalarray['is_any_obstacle_in_way'];
					} else {
						$okarraydata['is_any_obstacle_in_way']="0";
					}
					if(isset($finalarray['is_landline_fax_available'])){
						$okarraydata['is_landline_fax_available']=$finalarray['is_landline_fax_available'];
					} else {
						$okarraydata['is_landline_fax_available']="0";
					}
					if(isset($finalarray['mobile_connectivity'])){
						$okarraydata['mobile_connectivity']=$finalarray['mobile_connectivity'];
					} else {
						$okarraydata['mobile_connectivity']="0";
					}
					if(isset($finalarray['internet_facility'])){
						$okarraydata['internet_facility']=$finalarray['internet_facility'];
					} else {
						$okarraydata['internet_facility']="0";
					}
					if(isset($finalarray['is_insurgency_affected'])){
						$okarraydata['is_insurgency_affected']=$finalarray['is_insurgency_affected'];
					} else {
						$okarraydata['is_insurgency_affected']="0";
					}
					if(isset($finalarray['is_forest_area'])){
						$okarraydata['is_forest_area']=$finalarray['is_forest_area'];
					} else {
						$okarraydata['is_forest_area']="0";
					}
					if(isset($finalarray['is_vulnerable_critical_location'])){
						$okarraydata['is_vulnerable_critical_location']=$finalarray['is_vulnerable_critical_location'];
					} else {
						$okarraydata['is_vulnerable_critical_location']="0";
					}
					if(isset($finalarray['permanent_ramp'])){
						$okarraydata['permanent_ramp']=$finalarray['permanent_ramp'];
					} else {
						$okarraydata['permanent_ramp']="0";
					}
					if(isset($finalarray['drinking_water'])){
						$okarraydata['drinking_water']=$finalarray['drinking_water'];
					} else {
						$okarraydata['drinking_water']="0";
					}
					if(isset($finalarray['adequate_furniture'])){
						$okarraydata['adequate_furniture']=$finalarray['adequate_furniture'];
					} else {
						$okarraydata['adequate_furniture']="0";
					}
					if(isset($finalarray['lighting'])){
						$okarraydata['lighting']=$finalarray['lighting'];
					} else {
						$okarraydata['lighting']="0";
					}
					if(isset($finalarray['help_desk'])){
						$okarraydata['help_desk']=$finalarray['help_desk'];
					} else {
						$okarraydata['help_desk']="0";
					}
					if(isset($finalarray['signage'])){
						$okarraydata['signage']=$finalarray['signage'];
					} else {
						$okarraydata['signage']="0";
					}
					if(isset($finalarray['toilet_facility'])){
						$okarraydata['toilet_facility']=$finalarray['toilet_facility'];
					} else {
						$okarraydata['toilet_facility']="0";
					}
				//echo "<pre>"; print_r($okarraydata); 
				$dataddddd=  (array) $dataddddd;	
				$datamfine[]= array_merge($dataddddd, $okarraydata);
			}
		}
	}	
		
		
		if(isset($input['ps_no'])){  // if ps id is set
			//	echo $input['st_code'].'-'.$input['ac_no']; die;
			$data2 = DB::connection('mysql2')->table('polling_station')
			->select('polling_station.id','polling_station.part_no','polling_station.part_name', 'polling_station.ps_no', 'polling_station.ps_loc_no', 'polling_station.ps_sr_no', 'polling_station.ps_address','polling_station.ps_building_type_code', 'polling_station.priority_choice','polling_station.ps_name_en', 'polling_station.ps_name_v1', 'polling_station.ps_name_v2','polling_station.ps_category','polling_station.locn_type', 'polling_station.psl_no', 'polling_station.is_located_in_same_polling_area', 'polling_station.is_more_convenient', 'polling_station.ps_building_distance_flag',
			'polling_station.ps_floor_info_flag', 'polling_station.is_ps_across_river', 'polling_station.is_ps_easy_accessible',     			
			'polling_station.is_ps_has_proper_signage_of_building_name_n_address', 'polling_station.is_google_map_view_available',			
			'polling_station.is_google_satellite_view_available', 'polling_station.is_part_boundry_map_available', 'polling_station.is_building_front_view_available', 
			'polling_station.is_cad_view_available', 'polling_station.is_key_map_view_available'
			)
			->leftjoin('ps_facility_master', 'ps_facility_master.ps_id', '=', 'polling_station.id')
			->where('polling_station.st_code', $input['st_code'])
			->where('polling_station.ac_no', $input['ac_no'])
			->where('polling_station.ps_no', $input['ps_no'])
			->groupBy('polling_station.ps_no')
			->orderBy('polling_station.id', 'asc')
			->get();		
			
			$datam=0;
			$datamfine=array();
			if(count($data2)>0){
				foreach($data2 as $dataddddd){ 	
				$data = DB::connection('mysql2')->table('ps_facility_master')
				->select('ps_facility_master.status', 'facility_master.field_name')
				->leftjoin('facility_master', 'facility_master.id', '=', 'ps_facility_master.facility_master_id')
				->where('ps_facility_master.ps_id', $dataddddd->id) //$dataddddd->id 
				->where('ps_facility_master.deleted', '0')
				->get();
				
					$narray=array();
					$finalarray=array();				
					foreach($data as $key=>$val){
						if($val->status==1){
						$finalarray[$val->field_name]="1";
						} else {
						$finalarray[$val->field_name]="0";
						}
					}
				
					$okarraydata=array();
				
					//echo "<pre>"; print_r($finalarray); die;
					if(isset($finalarray['bldg_quality_condition'])){
						$okarraydata['bldg_quality_condition']=$finalarray['bldg_quality_condition'];
					} else {
						$okarraydata['bldg_quality_condition']="0";
					}
					//echo $okarraydata['bldg_quality_condition']; die;
					if(isset($finalarray['ps_less_than_20_sqmtrs'])){
						$okarraydata['ps_less_than_20_sqmtrs']=$finalarray['ps_less_than_20_sqmtrs'];
					} else {
						$okarraydata['ps_less_than_20_sqmtrs']="0";
					}
					if(isset($finalarray['is_bldg_dangerous'])){
						$okarraydata['is_bldg_dangerous']=$finalarray['is_bldg_dangerous'];
					} else {
						$okarraydata['is_bldg_dangerous']="0";
					}
					if(isset($finalarray['is_govt_bldg'])){
						$okarraydata['is_govt_bldg']=$finalarray['is_govt_bldg'];
					} else {
						$okarraydata['is_govt_bldg']="0";
					}
					if(isset($finalarray['is_religious_inst'])){
						$okarraydata['is_religious_inst']=$finalarray['is_religious_inst'];
					} else {
						$okarraydata['is_religious_inst']="0";
					}
					if(isset($finalarray['is_school_college'])){
						$okarraydata['is_school_college']=$finalarray['is_school_college'];
					} else {
						$okarraydata['is_school_college']="0";
					}
					if(isset($finalarray['is_ground_floor'])){
						$okarraydata['is_ground_floor']=$finalarray['is_ground_floor'];
					} else {
						$okarraydata['is_ground_floor']="0";
					}
					if(isset($finalarray['is_separate_entry_exit'])){
						$okarraydata['is_separate_entry_exit']=$finalarray['is_separate_entry_exit'];
					} else {
						$okarraydata['is_separate_entry_exit']="0";
					}
					if(isset($finalarray['is_pol_party_office_within_200mtr'])){
						$okarraydata['is_pol_party_office_within_200mtr']=$finalarray['is_pol_party_office_within_200mtr'];
					} else {
						$okarraydata['is_pol_party_office_within_200mtr']="0";
					}
					if(isset($finalarray['is_electricity_available'])){
						$okarraydata['is_electricity_available']=$finalarray['is_electricity_available'];
					} else {
						$okarraydata['is_electricity_available']="0";
					}
					if(isset($finalarray['is_separate_toilet'])){
						$okarraydata['is_separate_toilet']=$finalarray['is_separate_toilet'];
					} else {
						$okarraydata['is_separate_toilet']="0";
					}
					if(isset($finalarray['is_shelter_available'])){
						$okarraydata['is_shelter_available']=$finalarray['is_shelter_available'];
					} else {
						$okarraydata['is_shelter_available']="0";
					}
					if(isset($finalarray['is_proper_road_connectivity'])){
						$okarraydata['is_proper_road_connectivity']=$finalarray['is_proper_road_connectivity'];
					} else {
						$okarraydata['is_proper_road_connectivity']="0";
					}
					if(isset($finalarray['is_any_obstacle_in_way'])){
						$okarraydata['is_any_obstacle_in_way']=$finalarray['is_any_obstacle_in_way'];
					} else {
						$okarraydata['is_any_obstacle_in_way']="0";
					}
					if(isset($finalarray['is_landline_fax_available'])){
						$okarraydata['is_landline_fax_available']=$finalarray['is_landline_fax_available'];
					} else {
						$okarraydata['is_landline_fax_available']="0";
					}
					if(isset($finalarray['mobile_connectivity'])){
						$okarraydata['mobile_connectivity']=$finalarray['mobile_connectivity'];
					} else {
						$okarraydata['mobile_connectivity']="0";
					}
					if(isset($finalarray['internet_facility'])){
						$okarraydata['internet_facility']=$finalarray['internet_facility'];
					} else {
						$okarraydata['internet_facility']="0";
					}
					if(isset($finalarray['is_insurgency_affected'])){
						$okarraydata['is_insurgency_affected']=$finalarray['is_insurgency_affected'];
					} else {
						$okarraydata['is_insurgency_affected']="0";
					}
					if(isset($finalarray['is_forest_area'])){
						$okarraydata['is_forest_area']=$finalarray['is_forest_area'];
					} else {
						$okarraydata['is_forest_area']="0";
					}
					if(isset($finalarray['is_vulnerable_critical_location'])){
						$okarraydata['is_vulnerable_critical_location']=$finalarray['is_vulnerable_critical_location'];
					} else {
						$okarraydata['is_vulnerable_critical_location']="0";
					}
					if(isset($finalarray['permanent_ramp'])){
						$okarraydata['permanent_ramp']=$finalarray['permanent_ramp'];
					} else {
						$okarraydata['permanent_ramp']="0";
					}
					if(isset($finalarray['drinking_water'])){
						$okarraydata['drinking_water']=$finalarray['drinking_water'];
					} else {
						$okarraydata['drinking_water']="0";
					}
					if(isset($finalarray['adequate_furniture'])){
						$okarraydata['adequate_furniture']=$finalarray['adequate_furniture'];
					} else {
						$okarraydata['adequate_furniture']="0";
					}
					if(isset($finalarray['lighting'])){
						$okarraydata['lighting']=$finalarray['lighting'];
					} else {
						$okarraydata['lighting']="0";
					}
					if(isset($finalarray['help_desk'])){
						$okarraydata['help_desk']=$finalarray['help_desk'];
					} else {
						$okarraydata['help_desk']="0";
					}
					if(isset($finalarray['signage'])){
						$okarraydata['signage']=$finalarray['signage'];
					} else {
						$okarraydata['signage']="0";
					}
					if(isset($finalarray['toilet_facility'])){
						$okarraydata['toilet_facility']=$finalarray['toilet_facility'];
					} else {
						$okarraydata['toilet_facility']="0";
					}
				$dataddddd=  (array) $dataddddd;	
				$datamfine[]= array_merge($dataddddd, $okarraydata);
			}
		}
	}	
		
		$datamfine = $this->removeNullInResultArray($datamfine);	
		if(count($datamfine)> 0 ){
			return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'Polling station details',  'result' => $datamfine]);
		} else {
			return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' =>false, 'message'=>'Polling station not found', 'result' =>[] ]);
		}
	}
	catch (Exception $ex) {
    return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
    }
  }	
	
   public function pollingbothdetails(Request $request)
   {	
		try{	
		
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){
			
				$ps_id=$this->getPsId($uId);
				if(empty($ps_id)){
					return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>"Officer's polling station not found" ]);
				}
				
				$result=array();				
				$getData = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)
				->get();					
				$result=array();				
				$state =$this->getState($getData[0]->st_code); 
				$distName =$this->getDist($getData[0]->st_code, $getData[0]->dist_no);
				$acName =$this->getAc($getData[0]->st_code, $getData[0]->ac_no); 				
				$ps_id=$this->getPsId($uId); 
				//$ps_id='862400';
				
				$data = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=',$uId)->first();	
				$datablo = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=',$uId)->first();	
				$psPhoto = DB::connection('mysql2')->table('polling_station')->select('photo_name')->where('id', '=', $ps_id)->get(); 
				$namee = DB::connection('mysql2')->table('polling_station')->select('ps_name_en')->where('id', '=', $ps_id)->value('ps_name_en'); 
				$namer = DB::connection('mysql2')->table('polling_station')->select('ps_name_v2')->where('id', '=', $ps_id)->value('ps_name_v2'); 
				$ps_address = DB::connection('mysql2')->table('polling_station')->select('ps_address')->where('id', '=', $ps_id)->value('ps_address');
				$ps_photosAll = DB::connection('mysql2')->table('ps_photos')->select('name')->where('ps_id', '=', $ps_id)->get();
				
				$psPic='';
				if(!empty($ps_photosAll)){
					foreach($ps_photosAll as $psd){
					$psPic.=$psd->name.',';	
					}
					
				}
				//echo  $datablo->full_name; die;
				//echo "<pre>"; print_r($datablo); die;
				$userPhoto='';				
				$checkExist = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=', $uId)->first();
				
				if(isset($checkExist->photo_name)){
				$userPhoto = $checkExist->photo_name;
				} 
				$sId = $this->SIdByPsId($uId);
				$AcId = $this->AcIdByPsId($uId);  
				
				$photo=$thumb='';
				if($userPhoto!=''){
				$photo=$this->imageUrl('thumbs', $sId, $AcId)."$userPhoto";
				$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $userPhoto);	
				} else {
				$photo=$this->NoImage();
				$thumb=$this->NoImage();
				}
				
				
				$result['basic_info']= array(
				'name'=>"$datablo->full_name",
				'mobile_number'=>"$datablo->mobile_number",
				'email'=>"$datablo->email",
				'designation'=>"$datablo->designation",
				'role_id'=>"$data->role_id",
				//'ps_no'=>$datablo->ps_no",
				'dist_code'=>"$datablo->dist_code",
				'ac_no'=>"$datablo->ac_no",
				'st_code'=>"$datablo->st_code",
				'Useraddress'=>"$datablo->address",
				'Userphoto'=>$photo,
				'thumbnail'=>$thumb
				); 	
				
				$data = DB::connection('mysql2')->table('ps_facility_master')
				->select('ps_facility_master.id', 'facility_master.field_name', 'facility_master.title', 'ps_facility_master.image', 'ps_facility_master.approved_status', 'ps_facility_master.reverfication_status', 
				'ps_facility_master.rating', 'ps_facility_master.status', 'ps_facility_master.updated_by', 'officer_login.full_name as updated_by_name', 'ps_facility_master.updated_at', 'ps_facility_master.blo_current_status')
				->join('facility_master', 'facility_master.id', '=', 'ps_facility_master.facility_master_id')
				->leftjoin('officer_login', 'officer_login.booth_officer_id', '=', 'ps_facility_master.updated_by')
				->where('ps_facility_master.ps_id', $ps_id) // $ps_id
				->orderBy('ps_facility_master.updated_at', "DESC")
				->groupBy('ps_facility_master.id')
				->get();
						
				$ps_array_main=array();
				if(count($data) > 0 ){
				  foreach($data as $key=>$val){ 				
					if($val->image!=''){ // 111
					$photo=$this->imageUrl('thumbs', $sId, $AcId).$val->image;
					$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $val->image);	
					} else {
					$photo=$this->NoImage();
					$thumb=$this->NoImage();
					}					
					$ps_array_main[]= array( 			
					'id'=>$val->id,
					'field_name'=>$val->field_name,
					'title'=>$val->title,
					'image'=>$photo,
					'thumb'=>$thumb,
					'approved_status'=>$val->approved_status,
					'reverfication_status'=>$val->reverfication_status,
					'rating'=>"$val->rating",
					'status'=>"$val->status",	
					'updated_by'=>$val->updated_by,	
					'updated_by_name'=>$val->updated_by_name,	
					'updated_at'=>$val->updated_at,	
					'blo_current_status'=>$val->blo_current_status,	
					);
				  }
				}
				$datam=array();		
				if(count($data)>0){
					$datam = $this->removeNullInResult($ps_array_main);
				}
				
				$result['amf_emf_list'] = $datam;				
				$Polling_Station = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				$polling=array();
				if(count($Polling_Station)>0){
					foreach($Polling_Station as $key=>$val){  
							if($val->photo_name!=''){
							$photo=$this->imageUrl('thumbs', $sId, $AcId).$val->photo_name;
							$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $val->photo_name);		
							} else {
							$photo=$this->NoImage();
							$thumb=$this->NoImage();
							}
							$polling[]= array( 			
							'id'=>$val->id,
							'part_no'=>$val->part_no,
							'part_name'=>$val->part_name,
							'ps_no'=>$val->ps_no,
							'ps_loc_no'=>$val->ps_loc_no,
							'ps_address'=>$val->ps_address,
							'ps_address_v1'=>$val->ps_address_v1,
							//'ps_building_type_code'=>$val->ps_building_type_code,
							'ps_name_en'=>$val->ps_name_en,
							'ps_name_v1'=>$val->ps_name_v1,
							'ps_name_v2'=>$val->ps_name_v2,
							//'ps_category'=>$val->ps_category,
							'photo_name'=>$photo,
							'thumb'=>$thumb,
							'lattitude'=>"$val->lattitude",
							'longitude'=>"$val->longitude",
							);
					  }
					
					$Polling_Station = $this->removeNullInResult($Polling_Station);
					 
				}
				
				$result['Polling_Station']=$polling;
				
				$police_station_info= DB::connection('mysql2')->table('police_station_info')
				->select('id','ps_id','station_name as name','address','officer_name as contact_name','contact_number','photo_name','latitude','longitude')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($police_station_info)>0){
					$police_station_info = $this->removeNullInResult($police_station_info);
					
					foreach($police_station_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $data['photo_name']);			
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  //'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'address'=>$data['address'],
						  'contact_name'=>$data['contact_name'],
						  'contact_number'=>$data['contact_number'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
				
				}
				$result['police_station_info']=$resultdata;
				
				$ps_photos = DB::connection('mysql2')->table('ps_photos')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($ps_photos)>0){
					$ps_photos = $this->removeNullInResult($ps_photos);
					foreach($ps_photos as $data){
						$photo=$thumb='';
						if($data['name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $data['name']);			
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  //'ps_id'=>$data['ps_id'],
						 // 'img_type'=>$data['img_type'],
						  //'status'=>$data['status'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
					}
				}
				$result['ps_photos']=$resultdata;
				$bus_stand_info= DB::connection('mysql2')->table('bus_stand_info')
				->select('id','ps_id','bus_stand_name as name','address','longitude','latitude','photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($bus_stand_info)>0){
					$bus_stand_info = $this->removeNullInResult($bus_stand_info);
					
					
					foreach($bus_stand_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $data['photo_name']);		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  //'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'address'=>$data['address'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
					}
				}
				$result['bus_stand_info']=$resultdata;
				$electors_info = DB::connection('mysql2')->table('electors_info')
				->select('id', 'electors_male','electors_female','electors_other','no_of_pwd_voters')
				->where('ps_id', '=', $ps_id)
				->get();
				
				if(count($electors_info)>0){
					$electors_info = $this->removeNullInResult($electors_info);
				}
				$result['electors_info']=$electors_info;
				
				
				
				$fire_station_info = DB::connection('mysql2')->table('fire_station_info')
				->select('id', 'fire_station_name  as name','contact_name','contact_number', 'address', 'latitude', 'longitude', 'photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($fire_station_info)>0){
					$fire_station_info = $this->removeNullInResult($fire_station_info);
					
					
					foreach($fire_station_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
							$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
							$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $data['photo_name']);
						} else {
							$photo=$this->NoImage();
							$thumb=$this->NoImage();
						}
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  //'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'contact_number'=>$data['contact_number'],
						  'address'=>$data['address'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
					}
				}
				
				$result['fire_station_info']=$resultdata;
				
				$fuel_pump_info = DB::connection('mysql2')->table('fuel_pump_info')
				->select('id', 'fuel_pump_name  as name','contact_name','contact_number', 'address', 'timings', 'fuel_types', 'latitude', 'longitude', 'photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
					if(count($fuel_pump_info)>0){
					$fuel_pump_info = $this->removeNullInResult($fuel_pump_info);
					
					foreach($fuel_pump_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $data['photo_name']);
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						 // 'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'contact_name'=>$data['contact_name'],
						  'contact_number'=>$data['contact_number'],
						  'address'=>$data['address'],
						  'timings'=>$data['timings'],
						  'fuel_types'=>$data['fuel_types'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
					}
				}				
				$result['fuel_pump_info']=$resultdata;
				$hospital_info = DB::connection('mysql2')->table('hospital_info')
				->select('id', 'hospital_name  as name','contact_name','contact_number', 'address', DB::raw('IFNULL(emergency_services, "No") as emergency_services'), 'longitude', 'latitude',  'photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();				
				$resultdata=array();
				if(count($hospital_info)>0){
					$hospital_info = $this->removeNullInResult($hospital_info);					
					foreach($hospital_info as $data){ 					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $data['photo_name']);	
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  //'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'contact_name'=>$data['contact_name'],
						  'contact_number'=>$data['contact_number'],
						  'address'=>$data['address'],
						  'emergency_services'=>$data['emergency_services'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
					}
				}
				$result['hospital_info']=$resultdata;
				$parking_info= DB::connection('mysql2')->table('parking_info')
				->select('id', 'address','longitude','latitude','photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($parking_info)>0){
				$parking_info = $this->removeNullInResult($parking_info);
				
					foreach($parking_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $data['photo_name']);
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  //'ps_id'=>$data['ps_id'],
						  'address'=>$data['address'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
					}
				}
				$result['parking_info']=$resultdata;		
				
				return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'state' => "$state", 'DistName' =>"$distName", 'Ac' => "$acName",'message'=>'Polling Station Details', 'result' => $result]);
		}
	} catch (Exception $ex) {
    return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
    }
  }
  // Completed above change to facailty_master and ps_facility_master, for removing m_amf and m_emf
  /*
  At Monday 16 Decmber 2019, will start below to remove m_amf, m_emf, ps_facility_master join and do join with facailty_master and ps_facility_master	
  ***This comment done at Friday 13 Decmber 2019   
  */
  public function approval_pending(Request $request)
   {	
		try{		
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){
			
				$ps_id=$this->getPsId($uId);
				if(empty($ps_id)){
					return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>"Officer's polling station not found" ]);
				}
				
				$result=array();				
				$getData = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)
				->get();	
				
				$result=array();
				
				$state =$this->getState($getData[0]->st_code); 
				$distName =$this->getDist($getData[0]->st_code, $getData[0]->dist_no);
				$acName =$this->getAc($getData[0]->st_code, $getData[0]->ac_no); 
				
				$ps_id=$this->getPsId($uId); 
				//$ps_id='862400';
				
				$data = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=',$uId)->first();	
				$datablo = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=',$uId)->first();	
				$psPhoto = DB::connection('mysql2')->table('polling_station')->select('photo_name')->where('id', '=', $ps_id)->get(); 
				$namee = DB::connection('mysql2')->table('polling_station')->select('ps_name_en')->where('id', '=', $ps_id)->value('ps_name_en'); 
				$namer = DB::connection('mysql2')->table('polling_station')->select('ps_name_v2')->where('id', '=', $ps_id)->value('ps_name_v2'); 
				$ps_address = DB::connection('mysql2')->table('polling_station')->select('ps_address')->where('id', '=', $ps_id)->value('ps_address');
				$ps_photosAll = DB::connection('mysql2')->table('ps_photos')->select('name')->where('ps_id', '=', $ps_id)->get();
				
				$psPic='';
				if(!empty($ps_photosAll)){
					foreach($ps_photosAll as $psd){
					$psPic.=$psd->name.',';	
					}
					
				}
				//echo  $datablo->full_name; die;
				//echo "<pre>"; print_r($datablo); die;
				$userPhoto='';				
				$checkExist = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=', $uId)->first();				
				if(isset($checkExist->photo_name)){
				$userPhoto = $checkExist->photo_name;
				} 
				$sId = $this->SIdByPsId($uId);
				$AcId = $this->AcIdByPsId($uId);  
				
				$photo=$thumb='';
				if($userPhoto!=''){
				$photo=$this->imageUrl('thumbs', $sId, $AcId)."$userPhoto";
				$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $userPhoto);
				} else {
				$photo=$this->NoImage();
				$thumb=$this->NoImage();
				}
				
				
				$result1['basic_info']= array(
				'name'=>"$datablo->full_name",
				'mobile_number'=>"$datablo->mobile_number",
				'email'=>"$datablo->email",
				'designation'=>"$datablo->designation",
				'role_id'=>"$data->role_id",
				//'ps_no'=>$datablo->ps_no",
				'dist_code'=>"$datablo->dist_code",
				'ac_no'=>"$datablo->ac_no",
				'st_code'=>"$datablo->st_code",
				'Useraddress'=>"$datablo->address",
				'Userphoto'=>$photo,
				'thumbnail'=>$thumb
				); 	
				
				$data = DB::connection('mysql2')->table('ps_facility_master')
				->select('ps_facility_master.id', 'facility_master.field_name', 'facility_master.title', 'ps_facility_master.image', 'ps_facility_master.approved_status', 'ps_facility_master.reverfication_status', 
				'ps_facility_master.rating', 'ps_facility_master.status', 'ps_facility_master.updated_by', 'officer_login.full_name as updated_by_name', 'ps_facility_master.updated_at', 'ps_facility_master.blo_current_status')
				->join('facility_master', 'facility_master.id', '=', 'ps_facility_master.facility_master_id')
				->leftjoin('officer_login', 'officer_login.booth_officer_id', '=', 'ps_facility_master.updated_by')
				->where('ps_facility_master.ps_id', $ps_id) // 
				->where('ps_facility_master.approved_status', '0')
				->where('ps_facility_master.blo_current_status', '1')
				->orderBy('ps_facility_master.updated_at', "DESC")
				->groupBy('ps_facility_master.id')
				->get();				
				
				$datam=array();
				if(count($data)>0){
					$datam = $this->removeNullInResult($data);
				}
				
				$ps_array_main=array();
				if(count($datam) > 0 ){
				  foreach($datam as $key=>$val){ //echo "<pre>"; print_r($val); die;
					$photo="";
					$thumb="";
					if($val['image']!=''){
					$photo=$this->imageUrl('thumbs', $sId, $AcId).$val['image'];
					$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $val['image']);	
					} else {
					$photo=$this->NoImage();
					$thumb=$this->NoImage();
					}
					$rate="";
					
					if(isset($val['rating'])){
						$rate=$val['rating'];
					} else {
						$rate="";
					}
					
					$ps_array_main[]= array( 			
					'id'=>$val['id'],
					'field_name'=>$val['field_name'],
					'title'=>$val['title'],
					'image'=>$photo,
					'thumb'=>$thumb,
					'approved_status'=>$val['approved_status'],
					'reverfication_status'=>$val['reverfication_status'],
					'rating'=>"$rate",
					'status'=>$val['status'],	
					'updated_by'=>$val['updated_by'],	
					'updated_by_name'=>$val['updated_by_name'],	
					'updated_at'=>$val['updated_at'],	
					'blo_current_status'=>$val['blo_current_status'],	
					);
				  }
				}				
				$result= $ps_array_main;
				
				
				$Polling_Station = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				if(count($Polling_Station)>0){
					$Polling_Station = $this->removeNullInResult($Polling_Station);
				}
				
				//$result['Polling_Station']=$Polling_Station;
				
				$police_station_info= DB::connection('mysql2')->table('police_station_info')
				->select('id','ps_id','station_name as name','address','officer_name as contact_name','contact_number','photo_name','latitude','longitude')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($police_station_info)>0){
					$police_station_info = $this->removeNullInResult($police_station_info);					
					foreach($police_station_info as $data){ 					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $data['photo_name']);	
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'address'=>$data['address'],
						  'contact_name'=>$data['contact_name'],
						  'contact_number'=>$data['contact_number'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
				
				}
				//$result['police_station_info']=$resultdata;
				
				$ps_photos = DB::connection('mysql2')->table('ps_photos')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($ps_photos)>0){
					$ps_photos = $this->removeNullInResult($ps_photos);
					
					foreach($ps_photos as $data){ 
					
						$photo=$thumb='';
						if($data['name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $data['name']);	
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'img_type'=>$data['img_type'],
						  'status'=>$data['status'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
					
					
				}
				//$result['ps_photos']=$resultdata;
				
				
				$bus_stand_info= DB::connection('mysql2')->table('bus_stand_info')
				->select('id','ps_id','bus_stand_name as name','address','longitude','latitude','photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($bus_stand_info)>0){
					$bus_stand_info = $this->removeNullInResult($bus_stand_info);
					
					
					foreach($bus_stand_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'address'=>$data['address'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
					
				}
				//$result['bus_stand_info']=$resultdata;
				
				
				
				$electors_info = DB::connection('mysql2')->table('electors_info')
				->select('id','ps_id','electors_male','electors_female','electors_other','no_of_pwd_voters','disability_type','no_of_wheel_chair','no_of_vehicle_req')
				->where('ps_id', '=', $ps_id)
				->get();
				
				if(count($electors_info)>0){
					$electors_info = $this->removeNullInResult($electors_info);
				}
				//$result['electors_info']=$electors_info;
				
				
				
				$fire_station_info = DB::connection('mysql2')->table('fire_station_info')
				->select('id', 'ps_id', 'fire_station_name  as name','contact_name','contact_number', 'address', 'latitude', 'longitude', 'photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($fire_station_info)>0){
					$fire_station_info = $this->removeNullInResult($fire_station_info);
					
					
					foreach($fire_station_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'contact_number'=>$data['contact_number'],
						  'address'=>$data['address'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
				}
				//$result['fire_station_info']=$fire_station_info;
				
				$fuel_pump_info = DB::connection('mysql2')->table('fuel_pump_info')
				->select('id', 'ps_id', 'fuel_pump_name  as name','contact_name','contact_number', 'address', 'timings', 'fuel_types', 'latitude', 'longitude', 'photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
					if(count($fuel_pump_info)>0){
					$fuel_pump_info = $this->removeNullInResult($fuel_pump_info);
					
					foreach($fuel_pump_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'contact_name'=>$data['contact_name'],
						  'contact_number'=>$data['contact_number'],
						  'address'=>$data['address'],
						  'timings'=>$data['timings'],
						  'fuel_types'=>$data['fuel_types'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
					
				}
				//$result['fuel_pump_info']=$resultdata;
				
				$hospital_info = DB::connection('mysql2')->table('hospital_info')
				->select('id', 'ps_id', 'hospital_name  as name','contact_name','contact_number', 'address', DB::raw('IFNULL(emergency_services, "No") as emergency_services'), 'longitude', 'latitude',  'photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($hospital_info)>0){
					$hospital_info = $this->removeNullInResult($hospital_info);
					
					
					foreach($hospital_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'contact_name'=>$data['contact_name'],
						  'contact_number'=>$data['contact_number'],
						  'address'=>$data['address'],
						  'emergency_services'=>$data['emergency_services'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
					
				}
				//$result['hospital_info']=$resultdata;
				
				$parking_info= DB::connection('mysql2')->table('parking_info')
				->select('id', 'ps_id', 'address','longitude','latitude','photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($parking_info)>0){
				$parking_info = $this->removeNullInResult($parking_info);
				
					foreach($parking_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'address'=>$data['address'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
					
				}
				//$result['parking_info']=$resultdata;		
				
				//return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'state' => "$state", 'DistName' =>"$distName", 'Ac' => "$acName",'message'=>'Polling Station Details', 'result' => $result]);
				
				return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true,  'result' => $result]);
		}
	} catch (Exception $ex) {
    return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
    }
  }	
	
	
	
  public function completed_facility(Request $request)
   {	
		try{	
		
		//$data = DB::connection('mysql2')->table('ps_facility_master')->first();	
		//echo "<pre>"; print_r($data); die("Testing");
		
		
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){
				
				
				$ps_id=$this->getPsId($uId);
				if(empty($ps_id)){
					return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>"Officer's polling station not found" ]);
				}
				
				$result=array();				
				$getData = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)
				->get();	
				
				$result=array();
				
				$state =$this->getState($getData[0]->st_code); 
				$distName =$this->getDist($getData[0]->st_code, $getData[0]->dist_no);
				$acName =$this->getAc($getData[0]->st_code, $getData[0]->ac_no); 
				
				$ps_id=$this->getPsId($uId); 
				//$ps_id='862400';
				
				$data = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=', $uId)->first();	
				
				$datablo = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=', $uId)->first();	
				$psPhoto = DB::connection('mysql2')->table('polling_station')->select('photo_name')->where('id', '=', $ps_id)->get(); 
				$namee = DB::connection('mysql2')->table('polling_station')->select('ps_name_en')->where('id', '=', $ps_id)->value('ps_name_en'); 
				$namer = DB::connection('mysql2')->table('polling_station')->select('ps_name_v2')->where('id', '=', $ps_id)->value('ps_name_v2'); 
				$ps_address = DB::connection('mysql2')->table('polling_station')->select('ps_address')->where('id', '=', $ps_id)->value('ps_address');
				$ps_photosAll = DB::connection('mysql2')->table('ps_photos')->select('name')->where('ps_id', '=', $ps_id)->get();
				
				$psPic='';
				if(!empty($ps_photosAll)){
					foreach($ps_photosAll as $psd){
					$psPic.=$psd->name.',';	
					}
					
				}
				//echo  $datablo->full_name; die;
				//echo "<pre>"; print_r($datablo); die;
				$userPhoto='';				
				$checkExist = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=', $uId)->first();
				
				if(isset($checkExist->photo_name)){
				$userPhoto = $checkExist->photo_name;
				} 
				
				$sId = $this->SIdByPsId($uId);
				$AcId = $this->AcIdByPsId($uId);  
				
				$photo=$thumb='';
				if($userPhoto!=''){
				$photo=$this->imageUrl('thumbs', $sId, $AcId)."$userPhoto";
				$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $userPhoto);		
				} else {
				$photo=$this->NoImage();
				$thumb=$this->NoImage();
				}
				
				
				$result1['basic_info']= array(
				'name'=>"$datablo->full_name",
				'mobile_number'=>"$datablo->mobile_number",
				'email'=>"$datablo->email",
				'designation'=>"$datablo->designation",
				'role_id'=>"$data->role_id",
				//'ps_no'=>$datablo->ps_no",
				'dist_code'=>"$datablo->dist_code",
				'ac_no'=>"$datablo->ac_no",
				'st_code'=>"$datablo->st_code",
				'Useraddress'=>"$datablo->address",
				'Userphoto'=>$photo,
				'thumbnail'=>$thumb
				); 	
				
				$data = DB::connection('mysql2')->table('ps_facility_master')
				->select('ps_facility_master.id', 'facility_master.field_name', 'facility_master.title', 'ps_facility_master.image', 'ps_facility_master.approved_status', 'ps_facility_master.reverfication_status', 
				'ps_facility_master.rating', 'ps_facility_master.status', 'ps_facility_master.updated_by', 'officer_login.full_name as updated_by_name', 'ps_facility_master.updated_at', 'ps_facility_master.blo_current_status')
				->join('facility_master', 'facility_master.id', '=', 'ps_facility_master.facility_master_id')
				->leftjoin('officer_login', 'officer_login.booth_officer_id', '=', 'ps_facility_master.updated_by')
				->where('ps_facility_master.ps_id', $ps_id)//862394
				->where('ps_facility_master.blo_current_status', '1')
				->where('ps_facility_master.reverfication_status', '0')
				->orderBy('ps_facility_master.updated_at', "DESC")
				->groupBy('ps_facility_master.id')
				->get();
				
				$datam=array();	
				if(count($data)>0){
					$datam = $this->removeNullInResult($data);
				}
				
				$ps_array_main=array();
				if(count($datam) > 0 ){
				  foreach($datam as $key=>$val){  
					$photo="";
					$thumb="";
					if($val['image']!=''){
					$photo=$this->imageUrl('thumbs', $sId, $AcId).$val['image'];
					$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $val['image']);				
					} else {
					$photo=$this->NoImage();
					$thumb=$this->NoImage();
					}
					$rate="";
					if(isset($val['rating'])){
					$rate=$val['rating'];	
					} else {
					$rate="";	
					}
					
					$ps_array_main[]= array( 			
					'id'=>$val['id'],
					'field_name'=>$val['field_name'],
					'title'=>$val['title'],
					'image'=>$photo,
					'thumb'=>$thumb,
					'approved_status'=>$val['approved_status'],
					'reverfication_status'=>$val['reverfication_status'],
					'rating'=>"$rate",
					'status'=>$val['status'],	
					'updated_by'=>$val['updated_by'],	
					'updated_by_name'=>$val['updated_by_name'],	
					'updated_at'=>$val['updated_at'],	
					'blo_current_status'=>$val['blo_current_status'],	
					);
				  }
				}				
				$result= $ps_array_main;
				
				
				$Polling_Station = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				if(count($Polling_Station)>0){
					$Polling_Station = $this->removeNullInResult($Polling_Station);
				}
				
				//$result['Polling_Station']=$Polling_Station;
				
				$police_station_info= DB::connection('mysql2')->table('police_station_info')
				->select('id','ps_id','station_name as name','address','officer_name as contact_name','contact_number','photo_name','latitude','longitude')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($police_station_info)>0){
					$police_station_info = $this->removeNullInResult($police_station_info);
					
					foreach($police_station_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'address'=>$data['address'],
						  'contact_name'=>$data['contact_name'],
						  'contact_number'=>$data['contact_number'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
				
				}
				//$result['police_station_info']=$resultdata;
				
				$ps_photos = DB::connection('mysql2')->table('ps_photos')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($ps_photos)>0){
					$ps_photos = $this->removeNullInResult($ps_photos);
					
					foreach($ps_photos as $data){ 
					
						$photo=$thumb='';
						if($data['name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['name'];		
						} else { // aaaa
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'img_type'=>$data['img_type'],
						  'status'=>$data['status'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
					}
				}
				//$result['ps_photos']=$resultdata;
				
				
				$bus_stand_info= DB::connection('mysql2')->table('bus_stand_info')
				->select('id','ps_id','bus_stand_name as name','address','longitude','latitude','photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($bus_stand_info)>0){
					$bus_stand_info = $this->removeNullInResult($bus_stand_info);
					
					
					foreach($bus_stand_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'address'=>$data['address'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
					
				}
				//$result['bus_stand_info']=$resultdata;
				
				
				
				$electors_info = DB::connection('mysql2')->table('electors_info')
				->select('id','ps_id','electors_male','electors_female','electors_other','no_of_pwd_voters','disability_type','no_of_wheel_chair','no_of_vehicle_req')
				->where('ps_id', '=', $ps_id)
				->get();
				
				if(count($electors_info)>0){
					$electors_info = $this->removeNullInResult($electors_info);
				}
				//$result['electors_info']=$electors_info;
				
				
				
				$fire_station_info = DB::connection('mysql2')->table('fire_station_info')
				->select('id', 'ps_id', 'fire_station_name  as name','contact_name','contact_number', 'address', 'latitude', 'longitude', 'photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($fire_station_info)>0){
					$fire_station_info = $this->removeNullInResult($fire_station_info);
					
					
					foreach($fire_station_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'contact_number'=>$data['contact_number'],
						  'address'=>$data['address'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
				}
				//$result['fire_station_info']=$fire_station_info;
				
				$fuel_pump_info = DB::connection('mysql2')->table('fuel_pump_info')
				->select('id', 'ps_id', 'fuel_pump_name  as name','contact_name','contact_number', 'address', 'timings', 'fuel_types', 'latitude', 'longitude', 'photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
					if(count($fuel_pump_info)>0){
					$fuel_pump_info = $this->removeNullInResult($fuel_pump_info);
					
					foreach($fuel_pump_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'contact_name'=>$data['contact_name'],
						  'contact_number'=>$data['contact_number'],
						  'address'=>$data['address'],
						  'timings'=>$data['timings'],
						  'fuel_types'=>$data['fuel_types'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
					
				}
				//$result['fuel_pump_info']=$resultdata;
				
				$hospital_info = DB::connection('mysql2')->table('hospital_info')
				->select('id', 'ps_id', 'hospital_name  as name','contact_name','contact_number', 'address', DB::raw('IFNULL(emergency_services, "No") as emergency_services'), 'longitude', 'latitude',  'photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($hospital_info)>0){
					$hospital_info = $this->removeNullInResult($hospital_info);
					
					
					foreach($hospital_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'contact_name'=>$data['contact_name'],
						  'contact_number'=>$data['contact_number'],
						  'address'=>$data['address'],
						  'emergency_services'=>$data['emergency_services'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
					
				}
				//$result['hospital_info']=$resultdata;
				
				$parking_info= DB::connection('mysql2')->table('parking_info')
				->select('id', 'ps_id', 'address','longitude','latitude','photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($parking_info)>0){
				$parking_info = $this->removeNullInResult($parking_info);
				
					foreach($parking_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'address'=>$data['address'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
					
				}
				//$result['parking_info']=$resultdata;		
				
				//return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'state' => "$state", 'DistName' =>"$distName", 'Ac' => "$acName",'message'=>'Polling Station Details', 'result' => $result]);
				
				return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true,  'result' => $result]);
		}
	} catch (Exception $ex) {
    return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
    }
  }	
  public function reverification_required(Request $request)
   {	
		try{	
		
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){
			
				$ps_id=$this->getPsId($uId);
				if(empty($ps_id)){
					return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>"Officer's polling station not found" ]);
				}
				
				$result=array();				
				$getData = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)
				->get();	
				
				$result=array();
				
				$state =$this->getState($getData[0]->st_code); 
				$distName =$this->getDist($getData[0]->st_code, $getData[0]->dist_no);
				$acName =$this->getAc($getData[0]->st_code, $getData[0]->ac_no); 
				
				$ps_id=$this->getPsId($uId); 
				//$ps_id='862400';
				
				$data = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=',$uId)->first();	
				$datablo = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=',$uId)->first();	
				$psPhoto = DB::connection('mysql2')->table('polling_station')->select('photo_name')->where('id', '=', $ps_id)->get(); 
				$namee = DB::connection('mysql2')->table('polling_station')->select('ps_name_en')->where('id', '=', $ps_id)->value('ps_name_en'); 
				$namer = DB::connection('mysql2')->table('polling_station')->select('ps_name_v2')->where('id', '=', $ps_id)->value('ps_name_v2'); 
				$ps_address = DB::connection('mysql2')->table('polling_station')->select('ps_address')->where('id', '=', $ps_id)->value('ps_address');
				$ps_photosAll = DB::connection('mysql2')->table('ps_photos')->select('name')->where('ps_id', '=', $ps_id)->get();
				
				$psPic='';
				if(!empty($ps_photosAll)){
					foreach($ps_photosAll as $psd){
					$psPic.=$psd->name.',';	
					}
					
				}
				//echo  $datablo->full_name; die;
				//echo "<pre>"; print_r($datablo); die;
				$userPhoto='';				
				$checkExist = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=', $uId)->first();
				
				if(isset($checkExist->photo_name)){
				$userPhoto = $checkExist->photo_name;
				} 
				
				$sId = $this->SIdByPsId($uId);
				$AcId = $this->AcIdByPsId($uId); 
				
				$photo=$thumb='';
				if($userPhoto!=''){
				$photo=$this->imageUrl('thumbs', $sId, $AcId)."$userPhoto";
				$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $userPhoto);	
				} else {
				$photo=$this->NoImage();
				$thumb=$this->NoImage();
				}
				
				
				$result1['basic_info']= array(
				'name'=>"$datablo->full_name",
				'mobile_number'=>"$datablo->mobile_number",
				'email'=>"$datablo->email",
				'designation'=>"$datablo->designation",
				'role_id'=>"$data->role_id",
				//'ps_no'=>$datablo->ps_no",
				'dist_code'=>"$datablo->dist_code",
				'ac_no'=>"$datablo->ac_no",
				'st_code'=>"$datablo->st_code",
				'Useraddress'=>"$datablo->address",
				'Userphoto'=>$photo,
				'thumbnail'=>$thumb
				); 
				
				$data = DB::connection('mysql2')->table('ps_facility_master')
				->select('ps_facility_master.id', 'facility_master.field_name', 'facility_master.title', 'ps_facility_master.image', 'ps_facility_master.approved_status', 'ps_facility_master.reverfication_status', 
				'ps_facility_master.rating', 'ps_facility_master.status', 'ps_facility_master.updated_by', 'officer_login.full_name as updated_by_name', 'ps_facility_master.updated_at', 'ps_facility_master.blo_current_status')
				->join('facility_master', 'facility_master.id', '=', 'ps_facility_master.facility_master_id')
				->leftjoin('officer_login', 'officer_login.booth_officer_id', '=', 'ps_facility_master.updated_by')
				->where('ps_facility_master.ps_id', $ps_id)//862394 
				->where('ps_facility_master.reverfication_status', '1')
				->orderBy('ps_facility_master.updated_at', "DESC")
				->groupBy('ps_facility_master.id')
				->get();				
				
				$datam=array();
				if(count($data)>0){
					$datam = $this->removeNullInResult($data);
				}
				//echo "<pre>"; print_r($datam); die;
				$ps_array_main=array();
				if(count($datam) > 0 ){
				  foreach($datam as $key=>$val){  
					$photo="";
					$thumb="";
					if($val['image']!=''){
					$photo=$this->imageUrl('thumbs', $sId, $AcId).$val['image'];
					$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).str_replace("img", "thumb", $val['image']);	
					} else {
					$photo=$this->NoImage();
					$thumb=$this->NoImage();
					}
					
					$rate="";
					if(isset($val['rating'])){
					$rate=$val['rating'];	
					} else {
					$rate="";	
					}
					
					
					$ps_array_main[]= array( 			
					'id'=>$val['id'],
					'field_name'=>$val['field_name'],
					'title'=>$val['title'],
					'image'=>$photo,
					'thumb'=>$thumb,
					'approved_status'=>$val['approved_status'],
					'reverfication_status'=>$val['reverfication_status'],
					'rating'=>"$rate",
					'status'=>$val['status'],	
					'updated_by'=>$val['updated_by'],	
					'updated_by_name'=>$val['updated_by_name'],	
					'updated_at'=>$val['updated_at'],	
					'blo_current_status'=>$val['blo_current_status'],	
					);
				  }
				}				
				$result= $ps_array_main;
				
				
				$Polling_Station = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				if(count($Polling_Station)>0){
					$Polling_Station = $this->removeNullInResult($Polling_Station);
				}
				
				//$result['Polling_Station']=$Polling_Station;
				
				$police_station_info= DB::connection('mysql2')->table('police_station_info')
				->select('id','ps_id','station_name as name','address','officer_name as contact_name','contact_number','photo_name','latitude','longitude')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($police_station_info)>0){
					$police_station_info = $this->removeNullInResult($police_station_info);
					
					foreach($police_station_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'address'=>$data['address'],
						  'contact_name'=>$data['contact_name'],
						  'contact_number'=>$data['contact_number'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
				
				}
				//$result['police_station_info']=$resultdata;
				
				$ps_photos = DB::connection('mysql2')->table('ps_photos')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($ps_photos)>0){
					$ps_photos = $this->removeNullInResult($ps_photos);
					
					foreach($ps_photos as $data){ 
					
						$photo=$thumb='';
						if($data['name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'img_type'=>$data['img_type'],
						  'status'=>$data['status'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
					
					
				}
				//$result['ps_photos']=$resultdata;
				
				
				$bus_stand_info= DB::connection('mysql2')->table('bus_stand_info')
				->select('id','ps_id','bus_stand_name as name','address','longitude','latitude','photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($bus_stand_info)>0){
					$bus_stand_info = $this->removeNullInResult($bus_stand_info);
					
					
					foreach($bus_stand_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'address'=>$data['address'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
					
				}
				//$result['bus_stand_info']=$resultdata;
				
				
				
				$electors_info = DB::connection('mysql2')->table('electors_info')
				->select('id','ps_id','electors_male','electors_female','electors_other','no_of_pwd_voters','disability_type','no_of_wheel_chair','no_of_vehicle_req')
				->where('ps_id', '=', $ps_id)
				->get();
				
				if(count($electors_info)>0){
					$electors_info = $this->removeNullInResult($electors_info);
				}
				//$result['electors_info']=$electors_info;
				
				
				
				$fire_station_info = DB::connection('mysql2')->table('fire_station_info')
				->select('id', 'ps_id', 'fire_station_name  as name','contact_name','contact_number', 'address', 'latitude', 'longitude', 'photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($fire_station_info)>0){
					$fire_station_info = $this->removeNullInResult($fire_station_info);
					
					
					foreach($fire_station_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'contact_number'=>$data['contact_number'],
						  'address'=>$data['address'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
				}
				//$result['fire_station_info']=$fire_station_info;
				
				$fuel_pump_info = DB::connection('mysql2')->table('fuel_pump_info')
				->select('id', 'ps_id', 'fuel_pump_name  as name','contact_name','contact_number', 'address', 'timings', 'fuel_types', 'latitude', 'longitude', 'photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
					if(count($fuel_pump_info)>0){
					$fuel_pump_info = $this->removeNullInResult($fuel_pump_info);
					
					foreach($fuel_pump_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'contact_name'=>$data['contact_name'],
						  'contact_number'=>$data['contact_number'],
						  'address'=>$data['address'],
						  'timings'=>$data['timings'],
						  'fuel_types'=>$data['fuel_types'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
					
				}
				//$result['fuel_pump_info']=$resultdata;
				
				$hospital_info = DB::connection('mysql2')->table('hospital_info')
				->select('id', 'ps_id', 'hospital_name  as name','contact_name','contact_number', 'address', DB::raw('IFNULL(emergency_services, "No") as emergency_services'), 'longitude', 'latitude',  'photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($hospital_info)>0){
					$hospital_info = $this->removeNullInResult($hospital_info);
					
					
					foreach($hospital_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'name'=>$data['name'],
						  'contact_name'=>$data['contact_name'],
						  'contact_number'=>$data['contact_number'],
						  'address'=>$data['address'],
						  'emergency_services'=>$data['emergency_services'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
					
				}
				//$result['hospital_info']=$resultdata;
				
				$parking_info= DB::connection('mysql2')->table('parking_info')
				->select('id', 'ps_id', 'address','longitude','latitude','photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
				if(count($parking_info)>0){
				$parking_info = $this->removeNullInResult($parking_info);
				
					foreach($parking_info as $data){ 
					
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId, $AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$data['photo_name'];		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'address'=>$data['address'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
						  
					}
					
				}
				//$result['parking_info']=$resultdata;		
				
				//return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'state' => "$state", 'DistName' =>"$distName", 'Ac' => "$acName",'message'=>'Polling Station Details', 'result' => $result]);
				
				return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true,  'result' => $result]);
		}
	} catch (Exception $ex) {
    return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
    }
  }	
	
	
  public function sendOtp($mobno, $userId)
    {	//echo $mobno; die("Test");
        $otp = rand(100000, 999999);
	    $otp = '123456';
        $datamob = array('otp'=>$otp);
       DB::connection('mysql2')->table('officer_login')->where([['booth_officer_id', $userId],['mobile_number',$mobno]])->update($datamob);
        $mobile_message = 'Your OTP is ' .$otp. ' for ECI Garuda App. Please enter the OTP to proceed. Do not share this OTP to anyone';
		//$msgstatus = $this->sendmessage($mobno, $mobile_message);
		//$msgstatus = SmsgatewayHelper::gupshup($mobno, $mobile_message);/// It's working and commented to save sms
		//$msgstatus = SmsgatewayHelper::sendOtpSMS($mobile_message, $mobno);
    }
	
	public function verifyotp(Request $request)
    {	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){
				$minutes=10;
				$input=$request->All();				
				$validator = Validator::make($request->all(), [
                'otp' => 'required',
				'uuid_no' => 'required',
				'os_type' => 'required',
				'fcm_id' => 'required',
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'otp, uuid_no, os_type, fcm_id are required fields'
				]); }
				
				$expire =DB::connection('mysql2')->table('officer_login')->select('otp_expire')->where('booth_officer_id', '=', $uId)->value('otp_expire'); 
				
				date_default_timezone_set('Asia/Kolkata'); 
				$to_time = strtotime(date("Y-m-d H:i:s"));
				$from_time = strtotime($expire);
				$interval  = abs($to_time - $from_time);
				$minutes   = round($interval / 60);
				
				
			
				$imei_number='NA'; $sim_no='NA';
				if($input['os_type']=='android'){
						$validator = Validator::make($request->all(), [
						'imei_number' => 'required',
						'sim_no' => 'required',
						]);      
						if($validator->fails()){  return response()->json([
							'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'imei_number, sim_no  are required fields'
						]); }
						$imei_number=$this->validateInput($input['imei_number']); 
						$sim_no=$this->validateInput($input['sim_no']);
				}
				
				if( $minutes <= 3){ 
				$check = DB::connection('mysql2')->table('officer_login')
				->select('*')
				->where('otp', '=', $this->validateInput($input['otp']))
				->where('booth_officer_id', '=', $uId)
				->where('status', '=', 1)
				->first();
				
				//echo "<pre>"; print_r($check); die;
				
				$role = DB::connection('mysql2')->table('officer_login')->select('role_id')->where('booth_officer_id', '=', $uId)->value('role_id'); 
				$photo = DB::connection('mysql2')->table('officer_login')->select('photo_name')->where('booth_officer_id', '=', $uId)->value('photo_name'); 
			
				if( !empty($check)){ 			
				DB::connection('mysql2')->table('officer_login')
				->where('booth_officer_id', $uId)
				->update([
				"otp" =>'',
				"imei_number"=>$imei_number,
				"uuid_no"=>$this->validateInput($input['uuid_no']),
				"sim_no"=>$sim_no,
				"os_type"=>$this->validateInput($input['os_type']),
				"is_login"=>1,
				"fcm_id"=>$this->validateInput($input['fcm_id']),
				"login_time"=> date('Y-m-d H:i:s', time()),	
				]);		
				
				$sId = $this->SIdByPsId($uId);
				$AcId = $this->AcIdByPsId($uId); 
				
				$imagePath =  $this->imageUrl('images', $sId, $AcId); 
				
				$ST_NAME='NA';
				$ST_NAME =   DB::connection('mysql2')->table('m_state')->select('ST_NAME')
				->where('ST_CODE', '=', $sId)
				->value('ST_NAME'); 	
				
				$basic_info = array();
				$ps_id=$this->getPsId($uId); 
				
				$data = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=',$uId)->first();	
				$boothId = DB::connection('mysql')->table('polling_station_officer')->where('id', '=',$data->booth_officer_id)->first();
				
				$datablo = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=',$uId)->first();	
				$psPhoto = DB::connection('mysql2')->table('polling_station')->select('photo_name', 'part_no')->where('id', '=', $ps_id)->get(); 
				$namee = DB::connection('mysql2')->table('polling_station')->select('ps_name_en')->where('id', '=', $ps_id)->value('ps_name_en'); 
				$namer = DB::connection('mysql2')->table('polling_station')->select('ps_name_v2')->where('id', '=', $ps_id)->value('ps_name_v2'); 
				$ps_address = DB::connection('mysql2')->table('polling_station')->select('ps_address')->where('id', '=', $ps_id)->value('ps_address');
				$ps_photosAll = DB::connection('mysql2')->table('ps_photos')->select('name')->where('ps_id', '=', $ps_id)->get();
				
				$Dist_name='NA';
				$Dist_name =   DB::connection('mysql2')->table('m_district')->select('DIST_NAME')
				->where('ST_CODE', '=', $sId)
				->where('DIST_NO', '=', $datablo->dist_code)
				->value('DIST_NAME'); 	
				
				
				$AC_name='NA';
				$AC_name =   DB::connection('mysql2')->table('m_ac')->select('AC_NAME')
				->where('ST_CODE', '=', $sId)
				->where('AC_NO', '=', $datablo->ac_no)
				->value('AC_NAME'); 	
				
				
				$psPic='';
				if(!empty($ps_photosAll)){
					foreach($ps_photosAll as $psd){
					$psPic.=$psd->name.',';	
					}	
				}
				
				$userPhoto='';				
				$checkExist = DB::connection('mysql2')->table('officer_login')->where('booth_officer_id', '=', $data->id)->first();

				if(isset($checkExist->photo_name)){
				$userPhoto = $checkExist->photo_name;
				} 
				
				$photo=$thumb='';
				if($userPhoto!=''){
				$photo=$this->imageUrl('thumbs', $sId, $AcId).$userPhoto;
				$thumb=$this->imageUrlThumb('thumbs', $sId, $AcId).$userPhoto;		
				} else {
				$photo=$this->NoImage();
				$thumb=$this->NoImage();
				}
				$partno="NA";
				if(isset($psPhoto[0]->part_no)){
				$partno=$psPhoto[0]->part_no;	
				}

				$basic_info= array(
				'UserId'=>"$uId",
				'name'=>"$datablo->full_name",
				'mobile_number'=>"$datablo->mobile_number",
				'email'=>"$datablo->email",
				'designation'=>"$datablo->designation",
				'role_id'=>"$data->role_id",
				'ps_no'=>"$datablo->ps_no",
				'part_no'=>"$partno",
				'state_name'=>"$ST_NAME",
				'dist_name'=>"$Dist_name",
				'ac_name'=>"$AC_name",
				'dist_code'=>"$datablo->dist_code",
				'ac_no'=>"$datablo->ac_no",
				'st_code'=>"$datablo->st_code",
				'Useraddress'=>"$datablo->address",
				'Userphoto'=>"$photo",
				'thumbnail'=>"$thumb",
				'psPhoto'=>substr($psPic, 0, -1),
				'ps_name'=>"$namee",
				'ps_name_v2'=>"$namer",
				'ps_address'=>"$ps_address"
				); 	
				
				$b='';
				if($datablo->building_json!=''){
					$b=$this->imageUrl('thumbs', $sId, $AcId)."$datablo->building_json";
				}
				$c='';
				if($datablo->road_json!=''){
					$c=$this->imageUrl('thumbs', $sId, $AcId)."$datablo->road_json";
				}
				$d='';
				if($datablo->part_json!=''){
					$d=$this->imageUrl('thumbs', $sId, $AcId)."$datablo->part_json";
				}
				
				
				$geojson= array(
				'building_url'=>$b,
				'road_url'=>$c,
				'part_url'=>$d
				);
				
				return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true,  
					'user_id' => $uId, 'role_id'=>$role, 'token'=>$check->token, 'message'=>'OTP Matched, Login successlully.', 'booth_net_token'=>$boothId->api_token, 'basic_info'=>$basic_info, 'geojson'=>$geojson]);
				} else {
				 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Otp not matched']);
				 die;
				}				
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Otp verification time have expired. Try Again']);
			}		
		}
	} catch (Exception $ex) {
    return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
    }
    }
  
  
  
 
  
  public function pollingboothupdate(Request $request)
  {	   
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){
			
				$input=$request->All();				
				$validator = Validator::make($request->all(), [
                'part_name' => 'required',
				'ps_name_en' => 'required',
				'ps_category' => 'required',
				'locn_type' => 'required',
				'lattitude' => 'required',
				'longitude' => 'required',
				'photo_name' => 'required',
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'part_name, ps_name_en, ps_category, locn_type, photo_name, lattitude, longitude  are required fields'
				]); }			
				
				$mapping='';
				if(isset($input['mapping'])){
				$mapping=$this->validateInput($input['mapping']);
				}
				$mapping_file='';
				if(isset($input['mapping_file'])){
				$mapping_file=$this->validateInput($input['mapping_file']);
				}

				
				$ps_id=$this->getPsId($uId); 
				if( !empty($ps_id)){				
				$update = DB::connection('mysql2')->table('polling_station')
				->where('id', $ps_id)
				->update([
				"part_name"=>$this->validateInput($input['part_name']),
				"ps_name_en"=>$this->validateInput($input['ps_name_en']),
				"ps_category"=>$this->validateInput($input['ps_category']),
				"locn_type"=>$this->validateInput($input['locn_type']),
				"mapping"=>$mapping,
				"mapping_file"=>$mapping_file,
				"lattitude"=>$this->validateInput($input['lattitude']),
				"longitude"=>$this->validateInput($input['longitude']),
				"photo_name"=>$this->validateInput($input['photo_name'])
				]);			
					if($update){
						return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true,  
						'ps_id' => $ps_id, 'message'=>'Polling station updated']);
					} else {
						return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,  
						'ps_id' => $ps_id, 'message'=>'Polling station not updated']);
					}	
				} else {
				 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'ps_id' => $ps_id, 'message'=>'Polling station not updated']);
				 die;
				}				
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Otp verification time have expired. Try Again']);
		}
	} catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
  }
  
  public function profileupdate(Request $request)
  { 		 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
			
				$input=$request->All();
				
				$validator = Validator::make($request->all(), [
                'full_name' => 'required',
				'designation' => 'required',
				'address' => 'required',
				'language' => 'required',
				//'photo_name' => 'required',
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required.'
				]); }
				
				$validator = Validator::make($request->all(), [
                'email' => 'required|email',
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Please enter a valid email.'
				]); }
				
				$checkEmail = DB::connection('mysql2')->table('officer_login')
				->select('id')
				->where('email', '=', $this->validateInput($input['email']))
				->where('booth_officer_id', '!=', $uId)
				->get();
				
				if(count($checkEmail) > 0 ){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Email already exists. Please try with unique email'
				]); }
				
				
				
				//if(!isset($input['photo_name'])){ 
				//	return response()->json([
				//	'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Profile photo is required'], $this->UnsuccessStatus );
				//} 
				
						
				
				if( !empty($uId)){	
					$fileName=''; $img='';
					if ($request->hasFile('photo_name')) {	
							
					   $imgsize = $request->file('photo_name')->getSize(); 
					   if($imgsize <= $this->sizeimage) {	
						    
							$sId = $this->SIdByPsId($uId);
							$AcId = $this->AcIdByPsId($uId); 
							$createDirectory=$this->createDirectory($sId,$AcId);
							$img = $this->uploadImageInDirectroy($request->file('photo_name'), $sId, $AcId);
							//echo $uId; die;
							$isUpdate = DB::connection('mysql2')->table('officer_login')
							->where('booth_officer_id', $uId)
							->update([
							"full_name" =>$this->validateInput($input['full_name']),
							"email" =>$this->validateInput($input['email']),
							"designation"=>$this->validateInput($input['designation']),
							"address"=>$this->validateInput($input['address']),
							"language"=>$this->validateInput($input['language']),
							"photo_name"=>$this->imageUrlSave('image', $sId, $AcId).$img,
							"updated_at"=> date('Y-m-d H:i:s', time()),
							"updated_by"=> $uId,
							]); 
							
						// abc	
						if($isUpdate==1){
							return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 
							'full_name'=>$this->validateInput($input['full_name']), 'email'=>$this->validateInput($input['email']),
							'designation'=>$this->validateInput($input['designation']), 'address'=>$this->validateInput($input['address']), 
							'photo_name'=>$this->image('image', $sId, $AcId).$img, 
							'thumbnail'=>$this->thumb('thumbs', $sId, $AcId).$img, 
							'language'=>$this->validateInput($input['language']), 
							'message'=>'Profile updated']);
						} else {
							return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'Profile not updated']);
						}	
					} else {
					return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Please upload image less than 10 mb']);
					}
					
					} else  {	
							$isUpdate = DB::connection('mysql2')->table('officer_login')
							->where('booth_officer_id', $uId)
							->update([
							"full_name" =>$this->validateInput($input['full_name']),
							"email" =>$this->validateInput($input['email']),
							"designation"=>$this->validateInput($input['designation']),
							"address"=>$this->validateInput($input['address']),
							"language"=>$this->validateInput($input['language']),
							"updated_at"=> date('Y-m-d H:i:s', time()),
							"updated_by"=> $uId,
							]); 
						if($isUpdate==1){
							return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 
							'full_name'=>$this->validateInput($input['full_name']), 'email'=>$this->validateInput($input['email']),
							'designation'=>$this->validateInput($input['designation']), 'address'=>$this->validateInput($input['address']), 
							'photo_name'=>'', 
							'thumbnail'=>'', 
							'language'=>$this->validateInput($input['language']), 
							'message'=>'Profile updated']);
						}	
					}	
					
					
				} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Profile not updated']);
				die;
				}				
							
			
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	   }catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
  }
  
	public function psphotoadd(Request $request)
    {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
			
				$input=$request->All();
				
				if(!isset($input['psphoto'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Polling station photo is required']);
				} 
				
				/*if(count($input['psphoto'])==0){
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Polling station photo is required'], $this->UnsuccessStatus );
				}*/
				
				$imgtype='';
				if(isset($input['img_type'])){
					$imgtype=$input['img_type'];
				}
				
				
				$inserted_id=''; $image=''; $thumbnail='';
				$ps_id=$this->getPsId($uId);
				if( !empty($ps_id)){	
						$fileName=''; $img=''; 
						if ($request->hasFile('psphoto')) { 
							
						   $imgsize = $request->file('psphoto')->getSize(); 
						   if($imgsize <= $this->sizeimage) {
							$files = $request->file('psphoto');
							//foreach($request->file('psphoto') as $image)
							//{
								//$photo = $image;
								$photo = $files;
								$img = time().'.'.$photo->getClientOriginalExtension(); 
														
								$sId = $this->SIdByPsId($uId);
								$AcId = $this->AcIdByPsId($uId);  
								$createDirectory=$this->createDirectory($sId,$AcId);
								
								$destinationPath =public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/img');
								$thumb_img = Image::make($photo->getRealPath())->resize(400, 400);
								$thumb_img->orientate();
								$thumb_img->save($destinationPath.'/'.$img,80);			
								
								
								
								$destinationPath = public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/thumb');
								$photo->move($destinationPath, $img);	
								
								
								$image = $thumbnail = '';
								$image = $this->image('thumbs', $sId, $AcId).$img; 
								$thumbnail = $this->thumb('thumbs', $sId, $AcId).$img;
									
								
								$values = array('ps_id' => $ps_id,'name' => $this->imageUrlSave('images', $sId, $AcId).$img, 'img_type'=>$imgtype, 'latitude'=>$input['latitude'], 'longitude'=>$input['longitude'], 'status'=>1, 'deleted'=>0, 'created_at'=>date('Y-m-d H:i:s', time()), 'created_by'=>$uId);
								
								$inserted_id=DB::connection('mysql2')->table('ps_photos')->insertGetId($values);
								
								if($inserted_id){
									 return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'Polling station photo added','id'=>"$inserted_id", 'ps_id'=>$ps_id, 'photo'=>$image, 'thumbnail'=>$thumbnail]);
								} else {
									 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Image not uploaded', 'photo'=>'', 'thumbnail'=>'']);
								}
								
							//}
						} else {
									return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Please upload image less than 10 mb', 'photo'=>'', 'thumbnail'=>'']);
								}
						
						} else {
									return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Image not found', 'photo'=>'', 'thumbnail'=>'']);
								}
				     
				} else {
				 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Polling station not updated', 'photo'=>'', 'thumbnail'=>'']);
				 die;
				}				
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
   }
   
   
    public function psphotoupdate(Request $request)
    {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
				$input=$request->All();
				
			//	echo "<pre>"; print_r($input); die;
				
				if(!isset($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Polling station photo Id required']);
				}
				
				if(!is_numeric($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Polling station photos Id required']);
				}
				
				if(!isset($input['psphoto'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Polling station photo is required']);
				} 
				$insert_id=''; $image111=''; $thumbnail111='';
				
				$ps_id=$this->getPsId($uId);
				if( !empty($ps_id)){

						$sId = $this->SIdByPsId($uId);
						$AcId = $this->AcIdByPsId($uId); 
					
						$fileName=''; $img=''; $message='';
						if ($request->hasFile('psphoto')) {
						$imgsize = $request->file('psphoto')->getSize(); 
						   if($imgsize <= $this->sizeimage) {	 // Test	
							$img = $this->uploadImageInDirectroyBigSize($request->file('psphoto'), $sId, $AcId);  
								
							$imgtype='';
							if(isset($input['img_type'])){
								$imgtype=$input['img_type'];
							}	
							
							$act='';
							$actins='';	 	$insert_id='';
							if( $input['id'] > 0){		
								$actins = DB::connection('mysql2')->table('ps_photos')
								->where('id', $input['id'])
								->where('ps_id', $ps_id)
								->update([
								"name" =>$this->imageUrlSave('image', $sId, $AcId).$img,
								"img_type" =>$imgtype,
								"latitude"=>$input['latitude'],
								"longitude"=>$input['longitude'],
								"status"=>1,
								"deleted"=>0,
								"updated_by"=> $uId,
								"updated_at"=> date('Y-m-d H:i:s', time())
								]); 
							$message='Polling station photo updated';	
						
							if($actins){
									
							$image111=$thumbnail111='';
							$image111 = $this->imageUrl('thumbs', $sId, $AcId).$img; 
							$thumbnail111 = $this->imageUrlThumb('thumbs', $sId, $AcId).$img;
							
									
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 
								'id'=>$input['id'], 'ps_id'=>$ps_id, 'photo'=>$image111, 'thumbnail'=>$thumbnail111]);	
								} else {
									return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => true, 'message'=>'Polling station photo not updated', 'photo'=>'', 'thumbnail'=>'']);	
								}
							
							
								
							} else {
								$act = DB::connection('mysql2')->table('ps_photos')
								->insertGetId([
								"ps_id"=>$ps_id,
								"name" =>$this->imageUrlSave('image', $sId, $AcId).$img,
								"img_type" =>$imgtype,
								"latitude"=>$input['latitude'],
								"longitude"=>$input['longitude'],
								"status"=>1,
								"deleted"=>0,
								"created_by"=> $uId,
								"created_at"=> date('Y-m-d H:i:s', time())
								]); 
							$message='Polling station photo created';	
								if($act){
									
							$image111=$thumbnail111='';
							$image111 = $this->image('thumbs', $sId, $AcId).$img; 
							$thumbnail111 = $this->thumb('thumbs', $sId, $AcId).$img;
						
									
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 
								'id'=>$act, 'ps_id'=>$ps_id, 'photo'=>$image111, 'thumbnail'=>$thumbnail111]);	
								} else {
									return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => true, 'message'=>'Polling station photo not updated', 'photo'=>'', 'thumbnail'=>'']);	
								}
							}	
				} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Please upload image less than 10 mb', 'photo'=>'', 'thumbnail'=>'']);
				}			
							
				} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Polling station image not updated']);
				 die;
				}
				
						
				} else {
				 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Polling station not updated']);
				 die;
				}				
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error']);
      }
   }
   
   public function getpsbyac(Request $request){
	   try{	
		$input=$request->All();
		echo "<pre>"; print_r($input); die;
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
	   
   }
   
  public function amfupdate(Request $request)
  {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
			
				$input=$request->All();
				
				$validator = Validator::make($request->all(), [
                'permanent_ramp' => 'required',
				'drinking_water' => 'required',
				'adequate_furniture' => 'required',
				'lighting' => 'required',
				'help_desk' => 'required',
				'signage' => 'required',
				'toilet_facility' => 'required',
				'perm_ramp_update_on' => 'required',
				'perm_ramp_update_by' => 'required',
				'drink_water_update_on' => 'required',
				'drink_water_update_by' => 'required',
				'furniture_update_on' => 'required',
				'furniture_update_by' => 'required',
				'lighting_update_on' => 'required',
				'lighting_update_by' => 'required',
				'help_desk_update_on' => 'required',
				'help_desk_update_by' => 'required',
				'signage_update_on' => 'required',
				'signage_update_by' => 'required',		
				'toilet_facility_update_on' => 'required',
				'toilet_facility_update_by' => 'required',
				'perm_ramp_approved_status' => 'required',
				'drink_water_approved_status' => 'required',
				'furniture_approved_status' => 'required',
				'lighting_approved_status' => 'required',
				'help_desk_approved_status' => 'required',
				'signage_approved_status' => 'required',
				'toilet_facility_approved_status' => 'required',
				'perm_ramp_reverfication_status' => 'required',
				'drink_water_reverfication_status' => 'required',
				'furniture_reverfication_status' => 'required',
				'lighting_reverfication_status' => 'required',
				'help_desk_reverfication_status' => 'required',
				'signage_reverfication_status' => 'required',
				'toilet_facility_reverfication_status' => 'required',
					
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required.'
				]); }
				
				if(!isset($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'AMF id not found'], $this->UnsuccessStatus );
				}
				
				if(!is_numeric($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'AMF id not found'], $this->UnsuccessStatus );
				}
				
				if(!isset($input['photo_name'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'AMF photo not found'], $this->UnsuccessStatus );
				} 
				
				
				$ps_id=$this->getPsId($uId);
				if( isset($input['permanent_ramp'])){	
						$fileName=''; $img=''; $message='';
						if ($request->hasFile('photo_name')) {

								$sId = $this->SIdByPsId($uId);
								$AcId = $this->AcIdByPsId($uId); 
								$img = $this->uploadImageInDirectroy($request->file('photo_name'), $sId,$AcId); 
								
								
								
							if( $input['id'] > 0 ){	
							
								$isUpdate = DB::connection('mysql2')->table('amf_master')
								->where('id', $input['id'])
								->update([
								"permanent_ramp" =>$this->validateInput($input['permanent_ramp']),
								"drinking_water" =>$this->validateInput($input['drinking_water']),
								"adequate_furniture"=>$this->validateInput($input['adequate_furniture']),
								"lighting"=>$this->validateInput($input['lighting']),
								"help_desk"=>$this->validateInput($input['help_desk']),
								"signage"=>$this->validateInput($input['signage']),
								"toilet_facility"=>$this->validateInput($input['toilet_facility']),
								"photo_name"=> $this->imageUrlSave('image', $sId,$AcId).$img,
								"updated_at"=> date('Y-m-d H:i:s', time()),
								"updated_by"=> $uId,
								"perm_ramp_update_on"=>$this->validateInput($input['perm_ramp_update_on']),
								"perm_ramp_update_by"=>$this->validateInput($input['perm_ramp_update_by']),
								"drink_water_update_on"=>$this->validateInput($input['drink_water_update_on']),
								"drink_water_update_by"=>$this->validateInput($input['drink_water_update_by']),
								"furniture_update_on"=>$this->validateInput($input['furniture_update_on']),
								"furniture_update_by"=>$this->validateInput($input['furniture_update_by']),
								"lighting_update_on"=>$this->validateInput($input['lighting_update_on']),
								"lighting_update_by"=>$this->validateInput($input['lighting_update_by']),
								"help_desk_update_on"=>$this->validateInput($input['help_desk_update_on']),
								"help_desk_update_by"=>$this->validateInput($input['help_desk_update_by']),
								"signage_update_on"=>$this->validateInput($input['signage_update_on']),
								"signage_update_by"=>$this->validateInput($input['signage_update_by']),
								"toilet_facility_update_on"=>$this->validateInput($input['toilet_facility_update_on']),
								"perm_ramp_approved_status"=>$this->validateInput($input['perm_ramp_approved_status']),
								"drink_water_approved_status"=>$this->validateInput($input['drink_water_approved_status']),
								"furniture_approved_status"=>$this->validateInput($input['furniture_approved_status']),
								"lighting_approved_status"=>$this->validateInput($input['lighting_approved_status']),
								"help_desk_approved_status"=>$this->validateInput($input['help_desk_approved_status']),
								"signage_approved_status"=>$this->validateInput($input['signage_approved_status']),
								"toilet_facility_approved_status"=>$this->validateInput($input['toilet_facility_approved_status']),
								"perm_ramp_reverfication_status"=>$this->validateInput($input['perm_ramp_reverfication_status']),
								"drink_water_reverfication_status"=>$this->validateInput($input['drink_water_reverfication_status']),
								"furniture_reverfication_status"=>$this->validateInput($input['furniture_reverfication_status']),
								"lighting_reverfication_status"=>$this->validateInput($input['lighting_reverfication_status']),
								"help_desk_reverfication_status"=>$this->validateInput($input['help_desk_reverfication_status']),
								"signage_reverfication_status"=>$this->validateInput($input['signage_reverfication_status']),
								"toilet_facility_reverfication_status"=>$this->validateInput($input['toilet_facility_reverfication_status']),
								]); 
								
								$message='Amf details updated';
							} else {	
							
								$isUpdate = DB::connection('mysql2')->table('amf_master')
								->insert([
								"ps_id" =>$ps_id,
								"permanent_ramp" =>$this->validateInput($input['permanent_ramp']),
								"drinking_water" =>$this->validateInput($input['drinking_water']),
								"adequate_furniture"=>$this->validateInput($input['adequate_furniture']),
								"lighting"=>$this->validateInput($input['lighting']),
								"help_desk"=>$this->validateInput($input['help_desk']),
								"signage"=>$this->validateInput($input['signage']),
								"toilet_facility"=>$this->validateInput($input['toilet_facility']),
								"photo_name"=> $this->imageUrlSave('image', $sId,$AcId).$img,
								"created_at"=> date('Y-m-d H:i:s', time()),
								"created_by"=> $uId,
								"perm_ramp_update_on"=>$this->validateInput($input['perm_ramp_update_on']),
								"perm_ramp_update_by"=>$this->validateInput($input['perm_ramp_update_by']),
								"drink_water_update_on"=>$this->validateInput($input['drink_water_update_on']),
								"drink_water_update_by"=>$this->validateInput($input['drink_water_update_by']),
								"furniture_update_on"=>$this->validateInput($input['furniture_update_on']),
								"furniture_update_by"=>$this->validateInput($input['furniture_update_by']),
								"lighting_update_on"=>$this->validateInput($input['lighting_update_on']),
								"lighting_update_by"=>$this->validateInput($input['lighting_update_by']),
								"help_desk_update_on"=>$this->validateInput($input['help_desk_update_on']),
								"help_desk_update_by"=>$this->validateInput($input['help_desk_update_by']),
								"signage_update_on"=>$this->validateInput($input['signage_update_on']),
								"signage_update_by"=>$this->validateInput($input['signage_update_by']),
								"toilet_facility_update_on"=>$this->validateInput($input['toilet_facility_update_on']),
								"toilet_facility_update_by"=>$this->validateInput($input['toilet_facility_update_by']),
								"perm_ramp_approved_status"=>$this->validateInput($input['perm_ramp_approved_status']),
								"drink_water_approved_status"=>$this->validateInput($input['drink_water_approved_status']),
								"furniture_approved_status"=>$this->validateInput($input['furniture_approved_status']),
								"lighting_approved_status"=>$this->validateInput($input['lighting_approved_status']),
								"help_desk_approved_status"=>$this->validateInput($input['help_desk_approved_status']),
								"signage_approved_status"=>$this->validateInput($input['signage_approved_status']),
								"toilet_facility_approved_status"=>$this->validateInput($input['toilet_facility_approved_status']),
								"perm_ramp_reverfication_status"=>$this->validateInput($input['perm_ramp_reverfication_status']),
								"drink_water_reverfication_status"=>$this->validateInput($input['drink_water_reverfication_status']),
								"furniture_reverfication_status"=>$this->validateInput($input['furniture_reverfication_status']),
								"lighting_reverfication_status"=>$this->validateInput($input['lighting_reverfication_status']),
								"help_desk_reverfication_status"=>$this->validateInput($input['help_desk_reverfication_status']),
								"signage_reverfication_status"=>$this->validateInput($input['signage_reverfication_status']),
								"toilet_facility_reverfication_status"=>$this->validateInput($input['toilet_facility_reverfication_status']),
								]); 								
								$message='Amf details created';
							}
							
							if($isUpdate==1){
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message]);
							} else {
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'Amf details not updated']);
							}	
						} else {
						 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Amf details  not updated']);
						}		
						
				} else {
				 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Amf details  not updated']);
				 die;
				}				
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
  }
   
   public function emfupdate(Request $request)
   {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
			
				$input=$request->All();				
				$validator = Validator::make($request->all(), [
                'bldg_quality_condition' => 'required',
				'ps_less_than_20_sqmtrs' => 'required',
				'is_bldg_dangerous' => 'required',
				'is_govt_bldg' => 'required',
				'is_religious_inst' => 'required',
				'is_school_college' => 'required',
				'is_ground_floor' => 'required',
				'is_separate_entry_exit' => 'required',
				'is_pol_party_office_within_200mtr' => 'required',
				'is_electricity_available' => 'required',
				'is_separate_toilet' => 'required',
				'is_shelter_available' => 'required',
				'is_proper_road_connectivity' => 'required',
				'is_any_obstacle_in_way' => 'required',
				'is_landline_fax_available' => 'required',
				'mobile_connectivity' => 'required',
				'is_insurgency_affected' => 'required',
				'is_forest_area' => 'required',
				'is_vulnerable_critical_location' => 'required',
				'is_sensitive_area' => 'required',
				'bldg_cond_update_on' => 'required',
				'bldg_cond_update_by' => 'required',
				'ps_distance_update_on' => 'required',
				'ps_distance_update_by' => 'required',
				'is_dangerous_update_on' => 'required',
				'is_dangerous_update_by' => 'required',
				'is_govt_bldg_update_on' => 'required',
				'is_govt_bldg_update_on' => 'required',
				'religious_inst_update_on' => 'required',
				'religious_inst_update_by' => 'required',
				'is_school_update_on' => 'required',
				'is_school_update_by' => 'required',
				'ground_floor_update_on' => 'required',
				'ground_floor_update_by' => 'required',
				'separate_entry_update_on' => 'required',
				'separate_entry_update_by' => 'required',
				'party_office_update_on' => 'required',
				'party_office_update_by' => 'required',
				'electricity_available_update_on' => 'required',
				'electricity_available_update_by' => 'required',
				'separate_toilet_update_on' => 'required',
				'separate_toilet_update_by' => 'required',
				'shelter_option_update_on' => 'required',
				'shelter_option_update_by' => 'required',
				'road_option_update_on' => 'required',
				'road_option_update_by' => 'required',
				'any_obstacle_update_on' => 'required',
				'any_obstacle_update_by' => 'required',
				'fax_status_update_on' => 'required',
				'fax_status_update_by' => 'required',
				'mobile_connectivity_update_on' => 'required',
				'mobile_connectivity_update_by' => 'required',
				'internet_facility_update_on' => 'required',
				'internet_facility_update_by' => 'required',
				'insurgency_status_update_on' => 'required',
				'insurgency_status_update_by' => 'required',
				'is_forest_area_update_on' => 'required',
				'is_forest_area_update_by' => 'required',
				'vulnerable_status_update_on' => 'required',
				'vulnerable_status_update_by' => 'required',
				'sensitive_area_update_on' => 'required',
				'sensitive_area_update_by' => 'required',
				'bldg_cond_approved_status' => 'required',
				'ps_distance_approved_status' => 'required',
				'is_dangerous_approved_status' => 'required',
				'is_govt_approved_status' => 'required',
				'religious_inst_approved_status' => 'required',
				'is_school_approved_status' => 'required',
				'ground_floor_approved_status' => 'required',
				'separate_entry_approved_status' => 'required',
				'party_office_approved_status' => 'required',
				'electricity_available_approved_status' => 'required',
				'separate_toilet_approved_status' => 'required',
				'shelter_option_approved_status' => 'required',
				'road_option_approved_status' => 'required',
				'any_obstacle_approved_status' => 'required',
				'fax_status_approved_status' => 'required',
				'mobile_connectivity_approved_status' => 'required',
				'internet_facility_approved_status' => 'required',
				'insurgency_status_approved_status' => 'required',
				'is_forest_area_approved_status' => 'required',
				'vulnerable_status_approved_status' => 'required',
				'sensitive_area_approved_status' => 'required',
				'bldg_cond_reverfication_status' => 'required',
				'ps_distance_reverfication_status' => 'required',
				'is_dangerous_reverfication_status' => 'required',
				'is_govt_reverfication_status' => 'required',
				'religiou_inst_reverfication_status' => 'required',
				'is_school_reverfication_status' => 'required',
				'ground_floor_reverfication_status' => 'required',
				'separate_entry_reverfication_status' => 'required',
				'party_office_reverfication_status' => 'required',
				'electricity_available_reverfication_status' => 'required',
				'separate_toilet_reverfication_status' => 'required',
				'shelter_option_reverfication_status' => 'required',
				'road_option_reverfication_status' => 'required',
				'any_obstacle_reverfication_status' => 'required',
				'fax_status_reverfication_status' => 'required',
				'mobile_connectivity_reverfication_status' => 'required',
				'internet_facility_reverfication_status' => 'required',
				'insurgency_status_reverfication_status' => 'required',
				'is_forest_area_reverfication_status' => 'required',
				'vulnerable_status_reverfication_status' => 'required',
				'sensitive_area_reverfication_status' => 'required',
				
				
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required'
				]); }
				
				
				if(!isset($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'EMF id not found'], $this->UnsuccessStatus );
				}
				
				if(!is_numeric($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'EMF id not found'], $this->UnsuccessStatus );
				}
				
				if(!isset($input['photo_name'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'EMF photo not found'], $this->UnsuccessStatus );
				} 
				$ps_id=$this->getPsId($uId);
				$id=$input['id'];
				if( !empty($input['bldg_quality_condition'])){	
						$fileName=''; $img=''; $message='';
						if ($request->hasFile('photo_name')) {	
								
								
								$sId = $this->SIdByPsId($uId);
								$AcId = $this->AcIdByPsId($uId); 
								$img = $this->uploadImageInDirectroy($request->file('photo_name'),$sId,$AcId);
								
								
							if( $input['id'] > 0 ){		
								$isUpdate = DB::connection('mysql2')->table('emf_master')
								->where('id', $id)
								->update([
								"bldg_quality_condition" =>$this->validateInput($input['bldg_quality_condition']),
								"ps_less_than_20_sqmtrs" =>$this->validateInput($input['ps_less_than_20_sqmtrs']),
								"is_bldg_dangerous"=>$this->validateInput($input['is_bldg_dangerous']),
								"is_govt_bldg"=>$this->validateInput($input['is_govt_bldg']),
								"is_religious_inst"=>$this->validateInput($input['is_religious_inst']),
								"is_school_college"=>$this->validateInput($input['is_school_college']),
								"is_ground_floor"=>$this->validateInput($input['is_ground_floor']),
								"is_separate_entry_exit"=>$this->validateInput($input['is_separate_entry_exit']),
								"is_pol_party_office_within_200mtr"=>$this->validateInput($input['is_pol_party_office_within_200mtr']),
								"is_electricity_available"=>$this->validateInput($input['is_electricity_available']),
								"is_separate_toilet"=>$this->validateInput($input['is_separate_toilet']),
								"is_shelter_available"=>$this->validateInput($input['is_shelter_available']),
								"is_proper_road_connectivity"=>$this->validateInput($input['is_proper_road_connectivity']),
								"is_any_obstacle_in_way"=>$this->validateInput($input['is_any_obstacle_in_way']),
								"is_landline_fax_available"=>$this->validateInput($input['is_landline_fax_available']),
								"mobile_connectivity"=>$this->validateInput($input['mobile_connectivity']),
								"is_insurgency_affected"=>$this->validateInput($input['is_insurgency_affected']),
								"is_forest_area"=>$this->validateInput($input['is_forest_area']),
								"is_vulnerable_critical_location"=>$this->validateInput($input['is_vulnerable_critical_location']),
								"is_sensitive_area"=>$this->validateInput($input['is_sensitive_area']),
								"bldg_cond_update_on"=>$this->validateInput($input['bldg_cond_update_on']),
								"bldg_cond_update_by"=>$this->validateInput($input['bldg_cond_update_by']),
								"ps_distance_update_on"=>$this->validateInput($input['ps_distance_update_on']),
								"ps_distance_update_by"=>$this->validateInput($input['ps_distance_update_by']),
								"is_dangerous_update_on"=>$this->validateInput($input['is_dangerous_update_on']),
								"is_dangerous_update_by"=>$this->validateInput($input['is_dangerous_update_by']),
								"is_govt_bldg_update_on"=>$this->validateInput($input['is_govt_bldg_update_on']),
								"is_govt_bldg_update_by"=>$this->validateInput($input['is_govt_bldg_update_by']),
								"religious_inst_update_on"=>$this->validateInput($input['religious_inst_update_on']),
								"religious_inst_update_by"=>$this->validateInput($input['religious_inst_update_by']),
								"is_school_update_on"=>$this->validateInput($input['is_school_update_on']),
								"is_school_update_by"=>$this->validateInput($input['is_school_update_by']),
								"ground_floor_update_on"=>$this->validateInput($input['ground_floor_update_on']),
								"ground_floor_update_by"=>$this->validateInput($input['ground_floor_update_by']),
								"separate_entry_update_on"=>$this->validateInput($input['separate_entry_update_on']),
								"separate_entry_update_by"=>$this->validateInput($input['separate_entry_update_by']),
								"party_office_update_on"=>$this->validateInput($input['party_office_update_on']),
								"party_office_update_by"=>$this->validateInput($input['party_office_update_by']),
								"electricity_available_update_on"=>$this->validateInput($input['electricity_available_update_on']),
								"electricity_available_update_by"=>$this->validateInput($input['electricity_available_update_by']),
								"separate_toilet_update_on"=>$this->validateInput($input['separate_toilet_update_on']),
								"separate_toilet_update_by"=>$this->validateInput($input['separate_toilet_update_by']),
								"shelter_option_update_on"=>$this->validateInput($input['shelter_option_update_on']),
								"shelter_option_update_by"=>$this->validateInput($input['shelter_option_update_by']),
								"road_option_update_on"=>$this->validateInput($input['road_option_update_on']),
								"road_option_update_by"=>$this->validateInput($input['road_option_update_by']),
								"any_obstacle_update_on"=>$this->validateInput($input['any_obstacle_update_on']),
								"any_obstacle_update_by"=>$this->validateInput($input['any_obstacle_update_by']),
								"fax_status_update_on"=>$this->validateInput($input['fax_status_update_on']),
								"fax_status_update_by"=>$this->validateInput($input['fax_status_update_by']),
								"mobile_connectivity_update_on"=>$this->validateInput($input['mobile_connectivity_update_on']),
								"mobile_connectivity_update_by"=>$this->validateInput($input['mobile_connectivity_update_by']),
								"internet_facility_update_on"=>$this->validateInput($input['internet_facility_update_on']),
								"internet_facility_update_by"=>$this->validateInput($input['internet_facility_update_by']),
								"insurgency_status_update_on"=>$this->validateInput($input['insurgency_status_update_on']),
								"insurgency_status_update_by"=>$this->validateInput($input['insurgency_status_update_by']),
								"is_forest_area_update_on"=>$this->validateInput($input['is_forest_area_update_on']),
								"is_forest_area_update_by"=>$this->validateInput($input['is_forest_area_update_by']),
								"vulnerable_status_update_on"=>$this->validateInput($input['vulnerable_status_update_on']),
								"vulnerable_status_update_by"=>$this->validateInput($input['vulnerable_status_update_by']),
								"sensitive_area_update_on"=>$this->validateInput($input['sensitive_area_update_on']),
								"sensitive_area_update_by"=>$this->validateInput($input['sensitive_area_update_by']),
								"bldg_cond_approved_status"=>$this->validateInput($input['bldg_cond_approved_status']),
								"ps_distance_approved_status"=>$this->validateInput($input['ps_distance_approved_status']),
								"is_dangerous_approved_status"=>$this->validateInput($input['is_dangerous_approved_status']),
								"is_govt_approved_status"=>$this->validateInput($input['is_govt_approved_status']),
								"religious_inst_approved_status"=>$this->validateInput($input['religious_inst_approved_status']),
								"is_school_approved_status"=>$this->validateInput($input['is_school_approved_status']),
								"ground_floor_approved_status"=>$this->validateInput($input['ground_floor_approved_status']),
								"separate_entry_approved_status"=>$this->validateInput($input['separate_entry_approved_status']),
								"party_office_approved_status"=>$this->validateInput($input['party_office_approved_status']),
								"electricity_available_approved_status"=>$this->validateInput($input['electricity_available_approved_status']),
								"separate_toilet_approved_status"=>$this->validateInput($input['separate_toilet_approved_status']),
								"shelter_option_approved_status"=>$this->validateInput($input['shelter_option_approved_status']),
								"road_option_approved_status"=>$this->validateInput($input['road_option_approved_status']),
								"any_obstacle_approved_status"=>$this->validateInput($input['any_obstacle_approved_status']),
								"fax_status_approved_status"=>$this->validateInput($input['fax_status_approved_status']),
								"mobile_connectivity_approved_status"=>$this->validateInput($input['mobile_connectivity_approved_status']),
								"internet_facility_approved_status"=>$this->validateInput($input['internet_facility_approved_status']),
								"insurgency_status_approved_status"=>$this->validateInput($input['insurgency_status_approved_status']),
								"is_forest_area_approved_status"=>$this->validateInput($input['is_forest_area_approved_status']),
								"vulnerable_status_approved_status"=>$this->validateInput($input['vulnerable_status_approved_status']),
								"sensitive_area_approved_status"=>$this->validateInput($input['sensitive_area_approved_status']),
								"bldg_cond_reverfication_status"=>$this->validateInput($input['bldg_cond_reverfication_status']),
								"ps_distance_reverfication_status"=>$this->validateInput($input['ps_distance_reverfication_status']),
								"is_dangerous_reverfication_status"=>$this->validateInput($input['is_dangerous_reverfication_status']),
								"is_govt_reverfication_status"=>$this->validateInput($input['is_govt_reverfication_status']),
								"religiou_inst_reverfication_status"=>$this->validateInput($input['religiou_inst_reverfication_status']),
								"is_school_reverfication_status"=>$this->validateInput($input['is_school_reverfication_status']),
								"ground_floor_reverfication_status"=>$this->validateInput($input['ground_floor_reverfication_status']),
								"separate_entry_reverfication_status"=>$this->validateInput($input['separate_entry_reverfication_status']),
								"party_office_reverfication_status"=>$this->validateInput($input['party_office_reverfication_status']),
								"electricity_available_reverfication_status"=>$this->validateInput($input['electricity_available_reverfication_status']),
								"separate_toilet_reverfication_status"=>$this->validateInput($input['separate_toilet_reverfication_status']),
								"shelter_option_reverfication_status"=>$this->validateInput($input['shelter_option_reverfication_status']),
								"road_option_reverfication_status"=>$this->validateInput($input['road_option_reverfication_status']),
								"any_obstacle_reverfication_status"=>$this->validateInput($input['any_obstacle_reverfication_status']),
								"fax_status_reverfication_status"=>$this->validateInput($input['fax_status_reverfication_status']),
								"mobile_connectivity_reverfication_status"=>$this->validateInput($input['mobile_connectivity_reverfication_status']),
								"internet_facility_reverfication_status"=>$this->validateInput($input['internet_facility_reverfication_status']),
								"insurgency_status_reverfication_status"=>$this->validateInput($input['insurgency_status_reverfication_status']),
								"is_forest_area_reverfication_status"=>$this->validateInput($input['is_forest_area_reverfication_status']),
								"vulnerable_status_reverfication_status"=>$this->validateInput($input['vulnerable_status_reverfication_status']),
								"sensitive_area_reverfication_status"=>$this->validateInput($input['sensitive_area_reverfication_status']),
								
								"photo_name"=>$this->imageUrlSave('image',$sId,$AcId).$img, 
								"updated_at"=> date('Y-m-d H:i:s', time()),
								"updated_by"=> $uId,
								"status"=> 1,
								"deleted"=> 0,
								]); 
							$message='EMF details updated';	
							} else {		
								$isUpdate = DB::connection('mysql2')->table('emf_master')
								->insert([
								"ps_id" =>$ps_id,
								"bldg_quality_condition" =>$this->validateInput($input['bldg_quality_condition']),
								"ps_less_than_20_sqmtrs" =>$this->validateInput($input['ps_less_than_20_sqmtrs']),
								"is_bldg_dangerous"=>$this->validateInput($input['is_bldg_dangerous']),
								"is_govt_bldg"=>$this->validateInput($input['is_govt_bldg']),
								"is_religious_inst"=>$this->validateInput($input['is_religious_inst']),
								"is_school_college"=>$this->validateInput($input['is_school_college']),
								"is_ground_floor"=>$this->validateInput($input['is_ground_floor']),
								"is_separate_entry_exit"=>$this->validateInput($input['is_separate_entry_exit']),
								"is_pol_party_office_within_200mtr"=>$this->validateInput($input['is_pol_party_office_within_200mtr']),
								"is_electricity_available"=>$this->validateInput($input['is_electricity_available']),
								"is_separate_toilet"=>$this->validateInput($input['is_separate_toilet']),
								"is_shelter_available"=>$this->validateInput($input['is_shelter_available']),
								"is_proper_road_connectivity"=>$this->validateInput($input['is_proper_road_connectivity']),
								"is_any_obstacle_in_way"=>$this->validateInput($input['is_any_obstacle_in_way']),
								"is_landline_fax_available"=>$this->validateInput($input['is_landline_fax_available']),
								"mobile_connectivity"=>$this->validateInput($input['mobile_connectivity']),
								"is_insurgency_affected"=>$this->validateInput($input['is_insurgency_affected']),
								"is_forest_area"=>$this->validateInput($input['is_forest_area']),
								"is_vulnerable_critical_location"=>$this->validateInput($input['is_vulnerable_critical_location']),
								"is_sensitive_area"=>$this->validateInput($input['is_sensitive_area']),
								"bldg_cond_update_on"=>$this->validateInput($input['bldg_cond_update_on']),
								"bldg_cond_update_by"=>$this->validateInput($input['bldg_cond_update_by']),
								"ps_distance_update_on"=>$this->validateInput($input['ps_distance_update_on']),
								"ps_distance_update_by"=>$this->validateInput($input['ps_distance_update_by']),
								"is_dangerous_update_on"=>$this->validateInput($input['is_dangerous_update_on']),
								"is_dangerous_update_by"=>$this->validateInput($input['is_dangerous_update_by']),
								"is_govt_bldg_update_on"=>$this->validateInput($input['is_govt_bldg_update_on']),
								"is_govt_bldg_update_by"=>$this->validateInput($input['is_govt_bldg_update_by']),
								"religious_inst_update_on"=>$this->validateInput($input['religious_inst_update_on']),
								"religious_inst_update_by"=>$this->validateInput($input['religious_inst_update_by']),
								"is_school_update_on"=>$this->validateInput($input['is_school_update_on']),
								"is_school_update_by"=>$this->validateInput($input['is_school_update_by']),
								"ground_floor_update_on"=>$this->validateInput($input['ground_floor_update_on']),
								"ground_floor_update_by"=>$this->validateInput($input['ground_floor_update_by']),
								"separate_entry_update_on"=>$this->validateInput($input['separate_entry_update_on']),
								"separate_entry_update_by"=>$this->validateInput($input['separate_entry_update_by']),
								"party_office_update_on"=>$this->validateInput($input['party_office_update_on']),
								"party_office_update_by"=>$this->validateInput($input['party_office_update_by']),
								"electricity_available_update_on"=>$this->validateInput($input['electricity_available_update_on']),
								"electricity_available_update_by"=>$this->validateInput($input['electricity_available_update_by']),
								"separate_toilet_update_on"=>$this->validateInput($input['separate_toilet_update_on']),
								"separate_toilet_update_by"=>$this->validateInput($input['separate_toilet_update_by']),
								"shelter_option_update_on"=>$this->validateInput($input['shelter_option_update_on']),
								"shelter_option_update_by"=>$this->validateInput($input['shelter_option_update_by']),
								"road_option_update_on"=>$this->validateInput($input['road_option_update_on']),
								"road_option_update_by"=>$this->validateInput($input['road_option_update_by']),
								"any_obstacle_update_on"=>$this->validateInput($input['any_obstacle_update_on']),
								"any_obstacle_update_by"=>$this->validateInput($input['any_obstacle_update_by']),
								"fax_status_update_on"=>$this->validateInput($input['fax_status_update_on']),
								"fax_status_update_by"=>$this->validateInput($input['fax_status_update_by']),
								"mobile_connectivity_update_on"=>$this->validateInput($input['mobile_connectivity_update_on']),
								"mobile_connectivity_update_by"=>$this->validateInput($input['mobile_connectivity_update_by']),
								"internet_facility_update_on"=>$this->validateInput($input['internet_facility_update_on']),
								"internet_facility_update_by"=>$this->validateInput($input['internet_facility_update_by']),
								"insurgency_status_update_on"=>$this->validateInput($input['insurgency_status_update_on']),
								"insurgency_status_update_by"=>$this->validateInput($input['insurgency_status_update_by']),
								"is_forest_area_update_on"=>$this->validateInput($input['is_forest_area_update_on']),
								"is_forest_area_update_by"=>$this->validateInput($input['is_forest_area_update_by']),
								"vulnerable_status_update_on"=>$this->validateInput($input['vulnerable_status_update_on']),
								"vulnerable_status_update_by"=>$this->validateInput($input['vulnerable_status_update_by']),
								"sensitive_area_update_on"=>$this->validateInput($input['sensitive_area_update_on']),
								"sensitive_area_update_by"=>$this->validateInput($input['sensitive_area_update_by']),
								"bldg_cond_approved_status"=>$this->validateInput($input['bldg_cond_approved_status']),
								"ps_distance_approved_status"=>$this->validateInput($input['ps_distance_approved_status']),
								"is_dangerous_approved_status"=>$this->validateInput($input['is_dangerous_approved_status']),
								"is_govt_approved_status"=>$this->validateInput($input['is_govt_approved_status']),
								"religious_inst_approved_status"=>$this->validateInput($input['religious_inst_approved_status']),
								"is_school_approved_status"=>$this->validateInput($input['is_school_approved_status']),
								"ground_floor_approved_status"=>$this->validateInput($input['ground_floor_approved_status']),
								"separate_entry_approved_status"=>$this->validateInput($input['separate_entry_approved_status']),
								"party_office_approved_status"=>$this->validateInput($input['party_office_approved_status']),
								"electricity_available_approved_status"=>$this->validateInput($input['electricity_available_approved_status']),
								"separate_toilet_approved_status"=>$this->validateInput($input['separate_toilet_approved_status']),
								"shelter_option_approved_status"=>$this->validateInput($input['shelter_option_approved_status']),
								"road_option_approved_status"=>$this->validateInput($input['road_option_approved_status']),
								"any_obstacle_approved_status"=>$this->validateInput($input['any_obstacle_approved_status']),
								"fax_status_approved_status"=>$this->validateInput($input['fax_status_approved_status']),
								"mobile_connectivity_approved_status"=>$this->validateInput($input['mobile_connectivity_approved_status']),
								"internet_facility_approved_status"=>$this->validateInput($input['internet_facility_approved_status']),
								"insurgency_status_approved_status"=>$this->validateInput($input['insurgency_status_approved_status']),
								"is_forest_area_approved_status"=>$this->validateInput($input['is_forest_area_approved_status']),
								"vulnerable_status_approved_status"=>$this->validateInput($input['vulnerable_status_approved_status']),
								"sensitive_area_approved_status"=>$this->validateInput($input['sensitive_area_approved_status']),
								"bldg_cond_reverfication_status"=>$this->validateInput($input['bldg_cond_reverfication_status']),
								"ps_distance_reverfication_status"=>$this->validateInput($input['ps_distance_reverfication_status']),
								"is_dangerous_reverfication_status"=>$this->validateInput($input['is_dangerous_reverfication_status']),
								"is_govt_reverfication_status"=>$this->validateInput($input['is_govt_reverfication_status']),
								"religiou_inst_reverfication_status"=>$this->validateInput($input['religiou_inst_reverfication_status']),
								"is_school_reverfication_status"=>$this->validateInput($input['is_school_reverfication_status']),
								"ground_floor_reverfication_status"=>$this->validateInput($input['ground_floor_reverfication_status']),
								"separate_entry_reverfication_status"=>$this->validateInput($input['separate_entry_reverfication_status']),
								"party_office_reverfication_status"=>$this->validateInput($input['party_office_reverfication_status']),
								"electricity_available_reverfication_status"=>$this->validateInput($input['electricity_available_reverfication_status']),
								"separate_toilet_reverfication_status"=>$this->validateInput($input['separate_toilet_reverfication_status']),
								"shelter_option_reverfication_status"=>$this->validateInput($input['shelter_option_reverfication_status']),
								"road_option_reverfication_status"=>$this->validateInput($input['road_option_reverfication_status']),
								"any_obstacle_reverfication_status"=>$this->validateInput($input['any_obstacle_reverfication_status']),
								"fax_status_reverfication_status"=>$this->validateInput($input['fax_status_reverfication_status']),
								"mobile_connectivity_reverfication_status"=>$this->validateInput($input['mobile_connectivity_reverfication_status']),
								"internet_facility_reverfication_status"=>$this->validateInput($input['internet_facility_reverfication_status']),
								"insurgency_status_reverfication_status"=>$this->validateInput($input['insurgency_status_reverfication_status']),
								"is_forest_area_reverfication_status"=>$this->validateInput($input['is_forest_area_reverfication_status']),
								"vulnerable_status_reverfication_status"=>$this->validateInput($input['vulnerable_status_reverfication_status']),
								"sensitive_area_reverfication_status"=>$this->validateInput($input['sensitive_area_reverfication_status']),
								"photo_name"=>$this->imageUrlSave('image',$sId,$AcId).$img, 
								"created_at"=> date('Y-m-d H:i:s', time()),
								"created_by"=> $uId,
								"status"=> 1,
								"deleted"=> 0,
								]); 
							  $message='EMF details created';	
							}	
							
							if($isUpdate==1){
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message]);
							} else {
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'EMF details not updated']);
							}
						}else{
							    return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'EMF photo missing']);
						}
						
				} else {
				 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'EMF details  not updated']);
				 die;
				}				
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
   }
   
   public function electorupdate(Request $request)
   {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
			
				$input=$request->All();				
				$validator = Validator::make($request->all(), [
				'id' => 'required',
                'electors_male' => 'required',
				'electors_female' => 'required',
				'electors_other' => 'required',
				'electors_pwd' => 'required',
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required.'
				]); }
				
				if(!isset($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Elector id not found'], $this->UnsuccessStatus );
				}
				
				if(!is_numeric($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Elector id not found'], $this->UnsuccessStatus );
				} 
				
				$ps_id=$this->getPsId($uId);
				$electors_male=$input['electors_male'];
				if( !empty($electors_male)){	
						$fileName=''; $img=''; $message='';
							if( $input['id'] > 0){	
								$isUpdate1 = DB::connection('mysql2')->table('electors_info')
								->where('id', $input['id'])
								->update([
								"st_code" =>$this->getStateCode($ps_id),
								"ac_no" =>$this->getACNo($ps_id),
								"electors_male" =>$this->validateInput($input['electors_male']),
								"electors_female" =>$this->validateInput($input['electors_female']),
								"electors_other"=>$this->validateInput($input['electors_other']),
								"no_of_pwd_voters" =>$this->validateInput($input['electors_pwd']),
								"updated_at"=> date('Y-m-d H:i:s', time()),
								"updated_by"=> $uId,
								]); 								
								
								/*$isUpdate = DB::connection('mysql2')->table('pwd_info')
								->where('ps_id', $ps_id)
								->update([
								"no_of_pwd_voters" =>$this->validateInput($input['electors_pwd']),
								"updated_at"=> date('Y-m-d H:i:s', time()),
								"updated_by"=> $uId,
								]); */ 
							$message = 'Elector info updated';	
								
							} else {
								
								$isUpdate1 = DB::connection('mysql2')->table('electors_info')
								->insert([
								"st_code" =>$this->getStateCode($ps_id),
								"ac_no" =>$this->getACNo($ps_id),
								"ps_id" =>$ps_id,
								"electors_male" =>$this->validateInput($input['electors_male']),
								"electors_male" =>$this->validateInput($input['electors_male']),
								"electors_female" =>$this->validateInput($input['electors_female']),
								"electors_other"=>$this->validateInput($input['electors_other']),
								"no_of_pwd_voters" =>$this->validateInput($input['electors_pwd']),
								"created_at"=> date('Y-m-d H:i:s', time()),
								"created_by"=> $uId,
								]); 								
								
								/*$isUpdate = DB::connection('mysql2')->table('pwd_info')
								->insert([
								"ps_id" =>$ps_id,
								"no_of_pwd_voters" =>$this->validateInput($input['electors_pwd']),
								"created_at"=> date('Y-m-d H:i:s', time()),
								"created_by"=> $uId,
								]); */ 
							$message = 'Elector info added';	
							}
								
							if($isUpdate1==1){	
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message]);
							} else {
								return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Elector info not updated']);
							}	
				} else {
				 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Elector details  not updated']);
				 die;
				}				
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
	public function hospitalview(Request $request)
    {	
		/*return "Speed Testing without condition";		
		$result = DB::connection('mysql2')->table('hospital_info')
				->select('id')
				->where('ps_id', '=', 1)
				->where('deleted', '=', 0)
				->get();				
		return "Speed Testing with condition"; */
		
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){
			
				$ps_id=$this->getPsId($uId);
				if(empty($ps_id)){
					return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>"Hospital info not found" ]);
				}
				$result=array();
				$getData = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();	
				
				$result=array();
				$state =$this->getState($getData[0]->st_code); 
				$distName =$this->getDist($getData[0]->st_code, $getData[0]->dist_no);
				$acName =$this->getAc($getData[0]->st_code, $getData[0]->ac_no); 
				
				$result = DB::connection('mysql2')->table('hospital_info')
				->select('id', 'ps_id', 'hospital_name','contact_name','contact_number', 'address', DB::raw('IFNULL(emergency_services, "No") as emergency_services'), 'longitude', 'latitude',  'photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				
				$sId = $this->SIdByPsId($uId);
				$AcId = $this->AcIdByPsId($uId);
				
				$resultdata=array();
				if(count($result)>0){
				$result = $this->removeNullInResult($result);
				foreach($result as $data){ 
						
				
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs', $sId,$AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId,$AcId).str_replace("img", "thumb", $data['photo_name']);		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'hospital_name'=>$data['hospital_name'],
						  'address'=>$data['address'],
						  'contact_name'=>$data['contact_name'],
						  'contact_number'=>$data['contact_number'],
						  'emergency_services'=>$data['emergency_services'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude']
						  );
				}
				
				}
				
				
				
				$message='';
				if(count($result)>0){
					$message='Hospitals Details';
				} else {
					$message='Hospitals not found';
				}
				return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'result' =>$resultdata]);
		 }
	   } catch (Exception $ex) {
       return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
	
   public function hospitalupdate(Request $request)
   {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
			
				$input=$request->All();				
				$validator = Validator::make($request->all(), [
				'id' => 'required',
                'hospital_name' => 'required',
				'contact_name' => 'required',
				'contact_number' => 'required',
				'address' => 'required',
				'emergency_services' => 'required',
				//'photo_name' => 'required',
				'latitude' => 'required',
				'longitude' => 'required'
				
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required.'
				]); }
				
				if(!isset($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Hospital id not found'], $this->UnsuccessStatus );
				}
				
				if(!is_numeric($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Hospital id not found'], $this->UnsuccessStatus );
				}
				
				
				$ps_id=$this->getPsId($uId);	
				if( !empty($input['hospital_name'])){	
				
							$fileName=''; $img=''; $message='';								
							$sId = $this->SIdByPsId($uId);
							$AcId = $this->AcIdByPsId($uId); 
							
							if ($request->hasFile('photo_name')) {
							$img = $this->uploadImageInDirectroy($request->file('photo_name'), $sId,$AcId);
							if( $input['id'] > 0 ){
								$isUpdate = DB::connection('mysql2')->table('hospital_info')
								->where('id', $input['id'])
								->update([
								"hospital_name" =>$this->validateInput($input['hospital_name']),
								"contact_name" =>$this->validateInput($input['contact_name']),
								"contact_number" =>$this->validateInput($input['contact_number']),
								"address"=>$this->validateInput($input['address']),
								"emergency_services"=>$this->validateInput($input['emergency_services']),
								"latitude"=>$this->validateInput($input['latitude']),
								"longitude"=>$this->validateInput($input['longitude']),
								"photo_name"=>$this->imageUrlSave('image', $sId,$AcId).$img,
								"updated_at"=> date('Y-m-d H:i:s', time()),
								"updated_by"=> $uId,
								]); 
							  $message='Hospital info updated';	
							} else {
								$isUpdate = DB::connection('mysql2')->table('hospital_info')
								->insert([
								"st_code" =>$this->getStateCode($ps_id),
								"dist_code" =>$this->getDistNo($ps_id),
								"ac_no" =>$this->getACNo($ps_id),
								"ps_id" =>$ps_id,
								"hospital_name" =>$this->validateInput($input['hospital_name']),
								"contact_name" =>$this->validateInput($input['contact_name']),
								"contact_number" =>$this->validateInput($input['contact_number']),
								"address"=>$this->validateInput($input['address']),
								"emergency_services"=>$this->validateInput($input['emergency_services']),
								"latitude"=>$this->validateInput($input['latitude']),
								"longitude"=>$this->validateInput($input['longitude']),
								"photo_name"=>$this->imageUrlSave('image', $sId, $AcId).$img,
								"created_at"=> date('Y-m-d H:i:s', time()),
								"created_by"=> $uId,
								]); 
							 $message='Hospital info created';		
							}								
							if($isUpdate==1){	
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'photo'=>$this->image('image', $sId,$AcId).$img, 'thumbnail'=>$this->thumb('thumbs', $sId,$AcId).$img]);
							} else {
								return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Hospital info not updated']);
							}
						} else {
						if( $input['id'] > 0 ){
								$isUpdate = DB::connection('mysql2')->table('hospital_info')
								->where('id', $input['id'])
								->update([
								"hospital_name" =>$this->validateInput($input['hospital_name']),
								"contact_name" =>$this->validateInput($input['contact_name']),
								"contact_number" =>$this->validateInput($input['contact_number']),
								"address"=>$this->validateInput($input['address']),
								"emergency_services"=>$this->validateInput($input['emergency_services']),
								"latitude"=>$this->validateInput($input['latitude']),
								"longitude"=>$this->validateInput($input['longitude']),
								//"photo_name"=>$this->imageUrlSave('image', $sId,$AcId).$img,
								"updated_at"=> date('Y-m-d H:i:s', time()),
								"updated_by"=> $uId,
								]); 
							  $message='Hospital info updated';	
							} else {
								$isUpdate = DB::connection('mysql2')->table('hospital_info')
								->insert([
								"st_code" =>$this->getStateCode($ps_id),
								"dist_code" =>$this->getDistNo($ps_id),
								"ac_no" =>$this->getACNo($ps_id),
								"ps_id" =>$ps_id,
								"hospital_name" =>$this->validateInput($input['hospital_name']),
								"contact_name" =>$this->validateInput($input['contact_name']),
								"contact_number" =>$this->validateInput($input['contact_number']),
								"address"=>$this->validateInput($input['address']),
								"emergency_services"=>$this->validateInput($input['emergency_services']),
								"latitude"=>$this->validateInput($input['latitude']),
								"longitude"=>$this->validateInput($input['longitude']),
								//"photo_name"=>$this->imageUrlSave('image', $sId, $AcId).$img,
								"created_at"=> date('Y-m-d H:i:s', time()),
								"created_by"=> $uId,
								]); 
							 $message='Hospital info created';		
							}								
							if($isUpdate==1){	
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'photo'=>'', 'thumbnail'=>'']);
							} else {
								return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Hospital info not updated']);
							}	
						}	



							
				} else {
				 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Hospital details  not updated']);
				 die;
				}				
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
   }
  
   public function firestationview(Request $request)
    {	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){
			
				$ps_id=$this->getPsId($uId);
				if(empty($ps_id)){
					return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>"Fire station  info not found" ]);
				}
				$result=array();
				$getData = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)
				->get();	
				
				//echo  $ps_id; die;
				
				$sId = $this->SIdByPsId($uId);
				$AcId = $this->AcIdByPsId($uId); 
				
				$result=array();
				$state =$this->getState($getData[0]->st_code); 
				$distName =$this->getDist($getData[0]->st_code, $getData[0]->dist_no);
				$acName =$this->getAc($getData[0]->st_code, $getData[0]->ac_no); 
				
				$result = DB::connection('mysql2')->table('fire_station_info')
				->select('id', 'ps_id', 'fire_station_name','contact_name','contact_number', 'address', 'latitude', 'longitude', 'photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata=array();
						
				if(count($result)>0){
						$result = $this->removeNullInResult($result);
						foreach($result as $data){
						$photo=$thumb='';
						if($data['photo_name']!=''){ 						
						
						$photo=$this->imageUrl('thumbs', $sId,$AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId,$AcId). str_replace("img", "thumb", $data['photo_name']);		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
							
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'fire_station_name'=>$data['fire_station_name'], 	
						  'contact_name'=>$data['contact_name'],
						  'contact_number'=>$data['contact_number'],
						  'address'=>$data['address'],
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,	
						  );
						}
				}
				
				$message='';
				if(count($result)>0){
					$message='Fire station Details';
				} else {
					$message='Fire station not found';
				}
				return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'result' =>$resultdata]);
		 }
	   } catch (Exception $ex) {
       return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
	
	
	
	public function fuelpumpview(Request $request)
    {	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){
			
				$ps_id=$this->getPsId($uId);
				if(empty($ps_id)){
					return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>"Fuel pump info not found" ]);
				}
				$result=array();
				$getData = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)
				->get();	
				
				$result=array();
				$state =$this->getState($getData[0]->st_code); 
				$distName =$this->getDist($getData[0]->st_code, $getData[0]->dist_no);
				$acName =$this->getAc($getData[0]->st_code, $getData[0]->ac_no); 
				
				$result = DB::connection('mysql2')->table('fuel_pump_info')
				->select('id', 'ps_id', 'fuel_pump_name','contact_name','contact_number', 'address', 'timings', 'fuel_types', 'latitude', 'longitude', 'photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdata= array();
				if(count($result)>0){
				$result = $this->removeNullInResult($result);
				$sId = $this->SIdByPsId($uId);
				$AcId = $this->AcIdByPsId($uId); 
				foreach($result as $data){
						$photo=$thumb='';
						if($data['photo_name']!=''){ 
						$photo=$this->imageUrl('thumbs', $sId,$AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs', $sId,$AcId).str_replace("img", "thumb", $data['photo_name']);		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}							
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'fuel_pump_name'=>$data['fuel_pump_name'], 	
						  'contact_name'=>$data['contact_name'],
						  'contact_number'=>$data['contact_number'],
						  'timings'=>$data['timings'],
						  'fuel_types'=>$data['fuel_types'],
						  'address'=>$data['address'],
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,	
						  );
						}
				}
				$message='';
				if(count($result)>0){
					$message='Fuel pump Details';
				} else {
					$message='Fuel pump not found';
				}
				return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'result' =>$resultdata]);
		 }
	   } catch (Exception $ex) {
       return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
	public function fuelpumpupdate(Request $request)
    {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
			
				$input=$request->All();				
				$validator = Validator::make($request->all(), [
				'id' => 'required',
                'fuel_pump_name' => 'required',
				'contact_name' => 'required',
				'contact_number' => 'required',
				'timings' => 'required',
				'fuel_types' => 'required',
				'longitude' => 'required',
				'latitude' => 'required',
				//'photo_name' => 'required'
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required.'
				]); }
				
				if(!isset($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Fuel pump  id not found'], $this->UnsuccessStatus );
				}
				
				if(!is_numeric($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Fuel pump id not found'], $this->UnsuccessStatus );
				}
				$ps_id=$this->getPsId($uId);	
				$id=$input['id'];
				$img='';
				if( !empty($input['fuel_pump_name'])){	
				
							$fileName=''; $img=''; $message='';
								
							$sId = $this->SIdByPsId($uId);
							$AcId = $this->AcIdByPsId($uId); 	
							//echo "<pre>"; print_r($request->file('photo_name')); die;
							if ($request->hasFile('photo_name')) { //	die("1");
							$img = $this->uploadImageInDirectroy($request->file('photo_name'),$sId,$AcId); 
							if( $id  > 0 ) {		
								$isUpdate = DB::connection('mysql2')->table('fuel_pump_info')
								->where('id', $input['id'])
								->update([
								"fuel_pump_name" =>$this->validateInput($input['fuel_pump_name']),
								"contact_name" =>$this->validateInput($input['contact_name']),
								"address"=>$this->validateInput($input['address']),
								"contact_number"=>$this->validateInput($input['contact_number']),
								"latitude"=>$this->validateInput($input['latitude']),
								"longitude"=>$this->validateInput($input['longitude']),
								"timings"=>$this->validateInput($input['timings']),
								"fuel_types"=>$this->validateInput($input['fuel_types']),
								"photo_name"=>$this->imageUrlSave('image', $sId,$AcId).$img,
								"updated_at"=> date('Y-m-d H:i:s', time()),
								"updated_by"=> $uId,
								]); 
								$message="Fuel pump info updated";	
							} else {
								$isUpdate = DB::connection('mysql2')->table('fuel_pump_info')
								->insert([
								"st_code" =>$this->getStateCode($ps_id),
								"dist_code" =>$this->getDistNo($ps_id),
								"ac_no" =>$this->getACNo($ps_id),
								"ps_id" =>$ps_id,
								"fuel_pump_name" =>$this->validateInput($input['fuel_pump_name']),
								"contact_name" =>$this->validateInput($input['contact_name']),
								"address"=>$this->validateInput($input['address']),
								"contact_number"=>$this->validateInput($input['contact_number']),
								"latitude"=>$this->validateInput($input['latitude']),
								"longitude"=>$this->validateInput($input['longitude']),
								"timings"=>$this->validateInput($input['timings']),
								"fuel_types"=>$this->validateInput($input['fuel_types']),
								"photo_name"=>$this->imageUrlSave('image', $sId,$AcId).$img,
								"created_at"=> date('Y-m-d H:i:s', time()),
								"created_by"=> $uId,
								]); 
								$message="Fuel pump created";
							}
							if($isUpdate==1){	
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'photo'=>$this->image('image',$sId,$AcId ).$img, 'thumbnail'=>$this->thumb('thumbs',$sId,$AcId).$img]);
							} else {
								return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Fuel pump info not updated']);
							}
						} else { // die("2");
							if( $id  > 0 ) {		
								$isUpdate = DB::connection('mysql2')->table('fuel_pump_info')
								->where('id', $input['id'])
								->update([
								"fuel_pump_name" =>$this->validateInput($input['fuel_pump_name']),
								"contact_name" =>$this->validateInput($input['contact_name']),
								"address"=>$this->validateInput($input['address']),
								"contact_number"=>$this->validateInput($input['contact_number']),
								"latitude"=>$this->validateInput($input['latitude']),
								"longitude"=>$this->validateInput($input['longitude']),
								"timings"=>$this->validateInput($input['timings']),
								"fuel_types"=>$this->validateInput($input['fuel_types']),
								//"photo_name"=>$this->imageUrlSave('image', $sId,$AcId).$img,
								"updated_at"=> date('Y-m-d H:i:s', time()),
								"updated_by"=> $uId,
								]); 
								$message="Fuel pump info updated";	
							} else {
								$isUpdate = DB::connection('mysql2')->table('fuel_pump_info')
								->insert([
								"st_code" =>$this->getStateCode($ps_id),
								"dist_code" =>$this->getDistNo($ps_id),
								"ac_no" =>$this->getACNo($ps_id),
								"ps_id" =>$ps_id,
								"fuel_pump_name" =>$this->validateInput($input['fuel_pump_name']),
								"contact_name" =>$this->validateInput($input['contact_name']),
								"address"=>$this->validateInput($input['address']),
								"contact_number"=>$this->validateInput($input['contact_number']),
								"latitude"=>$this->validateInput($input['latitude']),
								"longitude"=>$this->validateInput($input['longitude']),
								"timings"=>$this->validateInput($input['timings']),
								"fuel_types"=>$this->validateInput($input['fuel_types']),
								//"photo_name"=>$this->imageUrlSave('image', $sId,$AcId).$img,
								"created_at"=> date('Y-m-d H:i:s', time()),
								"created_by"=> $uId,
								]); 
								$message="Fuel pump created";
							}
							if($isUpdate==1){	
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'photo'=>'', 'thumbnail'=>'']);
							} else {
								return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Fuel pump info not updated']);
							}
						}
							
				} else {
				 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Fuel pump details  not updated']);
				 die;
				}				
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
	public function firestationupdate(Request $request)
    {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
			
				$input=$request->All();				
				$validator = Validator::make($request->all(), [
				'id' => 'required',
                'fire_station_name' => 'required',
				'contact_name' => 'required',
				'contact_number' => 'required',
				'longitude' => 'required',
				'latitude' => 'required',
				//'photo_name' => 'required'
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required.'
				]); }
				
				if(!isset($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Fire Station id not found'], $this->UnsuccessStatus );
				}
				
				if(!is_numeric($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Fire Station id not found'], $this->UnsuccessStatus );
				}
				$ps_id=$this->getPsId($uId);	
				$id=$input['id'];
				$img='';
				if( !empty($input['fire_station_name'])){	
				
								$fileName=''; $img=''; $message='';
								$sId = $this->SIdByPsId($uId);
								$AcId = $this->AcIdByPsId($uId); 
								
						if ($request->hasFile('photo_name')) {	
							$img = $this->uploadImageInDirectroy($request->file('photo_name'), $sId,$AcId);	
							if( $id  > 0 ) {		
								$isUpdate = DB::connection('mysql2')->table('fire_station_info')
								->where('id', $input['id'])
								->update([
								"fire_station_name" =>$this->validateInput($input['fire_station_name']),
								"contact_name" =>$this->validateInput($input['contact_name']),
								"address"=>$this->validateInput($input['address']),
								"contact_number"=>$this->validateInput($input['contact_number']),
								"latitude"=>$this->validateInput($input['latitude']),
								"longitude"=>$this->validateInput($input['longitude']),
								"photo_name"=>$this->imageUrlSave('image', $sId,$AcId).$img,
								"updated_at"=> date('Y-m-d H:i:s', time()),
								"updated_by"=> $uId,
								]); 
								$message="Fire Station info updated";	
							} else {
								$isUpdate = DB::connection('mysql2')->table('fire_station_info')
								->insert([
								"st_code" =>$this->getStateCode($ps_id),
								"dist_code" =>$this->getDistNo($ps_id),
								"ac_no" =>$this->getACNo($ps_id),
								"ps_id" =>$ps_id,
								"fire_station_name" =>$this->validateInput($input['fire_station_name']),
								"contact_name" =>$this->validateInput($input['contact_name']),
								"address"=>$this->validateInput($input['address']),
								"contact_number"=>$this->validateInput($input['contact_number']),
								"latitude"=>$this->validateInput($input['latitude']),
								"longitude"=>$this->validateInput($input['longitude']),
								"photo_name"=>$this->imageUrlSave('image', $sId,$AcId).$img,
								"created_at"=> date('Y-m-d H:i:s', time()),
								"created_by"=> $uId,
								]); 
								$message="Fire Station created";
							}
							if($isUpdate==1){	
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'photo'=>$this->image('image', $sId,$AcId).$img, 'thumbnail'=>$this->thumb('thumbs', $sId,$AcId).$img]);
							} else {
								return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Fire Station info not updated']);
							}
						} else {
						if( $id  > 0 ) {		
								$isUpdate = DB::connection('mysql2')->table('fire_station_info')
								->where('id', $input['id'])
								->update([
								"fire_station_name" =>$this->validateInput($input['fire_station_name']),
								"contact_name" =>$this->validateInput($input['contact_name']),
								"address"=>$this->validateInput($input['address']),
								"contact_number"=>$this->validateInput($input['contact_number']),
								"latitude"=>$this->validateInput($input['latitude']),
								"longitude"=>$this->validateInput($input['longitude']),
								//"photo_name"=>$this->imageUrlSave('image', $sId,$AcId).$img,
								"updated_at"=> date('Y-m-d H:i:s', time()),
								"updated_by"=> $uId,
								]); 
								$message="Fire Station info updated";	
							} else {
								$isUpdate = DB::connection('mysql2')->table('fire_station_info')
								->insert([
								"st_code" =>$this->getStateCode($ps_id),
								"dist_code" =>$this->getDistNo($ps_id),
								"ac_no" =>$this->getACNo($ps_id),
								"ps_id" =>$ps_id,
								"fire_station_name" =>$this->validateInput($input['fire_station_name']),
								"contact_name" =>$this->validateInput($input['contact_name']),
								"address"=>$this->validateInput($input['address']),
								"contact_number"=>$this->validateInput($input['contact_number']),
								"latitude"=>$this->validateInput($input['latitude']),
								"longitude"=>$this->validateInput($input['longitude']),
								//"photo_name"=>$this->imageUrlSave('image', $sId,$AcId).$img,
								"created_at"=> date('Y-m-d H:i:s', time()),
								"created_by"=> $uId,
								]); 
								$message="Fire Station created";
							}
							if($isUpdate==1){	
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'photo'=>'', 'thumbnail'=>'']);
							} else {
								return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Fire Station info not updated']);
							}
						}




							
				} else {
				 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Fire Station details  not updated']);
				 die;
				}				
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
    public function electorview(Request $request)
    {	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){
			
				$ps_id=$this->getPsId($uId);
				if(empty($ps_id)){
					return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>"Electors's polling station not found" ]);
				}
				$result=array();
				$getData = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)
				->get();	
				
				$result=array();
				$state =$this->getState($getData[0]->st_code); 
				$distName =$this->getDist($getData[0]->st_code, $getData[0]->dist_no);
				$acName =$this->getAc($getData[0]->st_code, $getData[0]->ac_no); 
				
				$result = DB::connection('mysql2')->table('electors_info')
				->select('id','electors_male','electors_female','electors_other', 'no_of_pwd_voters', 'disability_type','no_of_wheel_chair','no_of_vehicle_req')
				->where('ps_id', '=', $ps_id)
				->get();
				
				if(count($result)>0){
				$result = $this->removeNullInResult($result);
				}
				
				$message='';
				if(count($result)>0){
					$message='Elector Details';
				} else {
					$message='Elector not found';
				}
				
				return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'state' => "$state", 'DistName' => "$distName", 'Ac' => "$acName", 'message'=>$message, 'result' => $result]);
		 }
	   } catch (Exception $ex) {
       return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
	public function policestationview(Request $request)
    {	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){
			
				$ps_id=$this->getPsId($uId);
				if(empty($ps_id)){
					return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>"Police Station not found" ]);
				}
				$result=array();
				$getData = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)				
				->where('deleted', '=', 0)
				->get();	
				
				$result=array();
				$state =$this->getState($getData[0]->st_code); 
				$distName =$this->getDist($getData[0]->st_code, $getData[0]->dist_no);
				$acName =$this->getAc($getData[0]->st_code, $getData[0]->ac_no); 
				
				$result = DB::connection('mysql2')->table('police_station_info')
				->select('id','ps_id','station_name','address','officer_name','contact_number','photo_name','latitude','longitude')
				->where('ps_id', '=', $ps_id)
				->get();
				
				$sId = $this->SIdByPsId($uId);
				$AcId = $this->AcIdByPsId($uId); 
				
				$resultdaat=array();
				if(count($result)>0){
					$result = $this->removeNullInResult($result);
					foreach($result as $data){
						
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs',$sId,$AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs',$sId,$AcId).str_replace("img", "thumb", $data['photo_name']);		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						$resultdaat[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'station_name'=>$data['station_name'], 	
						  'officer_name'=>$data['officer_name'],
						  'contact_number'=>$data['contact_number'],
						  'address'=>$data['address'],
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb,	
						);
					}
				}
				$message='';
				if(count($result)>0){
					$message='Police station details';
				} else {
					$message='Police station not found';
				}
				
				return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'state' => "$state", 'DistName' => "$distName", 'Ac' => "$acName",	'message'=>$message, 'result' => $resultdaat]);
		 }
	   } catch (Exception $ex) {
       return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
	public function policestationupdate(Request $request)
    {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
			
				$input=$request->All();
				
				$validator = Validator::make($request->all(), [
                'id' => 'required',
				'station_name' => 'required',
				'address' => 'required',
				'officer_name' => 'required',
				'contact_number' => 'required',
				//'photo_name' => 'required',
				'latitude' => 'required',
				'longitude' => 'required'
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required.'
				]); }
				
				if(!isset($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Police station id\' not found'], $this->UnsuccessStatus );
				}
				
				if(!is_numeric($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Police station id\' not found'], $this->UnsuccessStatus );
				}
				
				/*
				if(!isset($input['photo_name'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Photo not found'], $this->UnsuccessStatus );
				} */
				
				$img='';
				$ps_id=$this->getPsId($uId);
				$station_name=$input['station_name'];
				if( !empty($station_name)){	
						$fileName=''; $img=''; $message='';
						
						if ($request->hasFile('photo_name')) {
							$sId = $this->SIdByPsId($uId);
							$AcId = $this->AcIdByPsId($uId); 
							$img = $this->uploadImageInDirectroy($request->file('photo_name'),$sId,$AcId);
							
							if( $input['id'] > 0  ){								
								$isUpdate = DB::connection('mysql2')->table('police_station_info')
								->where('id', $input['id'])
								->update([
								"station_name" =>$this->validateInput($input['station_name']),
								"address" =>$this->validateInput($input['address']),
								"officer_name" =>$this->validateInput($input['officer_name']),
								"contact_number"=>$this->validateInput($input['contact_number']),
								"longitude"=>$this->validateInput($input['longitude']),
								"latitude"=>$this->validateInput($input['latitude']),
								"photo_name"=> $this->imageUrlSave('image',$sId,$AcId).$img,
								"updated_at"=> date('Y-m-d H:i:s', time()),
								"updated_by"=> $uId,
								]); 
							   $message='Police station details updated';	
							} else {
								$isUpdate = DB::connection('mysql2')->table('police_station_info')
								->insert([
								"st_code" =>$this->getStateCode($ps_id),
								"dist_code" =>$this->getDistNo($ps_id),
								"ac_no" =>$this->getACNo($ps_id),
								"ps_id" =>$ps_id,
								"station_name" =>$this->validateInput($input['station_name']),
								"address" =>$this->validateInput($input['address']),
								"officer_name" =>$this->validateInput($input['officer_name']),
								"contact_number"=>$this->validateInput($input['contact_number']),
								"longitude"=>$this->validateInput($input['longitude']),
								"latitude"=>$this->validateInput($input['latitude']),
								"photo_name"=> $this->imageUrlSave('image',$sId,$AcId).$img,
								"created_at"=> date('Y-m-d H:i:s', time()),
								"created_by"=> $uId,
								]); 
							   $message='Police station details created';	
								
							}	
								
								
							if($isUpdate==1){
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'photo'=>$this->image('image',$sId,$AcId).$img, 'thumbnail'=>$this->thumb('thumbs',$sId,$AcId).$img]);
							} else {
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'Police station details not updated']);
							}	
						} else if (!$request->hasFile('photo_name')) {
							$sId = $this->SIdByPsId($uId);
							$AcId = $this->AcIdByPsId($uId); 
							//$img = $this->uploadImageInDirectroy($request->file('photo_name'),$sId,$AcId);
							
							if( $input['id'] > 0  ){								
								$isUpdate = DB::connection('mysql2')->table('police_station_info')
								->where('id', $input['id'])
								->update([
								"station_name" =>$this->validateInput($input['station_name']),
								"address" =>$this->validateInput($input['address']),
								"officer_name" =>$this->validateInput($input['officer_name']),
								"contact_number"=>$this->validateInput($input['contact_number']),
								"longitude"=>$this->validateInput($input['longitude']),
								"latitude"=>$this->validateInput($input['latitude']),
								//"photo_name"=> $this->imageUrlSave('image',$sId,$AcId).$img,
								"updated_at"=> date('Y-m-d H:i:s', time()),
								"updated_by"=> $uId,
								]); 
							   $message='Police station details updated';	
							} else {
								$isUpdate = DB::connection('mysql2')->table('police_station_info')
								->insert([
								"st_code" =>$this->getStateCode($ps_id),
								"dist_code" =>$this->getDistNo($ps_id),
								"ac_no" =>$this->getACNo($ps_id),
								"ps_id" =>$ps_id,
								"station_name" =>$this->validateInput($input['station_name']),
								"address" =>$this->validateInput($input['address']),
								"officer_name" =>$this->validateInput($input['officer_name']),
								"contact_number"=>$this->validateInput($input['contact_number']),
								"longitude"=>$this->validateInput($input['longitude']),
								"latitude"=>$this->validateInput($input['latitude']),
								//"photo_name"=> $this->imageUrlSave('image',$sId,$AcId).$img,
								"created_at"=> date('Y-m-d H:i:s', time()),
								"created_by"=> $uId,
								]); 
							   $message='Police station details created';	
								
							}	
								
								
							if($isUpdate==1){
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'photo'=>'', 'thumbnail'=>'']);
							} else {
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'Police station details not updated']);
							}	
						}
						else {
						 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Police station details  not updated']);
						}		
						
				} else {
				 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Police station details  not updated']);
				 die;
				}				
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
	    }
	   }catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
	
	public function policestationupdategeolocation(Request $request)
    {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
			
				$input=$request->All();
				
				$validator = Validator::make($request->all(), [
                'id' => 'required',
				'latitude' => 'required',
				'longitude' => 'required',
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required.'
				]); }
				
				if(!isset($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Police station id\' not found'], $this->UnsuccessStatus );
				}
				
				if(!is_numeric($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Police station id\' not found'], $this->UnsuccessStatus );
				}
				
				
				$ps_id=$input['id'];
				if( !empty($ps_id)){	
								$fileName=''; $img='';
								$isUpdate = DB::connection('mysql2')->table('police_station_info')
								->where('id', $input['id'])
								->update([
								"latitude" =>$this->validateInput($input['latitude']),
								"longitude" =>$this->validateInput($input['longitude']),
								"updated_at"=> date('Y-m-d H:i:s', time()),
								"updated_by"=> $uId,
								]); 
							if($isUpdate==1){
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'Police station\s geo location updated']);
							} else {
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'Geo location not updated']);
							}
				} else {
				 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Geo location not updated']);
				 die;
				}				
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
   function validateInput($data) { 
    $data = trim($data);
    $data = stripslashes($data);
    // $data = mysql_real_escape_string($data);
       $data = htmlspecialchars($data);
       return $data;
    }		
	
	function logout(Request $request){ 

		$header =   $request->headers->all(); 
		$data   =   $header['token'][0];
		
		DB::connection('mysql2')->table('officer_login')
		->where('token', $data)
		->update([
		"otp" =>'',
		"token"=>'',
		"otp_expire"=>'null',
		"is_login"=>0,
		"logout_time"=> date('Y-m-d H:i:s', time()),
		//"updated_by"=> $decrypted_token,
		"updated_at"=> date('Y-m-d H:i:s', time()),
		]);
		return response()->json(['code'=>$this->successStatus, 'status'=>1, 'success' => true, 'message'=>'Logout is success!']);


		/*if(preg_match("/^(.*)::(.*)$/", $data, $regs)) {
		list(, $crypted_token, $enc_iv) = $regs;
		$enc_method = 'AES-128-CBC';
		$enc_key = openssl_digest(gethostname() . "|" . ip2long($_SERVER['SERVER_ADDR']), 'SHA256', TRUE);
		$decrypted_token = openssl_decrypt($crypted_token, $enc_method, $enc_key, 0, hex2bin($enc_iv));
		unset($crypted_token, $enc_method, $enc_key, $enc_iv, $regs);
		
		DB::connection('mysql2')->table('officer_login')
		->where('booth_officer_id', $decrypted_token)
		->update([
		"otp" =>'',
		"token"=>'',
		"otp_expire"=>'null',
		"is_login"=>0,
		"logout_time"=> date('Y-m-d H:i:s', time()),
		"updated_by"=> $decrypted_token,
		"updated_at"=> date('Y-m-d H:i:s', time()),
		]);
		return response()->json(['code'=>$this->successStatus, 'status'=>1, 'success' => true, 'message'=>'Logout is success!']);
		} */
	}

   
	public function parkingareaview(Request $request)
    {	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){
			
				$ps_id=$this->getPsId($uId);
				if(empty($ps_id)){
					return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>"Parking area info not found" ]);
				}
				$result=array();
				$getData = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)
				->get();	
				//echo $ps_id; die;
				$data=array();
				$resultdata=array();
				$state =$this->getState($getData[0]->st_code); 
				$distName =$this->getDist($getData[0]->st_code, $getData[0]->dist_no);
				$acName =$this->getAc($getData[0]->st_code, $getData[0]->ac_no); 
			
				$resultdata = DB::connection('mysql2')->table('parking_info')
				->select('id', 'ps_id', 'address','longitude','latitude','photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				
				$resultdataarray=array();
				$sId = $this->SIdByPsId($uId);
				$AcId = $this->AcIdByPsId($uId); 
				if(count($resultdata)>0){
				 $resultdata = $this->removeNullInResult($resultdata);
				foreach($resultdata as $data){		
				
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs',$sId,$AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs',$sId,$AcId).str_replace("img", "thumb", $data['photo_name']);		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
					$resultdataarray[]= array( 					
					  'id'=>$data['id'],
					  'ps_id'=>$data['ps_id'],
					  'address'=>$data['address'],
					  'latitude'=>$data['latitude'],
					  'longitude'=>$data['longitude'],
					  'photo_name'=>$photo,
					  'thumbnail'=>$thumb,	
					  );
					}
				}
				//echo "<pre>"; print_r($resultdataarray); die;
				$message='';				
				if(count($resultdata)>0){
					$message='Parking area Details';
				} else {
					$message='Parking area not found';
				}
				
				
				return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'state' =>"$state", 'DistName' =>"$distName", 'Ac' => "$acName",'message'=>$message, 'result' =>$resultdataarray]);
		 }
	   } catch (Exception $ex) {
       return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
	public function busstandview(Request $request)
    {	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){
			
				$ps_id=$this->getPsId($uId);
				if(empty($ps_id)){
					return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>"Bus stand info not found" ]);
				}
				$result=array();
				$getData = DB::connection('mysql2')->table('polling_station')
				->where('id', '=', $ps_id)
				->get();	
				
				$result=array();
				$state =$this->getState($getData[0]->st_code); 
				$distName =$this->getDist($getData[0]->st_code, $getData[0]->dist_no);
				$acName =$this->getAc($getData[0]->st_code, $getData[0]->ac_no); 
				
				$result = DB::connection('mysql2')->table('bus_stand_info')
				->select('id','ps_id','bus_stand_name','address','longitude','latitude','photo_name')
				->where('ps_id', '=', $ps_id)
				->where('deleted', '=', 0)
				->get();
				$sId = $this->SIdByPsId($uId);
				$AcId = $this->AcIdByPsId($uId); 
				$resultdata=array();
				if(count($result)>0){
				$result = $this->removeNullInResult($result);
					
					foreach($result as $data){
						
						$photo=$thumb='';
						if($data['photo_name']!=''){
						$photo=$this->imageUrl('thumbs',$sId,$AcId).$data['photo_name'];
						$thumb=$this->imageUrlThumb('thumbs',$sId,$AcId).str_replace("img", "thumb", $data['photo_name']);		
						} else {
						$photo=$this->NoImage();
						$thumb=$this->NoImage();
						}
						
						$resultdata[]= array( 					
						  'id'=>$data['id'],
						  'ps_id'=>$data['ps_id'],
						  'bus_stand_name'=>$data['bus_stand_name'], 	
						  'address'=>$data['address'],
						  'latitude'=>$data['latitude'],
						  'longitude'=>$data['longitude'],
						  'photo_name'=>$photo,
						  'thumbnail'=>$thumb	
						  );
					}
				}
								
				$message='';
				if(count($result)>0){
					$message='Bus stand Details';
				} else {
					$message='Bus stand not found';
				}
				return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'result' =>$resultdata]);
		 }
	   } catch (Exception $ex) {
       return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
	
	
	
	public function parkingareaupdate(Request $request)
    {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
			
				$input=$request->All();				
				$validator = Validator::make($request->all(), [
				'id' => 'required',
                'address' => 'required',
				//'photo_name' => 'required',
				'latitude' => 'required',
				'longitude' => 'required'
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required.'
				]); }
				
				if(!isset($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Parking area\'s id not found'], $this->UnsuccessStatus );
				}
				
				if(!is_numeric($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Parking area\'s id not found'], $this->UnsuccessStatus );
				}
				$ps_id=$this->getPsId($uId);	
				$id=$input['id'];
				$img='';
				if( !empty($input['address'])){	
				
								$fileName=''; $img=''; $message='';
								
								$sId = $this->SIdByPsId($uId);
								$AcId = $this->AcIdByPsId($uId); 
								
						if ($request->hasFile('photo_name')) {		
							$img = $this->uploadImageInDirectroy($request->file('photo_name'),$sId,$AcId); 
							if( $id > 0){		
								$isUpdate = DB::connection('mysql2')->table('parking_info')
								->where('id', $input['id'])
								->update([
								"address" =>$this->validateInput($input['address']),
								"longitude" =>$this->validateInput($input['longitude']),
								"latitude"=>$this->validateInput($input['latitude']),
								"photo_name"=>$this->imageUrlSave('image',$sId,$AcId).$img,
								"updated_at"=> date('Y-m-d H:i:s', time()),
								"updated_by"=> $uId,
								]); 
							 $message='Parking area info updated';	
							} else {
								$isUpdate = DB::connection('mysql2')->table('parking_info')
								->insert([
								"st_code" =>$this->getStateCode($ps_id),
								"dist_code" =>$this->getDistNo($ps_id),
								"ac_no" =>$this->getACNo($ps_id),
								"ps_id" =>$ps_id,
								"address" =>$this->validateInput($input['address']),
								"longitude" =>$this->validateInput($input['longitude']),
								"latitude"=>$this->validateInput($input['latitude']),
								"photo_name"=>$this->imageUrlSave('image',$sId,$AcId).$img,
								"created_at"=> date('Y-m-d H:i:s', time()),
								"created_by"=> $uId,
								]); 
							 $message='Parking area info created';	
							}
							if($isUpdate==1){	
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'photo'=>$this->image('image',$sId,$AcId).$img, 'thumbnail'=>$this->thumb('thumbs',$sId,$AcId).$img]);
							} else {
								return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Parking area info not updated']);
							}
						} else {
							if( $id > 0){	
								$isUpdate = DB::connection('mysql2')->table('parking_info')
								->where('id', $input['id'])
								->update([
								"address" =>$this->validateInput($input['address']),
								"longitude" =>$this->validateInput($input['longitude']),
								"latitude"=>$this->validateInput($input['latitude']),
								//"photo_name"=>$this->imageUrlSave('image',$sId,$AcId).$img,
								"updated_at"=> date('Y-m-d H:i:s', time()),
								"updated_by"=> $uId,
								]); 
							 $message='Parking area info updated';	
							} else { 
								$isUpdate = DB::connection('mysql2')->table('parking_info')
								->insert([
								"st_code" =>$this->getStateCode($ps_id),
								"dist_code" =>$this->getDistNo($ps_id),
								"ac_no" =>$this->getACNo($ps_id),
								"ps_id" =>$ps_id,
								"address" =>$this->validateInput($input['address']),
								"longitude" =>$this->validateInput($input['longitude']),
								"latitude"=>$this->validateInput($input['latitude']),
								//"photo_name"=>$this->imageUrlSave('image',$sId,$AcId).$img,
								"created_at"=> date('Y-m-d H:i:s', time()),
								"created_by"=> $uId,
								]); 
							 $message='Parking area info created';	
							}
							if($isUpdate==1){	
								return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'photo'=>'', 'thumbnail'=>'']);
							} else {
								return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Parking area info not updated']);
							}
						}

				} else {
				 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Parking area info not updated']);
				 die;
				}				
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
	
	public function busstandupdate(Request $request)
    {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
			
				$input=$request->All();				
				$validator = Validator::make($request->all(), [
				'id' => 'required',
                'bus_stand_name' => 'required',
				'address' => 'required',
				'longitude' => 'required',
				'latitude' => 'required',
				//'photo_name' => 'required'
				]);   
				
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required.'
				]); }
				
				if(!isset($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Bus stand\'s id not found'], $this->UnsuccessStatus );
				}
				
				if(!is_numeric($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Bus stand\'s id not found'], $this->UnsuccessStatus );
				}
				
				$img='';
				if( !empty($input['bus_stand_name'])){	
				
							
							$fileName=''; $img=''; $message='';	
							
							$sId = $this->SIdByPsId($uId);
							$AcId = $this->AcIdByPsId($uId); 
							
								
								
							$ps_id=$this->getPsId($uId);	
							if ($request->hasFile('photo_name')) {
								$img = $this->uploadImageInDirectroy($request->file('photo_name'),$sId,$AcId); 	
								if( $input['id'] > 0 ){	
									$isUpdate = DB::connection('mysql2')->table('bus_stand_info')
									->where('id', $input['id'])
									->update([
									"st_code" =>$this->getStateCode($ps_id),
									"dist_code" =>$this->getDistNo($ps_id),
									"ac_no" =>$this->getACNo($ps_id),
									"ps_id" =>$ps_id,
									"bus_stand_name" =>$this->validateInput($input['bus_stand_name']),
									"address" =>$this->validateInput($input['address']),
									"latitude"=>$this->validateInput($input['latitude']),
									"longitude"=>$this->validateInput($input['longitude']),
									"photo_name"=>$this->imageUrlSave('image',$sId,$AcId).$img,
									"updated_at"=> date('Y-m-d H:i:s', time()),
									"updated_by"=> $uId,
									]); 
									
								$message='Bus stand info updated';	
									
									
							} else {
									
									$isUpdate = DB::connection('mysql2')->table('bus_stand_info')
									->insert([
									"st_code" =>$this->getStateCode($ps_id),
									"dist_code" =>$this->getDistNo($ps_id),
									"ac_no" =>$this->getACNo($ps_id),
									"ps_id" =>$ps_id,
									"bus_stand_name" =>$this->validateInput($input['bus_stand_name']),
									"address" =>$this->validateInput($input['address']),
									"latitude"=>$this->validateInput($input['latitude']),
									"longitude"=>$this->validateInput($input['longitude']),
									"photo_name"=>$this->imageUrlSave('image',$sId,$AcId).$img,
									"created_at"=> date('Y-m-d H:i:s', time()),
									"created_by"=> $uId,
									]); 
									
									$message='Bus stand info created';
							}
							if($isUpdate==1){	
							return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'photo'=>$this->image('image',$sId,$AcId).$img, 'thumbnail'=>$this->thumb('thumbs',$sId,$AcId).$img]);
							} else {
							return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Bus Stand info not updated']);
							}	  
								  
								  
							} else {
								
								  if( $input['id'] > 0 ){	
									$isUpdate = DB::connection('mysql2')->table('bus_stand_info')
									->where('id', $input['id'])
									->update([
									"st_code" =>$this->getStateCode($ps_id),
									"dist_code" =>$this->getDistNo($ps_id),
									"ac_no" =>$this->getACNo($ps_id),
									"ps_id" =>$ps_id,
									"bus_stand_name" =>$this->validateInput($input['bus_stand_name']),
									"address" =>$this->validateInput($input['address']),
									"latitude"=>$this->validateInput($input['latitude']),
									"longitude"=>$this->validateInput($input['longitude']),
									//"photo_name"=>$this->imageUrlSave('image',$sId,$AcId).$img,
									"updated_at"=> date('Y-m-d H:i:s', time()),
									"updated_by"=> $uId,
									]); 
									
								$message='Bus stand info updated';	
									
									
								} else {
									
									$isUpdate = DB::connection('mysql2')->table('bus_stand_info')
									->insert([
									"st_code" =>$this->getStateCode($ps_id),
									"dist_code" =>$this->getDistNo($ps_id),
									"ac_no" =>$this->getACNo($ps_id),
									"ps_id" =>$ps_id,
									"bus_stand_name" =>$this->validateInput($input['bus_stand_name']),
									"address" =>$this->validateInput($input['address']),
									"latitude"=>$this->validateInput($input['latitude']),
									"longitude"=>$this->validateInput($input['longitude']),
									//"photo_name"=>$this->imageUrlSave('image',$sId,$AcId).$img,
									"created_at"=> date('Y-m-d H:i:s', time()),
									"created_by"=> $uId,
									]); 
									$message='Bus stand info created';
								  }
							if($isUpdate==1){	
							return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>$message, 'photo'=>'', 'thumbnail'=>'']);
							} else {
							return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Bus Stand info not updated']);
							}  
								  
							}
							
								
				} else {
				 return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Bus STand info not updated']);
				 die;
				}				
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
	public function ps_address_update(Request $request)
    {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
				$input=$request->All();				
				$validator = Validator::make($request->all(), [
				'id' => 'required',
				'ps_address' => 'required',
				'ps_address_v1' => 'required',
				'user_id' => 'required',
				'lattitude' => 'required',
				'longitude' => 'required',
				
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required.'
				]); }
				
				if(!isset($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Table id not found']);
				}
				if($input['user_id']!=$uId){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Invalid user id.']);
				}
				
				
				if(!is_numeric($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Table id not found'] );
				} 
				
				
				if( $input['id'] > 0 ){
						$isUpdate = DB::connection('mysql2')->table('polling_station')
							->where('id', $input['id'])
							->update([
							'ps_address' =>$this->validateInput($input['ps_address']),
							'ps_address_v1' =>$this->validateInput($input['ps_address_v1']),
							'lattitude' =>$this->validateInput($input['lattitude']),
							'longitude' =>$this->validateInput($input['longitude']),
						]); 
						
						if($isUpdate){								
							return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'Record updated.']);
						} else {
							return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Record not updated.']);
						}
						
						
				} else {
					return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid table id.']);
				}
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }	
	
	
	
	public function amf_emf_update(Request $request)
    {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
				$input=$request->All();				
				$validator = Validator::make($request->all(), [
				'id' => 'required',
				'status' => 'required',
				'user_id' => 'required',
				'blo_current_status' => 'required',
				'lattitude' => 'required',
				'longitude' => 'required',
				
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required.'
				]); }
				
				if(!isset($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Table id not found']);
				}
				if($input['user_id']!=$uId){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Invalid user id.']);
				}
				
				
				if(!is_numeric($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Table id not found'] );
				} 
				
				if( ($input['blo_current_status']!=0) and  ($input['blo_current_status'] != 1)) { 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Blo current status value should be either 0 or 1']);
				} 
				if( ($input['status']!=0) and  ($input['status'] != 1) and  ($input['status'] != 2)) { 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Status value should be either 0 or 1 or 2']);
				} 
				if( $input['id'] > 0 ){
						$isUpdate = DB::connection('mysql2')->table('ps_facility_master')
							->where('id', $input['id'])
							->update([
							'status' =>$this->validateInput($input['status']),
							//'approved_status' =>'1',
							'blo_current_status' =>$this->validateInput($input['blo_current_status']),
							'reverfication_status' =>'0',
							'lattitude' =>$this->validateInput($input['lattitude']),
							'longitude' =>$this->validateInput($input['longitude']),
							'updated_at' =>date('Y-m-d H:i:s', time()),
							'updated_by' =>$this->validateInput($input['user_id']),
						]); 
						
						if($isUpdate){								
							return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'Record updated.']);
						} else {
							return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Record not updated.']);
						}
						
						
				} else {
					return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid table id.']);
				}
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }	
	
	public function psdata(Request $request)
	{	
	try{	
			$input=$request->All();				
			$validator = Validator::make($request->all(), [
			'epic' => 'required'
			]);      
			
			if($validator->fails()){  return response()->json([
				'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Epic No. is required required.', 'result'=>[]
			]); }
			    
			$epic=$input['epic'];
			set_time_limit(0);
			$url="https://electoralsearch.in/VoterSearch/SASSearch";
			$qry_str = "?epic_no=$epic&search_type=epic&pass_key=1bc229d474689492ffac764ac2930843cb4bbc3ce69334b7dc0c23bd6f96823ff3595cfd5b353d99ed42dbdb05f54e020790d75e98f736e69a383eac4717bc8c";
			$ch = curl_init();
			// Set query data here with the URL
			curl_setopt($ch, CURLOPT_URL, $url . $qry_str); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 3000); // In Second
			$content = trim(curl_exec($ch));
			curl_close($ch);
			$datav = json_decode($content);
			
			
		if(isset($datav->response->docs['0']->st_code) && isset($datav->response->docs['0']->ac_no) && isset($datav->response->docs['0']->ps_no))	{
			//echo $datav->response->docs['0']->st_code; die;
			
			$st=$datav->response->docs['0']->st_code;				
			$ac=$datav->response->docs['0']->ac_no;			
			$ps=$datav->response->docs['0']->ps_no;				
				
			//echo $st.'-'.$ac.'-'.$ps; die;	
			//$st="S01";
			//$ac="1";
			//$ps="1";	
			
			//die("ok");
			
            $ps_array_main=array();			
			$data2 = DB::connection('mysql2')->table('polling_station')
			->select('polling_station.id','polling_station.part_no','polling_station.part_name', 'polling_station.ps_no', 'polling_station.ps_loc_no', 'polling_station.ps_address', 'polling_station.ps_name_en', 'polling_station.ps_name_v2',  'polling_station.photo_name as polling_station_photo', 'polling_station.lattitude', 'polling_station.longitude', 'electors_info.electors_male', 'electors_info.electors_female', 'electors_info.electors_other', 'electors_info.no_of_pwd_voters', 'bus_stand_info.bus_stand_name', 'bus_stand_info.address as bus_stand_address',
			'bus_stand_info.longitude as bus_stand_longitude', 'bus_stand_info.latitude as bus_stand_latitude', 'bus_stand_info.photo_name as bus_stand_photo_name',
			'police_station_info.station_name as polic_station_name', 'police_station_info.address as police_station_address', 'police_station_info.officer_name',
			'police_station_info.contact_number as police_station_contact_number', 'police_station_info.longitude as police_station_longitude', 'police_station_info.latitude as police_station_latitude','police_station_info.photo_name as police_station_photo_name', 'fuel_pump_info.fuel_pump_name', 'fuel_pump_info.contact_name as fuel_pump_contact_name', 'fuel_pump_info.contact_number as fuel_pump_contact_number', 'fuel_pump_info.address as fuel_pump_address', 'fuel_pump_info.timings as fuel_pump_timings', 'fuel_pump_info.fuel_types', 'fuel_pump_info.photo_name as fuel_pump_info_photo_name', 'fuel_pump_info.longitude as fuel_pump_info_longitude',
			'fuel_pump_info.latitude as fuel_pump_info_latitude'
			)
			->leftjoin('electors_info', 'electors_info.ps_id', '=', 'polling_station.id') 
			->leftjoin('bus_stand_info', 'bus_stand_info.ps_id', '=', 'polling_station.id')
			->leftjoin('police_station_info', 'police_station_info.ps_id', '=', 'polling_station.id')
			->leftjoin('fuel_pump_info', 'fuel_pump_info.ps_id', '=', 'polling_station.id')
			->where('polling_station.st_code', $st)
			->where('polling_station.ac_no', $ac)
			->where('polling_station.ps_no', $ps)
			//->limit(5)
			->get();
			
			$datam=0;
			$data=array();
			$datamfine=array();
			if(count($data2)>0){
				foreach($data2 as $dataddddd){ 
				//	echo $dataddddd->id; die;	
				$data = DB::connection('mysql2')->table('ps_facility_master')
				->select('ps_facility_master.status', 'facility_master.field_name')
				->join('facility_master', 'facility_master.id', '=', 'ps_facility_master.facility_master_id')
				->where('ps_facility_master.ps_id', $dataddddd->id)
				->where('ps_facility_master.status', '1')
				->where('ps_facility_master.deleted', '0')
				->get();
				
				$datam=array();
				$narray=array();
				$finalarray=array();
				
				foreach($data as $key=>$val){
					if($val->status==1){
						$finalarray[$val->field_name]=True;
					} else {
						$finalarray[$val->field_name]="";
					}
				}
				
				//echo "<pre>"; print_r($dataddddd->ps_photo); die;
				
					$imge='';
					$mainarray=array();
					$ps_array_main=array();
					if(isset($dataddddd->police_station_photo_name)){ 
						$imgok=$this->imageUrl('thumbs', $st, $ac).$dataddddd->police_station_photo_name;
					} else {
						$imgok=$this->NoImage();
					}
					
					
					/*$ps_array_main['polling_station']= array( 			
					'part_no'=>$dataddddd->part_no,
					'part_name'=>$dataddddd->part_name,
					'ps_no'=>$dataddddd->ps_no,
					'ps_address'=>$dataddddd->ps_address,
					'ps_name_en'=>$dataddddd->ps_name_en,
					'ps_name_v2'=>$dataddddd->ps_name_v2,
					'polling_station_lattitude'=>$dataddddd->lattitude,
					'polling_station_longitude'=>$dataddddd->longitude,	
					'polling_station_pic'=>$imgok	
					);
					$mainarray = array_merge($mainarray, $ps_array_main);	*/
					
					
					$mainarray['part_no']=$dataddddd->part_no;
					$mainarray['part_name']=$dataddddd->part_name;
					$mainarray['ps_no']=$dataddddd->ps_no;
					$mainarray['ps_address']=$dataddddd->ps_address;
					$mainarray['ps_name_en']=$dataddddd->ps_name_en;
					$mainarray['ps_name_v2']=$dataddddd->ps_name_v2;
					$mainarray['polling_station_lattitude']=$dataddddd->lattitude;
					$mainarray['polling_station_longitude']=$dataddddd->longitude;
					
					if(isset($dataddddd->polling_station_photo)){ 
						//$mainarray['polling_station_photo']=$this->imageUrl('thumbs').$dataddddd->polling_station_photo;
					} else { 
						//$mainarray['polling_station_photo']=$this->imageUrl('thumbs')."nopic.png";
					}
					
					
					
				$psPhoto = DB::connection('mysql2')->table('ps_photos')
				->select('name as ps_photo', 'latitude', 'longitude')
				->where('ps_id', $dataddddd->id) // $dataddddd->id
				->where('status', '1')
				->where('deleted', '0')
				->get();
				//echo "<pre>"; print_r($psPhoto); die;
				
				$psarray=array();
				if(count($psPhoto)>0){
				foreach($psPhoto as $key=>$val){ 
					$psarray['ps_photo_details'][$key]=(array)$val;
				}
				} else {
					$mainarray['ps_photo_details']=array();
				}

				
				$arrOKdatamy=array();
				if(count($psarray)>0){
					foreach($psarray['ps_photo_details'] as $key=>$val){ 
					if(isset($val['ps_photo'])){
						$img = $this->imageUrl('thumbs', $st, $ac).$val['ps_photo'];
					} else {
						$img =$this->NoImage();
					}
					$arrOKdatamy[]= array( 					
						  'ps_photo'=>$img,
						  'latitude'=>$val['latitude'],
						  'longitude'=>$val['longitude'],	
						  );
				   }
				   
				   
				  foreach($arrOKdatamy as $key=>$val){ 
					$mainarray['ps_photo_details'][$key]=(array)$val;
					}
				}
					
					
					
					
					$mainarray['electors_male']=$dataddddd->electors_male;
					$mainarray['electors_female']=$dataddddd->electors_female;
					$mainarray['electors_other']=$dataddddd->electors_other;
					$mainarray['no_of_pwd_voters']=$dataddddd->no_of_pwd_voters;
					$mainarray['bus_stand_name']=$dataddddd->bus_stand_name;
					$mainarray['bus_stand_address']=$dataddddd->bus_stand_address;
					$mainarray['bus_stand_longitude']=$dataddddd->bus_stand_longitude;
					$mainarray['bus_stand_latitude']=$dataddddd->bus_stand_latitude;
					if(isset($dataddddd->bus_stand_photo_name)){ 
						$mainarray['bus_stand_photo_name']=$this->imageUrl('thumbs', $st, $ac).$dataddddd->bus_stand_photo_name;
					} else { 
						$mainarray['bus_stand_photo_name']=$this->NoImage();
					}
					
					
					$mainarray['fuel_pump_name']=$dataddddd->fuel_pump_name;
					$mainarray['fuel_pump_contact_name']=$dataddddd->fuel_pump_contact_name;
					$mainarray['fuel_pump_contact_number']=$dataddddd->fuel_pump_contact_number;
					$mainarray['fuel_pump_address']=$dataddddd->fuel_pump_address;
					$mainarray['fuel_pump_timings']=$dataddddd->fuel_pump_timings;
					$mainarray['fuel_types']=$dataddddd->fuel_types;
					$mainarray['fuel_pump_info_latitude']=$dataddddd->fuel_pump_info_latitude;
					$mainarray['fuel_pump_info_longitude']=$dataddddd->fuel_pump_info_longitude;
					if(isset($dataddddd->fuel_pump_info_photo_name)){ 
						$mainarray['fuel_pump_info_photo_name']=$this->imageUrl('thumbs', $st, $ac).$dataddddd->fuel_pump_info_photo_name;
					} else {
						$mainarray['fuel_pump_info_photo_name']=$this->NoImage();
					}
					
					//$mainarray['bus_stand_photo_name']=$dataddddd->bus_stand_photo_name;
					$mainarray['police_station_address']=$dataddddd->police_station_address;
					$mainarray['officer_name']=$dataddddd->officer_name;
					$mainarray['police_station_contact_number']=$dataddddd->police_station_contact_number;
					//$mainarray['police_station_photo_name']=$dataddddd->police_station_photo_name;
					$mainarray['police_station_longitude']=$dataddddd->police_station_longitude;
					$mainarray['police_station_latitude']=$dataddddd->police_station_latitude;
					if(isset($dataddddd->police_station_photo_name)){ 
						$mainarray['police_station_photo_name']=$this->imageUrl('thumbs', $st, $ac).$dataddddd->police_station_photo_name;
					} else {
						$mainarray['police_station_photo_name']=$this->NoImage();
					}
					//echo "<pre>"; print_r($finalarray['bldg_quality_condition']); die;
					
					
					$okarraydata=array();
					
					if(isset($finalarray['bldg_quality_condition'])){
						$okarraydata['bldg_quality_condition']=$finalarray['bldg_quality_condition'];
					} else {
						$okarraydata['bldg_quality_condition']="";
					}
					if(isset($finalarray['ps_less_than_20_sqmtrs'])){
						$okarraydata['ps_less_than_20_sqmtrs']=$finalarray['ps_less_than_20_sqmtrs'];
					} else {
						$okarraydata['ps_less_than_20_sqmtrs']="";
					}
					if(isset($finalarray['is_bldg_dangerous'])){
						$okarraydata['is_bldg_dangerous']=$finalarray['is_bldg_dangerous'];
					} else {
						$okarraydata['is_bldg_dangerous']="";
					}
					if(isset($finalarray['is_govt_bldg'])){
						$okarraydata['is_govt_bldg']=$finalarray['is_govt_bldg'];
					} else {
						$okarraydata['is_govt_bldg']="";
					}
					if(isset($finalarray['is_religious_inst'])){
						$okarraydata['is_religious_inst']=$finalarray['is_religious_inst'];
					} else {
						$okarraydata['is_religious_inst']="";
					}
					if(isset($finalarray['is_school_college'])){
						$okarraydata['is_school_college']=$finalarray['is_school_college'];
					} else {
						$okarraydata['is_school_college']="";
					}
					if(isset($finalarray['is_ground_floor'])){
						$okarraydata['is_ground_floor']=$finalarray['is_ground_floor'];
					} else {
						$okarraydata['is_ground_floor']="";
					}
					if(isset($finalarray['is_separate_entry_exit'])){
						$okarraydata['is_separate_entry_exit']=$finalarray['is_separate_entry_exit'];
					} else {
						$okarraydata['is_separate_entry_exit']="";
					}
					if(isset($finalarray['is_pol_party_office_within_200mtr'])){
						$okarraydata['is_pol_party_office_within_200mtr']=$finalarray['is_pol_party_office_within_200mtr'];
					} else {
						$okarraydata['is_pol_party_office_within_200mtr']="";
					}
					if(isset($finalarray['is_electricity_available'])){
						$okarraydata['is_electricity_available']=$finalarray['is_electricity_available'];
					} else {
						$okarraydata['is_electricity_available']="";
					}
					if(isset($finalarray['is_separate_toilet'])){
						$okarraydata['is_separate_toilet']=$finalarray['is_separate_toilet'];
					} else {
						$okarraydata['is_separate_toilet']="";
					}
					if(isset($finalarray['is_shelter_available'])){
						$okarraydata['is_shelter_available']=$finalarray['is_shelter_available'];
					} else {
						$okarraydata['is_shelter_available']="";
					}
					if(isset($finalarray['is_proper_road_connectivity'])){
						$okarraydata['is_proper_road_connectivity']=$finalarray['is_proper_road_connectivity'];
					} else {
						$okarraydata['is_proper_road_connectivity']="";
					}
					if(isset($finalarray['is_any_obstacle_in_way'])){
						$okarraydata['is_any_obstacle_in_way']=$finalarray['is_any_obstacle_in_way'];
					} else {
						$okarraydata['is_any_obstacle_in_way']="";
					}
					if(isset($finalarray['is_landline_fax_available'])){
						$okarraydata['is_landline_fax_available']=$finalarray['is_landline_fax_available'];
					} else {
						$okarraydata['is_landline_fax_available']="";
					}
					if(isset($finalarray['mobile_connectivity'])){
						$okarraydata['mobile_connectivity']=$finalarray['mobile_connectivity'];
					} else {
						$okarraydata['mobile_connectivity']="";
					}
					if(isset($finalarray['internet_facility'])){
						$okarraydata['internet_facility']=$finalarray['internet_facility'];
					} else {
						$okarraydata['internet_facility']="";
					}
					if(isset($finalarray['is_insurgency_affected'])){
						$okarraydata['is_insurgency_affected']=$finalarray['is_insurgency_affected'];
					} else {
						$okarraydata['is_insurgency_affected']="";
					}
					if(isset($finalarray['is_forest_area'])){
						$okarraydata['is_forest_area']=$finalarray['is_forest_area'];
					} else {
						$okarraydata['is_forest_area']="";
					}
					if(isset($finalarray['is_vulnerable_critical_location'])){
						$okarraydata['is_vulnerable_critical_location']=$finalarray['is_vulnerable_critical_location'];
					} else {
						$okarraydata['is_vulnerable_critical_location']="";
					}
					if(isset($finalarray['permanent_ramp'])){
						$okarraydata['permanent_ramp']=$finalarray['permanent_ramp'];
					} else {
						$okarraydata['permanent_ramp']="";
					}
					if(isset($finalarray['drinking_water'])){
						$okarraydata['drinking_water']=$finalarray['drinking_water'];
					} else {
						$okarraydata['drinking_water']="";
					}
					if(isset($finalarray['adequate_furniture'])){
						$okarraydata['adequate_furniture']=$finalarray['adequate_furniture'];
					} else {
						$okarraydata['adequate_furniture']="";
					}
					if(isset($finalarray['lighting'])){
						$okarraydata['lighting']=$finalarray['lighting'];
					} else {
						$okarraydata['lighting']="";
					}
					if(isset($finalarray['help_desk'])){
						$okarraydata['help_desk']=$finalarray['help_desk'];
					} else {
						$okarraydata['help_desk']="";
					}
					if(isset($finalarray['signage'])){
						$okarraydata['signage']=$finalarray['signage'];
					} else {
						$okarraydata['signage']="";
					}
					if(isset($finalarray['toilet_facility'])){
						$okarraydata['toilet_facility']=$finalarray['toilet_facility'];
					} else {
						$okarraydata['toilet_facility']="";
					}
				
				
				
				$datamfine = array_merge($mainarray, $okarraydata); 
				//$datamfine = array_merge($datamfine, $lastarray);
			}
		}
		$datamfine = $this->removeNullInResultArray($datamfine);	
		return response()->json(['code'=>200, 'status' => 1, 'success' => true, 'message' => 'Polling station details',  'result' => $datamfine]);
		
		
	} else {
	 return response()->json(['code'=>204, 'status' =>0, 'success' => false, 'message' => 'Polling station not found', 'result'=>[]]);	
	}	
		
		
		
		
		
		
	}
	catch (Exception $ex) {
    return response()->json(['code'=>204, 'status' =>1, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
    }
  }	
	
		
	
	
	
	public function amf_emf_image(Request $request)
    {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
				$input=$request->All();			
				
				$validator = Validator::make($request->all(), [
				'id' => 'required',
				'image' => 'required',
				'user_id' => 'required',
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required.'
				]); }
				
				if(!isset($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Table id not found']);
				} 
				
				
				if($input['user_id']!=$uId){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Invalid user id.']);
				}
				
				if(!is_numeric($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Table id not found']);
				} 		
						$img = '';
						if( $input['id'] > 0 ){
									
									$sId = $this->SIdByPsId($uId);
									$AcId = $this->AcIdByPsId($uId); 
									
									$img = $this->uploadImageInDirectroy($request->file('image'),$sId,$AcId);
									
									$thumb=$this->thumb('thumbs',$sId,$AcId).$img;	
									$photo=$this->image('thumbs',$sId,$AcId)."$img";
									
									
									$isUpdate = DB::connection('mysql2')->table('ps_facility_master')
									->where('id', $input['id'])
									->update([
									'image' =>$this->imageUrlSave('image',$sId,$AcId).$img,
									//'updated_at' =>date('Y-m-d H:i:s', time()),
									//'updated_by' =>$this->validateInput($input['user_id']),
									]); 
								if($isUpdate){								
									return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'Image uploaded.',
									'photo'=>$photo, 'thumbnail'=>$thumb]);
								} else {
									return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Image not uploaded.']);
								}
								
						} else {
							return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid table id.']);
						}
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }
	
	
	public function rating_update(Request $request)
    {	 	
		try{	
		$uId = $this->decrypt_token($request);
		if(($uId > 0) && !empty($uId)){ 
				$input=$request->All();				
				$validator = Validator::make($request->all(), [
				'id' => 'required',
				'rating' => 'required',
				'user_id' => 'required',
				]);      
				if($validator->fails()){  return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'All fields is required.'
				]); }
				
				if(!isset($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Table id not found']);
				} 
				
				if( $input['rating']< 1 or  $input['rating'] > 5) { 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Rating value should be between 1 to 5']);
				}
				
				
				//echo $uId; die;
				
				if($input['user_id']!=$uId){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Invalid user id.']);
				}
				//die("alam");
				
				if(!is_numeric($input['id'])){ 
					return response()->json([
					'code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'message'=>'Table id not found']);
				} 
						if( $input['id'] > 0 ){
								
									$isUpdate = DB::connection('mysql2')->table('ps_facility_master')
									->where('id', $input['id'])
									->update([
									'rating' =>$this->validateInput($input['rating']),
									'updated_at' =>date('Y-m-d H:i:s', time()),
									'updated_by' =>$this->validateInput($input['user_id']),
									]);			
								
								if($isUpdate){								
									return response()->json(['code'=>$this->successStatus, 'status' => $this->passStatus, 'success' => true, 'message'=>'Rating updated.']);
								} else {
									return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Rating not updated.']);
								}
								
						} else {
							return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid table id.']);
						}
			} else {
				return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message'=>'Invalid token.']);
		}
	 } catch (Exception $ex) {
        return response()->json(['code'=>$this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
      }
    }	
	function getState($state){
		return DB::connection('mysql2')->table('m_state')->select('ST_NAME')->where('ST_CODE', '=', $state)->value('ST_NAME'); 
	}			
	function getDist($state, $dist){
		return DB::connection('mysql2')->table('m_district')->select('DIST_NAME')->where('ST_CODE', '=', $state)->where('DIST_NO', '=', $dist)->value('DIST_NAME'); 
	}
	function getAc($state, $ac){
		return DB::connection('mysql2')->table('m_ac')->select('AC_NAME')->where('ST_CODE', '=', $state)->where('AC_NO', '=', $ac)->value('AC_NAME'); 
	}
	
	function getStateCode($pid){
		return DB::connection('mysql2')->table('polling_station')->select('st_code')->where('id', '=', $pid)->value('st_code'); 
	}
	function getDistNo($pid){
		return DB::connection('mysql2')->table('polling_station')->select('dist_no')->where('id', '=', $pid)->value('dist_no'); 
	}
	function getACNo($pid){
		return DB::connection('mysql2')->table('polling_station')->select('ac_no')->where('id', '=', $pid)->value('ac_no'); 
	}
	function getPsId($uId){
		$det = DB::connection('mysql2')->table('officer_login')->select('st_code', 'ac_no', 'ps_no')->where('booth_officer_id', '=', $uId)->first(); 
		//echo $det->st_code.'-'.$det->ac_no.'-'.$det->ps_no; die;
		$data =   DB::connection('mysql2')->table('polling_station')->select('id')
		->where('st_code', '=', $det->st_code)
		->where('ac_no', '=', $det->ac_no)
		->where('ps_no', '=', $det->ps_no)
		->value('id'); 		
		if(!empty($data)){
			return $data;
		} else {
			return 0;
		}
	} 
	// GetPsOnLogin
	function getPsIdOnLogin($sid, $ac, $psno){
		$data =   DB::connection('mysql2')->table('polling_station')->select('id')
		->where('st_code', '=', $sid)
		->where('ac_no', '=', $ac)
		->where('ps_no', '=', $psno)
		->value('id'); 		
		if(!empty($data)){
			return $data;
		} else {
			return 0;
		}
	}
	
	
	function NoImage(){
	$_SERVER['HTTP_HOST']; 
	$project = $_SERVER['REQUEST_URI'];
	$esp = explode('/',$project);
	//return 'http://'.$_SERVER['HTTP_HOST'].'/garuda/nopic.png'; // live
	return 'http://'.$_SERVER['HTTP_HOST'].'/garuda/nopic.png'; // Local
	}	
	function imageUrl($dir, $sId, $AcId){
		$dir = $dir;
		//echo $_SERVER['HTTP_HOST']; die;
		$_SERVER['HTTP_HOST']; 
		$project = $_SERVER['REQUEST_URI'];
		$esp = explode('/',$project);
		return 'http://'.$_SERVER['HTTP_HOST'].'/'; 
	}	
	function imageUrlThumb($dir, $sId, $AcId){
		$dir = $dir;
		$_SERVER['HTTP_HOST']; 
		$project = $_SERVER['REQUEST_URI'];
		$esp = explode('/',$project);
		return 'http://'.$_SERVER['HTTP_HOST'].'/';
	}	
	
	function image($dir, $sId, $AcId){
		$dir = $dir;
		$_SERVER['HTTP_HOST']; 
		$project = $_SERVER['REQUEST_URI'];
		$esp = explode('/',$project);
		//return 'http://'.$_SERVER['HTTP_HOST'].'/garuda/uploads/'.$sId.'/'.$AcId.'/img/'; live
		return 'http://'.$_SERVER['HTTP_HOST'].'/garuda/uploads/'.'/'.$sId.'/'.$AcId.'/img/'; // local
	}	
	function thumb($dir, $sId, $AcId){
		$dir = $dir;
		$_SERVER['HTTP_HOST']; 
		$project = $_SERVER['REQUEST_URI'];
		$esp = explode('/',$project);
		//return 'http://'.$_SERVER['HTTP_HOST'].'/garuda/uploads/'.$sId.'/'.$AcId.'/thumb/'; // Live
		return 'http://'.$_SERVER['HTTP_HOST'].'/garuda/uploads/'.'/'.$sId.'/'.$AcId.'/thumb/'; // local
	}	

	function imageUrlSave($dir, $sId, $AcId){
		$dir = $dir;
		//echo $_SERVER['HTTP_HOST']; die;
		$_SERVER['HTTP_HOST']; 
		$project = $_SERVER['REQUEST_URI'];
		$esp = explode('/',$project);
		return 'garuda/uploads/'.$sId.'/'.$AcId.'/img/';
		//return '';
	}	
	function SIdByPsId($uId){
		$det = DB::connection('mysql2')->table('officer_login')->select('st_code', 'ac_no', 'ps_no')->where('booth_officer_id', '=', $uId)->first(); 
		//echo $det->st_code.'-'.$det->ac_no.'-'.$det->ps_no; die;
		if(!empty($det->st_code)){
			return $det->st_code;
		} else {
			return 0;
		}
	}	
	function AcIdByPsId($uId){
		$det = DB::connection('mysql2')->table('officer_login')->select('st_code', 'ac_no', 'ps_no')->where('booth_officer_id', '=', $uId)->first(); 
		//echo $det->st_code.'-'.$det->ac_no.'-'.$det->ps_no; die;
		if(!empty($det->ac_no)){
			return $det->ac_no;
		} else {
			return 0;
		}
	}	
	function createDirectory($sId, $AcId){
		if(!is_dir(public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/img'))) {	
		  $oldmask = umask(0);
		  mkdir(public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/img'), 0777, true);
		  umask($oldmask);
		}
		if(!is_dir(public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/thumb'))) {	
		  $oldmask = umask(0);
		  mkdir(public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/thumb'), 0777, true);
		  umask($oldmask);
		}
	}	
	function replace_null_with_empty_string($array)
	{
			foreach ($array as $key => $value) 
			{
				if(is_array($value))
					$array[$key] = $this->replace_null_with_empty_string($value);
				else
				{
					if (is_null($value))
						$array[$key] = "";
				}
			}
			return $array;
	}
	
	function removeNullInResult($result){
		$mayya = array();
		foreach ($result as $key => $value) {
			$valueto[] = (array)$value;
		}	
		$mayya =$this->replace_null_with_empty_string($valueto);
		return $mayya;
	} 
	
	function removeNullInResultArray($result){
		$mayya = array();			
		$mayya =$this->replace_null_with_empty_string($result);
		return $mayya;
	}
	
	function uploadImageInDirectroy($image, $sId, $AcId){ 
		$photo = $image;		
		if(!is_dir(public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/img'))) {	
		  $oldmask = umask(0);
		  mkdir(public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/img'), 0777, true);
		  umask($oldmask);
		}
		if(!is_dir(public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/thumb'))) {	
		  $oldmask = umask(0);
		  mkdir(public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/thumb'), 0777, true);
		  umask($oldmask);
		}		
		$imagename = time().'.'.$photo->getClientOriginalExtension(); 
		$destinationPath = public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/thumb');
		$thumb_img = Image::make($photo->getRealPath())->resize(100, 100);
		$thumb_img->orientate();
		$thumb_img->save($destinationPath.'/'.$imagename,80);			
		$destinationPath = public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/img');
		$photo->move($destinationPath, $imagename);	
		return $imagename;
	}
	function uploadImageInDirectroyBigSize($image, $sId, $AcId){ 
		$photo = $image;		
		if(!is_dir(public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/img'))) {	
		  $oldmask = umask(0);
		  mkdir(public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/img'), 0777, true);
		  umask($oldmask);
		}
		if(!is_dir(public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/thumb'))) {	
		  $oldmask = umask(0);
		  mkdir(public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/thumb'), 0777, true);
		  umask($oldmask);
		}		
		$imagename = time().'.'.$photo->getClientOriginalExtension(); 
		$destinationPath = public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/thumb');
		$thumb_img = Image::make($photo->getRealPath())->resize(400, 400);
		$thumb_img->orientate();
		$thumb_img->save($destinationPath.'/'.$imagename,80);			
		$destinationPath = public_path('/garuda/uploads/'.$sId.'/'.$AcId.'/img');
		$photo->move($destinationPath, $imagename);	
		return $imagename;
	}	
}
