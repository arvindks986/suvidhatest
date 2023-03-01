<?php
    namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;
    use DB;
class ROPCModel extends Model
{
    //
    public function Allcandidatelist($user,$status="all",$search='')
    		{  
    			DB::enableQueryLog();
    		if($user->CONST_TYPE=="AC") { 
    					$v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
    					}
  			elseif($user->CONST_TYPE=="PC") { 
  						$v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
  					}
       
  			$a='4'; $a1='3';$a2='5';$a3='6';$a4='2';$a5='1'; 
  			if($status=="all" || $status=="") {
    			$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
				 		->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->Where('candidate_nomination_detail.election_id', '=', $user->ELECTION_ID)
		    		->where(function($query1) use ($a,$a1,$a2,$a3,$a4,$a5){
                    		$query1->where('candidate_nomination_detail.application_status','=',$a)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a1)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a2)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a3)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a4)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a5);
              			})
           ->Where('candidate_personal_detail.cand_name', 'like', '%'.$search)
          
					->orderBy('candidate_nomination_detail.cand_sl_no', 'ASC')  
    			 ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id','candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.pc_no','candidate_nomination_detail.st_code','candidate_nomination_detail.cand_sl_no','candidate_nomination_detail.new_srno','candidate_nomination_detail.party_type','candidate_nomination_detail.scrutiny_date','candidate_nomination_detail.rejection_message','candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status','candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.cand_alias_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_vname','candidate_personal_detail.cand_image','candidate_personal_detail.is_candidate_vip','candidate_personal_detail.cand_panno','candidate_personal_detail.cand_gender','candidate_personal_detail.cand_age','candidate_nomination_detail.cand_party_type','candidate_nomination_detail.cand_party_type')->get(); 
    		   }
    		   else { //dd("hello");
    		   		$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
		    	->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		    	->where('candidate_nomination_detail.application_status','=',$status)
          ->Where('candidate_nomination_detail.election_id', '=', $user->ELECTION_ID)
          ->Where('candidate_personal_detail.cand_name', 'like', '%'.$search)
		    	->orderBy('candidate_nomination_detail.cand_sl_no', 'ASC') 
          ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id','candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.pc_no','candidate_nomination_detail.st_code','candidate_nomination_detail.cand_sl_no','candidate_nomination_detail.new_srno','candidate_nomination_detail.rejection_message','candidate_nomination_detail.party_type','candidate_nomination_detail.scrutiny_date','candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.cand_alias_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_vname','candidate_personal_detail.cand_image','candidate_personal_detail.is_candidate_vip','candidate_personal_detail.cand_panno','candidate_personal_detail.cand_gender','candidate_personal_detail.cand_age','candidate_nomination_detail.finalaccepted','candidate_nomination_detail.cand_party_type')->get();
    		   }	
    		       // $query = DB::getQueryLog();
					   
					  //  dd($query);
    		   return $list;
    		}
    public function withdrawn($user,$status="all",$search='')
        {  
       
        if($user->CONST_TYPE=="AC") { 
              $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
              }
        elseif($user->CONST_TYPE=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
            }
        $a='5'; $a1='6'; 
        if($status=="all" || $status=="") {
          $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->Where('candidate_nomination_detail.election_id', '=', $user->ELECTION_ID)
            ->where(function($query1) use ($a,$a1){
                        $query1->where('candidate_nomination_detail.application_status','=',$a)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a1);
                    })
         // ->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')

          ->orderBy('candidate_nomination_detail.cand_sl_no', 'ASC') 
           ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id','candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.pc_no','candidate_nomination_detail.st_code','candidate_nomination_detail.cand_sl_no','candidate_nomination_detail.rejection_message','candidate_nomination_detail.new_srno','candidate_nomination_detail.party_type','candidate_nomination_detail.scrutiny_date','candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status','candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.cand_alias_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_vname','candidate_personal_detail.cand_image','candidate_personal_detail.is_candidate_vip','candidate_personal_detail.cand_panno','candidate_personal_detail.cand_gender','candidate_personal_detail.cand_age','candidate_nomination_detail.finalaccepted','candidate_nomination_detail.cand_party_type')->get(); 
           }
           else { //dd("hello");
              $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
          ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
          ->where('candidate_nomination_detail.application_status','=',$status)
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
          ->Where('candidate_nomination_detail.election_id', '=', $user->ELECTION_ID)
         //->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
          ->orderBy('candidate_nomination_detail.cand_sl_no', 'ASC') 
           ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id','candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.pc_no','candidate_nomination_detail.st_code','candidate_nomination_detail.cand_sl_no','candidate_nomination_detail.rejection_message','candidate_nomination_detail.new_srno','candidate_nomination_detail.party_type','candidate_nomination_detail.scrutiny_date','candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status','candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.cand_alias_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_vname','candidate_personal_detail.cand_image','candidate_personal_detail.is_candidate_vip','candidate_personal_detail.cand_panno','candidate_personal_detail.cand_gender','candidate_personal_detail.cand_age','candidate_nomination_detail.finalaccepted','candidate_nomination_detail.cand_party_type')->get();
           }  
             return $list;
        }
    public function Allapplicantlist($user,$status="all")
    		{  dd($user); 
    		DB::enableQueryLog();
    		if($user->CONST_TYPE=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
                        }
        elseif($user->CONST_TYPE=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
            }
  			$a='1'; $a1='2';$a2='3';$a3='4'; $a4='5';$a5='6'; 
      if($status=="all" || $status=="") {
    			$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
					  ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->Where('candidate_nomination_detail.election_id', '=', $user->ELECTION_ID)
            ->where(function($query1) use ($a,$a1,$a2){
                        $query1->where('candidate_nomination_detail.application_status','=',$a)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a1)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a2);
                    })
            ->orderBy('candidate_nomination_detail.cand_sl_no', 'ASC')
    				 ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id','candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.rejection_message','candidate_nomination_detail.pc_no','candidate_nomination_detail.st_code','candidate_nomination_detail.cand_sl_no','candidate_nomination_detail.new_srno','candidate_nomination_detail.party_type','candidate_nomination_detail.scrutiny_date','candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status','candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.cand_alias_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_vname','candidate_personal_detail.cand_image','candidate_personal_detail.is_candidate_vip','candidate_personal_detail.cand_panno','candidate_personal_detail.cand_gender','candidate_personal_detail.cand_age','candidate_nomination_detail.finalaccepted','candidate_nomination_detail.cand_party_type')->get();
    		   }
    		   else {  
    		   		$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
					  ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->Where('candidate_nomination_detail.election_id', '=', $user->ELECTION_ID)
		    		->where('candidate_nomination_detail.application_status','=',$status)
		    		->orderBy('candidate_nomination_detail.cand_sl_no', 'ASC')
    				 ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id','candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.rejection_message','candidate_nomination_detail.pc_no','candidate_nomination_detail.st_code','candidate_nomination_detail.cand_sl_no','candidate_nomination_detail.new_srno','candidate_nomination_detail.party_type','candidate_nomination_detail.scrutiny_date','candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status','candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.cand_alias_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_vname','candidate_personal_detail.cand_image','candidate_personal_detail.is_candidate_vip','candidate_personal_detail.cand_panno','candidate_personal_detail.cand_gender','candidate_personal_detail.cand_age','candidate_nomination_detail.finalaccepted','candidate_nomination_detail.cand_party_type')->get();  
    		    }		 
    		   return $list;
    		}
  public function acceptedcandidate($user,$search='')
        {  
        if($user->CONST_TYPE=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
                        }
        elseif($user->CONST_TYPE=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
            }
        $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
          //  ->leftjoin('candidate_affidavit_detail', 'candidate_nomination_detail.nom_id', '=', 'candidate_affidavit_detail.nom_id') 
          //  ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          //  ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
            ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('candidate_nomination_detail.application_status','=','6')
            ->Where('candidate_nomination_detail.election_id', '=', $user->ELECTION_ID)
            // ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            ->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id','candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.rejection_message','candidate_nomination_detail.pc_no','candidate_nomination_detail.st_code','candidate_nomination_detail.cand_sl_no','candidate_nomination_detail.new_srno','candidate_nomination_detail.party_type','candidate_nomination_detail.scrutiny_date','candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status','candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.cand_alias_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_vname','candidate_personal_detail.cand_image','candidate_personal_detail.is_candidate_vip','candidate_personal_detail.cand_panno','candidate_personal_detail.cand_gender','candidate_personal_detail.cand_age','candidate_nomination_detail.finalaccepted','candidate_nomination_detail.cand_party_type')->get(); 
          return $list;
        }
    public function Symbolcandidate($user)
        {  DB::enableQueryLog();
       if($user->CONST_TYPE=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
                        }
        elseif($user->CONST_TYPE=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
            }
        $nu='';
        $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
          ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
          ->Where('candidate_nomination_detail.election_id', '=', $user->ELECTION_ID)
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          //->where('candidate_nomination_detail.symbol_id','=','0')
          ->where('candidate_nomination_detail.application_status','=','6')
           ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id','candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.rejection_message','candidate_nomination_detail.pc_no','candidate_nomination_detail.st_code','candidate_nomination_detail.cand_sl_no','candidate_nomination_detail.new_srno','candidate_nomination_detail.party_type','candidate_nomination_detail.scrutiny_date','candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status','candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.cand_alias_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_vname','candidate_personal_detail.cand_image','candidate_personal_detail.is_candidate_vip','candidate_personal_detail.cand_panno','candidate_personal_detail.cand_gender','candidate_personal_detail.cand_age','candidate_nomination_detail.finalaccepted','candidate_nomination_detail.cand_party_type')->get(); 
           
          return $list;
        }
    public function Symbolassign($nom_id)
        {   
         $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
             ->where('candidate_nomination_detail.nom_id','=',$nom_id)
             ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id','candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.rejection_message','candidate_nomination_detail.pc_no','candidate_nomination_detail.st_code','candidate_nomination_detail.cand_sl_no','candidate_nomination_detail.new_srno','candidate_nomination_detail.party_type','candidate_nomination_detail.scrutiny_date','candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status','candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.cand_alias_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_vname','candidate_personal_detail.cand_image','candidate_personal_detail.is_candidate_vip','candidate_personal_detail.cand_panno','candidate_personal_detail.cand_gender','candidate_personal_detail.cand_age','candidate_nomination_detail.finalaccepted','candidate_nomination_detail.cand_party_type')->first();
           return $list;
        }
    public function finalize_candidate_ac($st,$ac,$actype,$dat)
        {
         $udata = array('finalize'=>'1'); 
         DB::table('candidate_finalized_ac')->where('st_code',$st)->where('const_no',$ac)->where('const_type',$actype)->update($dat);
         if($actype=="AC") {$field="ac_no"; $val=$ac; } elseif($actype=="PC"){$field="pc_no"; $val=$ac; }

          DB::table('candidate_nomination_detail')->where('st_code',$st)->where($field,$val)->update($udata);
          $r =DB::table('candidate_personal_detail')->where('cand_name','NOTA')->first();  
          $tot =DB::table('candidate_nomination_detail')->where('st_code',$st)->where($field,$val)->where('finalize','1')->where('application_status','6')->get()->count();
          
          if(!isset($r)){
            $candata=array('cand_name'=>'NOTA','cand_hname'=>'NOTA');   
            $c = DB::table('candidate_personal_detail')->insert($candata);
            $r =DB::table('candidate_personal_detail')->where('cand_name','NOTA')->first(); 
          } 
          $checknota =DB::table('candidate_nomination_detail')->where('candidate_id',$r->candidate_id)->where('st_code',$st)->where($field,$val)->first(); 
           $new_sr=$tot+1;
          // dd($new_sr);
          $r1 =DB::table('candidate_nomination_detail')->where('st_code',$st)->where($field,$val)->where('finalize','1')->where('application_status','6')->first();  
          $nom_data = array('candidate_id'=>$r->candidate_id,'ac_no'=>$r1->ac_no,'election_id'=>$r1->election_id,'pc_no'=>$r1->pc_no,'ST_CODE'=>$st,'finalize'=>'1','application_status'=>'6','new_srno'=>$new_sr,'date_of_submit'=>date("Y-m-d"),'scrutiny_date'=>date("Y-m-d"),'party_id'=>'1180','finalaccepted'=>1,'cand_party_type'=>'-Z','symbol_id'=>'-1'); 
          
          if(empty($checknota)) {
              $n = DB::table('candidate_nomination_detail')->insert($nom_data);
            }
          else{  
             $n_data = array('finalize'=>'1','new_srno'=>$new_sr,'finalaccepted'=>1); 
            
             DB::table('candidate_nomination_detail')->where('nom_id',$checknota->nom_id)->where($field,$val)->update($n_data);
          }
          return true;
        }
     public function public_affidavit_ac($st,$ac)
        {
         $udata = array('affidavit_public'=>'yes'); 
          DB::table('candidate_nomination_detail')->where('ST_CODE',$st)->where('ac_no',$ac)->update($udata);
          return true;
        }
    public function checkfinalize_acbyro($st,$ac,$actype)
        { 
          DB::enableQueryLog();  //echo $st."-".$ac."-".$actype;
         if($actype=="AC")
          $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st)->where('ac_no',$ac)->where('finalize','1')->first();
        else 
          $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st)->where('pc_no',$ac)->where('finalize','1')->first();
 
        if(isset($rec))
                return 1;
          else
                 return 0;
        }
  function getNominationbyId($nomid){
              $getnomination = DB::table('candidate_nomination_detail')->where('nom_id',$nomid)->get();
              return $getnomination ;
      }
  function getcandNomination($candId){
        $getcandNomination = DB::table('candidate_nomination_detail')->where('candidate_id',$candId)->get();
        return $getcandNomination ;
    }
       
    
      function getCandidateByUserid($candidate_id)
    {
        $getCandidateByUserid = DB::table('candidate_personal_detail')->where('candidate_id', $candidate_id)->first();
        return $getCandidateByUserid;
    }
    
    function getACbyStatenAcId($acno,$stcode)
    {
        $getAC = DB::table('m_ac')->select('AC_NO','AC_NAME')->where('AC_NO', $acno)->where('ST_CODE',$stcode)->first();
        return $getAC;
    }
    function getCandidateByOfficerId($officerName)
            {
            $getCandidateByOfficerId = DB::table('candidate_personal_detail')->where('created_by', $officerName)->get();
            return $getCandidateByOfficerId;
            }
    function getallaffidavitbyro($officerId)
            {
            $getaffidavitbyOfficerId = DB::table('candidate_affidavit_detail')->where('created_by', $officerId)->get();
             return ($getaffidavitbyOfficerId);
            }
    function getcounteraffidavitbyOfficerId($officerId){
        //DB::enableQueryLog();
        $getcounteraffidavitbyOfficerId = DB::table('candidate_counteraffidavit_detail')->where('created_by', $officerId)->get();
        //dd(DB::getQueryLog());
       return ($getcounteraffidavitbyOfficerId);
    }
    function getaffidavit($candId,$nomid){
        //DB::enableQueryLog();
        $getaffidavit = DB::table('candidate_affidavit_detail')->where('candidate_id', $candId)->where('nom_id', $nomid)->first();
        //dd(DB::getQueryLog());
       return ($getaffidavit);
    } 
  public function contestingcandidate($user,$search='')
        {  
        if($user->CONST_TYPE=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
                        }
        elseif($user->CONST_TYPE=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
            }
        $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->Where('candidate_nomination_detail.election_id', '=', $user->ELECTION_ID)
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            ->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id','candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.rejection_message','candidate_nomination_detail.finalaccepted','candidate_nomination_detail.pc_no','candidate_nomination_detail.st_code','candidate_nomination_detail.cand_sl_no','candidate_nomination_detail.new_srno','candidate_nomination_detail.party_type','candidate_nomination_detail.scrutiny_date','candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status','candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.cand_alias_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_vname','candidate_personal_detail.cand_image','candidate_personal_detail.is_candidate_vip','candidate_personal_detail.cand_panno','candidate_personal_detail.cand_gender','candidate_personal_detail.cand_age','candidate_nomination_detail.cand_party_type')->get(); 
          return $list;
        }

  public function partywisecontenestingcandidate($user,$a,$a1)
        {  
        if($user->CONST_TYPE=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
                        }
        elseif($user->CONST_TYPE=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
            }
        $cands = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
        
            ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
            ->Where('candidate_nomination_detail.election_id', '=', $user->ELECTION_ID)
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
       
         ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
             ->where(function($query1) use ($a,$a1){
                        $query1->where('m_party.PARTYTYPE','=',$a)
                        ->orWhere('m_party.PARTYTYPE','=',$a1);
                    })
        ->orderBy('candidate_nomination_detail.new_srno', 'asc')
        ->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address',
                'candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES',
                'candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode',
                'candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.candidate_residence_acno','candidate_personal_detail.cand_image')->get(); 
          return $cands;
        }  

  public function partywiseallcontenestingcandidate($user)
        {  
        if($user->CONST_TYPE=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
                        }
        elseif($user->CONST_TYPE=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
            }
        $cands = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
        
            ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m)
            ->Where('candidate_nomination_detail.election_id', '=', $user->ELECTION_ID) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->orderBy('candidate_nomination_detail.new_srno', 'asc')
            ->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address',
                'candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES',
                'candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode',
                'candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.candidate_residence_acno','candidate_personal_detail.cand_image')->get(); 
          return $cands;
        } 




        public function form3areportsdetails($user,$sub_date)
              {  
                 
               if($user['CONST_TYPE']=="PC") { 
                    $v= 'candidate_nomination_detail.pc_no'; $m=$user['CONST_NO']; 
                  }
              $list = DB::table('candidate_nomination_detail')
                  ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
                  ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
                  ->where('candidate_nomination_detail.st_code','=',$user['ST_CODE'])->where($v,'=',$m) 
                  ->Where('candidate_nomination_detail.election_id', '=', $user['ELECTION_ID'])
                  ->Where('candidate_nomination_detail.date_of_submit', '=', $sub_date)
                  ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                  ->orderBy('candidate_nomination_detail.new_srno', 'asc')
                  ->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_epic_no','candidate_nomination_detail.date_of_submit',
                     'candidate_personal_detail.cand_fhname','candidate_personal_detail.candidate_residence_address',
                      'candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode',
                      'candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.candidate_residence_acno',
                      'candidate_personal_detail.cand_age','candidate_personal_detail.cand_category','candidate_personal_detail.cand_cast',
                      'candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','candidate_nomination_detail.st_code',
                      'candidate_nomination_detail.pc_no','candidate_nomination_detail.district_no','candidate_nomination_detail.cand_sl_no',
                      'candidate_nomination_detail.new_srno', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE')->get(); 

                return $list;
              }   

        public function form4reportsdetails($user,$a,$a1)
              {  
              if($user->CONST_TYPE=="AC") { 
                              $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
                              }
              elseif($user->CONST_TYPE=="PC") { 
                    $v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
                  }
              $list = DB::table('candidate_nomination_detail')
                  ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
                  ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
                  ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
                  ->Where('candidate_nomination_detail.election_id', '=', $user->ELECTION_ID)
                  ->where('candidate_nomination_detail.application_status','=','6')
                  ->where('candidate_nomination_detail.finalaccepted','=','1')
                  ->Where('candidate_nomination_detail.symbol_id', '<>', '1180')
                  ->where('candidate_nomination_detail.application_status','<>','11')
                  ->where(function($query1) use ($a,$a1){
                        $query1->where('m_party.PARTYTYPE','=',$a)
                        ->orWhere('m_party.PARTYTYPE','=',$a1);
                    }) 
                  ->orderBy('candidate_nomination_detail.new_srno', 'asc')
                  ->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_epic_no','candidate_personal_detail.cand_fhname','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.date_of_submit',
                      'candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode',
                      'candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.candidate_residence_acno',
                      'candidate_personal_detail.cand_age','candidate_personal_detail.cand_category','candidate_personal_detail.cand_cast',
                      'candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','candidate_nomination_detail.st_code',
                      'candidate_nomination_detail.pc_no','candidate_nomination_detail.district_no','candidate_nomination_detail.cand_sl_no',
                      'candidate_nomination_detail.new_srno', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE')->get(); 
                return $list;
              }   
}
