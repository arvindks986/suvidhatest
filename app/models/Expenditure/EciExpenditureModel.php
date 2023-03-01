<?php
namespace App\models\Expenditure;
use Illuminate\Database\Eloquent\Model;
use DB;
class EciExpenditureModel extends Model
{
	
//By Niraj For getting access date 29-07-19
  function getzonestate($officername)
  { 
    $zonestate = DB::table('expenditure_zone_users')
     ->select('assign_state')->where('officername',$officername)->get();
      return ($zonestate);
  }
	
//By Niraj For getting access date 23-07-19
  function getpermitstate($permitstate)
  { 
    $g = DB::table('m_state')
    ->whereIn('ST_CODE', $permitstate)
    ->orderBy('ST_CODE', 'ASC')->get();
      return ($g);
  }
   
  //By Niraj For getting start data entry count date 8-5-19
  function gettotaldataentryStart($const_type,$st_code='',$const_no='')
  { 	DB::enableQueryLog();
    if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
  // echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no;
  if($const_type=="PC" && $st_code=='' && $const_no=='') {
    $gettotaldataentryStart =DB::table('expenditure_reports')
    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
	->select(DB::raw('count(DISTINCT expenditure_reports.candidate_id) as cnt'))
    ->where('candidate_nomination_detail.application_status', '=', '6')
    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
    ->get();
    } elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
      $gettotaldataentryStart =DB::table('expenditure_reports')
	   ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
	   ->select(DB::raw('count(DISTINCT expenditure_reports.candidate_id) as cnt'))
    ->where('candidate_nomination_detail.application_status', '=', '6')
    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
      ->where('expenditure_reports.ST_CODE',$st_code)
    ->get();
    }elseif($const_type=="PC" && $st_code!='' &&  $const_no!=''){
      $gettotaldataentryStart =DB::table('expenditure_reports')
	   ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
	   ->select(DB::raw('count(DISTINCT expenditure_reports.candidate_id) as cnt'))
    ->where('candidate_nomination_detail.application_status', '=', '6')
    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
     ->where('expenditure_reports.ST_CODE',$st_code)
     ->where('expenditure_reports.constituency_no',$const_no)
    ->get();
    }
    $gettotaldataentryStart = $gettotaldataentryStart[0]->cnt;
     //dd(DB::getQueryLog());
     return $gettotaldataentryStart;	
  }


  //By Niraj For getting finalize data entry count date 8-5-19
  function gettotaldataentryFinal($const_type,$st_code='',$const_no='')
  { 	DB::enableQueryLog();
    if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
  if($const_type=="PC" && $st_code=='') {
    $gettotaldataentryFinal =DB::table('expenditure_reports')
    //->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
    ->where('finalized_status','1')->count();
    }elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
      $gettotaldataentryFinal =DB::table('expenditure_reports')
     ->where('ST_CODE',$st_code)
    // ->where('constituency_no',$const_no)
    ->where('finalized_status','1')->count();
    }elseif($const_type=="PC" && $st_code!='' && $const_no!=''){
      $gettotaldataentryFinal =DB::table('expenditure_reports')
     ->where('ST_CODE',$st_code)
     ->where('constituency_no',$const_no)
    ->where('finalized_status','1')->count();
    } 
   elseif($const_type=="AC"){
    $gettotaldataentryFinal =DB::table('expenditure_reports')
    //->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
    ->where('finalized_status','1')->count(); 
    } 
   //dd(DB::getQueryLog());
      return $gettotaldataentryFinal;	
  }

  //By Niraj For getting account loged count date 8-5-19
  function gettotallogedAccount($const_type,$st_code='',$const_no='')
  { 	DB::enableQueryLog();
    if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
  if($const_type=="PC" && $st_code=='') {
    $gettotallogedAccount =DB::table('expenditure_reports')
    //->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
    ->where('candidate_lodged_acct','Yes')->count();
    } elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
      $gettotallogedAccount =DB::table('expenditure_reports')
      ->where('ST_CODE',$st_code)
      //->where('constituency_no',$const_no)
      ->where('candidate_lodged_acct','Yes')->count();

    } elseif($const_type=="PC" && $st_code!='' && $const_no!=''){
      $gettotallogedAccount =DB::table('expenditure_reports')
      ->where('ST_CODE',$st_code)
      ->where('constituency_no',$const_no)
      ->where('candidate_lodged_acct','Yes')->count();

    }
   elseif($const_type=="AC"){
    $gettotallogedAccount =DB::table('expenditure_reports')
    //->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
    ->where('candidate_lodged_acct','Yes')->count(); 
    } 
   //dd(DB::getQueryLog());
      return $gettotallogedAccount;	
  }

  //By Niraj For getting account loged count date 8-5-19
  function gettotalNotinTime($const_type,$st_code='',$const_no='')
  { 	DB::enableQueryLog();
   if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
  if($const_type=="PC" && $st_code=='' && $const_no=='') {
    $gettotalNotinTime =DB::table('expenditure_reports')
    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
    ->where('expenditure_reports.account_lodged_time','No')
    ->where('candidate_nomination_detail.application_status', '=', '6')
    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
    ->count();
    } elseif($const_type=="PC" && $st_code!='' && $const_no==''){
      $gettotalNotinTime =DB::table('expenditure_reports')
    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
    ->where('expenditure_reports.account_lodged_time','No')
    ->where('candidate_nomination_detail.application_status', '=', '6')
    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
     ->where('expenditure_reports.ST_CODE',$st_code)
    ->count();
    } elseif($const_type=="PC" && $st_code!='' && $const_no!=''){
      $gettotalNotinTime =DB::table('expenditure_reports')
    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
     ->where('expenditure_reports.account_lodged_time','No')
     ->where('candidate_nomination_detail.application_status', '=', '6')
     ->where('candidate_nomination_detail.finalaccepted', '=', '1')
     ->where('candidate_nomination_detail.symbol_id', '<>', '200')
      ->where('expenditure_reports.ST_CODE',$st_code)
      ->where('expenditure_reports.constituency_no',$const_no)
     ->count();
    }
   //dd(DB::getQueryLog());
      return $gettotalNotinTime;	
  }


  //By Niraj For Defects in format count date 8-5-19
  function gettotalDefectformats($const_type,$st_code='',$const_no='')
  { 	//DB::enableQueryLog();
   if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
  if($const_type=="PC" && $st_code =='' &&  $const_no=='') {
    $gettotalDefectformats =DB::table('expenditure_reports')
	->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
     ->where('expenditure_reports.rp_act','No')
     ->where('candidate_nomination_detail.application_status', '=', '6')
     ->where('candidate_nomination_detail.finalaccepted', '=', '1')
     ->where('candidate_nomination_detail.symbol_id', '<>', '200')
     ->count();
    } elseif($const_type=="PC" && $st_code !='' &&  $const_no==''){
      $gettotalDefectformats =DB::table('expenditure_reports')
	  ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
     ->where('expenditure_reports.rp_act','No')
     ->where('candidate_nomination_detail.application_status', '=', '6')
     ->where('candidate_nomination_detail.finalaccepted', '=', '1')
     ->where('candidate_nomination_detail.symbol_id', '<>', '200')
      ->where('expenditure_reports.ST_CODE',$st_code)
      ->count();
    } elseif($const_type=="PC" && $st_code !='' && $const_no !=''){
      $gettotalDefectformats =DB::table('expenditure_reports')
	  ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
     ->where('expenditure_reports.rp_act','No')
     ->where('candidate_nomination_detail.application_status', '=', '6')
     ->where('candidate_nomination_detail.finalaccepted', '=', '1')
     ->where('candidate_nomination_detail.symbol_id', '<>', '200')
      ->where('expenditure_reports.ST_CODE',$st_code)
      ->where('expenditure_reports.constituency_no',$const_no)
       ->count();
     }
     // dd(DB::getQueryLog());
      return $gettotalDefectformats;	
  }

 //By Niraj For Expense Understated count date 8-5-19
 function gettotalexpenseUnderStated($const_type,$st_code='',$const_no='')
 { //	DB::enableQueryLog();
  if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
 if($const_type=="PC" && $st_code=='' &&  $const_no=='') {
   $gettotalexpenseUnderStated =DB::table('expenditure_understated')
   //->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
   ->where('page_no_observation','Yes')->count();
   } elseif($const_type=="PC" && $st_code!='' &&  $const_no=='') {
    $gettotalexpenseUnderStated =DB::table('expenditure_understated')
     ->where('ST_CODE',$st_code)
    ->where('page_no_observation','Yes')->count();
    }elseif($const_type=="PC" && $st_code!='' && $const_no !='') {
      $gettotalexpenseUnderStated =DB::table('expenditure_understated')
      ->where('ST_CODE',$st_code)
	  ->where('constituency_no',$const_no)
      ->where('page_no_observation','Yes')->count();
      } 
  // dd(DB::getQueryLog());
     return $gettotalexpenseUnderStated;	
 }

 //By Niraj For party fund count date 8-5-19
 function gettotalPartyfund($const_type,$st_code='',$const_no='')
 { 	DB::enableQueryLog();
// echo $const_type.'st_code=>'.$st_code.'const_no=>'.$const_no;
if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
 if($const_type=="PC" && $st_code=='') {
   $gettotalPartyfund =DB::table('expenditure_fund_parties')
   ->select(DB::raw('IFNULL(SUM(political_fund_cash + political_fund_checque + political_fund_kind),0) AS total_partyfund'))
   //->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
   ->first();
   }elseif($const_type=="PC" && $st_code!='') {
    $gettotalPartyfund =DB::table('expenditure_fund_parties')
    ->select(DB::raw('IFNULL(SUM(political_fund_cash + political_fund_checque + political_fund_kind),0) AS total_partyfund'))
     ->where('ST_CODE',$st_code)
    ->first();
    }if($const_type=="PC" && $st_code !='' && $const_no !='') {
      $gettotalPartyfund =DB::table('expenditure_fund_parties')
      ->select(DB::raw('IFNULL(SUM(political_fund_cash + political_fund_checque + political_fund_kind),0) AS total_partyfund'))
      ->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
      ->first();
      }   elseif($const_type=="AC"){
    $gettotalPartyfund =DB::table('expenditure_fund_parties')
    ->select(DB::raw('IFNULL(SUM(political_fund_cash + political_fund_checque + political_fund_kind),0) AS total_partyfund'))
    //->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
    ->first();
    } 
  // dd(DB::getQueryLog());
     return $gettotalPartyfund;	
 }

  //By Niraj For party fund count date 8-5-19
  function gettotalOtherSourcesfund($const_type,$st_code='',$const_no='')
  { 	DB::enableQueryLog();
    if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
  if($const_type=="PC" && $st_code=='') {
    $gettotalOtherSourcesfund =DB::table('expenditure_fund_source')
    ->select(DB::raw('IFNULL(SUM(other_source_amount),0) AS total_otherSourcesfund'))
    //->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
    ->first();
    }  elseif($const_type=="PC" && $st_code!='' &&  $const_no=='') {
      $gettotalOtherSourcesfund =DB::table('expenditure_fund_source')
      ->select(DB::raw('IFNULL(SUM(other_source_amount),0) AS total_otherSourcesfund'))
      ->where('ST_CODE',$st_code)
      //->where('constituency_no',$const_no)
      ->first();
      }  elseif($const_type=="PC" && $st_code!='' && $const_no !='') {
        $gettotalOtherSourcesfund =DB::table('expenditure_fund_source')
        ->select(DB::raw('IFNULL(SUM(other_source_amount),0) AS total_otherSourcesfund'))
        ->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
        ->first();
        } 
   elseif($const_type=="AC"){
    $gettotalOtherSourcesfund =DB::table('expenditure_fund_source')
    ->select(DB::raw('IFNULL(SUM(other_source_amount),0) AS total_otherSourcesfund'))
    //->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
    ->first(); 
    } 
   // dd(DB::getQueryLog());
      return $gettotalOtherSourcesfund;	
  }


  
  ###################start status dashboard##############################
  //By Niraj For getting start data entry count date 16-5-19
  function gettotalpartiallypending($const_type,$st_code='',$const_no='')
  { 	DB::enableQueryLog();
  // echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no;
  if($const_no=='0') $const_no=''; if($st_code=='0') $st_code='';
  if($const_type=="PC" && $st_code==''  &&  $const_no=='') {
    $gettotalpartiallypending =DB::table('expenditure_reports')
	  ->where('final_by_ro','1')
      ->whereNotNull('date_of_sending_deo')
     ->where(function($query) {
		 $query->whereNull('date_of_receipt');
		  $query->orwhere('date_of_receipt', '=','');
		   })
    ->count();
    } elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
      $gettotalpartiallypending =DB::table('expenditure_reports')
      ->where('ST_CODE',$st_code)
	  ->where('final_by_ro','1')
    ->whereNotNull('date_of_sending_deo')
    ->where(function($query) {
		 $query->whereNull('date_of_receipt');
		  $query->orwhere('date_of_receipt', '=','');
		   })
      //->where('expenditure_notification.deo_action','0')
      //->where('constituency_no',$const_no)
    ->count();
    }elseif($const_type=="PC" && $st_code!='' &&  $const_no!=''){
      $gettotalpartiallypending =DB::table('expenditure_reports')
      //->join('expenditure_notification', 'expenditure_notification.candidate_id', '=', 'expenditure_reports.candidate_id') 
      ->where('ST_CODE',$st_code)
      ->where('constituency_no',$const_no)
      //->where('expenditure_notification.deo_action','0')
	  ->where('final_by_ro','1')
    ->whereNotNull('date_of_sending_deo')
     ->where(function($query) {
		 $query->whereNull('date_of_receipt');
		  $query->orwhere('date_of_receipt', '=','');
		   })
      ->count();
    }
      //dd(DB::getQueryLog());
      return $gettotalpartiallypending;	
  }

  //By Niraj For getting start data entry count date 16-5-19
  function getdefaulter($const_type,$st_code='',$const_no='')
  {
       DB::enableQueryLog();
       if($const_no=='0') $const_no=''; if($st_code=='0') $st_code='';
  // echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no; die;
  if($const_type=="PC" && $st_code=='' &&  $const_no=='') {
    $getdefaulter =DB::table('expenditure_understated')
    ->select(DB::raw('IFNULL(SUM(amt_as_per_observation),0) AS totalobseramnt'),
     DB::raw('IFNULL(SUM(amt_understated_by_candidate),0) AS totalcandamnt'))
     ->having('totalobseramnt','<','totalcandamnt')
    //->where('ST_CODE',$st_code)
    //->where('constituency_no',$const_no)
    ->groupBy('candidate_id')
    ->get();
    } elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
      $getdefaulter =DB::table('expenditure_understated')
    ->select(DB::raw('IFNULL(SUM(amt_as_per_observation),0) AS totalobseramnt'),
     DB::raw('IFNULL(SUM(amt_understated_by_candidate),0) AS totalcandamnt'))
     ->having('totalobseramnt','<','totalcandamnt')
    ->where('ST_CODE',$st_code)
    //->where('constituency_no',$const_no)
    ->groupBy('candidate_id')
    ->get();
    }elseif($const_type=="PC" && $st_code!='' &&  $const_no!=''){
      $getdefaulter =DB::table('expenditure_understated')
    ->select(DB::raw('IFNULL(SUM(amt_as_per_observation),0) AS totalobseramnt'),
     DB::raw('IFNULL(SUM(amt_understated_by_candidate),0) AS totalcandamnt'))
     ->having('totalobseramnt','<','totalcandamnt')
    ->where('ST_CODE',$st_code)
    ->where('constituency_no',$const_no)
    ->groupBy('candidate_id')
    ->get();
    }elseif($const_type=="AC" && $st_code==''  &&  $const_no =='') {
      $gettotaldataentryStart =DB::table('expenditure_understated')
    ->select(DB::raw('IFNULL(SUM(amt_as_per_observation),0) AS totalobseramnt'),
     DB::raw('IFNULL(SUM(amt_understated_by_candidate),0) AS totalcandamnt'))
     ->having('totalobseramnt','<','totalcandamnt')
    //->where('ST_CODE',$st_code)
    //->where('constituency_no',$const_no)
    ->groupBy('candidate_id')
    ->get();
      } elseif($const_type=="AC" && $st_code!='' &&  $const_no==''){
        $getdefaulter =DB::table('expenditure_understated')
    ->select(DB::raw('IFNULL(SUM(amt_as_per_observation),0) AS totalobseramnt'),
     DB::raw('IFNULL(SUM(amt_understated_by_candidate),0) AS totalcandamnt'))
     ->having('totalobseramnt','<=','totalcandamnt')
    ->where('ST_CODE',$st_code)
    //->where('constituency_no',$const_no)
    ->groupBy('candidate_id')
    ->get();
      }if($const_type=="AC"  && $st_code!='' &&  $const_no!='') {
    $getdefaulter =DB::table('expenditure_understated')
    ->select(DB::raw('IFNULL(SUM(amt_as_per_observation),0) AS totalobseramnt'),
     DB::raw('IFNULL(SUM(amt_understated_by_candidate),0) AS totalcandamnt'))
     ->having('totalobseramnt','<=','totalcandamnt')
    //->where('ST_CODE',$st_code)
    //->where('constituency_no',$const_no)
    ->groupBy('candidate_id')
    ->get();
    
    //dd(DB::getQueryLog());
      return $getdefaulter;	
    }
  }
  
  //By Niraj For getting finalize data by CEO entry count date 8-5-19
