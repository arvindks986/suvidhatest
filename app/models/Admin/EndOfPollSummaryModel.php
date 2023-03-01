<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\models\Admin\StateModel;
class EndOfPollSummaryModel extends Model
{
    protected $table = 'pd_scheduledetail';

    public static function get_percentage_2019($data = array()){

        $result = [
            'total_percentage'      => 0
        ];

        $sql_raw = "ROUND(SUM(pd_scheduledetail.total)/SUM(electors_cdac.electors_total)*100,2) as total_percentage";
    
        $sql = EndOfPollModel::join('electors_cdac',[
            ['pd_scheduledetail.pc_no', '=','electors_cdac.pc_no'],
            ['pd_scheduledetail.st_code', '=','electors_cdac.st_code'],
            ['pd_scheduledetail.ac_no', '=','electors_cdac.ac_no'],
        ])->selectRaw($sql_raw);

        $sql->where("electors_cdac.year", 2019);

        if(!empty($data['state'])){
          $sql->where("electors_cdac.st_code", $data['state']);
        }

        if(!empty($data['pc_no'])){
          $sql->where("electors_cdac.pc_no", $data['pc_no']);
        }

        if(!empty($data['ac_no'])){
          $sql->where("electors_cdac.ac_no", $data['ac_no']);
        }

        if(!empty($data['phase'])){
            $sql->where("pd_scheduledetail.scheduleid", $data['phase']);
        }else{
            $sql->whereIn("pd_scheduledetail.scheduleid", config('public_config.phases'));
        }

        if(!empty($data['group_by'])){
            if($data['group_by']=='pc_no'){
                $sql->groupBy("electors_cdac.pc_no")->groupBy("electors_cdac.st_code");
            }else if($data['group_by']=='ac_no'){
                $sql->groupBy("electors_cdac.ac_no")->groupBy("electors_cdac.st_code");
            }else if($data['group_by']=='national'){
              
            }else{
                $sql->groupBy("electors_cdac.st_code");
            }
        }else{
            $sql->groupBy("electors_cdac.pc_no")->groupBy("electors_cdac.st_code");
        }

        $query = $sql->first();
        if($query){
            $result = [
                'total_percentage'  => ($query->total_percentage)?$query->total_percentage:'-'
            ];
        }else{
            $result = [
                'total_percentage'  => 'Not in phase'
            ];
        }
        return $result;
   }



}