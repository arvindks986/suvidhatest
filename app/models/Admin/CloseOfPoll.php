<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;
class CloseOfPoll extends Model
{
  
   protected $table = 'electors_cdac';

   public static function get_sum($data = array()){

        $result = [
            'total_votar_total' => 0,
            'elector_total'     => 0,
            'old_elector_percentage' => 0
        ];

        $sql_raw = "ROUND(SUM(electors_cdac.voter_total)/SUM(electors_cdac.electors_total)*100,2) as old_elector_percentage, IFNULL(SUM(electors_cdac.electors_total),0) as elector_total, IFNULL(SUM(electors_cdac.voter_total),0) as total_votar_total";
    
        $sql = ElectorModel::join('pd_scheduledetail as sd1',[
            ['sd1.pc_no', '=','electors_cdac.pc_no'],
            ['sd1.st_code', '=','electors_cdac.st_code'],
            ['sd1.ac_no', '=','electors_cdac.ac_no'],
        ])->selectRaw($sql_raw);

        if(!empty($data['state'])){
          $sql->where("electors_cdac.st_code", $data['state']);
        }

        if(!empty($data['pc_no'])){
          $sql->where("electors_cdac.pc_no", $data['pc_no']);
        }

        if(!empty($data['phase'])){
          $sql->where("sd1.scheduleid", $data['phase']);
        }

        if(!empty($data['year'])){
          $sql->where("electors_cdac.year", $data['year']);
        }

        if(!empty($data['group_by'])){
            if($data['group_by']=='pc_no'){
                $sql->groupBy("electors_cdac.pc_no")->groupBy("electors_cdac.st_code");
            }else if($data['group_by']=='ac_no'){
                $sql->groupBy("electors_cdac.ac_no")->groupBy("electors_cdac.st_code");
            }else if($data['group_by']=='state'){
                $sql->groupBy("electors_cdac.st_code");
            }
        }

        if(!empty($data['order_by']) && in_array($data['order_by'],['pc_no','ac_no'])){
            if($data['order_by']=='pc_no'){
                $sql->orderByRaw("electors_cdac.pc_no ASC");
            }else if($data['order_by']=='ac_no'){
                $sql->orderByRaw("electors_cdac.pc_no, electors_cdac.ac_no ASC");
            }
        }else{
            $sql->orderByRaw("electors_cdac.pc_no ASC");
        }

        $query = $sql->first();

        if($query){
            $result = $query->toArray();
        }
        return $result;

   }


   public static function get_reports($data = array()){

        $sql_raw = "IFNULL(SUM(sd1.total),0) as total_voter, IFNULL(SUM(sd1.electors_total),0) as total_elector, pc.PC_NO as pc_no, pc.PC_NAME as pc_name, pc.st_code, sd1.ac_no as ac_no";

        $sql = DB::table('pd_scheduledetail as sd1')
        ->join('pd_schedulemaster as sm1',[
              ['sd1.pd_scheduleid', '=','sm1.pd_scheduleid']
        ])
        ->join('m_pc as pc',[
              ['pc.PC_NO', '=','sd1.pc_no'],
              ['pc.ST_CODE', '=','sd1.st_code'],
        ])
        ->leftjoin('m_state as state',[
              ['state.ST_CODE', '=','pc.ST_CODE']
        ]);

        $sql->selectRaw($sql_raw);

        if(!empty($data['state'])){
          $sql->where("sd1.st_code", $data['state']);
        }

        if(!empty($data['phase'])){
          $sql->where("sm1.schedule_id", $data['phase']);
        }

        if(!empty($data['pc_no'])){
          $sql->where("sm1.pc_no", $data['pc_no']);
        }

        if(!empty($data['group_by'])){
            if($data['group_by']=='pc_no'){
                $sql->groupBy("sd1.pc_no")->groupBy("sd1.st_code");
            }else if($data['group_by']=='ac_no'){
                $sql->groupBy("sd1.ac_no")->groupBy("sd1.st_code");
            }else if($data['group_by']=='state'){
                $sql->groupBy("sd1.st_code");
            }
        }

        if(!empty($data['order_by']) && in_array($data['order_by'],['pc_no','ac_no'])){
            if($data['order_by']=='pc_no'){
                $sql->orderByRaw("state.ST_NAME, pc.pc_no, pc.PC_NAME ASC");
            }else if($data['order_by']=='ac_no'){
                $sql->orderByRaw("state.ST_NAME, pc.pc_no, pc.PC_NAME, sd1.ac_no ASC");
            }
        }else{
            $sql->orderByRaw("state.ST_NAME, pc.pc_no, pc.PC_NAME ASC");
        }

        $query = $sql->get();
     
        return $query;

    }

