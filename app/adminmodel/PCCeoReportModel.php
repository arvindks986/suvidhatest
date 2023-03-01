<?php
namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;
    use DB;
class PCCeoReportModel extends Model
{

	function duplicateSymboleCandidate($stcode)
    {  
    $query = "SELECT X.st_code,X.pc_no,X.party_id,X.symbol_id,X.candidate_id FROM `candidate_nomination_detail` X JOIN (SELECT st_code,pc_no,party_id,symbol_id,candidate_id,COUNT(symbol_id)cnt,nom_id FROM `candidate_nomination_detail` WHERE party_id !=1180 AND application_status=6 AND finalaccepted=1 AND symbol_id!=200 AND `st_code` = '$stcode' GROUP BY candidate_id,pc_no,party_id,symbol_id HAVING cnt>1) Y ON X.party_id=Y.party_id AND X.st_code=Y.st_code AND X.pc_no=Y.pc_no AND X.symbol_id=Y.symbol_id WHERE X.party_id !=1180 AND X.application_status=6 AND X.finalaccepted=1 AND X.symbol_id!=200 AND X.`st_code` = '$stcode' GROUP BY X.nom_id ORDER BY X.symbol_id,X.candidate_id";
           $duplicateSymboleCandidate = DB::select(DB::raw($query)); 
            return $duplicateSymboleCandidate;
    }

    function getduplicatenominationparty($stcode)
             { //DB::enableQueryLog();
            
  $query="SELECT  X.st_code,X.pc_no,X.party_id,X.symbol_id,X.candidate_id FROM `candidate_nomination_detail` X JOIN (SELECT st_code,pc_no,party_id,symbol_id,candidate_id,COUNT(party_id)cnt,nom_id FROM `candidate_nomination_detail` WHERE party_id !=1180 AND application_status=6 AND finalaccepted=1 AND cand_party_type!='Z' AND symbol_id!=200 AND `st_code` = '$stcode' GROUP BY candidate_id,pc_no,party_id,symbol_id HAVING cnt>1) Y ON X.party_id=Y.party_id AND X.st_code=Y.st_code AND X.pc_no=Y.pc_no  AND X.symbol_id=Y.symbol_id WHERE X.party_id !=1180 AND X.application_status=6 AND X.finalaccepted=1 AND X.symbol_id!=200 AND X.`st_code` = '$stcode' GROUP BY X.nom_id ORDER BY X.party_id,X.candidate_id";
		   
           $getduplicatenominationparty = DB::select(DB::raw($query)); 

           return $getduplicatenominationparty;  
             }
             function getCandidateListbyPC($st_code,$pc_no)
         {

            //$getCandidateListbyPC =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('application_status',6)->get();
           $getCandidateListbyPC =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('pc_no',$pc_no)->where('application_status',6)->get();
           return $getCandidateListbyPC;
         }
          
           function totalnominationcntbystatus($status,$pc_no)
             {  //DB::enableQueryLog();
               $totalnominationcntbystatus =DB::table('candidate_nomination_detail')->where('pc_no',$pc_no)->where('application_status',$status)->get()->count();
              // dd(DB::getQueryLog());
             return $totalnominationcntbystatus;    
             }
             function independentcandidatelist($st_code,$cand_party_type,$finalize)
       {
            DB::enableQueryLog();
            $independentcandidatelist =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('finalize',$finalize)->get();
            //dd(DB::getQueryLog());
            return $independentcandidatelist;
       }

