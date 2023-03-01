<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;

class IndexCardComplainModel extends Model
{
    protected $table = 'indexcard_complains';

    public static function add_complain($data){
      $object                 = new IndexCardComplainModel();
      $object->election_id    = $data['election_id'];
      $object->st_code        = $data['st_code'];
      $object->pc_no          = $data['pc_no'];
      $object->need_approval  = $data['need_approval'];
      $object->sub_heading    = $data['sub_heading'];
      $object->type           = $data['type'];
      $object->complain       = serialize($data['complain']);
      $object->status         = 0;
      return $object->save();
    }

    public static function get_complains($filter = array()){
      $sql = IndexCardComplainModel::join('m_pc',[
          ['indexcard_complains.st_code', '=','m_pc.ST_CODE'],
          ['indexcard_complains.pc_no', '=','m_pc.PC_NO']])
      ->join('m_state',[
          ['indexcard_complains.st_code', '=','m_state.ST_CODE'],
          ]);
      if(!empty($filter['election_id'])){
        $sql->where('indexcard_complains.election_id', $filter['election_id']);
      }
      if(!empty($filter['pc_no'])){
        $sql->where('indexcard_complains.pc_no', $filter['pc_no']);
      }
      if(!empty($filter['st_code'])){
        $sql->where('indexcard_complains.st_code', $filter['st_code']);
      }
     $sql->select('indexcard_complains.*','ST_NAME as st_name','PC_NAME as pc_name');

     // $query = $sql->groupBy(DB::raw("DATE(indexcard_complains.created_at)"))->orderByRaw("DATE(indexcard_complains.created_at) DESC");
       $sql->orderByRaw("DATE(m_pc.ST_CODE) ASC");
      $query = $sql->orderByRaw("DATE(m_pc.PC_NO) ASC");

      return $query->get();
    }

    public static function get_hierarchy_complains($filter = array()){
      $sql = IndexCardComplainModel::join('m_pc',[
          ['indexcard_complains.st_code', '=','m_pc.ST_CODE'],
          ['indexcard_complains.pc_no', '=','m_pc.PC_NO']])
      ->join('m_state',[
          ['indexcard_complains.st_code', '=','m_state.ST_CODE'],
          ]);
      if(!empty($filter['election_id'])){
        $sql->where('indexcard_complains.election_id', $filter['election_id']);
      }
      if(!empty($filter['pc_no'])){
        $sql->where('indexcard_complains.pc_no', $filter['pc_no']);
      }
      if(!empty($filter['st_code'])){
        $sql->where('indexcard_complains.st_code', $filter['st_code']);
      }
      if(!empty($filter['sub_heading'])){
        $sql->where('sub_heading', $filter['sub_heading']);
      }
      if(!empty($filter['date'])){
        $where_raw =  "DATE(indexcard_complains.created_at) = '".$filter['date']."'";
        $sql->whereRaw($where_raw);
      }

      $query = $sql->select('complain','type','need_approval','sub_heading')->orderByRaw("DATE(indexcard_complains.created_at), indexcard_complains.type DESC, sub_heading ASC");
      return $query->get()->toArray();
    }

    //first level
    public static function get_first_level_data($filter = array()){
      $sql = IndexCardComplainModel::join('m_pc',[
          ['indexcard_complains.st_code', '=','m_pc.ST_CODE'],
          ['indexcard_complains.pc_no', '=','m_pc.PC_NO']])
      ->join('m_state',[
          ['indexcard_complains.st_code', '=','m_state.ST_CODE'],
          ]);
      if(!empty($filter['election_id'])){
        $sql->where('indexcard_complains.election_id', $filter['election_id']);
      }
      if(!empty($filter['pc_no'])){
        $sql->where('indexcard_complains.pc_no', $filter['pc_no']);
      }
      if(!empty($filter['st_code'])){
        $sql->where('indexcard_complains.st_code', $filter['st_code']);
      }
      if(!empty($filter['sub_heading'])){
        $sql->where('sub_heading', $filter['sub_heading']);
      }
      if(!empty($filter['date'])){
        $where_raw =  "DATE(indexcard_complains.created_at) = '".$filter['date']."'";
        $sql->whereRaw($where_raw);
      }

      $query = $sql->select('complain','type','need_approval','sub_heading')->orderByRaw("DATE(indexcard_complains.created_at), indexcard_complains.type DESC, sub_heading ASC")->groupBy('type');
      return $query->get()->toArray();
    }


    public static function get_subheading_wise_data($filter = array()){
      $sql = IndexCardComplainModel::join('m_pc',[
          ['indexcard_complains.st_code', '=','m_pc.ST_CODE'],
          ['indexcard_complains.pc_no', '=','m_pc.PC_NO']])
      ->join('m_state',[
          ['indexcard_complains.st_code', '=','m_state.ST_CODE'],
          ]);
      if(!empty($filter['election_id'])){
        $sql->where('indexcard_complains.election_id', $filter['election_id']);
      }
      if(!empty($filter['pc_no'])){
        $sql->where('indexcard_complains.pc_no', $filter['pc_no']);
      }
      if(!empty($filter['st_code'])){
        $sql->where('indexcard_complains.st_code', $filter['st_code']);
      }
      if(!empty($filter['date'])){
        $where_raw =  "DATE(indexcard_complains.created_at) = '".$filter['date']."'";
        $sql->whereRaw($where_raw);
      }
      if(!empty($filter['sub_heading'])){
        $sql->where('sub_heading', $filter['sub_heading']);
      }
      if(!empty($filter['type'])){
        $sql->where('type', $filter['type']);
      }
      $query = $sql->select('complain','type','need_approval','sub_heading')->groupBy('sub_heading')->orderByRaw("DATE(indexcard_complains.created_at), indexcard_complains.type DESC, sub_heading ASC");
      return $query->get()->toArray();
    }

}