<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;
class ReportModel extends Model
{
  

public function get_phases(){
    return DB::table('m_schedule')->get();
}

public function get_scrutny_report_ceo($data = array()){
  $sql = DB::table('m_election_details')->join('m_pc',[
          ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_pc.PC_NO']]);

    if(!empty($data['state_code'])){
       $sql->where('m_election_details.ST_CODE',$data['state_code']);
    }

    if(!empty($data['pc_no'])){
      $sql->where('m_election_details.CONST_NO',$data['pc_no']);
    }

    if(!empty($data['const_type'])){
      $sql->where('m_election_details.CONST_TYPE',$data['const_type']);
    }

    if(!empty($data['phase_id'])){
      $sql->where('m_election_details.PHASE_NO',$data['phase_id']);
    }

    return $sql->orderBy('m_pc.PC_NO', 'ASC')->orderBy('m_pc.PC_NAME', 'ASC')
          ->select('m_election_details.*','m_pc.*','m_election_details.CONST_NO as CCODE','m_election_details.ST_CODE as st_code')->groupBy('m_election_details.CCODE')->get();
}

//not delete
function electiondetailsbystatecode($st_code, $consttype, $const = '')
{
  
    $rec =DB::table('m_election_details')
        ->join('m_pc',[
          ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_pc.PC_NO']
        ])
        ->where('m_election_details.ST_CODE',$st_code)->where('m_election_details.CONST_NO',$const)
        ->where('m_election_details.CONST_TYPE',$consttype)->orderBy('m_election_details.CONST_NO', 'ASC')
        ->select('m_election_details.*','m_pc.*')->get();
     
      return $rec;

    }		

  public function election_detail($data = array()){

    $sql = DB::table('m_election_details')->join('m_pc',[
          ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_pc.PC_NO']]);

    if(!empty($data['state_code'])){
       $sql->where('m_election_details.ST_CODE',$data['state_code']);
    }

    if(!empty($data['pc_no'])){
      $sql->where('m_election_details.CONST_NO',$data['pc_no']);
    }

    if(!empty($data['const_type'])){
      $sql->where('m_election_details.CONST_TYPE',$data['const_type']);
    }

    return $sql->orderBy('m_election_details.CONST_NO', 'ASC')
          ->select('m_election_details.*','m_pc.*','m_election_details.CONST_NO as CONST_NO')->first();
  
  }

  public function election_details($data = array()){
  
    $sql = DB::table('m_election_details')->join('m_pc',[
          ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_pc.PC_NO']]);

    if(!empty($data['state_code'])){
       $sql->where('m_election_details.ST_CODE',$data['state_code']);
    }

    if(!empty($data['pc_no'])){
      $sql->where('m_election_details.CONST_NO',$data['pc_no']);
    }

    if(!empty($data['const_type'])){
      $sql->where('m_election_details.CONST_TYPE',$data['const_type']);
    }

 

    return $sql->orderBy('m_pc.ST_CODE', 'ASC')->orderBy('m_pc.PC_NAME', 'ASC')
          ->select('m_election_details.*','m_pc.*')->get();
  
  }

  //not delete  
  function get_total_nomination($status, $data = array())
  {

    $sql = DB::table('candidate_nomination_detail as candidate');

    if(!empty($data['st_code'])){
      $sql->where('candidate.ST_CODE',$data['st_code']);
    }
    
    if($data['const_type']=='PC' && !empty($data['const_no'])){
      $sql->where('candidate.pc_no',$data['const_no']);
    }else{
      $sql->where('candidate.pc_no','!=','0')->where('candidate.pc_no','!=',NULL);
    }

    if(!empty($data['final_accepted']) && !empty($data['symbol_excluded'])){
      $sql->where('candidate.finalaccepted','=','1')->where('candidate.symbol_id','!=','200');
    }else if(!empty($data['final_accepted']) ){
     // $status = 0;
      $sql->where('candidate.finalaccepted','=','1');
    }

    if(!empty($data['phase'])){
      $sql->where('candidate.scheduleid',$data['phase']);
    }

    if($status != 1 && $status > 0){
      $status_array = [$status];
      $sql->whereIn('candidate.application_status',$status_array);
    }
 
    if(!empty($data['from_date']) && !empty($data['to_date'])){
      $sql->whereBetween('candidate.date_of_submit', [$data['from_date'], $data['to_date']]);
    }

    $query = $sql->where('candidate.party_id','!=','1180')->where('candidate.application_status','!=', '11')->count(); 

    return $query;

  }

  function get_nominations($status, $data = array())
  {
  
    $sql = DB::table('candidate_nomination_detail')
    ->join('candidate_personal_detail','candidate_personal_detail.candidate_id','=','candidate_nomination_detail.candidate_id')
    ->join('m_status','m_status.id','=','candidate_nomination_detail.application_status')
    ->join('m_party','m_party.CCODE','=','candidate_nomination_detail.party_id')
    ->leftJoin('m_pc','m_pc.PC_NO','=','candidate_nomination_detail.pc_no')
    ->leftJoin('m_symbol','m_symbol.SYMBOL_NO','=','candidate_nomination_detail.symbol_id');

    if(!empty($data['st_code'])){
      $sql->where('candidate_nomination_detail.ST_CODE',$data['st_code']);
    }
    
    if($data['const_type']=='PC' && !empty($data['const_no'])){
      $sql->where('candidate_nomination_detail.pc_no',$data['const_no']);
    }else{
      $sql->where('candidate_nomination_detail.pc_no','!=','0')->where('candidate_nomination_detail.pc_no','!=',NULL);
    }

    if(!empty($data['final_accepted']) && !empty($data['symbol_excluded'])){
      $sql->where('candidate_nomination_detail.finalaccepted','=','1')->where('candidate_nomination_detail.symbol_id','!=','200');
    }else if(!empty($data['final_accepted']) ){
      //$status = 0;
      $sql->where('candidate_nomination_detail.finalaccepted','=','1');
    }

    if(!empty($data['phase'])){
      $sql->where('candidate_nomination_detail.scheduleid',$data['phase']);
    }

    if($status != 1 && $status > 0){
      $status_array = [$status];
      $sql->whereIn('candidate_nomination_detail.application_status',$status_array);
    }
 
    if(!empty($data['from_date']) && !empty($data['to_date'])){
      $sql->whereBetween('candidate_nomination_detail.date_of_submit', [$data['from_date'], $data['to_date']]);
    }

    $query = $sql->where('party_id','!=',1180)->where('application_status','!=', 11)->select('candidate_personal_detail.*','m_status.status as status_name','candidate_nomination_detail.nom_id as nomination_id','m_party.*','m_symbol.*','candidate_nomination_detail.finalaccepted','candidate_nomination_detail.application_status','candidate_nomination_detail.new_srno','candidate_nomination_detail.ST_CODE','m_pc.PC_NAME','candidate_nomination_detail.pc_no as PC_NO')->groupBy('nom_id')->orderBy('candidate_nomination_detail.new_srno','ASC');

    return $query->get();  

  }


  public function get_pc_detail($filter_array = array()){
    $sql = DB::table('m_pc')->where('PC_NO',$filter_array['const_no'])->where('ST_CODE',$filter_array['st_code'])->first();
    if(!$sql){
      return '';
    }
    return $sql;
  }

}