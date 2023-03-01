<?php
    namespace App\models\Expenditure;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Auth;
    use DB;
class ExpenditureModel extends Model
{
   
 //By Niraj For getting start data entry count date 8-5-19
  function gettotaldataentryStart($const_type,$st_code='',$const_no='')
  { 	DB::enableQueryLog();
  //echo $st_code.'pc==>'.$const_no;
  if($const_type=="PC" && $const_no!='0') {
    /*$gettotaldataentryStart =DB::table('expenditure_reports')->where('ST_CODE',$st_code)->where('constituency_no',$const_no)->count();*/
	 $gettotaldataentryStart = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $const_no)
                            // ->where('expenditure_reports.finalized_status','=','1') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
	
    } elseif($const_type=="PC" && $const_no=='0'){
      /*$gettotaldataentryStart =DB::table('expenditure_reports')->where('ST_CODE',$st_code)
      ->count();*/
	  
	  $gettotaldataentryStart = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no', '=', $const_no)
                            // ->where('expenditure_reports.finalized_status','=','1') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
    }
   // dd(DB::getQueryLog());
      return $gettotaldataentryStart;	
  }


  //By Niraj For getting finalize data entry count date 8-5-19
  function gettotaldataentryFinal($const_type,$st_code='',$const_no='')
  { 	DB::enableQueryLog();
  if($const_type=="PC" && $const_no!='0') {
    /*$gettotaldataentryFinal =DB::table('expenditure_reports')->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
    ->where('finalized_status','1')->count();*/
	 $gettotaldataentryFinal = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $const_no)
                            ->where('expenditure_reports.finalized_status','=','1') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
	
    } elseif($const_type=="PC" && $const_no=='0'){
     /* $gettotaldataentryFinal =DB::table('expenditure_reports')->where('ST_CODE',$st_code)
      ->where('finalized_status','1')->count();*/
	  $gettotaldataentryFinal = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.finalized_status','=','1') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
    }
   //dd(DB::getQueryLog());
      return $gettotaldataentryFinal;	
  }

  //By Niraj For getting account loged count date 8-5-19
  function gettotallogedAccount($const_type,$st_code='',$const_no='')
  { 	DB::enableQueryLog();
  if($const_type=="PC" && $const_no!='0') {
    /*$gettotallogedAccount =DB::table('expenditure_reports')->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
    ->where('candidate_lodged_acct','Yes')->count();*/
	$gettotallogedAccount = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $const_no)
                            ->where('expenditure_reports.candidate_lodged_acct','=','Yes') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
	
    }elseif($const_type=="PC" && $const_no=='0'){
      /*$gettotallogedAccount =DB::table('expenditure_reports')->where('ST_CODE',$st_code)
      ->where('candidate_lodged_acct','Yes')->count();*/
	  $gettotallogedAccount = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.candidate_lodged_acct','=','Yes') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
	  
    }
   //dd(DB::getQueryLog());
      return $gettotallogedAccount;	
  }

  //By Niraj For getting account loged count date 8-5-19
  function gettotalNotinTime($const_type,$st_code='',$const_no='')
  { 	DB::enableQueryLog();
  if($const_type=="PC" && $const_no!='0') {
   /* $gettotalNotinTime =DB::table('expenditure_reports')->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
    ->where('account_lodged_time','No')->count();*/
	$gettotalNotinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $const_no)
                            ->where('expenditure_reports.account_lodged_time','=','No') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
    }elseif($const_type=="PC" && $const_no=='0'){
      /*$gettotalNotinTime =DB::table('expenditure_reports')->where('ST_CODE',$st_code)
      ->where('account_lodged_time','No')->count();*/
	  $gettotalNotinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.account_lodged_time','=','No') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
    }
     //dd(DB::getQueryLog());
      return $gettotalNotinTime;	
  }


  //By Niraj For Defects in format count date 8-5-19
  function gettotalDefectformats($const_type,$st_code='',$const_no='')
  { 	//DB::enableQueryLog();
  if($const_type=="PC" && $const_no!='0') {
   /* $gettotalDefectformats =DB::table('expenditure_reports')
    ->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
	 ->where('rp_act','No')
    ->count();*/
	$gettotalDefectformats = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $const_no)
                            ->where('expenditure_reports.rp_act','=','No') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
    } elseif($const_type=="PC" && $const_no=='0'){
      /*$gettotalDefectformats =DB::table('expenditure_reports')
      ->where('ST_CODE',$st_code)
       ->where('rp_act','No')
      ->count();*/
	  $gettotalDefectformats = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.rp_act','=','No') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
    } 
   // dd(DB::getQueryLog());
      return $gettotalDefectformats;	
  }

 //By Niraj For Expense Understated count date 8-5-19
 function gettotalexpenseUnderStated($const_type,$st_code='',$const_no='')
 { //	DB::enableQueryLog();
 if($const_type=="PC" && $const_no!='0') {
   $gettotalexpenseUnderStated =DB::table('expenditure_understated')->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
   ->where('page_no_observation','Yes')->count();
   } elseif($const_type=="PC" && $const_no=='0'){
    $gettotalexpenseUnderStated =DB::table('expenditure_understated')->where('ST_CODE',$st_code)
   ->where('page_no_observation','Yes')->count();
  } 
  elseif($const_type=="AC"){
   $gettotalexpenseUnderStated =DB::table('expenditure_understated')->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
   ->where('page_no_observation','Yes')->count(); 
   } 
  // dd(DB::getQueryLog());
     return $gettotalexpenseUnderStated;	
 }

 //By Niraj For party fund count date 8-5-19
 function gettotalPartyfund($const_type,$st_code='',$const_no='')
 { 	DB::enableQueryLog();
// echo $const_type.'st_code=>'.$st_code.'const_no=>'.$const_no;
 if($const_type=="PC" && $const_no!='0') {
   $gettotalPartyfund =DB::table('expenditure_fund_parties')
   ->select(DB::raw('IFNULL(SUM(political_fund_cash + political_fund_checque + political_fund_kind),0) AS total_partyfund'))
   ->where('ST_CODE',$st_code)->where('constituency_no',$const_no)->first();
   }elseif($const_type=="PC" && $const_no=='0'){
    $gettotalPartyfund =DB::table('expenditure_fund_parties')
   ->select(DB::raw('IFNULL(SUM(political_fund_cash + political_fund_checque + political_fund_kind),0) AS total_partyfund'))
   ->where('ST_CODE',$st_code)->first();
  }  elseif($const_type=="AC"){
    $gettotalPartyfund =DB::table('expenditure_fund_parties')
    ->select(DB::raw('IFNULL(SUM(political_fund_cash + political_fund_checque + political_fund_kind),0) AS total_partyfund'))
    ->where('ST_CODE',$st_code)->where('constituency_no',$const_no)->first();
    } 
  // dd(DB::getQueryLog());
     return $gettotalPartyfund;	
 }

  //By Niraj For party fund count date 8-5-19
  function gettotalOtherSourcesfund($const_type,$st_code='',$const_no='')
  { 	DB::enableQueryLog();
  if($const_type=="PC" && $const_no!='0') {
    $gettotalOtherSourcesfund =DB::table('expenditure_fund_source')
    ->select(DB::raw('IFNULL(SUM(other_source_amount),0) AS total_otherSourcesfund'))
    ->where('ST_CODE',$st_code)->where('constituency_no',$const_no)->first();
    }elseif($const_type=="PC" && $const_no=='0'){
      $gettotalOtherSourcesfund =DB::table('expenditure_fund_source')
      ->select(DB::raw('IFNULL(SUM(other_source_amount),0) AS total_otherSourcesfund'))
      ->where('ST_CODE',$st_code)->first();
    } 
   elseif($const_type=="AC"){
    $gettotalOtherSourcesfund =DB::table('expenditure_fund_source')
    ->select(DB::raw('IFNULL(SUM(other_source_amount),0) AS total_otherSourcesfund'))
    ->where('ST_CODE',$st_code)->where('constituency_no',$const_no)->first(); 
    } 
   // dd(DB::getQueryLog());
      return $gettotalOtherSourcesfund;	
  }
  function gettotalreturn($const_type,$st_code='',$const_no='',$return='')
  { 	

//DB::enableQueryLog();
  if($const_type=="PC" && !empty($st_code) && !empty($const_no)) {
   
	  $gettotalreturn = DB::table('expenditure_reports') 
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $const_no)                           
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                             ->where('expenditure_reports.return_status', '=', $return)
                             ->where('expenditure_reports.finalized_status', '=', '1')
                             ->where('expenditure_reports.final_by_ro', '=', '1')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
    } elseif($const_type=="PC" && !empty($st_code) && empty($const_no)){
     
	   $gettotalreturn = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)                        
                             
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.return_status', '=', $return) 	
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('expenditure_reports.final_by_ro', '=', '1')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
     }
     else{
      
	   $gettotalreturn = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                                  
                             
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.return_status', '=', $return) 	
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('expenditure_reports.final_by_ro', '=', '1')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
     }
      return $gettotalreturn;	
  }


 ####################Start Status Dashboard Function by Niraj 16-05-19##########################

        //By Niraj For getting start data entry count date 16-5-19
  function gettotalpartiallypending($const_type,$st_code='',$const_no='')
  { 	DB::enableQueryLog();
  //echo $st_code.'pc==>'.$const_no;
  if($const_type=="PC" && $const_no!='0') {
   /* $gettotaldataentryStart =DB::table('expenditure_reports')->where('ST_CODE',$st_code)
    ->where('constituency_no',$const_no)
    ->where('final_by_ro','1')
    ->whereNotNull('date_of_sending_deo')
    ->where(function($query) {
      $query->whereNull('date_of_receipt');
       $query->orwhere('date_of_receipt', '=','');
        })
      ->count();*/
	  $gettotaldataentryStart = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $const_no)
                            ->where('expenditure_reports.final_by_ro','=','1') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
							->whereNotNull('expenditure_reports.date_of_sending_deo') 
							->where(function($query) {
							  $query->whereNull('expenditure_reports.date_of_receipt');
							   $query->orwhere('expenditure_reports.date_of_receipt', '=','');
								})
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
    } elseif($const_type=="PC" && $const_no=='0'){
     /* $gettotaldataentryStart =DB::table('expenditure_reports')
      ->where('ST_CODE',$st_code)
      ->where('final_by_ro','1')
      ->whereNotNull('date_of_sending_deo')
      ->where(function($query) {
        $query->whereNull('date_of_receipt');
         $query->orwhere('date_of_receipt', '=','');
          })
      ->count();*/
	  $gettotaldataentryStart = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.final_by_ro','=','1') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
							->whereNotNull('expenditure_reports.date_of_sending_deo') 
							->where(function($query) {
							  $query->whereNull('expenditure_reports.date_of_receipt');
							   $query->orwhere('expenditure_reports.date_of_receipt', '=','');
								})
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
    }
    // dd(DB::getQueryLog());
      return $gettotaldataentryStart;	
  }

  function getdefaulter($const_type,$st_code='',$const_no='')
  {  	DB::enableQueryLog();
  //echo $st_code.'pc==>'.$const_no;
  if($const_type=="PC" && $const_no!='0') {
    $gettotaldataentryStart =DB::table('expenditure_understated')
    ->select(DB::raw('IFNULL(SUM(amt_as_per_observation),0) AS totalobseramnt'),
     DB::raw('IFNULL(SUM(amt_understated_by_candidate),0) AS totalcandamnt'))
     ->having('totalobseramnt','<=','totalcandamnt')
    ->where('ST_CODE',$st_code)
    ->where('constituency_no',$const_no)
    ->groupBy('candidate_id')
    ->get();
    } elseif($const_type=="PC" && $const_no=='0'){
      $gettotaldataentryStart =DB::table('expenditure_understated')
      ->select(DB::raw('IFNULL(SUM(amt_as_per_observation),0) AS totalobseramnt'),
      DB::raw('IFNULL(SUM(amt_understated_by_candidate),0) AS totalcandamnt'))
      ->having('totalobseramnt','<=','totalcandamnt')
      ->where('ST_CODE',$st_code)
      ->groupBy('candidate_id')
      ->get();
    }elseif($const_type=="AC" && $const_no!='0') {
      $gettotaldataentryStart =DB::table('expenditure_understated')
      ->select(DB::raw('IFNULL(SUM(amt_as_per_observation),0) AS totalobseramnt'),
      DB::raw('IFNULL(SUM(amt_understated_by_candidate),0) AS totalcandamnt'))
      ->having('totalobseramnt','<=','totalcandamnt')
      ->where('ST_CODE',$st_code)
      ->where('constituency_no',$const_no)
      ->groupBy('candidate_id')
      ->get();
      } elseif($const_type=="AC" && $const_no=='0'){
        $gettotaldataentryStart =DB::table('expenditure_understated')
        ->select(DB::raw('IFNULL(SUM(amt_as_per_observation),0) AS totalobseramnt'),
        DB::raw('IFNULL(SUM(amt_understated_by_candidate),0) AS totalcandamnt'))
        ->having('totalobseramnt','<=','totalcandamnt')
        ->where('ST_CODE',$st_code)
        ->groupBy('candidate_id')
        ->get();
      }
    //dd(DB::getQueryLog());
      return $gettotaldataentryStart;	
 }
 
  //By Niraj For getting finalize data entry by ceo count date 21-5-19
  function gettotalfinalbyceo($const_type,$st_code='',$const_no='')
  { 	DB::enableQueryLog();
  if($const_type=="PC" && $const_no!='0') {
    /*$gettotalfinalbyceo =DB::table('expenditure_reports')->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
     ->where('final_by_ceo','1')
     ->whereNotNull('date_of_receipt')
     ->whereNull('date_of_receipt_eci')
	->count();*/
	 $gettotalfinalbyceo = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $const_no)
                            ->where('expenditure_reports.final_by_ceo','=','1') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
							->whereNotNull('expenditure_reports.date_of_receipt') 
							->where(function($query) {
							  $query->whereNull('expenditure_reports.date_of_receipt_eci');
							   $query->orwhere('expenditure_reports.date_of_receipt_eci', '=','');
								})
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
    } elseif($const_type=="PC" && $const_no=='0'){
      /*$gettotalfinalbyceo =DB::table('expenditure_reports')->where('ST_CODE',$st_code)
      ->where('final_by_ceo','1')
      ->whereNotNull('date_of_receipt')
      ->whereNull('date_of_receipt_eci')
     ->count();*/
	  $gettotalfinalbyceo = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.final_by_ceo','=','1') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
							->whereNotNull('expenditure_reports.date_of_receipt') 
							->where(function($query) {
							  $query->whereNull('expenditure_reports.date_of_receipt_eci');
							   $query->orwhere('expenditure_reports.date_of_receipt_eci', '=','');
								})
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
    }
        //dd(DB::getQueryLog());
      return $gettotalfinalbyceo;	
  }

  //By Niraj For getting finalize data entry by ECI date 21-5-19
  function gettotalfinalbyeci($const_type,$st_code='',$const_no='')
  { 	DB::enableQueryLog();
  if($const_type=="PC" && $const_no!='0') {
   /* $gettotalfinalbyeci =DB::table('expenditure_reports')->where('ST_CODE',$st_code)->where('constituency_no',$const_no)
     ->where('final_by_eci','1')
     ->whereNotNull('date_of_receipt_eci')
     ->count();*/
	  $gettotalfinalbyeci = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $const_no)
                            ->where('expenditure_reports.final_by_eci','=','1') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
							->whereNotNull('expenditure_reports.date_of_receipt_eci') 
							->whereNull('expenditure_reports.final_action')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
    } elseif($const_type=="PC" && $const_no=='0'){
     /* $gettotalfinalbyeci =DB::table('expenditure_reports')->where('ST_CODE',$st_code)
      ->where('final_by_eci','1')
      ->whereNotNull('date_of_receipt_eci')
      ->count();*/
	   $gettotalfinalbyeci = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.final_by_eci','=','1') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
							->whereNotNull('expenditure_reports.date_of_receipt_eci') 
							->whereNull('expenditure_reports.final_action')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
     }
      return $gettotalfinalbyeci;	
  }
  
  //By Niraj For getting Notice data by ECI entry count date 09-07-2019