    public static function get_percentage_2019($data = array()){

        $result = [
            'total_elector_male'    => 0,
            'total_elector_female'  => 0,
            'total_elector_other'   => 0,
            'total_elector_total'   => 0,
            'total_voter_male'      => 0,
            'total_voter_female'    => 0,
            'total_voter_other'     => 0,
            'total_voter_total'     => 0,
            'total_percentage'      => 0
        ];

        $sql_raw = "IFNULL(SUM(electors_cdac.electors_male),0) AS total_elector_male, IFNULL(SUM(electors_cdac.electors_female),0) AS total_elector_female, IFNULL(SUM(electors_cdac.electors_other),0) AS total_elector_other, IFNULL(SUM(electors_cdac.electors_total),0) AS total_elector_total, ROUND(SUM(pd_scheduledetail.total)/SUM(electors_cdac.electors_total)*100,2) as total_percentage, IFNULL(SUM(pd_scheduledetail.total_male),0) AS total_voter_male, IFNULL(SUM(pd_scheduledetail.total_female),0) AS total_voter_female, IFNULL(SUM(pd_scheduledetail.total_other),0) AS total_voter_other, IFNULL(SUM(pd_scheduledetail.total),0) AS total_voter_total";
    
        $sql = EndOfPollModel::join('electors_cdac',[
            ['pd_scheduledetail.pc_no', '=','electors_cdac.pc_no'],
            ['pd_scheduledetail.st_code', '=','electors_cdac.st_code'],
            ['pd_scheduledetail.ac_no', '=','electors_cdac.ac_no'],
        ])->selectRaw($sql_raw);

        if(!empty($data['state'])){
          $sql->where("electors_cdac.st_code", $data['state']);
        }

        if(!empty($data['pc_no'])){
          $sql->where("electors_cdac.pc_no", $data['pc_no']);
        }

        if(!empty($data['ac_no'])){
          $sql->where("electors_cdac.ac_no", $data['ac_no']);
        }

        if(!empty($data['phase'])){
          $sql->where("pd_scheduledetail.scheduleid", $data['phase']);
        }

        if(!empty($data['group_by'])){
            if($data['group_by']=='pc_no'){
                $sql->groupBy("electors_cdac.pc_no")->groupBy("electors_cdac.st_code");
            }else if($data['group_by']=='ac_no'){
                $sql->groupBy("electors_cdac.ac_no")->groupBy("electors_cdac.st_code");
            }else if($data['group_by']=='state'){
                $sql->groupBy("electors_cdac.st_code");
            }else{
                
            }
        }else{
            $sql->groupBy("electors_cdac.pc_no")->groupBy("electors_cdac.st_code");
        }

        if(!empty($data['order_by']) && in_array($data['order_by'],['pc_no','ac_no'])){
            if($data['order_by']=='pc_no'){
                $sql->orderByRaw("electors_cdac.pc_no ASC");
            }else if($data['order_by']=='ac_no'){
                $sql->orderByRaw("electors_cdac.pc_no, electors_cdac.ac_no ASC");
            }
        }else{
            $sql->orderByRaw("electors_cdac.pc_no ASC");
        }

        $query = $sql->first();

        if($query){
            $result = $query->toArray();
        }
        return $result;
   }

}