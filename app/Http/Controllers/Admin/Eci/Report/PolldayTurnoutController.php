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

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class PolldayTurnoutController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/report/voting/list-schedule';
  public $action_pc     = 'eci/report/voting/list-schedule/state';
  public $action_ac     = 'eci/report/voting/list-schedule/state/pc';
  public $view_path     = "admin.pc.eci";

  public function __construct(){
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->middleware(function ($request, $next) {
        if(Auth::user() && Auth::user()->role_id=='26'){
          $this->action_state  = str_replace('eci','eci-agent',$this->action_state);
          $this->action_pc     = str_replace('eci','eci-agent',$this->action_pc);
          $this->action_ac     = str_replace('eci','eci-agent',$this->action_ac);
        }
        return $next($request);
    });
  }

  public function report_state(Request $request){
   
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
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;
      }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Estimated Poll Day Turnout Details';
      if(isset($from_date) && isset($from_to)){
        $data['heading_title'] .= ' between '.date('d-M-Y',strtotime($from_date)).' to '.date('d-M-Y',strtotime($from_to));
      }
      if($data['phase']){
        $title_array[] = "Phase: ".$data['phase'];
      }
      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
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
        'name' => "AC Wise Report",
        'href' =>  url($this->action_ac),
        'target' => false
      ];
      $data['buttons'][]  = [
        'name' => "PC Wise Report",
        'href' =>  url($this->action_pc),
        'target' => false
      ];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_state.'/excel').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_state.'/pdf').'?'.implode('&', $request_array),
        'target' => true
      ];

      $data['action']         = url($this->action_state);

      $results                = [];
      $filter_election = [
        'state'         => $data['state'],
        'phase'         => $data['phase'],
      ];

      $object    = PollDayModel::get_reports([
        'phase'     => $data['phase'],
        'group_by'  => 'state',
        'order_by'  => 'state'
      ]);
  
      foreach ($object as $result) {

          $filter_data = [
            'state'         => $result->st_code,
            'phase'         => $data['phase']
          ];

          $individual_filter_array = [];
          if($data['phase']){
            $individual_filter_array['phase'] = 'phase='.$data['phase'];
          }
          $individual_filter_array['state'] = 'state='.base64_encode($result->st_code);
          $individual_filter    = implode('&', $individual_filter_array);

          $old_percentage = ElectorModel::get_sum([
            'state'         => $result->st_code,
            'phase'         => $data['phase'],
            'group_by'      => 'state',
          ]);

          $results[] = [
            'label'                 => $result->st_name,
            'filter'                => $individual_filter,
            "est_total_round1"      => $result->est_total_round1,
            "est_total_round2"      => $result->est_total_round2,
            "est_total_round3"      => $result->est_total_round3,
            "est_total_round4"      => $result->est_total_round4,
            "est_total_round5"      => $result->est_total_round5,
            'close_of_poll'         => $result->close_of_poll,
            "est_total"             => $result->est_total,
            "total_record"          => $result->total_record,
            "old_total_percentage"  => $old_percentage,
            "total_percentage"      => $result->total_percentage,
            "difference"            => ROUND($result->total_percentage - $old_percentage, 2),
            "PC_NO"                 => $result->pc_no,
            "PC_NAME"               => $result->pc_name,
            "st_code"               => $result->st_code,
            "href"                  => url($this->action_pc)."?".$individual_filter
          ];      

      }   

      $total_filter = [
        'state'         => $data['state'],
        'phase'         => $data['phase']
      ];
      $data['number_of_voting'] =  PollDayModel::get_average_sum($total_filter);

      $data['results']    =   $results;
      $data['user_data']  =   Auth::user();

       $data['heading_title_with_all'] = $data['heading_title'];
      // if(Auth::user()->designation == 'CEO' && !$request->has('is_excel')){
      //   return $data;
      // }

      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }

      return view($this->view_path.'.pollday.estimated.report_state', $data);

     try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }

  public function report_pc(Request $request){
    
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


      if(Auth::user()->designation=='CEO'){
        $data['state'] = Auth::user()->st_code;
      }

      $data['pc_no'] = NULL;
      if($request->has('pc_no')){
        $data['pc_no']    = $request->pc_no;
        $request_array[]  = 'pc_no='.$request->pc_no;
      }


      //set title
      $title_array  = [];
      $data['heading_title'] = 'Estimated Poll Day Turnout Details';

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
        'name' => "AC Wise Report",
        'href' =>  url($this->action_ac),
        'target' => false
      ];
      $data['buttons'][]  = [
        'name' => "PC Wise Report",
        'href' =>  url($this->action_pc),
        'target' => false
      ];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_pc.'/excel').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_pc.'/pdf').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Back',
        'href' =>  url($this->action_state).'?'.'phase='.$data['phase'],
        'target' => false
      ];

      $data['action']         = url($this->action_pc);

      $data['consituencies']  = PcModel::get_records([
        'state'         => $data['state'],
        'phase'         => $data['phase']
      ]);

      $results                = [];

      $filter_election = [
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'pc_no'         => $data['pc_no'],
        'group_by'      => 'pc_no'
      ];

      $object         = PollDayModel::get_reports($filter_election);
 
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

          $old_percentage = ElectorModel::get_sum([
            'state'         => $result->st_code,
            'phase'         => $data['phase'],
            'pc_no'         => $result->pc_no,
            'group_by'      => 'pc_no',
          ]);

          $results[] = [
            'label'                 => $state_name,
            'pc_no'                 => $result->pc_no,
            'pc_name'               => $result->pc_name,
            'filter'                => $individual_filter,
            "est_total_round1"      => $result->est_total_round1,
            "est_total_round2"      => $result->est_total_round2,
            "est_total_round3"      => $result->est_total_round3,
            "est_total_round4"      => $result->est_total_round4,
            "est_total_round5"      => $result->est_total_round5,
            'close_of_poll'         => $result->close_of_poll,
            "est_total"             => $result->est_total,
            "total_record"          => $result->total_record,
            "old_total_percentage"  => $old_percentage,
            "total_percentage"      => $result->total_percentage,
            "difference"            => ROUND($result->total_percentage - $old_percentage, 2),
            "pc_no"                 => $result->pc_no,
            "pc_name"               => $result->pc_name,
            "st_code"               => $result->st_code,
            "href"                  => url($this->action_ac)."?".$individual_filter
          ];      

      }   

      $total_filter = [
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'group_by'      => 'state'
      ];
      $data['number_of_voting'] =  PollDayModel::get_average_sum($total_filter);


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

      return view($this->view_path.'.pollday.estimated.report_pc', $data);

    try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }
  }

  public function report_ac(Request $request){
    
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

      if(Auth::user()->designation=='CEO'){
        $data['state'] = Auth::user()->st_code;
      }

      $data['pc_no'] = NULL;
      if($request->has('pc_no')){
        $data['pc_no']    = $request->pc_no;
        $request_array[]  = 'pc_no='.$request->pc_no;
      }


      //set title
      $title_array  = [];
      $data['heading_title'] = 'Estimated Poll Day Turnout Details';

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
        'name' => "AC Wise Report",
        'href' =>  url($this->action_ac),
        'target' => false
      ];
      $data['buttons'][]  = [
        'name' => "PC Wise Report",
        'href' =>  url($this->action_pc),
        'target' => false
      ];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_ac.'/excel').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_ac.'/pdf').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Back',
        'href' =>  url($this->action_pc).'?'.'phase='.$data['phase'],
        'target' => false
      ];

      $data['action']         = url($this->action_ac);

      $data['consituencies']  = PcModel::get_records([
        'state'         => $data['state'],
        'phase'         => $data['phase']
      ]);

      $results                = [];

      $filter_election = [
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'pc_no'         => $data['pc_no'],
        'group_by'      => 'ac_no',
        'order_by'      => 'ac_no'
      ];

      $object         = PollDayModel::get_ac_reports($filter_election);
 
      $est_total_round1 = 0;
      $est_total_round2 = 0;
      $est_total_round3 = 0;
      $est_total_round4 = 0;
      $est_total_round5 = 0;
      $close_of_poll    = 0;
      $est_total        = 0;
      $total_percentage = 0;

      foreach ($object as $result) { 

          $individual_filter    = implode('&', array_merge($request_array,[
            'pc_no' => 'pc_no='.$result->pc_no,
          ]));

          $ac_name    = '';
          $get_ac     = AcModel::get_record([
            'state' => $result->st_code,
            'ac_no' => $result->ac_no
          ]);

          if($get_ac){
            $ac_name = $get_ac['ac_name'];
          }

          $state_name = '';
          $state_object = StateModel::get_state_by_code($result->st_code);
          if($state_object){
            $state_name = $state_object['ST_NAME'];
          }

          $old_percentage = ElectorModel::get_sum([
            'state'         => $result->st_code,
            'phase'         => $data['phase'],
            'pc_no'         => $result->pc_no,
            'group_by'      => 'ac_no',
          ]);

          $results[] = [
            'label'                 => $state_name,
            'ac_no'                 => $result->ac_no,
            'ac_name'               => $ac_name,
            'pc_no'                 => $result->pc_no,
            'pc_name'               => $result->pc_name,
            'filter'                => $individual_filter,
            "est_total_round1"      => $result->est_total_round1,
            "est_total_round2"      => $result->est_total_round2,
            "est_total_round3"      => $result->est_total_round3,
            "est_total_round4"      => $result->est_total_round4,
            "est_total_round5"      => $result->est_total_round5,
            'close_of_poll'         => $result->close_of_poll,
            "est_total"             => $result->est_total,
            "total_record"          => $result->total_record,
            "old_total_percentage"  => $old_percentage,
            "total_percentage"      => $result->total_percentage,
            "difference"            => ROUND($result->total_percentage - $old_percentage, 2),
            "pc_no"                 => $result->pc_no,
            "pc_name"               => $result->pc_name,
            "st_code"               => $result->st_code,
            "href"                  => 'javascript:void(0)'
          ];      

      }   

      if($data['pc_no']){
        $group_by = 'pc_no';
      }elseif($data['state']){
        $group_by = 'state';
      }else{
        $group_by = NULL;
      }

      $total_filter = [
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'pc_no'         => $data['pc_no'],
        'group_by'      => $group_by
      ];
      $data['number_of_voting'] =  PollDayModel::get_average_sum($total_filter);

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

      return view($this->view_path.'.pollday.estimated.report_ac', $data);

    try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }
  }

  public function export_excel_report_state(Request $request){

    set_time_limit(6000);
    $data = $this->report_state($request->merge(['is_excel' => 1]));

    $export_data = [];
    $headings[] = [$data['heading_title']];
    // $export_data[] = ['State', 'Round1 %(Poll Start to 9:00 AM)','Round2 %(Poll Start to 11:00 AM)','Round3 %(Poll Start to 1:00 PM)', 'Round4 %(Poll Start to 3:00 PM)','Round5 %(Poll Start to 5:00 PM)','Latest Updated %'];
    $export_data[] = ['State', 'Turnout % (2014)', 'Latest Updated Poll %(2019)','Change from 2014'];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        // ($lis['est_total_round1'])?$lis['est_total_round1']:'0',
        // ($lis['est_total_round2'])?$lis['est_total_round2']:'0',
        // ($lis['est_total_round3'])?$lis['est_total_round3']:'0',
        // ($lis['est_total_round4'])?$lis['est_total_round4']:'0',
        // ($lis['est_total_round5'])?$lis['est_total_round5']:'0',
        ($lis['old_total_percentage'])?$lis['old_total_percentage']:'0',
        ($lis['total_percentage'])?$lis['total_percentage']:'0',
        ($lis['difference'])?$lis['difference']:'0',
      ];
    }

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');        

    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:D1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function export_pdf_report_state(Request $request){
    $data = $this->report_state($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.pollday.estimated.report_state_pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }

  //export PC's
  public function export_excel_report_pc(Request $request){

    set_time_limit(6000);
    $data = $this->report_pc($request->merge(['is_excel' => 1]));

    $export_data = [];
    $headings[] = [$data['heading_title']];
    // $export_data[] = ['State', 'PC No' ,'PC Name', 'Round1 %(Poll Start to 9:00 AM)','Round2 %(Poll Start to 11:00 AM)','Round3 %(Poll Start to 1:00 PM)', 'Round4 %(Poll Start to 3:00 PM)','Round5 %(Poll Start to 5:00 PM)','Latest Updated %'];
    //$export_data[] = ['State', 'PC No' ,'PC Name', 'Turnout % (2014)', 'Latest Updated Poll %(2019)','Change from 2014'];
    $export_data[] = ['State', 'PC No' ,'PC Name', 'Turnout % (2014)', 'Latest Updated Poll %(2019)'];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['pc_no'],
        $lis['pc_name'],
        // ($lis['est_total_round1'])?$lis['est_total_round1']:'0',
        // ($lis['est_total_round2'])?$lis['est_total_round2']:'0',
        // ($lis['est_total_round3'])?$lis['est_total_round3']:'0',
        // ($lis['est_total_round4'])?$lis['est_total_round4']:'0',
        // ($lis['est_total_round5'])?$lis['est_total_round5']:'0',
        ($lis['old_total_percentage'])?$lis['old_total_percentage']:'0',
        ($lis['total_percentage'])?$lis['total_percentage']:'0',
        //($lis['difference'])?$lis['difference']:'0',
      ];
    }

    // $export_data[] = [
    //   $data['totals']['label'],
    //   '',
    //   '',
    //   ($data['totals']['est_total_round1'])?$data['totals']['est_total_round1']:'0',
    //   ($data['totals']['est_total_round2'])?$data['totals']['est_total_round2']:'0',
    //   ($data['totals']['est_total_round3'])?$data['totals']['est_total_round3']:'0',
    //   ($data['totals']['est_total_round4'])?$data['totals']['est_total_round4']:'0',
    //   ($data['totals']['est_total_round5'])?$data['totals']['est_total_round5']:'0',
    //   ($data['totals']['total_percentage'])?$data['totals']['total_percentage']:'0',
    // ];

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');        

    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:F1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function export_pdf_report_pc(Request $request){
    $data = $this->report_pc($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.pollday.estimated.report_pc_pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }

  //export AC's
  public function export_excel_report_ac(Request $request){

    set_time_limit(6000);
    $data = $this->report_ac($request->merge(['is_excel' => 1]));

    $export_data = [];
    $headings[] = [$data['heading_title']];
    $export_data[] = ['State', 'PC No' ,'PC Name','AC No' ,'AC Name','Turnout % (2014)', 'Round1 %(Poll Start to 9:00 AM)','Round2 %(Poll Start to 11:00 AM)','Round3 %(Poll Start to 1:00 PM)', 'Round4 %(Poll Start to 3:00 PM)','Round5 %(Poll Start to 5:00 PM)','Round6 %(Close Of Poll)', 'Latest Updated Poll %(2019)'];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['pc_no'],
        $lis['pc_name'],
        $lis['ac_no'],
        $lis['ac_name'],
        ($lis['old_total_percentage'])?$lis['old_total_percentage']:'0',
        ($lis['est_total_round1'])?$lis['est_total_round1']:'0',
        ($lis['est_total_round2'])?$lis['est_total_round2']:'0',
        ($lis['est_total_round3'])?$lis['est_total_round3']:'0',
        ($lis['est_total_round4'])?$lis['est_total_round4']:'0',
        ($lis['est_total_round5'])?$lis['est_total_round5']:'0',
        ($lis['close_of_poll'])?$lis['close_of_poll']:'0',
        ($lis['total_percentage'])?$lis['total_percentage']:'0',
      ];
    }

    // $export_data[] = [
    //   $data['totals']['label'],
    //   '',
    //   '',
    //   '',
    //   '',
    //   ($data['totals']['est_total_round1'])?$data['totals']['est_total_round1']:'0',
    //   ($data['totals']['est_total_round2'])?$data['totals']['est_total_round2']:'0',
    //   ($data['totals']['est_total_round3'])?$data['totals']['est_total_round3']:'0',
    //   ($data['totals']['est_total_round4'])?$data['totals']['est_total_round4']:'0',
    //   ($data['totals']['est_total_round5'])?$data['totals']['est_total_round5']:'0',
    //   ($data['totals']['total_percentage'])?$data['totals']['total_percentage']:'0',
    // ];

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');        

    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:N1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function export_pdf_report_ac(Request $request){
    $data = $this->report_ac($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.pollday.estimated.report_ac_pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }


  


}  // end class