       function ceosymbolno_200pdf($st_code)
       {     
           DB::enableQueryLog();
            $independentcandidatelist =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('symbol_id',200)->get();
            //dd(DB::getQueryLog());
            return $independentcandidatelist;
       }

      


function getCountStatus($st_code,$pcno,$status)
       {
        //DB::enableQueryLog();
               $count =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('pc_no',$pcno)->where('application_status',$status)->get()->count();
              // dd(DB::getQueryLog());
            return $count;
       }


function getelectorssummarybyState($stcode,$election_id)
		{  DB::enableQueryLog();
		
$query ="SELECT 
m_pc.PC_NO,
m_pc.PC_NAME,
SUM(elector_details.gen_m) as total_gen_m ,
SUM(elector_details.gen_f) as total_gen_f ,
SUM(elector_details.gen_o) as total_gen_o ,
SUM(elector_details.gen_t) as total_gen_t ,
SUM(elector_details.ser_m) as total_ser_m ,
SUM(elector_details.ser_f) as total_ser_f ,
SUM(elector_details.ser_o) as total_ser_o,
SUM(elector_details.ser_t) as total_ser_t ,
SUM(elector_details.polling_reg) as total_polling_reg,
SUM(elector_details.polling_auxillary) as total_polling_auxillary,
SUM(elector_details.polling_total) as total_polling_total  
FROM m_pc left join elector_details
on m_pc.PC_NO=elector_details.pc_no and m_pc.ST_CODE=elector_details.st_code
WHERE m_pc.ST_CODE='$stcode' GROUP BY m_pc.PC_NO,m_pc.PC_NAME";
		   $getelectorssummarybyState = DB::select(DB::raw($query));
		    
		//dd(DB::getQueryLog());
		return $getelectorssummarybyState;
		}  
function getAcByPC($stcode,$pc_no,$election_id)
		{  DB::enableQueryLog();
		$getelectorsData = DB::table('elector_details')->where('election_id',$election_id)->where('st_code', $stcode)->where('pc_no',$pc_no)->get();
		
		if(!empty($getelectorsData)){
		 $query="select `elector_details`.*, `m_ac`.`AC_NO`, `m_ac`.`AC_NAME` from `m_ac` left join `elector_details` on (`m_ac`.`PC_NO` = `elector_details`.`pc_no` and `m_ac`.`AC_NO` = `elector_details`.`ac_no` AND `elector_details`.`st_code`='$stcode') WHERE  `m_ac`.`st_code`= '$stcode' and `m_ac`.`PC_NO` = $pc_no group by `m_ac`.`AC_NO`, `m_ac`.`AC_NAME`, `elector_details`.`ac_no`";
				   $getAcListByPCNo = DB::select(DB::raw($query));
		    }else{
		   $getAcListByPCNo = DB::table('m_ac')->where('ST_CODE', $stcode)->where('PC_NO',$pc_no)->orderBy('AC_NO', 'asc')->get();
		   }
		//dd(DB::getQueryLog());
		return $getAcListByPCNo;
		}
		
		
public function ComputeSha512Hash($st_code,$ac_no)
{ 
// Create a SHA512
$secureKey = "ABCD1234#123521GISTECIKEY";

$data=$st_code.$ac_no.$secureKey; 
$SHA512sha = strtoupper(hash('sha512', $data));
return $SHA512sha;
                
}
function callAPI($method, $url, $data){
   $curl = curl_init();
  
   switch ($method){
      case "POST":
         curl_setopt($curl, CURLOPT_POST, 1);
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         break;
      case "PUT":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
         break;
      default:
         if ($data)
            $url = sprintf("%s?%s", $url, http_build_query($data));
   }

   // OPTIONS:
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
   ));
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  // curl_setopt($curl, CURLOPT_TIMEOUT, 5);
   //curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

   // EXECUTE:
   $json = curl_exec($curl);
   $result = json_decode($json);
   //if(!$result){die("Connection Failure");}
   curl_close($curl);
   return $result;
}
	
//By Niraj 18-03-19
function getAllCandidateListbyPC($st_code,$pc_no)
{
  $getAllCandidateListbyPC =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('pc_no',$pc_no)
  ->where('party_id','!=','1180')->where('application_status','!=','11')
  ->get();
  return $getAllCandidateListbyPC;
}

//By Niraj 18-03-19
function getDatewiseCandidateListbyPC($stcode,$pc_no,$fromdate, $todate)
{
 
  if($fromdate!='' & $todate!=''){ 
   $getDatewiseCandidateListbyPC =  DB::table('candidate_nomination_detail')
   ->where('st_code',$stcode)
   ->where('pc_no',$pc_no)->where('party_id','!=','1180')->where('application_status','!=','11')
   ->whereBetween('date_of_submit', [$fromdate, $todate])->get();
  }else{
      $getDatewiseCandidateListbyPC =  DB::table('candidate_nomination_detail')
      ->where('st_code',$stcode)
      ->where('pc_no',$pc_no)->where('party_id','!=','1180')->where('application_status','!=','11')
      ->get();
  }	
  return $getDatewiseCandidateListbyPC;
}
   
// getDatewisenomination CEO level by Niraj 18-3-19		
function getDatewisenomination($stcode,$pc_no,$fromdate, $todate)
{  DB::enableQueryLog();
 if($fromdate!='' & $todate!=''){ 
  $getDatewisenomination =  DB::table('candidate_nomination_detail')
  ->select('*', DB::raw('count(nom_id) as totalnomination'))
  ->where('st_code',$stcode)->where('party_id','!=','1180')->where('application_status','!=','11')
  ->groupBy('pc_no')
  ->whereBetween('date_of_submit', [$fromdate, $todate])->get();
 }else{
     $getDatewisenomination =  DB::table('candidate_nomination_detail')
     ->select('*', DB::raw('count(nom_id) as totalnomination'))
     ->where('st_code',$stcode)->where('party_id','!=','1180')->where('application_status','!=','11')
     ->groupBy('pc_no')
     ->get();
 }		  
  //dd(DB::getQueryLog());
  
  return $getDatewisenomination;
} //end function getDatewisenomination
	
		
		
}
?>