<?php

namespace App\Http\Controllers\Admin\Eci\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\commonModel;
use App\models\Admin\MissedTurnoutModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\PcModel;
use App\models\Admin\AcModel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class MissingTurnoutController extends Controller
{

  public $base          = 'ro';
  public $folder        = 'eci';
  public $action            = 'eci/report/voting/get_missed';
  public $action_missed_ac  = "eci/report/voting/list-schedule/state/pc/missed";
  public $view_path     = "admin.pc.eci";

  public function __construct()
  {
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->middleware(function ($request, $next) {
      if (Auth::user() && Auth::user()->role_id == '26') {
        $this->action  = str_replace('eci', 'eci-agent', $this->action);
        $this->action_missed_ac  = str_replace('eci', 'eci-agent', $this->action_missed_ac);
      }
      return $next($request);
    });
  }

  public function get_missed(Request $request)
  {

    $data = [];
    $default_phase = PhaseModel::get_current_phase();

    $request_array = [];
    $data['phases'] = PhaseModel::get_phases();

    $data['phase'] = NULL;
    if ($request->has('phase')) {
      if ($request->phase != 'all') {
        $data['phase'] = $request->phase;
      }
      $request_array[] =  'phase=' . $request->phase;
    } else {
      $data['phase']    = $default_phase;
      $request_array[]  =  'phase=' . $default_phase;
    }

    $data['state'] = NULL;
    if ($request->has('state')) {

      //valid a state is exist in the current filter phase
      $is_state_valid = StateModel::get_pc_states_with_filter([
        'state' => base64_decode($request->state),
        'phase' => $data['phase']
      ]);

      if (count($is_state_valid) > 0) {
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state=' . $request->state;
      }
    }

    if (Auth::user()->designation == 'CEO') {
      $data['state'] = Auth::user()->st_code;
    }

    $data['pc_no'] = NULL;
    if ($request->has('pc_no')) {
      $data['pc_no']    = $request->pc_no;
      $request_array[]  = 'pc_no=' . $request->pc_no;
    }

    if ($request->has('round')) {
      $data['round']  = $request->round;
      $request_array[]  = 'round=' . $request->round;
    } else {
      $data['round']  = 0;
    }


    //set title
    $title_array  = [];
    $data['heading_title'] = "Ac's Not filled report";

    if ($data['phase']) {
      $title_array[] = "Phase: " . $data['phase'];
    }

    if ($data['state']) {
      $state_object = StateModel::get_state_by_code($data['state']);
      if ($state_object) {
        $title_array[]  = "State: " . $state_object['ST_NAME'];
      }
    }

    if ($data['pc_no'] && $data['state']) {
      $pc_object = PcModel::get_record([
        'state' => $data['state'],
        'pc_no' => $data['pc_no']
      ]);
      if ($pc_object) {
        $title_array[] = "Consituency: " . $pc_object['pc_name'];
      }
    }

    $data['filter_buttons'] = $title_array;

    $filter_for_state = [
      'phase' => $data['phase']
    ];

    $states = StateModel::get_pc_states_with_filter($filter_for_state);

    $data['states'] = [];
    foreach ($states as $result) {
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
      'href' =>  url($this->action . '/excel') . '?' . implode('&', $request_array),
      'target' => true
    ];
    $data['buttons'][]  = [
      'name' => 'Export Pdf',
      'href' =>  url($this->action . '/pdf') . '?' . implode('&', $request_array),
      'target' => true
    ];

    $data['action']         = url($this->action);

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
      'order_by'      => 'ac_no',
      'round'         => $data['round'],
      'level'      => 'ceomissed'
    ];



    if ($data['round']) {

      $object         = MissedTurnoutModel::get_reports($filter_election);



      foreach ($object as $result) {


        $individual_filter    = implode('&', array_merge($request_array, [
          'pc_no' => 'pc_no=' . $result->pc_no,
        ]));

        $ac_name    = '';
        $get_ac     = AcModel::get_record([
          'state' => $result->st_code,
          'ac_no' => $result->ac_no
        ]);

        if ($get_ac) {
          $ac_name = $get_ac['ac_name'];
        }

        $state_name = '';
        $state_object = StateModel::get_state_by_code($result->st_code);
        if ($state_object) {
          $state_name = $state_object['ST_NAME'];
        }

        $results[] = [
          'label'                 => $state_name,
          'ac_no'                 => $result->ac_no,
          'ac_name'               => $ac_name,
          'pc_no'                 => $result->pc_no,
          'pc_name'               => $result->pc_name,
          'filter'                => $individual_filter,
          "st_code"               => $result->st_code,
          "name"                  => $result->name,
          "Phone_no"              => $result->Phone_no,
          "href"                  => 'javascript:void(0)'
        ];
      }
    }

    $data['results']    =   $results;
    $data['user_data']  =   Auth::user();

    //if(Auth::user()->designation == 'CEO' && !$request->has('is_excel')){
    //   return $data;
    // }

    $data['heading_title_with_all'] = $data['heading_title'];

    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }

    return view($this->view_path . '.pollday.report_missed', $data);

    try {
    } catch (\Exception $e) {
      return Redirect::to('/eci/dashboard');
    }
  }


  //missed ac's export
  public function export_excel_report_ac_missed(Request $request)
  {

    set_time_limit(6000);
    $data = $this->get_missed($request->merge(['is_excel' => 1]));

    $export_data = [];
    $headings[] = [$data['heading_title']];
    $export_data[] = ['State', 'PC No', 'PC Name', 'AC No', 'AC Name', 'ARO Name', 'ARO Mobile No'];


    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['pc_no'],
        $lis['pc_name'],
        $lis['ac_no'],
        $lis['ac_name'],
        $lis['name'],
        $lis['Phone_no'],

      ];
    }

    /* $export_data[] = [
      $data['totals']['label'],
      '',
      '',
      '',
      '',
      ($data['totals']['est_total_round1'])?$data['totals']['est_total_round1']:'0',
      ($data['totals']['est_total_round2'])?$data['totals']['est_total_round2']:'0',
      ($data['totals']['est_total_round3'])?$data['totals']['est_total_round3']:'0',
      ($data['totals']['est_total_round4'])?$data['totals']['est_total_round4']:'0',
      ($data['totals']['est_total_round5'])?$data['totals']['est_total_round5']:'0',
      ($data['totals']['close_of_poll'])?$data['totals']['close_of_poll']:'0',
      ($data['totals']['total_percentage'])?$data['totals']['total_percentage']:'0',
    ];*/

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');

    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:G1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function export_pdf_report_ac_missed(Request $request)
  {
    $data = $this->get_missed($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.pollday.report_missed_pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }


  //waseem missed turnout report

  public function get_missed_ac(Request $request)
  {


    $data = [];
    $default_phase = PhaseModel::get_current_phase();

    $request_array = [];
    $data['phases'] = PhaseModel::get_phases();

    $data['phase'] = NULL;
    if ($request->has('phase')) {
      if ($request->phase != 'all') {
        $data['phase'] = $request->phase;
      }
      $request_array[] =  'phase=' . $request->phase;
    } else {
      $data['phase']    = $default_phase;
      $request_array[]  =  'phase=' . $default_phase;
    }

    $data['state'] = NULL;
    if ($request->has('state')) {

      //valid a state is exist in the current filter phase
      $is_state_valid = StateModel::get_pc_states_with_filter([
        'state' => base64_decode($request->state),
        'phase' => $data['phase']
      ]);

      if (count($is_state_valid) > 0) {
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state=' . $request->state;
      }
    }

    if (Auth::user()->designation == 'CEO') {
      $data['state'] = Auth::user()->st_code;
    }

    $data['pc_no'] = NULL;
    if ($request->has('pc_no')) {
      $data['pc_no']    = $request->pc_no;
      $request_array[]  = 'pc_no=' . $request->pc_no;
    }

    //filled
    $data['filters_by'] = [
      [
        'id' => 0,
        'name' => 'All'
      ],
      [
        'id' => 1,
        'name' => 'Filled'
      ],
      [
        'id' => 2,
        'name' => 'Not Filled'
      ],
    ];
    $data['filter_by'] = NULL;
    if ($request->has('filter_by')) {
      $data['filter_by']  = $request->filter_by;
      $request_array[]    = 'filter_by=' . $request->filter_by;
    }

    //set title
    $title_array  = [];
    $data['heading_title'] = "Ac's Not filled report";

    if ($data['phase']) {
      $title_array[] = "Phase: " . $data['phase'];
    }

    if ($data['state']) {
      $state_object = StateModel::get_state_by_code($data['state']);
      if ($state_object) {
        $title_array[]  = "State: " . $state_object['ST_NAME'];
      }
    }

    if ($data['pc_no'] && $data['state']) {
      $pc_object = PcModel::get_record([
        'state' => $data['state'],
        'pc_no' => $data['pc_no']
      ]);
      if ($pc_object) {
        $title_array[] = "Consituency: " . $pc_object['pc_name'];
      }
    }

    $data['filter_buttons'] = $title_array;

    $filter_for_state = [
      'phase' => $data['phase']
    ];

    $states = StateModel::get_pc_states_with_filter($filter_for_state);

    $data['states'] = [];
    foreach ($states as $result) {
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
      'href' =>  url($this->action_missed_ac . '/excel') . '?' . implode('&', $request_array),
      'target' => true
    ];
    $data['buttons'][]  = [
      'name' => 'Export Pdf',
      'href' =>  url($this->action_missed_ac . '/pdf') . '?' . implode('&', $request_array),
      'target' => true
    ];

    $data['action']         = url($this->action_missed_ac);

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
      'order_by'      => 'ac_no',
      'filter_by'     => $data['filter_by']
    ];

    $object       = MissedTurnoutModel::get_missed_reports($filter_election);
    $time         = "23:59";
    // $time         = date("H:i");
    if ($data['phase']) {
      $phase_detail   = PhaseModel::get_phase($data['phase']);
      if (isset($phase_detail) && $phase_detail['DATE_POLL'] == date('Y-m-d')) {
        $time         = date("H:i");
      }
    }



    foreach ($object as $key => $result) {

      $ac_name    = '';
      $get_ac     = AcModel::get_record([
        'state' => $result->st_code,
        'ac_no' => $result->ac_no
      ]);
      if ($get_ac) {
        $ac_name = $get_ac['ac_name'];
      }

      $individual_filter    = implode('&', array_merge($request_array, [
        'pc_no' => 'pc_no=' . $result->pc_no,
      ]));

      $missed_1 = 'Not Open';
      $missed_2 = 'Not Open';
      $missed_3 = 'Not Open';
      $missed_4 = 'Not Open';
      $missed_5 = 'Not Open';
      $mis_1 = false;
      $mis_2 = false;
      $mis_3 = false;
      $mis_4 = false;
      $mis_5 = false;

      if ($result->est_total_round1 == 0) {
        $missed_1 = 'Not Filled';
        $mis_1 = true;
      }

      if ($result->est_total_round2 == 0) {
        $missed_2 = 'Not Filled';
        $mis_2 = true;
      }

      if ($result->est_total_round3 == 0) {
        $missed_3 = 'Not Filled';
        $mis_3 = true;
      }
      if ($result->est_total_round4 == 0) {
        $missed_4 = 'Not Filled';
        $mis_4    = true;
      }

      if ($result->est_total_round5 == 0) {
        $missed_5 = 'Not Filled';
        $mis_5    = true;
      }

      if ($time <= '17:00' && $mis_5) {
        $missed_5 = 'Not Filled';
      }
      if ($time <= '15:00' && $mis_4) {
        $missed_4 = 'Not Filled';
      }
      if ($time <= '13:00' && $mis_3) {
        $missed_3 = 'Not Filled';
      }
      if ($time <= '11:00' && $mis_3) {
        $missed_2 = 'Not Filled';
      }
      if ($time <= '9:00' && $mis_3) {
        $missed_1 = 'Not Filled';
      }


      $results[] = [
        'label'                 => $result->st_name,
        'ac_no'                 => $result->ac_no,
        'ac_name'               => $ac_name,
        'pc_no'                 => $result->pc_no,
        'pc_name'               => $result->pc_name,
        'filter'                => $individual_filter,
        "est_total_round1"      => ($result->est_total_round1) ? $result->est_total_round1 : $missed_1,
        "est_total_round2"      => ($result->est_total_round2) ? $result->est_total_round2 : $missed_2,
        "est_total_round3"      => ($result->est_total_round3) ? $result->est_total_round3 : $missed_3,
        "est_total_round4"      => ($result->est_total_round4) ? $result->est_total_round4 : $missed_4,
        "est_total_round5"      => ($result->est_total_round5) ? $result->est_total_round5 : $missed_5,
        "close_of_poll"         => $result->close_of_poll,
        "est_total"             => $result->est_total,
        "total_record"          => $result->total_record,
        "total_percentage"      => $result->total_percentage,
        "st_code"               => $result->st_code,
        "href"                  => 'javascript:void(0)',
      ];
    }
    $data['results'] = $results;

    $data['results']    =   $results;
    $data['user_data']  =   Auth::user();

    //if(Auth::user()->designation == 'CEO' && !$request->has('is_excel')){
    //   return $data;
    // }

    $data['heading_title_with_all'] = $data['heading_title'];

    if ($request->has('is_data')) {
      return $data;
    }

    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }


    return view($this->view_path . '.pollday.report_ac_missed', $data);
  }

  //missed ac's export
  public function export_excel_report_missed(Request $request)
  {

    set_time_limit(6000);
    $data = $this->get_missed_ac($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['State', 'PC No', 'PC Name', 'AC No', 'AC Name', 'Round1 %(Poll Start to 9:00 AM)', 'Round2 %(Poll Start to 11:00 AM)', 'Round3 %(Poll Start to 1:00 PM)', 'Round4 %(Poll Start to 3:00 PM)', 'Round5 %(Poll Start to 5:00 PM)', 'Latest Updated %'];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['pc_no'],
        $lis['pc_name'],
        $lis['ac_no'],
        $lis['ac_name'],
        ($lis['est_total_round1']) ? $lis['est_total_round1'] : '0',
        ($lis['est_total_round2']) ? $lis['est_total_round2'] : '0',
        ($lis['est_total_round3']) ? $lis['est_total_round3'] : '0',
        ($lis['est_total_round4']) ? $lis['est_total_round4'] : '0',
        ($lis['est_total_round5']) ? $lis['est_total_round5'] : '0',
        ($lis['total_percentage']) ? $lis['total_percentage'] : '0',
      ];
    }

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    Excel::create($name_excel . '_' . date('d-m-Y') . '_' . time(), function ($excel) use ($export_data) {
      $excel->sheet('Sheet1', function ($sheet) use ($export_data) {
        $sheet->mergeCells('A1:L1');
        $sheet->cell('A1', function ($cell) {
          $cell->setAlignment('center');
          $cell->setFontWeight('bold');
        });
        $sheet->fromArray($export_data, null, 'A1', false, false);
      });
    })->export('xls');
  }

  public function export_pdf_report_missed(Request $request)
  {
    $data = $this->get_missed_ac($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.pollday.report_ac_missed_pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }


  public function get_enable_acs_for_update(Request $request)
  {

    $data = [];
    $default_phase = PhaseModel::get_current_phase();
    
    $request_array = [];
    $data['phases'] = PhaseModel::get_phases();

    $data['phase'] = NULL;
    if ($request->has('phase')) {
      if ($request->phase != 'all') {
        $data['phase'] = $request->phase;
      }
      $request_array[] =  'phase=' . $request->phase;
    } else {
      $data['phase']    = $default_phase;
      $request_array[]  =  'phase=' . $default_phase;
    }

    $data['state'] = NULL;
    if ($request->has('state')) {

      //valid a state is exist in the current filter phase
      $is_state_valid = StateModel::get_pc_states_with_filter([
        'state' => base64_decode($request->state),
        'phase' => $data['phase']
      ]);

      if (count($is_state_valid) > 0) {
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state=' . $request->state;
      }
    }

    if (Auth::user()->designation == 'CEO') {
      $data['state'] = Auth::user()->st_code;
    }

    $data['pc_no'] = NULL;
    if ($request->has('pc_no')) {
      $data['pc_no']    = $request->pc_no;
      $request_array[]  = 'pc_no=' . $request->pc_no;
    }

    $data['ac_no'] = NULL;
    if ($request->has('ac_no')) {
      $data['ac_no']    = $request->ac_no;
      $request_array[]  = 'ac_no=' . $request->ac_no;
    }

    if ($request->has('round')) {
      $data['round']  = $request->round;
      $request_array[]  = 'round=' . $request->round;
    } else {
      $data['round']  = 0;
    }


    //set title
    $title_array  = [];
    $data['heading_title'] = "AC List";

    if ($data['phase']) {
      $title_array[] = "Phase: " . $data['phase'];
    }

    if ($data['state']) {
      $state_object = StateModel::get_state_by_code($data['state']);
      if ($state_object) {
        $title_array[]  = "State: " . $state_object['ST_NAME'];
      }
    }

    if ($data['pc_no'] && $data['state']) {
      $pc_object = PcModel::get_record([
        'state' => $data['state'],
        'pc_no' => $data['pc_no']
      ]);
      if ($pc_object) {
        $title_array[] = "Consituency: " . $pc_object['pc_name'];
      }
    }

    $data['filter_buttons'] = $title_array;

    $filter_for_state = [
      'phase' => $data['phase']
    ];

    $states = StateModel::get_pc_states_with_filter($filter_for_state);

    $data['states'] = [];
    foreach ($states as $result) {
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
      'href' =>  url($this->action . '/excel') . '?' . implode('&', $request_array),
      'target' => true
    ];
    $data['buttons'][]  = [
      'name' => 'Export Pdf',
      'href' =>  url($this->action . '/pdf') . '?' . implode('&', $request_array),
      'target' => true
    ];

    $data['action']         = url($this->action);

    $data['consituencies']  = PcModel::get_records([
      'state'         => $data['state'],
      'phase'         => $data['phase']
    ]);

    $results                = [];

    $filter_election = [
      'state'         => $data['state'],
      'phase'         => $data['phase'],
      'pc_no'         => $data['pc_no'],
      'ac_no'         => $data['ac_no'],
      'group_by'      => 'ac_no',
      'order_by'      => 'ac_no',
      'round'         => $data['round'],
      'level'      => 'ceoenable'
    ];
    if ($data['round']) {
      $object         = MissedTurnoutModel::get_reports($filter_election);
      foreach ($object as $result) {
        $individual_filter    = implode('&', array_merge($request_array, [
          'ac_no' => 'ac_no=' . $result->ac_no,
        ]));
        $ac_name    = '';
        $get_ac     = AcModel::get_record([
          'state' => $result->st_code,
          'ac_no' => $result->ac_no
        ]);

        if ($get_ac) {
          $ac_name = $get_ac['ac_name'];
        }

        $state_name = '';
        $state_object = StateModel::get_state_by_code($result->st_code);
        if ($state_object) {
          $state_name = $state_object['ST_NAME'];
        }

        $results[] = [
          'label'                     => $state_name,
          'ac_no'                     => $result->ac_no,
          'pc_no'                     => $result->pc_no,
          'pc_name'                     => $result->pc_name,
          'ac_name'                   => $ac_name,
          'filter'                    => $individual_filter,
          "st_code"                   => $result->st_code,
          "name"                      => $result->name,
          "Phone_no"                  => $result->Phone_no,
          "est_turnout_round1"    => $result->est_turnout_round1,
          "est_turnout_round2"    => $result->est_turnout_round2,
          "est_turnout_round3"    => $result->est_turnout_round3,
          "est_turnout_round4"    => $result->est_turnout_round4,
          "est_turnout_round5"    => $result->est_turnout_round5,
          "est_turnout_round6"    => $result->close_of_poll,
          "missed_status_round1"  => $result->missed_status_round1,
          "missed_status_round2"  => $result->missed_status_round2,
          "missed_status_round3"  => $result->missed_status_round3,
          "missed_status_round4"  => $result->missed_status_round4,
          "missed_status_round5"  => $result->missed_status_round5,
          "missed_status_round6"  => $result->missed_status_round6,
          "modification_status_round1"  => $result->modification_status_round1,
          "modification_status_round2"  => $result->modification_status_round2,
          "modification_status_round3"  => $result->modification_status_round3,
          "modification_status_round4"  => $result->modification_status_round4,
          "modification_status_round5"  => $result->modification_status_round5,
          "modification_status_round6"  => $result->modification_status_round6,
          "href"                        => 'javascript:void(0)'
        ];
      }
    }
    $data['st_code'] = Auth::user()->st_code;
    $data['results']    =   $results;
    $data['user_data']  =   Auth::user();
    $data['heading_title_with_all'] = $data['heading_title'];
    //dd($results);
    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }


    return view($this->view_path . '.missed.enable_for_modification', $data);

    try {
    } catch (\Exception $e) {
      return Redirect::to('/eci/dashboard');
    }
  }
}  // end class