function gettotalfinalbyceo($const_type,$st_code='',$const_no='')
{ 	DB::enableQueryLog();
  if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
if($const_type=="PC" && $st_code=='') {
  $gettotalfinalbyceo =DB::table('expenditure_reports')
   //->join('expenditure_notification', 'expenditure_notification.candidate_id', '=', 'expenditure_reports.candidate_id') 
  //->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
  //->where('expenditure_notification.ceo_action','0')
   ->where('final_by_ceo','1')
   ->whereNotNull('date_of_receipt')
   ->whereNull('date_of_receipt_eci')
  ->count();
  }elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
    $gettotalfinalbyceo =DB::table('expenditure_reports')
   // ->join('expenditure_notification', 'expenditure_notification.candidate_id', '=', 'expenditure_reports.candidate_id') 
    ->where('ST_CODE',$st_code)
	 ->where('final_by_ceo','1')
   ->whereNotNull('date_of_receipt')
   ->whereNull('date_of_receipt_eci')
    // ->where('constituency_no',$const_no)
    //->where('expenditure_notification.ceo_action','0')
    ->count();
  }elseif($const_type=="PC" && $st_code!='' && $const_no!=''){
    $gettotalfinalbyceo =DB::table('expenditure_reports')
    //->join('expenditure_notification', 'expenditure_notification.candidate_id', '=', 'expenditure_reports.candidate_id') 
    ->where('ST_CODE',$st_code)
    ->where('constituency_no',$const_no)
    //->where('expenditure_notification.ceo_action','0')
    ->where('final_by_ceo','1')
    ->whereNotNull('date_of_receipt')
   ->whereNull('date_of_receipt_eci')
    ->count();
  } elseif($const_type=="AC" && $st_code=='') {
    $gettotalfinalbyceo =DB::table('expenditure_reports')
    //->join('expenditure_notification', 'expenditure_notification.candidate_id', '=', 'expenditure_reports.candidate_id') 
    //->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
    //->where('expenditure_notification.ceo_action','0')
	 ->where('final_by_ceo','1')
   ->whereNotNull('date_of_receipt')
   ->whereNull('date_of_receipt_eci')
    ->count();
    }elseif($const_type=="AC" && $st_code!='' &&  $const_no==''){
      $gettotalfinalbyceo =DB::table('expenditure_reports')
      //->join('expenditure_notification', 'expenditure_notification.candidate_id', '=', 'expenditure_reports.candidate_id') 
      ->where('ST_CODE',$st_code)
      // ->where('constituency_no',$const_no)
     // ->where('expenditure_notification.ceo_action','0')
	  ->where('final_by_ceo','1')
    ->whereNotNull('date_of_receipt')
   ->whereNull('date_of_receipt_eci')
    ->count();
    }elseif($const_type=="AC" && $st_code!='' && $const_no!=''){
      $gettotalfinalbyceo =DB::table('expenditure_reports')
      //->join('expenditure_notification', 'expenditure_notification.candidate_id', '=', 'expenditure_reports.candidate_id') 
      ->where('ST_CODE',$st_code)
      ->where('constituency_no',$const_no)
      //->where('expenditure_notification.ceo_action','0')
	  ->where('final_by_ceo','1')
      ->whereNotNull('date_of_receipt')
      ->whereNull('date_of_receipt_eci')
      ->count();
    } 
 //dd(DB::getQueryLog());
    return $gettotalfinalbyceo;	
}
//By Niraj For getting finalize data entry count date 8-5-19
function gettotalfinalbyeci($const_type,$st_code='',$const_no='')
{ 	DB::enableQueryLog();
  if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
 // echo 'const_type=>',$const_type.'st_code=>'.$st_code.'const_no=>'.$const_no;
if($const_type=="PC" && $st_code=='' &&  $const_no=='') {
$gettotalfinalbyeci =DB::table('expenditure_reports')
->whereNotNull('date_of_receipt_eci')
->where(function($query) {
 $query->whereNull('final_action');
  $query->orwhere('final_action', '=','');
   })
->count();
}elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
$gettotalfinalbyeci =DB::table('expenditure_reports')
->where('ST_CODE',$st_code)
->whereNotNull('date_of_receipt_eci')
->where(function($query) {
 $query->whereNull('final_action');
  $query->orwhere('final_action', '=','');
   })  ->count();
}elseif($const_type=="PC" && $st_code!='' && $const_no!=''){
$gettotalfinalbyeci =DB::table('expenditure_reports')
->where('ST_CODE',$st_code)
->where('constituency_no',$const_no)
->whereNotNull('date_of_receipt_eci')
->where(function($query) {
$query->whereNull('final_action');
$query->orwhere('final_action', '=','');
  })   
   ->count();
} 
    // dd(DB::getQueryLog());
    return $gettotalfinalbyeci;	
  }
  
   //By Niraj For getting finalize scrutiny report by ECI count date 14-06-19
