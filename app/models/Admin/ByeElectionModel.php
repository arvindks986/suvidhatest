<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;

class ByeElectionModel extends Model
{
    protected $table = 'm_state';
	
	// BYE
	
	public static function get_states_index_bye($filter = array()){
		
		$sql = ByeElectionModel::select('m_state.ST_CODE','m_state.ST_NAME')
			->join('m_election_details','m_state.ST_CODE','m_election_details.ST_CODE')
			->where('m_election_details.CONST_TYPE','PC')
			->where('m_election_details.CURRENTELECTION','Y')
			->where('m_election_details.ELECTION_TYPE','BYE')
			->orderBy('m_state.ST_NAME','ASC')
			->groupBy('m_state.ST_CODE')
			->get();
        return $sql;
    }
	
	public static function get_pcs_bye($filter = array()){
		
        $sql = PcModel::join('m_election_details',[
            ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
            ['m_election_details.CONST_NO', '=','m_pc.PC_NO']
        ]);

        $sql->where('m_election_details.CONST_TYPE','PC');
        $sql->where('m_election_details.CURRENTELECTION','Y');
        $sql->where('m_election_details.ELECTION_TYPE','BYE');
		
		
        if(!empty($filter['st_code']) && isset($filter['st_code'])){
            $sql->where('m_pc.ST_CODE',$filter['st_code']);
        }
        if(!empty($filter['pc_no']) && isset($filter['pc_no'])){
            $sql->where('m_pc.PC_NO',$filter['pc_no']);
        }
		
		
        $query = $sql->select('m_pc.PC_NO as pc_no','m_pc.PC_NAME as pc_name','m_pc.ST_CODE as st_code')->orderByRaw('m_pc.ST_CODE,m_pc.PC_NO ASC')->groupBy('m_pc.PC_NO')->groupBy("m_pc.ST_CODE")->get();
        return $query;
    }
	
	
	
	public static function get_list($filter = array()){
      $sql = PcModel::join('m_election_details',[
            ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
            ['m_election_details.CONST_NO', '=','m_pc.PC_NO']
        ]);
		
		$sql->join('m_state',[
            ['m_state.ST_CODE', '=','m_pc.ST_CODE']
        ]);

        $sql->where('m_election_details.CONST_TYPE','PC');
        $sql->where('m_election_details.CURRENTELECTION','Y');
        $sql->where('m_election_details.ELECTION_TYPE','BYE');
		
		
        if(!empty($filter['st_code']) && isset($filter['st_code'])){
            $sql->where('m_pc.ST_CODE',$filter['st_code']);
        }
        if(!empty($filter['pc_no']) && isset($filter['pc_no'])){
            $sql->where('m_pc.PC_NO',$filter['pc_no']);
        }
        $query = $sql->select('m_state.ST_Name as st_name','m_pc.PC_NO as pc_no','m_pc.PC_NAME as pc_name','m_pc.ST_CODE as st_code')->orderByRaw('m_pc.ST_CODE,m_pc.PC_NO ASC')->groupBy('m_pc.PC_NO')->groupBy("m_pc.ST_CODE")->get();
				
		return $query;
    }
	
	
	
	
	//Jitendra Code End

}
