<?php namespace App\Http\Controllers\IndexCardReports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Session;
use App\commonModel;
use DB;

class AddIndexcardDataController extends Controller
{
	
	 public function __construct()
  {
     $this->middleware('adminsession');
     $this->middleware(['auth:admin','auth']);
     $this->middleware('ceo');
     $this->commonModel = new commonModel();
  }
  
	public function add(Request $request){
		//dd("Hello From Add Form Controller");
		$user = Auth::user();
          $uid=$user->id;
         $d=$this->commonModel->getunewserbyuserid($user->id);
         $d=$this->commonModel->getunewserbyuserid($uid);
         $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

       $sched=''; $search='';
       $status=$this->commonModel->allstatus();
       if(isset($ele_details)) {  $i=0;
         foreach($ele_details as $ed) {
            $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
            $const_type=$ed->CONST_TYPE;
          }
       }
       $session['election_detail'] = (array)$ele_details[0];
        $session['election_detail']['st_code'] = $user->st_code;
        $session['election_detail']['st_name'] = $user->placename;
     // echo "<pre>"; print_r($session); die;
     $election_detail = $session['election_detail'];
     $user_data = $d;



    $session = $request->session()->all();
  //  dd($session);
   $datanew = array();
    $st_code = $session['admin_login_details']['st_code'];

    $st_name = $session['admin_login_details']['placename'];
		
		return view('IndexCardReports.IndexCardData.Indexcarddata', compact('user_data'));
	}
	
	
}

?>