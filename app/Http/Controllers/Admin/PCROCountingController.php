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
		use App\adminmodel\PCCountingModel; 
		use Illuminate\Support\Facades\Schema;
		use App\Helpers\SmsgatewayHelper;
		use App\Classes\xssClean;
class PCROCountingController extends Controller
{
   public function __construct()
        {
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ro');
        $this->commonModel = new commonModel();
        $this->CountingModel = new PCCountingModel();
        $this->xssClean = new xssClean;
        }

     protected function guard(){
        return Auth::guard('admin');
    	}

     public function round_schedule_create()
	    {   
	            $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
  				$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
  				 if(!isset($ele_details)){
			      			return redirect('/logout');
			      	  }
			    $filter='';  
			    	
			    	$filter 	= [
	       	    	'st_code' 	=> $ele_details->ST_CODE,
	       	    	'pc_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	'order_by' =>'ac_no',
	       	      ];

  				$roundaclist=$this->CountingModel->roundsechudlepc($filter);  
  			if($roundaclist->isEmpty()) {  
  				$listac=getallacbypcno($ele_details->ST_CODE,$ele_details->CONST_NO);
  				if(isset($listac))
  					{
  						$sched_det=getschedulebyid($ele_details->ScheduleID);
  						$new_table=strtolower("counting_master_".$d->st_code);
  					foreach ($listac as $key => $v) {
  						$round_data = array('st_code'=>$ele_details->ST_CODE,'ac_no'=>$v->AC_NO,'pc_no'=>$v->PC_NO,
  										'date_poll'=>$sched_det->DATE_POLL,'date_count'=>$sched_det->DATE_COUNT,'election_id'=>$ele_details->ELECTION_ID,
  										'election_typeid'=>$ele_details->ELECTION_TYPEID,'ccenter_id'=>1,'created_by'=>$d->officername,'iscreated'=>'1',
  										'table_name'=>$new_table,'added_create_at'=>date("Y-m-d"),'created_at'=>date("Y-m-d H:i:s")); 
	           	       $this->commonModel->insertData('round_master', $round_data);
  					 
  					}  // end foreach

  					$roundaclist=$this->CountingModel->roundsechudlepc($filter);

  				     }   // end listac
  					} // end of  roundaclist
  				// dd($roundaclist);
		 		return view('admin.pc.counting.pcwise-roundschedule',['user_data' =>$d,'ele_details'=>$ele_details,'roundaclist'=>$roundaclist]);	
			 
                
		  }
	public function verify_round_schedule_create(Request $request)
		    {   
		        $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
  				$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
		        
		         $val = $this->xssClean->clean_input($request->input('val'));
				 
					$date = Carbon::now();
             		$currentTime = $date->format('Y-m-d H:i:s');
             		$currentdate = $date->format('Y-m-d');  
				  $rules = ['Please enter all new serial number'];
				 
   
				for ($i=1; $i<=$val;$i++){  
					 $this->validate($request, [
					    	'scheduled_round'.$i => 'required|min:1|max:10',
					     ],
		                [
			                'scheduled_round'.$i.'required' => 'Please enter current vote ',
			                'scheduled_round'.$i.'max' => 'Please enter integer value max 50 ',
			            ]);	
  				}

      die;
        //       DB::beginTransaction();
       	// try{
       		    $new_table=strtolower("counting_master_".$d->st_code);
 				for ($i=1; $i<=$val;$i++)
			       	{
			       	    $rid=$this->xssClean->clean_input($request->input('rid'.$i));
				       	$ac_no=$this->xssClean->clean_input($request->input('ac_no'.$i));
				       	$scheduled_round=$this->xssClean->clean_input($request->input('scheduled_round'.$i));
					    $round_data = array('scheduled_round'=>$scheduled_round,
					    	'updated_by'=>$d->officername,'updated_at'=>$currentTime,'added_update_at'=>$currentdate); 
			            DB::table('round_master')->where('id',$rid)->where('ac_no',$ac_no)->update($round_data);
			        } 
				   
				  // DB::commit();
			          
			   //  }
		    //     catch(\Exception $e){
		    //         DB::rollback();
		    
		    //         \Session::flash('error_mes', 'Please try again Data  do not inserted');
		    //         return Redirect::back();
		    //     }
		       
 				 \Session::flash('success_mes', 'This Round Successfully Updated');
	             
	             return Redirect::to('/ropc/counting/round-schedule-create');	  
        	 
		    }  // end index function   
 


}  // end class results-declaration   
