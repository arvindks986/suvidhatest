<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;
use IndexcardLogModel;
class IndexCardFinalize extends Model
{
  
   protected $table = 'electors_cdac_other_information';

  public static function get_reports($data = array()){

        $sql_raw = "w.finalize, p.PC_NO AS pcno,  p.PC_NAME AS pc_name, s.ST_NAME AS st_name,
                    s.ST_CODE AS st_code,IF(w.finalize_by_ro='1','Yes','No') AS FinalizeRo,IF(w.finalize_by_ceo='1','Yes','No') AS FinalizeCeo,
                    IF(finalize!='1','Yes','No') AS Finalize,IF(cf.finalized_ac='1','Yes','No') AS NominationFinalize,IF(cuf.status='1','Yes','No') AS CountingFinalize";

        $sql = DB::table('m_pc as p')
		->join('candidate_finalized_ac as cf',[
              ['p.PC_NO', '=','cf.const_no'],
              ['p.ST_CODE', '=','cf.st_code'],
        ])
		->join('winning_leading_candidate as cuf',[
              ['p.PC_NO', '=','cuf.pc_no'],
              ['p.ST_CODE', '=','cuf.st_code'],
        ])
        ->leftjoin('electors_cdac_other_information as w',[
              ['p.PC_NO', '=','w.pc_no'],
              ['p.ST_CODE', '=','w.st_code'],
        ])
        ->leftjoin('m_election_details as med',[
              ['p.ST_CODE', '=','med.ST_CODE'],
              ['p.PC_NO', '=','med.CONST_NO'],
            
        ])
        ->join('m_state as s',[
              ['p.ST_CODE', '=','s.ST_CODE']
        ]);

        $sql->selectRaw($sql_raw);

        if(!empty($data['state'])){
          $sql->where("s.ST_CODE", $data['state']);
        }

        if(!empty($data['pc_no'])){
          $sql->where("p.PC_NO", $data['pc_no']);
        }


       $sql->where("med.CONST_TYPE", "PC");
       //$sql->where("med.ELECTION_ID", "1");
       //$sql->where("med.YEAR", "2019");
       $sql->where("med.election_status", '!=','0');
     
       

        //$sql->whereRaw("p.PC_NO != 8 AND s.ST_CODE != 'S22'");

        $sql->orderByRaw("p.ST_CODE, p.PC_NO ASC");
        $sql->groupBy(DB::raw('p.PC_NO'));

        $query = $sql->get();
     
        return $query;

    }


   public static function get_states($data = array()){

        $sql_raw = "SELECT COUNT(p.PC_NO) AS total_pc, COUNT(IF(w.finalize='1',1,NULL)) AS finalize,COUNT(IF(w.finalize_by_ceo='1',1,NULL)) AS FinalizeCeo, SUM(IF(cf.finalized_ac='1',1,0)) AS NominationFinalize,SUM(IF(cuf.status='1',1,0)) AS CountingFinalize, s.ST_NAME AS st_name,s.ST_CODE AS st_code
		FROM m_pc AS p INNER JOIN candidate_finalized_ac AS cf ON (p.PC_NO = cf.const_no AND p.ST_CODE = cf.st_code) INNER JOIN
		 (SELECT ST_CODE,pc_no,status  FROM winning_leading_candidate  GROUP BY st_code,pc_no) AS cuf ON (p.PC_NO = cuf.pc_no AND p.ST_CODE = cuf.st_code) LEFT JOIN electors_cdac_other_information AS w ON (p.PC_NO = w.pc_no AND p.ST_CODE = w.st_code) LEFT JOIN m_election_details AS med ON (p.ST_CODE = med.ST_CODE AND p.PC_NO = med.CONST_NO) INNER JOIN m_state AS s ON (p.ST_CODE = s.ST_CODE)
		WHERE med.CONST_TYPE = 'PC' AND med.election_status != 0"; 


		if(!empty($data['state'])){
		  $state = $data['state'];		  
		  $sql_raw .=" AND s.ST_CODE = $state";		  
        }

        if(!empty($data['pc_no'])){
		  $pc_no = $data['pc_no'];		  
		  $sql_raw .=" AND p.PC_NO = $pc_no";
        }


		$sql_raw .=" GROUP BY s.ST_CODE ORDER BY s.ST_CODE ASC";

        $sql = DB::select($sql_raw);

        $query = $sql;
     
        return $query;

    }
	
	public static function definalize_status($filter = array()){
      $object = IndexCardFinalize::where($filter)->first();
      $object->finalize         = 0;
      $object->finalize_by_ro   = 0;
      $object->finalize_by_ceo  = 0;
      $object->finalize_by_eci  = 0;
      return $object->save();

    }

	public static function definalize_nomination($filter = array()){
		
		//dd($filter);
	   DB::table('candidate_nomination_detail')->where($filter)->update(['finalize' => '0']);
	  return DB::table('candidate_finalized_ac')
	  ->where('st_code',$filter['st_code'])
	  ->where('const_no',$filter['pc_no'])
	  ->where('const_type','PC')
	  ->update(['finalized_ac' => '0']);	  
    }


	public static function definalize_counting($filter = array()){
	  return DB::table('counting_pcmaster')->where($filter)->update(['is_indexcard_finalize' => 1]);
	  
    }
public static function definalize_pcs(){
		
	  return DB::select("select  b.st_name,c.pc_no,c.pc_name, a.type_finalize,max(created_at) as created_at from indexcard_log a join m_state b on a.st_code=b.st_code join m_pc c on a.pc_no=c.pc_no and a.st_code=c.st_code where finalize='0' group by b.st_name,c.pc_name, a.type_finalize");
	  
	  
    }

}