function gettotalCompletedbyEci($const_type,$st_code='',$const_no='')
{ 	DB::enableQueryLog(); $gettotalCompletedbyEci=0;
  if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
if($const_type=="PC" && $st_code==''  &&  $const_no=='') {
  $gettotalCompletedbyEci =DB::table('expenditure_reports')
   ->where('finalized_status','1')
  ->where('final_by_ro','1')
  ->where('final_by_eci','1')
  ->where(function($q) {
    $q->where('final_action', 'Closed')
     // ->orWhere('final_action','Disqualified')
      ->orWhere('final_action', 'Case Dropped');
    })
 // ->where('final_action','Closed')
  ->count();
  }elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
    $gettotalCompletedbyEci =DB::table('expenditure_reports')
   ->where('ST_CODE',$st_code)
    ->where('finalized_status','1')
   ->where('final_by_ro','1')
   ->where('final_by_eci','1')
   ->where(function($q) {
    $q->where('final_action', 'Closed')
    //  ->orWhere('final_action','Disqualified')
      ->orWhere('final_action', 'Case Dropped');
    })
   ->count();
  }elseif($const_type=="PC" && $st_code!='' && $const_no!=''){
    $gettotalCompletedbyEci =DB::table('expenditure_reports')
   ->where('ST_CODE',$st_code)
   ->where('constituency_no',$const_no)
    ->where('finalized_status','1')
   ->where('final_by_ro','1')
   ->where('final_by_eci','1')
   ->where(function($q) {
    $q->where('final_action', 'Closed')
      //->orWhere('final_action','Disqualified')
      ->orWhere('final_action', 'Case Dropped');
    })
   ->count();
   }  
   // dd(DB::getQueryLog());
    return $gettotalCompletedbyEci;	
}

 //By Niraj For getting disqualified by ECI count date 06-09-2019
 function gettotalDisqualifiedbyEci($const_type,$st_code='',$const_no='')
 { 	DB::enableQueryLog(); $gettotalCompletedbyEci=0;
   if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
 if($const_type=="PC" && $st_code==''  &&  $const_no=='') {
   $gettotalDisqualifiedbyEci =DB::table('expenditure_reports')
    ->where('finalized_status','1')
   ->where('final_by_ro','1')
   ->where('final_by_eci','1')
   ->where('final_action', 'Disqualified')
   ->count();
   }elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
     $gettotalDisqualifiedbyEci =DB::table('expenditure_reports')
    ->where('ST_CODE',$st_code)
	 ->where('finalized_status','1')
    ->where('final_by_ro','1')
    ->where('final_by_eci','1')
    ->where('final_action', 'Disqualified')
    ->count();
   }elseif($const_type=="PC" && $st_code!='' && $const_no!=''){
     $gettotalDisqualifiedbyEci =DB::table('expenditure_reports')
    ->where('ST_CODE',$st_code)
    ->where('constituency_no',$const_no)
	 ->where('finalized_status','1')
    ->where('final_by_ro','1')
    ->where('final_by_eci','1')
    ->where('final_action', 'Disqualified')
    ->count();
    }  
    // dd(DB::getQueryLog());
     return $gettotalDisqualifiedbyEci;	
 }

