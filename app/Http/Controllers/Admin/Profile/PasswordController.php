<?php namespace App\Http\Controllers\Admin\Profile;
    
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Session;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Redirect;
    use Illuminate\Support\Facades\Hash;
    use Carbon\Carbon;
use DB, Validator;
use App\Http\Requests\Admin\Profile\PasswordUpdateRequest;
	use App\Helpers\LogNotification;

class PasswordController extends Controller
  {

    public $action = "profile/password/update";

    public function __construct(){   
        $this->middleware(['auth:admin','auth']);
    }

    public function index(Request $request){

        $data                   = [];
        $data['buttons']        = [];
        $data['heading_title']  = "Change Password";
        $data['action']         = url($this->action);
        $data['user_data']      = Auth::user();
        return view('admin.common.password', $data);

    }

    public function update(PasswordUpdateRequest $request){

        if(Auth::user()->role_id == '7'){
            Session::flash('error_mes', 'Please enter pin.');
            return Redirect::back();
        }
        $data = [];
        $data = $request->all();
        try{
            $user_pass          = Auth::user()->password;
            $old_password       = $request->old_password;
            $decoded_password   = Hash::check($old_password, $user_pass);
            if($user_pass  != $decoded_password){
                Session::flash('error_mes', 'Old password mismatched.');
                return Redirect::back();
            }
            
            $result = \App\models\Admin\OfficerModel::update_profile_password($data);
            if(!$result){
                Session::flash('error_mes', 'Old password mismatched.');
                return Redirect::back();
            }
        }catch(\Exception $e){
            Session::flash('error_mes', 'We have encounted an error. Please try again.');
            return Redirect::back();
        }
        Session::flash('success_mes', 'Password has been updated successfully.');
		
		
		$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
		$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
		$ErrorMessage['MobNo']= $user_details->officername ?? '';
		$ErrorMessage['applicationType']= 'WebApp';
		$ErrorMessage['Module']= 'ENCORE';
		$ErrorMessage['TransectionType']= 'User';
		$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
		$ErrorMessage['TransectionAction']= 'Password_update';
		$ErrorMessage['TransectionStatus']= 'SUCCESS';
		$ErrorMessage['LogDescription']= 'Password has been updated successfully.';
		LogNotification::LogInfo($ErrorMessage);
		
		
        return Redirect::back();
    }

    public function update_by_ajax(Request $request){
        if(!Auth::user()){
            return \Response::json([
                'status'            => 0,
                'login_required'    => true,
                'message'           => "Please login to continue"
            ]);
        }

        $validator = Validator::make($request->all(),[
            'old_password'           => 'required',
            'password'              => 'required|confirmed|password',
            'password_confirmation' => 'required|password'
        ],[]);

        if ($validator->fails()){
            return \Response::json([
                'status' => false,
                'errors' => $validator->errors()->getMessageBag()
            ]);
        }

        $user_pass          = Auth::user()->password;
            $old_password       = $request->old_password;
            $decoded_password   = Hash::check($old_password, $user_pass);
            if($user_pass  != $decoded_password){
                return \Response::json([
                    'status'    => false,
                    'errors'   => [
                        "old_password" => ["Old password mismatched."]
                        ]
                ]);
            }

        Session::put("new_password",$request->password);
        return \Response::json([
            'status'    => true,
            'message'   => "Authenticate successfully"
        ]);
            

    }

    public function validate_pin(Request $request) {
        if(!Auth::user()){
            return \Response::json([
                'status'            => 0,
                'login_required'    => true,
                'message'           => "Please login to continue"
            ]);
        }

        
        $validator = Validator::make($request->all(),[
            'pin' => 'required|pin',
        ],[
            'regex'     => 'Please enter only numeric value.',
            'numeric'   => 'Please enter only numeric value.',
            'pin'       => 'Please enter a valid 4 digit number.'
        ]);

        if ($validator->fails()){
            return \Response::json([
                'status' => false,
                'errors' => $validator->errors()->getMessageBag()
            ]);
        }

        if(!Session::has('new_password')){
            return \Response::json([
                'status' => false,
                'login_required'    => true,
                'message'           => "Please try again."
            ]);
        }

        $data['pin'] = $request->pin ;
        $data['password'] = Session::get("new_password");
        $check_result = \App\models\Admin\OfficerModel::check_pin($data);
        if(!$check_result){
            return \Response::json([
                'status'    => false,
                'errors'   => [
                    "pin" => ["Old pin mismatched."]
                    ]
            ]);
        }
        Session::forget('new_password');
            return \Response::json([
                'status'    => true,
                'message'   => "Authenticate successfully"
            ]);
       
    }
   
    
} // end