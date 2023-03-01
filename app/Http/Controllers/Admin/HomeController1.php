<?php
	namespace App\Http\Controllers\Admin;
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use App\Admin;
	use Session;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Redirect;
	use Illuminate\Support\Facades\Hash;
    use Carbon\Carbon;
    use DB;
    use Validator;
    use Config;
    use \PDF;
    use Excel;
    use Mail;
    use App\commonModel;
    use App\Helpers\SmsgatewayHelper;
   // namespace App\Http\Controllers\Auth;
    //use Illuminate\Http\Request;
    //use Auth;  
	
	use App\Helpers\LogNotification;
	
	
class HomeController1 extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
        {

         $this->commonModel = new commonModel();

        }


    public function index(Request $request)
    {  

     
      $users=Session::get('admin_login_details');
	  
	  //dd($users);
	  
        $user = Auth::user();

       if(session()->has('admin_login')){  

            $uid=$users->id;
            $d=$this->commonModel->getunewserbyuserid($uid);
     

				$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
				$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
				$ErrorMessage['MobNo']= $users->officername ?? '';
				$ErrorMessage['applicationType']= 'WebApp';
				$ErrorMessage['Module']= 'ENCORE';
				$ErrorMessage['TransectionType']= 'User';
				$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
				$ErrorMessage['TransectionAction']= 'User_Logged_in';
				$ErrorMessage['TransectionStatus']= 'SUCCESS';
				$ErrorMessage['LogDescription']= 'User Logged In Successfully';
				LogNotification::LogInfo($ErrorMessage);
			
	 
        $role=$d->role_id;
          if($role == 7 || $role == 25){
            
                return Redirect::to('eci/dashboard');
            }
             
            elseif($role == 4 || $role == 23){
                return Redirect::to('pcceo/dashboard');
            }
            elseif($role == 5 || $role == 24){
                 return Redirect::to('pcdeo/dashboard');
            }
            elseif($role == 18  || $role == 22){
                 return Redirect::to('ropc/dashboard');
            }
            elseif($role == 19 || $role == 17 || $role == 20 || $role == 21 ){
                 return Redirect::to('aro/dashboard');
            }
			elseif($role == '26' ){
                 return Redirect::to('eci-agent/dashboard');
            }elseif($role == '27'){
                 return Redirect::to('eci-index/dashboard');
            }elseif($role == 28){
				// return Redirect::to('eci/dashboard');
                return Redirect::to('eci-expenditure/EciExpdashboard');
            }

            else{
				
				$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
				$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
				$ErrorMessage['MobNo']= $users->officername ?? '';
				$ErrorMessage['applicationType']= 'WebApp';
				$ErrorMessage['Module']= 'ENCORE';
				$ErrorMessage['TransectionType']= 'User';
				$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
				$ErrorMessage['TransectionAction']= 'User_Logged_in';
				$ErrorMessage['TransectionStatus']= 'FAILURE';
				$ErrorMessage['LogDescription']= 'User login failed';
				LogNotification::LogInfo($ErrorMessage);	
				
				
                return Redirect::to('/officer-login');
            }
           
        }
        else {  

				$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
				$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
				$ErrorMessage['MobNo']= $users->officername ?? '';
				$ErrorMessage['applicationType']= 'WebApp';
				$ErrorMessage['Module']= 'ENCORE';
				$ErrorMessage['TransectionType']= 'User';
				$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
				$ErrorMessage['TransectionAction']= 'User_Logged_in';
				$ErrorMessage['TransectionStatus']= 'FAILURE';
				$ErrorMessage['LogDescription']= 'User login failed';
				LogNotification::LogInfo($ErrorMessage);

		
                 return redirect('/officer-login');
            }
        
    }
     
 
    public function logout(Request $request){
		
		
		try{
          \DB::table("officer_login")->where("id",\Auth::id())->update([
            "login_flag" => 0
          ]);
        }catch(\Exception $e){
		}
          unset($_COOKIE['cdatabase']);
          setcookie('cdatabase', '', time() - 0);
		     
		
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
                // \Artisan::call('cache:clear');
                // \Artisan::call('view:clear');
                // \Artisan::call('config:cache');
               return Redirect::to('/officer-login');


            
        }
    public function changepassword() {
         if(Auth::check()){
                $user = Auth::user();
                $uid=$user->officer_id;
                $d=$this->commonModel->getuserbyuserid($uid);
                return view('changepassword', ['user_data' => $d]);
             }
            else {
                  return redirect('/officer-login');
                  }
         
        }
    public function changeprofile() {
            if(Auth::check()){
                $user = Auth::user();
                $uid=$user->officer_id;
                $d=$this->commonModel->getuserbyuserid($uid);
                
                return view('changeprofile', ['user_data' => $d]);
             }
            else {
                  return redirect('/officer-login');
                  }
            
        }
    public function validatechangeprofile(Request $request)
            {
                if(Auth::check()){
                    $user = Auth::user();
                    $uid=$user->officer_id; 
                    $pass=$user->password; 
            $this->validate(
                $request, 
                    [
                      'name' => 'required|min:8',
                      'phone_no' => 'required|min:10|numeric',
                      'email' => 'required|email',
                    ],
                    [
                      'name.required' => 'Please enter your name',
                      'name.min' => 'Name must be at least 8 characters.', 
                      'phone_no.required' => 'Please enter your valid mobile number', 
                      'phone_no.min' => 'Mobile number must be 10 digits',
                      'phone_no.numeric' => 'Please enter your valid mobile number',
                      'email.email' => 'Please add valid email address',
                      'email.required' => 'Please add valid email address',
                    ]);

                  $name = trim($request->input('name'));
                  $phone_no = trim($request->input('phone_no')); 
                  $email = trim($request->input('email'));  
                  
                   $profile_master = array('name'=>$name,'email'=>$email,'Phone_no'=>$phone_no); 
                   $profile = array('name'=>$name,'email'=>$email); 
                     $i = DB::table('officer_master')->where('officer_id', $uid)->update($profile_master);
                     $i = DB::table('officer_login')->where('officer_id', $uid)->update($profile);
                      //  return Redirect::to('logout'); 
                      \Session::flash('success_admin', 'Profile successfully change'); 
                      return Redirect::to('changeprofile');
                   
                 
            }
            else {
                  return redirect('/officer-login');
                  }

            }
        


    public function validatechangepassword(Request $request)
            {
                if(Auth::check()){
                    $user = Auth::user();
                    $uid=$user->user_id; 
                    $pass=$user->password; 
                   // dd($user); 
            $this->validate(
                $request, 
                    [
                      'cpassword' => 'required|min:8',
                      'newpassword' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%@]).*$/',
                      'confpassword' => 'required|same:newpassword',
                     
                    ],
                    [
                      'cpassword.required' => 'Please enter your current password',
                      'cpassword.min' => 'Current password must be at least 8 characters.', 
                      'newpassword.required' => 'Please enter valid New Password',
                      'newpassword.min' => 'New Password must be at least 8 characters.',
                      'newpassword.regex' => 'New Passwords must be alphanumeric with one special character',
                      'confpassword.required' => 'Please enter valid Confirm Password',
                      'confpassword.same' => 'Password and Confirm Password must match',
                    ]);
                  $cpassword = trim($request->input('cpassword'));
                  $newpassword = trim($request->input('newpassword'));  
                  if(Hash::check($cpassword, $pass) ){    
                            $change_pass = array('password'=>Hash::make($newpassword),); 
                            $i = DB::table('officer_login')->where('officer_id', $uid)->update($change_pass);
                                return Redirect::to('logout'); 
                      }
                   else {
                        
                        \Session::flash('old_password', 'Old Password not match'); 
                            return Redirect::to('changepassword');
                    }
                 
            }
            else {
                  return redirect('/officer-login');
                  }

            }
      public function refreshCaptcha()
            {    
                return response()->json(['captcha'=> captcha_img()]);
            }
}