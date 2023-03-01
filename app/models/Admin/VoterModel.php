<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;
class VoterModel extends Model
{
    protected $table = 'electors_cdac_other_information';

    protected $fillable = ['id', 'year', 'st_code', 'pc_no', 'general_male_voters', 'general_female_voters', 'general_other_voters', 'nri_male_voters', 'nri_female_voters', 'nri_other_voters', 'test_votes_49_ma', 'votes_not_retreived_from_evm','votes_counted_from_evm','votes_counted_from_vvpat', 'rejected_votes_due_2_other_reason', 'service_postal_votes_under_section_8', 'service_postal_votes_gov', 'postal_votes_rejected', 'proxy_votes', 'tendered_votes', 'total_polling_station_s_i_t_c', 'date_of_repoll', 'no_poll_station_where_repoll', 'is_by_or_countermanded_election', 'reasons_for_by_or_countermanded_election', 'submitted_by', 'created_at', 'updated_at'];

    public $timestamps = false;

    public static function get_voter_by_pc($data = array()){
        $result = [
            'general_male_voters' => 0, 
            'general_female_voters' => 0, 
            'general_other_voters' => 0,
            'nri_male_voters'  => 0,  
            'nri_female_voters'  => 0,  
            'nri_other_voters'  => 0,  
            'test_votes_49_ma'  => 0,  
            'votes_not_retreived_from_evm'  => 0,  
            'votes_counted_from_evm'  => 0,  
            'votes_counted_from_vvpat'  => 0,  
            'rejected_votes_due_2_other_reason'  => 0,  
            'service_postal_votes_under_section_8'  => 0,  
            'service_postal_votes_gov'  => 0,  
            'postal_votes_rejected'  => 0,  
            'proxy_votes'  => 0,  
            'tendered_votes'  => 0,  
            'total_polling_station_s_i_t_c'  => 0,  
            'date_of_repoll'  => '',  
            'no_poll_station_where_repoll'  => '',  
            'is_by_or_countermanded_election'  => 0,  
            'reasons_for_by_or_countermanded_election'  => '',
        ];
        $object = VoterModel::where('pc_no', $data['pc_no'])->where('st_code', $data['st_code'])
		//->where('year', $data['year'])
		->first();
        if($object){
            $result = $object->toArray();
        }
        return $result;
    }

    public static function update_index_card_pc_data($data = array(), $filter = array()){
        $object = VoterModel::firstOrNew($filter);
        if($object->save()){
            $object->update($data);
        }
    }

    public static function get_finalize_pc($filter = array()){
        $total = VoterModel::where('pc_no', $filter['pc_no'])->where('st_code', $filter['st_code'])
		//->where('year', $filter['year'])
		->where('finalize',1)->count();
        return $total;
    }

    public static function check_indexcard_pc_entry($filter = array()){
        $object = VoterModel::where('pc_no', $filter['pc_no'])->where('st_code', $filter['st_code'])
		//->where('year', $filter['year'])
		->first();
        if(!$object){
            return false;
        }
        return true;
    }

    public static function update_finalize_pc($filter = array()){
        $object = VoterModel::where('pc_no', $filter['pc_no'])->where('st_code', $filter['st_code'])
		//->where('year', $filter['year'])
		->first();
        $object->finalize = 1;
		$object->date_of_finalize_by_ro = date('Y-m-d');
        $object->finalize_by = \Auth::user()->officername;
        if(!empty($filter['finalize_by_ro'])){
            $object->finalize_by_ro = 1;
        }
        if(!empty($filter['finalize_by_ceo'])){
            $object->finalize_by_ceo = 1;
        }
        if(!empty($filter['finalize_by_eci'])){
            $object->finalize_by_eci = 1;
        }
        return $object->save();
    }
	
	public static function update_finalize_ceo($data = array()){
        $objects = VoterModel::where('id', $data['id'])->where('st_code', $data['st_code'])
		//->where('year', $data['year'])
		->where('finalize','1')->where('finalize_by_ro','1')->first();
        if(!$objects){
            return false;
        }
        $objects->finalize_by_ceo = 1;
		$objects->date_of_finalize_by_ceo = date('Y-m-d');
        return $objects->save();
    }

