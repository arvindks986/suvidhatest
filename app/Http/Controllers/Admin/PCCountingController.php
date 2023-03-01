<?php  
		namespace App\Http\Controllers\Admin;
		use Illuminate\Http\Request;
		use App\Http\Controllers\Controller;
		use Session;
		use Illuminate\Support\Facades\Auth;
		use Illuminate\Support\Facades\Input;
		use Illuminate\Support\Facades\Redirect;
		//use Illuminate\Database\MySqlConnection;
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
		use App\models\Counting\PostalBallotResultsPublishModel;
		use App\models\Counting\CountingResultsPublishModel;
class PCCountingController extends Controller
{

	public $mongo_sync = false;

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
     

     public function dashboardcounting()
	    {    
	     if(Auth::check()){ 
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);

                $new_table=strtolower("counting_master_".$d->st_code);
			    $ac_details=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
				
				$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
				$round_details=DB::table('round_master')->where('st_code', $ele_details->ST_CODE)
										->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->first();
               
              
        		$filter='';
             //dd($byro); 
	             $filter = [
					'st_code' 	=> $ele_details->ST_CODE,
					'pc_no' 	=>$ele_details->CONST_NO,
					'election_id'	=> $ele_details->ELECTION_ID,
					'order_by'=>'id'
			     ]; 
        
				 
			    $winn_data=$this->CountingModel->winn_lead($filter);    

        		$c_data=DB::table($new_table)->select('complete_round','finalized_round')
        						->where('ac_no', $d->ac_no)->where('election_id',$ele_details->ELECTION_ID)->first();

        		$master_data=DB::table($new_table)->where('ac_no',$d->ac_no)->where('election_id',$ele_details->ELECTION_ID)->orderBy('id')->get();       

         
		 		return view('admin.pc.counting.dashboard',	['user_data' =>$d,'ac_details'=>$ac_details,
		 							'ele_details'=>$ele_details,'round_details'=>$round_details,
		 							'master_data'=>$master_data,'new_table'=>$new_table,
		 							'winn_data'=>$winn_data]);	
			 
               }
		        else {
		              return redirect('/officer-login');
		        	  }	
		  }
	    	
    public function listac()
	    {   
	     if(Auth::check()){
		    $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
			 	$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
			    $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			    $seched=getschedulebyid($ele_details->ScheduleID);
                $sechdul=checkscheduledetails($seched);
       	    
       	    	$byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
	 			$record=getallacbypcno($ele_details->ST_CODE,$ele_details->CONST_NO);
	        //dd($check_finalize->finalized_ac);
	         if($check_finalize->finalized_ac==0){
                   return Redirect::to('/ropc/counting/prepare-counting');
 		       }
	     foreach($record as $r)
			 {   
			 	$date = Carbon::now();
        		$currentTime = $date->format('Y-m-d H:i:s');
        		$check = DB::table('counting_finalized_ac')->where('st_code',$ele_details->ST_CODE)
        				->where('ac_no',$r->AC_NO)->where('pc_no',$ele_details->CONST_NO)
        				->where('election_id',$ele_details->ELECTION_ID)->get();
        	 
        	 
        	 $date = Carbon::now();
             $currentTime = $date->format('Y-m-d H:i:s');
             $currentdate = $date->format('Y-m-d');
        	if($check->count()==0) {
			 		$n = array('st_code'=>$ele_details->ST_CODE,'pc_no'=>$ele_details->CONST_NO,'ac_no'=>$r->AC_NO,
			 					'election_id'=>$ele_details->ELECTION_ID,'created_at'=>$currentTime, 
			 					'added_create_at'=>$currentdate,'created_by'=>$d->officername);

			   		$this->commonModel->insertData('counting_finalized_ac',$n);	
				 }
			 }
			
			$list = DB::table('counting_finalized_ac')->where('st_code',$ele_details->ST_CODE)
					->where('pc_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->orderBy('ac_no','ASC')->get();
          
          	$filter='';
             //dd($byro); 
	             $filter = [
					'st_code' 	=> $ele_details->ST_CODE,
					'pc_no' 	=>$ele_details->CONST_NO,
					'election_id'	=> $ele_details->ELECTION_ID,
					'order_by'=>'id'
			     ]; 
        
				$pc_counting =$this->CountingModel->get_allpccandiade ($filter); 
			     
            
            if($pc_counting->isEmpty()){
                    \Session::flash('error_mes', 'Counting Data Not exit! Please click activate all AC for counting');
		        	 return Redirect::to('/ropc/counting/prepare-counting');
 		      }

		    return view('admin.pc.counting.aclist',['user_data' => $d,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,
		    					'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,
		    					'ropc'=>$byro,'lists'=>$list,'pc_no'=>$ele_details->CONST_NO,
		    					'st_code'=>$ele_details->ST_CODE,'ele_details'=>$ele_details,'pc_counting'=>$pc_counting]);	           
	        }
	        else {
	              return redirect('/officer-login');
	        	  }
	    }  // end index function     createcenter
	    
	function round_schedule()
			{
				if(Auth::check()){
			    $user = Auth::user();
			   	$d=$this->commonModel->getunewserbyuserid($user->id);
				 $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
				 $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			     $seched=getschedulebyid($ele_details->ScheduleID);
                 $sechdul=checkscheduledetails($seched);
       	    
		          
		 		$new_table=strtolower("counting_master_".$d->st_code);	   
				$round_details=$this->CountingModel->roundsechudleacpc($d->st_code,$d->ac_no,$d->pc_no,$ele_details->ELECTION_ID); 
 		 		$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('ac_no', $d->ac_no)
 		 						->where('ELECTION_ID',$ele_details->ELECTION_ID)->first();
 		 if(!isset($c_data)){
                     \Session::flash('error_mes', 'Candidate Nominations details has not been finalized yet by ROPC. To Start Counting Process ROPC Needs to finalize the Nomination Details first  and Go to Counting menu and prepare counting data.');
		        	 return Redirect::to('aro/dashboard');
 		 }


 		 if(!empty($c_data->complete_round)){
           			$complete_round=$c_data->complete_round; 
           			$finalized_round=$c_data->finalized_round;
           		}
             else {$complete_round=0; $finalized_round=0;}

 			 if(isset($round_details)) $rid=$round_details->id; else $rid='';
 
	           return view('admin.pc.counting.roundschedule',['user_data' => $d,'rid'=>$rid,'list'=>$round_details,'finalized_round'=>$finalized_round]);	           
		        }
		        else {
		              return redirect('/officer-login');
		        	 }
			} 



	public function verifyround(Request $request)
		    {    
		     if(Auth::check()){
			    $user = Auth::user();
			    $this->validate(
	                $request, 
	                    [
	                      'scheduled_round' => 'required|numeric|min:1|max:130',  
	                    ],
	                    [
	                      'scheduled_round.required' => 'Please enter round schedule ',
	                      'scheduled_round.numeric' => 'Please enter numeric value',
	                      'scheduled_round.min' => 'Please enter minimum value 1',
	                      'scheduled_round.max' => 'Please enter maximum value 130',
	                    ]);
			     
			      $d=$this->commonModel->getunewserbyuserid($user->id);
			      $ac_details=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
			   
				 $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
	  			$check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		   		$sched_det=getschedulebyid($ele_details->ScheduleID);

		        $sechdul=checkscheduledetails($sched_det);
                $new_table=strtolower("counting_master_".$ele_details->ST_CODE);
         
               $c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('ac_no', $d->ac_no)
         				->where('ELECTION_ID',$ele_details->ELECTION_ID)->first();
	 		 
	 		 if(isset($c_data) AND $c_data->finalized_round==1){
	                     \Session::flash('error_mes', 'Round is finalize');
			        	 return Redirect::to('aro/dashboard');
	 		 }  
        
        	$scheduled_round = Check_Input($this->xssClean->clean_input($request->input('scheduled_round')));
        	$scheduled_round1 = Check_Input($this->xssClean->clean_input($request->input('scheduled_round1')));	
			$rid = $this->xssClean->clean_input($request->input('rid'));
            if(isset($sched_det)) $poll_date=$sched_det['DATE_POLL'];  else $poll_date=''; 
            if(isset($sched_det)) $count_date=$sched_det['DATE_COUNT'];  else $count_date=''; 
       DB::beginTransaction();
         	try{
        		  $round_details=$this->CountingModel->roundsechudleacpc($d->st_code,$d->ac_no,$d->pc_no,$ele_details->ELECTION_ID); 
		 
			$c_data=DB::table($new_table)->select('complete_round','finalized_round')
											->where('ac_no', $d->ac_no)
											->where('election_id',$ele_details->ELECTION_ID)->first();

			if($c_data->complete_round>$scheduled_round and $scheduled_round1>$scheduled_round)
			   		  {
					   \Session::flash('error_mes', 'No of Rounds can not be less than completed rounds');
					    return Redirect::to('aro/counting/round-schedule');	
					 }
		 
		
		     $date = Carbon::now();
             $currentTime = $date->format('Y-m-d H:i:s');
             $currentdate = $date->format('Y-m-d');      
			 
				$ccenter_id=0;   
	    			$round_data = array('st_code'=>$d->st_code,
	    								'ac_no'=>$d->ac_no,
	    								'pc_no'=>$d->pc_no,
	    								'scheduled_round'=>$scheduled_round,
	    								'date_poll'=>$poll_date,
	    								'date_count'=>$count_date,
	    								'election_id'=>$ele_details->ELECTION_ID,
	    								'election_typeid'=>$ele_details->ELECTION_TYPEID,
	    								'ccenter_id'=>$ccenter_id,
	    								'created_by'=>$d->officername,
	    								'iscreated'=>'1',
	    								'table_name'=>$new_table,
	    								'added_create_at'=>$currentdate); 
	           	
       
		    if(!isset($round_details)) {
			        $this->commonModel->insertData('round_master', $round_data);
			        DB::commit();
			        \Session::flash('success_admin', 'Round Schedule Successfully Added');
	            }
	         else {
			        $this->commonModel->updatedata('round_master','id',$round_details->id,$round_data);
			        
			        \Session::flash('success_admin', 'Round Schedule Successfully Updated');
	              }
	        
	         DB::commit();
	     }
 		catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('error_mes', 'Please try again');
		            return Redirect::back();
		        }
		    


	          return Redirect::to('aro/counting/counting-data-entry');	           
		}
		else {
		              return redirect('/officer-login');
		        	  }
		    }  // end index function   

	

	function counting_data_entry($rid1='')
			{     
			
			if(Auth::check()){
			    	$user = Auth::user();
			    	$d=$this->commonModel->getunewserbyuserid($user->id);
			     	$new_table=strtolower("counting_master_".$d->st_code);
			     	$ac_details=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
				  	$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
	  			 	$check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			     	$seched=getschedulebyid($ele_details->ScheduleID);
                 	$sechdul=checkscheduledetails($seched); 
                    $rid= base64_decode($rid1);
		     
          		$round_details=DB::table('round_master')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $d->ac_no)
          						->where('election_id', $ele_details->ELECTION_ID)->first();
                
          			$filter='';
             //dd($byro); 
	             $filter = [
					'st_code' 	=> $ele_details->ST_CODE,
					'pc_no' 	=>$ele_details->CONST_NO,
					'election_id'	=> $ele_details->ELECTION_ID,
					'order_by'=>'id'
			     ]; 
        
				 
			    $winn_data=$this->CountingModel->winn_lead($filter);    
              
             
              if(!isset($round_details)) {
                			\Session::flash('success_admin', 'Round Schedule Not Created! Please Fill Round Schedule Details First.');
                			 return Redirect::to('aro/counting/round-schedule');
                }   
            

            	$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('ac_no', $d->ac_no)
            								->where('election_id',$ele_details->ELECTION_ID)->first();
             
             if(!empty($c_data->complete_round)){  
           			$complete_round=$c_data->complete_round; 
           			$finalized_round=$c_data->finalized_round;
           			$n=$complete_round+1;
           		 }
             else {		$complete_round=0; $finalized_round=0;  $n=$complete_round+1;	}

             if($rid!=''){ $n=$rid; }
			 if($n >130){$n=130;}
             $field="round".$n;
              
                 $master_data=DB::table($new_table)->select('*', $field)->where('ac_no', $d->ac_no)
                 					->where('election_id',$ele_details->ELECTION_ID)->get();

             $def_data=DB::table('counting_defect_rounds')->select('id','st_code','pc_no','ac_no','const_type','roundno','status','comments')
             					->where('st_code',$ele_details->ST_CODE)->where('pc_no', $d->pc_no)
             					->where('ac_no', $d->ac_no)->where('roundno',$n)
             					->where('election_id',$ele_details->ELECTION_ID)->where('status','0')->first(); 

             $filter = ['st_code'=>$ele_details->ST_CODE,
             			'pc_no'=>$d->pc_no,
             			'ac_no'=> $d->ac_no,
             			'election_id'=>$ele_details->ELECTION_ID,
             		 ]; 		   
            $def_round=$this->CountingModel->defected_rounds_details($filter); 
          
	        return view('admin.pc.counting.dataentrysechudle',['user_data' => $d, 'ac_details'=>$ac_details,'ele_details'=>$ele_details,'round_details'=>$round_details,'master_data'=>$master_data,'new_table'=>$new_table,'rid'=>$rid,'comp_round'=>$complete_round,'field'=>$field,'finalized_round'=>$finalized_round,'winn_data'=>$winn_data,'def_data'=>$def_data,'def_round'=>$def_round]);	           
		        }
		        else {
		              return redirect('/officer-login');
		        	 }
			}
	function counting_data_entry_edit(Request $request)
			{    
				$rid =$request->input('rid');
				if($rid!=''){
 					$nrid= base64_encode($rid);
				 
				return Redirect::to('aro/counting/counting-data-entry/'.$nrid);
			}
			else {
				\Session::flash('error_mes', '  Please Select   roundschedule');
		         return Redirect::to('aro/counting/counting-data-entry');
			}

				 
			}
	function verifycounting(Request $request)
			{
			    
			 if(Auth::check()){ 
			    $user = Auth::user();

			    $d=$this->commonModel->getunewserbyuserid($user->id);
			    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
			    $cschedule = $this->xssClean->clean_input($request->input('cschedule'));
		        $totalround = $this->xssClean->clean_input($request->input('totalround'));
		        
		        $leading_id = $this->xssClean->clean_input($request->input('leading_id'));
		        $ST_CODE =$d->st_code; // $this->xssClean->clean_input($request->input('ST_CODE'));
		        $CONST_TYPE = $this->xssClean->clean_input($request->input('CONST_TYPE'));
		        $CONST_NO = $this->xssClean->clean_input($request->input('CONST_NO'));
		        $ELECTION_ID=$this->xssClean->clean_input($request->input('ELECTION_ID'));
		        $nrid=$this->xssClean->clean_input($request->input('nrid'));   //
		        $val = $this->xssClean->clean_input($request->input('val'));
		        $comments = $this->xssClean->clean_input($request->input('comments'));
		        $defected =  $request->input('defected');
				$input = $request->all();
 				 
					$date = Carbon::now();
             		$currentTime = $date->format('Y-m-d H:i:s');
             		$currentdate = $date->format('Y-m-d');  
				 if(!empty($cschedule)) $newcschedule=$cschedule+1; else $newcschedule='';   

				$rules = ['Please enter all new serial number'];
				$total_voters = 0;
				
				for ($i=1; $i<=$val;$i++){  

				    $this->validate($request, ['roname' => 'required',
					    	'currentvote'.$i => 'required|digits_between:0,999999',
					    	'cschedule' => 'required|numeric', 'comments' => 'required_if:defected,==,on'
					    ],
		                [
			                'currentvote'.$i.'required' => 'Please enter current vote ',
			                'currentvote'.$i.'numeric' => 'Please enter integer value ',
			                'currentvote'.$i.'digits_between' => 'Please enter integer value max 999999 ',
			                'currentvote'.$i.'integer' => 'Please enter integer value ',
			                'currentvote'.$i.'regex' => 'Please enter integer value ',
			                'cschedule.required' => 'Please select select round',
			                'comments.required' => 'Please enter comments when defected is on',
		                ]);	

				  	$total_voters += $input['currentvote'.$i];

			    }
				
				if(str_replace(" ","",$request->input('roname')) <> str_replace(" ","",Auth::user()->name)){
						\Session::flash('error_mes', 'Please enter correct assistance returning officer name.');
                		 return Redirect::back()->withInput($request->all());		
				}

			    if($total_voters != $request->total){
			    	\Session::flash('error_mes', 'Total value is wrong.');
			    	return Redirect::back()->withInput($request->all());
			    }
                
        DB::beginTransaction();
       	try{
       		    $new_table=strtolower("counting_master_".$d->st_code);
 				for ($i=1; $i<=$val;$i++)
			       	{
				       	$mid=$this->xssClean->clean_input($request->input('mid'.$i));
				       	$nom_id=$this->xssClean->clean_input($request->input('nom_id'.$i));
				       	$currentvote=$this->xssClean->clean_input($request->input('currentvote'.$i));
				        $priviousvote=$this->xssClean->clean_input($request->input('priviousvote'.$i));
				       	$round="round".$cschedule;
				        
				         $filter_ele = ['id'=>$mid,'nom_id'=>$nom_id,'ac_no'=> $d->ac_no];
				       $total_value='';
				       $total_value=$this->CountingModel->grandtotalsum($new_table,$round,$filter_ele);
                         
				        $total_vote   = 0; 
				        $round_vote=0;

           			    if(isset($total_value) && $total_value){
				            $total_vote   = $total_value->grant_total;
				            $round_vote=$total_value->$round;
				           
				          }
				      
				       $total_vote= ($total_vote-$round_vote)+$currentvote;
				      //echo $total_vote."=";  echo $round_vote."=";   
				     //  dd($total_value);  die;
 					if($nrid==0){
			         	$n_data = array($round=>$currentvote,'total_vote'=>$total_vote,
			         					'complete_round'=>$cschedule,'added_update_at'=>$currentdate,
			         					'updated_at'=>$currentTime,'updated_by'=>$d->officername); 
			       }
			       else { 		$nr="round".$nrid;
			          			$n_data = array($round=>$currentvote,'total_vote'=>$total_vote,
			          				'added_update_at'=>$currentdate,'updated_at'=>$currentTime,
			          				'updated_by'=>$d->officername);
			       } 
 					  \App\models\Counting\CountingLogModel::clone_record($mid,strtolower($ST_CODE));
				      DB::table($new_table)->where('id',$mid)->update($n_data);	

				      
			        }

                  // print_r(($n_data);

	              $pcentry=$this->CountingModel->totalvotsbypcwise($new_table,$CONST_NO,$ELECTION_ID);
	              $mango_db_array=[];
	               // print_r($pcentry);
	              foreach ($pcentry as $v) {
	              			$postaldata=DB::table('counting_pcmaster')->select('postal_vote','evm_vote','migrate_votes')
	              					->where('st_code',$ST_CODE)->where('pc_no',$v->pc_no)->where('ELECTION_ID',$ELECTION_ID)
	              					->where('nom_id',$v->nom_id)->first();

	              	 $net = $v->sum_total+$postaldata->postal_vote+$postaldata->migrate_votes;
	               
	              	$w_data=array('evm_vote'=>$v->sum_total,'total_vote'=>$net,
	              				'added_update_at'=>$currentdate,'updated_at'=>$currentTime,'updated_by'=>$d->officername);
	              	 
	              	  DB::table('counting_pcmaster')->where('st_code',$ST_CODE)->where('pc_no',$v->pc_no)
	              	  				->where('ELECTION_ID',$ELECTION_ID)->where('nom_id',$v->nom_id)->update($w_data);
                    $data22 = ["add_evm_vote"=>$v->sum_total,'total_vote'=>$net, "nom_id"=>$v->nom_id, "st_code"=>$ST_CODE, "pc_no"=>$v->pc_no];
	              	  $mango_db_array[]=$data22;

	              }
	                 //dd($pcentry);
	              if( $this->mongo_sync){  
	              	  updateEvmById($mango_db_array);
				      //End API
				  }

				       $fdata=$this->CountingModel->selectfirsthightvalueofcounting('counting_pcmaster',$ST_CODE,$CONST_NO,$CONST_TYPE,$ELECTION_ID);
				       $sdata=$this->CountingModel->selectsecondhightvalueofcounting('counting_pcmaster',$ST_CODE,$CONST_NO,$CONST_TYPE,$ELECTION_ID);

				 

                    //if(isset($fdata) and isset($sdata) and ($fdata->max_total !=$sdata->max_total)){
						    $lead_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$fdata->candidate_id);
						    $lead_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$fdata->nom_id);
						    $lead_party=$this->commonModel->selectone('m_party','CCODE',$lead_nom->party_id);
					
					//if(isset($sdata)){$sdata=$fdata;}
		                $trail_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$sdata->candidate_id);
					    $trail_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$sdata->nom_id);
					    $trail_party=$this->commonModel->selectone('m_party','CCODE',$trail_nom->party_id);
					 

				    $margin=$fdata->max_total-$sdata->max_total;
				    $winn_update=array('candidate_id'=>$fdata->candidate_id,
				    					'nomination_id'=>$fdata->nom_id,'lead_cand_name'=>$lead_cand->cand_name,
				    					'lead_cand_partyid'=>$lead_party->CCODE,'lead_cand_party'=>$lead_party->PARTYNAME,
				    					'lead_party_type'=>$lead_party->PARTYTYPE,'lead_party_abbre'=>$lead_party->PARTYABBRE,
				    					'lead_cand_hname'=>$lead_cand->cand_hname,'lead_cand_hparty'=>$lead_party->PARTYHNAME,
				    					'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
				    					'trail_candidate_id'=>$sdata->candidate_id,'trail_nomination_id'=>$sdata->nom_id,
				    					'trail_cand_name'=>$trail_cand->cand_name,'trail_cand_partyid'=>$trail_party->CCODE,
				    					'trail_cand_party'=>$trail_party->PARTYNAME,'trail_party_type'=>$trail_party->PARTYTYPE,
				    					'trail_party_abbre'=>$trail_party->PARTYABBRE,'trail_cand_hname'=>$trail_cand->cand_hname,
				    					'trail_cand_hparty'=>$trail_party->PARTYHNAME,'trail_hpartyabbre'=>$trail_party->PARTYHABBR,
				    					'margin'=>$margin,'lead_total_vote'=>$fdata->max_total,'trail_total_vote'=>$sdata->max_total,
				    					'added_update_at'=>$currentdate,'updated_at'=>$currentTime);
				    
				    DB::table('winning_leading_candidate')->where('leading_id',$leading_id)->update($winn_update);
					
					$pubresult=['st_code'=>$ele_details->ST_CODE,
                        'election_id'=>$ST_CODE,
                        'pc_no'=>$CONST_NO,
                        'ac_no'=>0,
                        'round_id'=>$cschedule,
                        'certificate'=>"I, ".Auth::user()->name." certify that the Round wise data entered/ updated has been printed & manually verified by me and the observer is correct., 
                          I, understand that upon pressing the 'Publish' button below,the round will be immediately published/ updated with the correct data and round-wise data will be available in public domain. ,
                          I, certify that the round-wise publication on the server and at the counting center is done simultaneously.",
                        'name'=>$this->xssClean->clean_input($request->input('roname')),
                        'roname'=>Auth::user()->name,
                        'agree'=>'1',
                        ];
                    CountingResultsPublishModel::add_records($pubresult);
				  
				  if( $this->mongo_sync){
                     // API of Mango Node JS 
				     $winn_update1=array('st_code'=>$ST_CODE,'pc_no'=>$CONST_NO,'candidate_id'=>$fdata->candidate_id,
				     					'nomination_id'=>$fdata->nom_id,
				     					'lead_cand_name'=>$lead_cand->cand_name,
				     					'lead_cand_hname'=>$lead_cand->cand_hname,
				     					'lead_cand_partyid'=>$lead_party->CCODE,
				     					'lead_cand_party'=>$lead_party->PARTYNAME,
				     					'lead_party_type'=>$lead_party->PARTYTYPE,
				     					'lead_party_abbre'=>$lead_party->PARTYABBRE,
				     					'lead_cand_hparty'=>$lead_party->PARTYHNAME,
				     					'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
				    					'trail_candidate_id'=>$sdata->candidate_id,
				    					'trail_nomination_id'=>$sdata->nom_id,
				    					'trail_cand_name'=>$trail_cand->cand_name,'trail_cand_hname'=>$trail_cand->cand_hname,
				    					'trail_cand_partyid'=>$trail_party->CCODE,'trail_cand_party'=>$trail_party->PARTYNAME,
				    					'trail_party_type'=>$trail_party->PARTYTYPE,'trail_party_abbre'=>$trail_party->PARTYABBRE,
				    					'trail_cand_hparty'=>$trail_party->PARTYHNAME,'trail_hpartyabbre'=>$trail_party->PARTYHABBR,
				    					'margin'=>$margin,'lead_total_vote'=>$fdata->max_total,'trail_total_vote'=>$sdata->max_total);
				     updateWinningLeading($winn_update1);
				     //End API
                     }

				   
				   $def_data=DB::table('counting_defect_rounds')->select('id','st_code','pc_no','ac_no','const_type','roundno','status','comments')
				   				->where('st_code',$ele_details->ST_CODE)->where('pc_no', $d->pc_no)->where('ac_no', $d->ac_no)
				   				->where('roundno',$cschedule)->where('election_id',$ele_details->ELECTION_ID)->first();
				    
				
				    

				  DB::commit();
			          
			    }
		        catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('error_mes', 'Please try again Data  do not inserted');
		            return Redirect::back();
		        }
		       

			       \Session::flash('success_mes', 'This Round Successfully Updated');
                	 
                	return Redirect::to('/aro/counting/counting-data-entry');	        
		        }
		        else {
		              return redirect('/officer-login');
		        	  }
			}



	function counting_evm_finalized(Request $request)
			{
			 if(Auth::check()){ 
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
                 
			    $new_table=strtolower("counting_master_".$d->st_code);
			    $ac_details=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
				 $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');

				$round_details=DB::table('round_master')->where('st_code', $ele_details->ST_CODE)
									->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->first();
                
        			$filter='';
             //dd($byro); 
	             $filter = [
					'st_code' 	=> $ele_details->ST_CODE,
					'pc_no' 	=>$ele_details->CONST_NO,
					'election_id'	=> $ele_details->ELECTION_ID,
					'order_by'=>'id'
			     ]; 
        
				 
			    $winn_data=$this->CountingModel->winn_lead($filter);   

        		$c_data=DB::table($new_table)->select('complete_round','finalized_round')
        						->where('ac_no', $d->ac_no)->where('election_id',$ele_details->ELECTION_ID)->first();

        		$master_data=DB::table($new_table)->where('ac_no',$d->ac_no)->where('election_id',$ele_details->ELECTION_ID)->orderBy('id')->get();       

		        if(!isset($round_details)) {
		                			\Session::flash('success_admin', 'Round Schedule Not Created! Please Fill Round Schedule Details First.');
		                			 return Redirect::to('aro/counting/round-schedule');
		                }   
        
                $complete_round=0; $finalized_round=0;
		         if(isset($c_data)){
		         	$complete_round=$c_data->complete_round; $finalized_round=$c_data->finalized_round;
		            }
		             
		         
		         if($round_details->scheduled_round==$complete_round)
				   {
				   		 
				 return view('admin.pc.counting.evm_vote_finalize',['user_data' => $d,'ac_details'=>$ac_details,'ele_details'=>$ele_details,'round_details'=>$round_details,'master_data'=>$master_data,'new_table'=>$new_table,'comp_round'=>$complete_round,'finalized_round'=>$finalized_round,'winn_data'=>$winn_data]);	
					} 
		             else {
		               	\Session::flash('error_mes', 'All rounds not completed, Please Complete your rounds then finalized');
		                return Redirect::to('/aro/counting/counting-data-entry');	      
		               		   
		               	} 
					   
				        }
				        else {
				              return redirect('/officer-login');
				        	  }
			}
 	function postal_data_entry()
			{    
			if(Auth::check()){
			    $user = Auth::user();
			 $d=$this->commonModel->getunewserbyuserid($user->id);
             
			 $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
			 
			    $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			    $seched=getschedulebyid($ele_details->ScheduleID);
                $sechdul=checkscheduledetails($seched);
       	    	$byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
             $filter='';
             //dd($byro); 
	             $filter = [
					'st_code' 	=> $ele_details->ST_CODE,
					'pc_no' 	=>$ele_details->CONST_NO,
					'election_id'	=> $ele_details->ELECTION_ID,
					'order_by'=>'id'
			     ]; 
        
				$pc_counting =$this->CountingModel->get_allpccandiade ($filter); 
			    $winn_data=$this->CountingModel->winn_lead($filter);  
			if($pc_counting->isEmpty()) {
				    \Session::flash('unsuccess_insert', 'No records found');
	               return Redirect::to('ropc/counting/listac');	
			}

			$pc_finalize=DB::table('counting_pcmaster')->select('finalize')
							->where('st_code', $ele_details->ST_CODE)
							->where('pc_no',$ele_details->CONST_NO)
							->where('election_id',$ele_details->ELECTION_ID)->first();
 				
 		 
		 		
		 	$val=$this->CountingModel->checkallacfinalize($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
	 
 
	    if(isset($pc_finalize)){
           			$finalize=$pc_finalize->finalize; 
               }
        
      // echo $finalize;  echo $byro; die;
		return view('admin.pc.counting.postaldataentrysechudle',['user_data' => $d,'sechdul' => $sechdul,'ropc'=>$byro,
								'ele_details'=>$ele_details,'master_data'=>$pc_counting,'finalize'=>$finalize, 'winn_data'=>$winn_data,
								'st_code'=>$ele_details->ST_CODE,'pc_no'=>$ele_details->CONST_NO,'val'=>$val]);          
		        }
		        else {
		              return redirect('/officer-login');
		        	 }
			}
	function verifypostalentry(Request $request)
			{
			 
			 if(Auth::check()){ 
			    $user = Auth::user();
			        $user = Auth::user();
			 		$d=$this->commonModel->getunewserbyuserid($user->id);
			 		 $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
             $pc_finalize=DB::table('counting_pcmaster')->select('finalize')->where('st_code', $ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->where('finalize','0')->first();
 

		  if(!isset($pc_finalize)) {
	        \Session::flash('success_admin', 'Your Postal Ballot is Successfully finalized.');
	        return Redirect::to('ropc/counting-details');
	       }   
		 
		        $new_table = $this->xssClean->clean_input($request->input('new_table'));
		        $round_id = $this->xssClean->clean_input($request->input('round_id'));
		        $rejectedvotes = $this->xssClean->clean_input($request->input('rejectedvotes'));
		        $totalvotes = $this->xssClean->clean_input($request->input('totalvotes'));
		        $leading_id = $this->xssClean->clean_input($request->input('leading_id'));
		        $ST_CODE =$d->st_code; // $this->xssClean->clean_input($request->input('ST_CODE'));
		        $CONST_TYPE = trim($request->input('CONST_TYPE'));
		        $CONST_NO = $this->xssClean->clean_input($request->input('CONST_NO'));
		        $ELECTION_ID=$request->input('ELECTION_ID');
  				$val = trim($request->input('val'));
  				$totalvotes = $this->xssClean->clean_input($request->input('totalvotes'));
 				$tended_votes = $this->xssClean->clean_input($request->input('tended_votes'));

				$input = $request->all();
				// dd($input);
				$total=0;
				$rules = ['Please enter postal vote'];
				for ($i=1; $i<=$val;$i++)
				    {
				     $this->validate($request, ['roname' => 'required',
				     	'currentvote'.$i => 'required|digits_between:0,999999',
				     	'totalvotes'=> 'required|digits_between:0,9999999999999',
				     	'rejectedvotes'=> 'required|digits_between:0,9999999999999',
				     	'tended_votes'=> 'required|digits_between:0,9999999999999',
				     ],
		                [
		                'currentvote'.$i.'required' => 'Please enter postal vote ',
		                'currentvote'.$i.'regex' => 'Please enter valide votes',
		                'currentvote'.$i.'numeric' => 'Please enter valide votes',
		                'currentvote'.$i.'integer' => 'Please enter valide votes',
		                'currentvote'.$i.'digits_between' => 'Please enter valide votes',
		                'totalvotes.required' => 'Please enter Total Votes',
		                'totalvotes.digits_between' => 'Please enter valide votes',
		                'rejectedvotes.required' => 'Please enter Total Rejected Votes',
		                'rejectedvotes.digits_between' => 'Please enter valide votes',
		                 'tended_votes.required' => 'Please enter Total tended Votes',
		                 'tended_votes.digits_between' => 'Please enter valide votes',
		                ]);	
			        }
			    for ($i=1; $i<=$val;$i++)
				    {
				    	$cv=trim($request->input('currentvote'.$i));
				        $total=$total+$cv;
				     }
			      $total=$total+$rejectedvotes;
				if(str_replace(" ","",$request->input('roname')) <> str_replace(" ","",Auth::user()->name)){
						\Session::flash('error_mes', 'Please enter correct assistance returning officer name.');
                		 return Redirect::to('/ropc/counting/postal-data-entry');		
				}
			    if($totalvotes== $total)  {
			  
			  DB::beginTransaction();
		       	try{
					
                      $mango_db_array=[];
 				for ($i=1; $i<=$val;$i++)
			       	{
			       	$mid=$this->xssClean->clean_input($request->input('mid'.$i));
			       	$nom_id=$this->xssClean->clean_input($request->input('nom_id'.$i));
			       	$currentvote=(int)$this->xssClean->clean_input($request->input('currentvote'.$i));
			       	$priviousvote=(int)$this->xssClean->clean_input($request->input('priviousvote'.$i));
			       
			       		$postaldata=DB::table('counting_pcmaster')->where('nom_id',$nom_id)->where('pc_no',$CONST_NO)
			       					->where('ELECTION_ID',$ELECTION_ID)->first();
	              	if(isset($postaldata)) $evm_vote=$postaldata->evm_vote; else $evm_vote=$postaldata->evm_vote;
			        
			        $total_vote=(int)($evm_vote+$currentvote+$postaldata->migrate_votes);
			       		$n_data = array('total_vote'=>$total_vote,'postal_vote'=>$currentvote,'added_update_at'=>date("Y-m-d"),
			       						'updated_at'=>date("Y-m-d h:i:s"),'updated_by'=>$d->officername,'postalvote_update_at'=>date('Y-m-d H:i:s'),
			       						'rejectedvote'=>$rejectedvotes,'postaltotalvote'=>$totalvotes,'tended_votes'=>$tended_votes,'postal'=>'1'); 
			       	//dd($n_data);

			       	//log psotal ballot
			       	\App\models\Counting\CountingLogModel::clone_postal_ballot($mid);

			        DB::table('counting_pcmaster')->where('id',$mid)->update($n_data);	
			        $data22 = ["add_postal_vote"=>$currentvote, 'total_vote'=>$total_vote, "nom_id"=>$nom_id, "st_code"=>$ST_CODE, "pc_no"=>$CONST_NO];
					$mango_db_array[]= $data22;
			         
			       	}
			         if( $this->mongo_sync){
					         // API of Mango Node JS 
  							updatePostalById($mango_db_array);

					        //end of API
   						}
			         
 
			         $fdata=$this->CountingModel->selectfirsthightvalueofcounting('counting_pcmaster',$ST_CODE,$CONST_NO,$CONST_TYPE,$ELECTION_ID);
				     $sdata=$this->CountingModel->selectsecondhightvalueofcounting('counting_pcmaster',$ST_CODE,$CONST_NO,$CONST_TYPE,$ELECTION_ID);
                   
                    
                   // if(isset($fdata) and isset($sdata) and ($fdata->max_total !=$sdata->max_total)){
						    $lead_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$fdata->candidate_id);
						    $lead_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$fdata->nom_id);
						    $lead_party=$this->commonModel->selectone('m_party','CCODE',$lead_nom->party_id);
					
					//if(isset($sdata)){$sdata=$fdata;}
		                $trail_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$sdata->candidate_id);
					    $trail_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$sdata->nom_id);
					    $trail_party=$this->commonModel->selectone('m_party','CCODE',$trail_nom->party_id);
					 
                   // dd("hello");
				    $margin=$fdata->max_total-$sdata->max_total;
				    $winn_update=array('candidate_id'=>$fdata->candidate_id,'nomination_id'=>$fdata->nom_id,
				    					'lead_cand_name'=>$lead_cand->cand_name,
				    					'lead_cand_partyid'=>$lead_party->CCODE,
				    					'lead_cand_party'=>$lead_party->PARTYNAME,
				    					'lead_party_type'=>$lead_party->PARTYTYPE,
				    					'lead_party_abbre'=>$lead_party->PARTYABBRE,
				    					'lead_cand_hname'=>$lead_cand->cand_hname,
				    					'lead_cand_hparty'=>$lead_party->PARTYHNAME,
				    					'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
				    					'trail_candidate_id'=>$sdata->candidate_id,
				    					'trail_nomination_id'=>$sdata->nom_id,
				    					'trail_cand_name'=>$trail_cand->cand_name,
				    					'trail_cand_partyid'=>$trail_party->CCODE,
				    					'trail_cand_party'=>$trail_party->PARTYNAME,
				    					'trail_party_type'=>$trail_party->PARTYTYPE,
				    					'trail_party_abbre'=>$trail_party->PARTYABBRE,
				    					'trail_cand_hname'=>$trail_cand->cand_hname,
				    					'trail_cand_hparty'=>$trail_party->PARTYHNAME,
				    					'trail_hpartyabbre'=>$trail_party->PARTYHABBR,
				    					'margin'=>$margin,'lead_total_vote'=>$fdata->max_total,
				    					'trail_total_vote'=>$sdata->max_total,
				    					'added_update_at'=>date("Y-m-d"),'updated_at'=>date("Y-m-d H:i:s"));
				   //dd($winn_update);
				     DB::table('winning_leading_candidate')->where('leading_id',$leading_id)
				     						->where('st_code',$ST_CODE)->where('pc_no',$CONST_NO)->where('ELECTION_ID',$ELECTION_ID)->update($winn_update);
				      
					$pubresult=['st_code'=>$ele_details->ST_CODE,
                        'election_id'=>$ST_CODE,
                        'pc_no'=>$CONST_NO,
                        'ac_no'=>0,
                        'certificate'=>"I, ".Auth::user()->name." certify that the postal ballot votes data entered/ updated for has been printed & manually verified by me & the observer and is correct., 
                          I, understand that upon pressing the 'Publish' button below,the postal ballot votes will be immediately published/ updated with the correct data and round-wise data will be  available in public domain. ,
                          I, certify that the postal ballot data publication on the server and at the counting center is done simultaneously.",
                        'name'=>$this->xssClean->clean_input($request->input('roname')),
                        'roname'=>Auth::user()->name,
                        'agree'=>'1',
                        ];
                    PostalBallotResultsPublishModel::add_records($pubresult);


				       if( $this->mongo_sync){
				     // API of Mango Node JS 
				     $winn_update1=array('st_code'=>$ST_CODE,'pc_no'=>$CONST_NO,'candidate_id'=>$fdata->candidate_id,
				     					'nomination_id'=>$fdata->nom_id,
				     					'lead_cand_name'=>$lead_cand->cand_name,
				     					'lead_cand_hname'=>$lead_cand->cand_hname,
				     					'lead_cand_partyid'=>$lead_party->CCODE,
				     					'lead_cand_party'=>$lead_party->PARTYNAME,
				     					'lead_party_type'=>$lead_party->PARTYTYPE,
				     					'lead_party_abbre'=>$lead_party->PARTYABBRE,
				     					'lead_cand_hparty'=>$lead_party->PARTYHNAME,
				     					'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
				    					'trail_candidate_id'=>$sdata->candidate_id,
				    					'trail_nomination_id'=>$sdata->nom_id,
				    					'trail_cand_name'=>$trail_cand->cand_name,'trail_cand_hname'=>$trail_cand->cand_hname,
				    					'trail_cand_partyid'=>$trail_party->CCODE,'trail_cand_party'=>$trail_party->PARTYNAME,
				    					'trail_party_type'=>$trail_party->PARTYTYPE,'trail_party_abbre'=>$trail_party->PARTYABBRE,
				    					'trail_cand_hparty'=>$trail_party->PARTYHNAME,'trail_hpartyabbre'=>$trail_party->PARTYHABBR,
				    					'margin'=>$margin,'lead_total_vote'=>$fdata->max_total,'trail_total_vote'=>$sdata->max_total);
				     		updateWinningLeading($winn_update1);

				          //End API
                         }
				    // }
				    //  else     {
				    // 			 $winn_update2=array('candidate_id'=>0,'nomination_id'=>0,'lead_cand_name'=>'',
				    // 			 					'lead_cand_partyid'=>0,'lead_cand_party'=>'',
				    // 	 							'lead_party_type'=>'','lead_party_abbre'=>'','lead_cand_hname'=>'',
				    // 	 							'lead_cand_hparty'=>'','lead_hpartyabbre'=>'',
				    // 								'trail_candidate_id'=>0,'trail_nomination_id'=>0,'trail_cand_name'=>'',
				    // 								'trail_cand_partyid'=>0,'trail_cand_party'=>'',
				    // 								'trail_party_type'=>'','trail_party_abbre'=>'','trail_cand_hname'=>'',
				    // 								'trail_cand_hparty'=>'','trail_hpartyabbre'=>'',
				    // 								'margin'=>0,'lead_total_vote'=>0,'trail_total_vote'=>0);
				    
				    //  			DB::table('winning_leading_candidate')->where('leading_id',$leading_id)->update($winn_update2);
				    // 	       }

                         DB::commit();
			          
			    }
		        catch(\Exception $e){
		            DB::rollback();
		            \Session::flash('error_mes', 'We have encounted an issue. Please try again.');
		            return Redirect::back();
		        }

				         \Session::flash('success_mes', 'This Postal Vote Successfully Updated.');
	                		return Redirect::to('/ropc/counting/postal-data-entry');	        
		        }
		        else {
                        \Session::flash('error_mes', 'Total Votes and Candidate Votes Miss-Matched');
                		 return Redirect::to('/ropc/counting/postal-data-entry');	
		        }
		    }
		        else {
		              return redirect('/officer-login');
		        	  }
			}


	function counting_results()
			{

		   if(Auth::check()){
				    $user = Auth::user();
				 	$d=$this->commonModel->getunewserbyuserid($user->id);
		             $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
				    $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
				    $seched=getschedulebyid($ele_details->ScheduleID);
	                $sechdul=checkscheduledetails($seched);
	       	        $byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
	              
	               if($check_finalize->finalized_ac==0){
	                   return Redirect::to('/ropc/counting/prepare-counting');
	 		       }
           
           	 $filter='';
             //dd($byro); 
	             $filter = [
					'st_code' 	=> $ele_details->ST_CODE,
					'pc_no' 	=>$ele_details->CONST_NO,
					'election_id'	=> $ele_details->ELECTION_ID,
					'order_by'=>'id'
			     ]; 
        
				$pc_counting =$this->CountingModel->get_allpccandiade ($filter); 
			    $winn_data=$this->CountingModel->winn_lead($filter); 
			
			$pc_finalize=DB::table('counting_pcmaster')->select('finalize')->where('st_code', $ele_details->ST_CODE)
							->where('pc_no',$ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->where('finalize','0')->first();
		   
		    
		 	
		 	$val=$this->CountingModel->checkallacfinalize($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
		    
	       if($pc_counting->isEmpty()){
                    \Session::flash('error_mes', 'Counting Data Not exit! Please click activate all AC for counting');
		        	 return Redirect::to('/ropc/counting/prepare-counting');
 		      }


 		    $check_finalized_ro = $this->CountingModel->check_finalized_ro([
		 		'state' 		=> $ele_details->ST_CODE,
		 		'pc_no' 		=> $ele_details->CONST_NO,
		 		'election_id' 	=> $ele_details->ELECTION_ID
		 	]);

		 	if($check_finalized_ro){
		 		\Session::flash('error_mes', 'Total Votes in PC is Not finalized.To Finalize the Total Votes at PC Level, ARO need to Finalize the EVM Votes first in order to get the Finalize Button at PC Level.');
                return Redirect::to('/ropc/counting/postal-data-entry');
		 	}

		   //DD($pc_counting);  
	
	    if(!empty($pc_finalize->finalize)){
           			$finalize=$pc_finalize->finalize; 
               }
             else {$finalize=1; }
           
		return view('admin.pc.counting.counting-results',['user_data' => $d,'sechdul' => $sechdul,'ropc'=>$byro,
								'ele_details'=>$ele_details,'master_data'=>$pc_counting,'finalize'=>$finalize,
								'st_code'=>$ele_details->ST_CODE,'pc_no'=>$ele_details->CONST_NO,'val'=>$val,'winn_data'=>$winn_data]);
		 	           
		        }
		  else {
		        return redirect('/officer-login');
		       }
		}
	function counting_finalized()
			{
			if(Auth::check()){
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
			    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
			    $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			    $seched=getschedulebyid($ele_details->ScheduleID);
                $sechdul=checkscheduledetails($seched);
       	        $byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
			    $val=$this->CountingModel->checkallacfinalize($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
             
              
			$filter='';
             //dd($byro); 
	             $filter = [
					'st_code' 	=> $ele_details->ST_CODE,
					'pc_no' 	=>$ele_details->CONST_NO,
					'election_id'	=> $ele_details->ELECTION_ID,
					'order_by'=>'id'
			     ]; 
        
				$pc_counting =$this->CountingModel->get_allpccandiade ($filter); 
			    $winn_data=$this->CountingModel->winn_lead($filter); 

				$pc_finalize=DB::table('counting_pcmaster')->select('finalize')->where('st_code', $ele_details->ST_CODE)
									->where('pc_no',$ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->where('finalize','0')->first();
				 
		
		if(!isset($pc_finalize)) {
              \Session::flash('success_admin', 'Please finalize your AC');
               return Redirect::to('/ropc/counting/listac');
            }   
	
	    if(!empty($pc_finalize->finalize)){
           			$finalize=$pc_finalize->finalize; 
               }
             else {$finalize=0; }
             $otp='';	 
     

	     
			return view('admin.pc.counting.counting_finalize',['user_data' => $d,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'ropc'=>$byro,'ele_details'=>$ele_details,'master_data'=>$pc_counting,'otp'=>$otp,'finalize'=>$finalize,'winn_data'=>$winn_data,'st_code'=>$ele_details->ST_CODE,'pc_no'=>$ele_details->CONST_NO]);	           
		           
		        }
		        else {
		              return redirect('/officer-login');
		        	 }
			}

	function results_declaration(Request $request)
			{
			if(Auth::check()){
			    $user = Auth::user();
			 	$d=$this->commonModel->getunewserbyuserid($user->id);
	              $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
               
           $filter='';
             //dd($byro); 
	             $filter = [
					'st_code' 	=> $ele_details->ST_CODE,
					'pc_no' 	=>$ele_details->CONST_NO,
					'election_id'	=> $ele_details->ELECTION_ID,
					'order_by'=>'id'
			     ]; 
        
				$pc_counting =$this->CountingModel->get_allpccandiade ($filter); 
			    $winn_data=$this->CountingModel->winn_lead($filter); 

			$pc_finalize=DB::table('counting_pcmaster')->select('finalize')->where('st_code', $ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->where('finalize','0')->first();
		   
		 	$val = $this->CountingModel->checkallacfinalize($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);

		 	$check_finalized_ro = $this->CountingModel->check_finalized_ro([
		 		'state' 		=> $ele_details->ST_CODE,
		 		'pc_no' 		=> $ele_details->CONST_NO,
		 		'election_id' 	=> $ele_details->ELECTION_ID
		 	]);


		   
			if($val == 1 || $check_finalized_ro){
			 	\Session::flash('error_mes', 'Total Votes in PC is Not finalized.To Finalize the Total Votes at PC Level, ARO need to Finalize the EVM Votes first in order to get the Finalize Button at PC Level.');
                return Redirect::to('/ropc/counting/postal-data-entry');	
			}
			  
 			$date = Carbon::now()->subMinutes(10);
            $currentTime = $date->format('Y-m-d H:i:s');
            $currentdate = $date->format('Y-m-d');
			$n_data=array('status'=>'1','added_update_at'=>$currentdate,'updated_at'=>$currentTime);
			 DB::beginTransaction();
        		try{    
			$leading_id=$request->input('leading_id');
            $this->commonModel->updatedata('winning_leading_candidate','leading_id',$leading_id,$n_data);
              $filter = [
			'st_code' 	=> $ele_details->ST_CODE,
			'pc_no' 	=>$ele_details->CONST_NO,
			'status'	=> '1'
		     ];
		      if( $this->mongo_sync){
				     // API of Mango Node JS 	
                    updateWinningLeadingStatus($filter);
                   // End Api
                }
              DB::commit();
    		 }
		        catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('unsuccess_insert', 'Request timeout. Please try again');
		            return Redirect::back();
		        }
		      
            
			     \Session::flash('success_mes', 'This Result is Declared Successfully');
	                		return Redirect::to('/ropc/counting/counting-results');     	
		           
		        }
		        else {
		              return redirect('/officer-login');
		        	  }
			}
   
     function finalize_evm_rounds(Request $request)
    		{
    		if(Auth::check()){ 
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
          
			 $new_table=strtolower("counting_master_".$d->st_code);
			 $ac_details=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
			 $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');

		$round_details=DB::table('round_master')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $d->ac_no)
								->where('ELECTION_ID', $ele_details->ELECTION_ID)->first();
        $c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('ac_no', $d->ac_no)
        						->where('ELECTION_ID',$ele_details->ELECTION_ID)->first();
         
        $complete_round=0; $finalized_round=0;
         if(isset($c_data)){
         	$complete_round=$c_data->complete_round; $finalized_round=$c_data->finalized_round;
            }
        if($round_details->scheduled_round==$complete_round)
		   {  
		
		   	$n_data = array('finalized_round'=>'1','updated_at'=>date("Y-m-d H:i:s"),'added_update_at'=>date("Y-m-d"));    
            $c = array('finalized_ac'=>'1','updated_at'=>date("Y-m-d H:i:s"),'added_update_at'=>date("Y-m-d"));
      DB::beginTransaction();
        try{  
		
			DB::table($new_table)->where('ac_no',$d->ac_no)->where('ELECTION_ID',$ele_details->ELECTION_ID)->update($n_data);
			DB::table('round_master')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$d->ac_no)
													->where('ELECTION_ID',$ele_details->ELECTION_ID)->update($c);
			DB::table('counting_finalized_ac')->where('st_code',$d->st_code)->where('pc_no',$d->pc_no)->where('ac_no',$d->ac_no)
											 	->where('ELECTION_ID',$ele_details->ELECTION_ID)->update($c);
             DB::commit();
    		 }
		        catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('unsuccess_insert', 'Request timeout. Please try again');
		            return Redirect::back();
		        }
		       
		   	 \Session::flash('success_admin', 'Evm Rounds Successfully finalized');
             return Redirect::to('/aro/counting/counting-data-entry'); 
		   	} 
         else {
            \Session::flash('error_mes', 'All rounds are not completed, Please complete your rounds for finalizing.');
             return Redirect::to('/aro/counting/counting-data-entry');	      
               		   
            } 
			   
		  }
		   else {
		         return redirect('/officer-login');
		         }	
    		}
     
   function activate_allac()
   			{
   			if(Auth::check()){ 
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
            	 $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
		    	$new_table=strtolower("counting_master_".$d->st_code);
		    	
		    	$date = Carbon::now();
             	$currentTime = $date->format('Y-m-d H:i:s');
             	$currentdate = $date->format('Y-m-d');
	    	if($ele_details->CONST_TYPE!='PC') {
	    		\Session::flash('error_mes', 'Election Sechedule not define');
	             return Redirect::to('/ropc/counting/listac'); 
	    		}
			$record=$this->CountingModel->getallacbypcno($ele_details->ST_CODE,$ele_details->CONST_NO);
			  
			$cand_data=$this->CountingModel->cantestesting_nomination($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
           
	 		if (Schema::hasTable($new_table))
				{   
				  //echo "ok";
				} 
			else {
			    \DB::statement('CREATE TABLE '.$new_table.' LIKE counting_master_stcode');
                }
          DB::beginTransaction();
        		try{       
		         foreach($cand_data as $list){
					    $check = DB::table('counting_pcmaster')->where('nom_id',$list->nom_id)
					    ->where('st_code',$list->st_code)->where('pc_no',$list->pc_no)
					    ->where('election_id',$list->election_id)->first();
						  
							if(!isset($check)){
							$can=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$list->candidate_id);
							$p=getpartybyid($list->party_id);
		                           $ca_data = array('nom_id'=>$list->nom_id,'candidate_id'=>$list->candidate_id,'st_code'=>$list->st_code,'pc_no'=>$list->pc_no,'election_id'=>$list->election_id,'created_at'=>date("Y-m-d h:m:s"),'created_by'=>$d->officername,'candidate_name'=>$can->cand_name,'candidate_hname'=>$can->cand_hname,'party_id'=>$list->party_id,'party_abbre'=>$p->PARTYABBRE,'party_habbre'=>$p->PARTYHABBR,'party_name'=>$p->PARTYNAME,'party_hname'=>$p->PARTYHNAME,'added_create_at'=>$currentdate,'created_by'=>$d->officername); 
		                           $this->commonModel->insertData('counting_pcmaster', $ca_data);
		                          	}
								   }        
					 
				 foreach($record as $r)
					 {   
					 foreach($cand_data as $list){
					    $check = DB::table($new_table)->where('nom_id',$list->nom_id)->where('ac_no',$r->AC_NO)->where('pc_no',$list->pc_no)->where('election_id',$list->election_id)->first();
						  
							if(!isset($check)){
								$can=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$list->candidate_id);
								$p=getpartybyid($list->party_id);
		                           $ca_data = array('nom_id'=>$list->nom_id,'candidate_id'=>$list->candidate_id,'ac_no'=>$r->AC_NO,'pc_no'=>$list->pc_no,'election_id'=>$list->election_id,'created_at'=>$currentTime,'created_by'=>$d->officername,'added_create_at'=>$currentdate,'candidate_name'=>$can->cand_name,'party_id'=>$list->party_id,'party_abbre'=>$p->PARTYABBRE,'party_name'=>$p->PARTYNAME,'candidate_hname'=>$can->cand_hname,'party_habbre'=>$p->PARTYHABBR,'party_hname'=>$p->PARTYHNAME,'month'=>date("m"),'year'=>date("Y")); 
		                           $this->commonModel->insertData($new_table, $ca_data);
		                          	}
								   } 
					 }
				$lis_st=$this->commonModel->getstatebystatecode($ele_details->ST_CODE);
		        $lis_pc=$this->commonModel->getpcbypcno($ele_details->ST_CODE,$ele_details->CONST_NO);

				$check_d=DB::table('winning_leading_candidate')->where('st_code',$ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->first();   
				if(!isset($check_d)){	  
				 $winn_data=array('election_id'=>$ele_details->ELECTION_ID,'constituency_type'=>$ele_details->CONST_TYPE,'st_code'=>$ele_details->ST_CODE,'st_name'=>$lis_st->ST_NAME,'st_hname'=>$lis_st->ST_NAME_HI,'pc_no'=>$ele_details->CONST_NO,'pc_name'=>$lis_pc->PC_NAME,'pc_hname'=>$lis_pc->PC_NAME_HI,'created_at'=>date("Y-m-d h:m:s"),'added_create_at'=>$currentdate);
				   $this->commonModel->insertData('winning_leading_candidate', $winn_data);
		          }    
		         }
		        catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('unsuccess_insert', 'Request timeout. Please try again');
		            return Redirect::back();
		        }
		        DB::commit();


        		\Session::flash('success_admin', 'Your Ac is activated');
                 return Redirect::to('/ropc/counting/listac');
			   
		  }
		   else {
		         return redirect('/officer-login');
		         }	
    		}	
   	

   	function round_wise_entry()
   			{
   			if(Auth::check()){ 
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
                $new_table=strtolower("counting_master_".$d->st_code);
			    $ac_details=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
				
				 $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
				$round_details=DB::table('round_master')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->first();
               
              if(!isset($round_details)) {
                			\Session::flash('success_admin', 'Round Schedule Not Created! Please Fill Round Schedule Details First.');
                			 return Redirect::to('aro/counting/round-schedule');
                }  
        		$filter='';
             //dd($byro); 
	             $filter = [
					'st_code' 	=> $ele_details->ST_CODE,
					'pc_no' 	=>$ele_details->CONST_NO,
					'election_id'	=> $ele_details->ELECTION_ID,
					'order_by'=>'id'
			     ]; 
        
				 
			    $winn_data=$this->CountingModel->winn_lead($filter);  
        		$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('ac_no', $d->ac_no)->where('election_id',$ele_details->ELECTION_ID)->orderBy('id')->first();
        		$master_data=DB::table($new_table)->where('ac_no',$d->ac_no)->where('election_id',$ele_details->ELECTION_ID)->orderBy('id')->get();       

         
		 		return view('admin.pc.counting.round-wise-entry',['user_data' =>$d,'ac_details'=>$ac_details,'ele_details'=>$ele_details,'round_details'=>$round_details,'master_data'=>$master_data,'new_table'=>$new_table,'winn_data'=>$winn_data]);	
			 
               }
		        else {
		              return redirect('/officer-login');
		        	  }	
   			}	
   	

   	//edit
   	function counting_finalized_verify(Request $request)
    		{
    		if(Auth::check()){ 
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
       

		 $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
		 $val=$this->CountingModel->checkallacfinalize($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
      
		$pv=$this->CountingModel->checkpostalentry($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);	 
		 if($pv==0){
			 			\Session::flash('error_mes', 'Postal Votes Not Entry');
                		return Redirect::to('/ropc/counting/postal-data-entry');	
			 } 
        
	 
		DB::beginTransaction();
        		try{  
		    $c = array('finalize'=>'1');   
		    $cc = array('finalize_by_ro'=>'1', 'finalize_date'=>date("Y-m-d"),'updated_at_ro' => date("Y-m-d H:i:s"));

			  DB::table('counting_pcmaster')->where('st_code',$ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)
			 									->where('ELECTION_ID',$ele_details->ELECTION_ID)->update($c);
              DB::table('counting_finalized_ac')->where('st_code',$ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)
              												->where('ELECTION_ID',$ele_details->ELECTION_ID)->update($cc);

		   	
             }
		        catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('unsuccess_insert', 'Request timeout. Please try again');
		            return Redirect::back();
		        }
		        DB::commit();

		         \Session::flash('success_admin', 'Successfully finalized');
             return Redirect::to('/ropc/counting/postal-data-entry');   
		   	} 
       
			 
		   
		   else {
		         return redirect('/officer-login');
		         }	
    	}	
    public function prepear_counting()
	    {   
	     if(Auth::check()){
		    $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
			 $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
			    $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			    // dd( $check_finalize);
			    $seched=getschedulebyid($ele_details->ScheduleID);
                $sechdul=checkscheduledetails($seched);
       	        $byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
	 		$pc_counting=DB::table('counting_pcmaster')->where('st_code', $ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->get(); 
	        
           //
		    return view('admin.pc.counting.prepear_counting',['user_data' => $d,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'ropc'=>$byro,'pc_counting'=>$pc_counting,'pc_no'=>$ele_details->CONST_NO,'st_code'=>$ele_details->ST_CODE,'ele_details'=>$ele_details]);	           
	        }
	        else {
	              return redirect('/officer-login');
	        	  }
	    }  // end index function     createcenter	

	public function counting_details($dis_ac = "All", Request $request){   

		if(!Auth::check()){
	   		return redirect('/officer-login'); 
	   	}

	   	$dis_ac = NULL;
		if($request->has('dis_ac')){
			$dis_ac = $request->dis_ac;
		}
	
		if($request->has('dis_ac')){
			$check_ac_exist = DB::table('m_ac')->where([
				'ST_CODE' 	=> Auth::user()->st_code,
				'pc_no' 	=> Auth::user()->pc_no,
				'ac_no'     => $dis_ac
			])->first();

			if(!$check_ac_exist){
				\Session::flash('error_mes', 'You have enter the wrong AC.');
		        return Redirect::to('/ropc/counting-details');
			}
		}

		     if(Auth::check()){
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
				 $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
				 $new_table=strtolower("counting_master_".$d->st_code);
				 
	       	     $byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
	       	       $filter=''; $filter_max='';
	       	     $list_allac=getallacbypcno($ele_details->ST_CODE,$ele_details->CONST_NO);

	       	 	$filter_max = [
	       	 		'st_code'	=> $d->st_code,
	       	 		'pc_no'		=> $ele_details->CONST_NO,
	       	 		'ac_no' 	=> $dis_ac
	       	 	];

	       	    $filter 	= [
	       	    	'ac_no' 	=> $dis_ac,
	       	    	'pc_no' 	=> $ele_details->CONST_NO,
	       	    	'group_by' 	=> 'nom_id',
	       	    	'order_by' 	=> 'id'
	       	    ];
                
	      	 	$filterw='';
             //dd($byro); 
	             $filterw = [
					'st_code' 	=> $ele_details->ST_CODE,
					'pc_no' 	=>$ele_details->CONST_NO,
					'election_id'	=> $ele_details->ELECTION_ID,
					'order_by'=>'id'
			     ]; 
        
				 
			    $winn_data=$this->CountingModel->winn_lead($filterw);  


	       	 	$result=$this->CountingModel->get_all_acsum( $new_table, $filter);
	       	 	$rounds=$this->CountingModel->get_max_rounds($filter_max);

	       	  $filter_def = ['st_code'=>$ele_details->ST_CODE,
             			'pc_no'=>$ele_details->CONST_NO,
             			'ac_no'=> $dis_ac,
             			'election_id'=>$ele_details->ELECTION_ID,
             		 ]; 		   
               $def_round=$this->CountingModel->defected_rounds_details($filter_def);

                

		 		 // dd($result);
		 		 // $result=$this->CountingModel->getallpcrecords($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
		 		 // dd( $result);
	           //
			     return view('admin.pc.counting.counting-details',['user_data' => $d, 'ropc'=>$byro,'rounds'=>$rounds,'result'=>$result, 'ele_details'=>$ele_details,'list_allac'=>$list_allac,'dis_ac'=>$dis_ac,'winn_data'=>$winn_data,'def_round'=>$def_round]);	           
		        }
		        else {
		              return redirect('/officer-login');
		        	  }
		    }  // end 


		public function ac_wise_counting(Request $request)
       			{
				  $dis_ac=$request->input('dis_ac');
				 dd( $dis_ac);
				   return Redirect::to('/ropc/counting-details/'.$dis_ac);
       			}

    public function counting_dashboard()
    		   {
    			 if(Auth::check()){
    		  
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
				 $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
				 $new_table=strtolower("counting_master_".$d->st_code);	 
				 $winn_data=DB::table('winning_leading_candidate')->select('leading_id','st_code','ac_no','nomination_id','candidate_id','trail_nomination_id','trail_candidate_id','lead_total_vote','trail_total_vote','margin', 'status','lead_cand_name','lead_cand_hname','lead_cand_party','lead_cand_hparty','trail_cand_name','trail_cand_hname','trail_cand_party','trail_cand_hparty')->where('st_code',$ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->first();  
				 
	       	     $byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
	       	       $filter=''; $filter_max='';
	       	     $results=$this->CountingModel->getallpcrecords($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
	       	    //dd($results);
			     return view('admin.pc.counting.counting-dashboard',['user_data' => $d, 'ropc'=>$byro, 'result'=>$results, 'ele_details'=>$ele_details,'winn_data'=>$winn_data]);	           
		        }
		        else {
		              return redirect('/officer-login');
		        	  }
		    }  // end 




	//waseem 2019-04-30 RO LEVEL EDITING
		public function counting_details_edit($ac_no, $round, Request $request){

			$round 			= 	base64_decode($round);
			$ac_no 			= 	base64_decode($ac_no);

			if(!Auth::check()){
	   			return redirect('/officer-login'); 
	   		}

	        if((int)$round>51 || (int)$round <1){
	        	return redirect('/officer-login');
	        }

			$user 			= Auth::user();
			$d 				= $this->commonModel->getunewserbyuserid($user->id);
			$new_table 		= 	strtolower("counting_master_".$d->st_code);

			$ac_details 	= 	$this->commonModel->getacbyacno($d->st_code,$ac_no);
			$ele_details 	= 	$this->commonModel->election_details($d->st_code,$ac_no,$d->pc_no,$d->id,'PC');
		  	$check_finalize = 	candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			$seched 		= 	getschedulebyid($ele_details->ScheduleID);
	        $sechdul 		=	checkscheduledetails($seched); 
	    			     
	        $round_details = DB::table('round_master')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ac_no)->where('election_id', $ele_details->ELECTION_ID)->first();
	                
	        $winn_data 	= 	DB::table('winning_leading_candidate')->select('leading_id','st_code','ac_no','nomination_id','candidate_id','trail_nomination_id','trail_candidate_id','lead_total_vote','trail_total_vote','margin', 'status')
	        				->where('st_code',$ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)
	        				->where('election_id',$ele_details->ELECTION_ID)->first();
	        
	        if(!isset($round_details)) {
	        	\Session::flash('success_admin', 'Round Schedule Not Created! Please Fill Round Schedule Details First.');
	            return Redirect::to('ropc/counting-details');
	        }   

	        if( $winn_data->status==1) {
	        	\Session::flash('success_admin', 'Result is decelered ! Can not Modified');
	            return Redirect::to('ropc/counting-details');
	        }  
	        $c_data 	= 	DB::table($new_table)->select('complete_round','finalized_round')->where('ac_no', $ac_no)
	        						->where('election_id',$ele_details->ELECTION_ID)->orderBy('id')->first();
             
	        if(!empty($c_data->complete_round)){  
       			$complete_round=$c_data->complete_round; 
       			$finalized_round=$c_data->finalized_round;
       			$n=$complete_round+1;
	        }else {
	        	$complete_round=0; 
	        	$finalized_round=0;  
	        	$n=$complete_round+1;
	        }
	        
	        if($round!=''){ 
	        	$n=$round; 
	        }
	        $field="round".$n;
	        $master_data 	= 	DB::table($new_table)->select('*', $field)->where('ac_no', $ac_no)->where('election_id',$ele_details->ELECTION_ID)->orderBy('id')->get();


	        $total_vote = 0;

	        if($round > $complete_round){
	        	\Session::flash("error_mes","Round is not filled by ARO. Please ask ARO to Fill Vote Count First in order to edit it.");
	        	return \Redirect::to("ropc/counting-details?dis_ac=".$ac_no);
	        }

	                        
	        $data = [
	        	'user_data' 	=> 	$d, 
	        	'ac_details' 	=>	$ac_details,
	        	'ele_details' 	=>	$ele_details,
	        	'round_details' =>	$round_details,
	        	'master_data' 	=>	$master_data,
	        	'new_table' 	=>	$new_table,
	        	'rid' 			=>	$round,
	        	'comp_round' 	=>	$complete_round,
	        	'field' 			=>	$field,
	        	'finalized_round' 	=>	$finalized_round,
	        	'winn_data' 		=>	$winn_data,
	        	'total_vote'		=> $total_vote,
	        	'ac_no'				=> $ac_no
	        ];

	        return view('admin.pc.counting.dataentrysechudle_edit',$data);
		}


		public function update_round_by_ro(Request $request){
			 //dd($request);  
			 if(!Auth::check()){
	   			return redirect('/officer-login'); 
	   		}

	      	DB::beginTransaction();
       		try{

			    $user = Auth::user();

			    $d=$this->commonModel->getunewserbyuserid($user->id);
			    $cschedule = $this->xssClean->clean_input($request->input('cschedule'));
		        $totalround = $this->xssClean->clean_input($request->input('totalround'));
		        //$new_table = $this->xssClean->clean_input($request->input('new_table'));

		        $leading_id = $this->xssClean->clean_input($request->input('leading_id'));
		        $ST_CODE = $this->xssClean->clean_input($request->input('ST_CODE'));
		        $CONST_TYPE = $this->xssClean->clean_input($request->input('CONST_TYPE'));
		        $CONST_NO = $this->xssClean->clean_input($request->input('CONST_NO'));
		        $ELECTION_ID=$this->xssClean->clean_input($request->input('ELECTION_ID'));
		        $nrid=$this->xssClean->clean_input($request->input('nrid'));   //
		        $val = $this->xssClean->clean_input($request->input('val'));
				$input = $request->all();
 
					$date = Carbon::now();
             		$currentTime = $date->format('Y-m-d H:i:s');
             		$currentdate = $date->format('Y-m-d');  
				 if(!empty($cschedule)) $newcschedule=$cschedule+1; else $newcschedule='';   

				$rules = ['Please enter all new serial number'];
				$total_voters = 0;

				for ($i=1; $i<=$val;$i++){  

				    $this->validate($request, [
					    	'currentvote'.$i => 'required|digits_between:0,999999',
					    	'cschedule' => 'required|numeric'
					    ],
		                [
			                'currentvote'.$i.'required' => 'Please enter current vote ',
			                'currentvote'.$i.'numeric' => 'Please enter integer value ',
			                'currentvote'.$i.'digits_between' => 'Please enter integer value max 999999 ',
			                'currentvote'.$i.'integer' => 'Please enter integer value ',
			                'currentvote'.$i.'regex' => 'Please enter integer value ',
			                'cschedule.required' => 'Please select select round',
		                ]);	

				  	$total_voters += $input['currentvote'.$i];

			    }

			    if($total_voters != $request->total){
			    	\Session::flash('error_mes', 'Total value is wrong.');
			    	return Redirect::back()->withInput($request->all());
			    }

               
        
       		$new_table=strtolower("counting_master_".$d->st_code);
 			for ($i=1; $i<=$val; $i++)
			  	{
			      	$mid=$this->xssClean->clean_input($request->input('mid'.$i));
			       	$nom_id=$this->xssClean->clean_input($request->input('nom_id'.$i));
			       	$currentvote=$this->xssClean->clean_input($request->input('currentvote'.$i));
			        $priviousvote=$this->xssClean->clean_input($request->input('priviousvote'.$i));
			       	$round="round".$cschedule;
			
			       	$filter_ele = ['id'=>$mid,'nom_id'=>$nom_id,'ac_no'=> $d->ac_no];
			       	$total_value='';
			       	$total_value=$this->CountingModel->grandtotalsum($new_table,$round,$filter_ele);
                         
			        $total_vote   = 0; 
			        $round_vote=0;

           		    if(isset($total_value) && $total_value){
			            $total_vote   	= $total_value->grant_total;
			            $round_vote		= $total_value->$round;
			          
			        }
			        $total_vote= ($total_vote-$round_vote)+$currentvote;
				  
 					if($nrid==0){
			         	$n_data = array($round=>$currentvote,'total_vote'=>$total_vote, 'added_update_at'=>$currentdate,'updated_at'=>$currentTime,'updated_by'=>$d->officername); 
			       	}else { $nr="round".$nrid;
			          $n_data = array($round=>$currentvote,'total_vote'=>$total_vote,'added_update_at'=>$currentdate,'updated_at'=>$currentTime,'updated_by'=>$d->officername);
			       	}
 			   		
			       	\App\models\Counting\CountingLogModel::clone_record($mid,strtolower($ST_CODE));

			    	DB::table($new_table)->where('id',$mid)->update($n_data);	
		        }



	              $pcentry=$this->CountingModel->totalvotsbypcwise($new_table,$CONST_NO,$ELECTION_ID);
	              $mango_db_array=[];
	              	// dd($pcentry);
	              foreach ($pcentry as $v) {
	              	$postaldata=DB::table('counting_pcmaster')->where('st_code',$ST_CODE)->where('nom_id',$v->nom_id)->where('pc_no',$v->pc_no)->where('ELECTION_ID',$ELECTION_ID)->first();
	              	
	              	//if($postaldata){
	              		$net = $v->sum_total+$postaldata->postal_vote+$postaldata->migrate_votes;
	              	// }else{
	              	// 	$net = $v->sum_total+0;
	              	// }
	              	
	              	 //dd($postaldata->postal_vote); migrate_votes
	              	$w_data=array('evm_vote'=>$v->sum_total,'total_vote'=>$net,'added_update_at'=>$currentdate,'updated_at'=>$currentTime,'updated_by'=>$d->officername);
	              	 //dd($w_data);
	              	  DB::table('counting_pcmaster')->where('st_code',$ST_CODE)->where('nom_id',$v->nom_id)->where('pc_no',$v->pc_no)->where('ELECTION_ID',$ELECTION_ID)->update($w_data);	
	              $data22 = ["add_evm_vote"=>$v->sum_total,'total_vote'=>$net, "nom_id"=>$v->nom_id, "st_code"=>$ST_CODE, "pc_no"=>$v->pc_no];
	              	  $mango_db_array[]=$data22;

	              }
	                 //dd($pcentry);
	              if( $this->mongo_sync){
	              	  updateEvmById($mango_db_array);
				      //End API
				  }
	                  // dd($pcentry);
				       $fdata=$this->CountingModel->selectfirsthightvalueofcounting('counting_pcmaster',$ST_CODE,$CONST_NO,$CONST_TYPE,$ELECTION_ID);
				       $sdata=$this->CountingModel->selectsecondhightvalueofcounting('counting_pcmaster',$ST_CODE,$CONST_NO,$CONST_TYPE,$ELECTION_ID);

				 

                   // if(isset($fdata) and isset($sdata) and ($fdata->max_total !=$sdata->max_total)){
						    $lead_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$fdata->candidate_id);
						    $lead_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$fdata->nom_id);
						    $lead_party=$this->commonModel->selectone('m_party','CCODE',$lead_nom->party_id);
					
					//if(isset($sdata)){$sdata=$fdata;}
		                $trail_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$sdata->candidate_id);
					    $trail_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$sdata->nom_id);
					    $trail_party=$this->commonModel->selectone('m_party','CCODE',$trail_nom->party_id);
					 

				    $margin=$fdata->max_total-$sdata->max_total;
				    $winn_update=array('candidate_id'=>$fdata->candidate_id,
				    				'nomination_id'=>$fdata->nom_id,
				    				'lead_cand_name'=>$lead_cand->cand_name,
				    				'lead_cand_partyid'=>$lead_party->CCODE,
				    				'lead_cand_party'=>$lead_party->PARTYNAME,
				    				'lead_party_type'=>$lead_party->PARTYTYPE,
				    				'lead_party_abbre'=>$lead_party->PARTYABBRE,
				    				'lead_cand_hname'=>$lead_cand->cand_hname,
				    				'lead_cand_hparty'=>$lead_party->PARTYHNAME,
				    				'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
				    				'trail_candidate_id'=>$sdata->candidate_id,
				    				'trail_nomination_id'=>$sdata->nom_id,
				    				'trail_cand_name'=>$trail_cand->cand_name,
				    				'trail_cand_partyid'=>$trail_party->CCODE,
				    				'trail_cand_party'=>$trail_party->PARTYNAME,
				    				'trail_party_type'=>$trail_party->PARTYTYPE,
				    				'trail_party_abbre'=>$trail_party->PARTYABBRE,
				    				'trail_cand_hname'=>$trail_cand->cand_hname,
				    				'trail_cand_hparty'=>$trail_party->PARTYHNAME,
				    				'trail_hpartyabbre'=>$trail_party->PARTYHABBR,
				    				'margin'=>$margin,
				    				'lead_total_vote'=>$fdata->max_total,
				    				'trail_total_vote'=>$sdata->max_total,
				    				'added_update_at'=>$currentdate,
				    				'updated_at'=>$currentTime);
				      //dd($winn_update);
				     DB::table('winning_leading_candidate')->where('leading_id',$leading_id)->update($winn_update);

				      if( $this->mongo_sync){
				     // API of Mango Node JS 
				     $winn_update1=array('st_code'=>$ST_CODE,'pc_no'=>$CONST_NO,'candidate_id'=>$fdata->candidate_id,
				     					'nomination_id'=>$fdata->nom_id,
				     					'lead_cand_name'=>$lead_cand->cand_name,
				     					'lead_cand_hname'=>$lead_cand->cand_hname,
				     					'lead_cand_partyid'=>$lead_party->CCODE,
				     					'lead_cand_party'=>$lead_party->PARTYNAME,
				     					'lead_party_type'=>$lead_party->PARTYTYPE,
				     					'lead_party_abbre'=>$lead_party->PARTYABBRE,
				     					'lead_cand_hparty'=>$lead_party->PARTYHNAME,
				     					'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
				    					'trail_candidate_id'=>$sdata->candidate_id,
				    					'trail_nomination_id'=>$sdata->nom_id,
				    					'trail_cand_name'=>$trail_cand->cand_name,'trail_cand_hname'=>$trail_cand->cand_hname,
				    					'trail_cand_partyid'=>$trail_party->CCODE,'trail_cand_party'=>$trail_party->PARTYNAME,
				    					'trail_party_type'=>$trail_party->PARTYTYPE,'trail_party_abbre'=>$trail_party->PARTYABBRE,
				    					'trail_cand_hparty'=>$trail_party->PARTYHNAME,'trail_hpartyabbre'=>$trail_party->PARTYHABBR,
				    					'margin'=>$margin,'lead_total_vote'=>$fdata->max_total,'trail_total_vote'=>$sdata->max_total);
				     		           updateWinningLeading($winn_update1);

				          //End API
                         }
				    // }   // end if   else part
				    // else     {
				    // 			 $winn_update2=array('candidate_id'=>0,'nomination_id'=>0,'lead_cand_name'=>'',
				    // 			 					'lead_cand_partyid'=>0,'lead_cand_party'=>'',
				    // 	 							'lead_party_type'=>'','lead_party_abbre'=>'','lead_cand_hname'=>'',
				    // 	 							'lead_cand_hparty'=>'','lead_hpartyabbre'=>'',
				    // 								'trail_candidate_id'=>0,'trail_nomination_id'=>0,'trail_cand_name'=>'',
				    // 								'trail_cand_partyid'=>0,'trail_cand_party'=>'',
				    // 								'trail_party_type'=>'','trail_party_abbre'=>'','trail_cand_hname'=>'',
				    // 								'trail_cand_hparty'=>'','trail_hpartyabbre'=>'',
				    // 								'margin'=>0,'lead_total_vote'=>0,'trail_total_vote'=>0);
				    
				    //  			DB::table('winning_leading_candidate')->where('leading_id',$leading_id)->update($winn_update2);
				    // 	       }
				   
				  DB::commit();
			          
			    }
		        catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('error_mes', 'We have encounted an issue. Please try again.');
		            return Redirect::back();
		        }
		       	
		       	$ac_no = 'All';
		       	if($request->has('ac_no')){
		       		$ac_no = $request->ac_no;
		       	}

		       \Session::flash('success_mes', 'Votes updated Successfully.');
              	return Redirect::to('/ropc/counting-details?dis_ac='.$ac_no);	        

		}




	// winner candidate name verifying
	public function verify_winner_by_name(Request $request){
		$data = [];
		if(!Auth::user()){
			$data['warning'] = "Please login to continue.";
		}		

		if(!$request->has('winner_name') || trim($request->winner_name) == ""){
			$data['warning'] = "Please enter the winner name.";
		}

		if(count($data)>0){
			return \Response::json([
				'status'  => false,
				'message' => $data['warning']
			]);
		}
		
		$filter = [
			'st_code' 	=> Auth::user()->st_code,
			'pc_no' 	=> Auth::user()->pc_no,
			'status'	=> '0'
		];

		$result = DB::table("winning_leading_candidate")->where($filter)->first();
        
        
    
   
		if(!isset($result) && !$result){
			return \Response::json([
				'status'  => false,
				'message' => "Please try again."
			]);
		}

		if(strtolower(trim($request->winner_name)) != strtolower(trim($result->lead_cand_name))){
			return \Response::json([
				'status'  => false,
				'message' => "Winner name incorrect. Please enter the correct winner name."
			]);
		}

		return \Response::json([
			'status'  => true,
			'message' => ""
		]);

	}

	public function result_declared_by_lottery(Request $request){

		if(!$request->has('draw_leading_nomination_id') || !$request->has('draw_trailing_nomination_id')){
			return \Response::json([
				'status'  => false,
				'message' => "Winner and loser both are required."
			]);
		}

		if(trim($request->draw_leading_nomination_id) == ''){
			return \Response::json([
				'status'  => false,
				'message' => "Please select a winner."
			]);
		}

		if(trim($request->draw_trailing_nomination_id) == ''){
			return \Response::json([
				'status'  => false,
				'message' => "Please select a loser."
			]);
		}

		if($request->draw_leading_nomination_id == $request->draw_trailing_nomination_id){
			return \Response::json([
				'status'  => false,
				'message' => "Winner and loser can't be same."
			]);
		}

		$user_data = [
			'st_code' 	=> Auth::user()->st_code,
			'pc_no' 	=> Auth::user()->pc_no,
			'ac_no'     => Auth::user()->ac_no,
		];

		$ele_details = $this->commonModel->election_details(Auth::user()->st_code, Auth::user()->ac_no, Auth::user()->pc_no, Auth::id(),'PC');

		if(!$ele_details){
			return \Response::json([
				'status'  => false,
				'message' => "Please refresh page and try again."
			]);
		}

		$user_data 	= [
			'st_code' 		=> Auth::user()->st_code,
			'pc_no' 		=> Auth::user()->pc_no,
			'election_id' 	=> $ele_details->ELECTION_ID,
		];


		$object_leading = DB::table('counting_pcmaster')->leftJoin('m_party','m_party.CCODE','=','counting_pcmaster.party_id')->where(array_merge($user_data,[
			'nom_id' => $request->draw_leading_nomination_id
		]))->select('counting_pcmaster.*','m_party.PARTYTYPE')->first();

		$object_trailing = DB::table('counting_pcmaster')->leftJoin('m_party','m_party.CCODE','=','counting_pcmaster.party_id')->where(array_merge($user_data,[
			'nom_id' => $request->draw_trailing_nomination_id
		]))->select('counting_pcmaster.*','m_party.PARTYTYPE')->first();

		if(!$object_leading || !$object_trailing){
			return \Response::json([
				'status'  => false,
				'message' => "Please refresh page and try again."
			]);
		}

		if($object_leading->party_id == '1180' || $object_trailing->party_id == '1180'){
			return \Response::json([
				'status'  => false,
				'message' => "Nota can't be in winner or traling."
			]);
		}

		try{
			DB::table('winning_leading_candidate')->where($user_data)->update([
				'candidate_id' 		=> $object_leading->candidate_id, 
				'nomination_id' 	=> $object_leading->nom_id, 
				'lead_cand_name' 	=> $object_leading->candidate_name, 
				'lead_cand_hname' 	=> $object_leading->candidate_hname,
				'lead_cand_partyid' => $object_leading->party_id,
				'lead_cand_party' 	=> $object_leading->party_name, 
				'lead_cand_hparty' 	=> $object_leading->party_hname, 
				'lead_party_type' 	=> $object_leading->PARTYTYPE,
				'lead_party_abbre' 	=> $object_leading->party_abbre, 
				'lead_hpartyabbre' 	=> $object_leading->party_habbre, 
				'trail_candidate_id' => $object_trailing->candidate_id, 
				'trail_nomination_id' => $object_trailing->nom_id, 
				'trail_cand_name' 		=> $object_trailing->candidate_name, 
				'trail_cand_hname' 		=> $object_trailing->candidate_hname, 
				'trail_cand_partyid' 	=> $object_trailing->party_id, 
				'trail_cand_party' 		=> $object_trailing->party_name, 
				'trail_cand_hparty' 	=> $object_trailing->party_hname, 
				'trail_party_type' 		=> $object_trailing->PARTYTYPE, 
				'trail_party_abbre' 	=> $object_trailing->party_abbre, 
				'trail_hpartyabbre' 	=> $object_trailing->party_habbre, 
				'lead_total_vote' 	=> $object_leading->total_vote, 
				'trail_total_vote' 	=> $object_trailing->total_vote, 
				'margin' => 0, 
				'status' => 1, 
				'is_lottery' => 1
			]);
		}catch(\Exception $e){
			return \Response::json([
				'status'  => false,
				'message' => "Please refresh page and try again."
			]);
		}

		\Session::flash('success_mes', 'Result successfully updated.');
		return \Response::json([
			'status'  => true,
			'message' => "Result successfully updated."
		]);

	}

	//end of winner candidate verifying

 

