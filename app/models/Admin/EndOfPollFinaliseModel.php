<?php

namespace App\models\Admin;

use App\models\States;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EndOfPollFinaliseModel extends Model
{

      protected $table = 'pd_schedulemaster';

      public function state()
      {
            return $this->belongsTo(States::class, 'st_code', 'ST_CODE');
      }

      //GET POLLING STATION DATA FUNCTION STARTS
      public static function get_eop_finalise_data($data = array())
      {
            $sql_raw = "ms.ST_NAME AS state_name,pm.st_code, COUNT(DISTINCT(pm.pc_no)) AS total_pc, COUNT(DISTINCT(IF(pd.end_of_poll_finalize=1,pm.pc_no,NULL))) pc_finalised";
            $sql = DB::table('pd_schedulemaster as pm')
                  ->join('pd_scheduledetail as pd', [
                        ['pm.st_code', '=', 'pd.st_code'],
                        ['pm.pc_no', '=', 'pd.pc_no'],
                  ])
                  ->leftjoin('m_state as ms', [
                        ['ms.ST_CODE', '=', 'pd.st_code']
                  ]);
            $sql->selectRaw($sql_raw);
            if (!empty($data['phase'])) {
                  $sql->where("pd.scheduleid", $data['phase']);
            }
            $sql->groupBy("pm.st_code");
            $sql->orderByRaw("pm.pc_no");
            $query = $sql->get();
            return $query;
      }
      //GET POLLING STATION DATA FUNCTION ends
}
