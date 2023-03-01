<?php

namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PcModel extends Model
{
  protected $table = 'm_pc';

  public static function get_record($filter_array = array())
  {
    $sql = PcModel::where('PC_NO', $filter_array['pc_no'])->where('ST_CODE', $filter_array['state'])->select('PC_NAME as pc_name', 'PC_NO as pc_no')->first();
    if (!$sql) {
      return '';
    }
    return $sql->toArray();
  }

  public static function get_records($data = array())
  {

    $results = [];

    $sql = PcModel::join('m_election_details', [
      ['m_election_details.ST_CODE', '=', 'm_pc.ST_CODE'],
      ['m_election_details.CONST_NO', '=', 'm_pc.PC_NO']
    ]);

    $sql->where('m_election_details.CONST_TYPE', 'PC');

    if (!empty($data['state'])) {
      $sql->where('m_election_details.ST_CODE', $data['state']);
    }

    if (!empty($data['st_code'])) {
      $sql->where('m_election_details.ST_CODE', $data['st_code']);
    }

    if (!empty($data['pc_no'])) {
      $sql->where('m_pc.PC_NO', $data['pc_no']);
    }

    if (!empty($data['phase'])) {
      $sql->where('m_election_details.PHASE_NO', $data['phase']);
    }

    $query = $sql->select('m_pc.PC_NO as pc_no', 'm_pc.PC_NAME as pc_name')->orderByRaw('m_pc.ST_CODE,m_pc.PC_NO ASC')->groupBy('m_pc.PC_NO')->get();

    if (count($query) > 0) {
      $results = $query->toArray();
    }

    return $results;
  }

  public static function get_pcs($filter = array())
  {


    $sql = PcModel::join('m_election_details', [
      ['m_election_details.ST_CODE', '=', 'm_pc.ST_CODE'],
      ['m_election_details.CONST_NO', '=', 'm_pc.PC_NO']
    ]);

    $sql->where('m_election_details.CONST_TYPE', 'PC');


    if (!empty($filter['st_code']) && isset($filter['st_code'])) {
      $sql->where('m_pc.ST_CODE', $filter['st_code']);
    }
    if (!empty($filter['pc_no']) && isset($filter['pc_no'])) {
      $sql->where('m_pc.PC_NO', $filter['pc_no']);
    }
    $query = $sql->select('m_pc.PC_NO as pc_no', 'm_pc.PC_NAME as pc_name', 'm_pc.ST_CODE as st_code')->orderByRaw('m_pc.ST_CODE,m_pc.PC_NO ASC')->groupBy('m_pc.PC_NO')->groupBy("m_pc.ST_CODE")->get();
    return $query;
  }


  public static function get_distinct_pcs_with_state_name($filter = array())
  {
    $results = [];
    $election_id = Auth::user()->election_id;

    $sql = PcModel::join('m_election_details', [
      ['m_election_details.ST_CODE', '=', 'm_pc.ST_CODE'],
      ['m_election_details.CONST_NO', '=', 'm_pc.PC_NO']
    ])->join('m_state', [
      ['m_state.ST_CODE', '=', 'm_pc.ST_CODE'],
    ]);

    $sql->where('m_election_details.CONST_TYPE', 'PC');
    $sql->where('m_election_details.election_status', '1');
    $sql->where('m_election_details.ELECTION_ID', $election_id);

    if (!empty($data['st_code'])) {
      $sql->where('m_election_details.ST_CODE', $data['st_code']);
    }



    if (!empty($data['pc_no'])) {
      $sql->where('m_pc.PC_NO', $data['pc_no']);
    }

    if (!empty($data['phase'])) {
      $sql->where('m_election_details.PHASE_NO', $data['phase']);
    }



    $query = $sql->select('m_pc.PC_NO', 'm_pc.PC_NAME', 'm_pc.ST_CODE', 'm_state.ST_NAME')->orderByRaw('m_pc.ST_CODE,m_pc.PC_NO ASC')->groupBy(['m_pc.ST_CODE', 'm_pc.PC_NO'])->get();

    if (count($query) > 0) {
      $results = $query->toArray();
    }

    return $results;
  }
}
