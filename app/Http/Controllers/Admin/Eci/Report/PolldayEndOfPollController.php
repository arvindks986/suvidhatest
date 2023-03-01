<?php

namespace App\Http\Controllers\Admin\Eci\Report;

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
use App\models\Admin\EndOfPollModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\PcModel;
use App\models\Admin\AcModel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
//current

class PolldayEndOfPollController extends Controller
{

  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/report/voting/end-of-poll';
  public $action_pc     = 'eci/report/voting/end-of-poll/state';
  public $action_ac     = 'eci/report/voting/end-of-poll/state/pc';
  public $view_path     = "admin.pc.eci";

  public function __construct()
  {
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->middleware(function ($request, $next) {
      if (Auth::user() && Auth::user()->role_id == '26') {
        $this->action_state  = str_replace('eci', 'eci-agent', $this->action_state);
        $this->action_pc     = str_replace('eci', 'eci-agent', $this->action_pc);
        $this->action_ac     = str_replace('eci', 'eci-agent', $this->action_ac);
      }
      return $next($request);
    });
  }

  public function report_state(Request $request)
  {

    $data = [];
    $data['number_of_voting'] = 0;
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

    if ($data['phase'] == 1) {
      $data['phase']    = 1;
      $data['phases'] =  [];
    }

    $data['state'] = NULL;
    if ($request->has('state')) {
      $data['state'] = base64_decode($request->state);
      $request_array[] = 'state=' . $request->state;
    }

    //set title
    $title_array  = [];
    $data['heading_title'] = 'End of Poll';
    if (isset($from_date) && isset($from_to)) {
      $data['heading_title'] .= ' between ' . date('d-M-Y', strtotime($from_date)) . ' to ' . date('d-M-Y', strtotime($from_to));
    }
    if ($data['phase']) {
      $title_array[] = "Phase: " . $data['phase'];
    }
    if ($data['state']) {
      $state_object = StateModel::get_state_by_code($data['state']);
      if ($state_object) {
        $title_array[]  = "State: " . $state_object['ST_NAME'];
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
      'href' =>  url($this->action_state . '/excel') . '?' . implode('&', $request_array),
      'target' => true
    ];
    $data['buttons'][]  = [
      'name' => 'Export Pdf',
      'href' =>  url($this->action_state . '/pdf') . '?' . implode('&', $request_array),
      'target' => true
    ];

    $data['action']         = url($this->action_state);

    $results                = [];
    $filter_election = [
      'state'         => $data['state'],
      'phase'         => $data['phase'],
    ];


    $object_states = EndOfPollModel::get_reports($filter_election);

    foreach ($object_states as $result) {

      $filter_data = [
        'state'         => $result->st_code,
        'phase'         => $data['phase'],
        'group_by'      => 'state'
      ];

      $individual_filter_array = [];
      if ($data['phase']) {
        $individual_filter_array['phase'] = 'phase=' . $data['phase'];
      }
      $individual_filter_array['state'] = 'state=' . base64_encode($result->st_code);
      $individual_filter    = implode('&', $individual_filter_array);

      //get total electors
      $object_elector  = EndOfPollModel::get_total_elector($filter_data);

      $object_voter    = EndOfPollModel::get_percentage_2019($filter_data);

      $results[] = [
        'label'               => $result->st_name,
        'filter'              => $individual_filter,
        "pc_no"               => $result->pc_no,
        "pc_name"             => $result->pc_name,
        "st_code"             => $result->st_code,
        "ac_no"               => $result->ac_no,
        "old_total_male"      => $object_elector['old_total_male'],
        "old_total_female"    => $object_elector['old_total_female'],
        "old_total_other"     => $object_elector['old_total_other'],
        "old_total"           => $object_elector['old_total'],
        "total_male"          => $object_voter['total_voter_male'],
        "total_female"        => $object_voter['total_voter_female'],
        "total_other"         => $object_voter['total_voter_other'],
        "total"               => $object_voter['total_voter_total'],
        "total_percentage"    => $object_voter['total_percentage'],
        "href"                => url($this->action_pc) . "?" . $individual_filter
      ];
    }

    $total_filter = [
      'phase'         => $data['phase'],
      'group_by'      => 'national'
    ];

    //calculate total
    $total_object =  EndOfPollModel::get_reports($total_filter);

    if (count($total_object) > 0) {
      $result           = $total_object[0];
      $object_elector   = EndOfPollModel::get_total_elector([
        'phase'     => $data['phase'],
        'group_by'  => 'national',
      ]);

      $object_voter    = EndOfPollModel::get_percentage_2019([
        'phase'     => $data['phase'],
        'group_by'  => 'national',
      ]);

      $data['totals'] = [
        'label'               => 'Total',
        'filter'              => '',
        "pc_no"               => $result->pc_no,
        "pc_name"             => $result->pc_name,
        "st_code"             => $result->st_code,
        "ac_no"               => $result->ac_no,
        "old_total_male"      => $object_elector['old_total_male'],
        "old_total_female"    => $object_elector['old_total_female'],
        "old_total_other"     => $object_elector['old_total_other'],
        "old_total"           => $object_elector['old_total'],
        "total_male"          => $object_voter['total_voter_male'],
        "total_female"        => $object_voter['total_voter_female'],
        "total_other"         => $object_voter['total_voter_other'],
        "total"               => $object_voter['total_voter_total'],
        "total_percentage"    => $object_voter['total_percentage'],
        "href"                => ''
      ];

      $data['number_of_voting'] = $object_voter['total_percentage'];
    }

    $data['results']    =   $results;
    $data['user_data']  =   Auth::user();

    $data['heading_title_with_all'] = $data['heading_title'];
    // if(Auth::user()->designation == 'CEO' && !$request->has('is_excel')){
    //   return $data;
    // }

    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }

    return view($this->view_path . '.pollday.end_of_poll.state', $data);

    try {
    } catch (\Exception $e) {
      return Redirect::to('/eci/dashboard');
    }
  }

