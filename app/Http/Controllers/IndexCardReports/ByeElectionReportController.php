<?php

namespace App\Http\Controllers\IndexCardReports;

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
		use App\models\Admin\{ElectionModel, ByeElectionModel};
		use Config;
		use App;
		use \PDF;
		use MPDF;
		use App\commonModel;
		use App\adminmodel\CandidateModel;
		use App\adminmodel\PartyMaster;
		use App\adminmodel\CandidateNomination;
		use App\Helpers\SmsgatewayHelper;
		use App\Classes\xssClean;
		use App\adminmodel\SymbolMaster;
		use Illuminate\Support\Facades\URL;
		use Excel;
		
		ini_set("memory_limit","48000M");
        set_time_limit('6000');
        ini_set("pcre.backtrack_limit", "5000000000");

class ByeElectionReportController extends Controller
{
	
	public $base          = '';
	public $folder        = '';
	public $action        = '';
	public $current_page  = '';
	public $ac_no         = 0;
	public $st_code       = 0;
	public $view_path     = "IndexCardReports.ByeElectionIndexCard";
	public $bye_access = false;
	
	
	public function __construct(){
    $role_id = 0;
    $this->xssClean = new xssClean;
	$this->commonModel = new commonModel();
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
        $role_id = Auth::user()->role_id;
         if($role_id == '27'){
          $this->base         = 'eci-index';
          $this->action       = 'eci-index/bye-election-verify-report/post';
          $this->current_page = 'eci-index/bye-election-verify-report';
          $this->bye_access   = 'eci-index/bye-election-verify-report/post';
          $this->view_path    = '';
        } else{
          $this->base         = 'eci';
          $this->action       = 'eci/bye-election-verify-report/post';
          $this->current_page = 'eci/bye-election-verify-report';
          $this->bye_access   = 'eci/bye-election-verify-report/post';
          $this->view_path    = '';
        }

        if(in_array($role_id,['7','27'])){
          $this->bye_access = true;
        }

        return $next($request);
    });
  }

	// Bye Election Index-Card Report Start
	

	public function indexcardreportlist(Request $request){

        $data                   = [];
	  $filter                 = [];
	  $data['pc_no']          = NULL;
	  $data['st_code']        = NULL;
	  $data['election_id']    = NULL;
	  $data['custom_errors']  = [];
      if($request->has('election_id')){
        $data['election_id']       = $request->election_id;
        $filter['election_id']     = $data['election_id'];
      }

      if($request->has('pc_no')){
        $data['pc_no']       = $request->pc_no;
      } 

      if($request->has('st_code')){
        $data['st_code']       = $request->st_code;
      }     

      if(\Auth::user()->role_id == '18'){
        $data['pc_no']          = $this->pc_no;
        $data['st_code']        = $this->st_code;
        $filter['pc_no']        = $data['pc_no'];
        $filter['st_code']      = $data['st_code'];
      }

      if(\Auth::user()->role_id == '4'){
        $data['st_code']        = $this->st_code;
        $filter['st_code']      = $data['st_code'];
      }
      
      $data['action']         = url($this->action);
      $data['current_page']   = url($this->current_page);

      $data['heading_title']  = 'Index Card';
      $data['filter_buttons'] = [];

      //years
      $data['elections']      = [];
      $elections              = ElectionModel::get_current_elections();
      foreach ($elections as $key => $result) {
        $data['elections'][] = [
           'election_id'      => $result['ELECTION_ID'],
           'election_type'    => $result['ELECTION_TYPE'].'-'.$result['YEAR'],
        ];
      }

      $data['states'] = [];
	  
      $states = ByeElectionModel::get_states_index_bye();
	  
	  //dd($states);
	  
      foreach ($states as $key => $iterate_state) {
         $data['states'][] = [
           'st_code' => $iterate_state->ST_CODE,
           'st_name' => $iterate_state->ST_NAME,
         ];
      }

      $acs          = [];
      foreach (ByeElectionModel::get_pcs_bye(['st_code' => $data['st_code']]) as $ac_result){
		  if(\Auth::user()->role_id == '18'){    
			if($ac_result['pc_no']==$this->pc_no){
				$acs[] = [
				  'pc_no'   => $ac_result['pc_no'],
				  'pc_name' => $ac_result['pc_name']
				];
			}		  
		  }else{
			  $acs[] = [
				  'pc_no'   => $ac_result['pc_no'],
				  'pc_name' => $ac_result['pc_name']
				];
		  }
      }
	  
	  //echo '<pre>'; print_r($acs); die;
	  	  
      $data['acs']      = $acs;
      $data['results']  = [];

      $results = ByeElectionModel::get_list(['pc_no' => $data['pc_no'], 'st_code' => $data['st_code']]);
     
	 
	  //dd($results);
	 
	 
       foreach ($results as $res_iterate) {
		  
		//dd($res_iterate->pc_no);

		  
        $data['results'][] = [
            'st_code'           => @$res_iterate->st_code,
            'pc_no'             => @$res_iterate->pc_no,
            'bye_access' 		=> url($this->bye_access),
            'st_name'        	=> @$res_iterate->st_name,
            'pc_name'        	=> @$res_iterate->pc_name,
        ];

      } 
	  
      $data['user_data']  =   Auth::user();

      return view('IndexCardReports.ByeElectionIndexCard.bye_indexcard_list',$data);
  }
	
	
	public function byeverifyreportcheckbox(Request $request){
		 

         //dd($request);
		 $st_code = $request->st_code;
		 $pc_no = $request->pc_no;
		 $is_verified = $request->is_verified;
		 $date = date('Y-m-d H:i:s');
		 
		 
		 $data = DB::table('bye_election_report_verify')->select('st_code','pc_no')
		 ->where('st_code',$st_code)
		 ->where('pc_no', $pc_no)
		 ->get();
		 
		 
		 //echo "<pre>"; print_r($data); die;
		 
		 $insertData = [
          'is_verified' => $is_verified,
          'verifiat_date' => $date,
          'verified_by' => Auth::user()->officername,
          'pc_no' => $pc_no,
		  'st_code' => $st_code
		];
		
		$updateData = [
          'is_verified' => $is_verified,
		  'verifiat_date' => $date,
          'verified_by' => Auth::user()->officername
		];
	  
	  if(count($data) > 0){
		  $query = DB::table('bye_election_report_verify')
                ->where('pc_no', $pc_no)
				->where('st_code', $st_code)
                ->update($updateData);
				
		 
				
	  }else{
		  $query = DB::table('bye_election_report_verify')
               
                ->insert($insertData);
				
	
				
			
	  }
	  
	   if($query){
          $msg = 'Success';
          $queryinsert = DB::table('bye_election_report_verify_logs')->insert($insertData);
        }else{
          $msg = 'Fail';
        }
		
		 return response()->json(array('msg'=> $msg), 200);
	 
    }
	
}