//By Niraj For getting Notice data by ECI entry count date 23-06-19
function gettotalnoticeatCEO($const_type,$st_code='',$const_no='')
{ 	DB::enableQueryLog();
  if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
 // echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no; die;
 if($const_type=="PC" && $st_code=='' &&  $const_no=='') {
    $gettotalnoticeatCEO =DB::table('expenditure_reports')
    ->whereNotNull('date_of_issuance_notice')
    ->where('final_by_ceo','0')
    ->where('final_by_ro','0')
    ->where('final_by_eci', '<>', '1')
    ->where(function($q) {
     $q->where('final_action', 'Notice Issued')
       ->orWhere('final_action','Reply Issued')
       ->orWhere('final_action', 'Hearing Done');
     })
     ->count();
    }elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
      $gettotalnoticeatCEO =DB::table('expenditure_reports')
      ->where('ST_CODE',$st_code)
	    ->whereNotNull('date_of_issuance_notice')
      ->where('final_by_ceo','0')
      ->where('final_by_ro','0')
      ->where('final_by_eci', '<>', '1')
      ->where(function($q) {
     $q->where('final_action', 'Notice Issued')
       ->orWhere('final_action','Reply Issued')
       ->orWhere('final_action', 'Hearing Done');
     })
    ->count();
    }elseif($const_type=="PC" && $st_code!='' && $const_no!=''){
      $gettotalnoticeatCEO =DB::table('expenditure_reports')
      ->where('ST_CODE',$st_code)
      ->where('constituency_no',$const_no)
      ->whereNotNull('date_of_issuance_notice')
      ->where('final_by_ceo','0')
      ->where('final_by_ro','0')
      ->where('final_by_eci', '<>', '1')
      ->where(function($q) {
       $q->where('final_action', 'Notice Issued')
         ->orWhere('final_action','Reply Issued')
         ->orWhere('final_action', 'Hearing Done');
       })
       ->count();
    } 
    //dd(DB::getQueryLog());
    return $gettotalnoticeatCEO;	
}
//By Niraj For getting Notice data by ECI entry count date 23-06-19
function gettotalnoticeatDEO($const_type,$st_code='',$const_no='')
{ 	DB::enableQueryLog();
  if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
 // echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no; die;
 if($const_type=="PC" && $st_code=='' &&  $const_no=='') {
    $gettotalnoticeatDEO =DB::table('expenditure_reports')
    ->whereNotNull('date_sending_notice_service_to_deo')
    ->where('final_by_ro','0')
    ->where('final_by_ceo','0')
    ->where('final_by_eci', '<>', '1')
    ->where(function($q) {
     $q->where('final_action', 'Notice Issued')
       ->orWhere('final_action','Reply Issued')
       ->orWhere('final_action', 'Hearing Done');
     })
     ->count();
    }elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
      $gettotalnoticeatDEO =DB::table('expenditure_reports')
      ->where('ST_CODE',$st_code)
	    ->whereNotNull('date_sending_notice_service_to_deo')
    ->where('final_by_ro','0')
    ->where('final_by_ceo','0')
    ->where('final_by_eci', '<>', '1')
    ->where(function($q) {
     $q->where('final_action', 'Notice Issued')
       ->orWhere('final_action','Reply Issued')
       ->orWhere('final_action', 'Hearing Done');
     })
    ->count();
    }elseif($const_type=="PC" && $st_code!='' && $const_no!=''){
      $gettotalnoticeatDEO =DB::table('expenditure_reports')
      ->where('ST_CODE',$st_code)
      ->where('constituency_no',$const_no)
      ->whereNotNull('date_sending_notice_service_to_deo')
      ->where('final_by_ro','0')
      ->where('final_by_ceo','0')
      ->where('final_by_eci', '<>', '1')
      ->where(function($q) {
       $q->where('final_action', 'Notice Issued')
         ->orWhere('final_action','Reply Issued')
         ->orWhere('final_action', 'Hearing Done');
       })
      ->count();
    } 
 // dd(DB::getQueryLog());
    return $gettotalnoticeatDEO;	
}

