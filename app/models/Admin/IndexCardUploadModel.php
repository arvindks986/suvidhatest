<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;

class IndexCardUploadModel extends Model
{
    protected $table = 'indexcard_upload_request';

    public static function get_total_approved($filter_array = array()){

	    $total_count = IndexCardUploadModel::where('st_code',$filter_array['st_code'])->where('pc_no',$filter_array['pc_no'])->where('election_id', $filter_array['election_id'])->whereIn('review_status',[0,1])->count();
	    return $total_count;

	 }

   public static function add_upload($data = array()){
     $object                  = new IndexCardUploadModel();
     $object->st_code         = $data['st_code'];
     $object->pc_no           = $data['pc_no'];
     $object->election_id     = $data['election_id'];
     $object->submitted_by    = \Auth::user()->officername;
     $object->file_name       = $data['file_name'];
     return $object->save();
   }

   public static function update_indexcard_status($data){
      $issue = NULL;
      if(!empty($data['issue']) && isset($data['issue'])){
        $issue = $data['issue'];
      }
      $object = IndexCardUploadModel::find($data['id']);
      $object->review_status  =  $data['review_status'];
      $object->issue          =  ($issue)?serialize($issue):'';
      $object->review_by      =  \Auth::user()->officername;
      $object->review_at      =  date("Y-m-d H:i:s");
      $object->review_comment = ($data['comment'])?$data['comment']:'';
      return $object->save();
   }

   public static function get_finalize_records($filter = array()){
    $sql = IndexCardUploadModel::join("m_state as state",'state.ST_CODE','=','indexcard_upload_request.st_code')
        ->join('m_pc',function($join){
          $join->on('indexcard_upload_request.st_code','=','m_pc.st_code')
            ->on('indexcard_upload_request.pc_no','=','m_pc.pc_no');
        })
        ->select('indexcard_upload_request.*','m_pc.PC_NAME','state.ST_NAME as st_name')
        ->where('indexcard_upload_request.election_id',$filter['election_id']);
      if(!empty($filter['st_code'])){
        $sql->where('indexcard_upload_request.st_code',$filter['st_code']);
      }
      $query = $sql->orderBy('indexcard_upload_request.id','DESC')->get()->toArray();
      return $query;
   }

}