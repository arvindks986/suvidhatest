<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use Auth, Session, Cookie;

class OfficerModel extends Model
{
    protected $table = 'officer_login';
	
    public static function check_otp_time($request){
        $date1 = date('Y-m-d H:i:s');
        $sql = OfficerModel::select('otp_time');
     
        if($request->has('mobile')){
            $sql->where('Phone_no',$request->mobile);
        }
        if($request->has('email')){
            $sql->where('email',$request->email);
        }
        $object = $sql->first();
        if(!$object){
            return false;
        }
        $date2 = $object->otp_time;
        $to_time = strtotime($date2);
        $from_time = strtotime($date1);
        return abs($to_time - $from_time);

    }

	public static function update_otp_by_mobile($mobile,$otp){
        $object = OfficerModel::where('Phone_no', $mobile)->first();
        $object->mobile_otp = $otp;
        $object->otp_time  = NOW();
        $object->otp_verify_by_string = OfficerModel::generate_unique_string($object->id);
        return $object->save();
    }

    public static function update_otp_by_email($email,$otp){
        $object = OfficerModel::where('email', $email)->first();
        $object->mobile_otp = $otp;
        $object->otp_time   = NOW();
        $object->otp_verify_by_string = OfficerModel::generate_unique_string($object->id);
        return $object->save();
    }
	
    public static function generate_unique_string($id){
        $length = 20;
        $token = substr_replace(str_random($length). time(), $id, 10, 0);
        Session::put('forgot-token',$token);
        return $token;
    }

    public static function get_user_by_toke($token){
        $object = OfficerModel::where('otp_verify_by_string', $token)->first();
        if(!$object){
            return false;
        }
        return $object->toArray();
    }

    public static function get_user($data = array()){
        $sql = OfficerModel::select();
        if(!empty($data['token'])){
            $sql->where('otp_verify_by_string', $data['token']);
        }
        if(!empty($data['mobile'])){
            $sql->where('Phone_no', $data['mobile']);
        }
        if(!empty($data['otp'])){
            $sql->where('mobile_otp', $data['otp']);
        }
        $object = $sql->first();
        if(!$object){
            return false;
        }
        return $object->toArray();
    }

    public static function get_users($data = array()){
        $sql = OfficerModel::leftJoin('m_state','m_state.ST_CODE','=','officer_login.st_code')->select('officer_login.*','m_state.ST_NAME as state_name');
        if(!empty($data['token'])){
            $sql->where('officer_login.otp_verify_by_string', $data['token']);
        }
        if(!empty($data['mobile'])){
            $sql->where('officer_login.Phone_no', $data['mobile']);
        }
        if(!empty($data['otp'])){
            $sql->where('officer_login.mobile_otp', $data['otp']);
        }
        if(!empty($data['st_code'])){
            $sql->where('officer_login.st_code', $data['st_code']);
        }
        if(!empty($data['role_id']) && count($data['role_id'])>0){
            $sql->whereIn('officer_login.role_id', $data['role_id']);
        }
        $object = $sql->orderByRaw('officer_login.st_code, officer_login.pc_no ASC')->get();
        if(!$object){
            return [];
        }
        return $object->toArray();
    }


    public static function update_password($data = array()){
        $object = OfficerModel::where('otp_verify_by_string', $data['token'])->first();
        $object->mobile_otp = '';
        $object->otp_time   = NOW();
        $object->password   = bcrypt($data['password']);
        $object->otp_verify_by_string = '';
        return $object->save();
    }

    public static function update_pin($data = array()){
        date_default_timezone_set('Asia/Kolkata');
        $datetime = date("Y-m-d H:i:s");
        $object = OfficerModel::where('id', Auth::id())->first();
        $object->two_step_pin       = bcrypt($data['pin']);
        $object->two_step_pin_flag  = 1;
        $object->two_step_pin_time  = $datetime;
        return $object->save();
    }

    public static function update_pin_by_state($data = array()){
        date_default_timezone_set('Asia/Kolkata');
        $datetime = date("Y-m-d H:i:s");
        if(Auth::user()->role_id != '7'){
            $object = OfficerModel::where('st_code',Auth::user()->st_code)->find($data['user_id']);
        }else{
            $object = OfficerModel::find($data['user_id']);
        }
        $object->two_step_pin       = bcrypt($data['pin']);
        $object->two_step_pin_flag  = 1;
        $object->two_step_pin_time  = $datetime;
        return $object->save();
    }

    public static function update_password_by_eci($data = array()){
        date_default_timezone_set('Asia/Kolkata');
        $datetime = date("Y-m-d H:i:s");
        $object = OfficerModel::find($data['user_id']);
        $object->password       = bcrypt($data['password']);
        return $object->save();
    }

