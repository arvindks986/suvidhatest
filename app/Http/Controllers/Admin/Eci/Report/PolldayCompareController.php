<?php namespace App\Http\Controllers\Admin\Eci\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session;
use Illuminate\Support\Facades\Hash;
use \PDF;
use App\commonModel;  
use App\models\Admin\PollDayModel;
use App\models\Admin\ElectorModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\PcModel;
use App\models\Admin\AcModel;

class PolldayCompareController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action        = 'eci/report/voting/compare';
  public $view_path     = "admin.pc.eci";

  public function __construct(){
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->middleware(function ($request, $next) {
        if(Auth::user() && Auth::user()->role_id=='26'){
          $this->action  = str_replace('eci','eci-agent',$this->action);
        }
        return $next($request);
    });
  }

  public function compare(Request $request){
       
      $data = [];
      $default_phase = PhaseModel::get_current_phase();

      $request_array = []; 
      $data['phases'] = PhaseModel::get_phases();

      $data['phase'] = NULL;
      if($request->has('phase')){
        if($request->phase != 'all'){
          $data['phase'] = $request->phase;
        }
        $request_array[] =  'phase='.$request->phase;
      }else{
        $data['phase']    = $default_phase;
        $request_array[]  =  'phase='.$default_phase; 
      }

      //$data['phase']    = $default_phase;

      $data['state'] = NULL;
      if($request->has('state')){

        //valid a state is exist in the current filter phase
        $is_state_valid = StateModel::get_pc_states_with_filter([
          'state' => base64_decode($request->state),
          'phase' => $data['phase']
        ]);

        if(count($is_state_valid)>0){
          $data['state'] = base64_decode($request->state);
          $request_array[] = 'state='.$request->state;
        }

      }

      $data['pc_no'] = NULL;
      if($request->has('pc_no')){
        $data['pc_no']    = $request->pc_no;
        $request_array[]  = 'pc_no='.$request->pc_no;
      }


      //set title
      $title_array  = [];
      $data['heading_title'] = 'Comparision Report';

      if($data['phase']){
        $title_array[] = "Phase: ".$data['phase'];
      }

      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }

      if($data['pc_no'] && $data['state']){
        $pc_object = PcModel::get_record([
          'state' => $data['state'],
          'pc_no' => $data['pc_no']
        ]);
        if($pc_object){
          $title_array[] = "Consituency: ".$pc_object['pc_name'];
        }
      }

      $data['filter_buttons'] = $title_array;

      $filter_for_state = [
        'phase' => $data['phase']
      ];

      $states = StateModel::get_pc_states_with_filter($filter_for_state); 

      $data['states'] = [];
      foreach($states as $result){
        $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
        ];
      }

      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action.'/excel').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action.'/pdf').'?'.implode('&', $request_array),
        'target' => true
      ];

      $data['action']         = url($this->action);

      $data['consituencies']  = PcModel::get_records([
        'state'         => $data['state'],
        'phase'         => $data['phase']
      ]);

      $results                = [];

      // $filter_election = [
      //   'state'         => $data['state'],
      //   'phase'         => $data['phase'],
      //   'pc_no'         => $data['pc_no'],
      //   'group_by'      => 'pc_no'
      // ];

      // $object         = PollDayModel::get_reports($filter_election);
 
      $states_with_filter = StateModel::get_pc_states_with_filter([
        'phase' => $data['phase'],
        'state' => $data['state']
      ]); 


      foreach ($states_with_filter as $state_result) {

          $statewise_results = [];
          $filter_election = [
            'state'         => $state_result->ST_CODE,
            'phase'         => $data['phase'],
            'group_by'      => 'pc_no'
          ];
          $object         = PollDayModel::get_reports($filter_election);


          foreach ($object as $result) {


              $percentage_year = ElectorModel::get_sum([
                'state'         => $result->st_code,
                'phase'         => $data['phase'],
                'pc_no'         => $result->pc_no,
                'year'          => 2014
              ]);

              $individual_filter_array = [];
              if($data['phase']){
                $individual_filter_array['phase'] = 'phase='.$data['phase'];
              }
              $individual_filter_array['state'] = 'state='.base64_encode($result->st_code);
              $individual_filter_array['pc_no'] = 'pc_no='.$result->pc_no;

              $individual_filter    = implode('&', $individual_filter_array);

              $state_name = '';
              $state_object = StateModel::get_state_by_code($result->st_code);
              if($state_object){
                $state_name = $state_object['ST_NAME'];
              }

              $statewise_results[] = [
                'label'                 => $state_name,
                'is_state'              => 0,
                'pc_no'                 => $result->pc_no,
                'pc_name'               => $result->pc_name,
                'filter'                => $individual_filter,
                'total_previous'        => $percentage_year,   
                "est_total_round1"      => $result->est_total_round1,
                "est_total_round2"      => $result->est_total_round2,
                "est_total_round3"      => $result->est_total_round3,
                "est_total_round4"      => $result->est_total_round4,
                "est_total_round5"      => $result->est_total_round5,
                "close_of_poll"         => $result->close_of_poll,
                "est_total"             => $result->est_total,
                "total_record"          => $result->total_record,
                "total_percentage"      => $result->total_percentage,
                "difference"            => round($result->total_percentage - $percentage_year,2),
                "st_code"               => $result->st_code,
                "href"                  => 'javascript:void(0)'
              ];   
          }

          // state wise total
          $object_state         = PollDayModel::get_report([
            'state'             => $state_result->ST_CODE,
            'phase'             => $data['phase'],
            'year'              => 2014
          ]);

          $percentage_year = ElectorModel::get_sum([
            'state'         => $state_result->ST_CODE,
            'phase'         => $data['phase'],
            'group_by'      => 'state',
            'year'          => 2014
          ]);

          $statewise_results[] = [
            'label'                 => 'Sub-Total',
            'is_state'              => 1,
            'pc_no'                 => '',
            'pc_name'               => '',
            'filter'                => '',
            'total_previous'        => $percentage_year,   
            "est_total_round1"      => $object_state['est_total_round1'],
            "est_total_round2"      => $object_state['est_total_round2'],
            "est_total_round3"      => $object_state['est_total_round3'],
            "est_total_round4"      => $object_state['est_total_round4'],
            "est_total_round5"      => $object_state['est_total_round5'],
            "close_of_poll"         => $object_state['close_of_poll'],
            "est_total"             => $object_state['est_total'],
            "total_record"          => $object_state['total_record'],
            "total_percentage"      => $object_state['total_percentage'],
            "difference"            => round($object_state['total_percentage'] - $percentage_year,2),
            "href"                  => 'javascript:void(0)'
          ];

          $results[] = $statewise_results;

      }


      //totals
      $total_filter = [
        'state'         => $data['state'],
        'phase'         => $data['phase']
      ];
      $data['number_of_voting'] =  PollDayModel::get_average_sum($total_filter);

      // state wise total
      $object_state         = PollDayModel::get_report([
        'phase'             => $data['phase'],
        'year'              => 2014
      ]);

      $percentage_year = ElectorModel::get_sum([
            'phase'         => $data['phase'],
            'year'          => 2014
      ]);

      $data['totals'] = [
            'label'                 => 'Total',
            'is_state'              => 1,
            'pc_no'                 => '',
            'pc_name'               => '',
            'filter'                => '',
            'total_previous'        => $percentage_year,   
            "est_total_round1"      => $object_state['est_total_round1'],
            "est_total_round2"      => $object_state['est_total_round2'],
            "est_total_round3"      => $object_state['est_total_round3'],
            "est_total_round4"      => $object_state['est_total_round4'],
            "est_total_round5"      => $object_state['est_total_round5'],
            "close_of_poll"         => $object_state['close_of_poll'],
            "est_total"             => $object_state['est_total'],
            "total_record"          => $object_state['total_record'],
            "total_percentage"      => $object_state['total_percentage'],
            "difference"            => round($object_state['total_percentage'] - $percentage_year,2),
            "href"                  => 'javascript:void(0)'
      ];


      $data['results']    =   $results;
      $data['user_data']  =   Auth::user();

      //if(Auth::user()->designation == 'CEO' && !$request->has('is_excel')){
      //   return $data;
      // }

      $data['heading_title_with_all'] = $data['heading_title'];

      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }

      return view($this->view_path.'.pollday.compare_report', $data);

    try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }
  }

  
  //export PC's
  public function export_excel_compare(Request $request){

    set_time_limit(6000);
    $data = $this->compare($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    //$export_data[] = ['State', 'PC No' ,'PC Name', '2014 Turnout(in %)','2019 Elections Estimated Turnout (in %)','Difference'];
    $export_data[] = ['State', 'PC No' ,'PC Name', '2014 Turnout(in %)','2019 Elections Estimated Turnout (in %)'];
    foreach ($data['results'] as $result) {
      foreach ($result as $lis) {
        $export_data[] = [
          $lis['label'],
          $lis['pc_no'],
          $lis['pc_name'],
          $lis['total_previous'],
          // ($lis['est_total_round1'])?$lis['est_total_round1']:'0',
          // ($lis['est_total_round2'])?$lis['est_total_round2']:'0',
          // ($lis['est_total_round3'])?$lis['est_total_round3']:'0',
          // ($lis['est_total_round4'])?$lis['est_total_round4']:'0',
          // ($lis['est_total_round5'])?$lis['est_total_round5']:'0',
          ($lis['est_total'])?$lis['total_percentage']:'0',
          //($lis['total_percentage'])?$lis['difference']:'0',
        ];
      }
    }

    
    if(isset($data['totals'])){
      $export_data[] = [
        $data['totals']['label'],
        '',
        '',
        ($data['totals']['est_total_round1'])?$data['totals']['est_total_round1']:'0',
        ($data['totals']['est_total_round2'])?$data['totals']['est_total_round2']:'0',
        ($data['totals']['est_total_round3'])?$data['totals']['est_total_round3']:'0',
        ($data['totals']['est_total_round4'])?$data['totals']['est_total_round4']:'0',
        ($data['totals']['est_total_round5'])?$data['totals']['est_total_round5']:'0',
        ($data['totals']['close_of_poll'])?$data['totals']['close_of_poll']:'0',
        ($data['totals']['est_total'])?$data['totals']['total_percentage']:'0',
        ($data['totals']['total_percentage'])?$data['totals']['difference']:'0',
      ];
    }

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
        $excel->sheet('Sheet1', function($sheet) use($export_data) {
          $sheet->mergeCells('A1:F1');
          $sheet->cell('A1', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->fromArray($export_data,null,'A1',false,false);
        });
    })->export('xls');

  }

  public function export_pdf_compare(Request $request){
    $data = $this->compare($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.pollday.compare_report_pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }

  

}  // end class