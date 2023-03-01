<?php
    namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;
    use DB;
class PCCountingModel extends Model
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
     public function selectsecondhightvalueofcounting($table,$st_code,$pcno,$contype,$eleid)
            {  
           $result = DB::table($table)
                    ->select([DB::raw('nom_id'),DB::raw('candidate_id'),DB::raw('MAX(total_vote) AS max_total')])->where('st_code',$st_code) 
                    ->where('pc_no',$pcno)->where('election_id',$eleid)->where('party_id','<>','1180') 
                    ->groupBy('nom_id')->groupBy('candidate_id')->orderBy('total_vote', 'desc')->limit(1)->offset(1)
                    ->first();    
                    return $result;
            } 
     public function selectfirsthightvalueofcounting($table,$st_code,$pcno,$contype,$eleid)
            { 
             $result = DB::table($table)
                    ->select([DB::raw('nom_id'),DB::raw('candidate_id'),DB::raw('MAX(total_vote) AS max_total')]) ->where('st_code',$st_code)
                    ->where('pc_no',$pcno)->where('election_id',$eleid)->where('party_id','<>','1180') 
                    ->groupBy('nom_id')->groupBy('candidate_id')->orderBy('total_vote','desc')->limit(1)
                    ->first(); 
                     return $result;
            }
     public function selectfirsthightvalueofcountingpc($table,$st_code,$pcno,$eleid)
            {   
             $result = DB::table($table)
                    ->select([DB::raw('nom_id'),DB::raw('candidate_id'),DB::raw('MAX(total_vote) AS max_total')]) ->where('st_code',$st_code)
                    ->where('pc_no',$pcno)->where('election_id',$eleid)->where('party_id','<>','1180')  
                    ->groupBy('nom_id')->groupBy('candidate_id')->orderBy('total_vote','desc')
                    ->first(); 

                    return $result;
            }
        public function selectsecondhightvalueofcountingpc($table,$st_code,$pcno,$eleid)
            {  
           $result = DB::table($table)
                    ->select([DB::raw('nom_id'),DB::raw('candidate_id'),DB::raw('MAX(total_vote) AS max_total')])->where('st_code',$st_code) 
                    ->where('pc_no',$pcno)->where('election_id',$eleid) ->where('party_id','<>','1180') 
                    ->groupBy('nom_id')->groupBy('candidate_id')->orderBy('total_vote', 'desc')->limit(1)->offset(1)
                    ->first();    
                    return $result;
            }
       public function totalvotsbypcwise($table,$pcno,$eleid)
            { 
            $result = DB::table($table)
                    ->select([DB::raw('pc_no'),DB::raw('nom_id'),DB::raw('SUM(total_vote) AS sum_total')]) 
                    ->where('pc_no',$pcno)->where('election_id',$eleid)  
                    ->groupBy('pc_no')->groupBy('nom_id')->get(); 

                    return $result;
            }
        public function totalvotsbyacwise($table,$acno,$eleid)
            { 
            $result = DB::table($table)
                    ->select([DB::raw('ac_no'),DB::raw('SUM(total_vote) AS sum_total'),DB::raw('election_id')]) 
                    ->where('ac_no',$acno)->where('election_id',$eleid) 
                    ->groupBy('ac_no')->groupBy('election_id')->first(); 
              
                    return $result;
            }  
        public function checkallacfinalize($st_code,$pcno,$eleid)
            {  
            $r=DB::table('counting_finalized_ac')->where('st_code',$st_code)->where('pc_no',$pcno)->where('election_id',$eleid)->where('finalized_ac','0')->first();
 
                if(isset($r))
                        return 1;
                  else
                         return 0;
            } 
        public function checkpostalentry($st_code,$pcno,$eleid)
            {   
            $re=DB::table('counting_pcmaster')->where('st_code',$st_code)->where('pc_no',$pcno)->where('election_id',$eleid)->where('postal_vote', '>', 0)->where('postaltotalvote', '>', 0)->get();
             if(isset($re)) 
                {
                return 1; 
                } 
            else {
                    return 0;
                }  
            } 
         public function cantestesting_nomination($st_code,$pc_no,$elec_id)
                {
                  $ndata=array('symbol_id'=>'-1');
                  $g = DB::table('candidate_nomination_detail')->where('party_id','1180')->update($ndata);
         
                  $result = DB::table('candidate_nomination_detail')
                      ->where('st_code',$st_code)
                      ->where('pc_no',$pc_no)
                      ->where('election_id',$elec_id)
                      ->where('application_status','6')
                      ->where('finalize','1')
                      ->where('finalaccepted','=','1')
                      ->where('symbol_id','<>','200')->orderBy('new_srno','ASC')->get();
                  
                   return $result;    
                      
                } 


        public static function grandtotalsum($table,$round,$data = array()){ 

         

       $sql_raw = "id, SUM(IFNULL(round1,0)+IFNULL(round2,0)+IFNULL(round3,0)+IFNULL(round4,0)+IFNULL(round5,0)+IFNULL(round6,0)+IFNULL(round7,0)+IFNULL(round8,0)+IFNULL(round9,0)+IFNULL(round10,0)+IFNULL(round11,0)+IFNULL(round12,0)+IFNULL(round13,0)+IFNULL(round14,0)+IFNULL(round15,0)+IFNULL(round16,0)+IFNULL( round17,0)+IFNULL(round18
        	,0)+IFNULL(round19,0)+IFNULL(round20,0)+IFNULL(round21,0)+IFNULL(round22,0)+IFNULL(round23,0)+IFNULL(round24,0)+IFNULL(round25,0)+IFNULL(round26,0)+IFNULL(round27,0)+IFNULL(round28,0)+IFNULL(round29,0)+IFNULL(round30,0)+IFNULL(round31,0)+IFNULL(round32,0)+IFNULL(round33,0)+IFNULL(round34,0)+IFNULL(round35,0)+IFNULL(round36,0)+IFNULL(
        	round37,0)+IFNULL(round38,0)+IFNULL(round39,0)+IFNULL(round40,0)+IFNULL(round41,0)+IFNULL(round42,0)+IFNULL(round43,0)+IFNULL(round44,0)+IFNULL(round45,0)+IFNULL(round46,0)+IFNULL(round47,0)+IFNULL(round48,0)+IFNULL(round49,0)+IFNULL(round50,0)+IFNULL(round51,0)+IFNULL(round52,0)+IFNULL(round53,0)+IFNULL(round54,0)+ IFNULL(round55,0) + IFNULL(round56,0)+IFNULL(round57,0)+
          IFNULL(round58,0)+IFNULL(round59,0)+IFNULL(round60,0)+IFNULL(round61,0)+IFNULL(round62,0)+IFNULL(round63,0)+IFNULL(round64,0)+IFNULL(round65,0)+IFNULL(round66,0)+IFNULL(round67,0)+IFNULL(round68,0)+IFNULL(round69,0)+IFNULL(round70,0)+IFNULL(round71,0)+IFNULL(round72,0)+IFNULL(round73,0)+IFNULL(round74,0)+IFNULL(round75,0)+IFNULL(round76,0)+IFNULL(round77,0)+IFNULL(round78,0)+IFNULL(round79,0)+IFNULL(round80,0)+IFNULL(round81,0)+IFNULL(round82,0)+IFNULL(round83,0)+IFNULL(round84,0)+IFNULL(round85,0)+IFNULL(round86,0)+IFNULL(round87,0)+IFNULL(round88,0)+IFNULL(round89,0)+IFNULL(round90,0)+IFNULL(round91,0)+IFNULL(round92,0)+IFNULL(round93,0)+IFNULL(round94,0)+IFNULL(round95,0)+IFNULL(round96,0)+IFNULL(round97,0)+IFNULL(round98,0)+IFNULL(round99,0)+IFNULL(round100,0)+IFNULL(round101,0)+IFNULL(round102,0)+IFNULL(round103,0)+IFNULL(round104,0)+IFNULL(round105,0)+IFNULL(round106,0)+IFNULL(round107,0)+IFNULL(round108,0)+IFNULL(round109,0)+IFNULL(round110,0)+IFNULL(round111,0)+IFNULL(round112,0)+IFNULL(round113,0)+IFNULL(round114,0)+IFNULL(round115,0)+IFNULL(round116,0)+IFNULL(round117,0)+IFNULL(round118,0)+IFNULL(round119,0)+IFNULL(round120,0)+IFNULL(round121,0)+IFNULL(round122,0)+IFNULL(round123,0)+IFNULL(round124,0)+IFNULL(round125,0)+IFNULL(round126,0)+IFNULL(round127,0)+IFNULL(round128,0)+IFNULL(round129,0)+IFNULL(round130,0)) AS grant_total,".$round;


                $sql = DB::table($table);
                $sql->selectRaw($sql_raw);

                if(!empty($data['id'])){
                  $sql->where("id", $data['id']);
                }

                if(!empty($data['nom_id'])){
                  $sql->where("nom_id", $data['nom_id']);
                }

                if(!empty($data['ac_no'])){
                  $sql->where("ac_no", $data['ac_no']);
                }
            $query = $sql->first();
     
        return $query;

    }  


    public static function get_all_acsum($table,$data = array()){
 
        $sql_raw = "IFNULL(sum(round1),0) as round1, IFNULL(sum(round2),0) as round2,IFNULL(sum(round3),0) as round3, IFNULL(sum(round4),0) as round4, IFNULL(sum(round5),0) as round5, IFNULL(sum(round6),0) as round6, IFNULL(sum(round7),0) as round7, IFNULL(sum(round8),0) as round8, IFNULL(sum(round9),0) as round9, IFNULL(sum(round10),0) as round10, IFNULL(sum(round11),0) as round11, IFNULL(sum(round12),0) as round12, IFNULL(sum(round13),0) as round13, IFNULL(sum(round14),0) as round14, IFNULL(sum(round15),0) as round15, IFNULL(sum(round16),0) as round16, IFNULL(sum(round17),0) as round17, IFNULL(sum(round17),0) as round17, IFNULL(sum(round18),0) as round18, IFNULL(sum(round19),0) as round19, IFNULL(sum(round20),0) as round20, IFNULL(sum(round21),0) as round21, IFNULL(sum(round22),0) as round22, IFNULL(sum(round23),0) as round23, IFNULL(sum(round24),0) as round24, IFNULL(sum(round25),0) as round25, IFNULL(sum(round26),0) as round26, IFNULL(sum(round27),0) as round27, IFNULL(sum(round28),0) as round28, IFNULL(sum(round29),0) as round29, IFNULL(sum(round30),0) as round30, IFNULL(sum(round31),0) as round31, IFNULL(sum(round32),0) as round32, IFNULL(sum(round33),0) as round33, IFNULL(sum(round34),0) as round34, IFNULL(sum(round35),0) as round35, IFNULL(sum(round36),0) as round36, IFNULL(sum(round37),0) as round37, IFNULL(sum(round38),0) as round38, IFNULL(sum(round39),0) as round39,IFNULL(sum(round40),0) as round40, IFNULL(sum(round41),0) as round41, IFNULL(sum(round42),0) as round42, IFNULL(sum(round43),0) as round43, IFNULL(sum(round44),0) as round44, IFNULL(sum(round45),0) as round45,IFNULL(sum(round46),0) as round46, IFNULL(sum(round47),0) as round47, IFNULL(sum(round48),0) as round48, IFNULL(sum(round49),0) as round49, IFNULL(sum(round50),0) as round50, IFNULL(sum(round51),0) as round51,IFNULL(sum(round52),0) as round52,IFNULL(sum(round53),0) as round53, IFNULL(sum(round54),0) as round54,IFNULL(sum(round55),0) as round55,IFNULL(sum(round56),0) as round56,IFNULL(sum(round57),0) as round57,IFNULL(sum(round58),0) as round58,IFNULL(sum(round59),0) as round59,IFNULL(sum(round60),0) as round60,IFNULL(sum(round61),0) as round61,IFNULL(sum(round62),0) as round62,IFNULL(sum(round63),0) as round63,IFNULL(sum(round64),0) as round64,IFNULL(sum(round65),0) as round65,IFNULL(sum(round66),0) as round66,IFNULL(sum(round67),0) as round67,IFNULL(sum(round68),0) as round68,IFNULL(sum(round69),0) as round69,IFNULL(sum(round70),0) as round70,IFNULL(sum(round71),0) as round71,IFNULL(sum(round72),0) as round72,IFNULL(sum(round73),0) as round73,IFNULL(sum(round74),0) as round74,IFNULL(sum(round75),0) as round75,IFNULL(sum(round76),0) as round76,IFNULL(sum(round77),0) as round77,IFNULL(sum(round78),0) as round78,IFNULL(sum(round79),0) as round79,IFNULL(sum(round80),0) as round80,IFNULL(sum(round81),0) as round81,IFNULL(sum(round82),0) as round82,IFNULL(sum(round83),0) as round83,IFNULL(sum(round84),0) as round84,IFNULL(sum(round85),0) as round85,IFNULL(sum(round86),0) as round86,IFNULL(sum(round87),0) as round87,IFNULL(sum(round88),0) as round88,IFNULL(sum(round89),0) as round89,IFNULL(sum(round90),0) as round90,IFNULL(sum(round91),0) as round91,IFNULL(sum(round92),0) as round92,IFNULL(sum(round93),0) as round93,IFNULL(sum(round94),0) as round94,IFNULL(sum(round95),0) as round95,IFNULL(sum(round96),0) as round96,IFNULL(sum(round97),0) as round97,IFNULL(sum(round98),0) as round98,IFNULL(sum(round99),0) as round99,IFNULL(sum(round100),0) as round100,IFNULL(sum(round101),0) as round101,IFNULL(sum(round102),0) as round102,IFNULL(sum(round103),0) as round103,IFNULL(sum(round104),0) as round104,IFNULL(sum(round105),0) as round105,IFNULL(sum(round106),0) as round106,IFNULL(sum(round107),0) as round107,IFNULL(sum(round108),0) as round108,IFNULL(sum(round109),0) as round109,IFNULL(sum(round110),0) as round110,IFNULL(sum(round111),0) as round111,IFNULL(sum(round112),0) as round112,IFNULL(sum(round113),0) as round113,IFNULL(sum(round114),0) as round114,IFNULL(sum(round115),0) as round115,IFNULL(sum(round116),0) as round116,IFNULL(sum(round117),0) as round117,IFNULL(sum(round118),0) as round118,IFNULL(sum(round119),0) as round119,IFNULL(sum(round120),0) as round120,IFNULL(sum(round121),0) as round121,IFNULL(sum(round122),0) as round122,IFNULL(sum(round123),0) as round123,IFNULL(sum(round124),0) as round124,IFNULL(sum(round125),0) as round125,IFNULL(sum(round126),0) as round126,IFNULL(sum(round127),0) as round127,IFNULL(sum(round128),0) as round128,IFNULL(sum(round129),0) as round129,IFNULL(sum(round130),0) as round130,IFNULL(sum(total_vote),0) as total_evm_vote, id,nom_id, candidate_id, candidate_name, candidate_hname, party_name, party_hname, ac_no, pc_no, election_id"; 

        $sql = DB::table($table) ;

        $sql->selectRaw($sql_raw);
  
        if(!empty($data['pc_no'])){
          $sql->where("pc_no", $data['pc_no']);
        }

        if(!empty($data['ac_no'])){
          $sql->where("ac_no", $data['ac_no']);
        }

       // if(!empty($data['nom_id']) && in_array($data['nom_id'],['pc_no'])){
            // if($data['group_by']=='nom_id'){
                $sql->groupBy("nom_id")->groupBy("candidate_id")->groupBy('pc_no');
           // } 
            if($data['order_by']=='id'){
                $sql->orderBy($data['order_by'], 'ASC');
            } 

 
        $query = $sql->get();
        
        
        return $query;

    } 

     public function getallpcrecords($st_code,$pcno,$eleid)
            {  
            $records=DB::table('counting_pcmaster')->where('st_code',$st_code)->where('pc_no',$pcno)->where('election_id',$eleid)->orderby('id','ASC')->get();
            return $records;
            }   
            
      public function get_max_rounds($data = array()) 
                    {
                    $result = [
                          "st_code"    => 0,
                          "pc_no"      => 0,
                          "ac_no"      => 0,
                          "max_round"  => 0,
                          "election_id"  => 0,
                    ];
                     $sql_raw = "max(`scheduled_round`) as max_round, st_code,ac_no, pc_no, election_id"; 
                     $sql = DB::table('round_master') ;
                     $sql->selectRaw($sql_raw);
  
                    if(!empty($data['st_code'])){
                      $sql->where("st_code", $data['st_code']);
                    }
                    if(!empty($data['pc_no'])){
                      $sql->where("pc_no", $data['pc_no']);
                    }
                    if(!empty($data['ac_no'])){
                      $sql->where("ac_no", $data['ac_no']);
                    }
                        $query = $sql->first();
                      if($query){
                          $result = [
                                'st_code'   => $query->st_code,
                                'pc_no'     => $query->pc_no,
                                'ac_no'     => $query->ac_no,
                                'max_round'  => $query->max_round,
                                'election_id'  => $query->election_id,
                                 
                            ];  
                        } 

                        return $result;     
                    } 

        //add by waseem
        public function check_finalized_ro($data = array()){

          $sql  =  DB::table('counting_finalized_ac')
          ->where('st_code',$data['state'])
          ->where('pc_no',$data['pc_no'])
          ->where('election_id',$data['election_id'])
          ->where(function($sql){
            $sql->orWhere('finalized_ac','0')->orWhere('finalize_by_ro','0');
          });
          $object = $sql->count();


          $state_count = DB::table('counting_master_'.strtolower($data['state']))
          ->where('pc_no',$data['pc_no'])
          ->where('election_id',$data['election_id'])
          ->where('finalized_round','0');
          $object_state = $state_count->count();

          if($object > 0 || $object_state>0){
            return true;
          }else{
            return false;
          }

        } 

        
        //get AC not filled message for RO
        public function ac_not_finalize($data = array()){
          $ac   = '';
          $sql  = "GROUP_CONCAT(ac_no) as ac_no";
          $query  = DB::table('counting_finalized_ac')->where('st_code',$data['st_code'])->where('pc_no',$data['pc_no'])->where('election_id',$data['election_id'])->where('finalized_ac',0)->selectRaw($sql)->first();
          
          if(isset($query) && $query){
            $ac = ($query->ac_no)?$query->ac_no:'';
          }

          return $ac;
        }

      public static function winn_lead($data = array()){ 
            $sql_raw = "leading_id,st_code,ac_no,nomination_id,candidate_id,trail_nomination_id,trail_candidate_id,lead_total_vote,trail_total_vote,margin,status,
                        lead_cand_name,lead_cand_hname,lead_cand_party,lead_cand_hparty,trail_cand_name,trail_cand_hname,trail_cand_party,trail_cand_hparty";

                $sql = DB::table('winning_leading_candidate');
                $sql->selectRaw($sql_raw);
                if(!empty($data['st_code'])){
                  $sql->where("st_code", $data['st_code']);
                }

                if(!empty($data['pc_no'])){
                  $sql->where("pc_no", $data['pc_no']);
                }

                if(!empty($data['election_id'])){
                  $sql->where("election_id", $data['election_id']);
                }
                 
                    $query = $sql->first();
     
                return $query;

            }  
        public static function get_allpccandiade($data = array()){ 
            $sql_raw = "counting_pcmaster.*";

                $sql = DB::table('counting_pcmaster');
                $sql->selectRaw($sql_raw);
                if(!empty($data['st_code'])){
                  $sql->where("st_code", $data['st_code']);
                }

                if(!empty($data['pc_no'])){
                  $sql->where("pc_no", $data['pc_no']);
                }

                if(!empty($data['election_id'])){
                  $sql->where("election_id", $data['election_id']);
                }
                if($data['order_by']=='id'){
                $sql->orderBy($data['order_by'], 'ASC');
                } 
                    $query = $sql->get();
     
                return $query;

            }  
      public static function defected_rounds_details($data = array()){ 
                $sql_raw = "id,st_code,pc_no,ac_no,const_type,roundno,status,comments";

                $sql = DB::table('counting_defect_rounds');
                $sql->selectRaw($sql_raw);

                if(!empty($data['st_code'])){
                  $sql->where("st_code", $data['st_code']);
                }

                if(!empty($data['pc_no'])){
                  $sql->where("pc_no", $data['pc_no']);
                }
                 if(!empty($data['ac_no'])){
                  $sql->where("ac_no", $data['ac_no']);
                }
                if(!empty($data['election_id'])){
                  $sql->where("election_id", $data['election_id']);
                }
                    $query = $sql->get();
     
                return $query;

            }  
              
        public function get_previous_total($data = array()){
         $sub_query  = "";
         $sub_sql  = [];
         $previous_round = $data['round'] - 1;
         if($previous_round != 0){
           for($i = $previous_round; $i > 0; $i--) {
             $sub_sql[] = "IFNULL(round".$i.",0)";
           }

           $round_sql = implode('+',$sub_sql);
           if($round_sql){
             $sub_query .= $round_sql." AS previous_total";
           }
         }else{
           $sub_query = "0 AS previous_total";
         }

         $sub_query .= ", table1.nom_id, table1.ac_no, table1.candidate_name, table1.party_name, table1.party_id";
         $object = DB::table("counting_master_".strtolower($data['st_code'])." as table1")->leftJoin('counting_pcmaster','counting_pcmaster.nom_id','=','table1.nom_id')->selectRaw($sub_query)->where('table1.ac_no',$data['ac_no'])->groupBy('table1.ac_no')->groupBy('table1.nom_id')->orderBy('table1.id','ASC')->get();
         return $object;
       }     
		
		public function get_previous_total_by_ac($data = array()){
         $sub_query  = "";
         $sub_sql  = [];
         $previous_round = $data['round'];
         if($previous_round != 1){
           for($i = 1; $i < $previous_round; $i++) {
             $sub_sql[] = "SUM(IFNULL(round".$i.",0))";
           }

           $round_sql = implode('+',$sub_sql);
           if($round_sql){
             $sub_query .= $round_sql." AS previous_total";
           }
         }else{
           $sub_query = "0 AS previous_total";
         }

         $sub_query = $sub_query . ", SUM(IFNULL(round".$previous_round.",0)) as current_total";

         $sub_query .= ", table1.nom_id, table1.ac_no, table1.candidate_name, table1.party_name, table1.party_id";
         $object = DB::table("counting_master_".strtolower($data['st_code'])." as table1")->leftJoin('counting_pcmaster','counting_pcmaster.nom_id','=','table1.nom_id')->where('table1.pc_no',$data['pc_no'])->selectRaw($sub_query)->groupBy('table1.nom_id')->orderBy('table1.id','ASC')->get();
         return $object;
       } 

	   
    }
