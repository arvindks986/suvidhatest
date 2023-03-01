<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;
class RepollModel extends Model
{
  
  protected $table = 'repoll_pc_ic';

  public static function get_records($filter = array()){
    $data = [];
    $results = RepollModel::where('st_code', $filter['st_code'])->where('pc_no', $filter['pc_no'])->get();
    foreach($results as $result){
      $data[] = [
        'date_repoll'       => date('d/m/Y',strtotime($result->date_repoll)),
        'no_of_ps_repoll'   => $result->no_of_ps_repoll
      ];
    }
    return $data;
  }

  public static function add_repoll($data){
    $object = new RepollModel();
    $object->date_repoll = date('Y-m-d',strtotime($data['date_repoll']));
    $object->no_of_ps_repoll = (int)$data['no_of_ps_repoll'];
    $object->pc_no = (int)$data['pc_no'];
    $object->st_code = $data['st_code'];
    $object->save();
  }

  public static function delete_repoll($filter = array()){
    RepollModel::where('st_code', $filter['st_code'])->where('pc_no', $filter['pc_no'])->delete();
  }
  //date of result
  public static function get_date_of_result($filter = array()){
    $object = DB::table("winning_leading_candidate")->where("st_code", $filter["st_code"])->where("pc_no", $filter["pc_no"])->first();
    if(!$object){
      return '';
    }
    return $object->result_declared_date;
  }

  public static function update_date_of_result($data, $filter = array()){
    DB::table("winning_leading_candidate")->where("st_code", $filter["st_code"])->where("pc_no", $filter["pc_no"])->update([
      'result_declared_date' => date('Y-m-d',strtotime($data['date_of_result']))
    ]);
  }
   
}