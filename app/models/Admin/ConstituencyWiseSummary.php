<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;
class ConstituencyWiseSummary extends Model
{
  
   protected $table = 'electors_cdac_other_information';

   public static function get_reports($data = array()){

       $sql_raw = "C.st_code as st_code, C.pc_no AS constno, P.PC_NAME AS const_name,COUNT(C.ac_no) AS total_const,E.total_polling_station_s_i_t_c AS total_ps,
      SUM(C.nri_male_electors + C.nri_female_electors + C.nri_third_electors + C.service_male_electors + C.service_female_electors + C.gen_electors_male + C.gen_electors_female + C.gen_electors_other + C.service_third_electors) AS total_electors,ROUND((SUM(C.nri_male_electors + C.nri_female_electors + C.nri_third_electors + C.service_male_electors + C.service_female_electors + C.gen_electors_male + C.gen_electors_female + C.gen_electors_other + C.service_third_electors))/(E.total_polling_station_s_i_t_c),0) AS avg_elector_in_ps";


        $sql = DB::table('electors_cdac as C')
        ->leftjoin('electors_cdac_other_information as E',[
              ['C.st_code', '=','E.st_code'],
              ['C.pc_no', '=','E.pc_no'],
              ['C.year', '=','C.year'],
        ])
        ->leftjoin('m_pc as P',[
              ['C.st_code', '=','P.ST_CODE'],
              ['C.pc_no', '=','P.PC_NO'],
       
        ])
        ->leftjoin('m_election_details as med',[
              ['C.st_code', '=','med.ST_CODE'],
              ['C.pc_no', '=','med.CONST_NO'],
              /*['med.CONST_TYPE', '=',"PC"],
              ['med.ELECTION_ID', '=',"1"],
              ['med.election_status', '!=',"0"],*/
              ['C.year', '=','med.YEAR'],
        ]);

        $sql->selectRaw($sql_raw);

        if(!empty($data['state'])){
          $sql->where("C.st_code", $data['state']);
        }

        if(!empty($data['pc_no'])){
          $sql->where("P.PC_NO", $data['pc_no']);
        }

        // $sql->where("C.year", '2019');
         $sql->where("med.CONST_TYPE", "PC");
         //$sql->where("med.ELECTION_ID", "1");
         $sql->where("med.election_status", '!=','0');
        
       
        $sql->groupBy("C.pc_no")->groupBy("C.st_code");

        $sql->orderByRaw("C.st_code, P.PC_NO ASC");

        $query = $sql->get();
     
        return $query;

    }


     public static function get_sub_total_by_state($st_code){

        $sql_raw = "s.ST_CODE as st_code, s.ST_NAME as st_name, COUNT(C.ac_no) AS total_const,SUM(E.total_polling_station_s_i_t_c) AS total_ps,
          SUM(C.electors_total) AS total_electors,ROUND((SUM(C.electors_total))/(E.total_polling_station_s_i_t_c),0) AS avg_elector_in_ps";

        $sql = DB::table('electors_cdac as C')
        ->leftjoin('electors_cdac_other_information as E',[
              ['C.st_code', '=','E.st_code'],
              ['C.pc_no', '=','E.pc_no'],
              ['C.year', '=','C.year'],
        ])
        ->leftjoin('m_pc as P',[
              ['C.st_code', '=','P.ST_CODE'],
              ['C.pc_no', '=','P.PC_NO'],
       
        ])
        ->leftjoin('m_election_details as med',[
              ['C.st_code', '=','med.ST_CODE'],
              ['C.pc_no', '=','med.CONST_NO'],
              /*['med.CONST_TYPE', '=',"PC"],
              ['med.ELECTION_ID', '=',"1"],
              ['med.election_status', '!=',"0"],*/
              ['C.year', '=','med.YEAR'],
        ])
        ->join('m_state as s',[
              ['p.ST_CODE', '=','s.ST_CODE']
        ]);

        $sql->selectRaw($sql_raw);

        if(!empty($data['state'])){
          $sql->where("C.st_code", $data['state']);
        }

        if(!empty($data['pc_no'])){
          $sql->where("P.PC_NO", $data['pc_no']);
        }

         //$sql->where("C.year", '2019');
          $sql->where("med.CONST_TYPE", "PC");
         //$sql->where("med.ELECTION_ID", "1");
         $sql->where("med.election_status", '!=',"0");

         $sql->groupBy("s.ST_CODE");

         $query = $sql->first();
     
        return $query;

    }




    public static function get_all_voter_in_pc($st_code,$pc_no){

        $sql_raw = "SUM(general_male_voters + general_female_voters + general_other_voters + nri_male_voters + nri_female_voters + nri_other_voters + service_postal_votes_under_section_8 + service_postal_votes_gov) AS total_voter";

        $query = DB::table('electors_cdac_other_information')
              ->selectRaw($sql_raw)
               ->where("st_code", $st_code)
               ->where("pc_no", $pc_no)
               ->first();

     
        return $query;

    }



    public static function get_all_forefeited_cand($st_code,$pc_no){
       
       //male,female and third
     //   $sql = "SELECT cp.st_code,cp.pc_no, SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1 WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'male' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS forefeited_male, SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1 WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'female' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS forefeited_female, SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1 WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'third' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS forefeited_third, SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1 WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS forefeited_total FROM `counting_pcmaster` AS cp JOIN candidate_personal_detail AS C ON C.candidate_id = cp.candidate_id WHERE cp.candidate_id != (SELECT candidate_id FROM winning_leading_candidate AS w1 WHERE w1.pc_no = cp.pc_no AND w1.st_code = cp.st_code) AND cp.candidate_id != 4319 AND cp.st_code = '".$st_code."' AND cp.pc_no = '".$pc_no."'";

       $sql = "SELECT cp.st_code,cp.pc_no, SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1 WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS forefeited_total FROM `counting_pcmaster` AS cp JOIN candidate_personal_detail AS C ON C.candidate_id = cp.candidate_id WHERE cp.candidate_id != (SELECT candidate_id FROM winning_leading_candidate AS w1 WHERE w1.pc_no = cp.pc_no AND w1.st_code = cp.st_code) AND cp.party_id != 1180 AND cp.st_code = '".$st_code."' AND cp.pc_no = '".$pc_no."'";
     
        $query=DB::select($sql);
     
        return $query;


    }




}