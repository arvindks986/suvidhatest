<?php

namespace App\Http\Controllers\IndexCardReports\IndexCardDataReport;

/*use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use Session;
use PDF;
use Illuminate\Support\Facades\Route;*/
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth AS Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Config;
use \PDF;
use MPDF;
use App\commonModel;  
use App\adminmodel\CEOModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\adminmodel\CEOPCModel;
use App\adminmodel\PCCeoReportModel;
use App\Classes\xssClean;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;

ini_set("memory_limit","850M");
set_time_limit('120');
ini_set("pcre.backtrack_limit", "5000000");

class IndexCardReportController extends Controller
{
    public function __construct(){
    	$this->middleware('adminsession');
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ceo');
    	// $user_data = Session::get('admin_login_details');
    	// echo "<pre>"; var_dump(session()->all()); die;
    }
    protected function guard(){
        return Auth::guard();
    }
    public function indexCardReport(Request $request){
    	//dd("Hello");
    	$session = $request->session()->all();
    	$user_data = Session::get('admin_login_details');
    	// echo "<pre>"; print_r($user_data); die;
    	/*select * from `t_pc_ic` as `A` 
inner join (SELECT MAX(id) as id, MAX(created_at) AS created_at, st_code, pc_no FROM t_pc_ic GROUP BY st_code, pc_no) AS B on `A`.`created_at` = `B`.`created_at` AND A.id = B.id
inner join `m_pc` as `C` on `A`.`st_code` = `C`.`st_code` and `A`.`pc_no` = `C`.`pc_no` where (`A`.`st_code` = 'S02')*/
    	// DB::enableQueryLog();
    	$indexCardData = DB::table('t_pc_ic AS A')
    					->join(DB::raw('(SELECT MAX(id) as id, MAX(created_at) AS created_at, st_code, pc_no FROM t_pc_ic GROUP BY st_code, pc_no) AS B'),function($mJoin){
    							$mJoin->on('A.created_at','B.created_at')
    									->on('A.id','B.id');
    					})
    					->join('m_pc AS C',function($query){
    						$query->on('A.st_code','C.st_code')
    								->on('A.pc_no','C.pc_no');
    					})
    					->where(['A.st_code' => $user_data->st_code])
    					->get()->toArray();
    	// $queue = DB::getQueryLog();
    	// echo "<pre>"; print_r($queue); die;

    	foreach ($indexCardData as $value) {
	    	$sWhere = array(
	    		'pc_no' 	=> $value->pc_no,
	    		'st_code' 	=> $user_data->st_code
	    	);
	    	$updateID = DB::table('cand_cont_ic')
	    					->select(DB::raw('MAX(update_id) AS maxid'))
	    					->where($sWhere)
	    					->first();
	    	$responseFromIC = DB::table('cand_cont_ic')
    						->where($sWhere)
    						->where('update_id',(!is_null($updateID->maxid)?$updateID->maxid:0))
    						->orderBy('update_id','desc')
    						->get()->toArray();
	    	if($responseFromIC){
				
				
	    		$allPartyLists = DB::table('m_party')
	    							->select('PARTYABBRE','PARTYNAME')
	    							->get()->toArray();
	    		$allPartyArray = array();
	    		foreach ($allPartyLists as $allPartyList) {
	    			$allPartyArray[$allPartyList->PARTYABBRE] = $allPartyList->PARTYNAME;
	    		}
	    		$allSymbolLists = DB::table('m_symbol')
	    							->select('SYMBOL_NO','SYMBOL_DES')
	    							->get()->toArray();
	    		$allSymbolArray = array();
	    		foreach ($allSymbolLists as $allSymbolList) {
	    			$allSymbolArray[$allSymbolList->SYMBOL_NO] = $allSymbolList->SYMBOL_DES;
	    		}
				
					$dataArrayCandidate = array();
				
	    		foreach ($responseFromIC as $responseFromICs) {

	    			DB::enableQueryLog();

	    			$candidate_detail = DB::table('candidate_personal_detail AS A')
	    								->join('candidate_nomination_detail AS B','A.candidate_id','B.candidate_id')
	    								->join('m_party AS C','B.party_id','C.CCODE')
	    								->join('m_symbol AS D','B.symbol_id','D.symbol_no')
	    								->where('A.candidate_id',$responseFromICs->con_cand_id)
	    								->first();

	    			$query = DB::getQueryLog();


	    		//echo '<pre>'; print_r($candidate_detail); die;

	    			//$candidate_detail = json_decode(json_encode($candidate_detail));
	    			//$candidate_detail = (object) $candidate_detail;
	    			//echo "<pre>"; print_r($responseFromICs->con_cand_id); die;
	    		
	    			$dataArrayCandidate[$responseFromICs->con_cand_id] = array(
	    				'candidate_id' 		=> $responseFromICs->con_cand_id,
	    				'candidate_name' 	=> $candidate_detail->cand_name,
	    				'party_abbre' 		=> $candidate_detail->PARTYABBRE,
	    				'party_name' 		=> $candidate_detail->PARTYNAME,
	    				'pc_no' 			=> $responseFromICs->pc_no,
	    				'election_id' 		=> $candidate_detail->election_id,
	    				'postaltotalvote' 	=> $responseFromICs->postal_vote_count,
	    				'cand_name' 		=> $candidate_detail->cand_name,
	    				'cand_gender' 		=> $candidate_detail->cand_gender,
	    				'cand_age' 			=> $candidate_detail->cand_age,
	    				'cand_category' 	=> $candidate_detail->cand_category,
	    				'isfinalise'		=> 0,
	    				'symb_desc'			=> $candidate_detail->SYMBOL_DES,
	    				'symb_no'			=> $candidate_detail->SYMBOL_NO
	    			);



				$bTable = "counting_master_".strtolower($user_data->st_code)." AS A";
				
    			$cWhere = array(
    				'candidate_id' => $responseFromICs->con_cand_id,
    				'm_ac.ST_CODE' => $user_data->st_code					
    			);
    			$cSelect = array(
    				'A.ac_no',
    				'A.total_vote',
					'm_ac.AC_NAME as ac_name'
    			);
    			$allACinPCs = DB::table($bTable)
								->join('m_ac', function($join){
									$join->on('A.ac_no','=','m_ac.AC_NO')
										->on('A.pc_no','=','m_ac.PC_NO');
								})
    							->select($cSelect)
    							->where($cWhere)
    							->orderBy('ac_no','ASC')
    							->get()->toArray();
							
								
	    		$allACList = array();
	    		foreach ($allACinPCs as $allACinPC) {
	    			$allACList[$allACinPC->ac_no] = $allACinPC->ac_name;
	    		}
	    						
				foreach ($allACinPCs as $acwisevote) {
    				$dataArrayCandidate[$responseFromICs->con_cand_id]['votescountacwise'][$acwisevote->ac_no] = $acwisevote->total_vote;
    			}
    			/* foreach ($allACinPCs as $keyAC => $valueAC) {
    				if(!$dataArrayCandidate[$responseFromICs->con_cand_id]['votescountacwise'][$keyAC]){
    					$dataArrayCandidate[$responseFromICs->con_cand_id]['votescountacwise'][$keyAC] = 0;
    				}
    			} */
					
					


	    			$eSelect = [
	    				
	    				'A.ac_no',
	    				'B.ac_type',
	    				'B.ac_name',
	    				'A.electors_male as e_gen_m',
	    				'A.electors_female as e_gen_f',
	    				'A.electors_other as e_gen_o',
	    				'A.nri_male_votes as e_nri_m',
	    				'A.nri_female_votes as e_nri_f',
	    				'A.nri_third_votes as e_nri_o',
	    				'A.service_male_electors as e_ser_m',
	    				'A.service_female_electors as e_ser_f',
	    				'A.service_postal_votes as e_ser_m',

	    				];

	    			$eWhere = array(
			    		'A.pc_no' 		=> $value->pc_no,
			    		'A.st_code' 	=> $user_data->st_code
			    	);


                    	$electorsDataACWise = DB::table('m_ac AS B')
                    	->select($eSelect)
                    //->select('*','ed.electors_male','ed.electors_female','ed.electors_other','ed.electors_total')
                        ->leftJoin('electors_cdac AS A',function($query){
                           $query->on('B.AC_NO','A.ac_no')
                                   ->on('B.ST_CODE','A.st_code')
                                   ->on('B.PC_NO','A.pc_no');
                       })
                      ->where('B.st_code', $user_data->st_code)
                      ->where('A.pc_no', $value->pc_no)
                      ->where('A.year', 2019)
                      // ->where('ed.scheduledid', 1)
                    
                    ->get()->toArray();
	    		}


//echo '<pre>'; print_r($electorsDataACWise); die;


	    	}

	    	$value->allaclist = $allACList;
	    	$value->candidate_data = $dataArrayCandidate;
	    	$value->electorsDataACWise = $electorsDataACWise;
    	}
    	// echo "<pre>"; print_r($indexCardData); die;
    	if($request->path() == 'pcceo/IndexCardDataReport'){
    		return view('IndexCardReports.IndexCardDataReport.indexCardReport',compact('user_data','session','indexCardData'));
    	}elseif($request->path() == 'pcceo/IndexCardDataReportPDF'){
			
			//echo '<pre>'; print_r($indexCardData); die;
			
			
			
    		$pdf=PDF::loadView('IndexCardReports.IndexCardDataReport.indexCardReportPDF',[
    		'session'=>$session,
    		'indexCardData'=>$indexCardData
    	]);
		return $pdf->download('Index_card_data_report.pdf');
    		
    	}else{
    		die('No Data Found');
    	}
    }
}
