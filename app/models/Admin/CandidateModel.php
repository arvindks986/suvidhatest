<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;

class CandidateModel extends Model
{
    protected $table = 'candidate_personal_detail';

    private $candidate_id = [];
	
    public function get_detail($nomination_id, $data = array()){

        $sql = "SELECT cd.*,cn.*,mp.PARTYNAME,sy.SYMBOL_DES,s.ST_NAME,p.PC_NAME
            FROM `candidate_nomination_detail` AS cn
            INNER JOIN `candidate_personal_detail` cd ON cd.candidate_id = cn.candidate_id
            LEFT JOIN m_party mp ON cn.party_id = mp.CCODE
            LEFT JOIN m_symbol sy ON cn.symbol_id = sy.SYMBOL_NO
            LEFT JOIN m_pc p ON cn.pc_no = p.PC_NO AND p.ST_CODE = cn.st_code
            LEFT JOIN m_state s ON p.ST_CODE = s.ST_CODE
            WHERE cn.nom_id= '".$nomination_id."'
            AND cn.party_id != 1180
            AND cn.application_status != 11";

        return DB::select($sql);
    }


    public static function get_candidates($filter = array()){
        // $sql = CandidateModel::join("candidate_nomination_detail as cn","candidate_personal_detail.candidate_id","=","cn.candidate_id")->where('party_id', '!=', '1180')->where('application_status', '!=','11');
        // if(!empty($filter['state'])){
        //     $sql->where("st_code",$filter['state']);
        // }
      // dd($filter);

        $sql = CandidateModel::join("candidate_nomination_detail as cn","candidate_personal_detail.candidate_id","=","cn.candidate_id")
        ->join('m_election_details', [['cn.st_code','=','m_election_details.st_code'],['cn.pc_no','=','m_election_details.CONST_NO']])
        ->leftJoin('m_symbol as sy', 'cn.symbol_id', '=', 'sy.SYMBOL_NO')
        ->where('m_election_details.CONST_TYPE', 'PC')
        ->where('party_id', '!=', '1180')
        ->where('application_status', '!=','11');
        if(!empty($filter['status'])){
            $sql->where("cn.application_status",$filter['status']);
            $sql->where("cn.finalaccepted",'1');
            $sql->where("cn.party_id",'!=','1180');
            $sql->where("cn.symbol_id",'!=','200');
        }
        if(!empty($filter['state'])){
            $sql->where("cn.st_code",$filter['state']);
        }
        if(!empty($filter['dist_no'])){
            $sql->where("district_no",$filter['dist_no']);
        }
        if(!empty($filter['ac_no'])){
            $sql->where("pc_no",$filter['pc_no']);
        }
        
        if(!empty($filter['election_type_id'])){
            $sql->where('m_election_details.ELECTION_TYPEID', $filter['election_type_id']);
        }
        // if(!empty($filter['election_phase'])){
        //  $sql->where('m_election_details.ScheduleID', $filter['election_phase']);
        // }
        

                $query = $sql->select('cn.st_code', 'cn.pc_no','cn.district_no as dist_no','cand_name','candidate_personal_detail.candidate_id', 'candidate_personal_detail.candidate_id','application_status','finalaccepted','new_srno','cand_gender','sy.SYMBOL_DES','candidate_personal_detail.is_criminal')->groupBy('candidate_personal_detail.candidate_id')->orderByRaw('st_code, district_no, pc_no, new_srno ASC')->get();
                
        
        return $query;
    }

