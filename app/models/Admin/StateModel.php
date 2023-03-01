<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;

class StateModel extends Model
{
    protected $table = 'm_state';
	
	public static function get_states($filter = array()){
        $sql = StateModel::join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
        ])->where('m_election_details.CONST_TYPE','PC');

        if(in_array(\Auth::user()->role_id,['4','18'])){
            $sql->where('m_state.ST_CODE', \Auth::user()->st_code);
        }

        $sql->where('election_status','1')->orderBy('ST_NAME','ASC')->groupBy('m_state.ST_CODE');
        return $sql->get();
    }

    //get phase wise filter start
    public static function get_phasewise_states(){
        $sql = StateModel::join('m_pc',[
            ['m_pc.ST_CODE','=','m_state.ST_CODE']
        ])
        ->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_pc.PC_NO']
        ])
        ->where('m_election_details.CONST_TYPE','PC')
        ->whereIn('m_election_details.ScheduleID',config('public_config.phases'))
        ->orderBy('ST_NAME','ASC')
        ->groupBy('m_state.ST_CODE');
        return $sql->get();
    }
    //get phase wise filter end

    public static function get_pc_states($filter = array()){
        $sql = StateModel::join('m_pc',[
            ['m_pc.ST_CODE','=','m_state.ST_CODE']
        ])->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_pc.PC_NO']
      ])->where('m_election_details.CONST_TYPE','PC')->orderBy('ST_NAME','ASC')->groupBy('m_state.ST_CODE');
        return $sql->get();
    }

    public static function get_pc_states_with_filter($filter = array()){
        $sql = StateModel::join('m_pc',[
            ['m_pc.ST_CODE','=','m_state.ST_CODE']
        ])
        ->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_pc.PC_NO']
        ])
        ->where('m_election_details.CONST_TYPE','PC');

        if(!empty($filter['phase'])){
            $sql->where('m_election_details.ScheduleID',$filter['phase']);
        }

        if(!empty($filter['state'])){
            $sql->where('m_election_details.ST_CODE',$filter['state']);
        }

        $sql->select('m_state.*')->orderBy('ST_NAME','ASC')->groupBy('m_state.ST_CODE');

        return $sql->get();
    }

    public static function get_state_by_code($state_code = ''){
    	$sql = StateModel::where('ST_CODE',$state_code)->first();
    	if(!$sql){
    		return false;
    	}
    	return $sql->toArray();
    }


    public static function get_pc_states_comparison($filter = array()){
        $sql = StateModel::join('m_pc',[
            ['m_pc.ST_CODE','=','m_state.ST_CODE']
        ])
        ->join('pd_scheduledetail',[
          ['pd_scheduledetail.st_code', '=','m_pc.ST_CODE'],
          ['pd_scheduledetail.pc_no', '=','m_pc.PC_NO']
        ]);
        /*->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_pc.PC_NO']
        ])*/
        //->where('m_election_details.CONST_TYPE','PC');

        if(!empty($filter['phase'])){
            $sql->where('pd_scheduledetail.scheduleid',$filter['phase']);
        }

        if(!empty($filter['state'])){
            $sql->where('m_election_details.ST_CODE',$filter['state']);
        }

        $sql->select('m_state.*')->orderBy('ST_NAME','ASC')->groupBy('m_state.ST_CODE');
        //dd($sql->get());
        return $sql->get();
    }

}
