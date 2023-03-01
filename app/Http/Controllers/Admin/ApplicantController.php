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
		use App\adminmodel\ROModel;
class ApplicantController extends Controller
{
     public function __construct()
        {
       		$this->middleware(['auth:admin','auth']);
        	$this->middleware('ro');
        	$this->commonModel = new commonModel();
        	$this->ROModel = new ROModel();
        }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
 	protected function guard(){
         return Auth::guard('admin');
    	}
public function listapplicant(Request $request)
		    {
		     if(Auth::check()){
		          $user = Auth::user();
	              $uid=$user->id;
		          $d=$this->commonModel->getunewserbyuserid($uid);
		          if($d->officerlevel=="AC")
            	$edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,$d->officerlevel,''); 
        elseif($d->officerlevel=="PC")
            	$edetails=$this->commonModel->election_details_cons($d->ST_CODE,$d->PC_NO,$d->officerlevel,'');
            //dd($edetails);
		          $val=$this->ROModel->checkfinalize_acbyro($d->ST_CODE,$d->AC_NO,$d->officerlevel);	
				  $cand_status='all';
		   		  $cand_status = $request->input('cand_status');   //
		   		  $list=$this->ROModel->Allapplicantlist($edetails,$cand_status);
		   		  $status=$this->commonModel->allstatus();
		   		  // dd($list);
		        return view('admin.ro.listapplicant',['user_data' => $d,'lists'=>$list,'status'=>$cand_status,'checkval'=>$val,'showpage'=>'candidate','status_list'=>$status,'edetails'=>$edetails]);
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
		     
		    }  // end  function   
   
}