    public static function get_count_nominated($st_code,$pc_no){

        $cont_male   = CandidateModel::get_nom_count_by_status($st_code, $pc_no,6,1,'male');
        $cont_female = CandidateModel::get_nom_count_by_status($st_code, $pc_no,6,1,'female');
        $cont_third  = CandidateModel::get_nom_count_by_status($st_code, $pc_no,6,1,'third');

        $nom_male   = CandidateModel::get_nom_count_by_status($st_code, $pc_no,0,0,'male');
        $nom_female = CandidateModel::get_nom_count_by_status($st_code, $pc_no,0,0,'female');
        $nom_third  = CandidateModel::get_nom_count_by_status($st_code, $pc_no,0,0,'third');

        $rej_male   = CandidateModel::get_nom_count_by_status($st_code, $pc_no,4,0,'male');
        $rej_female = CandidateModel::get_nom_count_by_status($st_code, $pc_no,4,0,'female');
        $rej_third  = CandidateModel::get_nom_count_by_status($st_code, $pc_no,4,0,'third');

        $with_male   = CandidateModel::get_nom_count_by_status($st_code, $pc_no,5,0,'male');
        $with_female = CandidateModel::get_nom_count_by_status($st_code, $pc_no,5,0,'female');
        $with_third  = CandidateModel::get_nom_count_by_status($st_code, $pc_no,5,0,'third');

        

        $data = [
            'nom_male'      => $nom_male,
            'nom_female'    => $nom_female,
            'nom_third'     => $nom_third,
            'nom_total'     => $nom_male+$nom_female+$nom_third,
            'rej_male'      => $rej_male,
            'rej_female'    => $rej_female,
            'rej_third'     => $rej_third,
            'rej_total'     => $rej_male+$rej_female+$rej_third,
            'with_male'      => $with_male,
            'with_female'    => $with_female,
            'with_third'     => $with_third,
            'with_total'     => $with_male+$with_female+$with_third,
            'cont_male'      => $cont_male,
            'cont_female'    => $cont_female,
            'cont_third'     => $cont_third,
            'cont_total'     => $cont_male+$cont_female+$cont_third, 
        ];
        
        return $data;

    }


