<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
use DB;
class TurnoutModel extends Model 
{ 
    
  public static  function get_turnout_report($data = array())
          {
            
            $result = [
                    'electors_male'    => 0,
                    'electors_female'  => 0,
                    'electors_other'   => 0,
                    'electors_total'   => 0,
                    'electors_service' => 0,
                    'grand_total'    => 0,
                    'voter_male'    => 0,
                    'voter_female'   => 0,
                    'voter_other'   => 0,
                    'voter_total'   => 0,
                    
            ];    
            
    $sql = "s.ST_CODE as st_code,s.ST_NAME as st_name,pc.PC_NO as pc_no,pc.PC_NAME as pc_name, IFNULL(SUM(e.electors_male),0) as electors_male, 
                IFNULL(SUM(e.electors_female),0) as electors_female, IFNULL(SUM(e.electors_other),0) as electors_other, 
                IFNULL(SUM(e.electors_total),0) as electors_total, IFNULL(SUM(e.electors_service),0) as electors_service,
                IFNULL(SUM(e.electors_total+e.electors_service),0) as grand_total, IFNULL(SUM(pd.total_male),0) as voter_male,
                IFNULL(SUM(pd.total_female),0) as voter_female,IFNULL(SUM(pd.total_other),0) as voter_other,IFNULL(SUM(pd.total),0) as total ";
    
    $sql = DB::table('electors_cdac as e')
    ->join('pd_scheduledetail as pd',[
        ['e.st_code','=','pd.st_code'],  ['e.pc_no','=','pd.pc_no'], ['e.ac_no','=','pd.ac_no'], ['e.election_id','=','pd.election_id']])
    ->join('m_state as s','s.ST_CODE','=','e.st_code')
    ->join('m_pc as pc',[
        ['pc.PC_NO','=','e.pc_no'],
        ['pc.ST_CODE','=','e.st_code']
    ])
     
    ->selectRaw($sql);
    if(!empty($data['state'])){
      $sql->where('e.st_code',$data['state']);
    }
    if(!empty($data['election_id'])){
      $sql->where('e.election_id',$data['election_id']);
    }
      
    if(!empty($data['group_by'])){
            if($data['group_by']=='pc_no'){
              $sql->groupBy("e.st_code")->groupBy("e.pc_no");
             
            }else if($data['group_by']=='state'){
              $sql->groupBy("e.st_code");
            }else{

            }
        }else{
          $sql->groupBy("e.st_code");
        }
   $query = $sql->get();

    if($query){
      $result =$query;
    }

    return $result;

  }
 
 public static function get_turnout_votes($data = array())
          {
            
            $result = [
                    'evm_vote'    => 0,
                    'postal_vote'  => 0,
                    'migrate_votes'   => 0,
                    'total_vote'   => 0,
             ];   
            
    $sql = "s.ST_CODE,s.ST_NAME,pc.PC_NO,pc.PC_NAME, IFNULL(SUM(c.evm_vote),0) as evm_vote, IFNULL(SUM(c.postal_vote),0) as postal_vote, 
                IFNULL(SUM(c.migrate_votes),0) as migrate_votes, IFNULL(SUM(c.total_vote),0) as total_vote  ";
    
    $sql = DB::table('counting_pcmaster as c')->join('m_state as s','s.ST_CODE','=','c.st_code')
    ->join('m_pc as pc',[
        ['pc.PC_NO','=','c.pc_no'],
        ['pc.ST_CODE','=','c.st_code']
    ]) ->selectRaw($sql);
    if(!empty($data['state'])){
      $sql->where('c.st_code',$data['state']);
    }
    if(!empty($data['election_id'])){
      $sql->where('c.election_id',$data['election_id']);
    }
    if(!empty($data['pc_no'])){
      $sql->where('c.pc_no',$data['pc_no']);
    }   
      
      if(!empty($data['phase'])){
          $sql->where("pd.schedule_id", $data['phase']);
        }

    if(!empty($data['group_by'])){
            if($data['group_by']=='pc_no'){
              $sql->groupBy("c.st_code")->groupBy("c.pc_no");
             
            }else if($data['group_by']=='state'){
              $sql->groupBy("c.st_code");
             }else if($data['group_by']=='national'){
                 
            } else{

            }
        }else{
          $sql->groupBy("c.st_code");
        }
        $query = $sql->first();

        if($query){
          $result =$query;
        }

        return $result;

  }   


