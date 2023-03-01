<?php

namespace App\Http\Controllers\IndexCardReports;

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
ini_set('max_execution_time', 300);

class dataMigrationCandidateWiseContoller extends Controller
{
    public function startMigForCandidateVotes($st_code){
    	//dd($st_code);
    	// echo "<pre>"; var_dump($st_code); die;
    	$bTable = "counting_master_".strtolower($st_code)." AS A";
    	$gTable = "counting_master_".strtolower($st_code)." AS cm";
    	$pc_nos = DB::table('m_pc')->select('pc_no')->where('st_code',$st_code)->get()->toArray();
    	 //echo "<pre>"; print_r($pc_nos); die;
    	$count = 0;

    	foreach ($pc_nos as $pc_no) {
	    		$bWhere = array(
	    			'A.st_code' 			=> $st_code,
	    			'A.pc_no' 				=> $pc_no->pc_no,
	    			'A.application_status' 	=> 6,
	    			'A.finalaccepted'       =>1
	    		);
	    		$bSelect = array(
	    			'A.candidate_id',
	    			'A.party_id',
	    			'A.symbol_id',
	    			'A.election_id',
	    			'A.pc_no',
	    			'cm.ac_no',
	    			'A.st_code',
	    			'B.cand_name',
	    			'B.cand_gender',
	    			'B.cand_age',
	    			'B.cand_category',
	    			'C.PARTYABBRE',
	    			'C.PARTYNAME',
	    			'C.PARTYTYPE',
	    			'D.symbol_no',
	    			'D.SYMBOL_DES',
	    			'E.postal_vote',
	    			'E.evm_vote',
	    			'cm.total_vote'
	    		);
	    		

	    		$responseFronCountingPC = DB::table('candidate_nomination_detail AS A')
	    									->select($bSelect)
	    									->join('candidate_personal_detail AS B','A.candidate_id','B.candidate_id')
	    									->join('m_party AS C','A.party_id','C.ccode')
	    									->join('m_symbol AS D','A.symbol_id','D.symbol_no')
	    									->join('counting_pcmaster AS E','A.candidate_id','E.candidate_id')
	    									->leftJoin($gTable,'cm.candidate_id', 'A.candidate_id')
	    									->where($bWhere)
	    									->get()->toArray();
	    		

				/* echo '<pre>'; print_r($responseFronCountingPC);
        		die('');


				 */

	    		$aWhere = array(
	    			'st_code' 	=> $st_code,
	    			'pc_no'		=> $pc_no->pc_no
	    		);   

	    		 		
	    		$dataArrayCandidate = array();
	    		foreach ($responseFronCountingPC as $key) {
	    			//echo "<pre>"; print_r($responseFronCountingPC); die;
	    			$dataArrayCandidate[$key->candidate_id] = array(
	    				'candidate_id' 		=> $key->candidate_id,
	    				'candidate_name' 	=> $key->cand_name,
	    				'party_abbre' 		=> $key->PARTYABBRE,
	    				'party_name' 		=> $key->PARTYNAME,
	    				'pc_no' 			=> $key->pc_no,
	    				'ac_no'				=> $key->ac_no,
	    				'vote_count'		=>$key->total_vote,
	    				'election_id' 		=> $key->election_id,
	    				'postaltotalvote' 	=> $key->postal_vote,
	    				'cand_name' 		=> $key->cand_name,
	    				'cand_gender' 		=> $key->cand_gender,
	    				'cand_age' 			=> $key->cand_age,
	    				'cand_category' 	=> $key->cand_category,
	    				'isfinalise'		=> 0,
	    				'symb_desc'			=> $key->SYMBOL_DES,
	    				'symb_no'			=> $key->symbol_no
	    			);
	    			$cWhere = array(
	    				'candidate_id' => $key->candidate_id
	    			);
	    			$cSelect = array(
	    				'ac_no',
	    				'total_vote'
	    			);
	    			DB::enableQueryLog();
	    			$acwisevotes = DB::table($bTable)
	    							->select($cSelect)
	    							->where($cWhere)
	    							->orderBy('ac_no','ASC')
	    							->get()->toArray();

	    			
	    			$totalValidVotes = 0;
	    			foreach ($acwisevotes as $acwisevote) {
	    				$dataArrayCandidate[$key->candidate_id]['votescountacwise'][$acwisevote->ac_no] = $acwisevote->total_vote;
	    				
	    			}

	    			DB::enableQueryLog();
	    			$allACinPCs = DB::table('m_ac')
		    						->select('ac_no','ac_name')
		    						->where($aWhere)
		    						->orderBy('ac_name','ASC')
		    						->get()->toArray();

		    		$queue = DB::getQueryLog();
	    			//echo "<pre>"; print_r($queue); die;
	    			
		    		$allACList = array();
		    		foreach ($allACinPCs as $allACinPC) {
		    			$allACList[$allACinPC->ac_no] = $allACinPC->ac_name;
		    		}
	    			foreach ($allACList as $keyAC => $valueAC) {
	    				if(!$dataArrayCandidate[$key->candidate_id]['votescountacwise'][$keyAC]){
	    					$dataArrayCandidate[$key->candidate_id]['votescountacwise'][$keyAC] = 0;

	    				}
	    			}
	    		}
	    		//echo "<pre>"; print_r($dataArrayCandidate); die; 
	    		foreach($dataArrayCandidate as $key) {
		    			// echo "<pre>"; print_r($key); die;
		    	
		    			// echo "<pre>"; print_r($kkey."=>".$v);
		    			$dataForSave['con_cand_id'] 		= $key['candidate_id'];
		    			$dataForSave['st_code'] 			= $st_code;
		    			$dataForSave['schedule_id'] 		= 1;
		    			$dataForSave['pc_no'] 				= $key['pc_no'];
		    			
		    			
		    			$dataForSave['postal_vote_count'] 	= $key['postaltotalvote'];
		    			$dataForSave['total_valid_vote'] 	= array_sum($key['votescountacwise']);
		    			$dataForSave['updated_by'] 			= 0;
		    			$dataForSave['updated_at'] 			= date("Y-m-d H:i:s");
		    			$dataForSave['update_id'] 			= 0;


		                $dataForSave = array_map(
		                   function($val){
		                       return ($val)?$val:0;
		                       },$dataForSave
		                   );

		                foreach ($key['votescountacwise'] as $ackey => $accountdata ) {
		                	$dataForSave['ac_no'] 				= $ackey;
		                	$dataForSave['vote_count'] 			= $accountdata;
		                
		               // echo "<pre>"; print_r($dataForSave); die;
						
						
		    			$insert = DB::table('candidate_count_ic')->insert($dataForSave);
		    			$count++;
		    			echo "Data inserted for State ".$st_code." candidate id ".$key['candidate_id']."<br>";
		    		}
		    		
	    		}
    		}
    		echo "Count : ".$count;
    	}
}
