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

class RoNotificationController extends Controller
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
        $this->middleware('ceo');
        $this->commonModel = new commonModel();
        $this->ceomodel = new CEOModel();
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




   
 
}  // end class