//By Niraj For gettotalfinalbyDEO count date 01-07-19
 function gettotalfinalbyDEO($const_type,$st_code='',$const_no='')
 { 	DB::enableQueryLog();
 // echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no;
 if($const_no=='0') $const_no=''; if($st_code=='0') $st_code='';
 if($const_type=="PC" && $st_code==''  &&  $const_no=='') {
   $gettotalfinalbyDEO =DB::table('expenditure_reports')
    ->where('finalized_status','1')
   ->where('final_by_ro','1')
   ->whereNotNull('date_of_sending_deo')
   ->count();
   } elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
     $gettotalfinalbyDEO =DB::table('expenditure_reports')
     ->where('ST_CODE',$st_code)
     ->where('finalized_status','1')
     ->where('final_by_ro','1')
      ->whereNotNull('date_of_sending_deo')
     ->count();
     }elseif($const_type=="PC" && $st_code!='' &&  $const_no!=''){
     $gettotalfinalbyDEO =DB::table('expenditure_reports')
     ->where('ST_CODE',$st_code)
     ->where('constituency_no',$const_no)
     ->where('finalized_status','1')
     ->where('final_by_ro','1')
     ->whereNotNull('date_of_sending_deo')
     ->count();
   }
     //dd(DB::getQueryLog());
     return $gettotalfinalbyDEO;	
 }


 
