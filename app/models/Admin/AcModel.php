<?php

namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AcModel extends Model
{
  protected $table = 'm_ac';

  public static function get_record($filter_array = array())
  {
    $sql = AcModel::where('AC_NO', $filter_array['ac_no'])->where('ST_CODE', $filter_array['state'])->select('AC_NAME as ac_name', 'AC_NO as ac_no')->first();
    if (!$sql) {
      return '';
    }
    return $sql->toArray();
  }

  public static function get_records($data = array())
  {

    $results = [];

    $sql = AcModel::select('m_ac.AC_NO as ac_no', 'm_ac.AC_NAME as ac_name');

    if (!empty($data['state'])) {
      $sql->where('m_ac.ST_CODE', $data['state']);
    }

    if (!empty($data['ac_no'])) {
      $sql->where('m_ac.AC_NO', $data['ac_no']);
    }

    if (!empty($data['pc_no'])) {
      $sql->where('m_ac.PC_NO', $data['pc_no']);
    }

    if (!empty($data['phase'])) {
      $sql->where('m_election_details.PHASE_NO', $data['phase']);
    }

    $query = $sql->orderByRaw('m_ac.ST_CODE,m_ac.AC_NO ASC')->groupBy('m_ac.AC_NO')->get();

    if (count($query) > 0) {
      $results = $query->toArray();
    }

    return $results;
  }

  public static function get_distinct_acs_with_state_name($data = array())
  {

    $results = [];
    $election_id = Auth::user()->election_id;

    $sql = AcModel::join('m_election_details', [
      ['m_election_details.ST_CODE', '=', 'm_ac.ST_CODE'],
      ['m_election_details.CONST_NO', '=', 'm_ac.PC_NO']
    ])->join('m_state', [
      ['m_state.ST_CODE', '=', 'm_ac.ST_CODE'],
    ]);

    $sql->where('m_election_details.CONST_TYPE', 'PC');
    $sql->where('m_election_details.election_status', '1');
    $sql->where('m_election_details.ELECTION_ID', $election_id);

    if (!empty($data['st_code'])) {
      $sql->where('m_election_details.ST_CODE', $data['st_code']);
    }

    if (!empty($data['pc_no'])) {
      $sql->where('m_ac.PC_NO', $data['pc_no']);
    }

    if (!empty($data['dist_no'])) {
      $sql->where('m_ac.DIST_NO_HDQTR', $data['dist_no']);
    }

    if (!empty($data['ac_no'])) {
      $sql->where('m_ac.AC_NO', $data['ac_no']);
    }

    if (!empty($data['phase'])) {
      $sql->where('m_election_details.PHASE_NO', $data['phase']);
    }



    $query = $sql->select('m_ac.AC_NO as ac_no', 'm_ac.AC_NAME as ac_name', 'm_ac.ST_CODE as st_code', 'm_ac.DIST_NO_HDQTR as dist_no', 'm_state.ST_NAME as state_name')->orderByRaw('m_ac.ST_CODE,m_ac.AC_NO ASC')->groupBy(['m_ac.ST_CODE', 'm_ac.AC_NO'])->get();

    if (count($query) > 0) {
      $results = $query->toArray();
    }

    return $results;
  }
}
