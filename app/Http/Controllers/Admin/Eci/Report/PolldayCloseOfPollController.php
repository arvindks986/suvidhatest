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
use App\models\Admin\CloseOfPoll;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\PcModel;
use App\models\Admin\AcModel;

class PolldayCloseOfPollController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action        = 'eci/report/voting/close-of-poll';
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

  public function pc(Request $request){
    
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

      if($data['phase']==1){      
		$data['phase']    = 1;
		$data['phases'] =  [];
	}

      $data['number_of_voting'] = 0;

      $data['state'] = NULL;
      if($request->has('state')){

        //valid a state is exist in the current filter phase
        $is_state_valid = StateModel::get_pc_states_comparison([
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
      $data['heading_title'] = 'End of Poll Comparision';

      if($data['phase']){
        $title_array[] = "Phase: ".$data['phase'];
      }

      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }

      if(Auth::user()->designation=='CEO'){
        $data['state'] = Auth::user()->st_code;
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

      $states = StateModel::get_pc_states_comparison($filter_for_state); 

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

      $states_with_filter = StateModel::get_pc_states_comparison([
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

          $object           = CloseOfPoll::get_reports($filter_election);

          foreach ($object as $result) {

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

              $old_result   = CloseOfPoll::get_sum([
                'state'         => $result->st_code,
                'phase'         => $data['phase'],
                'pc_no'         => $result->pc_no,
                'year'          => 2014
              ]);

              $object_voter    = CloseOfPoll::get_percentage_2019([
                'state'         => $result->st_code,
                'phase'         => $data['phase'],
                'pc_no'         => $result->pc_no,
              ]);

              $statewise_results[] = [
                'label'                  => $state_name,
                'is_state'               => 0,
                'total_elector'          => $object_voter['total_elector_total'],
                'total_voter'            => $object_voter['total_voter_total'],
                'total_percentage'       => $object_voter['total_percentage'],
                'old_total_voter'        => $old_result['total_votar_total'],
                'old_total_elector'      => $old_result['elector_total'],
                'old_elector_percentage' => $old_result['old_elector_percentage'],
                'difference'             => round($object_voter['total_percentage'] - $old_result['old_elector_percentage'],2),
                'pc_no'                  => $result->pc_no,
                'pc_name'                => $result->pc_name,
                'filter'                 => $individual_filter,
                "href"                   => 'javascript:void(0)'
              ];  

          }


          // state wise total
          $object_state         = CloseOfPoll::get_reports([
            'state'             => $state_result->ST_CODE,
            'phase'             => $data['phase'],
            'group_by'          => 'state'
          ]);

          $old_result = CloseOfPoll::get_sum([
            'state'         => $state_result->ST_CODE,
            'phase'         => $data['phase'],
            'year'          => 2014,
            'group_by'      => 'state'
          ]);

          $object_voter    = CloseOfPoll::get_percentage_2019([
            'state'         => $state_result->ST_CODE,
            'phase'         => $data['phase'],
            'group_by'      => 'state'
          ]);

          

          if($data['state']){
            $subtitle = "Total";
            $is_state = 0;
          }else{
            $subtitle = "Sub-Total";
            $is_state = 1;
          }


          if(count($object_state)>0){
            $statewise_results[] = [
              'label'                 => $subtitle,
              'is_state'              => $is_state,
              'pc_no'                 => '',
              'pc_name'               => '',
              'total_elector'          => $object_voter['total_elector_total'],
              'total_voter'            => $object_voter['total_voter_total'],
              'total_percentage'       => $object_voter['total_percentage'],
              'old_total_voter'        => $old_result['total_votar_total'],
              'old_total_elector'      => $old_result['elector_total'],
              'old_elector_percentage' => $old_result['old_elector_percentage'],
              'difference'             => round($object_voter['total_percentage'] - $old_result['old_elector_percentage'],2),
              'filter'                => '',
              "href"                  => 'javascript:void(0)'
            ];
          }

          $results[] = $statewise_results;

          if($data['state']){
            $data['number_of_voting']   =  $object_voter['total_percentage'];
          }
      }

 
      // total
      $old_result = CloseOfPoll::get_sum([
          'phase'         => $data['phase'],
          'year'          => 2014,
          'group_by'      => 'national'
      ]);

      $object_voter    = CloseOfPoll::get_percentage_2019([
        'phase'         => $data['phase'],
        'group_by'      => 'national'
      ]);

      
      
      $data['totals'] = [
          'label'                 => 'Total',
          'is_state'              => 1,
          'pc_no'                 => '',
          'pc_name'               => '',
          'total_elector'          => $object_voter['total_elector_total'],
          'total_voter'            => $object_voter['total_voter_total'],
          'total_percentage'       => $object_voter['total_percentage'],
          'old_total_voter'        => $old_result['total_votar_total'],
          'old_total_elector'      => $old_result['elector_total'],
          'old_elector_percentage' => $old_result['old_elector_percentage'],
          'difference'             => round($object_voter['total_percentage'] - $old_result['old_elector_percentage'],2),
          'filter'                => '',
          "href"                  => 'javascript:void(0)'
      ];
      $data['number_of_voting']   =  $object_voter['total_percentage'];


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

      return view($this->view_path.'.pollday.end_of_poll', $data);

    try{ }catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }
  }

  
  //export PC's
  public function export_excel_pc(Request $request){

    set_time_limit(6000);
    $data = $this->pc($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];

    $export_data[] = ['', '' ,'', '2014 Elections','','','2019 Elections','','',''];

    $export_data[] = ['State', 'PC No' ,'PC Name', 'Total Elector','Total Voter','2014 TURNOUT (in %)', 'Total Elector','Total Voter','2019 TURNOUT (in %)', 'Change from 2014'];

    foreach ($data['results'] as $result) {
      foreach ($result as $lis) {
        $export_data[] = [
          $lis['label'],
          $lis['pc_no'],
          $lis['pc_name'],
          ($lis['old_total_elector'])?$lis['old_total_elector']:'0',
          ($lis['old_total_voter'])?$lis['old_total_voter']:'0',
          ($lis['old_elector_percentage'])?$lis['old_elector_percentage']:'0',
          ($lis['total_elector'])?$lis['total_elector']:'0',
          ($lis['total_voter'])?$lis['total_voter']:'0',
          ($lis['total_percentage'])?$lis['total_percentage']:'0', 
          ($lis['difference'])?$lis['difference']:'0',
        ];
      }
    }

    
    if(isset($data['totals'])){
      $export_data[] = [
        $data['totals']['label'],
        '',
        '',
        ($data['totals']['old_total_elector'])?$data['totals']['old_total_elector']:'0',
        ($data['totals']['old_total_voter'])?$data['totals']['old_total_voter']:'0',
        ($data['totals']['old_elector_percentage'])?$data['totals']['old_elector_percentage']:'0',
        ($data['totals']['total_elector'])?$data['totals']['total_elector']:'0',
        ($data['totals']['total_voter'])?$data['totals']['total_voter']:'0',
        ($data['totals']['total_percentage'])?$data['totals']['total_percentage']:'0',
        ($data['totals']['difference'])?$data['totals']['difference']:'0',
      ];
    }

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
        $excel->sheet('Sheet1', function($sheet) use($export_data) {
          $sheet->mergeCells('A1:J1');
          $sheet->mergeCells('D2:F2');
          $sheet->mergeCells('G2:I2');


          $sheet->cell('A1', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->fromArray($export_data,null,'A1',false,false);
        });
    })->export('xls');

  }

  public function export_pdf_pc(Request $request){
    $data = $this->pc($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.pollday.end_of_poll_pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }

  

}  // end class