    public static function get_nom_count_by_status($st_code,$pc_no, $application_status, $finalaccepted, $cand_gender){

        $sql = CandidateModel::join("candidate_nomination_detail as cn","candidate_personal_detail.candidate_id","=","cn.candidate_id")->where('party_id', '!=', '1180')->where('application_status', '!=','11');
        $sql->where("st_code",$st_code)->where("pc_no",$pc_no);
        if($application_status){
            $sql->where("application_status",$application_status);
        }
        $sql->where("cand_gender",$cand_gender);
        if($finalaccepted){
            $sql->where('finalaccepted', 1)->where('symbol_id', '!=','200');
        }

        if($application_status == '5'){
            $sql->whereRaw("candidate_personal_detail.candidate_id NOT IN (SELECT candidate_id FROM candidate_nomination_detail WHERE st_code = '".$st_code."' AND pc_no = '".$pc_no."' AND application_status = '6' AND finalaccepted = '1')");
        }

        if($application_status == '4'){
            $sql->whereRaw("candidate_personal_detail.candidate_id NOT IN (SELECT candidate_id FROM candidate_nomination_detail WHERE st_code = '".$st_code."' AND pc_no = '".$pc_no."' AND (application_status = '5' OR (application_status = '6' AND finalaccepted = '1')))");
        }

        $query = $sql->count(DB::raw("DISTINCT candidate_personal_detail.candidate_id"));
        return ($query)?$query:0;

    }



//Jitendra Code 
public static function get_count_by_status_category(){

    $results = [];
    $sql = CandidateModel::selectRaw("cn.st_code,m_pc.PC_TYPE as cand_category")
	->join("candidate_nomination_detail as cn","candidate_personal_detail.candidate_id","=","cn.candidate_id")
	->join("m_pc", function($join){ 
		$join->on("m_pc.ST_CODE","=","cn.st_code")
			->on("m_pc.PC_NO","=","cn.pc_no");}
	)
	->join("m_election_details as med", function($join){ 
		$join->on("med.ST_CODE","=","cn.st_code")
			->on("med.CONST_NO","=","cn.pc_no");}
	)
	->where('party_id', '!=', '1180')->where('application_status', '!=','11')->where('med.election_status', '=','1')
	//->where('med.ELECTION_ID', '=','1')
	->where('med.CONST_TYPE', '=','PC');
    $cand_results =  $sql->orderBy("cn.st_code")->groupBy("cn.st_code")->groupBy("m_pc.PC_TYPE")->get();
   
    foreach ($cand_results as $key => $category) {

        $cont_male   = CandidateModel::get_nom_count_by_status_category($category->st_code, 6,1,'male', $category->cand_category);
        $cont_female = CandidateModel::get_nom_count_by_status_category($category->st_code, 6,1,'female', $category->cand_category);
        $cont_third  = CandidateModel::get_nom_count_by_status_category($category->st_code, 6,1,'third', $category->cand_category);

        $nom_male   = CandidateModel::get_nom_count_by_status_category($category->st_code, 0,0,'male', $category->cand_category);
        $nom_female = CandidateModel::get_nom_count_by_status_category($category->st_code, 0,0,'female', $category->cand_category);
        $nom_third  = CandidateModel::get_nom_count_by_status_category($category->st_code, 0,0,'third', $category->cand_category);

        $rej_male   = CandidateModel::get_nom_count_by_status_category($category->st_code, 4,0,'male', $category->cand_category);
        $rej_female = CandidateModel::get_nom_count_by_status_category($category->st_code, 4,0,'female', $category->cand_category);
        $rej_third  = CandidateModel::get_nom_count_by_status_category($category->st_code, 4,0,'third', $category->cand_category);

        $with_male   = CandidateModel::get_nom_count_by_status_category($category->st_code, 5,0,'male', $category->cand_category);
        $with_female = CandidateModel::get_nom_count_by_status_category($category->st_code, 5,0,'female', $category->cand_category);
        $with_third  = CandidateModel::get_nom_count_by_status_category($category->st_code, 5,0,'third', $category->cand_category);

        $results[] = [
			'st_code'		=> $category->st_code,
            'category'      => $category->cand_category,
            'nom_male'      => $nom_male,
            'nom_female'    => $nom_female,
            'nom_third'     => $nom_third,
            'nom_total'     => $nom_male+$nom_female+$nom_third,
            'rej_male'      => $rej_male,
            'rej_female'    => $rej_female,
            'rej_third'     => $rej_third,
            'rej_total'     => $rej_male+$rej_female+$rej_third,
            'with_male'      => $with_male,
            'with_female'    => $with_female,
            'with_third'     => $with_third,
            'with_total'     => $with_male+$with_female+$with_third,
            'cont_male'      => $cont_male,
            'cont_female'    => $cont_female,
            'cont_third'     => $cont_third,
            'cont_total'     => $cont_male+$cont_female+$cont_third, 
        ];
    }


        return $results;
        

    }


    public static function get_nom_count_by_status_category($st_code, $application_status, $finalaccepted, $cand_gender, $category){

        $sql = CandidateModel::join("candidate_nomination_detail as cn","candidate_personal_detail.candidate_id","=","cn.candidate_id")
		->join("m_pc", function($join){ 
			$join->on("m_pc.ST_CODE","=","cn.st_code")
				->on("m_pc.PC_NO","=","cn.pc_no");}
		)
		->join("m_election_details as med", function($join){ 
			$join->on("med.ST_CODE","=","cn.st_code")
				->on("med.CONST_NO","=","cn.pc_no");}
		)
		->where('party_id', '!=', '1180')->where('application_status', '!=','11')->where('med.election_status', '=','1')
		//->where('med.ELECTION_ID', '=','1')
		->where('med.CONST_TYPE', '=','PC');
        $sql->where("cn.st_code",$st_code)->where('m_pc.PC_TYPE', $category);
        if($application_status){
            $sql->where("application_status",$application_status);
        }
        $sql->where("cand_gender",$cand_gender);
        if($finalaccepted){
            $sql->where('finalaccepted', 1)->where('symbol_id', '!=','200');
        }

        if($application_status == '5'){
            $sql->whereRaw("candidate_personal_detail.candidate_id NOT IN (SELECT candidate_id FROM candidate_nomination_detail WHERE st_code = '".$st_code."' AND application_status = '6' AND finalaccepted = '1')");
        }

        if($application_status == '4'){
            $sql->whereRaw("candidate_personal_detail.candidate_id NOT IN (SELECT candidate_id FROM candidate_nomination_detail WHERE st_code = '".$st_code."' AND (application_status = '5' OR (application_status = '6' AND finalaccepted = '1')))");
        }

        $query = $sql->count(DB::raw("DISTINCT candidate_personal_detail.candidate_id"));
        return ($query)?$query:0;

    }


// //Jitendra Code end 