function gettotalnoticeatCEO($const_type,$st_code='',$const_no='')
{ 	DB::enableQueryLog();
  if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
 // echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no; die;
  if($const_type=="PC" &&  $const_no==''){
      $gettotalnoticeatCEO =DB::table('expenditure_reports')
      ->where('ST_CODE',$st_code)
	    ->whereNotNull('date_of_issuance_notice')
      ->where('final_by_ceo','0')
      ->where('final_by_ro','0')
      ->where(function($q) {
     $q->where('final_action', 'Notice Issued')
       ->orWhere('final_action','Reply Issued')
       ->orWhere('final_action', 'Hearing Done');
     })
    ->count();
    }elseif($const_type=="PC" && $const_no!=''){
      $gettotalnoticeatCEO =DB::table('expenditure_reports')
      ->where('ST_CODE',$st_code)
      ->where('constituency_no',$const_no)
      ->whereNotNull('date_of_issuance_notice')
      ->where('final_by_ceo','0')
      ->where('final_by_ro','0')
      ->where(function($q) {
       $q->where('final_action', 'Notice Issued')
         ->orWhere('final_action','Reply Issued')
         ->orWhere('final_action', 'Hearing Done');
       })
       ->count();
    } 
   // dd(DB::getQueryLog());
    return $gettotalnoticeatCEO;	
}

