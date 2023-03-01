<?php

namespace App\models\Admin\polling_station;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PollingStationModel extends Model
{

  protected $primaryKey = 'CCODE';
  protected $table = 'polling_station';
  public $timestamps = false;
  protected $fillable  = [
    'ST_CODE',
    'AC_NO',
    'pc_no',
    'election_id',
    'PART_NO',
    'PS_NO',
    'PART_NAME',
    'PS_NAME_EN',
    'PS_TYPE',
    'PS_CATEGORY',
    'LOCN_TYPE',
    'electors_male',
    'electors_female',
    'electors_other',
    'electors_total',
    'electors_finalize_by_ro',
    'electors_finalize_by_ro_date',
    'scheduleid'
  ];

  public static function get_ps_finalize_ceo_data($data = array())
  {
    $sql = DB::table('polling_station as ps');
    if (!empty($data['state'])) {
      $sql->where("ps.ST_CODE", $data['state']);
    }

    //CHECKING AC CODE
    if (!empty($data['ac_no'])) {
      $sql->where("ps.AC_NO", $data['ac_no']);
    }
    $sql->where("ps_finalize", 0);
    if ($sql->count() > 0) {
      return 1;
    } else {
      return 0;
    }
  }

  public static function get_ps_finalize_data_ro($data = array())
  {
    $sql = DB::table('polling_station as ps');
    if (!empty($data['st_code'])) {
      $sql->where("ps.ST_CODE", $data['st_code']);
    }
    //CHECKING AC CODE
    if (!empty($data['const_no'])) {
      $sql->where("ps.AC_NO", $data['const_no']);
    }
    $sql->select("ro_ps_finalize");
    $query = $sql->get();
    return $query;
  }

  public static function get_ps_finalize_data_deo($data = array())
  {
    $sql = DB::table('polling_station as ps');
    if (!empty($data['state'])) {
      $sql->where("ps.ST_CODE", $data['state']);
    }

    //CHECKING AC CODE
    if (!empty($data['ac_no'])) {
      $sql->where("ps.AC_NO", $data['ac_no']);
    }
    $sql->select("deo_ps_finalize");
    $query = $sql->get();
    return $query;
  }

  public static function get_ps_finalize_data_ceo($data = array())
  {
    $sql = DB::table('polling_station as ps');
    if (!empty($data['state'])) {
      $sql->where("ps.ST_CODE", $data['state']);
    }

    //CHECKING AC CODE
    if (!empty($data['ac_no'])) {
      $sql->where("ps.AC_NO", $data['ac_no']);
    }
    $sql->select("ps_finalize");
    $query = $sql->get();
    return $query;
  }
  public static function get_ps_finalize_data($data = array())
  {

    $role_id = Auth::user()->role_id;

    $sql = DB::table('polling_station as ps');
    if (!empty($data['state'])) {
      $sql->where("ps.ST_CODE", $data['state']);
    }

    //CHECKING AC CODE
    if (!empty($data['ac_no'])) {
      $sql->where("ps.AC_NO", $data['ac_no']);
    }

    if ($role_id === 4) {
      $sql->where("ps_finalize", 1);
    } else {
      $sql->where("ps_finalize", 0);
    }


    if ($sql->count() > 0) {
      return 1;
    } else {
      return 0;
    }
  }


  //GET POLLING STATION DATA FUNCTION STARTS
  public static function get_ps_data($data = array())
  {

    $sql_raw = "pc.PC_NAME AS pcn, pc.PC_NO AS pno, ac.AC_NAME AS acn, ac.AC_NO AS acn,state.ST_NAME AS state_name, ps.*";

    $sql = DB::table('polling_station as ps')
      ->join('m_pc as pc', [
        ['pc.PC_NO', '=', 'ps.PC_NO'],
        ['pc.ST_CODE', '=', 'ps.ST_CODE'],
      ])
      ->join('m_ac as ac', [
        ['pc.PC_NO', '=', 'ps.PC_NO'],
        ['ac.AC_NO', '=', 'ps.AC_NO'],
        ['ac.ST_CODE', '=', 'ps.ST_CODE'],
      ])
      ->leftjoin('m_state as state', [
        ['state.ST_CODE', '=', 'pc.ST_CODE']
      ]);



    $sql->selectRaw($sql_raw);


    //CHECKING STATE CODE
    if (!empty($data['state'])) {
      $sql->where("ps.ST_CODE", $data['state']);
    }

    //CHECKING PC CODE
    if (!empty($data['pc_no'])) {
      $sql->where("ps.pc_no", $data['pc_no']);
    }

    //CHECKING AC CODE
    if (!empty($data['ac_no'])) {
      $sql->where("ps.AC_NO", $data['ac_no']);
    }

    //ORDER BY STARTS
    $sql->orderByRaw("CONVERT(ps.PS_NO,INT) ASC");
    //ORDER BY ENDS


    $query = $sql->get();

    return $query;
  }
  //GET POLLING STATION DATA FUNCTION ends




