<?php
    namespace App\Http\Controllers\Admin;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Session;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Redirect;
    use Carbon\Carbon;
    use DB;
    use Illuminate\Support\Facades\Hash;
    use Validator;
    use Config;
    use \PDF;
    use App\commonModel;  
    use App\adminmodel\ECIModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\adminmodel\PCCountingModel; 

    

class EciIndexController extends Controller
{

   public function __construct(){   
        $this->commonModel = new commonModel();
        $this->ECIModel = new ECIModel();
        $this->CountingModel = new PCCountingModel();
    }

   public function dashboard(){ 
        
    $user = \Auth::user();
            $uid=$user->id;
            $d=$this->commonModel->getunewserbyuserid($uid);
            $list_record=$this->ECIModel->getallelectionphasewise();
            $list_state=$this->ECIModel->listcurrentelectionstate();
            $list_phase=$this->ECIModel->listcurrentelectionphase();
            $list_electionid=$this->ECIModel->getallelectionbyid();
            $list=$this->ECIModel->listelectiontype();
           
            $module=$this->commonModel->getallmodule();
             return view('admin.pc.eci.dashboard', ['user_data' => $d,'module' => $module,'list_record' => $list_record,'list_state'=>$list_state,'list_phase'=>$list_phase,'list_electionid'=>$list_electionid,'list'=>$list]);
             
  
  
        }   // end dashboard function
		
	 

}  // end class