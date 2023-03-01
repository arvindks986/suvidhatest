<?php 
namespace App\Http\Controllers\Admin\RoundWiseReport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Validator;
use Config;
use \PDF,Excel;
use App\commonModel;
use App\models\Admin\ReportModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;

class RoundWiseReportController extends Controller {
  
    public $view_path     = "admin.countingReport.scheduleReport";
    public $aro           = "aro";
    public $ro            = "ro";
    public $eci           = "eci";
    public $ceo           = "ceo";
    
    public function __construct(){
		$this->commonModel  = new commonModel();
        $this->report_model = new ReportModel();
        if(!Auth::user()){
            return redirect('/officer-login');
        }
    }
  
	public function index(){ 
	 $data=array();
	 $data['user_data']  =   Auth::user();		
	  $user_type=null;
	  $sIdl='NotSet';
	  $dIdl=0;
	  $wIdl=0;
	  $disabled='';	
	  $state_id='';	
	  $pc_no='';	
	  $acData='';
	  $ac_no='';	
	  $disabledAc='';	

	  if(isset($data['user_data']['role_id']) && ($data['user_data']['role_id']==7 ))
	  {
		$disabled='';
		$user_type='ECI';
		$state_id='';
        $pc_no='';	
		$ac_no='';
	  }
	
	  if(isset($data['user_data']['role_id']) && ($data['user_data']['role_id']==4 ))
	  { 
		$disabled='disabled';
		$user_type='CEO'; 
		$state_id=$data['user_data']['st_code'];	
		$pc_no=''; 
		$ac_no='';
	  }
	  
	  if(isset($data['user_data']['role_id']) && ($data['user_data']['role_id']==18 ))
	  {
		$disabled='disabled';
		$user_type='ARO'; 
		$state_id=$data['user_data']['st_code'];
		$pc_no=$data['user_data']['pc_no']; 
		$ac_no=''; 
	  } 

	  if(isset($data['user_data']['role_id']) && ($data['user_data']['role_id']==20 ))
	  {	
		$disabled='disabled';
		$user_type='ARO';
		$state_id=$data['user_data']['st_code'];
		$pc_no=$data['user_data']['pc_no']; 
		$ac_no=$data['user_data']['ac_no']; 
	  }
	
	 $state = DB::table('m_state')
	 ->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
        ])->where('m_election_details.CONST_TYPE','PC')
	 ->where('election_status','1')
	 ->orderBy('m_state.ST_NAME', 'ASC')->get();
	 
		$get_Pc_data = DB::table("m_pc")
		->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_pc.PC_NO'],
        ])->where('election_status','1')
		->select('m_pc.PC_NO','PC_NAME')
		->where('m_pc.ST_CODE', '=', $data['user_data']['st_code'])
		->orderBy('m_pc.PC_NO', 'ASC')
		->get(); 
		if(isset($get_Pc_data) && ((count($get_Pc_data)) > 0 ))	{
			$get_Pc_data = $get_Pc_data;
		} else {
			$get_Pc_data = array();
		}	
		
		$acData = DB::table("m_ac")
		->select('AC_NO','AC_NAME')
		->where('ST_CODE', '=', $data['user_data']['st_code'])
		->where('PC_NO', '=', $data['user_data']['pc_no'])
		->orderBy('AC_NO', 'ASC')
		->get(); 

		if(isset($acData) && ((count($acData)) > 0 ))	{
			$acData = $acData;
		} else {
			$acData = array();
		}
   
		$condidateData = array();
        if(isset($data['user_data']['st_code']) && ((!empty($data['user_data']['st_code']))) )	{
		$tablepr=strtolower($data['user_data']['st_code']);
		$condidateData = DB::table("counting_master_$tablepr")
		->select('nom_id','candidate_id','candidate_name','party_abbre')
		->where('pc_no', '=',  $data['user_data']['pc_no'])
		->orderBy('id', 'ASC')
		->groupBy('candidate_id')
		->get();
		if(isset($condidateData) && ((count($condidateData)) > 0 ))	{
			$condidateData = $condidateData;
		} else {
			$condidateData = array();
		} 
	 }	
	 return view('admin.countingReport.round-wise-report', $data, compact('data', 'state', 'disabled', 'user_type', 'state_id', 'pc_no', 'ac_no', 'get_Pc_data', 'acData', 'condidateData', 'disabledAc'));
  }
  
	public function getMatchedPcByStateId(Request $request){
		
		$input=$request->All();
		$st_code=$input['st_code'];
		$get_pc_data = DB::table("m_pc")
		->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_pc.PC_NO'],
        ])->where('election_status','1')
		->select('m_pc.PC_NO','PC_NAME')
		->where('m_pc.ST_CODE', '=', $st_code)
		->orderBy('m_pc.PC_NO', 'ASC')
		->get(); 
		
		$pcList = array();
		$arra = array();	
		if(count($get_pc_data)>0)
	    {
				foreach ($get_pc_data as $each_record) {
				  $pcList[$each_record->PC_NO] = $each_record->PC_NAME;
				}
	    } else {
			return '0';
		}
		if($pcList==0)
		{
			 return 0;
		}
		else
		{
			foreach($pcList as $dcode => $dval) { 
				 $arra['PC_NO'][]=$dcode;
				 $arra['PC_NAME'][]=$dval;
			}
		}	
		return json_encode( $arra );	
	}
	public function getMatchedAc(Request $request){
	  
		$input=$request->All();
		$st_code=$input['st_code'];
		$pcId=$input['pcId'];
		
		$get_ac_data = DB::table("m_ac")
		->select('AC_NO','AC_NAME')
		->where('ST_CODE', '=', $st_code)
		->where('PC_NO', '=', $pcId)
		->orderBy('AC_NO', 'ASC')
		->get(); 
		
		$acList = array();
		$arraAc = array();	
		if(count($get_ac_data)>0)
	    {
				foreach ($get_ac_data as $each_record) {
				  $acList[$each_record->AC_NO] = $each_record->AC_NAME;
				}
	    } else {
			return '0';
		}
		if($acList==0)
		{
			 return 0;
		}
		else
		{
			foreach($acList as $dcode => $dval) { 
				 $arraAc['AC_NO'][]=$dcode;
				 $arraAc['AC_NAME'][]=$dval;
			}
		}	
		return json_encode( $arraAc );	
	}
	
	public function getCondidfateListpkpk(Request $request){
	  
		$input=$request->All();
		$st_code=implode(',',$input['stateok']);
		$tablepr=strtolower($st_code);
		$pcId=$input['pcId'];
		$condidate = DB::table("counting_master_$tablepr")
		->select('nom_id','candidate_id','candidate_name','party_abbre')
		->where('pc_no', '=', $pcId)
		->orderBy('id', 'ASC')
		->groupBy('candidate_id')
		->get(); 
		
		$cList = array();
		$arraAc = array();	
		if(count($condidate)>0)
	    {
				foreach ($condidate as $each_record) {
				  $cList[$each_record->candidate_id] = $each_record->nom_id.'-'.$each_record->candidate_name.'('.$each_record->party_abbre.')';
				}
	    } else {
			return '0';
		}
		if($cList==0)
		{
			 return 0;
		}
		else
		{
			foreach($cList as $dcode => $dval) { 
				 $arraAc['candidate_id'][]=$dcode;
				 $arraAc['cParty'][]=$dval;
			}
		}	
		return json_encode( $arraAc );	
	}

	public function getCompleteResult(Request $request){
	  
		$input=$request->All();
		//echo "<pre>"; print_r($input); die;
		$st_code=implode(',',$input['st_code']);
		$pc=implode(',',$input['pc']);
		$ac=implode(',',$input['ac']);
		$condidate=implode(',',$input['condidate']);
		$LoginData['user_data']  =   Auth::user();
		$cnd=''; $candidate_name=''; $AC_NAME='';
		$user='';
		if(!empty($input['user_type'])){
		$user =  $input['user_type'];
		}

		$tablepr=strtolower($st_code);

		if($condidate=='000'){
			$cnd =	"";
			$candidate_name='All Candidate';
		}else{
			$cnd =	"and candidate_id in " .'('.$condidate .')';
			$candidate_name = DB::table("counting_master_$tablepr")->select('candidate_name')->where('candidate_id', $condidate)->first()->candidate_name;
		}

		$cndAc='';
		if($ac=='000'){
			$cndAc =	"";
			$AC_NAME="All AC";
		}else{
			$cndAc =	"and AC_NO in " .'('.$ac .')';
			$AC_NAME = DB::table('m_ac')->select('AC_NAME')->where('ST_CODE', $st_code)->where('AC_NO', '=', $ac)->first()->AC_NAME; 
		}

	 	$query = "select * from counting_master_$tablepr where pc_no='".$pc."' $cndAc  $cnd"; 
		$result = DB::select($query);
		
		//echo $result[0]->pc_no; die;


		$pc_name=''; $state_name='';
		$state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $st_code)->first()->ST_NAME;
		if(isset($result[0]->pc_no)){
			$pc_name = DB::table('m_pc')->select('PC_NAME')->where('ST_CODE', $st_code)->where('PC_NO', '=', $result[0]->pc_no)->first()->PC_NAME;
		}	
		
		if(!isset($input['download'])){
			return  view('admin.countingReport.round-wise-report-ajax-result', compact('result', 'st_code', 'state_name', 'pc', 'ac', 'pc_name', 'AC_NAME', 'candidate_name', 'user'));
		} else {
			$pdf = PDF::loadView('admin.countingReport.round-wise-report-pdf', compact('result', 'st_code', 'LoginData', 'state_name', 'pc', 'ac', 'pc_name', 'AC_NAME', 'candidate_name', 'user'));
			return $pdf->download('Round-Wise-Voting-Report.pdf');	
		}
	}
	
	public function getAc($state, $ac){
	$acName = DB::table("m_ac")
	->select('AC_NAME')
	->where('ST_CODE', '=', $state)
	->where('AC_NO', '=',  $ac)
	->get(); 
	if(count( $acName ) > 0 ){
		return $acName[0]->AC_NAME;
	} else {
		return 'NA';
	}		
	}

	public function getMaxRound($st, $pc, $ac){
		//return $st .'-'. $pc .'-'. $ac;
		if( $ac =='000'){
				$query = "select max(scheduled_round) as scheduled_round from round_master where st_code='".$st."' and pc_no='".$pc."'"; 
				$result = DB::select($query);
				if(count( $result ) > 0 ){
					return $result[0]->scheduled_round;
				} else {
					return 'NA';
				}
		} else {
				$query = "select scheduled_round from round_master where st_code='".$st."' and pc_no='".$pc."' and ac_no='".$ac."'"; 
				$result = DB::select($query);
				if(count( $result ) > 0 ){
					return $result[0]->scheduled_round;
				} else {
					return 'NA';
				}
		}	
	}

		
	function csvDownload(REQUEST $request) {
	    $input=$request->All();
		
		$st_code=implode(',',$input['st_code']);
		$pc=implode(',',$input['pc']);
		$ac=implode(',',$input['ac']);
		$condidate=implode(',',$input['condidate']);
		$LoginData['user_data']  =   Auth::user();
		$cnd='';
		if($condidate=='000'){
			$cnd =	"";
		}else{
			$cnd =	"and candidate_id in " .'('.$condidate .')';
		}

		$cndAc='';
		if($ac=='000'){
			$cndAc =	"";
		}else{
			$cndAc =	"and AC_NO in " .'('.$ac .')';
		}	
		$tablepr=strtolower($st_code);
		$query = "select * from counting_master_$tablepr where pc_no='".$pc."' $cndAc  $cnd"; 
		$result = DB::select($query);
		//echo "<pre>"; print_r($result); die;
        

		// echo $st_code.'-'.$pc.'-'.$ac; die;
		$maxRound=0;	
		$maxRound = $this->getMaxRound($st_code, $pc, $ac);
		

		 $content=''; $rem='sdbjsdfbsjdfhfsd';
       if (count($result) > 0 ) {
			 $file = "Round-Wise-EVM-Votes.csv";
			 header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			 header('Content-Disposition: attachment; filename='.$file);
			
			 //$content .= "test1,test1,test3\n";
			// $content .= "testtest,ttesttest2,testtest3\n";
			
			$content = "Sr. No,State, PC No.,PC,AC,AC No.,Party,Condidate,";
			$countData=array();
			
			
			$b = 0;
			 
			 $dataok=''; $mdataok=''; $rnd=0;
			 for($m=1; $m<=$maxRound; $m++){  $rnd++;
				$dataok .= " R$m,"; 
			 }  
			$mdataok=substr($dataok , 0, -1);
			if($rnd==0){
			$mdataok='Round';
			}
			
			$content.= "$mdataok, Total EVM Votes\n";
			$rem=$b;

               $i = 1;  $ok=array(); $final=''; $first='';
               foreach ($result as $k => $dval) { 
				  $mDatadata = (array)$dval;

				  $state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $st_code)->first()->ST_NAME;
                  $pc_name = DB::table('m_pc')->select('PC_NAME')->where('ST_CODE', $st_code)->where('PC_NO', '=', $pc)->first()->PC_NAME;
				  $ac_name = $this->getAc($st_code, $mDatadata['ac_no']);

                 
					$cndname=str_replace(",", '-', $state_name);
					if( $cndname =='') {  $cndname = 'NA'; }	
					$pc_name=str_replace(",", '-', $pc_name);	
					if( $pc_name =='') {  $pc_name = 'NA'; }	
					$ac_name=str_replace(",", '-', $ac_name);	
					if( $ac_name =='') {  $ac_name = 'NA'; }
					$party_name=str_replace(",", '-', $mDatadata['party_name']);	
					if( $party_name =='') {  $party_name = 'NA'; }	
					$party_abbre=str_replace(",", '-', $mDatadata['party_abbre']);	
					if( $party_abbre =='') {  $party_abbre = 'NA'; }	
					$candidate_name=str_replace(",", '-', $mDatadata['candidate_name']);	
					if( $candidate_name =='') {  $candidate_name = 'NA'; }
					$ac_no=str_replace(",", '-', $mDatadata['ac_no']);	
					if( $ac_no =='') {  $ac_no = '0'; }	

					$first.= "$i,$cndname, $pc,$pc_name($pc),$ac_name($ac_no),$ac_no,$party_name($party_abbre),$candidate_name,"; 


				   
				   
				   
				   
				
						$j=0;  $countData=array(); $mdatat=array();
						for($k=1; $k<=$maxRound; $k++){     	
							$dataok = 'round'.$k;
								$j=$j+1; 
								  array_push($countData, $j);  
						}						
						
						
						
								$total_votes=0; $p=0;  $ffirst='';$second=''; $fseond=$fokData=''; $third=''; $isRound=0;
								for($k=1; $k<=$maxRound; $k++){ 
		
								 $dataok = 'round'.$k;
											 $p++; $isRound++;
														 $second .= $mDatadata[$dataok].',';	
													     $total_votes=$total_votes + $mDatadata[$dataok];
														 $third=$total_votes."\n";		
												
								}
								$remain = $rem-$p;  $fourth='';
								 if($remain==0){
								  $fourth="\n";		
								 }
								if($total_votes==0){
								  $third="\n";		
								}
								
								 


								 $first.= $second.$third.$fourth; 
					
                   $i++; 

			   	
               } 
			   $finalData='';	
			   $finalData=$content.$first;	
			   echo $finalData;
                  
				

           }
       }
   

}  