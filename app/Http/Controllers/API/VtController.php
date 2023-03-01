<?php
	/////////////////////////////////////////////////////
	//  Code By Chanderkant for Suvidha Voter_Turnout App
	//////////////////////////////////////////////////////
	
	namespace App\Http\Controllers\API;
	use Illuminate\Support\Facades\Validator;
	use Illuminate\Support\Facades\Crypt;
	use Illuminate\Validation\Rule;
	use DB;
	use App\commonModel;
	use App\models\{States, Districts, AC};
	use App\Helpers\SmsgatewayHelper;
	use Illuminate\Support\Facades\Input;
	use Carbon\Carbon;
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use Illuminate\Support\Facades\Response;
	use App\adminmodel\CandidateApiModel;
	use GuzzleHttp\Client;
	
	class VtController extends Controller
	{
		public function __construct() {
			$this->commonModel = new commonModel();
			$this->candidateModel = new CandidateApiModel();
		}
		
		public $successStatus = 200;
		public $createdStatus = 201;
		public $nocontentStatus = 204;
		public $notmodifiedStatus = 304;
		public $badrequestStatus = 400;
		public $unauthorizedStatus = 401;
		public $notfoundStatus = 404;
		public $intservererrorStatus = 500;
		
		
		///DrillDown Filters
		
		//#####Filter # 1 ELECTION Type Dropdown
		public function ElectionTypePt(Request $request) {
			try{
				/* $validator = Validator::make($request->all(), [
					'user_id' => 'required',
					'ac_token' => 'required',
					]);
					
					if($validator->fails()){
					return response()->json(['success' => false,'message'=>'Please provide userid, ac_token and electiontype']);            
				}  */
				$summary=array();
				$summary['success'] = true;
				$summary['message'] = "Election Master Details";
				$electiontype = DB::table('election_master')->get();
				$summary['electiontype']=$electiontype;
				return response()->json($summary, $this->successStatus);
			}///EndTry
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
		}///EndFunction getElectionTypeDetails
		
		
		//#######Filter # 2 Get Phase Name and ID be Election ID
		public function PhaseListPt(Request $request) {
			try{
				$validator = Validator::make($request->all(), [
				/* 'user_id' => 'required',
				'ac_token' => 'required', */
				'electiontype' => 'required',
				'election_id' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->notfoundStatus);            
				} 
				
				$userInputs = $request->all();
				$userid = trim($userInputs['user_id']);
				$actoken = trim($userInputs['ac_token']);
				$election_id = trim($userInputs['election_id']);
				$electionId = trim($userInputs['electiontype']);
				$summary=array();
				$summary['success'] = true;
				$summary['message'] = "Phase by Electionid : $electionId";
				if(!empty($electionId))
				{
					
					$phase_details = DB::connection('mysql_Old')->table('m_election_details')->select('ScheduleID','ELECTION_TYPE','PHASE_NO','ELECTION_TYPEID')->where('ELECTION_TYPEID',$electionId)->groupBy('PHASE_NO')->get(); 
					
					//print_r($phase_details);
					//print_r(DB::connection()->getDatabaseName());
					if(count($phase_details)>0){
						$phaselist = array();
						//$phaselist[]=array("schedule_id" => 0, "name" => "All Phases");
						foreach($phase_details as $phase){
							if($phase->ScheduleID > 0)
							$phaselist[] = array("schedule_id"=> $phase->ScheduleID,"name"=> "Phase ".$phase->PHASE_NO);
						}
						
						$success['success'] = true;
						$success['phaselist'] =$phaselist;
						
						}else{ 
						$success['success'] = false;
						$success['phaselist'] = array();
						return response()->json($success, $this->successStatus);
					}
					return response()->json($success, $this->successStatus);
				}
				else
				{
					$summary['message'] = "Blank or invalid Electionid";
					return response()->json($summary, $this->successStatus);
				}
				
				return response()->json($summary, $this->successStatus);
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
		}///EndFunction 
		
		
		
		//########3 Filter # 3  Get Polling states list based on selected Election Type and Phase	
		public function StateListingPT(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required',
				'election_id' => 'required',
				'electionphase' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->notfoundStatus);            
				} 
				
				$userInputs = $request->all();
				$scheduleid = trim($userInputs['electionphase']);
				$election_id = trim($userInputs['election_id']);
				$electiontypeid = trim($userInputs['electiontype']);
				
				$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				//print_r($eldata); die;
				
				$pc_list = DB::table('m_election_details')->where('CONST_TYPE','=',$eldata->election_sort_name)->where('ELECTION_TYPE',$eldata->election_type);
				//$success['count'] = count($pc_list);
				if(!empty($electiontypeid))
				{
					if(!empty($scheduleid))
					{
						$phase_details = DB::table('m_election_details')->groupby('ST_CODE')
						->where('ScheduleID',$scheduleid)->where('ELECTION_TYPEID',$electiontypeid)->where('ELECTION_TYPE',$eldata->election_type)->where('CONST_TYPE','=',$eldata->election_sort_name)->get();
					}
					else
					{
						$phase_details = DB::table('m_election_details')->groupby('ST_CODE')
						->where('ELECTION_TYPEID',$electiontypeid)->where('ELECTION_TYPE',$eldata->election_type)->where('CONST_TYPE','=',$eldata->election_sort_name)->get();
					}
					//print_r($phase_details); die;
					if(count($phase_details)>0)
					{
						$statelist = array();
						//$statelist[]=array( "statename" => "All States","statecode" => 'S00',);
						foreach($phase_details as $state){
							$statelist[] = array("statename"=>trim($this->commonModel->getstatebystatecode($state->ST_CODE)->ST_NAME),"statecode"=>$state->ST_CODE);
						}
						/* usort($statelist,function($a,$b){
							return strcmp($a['statename'], $b['statename']);
						}); */
						$success['success'] = true;
						$success['statelist'] =$statelist;
						
						}else{ 
						$success['success'] = false;
						$success['statelist'] = array();
						return response()->json($success, $this->successStatus);
					}
					
					return response()->json($success, $this->successStatus);
				}
				} catch (Exception $ex) {
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
		}
		
		
		//########4 Filter # 4  Get Polling PC based on selected Election Type, Phase and State
		public function PcListingPT(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required',
				'election_id' => 'required',
				'electionphase' => 'required',
				'statecode' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->notfoundStatus);            
				} 
				
				$userInputs = $request->all();
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				$election_id = trim($userInputs['electiontype']);
				$statecode = trim($userInputs['statecode']);
				if(empty($scheduleid))
				$scheduleid=1;
				if(empty($electiontypeid))
				$electiontypeid=3;
				if(empty($election_id))
				$election_id=4;
				
				$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				if($eldata->election_sort_name == "PC")
				{	
					$pc_list = DB::table('m_election_details')->select('CONST_NO')->where('CONST_TYPE','=',$eldata->election_sort_name)->where('ELECTION_TYPE',$eldata->election_type);
					
					if(!empty($statecode))
					$pc_list->where('ST_CODE',$statecode);
					
					if(!empty($scheduleid))
					$pc_list->where('ScheduleID',$scheduleid);
					
					$c_name = $eldata->election_sort_name;
					
					$pc_list_filtered = $pc_list->get();
					//print_r($pc_list_filtered); die;
					if(count($pc_list_filtered)>0){
						$pclisting = array();
						//$pclisting[]=array("pcname" => 'All '.$c_name, "pcno" => 0);
						foreach($pc_list_filtered as $aclist){
							$pclisting[] = array("pcname"=>trim($this->commonModel->getpcbypcno($statecode,$aclist->CONST_NO)->PC_NAME),"pcno"=>($aclist->CONST_NO));
						}
						usort($pclisting,function($a,$b){
							return strcmp($a['pcname'], $b['pcname']);
						});
					}
					
					$success['success'] = true;
					$success['pclist'] =$pclisting;
					
				}
				else
				{ 
					$distlist = DB::table('m_district')->select('DIST_NO','DIST_NAME','DIST_NAME_V1')->where('ST_CODE',$statecode)->orderBy('ST_CODE','DIST_NAME')->get();
					//print_r($distlist);
					$district_listing=array();
					foreach($distlist as $drec){
						$district_listing[] = array("dist_no"=>$drec->DIST_NO,"district_name"=>$drec->DIST_NAME,"district_name_regional"=>$drec->DIST_NAME_V1);
					}
					//print_r($district_listing);
					
					$success['success'] = true;
					$success['districtlist'] =$district_listing;
					
				}
				
				return response()->json($success, $this->successStatus);
				
			} 
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
		}
		
		
		//########4 Filter # 5  Get Polling District based on selected Election Type, Phase and State
		public function DistListingPT(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required',
				'election_id' => 'required',
				'electionphase' => 'required',
				'statecode' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->notfoundStatus);            
				} 
				
				$userInputs = $request->all();
				//print_r($userInputs);
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				$election_id = trim($userInputs['electiontype']);
				$statecode = trim($userInputs['statecode']);
				if(empty($scheduleid))
				$scheduleid=1;
				if(empty($electiontypeid))
				$electiontypeid=3;
				if(empty($election_id))
				$election_id=8;
				
				$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				if($eldata->election_sort_name == "PC")
				{	
					$pc_list = DB::table('m_election_details')->select('CONST_NO')->where('CONST_TYPE','=',$eldata->election_sort_name)->where('ELECTION_TYPE',$eldata->election_type);
					
					if(!empty($statecode))
					$pc_list->where('ST_CODE',$statecode);
					
					if(!empty($scheduleid))
					$pc_list->where('ScheduleID',$scheduleid);
					
					$c_name = $eldata->election_sort_name;
					
					$pc_list_filtered = $pc_list->get();
					//print_r($pc_list_filtered); die;
					if(count($pc_list_filtered)>0){
						$pclisting = array();
						//$pclisting[]=array("pcname" => 'All '.$c_name, "pcno" => 0);
						foreach($pc_list_filtered as $aclist){
							$pclisting[] = array("pcname"=>trim($this->commonModel->getpcbypcno($statecode,$aclist->CONST_NO)->PC_NAME),"pcno"=>($aclist->CONST_NO));
						}
						usort($pclisting,function($a,$b){
							return strcmp($a['pcname'], $b['pcname']);
						});
					}
					
					$success['success'] = true;
					$success['pclist'] =$pclisting;
					
				}
				else
				{ 
					$distlist = DB::table('m_district')->select('DIST_NO','DIST_NAME','DIST_NAME_V1')->where('ST_CODE',$statecode)->orderBy('DIST_NAME')->get();
					//print_r($distlist);
					$district_listing=array();
					foreach($distlist as $drec){
						$district_listing[] = array("dist_no"=>$drec->DIST_NO,"district_name"=>$drec->DIST_NAME,"district_name_regional"=>$drec->DIST_NAME_V1);
					}
					//print_r($district_listing);
					
					$success['success'] = true;
					$success['districtlist'] =$district_listing;
					
				}
				
				return response()->json($success, $this->successStatus);
				
			} 
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
		}
		
		
		
		//########5 Filter # 6  Get Polling AC based on selected Election Type, Phase, State and PC
		public function PC2AcListingPT(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required',
				'electionphase' => 'required',
				'statecode' => 'required',
				'pc_no' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->notfoundStatus);            
				} 
				
				$userInputs = $request->all();
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				$statecode = trim($userInputs['statecode']);
				$pcno = trim($userInputs['pc_no']);
				/* if(empty($scheduleid))
					$scheduleid=2;
					if(empty($electiontypeid))
				$electiontypeid=1; */
				$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				if($eldata->election_sort_name == "PC")
				{	
					$pc_list_filtered = DB::table('m_ac')->where('ST_CODE',$statecode)->where('PC_NO',$pcno)->get();
					
					//print_r($pc_list_filtered);//die;
					$c_name = "AC";
					
					if(count($pc_list_filtered)>0)
					{
						$pclisting = array();
						//$pclisting[]=array("acname" => 'All '.$c_name, "acno" => 0);
						foreach($pc_list_filtered as $aclist){
						//print_r("..");
							$pclisting[] = array("acname"=>$aclist->AC_NAME,"acno"=>$aclist->AC_NO);
						}
						/* usort($pclisting,function($a,$b){
							return strcmp($a['acname'], $b['acname']);
						}); */
						usort($pclisting, function ($a, $b) { 
							//print_r($a);print_r("\n\n\n");print_r($b); die;
						return strcmp($a["acname"], $b["acname"]); });
						$success['success'] = true;
						$success['aclist'] =$pclisting;
						return response()->json($success, $this->successStatus);
					}
					else
					{ 
						$success['success'] = false;
						$success['aclist'] = array();
						return response()->json($success, $this->successStatus);
					}
				}
				else
				{
					$pc_list_filtered = DB::table('m_election_details')->where('ST_CODE',$statecode)->where('ScheduleID',$scheduleid)->get();
					
					//print_r($pc_list_filtered);die;
					$c_name = "AC";
					
					if(count($pc_list_filtered)>0){
						$pclisting = array();
						//$pclisting[]=array("acname" => 'All '.$c_name, "acno" => 0);
						foreach($pc_list_filtered as $aclist){
							
							$pclisting[] = array("acname"=>trim($this->commonModel->getacbyacno($statecode,$aclist->CONST_NO)->AC_NAME),"acno"=>$aclist->CONST_NO);
						}
						/* usort($pclisting,function($a,$b){
							return strcmp($a['acname'], $b['acname']);
						}); */
						usort($pclisting, function ($a, $b) { 
							//print_r($a);print_r("\n\n\n");print_r($b); die;
						return strcmp($a["acname"], $b["acname"]); });
						$success['success'] = true;
						$success['aclist'] =$pclisting;
						
						}else{ 
						$success['success'] = false;
						$success['aclist'] = array();
						return response()->json($success, $this->successStatus);
					}
					return response()->json($success, $this->successStatus);
					
				} 
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		//########5 Filter # 7  Get Polling AC based on selected Election Type, Phase, State and District
		public function Dist2AcListingPT(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required',
				'electionphase' => 'required',
				'statecode' => 'required',
				'district_no' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->notfoundStatus);            
				} 
				
				$userInputs = $request->all();
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				$statecode = trim($userInputs['statecode']);
				$distno = trim($userInputs['district_no']);
				
				/* if(empty($scheduleid))
					$scheduleid=2;
					if(empty($electiontypeid))
				$electiontypeid=1; */
				$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				if($eldata->election_sort_name == "PC")
				{	
					$pc_list_filtered = DB::table('m_ac')->where('ST_CODE',$statecode)->where('PC_NO',$distno)->get();
					
					//print_r($pc_list_filtered);die;
					$c_name = "AC";
					
					if(count($pc_list_filtered)>0)
					{
						$pclisting = array();
						//$pclisting[]=array("acname" => 'All '.$c_name, "acno" => 0);
						foreach($pc_list_filtered as $aclist){
							$pclisting[] = array("acname"=>$aclist->AC_NAME,"acno"=>$aclist->AC_NO);
						}
						/* usort($pclisting,function($a,$b){
							return strcmp($a['acname'], $b['acname']);
						}); */
						usort($pclisting, function ($a, $b) { 
							//print_r($a);print_r("\n\n\n");print_r($b); die;
						return strcmp($a["acname"], $b["acname"]); });
						$success['success'] = true;
						$success['aclist'] =$pclisting;
						
					}
					else
					{ 
						$success['success'] = false;
						$success['aclist'] = array();
						return response()->json($success, $this->successStatus);
					}
				}
				else
				{
					if($distno==0)
					{
						$aclist = DB::table('m_ac')->select('AC_NO','AC_NAME','AC_NAME_V1')->where('ST_CODE',$statecode)->orderBy('AC_NAME')->get();
					}
					else
					{
						$aclist = DB::table('m_ac')->select('AC_NO','AC_NAME','AC_NAME_V1')->where('ST_CODE',$statecode)->where('DIST_NO_HDQTR',$distno)->orderBy('AC_NAME')->get();
					}
					//print_r($distlist);
					
					$mac=DB::table('m_election_details')->select('CONST_NO')->where('ST_CODE',$statecode)->where('CONST_TYPE',$eldata->election_sort_name)->where('ELECTION_TYPE',$eldata->election_type)->where('ScheduleID',$scheduleid)->get();
					$constarray=array();
					if(count($mac) >0)
					{
						foreach($mac as $crec)
						{
							$constarray[]=$crec->CONST_NO;
						}
						}
					//print_r($mac);
					//print_r("\n\nConstArray\n\n");
					//print_r($constarray);
					$ac_listing=array();
					foreach($aclist as $acrec){
						if (in_array($acrec->AC_NO, $constarray))
						$ac_listing[] = array("acno"=>$acrec->AC_NO,"acname"=>$acrec->AC_NAME,"ac_name_regional"=>$acrec->AC_NAME_V1);
					}
					//print_r($district_listing);
					
					$success['success'] = true;
					$success['aclist'] =$ac_listing;
				}
				return response()->json($success, $this->successStatus);
				
			} 
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		//########6 Filter # 8  Get All Phase for given State
		public function PhaseByState(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'statecode' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->notfoundStatus);            
				} 
				
				$userInputs = $request->all();
				$statecode = trim($userInputs['statecode']);
				
				//$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				
				$phase_list = DB::table('pd_scheduledetail')->select('st_code','scheduleid')->where('st_code',$statecode)->groupBy('scheduleid')->orderBy('scheduleid')->get();
				//print_r($phase_list);die;
				$phaselist = array();
				if(count($phase_list)>0)
				{
					
					//$pclisting[]=array("pcname" => 'All '.$c_name, "pcno" => 0);
					foreach($phase_list as $phase){
						
						$phaselist[] = $phase->scheduleid;
					}
					$success['success'] = true;
					$success['phases'] =$phaselist;
					
				}
				else
				{ 
					$success['success'] = false;
					$success['phases'] = array();
					return response()->json($success, $this->successStatus);
				}
				return response()->json($success, $this->successStatus);
				
			} 
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
		}
		
		//########6 Filter # 8  Get All Phase for given State
		public function PollDate(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required',
				'electionphase' => 'required',
				'election_id' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->notfoundStatus);            
				} 
				
				$userInputs = $request->all();
				$electiontype = trim($userInputs['electiontype']);
				$phase = trim($userInputs['electionphase']);
				$electionid = trim($userInputs['election_id']);
				//$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				
				$date_data = DB::table('m_schedule')->select('DATE_POLL')->where('SCHEDULEID',$phase)->where('ELECTION_TYPEID',$electiontype)->first();
				//print_r($phase_list);die;
				if(count($date_data)>0)
				{
					
					$success['success'] = true;
					$success['poll_date'] =$date_data->DATE_POLL;
					
				}
				else
				{ 
					$success['success'] = false;
					$success['poll_date'] = NULL;
					return response()->json($success, $this->successStatus);
				}
				return response()->json($success, $this->successStatus);
				
			} 
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
		}
		
		
		
		
		/////////////////////////////
		// END OF FILTERS         //
		////////////////////////////
		
		############ PAGE CALLS #################
		
		public function HomePt(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required',
				'electionphase' => 'required'
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->notfoundStatus);            
				} 
				
				$userInputs = $request->all();
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				
				/* if(empty($scheduleid))
					$scheduleid=1;
					if(empty($electiontypeid))
				$electiontypeid=4; */
				
				$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				$pddata=DB::table('pd_schedulemaster')->where('schedule_id',$scheduleid)->get();
				$total_est=0;
				$pdilist=array();
				if(count($pddata))
				{
					foreach($pddata as $pditem)
					{
						$pdilist[] = $pditem->pd_scheduleid;
					}
				}
				//print_r($pdilist);
				$summary=array();
				
				$summary['success'] = true;
				$summary['message'] = "Poll TurnOut Home [StateWise]";
				$summary['phase'] = $scheduleid;
				$esubtype=$eldata->election_type;
				$result=array();
				/* if($eldata->election_sort_name == "PC")
				{ */
				$stlist= DB::table('pd_scheduledetail')->select('st_code')->whereIn('pd_scheduleid', $pdilist)->groupby('st_code')->get();
				$statids=array();
				$tcnt=count($stlist);
				$st_aggr=0;
				$gt_voter=0;
				$gt_elector=0;
				foreach($stlist as $sttid)
				{
					$staclist=DB::table('pd_scheduledetail')->select('ac_no')->where('st_code',$sttid->st_code)->get();
					$acount=count($staclist);
					//print_r("\n\n".$acount);
					$tempar=array();
					$tempar['st_code']=$sttid->st_code;
					$tempar['st_name']=$this->commonModel->getstatebystatecode($sttid->st_code)->ST_NAME;
					$tempar['total_ac']=$acount;
					$tempar['voters']=0;
					$tempar['r1_total'] = 0;
					$tempar['r1_per'] = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->sum('est_turnout_round1');
					if($acount>0)
					/* $tempar['r1_per']=number_format($tempar['r1_per']/$acount,2,".","");
					else
					$tempar['r1_per']=0;
					$tempar['r2_total'] = 0;
					$tempar['r2_per'] = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->sum('est_turnout_round2');
					if($acount>0)
					$tempar['r2_per']=number_format($tempar['r2_per']/$acount,2,".","");
					else
					$tempar['r2_per']=0;
					$tempar['r3_total'] = 0;
					$tempar['r3_per'] = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->sum('est_turnout_round3');
					if($acount>0)
					$tempar['r3_per']=number_format($tempar['r3_per']/$acount,2,".","");
					else
					$tempar['r3_per']=0;
					$tempar['r4_total'] = 0;
					$tempar['r4_per'] = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->sum('est_turnout_round4');
					if($acount>0)
					$tempar['r4_per']=number_format($tempar['r4_per']/$acount,2,".","");
					else
					$tempar['r4_per']=0;
					$tempar['r5_total'] = 0;
					$tempar['r5_per'] = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->sum('est_turnout_round5');
					if($acount>0)
					$tempar['r5_per']=number_format($tempar['r5_per']/$acount,2,".","");
					else
					$tempar['r5_per']=0;
					$tempar['end_total'] = 0;
					$tempar['end_per'] = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->sum('close_of_poll');
					if($acount>0)
					$tempar['end_per']=number_format($tempar['end_per']/$acount,2,".",""); */
					$tempar['final_total'] = 0;
					$tempar['final_per'] = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->sum('est_turnout_total');
					$telec = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->sum('electors_total');
					$tvoter = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->sum('est_voters');
					if(($tvoter>0) && ($telec>0))
					$tempar['final_per']=number_format(($tvoter * 100)/ $telec,2,".","");
					else
					$tempar['final_per']=0;
					//print_r($tempar['final_per']);die;
					//DB::selectRaw(select ROUND((SUM(est_voters) * 100 )/SUM(electors_total),2) as total_percent from `pd_scheduledetail` where  `scheduleid` = 2); 
					$gt_voter += $tvoter;
					$gt_elector += $telec;
					$st_aggr = $st_aggr + $tempar['final_per'];
					$statids[]=$tempar;
				}
				//die;
				$summary['statewise']=$statids;
				$aclist= DB::table('pd_scheduledetail')->select('ac_no')->where('scheduleid',$scheduleid)->get();
				$tcnt=count($aclist);
				$oall=array();
				$oall['voters']=$gt_voter;
				$oall['total']=$gt_elector;
				if(($gt_voter>0) && ($gt_elector>0))
				$oall['percentage']=number_format(($gt_voter * 100)/ $gt_elector,2,".","");
				else
				$oall['percentage']=0;
				$summary['overall']=$oall;
				$summary['total_ac']=$tcnt;
				$summary['total_state']=$tcnt;
				$tempdata = DB::table('pd_scheduledetail')->orderBy('updated_at','DESC')->first();
				$summary['last_update_time']=$tempdata->updated_at;
				return response()->json($summary, $this->successStatus);
				
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		public function PcwisePt(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required','electionphase' => 'required'
				]);
				
				if($validator->fails()){
					return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				if(isset($userInputs['statecode']))
				{
					$stcode=trim($userInputs['statecode']);
				}
				else
				{
					$stcode="S00";
				}
				/* if(empty($scheduleid))
					$scheduleid=7;
					if(empty($electiontypeid))
				$electiontypeid=1; */
				
				$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				
				$pddata=DB::table('pd_schedulemaster')->where('schedule_id',$scheduleid)->get();
				$total_est=0;
				$pdilist=array();
				if(count($pddata))
				{
					foreach($pddata as $pditem)
					{
						$pdilist[] = $pditem->pd_scheduleid;
					}
				}
				//print_r($pdilist);die;
				$summary=array();
				
				$summary['success'] = true;
				$summary['message'] = "Poll TurnOut Home [PCWise]";
				$summary['phase'] = $scheduleid;
				$esubtype=$eldata->election_type;
				$result=array();
				if(empty($stcode) || $stcode=="S00")
				{
					$stlist= DB::table('pd_scheduledetail')->select('st_code','pc_no')->groupby('st_code','pc_no')->get();
					$aclist=DB::table('pd_scheduledetail')->select('st_code','pc_no')->get();
				}
				else
				{
					$stlist=DB::table('pd_scheduledetail')->select('st_code','pc_no')->where('st_code',$stcode)->groupby('pc_no')->get();
					$aclist=DB::table('pd_scheduledetail')->select('st_code','pc_no')->where('st_code',$stcode)->get();
				}
				//print_r($stlist);//die;
				$statids=array();
				$tpcnt=count($stlist);
				$tacnt=count($aclist);
				foreach($stlist as $pcdata)
				{
					//print_r("\n\nPC\n\n");
					//print_r($pcdata);
					//print_r("\n");
					$tmplist=DB::table('pd_scheduledetail')->select('ac_no')->where('st_code',$pcdata->st_code)->where('pc_no',$pcdata->pc_no)->get();
					//print_r($tmplist);die;
					$tcnt=count($tmplist);
					$tempar=array();
					$tempar['st_code']=$pcdata->st_code;
					$tempar['st_name']=$this->commonModel->getstatebystatecode($pcdata->st_code)->ST_NAME;
					$tempar['pcno']=$pcdata->pc_no;
					$tempar['pc_name']=$this->commonModel->getpcbypcno($pcdata->st_code,$pcdata->pc_no)->PC_NAME;
					$tempar['total_ac']=$tcnt;
					$tempar['voters']=0;
					/* $tempar['r1_total'] = 0;
					$tempar['r1_per'] = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('pc_no', $pcdata->pc_no)->sum('est_turnout_round1');
					$tempar['r1_per']=number_format($tempar['r1_per']/$tcnt,2,".","");
					$tempar['r2_total'] = 0;
					$tempar['r2_per'] = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('pc_no', $pcdata->pc_no)->sum('est_turnout_round2');
					$tempar['r2_per']=number_format($tempar['r2_per']/$tcnt,2,".","");
					$tempar['r3_total'] = 0;
					$tempar['r3_per'] = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('pc_no', $pcdata->pc_no)->sum('est_turnout_round3');
					$tempar['r3_per']=number_format($tempar['r3_per']/$tcnt,2,".","");
					$tempar['r4_total'] = 0;
					$tempar['r4_per'] = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('pc_no', $pcdata->pc_no)->sum('est_turnout_round4');
					$tempar['r4_per']=number_format($tempar['r4_per']/$tcnt,2,".","");
					$tempar['r5_total'] = 0;
					$tempar['r5_per'] = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('pc_no', $pcdata->pc_no)->sum('est_turnout_round5');
					$tempar['r5_per']=number_format($tempar['r5_per']/$tcnt,2,".","");
					$tempar['end_total'] = 0;
					$tempar['end_per'] = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('pc_no', $pcdata->pc_no)->sum('close_of_poll');
					$tempar['end_per']=number_format($tempar['end_per']/$tcnt,2,".",""); */
					
					$tempar['final_total'] = 0;
					$telec = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('pc_no', $pcdata->pc_no)->sum('electors_total');
					$tvoter = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('pc_no', $pcdata->pc_no)->sum('est_voters');
					if(($tvoter>0) && ($telec>0))
					$tempar['final_per']=number_format(($tvoter * 100)/ $telec,2,".","");
					else
					$tempar['final_per']=0;
					$statids[]=$tempar;
				}
				$summary['pcwise']=$statids;
				$oall=array();
				
				if(empty($stcode) || $stcode=="S00")
				{
					$telec = DB::table('pd_scheduledetail')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->sum('electors_total');
					$tvoter = DB::table('pd_scheduledetail')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->sum('est_voters');
					if(($tvoter>0) && ($telec>0))
					$fper=number_format(($tvoter * 100)/ $telec,2,".","");
					else
					$fper=0;
				}
				else
				{
					$telec = DB::table('pd_scheduledetail')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->where('st_code',$stcode)->sum('electors_total');
					$tvoter = DB::table('pd_scheduledetail')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->where('st_code',$stcode)->sum('est_voters');
					if(($tvoter>0) && ($telec>0))
					$fper=number_format(($tvoter * 100)/ $telec,2,".","");
					else
					$fper=0;
					
				}
				$oall['voters']=$tvoter;
				$oall['total']=$telec;
				$oall['percentage']=$fper;
				$summary['overall']=$oall;
				$summary['total_pc']=$tpcnt;
				$summary['total_ac']=$tacnt;
				$tempdata = DB::table('pd_scheduledetail')->orderBy('updated_at','DESC')->first();
				$summary['last_update_time']=$tempdata->updated_at;
				return response()->json($summary, $this->successStatus);
				
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		public function DISTwisePt(Request $request) {
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required',
				'electionphase' => 'required'
				]);
				
				if($validator->fails()){
					return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				if(isset($userInputs['statecode']))
				{
					$stcode=trim($userInputs['statecode']);
					$pddata=DB::table('pd_schedulemaster')->where('schedule_id',$scheduleid)->where('st_code',$stcode)->get();
				}
				else
				{
					$stcode="S00";
					$pddata=DB::table('pd_schedulemaster')->where('schedule_id',$scheduleid)->get();
				}
				/* if(empty($scheduleid))
					$scheduleid=7;
					if(empty($electiontypeid))
				$electiontypeid=1; */
				
				$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				
				
				$total_est=0;
				$pdilist=array();
				if(count($pddata))
				{
					foreach($pddata as $pditem)
					{
						$pdilist[] = $pditem->pd_scheduleid;
					}
				}
				//print_r($pdilist);die;
				$summary=array();
				
				$summary['success'] = true;
				$summary['message'] = "Poll TurnOut Home [District Wise]";
				$summary['phase'] = $scheduleid;
				$electiontype=$eldata->election_type;
				$esubtype=$eldata->election_sort_name;
				$result=array();
				if(empty($stcode) || $stcode=="S00")
				{
					$distlist= DB::table('pd_scheduledetail')->select('st_code','dist_no')->groupby('st_code','dist_no')->get();
					$aclist=DB::table('pd_scheduledetail')->select('st_code','dist_no')->get();
				}
				else
				{
					$distlist=DB::table('pd_scheduledetail')->select('st_code','dist_no')->where('st_code',$stcode)->groupby('dist_no')->get();
					$aclist=DB::table('pd_scheduledetail')->select('st_code','dist_no')->where('st_code',$stcode)->get();
				}
				//print_r($stlist);//die;
				$statids=array();
				$tpcnt=count($distlist);
				$tacnt=count($aclist);
				foreach($distlist as $pcdata)
				{
					//print_r("\n\nPC\n\n");
					//print_r($pcdata);
					//print_r("\n");
					//$tmplist=DB::table('pd_scheduledetail')->select('ac_no')->where('st_code',$pcdata->st_code)->where('ac_no',$pcdata->ac_no)->get();
					//print_r($tmplist);die;
					//$tcnt=count($tmplist);
					$tempar=array();
					$tempar['st_code']=$pcdata->st_code;
					$tempar['st_name']=$this->commonModel->getstatebystatecode($pcdata->st_code)->ST_NAME;
					$tempar['dist_no']=$pcdata->dist_no;
					$tempar['dist_name']=$this->commonModel->getdistrictbydistrictno($pcdata->st_code,$pcdata->dist_no)->DIST_NAME;
					//$tempar['total_ac']=$tcnt;
					$tempar['voters']=0;
					/* $tempar['r1_total'] = 0;
					$tempar['r1_per'] = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('ac_no', $pcdata->ac_no)->sum('est_turnout_round1');
					$tempar['r1_per']=number_format($tempar['r1_per']/$tcnt,2,".","");
					$tempar['r2_total'] = 0;
					$tempar['r2_per'] = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('ac_no', $pcdata->ac_no)->sum('est_turnout_round2');
					$tempar['r2_per']=number_format($tempar['r2_per']/$tcnt,2,".","");
					$tempar['r3_total'] = 0;
					$tempar['r3_per'] = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('ac_no', $pcdata->ac_no)->sum('est_turnout_round3');
					$tempar['r3_per']=number_format($tempar['r3_per']/$tcnt,2,".","");
					$tempar['r4_total'] = 0;
					$tempar['r4_per'] = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('ac_no', $pcdata->ac_no)->sum('est_turnout_round4');
					$tempar['r4_per']=number_format($tempar['r4_per']/$tcnt,2,".","");
					$tempar['r5_total'] = 0;
					$tempar['r5_per'] = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('ac_no', $pcdata->ac_no)->sum('est_turnout_round5');
					$tempar['r5_per']=number_format($tempar['r5_per']/$tcnt,2,".","");
					$tempar['end_total'] = 0;
					$tempar['end_per'] = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('ac_no', $pcdata->ac_no)->sum('close_of_poll');
					$tempar['end_per']=number_format($tempar['end_per']/$tcnt,2,".",""); */
					
					$tempar['final_total'] = 0;
					$telec = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('dist_no', $pcdata->dist_no)->sum('electors_total');
					$tvoter = DB::table('pd_scheduledetail')->where('st_code',$pcdata->st_code)->where('dist_no', $pcdata->dist_no)->sum('est_voters');
					if(($tvoter>0) && ($telec>0))
					$tempar['final_per']=number_format(($tvoter * 100)/ $telec,2,".","");
					else
					$tempar['final_per']=0;
					$statids[]=$tempar;
				}
				$summary['pcwise']=$statids;
				$oall=array();
				
				if(empty($stcode) || $stcode=="S00")
				{
					$telec = DB::table('pd_scheduledetail')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->sum('electors_total');
					$tvoter = DB::table('pd_scheduledetail')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->sum('est_voters');
					if(($tvoter>0) && ($telec>0))
					$fper=number_format(($tvoter * 100)/ $telec,2,".","");
					else
					$fper=0;
				}
				else
				{
					$telec = DB::table('pd_scheduledetail')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->where('st_code',$stcode)->sum('electors_total');
					$tvoter = DB::table('pd_scheduledetail')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->where('st_code',$stcode)->sum('est_voters');
					if(($tvoter>0) && ($telec>0))
					$fper=number_format(($tvoter * 100)/ $telec,2,".","");
					else
					$fper=0;
					
				}
				$oall['voters']=$tvoter;
				$oall['total']=$telec;
				$oall['percentage']=$fper;
				$summary['overall']=$oall;
				$summary['total_pc']=$tpcnt;
				$summary['total_ac']=$tacnt;
				$tempdata = DB::table('pd_scheduledetail')->orderBy('updated_at','DESC')->first();
				$summary['last_update_time']=$tempdata->updated_at;
				return response()->json($summary, $this->successStatus);
				
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
			
		}



		public function PC2AcwisePt(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required',
				'electionphase' => 'required',
				'election_id' => 'required',
				'statecode' => 'required',
				'pcno' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				if(isset($userInputs['statecode']))
				{
					$stcode=trim($userInputs['statecode']);
				}
				else
				{
					$stcode="S00";
				}
				if(isset($userInputs['pcno']))
				{
					$pcno=trim($userInputs['pcno']);
				}
				else
				{
					$pcno=0;
				}
				/* if(empty($scheduleid))
					$scheduleid=7;
					if(empty($electiontypeid))
				$electiontypeid=1; */
				if($stcode!="S00")
				{
					$pddata=DB::table('pd_schedulemaster')->where('schedule_id',$scheduleid)->get();
				}
				else
				{
					$pddata=DB::table('pd_schedulemaster')->where('schedule_id',$scheduleid)->where('st_code',$stcode)->get();
				}
				$pdilist=array();
				if(count($pddata))
				{
					foreach($pddata as $pditem)
					{
						$pdilist[] = $pditem->pd_scheduleid;
					}
				}
				//print_r($pdilist);die;
				$summary=array();
				
				$summary['success'] = true;
				$summary['message'] = "Poll TurnOut Home [PC 2 ACWise]";
				$summary['phase'] = $scheduleid;
				//$esubtype=$eldata->election_type;
				$result=array();
				/* if(empty($pcno) || $pcno==0 || empty($stcode) || $stcode=="S00")
					{
					$stlist= DB::table('pd_scheduledetail')->get();
					}
					else
					{
					$stlist= $stlist= DB::table('pd_scheduledetail')->where('st_code',$stcode)->where('pc_no',$pcno)->get();
				} */
				$stlist= $stlist= DB::table('pd_scheduledetail')->where('st_code',$stcode)->get();
				$acount=count($stlist);
				//print_r("\n\n 1. ");
				//print_r($acount);//die;
				$statids=array();
				$tcnt=count($stlist);
				foreach($stlist as $sttid)
				{
					$tempar=array();
					$tempar['acno']=$sttid->ac_no;
					$tempar['ac_name']=$this->commonModel->getacbyacno($sttid->st_code,$sttid->ac_no)->AC_NAME;
					$tempar['pcno']=0;//$sttid->pc_no;
					$tempar['pc_name']='';//$this->commonModel->getpcbypcno($sttid->st_code,$sttid->pc_no)->PC_NAME;
					$tempar['st_code']=$sttid->st_code;
					$tempar['st_name']=$this->commonModel->getstatebystatecode($sttid->st_code)->ST_NAME;
					$tempar['voters']=0;
					/* $tempar['r1_total'] = 0;
					$tempar['r1_per'] = $sttid->est_turnout_round1;
					$tempar['r2_total'] = 0;
					$tempar['r2_per'] = $sttid->est_turnout_round2;
					$tempar['r3_total'] = 0;
					$tempar['r3_per'] = $sttid->est_turnout_round3;
					$tempar['r4_total'] = 0;
					$tempar['r4_per'] = $sttid->est_turnout_round4;
					$tempar['r5_total'] = 0;
					$tempar['r5_per'] = $sttid->est_turnout_round5;
					$tempar['end_total'] = 0;
					$tempar['end_per'] = $sttid->close_of_poll; */
					$tempar['final_total'] = 0;
					$tempar['final_per'] = $sttid->est_turnout_total;
					$statids[]=$tempar;
				}
				//print_r(count($statids));die;
				$summary['acwise']=$statids;
				$oall=array();
				if(empty($pcno) || $pcno==0 || empty($stcode) || $stcode=="S00")
				{
					$telec = DB::table('pd_scheduledetail')->sum('electors_total');
					$tvoter = DB::table('pd_scheduledetail')->sum('est_voters');
					if(($tvoter>0) && ($telec>0))
					$fper=number_format(($tvoter * 100)/ $telec,2,".","");
					else
					$fper=0;
				}
				else
				{
					$telec = DB::table('pd_scheduledetail')->where('st_code',$stcode)->sum('electors_total');
					$tvoter = DB::table('pd_scheduledetail')->where('st_code',$stcode)->sum('est_voters');
					if(($tvoter>0) && ($telec>0))
					$fper=number_format(($tvoter * 100)/ $telec,2,".","");
					else
					$fper=0;
					
				}
				$oall['voters']=$tvoter;
				$oall['total']=$telec;
				$oall['percentage']=$fper;
				//print_r("\n\n2. \n");
				//print_r($stsum);die;
				
				$summary['overall']=$oall;
				$summary['total_ac']=$acount;
				$tempdata = DB::table('pd_scheduledetail')->orderBy('updated_at','DESC')->first();
				$summary['last_update_time']=$tempdata->updated_at;
				return response()->json($summary, $this->successStatus);
				
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		public function Dist2AcwisePt(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required',
				'electionphase' => 'required',
				'election_id' => 'required',
				'statecode' => 'required',
				'district_no' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				if(isset($userInputs['statecode']))
				{
					$stcode=trim($userInputs['statecode']);
				}
				else
				{
					$stcode="S00";
				}
				if(isset($userInputs['district_no']))
				{
					$distno=trim($userInputs['district_no']);
				}
				else
				{
					$distno=0;
				}

				$summary=array();
				$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				//print_r($eldata);
				$summary['success'] = true;
				$summary['message'] = "Poll TurnOut Home [District 2 ACWise]";
				$summary['phase'] = $scheduleid;
				$esubtype=$eldata->election_type;
				$result=array();
				if(empty($stcode) || $stcode=="S00")
					{
					$squery= DB::table('pd_scheduledetail');
					}
					else
					{
					$squery= DB::table('pd_scheduledetail')->where('st_code',$stcode);
				}
				if(!empty($distno) && $distno>0)
					{
					$squery= $squery->where('dist_no',$distno);
				}
				$stlist=$squery->get();
				//$stlist= $stlist= DB::table('pd_scheduledetail')->where('st_code',$stcode)->get();
				$acount=count($stlist);
				//print_r("\n\n 1. ");
				//print_r($acount);//die;
				//print_r("\n\n 2. ");
				//print_r($stlist);//die;
				$statids=array();
				$tcnt=count($stlist);
				foreach($stlist as $sttid)
				{
					$tempar=array();
					$tempar['acno']=$sttid->ac_no;
					$tempar['ac_name']=$this->commonModel->getacbyacno($sttid->st_code,$sttid->ac_no)->AC_NAME;
					$tempar['dist_no']=$sttid->dist_no;
					$tempar['district_name']=$this->commonModel->getdistrictbydistrictno($sttid->st_code,$sttid->dist_no)->DIST_NAME;
					$tempar['st_code']=$sttid->st_code;
					$tempar['st_name']=$this->commonModel->getstatebystatecode($sttid->st_code)->ST_NAME;
					$tempar['voters']=0;
					/* $tempar['r1_total'] = 0;
					$tempar['r1_per'] = $sttid->est_turnout_round1;
					$tempar['r2_total'] = 0;
					$tempar['r2_per'] = $sttid->est_turnout_round2;
					$tempar['r3_total'] = 0;
					$tempar['r3_per'] = $sttid->est_turnout_round3;
					$tempar['r4_total'] = 0;
					$tempar['r4_per'] = $sttid->est_turnout_round4;
					$tempar['r5_total'] = 0;
					$tempar['r5_per'] = $sttid->est_turnout_round5;
					$tempar['end_total'] = 0;
					$tempar['end_per'] = $sttid->close_of_poll; */
					$tempar['final_total'] = 0;
					$tempar['final_per'] = $sttid->est_turnout_total;
					$statids[]=$tempar;
				}
				//print_r(count($statids));die;
				$summary['acwise']=$statids;
				$oall=array();
				if(empty($distno) || $distno==0 || empty($stcode) || $stcode=="S00")
				{
					$telec = DB::table('pd_scheduledetail')->sum('electors_total');
					$tvoter = DB::table('pd_scheduledetail')->sum('est_voters');
					if(($tvoter>0) && ($telec>0))
					$fper=number_format(($tvoter * 100)/ $telec,2,".","");
					else
					$fper=0;
				}
				else
				{
					$telec = DB::table('pd_scheduledetail')->where('st_code',$stcode)->where('dist_no',$stcode)->sum('electors_total');
					$tvoter = DB::table('pd_scheduledetail')->where('st_code',$stcode)->where('dist_no',$stcode)->sum('est_voters');
					if(($tvoter>0) && ($telec>0))
					$fper=number_format(($tvoter * 100)/ $telec,2,".","");
					else
					$fper=0;
					
				}
				$oall['voters']=$tvoter;
				$oall['total']=$telec;
				$oall['percentage']=$fper;
				//print_r("\n\n2. \n");
				//print_r($stsum);die;
				
				$summary['overall']=$oall;
				$summary['total_ac']=$acount;
				$tempdata = DB::table('pd_scheduledetail')->orderBy('updated_at','DESC')->first();
				$summary['last_update_time']=$tempdata->updated_at;
				return response()->json($summary, $this->successStatus);
				
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		
		public function AcPt(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required',
				'electionphase' => 'required',
				'statecode' => 'required',
				'acno' => 'required'
				]);
				
				if($validator->fails()){
					return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				$stcode=trim($userInputs['statecode']);
				if(isset($userInputs['pcno']))
				$pcno=trim($userInputs['pcno']);
			else
				$pcno=0;
			
				$acno=trim($userInputs['acno']);
				
				/* if(empty($scheduleid))
					$scheduleid=7;
					if(empty($electiontypeid))
					$electiontypeid=1;
				*/
				$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				
				$pddata=DB::table('pd_schedulemaster')->where('schedule_id',$scheduleid)->get();
				$total_est=0;
				$pdilist=array();
				if(count($pddata))
				{
					foreach($pddata as $pditem)
					{
						$pdilist[] = $pditem->pd_scheduleid;
					}
				}
				//print_r($pdilist);die;
				$summary=array();
				
				$summary['success'] = true;
				$summary['message'] = "Poll TurnOut for AC";
				$summary['phase'] = $scheduleid;
				$esubtype=$eldata->election_type;
				$tempar=array();
				if(!empty($acno) && !empty($stcode) )
				{
					
					
					$tempar['acno']=$acno;
					$acdata=$this->commonModel->getacbyacno($stcode,$acno);
					$tempar['ac_name']=$acdata->AC_NAME;
										$tempar['st_code']=$stcode;
					$tempar['st_name']=$this->commonModel->getstatebystatecode($stcode)->ST_NAME;
					//$tempar['voters']=0;
					//$tempar['r1_total'] = 0;
					
					$tempdata = DB::table('pd_scheduledetail')->where('st_code',$stcode)->where('ac_no', $acno)->first();
					
					//print_r($tempdata);die;
					if(isset($tempdata))
					{
						/* $tempar['r1_total'] = 0;
						$tempar['r1_per']=$tempdata->est_turnout_round1;
						$tempar['r1_time']=$tempdata->update_at_round1;
						$tempar['r1_device']=$tempdata->update_device_round1;
						$tempar['r2_total'] = 0;
						$tempar['r2_per'] = $tempdata->est_turnout_round2;
						$tempar['r2_time']=$tempdata->update_at_round2;
						$tempar['r2_device']=$tempdata->update_device_round2;
						$tempar['r3_total'] = 0;
						$tempar['r3_per'] = $tempdata->est_turnout_round3;
						$tempar['r3_time']=$tempdata->update_at_round3;
						$tempar['r3_device']=$tempdata->update_device_round3;
						$tempar['r4_total'] = 0;
						$tempar['r4_per'] = $tempdata->est_turnout_round4;
						$tempar['r4_time']=$tempdata->update_at_round4;
						$tempar['r4_device']=$tempdata->update_device_round4;
						$tempar['r5_total'] = 0;
						$tempar['r5_per'] = $tempdata->est_turnout_round5;
						$tempar['r5_time']=$tempdata->update_at_round5;
						$tempar['r5_device']=$tempdata->update_device_round5;
						$tempar['end_total'] = 0;
						$tempar['end_per'] = $tempdata->close_of_poll;
						$tempar['end_time']=$tempdata->updated_at_close_of_poll;
						$tempar['end_device']=$tempdata->updated_device_close_of_poll; */
						$tempar['final_total'] = 0;
						$tempar['final_per'] = $tempdata->est_turnout_total;
						$tempar['final_time']=$tempdata->update_at_final;
						$tempar['final_device']=$tempdata->update_device_final;
						$tempar['timestamp'] = now();
					}
					
				}
				$summary['acdata']=$tempar;
				$summary['last_update_time']="";//$tempdata->updated_at;
				return response()->json($summary, $this->successStatus);
				
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		
		public function AcPtAdmin(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required',
				'electionphase' => 'required',
				'statecode' => 'required',
				'pcno' => 'required',
				'acno' => 'required',
				'Dist_No' => 'required',
				'election_id' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$distno=trim($userInputs['Dist_No']);
				$userInputs = $request->all();
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				$stcode=trim($userInputs['statecode']);
				$pcno=trim($userInputs['pcno']);
				$acno=trim($userInputs['acno']);
				
				/* if(empty($scheduleid))
					$scheduleid=7;
					if(empty($electiontypeid))
				$electiontypeid=1; */
				
				$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				
				$pddata=DB::table('pd_schedulemaster')->where('schedule_id',$scheduleid)->get();
				$total_est=0;
				$pdilist=array();
				if(count($pddata))
				{
					foreach($pddata as $pditem)
					{
						$pdilist[] = $pditem->pd_scheduleid;
					}
				}
				//print_r($pdilist);die;
				$summary=array();
				
				$summary['success'] = true;
				$summary['message'] = "Poll TurnOut for AC";
				$summary['phase'] = $scheduleid;
				$esubtype=$eldata->election_type;
				$tempar=array();
				if(!empty($acno) && !empty($pcno) && !empty($stcode) )
				{
					
					
					$tempar['acno']=$acno;
					$acdata=$this->commonModel->getacbyacno($stcode,$acno);
					$tempar['ac_name']=$acdata->AC_NAME;
					$tempar['pcno']=$pcno;
					$tempar['pc_name']=$this->commonModel->getpcbypcno($stcode,$pcno)->PC_NAME;
					$tempar['st_code']=$stcode;
					$tempar['st_name']=$this->commonModel->getstatebystatecode($stcode)->ST_NAME;
					$tempar['voters']=0;
					$tempar['r1_total'] = 0;
					$tempdata = DB::table('pd_scheduledetail')->where('st_code',$stcode)->where('pc_no', $pcno)->where('ac_no', $acno)->first();
					
					//print_r($tempdata);die;
					if(isset($tempdata))
					{
						/* $tempar['r1_total'] = 0;
						$tempar['r1_per']=$tempdata->est_turnout_round1;
						$tempar['r1_time']=$tempdata->update_at_round1;
						$tempar['r1_device']=$tempdata->update_device_round1;
						$tempar['r2_total'] = 0;
						$tempar['r2_per'] = $tempdata->est_turnout_round2;
						$tempar['r2_time']=$tempdata->update_at_round2;
						$tempar['r2_device']=$tempdata->update_device_round2;
						$tempar['r3_total'] = 0;
						$tempar['r3_per'] = $tempdata->est_turnout_round3;
						$tempar['r3_time']=$tempdata->update_at_round3;
						$tempar['r3_device']=$tempdata->update_device_round3;
						$tempar['r4_total'] = 0;
						$tempar['r4_per'] = $tempdata->est_turnout_round4;
						$tempar['r4_time']=$tempdata->update_at_round4;
						$tempar['r4_device']=$tempdata->update_device_round4;
						$tempar['r5_total'] = 0;
						$tempar['r5_per'] = $tempdata->est_turnout_round5;
						$tempar['r5_time']=$tempdata->update_at_round5;
						$tempar['r5_device']=$tempdata->update_device_round5;
						$tempar['end_total'] = 0;
						$tempar['end_per'] = $tempdata->close_of_poll;
						$tempar['end_time']=$tempdata->updated_at_close_of_poll;
						$tempar['end_device']=$tempdata->updated_device_close_of_poll; */
						$tempar['final_total'] = 0;
						$tempar['final_per'] = $tempdata->est_turnout_total;
						$tempar['final_time']=$tempdata->update_at_final;
						$tempar['final_device']=$tempdata->update_device_final;
						$tempar['timestamp'] = now();
					}
					
				}
				$summary['acdat']=$tempar;
				$summary['last_update_time']=$tempdata->updated_at;
				return response()->json($summary, $this->successStatus);
				
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		public function PhaseWiseState(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required','electionphase' => 'required'
				]);
				
				if($validator->fails()){
					return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				
				/* if(empty($scheduleid))
					$scheduleid=1;
					if(empty($electiontypeid))
				$electiontypeid=1; */
				
				$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				if($eldata->election_sort_name == "PC")
				{
					$pddata=DB::table('pd_schedulemaster')->where('schedule_id',$scheduleid)->get();
				}
				else
				{
					$pddata=DB::connection('mysql_Old')->table('pd_schedulemaster')->where('schedule_id',$scheduleid)->get();
				}
				$total_est=0;
				$pdilist=array();
				if(count($pddata))
				{
					foreach($pddata as $pditem)
					{
						$pdilist[] = $pditem->pd_scheduleid;
					}
				}
				$summary=array();
				
				$summary['success'] = true;
				$summary['message'] = "Final TurnOut Home [StateWise]";
				$summary['phase'] = $scheduleid;
				$summary['poll_date']=DB::table('m_schedule')->select('DATE_POLL')->where('scheduleid',$scheduleid)->first()->DATE_POLL;
				$esubtype=$eldata->election_type;
				$result=array();
				if($eldata->election_sort_name == "PC")
				{
					$stlist= DB::table('pd_scheduledetail')->select('st_code')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->groupby('st_code')->orderby('st_code')->get();
				}
				else
				{
					$stlist= DB::connection('mysql_Old')->table('pd_scheduledetail')->select('st_code')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->groupby('st_code')->orderby('st_code')->get();
				}
				$statids=array();
				$tcnt=count($stlist);
				$st_aggr=0;
				$gt_voter=0;
				$gt_voter_male=0;
				$gt_voter_female=0;
				$gt_voter_other=0;
				$gt_elector=0;
				foreach($stlist as $sttid)
				{
					if($eldata->election_sort_name == "PC")
					{
						$staclist=DB::table('pd_scheduledetail')->select('ac_no')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->where('st_code',$sttid->st_code)->get();
						$tempar=array();
						$tempar['st_code']=$sttid->st_code;
						$tempar['st_name']=$this->commonModel->getstatebystatecode($sttid->st_code)->ST_NAME;
						$tempar['total_electors']=DB::table('electors_cdac')->where('st_code',$sttid->st_code)->where('scheduledid', $scheduleid)->where('year', 2019)->sum('electors_total');
						$tempar['turnout_male'] = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total_male');
						$tempar['turnout_female'] = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total_female');
						$tempar['turnout_other'] = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total_other');
						$tempar['turnout_total'] = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total');
						if($tempar['total_electors']>0)
						$tempar['turnout_per'] = number_format(($tempar['turnout_total'] * 100)/ $tempar['total_electors'],2,".","");
						else
						$tempar['turnout_per'] = 0;
						$gt_voter += $tempar['turnout_total'];
						$gt_voter_male += $tempar['turnout_male'];
						$gt_voter_female += $tempar['turnout_female'];
						$gt_voter_other += $tempar['turnout_other'];
						$gt_elector += $tempar['total_electors'];
						$statids[]=$tempar;
					}
					else
					{
						$staclist=DB::connection('mysql_Old')->table('pd_scheduledetail')->select('ac_no')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->where('st_code',$sttid->st_code)->get();
						$tempar=array();
						$tempar['st_code']=$sttid->st_code;
						$tempar['st_name']=$this->commonModel->getstatebystatecode($sttid->st_code)->ST_NAME;
						$tempar['total_electors']=DB::connection('mysql_Old')->table('electors_cdac')->where('st_code',$sttid->st_code)->where('scheduledid', $scheduleid)->where('year', 2019)->sum('electors_total');
						$tempar['turnout_male'] = DB::connection('mysql_Old')->table('pd_scheduledetail')->where('st_code',$sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total_male');
						$tempar['turnout_female'] = DB::connection('mysql_Old')->table('pd_scheduledetail')->where('st_code',$sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total_female');
						$tempar['turnout_other'] = DB::connection('mysql_Old')->table('pd_scheduledetail')->where('st_code',$sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total_other');
						$tempar['turnout_total'] = DB::connection('mysql_Old')->table('pd_scheduledetail')->where('st_code',$sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total');
						if($tempar['total_electors']>0)
						$tempar['turnout_per'] = number_format(($tempar['turnout_total'] * 100)/ $tempar['total_electors'],2,".","");
						else
						$tempar['turnout_per'] = 0;
						$gt_voter += $tempar['turnout_total'];
						$gt_voter_male += $tempar['turnout_male'];
						$gt_voter_female += $tempar['turnout_female'];
						$gt_voter_other += $tempar['turnout_other'];
						$gt_elector += $tempar['total_electors'];
						$statids[]=$tempar;
					}
					
				}
				//die;
				usort($statids, function ($a, $b) { 
					//print_r($a);print_r("\n\n\n");print_r($b); die;
				return strcmp($a["st_name"], $b["st_name"]); });
				$summary['statewise']=$statids;
				
				$oall=array();
				$oall['total_voters']=$gt_voter;
				$oall['total_voters_male']=$gt_voter_male;
				$oall['total_voters_female']=$gt_voter_female;
				$oall['total_voters_other']=$gt_voter_other;
				$oall['total_electors']=$gt_elector;
				if($gt_elector>0)
				$oall['percentage']=number_format(($gt_voter * 100)/ $gt_elector,2,".","");
				else
				$oall['percentage']=0;
				$summary['overall']=$oall;
				//$tempdata = DB::table('pd_scheduledetail')->orderBy('updated_at','DESC')->first();
				//$summary['last_update_time']=$tempdata->updated_at;
				return response()->json($summary, $this->successStatus);
				
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		
		public function PhaseWisePC(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required','electionphase' => 'required'
				]);
				
				if($validator->fails()){
					return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				if(isset($userInputs['statecode']))
				$stcode = trim($userInputs['statecode']);
				else
				$stcode="S00";
				
				if(empty($scheduleid))
				$scheduleid=1;
				if(empty($electiontypeid))
				$electiontypeid=1;
				if(empty($stcode))
				$stcode="S00";
				
				$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				if($eldata->election_sort_name == "PC")
				{
					if($stcode == "S00")
					{
						$pddata=DB::table('pd_schedulemaster')->where('schedule_id',$scheduleid)->get();
					}
					else
					{
						$pddata=DB::table('pd_schedulemaster')->where('schedule_id',$scheduleid)->where('st_code',$stcode)->get();
					}
				}
				else
				{
					if($stcode == "S00")
					{
						$pddata=DB::connection('mysql_Old')->table('pd_schedulemaster')->where('schedule_id',$scheduleid)->get();
					}
					else
					{
						$pddata=DB::connection('mysql_Old')->table('pd_schedulemaster')->where('schedule_id',$scheduleid)->where('st_code',$stcode)->get();
					}
				}
				$total_est=0;
				$pdilist=array();
				if(count($pddata))
				{
					foreach($pddata as $pditem)
					{
						$pdilist[] = $pditem->pd_scheduleid;
					}
				}
				$summary=array();
				
				$summary['success'] = true;
				$summary['message'] = "Final TurnOut PCWise";
				$summary['phase'] = $scheduleid;
				$esubtype=$eldata->election_type;
				$result=array();
				if($eldata->election_sort_name == "PC")
				{
					if($stcode == "S00")
					{
						$stlist= DB::table('pd_scheduledetail')->select('st_code','pc_no')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->groupby('pc_no')->get();
					}
					else
					{
						$stlist= DB::table('pd_scheduledetail')->select('st_code','pc_no')->where('scheduleid',$scheduleid)->where('st_code',$stcode)->whereIn('pd_scheduleid', $pdilist)->groupby('pc_no')->get();
					}
				}
				else
				{
					if($stcode == "S00")
					{
						$stlist= DB::connection('mysql_Old')->table('pd_scheduledetail')->select('st_code','pc_no')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->groupby('pc_no')->orderby('st_code')->get();
					}
					else
					{
						$stlist= DB::connection('mysql_Old')->table('pd_scheduledetail')->select('st_code','pc_no')->where('scheduleid',$scheduleid)->where('st_code',$stcode)->groupby('pc_no')->orderby('st_code')->get();
					}
				}
				$statids=array();
				$tcnt=count($stlist);
				$st_aggr=0;
				$gt_voter=0;
				$gt_voter_male=0;
				$gt_voter_female=0;
				$gt_voter_other=0;
				$gt_elector=0;
				//print_r($stlist);
				foreach($stlist as $sttid)
				{
					if($eldata->election_sort_name == "PC")
					{
						$staclist=DB::table('pd_scheduledetail')->select('ac_no')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->where('st_code',$sttid->st_code)->get();
						$tempar=array();
						$tempar['st_code']=$sttid->st_code;
						$tempar['st_name']=$this->commonModel->getstatebystatecode($sttid->st_code)->ST_NAME;
						$tempar['pc_no']=$sttid->pc_no;
						$tempar['pc_name']=$this->commonModel->getpcbypcno($sttid->st_code,$sttid->pc_no)->PC_NAME;
						$tempar['total_electors']=DB::table('electors_cdac')->where('st_code',$sttid->st_code)->where('pc_no',$sttid->pc_no)->where('scheduledid',$scheduleid)->where('year',2019)->sum('electors_total');
						$tempar['turnout_male'] = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->where('pc_no',$sttid->pc_no)->whereIn('pd_scheduleid', $pdilist)->sum('total_male');
						$tempar['turnout_female'] = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->where('pc_no',$sttid->pc_no)->whereIn('pd_scheduleid', $pdilist)->sum('total_female');
						$tempar['turnout_other'] = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->where('pc_no',$sttid->pc_no)->whereIn('pd_scheduleid', $pdilist)->sum('total_other');
						$tempar['turnout_total'] = DB::table('pd_scheduledetail')->where('st_code',$sttid->st_code)->where('pc_no',$sttid->pc_no)->whereIn('pd_scheduleid', $pdilist)->sum('total');
						if($tempar['total_electors']>0)
						$tempar['turnout_per'] = number_format(($tempar['turnout_total'] * 100)/ $tempar['total_electors'],2,".","");
						else
						$tempar['turnout_per']=0;
						$gt_voter += $tempar['turnout_total'];
						$gt_voter_male += $tempar['turnout_male'];
						$gt_voter_female += $tempar['turnout_female'];
						$gt_voter_other += $tempar['turnout_other'];
						$gt_elector += $tempar['total_electors'];
						$statids[]=$tempar;
					}
					else
					{
						$staclist=DB::connection('mysql_Old')->table('pd_scheduledetail')->select('ac_no')->where('scheduleid',$scheduleid)->whereIn('pd_scheduleid', $pdilist)->where('st_code',$sttid->st_code)->get();
						//print_r($sttid);die;
						
						$tempar=array();
						$tempar['st_code']=$sttid->st_code;
						$tempar['st_name']=$this->commonModel->getstatebystatecode($sttid->st_code)->ST_NAME;
						$tempar['pc_no']=$sttid->pc_no;
						$tempar['pc_name']=$this->commonModel->getpcbypcno($sttid->st_code,$sttid->pc_no)->PC_NAME;
						$tempar['total_electors']=DB::connection('mysql_Old')->table('electors_cdac')->where('st_code',$sttid->st_code)->where('pc_no',$sttid->pc_no)->where('scheduledid',$scheduleid)->where('year',2019)->sum('electors_total');
						$tempar['turnout_male'] = DB::connection('mysql_Old')->table('pd_scheduledetail')->where('st_code',$sttid->st_code)->where('pc_no',$sttid->pc_no)->whereIn('pd_scheduleid', $pdilist)->sum('total_male');
						$tempar['turnout_female'] = DB::connection('mysql_Old')->table('pd_scheduledetail')->where('st_code',$sttid->st_code)->where('pc_no',$sttid->pc_no)->whereIn('pd_scheduleid', $pdilist)->sum('total_female');
						$tempar['turnout_other'] = DB::connection('mysql_Old')->table('pd_scheduledetail')->where('st_code',$sttid->st_code)->where('pc_no',$sttid->pc_no)->whereIn('pd_scheduleid', $pdilist)->sum('total_other');
						$tempar['turnout_total'] = DB::connection('mysql_Old')->table('pd_scheduledetail')->where('st_code',$sttid->st_code)->where('pc_no',$sttid->pc_no)->whereIn('pd_scheduleid', $pdilist)->sum('total');
						$tempar['turnout_per'] = number_format(($tempar['turnout_total'] * 100)/ $tempar['total_electors'],2,".","");
						$gt_voter += $tempar['turnout_total'];
						$gt_voter_male += $tempar['turnout_male'];
						$gt_voter_female += $tempar['turnout_female'];
						$gt_voter_other += $tempar['turnout_other'];
						$gt_elector += $tempar['total_electors'];
						$statids[]=$tempar;
					}
					
				}
				//die;
				usort($statids, function ($a, $b) { 
				return strcmp($a["st_name"], $b["st_name"]); });
				
				$summary['pcwise']=$statids;
				
				$oall=array();
				$oall['total_voters']=$gt_voter;
				$oall['total_voters_male']=$gt_voter_male;
				$oall['total_voters_female']=$gt_voter_female;
				$oall['total_voters_other']=$gt_voter_other;
				$oall['total_electors']=$gt_elector;
				if($gt_elector > 0)
				$oall['percentage']=number_format(($gt_voter * 100)/ $gt_elector,2,".","");
				else
				$gt_elector=0;
				$summary['overall']=$oall;
				//$tempdata = DB::table('pd_scheduledetail')->orderBy('updated_at','DESC')->first();
				//$summary['last_update_time']=$tempdata->updated_at;
				return response()->json($summary, $this->successStatus);
				
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		
		public function PhaseWiseAC(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required','electionphase' => 'required','statecode' => 'required'
				]);
				
				if($validator->fails()){
					return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				$stcode = trim($userInputs['statecode']);
				if(isset($userInputs['pcno']))
				$pcno = trim($userInputs['pcno']);
				else
				$pcno=0;
				
				if(empty($scheduleid))
				$scheduleid=1;
				if(empty($electiontypeid))
				$electiontypeid=1;
				
				$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				if($eldata->election_sort_name == "PC")
				{
					if($pcno > 0)
					$pddata=DB::table('pd_schedulemaster')->where('schedule_id',$scheduleid)->where('st_code',$stcode)->where('pc_no',$pcno)->get();
					else
					$pddata=DB::table('pd_schedulemaster')->where('schedule_id',$scheduleid)->where('st_code',$stcode)->orderby('st_code')->get();
				}
				else
				{
					$pddata=DB::connection('mysql_Old')->table('pd_schedulemaster')->where('schedule_id',$scheduleid)->where('st_code',$stcode)->get();
				}
				$total_est=0;
				
				$summary=array();
				
				$summary['success'] = true;
				$summary['message'] = "Final TurnOut ACWise";
				$summary['phase'] = $scheduleid;
				$esubtype=$eldata->election_type;
				$result=array();
				if($eldata->election_sort_name == "PC")
				{
					$stlist= DB::table('pd_scheduledetail')->select('st_code','pc_no','ac_no')->where('scheduleid',$scheduleid)->where('st_code',$stcode)->where('pc_no',$pcno)->get();
				}
				else
				{
					$stlist= DB::connection('mysql_Old')->table('pd_scheduledetail')->select('st_code','pc_no','ac_no')->where('scheduleid',$scheduleid)->where('st_code',$stcode)->get();
				}
				$statids=array();
				$tcnt=count($stlist);
				$st_aggr=0;
				$gt_voter=0;
				$gt_voter_male=0;
				$gt_voter_female=0;
				$gt_voter_other=0;
				$gt_elector=0;
				foreach($stlist as $sttid)
				{
					if($eldata->election_sort_name == "PC")
					{
						$acdata=DB::table('pd_scheduledetail')->where('scheduleid',$scheduleid)->where('st_code',$sttid->st_code)->where('pc_no',$pcno)->where('ac_no',$sttid->ac_no)->first();
						$tempar=array();
						$tempar['st_code']=$sttid->st_code;
						$tempar['st_name']=$this->commonModel->getstatebystatecode($sttid->st_code)->ST_NAME;
						$tempar['pc_no']=$sttid->pc_no;
						$tempar['pc_name']=$this->commonModel->getpcbypcno($sttid->st_code,$sttid->pc_no)->PC_NAME;
						$tempar['ac_no']=$sttid->ac_no;
						$tempar['ac_name']=$this->commonModel->getacbyacno($sttid->st_code,$sttid->ac_no)->AC_NAME;
						$cdacm=DB::table('electors_cdac')->where('st_code',$sttid->st_code)->where('pc_no',$pcno)->where('ac_no',$sttid->ac_no)->where('year',2019)->first();
						$tempar['total_electors']=$cdacm->electors_total;
						$tempar['turnout_male'] = $acdata->total_male;
						$tempar['turnout_female'] = $acdata->total_female;
						$tempar['turnout_other'] = $acdata->total_other;
						$tempar['turnout_total'] = $acdata->total;
						$tempar['turnout_per'] = number_format(($tempar['turnout_total'] * 100)/ $tempar['total_electors'],2,".","");
						$gt_voter += $tempar['turnout_total'];
						$gt_voter_male += $tempar['turnout_male'];
						$gt_voter_female += $tempar['turnout_female'];
						$gt_voter_other += $tempar['turnout_other'];
						$gt_elector += $tempar['total_electors'];
						$statids[]=$tempar;
					}
					else
					{
						
						$acdata=DB::connection('mysql_Old')->table('pd_scheduledetail')->where('scheduleid',$scheduleid)->where('st_code',$sttid->st_code)->where('ac_no',$sttid->ac_no)->first();
						$tempar=array();
						$tempar['st_code']=$sttid->st_code;
						$tempar['st_name']=$this->commonModel->getstatebystatecode($sttid->st_code)->ST_NAME;
						$tempar['pc_no']=0;//$sttid->pc_no;
						$tempar['pc_name']="";//$this->commonModel->getpcbypcno($sttid->st_code,$sttid->pc_no)->PC_NAME;
						$tempar['ac_no']=$sttid->ac_no;
						$tempar['ac_name']=$this->commonModel->getacbyacno($sttid->st_code,$sttid->ac_no)->AC_NAME;
						$cdacm=DB::table('electors_cdac')->where('st_code',$sttid->st_code)->where('ac_no',$sttid->ac_no)->where('year',2019)->first();
						$tempar['total_electors']=$cdacm->electors_total;
						$tempar['turnout_male'] = $acdata->total_male;
						$tempar['turnout_female'] = $acdata->total_female;
						$tempar['turnout_other'] = $acdata->total_other;
						$tempar['turnout_total'] = $acdata->total;
						$tempar['turnout_per'] = number_format(($tempar['turnout_total'] * 100)/ $tempar['total_electors'],2,".","");
						$gt_voter += $tempar['turnout_total'];
						$gt_voter_male += $tempar['turnout_male'];
						$gt_voter_female += $tempar['turnout_female'];
						$gt_voter_other += $tempar['turnout_other'];
						$gt_elector += $tempar['total_electors'];
						$statids[]=$tempar;
					}
					
				}
				//die;
				$summary['pcwise']=$statids;
				
				$oall=array();
				$oall['total_voters']=$gt_voter;
				$oall['total_voters_male']=$gt_voter_male;
				$oall['total_voters_female']=$gt_voter_female;
				$oall['total_voters_other']=$gt_voter_other;
				$oall['total_electors']=$gt_elector;
				if($gt_elector > 0)
				$oall['percentage']=number_format(($gt_voter * 100)/ $gt_elector,2,".","");
				else
				$oall['percentage']=0;
				$summary['overall']=$oall;
				//$tempdata = DB::table('pd_scheduledetail')->orderBy('updated_at','DESC')->first();
				//$summary['last_update_time']=$tempdata->updated_at;
				return response()->json($summary, $this->successStatus);
				
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		
		public function AddPT(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required',
				'electionphase' => 'required',
				'statecode' => 'required',
				'pcno' => 'required',
				'acno' => 'required',
				'user_id' => 'required',
				'ac_token' => 'required',
				'round' => 'required',
				'percentage' => 'required',
				'Dist_No' => 'required',
				'election_id' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$distno=trim($userInputs['Dist_No']);
				$userInputs = $request->all();
				$actoken=trim($userInputs['ac_token']);
				$userid=trim($userInputs['user_id']);
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				$stcode=trim($userInputs['statecode']);
				$pcno=trim($userInputs['pcno']);
				$acno=trim($userInputs['acno']);
				
				$vround=trim($userInputs['round']);
				$percval=trim($userInputs['percentage']);
				
				if(empty($scheduleid))
				$scheduleid=1;
				if(empty($electiontypeid))
				$electiontypeid=3;
				
				$summary=array();
				$summary['success'] = false;
				$summary['message'] = "Login Failed";
				
				if(!empty($userid) && !empty($actoken))
				{
					$udata=DB::table('officer_login')->where('id',$userid)->first();
					if(isset($udata->officername))
					{
						//if($udata->officername == $userid)
						if($udata->accesstoken == $actoken)	
						{
							$summary['success'] = true;
							$summary['message'] = "";
						}
						else
						{
							$summary['message'] = "Access Token Mismatched";
							return response()->json($summary, $this->successStatus);
						}
					}
					else
					{
						$summary['message'] = "User_id not found";
						return response()->json($summary, $this->successStatus);
					}
					if($summary['success'])
					{
						$tstamp=now()->toDateTimeString();
						$acdata=DB::table('pd_scheduledetail')->where('st_code',$stcode)->where('pc_no', $pcno)->where('ac_no', $acno)->first();
						$tvoter=number_format(($acdata->electors_total * $percval)/100,0,".","");
						$lastr=0;
						if($acdata->est_turnout_round1 > 0)
						{
							$lastr=1;
							$lastv=$acdata->est_turnout_round1;
						}
						if($acdata->est_turnout_round2 > 0)
						{
							$lastr=2;
							$lastv=$acdata->est_turnout_round2;
						}
						if($acdata->est_turnout_round3 > 0)
						{
							$lastr=3;
							$lastv=$acdata->est_turnout_round3;
						}
						if($acdata->est_turnout_round4 > 0)
						{
							$lastr=4;
							$lastv=$acdata->est_turnout_round4;
						}
						if($acdata->est_turnout_round5 > 0)
						{
							$lastr=5;
							$lastv=$acdata->est_turnout_round5;
						}
						if($acdata->close_of_poll > 0)
						{
							$lastr=6;
							$lastv=$acdata->close_of_poll;
						}
						$ur=0;
						$last_final = $acdata->est_turnout_total;
						if($vround ==1)
						{
							$updata=array('est_turnout_round1'=>$percval, 'est_turnout_total' =>$percval, 'update_device_round1' => 'Mobile', 'update_at_round1' => $tstamp, 'update_at_final' => $tstamp, 'update_device_final' => 'Mobile',  'est_voters' => $tvoter);
							$ur=1;
						}
						elseif($vround ==2)
						{
							$updata=array('est_turnout_round2'=>$percval, 'est_turnout_total' => $percval, 'update_device_round2' => 'Mobile', 'update_at_round2' => $tstamp, 'update_at_final' => $tstamp, 'update_device_final' => 'Mobile',  'est_voters' => $tvoter);
							$ur=2;
						}
						elseif($vround ==3)
						{
							$updata=array('est_turnout_round3'=>$percval, 'est_turnout_total' => $percval, 'update_device_round3' => 'Mobile', 'update_at_round3' => $tstamp, 'update_at_final' => $tstamp, 'update_device_final' => 'Mobile',  'est_voters' => $tvoter);
							$ur=3;
						}
						elseif($vround ==4)
						{
							$updata=array('est_turnout_round4'=>$percval, 'est_turnout_total' => $percval, 'update_device_round4' => 'Mobile', 'update_at_round4' => $tstamp, 'update_at_final' => $tstamp, 'update_device_final' => 'Mobile',  'est_voters' => $tvoter);
							$ur=4;
						}
						elseif($vround ==5)
						{
							$updata=array('est_turnout_round5'=>$percval, 'est_turnout_total' => $percval, 'update_device_round5' => 'Mobile', 'update_at_round5' => $tstamp, 'update_at_final' => $tstamp, 'update_device_final' => 'Mobile',  'est_voters' => $tvoter);
							$ur=5;
						}
						elseif($vround ==6)
						{
							$updata=array('close_of_poll'=>$percval, 'est_turnout_total' => $percval, 'updated_device_close_of_poll' => 'Mobile', 'updated_at_close_of_poll' => $tstamp, 'update_at_final' => $tstamp, 'update_device_final' => 'Mobile',  'est_voters' => $tvoter);
							$ur=6;
							
						}
						
						
						$upstat=DB::table('pd_scheduledetail')->where('st_code',$stcode)->where('pc_no', $pcno)->where('ac_no', $acno)->update($updata);
						//print_r("Last : $lastr / $last_final, Current : $vround / $percval"); 
						if($vround < $lastr)
						{
							if($last_final < $percval)
							{
								$tvoter=number_format(($acdata->electors_total * $percval)/100,0,".","");
								$updata=array('est_turnout_total' => $percval,'est_voters' => $tvoter);
							}
							else
							{
								$tvoter=number_format(($acdata->electors_total * $last_final)/100,0,".","");
								$updata=array('est_turnout_total' => $last_final,'est_voters' => $tvoter);
							}
							//print_r("\n\n Updating ".$acdata->est_turnout_total." with $last_final");die;
							$upstat=DB::table('pd_scheduledetail')->where('st_code',$stcode)->where('pc_no', $pcno)->where('ac_no', $acno)->update($updata);
						}
						$summary['message'] = "Data Saved successfully";
						$summary['upstat'] = $upstat;
						return response()->json($summary, $this->successStatus);
					}///EndSummary(success)
				}///EndEmptyToken
				return response()->json($summary, $this->successStatus);
				
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		
		public function ObList(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'user_id' => 'required','ac_token' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$actoken=trim($userInputs['ac_token']);
				$userid=trim($userInputs['user_id']);
				if(isset($userInputs['statecode']))
				$stcode=trim($userInputs['statecode']);
				else
				$stcode='';
				if(isset($userInputs['pcno']))
				$pcno=trim($userInputs['pcno']);
				else
				$pcno='';
				if(isset($userInputs['acno']))
				$acno=trim($userInputs['acno']);
				else
				$acno='';
				
				
				$summary=array();
				$summary['success'] = false;
				$summary['message'] = "Login Failed";
				
				if(!empty($userid) && !empty($actoken))
				{
					$udata=DB::table('officer_login')->where('id',$userid)->first();
					if(isset($udata->officername))
					{
						//if($udata->officername == $userid)
						if($udata->accesstoken == $actoken)	
						{
							$summary['success'] = true;
							$summary['message'] = "";
						}
						else
						{
							$summary['message'] = "Access Token Mismatched";
							return response()->json($summary, $this->successStatus);
						}
					}
					else
					{
						$summary['message'] = "User_id not found";
						return response()->json($summary, $this->successStatus);
					}
					if($summary['success'])
					{
						$psturl="http://164.100.128.75/obsSuvidha/api/ObsDeployeDetails/GetObsDeploymentStWise?St_code=$stcode&PC_No=$pcno&AC_No=$acno&AuthKey=ascb234$$%^L;:l;aeA!de2^DHESA";
						$client = new Client();
						$res = $client->request('POST', $psturl, [
						'form_params' => [
						'St_code' => $stcode,
						'PC_No' => $pcno,
						'AC_No' => $acno,
						'AuthKey' => 'ascb234$$%^L;:l;aeA!de2^DHESA',
						
						]
						]);
						
						
						$summary['message'] = "Observer List";
						$summary['oblist'] = $res->getBody()->getContents();
						$summary['timestamp']=now()->toDateTimeString();
						return response()->json($summary, $this->successStatus);
					}///EndSummary(success)
				}///EndEmptyToken
				return response()->json($summary, $this->successStatus);
				
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		public function acInPcCounting(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'user_id' => 'required','ac_token' => 'required','statecode' => 'required','pcno' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json(Crypt::encryptString(json_encode(['success' => false,'message'=>'Please Check the Input Details'])));
					
					// response()->json(['success' => false,'message'=>'Please Check the Input Details']);
					
				} 
				
				$userInputs = $request->all();
				$actoken=trim($userInputs['ac_token']);
				$userid=trim($userInputs['user_id']);
				$stcode=trim($userInputs['statecode']);
				$pcno=trim($userInputs['pcno']);
				
				
				
				$summary=array();
				$summary['success'] = false;
				$summary['message'] = "Login Failed";
				
				if(!empty($userid) && !empty($actoken))
				{
					$udata=DB::table('officer_login')->where('id',$userid)->first();
					if(isset($udata->officername))
					{
						
						if($udata->accesstoken == $actoken)	
						{
							
							if($udata->officerlevel == 'ECI')
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							elseif(($udata->officerlevel == 'CEO') && ($udata->st_code == $stcode))
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							elseif(($udata->officerlevel == 'PC') && ($udata->st_code == $stcode) && ($udata->pc_no == $pcno))
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							else
							{
								$summary['message'] = "Access Level error. No clearance for this Login";
								return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
								//return response()->json($summary, $this->successStatus);
							}
						}
						else
						{
							$summary['message'] = "Access Token Mismatched";
							return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
							//return response()->json($summary, $this->successStatus);
						}
					}
					else
					{
						$summary['message'] = "User_id not found";
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
						//return response()->json($summary, $this->successStatus);
					}
					if($summary['success'])
					{
						$summary['st_code'] = $stcode;
						$summary['st_name'] = $this->commonModel->getstatebystatecode($stcode)->ST_NAME;
						$summary['pc_no'] = $pcno;
						$summary['pc_name'] = $this->commonModel->getpcbypcno($stcode,$pcno)->PC_NAME;
						$ctname="counting_master_".strtolower($stcode);
						$cdata=DB::table($ctname)->select('ac_no','complete_round')->where('pc_no',$pcno)->groupBy('ac_no')->orderby('ac_no')->get();
						$mdata=array();
						foreach($cdata as $facd)
						{
							$mdata[$facd->ac_no]['complete_round']=$facd->complete_round;
							$mdata[$facd->ac_no]['total_round']= 0;
							$mdata[$facd->ac_no]['pending_round']= 0;
						}
						$rdatam=DB::table('round_master')->select('pc_no','ac_no','scheduled_round')->where('st_code',strtoupper($stcode))->where('pc_no',$pcno)->get();
						
						foreach($rdatam as $facd)
						{
							$mdata[$facd->ac_no]['total_round']=$facd->scheduled_round;
							$mdata[$facd->ac_no]['pending_round']= $facd->scheduled_round - $mdata[$facd->ac_no]['complete_round'];
						}
						$aclist=DB::table('counting_finalized_ac')->where('st_code',$stcode)->where('pc_no', $pcno)->get();
						$acf=array();
						
						foreach($aclist as $acitem)
						{
							//ac_name, ac_no, login_user, finalized
							$actmp=array();
							$actmp['ac_no']=$acitem->ac_no;
							$actmp['ac_name']=$this->commonModel->getacbyacno($stcode,$acitem->ac_no)->AC_NAME;
							$logdata=DB::table('officer_login')->where('st_code',$stcode)->where('pc_no', $pcno)->where('ac_no', $acitem->ac_no)->first();
							$actmp['login_user']=$logdata->officername;
							if(isset($mdata[$acitem->ac_no]['total_round']))
							$actmp['total_rounds']=$mdata[$acitem->ac_no]['total_round'];
							else
							$actmp['total_rounds']=0;
							if(isset($mdata[$acitem->ac_no]))
							$actmp['complete_rounds']=$mdata[$acitem->ac_no]['complete_round'];
							else
							$actmp['complete_rounds']=0;
							if(isset($mdata[$acitem->ac_no]))
							$actmp['pending_rounds']=$mdata[$acitem->ac_no]['pending_round'];
							else
							$actmp['pending_rounds']=0;
							if(isset($mdata[$acitem->ac_no]))
							$actmp['finalized']=$acitem->finalized_ac;
							else
							$actmp['finalized']=0;
							$acf[]=$actmp;
						}
						
						$summary['message']="ACs in ".$summary['pc_name'];
						$summary['acfinalized'] = $acf;
						$summary['timestamp']=now()->toDateTimeString();
						
						//return response()->json($summary, $this->successStatus);
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus);
					}///EndSummary(success)
				}///EndEmptyToken
				
				//return response()->json($summary, $this->successStatus);
				return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus);
				
			}
			catch (Exception $ex) 
			{
				return response()->json(Crypt::encryptString(json_encode(['success' => false,'error'=>'Internal Server Error'])), $this->intservererrorStatus); 
				//return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		
		public function postalPC(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'user_id' => 'required','ac_token' => 'required','statecode' => 'required','pcno' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json(Crypt::encryptString(json_encode(['success' => false,'message'=>'Please Check the Input Details']))); 
					//return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$actoken=trim($userInputs['ac_token']);
				$userid=trim($userInputs['user_id']);
				$stcode=trim($userInputs['statecode']);
				$pcno=trim($userInputs['pcno']);
				
				$summary=array();
				$summary['success'] = false;
				$summary['message'] = "Login Failed";
				
				if(!empty($userid) && !empty($actoken))
				{
					$udata=DB::table('officer_login')->where('id',$userid)->first();
					if(isset($udata->officername))
					{
						
						if($udata->accesstoken == $actoken)	
						{
							
							if($udata->officerlevel == 'ECI')
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							elseif(($udata->officerlevel == 'CEO') && ($udata->st_code == $stcode))
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							elseif(($udata->officerlevel == 'PC') && ($udata->st_code == $stcode) && ($udata->pc_no == $pcno))
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							else
							{
								$summary['message'] = "Access Level error. No clearance for this Login";
								return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
								//return response()->json($summary, $this->successStatus);
							}
						}
						else
						{
							$summary['message'] = "Access Token Mismatched";
							return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
							//return response()->json($summary, $this->successStatus);
						}
					}
					else
					{
						$summary['message'] = "User_id not found";
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
						//return response()->json($summary, $this->successStatus);
					}
					if($summary['success'])
					{
						$summary['st_code'] = $stcode;
						$summary['st_name'] = $this->commonModel->getstatebystatecode($stcode)->ST_NAME;
						$summary['pc_no'] = $pcno;
						$summary['pc_name'] = $this->commonModel->getpcbypcno($stcode,$pcno)->PC_NAME;
						$ctname="counting_master_".strtolower($stcode);
						$votelist=DB::table('counting_pcmaster')->select('nom_id','candidate_id','candidate_name','party_id','party_name','evm_vote','postal_vote','total_vote')->where('st_code', strtoupper($stcode))->where('pc_no', $pcno)->get();
						$pvl=array();
						foreach($votelist as $partyvote)
						{
							//party_code, party_name, candidate_id, candidate_name,total_evm, total_postal
							$pitem=array();
							$pitem['party_code']=$partyvote->party_id;
							$pitem['party_name']=$partyvote->party_name;
							$pitem['candidate_id']=$partyvote->candidate_id;
							$pitem['candidate_name']=$partyvote->candidate_name;
							$pitem['total_evm']=$partyvote->evm_vote;
							$pitem['total_postal']=$partyvote->postal_vote;
							$pitem['total'] = $partyvote->total_vote;
							$pvl[]=$pitem;
						}
						
						$summary['message']="Postal Votes in ".$summary['pc_name'];
						$summary['postalVotes'] = $pvl;
						$summary['timestamp']=now()->toDateTimeString();
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus);
						
						//return response()->json($summary, $this->successStatus);
					}///EndSummary(success)
				}///EndEmptyToken
				
				//return response()->json($summary, $this->successStatus);
				return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus);
				
			}
			catch (Exception $ex) 
			{
				return response()->json(Crypt::encryptString(json_encode(['success' => false,'error'=>'Internal Server Error'])), $this->intservererrorStatus); 
				//return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		public function partyInAc(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'user_id' => 'required','ac_token' => 'required','statecode' => 'required','pcno' => 'required','acno' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json(Crypt::encryptString(json_encode(['success' => false,'message'=>'Please Check the Input Details']))); 
					//return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$actoken=trim($userInputs['ac_token']);
				$userid=trim($userInputs['user_id']);
				$stcode=trim($userInputs['statecode']);
				$pcno=trim($userInputs['pcno']);
				$acno=trim($userInputs['acno']);
				
				
				$summary=array();
				$summary['success'] = false;
				$summary['message'] = "Login Failed";
				
				if(!empty($userid) && !empty($actoken))
				{
					$udata=DB::table('officer_login')->where('id',$userid)->first();
					if(isset($udata->officername))
					{
						
						if($udata->accesstoken == $actoken)	
						{
							
							if($udata->officerlevel == 'ECI')
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							elseif(($udata->officerlevel == 'CEO') && ($udata->st_code == $stcode))
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							elseif(($udata->officerlevel == 'PC') && ($udata->st_code == $stcode) && ($udata->pc_no == $pcno))
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							elseif(($udata->officerlevel == 'AC') && ($udata->st_code == $stcode) && ($udata->pc_no == $pcno) && ($udata->ac_no == $acno))
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							else
							{
								$summary['message'] = "Access Level error. No clearance for this Login";
								return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
								//return response()->json($summary, $this->successStatus);
							}
						}
						else
						{
							$summary['message'] = "Access Token Mismatched";
							return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
							//return response()->json($summary, $this->successStatus);
						}
					}
					else
					{
						$summary['message'] = "User_id not found";
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
						//return response()->json($summary, $this->successStatus);
					}
					if($summary['success'])
					{
						$summary['st_code'] = $stcode;
						$summary['st_name'] = $this->commonModel->getstatebystatecode($stcode)->ST_NAME;
						$summary['pc_no'] = $pcno;
						$summary['pc_name'] = $this->commonModel->getpcbypcno($stcode,$pcno)->PC_NAME;
						$summary['ac_no'] = $acno;
						$summary['ac_name'] = $this->commonModel->getacbyacno($stcode,$acno)->AC_NAME;
						$ctname="counting_master_".strtolower($stcode);
						$prtcol="counting_master_".strtolower($stcode).".party_id";
						$parties=DB::table($ctname)->select($prtcol)->where($ctname.'.pc_no', $pcno)->where($ctname.'.ac_no', $acno)->get();
						$summary['message']="Parties in AC ".trim($summary['ac_name'])." in PC ".trim($summary['pc_name'])." of ".$summary['st_name'] ;
						$summary['parties'] = array();
						//print_r($parties);
						foreach($parties as $partydata)
						{
							$pdata=array();
							$pdata['party_id']=$partydata->party_id;
							$pdat=$this->commonModel->getparty($partydata->party_id);
							$sdata=$this->commonModel->getsymbol($pdat->PARTYSYM);
							$pdata['party_abbr']=$pdat->PARTYABBRE;
							$pdata['party_name']=$pdat->PARTYNAME;
							if(isset($sdata->SYMBOL_DES))
							{
								$pdata['symbol_text']=$sdata->SYMBOL_DES;
								$pdata['symbol']=$sdata->Symbol_Img;
							}
							else
							{
								$pdata['symbol_text']='';
								$pdata['symbol']='';
							}
							$summary['parties'][]=$pdata;	
							
						}
						$summary['timestamp']=now()->toDateTimeString();
						
						//return response()->json($summary, $this->successStatus);
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus);
					}///EndSummary(success)
				}///EndEmptyToken
				
				//return response()->json($summary, $this->successStatus);
				return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus);
			}
			catch (Exception $ex) 
			{
				return response()->json(Crypt::encryptString(json_encode(['success' => false,'error'=>'Internal Server Error'])), $this->intservererrorStatus); 
				//return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		public function CountingACList(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'user_id' => 'required','ac_token' => 'required','statecode' => 'required','pcno' => 'required','acno' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json(Crypt::encryptString(json_encode(['success' => false,'message'=>'Please Check the Input Details']))); 
					//return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$actoken=trim($userInputs['ac_token']);
				$userid=trim($userInputs['user_id']);
				$stcode=trim($userInputs['statecode']);
				$pcno=trim($userInputs['pcno']);
				$acno=trim($userInputs['acno']);
				
				
				$summary=array();
				$summary['success'] = false;
				$summary['message'] = "Login Failed";
				
				if(!empty($userid) && !empty($actoken))
				{
					$udata=DB::table('officer_login')->where('id',$userid)->first();
					if(isset($udata->officername))
					{
						
						if($udata->accesstoken == $actoken)	
						{
							
							if($udata->officerlevel == 'ECI')
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							elseif(($udata->officerlevel == 'CEO') && ($udata->st_code == $stcode))
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							elseif(($udata->officerlevel == 'PC') && ($udata->st_code == $stcode) && ($udata->pc_no == $pcno))
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							elseif(($udata->officerlevel == 'AC') && ($udata->st_code == $stcode) && ($udata->pc_no == $pcno) && ($udata->ac_no == $acno))
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							else
							{
								$summary['message'] = "Access Level error. No clearance for this Login";
								return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
								//return response()->json($summary, $this->successStatus);
							}
						}
						else
						{
							$summary['message'] = "Access Token Mismatched";
							return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
							//return response()->json($summary, $this->successStatus);
						}
					}
					else
					{
						$summary['message'] = "User_id not found";
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
						//return response()->json($summary, $this->successStatus);
					}
					if($summary['success'])
					{
						$summary['st_code'] = $stcode;
						$tmp=$this->commonModel->getstatebystatecode($stcode);
						if(isset($tmp->ST_NAME))
						$summary['st_name'] = $tmp->ST_NAME;
						else
						$summary['st_name'] = 'Not Found';
						$summary['pc_no'] = $pcno;
						$tmp=$this->commonModel->getpcbypcno($stcode,$pcno);
						if(isset($tmp->PC_NAME))
						$summary['pc_name'] = $tmp->PC_NAME;
						else
						$summary['pc_name'] = 'Not Found';
						//$summary['pc_name'] = $this->commonModel->getpcbypcno($stcode,$pcno)->PC_NAME;
						$summary['ac_no'] = $acno;
						$tmp=$this->commonModel->getacbyacno($stcode,$acno);
						if(isset($tmp->AC_NAME))
						$summary['ac_name'] = $tmp->AC_NAME;
						else
						$summary['ac_name'] = 'Not Found';
						$ctname="counting_master_".strtolower($stcode);
						$trnd=DB::table('round_master')->where('st_code',$stcode)->where('pc_no', $pcno)->where('ac_no', $acno)->first();
						if(isset($trnd->scheduled_round))
						{
							$summary['total_rounds'] = $trnd->scheduled_round;
						}
						else
						{
							$summary['total_rounds']=0;
						}
						//print_r($summary['total_rounds']);die;
						$parties=DB::table($ctname)->select('party_id','candidate_id')->where('pc_no', $pcno)->where('ac_no', $acno)->get();
						$party_ids = array();
						foreach($parties as $party)
						{
							$tp=array();
							$tp['party']=$party->party_id;
							$tp['candidate']=$party->candidate_id;
							$party_ids[]=$tp;
						}
						//print_r(json_encode($rounds_data));
						$finalised_ac=DB::table($ctname)->where('pc_no', $pcno)->where('ac_no', $acno)->get();
						$mdata=array();
						//print_r($finalised_ac);
						foreach($finalised_ac as $facd)
						{
							$mrow=array();
							$mrow['nom_id']=$facd->nom_id;
							$mrow['candidate_id']=$facd->candidate_id;
							$mrow['candidate_name']=$facd->candidate_name;
							$mrow['party_id']=$facd->party_id;
							$mrow['party_abbre']=$facd->party_abbre;
							$mrow['party_name']=$facd->party_name;
							$mrow['postalballot_vote']=$facd->postalballot_vote;
							$mrow['total_vote']=$facd->total_vote;
							$mrow['complete_round']=$facd->complete_round;
							$mrow['finalized_round']=$facd->finalized_round;
							for($i=1;$i<=$summary['total_rounds'];$i++)
							{	
								$rname='round'.$i;
								$mrow[$rname]=$facd->$rname;
							}
							$mdata[]=$mrow;
						}
						//print_r($mdata);
						//print_r("\n\n\n");
						$mcnt=count($mdata);
						//print_r($mcnt);
						$summary['rounds_finalized'] = 0;
						$summary['rounds_pending'] = 0;
						$summary['message']="RoundWise counting details for ";
						$summary['roundwise'] = array();
						for($i=1;$i<=$summary['total_rounds'];$i++)
						{	
							$rounddata=array();
							$rounddata['round']=$i;
							$rname='round'.$i;
							$rounddata['leading_party_id']=0;
							$rounddata['leading_party_name']='';
							$rounddata['leading_votes']=0;
							$rounddata['leading_candidate_id']=0;
							$rounddata['leading_candidate_name']='';
							$rounddata['parties']=array();
							$lp=0;
							$lpn='';
							$lv=0;
							$lc='';
							$lci=0;
							
							foreach($party_ids as $pid)
							{
								$pdatat=array();
								for($j=0;$j<$mcnt;$j++)
								{
									if(($mdata[$j]['party_id']==$pid['party']) && ($mdata[$j]['candidate_id']==$pid['candidate']))
									{
										//party_name, party_code, party_logo_url, candidate_name, candidate_id,
										$pdatat['party_code']=$pid;
										$pdatat['party_abbr']=$mdata[$j]['party_abbre'];
										$pdatat['party_name']=$mdata[$j]['party_name'];
										$pdatat['candidate_name']=$mdata[$j]['candidate_name'];
										$pdatat['vote']=$mdata[$j][$rname];
										
										if($lp==0)
										{
											if($pid['party'] != 1180)
											{
												$lp=$pid['party'];
												$lpn=$mdata[$j]['party_name'];
												$lv=$pdatat['vote'];
												$lc=$pdatat['candidate_name'];
												$lci=$mdata[$j]['candidate_id'];
											}
										}
										elseif($lv < $pdatat['vote'])
										{
											if($pid['party'] != 1180)
											{
												$lp = $pid['party'];
												$lpn=$mdata[$j]['party_name'];
												$lv = $pdatat['vote'];
												$lc = $pdatat['candidate_name'];
												$lci=$mdata[$j]['candidate_id'];
											}
										}
									}
								}
								
								$rounddata['parties'][]=$pdatat;	
							}
							
							$rounddata['leading_party_id']=$lp;
							$rounddata['leading_party_name']=$lpn;
							$rounddata['leading_votes']=$lv;
							$rounddata['leading_candidate_id']=$lci;
							$rounddata['leading_candidate_name']=$lc;
							$summary['roundwise'][]=$rounddata;
						}
						//die;
						$summary['timestamp']=now()->toDateTimeString();
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
						//return response()->json($summary, $this->successStatus);
					}///EndSummary(success)
				}///EndEmptyToken
				return response()->json(Crypt::encryptString($summary), $this->successStatus);
				
				//return response()->json($summary, $this->successStatus);
			}
			catch (Exception $ex) 
			{
				return response()->json(Crypt::encryptString(json_encode(['success' => false,'error'=>'Internal Server Error'])), $this->intservererrorStatus); 
				//return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		
		public function pcCounting(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'user_id' => 'required','ac_token' => 'required','statecode' => 'required','pcno' => 'required'
				]);
				
				if($validator->fails()){
					return response()->json(Crypt::encryptString(json_encode(['success' => false,'message'=>'Please Check the Input Details']))); 
					//return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$actoken=trim($userInputs['ac_token']);
				$userid=trim($userInputs['user_id']);
				$stcode=trim($userInputs['statecode']);
				$pcno=trim($userInputs['pcno']);
				$summary=array();
				$summary['success'] = false;
				$summary['message'] = "Login Failed";
				
				if(!empty($userid) && !empty($actoken))
				{
					$udata=DB::table('officer_login')->where('id',$userid)->first();
					if(isset($udata->officername))
					{
						
						if($udata->accesstoken == $actoken)	
						{
							
							if($udata->officerlevel == 'ECI')
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							elseif(($udata->officerlevel == 'CEO') && ($udata->st_code == $stcode))
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							elseif(($udata->officerlevel == 'PC') && ($udata->st_code == $stcode) && ($udata->pc_no == $pcno))
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							else
							{
								$summary['message'] = "Access Level error. No clearance for this Login";
								return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
								//return response()->json($summary, $this->successStatus);
							}
						}
						else
						{
							$summary['message'] = "Access Token Mismatched";
							return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
							//return response()->json($summary, $this->successStatus);
						}
					}
					else
					{
						$summary['message'] = "User_id not found";
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
						//return response()->json($summary, $this->successStatus);
					}
					if($summary['success'])
					{
						$summary['st_code'] = $stcode;
						$summary['st_name'] = trim($this->commonModel->getstatebystatecode($stcode)->ST_NAME);
						$summary['pc_no'] = $pcno;
						$summary['pc_name'] = trim($this->commonModel->getpcbypcno($stcode,$pcno)->PC_NAME);
						$pcdata=DB::table('counting_pcmaster')->select('nom_id','candidate_id','candidate_name','party_id','party_abbre','party_name','st_code','pc_no','evm_vote','postal_vote','migrate_votes','total_vote','finalize','rejectedvote','postaltotalvote','tended_votes')->where('pc_no', $pcno)->where('st_code', $stcode)->get();
						$summary['partyWise'] = array();
						foreach($pcdata as $facd)
						{
							$pdata=array();
							$pdata['party_code']=$facd->party_id;
							$pdata['party_name']=$facd->party_name;
							$pdata['party_abbr']=$facd->party_abbre;
							$pdata['candidate_id']=$facd->candidate_id;
							$pdata['candidate_name']=$facd->candidate_name;
							$pdata['total_evm']=$facd->evm_vote;
							$pdata['total_postal']=$facd->postal_vote;
							$pdata['total']=$facd->total_vote;
							$pdata['migrant']=$facd->migrate_votes;
							$pdata['rejected']=$facd->rejectedvote;
							$pdata['tainted']=$facd->tended_votes;
							$pdata['finalized']=$facd->finalize;
							$summary['partyWise'][]=$pdata;
						}
						$summary['message']="Party-Wise counting details for PC - ".trim($summary['pc_name'])." of ".trim($summary['st_name']);
						$summary['timestamp']=now()->toDateTimeString();
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
						//return response()->json($summary, $this->successStatus);
					}///EndSummary(success)
				}///EndEmptyToken
				
				//return response()->json($summary, $this->successStatus);
				return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus);
			}
			catch (Exception $ex) 
			{
				return response()->json(Crypt::encryptString(json_encode(['success' => false,'error'=>'Internal Server Error'])), $this->intservererrorStatus); 
				//return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		
		public function stateCounting(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'user_id' => 'required','ac_token' => 'required','statecode' => 'required'
				]);
				
				if($validator->fails()){
					return response()->json(Crypt::encryptString(json_encode(['success' => false,'message'=>'Please Check the Input Details']))); 
					//return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$actoken=trim($userInputs['ac_token']);
				$userid=trim($userInputs['user_id']);
				$stcode=trim($userInputs['statecode']);
				
				$summary=array();
				$summary['success'] = false;
				$summary['message'] = "Login Failed";
				
				if(!empty($userid) && !empty($actoken))
				{
					$udata=DB::table('officer_login')->where('id',$userid)->first();
					if(isset($udata->officername))
					{
						
						if($udata->accesstoken == $actoken)	
						{
							
							if($udata->officerlevel == 'ECI')
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							elseif(($udata->officerlevel == 'CEO') && ($udata->st_code == $stcode))
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							else
							{
								$summary['message'] = "Access Level error. No clearance for this Login";
								return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
								//return response()->json($summary, $this->successStatus);
							}
						}
						else
						{
							$summary['message'] = "Access Token Mismatched";
							return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
							//return response()->json($summary, $this->successStatus);
						}
					}
					else
					{
						$summary['message'] = "User_id not found";
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
						//return response()->json($summary, $this->successStatus);
					}
					if($summary['success'])
					{
						$summary['st_code'] = $stcode;
						$summary['st_name'] = $this->commonModel->getstatebystatecode($stcode)->ST_NAME;
						$ctname="counting_master_".strtolower($stcode);
						$finalised_ac=DB::table($ctname)->select('pc_no','ac_no','postalballot_vote','total_vote','complete_round')->get();
						$mdata=array();
						foreach($finalised_ac as $facd)
						{
							$mrow=array();
							$mrow['pcno']=$facd->pc_no;
							$mrow['acno']=$facd->ac_no;
							$mrow['postalballot_vote']=$facd->postalballot_vote;
							$mrow['total_vote']=$facd->total_vote;
							$mrow['complete_round']=$facd->complete_round;
							
							
							$mdata[]=$mrow;
						}
						//print_r($mdata);
						//print_r("\n\nAll PCs \n\n");
						$pcnos = array();
						foreach($mdata as $mrow)
						{
							$pcnos[]=$mrow['pcno'];
						}
						$pcnos = array_unique($pcnos);
						$tdata=array();
						foreach($pcnos as $tpc)
						$tdata[]=$tpc;
						$pcnos=$tdata;
						//print_r($pcnos);
						//print_r("\n\nStatting PC Loop \n\n");
						$pcnt=count($pcnos)-1;
						$mcnt=count($mdata)-1;
						$summary['message']="State-Wise counting details for State - ".trim($summary['st_name']);
						$summary['pcResults'] = array();
						for($j=0;$j<=$pcnt;$j++)
						{
							//print_r("\nPC VAlue ".$pcnos[$j]." Index $j \n");
							$acnos = array();
							foreach($mdata as $mrow)
							{
								if($mrow['pcno']==$pcnos[$j])
								$acnos[]=$mrow['acno'];
							}
							$acnos = array_unique($acnos);
							$tdata=array();
							foreach($acnos as $tac)
							$tdata[]=$tac;
							$acnos=$tdata;
							//print_r("\nAll AC \n");
							//print_r($acnos);
							$acnt=count($acnos)-1;
							$pcwise=array();
							$pcwise['pc_no']=$pcnos[$j];
							$pcwise['pc_name']=$this->commonModel->getpcbypcno($stcode,$pcnos[$j])->PC_NAME;
							$pcwise['acResults'] = array();
							//print_r("\n Starting AC Loop\n");
							for($i=0;$i<=$acnt;$i++)
							{	
								//print_r("\nAC VAlue ".$acnos[$i]." Index $i \n");
								$adata=array();
								$adata['total']=0;
								$adata['completed']=0;
								$inst=0;
								foreach($mdata as $mrow)
								{
									if(($mrow['pcno']==$pcnos[$j]) && ($mrow['acno']==$acnos[$i]))
									{
										if($inst==0)
										{
											$adata['ac_no']=$mrow['acno'];
											$adata['ac_name']=$this->commonModel->getacbyacno($stcode,$mrow['acno'])->AC_NAME;
											$adata['total_evm']=$mrow['total_vote'];
											$adata['total_postal']=$mrow['postalballot_vote'];
											$adata['total']=$adata['total_evm']+$adata['total_postal'];
											$adata['completed']=$mrow['complete_round'];
										}
										else
										{
											$adata['total_evm']+=$mrow['total_vote'];
											$adata['total_postal']+=$mrow['postalballot_vote'];
											$adata['total']=$adata['total_evm']+$adata['total_postal'];
											$adata['completed']=$mrow['complete_round'];
										}
										$inst++;
									}
								}
								$fdata=array();
								$fdata['ac_no']=$adata['ac_no'];
								$fdata['ac_name']=$adata['ac_name'];
								$fdata['status']=0;
								if(($adata['total']==0) && ($adata['completed']==0))
								$fdata['status']=1;
								else
								$fdata['status']=2;
								$afdata=DB::table('counting_finalized_ac')->select('pc_no','ac_no','finalized_ac')->where('ac_no',$adata['ac_no'])->where('pc_no',$pcnos[$j])->first();
								if($afdata->finalized_ac > 0)
								$fdata['status']=3;
								$pcwise['acResults'][]=$fdata;
							}
							$summary['pcResults'][]=$pcwise;
						}
						$summary['timestamp']=now()->toDateTimeString();
						
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
						//return response()->json($summary, $this->successStatus);
					}///EndSummary(success)
				}///EndEmptyToken
				
				//return response()->json($summary, $this->successStatus);
				return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus);
			}
			catch (Exception $ex) 
			{
				return response()->json(Crypt::encryptString(json_encode(['success' => false,'error'=>'Internal Server Error'])), $this->intservererrorStatus); 
				//return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		
		public function stateSpeed(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'user_id' => 'required','ac_token' => 'required','statecode' => 'required'
				]);
				
				if($validator->fails()){
					return response()->json(Crypt::encryptString(json_encode(['success' => false,'message'=>'Please Check the Input Details']))); 
					//return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$actoken=trim($userInputs['ac_token']);
				$userid=trim($userInputs['user_id']);
				$stcode=trim($userInputs['statecode']);
				
				$summary=array();
				$summary['success'] = false;
				$summary['message'] = "Login Failed";
				
				if(!empty($userid) && !empty($actoken))
				{
					$udata=DB::table('officer_login')->where('id',$userid)->first();
					if(isset($udata->officername))
					{
						
						if($udata->accesstoken == $actoken)	
						{
							
							if($udata->officerlevel == 'ECI')
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							elseif(($udata->officerlevel == 'CEO') && ($udata->st_code == $stcode))
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							else
							{
								$summary['message'] = "Access Level error. No clearance for this Login";
								return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
								//return response()->json($summary, $this->successStatus);
							}
						}
						else
						{
							$summary['message'] = "Access Token Mismatched";
							return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
							//return response()->json($summary, $this->successStatus);
							
						}
					}
					else
					{
						$summary['message'] = "User_id not found";
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
						//return response()->json($summary, $this->successStatus);
					}
					if($summary['success'])
					{
						$summary['st_code'] = $stcode;
						$summary['st_name'] = trim($this->commonModel->getstatebystatecode($stcode)->ST_NAME);
						$ctname="counting_master_".strtolower($stcode);
						$cdata=DB::table($ctname)->select('pc_no','ac_no','complete_round')->groupBy('ac_no')->orderby('pc_no')->get();
						$mdata=array();
						foreach($cdata as $facd)
						{
							$mdata[$facd->pc_no][$facd->ac_no]['complete_round']=$facd->complete_round;
							$mdata[$facd->pc_no][$facd->ac_no]['total_round']= 0;
							$mdata[$facd->pc_no][$facd->ac_no]['completion']= 0;
						}
						//print_r($mdata);
						//print_r("\n\nAdding Total Rounds\n\n\n");
						$rdatam=DB::table('round_master')->select('pc_no','ac_no','scheduled_round')->where('st_code',strtoupper($stcode))->get();
						
						foreach($rdatam as $facd)
						{
							$mdata[$facd->pc_no][$facd->ac_no]['total_round']=$facd->scheduled_round;
							if($mdata[$facd->pc_no][$facd->ac_no]['complete_round'] > 0)
							$mdata[$facd->pc_no][$facd->ac_no]['completion'] = number_format(($mdata[$facd->pc_no][$facd->ac_no]['complete_round'] * 100)/ $facd->scheduled_round,2);
						}
						//print_r($mdata);die;
						//print_r("\n\nAll PCs \n\n");
						
						
						$summary['message']="State-Wise counting percentage for State - ".trim($summary['st_name']);
						$summary['pcwise'] = array();
						$strounds=0;
						$scrounds=0;
						foreach($mdata as $pc=>$pcdata)
						{
							$ptrounds=0;
							$pcrounds=0;
							$pdata=array();
							$pdata['pc_no']=$pc;
							$pdata['pc_name']=trim($this->commonModel->getpcbypcno($stcode,$pc)->PC_NAME);
							$pdata['acwise']=array();
							foreach($pcdata as $ac=>$acdata)
							{
								$adata=array();
								$adata['ac_no']=$ac;
								$adata['ac_name']=trim($this->commonModel->getacbyacno($stcode,$ac)->AC_NAME);
								$adata['total_rounds']= $mdata[$pc][$ac]['total_round'];
								$adata['completed_rounds']= $mdata[$pc][$ac]['complete_round'];
								$adata['rounds_percent_completed']=$mdata[$pc][$ac]['completion'];
								$ptrounds += $mdata[$pc][$ac]['total_round'];
								$pcrounds += $mdata[$pc][$ac]['complete_round'];
								$strounds += $mdata[$pc][$ac]['total_round'];
								$scrounds += $mdata[$pc][$ac]['complete_round'];
								$pdata['acwise'][]=$adata;
							}
							$pdata['total_rounds']= $ptrounds;
							$pdata['completed_rounds']= $pcrounds;
							if($pcrounds > 0)
							$pdata['rounds_percent_completed']=number_format(($pcrounds * 100) / $ptrounds , 2);
							else
							$pdata['rounds_percent_completed']=0;
							$summary['pcwise'][]= $pdata;
						}
						$summary['total_rounds']= $strounds;
						$summary['completed_rounds']= $scrounds;
						if($scrounds > 0 )
						$summary['rounds_percent_completed']=number_format(($scrounds * 100) / $strounds , 2);
						else
						$summary['rounds_percent_completed']=0;
						//die;
						$summary['timestamp']=now()->toDateTimeString();
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus);
						
						//return response()->json($summary, $this->successStatus);
					}///EndSummary(success)
				}///EndEmptyToken
				
				// response()->json($summary, $this->successStatus);
				return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus);
			}
			catch (Exception $ex) 
			{
				return response()->json(Crypt::encryptString(json_encode(['success' => false,'error'=>'Internal Server Error'])), $this->intservererrorStatus); 
				//return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		public function PCleading(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'user_id' => 'required','ac_token' => 'required','statecode' => 'required','pcno' => 'required']);
				
				if($validator->fails()){
					return response()->json(Crypt::encryptString(json_encode(['success' => false,'message'=>'Please Check the Input Details']))); 
					//return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
				} 
				
				$userInputs = $request->all();
				$actoken=trim($userInputs['ac_token']);
				$userid=trim($userInputs['user_id']);
				$stcode=trim($userInputs['statecode']);
				$pcno=trim($userInputs['pcno']);
				
				$summary=array();
				$summary['success'] = false;
				$summary['message'] = "Login Failed";
				
				if(!empty($userid) && !empty($actoken))
				{
					$udata=DB::table('officer_login')->where('id',$userid)->first();
					if(isset($udata->officername))
					{
						
						if($udata->accesstoken == $actoken)	
						{
							
							if($udata->officerlevel == 'ECI')
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							elseif(($udata->officerlevel == 'CEO') && ($udata->st_code == $stcode))
							{
								$summary['success'] = true;
								$summary['message'] = "";
							}
							else
							{
								$summary['message'] = "Access Level error. No clearance for this Login";
								return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
								//return response()->json($summary, $this->successStatus);
							}
						}
						else
						{
							$summary['message'] = "Access Token Mismatched";
							return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
							//return response()->json($summary, $this->successStatus);
						}
					}
					else
					{
						$summary['message'] = "User_id not found";
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
						//return response()->json($summary, $this->successStatus);
					}
					if($summary['success'])
					{
						$summary['message'] = "Winning Leading Margin of PC # $pcno of $stcode";
						$ldata=DB::table('winning_leading_candidate')->select('leading_id','st_code','st_name','pc_no','pc_name','candidate_id','nomination_id','lead_cand_name','lead_cand_partyid','lead_cand_party','trail_candidate_id','trail_nomination_id','trail_cand_name','trail_cand_partyid','trail_cand_party','lead_total_vote','trail_total_vote','margin')->where('st_code',$stcode)->where('pc_no',$pcno)->first();
						$summary['st_code'] = $stcode;
						$summary['st_name'] = $ldata->st_name;
						$summary['pc_no'] = $stcode;
						$summary['pc_name'] = $ldata->pc_name;
						$summary['leading']['party_name']=$ldata->lead_cand_party;
						$summary['leading']['party_no']=$ldata->lead_cand_partyid;
						$summary['leading']['candidate_no']=$ldata->candidate_id;
						$summary['leading']['candidate_name']=$ldata->lead_cand_name;
						$summary['leading']['total']=$ldata->lead_total_vote;
						$summary['trailing']['party_name']=$ldata->trail_cand_party;
						$summary['trailing']['party_no']=$ldata->trail_cand_partyid;
						$summary['trailing']['candidate_no']=$ldata->trail_candidate_id;
						$summary['trailing']['candidate_name']=$ldata->trail_cand_name;
						$summary['trailing']['total']=$ldata->trail_total_vote;
						$summary['margin']=$ldata->margin;
						$summary['timestamp']=now()->toDateTimeString();
						
						return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus); 
						//return response()->json($summary, $this->successStatus);
					}///EndSummary(success)
				}///EndEmptyToken
				
				//return response()->json($summary, $this->successStatus);
				return response()->json(Crypt::encryptString(json_encode($summary)), $this->successStatus);
			}
			catch (Exception $ex) 
			{
				return response()->json(Crypt::encryptString(json_encode(['success' => false,'error'=>'Internal Server Error'])), $this->intservererrorStatus); 
				//return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
	}																												