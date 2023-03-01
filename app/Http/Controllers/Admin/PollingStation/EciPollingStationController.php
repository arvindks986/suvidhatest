<?php

namespace App\Http\Controllers\Admin\PollingStation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\commonModel;
use App\models\Admin\PollDayModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\PcModel;
use App\models\Admin\AcModel;

//INCLUDING CLASSES
use App\Classes\xssClean;
use App\Exports\ExcelExport;
use App\Helpers\LogNotification;
use App\Http\Controllers\Admin\Eci\Report\MissingTurnoutController;
use App\models\AC;
use App\models\Admin\EndOfPollFinaliseModel;
use Maatwebsite\Excel\Facades\Excel;

//POLLING STATION MODELS
use App\models\Admin\polling_station\PollingStationModel;
use App\models\Admin\turnout\TurnoutModel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class EciPollingStationController extends Controller
{


  public $base          = 'ro';
  public $folder        = 'eci';
  public $action        = 'eci/EciPsWiseDetails';
  public $actionEnableClosePollEntry        = 'eci/EnableClosePollEntry';
  public $action_pc     = 'eci/EciPsWiseDetails/state';
  public $action_ac     = 'eci/EciPsWiseDetails/state/ac';
  public $action_ps     = 'eci/EciPsWiseDetails/state/ps';
  public $view_path     = "admin.pc.eci";


  public function __construct()
  {
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->PollingStationModel = new PollingStationModel();
    $this->MissingTurnoutModel = new MissingTurnoutController;

    $this->middleware(function (Request $request, $next) {
      return $next($request);
    });
  }


  //ALL PCS BY STATE CODE
  public function get_pc_list(Request $request)
  {

    $PC_LIST = DB::table('m_pc')
      ->join('m_election_details', [
        ['m_election_details.ST_CODE', '=', 'm_pc.ST_CODE'],
        ['m_election_details.CONST_NO', '=', 'm_pc.PC_NO']
      ])
      ->where('m_pc.ST_CODE', '=', $request->input('id'))
      ->where('m_election_details.CONST_TYPE', 'PC')
      ->where('m_election_details.election_status', '1')
      ->where('m_election_details.ELECTION_ID', $request->input('election_id'))
      ->orderBy('m_pc.PC_NO', 'ASC')
      ->get();


    if ($PC_LIST) {
      return response()->json(['error' => false, 'status' => 200, 'data' => $PC_LIST]);
    } else {
      return response()->json(['error' => true, 'status' => 401, 'data' => '']);
    }
  }



  //ALL ACS BY STATE CODE AND PC CODE
  public function get_ac_list_by_st_pc(Request $request)
  {

    $AC_LIST = DB::table('m_ac')
      ->where('m_ac.ST_CODE', '=', $request->input('state'))
      ->where('m_ac.PC_NO', '=', $request->input('pc_id'))
      ->orderBy('m_ac.AC_NO', 'ASC')
      ->get();
    if ($AC_LIST) {
      return response()->json(['error' => false, 'status' => 200, 'acdata' => $AC_LIST]);
    } else {
      return response()->json(['error' => true, 'status' => 401, 'acdata' => '']);
    }
  }



  public function EciPsWiseDetails(Request $request)
  {

    $data = [];
    $request_array = [];
    $data['phases'] = PhaseModel::get_phases();
    $title_array  = [];
    $data['heading_title'] = "PS Wise Voter Turnout";

    $data['state'] = NULL;
    if ($request->has('state')) {
      $data['state'] = $request->state;
    }
    $data['pc_id'] = NULL;
    if ($request->has('pc_id')) {
      $data['pc_id'] = $request->pc_id;
    }
    $data['ac_id'] = NULL;
    if ($request->has('ac_id')) {
      $data['ac_id'] = $request->ac_id;
    }
    $xss = new xssClean;
    //CHECKING REQUEST VARIABES STARTS
    if ($request->has('state') && $request->has('pc_id') && $request->has('ac_id')) {
      $validator = Validator::make($request->all(), [
        'state'          => 'required|string',
        'pc_id'          => 'required|numeric',
        'ac_id'          => 'required|numeric',
      ]);

      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }
      $state    = $xss->clean_input($request->state);
      $pc_id    = $xss->clean_input($request->pc_id);
      $ac_id    = $xss->clean_input($request->ac_id);
      $filter_election = [
        'state'         => $state,
        'pc_no'         => $pc_id,
        'ac_no'         => $ac_id,
      ];


      $request_array[] =  'state=' . $state;
      $request_array[] =  'pc_id=' . $pc_id;
      $request_array[] =  'ac_id=' . $ac_id;

      $statename = getstatebystatecode($state);
      $pcname = getpcbypcno($state, $pc_id);
      $acame = getacbyacno($state, $ac_id);


      $title_array[] = "State: " . $statename->ST_NAME;
      $title_array[] = "PC: " . $pcname->PC_NAME;
      $title_array[] = "AC: " . $acame->AC_NAME;


      $data['filter_buttons'] = $title_array;

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
      $object         = PollingStationModel::get_ps_data($filter_election);
      $data['results']    =   $object;
    } else {
      $data['buttons']    = [];
      $data['action']         = url($this->action);
      $data['results'] = [];
    }
    $data['user_data']  =   Auth::user();
    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }
    return view($this->view_path . '.polling_station.PsWiseDetails', $data);
  }

  //EXCEL REPORT STARTS
  public function EciPsWiseDetailsExcel(Request $request)
  {
    set_time_limit(6000);
    $data = $this->EciPsWiseDetails($request->merge(['is_excel' => 1]));
    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['PS No', 'PS Name', 'PS Type', 'Electors Male', 'Electors Female', 'Electors Other', 'Electors Total', 'Voter Male', 'Voter Female', 'Voter Other', 'Voter Total'];
    $arr  = array();
    $TotalElectorMale = 0;
    $TotalElectorFeMale = 0;
    $TotalElectorOther = 0;
    $TotalElector = 0;
    $TotalVoterMale = 0;
    $TotalVoterFeMale = 0;
    $TotalVoterOther = 0;
    $TotalVoter = 0;

    $headings[] = [];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        ($lis->PS_NO) ? ($lis->PS_NO) : '0',
        ($lis->PS_NAME_EN) ? ($lis->PS_NAME_EN) : '0',
        ($lis->PS_TYPE) ? ($lis->PS_TYPE) : '0',
        ($lis->electors_male) ? ($lis->electors_male) : '0',
        ($lis->electors_female) ? ($lis->electors_female) : '0',
        ($lis->electors_other) ? ($lis->electors_other) : '0',
        ($lis->electors_total) ? ($lis->electors_total) : '0',
        ($lis->voter_male) ? ($lis->voter_male) : '0',
        ($lis->voter_female) ? ($lis->voter_female) : '0',
        ($lis->voter_other) ? ($lis->voter_other) : '0',
        ($lis->voter_total) ? ($lis->voter_total) : '0',

      ];

      $TotalElectorMale   += $lis->electors_male;
      $TotalElectorFeMale += $lis->electors_female;
      $TotalElectorOther  += $lis->electors_other;
      $TotalElector       += $lis->electors_total;
      $TotalVoterMale     += $lis->voter_male;
      $TotalVoterFeMale   += $lis->voter_female;
      $TotalVoterOther    += $lis->voter_other;
      $TotalVoter         += $lis->voter_total;

      // $totalvalues = array('Total','',$TotalElectorMale,$TotalElectorFeMale,$TotalElectorOther,$TotalElector,$TotalVoterMale,$TotalVoterFeMale,$TotalVoterOther,$TotalVoter);

    }
    //$export_data[] = $totalvalues;

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
  }
  //EXCEL REPORT ENDS


  public function EciPsWiseDetailsPdf(Request $request)
  {
    $data = $this->EciPsWiseDetails($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.polling_station.PsWiseDetailsPdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }



  public function getEciPcsListForMissedEntry(Request $request)
  {
    try {
      $user = Auth::user();
      if ($user->officername != 'ECIECI2' && $user->officername != 'PLANDIV') {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
      }
      $st_code     = base64_decode($request->input('state', ''));
      $request->merge([
        'is_excel' => 1,
        'state' => base64_encode($st_code)
      ]);
      $data = $this->MissingTurnoutModel->get_enable_acs_for_update($request);
      $data['buttons']    = [];
      $data['action']         = url('eci/EnableClosePollEntry');
      if (isset($data['states'][0]) && ($st_code == null || $st_code == "")) {
        $st_code = base64_decode($data['states'][0]['code']);
        $data['st_code'] = $st_code;
      }
      if (isset($data['phases'][0]) && ($data['phase'] == null || $data['phase'] == "")) {
        $data['phase'] = $data['phases'][0]->SCHEDULENO;
      }
      $estimated_time = getschedulebyid($data['phase']);
      $data['estimated_time'] = $estimated_time;
      $data['pcs'] = PcModel::get_distinct_pcs_with_state_name(['st_code' => $st_code, 'phase' => $data['phase']]);
      $data['acs'] = AcModel::get_distinct_acs_with_state_name(['st_code' => $st_code, 'phase' => $data['phase'], 'pc_no' => $data['pc_no']]);
      if (session()->has('admin_login')) {
        return view($this->view_path . '.polling_station.enable-close-poll-entry', $data);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function enbale_modified_acs(Request $request)
  {
    try {
      if (session()->has('admin_login')) {
        $state_code = $request->input('st_code');
        $phase_no = $request->input('phase_no');
        $round_no = $request->input('round_no');
        $ac_no = $request->input('ac_no');
        $data_option = $request->input('data_option');
        if ($data_option == 'on') {
          $flagval = 1;
          $message = 'enabled';
        } else {
          $message = 'disabled';
          $flagval = 0;
        }
        if (!empty($phase_no) && !empty($round_no) && !empty($ac_no)) {
          $missed_flag = 'modification_status_round' . $round_no;
          DB::table('pd_scheduledetail')->where('st_code', $state_code)->where('ac_no', $ac_no)->update([$missed_flag => $flagval]);
          Session::flash('success_mes', 'Option ' . $message . ' successfully.');
          return Redirect::back();
        } else {
          Session::flash('error_mes', 'Please try again');
          return Redirect::back();
        }
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function PcECIPSElectoralDefinalzied(Request $request)
  {
    try {
      $user = Auth::user();
      if ($user->officername != 'ECIECI2' && $user->officername != 'PLANDIV') {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
      }
      $user = Auth::user();
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $data = [];
      $data['user_data'] = $user_data;
      $data['election_type'] = ($request->has('election_type')) ?? $request->election_type;
      $data['phases'] = PhaseModel::get_phases(['election_type' => $data['election_type']]);
      $data['phase'] = ($request->has('phase')) ? $request->phase : 1;
      $data['state'] = ($request->has('state')) ? $request->state : null;
      $data['results'] = [];
      $data['states'] = EndOfPollFinaliseModel::with('state')->where('schedule_id',  $data['phase'])->orderBy('st_code')->groupBy('st_code')->get();
      if ($data['state'] != null) {
        $acsForSelectedPhase = EndOfPollFinaliseModel::where('schedule_id',  $data['phase'])->where('st_code', $request->state)->pluck('ac_no');
        $data['results'] = AC::with(['state', 'pc'  => function ($query) use ($request) {
          $query->where('ST_CODE', $request->state);
        }])->whereIn('AC_NO', $acsForSelectedPhase)->where('ST_CODE', $request->state)->orderBy('AC_NO')->get()->map(function ($item, $key) use ($request) {
          $temp = $item;
          $total_ps = PollingStationModel::getAcPollingStationCount($request->state, $item->AC_NO, $item->PC_NO);
          $total_ps_finalized = PollingStationModel::getAcPollingStationFinalizedCount($request->state, $item->AC_NO, $item->PC_NO);
          $total_ps_enable_for_edit = PollingStationModel::getAcPollingStationEnableForEditCount($request->state, $item->AC_NO, $item->PC_NO);
          if ($request->has('excel') && $request->input('excel') == 'download') {
            $temp = [];
            $temp['ST_CODE'] = $item->ST_CODE;
            $temp['ST_NAME'] = $item->state->ST_NAME;
            $temp['PC_NO'] = $item['PC_NO'];
            $temp['PC_NAME'] = $item['pc']['PC_NAME'];
            $temp['AC_NO'] = $item->AC_NO;
            $temp['AC_NAME'] = $item->AC_NAME;
            $temp['ps_finalized'] = (($total_ps != 0 && $total_ps_finalized != 0) && $total_ps == $total_ps_finalized) ? 'Finalized' : 'Not Yet Finalize';
          } else {
            $temp['ps_finalized'] = (($total_ps != 0 && $total_ps_finalized != 0) && $total_ps == $total_ps_finalized) ? 1 : 0;
            $temp['show_enable_edit_btn'] = ($total_ps_enable_for_edit > 0) ? 1 : 0;
          }
          return $temp;
        });
      }
      $filter = [
        'st_code'       => $data['state'],
        'election_id'   => $user->election_id,
        'pc_no'         => '',
      ];
      if ($data['phase'] != 1) {
        $filter['phase_no'] = $data['phase'];
      }
      $estimated_time = TurnoutModel::get_scheduletime($filter);
      $data['poll_date'] = $estimated_time->poll_date;
      $data['showDefinalizeAndEditEnableBtn'] = (date('Y-m-d') >= $estimated_time->poll_date) ? true : false;
      $data['heading_title'] = 'AC List with Polling Station Electorals Finalized Status';
      if ($request->has('excel') && $request->input('excel') == 'download') {
        $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], "Polling_Station_Electoral_Finalize_" . $data['state'] . "_Report"));
        $headings = [
          "State Code",
          "State Name",
          "PC No",
          "PC Name",
          "AC No",
          "AC Name",
          "Status",
        ];
        if (config("public_config.vt_log")) {
          $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
          $ErrorMessage['applicationType'] = 'WebApp';
          $ErrorMessage['Module'] = 'ENCORE';
          $ErrorMessage['TransectionType'] = 'VoterTurnout';
          $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral finalized report Imports';
          $ErrorMessage['TransectionStatus'] = 'Success';
          $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral finalized report Imports done for state " . $data['state'];
          LogNotification::LogInfo($ErrorMessage);
        }
        return Excel::download(new ExcelExport($headings, $data['results']), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
      } else {
        return view('admin.pc.ceo.polling_station.PcECIPSElectoralDefinalzied', $data);
      }
    } catch (Exception $ex) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral finalized report';
        $ErrorMessage['TransectionStatus'] = 'Failed';
        $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral finalized report failed ";
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function PcECIPSElectoralDefinalziedUpdate(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'ac_no'   => 'required',
        'st_code'   => 'required',
        'disableEdit'   => 'required',
      ]);

      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }
      $user = Auth::user();
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $data['user_data'] = $user_data;
      if ($request->disableEdit == 1) {
        $update = [
          'electors_enable_edit_by_eci' => 0,
          'electors_enable_edit_by_eci_datetime' => date('Y-m-d H:i:s', time())
        ];
        if (config("public_config.vt_log")) {
          $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
          $ErrorMessage['applicationType'] = 'WebApp';
          $ErrorMessage['Module'] = 'ENCORE';
          $ErrorMessage['TransectionType'] = 'VoterTurnout';
          $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral disabled modification Option';
          $ErrorMessage['TransectionStatus'] = 'Success';
          $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral disabled modification Option successfully for state " . $request->st_code . " AC " . $request->ac_no;
          LogNotification::LogInfo($ErrorMessage);
        }
      } else {
        $update = [
          'electors_finalize_by_ro' => 0,
          'electors_finalize_by_ro_date' => date('Y-m-d H:i:s', time()),
          'electors_enable_edit_by_eci' => 1,
          'electors_enable_edit_by_eci_datetime' => date('Y-m-d H:i:s', time())
        ];
        if (config("public_config.vt_log")) {
          $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
          $ErrorMessage['applicationType'] = 'WebApp';
          $ErrorMessage['Module'] = 'ENCORE';
          $ErrorMessage['TransectionType'] = 'VoterTurnout';
          $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral definalized and Enable Edit Option';
          $ErrorMessage['TransectionStatus'] = 'Success';
          $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral definalized and Enable Edit Option successfully for state " . $request->st_code . " AC " . $request->ac_no;
          LogNotification::LogInfo($ErrorMessage);
        }
      }
      PollingStationModel::where('ST_CODE', $request->st_code)->where('AC_NO', $request->ac_no)->update($update);
      $msg = ($request->disableEdit == 1) ? "Polling Station Electorals modification is disabled RO has to Finalize it Again from thier respected Account" : "Polling Station Electorals is definalized and Modification option is enabled";
      return redirect()->back()->with("success", $msg);
    } catch (Exception $ex) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral definalized and Enable Edit Option';
        $ErrorMessage['TransectionStatus'] = 'Failed';
        $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral definalized and Enable Edit Option failed for AC " . $request->ac_no;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }
}
