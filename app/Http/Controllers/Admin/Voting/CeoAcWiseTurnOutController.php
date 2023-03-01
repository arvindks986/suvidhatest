<?php namespace App\Http\Controllers\Admin\Voting;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Session;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Redirect;
    use Carbon\Carbon;
    use DB;
    use Illuminate\Support\Facades\Hash;
    use Validator;
    use Config;
    use \PDF;
    use App\commonModel;  
    use App\models\Admin\ReportModel;
    use App\models\Admin\PollDayModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\models\Admin\StateModel;

class CeoAcWiseTurnOutController extends Controller {
  
  public $base    = 'ro';
  public $folder  = 'ceo';
  public $action    = 'pcceo/voting/list-schedule';
  public $view_path = "admin.pc.ceo";

  public function __construct(){
    $this->middleware('ceo');
    $this->commonModel  = new commonModel();
    $this->report_model = new ReportModel();
    $this->voting_model = new PollDayModel();
    if(!Auth::user()){
      return redirect('/officer-login');
    }
  }

  public function report(){
    $data = [];
    $data['results'] = [];
    $sql = "SELECT m.ST_NAME,p.pc_no,q.`PC_NAME`,p.ac_no,e.ac_name,`electors_total`,total AS Latest_total,CONCAT(ROUND(total/`electors_total`*100,2),'%') Percent FROM `pd_scheduledetail` p JOIN `electors_cdac` e ON p.st_code=e.st_code AND p.pc_no=e.pc_no AND p.ac_no=e.ac_no JOIN m_state m ON m.ST_CODE=p.st_code JOIN m_pc q ON q.`ST_CODE`=p.st_code AND q.`PC_NO`=e.pc_no WHERE q.`ST_CODE` = '".Auth::user()->st_code."' ORDER BY m.ST_NAME,p.pc_no,q.`PC_NAME`,p.ac_no,e.ac_name";
    $results = DB::select($sql);
    if(count($results)>0){
      $data['results'] = $results;
    }
    $data['buttons'] = [];
    $data['buttons'][]  = [
      'name' => 'Export Excel',
      'href' =>  url('pcceo/voting/list-schedule/acwise/export'),
      'target' => true
    ];

    $data['user_data']  = Auth::user();
    return view($this->view_path.'.voting.ceo_wise_turn_out', $data);
  }

  public function export(){
    $export_data   = [];

    $data[] = ['State','PC No' ,'PC Name','AC No' ,'AC Name','Total Elector','Latest Total','Percentage'];
    $sql = "SELECT m.ST_NAME,p.pc_no,q.`PC_NAME`,p.ac_no,e.ac_name,`electors_total`,total AS Latest_total,CONCAT(ROUND(total/`electors_total`*100,2),'%') Percent FROM `pd_scheduledetail` p JOIN `electors_cdac` e ON p.st_code=e.st_code AND p.pc_no=e.pc_no AND p.ac_no=e.ac_no JOIN m_state m ON m.ST_CODE=p.st_code JOIN m_pc q ON q.`ST_CODE`=p.st_code AND q.`PC_NO`=e.pc_no WHERE q.`ST_CODE` = '".Auth::user()->st_code."' ORDER BY m.ST_NAME,p.pc_no,q.`PC_NAME`,p.ac_no,e.ac_name";
    $results = DB::select($sql);
    if(count($results)>0){
      foreach ($results as $result) {
        $row_data = [];
        foreach ($result as $key => $value) {
          $row_data[] = $value;
        }
        $data[] = $row_data;
      }
    }
    \Excel::create('acwise_poll_turn_out'.date('d-m-Y').'_'.time(), function($excel) use($data) {
        $excel->sheet('Sheet1', function($sheet) use($data) {
          $sheet->fromArray($data,null,'A1',false,false);
        });
    })->export('xls');


  }


}  // end class