public function pdf(Request $request){

			if(!\Auth::user()){
				return false;
			}
			$d 				= \Auth::user();
			$ele_details 	= $this->commonModel->election_details($d->st_code,$d->ac_no,$d->dist_no,$d->id,'PC');


	        if($request->has('print_table') && $request->has('pc_no') && $request->has('round') && $request->has('ac_no')){
	            \Session::put('print_table',$request->print_table);
	            \Session::put('pc_no',$request->pc_no);
	            \Session::put('ac_no',$request->ac_no);
	            \Session::put('round',$request->round);
	        }

	        if(\Session::has('print_table') && \Auth::user()){
	        	$st_name = '';
	        	$state_object = \App\models\Admin\StateModel::get_state_by_code(\Auth::user()->st_code);
	        	if($state_object){
	        		$st_name = $state_object['ST_NAME'];
	        	}
	            $data = [];
	            $data['table']         	= \Session::get('print_table');
	            $data['pc_no']         	= \Session::get('pc_no');
	            $data['ac_no']         	= \Session::get('ac_no');

	            $pc_name = '';
	            $get_pc = $this->commonModel->getpcbypcno(\Auth::user()->st_code,$data['pc_no']);
	            if($get_pc){
	            	$pc_name = $get_pc->PC_NAME;
	            }

	            $ac_name = '';
	            $get_ac = $this->commonModel->getacbyacno(\Auth::user()->st_code,$data['ac_no']);
	            if($get_ac){
	            	$ac_name = $get_ac->AC_NAME;
	            }

	            $data['pc_name']     	= $pc_name;
	            $data['ac_name']     	= $ac_name;
	            $data['round']         	= \Session::get('round');
	            $data['st_code'] 		= \Auth::user()->st_code;
	            $data['heading_title'] 	= '';
	            $data['st_name'] 		= $st_name;
	            $data['election'] 		= @$ele_details->ELECTION_TYPE;

	            $name_excel = 'round'.$data['round'].'_'.$data['ac_no'];

	            //round to be sum and print previous
	            $object = $this->CountingModel->get_previous_total($data);
	        	
	        	$nominator = [];
	        	foreach (explode(',',$data['table']) as $key => $value) {
	        		$explode_array = explode('_', $value);
	        		$nominator[$explode_array[0]] = [
	        			'nom_id' => $explode_array[0],
	        			'vote'   => $explode_array[1]
	        		];
	        	}

	        	$i = 1;
	        	$aggregate_total 			= 0;
	        	$aggregate_previous_total 	= 0;
	        	$aggregate_current_total 	= 0;
	        	foreach ($object as $result) {
	        		$current_total 	= 0;
	        		$total 			= 0;
	        		if(isset($nominator[$result->nom_id])){
	        			$current_total 	= $nominator[$result->nom_id]['vote'];
	        			$total 			= $result->previous_total+$nominator[$result->nom_id]['vote'];
	        		}
	        		$results[] = [
	        			'sr_no' 			=> $i,
	        			'candidate_name' 	=> $result->candidate_name,
	        			'party_name'  		=> $result->party_name,
	        			'total'  			=> format_digit($total),
	        			'previous_total'  	=> format_digit($result->previous_total),
	        			'current_total'  	=> format_digit($current_total),
	        		];
	        		$aggregate_total 			+= $total;
	        		$aggregate_previous_total 	+= $result->previous_total;
	        		$aggregate_current_total 	+= $current_total;
	        		$i++;
	        	}

	        

	        	$results[] = [
	        		'sr_no' 			=> '',
	        		'candidate_name' 	=> '',
	        		'party_name'  		=> 'Total',
	        		'total'  			=> format_digit($aggregate_total),
	        		'previous_total'  	=> format_digit($aggregate_previous_total),
	        		'current_total'  	=> format_digit($aggregate_current_total),
	        	];

	        	$data['results'] = $results;
	        
	            $setting_pdf = [
					'margin_top'        => 80,        // Set the page margins for the new document.
					'margin_bottom'     => 10,    
				];

	            $pdf = \PDF::loadView('admin.pc.counting.pdf',$data,[], $setting_pdf);

	            if($request->has('json')){
	                return \Response::json([
	                    'success' => true
	                ]);
	            }
	            return $pdf->download($name_excel.'_'.time().'.pdf');
	        }else{
	            return \Redirect::to('/officer-login');
	        }
	    }
		
    public function ballot_pdf(Request $request){

    	if(!\Auth::user()){
			return false;
		}
		$d 				= \Auth::user();
		$ele_details 	= $this->commonModel->election_details($d->st_code,$d->ac_no,$d->dist_no,$d->id,'PC');

        if($request->has('print_table') && $request->has('pc_no') && $request->has('round') && $request->has('pc_name')){
            \Session::put('print_table',$request->print_table);
            \Session::put('pc_no',$request->pc_no);
            \Session::put('pc_name',$request->pc_name);
            \Session::put('round',$request->round);
        }

        if(\Session::has('print_table') && \Auth::user()){
        	$st_name = '';
        	$state_object = \App\models\Admin\StateModel::get_state_by_code(\Auth::user()->st_code);
        	if($state_object){
        		$st_name = $state_object['ST_NAME'];
        	}
            $data = [];
            $data['table']         = \Session::get('print_table');
            $data['pc_no']         = \Session::get('pc_no');
            $data['pc_name']     = \Session::get('pc_name');
            $data['round']         = \Session::get('round');
            $data['heading_title'] = '';
            $data['st_name'] 		= $st_name;
            $data['election'] 		= @$ele_details->ELECTION_TYPE;
			
			$pc_name = '';
	            $get_pc = $this->commonModel->getpcbypcno(\Auth::user()->st_code,$data['pc_no']);
	            if($get_pc){
	            	$pc_name = $get_pc->PC_NAME;
	            }

	            $ac_name = '';
	            

	            $data['pc_name']     	= $pc_name;
	            $data['ac_name']     	= $ac_name;
			

            $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

            $setting_pdf = [
				'margin_top'        => 80,        // Set the page margins for the new document.
				'margin_bottom'     => 10,    
			];

            $pdf = \PDF::loadView('admin.pc.counting.ballot_pdf',$data, [], $setting_pdf);
            if($request->has('json')){
                return \Response::json([
                    'success' => true
                ]);
            }
            return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
        }else{
            return \Redirect::to('/officer-login');
        }
    }


    
    public function migrant_pdf(Request $request){
    	
    	if(!\Auth::user()){
			return false;
		}
		$d 				= \Auth::user();
		$ele_details 	= $this->commonModel->election_details($d->st_code,$d->ac_no,$d->dist_no,$d->id,'PC');

        if($request->has('print_table') && $request->has('pc_no') && $request->has('round') && $request->has('pc_name')){
            \Session::put('print_table',$request->print_table);
            \Session::put('pc_no',$request->pc_no);
            \Session::put('pc_name',$request->pc_name);
            \Session::put('round',$request->round);
        }

        if(\Session::has('print_table') && \Auth::user()){
        	$st_name = '';
        	$state_object = \App\models\Admin\StateModel::get_state_by_code(\Auth::user()->st_code);
        	if($state_object){
        		$st_name = $state_object['ST_NAME'];
        	}
            $data = [];
            $data['table']         = \Session::get('print_table');
            $data['pc_no']         = \Session::get('pc_no');
            $data['pc_name']     = \Session::get('pc_name');
            $data['round']         = \Session::get('round');
            $data['heading_title'] = '';
            $data['st_name'] 		= $st_name;
            $data['election'] 		= @$ele_details->ELECTION_TYPE;

            $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
            $setting_pdf = [
				'margin_top'        => 80,        // Set the page margins for the new document.
				'margin_bottom'     => 10,    
			];
            $pdf = \PDF::loadView('admin.pc.counting.migrant_pdf',$data,[],$setting_pdf);
            if($request->has('json')){
                return \Response::json([
                    'success' => true
                ]);
            }
            return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
        }else{
            return \Redirect::to('/officer-login');
        }
    }



   public function tenders_votes(Request $request)
    		   {
    			 if(Auth::check()){
    		    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
				 $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
				$this->validate(
	                $request, 
	                    [
	                      'tended_votes'=> 'required|digits_between:0,999999',
	                    ],
	                    [
	                      'tended_votes.required' => 'Please enter round schedule ',
	                      'tended_votes.digits_between' => 'Please enter numeric value',
	                      
	                    ]);  
				$st_code=$this->xssClean->clean_input($request->input('st_code'));
				$pc_no=$this->xssClean->clean_input($request->input('pc_no'));
				$tended_votes=$this->xssClean->clean_input($request->input('tended_votes'));
				 DB::beginTransaction();
        		try{ 
				   $n_data = array('tended_votes'=>$tended_votes,'updated_at'=>date("Y-m-d H:i:s"),'added_update_at'=>date("Y-m-d"),'tended'=>1); 
			       DB::table('counting_pcmaster')->where('st_code',$st_code)->where('pc_no',$pc_no)->update($n_data);	  
				DB::commit(); 
				}
		        catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('unsuccess_insert', 'Please try again');
		            return Redirect::back();
		        }
		         

				  \Session::flash('success_mes', 'Tendered Votes updated Successfully.');
              	  return Redirect::to('/ropc/counting/counting-results');	         
		        }
		        else {
		              return redirect('/officer-login');
		        	  }
		    }  // end 

         function migrate_votes()
			{
			if(Auth::check()){
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
			  
			   

		      $pc_finalize=DB::table('counting_pcmaster')->select('finalize')->where('st_code', $ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->where('finalize','0')->first();
 

				  if(!isset($pc_finalize)) {
			        \Session::flash('success_admin', 'Your Postal Ballot is Successfully finalized.');
			        return Redirect::to('ropc/counting-details');
			       }   
			 $filter='';
             //dd($byro); 
	             $filter = [
					'st_code' 	=> $ele_details->ST_CODE,
					'pc_no' 	=>$ele_details->CONST_NO,
					'election_id'	=> $ele_details->ELECTION_ID,
					'order_by'=>'id'
			     ]; 
        
				$pc_counting =$this->CountingModel->get_allpccandiade ($filter); 
			    $winn_data=$this->CountingModel->winn_lead($filter); 
		 
		        $val=$this->CountingModel->checkallacfinalize($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
	 
 
	
	    if(!empty($pc_finalize->finalize)){
           			$finalize=$pc_finalize->finalize; 
               }
             else {$finalize=0; }
       
		return view('admin.pc.counting.migrate-votes',['user_data' => $d,'ele_details'=>$ele_details,'master_data'=>$pc_counting,
				'finalize'=>$finalize, 'winn_data'=>$winn_data,'st_code'=>$ele_details->ST_CODE,'pc_no'=>$ele_details->CONST_NO,'val'=>$val]);          
		        }
		        else {
		              return redirect('/officer-login');
		        	 }
			}
	function verify_migrate_votes(Request $request)
			{
			 
			 if(Auth::check()){ 
			    $user = Auth::user();
			        $user = Auth::user();
			 		$d=$this->commonModel->getunewserbyuserid($user->id);
			 		 $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
             $pc_finalize=DB::table('counting_pcmaster')->select('finalize')->where('st_code', $ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->where('finalize','0')->first();
               

				  if(!isset($pc_finalize)) {
			        \Session::flash('success_admin', 'Your Postal Ballot is Successfully finalized.');
			        return Redirect::to('ropc/counting-details');
			       }   
		        $new_table = $this->xssClean->clean_input($request->input('new_table'));
		        $round_id = $this->xssClean->clean_input($request->input('round_id'));
		         
		        $totalvotes = $this->xssClean->clean_input($request->input('totalvotes'));
		        $leading_id = $this->xssClean->clean_input($request->input('leading_id'));
		        $ST_CODE = $this->xssClean->clean_input($request->input('ST_CODE'));
		        $CONST_TYPE = trim($request->input('CONST_TYPE'));
		        $CONST_NO = $this->xssClean->clean_input($request->input('CONST_NO'));
		        $ELECTION_ID=$request->input('ELECTION_ID');
  				$val = trim($request->input('val'));
  				 
 				 
				$input = $request->all();
				//  dd($input);
				  
				$total=0;
				$rules = ['Please enter postal vote'];
				for ($i=1; $i<=$val;$i++)
				    {
				     $this->validate($request, [
				     	'currentvote'.$i => 'required|digits_between:0,999999',
				     	'totalvotes'=> 'required|digits_between:0,9999999999999',
				     	 
				     ],
		                [
		                'currentvote'.$i.'required' => 'Please enter postal vote ',
		                'currentvote'.$i.'regex' => 'Please enter valide votes',
		                'currentvote'.$i.'numeric' => 'Please enter valide votes',
		                'currentvote'.$i.'integer' => 'Please enter valide votes',
		                'currentvote'.$i.'digits_between' => 'Please enter valide votes',
		                'totalvotes.required' => 'Please enter Total Votes',
		                'totalvotes.digits_between' => 'Please enter valide votes',
		                 
		                ]);	
			        }
			    for ($i=1; $i<=$val;$i++)
				    {
				    	$cv=trim($request->input('currentvote'.$i));
				        $total=$total+$cv;
				     }
			      

			    if($totalvotes== $total)  {
			  DB::beginTransaction();
		       	try{
 				for ($i=1; $i<=$val;$i++)
			       	{
			       	$mid=$this->xssClean->clean_input($request->input('mid'.$i));
			       	$nom_id=$this->xssClean->clean_input($request->input('nom_id'.$i));
			       	$currentvote=(int)$this->xssClean->clean_input($request->input('currentvote'.$i));
			       	$priviousvote=(int)$this->xssClean->clean_input($request->input('priviousvote'.$i));
			       	$postaldata=DB::table('counting_pcmaster')->where('nom_id',$nom_id)->where('pc_no',$CONST_NO)->where('ELECTION_ID',$ELECTION_ID)->first();
	              	$evm_vote=0; $postal_vote=0;
	              	if(isset($postaldata)) { $evm_vote=$postaldata->evm_vote; $postal_vote=$postaldata->postal_vote; } 
	              	else { $evm_vote=$postaldata->evm_vote; $postal_vote=$postaldata->postal_vote; }

			        
			        $total_vote= $evm_vote+$currentvote+$postal_vote;
			       	$n_data = array('total_vote'=>$total_vote,
			       					'migrate_votes'=>$currentvote,
			       					'added_update_at'=>date("Y-m-d"),
			       					'updated_at'=>date("Y-m-d h:i:s"),'updated_by'=>$d->officername); 
			       	//dd($n_data);

			       	//log psotal ballot
			       	//\App\models\Counting\CountingLogModel::clone_postal_ballot($mid);

			        DB::table('counting_pcmaster')->where('id',$mid)->update($n_data);	
			        
			       	}
			        
 
			         $fdata=$this->CountingModel->selectfirsthightvalueofcounting('counting_pcmaster',$ST_CODE,$CONST_NO,$CONST_TYPE,$ELECTION_ID);
				     $sdata=$this->CountingModel->selectsecondhightvalueofcounting('counting_pcmaster',$ST_CODE,$CONST_NO,$CONST_TYPE,$ELECTION_ID);
                   
                    
                    //if(isset($fdata) and isset($sdata) and ($fdata->max_total !=$sdata->max_total)){
						    $lead_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$fdata->candidate_id);
						    $lead_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$fdata->nom_id);
						    $lead_party=$this->commonModel->selectone('m_party','CCODE',$lead_nom->party_id);
					
					//if(isset($sdata)){$sdata=$fdata;}
		                $trail_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$sdata->candidate_id);
					    $trail_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$sdata->nom_id);
					    $trail_party=$this->commonModel->selectone('m_party','CCODE',$trail_nom->party_id);
					 
                   // dd("hello");
				    $margin=$fdata->max_total-$sdata->max_total;
				    $winn_update=array('candidate_id'=>$fdata->candidate_id,
				    					'nomination_id'=>$fdata->nom_id,
				    					'lead_cand_name'=>$lead_cand->cand_name,
				    					'lead_cand_partyid'=>$lead_party->CCODE,
				    					'lead_cand_party'=>$lead_party->PARTYNAME,
				    					'lead_party_type'=>$lead_party->PARTYTYPE,
				    					'lead_party_abbre'=>$lead_party->PARTYABBRE,
				    					'lead_cand_hname'=>$lead_cand->cand_hname,
				    					'lead_cand_hparty'=>$lead_party->PARTYHNAME,
				    					'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
				    					'trail_candidate_id'=>$sdata->candidate_id,
				    					'trail_nomination_id'=>$sdata->nom_id,
				    					'trail_cand_name'=>$trail_cand->cand_name,
				    					'trail_cand_partyid'=>$trail_party->CCODE,
				    					'trail_cand_party'=>$trail_party->PARTYNAME,
				    					'trail_party_type'=>$trail_party->PARTYTYPE,
				    					'trail_party_abbre'=>$trail_party->PARTYABBRE,
				    					'trail_cand_hname'=>$trail_cand->cand_hname,
				    					'trail_cand_hparty'=>$trail_party->PARTYHNAME,
				    					'trail_hpartyabbre'=>$trail_party->PARTYHABBR,
				    					'margin'=>$margin,'lead_total_vote'=>$fdata->max_total,
				    					'trail_total_vote'=>$sdata->max_total,'added_update_at'=>date("Y-m-d"),
				    					'updated_at'=>date("Y-m-d H:i:s"));
				   //dd($winn_update);
				     DB::table('winning_leading_candidate')->where('leading_id',$leading_id)
				     				->where('st_code',$ST_CODE)->where('pc_no',$CONST_NO)
				     				->where('ELECTION_ID',$ELECTION_ID)->update($winn_update);
				   // }

                     DB::commit();
			          
			    }
		        catch(\Exception $e){
		            DB::rollback();
		            \Session::flash('error_mes', ' Please try again.');
		            return Redirect::back();
		        }

				         \Session::flash('success_mes', 'This Migrant Vote Successfully Updated.');
	                		return Redirect::to('/ropc/counting/migrate-votes');	        
		        }
		        else {
                        \Session::flash('error_mes', 'Total Votes and Migrant Votes Miss-Matched');
                		 return Redirect::to('/ropc/counting/migrate-votes');	
		        }
		    }
		        else {
		              return redirect('/officer-login');
		        	  }
			}
			
			
	public function get_round_report(Request $request){
		$action = "ropc/counting/round-report";
		$data 					= [];
		$d 						= Auth::user();
		$data['heading_title'] 	= "Round wise report";
		$data['buttons']    	= [];
		$title_array 			= [];
		$request_array 			= [];
		$data['action']			= url($action);

		$data['round'] = NULL;
		if($request->has('round')){
	        $data['round'] = $request->round;
	        $request_array[] = 'round='.$request->round;
	    }

	    $data['buttons'][]  = [
	        'name' => 'Export',
	        'href' =>  url($action.'/pdf').'?'.implode('&', $request_array),
	        'target' => true
	    ];

      	$data['filter_buttons'] = $title_array;
		$ele_details 		= $this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
		if(!$ele_details){
			return redirect("officer-login");
		}


		$data['st_code'] 	= \Auth::user()->st_code;
		$data['pc_no'] 		= \Auth::user()->pc_no;


		$st_name = '';
	    $state_object = \App\models\Admin\StateModel::get_state_by_code(\Auth::user()->st_code);
	    if($state_object){
	        		$st_name = $state_object['ST_NAME'];
	    }
	    
	    $pc_name = '';
	    $get_pc = $this->commonModel->getpcbypcno(\Auth::user()->st_code,$data['pc_no']);
	    if($get_pc){
	      	$pc_name = $get_pc->PC_NAME;
	    }

	    $data['pc_name']     	= $pc_name;
	    $data['heading_title'] 	= '';
	    $data['st_name'] 		= $st_name;
	    $data['election'] 		= @$ele_details->ELECTION_TYPE;

		$rounds = DB::table("round_master")->where('st_code',$data['st_code'])->where('pc_no',$data['pc_no'])->max("scheduled_round");
		$data['rounds'] 		= $rounds;

		if($data['round']){
			$data['round'] 		= $data['round'];
		}else{
			$data['round'] 		= 1;
		}

		$acs_not_filled = [];
		$round_acs = DB::table("counting_master_".strtolower($data['st_code']))->where('pc_no', $data['pc_no'])->where('complete_round','<',$data['round'])->select('ac_no')->groupBy("ac_no")->get();
		foreach ($round_acs as $key => $round_ac) {
			$acs_result = $this->commonModel->getacbyacno($data['st_code'],$round_ac->ac_no);
			if($acs_result){
				$acs_not_filled[] = "<span class='badge badge-info'>".$acs_result->AC_NAME."</span> ";
			} 
		}

		$data['acs_not_filled'] = implode('', $acs_not_filled);

		$object 			= $this->CountingModel->get_previous_total_by_ac($data);
		$i = 1;
	        	$aggregate_total 			= 0;
	        	$aggregate_previous_total 	= 0;
	        	$aggregate_current_total 	= 0;
	        	foreach ($object as $result) {
	        		$total 			= $result->previous_total + $result->current_total;
	        		$results[] = [
	        			'sr_no' 			=> $i,
	        			'candidate_name' 	=> $result->candidate_name,
	        			'party_name'  		=> $result->party_name,
	        			'total'  			=> format_digit($total),
	        			'previous_total'  	=> format_digit($result->previous_total),
	        			'current_total'  	=> format_digit($result->current_total),
	        		];
	        		$aggregate_total 			+= $total;
	        		$aggregate_previous_total 	+= $result->previous_total;
	        		$aggregate_current_total 	+= $result->current_total;
	        		$i++;
	        	}
	        	$results[] = [
	        		'sr_no' 			=> '',
	        		'candidate_name' 	=> '',
	        		'party_name'  		=> 'Total',
	        		'total'  			=> format_digit($aggregate_total),
	        		'previous_total'  	=> format_digit($aggregate_previous_total),
	        		'current_total'  	=> format_digit($aggregate_current_total),
	        	];
	
	    $data['results'] 	= $results;
		$data['user_data'] 	= $d;

		if($request->has('is_excel')){
	        return $data;
	    }

		return view('admin.pc.counting.round_wise_report',$data);

	}

	public function export_round_report(Request $request){
		$data = $this->get_round_report($request->merge(['is_excel' => 1]));

		$setting_pdf = [
					'margin_top'        => 80,        // Set the page margins for the new document.
					'margin_bottom'     => 10,    
				];



	    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
	    $pdf = \PDF::loadView('admin.pc.counting.round_wise_pdf',$data,[], $setting_pdf);
	    return $pdf->download($name_excel.'_'.time().'.pdf');
	}
			
			

}  // end class results-declaration   