    public static function update_via_web($data = array()){
        $object = OfficerModel::find(Auth::id());
        if(!($object && \Hash::check($data['old_pin'], $object->two_step_pin))){
            return false;
        }
        $object->two_step_pin = bcrypt($data['pin']);
        $object->two_step_pin_flag  = 1;
        return $object->save();
    }

    public static function update_profile_password($data = array()){
        $object = OfficerModel::find(Auth::id());
        if(!$object){
            return false;
        }
        $object->password = bcrypt($data['password']);
        return $object->save();
    }

    public static function logout(){

        $object = OfficerModel::where('id', Auth::id())->first();
        $object->login_flag  = 0;
        $object->save();

        Auth::logout();
        Auth::guard('admin')->logout();
        Session::forget('key');
        Session::flush();
        Session::regenerate();
        Session::flash('key', 'value');
        Session::reflash();

        return true;

    }

    public static function authenticate($data = array()){
        if(!isset($data['password']) && empty($data['password'])){
            return OfficerModel::where($data)->count();
        }else{
            return Auth::guard('admin')->validate($data);
        }
    }

	public static function attempt_login($data = array()){
        $pin = $data['two_step_pin'];
        unset($data['two_step_pin']);
		$userob =  OfficerModel::where([
                'officername'  => $data['officername'],
                'is_active'   => $data['is_active']
            ])->first();
        if(!isset($data['password']) && empty($data['password'])){
            $user =  OfficerModel::where($data)->first();
            if($user){ 
				if(!($userob && \Hash::check($pin, $userob->two_step_pin))){
                return false;
				}else{
					  $user = Auth::guard('admin')->loginUsingId($user->id);
				}
            }
        }else{
            if(!($userob && \Hash::check($pin, $userob->two_step_pin))){
                return false;
            }else{
                $user = Auth::guard('admin')->attempt($data);
            }
        }

        if(isset($user) && $user){
            $sessionid  = session()->getId();
            $user_data  = Auth()->guard('admin')->user();
            Session::put('admin_login_details', $user_data);
            if($user_data && $user_data->id){
                Session::put('logged_DB_id', $user_data->id);
            }
            Session::put('admin_login',true);

            $concurrent_token = str_random(30).Auth::id().time();
            Session::put('valid_concurrent_token', $concurrent_token);

            if($user_data && $user_data->id){
                $object         = OfficerModel::find($user_data->id);
                $object->ip     = \Request::ip();
                $object->token      =  $concurrent_token;
                $object->login_flag = 1;
                $object->save();
            

                $history_data = [
                    'officer_id'        =>  $user_data->id, 
                    'officer_login_id'  =>  $user_data->officername,
                    'ipaddress'         =>  \Request::ip(), 
                    'login_date'        =>  date('Y-m-d H:i:s'),
                    'session_id'        =>  Session::getId(),
                ];       
                \DB::table('officer_history')->insert($history_data);

            }

            return true;

        } else{

            return false;

        }
    }


    public static function remove_concurrent_session(){
        if(Auth::user() && (Auth::user()->role_id == '7' || Auth::user()->role_id == '4'  || Auth::user()->role_id == '25')){
            return 0;
        }
        $is_logout = 0;
        $setting = \App\models\Admin\SettingModel::get_setting_cache();
        if(!empty($setting['concurrent_login']) && $setting['concurrent_login'] == '1' && Auth::user() && Session::has('valid_concurrent_token') && Auth::user()->role_id != 7){
            if(Auth::user()->token != Session::get('valid_concurrent_token')){
                $is_logout = 1;
                \App\models\Admin\OfficerModel::logout();
            }
        }
        return $is_logout;

    }

    public static function skip_password_network(){
        

            $setting = \App\models\Admin\SettingModel::get_setting_cache();
            if(!empty($setting['skip_password_network']) && $setting['skip_password_network'] == 1){
                $skip_password_network = 1;
            }
            
            if(isset($_COOKIE['client_ip'])){
                $client_ip = $_COOKIE['client_ip'];
            }
          
            $ip = \Request::ip();

            if(isset($skip_password_network) && isset($client_ip) && $client_ip == $ip){
                return true;
            }else{
                return false;
            }
            
        
    }

    public static function check_pin($data = array()) {
        $user_id  = Auth::id();
        $object = \App\models\Admin\OfficerModel::find($user_id);
        if($object && \Hash::check($data['pin'], $object->two_step_pin)){
            $object->password = bcrypt($data['password']);
            return $object->save();
        }else{
            return false;
        }
    }

}
