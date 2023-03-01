<?php

namespace App\adminmodel;

use Illuminate\Database\Eloquent\Model;
use DB;
class PCRoreportModel extends Model
{
    public function Allcandidatelist($user,$status="all",$search='',$constituency='',$const_type)
    		{  
          DB::enableQueryLog();
          // dd($status);
          if($const_type=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$constituency;
                        }
        elseif($const_type=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$constituency;
            }
       // else 
      //  { $v=''; $m=''; }
    		 $a='1'; $a1='2';$a2='3';$a3='4'; $a4='5';$a5='6'; 
        //  dd($status);
    		if($search=='' and $constituency=='') {
  			if($status=="all" || $status=="") {
          DB::enableQueryLog();
    			$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                      ->where('candidate_nomination_detail.ST_CODE','=',$user->ST_CODE)
                      ->where('candidate_nomination_detail.district_no','=',$user->DIST_NO)
		    		->where(function($query1) use ($a,$a1,$a2,$a3,$a4,$a5){
                    		$query1->where('candidate_nomination_detail.application_status','=',$a)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a1)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a2)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a3)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a4)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a5);
              			})
            ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name')->limit(100)->get(); 
            //dd(DB::getQueryLog()); 
    		   }
    		   else {  
    		   		$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
					
                ->where('candidate_nomination_detail.ST_CODE','=',$user->ST_CODE)
                ->where('candidate_nomination_detail.district_no','=',$user->DIST_NO)
		    	->where('candidate_nomination_detail.application_status','=',$status)
		    	 ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name')->limit(100)->get();  
    		   }	
    		   }
    		elseif($constituency=='' || $constituency=='all') {
    			$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                   ->where('candidate_nomination_detail.ST_CODE','=',$user->ST_CODE)
                   ->where('candidate_nomination_detail.district_no','=',$user->DIST_NO) 
		    	 ->where('candidate_nomination_detail.qrcode','=',$search)
		    	 ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name')->limit(100)->get(); 
    		}
        elseif(($search=='' || $search=='all') && ($status=='' || $status=='all')) {
          $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
           ->where('candidate_nomination_detail.ST_CODE','=',$user->ST_CODE)
           ->where('candidate_nomination_detail.district_no','=',$user->DIST_NO)
           ->where($v,'=',$m)
           ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name')->limit(100)->get(); 
        }
        elseif(($search=='' || $search=='all') && ($status!='' || $status!='all')) {
          $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
           ->where('candidate_nomination_detail.ST_CODE','=',$user->ST_CODE)
           ->where('candidate_nomination_detail.district_no','=',$user->DIST_NO)
           ->where($v,'=',$m) 
           ->where('candidate_nomination_detail.application_status','=',$status)
           ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name')->limit(100)->get(); 
        }
        // dd(DB::getQueryLog());
        // dd($list);
    		   return $list;
    		 
            }

     function electiondetailsbystatecode($st_code,$consttype,$const='')
            {
              if($const=='undefined' || $const=='all') {
                $const='';
              }
              if($const=='' && $consttype=="AC") {
              $rec =DB::table('m_election_details')
              ->join('m_pc',[
                ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
                ['m_election_details.CONST_NO', '=','m_pc.PC_NO']
                ])
              ->where('m_election_details.ST_CODE',$st_code)->where('m_election_details.CONST_NO',$const)
              ->where('m_election_details.CONST_TYPE',$consttype)->orderBy('m_election_details.CONST_NO', 'ASC')
              ->select('m_election_details.*','m_pc.*')->get();
              // dd($rec);
            // dd(DB::getQueryLog());
            }
              elseif ($const=='' && $consttype=="PC") {
                // DB::enableQueryLog();
                $rec =DB::table('m_election_details')
                ->join('m_pc',[
                  ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
                  ['m_election_details.CONST_NO', '=','m_pc.PC_NO']
                  ])
                ->where('m_election_details.ST_CODE',$st_code)->where('m_election_details.CONST_NO',$const)
                ->where('m_election_details.CONST_TYPE',$consttype)->orderBy('m_election_details.CONST_NO', 'ASC')
                ->select('m_election_details.*','m_pc.*')->get();
                // dd(DB::getQueryLog());
                // dd($rec);
              }
              else {
              if($const!='' && $consttype=="AC") {
                // dd("hello");
                $rec =DB::table('m_election_details')
                ->join('m_pc',[
                  ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
                  ['m_election_details.CONST_NO', '=','m_pc.PC_NO']
                  ])
                ->where('m_election_details.ST_CODE',$st_code)->where('m_election_details.CONST_NO',$const)
                ->where('m_election_details.CONST_TYPE',$consttype)->orderBy('m_election_details.CONST_NO', 'ASC')
                ->select('m_election_details.*','m_pc.*')->get(); }
  
                elseif ($const!=='' && $consttype=="PC") {
                  $rec =DB::table('m_election_details')
                  ->join('m_pc',[
                    ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
                    ['m_election_details.CONST_NO', '=','m_pc.PC_NO']
                    ])
                  ->where('m_election_details.ST_CODE',$st_code)->where('m_election_details.CONST_NO',$const)
                  ->where('m_election_details.CONST_TYPE',$consttype)->orderBy('m_election_details.CONST_NO', 'ASC')
                  ->select('m_election_details.*','m_pc.*')->get();
                } }
              return $rec;
            }		

      function gettotalnominationcntbystatus($status, $const_type,$st_code,$const_no, $fromdate, $todate)
      		{
            // DB::enableQueryLog();
            if($fromdate=='') {
              if($const_type=="PC") {
                $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('pc_no',$const_no)->where('application_status',$status)->where('party_id','!=','1180')->get()->count();
                } 
              elseif($const_type=="AC"){
                $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('ac_no',$const_no)->where('application_status',$status)->where('party_id','!=','1180')->get()->count(); 
                } 
            }else {
              if($const_type=="PC") {
                $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('pc_no',$const_no)->where('application_status',$status)->whereBetween('date_of_submit', [$fromdate, $todate])->where('party_id','!=','1180')->get()->count();
                } 
              elseif($const_type=="AC"){
                $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('ac_no',$const_no)->where('application_status',$status)->whereBetween('date_of_submit', [$fromdate, $todate])->where('party_id','!=','1180')->get()->count(); 
                } 
            }
              // dd(DB::getQueryLog());
              return $rec;	
              // dd($rec);
      		}
     function gettotalnominationcnt($const_type,$st_code,$const_no, $fromdate='', $todate='')
      		{
      			DB::enableQueryLog();
      		if($fromdate==''){
            if($const_type=="PC") {
              $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('pc_no',$const_no)->where('application_status','!=','11')->where('party_id','!=','1180')->get()->count();
              } 
              elseif($const_type=="AC"){
              $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('ac_no',$const_no)->where('application_status','!=','11')->where('party_id','!=','1180')->get()->count(); 
              }
          } else{
            if($const_type=="PC") {
              $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('pc_no',$const_no)->whereBetween('date_of_submit', [$fromdate, $todate])->where('party_id','!=','1180')->where('application_status','!=','11')->get()->count();
              } 
              elseif($const_type=="AC"){
              $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('ac_no',$const_no)->whereBetween('date_of_submit', [$fromdate, $todate])->where('party_id','!=','1180')->where('application_status','!=','11')->get()->count(); 
              }
          }
      	 
              return $rec;	
      		}
    function Allcandidate_finaliselist($st_code)
          {
            $rec =DB::table('candidate_finalized_ac')->where('ST_CODE',$st_code)->get();
            return $rec;
          }
    function get_candidate_finalizeac($st_code,$ac_no,$actype)
          {
            $rec =DB::table('candidate_finalized_ac')->where('ST_CODE',$st_code)->where('CONS_NO',$ac_no)->where('CONS_TYPE',$actype)->first();
            return $rec;
          }
     public function definalize_candidate_ac($st,$ac,$actype,$dat)
        {
         $udata = array('finalize'=>'0'); 
         DB::table('candidate_finalized_ac')->where('ST_CODE',$st)->where('CONS_NO',$ac)->where('CONS_TYPE',$actype)->update($dat);
         if($actype=="AC") {$field="ac_no"; $val=$ac; } elseif($actype=="PC"){$field="pc_no"; $val=$ac; }

        DB::table('candidate_nomination_detail')->where('ST_CODE',$st)->where($field,$val)->update($udata);
          
         return true;
        }
     public function public_affidavit_ac($st,$ac)
        {
         $udata = array('affidavit_public'=>'yes'); 
          DB::table('candidate_nomination_detail')->where('ST_CODE',$st)->where('ac_no',$ac)->update($udata);
          return true;
        }
}