<?php
	
	namespace App\Http\Controllers\API;
	
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use App\adminmodel\OfficerApiModel;
	use Illuminate\Support\Facades\Response;
	use App\OfficerApiModel as AppOfficerApiModel;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Crypt;
	use DB;
	use App\commonModel;
	
	class OfficerController extends Controller
	{
        public function __construct() {
			$this->commonModel = new commonModel();
			
		}
		public $successStatus = 200;
        public $createdStatus = 201;
        public $nocontentStatus = 204;
        public $notmodifiedStatus = 304;
        public $badrequestStatus = 400;
        public $unauthorizedStatus = 401;
        public $notfoundStatus = 404;
        public $intservererrorStatus = 500;
		
        public function authenticate(Request $request)
		{  
			$username = trim($request['username']);
			$password = trim($request['password']);
			$password= hash('sha256',$password);
            $officer_login = OfficerApiModel::where(['officername' => $username, 'password' => $password])->first();
            if (!empty($officer_login))
            {
                $user_data=Auth()->guard('admin')->loginUsingId($officer_login->id);
				$d = OfficerApiModel::find($user_data->id, ['id', 'officername', 'designation', 'placename',
				'name','st_code','dist_no','ac_no','pc_no', 'Phone_no','email','officerlevel']);
				$desig=$d['officerlevel']; $lst=$d['st_code']; $lpc=$d['pc_no'];$lac=$d['ac_no'];
				
				if($lpc==0)
				{
					$lpc=1;
				}
				if($lac==0)
				{
					$lac=1;
				}
				if($lst=='')
				{
					$lst="S01";
				}
				
				//print_r($desig);die;
				if($desig=="AC" || $desig=="PC" || $desig=="CEO")
				{
					
					$pidt=DB::table('pd_scheduledetail')->select('scheduleid')->where('st_code',$lst)->where('pc_no', $lpc)->first();
					$d['phase']=0;
					//print_r($pidt->scheduleid);die;
				}
				else
				{
					$d['phase']=0;
				}
				//print_r("\n\n AC = $lac, PC = $lpc, State = $lst \n\n\n");
				$d['ac_name']=$this->commonModel->getacbyacno($lst,$lac)->AC_NAME;
				$d['pc_name']=$this->commonModel->getpcbypcno($lst,$lpc)->PC_NAME;
				$d['st_name']=$this->commonModel->getstatebystatecode($lst)->ST_NAME;
				$token = $d->createToken('MyApp')->accessToken;
				$id = $user_data->id;
				$officername = $user_data->officername;
				
				$array = array('accesstoken'=> $token);
				DB::table('officer_login')->where([['id' , $id],['officername' , $officername]])->update($array);
				$success['success'] = true;
				$success['message'] = 'You Are Successfully Logged In';
				$success['userdetails'] = $d;
				$success['token'] = $token;
				return response()->json($success, $this->successStatus);
			}
			else{
                $success['success'] = false;
                $success['message'] = 'Invalid user please check the username or password';
                return response()->json($success, $this->successStatus);
			}
		}
		
        public function logout(Request $request) {
            $username = trim($request['username']);
            $password = trim($request['password']);
			
            if (auth()->guard('admin')->attempt(['officername' => $username, 'password' => $password,'is_active'=>1]))
            {
                $user_data=Auth()->guard('admin')->user();
                $d = OfficerApiModel::find($user_data->id, ['id', 'officername', 'designation', 'placename',
				'name','st_code','dist_no','ac_no','pc_no', 'Phone_no','email','officerlevel']);
                $token = "";
                $id = $user_data->id;
                $officername = $user_data->officername;
                
                $array = array('accesstoken'=> $token);
                DB::table('officer_login')->where([['id' , $id],['officername' , $officername]])->update($array);
                $success['success'] = true;
                $success['message'] = 'You Are Successfully Logged Out';
                return response()->json($success, $this->successStatus);
			}
			else{
				$success['success'] = false;
				$success['message'] = 'Invalid user please check the username or password';
				return response()->json($success, $this->successStatus);
			}
		}
		
		///////////////////////Code by ChanderKant ////////////////////
		///   Encrypted Login and Logout with BruteForce Protection  //
		///////////////////////////////////////////////////////////////
		
	public function loginSecure(Request $request)
		{  
			$username = trim($request['username']);
			$password = trim($request['password']);
			$udata=DB::table('officer_login')->select('id','updated_at','no_of_attempt','accesstoken')->where('officername', $username)->first();
			if(isset($udata->id))
			{
				if(isset($udata->updated_at))
				$lutime= \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $udata->updated_at);
				else
				$lutime= \Carbon\Carbon::now();
				$ctime=\Carbon\Carbon::now();
				
				if(($udata->no_of_attempt < 5) || ($lutime->diffInSeconds($ctime)>60))
				{
					if (auth()->guard('admin')->attempt(['officername' => $username, 'password' => $password,'is_active'=>1]))
					{
						
						$user_data=Auth()->guard('admin')->user();
						$d = OfficerApiModel::find($user_data->id, ['id', 'officername', 'designation', 'placename',
						'name','st_code','dist_no','ac_no','pc_no', 'Phone_no','email','officerlevel']);
						$desig=$d['officerlevel']; $lst=$d['st_code']; $lpc=$d['pc_no'];$lac=$d['ac_no'];
						if($d['no_of_attempt']<5)
						{
							if($lpc==0)
							{
								$lpc=1;
							}
							if($lac==0)
							{
								$lac=1;
							}
							if($lst=='')
							{
								$lst="S01";
							}
							
							//print_r($desig);die;
							if($desig=="AC" || $desig=="PC" || $desig=="CEO")
							{
								
								$pidt=DB::table('pd_scheduledetail')->select('scheduleid')->where('st_code',$lst)->where('pc_no', $lpc)->first();
								//$d['phase']=$pidt->scheduleid;
								//print_r($pidt->scheduleid);die;
								$d['phase']=0;
							}
							else
							{
								$d['phase']=0;
							}
							//print_r("\n\n AC = $lac, PC = $lpc, State = $lst \n\n\n");
							$d['ac_name']=$this->commonModel->getacbyacno($lst,$lac)->AC_NAME;
							$d['pc_name']=$this->commonModel->getpcbypcno($lst,$lpc)->PC_NAME;
							$d['st_name']=$this->commonModel->getstatebystatecode($lst)->ST_NAME;
							$id = $user_data->id;
							$officername = $user_data->officername;
							if(($desig=="CEO") || ($desig=="ECI"))
							{
								$token = $udata->accesstoken;
							}
							else
							{
								$token = $d->createToken('MyApp')->accessToken;
							}
							$array = array('no_of_attempt' => 0, 'accesstoken'=> $token, 'updated_at' => now()->toDateTimeString());
							DB::table('officer_login')->where([['id' , $id],['officername' , $officername]])->update($array);
							$success['success'] = true;
							$success['message'] = 'You Are Successfully Logged In';
							$success['userdetails'] = $d;
							$success['token'] = $token;
							return response()->json(Crypt::encryptString(json_encode($success)), $this->successStatus);
							//return response()->json($success, $this->successStatus);
						}
						
					}
					else
					{
						$success['success'] = false;
						$array = array('no_of_attempt' => $udata->no_of_attempt + 1, 'updated_at' => now()->toDateTimeString());
						DB::table('officer_login')->where([['id' , $udata->id],['officername' , $username]])->update($array);
						$success['message'] = 'Invalid user please check the username or password';
						return response()->json(Crypt::encryptString(json_encode($success)), $this->successStatus);
						//return response()->json($success, $this->successStatus);
					}
				}
				else
				{
					$success['success'] = false;
					$success['message'] = 'Too many faield attempts. Please try after some time';
					$success['attempt'] = $udata->no_of_attempt;
					$success['last'] = $udata->updated_at;
					$success['time'] = $lutime->diffInSeconds($ctime);
					return response()->json(Crypt::encryptString(json_encode($success)), $this->successStatus);
					//return response()->json($success, $this->successStatus);
				}
				
			}
			else
			{
				$success['success'] = false;
				$success['message'] = 'Invalid user please check the username or password';
				return response()->json(Crypt::encryptString(json_encode($success)), $this->successStatus);
				//return response()->json($success, $this->successStatus);
			}
		}
		
		public function logoutSecure(Request $request) {
			$username = trim($request['username']);
			$password = trim($request['password']);
			$udata=DB::table('officer_login')->select('id','updated_at','no_of_attempt','accesstoken')->where('officername', $username)->first();
			if (auth()->guard('admin')->attempt(['officername' => $username, 'password' => $password,'is_active'=>1]))
			{
				$user_data=Auth()->guard('admin')->user();
				$d = OfficerApiModel::find($user_data->id, ['id', 'officername', 'designation', 'placename',
				'name','st_code','dist_no','ac_no','pc_no', 'Phone_no','email','officerlevel']);
				if(($desig=="CEO") || ($desig=="ECI"))
				{
					$token = $udata->accesstoken;
				}
				else
				{
					$token = "";
				}
				$id = $user_data->id;
				$officername = $user_data->officername;
				
				$array = array('accesstoken'=> $token);
				DB::table('officer_login')->where([['id' , $id],['officername' , $officername]])->update($array);
				$success['success'] = true;
				$success['message'] = 'You Are Successfully Logged Out';
				return response()->json(Crypt::encryptString(json_encode($success)), $this->successStatus);
				//return response()->json($success, $this->successStatus);
			}
			else{
			$success['success'] = false;
				$success['message'] = 'Invalid user please check the username or password';
				return response()->json(Crypt::encryptString(json_encode($success)), $this->successStatus);
				//return response()->json($success, $this->successStatus);
			}
		}
		
			
			
		}
		