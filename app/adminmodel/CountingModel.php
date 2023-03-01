<?php
    namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;
    use DB;
class CountingModel extends Model
    {
     
      public function getallacbypcno($st_code,$pc_no)
            {       
            $d = DB::table('m_ac')->where('ST_CODE',$st_code )->where('PC_NO',$pc_no )->get();
            return $d;
            } 
     public function getcountingcenter($st_code,$cons_no,$cons_type)
            {       
            $data = DB::table('counting_center_master')->where('st_code',$st_code )->where('const_no',$cons_no )->where('const_type',$cons_type )->first();
            return $data;
            } 
     public function roundsechudle($st_code,$cons_no,$eleid)
            {       
            $data = DB::table('round_master')->where('st_code',$st_code )->where('ac_no',$cons_no )->where('election_id',$eleid )->first();
            return $data;
            }
     public function roundsechudleacpc($st_code,$cons_no,$pc_no,$eleid)
            {   
                 
            $data = DB::table('round_master')->where('st_code',$st_code )->where('ac_no',$cons_no)->where('pc_no',$pc_no)->where('election_id',$eleid )->first();
            return $data;
            }  
     public function selectsecondhightvalueofcounting($table,$st_code,$acno,$pcno,$contype,$eleid)
            {  
            if($contype=="AC")     
             $result = DB::table($table)
                    ->select([DB::raw('id'),DB::raw('nom_id'),DB::raw('candidate_id'),DB::raw('MAX(total_vote) AS max_total')]) 
                    ->where('ac_no',$acno)->where('election_id',$eleid) 
                    ->groupBy('id')->groupBy('nom_id')->groupBy('candidate_id')->orderBy('total_vote', 'desc')->limit(1)->offset(1)
                    ->first();  
            elseif($contype=="PC") 
                $result = DB::table($table)
                    ->select([DB::raw('id'),DB::raw('nom_id'),DB::raw('candidate_id'),DB::raw('MAX(total_vote) AS max_total')]) 
                    ->where('pc_no',$pcno)->where('election_id',$eleid) 
                    ->groupBy('id')->groupBy('nom_id')->groupBy('candidate_id')->orderBy('total_vote', 'desc')->limit(1)->offset(1)
                    ->first();    
                    return $result;
            } 
     public function selectfirsthightvalueofcounting($table,$st_code,$acno,$pcno,$contype,$eleid)
            { 
            if($contype=="AC")       
             $result = DB::table($table)
                    ->select([DB::raw('id'),DB::raw('nom_id'),DB::raw('candidate_id'),DB::raw('MAX(total_vote) AS max_total')]) 
                    ->where('ac_no',$acno)->where('election_id',$eleid) 
                    ->groupBy('id')->groupBy('nom_id')->groupBy('candidate_id')->orderBy('total_vote', 'desc')
                    ->first(); 
             elseif($contype=="PC")
                $result = DB::table($table)
                    ->select([DB::raw('id'),DB::raw('nom_id'),DB::raw('candidate_id'),DB::raw('MAX(total_vote) AS max_total')]) 
                    ->where('pc_no',$acno)->where('election_id',$eleid) 
                    ->groupBy('id')->groupBy('nom_id')->groupBy('candidate_id')->orderBy('total_vote', 'desc')
                    ->first(); 

                    return $result;
            }      
    }
