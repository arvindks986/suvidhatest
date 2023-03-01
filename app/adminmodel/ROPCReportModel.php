<?php
    namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;
    use DB;
class ROPCReportModel extends Model
{
    //devloped by Niraj
		  
	  function getAcByPC($stcode,$pc_no,$election_id)
		{ DB::enableQueryLog();
		$getelectorsData = DB::table('elector_details')->where('election_id',$election_id)->where('st_code', $stcode)->where('pc_no',$pc_no)->get();
		if(!empty($getelectorsData)){
				 $query="select `elector_details`.*, `m_ac`.`AC_NO`, `m_ac`.`AC_NAME` from `m_ac` left join `elector_details` on (`m_ac`.`PC_NO` = `elector_details`.`pc_no` and `m_ac`.`AC_NO` = `elector_details`.`ac_no` AND `elector_details`.`st_code`='$stcode') WHERE  `m_ac`.`st_code`= '$stcode' and `m_ac`.`PC_NO` = $pc_no group by `m_ac`.`AC_NO`, `m_ac`.`AC_NAME`, `elector_details`.`ac_no`";
				   $getAcListByPC = DB::select(DB::raw($query));
		    }else{
		   $getAcListByPC = DB::table('m_ac')->where('ST_CODE', $stcode)->where('PC_NO',$pc_no)->orderBy('AC_NO', 'asc')->get();
		   }
		return $getAcListByPC;
		}
		
// Officer Details ROPC level by Niraj 19-2-19		
	function getOfficerlistByROPC($stcode,$pc_no='')
		{  DB::enableQueryLog();
		 if($pc_no!=''){
		  $getOfficerlistByROPC =  DB::table('officer_login')->where('st_code',$stcode)->where('pc_no',$pc_no)->get();
		 }else{
			  $getOfficerlistByROPC =  DB::table('officer_login')->where('st_code',$stcode)->get();
		 }		  
		  //dd(DB::getQueryLog());
		  
		  return $getOfficerlistByROPC;
		} //end function getOfficerlistByROPC
		
		// getnominationByROPC ROPC level by Niraj 6-3-19		
	function getnominationByROPC($stcode,$pc_no,$fromdate, $todate)
		{  DB::enableQueryLog();
		 if($fromdate!='' & $todate!=''){
		  $getnominationByROPC =  DB::table('candidate_nomination_detail')->where('st_code',$stcode)->where('pc_no',$pc_no)->whereBetween('date_of_submit', [$fromdate, $todate])->get();
		 }else{
			  $getnominationByROPC =  DB::table('candidate_nomination_detail')->where('st_code',$stcode)->where('pc_no',$pc_no)->get();
		 }		  
		  //dd(DB::getQueryLog());
		  
		  return $getnominationByROPC;
		} //end function getOfficerlistByROPC
		
		 // getform4AByROPC Details ROPC level by Niraj 7-3-19		
	function getform4AByROPC($stcode,$pc_no,$fromdate, $todate)
		{  DB::enableQueryLog();
		 if($fromdate!='' & $todate!=''){
		  $getform4AByROPC =  DB::table('candidate_nomination_detail')->where('st_code',$stcode)->where('pc_no',$pc_no)->where('application_status',6)->whereBetween('date_of_submit', [$fromdate, $todate])->get();
		 }else{
			  $getform4AByROPC =  DB::table('candidate_nomination_detail')->where('st_code',$stcode)->where('pc_no',$pc_no)->where('application_status',6)->get();
		 }		  
		  //dd(DB::getQueryLog());
		  
		  return $getform4AByROPC;
		} //end function getform4AByROPC
		
	 
 }