//By Niraj For gettotalfinalbyDEO count date 01-07-19
function gettotalfundedparty($const_type,$st_code='',$const_no='')
{ 	DB::enableQueryLog();
// echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no;
if($const_no=='0') $const_no=''; if($st_code=='0') $st_code='';
if($const_type=="PC" && $st_code==''  &&  $const_no=='') {
  $gettotalfinalbyDEO =DB::table('expenditure_reports')
   ->where('finalized_status','1')
  ->where('final_by_ro','1')
  ->whereNotNull('date_of_sending_deo')
  ->count();
  } elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
    $gettotalfinalbyDEO =DB::table('expenditure_reports')
    ->where('ST_CODE',$st_code)
    ->where('finalized_status','1')
    ->where('final_by_ro','1')
     ->whereNotNull('date_of_sending_deo')
    ->count();
    }elseif($const_type=="PC" && $st_code!='' &&  $const_no!=''){
    $gettotalfinalbyDEO =DB::table('expenditure_reports')
    ->where('ST_CODE',$st_code)
    ->where('constituency_no',$const_no)
    ->where('finalized_status','1')
    ->where('final_by_ro','1')
    ->whereNotNull('date_of_sending_deo')
    ->count();
  }
    //dd(DB::getQueryLog());
    return $gettotalfinalbyDEO;	
}

