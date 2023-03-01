<?php
namespace App\Http\Controllers\Admin\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
//use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel;
//use Excel;
use Validator;
use Config;
use \PDF;
use App\commonModel;  
use App\adminmodel\CEOModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\adminmodel\PCCeoReportModel;

//POLL TURNOUT MODELS
use App\models\Admin\PollDayModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\PcModel;
use App\models\Admin\AcModel;
use App\models\Admin\MissedTurnoutModel;

use App\Http\Controllers\Admin\Eci\Report\PolldayTurnoutController;
use App\Http\Controllers\Admin\Eci\Report\MissingTurnoutController;
use App\Http\Controllers\Admin\Eci\Report\PolldayCloseOfPollController;
use App\Http\Controllers\Admin\Eci\Report\PolldayEndOfPollController;
 

//INCLUDING CLASSES
use App\Classes\xssClean;
use App\Classes\secureCode;

//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;

  date_default_timezone_set('Asia/Kolkata');

class CountingNotificationController extends Controller
{   

     //USING TRAIT FOR COMMON FUNCTIONS
    use CommonTraits;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
    //date_default_timezone_set('Asia/Kolkata');    
        $this->middleware(['auth:admin','auth']);
        $this->commonModel = new commonModel();
        $this->pcceoreportModel = new PCCeoReportModel();
        $this->PolldayTurnoutModel = new PolldayTurnoutController;
        $this->MissingTurnoutModel = new MissingTurnoutController;
        $this->CloseOfPollModel = new PolldayCloseOfPollController;
        $this->EndOfPollModel = new PolldayEndOfPollController;
		
    }
/**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    protected function guard(){
        return Auth::guard();
    }


    //PC CEO NOTIFICATION STARTS
    public function notification(Request $request){  
      //PC CEO COUNTING RESULT DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $cur_time    = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;


             return view('admin.pc.ceo.notification.notification',['user_data' => $user_data]); 
                              
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //PC CEO NOTIFICATION TRY CATCH BLOCK ENDS
        
    }
    //PC CEO NOTIFICATION FUNCTION ENDS


    //PC CEO NOTIFICATION CURL STARTS
    public function notificationCurl(Request $request){  
      //PC CEO NOTIFICATION CURL TRY CATCH BLOCK STARTS
       

     $stcode  =$request->stcode;
     $token  =$request->currentToken;

     Auth::user()->id;

     if($request->has('pcno')){
      //RO
      $pcno = $request->pcno;
      $url = 'https://iid.googleapis.com/iid/v1/'.$token.'/rel/topics/RO_'.$stcode.'_'.$pcno;
      

     }else if($request->has('acno') && $request->has('pcno')){
      
      //ARO
      $acno = $request->acno;
      $pcno = $request->pcno;
      $url = 'https://iid.googleapis.com/iid/v1/'.$token.'/rel/topics/ARO_'.$stcode.'_'.$pcno.'_'.$acno;
     
     }else{
      //CEO
      $url = 'https://iid.googleapis.com/iid/v1/'.$token.'/rel/topics/CEO_'.$stcode;
     }   

    $MY_KEY = 'AIzaSyA35mgXSz16ioGBDk_LW085fxhL77t7CH8';

    $headers = array (
                    'Authorization: key=' . $MY_KEY,
                    'Content-Type: application/json',
                    'Content-Length: 0'
                  );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
   // echo "The Result : " . $result;die;

    return $result;
      
    //PC CEO NOTIFICATION CURL TRY CATCH BLOCK ENDS
        
    }
    //PC CEO NOTIFICATION CURL FUNCTION ENDS


   //CEO NOTIFICATIONS STARTS
    public function send_notification_fcm($userid){    
      
        

        //GETTING USER DATA STARTS
        $user_data=$this->commonModel->getunewserbyuserid($userid);

        $cur_time    = Carbon::now();
        $st_code = $user_data->st_code;
        $st_name = $user_data->placename;


        //CURL METHOD FOR SENDING NOTIFICTION TO MOBILE ENDS

       // $url = 'https://fcm.googleapis.com/fcm/send';
        $now = Carbon::now();

        $date =  strtotime($now);
        //$date = Carbon::parse($now)->format('Y-m-d h:i:sa');
        
        //MESSAGE ARRAY FOR RO USER
       if($user_data->pc_no ===0){
           
           //$url = 'https://iid.googleapis.com/iid/v1/'.$token.'/rel/topics/'.$stcode.'-'.$pcno;

          $title   = $user_data->designation.' Updated Data';
          $message = 'Test notification for ARO to CEO Messsge';

           $fields = array (
                    'to' => '/topics/RO_'.$stcode.'_'.$pcno,
                    'data'  => array (
                            "message"  => $message,
                            "title"    => $title
                           
                    )
            );
          
       }else if($user_data->pc_no !=0 && $user_data->ac_no !=0){

          $title   = $user_data->designation.' Updated Data';
          $message = 'Test notification for ARO to CEO Messsge';

           $fields = array (
                    'to' => '/topics/ARO_'.$user_data->st_code.'_'.$user_data->pc_no.'_'.$user_data->ac_no,
                    'data'  => array (
                            "message"  => $message,
                            "title"    => $title
                           
                    )
            );


       }else{
           
           //MESSAGE ARRAY FOR CEO USER
           //$url = 'https://iid.googleapis.com/iid/v1/'.$token.'/rel/topics/'.$stcode;

          $title   = $user_data->designation.' Updated Data';
          $message = 'Test notification for RO to CEO Messsge';

           $fields = array (
                    'to' => '/topics/CEO_'.$stcode,
                    'data'  => array (
                            "message"  => $message,
                            "title" => $title,
                           
                    )
            );

       }
        

        $fields = json_encode ( $fields );

        $MY_KEY = 'AIzaSyA35mgXSz16ioGBDk_LW085fxhL77t7CH8';

        $headers = array (
                'Authorization: key=' . $MY_KEY,
                'Content-Type: application/json'
        );

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

        $result = curl_exec ( $ch );
        //dd($result);
        curl_close ( $ch );

    }




   
 
}  // end class