		public static function get_count_candidate($st_code,$pc_no){

			$cont_male   = CandidateModel::get_nom_count_by_status($st_code, $pc_no,6,1,'male');
			$cont_female = CandidateModel::get_nom_count_by_status($st_code, $pc_no,6,1,'female');
			$cont_third  = CandidateModel::get_nom_count_by_status($st_code, $pc_no,6,1,'third');

			return $cont_male+$cont_female+$cont_third;
		
		}
		
		
		 //pardeep

        public static function get_count_women_by_status_category($st_code){
       
        $results = [];
        $results[] = [
            'st_code'       => $st_code,
            'category'      => 'general',
            'cont_female'    => CandidateModel::get_nom_women_count($st_code, 6,1,'female', 'general'),
        ];
        $results[] = [
            'st_code'       => $st_code,
            'category'      => 'sc',
            'cont_female'    => CandidateModel::get_nom_women_count($st_code, 6,1,'female', 'sc'),
        ];
        $results[] = [
            'st_code'       => $st_code,
            'category'      => 'st',
            'cont_female'    => CandidateModel::get_nom_women_count($st_code, 6,1,'female', 'st'),
        ];

        return $results;
        

    }


    public static function get_nom_women_count($st_code, $application_status, $finalaccepted, $cand_gender, $category){

        $sql = CandidateModel::join("candidate_nomination_detail as cn","candidate_personal_detail.candidate_id","=","cn.candidate_id")
        ->join("m_pc", function($join){ 
            $join->on("m_pc.ST_CODE","=","cn.st_code")
                ->on("m_pc.PC_NO","=","cn.pc_no");}
        )
		->join("m_election_details as med",  function($join){ 
            $join->on("med.st_code","=","cn.st_code")
                ->on("med.CONST_NO","=","cn.pc_no");
				})
        ->where('med.CONST_TYPE','PC')
        ->where('med.election_status','1')
        ->where('party_id', '!=', '1180')->where('application_status', '!=','11');
        $sql->where("cn.st_code",$st_code);
        if($application_status){
            $sql->where("application_status",$application_status);
        }
        $sql->where("cand_gender",$cand_gender);
        $sql->where('cand_category', $category);

        if($finalaccepted){
            $sql->where('finalaccepted', 1)->where('symbol_id', '!=','200');
        }

        if($application_status == '5'){
            $sql->whereRaw("candidate_personal_detail.candidate_id NOT IN (SELECT candidate_id FROM candidate_nomination_detail WHERE st_code = '".$st_code."' AND application_status = '6' AND finalaccepted = '1')");
        }

        if($application_status == '4'){
            $sql->whereRaw("candidate_personal_detail.candidate_id NOT IN (SELECT candidate_id FROM candidate_nomination_detail WHERE st_code = '".$st_code."' AND (application_status = '5' OR (application_status = '6' AND finalaccepted = '1')))");
        }

        $query = $sql->count(DB::raw("DISTINCT candidate_personal_detail.candidate_id"));
        return ($query)?$query:0;

    }







}
