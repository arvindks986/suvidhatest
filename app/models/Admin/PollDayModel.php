<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\models\Admin\StateModel;
class PollDayModel extends Model
{
    protected $table = 'pd_scheduledetail';

    public static function get_reports($data = array()){

        $sql_raw = "IFNULL(ROUND(AVG(est_turnout_round1),2),0) as est_total_round1, IFNULL(ROUND(AVG(est_turnout_round2),2),0) as est_total_round2, IFNULL(ROUND(AVG(est_turnout_round3),2),0) as est_total_round3, IFNULL(ROUND(AVG(est_turnout_round4),2),0) as est_total_round4, IFNULL(ROUND(AVG(est_turnout_round5),2),0) as est_total_round5, IFNULL(ROUND(AVG(close_of_poll),2),0) as close_of_poll, IFNULL(ROUND(AVG(est_turnout_total),2),0) as est_total, COUNT(*) as total_record, IFNULL(ROUND((SUM(est_voters) * 100 )/SUM(electors_total),2),0) as total_percentage, pc.PC_NO as pc_no, pc.PC_NAME as pc_name, pc.st_code, state.ST_NAME as st_name, sd1.ac_no as ac_no";

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
            }else{

            }
        }else{
          $sql->groupBy("sd1.st_code");
        }

        if(!empty($data['order_by'])){
            if($data['order_by']=='pc_no'){
                $sql->orderByRaw("state.ST_NAME, pc.pc_no, pc.PC_NAME ASC");
            }else if($data['order_by']=='ac_no'){
                $sql->orderByRaw("state.ST_NAME, pc.pc_no, pc.PC_NAME, sd1.ac_no ASC");
            }else if($data['order_by']=='state'){
                $sql->orderByRaw("state.ST_NAME ASC");
            }else{
              
            }
        }else{
            $sql->orderByRaw("state.ST_NAME, pc.pc_no, pc.PC_NAME ASC");
        }

        $query = $sql->get();
     
        return $query;

    }

    public static function get_report($data = array()){

        $result = [
              "est_total_round1"      =>  0,
              "est_total_round2"      =>  0,
              "est_total_round3"      =>  0,
              "est_total_round4"      =>  0,
              "est_total_round5"      =>  0,
              "close_of_poll"         =>  0,
              "est_total"             =>  0,
              "total_record"          =>  0,
              "total_percentage"      =>  0,
              "pc_no"                 =>  "",
              "pc_name"               =>  "",
              "st_code"               =>  "",
        ];



        $sql_raw = "IFNULL(ROUND(AVG(est_turnout_round1),2),0) as est_total_round1, IFNULL(ROUND(AVG(est_turnout_round2),2),0) as est_total_round2, IFNULL(ROUND(AVG(est_turnout_round3),2),0) as est_total_round3, IFNULL(ROUND(AVG(est_turnout_round4),2),0) as est_total_round4, IFNULL(ROUND(AVG(est_turnout_round5),2),0) as est_total_round5, IFNULL(ROUND(AVG(close_of_poll),2),0) as close_of_poll, IFNULL(ROUND(AVG(est_turnout_total),2),0) as est_total, COUNT(*) as total_record, IFNULL(ROUND((SUM(est_voters) * 100 )/SUM(electors_total),2),0) as total_percentage, pc.PC_NO as pc_no, pc.PC_NAME as pc_name, pc.st_code, sd1.ac_no as ac_no";

        $sql = DB::table('pd_scheduledetail as sd1')
        ->join('pd_schedulemaster as sm1',[
              ['sd1.pd_scheduleid', '=','sm1.pd_scheduleid']
        ])
        ->join('m_pc as pc',[
              ['pc.PC_NO', '=','sd1.pc_no'],
              ['pc.ST_CODE', '=','sd1.st_code'],
        ])
        ->join('m_state as state',[
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

        if(!empty($data['group_by']) && in_array($data['group_by'],['pc_no','ac_no'])){
            if($data['group_by']=='pc_no'){
                $sql->groupBy("sd1.pc_no")->groupBy("sd1.st_code");
            }else if($data['group_by']=='ac_no'){
                $sql->groupBy("sd1.ac_no")->groupBy("sd1.st_code");
            }
        }else{
            $sql->groupBy("sd1.st_code");
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

        $query = $sql->first();
        
        if($query){
            $result = [
                  "est_total_round1"      =>  $query->est_total_round1,
                  "est_total_round2"      =>  $query->est_total_round2,
                  "est_total_round3"      =>  $query->est_total_round3,
                  "est_total_round4"      =>  $query->est_total_round4,
                  "est_total_round5"      =>  $query->est_total_round5,
                  "close_of_poll"         =>  $query->close_of_poll,
                  "est_total"             =>  $query->est_total,
                  "total_record"          =>  $query->total_record,
                  "total_percentage"      =>  $query->total_percentage,
                  "pc_no"                 =>  $query->pc_no,
                  "pc_name"               =>  $query->pc_name,
                  "st_code"               =>  $query->st_code,
            ];
        }
        return $result;

    }


    public static function get_average_sum($data = array()){

      $sql_raw  = "IFNULL(ROUND((SUM(est_voters) * 100 )/SUM(electors_total),2),0) as total_percent";
      $sql    = DB::table('pd_scheduledetail as sd1')->selectRaw($sql_raw);
      if(!empty($data['state'])){
        $sql->where("sd1.st_code", $data['state']);
      }

      if(!empty($data['phase'])){
        $sql->where("sd1.scheduleid", $data['phase']);
      }

      if(!empty($data['pc_no'])){
        $sql->where("sd1.pc_no", $data['pc_no']);
      }

      if(!empty($data['group_by']) && in_array($data['group_by'],['pc_no','ac_no'])){
          if($data['group_by']=='pc_no'){
            $sql->groupBy("sd1.pc_no")->groupBy("sd1.st_code");
          }else if($data['group_by']=='ac_no'){
            $sql->groupBy("sd1.ac_no")->groupBy("sd1.st_code");
          }else{
          $sql->groupBy("sd1.st_code");
        }
      }

      $query = $sql->first();
      return ($query)?$query->total_percent:0;

    }


    //below are waste queries
  
    public function get_scrutny_report_ceo($data = array()){

        $sql = DB::table('m_election_details')->join('m_pc',[
              ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
              ['m_election_details.CONST_NO', '=','m_pc.PC_NO']]);

        if(!empty($data['state_code'])){
           $sql->where('m_election_details.ST_CODE',$data['state_code']);
        }

        if(!empty($data['pc_no'])){
          $sql->where('m_election_details.CONST_NO',$data['pc_no']);
        }

        
        $sql->where('m_election_details.CONST_TYPE','PC');
        

        if(!empty($data['phase_id'])){
          $sql->where('m_election_details.PHASE_NO',$data['phase_id']);
        }

        return $sql->orderByRaw('m_pc.ST_CODE,m_pc.PC_NO ASC')//orderBy('m_pc.ST_CODE')->orderBy('m_pc.PC_NO')
              ->select('m_election_details.*','m_pc.*','m_election_details.CONST_NO as CCODE','m_election_details.ST_CODE as st_code')
              ->groupBy('m_election_details.CCODE')->get();
    }


    public function get_ac_by_pc($data = array()){

        $sql = DB::table('m_ac')->leftjoin('m_election_details',[
              ['m_ac.ST_CODE', '=','m_election_details.ST_CODE'],
              ['m_ac.AC_NO', '=','m_election_details.CONST_NO']]);

        if(!empty($data['state_code'])){
           $sql->where('m_ac.ST_CODE',$data['state_code']);
        }

        if(!empty($data['pc_no'])){
            $sql->where('m_ac.PC_NO',$data['pc_no']);
        }

        $sql->where('CONST_TYPE','PC');
     
        if(!empty($data['phase_id'])){
          $sql->where('m_election_details.ScheduleID',$data['phase_id']);
        }

        return $sql->orderBy('m_ac.AC_NO', 'ASC')->orderBy('m_ac.AC_NAME', 'ASC')
              ->select('m_ac.*')
              ->groupBy('m_ac.AC_NO')->get();
    }



  //not delete  
  function get_total_by_state($data = array())
  {

    $result = [
            'round_1_m'              => 0,
            'round_1_f'              => 0,
            'round_1_o'              => 0,
            'round_1_t'              => 0,
            'round_2_m'              => 0,
            'round_2_f'              => 0,
            'round_2_o'              => 0,
            'round_2_t'              => 0,
            'round_3_m'              => 0,
            'round_3_f'              => 0,
            'round_3_o'              => 0,
            'round_3_t'              => 0,
            'round_4_m'              => 0,
            'round_4_f'              => 0,
            'round_4_o'              => 0,
            'round_4_t'              => 0,
            'round_5_m'              => 0,
            'round_5_f'              => 0,
            'round_5_o'              => 0,
            'round_5_t'              => 0,
            'round_end_m'              => 0,
            'round_end_f'              => 0,
            'round_end_o'              => 0,
            'round_end_t'              => 0,
            'total_male'            => 0,
            'total_female'          => 0,
            'total_other'           => 0,
            'total'                 => 0,
    ];  
    $sql = "IFNULL(SUM(round1_voter_male),0) as round_1_m, IFNULL(SUM(round1_voter_female),0) as round_1_f, IFNULL(SUM(round1_voter_other),0) as round_1_o, IFNULL(SUM(round1_voter_total),0) as round_1_t, IFNULL(SUM(round2_voter_male),0) as round_2_m, IFNULL(SUM(round2_voter_female),0) as round_2_f, IFNULL(SUM(round2_voter_other),0) as round_2_o, IFNULL(SUM(round2_voter_total),0) as round_2_t, IFNULL(SUM(round3_voter_male),0) as round_3_m, IFNULL(SUM(round3_voter_female),0) as round_3_f, IFNULL(SUM(round3_voter_other),0) as round_3_o, IFNULL(SUM(round3_voter_total),0) as round_3_t, IFNULL(SUM(round4_voter_male),0) as round_4_m, IFNULL(SUM(round4_voter_female),0) as round_4_f, IFNULL(SUM(round4_voter_other),0) as round_4_o, IFNULL(SUM(round4_voter_total),0) as round_4_t, IFNULL(SUM(round5_voter_male),0) as round_5_m, IFNULL(SUM(round5_voter_female),0) as round_5_f, IFNULL(SUM(round5_voter_other),0) as round_5_o, IFNULL(SUM(round5_voter_total),0) as round_5_t, IFNULL(SUM(end_voter_male),0) as round_end_m, IFNULL(SUM(end_voter_female),0) as round_end_f, IFNULL(SUM(end_voter_other),0) as round_end_o, IFNULL(SUM(end_voter_total),0) as round_end_t, IFNULL(SUM(total_male),0) as total_male, IFNULL(SUM(total_female),0) as total_female, IFNULL(SUM(total_other),0) as total_other, IFNULL(SUM(total),0) as total";


    $sql = DB::table('pd_scheduledetail as ps')->join('pd_schedulemaster','pd_schedulemaster.pd_scheduleid','=','ps.pd_scheduleid')->join('m_pc',[
        ['m_pc.PC_NO','=','ps.pc_no'],
        ['m_pc.ST_CODE','=','ps.st_code']
    ])
    ->join('m_election_details',[
              ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
              ['m_election_details.CONST_NO', '=','m_pc.PC_NO']])
    ->selectRaw($sql);
    if(!empty($data['st_code'])){
      $sql->where('ps.st_code',$data['st_code']);
    }
    if(!empty($data['phase'])){
      $sql->where('pd_schedulemaster.schedule_id',$data['phase']);
    }

    $sql->where('m_election_details.CONST_TYPE','PC');
    
    $query = $sql->first();

    if($query){
      $result = [
            'round_1_m'              => $query->round_1_m,
            'round_1_f'              => $query->round_1_f,
            'round_1_o'              => $query->round_1_o,
            'round_1_t'              => $query->round_1_t,
            'round_2_m'              => $query->round_2_m,
            'round_2_f'              => $query->round_2_f,
            'round_2_o'              => $query->round_2_o,
            'round_2_t'              => $query->round_2_t,
            'round_3_m'              => $query->round_3_m,
            'round_3_f'              => $query->round_3_f,
            'round_3_o'              => $query->round_3_o,
            'round_3_t'              => $query->round_3_t,
            'round_4_m'              => $query->round_4_m,
            'round_4_f'              => $query->round_4_f,
            'round_4_o'              => $query->round_4_o,
            'round_4_t'              => $query->round_4_t,
            'round_5_m'              => $query->round_5_m,
            'round_5_f'              => $query->round_5_f,
            'round_5_o'              => $query->round_5_o,
            'round_5_t'              => $query->round_5_t,
            'round_end_m'              => $query->round_end_m,
            'round_end_f'              => $query->round_end_f,
            'round_end_o'              => $query->round_end_o,
            'round_end_t'              => $query->round_end_t, 
            'total_male'            => $query->total_male,
            'total_female'          => $query->total_female,
            'total_other'           => $query->total_other,
            'total'                 => $query->total,
        ];  
    }

    return $result;

  }

  public function get_total_round($data = array()){

    $total = [
        'round_1_total' => 0,
        'round_2_total' => 0,
        'round_3_total' => 0,
        'round_4_total' => 0,
        'round_5_total' => 0,
        'round_end_total' => 0,
        'total_voter_male'      => 0,
        'total_voter_female'    => 0,
        'total_voter_other'     => 0,
    ];
    $sql = "IFNULL(SUM(round1_voter_male+round2_voter_male+round3_voter_male+round4_voter_male+round5_voter_male+end_voter_male),0) as total_voter_male,IFNULL(SUM(round1_voter_female+round2_voter_female+round3_voter_female+round4_voter_female+round5_voter_female+end_voter_female),0) as total_voter_female, IFNULL(SUM(round1_voter_other+round2_voter_other+round3_voter_other+round4_voter_other+round5_voter_other+end_voter_other),0) as total_voter_other, IFNULL(SUM(round1_voter_total),0) as round_1_total, IFNULL(SUM(round2_voter_total),0) as round_2_total, IFNULL(SUM(round3_voter_total),0) as round_3_total, IFNULL(SUM(round4_voter_total),0) as round_4_total, IFNULL(SUM(round5_voter_total),0) as round_5_total, IFNULL(SUM(end_voter_total),0) as round_end_total, IFNULL(SUM(total_male),0) as total_male, IFNULL(SUM(total_female),0) as total_female, IFNULL(SUM(total_other),0) as total_other, IFNULL(SUM(total),0) as total";
    $sql = DB::table('pd_scheduledetail as ps')->join('pd_schedulemaster','pd_schedulemaster.pd_scheduleid','=','ps.pd_scheduleid')->join('m_pc',[
        ['m_pc.PC_NO','=','ps.pc_no'],
        ['m_pc.ST_CODE','=','ps.st_code']
    ])->selectRaw($sql);
    if(!empty($data['st_code'])){
      $sql->where('ps.st_code',$data['st_code']);
    }
    if(!empty($data['pc_no'])){
      $sql->where('ps.pc_no',$data['pc_no']);
    }

    if(!empty($data['phase'])){
      $sql->where('pd_schedulemaster.schedule_id',$data['phase']);
    }
    $sql->where('ps.pc_no','!=','0')->where('ps.pc_no','!=',NULL);
    $query = $sql->first();
    if($query){
        $total = $query;
    }
    return $total;
  }


  public function get_elector_total($data = array()){
    $total = [
        'gen_m' => 0,
        'gen_f' => 0,
        'gen_o' => 0,
        'gen_t' => 0,
    ];
    $sql = "IFNULL(SUM(electors_male),0) as gen_m, IFNULL(SUM(electors_female),0) as gen_f, IFNULL(SUM(electors_other),0) as gen_o, IFNULL(SUM(electors_total),0) as gen_t";
    $sql = DB::table('electors_cdac as ed')->selectRaw($sql);
    if(!empty($data['st_code'])){
      $sql->where('ed.st_code',$data['st_code']);
    }
    if(!empty($data['pc_no'])){
      $sql->where('ed.pc_no',$data['pc_no']);
    }
    if(!empty($data['phase'])){
      $sql->where('ed.scheduledid',$data['phase']);
    }

    $sql->where('ed.pc_no','!=','0')->where('ed.pc_no','!=',NULL);
    $query = $sql->first();
    if($query){
        $total = $query;
    }
    return $total;
  }


  public function get_schedule_detail($data){

    $total = [
            'round_1_m'              => 0,
            'round_1_f'              => 0,
            'round_1_o'              => 0,
            'round_1_t'              => 0,
            'round_2_m'              => 0,
            'round_2_f'              => 0,
            'round_2_o'              => 0,
            'round_2_t'              => 0,
            'round_3_m'              => 0,
            'round_3_f'              => 0,
            'round_3_o'              => 0,
            'round_3_t'              => 0,
            'round_4_m'              => 0,
            'round_4_f'              => 0,
            'round_4_o'              => 0,
            'round_4_t'              => 0,
            'round_5_m'              => 0,
            'round_5_f'              => 0,
            'round_5_o'              => 0,
            'round_5_t'              => 0,
            'round_end_m'            => 0,
            'round_end_f'            => 0,
            'round_end_o'            => 0,
            'round_end_t'            => 0,
            'total_male'            => 0,
            'total_female'          => 0,
            'total_other'           => 0,
            'total'                 => 0,
    ];

    $sql = "IFNULL(SUM(round1_voter_male),0) as round_1_m, IFNULL(SUM(round1_voter_female),0) as round_1_f, IFNULL(SUM(round1_voter_other),0) as round_1_o, IFNULL(SUM(round1_voter_total),0) as round_1_t, IFNULL(SUM(round2_voter_male),0) as round_2_m, IFNULL(SUM(round2_voter_female),0) as round_2_f, IFNULL(SUM(round2_voter_other),0) as round_2_o, IFNULL(SUM(round2_voter_total),0) as round_2_t, IFNULL(SUM(round3_voter_male),0) as round_3_m, IFNULL(SUM(round3_voter_female),0) as round_3_f, IFNULL(SUM(round3_voter_other),0) as round_3_o, IFNULL(SUM(round3_voter_total),0) as round_3_t, IFNULL(SUM(round4_voter_male),0) as round_4_m, IFNULL(SUM(round4_voter_female),0) as round_4_f, IFNULL(SUM(round4_voter_other),0) as round_4_o, IFNULL(SUM(round4_voter_total),0) as round_4_t, IFNULL(SUM(round5_voter_male),0) as round_5_m, IFNULL(SUM(round5_voter_female),0) as round_5_f, IFNULL(SUM(round5_voter_other),0) as round_5_o, IFNULL(SUM(round5_voter_total),0) as round_5_t, IFNULL(SUM(end_voter_male),0) as round_end_m, IFNULL(SUM(end_voter_female),0) as round_end_f, IFNULL(SUM(end_voter_other),0) as round_end_o, IFNULL(SUM(end_voter_total),0) as round_end_t, IFNULL(SUM(total_male),0) as total_male, IFNULL(SUM(total_female),0) as total_female, IFNULL(SUM(total_other),0) as total_other, IFNULL(SUM(total),0) as total";


    $sql = DB::table('pd_scheduledetail')->join('pd_schedulemaster','pd_schedulemaster.pd_scheduleid','=','pd_scheduledetail.pd_scheduleid')->join('m_pc',[
        ['m_pc.PC_NO','=','pd_scheduledetail.pc_no'],
        ['m_pc.ST_CODE','=','pd_scheduledetail.st_code']
    ])->selectRaw($sql);
    if(!empty($data['st_code'])){
      $sql->where('pd_scheduledetail.st_code',$data['st_code']);
    }
    if(!empty($data['pc_no'])){
      $sql->where('pd_scheduledetail.pc_no',$data['pc_no']);
    }
    if(!empty($data['phase'])){
      $sql->where('pd_schedulemaster.schedule_id',$data['phase']);
    }
    $sql->where('pd_scheduledetail.pc_no','!=','0')->where('pd_scheduledetail.pc_no','!=',NULL);
    $query = $sql->first();

    if($query){
       
        $total = [
            'round_1_m'              => $query->round_1_m,
            'round_1_f'              => $query->round_1_f,
            'round_1_o'              => $query->round_1_o,
            'round_1_t'              => $query->round_1_t,
            'round_2_m'              => $query->round_2_m,
            'round_2_f'              => $query->round_2_f,
            'round_2_o'              => $query->round_2_o,
            'round_2_t'              => $query->round_2_t,
            'round_3_m'              => $query->round_3_m,
            'round_3_f'              => $query->round_3_f,
            'round_3_o'              => $query->round_3_o,
            'round_3_t'              => $query->round_3_t,
            'round_4_m'              => $query->round_4_m,
            'round_4_f'              => $query->round_4_f,
            'round_4_o'              => $query->round_4_o,
            'round_4_t'              => $query->round_4_t,
            'round_5_m'              => $query->round_5_m,
            'round_5_f'              => $query->round_5_f,
            'round_5_o'              => $query->round_5_o,
            'round_5_t'              => $query->round_5_t,
            'round_end_m'            => $query->round_end_m,
            'round_end_f'            => $query->round_end_f,
            'round_end_o'            => $query->round_end_o,
            'round_end_t'            => $query->round_end_t,
            'total_male'            => $query->total_male,
            'total_female'          => $query->total_female,
            'total_other'           => $query->total_other,
            'total'                 => $query->total,
        ];
    }
    return $total;
  }


  //for ac

  public function get_schedule_detail_for_ac($data){

    $total = [
            'round_1_m'              => 0,
            'round_1_f'              => 0,
            'round_1_o'              => 0,
            'round_1_t'              => 0,
            'round_2_m'              => 0,
            'round_2_f'              => 0,
            'round_2_o'              => 0,
            'round_2_t'              => 0,
            'round_3_m'              => 0,
            'round_3_f'              => 0,
            'round_3_o'              => 0,
            'round_3_t'              => 0,
            'round_4_m'              => 0,
            'round_4_f'              => 0,
            'round_4_o'              => 0,
            'round_4_t'              => 0,
            'round_5_m'              => 0,
            'round_5_f'              => 0,
            'round_5_o'              => 0,
            'round_5_t'              => 0,
            'round_end_m'            => 0,
            'round_end_f'            => 0,
            'round_end_o'            => 0,
            'round_end_t'            => 0,
            'total_male'            => 0,
            'total_female'          => 0,
            'total_other'           => 0,
            'total'                 => 0,
    ];

    $sql = "IFNULL(SUM(round1_voter_male),0) as round_1_m, IFNULL(SUM(round1_voter_female),0) as round_1_f, IFNULL(SUM(round1_voter_other),0) as round_1_o, IFNULL(SUM(round1_voter_total),0) as round_1_t, IFNULL(SUM(round2_voter_male),0) as round_2_m, IFNULL(SUM(round2_voter_female),0) as round_2_f, IFNULL(SUM(round2_voter_other),0) as round_2_o, IFNULL(SUM(round2_voter_total),0) as round_2_t, IFNULL(SUM(round3_voter_male),0) as round_3_m, IFNULL(SUM(round3_voter_female),0) as round_3_f, IFNULL(SUM(round3_voter_other),0) as round_3_o, IFNULL(SUM(round3_voter_total),0) as round_3_t, IFNULL(SUM(round4_voter_male),0) as round_4_m, IFNULL(SUM(round4_voter_female),0) as round_4_f, IFNULL(SUM(round4_voter_other),0) as round_4_o, IFNULL(SUM(round4_voter_total),0) as round_4_t, IFNULL(SUM(round5_voter_male),0) as round_5_m, IFNULL(SUM(round5_voter_female),0) as round_5_f, IFNULL(SUM(round5_voter_other),0) as round_5_o, IFNULL(SUM(round5_voter_total),0) as round_5_t, IFNULL(SUM(end_voter_male),0) as round_end_m, IFNULL(SUM(end_voter_female),0) as round_end_f, IFNULL(SUM(end_voter_other),0) as round_end_o, IFNULL(SUM(end_voter_total),0) as round_end_t, IFNULL(SUM(total_male),0) as total_male, IFNULL(SUM(total_female),0) as total_female, IFNULL(SUM(total_other),0) as total_other, IFNULL(SUM(total),0) as total";

    $sql = DB::table('pd_scheduledetail')->join('pd_schedulemaster','pd_schedulemaster.pd_scheduleid','=','pd_scheduledetail.pd_scheduleid')->join('m_ac',[
        ['m_ac.AC_NO','=','pd_scheduledetail.ac_no'],
        ['m_ac.ST_CODE','=','pd_scheduledetail.st_code']
    ])->selectRaw($sql);
    if(!empty($data['st_code'])){
      $sql->where('pd_scheduledetail.st_code',$data['st_code']);
    }

    if(!empty($data['ac_no'])){
      $sql->where('pd_scheduledetail.ac_no',$data['ac_no']);
    }

    if(!empty($data['pc_no'])){
      $sql->where('pd_scheduledetail.pc_no',$data['pc_no']);
    }

    if(!empty($data['phase'])){
      $sql->where('pd_schedulemaster.schedule_id',$data['phase']);
    }
    $sql->where('pd_scheduledetail.ac_no','!=','0')->where('pd_scheduledetail.ac_no','!=',NULL);
    $query = $sql->first();
    if($query){
        $total = [
            
            'round_1_m'              => $query->round_1_m,
            'round_1_f'              => $query->round_1_f,
            'round_1_o'              => $query->round_1_o,
            'round_1_t'              => $query->round_1_t,
            'round_2_m'              => $query->round_2_m,
            'round_2_f'              => $query->round_2_f,
            'round_2_o'              => $query->round_2_o,
            'round_2_t'              => $query->round_2_t,
            'round_3_m'              => $query->round_3_m,
            'round_3_f'              => $query->round_3_f,
            'round_3_o'              => $query->round_3_o,
            'round_3_t'              => $query->round_3_t,
            'round_4_m'              => $query->round_4_m,
            'round_4_f'              => $query->round_4_f,
            'round_4_o'              => $query->round_4_o,
            'round_4_t'              => $query->round_4_t,
            'round_5_m'              => $query->round_5_m,
            'round_5_f'              => $query->round_5_f,
            'round_5_o'              => $query->round_5_o,
            'round_5_t'              => $query->round_5_t,
            'round_end_m'            => $query->round_end_m,
            'round_end_f'            => $query->round_end_f,
            'round_end_o'            => $query->round_end_o,
            'round_end_t'            => $query->round_end_t,
            'total_male'            => $query->total_male,
            'total_female'          => $query->total_female,
            'total_other'           => $query->total_other,
            'total'                 => $query->total,
        
        ];
    }
    return $total;
  }

  public function get_total_round_for_ac($data = array()){
    $total = [
        'round_1_total' => 0,
        'round_2_total' => 0,
        'round_3_total' => 0,
        'round_4_total' => 0,
        'round_5_total' => 0,
        'round_end_total' => 0,
        'total_voter_male'      => 0,
        'total_voter_female'    => 0,
        'total_voter_other'     => 0,
    ];
    $sql = "IFNULL(SUM(round1_voter_male+round2_voter_male+round3_voter_male+round4_voter_male+round5_voter_male+end_voter_male),0) as total_voter_male,IFNULL(SUM(round1_voter_female+round2_voter_female+round3_voter_female+round4_voter_female+round5_voter_female+end_voter_female),0) as total_voter_female, IFNULL(SUM(round1_voter_other+round2_voter_other+round3_voter_other+round4_voter_other+round5_voter_other+end_voter_other),0) as total_voter_other, IFNULL(SUM(round1_voter_total),0) as round_1_total, IFNULL(SUM(round2_voter_total),0) as round_2_total, IFNULL(SUM(round3_voter_total),0) as round_3_total, IFNULL(SUM(round4_voter_total),0) as round_4_total, IFNULL(SUM(round5_voter_total),0) as round_5_total, IFNULL(SUM(end_voter_total),0) as round_end_total, IFNULL(SUM(total_male),0) as total_male, IFNULL(SUM(total_female),0) as total_female, IFNULL(SUM(total_other),0) as total_other, IFNULL(SUM(total),0) as total";
    $sql = DB::table('pd_scheduledetail as ps')->join('pd_schedulemaster','pd_schedulemaster.pd_scheduleid','=','ps.pd_scheduleid')->join('m_ac',[
        ['m_ac.AC_NO','=','ps.ac_no'],
        ['m_ac.ST_CODE','=','ps.st_code']
    ])->selectRaw($sql);
    if(!empty($data['st_code'])){
      $sql->where('ps.st_code',$data['st_code']);
    }
    if(!empty($data['ac_no'])){
      $sql->where('ps.ac_no',$data['ac_no']);
    }
    if(!empty($data['pc_no'])){
      $sql->where('ps.pc_no',$data['pc_no']);
    }
    if(!empty($data['phase'])){
      $sql->where('pd_schedulemaster.schedule_id',$data['phase']);
    }
    $sql->where('ps.ac_no','!=','0')->where('ps.ac_no','!=',NULL);
    $query = $sql->first();
    if($query){
        $total = $query;
    }
    return $total;
  }


  public function get_elector_total_for_ac($data = array()){
    $total = [
        'gen_m' => 0,
        'gen_f' => 0,
        'gen_o' => 0,
        'gen_t' => 0,
    ];
    $sql = "IFNULL(SUM(electors_male),0) as gen_m, IFNULL(SUM(electors_female),0) as gen_f, IFNULL(SUM(electors_other),0) as gen_o, IFNULL(SUM(electors_total),0) as gen_t";
    $sql = DB::table('electors_cdac as ed')->join('m_ac',[
        ['m_ac.AC_NO','=','ed.ac_no'],
        ['m_ac.ST_CODE','=','ed.st_code']
    ])->selectRaw($sql);
    if(!empty($data['st_code'])){
      $sql->where('ed.st_code',$data['st_code']);
    }
    if(!empty($data['ac_no'])){
      $sql->where('ed.ac_no',$data['ac_no']);
    }
    if(!empty($data['pc_no'])){
      $sql->where('ed.pc_no',$data['pc_no']);
    }
    if(!empty($data['phase'])){
      $sql->where('ed.scheduledid',$data['phase']);
    }


    $sql->where('ed.ac_no','!=','0')->where('ed.ac_no','!=',NULL);
    $query = $sql->first();
    if($query){
        $total = $query;
    }
    return $total;
  }

  public function get_pc_detail($filter_array = array()){
    $sql = DB::table('m_pc')->where('PC_NO',$filter_array['const_no'])->where('ST_CODE',$filter_array['st_code'])->first();
    if(!$sql){
      return '';
    }
    return $sql;
  }
  
  //get ac report only
  public static function get_ac_reports($data = array()){

        $sql_raw = "IFNULL(ROUND(est_turnout_round1,2),0) as est_total_round1, IFNULL(ROUND(est_turnout_round2,2),0) as est_total_round2, IFNULL(ROUND(est_turnout_round3,2),0) as est_total_round3, IFNULL(ROUND(est_turnout_round4,2),0) as est_total_round4, IFNULL(ROUND(est_turnout_round5,2),0) as est_total_round5, IFNULL(ROUND(close_of_poll,2),0) as close_of_poll, IFNULL(ROUND(est_turnout_total,2),0) as est_total, COUNT(*) as total_record, IFNULL(ROUND((SUM(est_voters) * 100 )/SUM(electors_total),2),0) as total_percentage, pc.PC_NO as pc_no, pc.PC_NAME as pc_name, pc.st_code, state.ST_NAME as st_name, sd1.ac_no as ac_no";

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
            }else{

            }
        }else{
          $sql->groupBy("sd1.st_code");
        }

        if(!empty($data['order_by'])){
            if($data['order_by']=='pc_no'){
                $sql->orderByRaw("state.ST_NAME, pc.pc_no, pc.PC_NAME ASC");
            }else if($data['order_by']=='ac_no'){
                $sql->orderByRaw("state.ST_NAME, pc.pc_no, pc.PC_NAME, sd1.ac_no ASC");
            }else if($data['order_by']=='state'){
                $sql->orderByRaw("state.ST_NAME ASC");
            }else{
              
            }
        }else{
            $sql->orderByRaw("state.ST_NAME, pc.pc_no, pc.PC_NAME ASC");
        }

        $query = $sql->get();
     
        return $query;

    }

}