<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\commonModel;
use App\adminmodel\ECIModel;
use App\models\Admin\polling_station\PollingStationModel;
use App\Classes\xssClean;
use App\Http\Traits\CommonTraits;
use App\adminmodel\Pollday;
use App\Exports\ExcelExport;
use App\Helpers\LogNotification;
use App\Imports\PollingStationImport;
use App\models\Admin\ElectorModel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

date_default_timezone_set('Asia/Kolkata');


class ElectorsDetailsController extends Controller
{
  //USING TRAIT FOR COMMON FUNCTIONS
  use CommonTraits;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware(['auth:admin', 'auth']);
    $this->middleware('clean_url');
    $this->middleware('aro');
    $this->commonModel = new commonModel();
    $this->ECIModel = new ECIModel();
    $this->pollday = new Pollday;
    $this->PollingStationModel = new PollingStationModel();
  }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */

  protected function guard()
  {
    return Auth::guard();
  }



  //ECI ELECTORS DEATILS  STARTS
  public function ElectorsDetails(Request $request)
  {
    //ECI ELECTORS DEATILS  TRY CATCH BLOCK STARTS
    try {
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;

        $user_data = $this->commonModel->getunewserbyuserid($uid);

        $ElectorsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $user_data->ac_no];

        $ElectorsDetails = DB::table('electors_cdac')->where($ElectorsWhere)->get();
        $pdseche_details = DB::table('pd_scheduledetail')->where('st_code', $user_data->st_code)->where('ac_no', $user_data->ac_no)->first();
        $end_of_poll_finalize = 0;
        if (isset($pdseche_details)) {
          $end_of_poll_finalize = $pdseche_details->end_of_poll_finalize;
        }

        return view('admin.pc.ro.voting.ElectorsDetails', ['user_data' => $user_data, 'ElectorsDetails' => $ElectorsDetails, 'ScheduleID' => @$ele_details->ScheduleID, 'end_of_poll_finalize' => $end_of_poll_finalize]);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //ECI ELECTORS DEATILS  TRY CATCH BLOCK ENDS

  }
  //ECI ELECTORS DEATILS  FUNCTION ENDS


  //ECI ELECTORS DEATILS UPATE STARTS
  public function ElectorsDetailsUpdate(Request $request)
  {
    //ECI ELECTORS DEATILS  UPATE TRY CATCH BLOCK STARTS
    try {
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $ElectorsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $user_data->ac_no];
        $validator = Validator::make($request->all(), [
          'electors_male'     => 'required|numeric|min:1|integer|between:1,9999999',
          'electors_female'   => 'required|numeric|min:1|integer|between:1,9999999',
          'electors_other'    => 'required|numeric|min:0|integer|between:0,9999999',
          'electors_total'    => 'required|numeric|min:1|integer|between:1,9999999',

        ]);
        if ($validator->fails()) {
          return Redirect::back()
            ->withErrors($validator)
            ->withInput();
        }

        $xss = new xssClean;

        $request              = $request->all();
        $electors_male        = $xss->clean_input($request['electors_male']);
        $electors_female      = $xss->clean_input($request['electors_female']);
        $electors_other       = $xss->clean_input($request['electors_other']);
        $electors_total       = $xss->clean_input($request['electors_total']);
        $update_fields = array(
          'electors_male'      => $electors_male,
          'electors_female'    => $electors_female,
          'electors_other'     => $electors_other,
          'electors_total'     => $electors_total,
        );

        $ElectorsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $user_data->ac_no];
        DB::table('electors_cdac')->where($ElectorsWhere)->update($update_fields);
        if (config("public_config.vt_log")) {
          $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
          $ErrorMessage['applicationType'] = 'WebApp';
          $ErrorMessage['Module'] = 'ENCORE';
          $ErrorMessage['TransectionType'] = 'VoterTurnout';
          $ErrorMessage['TransectionAction'] = 'AC Electoral Details Update';
          $ErrorMessage['TransectionStatus'] = 'Success';
          $ErrorMessage['LogDescription'] = "AC Electoral Details Update done for st_code " . $user_data->st_code . " ac " . $user_data->ac_no;
          LogNotification::LogInfo($ErrorMessage);
        }
        return Redirect('/aro/voting/ElectorsDetails/')->with('success', 'Electrol Data Updated Successfully !');
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'AC Electoral Details Update';
        $ErrorMessage['TransectionStatus'] = 'Failed';
        $ErrorMessage['LogDescription'] = "AC Electoral Details Update failed";
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //ECI ELECTORS DEATILS  UPATE TRY CATCH BLOCK ENDS

  }
  //ECI ELECTORS DEATILS  UPATE FUNCTION ENDS


  //ECI PS WISE DEATILS  STARTS
  public function PsWiseDetails(Request $request)
  {
    //ECI PS WISE DEATILS  TRY CATCH BLOCK STARTS
    try {
      $round = '';
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $d = $this->commonModel->getunewserbyuserid($uid);
        $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $user_data->ac_no];
        $PsWiseDetails = DB::table('polling_station')->where($PsWiseDetailsWhere)
          ->orderByRaw("CONVERT(`PS_NO`,INT) ASC")
          ->get();
        $cyear = date('Y');
        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
        $seched = getschedulebyid($ele_details->ScheduleID);
        $round = base64_decode($round);

        $lists = DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $d->ac_no)->first();
        $ele = getcdacelectorsdetails($ele_details->ST_CODE, $d->ac_no, $cyear);
        $filter_election = [
          'st_code' => $ele_details->ST_CODE, 'const_no' => $d->ac_no, 'year' => $cyear,
        ];
        $total_round = $this->pollday->get_total_roundnewac($ele_details->ST_CODE, $d->ac_no, $cyear);

        $total_male   = 0;
        $total_female = 0;
        $total_other  = 0;
        $total_total  = 0;
        if (isset($total_round) && $total_round) {
          $total_male   = $total_round->total_male;
          $total_female = $total_round->total_female;
          $total_other  = $total_round->total_other;
          $total_total  = $total_round->total;
        }


        $total_elector_male   = 0;
        $total_elector_female = 0;
        $total_elector_other  = 0;
        $total_elector_total  = 0;
        $total_electors = $this->pollday->get_elector_totalac($ele_details->ST_CODE, $d->ac_no, $cyear);

        if (isset($total_electors) && $total_electors) {
          $total_elector_male   = $total_electors['electors_male'];
          $total_elector_female = $total_electors['electors_female'];
          $total_elector_other  = $total_electors['electors_other'];
          $total_elector_total  = $total_electors['electors_total'];
        }


        $totalturnout_per = 0;
        $maleturnout_per  = 0;
        $femaleturnout_per  = 0;
        $othersturnout_per = 0;

        if ($total_male > 0 && $total_elector_male > 0) {
          $maleturnout_per = round((($total_male / $total_elector_male) * 100), 2);
        }
        if ($total_female > 0 && $total_elector_female > 0) {
          $femaleturnout_per = round((($total_female / $total_elector_female) * 100), 2);
        }
        if ($total_other > 0 && $total_elector_other > 0) {
          $othersturnout_per = round((($total_other / $total_elector_other) * 100), 2);
        }
        if ($total_total > 0 && $total_elector_total > 0) {
          $totalturnout_per = round((($total_total / $total_elector_total) * 100), 2);
        }

        $is_finalize  = PollingStationModel::get_ps_finalize_data_ro($filter_election);
        if (count($is_finalize) > 0) {
          foreach ($is_finalize as $k => $v) {
            if ($v->ro_ps_finalize == 0) {
              $is_finalize = 0;
            } else {
              $is_finalize = 1;
            }
          }
        } else {
          $is_finalize = 0;
        }

        $showFinalizeBtn = false;
        $showTableColumns = false;
        if ($lists->end_of_poll_finalize == 0 && $is_finalize == 0 && count($PsWiseDetails) > 0) {
          if ($seched['DATE_POLL'] == date('Y-m-d')) {
            $showFinalizeBtn = true;
            $showTableColumns = true;
          } else if ($seched['DATE_POLL'] < date('Y-m-d')) {
            $showFinalizeBtn = true;
            $showTableColumns = true;
          }
        } else if ($is_finalize == 1) {
          $showTableColumns = true;
        }
        return view('admin.pc.ro.voting.PsWiseDetails', ['user_data' => $user_data, 'PsWiseDetails' => $PsWiseDetails, 'lists' => $lists, 'totalturnout_per' => $totalturnout_per, 'maleturnout_per' => $maleturnout_per, 'femaleturnout_per' => $femaleturnout_per, 'othersturnout_per' => $othersturnout_per, 'ele' => $ele, 'is_finalize' => $is_finalize, 'showFinalizeBtn' => $showFinalizeBtn, 'showTableColumns' => $showTableColumns]);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //ECI PS WISE DEATILS  TRY CATCH BLOCK ENDS

  }
  //ECI PS WISE DEATILS  FUNCTION ENDS


  //ECI PS WISE DEATILS UPATE STARTS
  public function PsWiseDetailsUpdate(Request $request)
  {
    //ECI PS WISE DEATILS  UPATE TRY CATCH BLOCK STARTS
    try {
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $user_data->ac_no];

        $validationArray = [
          'voter_male'     => 'required|numeric|min:0|integer|between:0,9999',
          'voter_female'   => 'required|numeric|min:0|integer|between:0,9999',
          'voter_other'    => 'required|numeric|min:0|integer|between:0,9999',
          'voter_total'    => 'required|numeric|min:0|integer|between:0,9999'

        ];
        $validator = Validator::make($request->all(), $validationArray);
        if ($validator->fails()) {
          return Redirect::back()
            ->withErrors($validator)
            ->withInput();
        }


        $xss = new xssClean;

        $request              = $request->all();
        $voter_male           = $xss->clean_input($request['voter_male']);
        $voter_female         = $xss->clean_input($request['voter_female']);
        $voter_other          = $xss->clean_input($request['voter_other']);
        $voter_total          = $xss->clean_input($request['voter_total']);
        $psno                 = $xss->clean_input($request['psnoinput']);
        $ccode                = $xss->clean_input($request['psccode']);

        $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $user_data->ac_no, 'PS_NO' => $psno, 'CCODE' => $ccode];

        $currentPollingStation = PollingStationModel::where($PsWiseDetailsWhere)->first();
        if($voter_male >  $currentPollingStation->electors_male || $voter_female >  $currentPollingStation->electors_female || $voter_other >  $currentPollingStation->electors_other){
          return Redirect::back()->with('error', 'Male, Female and Other Voters must be less than or equal to Electors Male, Female and Other respectively.');
        }

        //VOTERS DATA MATCHING STARTS
        if ($voter_male + $voter_female + $voter_other != $voter_total) {
          if (config("public_config.vt_log")) {
            $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
            $ErrorMessage['applicationType'] = 'WebApp';
            $ErrorMessage['Module'] = 'ENCORE';
            $ErrorMessage['TransectionType'] = 'VoterTurnout';
            $ErrorMessage['TransectionAction'] = 'Polling Station Voters Data updated';
            $ErrorMessage['TransectionStatus'] = 'Failed';
            $ErrorMessage['LogDescription'] = "Polling Station Voters Data updated failed voters data is not equal to total voter st_code " . $user_data->st_code . " Ac " . $user_data->ac_no . " CCODE " . $ccode;
            LogNotification::LogInfo($ErrorMessage);
          }
          return Redirect('/aro/voting/PsWiseDetails/')->with('error', 'Data Mismatch in Voters Data.');
        }
        //VOTERS DATA MATCHING ENDS


        $update_fields = array(
          'voter_male'      => $voter_male,
          'voter_female'    => $voter_female,
          'voter_other'     => $voter_other,
          'voter_total'     => $voter_total,
        );
        
        PollingStationModel::where($PsWiseDetailsWhere)->update($update_fields);
        if (config("public_config.vt_log")) {
          $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
          $ErrorMessage['applicationType'] = 'WebApp';
          $ErrorMessage['Module'] = 'ENCORE';
          $ErrorMessage['TransectionType'] = 'VoterTurnout';
          $ErrorMessage['TransectionAction'] = 'Polling Station Voters Data updated';
          $ErrorMessage['TransectionStatus'] = 'Success';
          $ErrorMessage['LogDescription'] = "Polling Station Voters Data updated st_code " . $user_data->st_code . " Ac " . $user_data->ac_no . " CCODE " . $ccode;
          LogNotification::LogInfo($ErrorMessage);
        }
        return Redirect('/aro/voting/PsWiseDetails/')->with('error', 'Polling Station Data Updated Successfully !');
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station Voters Data update';
        $ErrorMessage['TransectionStatus'] = 'Failes';
        $ErrorMessage['LogDescription'] = "Polling Station Voters Data update failed ";
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //ECI PS WISE DEATILS  UPATE TRY CATCH BLOCK ENDS

  }
  //ECI PS WISE DEATILS  UPATE FUNCTION ENDS
  public function PsWiseFinalize(Request $request)
  {
    try {
      $users = Session::get('admin_login_details');
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      if ($users) {
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $ac_no = $user_data->ac_no;
        $filter_election = [
          'state'         => $d->st_code,
          'ac_no'         => $d->ac_no,
        ];


        $ac_data         = PollingStationModel::get_ac_data($filter_election);
        $round = 'end';
        $m = $round . "_voter_male";
        $f = $round . "_voter_female";
        $o = $round . "_voter_other";
        $t = $round . "_voter_total";

        $st = array(
          $m => $ac_data->voter_male,
          $f => $ac_data->voter_female,
          $o => $ac_data->voter_other,
          $t => $ac_data->voter_total,
          'updated_at' => date("Y-m-d h:m:s"),
          'added_update_at' => date("Y-m-d"),
          'updated_by' => $d->officername,
          'total_male' => $ac_data->voter_male,
          'total_female' => $ac_data->voter_female,
          'total_other' => $ac_data->voter_other,
          'total' => $ac_data->voter_total
        );

        DB::table('pd_scheduledetail')->where('st_code', $d->st_code)->where('ac_no', $d->ac_no)->update($st);
        $update_fields = array(
          'ro_ps_finalize_date'   => now(),
          'ro_ps_finalize'        => 1,
        );

        $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $ac_no];

        DB::table('polling_station')->where($PsWiseDetailsWhere)->update($update_fields);
        if (config("public_config.vt_log")) {
          $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
          $ErrorMessage['applicationType'] = 'WebApp';
          $ErrorMessage['Module'] = 'ENCORE';
          $ErrorMessage['TransectionType'] = 'VoterTurnout';
          $ErrorMessage['TransectionAction'] = 'Polling Station Voters Data Finalized';
          $ErrorMessage['TransectionStatus'] = 'Success';
          $ErrorMessage['LogDescription'] = "Polling Station Voters Data Finalized st_code " . $user_data->st_code . " Ac " . $ac_no;
          LogNotification::LogInfo($ErrorMessage);
        }
        return Redirect::back()->with('success_mes', 'Polling Station Data Finalized Successfully !');
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station Voters Data Finalized';
        $ErrorMessage['TransectionStatus'] = 'Failed';
        $ErrorMessage['LogDescription'] = "Polling Station Voters Data Finalized";
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function PollingStationElectorsDetails(Request $request)
  {
    $data  = [];
    try {
      $user = Auth::user();
      $user_data = $this->commonModel->getunewserbyuserid($user->id);
      $data['user_data']    = $user_data;
      $ele_details = $this->commonModel->election_details($user_data->st_code, $user_data->ac_no, $user_data->pc_no, $user_data->id, 'PC');
      $statename = getstatebystatecode($ele_details->ST_CODE);
      $acame = getacbyacno($ele_details->ST_CODE, $ele_details->CONST_NO);
      $title_array[] = "State: " . $statename->ST_NAME;
      $title_array[] = "AC: " . $acame->AC_NAME;
      $data['seched'] = getschedulebyid($ele_details->ScheduleID);
      $data['filter_buttons'] = $title_array;
      $data['psTotalElectorMale'] = 0;
      $data['psTotalElectorFemale'] = 0;
      $data['psTotalElectorOther'] = 0;
      $data['psTotalElector'] = 0;
      $data['results'] = PollingStationModel::where('ST_CODE', $ele_details->ST_CODE)->where('AC_NO', $user_data->ac_no)->where('pc_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->orderBy('PART_NO')->get();
      $data['electorCdac'] = ElectorModel::select('electors_male', 'electors_female', 'electors_other', 'electors_service', 'electors_total')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $user_data->ac_no)->where('election_id', $ele_details->ELECTION_ID)->first();
      $totalPsElectoralDataFinalizedCount = 0;
      $modificationEnabledByECI = 0;
      foreach ($data['results'] as $key => $item) {
        $data['psTotalElectorMale'] = $data['psTotalElectorMale'] + $item->electors_male;
        $data['psTotalElectorFemale'] = $data['psTotalElectorFemale'] + $item->electors_female;
        $data['psTotalElectorOther'] = $data['psTotalElectorOther'] + $item->electors_other;
        $data['psTotalElector'] = $data['psTotalElector'] + $item->electors_total;
        if ($item->electors_finalize_by_ro == 1) {
          $totalPsElectoralDataFinalizedCount++;
        }
        if ($item->electors_enable_edit_by_eci == 1) {
          $modificationEnabledByECI++;
        }
      }
      $data['totalPsElectoralDataFinalized'] = $totalPsElectoralDataFinalizedCount;
      $data['modificationEnabledByECI'] = $modificationEnabledByECI;
      $data['heading_title']    = "Polling Station Electors Details";
      $data['diabledPSModifications']    = ( date('Y-m-d') >= $data['seched']['DATE_POLL']) ? true : false;
      $data['importPollingStationStatus']= true;
      if(count($data['results']) > 0 && count($data['results']) == $totalPsElectoralDataFinalizedCount){
        $data['importPollingStationStatus'] = false;
      }
      if($data['diabledPSModifications']){
        $data['importPollingStationStatus'] = false;
      }
      return view('admin.pc.ro.voting.PollingStationElectorsDetails', $data);
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function PollingStationElectorsDetailsExport(Request $request)
  {
    try {
      $user = Auth::user();
      $user_data = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_details($user_data->st_code, $user_data->ac_no, $user_data->pc_no, $user_data->id, 'PC');
      $export_data = PollingStationModel::where('ST_CODE', $ele_details->ST_CODE)->where('AC_NO', $user_data->ac_no)->where('pc_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->select(["PART_NO", "PART_NAME", "PS_NO", "PS_NAME_EN", "PS_TYPE", "PS_CATEGORY", "LOCN_TYPE", "electors_male", "electors_female", "electors_other", "electors_total"])->orderBy('PART_NO')->get();
      $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], "Polling_Station_State_" . $ele_details->ST_CODE . "_AC_" . $ele_details->CONST_NO . "_Report"));
      $headings = [
        "Part No",
        "Part Name",
        "PS No",
        "PS Name EN",
        "PS Type",
        "PS Category",
        "Location Type",
        "Electors Male",
        "Electors Female",
        "Electors Other",
        "Electors Total",
      ];
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station export';
        $ErrorMessage['TransectionStatus'] = 'Success';
        $ErrorMessage['LogDescription'] = "Polling Station export success ac " . $user_data->ac_no . " st_code " . $user_data->st_code;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
    } catch (Exception $ex) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station export';
        $ErrorMessage['TransectionStatus'] = 'Failed';
        $ErrorMessage['LogDescription'] = "Polling Station export failed";
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }


  public function PollingStationElectorsDetailsUpdate(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'psccode'     => 'required',
        'PS_NAME_EN'     => 'required',
        'PART_NAME'     => 'required',
        'electors_male'   => 'required|numeric|min:0|integer|between:0,9999999',
        'electors_female'    => 'required|numeric|min:0|integer|between:0,9999999',
        'electors_other'    => 'required|numeric|min:0|integer|between:0,9999999',
        'electors_total' => 'required|numeric|min:0|integer|between:0,9999999',
      ]);


      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }

      if (($request->electors_male + $request->electors_female + $request->electors_other) !=  $request->electors_total) {
        return Redirect('/aro/voting/polling-station-electors-details')->with('error', 'Total electors are not equal to the sum of male, female and other electors.');
      }
      $update = $request->all();
      unset($update['_token']);
      unset($update['psccode']);
      PollingStationModel::where('CCODE', $request->psccode)->update($update);
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station electoral updated';
        $ErrorMessage['TransectionStatus'] = 'Success';
        $ErrorMessage['LogDescription'] = "Polling Station electoral updated psccode " . $request->psccode;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/aro/voting/polling-station-electors-details')->with('success', 'Polling Station Updated Successfully !');
    } catch (Exception $ex) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station electoral update';
        $ErrorMessage['TransectionStatus'] = 'Failed';
        $ErrorMessage['LogDescription'] = "Polling Station electoral update failed";
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }


  public function PollingStationImport(Request $request)
  {
    try {
      $validator = Validator::make(
        [
          'file'      => $request->excel,
          'extension' => strtolower($request->excel->getClientOriginalExtension()),
        ],
        [
          'file'          => 'required',
          'extension'      => 'required|in:xlsx,xml',
        ]
      );
      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }

      $user = Auth::user();
      $user_data = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_details($user_data->st_code, $user_data->ac_no, $user_data->pc_no, $user_data->id, 'PC');
      Excel::import(new PollingStationImport($ele_details, $user_data), request()->file('excel'));
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station impoted';
        $ErrorMessage['TransectionStatus'] = 'Success';
        $ErrorMessage['LogDescription'] = "Polling Station imported AC " . $user_data->ac_no . " State " . $user_data->st_code;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/aro/voting/polling-station-electors-details')->with('success', 'Your request is processed successfully.');
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
      $failures = $e->failures();

      foreach ($failures as $failure) {
        $failure->row(); // row that went wrong
        $failure->attribute(); // either heading key (if using heading row concern) or column index
        $failure->errors(); // Actual error messages from Laravel validator
        $failure->values(); // The values of the row that has failed.
        // dd($failure->errors());
      }
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station import';
        $ErrorMessage['TransectionStatus'] = 'Failed';
        $ErrorMessage['LogDescription'] = "Polling Station import failed";
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/aro/voting/polling-station-electors-details')->with('error', $failures);
    } catch (Exception $ex) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station import';
        $ErrorMessage['TransectionStatus'] = 'Failed';
        $ErrorMessage['LogDescription'] = "Polling Station import failed";
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function PollingStationElectorsFinalized(Request $request)
  {
    try {
      $user = Auth::user();
      $user_data = $this->commonModel->getunewserbyuserid($user->id);
      $update = [
        'electors_finalize_by_ro' => 1,
        'electors_enable_edit_by_eci' => 0,
        'electors_finalize_by_ro_date' => date('Y-m-d H:i:s', time())
      ];
      PollingStationModel::where('ST_CODE', $user_data->st_code)->where('AC_NO', $user_data->ac_no)->update($update);
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station electors finalzied';
        $ErrorMessage['TransectionStatus'] = 'Success';
        $ErrorMessage['LogDescription'] = "Polling Station electorals finalzied AC " . $user_data->ac_no . " State " . $user_data->st_code;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/aro/voting/polling-station-electors-details')->with('success', 'Polling Station Updated Successfully !');
    } catch (Exception $ex) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station electors finalzied';
        $ErrorMessage['TransectionStatus'] = 'Failed';
        $ErrorMessage['LogDescription'] = "Polling Station electorals finalzied AC failed";
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }
}  // end class
