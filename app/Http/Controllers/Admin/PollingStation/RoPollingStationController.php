<?php

namespace App\Http\Controllers\Admin\PollingStation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use App\commonModel;
use App\models\Admin\PollDayModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\AcModel;

//INCLUDING CLASSES
use App\Classes\xssClean;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

//POLLING STATION MODELS
use App\models\Admin\polling_station\PollingStationModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//current

class RoPollingStationController extends Controller
{


  public $base          = 'ro';
  public $folder        = 'ropc';
  public $action        = 'ropc/RoPsWiseDetails';
  public $action_pc     = 'ropc/RoPsWiseDetails/state';
  public $action_ac     = 'ropc/RoPsWiseDetails/state/ac';
  public $action_ps     = 'ropc/RoPsWiseDetails/state/ps';
  public $actionupdate  = 'ropc/RoPsWiseDetailsUpdate';
  public $view_path     = "admin.pc.ro";


  public function __construct()
  {
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->PollingStationModel = new PollingStationModel();
    if (!Auth::user()) {
      return redirect('/officer-login');
    }
  }



  public function RoPsWiseDetails(Request $request)
  {
    $data = [];
    $request_array = [];
    $data['phases'] = PhaseModel::get_phases();
    $title_array  = [];
    $data['heading_title'] = "PS Wise Voter Turnout";

    $data['ac_id'] = NULL;
    if ($request->has('ac_id')) {
      $data['ac_id'] = $request->ac_id;
    }
    $data['user_data']  =   Auth::user();

    $ele_details = $this->commonModel->election_details(Auth::user()->st_code, Auth::user()->ac_no, Auth::user()->pc_no, Auth::user()->id, 'PC');

    $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
    if ($check_finalize == '') {
      $cand_finalize_ceo = 0;
      $cand_finalize_ro = 0;
    } else {
      $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
      $cand_finalize_ro = $check_finalize->finalized_ac;
    }

    $seched = getschedulebyid($ele_details->ScheduleID);
    $sechdul = checkscheduledetails($seched);

    $data['ele_details']         = $ele_details;
    $data['seched']              = $seched;
    $data['sechdul']             = $sechdul;
    $data['cand_finalize_ceo']   = $cand_finalize_ceo;
    $data['cand_finalize_ro']    = $cand_finalize_ro;

    if (Auth::user()->designation == 'ROPC') {
      $data['state'] = Auth::user()->st_code;
      $data['pc_id'] = Auth::user()->pc_no;
    }


    $xss = new xssClean;
    //CHECKING REQUEST VARIABES STARTS
    if ($request->has('ac_id')) {

      $validator = Validator::make($request->all(), [
        'ac_id'          => 'required|numeric',
      ]);

      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }
      $ac_id    = $xss->clean_input($request->ac_id);
      $filter_election = [
        'state'         => $data['state'],
        'pc_no'         => $data['pc_id'],
        'ac_no'         => $ac_id,
      ];
      $request_array[] =  'state=' . $data['state'];
      $request_array[] =  'pc_id=' . $data['pc_id'];
      $request_array[] =  'ac_id=' . $ac_id;
      $statename = getstatebystatecode($data['state']);
      $pcname = getpcbypcno($data['state'], $data['pc_id']);
      $acame = getacbyacno($data['state'], $ac_id);
      $title_array[] = "State: " . $statename->ST_NAME;
      $title_array[] = "PC: " . $pcname->PC_NAME;
      $title_array[] = "AC: " . $acame->AC_NAME;

      $data['consituencies']  = AcModel::get_records([
        'state'         => $data['state'],
        'pc_no'         => $data['pc_id'],
      ]);
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
      $is_finalize  = PollingStationModel::get_ps_finalize_data_deo($filter_election);
      if (count($is_finalize) > 0) {
        foreach ($is_finalize as $k => $v) {
          if ($v->deo_ps_finalize == 0) {
            $data['is_finalize'] = 0;
          } else {
            $data['is_finalize'] = 1;
          }
        }
      } else {
        $data['is_finalize'] = 0;
      }
      $filter_election_ro = [
        'st_code'         => $data['state'],
        'pc_no'         => $data['pc_id'],
        'const_no'         => $ac_id,
      ];
      $ac_data         = PollingStationModel::get_ac_data($filter_election);
      $data['ac_data'] = $ac_data;
      $is_finalize_ro  = PollingStationModel::get_ps_finalize_data_ro($filter_election_ro);
      if (count($is_finalize_ro) > 0) {
        foreach ($is_finalize_ro as $k => $v) {
          if ($v->ro_ps_finalize == 0) {
            $data['is_finalize_ro'] = 0;
          } else {
            $data['is_finalize_ro'] = 1;
          }
        }
      } else {
        $data['is_finalize_ro'] = 0;
      }
      $is_finalize_ceo  = PollingStationModel::get_ps_finalize_data_ceo($filter_election);
      if (count($is_finalize_ceo) > 0) {
        foreach ($is_finalize_ceo as $k => $v) {
          if ($v->ps_finalize == 0) {
            $data['is_finalize_ceo'] = 0;
          } else {
            $data['is_finalize_ceo'] = 1;
          }
        }
      } else {
        $data['is_finalize_ceo'] = 0;
      }

      $filter = [
        'st_code'       => $data['state'],
        'ac_no'         => $ac_id
      ];
      $data['lists'] = $this->PollingStationModel->get_scheduledetail($filter);
    } else {
      $ac_id = 0;
      $filter = [
        'st_code'       => $data['state'],
        'ac_no'         => $ac_id
      ];
      $lists = $this->PollingStationModel->get_scheduledetail($filter);
      $data['lists'] = $lists;
      $data['is_finalize'] = 0;
      $data['is_finalize_ceo'] = 0;
      $data['is_finalize_ro'] = 0;
      $data['buttons']    = [];
      $data['action']         = url($this->action);
      $data['results'] = [];

      $data['consituencies']  = AcModel::get_records([
        'state'         => $data['state'],
        'pc_no'         => $data['pc_id'],
      ]);
    }
    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }
    return view($this->view_path . '.polling_station.RoPsWiseDetails', $data);
  }

  //EXCEL REPORT STARTS
  public function RoPsWiseDetailsExcel(Request $request)
  {
    set_time_limit(6000);
    $data = $this->RoPsWiseDetails($request->merge(['is_excel' => 1]));
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

      // $totalvalues = array('Total','','',$TotalElectorMale,$TotalElectorFeMale,$TotalElectorOther,$TotalElector,$TotalVoterMale,$TotalVoterFeMale,$TotalVoterOther,$TotalVoter);

    }
    // $export_data[] = $totalvalues;

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
  }
  //EXCEL REPORT ENDS


  public function RoPsWiseDetailsPdf(Request $request)
  {
    $data = $this->RoPsWiseDetails($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.polling_station.RoPsWiseDetailsPdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }




  public function RoPsWiseDetailsUpdate(Request $request)
  {
    $user = Auth::user();
    if (session()->has('admin_login')) {
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $validator = Validator::make($request->all(), [
        //'PS_NAME_EN'        => 'required|string|min:2|max:350',
        'electors_male'     => 'required|numeric|min:0|integer|between:0,9999',
        'electors_female'   => 'required|numeric|min:0|integer|between:0,9999',
        'electors_other'    => 'required|numeric|min:0|integer|between:0,9999',
        'electors_total'    => 'required|numeric|min:0|integer|between:0,9999',
        'voter_male'        => 'required|numeric|min:0|integer|between:0,9999',
        'voter_female'      => 'required|numeric|min:0|integer|between:0,9999',
        'voter_other'       => 'required|numeric|min:0|integer|between:0,9999',
        'voter_total'       => 'required|numeric|min:0|integer|between:0,9999',
        'pc_no'             => 'required|numeric',
        'ac_no'             => 'required|numeric',
      ]);

      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }

      $xss = new xssClean;

      $request              = $request->all();
      // $PS_NAME_EN           = $xss->clean_input($request['PS_NAME_EN']);
      $electors_male        = $xss->clean_input($request['electors_male']);
      $electors_female      = $xss->clean_input($request['electors_female']);
      $electors_other       = $xss->clean_input($request['electors_other']);
      $electors_total       = $xss->clean_input($request['electors_total']);
      $voter_male           = $xss->clean_input($request['voter_male']);
      $voter_female         = $xss->clean_input($request['voter_female']);
      $voter_other          = $xss->clean_input($request['voter_other']);
      $voter_total          = $xss->clean_input($request['voter_total']);
      $psno                 = $xss->clean_input($request['psnoinput']);
      $ccode                = $xss->clean_input($request['psccode']);
      $pc_no                = $xss->clean_input($request['pc_no']);
      $ac_no                = $xss->clean_input($request['ac_no']);

      if ($electors_male + $electors_female + $electors_other != $electors_total) {
        return Redirect::back()->with('error', 'Data Mismatch in Electors Data.');
      }
      //ELECTORS DATA MATCHING ENDS

      //VOTERS DATA MATCHING STARTS
      if ($voter_male + $voter_female + $voter_other != $voter_total) {
        return Redirect::back()->with('error', 'Data Mismatch in Voters Data.');
      }
      //VOTERS DATA MATCHING ENDS


      $update_fields = array(
        //'PS_NAME_EN'         => $PS_NAME_EN,
        'electors_male'      => $electors_male,
        'electors_female'    => $electors_female,
        'electors_other'     => $electors_other,
        'electors_total'     => $electors_total,
        'voter_male'         => $voter_male,
        'voter_female'       => $voter_female,
        'voter_other'        => $voter_other,
        'voter_total'        => $voter_total,
      );
      $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $ac_no, 'PS_NO' => $psno, 'CCODE' => $ccode];
      DB::table('polling_station')->where($PsWiseDetailsWhere)->update($update_fields);
      return Redirect::back()->with('error', 'Polling Station Data Updated Successfully !');
    } else {
      return redirect('/admin-login');
    }
  }


  public function RoPCPsDefinalizeUpdate(Request $request)
  {
    $user = Auth::user();
    $uid = $user->id;
    $user_data = $this->commonModel->getunewserbyuserid($uid);
    $request     = $request->all();
    $ac_no = $request['ac_no'];

    $update_fields = array(
      'ro_ps_finalize_date'   => NULL,
      'ro_ps_finalize'        => 0,
      'deo_ps_finalize'        => 0,
    );
    $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $ac_no];
    DB::table('polling_station')->where($PsWiseDetailsWhere)->update($update_fields);
    return Redirect::back()->with('error', 'Polling Station Data definalized Successfully !');
  }


  public function RoPCFinalizeUpdate(Request $request)
  {
    $user = Auth::user();
    $uid = $user->id;
    $user_data = $this->commonModel->getunewserbyuserid($uid);
    $request     = $request->all();
    $ac_no = $request['ac_no'];
    $update_fields = array(
      'deo_ps_finalize'        => 1,
      'deo_ps_finalize_date'        => now()
    );
    $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $ac_no];
    DB::table('polling_station')->where($PsWiseDetailsWhere)->update($update_fields);
    return Redirect::back()->with('error', 'Polling Station Data finalized Successfully !');
  }

  public function DeoPsFinalizeAndCeoUpdate(Request $request)
  {
    $user = Auth::user();
    $uid = $user->id;
    $user_data = $this->commonModel->getunewserbyuserid($uid);
    $request     = $request->all();
    $ac_no = $request['ac_no'];
    $update_fields = array(
      'ro_ps_finalize_date'   => now(),
      'ro_ps_finalize'        => 1,
      'deo_ps_finalize'        => 1,
      'deo_ps_finalize_date'        => now()
    );

    $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $ac_no];

    DB::table('polling_station')->where($PsWiseDetailsWhere)->update($update_fields);
    return Redirect::back()->with('error', 'Polling Station Data finalized Successfully !');
  }
}  // end class