// for getting total return candidates on 2019 PC
function getTotalelectedcandbystate($const_type, $st_code = '') {
        $getTotalelectedcandbystate = DB::table('winning_leading_candidate')
                ->Where('constituency_type', '=', 'PC')
                ->Where('st_code', '=', $st_code)
                ->count();
        return $getTotalelectedcandbystate;
    }

  ###########################end status dashboard#########################

  //manoj start
  function gettotalState($const_type='')
    { 
        $data =  DB::select("SELECT
                              
                              S.ST_NAME AS state_name,
                              S.ST_CODE AS state_code
                            FROM
                              m_state S
                            GROUP BY
                              S.ST_CODE");
                    return $data;
    }
function getTotalContestingcandidateeci($const_type,$st_code=''){
   $totalContestedCandidate = DB::table('candidate_nomination_detail')
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
          ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
          ->where('candidate_nomination_detail.st_code','=',$st_code)
            
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
          ->count();
          return $totalContestedCandidate;
}
  function gettotaldataentryStartdataeci($const_type,$st_code='')
    { 

        $data =  DB::select("SELECT
                              IFNULL(COUNT(R.ST_CODE),
                              0) AS total,
                              S.ST_NAME AS state_name,
                              S.ST_CODE AS state_code
                            FROM
                              m_state S
                            LEFT JOIN
                              `expenditure_reports` R ON R.ST_CODE = S.ST_CODE
                              where  S.`ST_CODE` = '$st_code'
                            GROUP BY
                              S.ST_CODE");
                    return $data;
    }
    public function gettotaldataentryFinaldataeci($const_type='',$st_code=''){

       $data =  DB::select("SELECT
                              IFNULL(COUNT(R.ST_CODE),
                              0) AS total,
                              S.ST_NAME AS state_name,
                              S.ST_CODE AS state_code
                            FROM
                              m_state S
                            LEFT JOIN
                              `expenditure_reports` R ON R.ST_CODE = S.ST_CODE
                              where  S.`ST_CODE` = '$st_code'
                              AND R.finalized_status = '1'
                            GROUP BY
                              S.ST_CODE");
       return $data;

  }
   public function gettotallogedaccountdataeci($const_type='',$st_code=''){

        $data = DB::select("SELECT
                              IFNULL(COUNT(R.ST_CODE),
                              0) AS total,
                              S.ST_NAME AS state_name,
                              S.ST_CODE AS state_code
                            FROM
                              m_state S
                            LEFT JOIN
                              `expenditure_reports` R ON R.ST_CODE = S.ST_CODE
                              where  S.`ST_CODE` = '$st_code'
                              AND R.candidate_lodged_acct = 'Yes'
                            GROUP BY
                              S.ST_CODE");
        return $data;

       
  }
     public function gettotalNotinTimeeci($const_type='',$st_code=''){

        
        $data = DB::select("SELECT
                              IFNULL(COUNT(R.ST_CODE),
                              0) AS total,
                              S.ST_NAME AS state_name,
                              S.ST_CODE AS state_code
                            FROM
                              m_state S
                            LEFT JOIN
                              `expenditure_reports` R ON R.ST_CODE = S.ST_CODE
                              where  S.`ST_CODE` = '$st_code'
                              AND R.candidate_lodged_acct = 'No'
                            GROUP BY
                              S.ST_CODE");
        return $data;

       
  }
  public function gettotalformatedefectsdata($const_type='',$st_code=''){
    $data =DB::select("SELECT
                              IFNULL(COUNT(R.ST_CODE),
                              0) AS total,
                              S.ST_NAME AS state_name,
                              S.ST_CODE AS state_code
                            FROM
                              m_state S
                            LEFT JOIN
                              `expenditure_reports` R ON R.ST_CODE = S.ST_CODE
                              where  S.`ST_CODE` = '$st_code'
                              AND R.rp_act = 'No'
                            GROUP BY
                              S.ST_CODE");

        return $data;

  }
  function gettotalexpenseUnderStateddataeci($const_type,$st_code='')
 { 
  $data = DB::select("SELECT
                              IFNULL(COUNT(R.ST_CODE),
                              0) AS total,
                              S.ST_NAME AS state_name,
                              S.ST_CODE AS state_code
                            FROM
                              m_state S
                            LEFT JOIN
                              `expenditure_understated` R ON R.ST_CODE = S.ST_CODE
                              where  S.`ST_CODE` = '$st_code'
                              AND R.page_no_observation = 'Yes'
                            GROUP BY
                              S.ST_CODE");
              return $data;
      
  }
    function gettotalPartyfunddataeci($const_type,$st_code='')
   {
    $data = DB::select("SELECT
                              IFNULL(COUNT(R.ST_CODE),
                              0) AS total,
                              S.ST_NAME AS state_name,
                              S.ST_CODE AS state_code,
                              IFNULL(
                                  SUM(
                                    R.political_fund_cash + R.political_fund_checque + R.political_fund_kind
                                  ),
                                  0
                                ) AS total_partyfund
                            FROM
                              m_state S
                            LEFT JOIN
                              `expenditure_fund_parties` R ON R.ST_CODE = S.ST_CODE
                              where  S.`ST_CODE` = '$st_code'                               
                            GROUP BY
                              S.ST_CODE");
    return $data;


              }
  
    function gettotalOtherSourcesfunddata($const_type,$st_code='',$const_no='')
    { 
      $data =DB::select("SELECT
                              IFNULL(COUNT(R.ST_CODE),
                              0) AS total,
                              S.ST_NAME AS state_name,
                              S.ST_CODE AS state_code,
                              IFNULL(SUM(R.other_source_amount),0) AS total_otherSourcesfund
                            FROM
                              m_state S
                            LEFT JOIN
                              `expenditure_fund_source` R ON R.ST_CODE = S.ST_CODE
                              where  S.`ST_CODE` = '$st_code'                               
                            GROUP BY
                              S.ST_CODE");
         
       return $data;
    }
    function getTotalContestingcandidateecibystate($const_type, $st_code = '') {
        $totalContestedCandidate = DB::table('candidate_nomination_detail')
                ->select('m_state.CCODE as state_id', 'm_state.ST_CODE as state_code', 'm_state.ST_NAME as state_name', DB::raw('count(*) as total'))
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                ->join('m_state', 'candidate_nomination_detail.st_code', '=', 'm_state.ST_CODE')
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->groupBy('m_state.ST_CODE')
                ->get();
        return $totalContestedCandidate;
    }

    function getTotalreprotcandidateecibystate($const_type, $st_code = '') {
        $DataentryStartCandList = DB::table('expenditure_reports')
                ->select('m_state.CCODE as state_id', 'm_state.ST_CODE as state_code', 'm_state.ST_NAME as state_name', DB::raw('count(expenditure_reports.candidate_id) as total'))
                ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->join('m_state', 'candidate_nomination_detail.st_code', '=', 'm_state.ST_CODE')
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->Where('m_state.ST_CODE', '=', $st_code)
                ->groupBy('m_state.CCODE')
                ->get();
        return $DataentryStartCandList;
    }

    function getTotalpartialcandidateecibystate($const_type, $st_code = '') {
        $partialCandList = DB::table('expenditure_reports')
                ->select('m_state.CCODE as state_id', 'm_state.ST_CODE as state_code', 'm_state.ST_NAME as state_name', DB::raw('count(expenditure_reports.candidate_id) as total'))
                ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->join('m_state', 'candidate_nomination_detail.st_code', '=', 'm_state.ST_CODE')
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->where('expenditure_reports.finalized_status', '0')
                ->Where('m_state.ST_CODE', '=', $st_code)
                ->groupBy('m_state.CCODE')
                ->get();
        return $partialCandList;
    }

    public function gettotalDefaulterreports($const_type, $st_code = '') {
        $defaulterCandList = DB::table('expenditure_understated')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->join('m_state', 'candidate_nomination_detail.st_code', '=', 'm_state.ST_CODE')
                ->select('m_state.CCODE as state_id', 'm_state.ST_CODE as state_code', 'm_state.ST_NAME as state_name', DB::raw('count(expenditure_understated.candidate_id) as total'),DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'), DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                 
                ->having('totalobseramnt', '<=', 'totalcandamnt')                            
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->Where('m_state.ST_CODE', '=', $st_code)
                ->groupBy('m_state.CCODE')
                ->get();
        return $defaulterCandList;
    }
   // manoj end
   
    #########Functions For PC May-2014 Election by Niraj Date:26-11-2019########################
    //By Niraj For gettotalfinalbyDEO count date 27-11-19
 function gettotalfinalbyDEO2014($const_type,$st_code='',$const_no='')
 {  DB::enableQueryLog();
 // echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no;
 if($const_no=='0') $const_no=''; if($st_code=='0') $st_code='';
 if($const_type=="PC" && $st_code==''  &&  $const_no=='') {
   $gettotalfinalbyDEO =DB::table('expenditure_report')
    ->where('RPT_FINAL','F')
   ->count();
   } elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
     $gettotalfinalbyDEO =DB::table('expenditure_report')
     ->where('ST_CODE',$st_code)
     ->where('RPT_FINAL','F')
     ->count();
     }elseif($const_type=="PC" && $st_code!='' &&  $const_no!=''){
     $gettotalfinalbyDEO =DB::table('expenditure_report')
     ->where('ST_CODE',$st_code)
     ->where('PC_NO',$const_no)
     ->where('RPT_FINAL','F')
     ->count();
   }
     //dd(DB::getQueryLog());
     return $gettotalfinalbyDEO;  
 }

function gettotaldataentryStart2014($const_type,$st_code='',$const_no='')
 {  DB::enableQueryLog();
 // echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no;
 if($const_no=='0') $const_no=''; if($st_code=='0') $st_code='';
 if($const_type=="PC" && $st_code==''  &&  $const_no=='') {
   $gettotaldataentryStart2014 =DB::table('expenditure_report')
   ->count();
   } elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
     $gettotaldataentryStart2014 =DB::table('expenditure_report')
     ->where('ST_CODE',$st_code)
     ->count();
     }elseif($const_type=="PC" && $st_code!='' &&  $const_no!=''){
     $gettotaldataentryStart2014 =DB::table('expenditure_report')
     ->where('ST_CODE',$st_code)
     ->where('PC_NO',$const_no)
     ->count();
   }
     //dd(DB::getQueryLog());
     return $gettotaldataentryStart2014;  
 }

 function gettotalnoticeatCEO2014($const_type,$st_code='',$const_no='')
 {  DB::enableQueryLog();
 // echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no;
 if($const_no=='0') $const_no=''; if($st_code=='0') $st_code='';
 if($const_type=="PC" && $st_code==''  &&  $const_no=='') {
   $gettotalnoticeatCEO2014 =DB::table('vw_vw_d_faultycandstatus')
    ->where('Status','NE')
   ->count();
   } elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
     $gettotalnoticeatCEO2014 =DB::table('vw_vw_d_faultycandstatus')
     ->where('ST_CODE',$st_code)
      ->where('Status','NE')
     ->count();
     }elseif($const_type=="PC" && $st_code!='' &&  $const_no!=''){
     $gettotalnoticeatCEO2014 =DB::table('vw_vw_d_faultycandstatus')
     ->where('ST_CODE',$st_code)
     ->where('PC_NO',$const_no)
      ->where('Status','NE')
     ->count();
   }
     //dd(DB::getQueryLog());
     return $gettotalnoticeatCEO2014;  
 }
  
// for getting total return candidates on 2014 PC by Niraj 21-01-2020
 function getTotalelectedcandbystate2014($const_type, $st_code = '',$const_no='') {
  if($const_no=='0') $const_no=''; if($st_code=='0') $st_code='';
  if($const_type=="PC" && $st_code==''  &&  $const_no=='') {
   $getTotalelectedcandbystate2014 =DB::table('vw_vw_pcwinner')
   ->count();
    } elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
     $getTotalelectedcandbystate2014 =DB::table('vw_vw_pcwinner')
     ->Where('st_code', '=', $st_code)
     ->count();
     }elseif($const_type=="PC" && $st_code!='' &&  $const_no!=''){
     $getTotalelectedcandbystate2014 =DB::table('vw_vw_pcwinner')
     ->where('ST_CODE',$st_code)
     ->where('PC_NO',$const_no)
     ->count();
    }
        return $getTotalelectedcandbystate2014;
    }
  #########end Functions for PC May-2014 #####################################################


  
 }