    public static function update_definalize_ceo($data = array()){
        $objects = VoterModel::where('id', $data['id'])->where('st_code', $data['st_code'])
		//->where('year', $data['year'])
		->where('finalize','1')->where('finalize_by_ro','1')->first();
        if(!$objects){
            return false;
        }
        $objects->finalize_by_ceo = 0;
        $objects->finalize_by_ro = 0;
        $objects->finalize = 0;
        return $objects->save();
    }
	
	//result date update
    public static function get_result_declared_date($st_code, $pc_no){
        $object = \DB::table("winning_leading_candidate")->where("st_code", $st_code)->where("pc_no", $pc_no)->first();
        if(!$object){
            return "2019-05-23";
        }
        return $object->result_declared_date;
    }

    public static function update_result_date($date, $filter){
        \DB::table("winning_leading_candidate")->where($filter)->update(['result_declared_date' => $date]);
    }
public static function get_total($filter = array()){
		$result = [
            'vt_all_t'    => 0,
            'postal_valid_votes'  => 0,
        ];

        $sql_raw = "IFNULL(SUM(general_male_voters + general_female_voters + general_other_voters + nri_male_voters + nri_female_voters + nri_other_voters),0) AS vt_all_t, IFNULL(SUM(service_postal_votes_under_section_8 + service_postal_votes_gov),0) AS postal_valid_votes";
    
        $sql = VoterModel::join("m_election_details as m_election",[
              ['m_election.CONST_NO', '=','electors_cdac_other_information.pc_no'],
              ['m_election.ST_CODE', '=','electors_cdac_other_information.st_code'],
        ])->selectRaw($sql_raw);

        if(!empty($filter['st_code'])){
          $sql->where("m_election.st_code", $filter['st_code']);
        }

        if(!empty($filter['pc_no'])){
          $sql->where("m_election.pc_no", $filter['pc_no']);
        }
		
		if(!empty($filter['election_id'])){
          $sql->where("election_id", $filter['election_id']);
        }
		
        if(!empty($filter['year'])){
          //$sql->where("m_election.year", $filter['year']);
        }
		
		$sql->where("m_election.election_status","1")->where("CONST_TYPE","PC");

        if(!empty($filter['group_by'])){
            if($filter['group_by']=='pc_no'){
                $sql->groupBy("m_election.pc_no")->groupBy("st_code");
            }else if($filter['group_by']=='national'){
              
            }else{
                $sql->groupBy("m_election.st_code");
            }
        }else{
            $sql->groupBy("m_election.pc_no")->groupBy("st_code");
        }

        $query = $sql->first();

        if($query){
            $result = $query->toArray();
        }
        return $result;
	}
	
	
	/*********** Jitendra Code Start ***********/

	public static function get_candedates_votes_by_ac_no($st,$pc,$ac,$eleid = 1){
      $table='counting_master_'.strtolower(trim($st));
       $select=DB::raw("SELECT cpd.cand_name as candidate_name,mp.PARTYABBRE as party_abbre,total_vote FROM $table AS CM join candidate_nomination_detail as cnd on cnd.candidate_id = CM.candidate_id join candidate_personal_detail as cpd on cpd.candidate_id = cnd.candidate_id join m_party as mp on mp.CCODE = cnd.party_id WHERE CM.ac_no=$ac and CM.pc_no=$pc and CM.party_id != '1180' and cnd.application_status ='6' and cnd.finalaccepted = '1' ORDER BY cnd.new_srno ASC");
       $result=DB::select($select);
       return $result;
  }