  //GET POLLING STATION DATA FUNCTION STARTS
  public static function get_ac_data($data = array())
  {

    $sql_raw = "sum(electors_male) as electors_male,sum(electors_female) as electors_female,sum(electors_other) as electors_other,sum(electors_total) as electors_total,sum(voter_male) as voter_male,sum(voter_female) as voter_female,sum(voter_other) as voter_other,sum(voter_total) as voter_total";

    $sql = DB::table('polling_station as ps');

    $sql->selectRaw($sql_raw);
    //CHECKING STATE CODE
    if (!empty($data['state'])) {
      $sql->where("ps.ST_CODE", $data['state']);
    }
    //CHECKING AC CODE
    if (!empty($data['ac_no'])) {
      $sql->where("ps.AC_NO", $data['ac_no']);
    }
    //GROUP BY STARTS
    $sql->groupBy("ps.AC_NO");
    //GROUP BY ENDS
    $query = $sql->first();
    return $query;
  }



  public  function get_scheduledetail($data = array())
  {
    $sql_raw = "id, election_id, st_code,ac_no,pc_no,electors_total, est_voters,updated_at,updated_at_finalize,est_poll_close,close_of_poll,pd_scheduleid, scheduleid, end_voter_male,end_voter_female,end_voter_other,end_voter_total, total_male,total_female,total_other,total,est_turnout_round1,est_turnout_round2, est_turnout_round3,est_turnout_round4,est_turnout_round5, est_turnout_total, ac_election,update_at_round3,update_at_round1,update_at_round2,update_at_round4, update_at_round5,update_at_final,update_device_round1,update_device_round2, update_device_round3,update_device_round4,update_device_round5, update_device_final, end_of_poll_finalize,missed_status_round1,missed_status_round2,missed_status_round3,missed_status_round4,missed_status_round5,missed_status_round6
	,modification_status_round1,modification_status_round2,modification_status_round3,modification_status_round4,modification_status_round5,modification_status_round6";

    $sql = DB::table('pd_scheduledetail')->selectRaw($sql_raw);
    if (!empty($data['election_id'])) {
      $sql->where("election_id", $data['election_id']);
    }
    if (!empty($data['st_code'])) {
      $sql->where("st_code", $data['st_code']);
    }
    if (!empty($data['pc_no'])) {
      $sql->where("pc_no", $data['pc_no']);
    }
    if (!empty($data['ac_no'])) {
      $sql->where("ac_no", $data['ac_no']);
    }
    $query = $sql->first();
    return $query;
  }

  public static function getAcPollingStationCount($st_code, $ac_no, $pc_no)
  {
    return PollingStationModel::where('ST_CODE', $st_code)->where('AC_NO', $ac_no)->where('pc_no', $pc_no)->count();
  }

  public static function getAcPollingStationFinalizedCount($st_code, $ac_no, $pc_no)
  {
    return PollingStationModel::where('ST_CODE', $st_code)->where('AC_NO', $ac_no)->where('pc_no', $pc_no)->where('electors_finalize_by_ro', 1)->count();
  }

  public static function getAcPollingStationEnableForEditCount($st_code, $ac_no, $pc_no)
  {
    return PollingStationModel::where('ST_CODE', $st_code)->where('AC_NO', $ac_no)->where('pc_no', $pc_no)->where('electors_enable_edit_by_eci', 1)->count();
  }
}
