<?php

namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PhaseModel extends Model
{

    protected $table = 'm_schedule';

    public static function get_current_phase()
    {
        $election_id = Auth::user()->election_id;
        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d");
        $sql_raw = "e.PHASE_NO, e.ELECTION_ID, e.election_status, e.ELECTION_TYPEID, ms.SCHEDULENO";
        $sql = DB::table('m_election_details as e')
            ->join('m_schedule as ms', [
                ['ms.SCHEDULENO', '=', 'e.PHASE_NO']
            ]);
        $sql->selectRaw($sql_raw);
        $sql->where('ms.DATE_POLL', '<=', $date);
        //$sql->where('ms.DATE_POLL','=',$date);
        $sql->where('e.CONST_TYPE', 'AC');
        $sql->where('e.election_status', '1');
        $sql->where('e.ELECTION_ID', $election_id);
        $sql->groupBy("e.PHASE_NO");
        $sql->orderByRaw("ms.DATE_POLL DESC");
        $query = $sql->first();
        //$query = PhaseModel::where('DATE_POLL','<=',$date)->orderBy('DATE_POLL','DESC')->first();
        if (!$query) {
            return "";
        }
        return $query->PHASE_NO;
        //return 1;
    }

    public static function get_phase($phase_id)
    {

        $query = PhaseModel::where('SCHEDULEID', $phase_id)->first();
        if (!$query) {
            return "";
        }
        return $query->toArray();
    }

    public static function get_active_phases()
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d");
        return PhaseModel::where('DATE_POLL', '<=', $date)->get();
    }

    public static function get_phases($data = array())
    {
        $election_id = Auth::user()->election_id;

        $sql_raw = "e.PHASE_NO, e.ELECTION_ID, e.election_status, e.ELECTION_TYPEID, ms.SCHEDULENO,ms.DATE_POLL, ms.SCHEDULEID";

        $sql = DB::table('m_election_details as e')
        ->leftjoin('m_schedule as ms',[
              ['ms.SCHEDULENO', '=','e.PHASE_NO']
        ]);

        $sql->selectRaw($sql_raw);

		if(!empty($data['election_type'])){
            $sql->where('e.ELECTION_TYPEID',$data['election_type']);
        }

        $sql->where('e.CONST_TYPE','PC');
        $sql->where('e.election_status','1');
        $sql->where('e.ELECTION_ID',$election_id);
        $sql->groupBy("e.PHASE_NO");
        $sql->orderByRaw("e.PHASE_NO ASC");
        return $sql->get();
    }


    //phase 1
    public static function get_phases_for_phase1()
    {
        return PhaseModel::where('SCHEDULEID', 1)->get();
    }
}