	public static function get_nota_votes_by_ac_no($st,$pc,$ac,$eleid = 1){ 
        $table='counting_master_'.strtolower(trim($st));	
		$select=DB::raw("SELECT total_vote FROM $table AS CM WHERE CM.ac_no=$ac and CM.pc_no=$pc and CM.party_id= '1180'");
		$result=DB::select($select);
		return $result;
    }
	public static function get_postal_by_pc_no($st,$pc,$eleid = 1){
       $select=DB::raw("SELECT cpd.cand_name as candidate_name,mp.PARTYABBRE as party_abbre,cpm.postal_vote  FROM counting_pcmaster as cpm join candidate_nomination_detail as cnd on cnd.candidate_id = cpm.candidate_id join candidate_personal_detail as cpd on cpd.candidate_id = cnd.candidate_id join m_party as mp on mp.CCODE = cnd.party_id WHERE cpm.st_code = '$st' AND cpm.pc_no = '$pc'  and cnd.party_id != '1180' and cnd.application_status ='6' and cnd.finalaccepted = '1'  ORDER BY cnd.new_srno  ASC");
       $result=DB::select($select);
       return $result;
  }
	public static function get_total_valid_votes_by_pc_no($st,$pc,$eleid = 1){	
		$select=DB::raw("SELECT SUM(total_vote) as total_vote  FROM `counting_pcmaster` WHERE `st_code` = '$st' AND `pc_no` = '$pc'  and party_id != '1180' ORDER BY `candidate_name`  ASC");
		$result=DB::select($select);
		return $result;
    }
	
	public static function get_nota_potal_votes_by_pc_no($st,$pc,$eleid = 1){	
		$select=DB::raw("SELECT postal_vote FROM `counting_pcmaster` WHERE `st_code` = '$st' AND `pc_no` = '$pc'  and party_id = '1180' ORDER BY `candidate_name`  ASC");
		$result=DB::select($select);
		return $result;
    }
	
	public static function get_total_valid_votes_by_st_code($st,$eleid = 1){	
		$select=DB::raw("SELECT SUM(total_vote) as total_vote  FROM `counting_pcmaster` WHERE `st_code` = '$st'  and party_id != '1180' ORDER BY `candidate_name`  ASC");
		$result=DB::select($select);
		return $result;
    }
	public static function get_total_valid_votes_by_all($eleid = 1){	
		$select=DB::raw("SELECT SUM(total_vote) as total_vote  FROM `counting_pcmaster` WHERE party_id != '1180' ORDER BY `candidate_name`  ASC");
		$result=DB::select($select);
		return $result;
    }
	public static function get_migrante($st_code,$pc_no){		
		return DB::table('counting_pcmaster')->select(DB::raw("sum(migrate_votes) as migrate_votes"))->where('st_code',$st_code)->where('pc_no',$pc_no)->get()->toArray();		
	}
	public static function get_migrante_by_pc_no($st,$pc,$eleid = 1){    
        $select=DB::raw("SELECT cpd.cand_name as candidate_name,mp.PARTYABBRE as party_abbre,cpm.migrate_votes  FROM counting_pcmaster as cpm join candidate_nomination_detail as cnd on cnd.candidate_id = cpm.candidate_id join candidate_personal_detail as cpd on cpd.candidate_id = cnd.candidate_id join m_party as mp on mp.CCODE = cnd.party_id WHERE cpm.st_code = '$st' AND cpm.pc_no = '$pc'  and cnd.party_id != '1180' and cnd.application_status ='6' and cnd.finalaccepted = '1'  ORDER BY cnd.new_srno  ASC");
        $result=DB::select($select);
        return $result;
   }
	
	
	public static function get_rejecteddue($st_code,$pc_no){	
		return DB::table('electors_cdac_other_information')->select(DB::raw("rejected_votes_due_2_other_reason as rejected_votes_due"))->where('st_code',$st_code)->where('pc_no',$pc_no)->get()->toArray();		
	}
	
	public static function get_notretrive($st_code,$pc_no){		
		return DB::table('electors_cdac_other_information')->select(DB::raw("votes_not_retreived_from_evm as votes_not_retreived"))->where('st_code',$st_code)->where('pc_no',$pc_no)->get()->toArray();		
	}
	/*********** Jitendra Code End ***********/
	
}