  public function report_pc(Request $request)
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

    if ($data['phase'] == 1) {
      $data['phase']    = 1;
      $data['phases'] =  [];
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


    //set title
    $title_array  = [];
    $data['heading_title'] = 'End of Poll';

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
      'href' =>  url($this->action_pc . '/excel') . '?' . implode('&', $request_array),
      'target' => true
    ];
    $data['buttons'][]  = [
      'name' => 'Export Pdf',
      'href' =>  url($this->action_pc . '/pdf') . '?' . implode('&', $request_array),
      'target' => true
    ];
    $data['buttons'][]  = [
      'name' => 'Back',
      'href' =>  url($this->action_state) . '?' . 'phase=' . $data['phase'],
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

    $object         = EndOfPollModel::get_reports($filter_election);

    foreach ($object as $result) {

      $individual_filter_array = [];
      if ($data['phase']) {
        $individual_filter_array['phase'] = 'phase=' . $data['phase'];
      }
      $individual_filter_array['state'] = 'state=' . base64_encode($result->st_code);
      $individual_filter_array['pc_no'] = 'pc_no=' . $result->pc_no;

      $individual_filter    = implode('&', $individual_filter_array);

      $state_name = '';
      $state_object = StateModel::get_state_by_code($result->st_code);
      if ($state_object) {
        $state_name = $state_object['ST_NAME'];
      }

      $object_elector  = EndOfPollModel::get_total_elector([
        'pc_no' => $result->pc_no,
        'state' => $result->st_code,
        'phase'     => $data['phase'],
        'group_by'  => 'pc_no',
      ]);

      $object_voter    = EndOfPollModel::get_percentage_2019([
        'pc_no' => $result->pc_no,
        'state' => $result->st_code,
        'phase'     => $data['phase'],
        'group_by'  => 'pc_no',
      ]);

      $results[] = [
        'label'               => $result->st_name,
        'filter'              => $individual_filter,
        "pc_no"               => $result->pc_no,
        "pc_name"             => $result->pc_name,
        "st_code"             => $result->st_code,
        "ac_no"               => $result->ac_no,
        "old_total_male"      => $object_elector['old_total_male'],
        "old_total_female"    => $object_elector['old_total_female'],
        "old_total_other"     => $object_elector['old_total_other'],
        "old_total"           => $object_elector['old_total'],
        "total_male"          => $object_voter['total_voter_male'],
        "total_female"        => $object_voter['total_voter_female'],
        "total_other"         => $object_voter['total_voter_other'],
        "total"               => $object_voter['total_voter_total'],
        "total_percentage"    => $object_voter['total_percentage'],
        "href"                => url($this->action_ac) . "?" . $individual_filter
      ];
    }

    //calculate total
    if ($data['pc_no'] && $data['state']) {
      $group_by = 'state';
    } else if ($data['pc_no']) {
      $group_by = 'pc_no';
    } else {
      $group_by = 'national';
    }

    $total_filter = [
      'phase'         => $data['phase'],
      'state'         => $data['state'],
      'group_by'      => $group_by
    ];
    $total_object =  EndOfPollModel::get_reports($total_filter);


    if (count($total_object) > 0) {
      $result           = $total_object[0];
      $object_elector   = EndOfPollModel::get_total_elector([
        'phase'         => $data['phase'],
        'group_by'      => $group_by,
        'state'         => $data['state'],
      ]);

      $object_voter    = EndOfPollModel::get_percentage_2019([
        'phase'         => $data['phase'],
        'group_by'      => $group_by,
        'state'         => $data['state'],
      ]);

      $data['totals'] = [
        'label'               => 'Total',
        'filter'              => '',
        "pc_no"               => '',
        "pc_name"             => '',
        "st_code"             => $result->st_code,
        "ac_no"               => $result->ac_no,
        "old_total_male"      => $object_elector['old_total_male'],
        "old_total_female"    => $object_elector['old_total_female'],
        "old_total_other"     => $object_elector['old_total_other'],
        "old_total"           => $object_elector['old_total'],
        "total_male"          => $object_voter['total_voter_male'],
        "total_female"        => $object_voter['total_voter_female'],
        "total_other"         => $object_voter['total_voter_other'],
        "total"               => $object_voter['total_voter_total'],
        "total_percentage"    => $object_voter['total_percentage'],
        "href"                => ''
      ];

      $data['number_of_voting'] = $object_voter['total_percentage'];
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

    return view($this->view_path . '.pollday.end_of_poll.pc', $data);

    try {
    } catch (\Exception $e) {
      return Redirect::to('/eci/dashboard');
    }
  }

  //done her

  public function report_ac(Request $request)
  {
    try {
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

      if ($data['phase'] == 1) {
        $data['phase']    = 1;
        $data['phases'] =  [];
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


      //set title
      $title_array  = [];
      $data['heading_title'] = 'End of Poll';

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
        'href' =>  url($this->action_ac . '/excel') . '?' . implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_ac . '/pdf') . '?' . implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Back',
        'href' =>  url($this->action_pc) . '?' . 'phase=' . $data['phase'],
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

      $object         = EndOfPollModel::get_reports($filter_election);

      
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

        //get total electors
        $object_elector  = EndOfPollModel::get_total_elector([
          'pc_no'     => $result->pc_no,
          'ac_no'     => $result->ac_no,
          'state'     => $result->st_code,
          'phase'     => $data['phase'],
          'group_by'  => 'ac_no',
        ]);

        $object_voter    = EndOfPollModel::get_percentage_2019([
          'pc_no'     => $result->pc_no,
          'ac_no'     => $result->ac_no,
          'state'     => $result->st_code,
          'phase'     => $data['phase'],
          'group_by'  => 'ac_no',
        ]);

        $results[] = [
          'label'               => $result->st_name,
          'filter'              => $individual_filter,
          "pc_no"               => $result->pc_no,
          "pc_name"             => $result->pc_name,
          "st_code"             => $result->st_code,
          "ac_no"               => $result->ac_no,
          "ac_name"             => $ac_name,
          "old_total_male"      => $object_elector['old_total_male'],
          "old_total_female"    => $object_elector['old_total_female'],
          "old_total_other"     => $object_elector['old_total_other'],
          "old_total"           => $object_elector['old_total'],
          "total_male"          => $object_voter['total_voter_male'],
          "total_female"        => $object_voter['total_voter_female'],
          "total_other"         => $object_voter['total_voter_other'],
          "total"               => $object_voter['total_voter_total'],
          "total_percentage"    => $object_voter['total_percentage'],
          "href"                => 'javascript:void(0)'
        ];
      }


      //calculate total
      if ($data['pc_no'] && $data['state']) {
        $group_by = 'state';
      } else if ($data['pc_no']) {
        $group_by = 'pc_no';
      } else {
        $group_by = 'national';
      }


      $data['number_of_voting'] =  0;

      //calculate total
      $total_filter = [
        'phase'         => $data['phase'],
        'state'         => $data['state'],
        'pc_no'         => $data['pc_no'],
        'group_by'      => $group_by
      ];
      $total_object =  EndOfPollModel::get_reports($total_filter);


      if (count($total_object) > 0) {
        $result           = $total_object[0];
        $object_elector   = EndOfPollModel::get_total_elector([
          'phase'     => $data['phase'],
          'group_by'  => $group_by,
          'state'     => $data['state'],
          'pc_no'     => $data['pc_no'],
        ]);

        $object_voter    = EndOfPollModel::get_percentage_2019([
          'phase'     => $data['phase'],
          'group_by'  => $group_by,
          'state'     => $data['state'],
          'pc_no'     => $data['pc_no'],
        ]);

        $data['totals'] = [
          'label'               => 'Total',
          'filter'              => '',
          "pc_no"               => '',
          "pc_name"             => '',
          "ac_no"               => '',
          "ac_name"             => '',
          "st_code"             => $result->st_code,
          "ac_no"               => '',
          "old_total_male"      => $object_elector['old_total_male'],
          "old_total_female"    => $object_elector['old_total_female'],
          "old_total_other"     => $object_elector['old_total_other'],
          "old_total"           => $object_elector['old_total'],
          "total_male"          => $object_voter['total_voter_male'],
          "total_female"        => $object_voter['total_voter_female'],
          "total_other"         => $object_voter['total_voter_other'],
          "total"               => $object_voter['total_voter_total'],
          "total_percentage"    => $object_voter['total_percentage'],
          "href"                => ''
        ];

        $data['number_of_voting'] = $object_voter['total_percentage'];
      }

      $data['results']    =   $results;
      $data['user_data']  =   Auth::user();

      $data['heading_title_with_all'] = $data['heading_title'];

      if ($request->has('is_excel')) {
        if (isset($title_array) && count($title_array) > 0) {
          $data['heading_title'] .= "- " . implode(', ', $title_array);
        }
        return $data;
      }

      return view($this->view_path . '.pollday.end_of_poll.ac', $data);
    } catch (\Exception $e) {
      return Redirect::to('/eci/dashboard');
    }
  }

