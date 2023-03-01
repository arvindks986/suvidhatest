<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;
class ElectorModel extends Model
{
  
   protected $table = 'electors_cdac';

   public static function get_sum($data = array()){

        $percent = 0;

        $sql_raw = "ROUND(SUM(electors_cdac.voter_total)/SUM(electors_cdac.electors_total)*100,2) as voter_total";
    
        $sql = ElectorModel::join('pd_scheduledetail as sd1',[
            ['sd1.pc_no', '=','electors_cdac.pc_no'],
            ['sd1.st_code', '=','electors_cdac.st_code'],
            ['sd1.ac_no', '=','electors_cdac.ac_no'],
        ])->selectRaw($sql_raw);

        if(!empty($data['state'])){
          $sql->where("electors_cdac.st_code", $data['state']);
        }

        if(!empty($data['pc_no'])){
          $sql->where("electors_cdac.pc_no", $data['pc_no']);
        }

        if(!empty($data['phase'])){
          $sql->where("sd1.scheduleid", $data['phase']);
        }

        if(!empty($data['year'])){
          $sql->where("electors_cdac.year", $data['year']);
        }

        if(!empty($data['group_by'])){
            if($data['group_by']=='pc_no'){
              $sql->groupBy("electors_cdac.pc_no")->groupBy("electors_cdac.st_code");
            }else if($data['group_by']=='ac_no'){
              $sql->groupBy("electors_cdac.ac_no")->groupBy("electors_cdac.st_code");
            }else if($data['group_by']=='state'){
              $sql->groupBy("electors_cdac.st_code");
            }else{

            }
        }else{
          $sql->groupBy("electors_cdac.st_code");
        }

        $query = $sql->first();

        if($query){
            $percent = $query->voter_total;
        }
        return $percent;

   }

}