  public static function get_total_elector($data = array()){

        $result = [
            'total_male'    => 0,
            'total_female'  => 0,
            'total_other'   => 0,
            'total'         => 0,
            'total_service' => 0,
            'grand_total'   => 0
        ];

        $sql_raw = "IFNULL(SUM(e.electors_male),0) AS total_male, IFNULL(SUM(e.electors_female),0) AS total_female, IFNULL(SUM(e.electors_other),0) AS total_other, IFNULL(SUM(e.electors_total),0) AS total,IFNULL(SUM(e.electors_service),0) as total_service,
                IFNULL(SUM(e.electors_total+e.electors_service),0) as grand_total";
    
       $sql = DB::table('electors_cdac as e')->selectRaw($sql_raw);

        if(!empty($data['state'])){
          $sql->where("e.st_code", $data['state']);
        }

        if(!empty($data['pc_no'])){
          $sql->where("e.pc_no", $data['pc_no']);
        }

        if(!empty($data['ac_no'])){
          $sql->where("e.ac_no", $data['ac_no']);
        }

       
        if(!empty($data['election_id'])){
          $sql->where("e.election_id", $data['election_id']);
        }

        if(!empty($data['group_by'])){
            if($data['group_by']=='pc_no'){
                $sql->groupBy("e.pc_no")->groupBy("e.st_code");
            }else if($data['group_by']=='ac_no'){
                $sql->groupBy("e.ac_no")->groupBy("e.st_code");
            }else if($data['group_by']=='national'){
                 
            } else{
                $sql->groupBy("e.st_code");
            }
        }else{
            $sql->groupBy("e.pc_no")->groupBy("e.st_code");
        }

         if($data['order_by']=='pc_no'){
                $sql->orderByRaw("e.pc_no ASC");
            } 
        else{
            $sql->orderByRaw("e.st_code ASC");
        }

        $query = $sql->first();
        
        if($query){
          $result =$query;
        }
        // if($query){
        //     $result = $query->toArray();
        // }
        return $result;

   } // end get_total_elector

   public static function get_total_voters($data = array()){

        $result = [
            'voter_male'    => 0,
            'voter_female'   => 0,
            'voter_other'   => 0,
            'voter_total'   => 0,
        ];

       $sql_raw1 = "IFNULL(SUM(pd.total_male),0) as voter_male, IFNULL(SUM(pd.total_female),0) as voter_female,
               IFNULL(SUM(pd.total_other),0) as voter_other,IFNULL(SUM(pd.total),0) as total ";
       
       $sql = DB::table('pd_scheduledetail as pd')->selectRaw($sql_raw1);

        if(!empty($data['state'])){
          $sql->where("pd.st_code", $data['state']);
        }

        if(!empty($data['pc_no'])){
          $sql->where("pd.pc_no", $data['pc_no']);
        }

        if(!empty($data['ac_no'])){
          $sql->where("pd.ac_no", $data['ac_no']);
        }

       
        if(!empty($data['election_id'])){
          $sql->where("pd.election_id", $data['election_id']);
        }

        if(!empty($data['group_by'])){
            if($data['group_by']=='pc_no'){
                $sql->groupBy("pd.pc_no")->groupBy("pd.st_code");
            }else if($data['group_by']=='ac_no'){
                $sql->groupBy("pd.ac_no")->groupBy("pd.st_code");
            }else if($data['group_by']=='national'){
                 
            } else{
                $sql->groupBy("pd.st_code");
            }
        }else{
            $sql->groupBy("pd.pc_no")->groupBy("pd.st_code");
        }

         if($data['order_by']=='pc_no'){
                $sql->orderByRaw("pd.pc_no ASC");
            } 
        else{
            $sql->orderByRaw("pd.st_code ASC");
        }

        $query = $sql->first();
        
        if($query){
          $result =$query;
        }
        // if($query){
        //     $result = $query->toArray();
        // }
        return $result;

   } // end get_total_elector
}