  public function export_excel_report_state(Request $request)
  {

    set_time_limit(6000);
    $data = $this->report_state($request->merge(['is_excel' => 1]));

    $export_data = [];
    $headings[] = [$data['heading_title']];

    $export_data[] = ['', 'Electors', '', '', '', 'Voters', '', '', '', ''];

    $export_data[] = ['State', 'Male', 'Female', 'Other', 'Total', 'Male', 'Female', 'Other', 'Total', 'Total Percentage'];


    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        ($lis['old_total_male']) ? $lis['old_total_male'] : '0',
        ($lis['old_total_female']) ? $lis['old_total_female'] : '0',
        ($lis['old_total_other']) ? $lis['old_total_other'] : '0',
        ($lis['old_total']) ? $lis['old_total'] : '0',
        ($lis['total_male']) ? $lis['total_male'] : '0',
        ($lis['total_female']) ? $lis['total_female'] : '0',
        ($lis['total_other']) ? $lis['total_other'] : '0',
        ($lis['total']) ? $lis['total'] : '0',
        ($lis['total_percentage']) ? $lis['total_percentage'] : '0',
      ];
    }

    $export_data[] = [
      $data['totals']['label'],
      ($data['totals']['old_total_male']) ? $data['totals']['old_total_male'] : '0',
      ($data['totals']['old_total_female']) ? $data['totals']['old_total_female'] : '0',
      ($data['totals']['old_total_other']) ? $data['totals']['old_total_other'] : '0',
      ($data['totals']['old_total']) ? $data['totals']['old_total'] : '0',

      ($data['totals']['total_male']) ? $data['totals']['total_male'] : '0',
      ($data['totals']['total_female']) ? $data['totals']['total_female'] : '0',
      ($data['totals']['total_other']) ? $data['totals']['total_other'] : '0',
      ($data['totals']['total']) ? $data['totals']['total'] : '0',
      ($data['totals']['total_percentage']) ? $data['totals']['total_percentage'] : '0',
    ];

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');

    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:J1');
    //       $sheet->mergeCells('B2:E2');
    //       $sheet->mergeCells('F2:I2');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function export_pdf_report_state(Request $request)
  {
    $data = $this->report_state($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.pollday.end_of_poll.state_pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }

  //export PC's
  public function export_excel_report_pc(Request $request)
  {

    set_time_limit(6000);
    $data = $this->report_pc($request->merge(['is_excel' => 1]));

    $export_data = [];
    $headings[] = [$data['heading_title']];
    $export_data[] = ['', '', '', 'Electors', '', '', '', 'Voters', '', '', '', ''];

    $export_data[] = ['State', 'pc no', 'pc name', 'Male', 'Female', 'Other', 'Total', 'Male', 'Female', 'Other', 'Total', 'Total Percentage'];


    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['pc_no'],
        $lis['pc_name'],
        ($lis['old_total_male']) ? $lis['old_total_male'] : '0',
        ($lis['old_total_female']) ? $lis['old_total_female'] : '0',
        ($lis['old_total_other']) ? $lis['old_total_other'] : '0',
        ($lis['old_total']) ? $lis['old_total'] : '0',
        ($lis['total_male']) ? $lis['total_male'] : '0',
        ($lis['total_female']) ? $lis['total_female'] : '0',
        ($lis['total_other']) ? $lis['total_other'] : '0',
        ($lis['total']) ? $lis['total'] : '0',
        ($lis['total_percentage']) ? $lis['total_percentage'] : '0',
      ];
    }

    $export_data[] = [
      $data['totals']['label'],
      $data['totals']['pc_no'],
      $data['totals']['pc_name'],
      ($data['totals']['old_total_male']) ? $data['totals']['old_total_male'] : '0',
      ($data['totals']['old_total_female']) ? $data['totals']['old_total_female'] : '0',
      ($data['totals']['old_total_other']) ? $data['totals']['old_total_other'] : '0',
      ($data['totals']['old_total']) ? $data['totals']['old_total'] : '0',
      ($data['totals']['total_male']) ? $data['totals']['total_male'] : '0',
      ($data['totals']['total_female']) ? $data['totals']['total_female'] : '0',
      ($data['totals']['total_other']) ? $data['totals']['total_other'] : '0',
      ($data['totals']['total']) ? $data['totals']['total'] : '0',
      ($data['totals']['total_percentage']) ? $data['totals']['total_percentage'] : '0',
    ];
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');


    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:L1');
    //       $sheet->mergeCells('D2:G2');
    //       $sheet->mergeCells('H2:K2');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function export_pdf_report_pc(Request $request)
  {
    $data = $this->report_pc($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.pollday.end_of_poll.pc_pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }

  //export AC's
  public function export_excel_report_ac(Request $request)
  {

    set_time_limit(6000);
    $data = $this->report_ac($request->merge(['is_excel' => 1]));

    $export_data = [];
    $headings[] = [$data['heading_title']];
    $export_data[] = ['', '', '', '', '', 'Electors', '', '', '', 'Voters', '', '', '', ''];

    $export_data[] = ['State', 'pc no', 'pc name', 'ac no', 'ac name', 'Male', 'Female', 'Other', 'Total', 'Male', 'Female', 'Other', 'Total', 'Total Percentage'];


    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['pc_no'],
        $lis['pc_name'],
        $lis['ac_no'],
        $lis['ac_name'],
        ($lis['old_total_male']) ? $lis['old_total_male'] : '0',
        ($lis['old_total_female']) ? $lis['old_total_female'] : '0',
        ($lis['old_total_other']) ? $lis['old_total_other'] : '0',
        ($lis['old_total']) ? $lis['old_total'] : '0',
        ($lis['total_male']) ? $lis['total_male'] : '0',
        ($lis['total_female']) ? $lis['total_female'] : '0',
        ($lis['total_other']) ? $lis['total_other'] : '0',
        ($lis['total']) ? $lis['total'] : '0',
        ($lis['total_percentage']) ? $lis['total_percentage'] : '0',
      ];
    }

    $export_data[] = [
      $data['totals']['label'],
      $data['totals']['pc_no'],
      $data['totals']['pc_name'],
      $data['totals']['ac_no'],
      $data['totals']['ac_name'],
      ($data['totals']['old_total_male']) ? $data['totals']['old_total_male'] : '0',
      ($data['totals']['old_total_female']) ? $data['totals']['old_total_female'] : '0',
      ($data['totals']['old_total_other']) ? $data['totals']['old_total_other'] : '0',
      ($data['totals']['old_total']) ? $data['totals']['old_total'] : '0',
      ($data['totals']['total_male']) ? $data['totals']['total_male'] : '0',
      ($data['totals']['total_female']) ? $data['totals']['total_female'] : '0',
      ($data['totals']['total_other']) ? $data['totals']['total_other'] : '0',
      ($data['totals']['total']) ? $data['totals']['total'] : '0',
      ($data['totals']['total_percentage']) ? $data['totals']['total_percentage'] : '0',
    ];

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');


    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:N1');
    //       $sheet->mergeCells('F2:I2');
    //       $sheet->mergeCells('J2:M2');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function export_pdf_report_ac(Request $request)
  {
    $data = $this->report_ac($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.pollday.end_of_poll.ac_pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }
}  // end class