//By Niraj For getting Notice data by ECI entry count date 09-07-2019
function gettotalnoticeatDEO($const_type,$st_code='',$const_no='')
{ 	DB::enableQueryLog();
  if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
 // echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no; die;
 if($const_type=="PC" &&  $const_no==''){
      $gettotalnoticeatDEO =DB::table('expenditure_reports')
      ->where('ST_CODE',$st_code)
	    ->whereNotNull('date_sending_notice_service_to_deo')
    ->where('final_by_ro','0')
    ->where('final_by_ceo','0')
    ->where(function($q) {
     $q->where('final_action', 'Notice Issued')
       ->orWhere('final_action','Reply Issued')
       ->orWhere('final_action', 'Hearing Done');
     })
    ->count();
    }elseif($const_type=="PC"  && $const_no!=''){
      $gettotalnoticeatDEO =DB::table('expenditure_reports')
      ->where('ST_CODE',$st_code)
      ->where('constituency_no',$const_no)
      ->whereNotNull('date_sending_notice_service_to_deo')
      ->where('final_by_ro','0')
      ->where('final_by_ceo','0')
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
  ####################End Status Dashboard Function by Niraj 16-05-19##########################
  
// add by manoj graph start
 function gettotalpc($const_type='',$st_code='',$const_no='')
  {  
   DB::enableQueryLog();
     $data = [];
  if($const_type=="PC" && $const_no!='0') {
     
    } elseif($const_type=="PC" && $const_no=='0'){
      $data = DB::select("SELECT
                          P.PC_NAME AS pc_name,
                          P.PC_NO AS pc_no
                        FROM
                          m_pc P
                        LEFT JOIN
                          `expenditure_reports` R ON R.constituency_no = P.PC_NO
                        WHERE
                          P.`ST_CODE` = '$st_code'
                        GROUP BY
                          P.PC_NO");
    } 
   
      return $data; 
  }
  
function gettotaldataentryStartdata($const_type='',$st_code='',$const_no='')
  {  
   DB::enableQueryLog();
     $data = [];
  if($const_type=="PC" && $const_no!='0') {
     
    } elseif($const_type=="PC" && $const_no=='0'){
      $data = DB::select("SELECT
                      COUNT(R.constituency_no) AS total,
                      P.PC_NAME AS pc_name,
                      P.PC_NO as pc_no
                    FROM
                      m_pc P
                    LEFT JOIN
                      `expenditure_reports` R ON R.constituency_no = P.PC_NO
                    WHERE
                      P.`ST_CODE` = '$st_code'
                    GROUP BY
                      P.PC_NO");
    } 
   
      return $data; 
  }
  function gettotalContestedCandidate($const_type='',$st_code='',$const_no='')
  {  
    $totalContestedCandidate = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
            ->where('candidate_nomination_detail.st_code','=',$st_code)
             ->where('candidate_nomination_detail.pc_no','=',$const_no) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->count();
      return $totalContestedCandidate; 
  }
  public function gettotaldataentryFinaldata($const_type='',$st_code='',$const_no=''){

       $data =  DB::select("SELECT
                      P.PC_NO AS pc_no,
                      P.PC_NAME AS pc_name,
                      COUNT(P.PC_NO) AS total
                    FROM
                      m_pc P
                    INNER JOIN
                      `expenditure_reports` R ON R.constituency_no = P.PC_NO
                    WHERE
                      R.finalized_status = '1'
                      AND P.`ST_CODE` = '$st_code'
                       AND P.`PC_NO` = '$const_no'
                    GROUP BY
                      P.PC_NO");
       return $data;

  }
   public function gettotallogedaccountdata($const_type='',$st_code='',$const_no=''){

       $data =  DB::select("SELECT
                      P.PC_NO AS pc_no,
                      P.PC_NAME AS pc_name,
                      COUNT(P.PC_NO) AS total
                    FROM
                      m_pc P
                    INNER JOIN
                      `expenditure_reports` R ON R.constituency_no = P.PC_NO
                    WHERE
                      R.candidate_lodged_acct = 'Yes'
                      AND P.`ST_CODE` = '$st_code'
                       AND P.`PC_NO` = '$const_no'
                    GROUP BY
                      P.PC_NO");
       return $data;

  }
  public function gettotalformatedefectsdata($const_type='',$st_code='',$const_no=''){

       $data =  DB::select("SELECT
                      P.PC_NO AS pc_no,
                      P.PC_NAME AS pc_name,
                      COUNT(P.PC_NO) AS total
                    FROM
                      m_pc P
                    INNER JOIN
                      `expenditure_reports` R ON R.constituency_no = P.PC_NO
                    WHERE
                      R.rp_act = 'No'
                      AND P.`ST_CODE` = '$st_code'
                       AND P.`PC_NO` = '$const_no'
                    GROUP BY
                      P.PC_NO");
       return $data;

  }
  function gettotalexpenseUnderStateddata($const_type,$st_code='',$const_no='')
 { 
      $data =  DB::select("SELECT
                      P.PC_NO AS pc_no,
                      P.PC_NAME AS pc_name,
                      COUNT(P.PC_NO) AS total
                    FROM
                      m_pc P
                    INNER JOIN
                      `expenditure_understated` R ON R.constituency_no = P.PC_NO
                    WHERE
                      R.page_no_observation = 'Yes'
                      AND P.`ST_CODE` = '$st_code'
                       AND P.`PC_NO` = '$const_no'
                    GROUP BY
                      P.PC_NO");
       return $data;
      
  }
    function gettotalPartyfunddata($const_type,$st_code='',$const_no='')
   {
         $data =  DB::select("SELECT
                                P.PC_NO AS pc_no,
                                P.PC_NAME AS pc_name,
                                COUNT(P.PC_NO) AS total,
                                IFNULL(
                                  SUM(
                                    R.political_fund_cash + R.political_fund_checque + R.political_fund_kind
                                  ),
                                  0
                                ) AS total_partyfund
                              FROM
                                m_pc P
                              INNER JOIN
                                `expenditure_fund_parties` R ON R.constituency_no = P.PC_NO
                              WHERE
                                P.`ST_CODE` = '$st_code' AND P.`PC_NO` = '$const_no'
                              GROUP BY
                                P.PC_NO");
       return $data;
    }
  
    function gettotalOtherSourcesfunddata($const_type,$st_code='',$const_no='')
    { 
        $data =  DB::select("SELECT
                      P.PC_NO AS pc_no,
                      P.PC_NAME AS pc_name,
                      COUNT(P.PC_NO) AS total,
                     IFNULL(SUM(R.other_source_amount),0) AS total_otherSourcesfund
                    FROM
                      m_pc P
                    INNER JOIN
                      `expenditure_fund_source` R ON R.constituency_no = P.PC_NO
                    WHERE                       
                       P.`ST_CODE` = '$st_code'
                       AND P.`PC_NO` = '$const_no'
                    GROUP BY
                      P.PC_NO");
       return $data;
    }
       // for ceo level
    public function  getTotalcandidatereports($const_type,$st_code='',$const_no=''){
         $DataentryStartCandList = DB::table('expenditure_reports')
                        ->select('expenditure_reports.constituency_no as pc_no', DB::raw('count(*) as total'))
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $const_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->groupBy('expenditure_reports.constituency_no')
                        ->get();
         return $DataentryStartCandList;
    }
    public function getpartialTotalcandidatereports($const_type, $st_code = '', $const_no = '') {
        $partiallyCandList = DB::table('expenditure_reports')
                ->select('expenditure_reports.constituency_no as pc_no', DB::raw('count(*) as total'))
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->where('expenditure_reports.ST_CODE', '=', $st_code)
                ->where('expenditure_reports.constituency_no', '=', $const_no)
                ->where('expenditure_reports.finalized_status', '=', '0')
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->groupBy('expenditure_reports.candidate_id')
                ->get();
        return $partiallyCandList;
    }

    public function getTotalDefaultreports($const_type, $st_code = '', $const_no = '') {
        $defaulterCandList = DB::table('expenditure_understated')
                ->select('expenditure_reports.constituency_no as pc_no', DB::raw('count(*) as total'))
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at', DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'), DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                ->having('totalobseramnt', '<=', 'totalcandamnt')
                ->where('expenditure_understated.ST_CODE', '=', $st_code)
                ->where('expenditure_understated.constituency_no', '=', $const_no)
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->groupBy('expenditure_understated.candidate_id')
                ->get();
        return $defaulterCandList;
    }
// add by manoj graph end
  /*
    //
   public function Allcandidatelist($user,$status="all",$search='',$constituency='',$const_type)
    		{  
    			DB::enableQueryLog();
          if($const_type=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$constituency; 
                        }
        elseif($const_type=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$constituency;
            }
       // else 
      //  { $v=''; $m=''; }
    		 $a='1'; $a1='2';$a2='3';$a3='4'; $a4='5';$a5='6'; 
         
    		if($search=='' and $constituency=='') {
  			if($status=="all" || $status=="") {
    			$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
				  	->where('candidate_nomination_detail.st_code','=',$user->st_code) 
		    		->where(function($query1) use ($a,$a1,$a2,$a3,$a4,$a5){
                    		$query1->where('candidate_nomination_detail.application_status','=',$a)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a1)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a2)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a3)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a4)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a5);
              			})
			  		->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name')->limit(100)->get();  
    		   }
    		   else {  
    		   		$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
					
		    	->where('candidate_nomination_detail.st_code','=',$user->st_code) 
		    	->where('candidate_nomination_detail.application_status','=',$status)
		    	 ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name')->limit(100)->get();  
    		   }	
    		   }
    		elseif($constituency=='') {
    			$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
				   ->where('candidate_nomination_detail.st_code','=',$user->st_code) 
		    	 ->where('candidate_nomination_detail.qrcode','=',$search)
		    	 ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name')->limit(100)->get(); 
    		}
        elseif($search=='') {
          $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
           ->where('candidate_nomination_detail.st_code','=',$user->st_code)->where($v,'=',$m) 
           ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name')->limit(100)->get(); 
        }
    		   return $list;
    		 
    		}
     function electiondetailsbystatecode($st_code,$const='')
            {
              if($const=='')
              $rec =DB::table('m_election_details')->where('st_code',$st_code)->orderBy('const_no', 'ASC')->get();
              else
               $rec =DB::table('m_election_details')->where('st_code',$st_code)->where('const_no', $const)->get();  
              return $rec;
            }		

    
     function gettotalnominationcnt($const_type,$st_code,$const_no, $fromdate, $todate)
      		{   
      			DB::enableQueryLog();
      		if($const_type=="PC") {
      		  $rec =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('pc_no',$const_no)->whereBetween('date_of_submit', [$fromdate, $todate])->where('party_id', '!=' ,'1180')->get()->count();
      		  } 
      		  elseif($const_type=="AC"){
      		  $rec =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('ac_no',$const_no)->whereBetween('date_of_submit', [$fromdate, $todate])->where('party_id', '!=' ,'1180')->get()->count(); 
      		  } 
      		    //$query = DB::getQueryLog();
				//$query = $query;
				//dd($query);
              return $rec;	
      		}
    function Allcandidate_finaliselist($st_code)
          {  
           $rec =DB::table('candidate_finalized_ac')->where('st_code',$st_code)->get();
            return $rec;
          }
    function get_candidate_finalizeac($st_code,$ac_no,$actype)
          {
            $rec =DB::table('candidate_finalized_ac')->where('st_code',$st_code)->where('const_no',$ac_no)->where('const_type',$actype)->first();
            return $rec;
          }
     public function definalize_candidate_ac($st,$ac,$actype,$dat)
        {
         $udata = array('finalize'=>'0'); 
         DB::table('candidate_finalized_ac')->where('st_code',$st)->where('const_no',$ac)->where('const_type',$actype)->update($dat);
         if($actype=="AC") {$field="ac_no"; $val=$ac; } elseif($actype=="PC"){$field="pc_no"; $val=$ac; }

        DB::table('candidate_nomination_detail')->where('st_code',$st)->where($field,$val)->update($udata);
          
         return true;
        }
     public function public_affidavit_ac($st,$ac)
        {
         $udata = array('affidavit_public'=>'yes'); 
          DB::table('candidate_nomination_detail')->where('st_code',$st)->where('ac_no',$ac)->update($udata);
          return true;
        }*/

        public function GetScrutinyReportData($candidate_id)
        {
            return DB::select("SELECT ER.*,CPD.*,EFP.*,EUS.*,ER.candidate_id as candidate_id,NAC.title as nature_of_default_ac,ST.ST_NAME AS state,pc.PC_NAME as PC_NAME,CPD.cand_name as contensting_candiate,  PT.PARTYNAME from expenditure_reports as ER join candidate_personal_detail as CPD ON CPD.candidate_id=ER.candidate_id left join expenditure_fund_parties as EFP ON EFP.candidate_id=ER.candidate_id  left join expenditure_understates as EUS ON EUS.candidate_id=ER.candidate_id INNER JOIN m_state ST ON ST.ST_CODE = ER.ST_CODE JOIN m_pc as pc ON pc.PC_NO=ER.constituency_no and (pc.ST_CODE=ER.st_code) left join expenditure_nature_of_default_ac as NAC ON NAC.id=ER.nature_of_default_ac
                       INNER JOIN candidate_nomination_detail CN ON
                        CN.candidate_id = CPD.candidate_id
                    INNER JOIN m_party PT ON
                        PT.CCODE = CN.party_id

             where ER.candidate_id='$candidate_id' group By ER.candidate_id");
        }

   //function for getting total breaching count by niraj 23-01-2020

  function gettotalbreaching($const_type,$st_code='',$const_no='')
  {   DB::enableQueryLog();
    if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
    // echo $st_code.'cons_no'.$const_no; 
    if($const_type=="PC" && $st_code=='' && $const_no =='') {
      $query="SELECT COUNT(DISTINCT(candidate_id)) as breachcount  FROM expenditure_understated";
    }elseif($const_type=="PC" && $st_code!='' &&  $const_no==''){
     $query="SELECT COUNT(DISTINCT(candidate_id)) as breachcount FROM expenditure_understated where ST_CODE ='".$st_code."'";
    }elseif($const_type=="PC" && $st_code!='' && $const_no!=''){
      $query="SELECT COUNT(DISTINCT(candidate_id)) as breachcount FROM expenditure_understated where ST_CODE ='".$st_code."' AND constituency_no = '".$const_no."'";
    }
    $gettotalbreaching= (DB::select($query));
   //dd(DB::getQueryLog());
      return $gettotalbreaching; 
  }
//function modified by Niraj date 18-12-2019
public function GetScrutinyUnderExpByitemData($candidate_id)
{ 
DB::enableQueryLog();
	$query="SELECT candidate_id,date_understated,amt_as_per_observation,
	amt_as_per_candidate,amt_understated_by_candidate ,expenditure_type,page_no_observation,description FROM expenditure_understated WHERE candidate_id = '".$candidate_id."' GROUP BY candidate_id,date_understated, expenditure_type,amt_understated_by_candidate";
	return DB::select($query);
}


        public function GetScrutinysourecefundByitemData($candidate_id)
        {
        	 return DB::select("SELECT * FROM  expenditure_fund_source WHERE candidate_id = '$candidate_id'");

        }

        public function GetScrutinyUnderExpData($candidate_id)
        {
           return DB::select("SELECT * FROM  expenditure_understates as eu join expenditure_understated_masters as eum ON eum.id=eu.understated_type_id WHERE eu.candidate_id = '$candidate_id'");
        }


        public static function GetExpeditureData($roleId=null,$constituency=null,$stcode=null,$condtition=null)
          {
               $user = Auth::user();


               if($roleId=="18" || $roleId=="5"){
               return DB::select("SELECT  er.*,NAC.title as nature_of_default_ac,ST.ST_NAME AS state,pc.PC_NAME as PC_NAME,pc.PC_NO as PC_NO,cpd.cand_name as contensting_candiate FROM expenditure_reports AS er  INNER JOIN m_state ST ON
                                      ST.ST_CODE = er.ST_CODE JOIN m_pc as pc ON pc.PC_NO=er.constituency_no and (pc.ST_CODE=er.st_code) left join expenditure_nature_of_default_ac as NAC ON NAC.id=er.nature_of_default_ac JOIN candidate_personal_detail AS cpd ON cpd.candidate_id=er.candidate_id where er.constituency_no ='$constituency' and er.ST_CODE='$stcode' $condtition ORDER BY er.id desc");
               }
               elseif($roleId=="4")
               {

               
                return DB::select("SELECT  er.*,NAC.title as nature_of_default_ac,ST.ST_NAME AS state,pc.PC_NAME as PC_NAME,pc.PC_NO as PC_NO,cpd.cand_name as contensting_candiate FROM expenditure_reports AS er  LEFT JOIN m_state ST ON
                                      ST.ST_CODE = er.ST_CODE  JOIN m_pc as pc ON pc.PC_NO=er.constituency_no and (pc.ST_CODE=er.st_code)  LEFT join expenditure_nature_of_default_ac as NAC ON NAC.id=er.nature_of_default_ac JOIN candidate_personal_detail AS cpd ON cpd.candidate_id=er.candidate_id where er.ST_CODE='$stcode' $condtition ORDER BY er.id desc");
               }
               elseif($roleId=="28"){

                
                    return DB::select("SELECT  er.*,NAC.title as nature_of_default_ac,ST.ST_NAME AS state,pc.PC_NAME as PC_NAME,pc.PC_NO as PC_NO,cpd.cand_name as contensting_candiate FROM expenditure_reports AS er  INNER JOIN m_state ST ON
                                      ST.ST_CODE = er.ST_CODE JOIN m_pc as pc ON pc.PC_NO=er.constituency_no and (pc.ST_CODE=er.st_code)   left join expenditure_nature_of_default_ac as NAC ON NAC.id=er.nature_of_default_ac JOIN candidate_personal_detail AS cpd ON cpd.candidate_id=er.candidate_id where 1=1 $condtition ORDER BY er.id desc");
               }
               else
               {}
          }


          public function getunewserbyuserid_uid($uid=NULL)
              {  
            $data = DB::table('candidate_nomination_detail')->where('candidate_nomination_detail.candidate_id',$uid )->join("m_pc","m_pc.PC_NO","=","candidate_nomination_detail.pc_no")->join("candidate_personal_detail","candidate_personal_detail.candidate_id","=","candidate_nomination_detail.candidate_id")->first();
                          return $data;
              }

          public function getunewserbyuserid($uid=NULL,$roleID=NULL)
              {  

               if($roleID=="18" || $roleID=="5"){
                 $data = DB::table('officer_login')->where('id',$uid )->join("m_pc",function($join){
                          $join->on("m_pc.PC_NO","=","officer_login.pc_no")
                              ->on("m_pc.ST_CODE","=","officer_login.st_code");
                                })
                            ->first();
                          return $data;
                } 
              elseif ($roleID=="4") {
                  $data = DB::table('officer_login')->where('id',$uid )->join('m_state','m_state.ST_CODE','officer_login.st_code')->first();
                 return $data;
                }  
                elseif($roleID=="28")
                {
                   $data = DB::table('officer_login')->where('id',$uid )->first();
                   return $data;
                }
             
              }
            
           public function getunewserbyuserid_uid_ceo($uid=NULL)
           {  
              $data = DB::table('expenditure_reports')->where('expenditure_reports.candidate_id',$uid )->join('m_pc', function ($join) {
              $join->on("m_pc.PC_NO","=","expenditure_reports.constituency_no")
                ->on("m_pc.ST_CODE","=","expenditure_reports.ST_CODE");
              })->join('candidate_personal_detail',"candidate_personal_detail.candidate_id",'=',"expenditure_reports.candidate_id")->first();
                          return $data;
           } 

          public function GetExpeditureSingleData($candidate_id=NULL)
          {
             return DB::select("SELECT
                                      er.*,
                                      ND.title AS default_nature_text,
                                      CS.title AS current_status_text,
                                      ST.ST_NAME AS state,
                                      pc.PC_NAME AS PC_NAME,
                                      pc.PC_NO AS PC_NO,
                                      cpd.cand_name AS contensting_candiate
                                    FROM
                                      expenditure_reports AS er
                                    INNER JOIN
                                      m_state ST ON ST.ST_CODE = er.ST_CODE
                                    JOIN
                                      m_pc AS pc ON pc.PC_ID = er.constituency_no
                                    JOIN
                                      candidate_personal_detail AS cpd ON cpd.candidate_id = er.candidate_id
                                    LEFT JOIN
                                      expenditure_nature_of_default_ac ND ON ND.id = er.nature_of_default_ac
                                    LEFT JOIN
                                    expenditure_mis_current_sataus CS ON CS.id = er.current_status
                                    WHERE
                                      er.candidate_id = '$candidate_id'"); 
        }



          public static function singledata($id) {
            $data=  DB::select("SELECT  er.*,ST.ST_NAME AS state,pc.PC_NAME as PC_NAME,cpd.cand_name as contensting_candiate,NAC.title as nature_of_default_ac FROM expenditure_reports AS er  INNER JOIN m_state ST ON
                                      ST.ST_CODE = er.ST_CODE left join expenditure_nature_of_default_ac as NAC ON NAC.id=er.nature_of_default_ac JOIN m_pc as pc ON pc.PC_ID=er.constituency_no JOIN candidate_personal_detail AS cpd ON cpd.candidate_id=er.candidate_id where er.id='$id'"); 
            return $data;
    }

    function GetMasterEntry()
    {
 
     $list = DB::select('SELECT * FROM expenditure_master_entry as eme join m_state ON m_state.ST_CODE=eme.st_code order by id desc');
     return $list;
 
    }

 public function getcandidatetotalexpenditure($candidate_id=null)
    {
          $candidate_id = rtrim($candidate_id, ',');
        if(!empty($candidate_id))
        {
            $other_source_fund = DB::select("select sum(other_source_amount) as source_fund from expenditure_fund_source where candidate_id IN ($candidate_id) ");
            $party_fund = DB::select("select sum(political_fund_cash) as political_fund_cash,sum(political_fund_checque) as political_fund_checque,sum(political_fund_kind) as political_fund_kind from expenditure_fund_parties where candidate_id iN ($candidate_id) ");
            $political_fund_cash = !empty($party_fund[0]->political_fund_cash)?$party_fund[0]->political_fund_cash:0;
             $political_fund_checque = !empty($party_fund[0]->political_fund_checque)?$party_fund[0]->political_fund_checque:0;
              $political_fund_kind = !empty($party_fund[0]->political_fund_kind)?$party_fund[0]->political_fund_kind:0;

            $total_exp = $other_source_fund[0]->source_fund + $political_fund_cash + $political_fund_checque + $political_fund_kind;

            return $total_exp;
        }
        else
        {
          return 0;
        }
    }


    public function getpartyExp($candidate_id=null)
    {
      //echo $candidate_id;die;
        $candidate_id = rtrim($candidate_id, ',');
        if(!empty($candidate_id))
        {
            $other_source_fund = DB::select("select sum(other_source_amount) as source_fund from expenditure_fund_source where candidate_id IN ($candidate_id) ");

            $party_fund = DB::select("select sum(political_fund_cash) as political_fund_cash,sum(political_fund_checque) as political_fund_checque,sum(political_fund_kind) as political_fund_kind from expenditure_fund_parties where candidate_id IN ($candidate_id) ");
              $political_fund_cash = !empty($party_fund[0]->political_fund_cash)?$party_fund[0]->political_fund_cash:0;
              $political_fund_checque = !empty($party_fund[0]->political_fund_checque)?$party_fund[0]->political_fund_checque:0;
              $political_fund_kind = !empty($party_fund[0]->political_fund_kind)?$party_fund[0]->political_fund_kind:0;
              $total_exp = $other_source_fund[0]->source_fund + $political_fund_cash + $political_fund_checque + $political_fund_kind;
           // print_r($total_exp);die;

            return $total_exp;
        }
        else
        {
          return 0;
        }
    }

    public function getpartytotalexpenditure($party_id=null,$state=null,$pc=null)
    {
      if(!empty($state) && empty($pc)){
      $candidate_ids = DB::select("SELECT GROUP_CONCAT(DISTINCT(er.candidate_id)) as cand_ids FROM expenditure_reports as er join candidate_nomination_detail as cnd on cnd.candidate_id=er.candidate_id WHERE  er.st_code='$state' and cnd.application_status = '6' and cnd.party_id = '$party_id'  and cnd.finalaccepted = '1' and cnd.party_id <> 1180 and cnd.symbol_id <> 743");
      //print_r($candidate_ids);die;
      }
      elseif(!empty($state) && !empty($pc))
      {
      $candidate_ids = DB::select("SELECT GROUP_CONCAT(DISTINCT(er.candidate_id)) as cand_ids FROM expenditure_reports as er join candidate_nomination_detail as cnd on cnd.candidate_id=er.candidate_id WHERE  er.st_code='$state' and er.constituency_no='$pc' and cnd.party_id='$party_id' and cnd.application_status = '6' and cnd.party_id = '$party_id' and cnd.finalaccepted = '1' and cnd.party_id <> 1180 and cnd.symbol_id <> 743");
      }
      else{
      $candidate_ids = DB::select("SELECT GROUP_CONCAT(DISTINCT(er.candidate_id)) as cand_ids FROM expenditure_reports as er join candidate_nomination_detail as cnd on cnd.candidate_id=er.candidate_id WHERE cnd.application_status = '6' and cnd.party_id = '$party_id' and cnd.party_id <> 1180 and cnd.finalaccepted = '1' and cnd.party_id <> 743 ");
      }
       

      if(!empty($candidate_ids[0]->cand_ids)){
          $expenseTotal = $this->getpartyExp($candidate_ids[0]->cand_ids);
          return $expenseTotal;
        }
        else
        {
          return 0;
        }
    }
	
##############################Start Nationalpartyies fund  Report  By Niraj 19-08-2019###################################

public function getGrandTotalExp($candidate_id=null)
{
  //echo $candidate_id;die;
    $candidate_id = rtrim($candidate_id, ',');
    if(!empty($candidate_id))
    {
        $grand_total = DB::select("select sum(grand_total_election_exp_by_cadidate) as grand_total_exp from expenditure_reports where candidate_id IN ($candidate_id) ");
        $grand_total_exp = $grand_total[0]->grand_total_exp;
        return $grand_total_exp;
    }
    else
    {
      return 0;
    }
}

public function getOtherSourcesExp($candidate_id=null)
{
    $candidate_id = rtrim($candidate_id, ',');
    if(!empty($candidate_id))
    {
        $other_source_fund = DB::select("select sum(other_source_amount) as source_fund from expenditure_fund_source where candidate_id IN ($candidate_id) ");
        $total_others_exp = $other_source_fund[0]->source_fund;
        return $total_others_exp;
    }
    else
    {
      return 0;
    }
}

public function getPoliticalpartyExp($candidate_id=null)
{
  //echo $candidate_id;die;
    $candidate_id = rtrim($candidate_id, ',');
    if(!empty($candidate_id))
    {
        $party_fund = DB::select("select sum(political_fund_cash) as political_fund_cash,sum(political_fund_checque) as political_fund_checque,sum(political_fund_kind) as political_fund_kind from expenditure_fund_parties where candidate_id IN ($candidate_id) ");
        $political_fund_cash = !empty($party_fund[0]->political_fund_cash)?$party_fund[0]->political_fund_cash:0;
        $political_fund_checque = !empty($party_fund[0]->political_fund_checque)?$party_fund[0]->political_fund_checque:0;
        $political_fund_kind = !empty($party_fund[0]->political_fund_kind)?$party_fund[0]->political_fund_kind:0;
        $total_political_party_exp = $political_fund_cash + $political_fund_checque + $political_fund_kind;
       // print_r($total_political_party_exp);die;
        return $total_political_party_exp;
    }
    else
    {
      return 0;
    }
}
 
 public function getcandidatesbyparties($party_id=null,$state=null,$pc=null)
 { 	
   
  DB::enableQueryLog();
   if(!empty($state) && empty($pc)){
   $candidate_ids = DB::select("SELECT GROUP_CONCAT(DISTINCT(er.candidate_id)) as cand_ids FROM expenditure_reports as er join candidate_nomination_detail as cnd on cnd.candidate_id=er.candidate_id WHERE  er.st_code='$state' and cnd.application_status = '6' and cnd.party_id = '$party_id'  and cnd.finalaccepted = '1' and cnd.party_id <> 1180 and cnd.symbol_id <> 743");
   //print_r($candidate_ids);die;
   }
   elseif(!empty($state) && !empty($pc))
   {
   $candidate_ids = DB::select("SELECT GROUP_CONCAT(DISTINCT(er.candidate_id)) as cand_ids FROM expenditure_reports as er join candidate_nomination_detail as cnd on cnd.candidate_id=er.candidate_id WHERE  er.st_code='$state' and er.constituency_no='$pc' and cnd.party_id='$party_id' and cnd.application_status = '6' and cnd.party_id = '$party_id' and cnd.finalaccepted = '1' and cnd.party_id <> 1180 and cnd.symbol_id <> 743");
   }
   else{
   $candidate_ids = DB::select("SELECT GROUP_CONCAT(DISTINCT(er.candidate_id)) as cand_ids FROM expenditure_reports as er join candidate_nomination_detail as cnd on cnd.candidate_id=er.candidate_id WHERE cnd.application_status = '6' and cnd.party_id = '$party_id' and cnd.party_id <> 1180 and cnd.finalaccepted = '1' and cnd.party_id <> 743 ");
   }
 // dd(DB::getQueryLog()) ;
   if(!empty($candidate_ids[0]->cand_ids)){ 
        //$countRec = count(explode(',',$candidate_ids[0]->cand_ids));
      // $expenseTotal = $this->getpartyExp($candidate_ids[0]->cand_ids);
       return $candidate_ids[0]->cand_ids;
     }
     else
     {
       return 0;
     }
 }

 
	##############################End Nationalpartyies fund  Report  By Niraj 19-08-2019###################################


  
#################################Party & Candidate Wise Expenditure By Niraj 12-09-2019##############

//By Niraj For getpartywiseexpenditure count date 12-09-19
function gettotalcontestedparties($const_type,$st_code='',$const_no='')
{ 	DB::enableQueryLog();
// echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no;
if($const_no=='0') $const_no=''; if($st_code=='0') $st_code='';

$conditions="";
$totalparty=0;
if(!empty($st_code)){
$state = $st_code;
$conditions .=" and candidate_nomination_detail.st_code='$state' ";
}

if(!empty($const_no)){ 
$pc = $const_no;
$conditions .=" and candidate_nomination_detail.pc_no='$pc' ";
}

$partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail WHERE 1 $conditions");
  
if(!empty($partyids))
 {
  foreach ($partyids as  $value) {
    $partyID[] = $value->party_id;
  }
  $partyids = implode(',', $partyID);
 } 
 //print_r($partyids);die; 
  $partyids = !empty($partyids)?$partyids:0;           
  $partyids = rtrim(implode(',',array_unique(explode(',',$partyids))), ',');

 $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
 
 $totalparty=count($partylist);

    //dd(DB::getQueryLog());
    return $totalparty;	
}


//By Niraj For getpartywiseexpenditure count date 12-09-19
function partieswhichexpendisgetterthanzero($const_type,$st_code='',$const_no='')
{ 	DB::enableQueryLog();
// echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no;
if($const_no=='0') $const_no=''; if($st_code=='0') $st_code='';
$conditions="";

if(!empty($st_code)){
$st_code = $st_code;
$conditions .=" and candidate_nomination_detail.st_code='$st_code' ";
}

if(!empty($const_no)){ 
$const_no = $const_no;
$conditions .=" and candidate_nomination_detail.pc_no='$const_no' ";
}

$partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail WHERE 1 $conditions");
  
if(!empty($partyids))
 {
  foreach ($partyids as  $value) {
    $partyID[] = $value->party_id;
  }
  $partyids = implode(',', $partyID);
 } 
 //print_r($partyids);die; 
  $partyids = !empty($partyids)?$partyids:0;           
  $partyids = rtrim(implode(',',array_unique(explode(',',$partyids))), ',');

 $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
 $grandTotal = 0;
 $partyID=0;
 if(!empty($partylist)){
   foreach($partylist as $partylists){
    $totalexpen=$this->getpartytotalexpenditure($partylists->CCODE,$st_code,$const_no);
    if($totalexpen > 0) {
      $partyID++;
     // $grandTotal += $totalexpen; 
    //  $grandTotal += $partyID; 
    }
   }
 }
    //dd(DB::getQueryLog());
    return $partyID;	
}



//By Niraj For getpartywiseexpenditure count date 12-09-19
function getcontestedcandidate($const_type,$st_code='',$const_no='')
{ 	DB::enableQueryLog();
// echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no;
if($const_no=='0') $const_no=''; if($st_code=='0') $st_code='';
$conditions="";
$candID=0;
if(!empty($st_code)){
$st_code = $st_code;
$conditions .=" and cnd.st_code='$st_code' ";
}

if(!empty($const_no)){ 
$const_no = $const_no;
$conditions .=" and cnd.pc_no='$const_no' ";
}

//$candList = DB::select("select m_election_details.YEAR,m_election_details.ELECTION_TYPE,candidate_personal_detail.cand_hname,candidate_nomination_detail.pc_no,candidate_nomination_detail.st_code,candidate_nomination_detail.district_no,candidate_nomination_detail.party_id,candidate_personal_detail.cand_name,candidate_personal_detail.candidate_id, `expenditure_reports`.`finalized_status`, `expenditure_reports`.`updated_at` as `finalized_date`, `expenditure_reports`.`final_by_ro`, `expenditure_reports`.`date_of_declaration` from `candidate_nomination_detail` left join `candidate_personal_detail` on `candidate_nomination_detail`.`candidate_id` = `candidate_personal_detail`.`candidate_id` inner join `m_election_details` on `m_election_details`.`st_code` = `candidate_nomination_detail`.`st_code` and `m_election_details`.`CONST_NO` = `candidate_nomination_detail`.`pc_no` left join `expenditure_reports` on `expenditure_reports`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` where `candidate_nomination_detail`.`application_status` = 6 and `candidate_nomination_detail`.`party_id` <> 1180 and `candidate_nomination_detail`.`finalaccepted` = '1' and `m_election_details`.`CONST_TYPE` = 'PC' and expenditure_reports.date_of_declaration !='' $conditions order by candidate_personal_detail.cand_name desc");
 $candList = DB::select("select TEMP.YEAR,ELECTION_TYPE,cpd.cand_hname,TEMP.pc_no,TEMP.st_code,TEMP.district_no,
 TEMP.party_id,cpd.cand_name,cpd.candidate_id,TEMP.finalized_status,TEMP.finalized_date,TEMP.final_by_ro,
 TEMP.date_of_declaration
 from(
 select med.YEAR,med.ELECTION_TYPE,cnd.pc_no,
 cnd.st_code,cnd.district_no,cnd.candidate_id,
 cnd.party_id,er.finalized_status,
 er.updated_at as finalized_date, er.final_by_ro,
 er.date_of_declaration
 from candidate_nomination_detail cnd,
 m_election_details med ,expenditure_reports er
 where cnd.application_status = 6
 and cnd.party_id <> 1180
 and cnd.finalaccepted= 1 $conditions
 and med.CONST_TYPE = 'PC'
 and er.date_of_declaration !=''
 AND med.st_code = cnd.st_code
 and med.CONST_NO = cnd.pc_no
 and er.candidate_id =cnd.candidate_id
 )TEMP left join candidate_personal_detail cpd on TEMP.candidate_id = cpd.candidate_id
 group by TEMP.candidate_id
 order by cpd.cand_name desc");

$candID=count($candList);
    //dd(DB::getQueryLog());
    return $candID;	
}


//By Niraj For getpartywiseexpenditure count date 12-09-19
function candidatewhichexpendisgetterthanzero($const_type,$st_code='',$const_no='')
{ 	DB::enableQueryLog();
// echo 'const_type'.$const_type.'st_code'.$st_code.'const_no'.$const_no;
if($const_no=='0') $const_no=''; if($st_code=='0') $st_code='';
$conditions="";

if(!empty($st_code)){
$st_code = $st_code;
$conditions .=" and cnd.st_code='$st_code' ";
}

if(!empty($const_no)){ 
$const_no = $const_no;
$conditions .=" and cnd.pc_no='$const_no' ";
}

//$candList = DB::select("select m_election_details.YEAR,m_election_details.ELECTION_TYPE,candidate_personal_detail.cand_hname,candidate_nomination_detail.pc_no,candidate_nomination_detail.st_code,candidate_nomination_detail.district_no,candidate_nomination_detail.party_id,candidate_personal_detail.cand_name,candidate_personal_detail.candidate_id, `expenditure_reports`.`finalized_status`, `expenditure_reports`.`updated_at` as `finalized_date`, `expenditure_reports`.`final_by_ro`, `expenditure_reports`.`date_of_declaration` from `candidate_nomination_detail` left join `candidate_personal_detail` on `candidate_nomination_detail`.`candidate_id` = `candidate_personal_detail`.`candidate_id` inner join `m_election_details` on `m_election_details`.`st_code` = `candidate_nomination_detail`.`st_code` and `m_election_details`.`CONST_NO` = `candidate_nomination_detail`.`pc_no` left join `expenditure_reports` on `expenditure_reports`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` where `candidate_nomination_detail`.`application_status` = 6 and `candidate_nomination_detail`.`party_id` <> 1180 and `candidate_nomination_detail`.`finalaccepted` = '1' and `m_election_details`.`CONST_TYPE` = 'PC' and expenditure_reports.date_of_declaration !='' $conditions order by candidate_personal_detail.cand_name desc");
 
$candList = DB::select("select TEMP.YEAR,ELECTION_TYPE,cpd.cand_hname,TEMP.pc_no,TEMP.st_code,TEMP.district_no,
 TEMP.party_id,cpd.cand_name,cpd.candidate_id,TEMP.finalized_status,TEMP.finalized_date,TEMP.final_by_ro,
 TEMP.date_of_declaration
 from(
 select med.YEAR,med.ELECTION_TYPE,cnd.pc_no,
 cnd.st_code,cnd.district_no,cnd.candidate_id,
 cnd.party_id,er.finalized_status,
 er.updated_at as finalized_date, er.final_by_ro,
 er.date_of_declaration
 from candidate_nomination_detail cnd,
 m_election_details med ,expenditure_reports er
 where cnd.application_status = 6
 and cnd.party_id <> 1180
 and cnd.finalaccepted= 1 $conditions
 and med.CONST_TYPE = 'PC'
 and er.date_of_declaration !=''
 AND med.st_code = cnd.st_code
 and med.CONST_NO = cnd.pc_no
 and er.candidate_id =cnd.candidate_id
 )TEMP left join candidate_personal_detail cpd on TEMP.candidate_id = cpd.candidate_id
 group by TEMP.candidate_id
 order by cpd.cand_name desc");
 $candID=0;
 if(!empty($candList)){
   foreach($candList as $candDetails){
    $totalexpen=$this->getcandidatetotalexpenditure($candDetails->candidate_id);
    if($totalexpen > 0) {
      $candID++;
     // $grandTotal += $totalexpen; 
    //  $grandTotal += $partyID; 
    }
   }
 }
    //dd(DB::getQueryLog());
    return $candID;	
}

#################################End Party & Candidate Wise Expenditure By Niraj 12-09-2019##############
public function getResultDeclarationDate(){
     $resultDeclarationDate = DB::table('m_schedule')->select(DB::raw("min(DATE_COUNT) as start_result_declared_date, max(DATE_COUNT) as last_result_declared_date"))->get()->toArray();      
       return !empty($resultDeclarationDate[0])?[ 'start_result_declared_date'=>$resultDeclarationDate[0]->start_result_declared_date,
                                                  'last_result_declared_date'=>$resultDeclarationDate[0]->last_result_declared_date
                                                ] :[];
 
}


 }