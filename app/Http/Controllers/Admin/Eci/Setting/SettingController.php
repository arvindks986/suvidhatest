<?php namespace App\Http\Controllers\Admin\Eci\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session;
use Illuminate\Support\Facades\Hash;
use \PDF;
use App\models\Admin\SettingModel;
use App\Http\Requests\Admin\Setting\SettingRequest;

class SettingController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action        = 'eci/setting/setting/save';
  public $action_broadcast  = 'eci/setting/broadcast/save';
  public $view_path     = "admin.pc.eci";

  public function __construct(){
  
    if(!Auth::user()){
      return redirect('/officer-login');
    }
  }

  public function index(Request $request){

    try{ 
      $data = [];
      $request_array = []; 

      //set title
      $title_array  = [];
      $data['heading_title'] = "Setting";

      $data['filter_buttons'] = $title_array;
      $data['states'] = [];
      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['action']     = url($this->action);
      $object             = SettingModel::get_records('setting'); 
   
      if($request->old('two_step')){
        $data['two_step']  = $request->old('two_step');
      }else if(isset($object) && !empty($object['two_step'])){
        $data['two_step']  =  $object['two_step'];
      }else{
        $data['two_step']  =  '';
      }

      if($request->old('auto_logout_after')){
        $data['auto_logout_after']  = $request->old('auto_logout_after');
      }else if(isset($object) && !empty($object['auto_logout_after'])){
        $data['auto_logout_after']  =  $object['auto_logout_after'];
      }else{
        $data['auto_logout_after']  =  '';
      }

      if($request->old('two_step_login')){
        $data['two_step_login']  = $request->old('two_step_login');
      }else if(isset($object) && !empty($object['two_step_login'])){
        $data['two_step_login']  =  $object['two_step_login'];
      }else{
        $data['two_step_login']  =  '';
      }

      if($request->old('concurrent_login')){
        $data['concurrent_login']  = $request->old('concurrent_login');
      }else if(isset($object) && !empty($object['concurrent_login'])){
        $data['concurrent_login']  =  $object['concurrent_login'];
      }else{
        $data['concurrent_login']  =  '';
      }

      if($request->old('skip_password_network')){
        $data['skip_password_network']  = $request->old('skip_password_network');
      }else if(isset($object) && !empty($object['skip_password_network'])){
        $data['skip_password_network']  =  $object['skip_password_network'];
      }else{
        $data['skip_password_network']  =  '';
      }

      $data['user_data']  =   Auth::user();
      $data['heading_title_with_all'] = $data['heading_title'];

      return view($this->view_path.'.setting.setting_form', $data);

    }catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }
  }

  public function save(SettingRequest $request){
    $data           = array();
    DB::beginTransaction();
    try{
      SettingModel::add_record('setting',$request);  
      DB::commit();  
    }
    catch(\Exception $e){
      DB::rollback();
      Session::flash('error_mes',"Please try again.");  
      
    } 
    SettingModel::generate_cache();
    Session::flash('success_mes',"Setting has been updated.");   
    return Redirect::back();
 
  }
  
  public function broadcast(Request $request){

     try{
      $data = [];
      $request_array = []; 

      //set title
      $title_array  = [];
      $data['heading_title'] = "Broadcast Message to officers";

      $data['filter_buttons'] = $title_array;
      $data['states'] = [];
      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['action']     = url($this->action_broadcast);
      $object             = SettingModel::get_first_result('config'); 
   
      if($request->old('message')){
        $data['message']  = $request->old('message');
      }else if(isset($object) && !empty($object['message'])){
        $data['message']  =  $object['message'];
      }else{
        $data['message']  =  '';
      }

      $data['user_data']  =   Auth::user();
      $data['heading_title_with_all'] = $data['heading_title'];

      return view($this->view_path.'.setting.broadcast_form', $data);

    }catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }
  }


  public function save_broadcast(Request $request){
    $data           = array();
    DB::beginTransaction();
    try{
      SettingModel::add_record('config',$request);  
      DB::commit();  
    }
    catch(\Exception $e){
      DB::rollback();
      Session::flash('error_mes',"Please try again.");  
      
    } 
    Session::flash('success_mes',"Setting has been updated.");   
    return Redirect::back();
  }

 

  

}  // end class