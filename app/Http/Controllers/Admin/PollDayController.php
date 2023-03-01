<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\commonModel;
use App\Classes\xssClean;
use App\adminmodel\Pollday;
use App\Helpers\LogNotification;
use App\models\Admin\turnout\TurnoutModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PollDayController extends Controller
{
  //
  public function __construct()
  {
    $this->middleware('adminsession');
    $this->middleware(['auth:admin', 'auth']);
    $this->middleware('ro');
    //$this->middleware('clean_url');
    $this->commonModel = new commonModel();
    $this->xssClean = new xssClean;
    $this->pollday = new Pollday;
    $this->turnout = new TurnoutModel;
  }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  protected function guard()
  {
    return Auth::guard('admin');
  }

  public function index()
  {
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
      $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
      if ($check_finalize == '') {
        $cand_finalize_ceo = 0;
        $cand_finalize_ro = 0;
      } else {
        $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
        $cand_finalize_ro = $check_finalize->finalized_ac;
      }
      $list = DB::table('pd_schedulemaster')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)
        ->where('const_type', $ele_details->CONST_TYPE)->first();
      if (isset($list)) $pd_scheduleid = $list->pd_scheduleid;
      else $pd_scheduleid = '';

      return view('admin.pc.ro.voting.create-schedule', ['user_data' => $d, 'ele_details' => $ele_details, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'pd_scheduleid' => $pd_scheduleid, 'list' => $list]);
    } else {
      return redirect('/officer-login');
    }
  }  // end index function

  public function veryfy_schedule(Request $request)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
      $m_election = getelectiondetailbystcode($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
      //m_election_details
      $this->validate(
        $request,
        [
          'stdate' => 'required',
          'eddate' => 'required',
        ],
        [
          'stdate.required' => 'Please enter a Valid value',
          'eddate.required' => 'Please enter a Valid value',
        ]
      );
      $insstdate = $request->input('stdate');
      $inseddate = $request->input('eddate');
      $pd_scheduleid = $this->xssClean->clean_input($request->input('pd_scheduleid'));
      $month = date('m');
      $year = date('Y');
      $check = DB::table('pd_schedulemaster')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)
        ->where('const_type', $ele_details->CONST_TYPE)->first();
      if (isset($check)) {
        $sche_data = array(
          'year' => $year, 'month' => $month, 'start_time' => $insstdate, 'end_time' => $inseddate, 'added_update_at' => date("Y-m-d"),
          'updated_at' => date("Y-m-d H:m:s"), 'updated_by' => $d->officername
        );

        $this->commonModel->updatedata('pd_schedulemaster', 'pd_scheduleid', $pd_scheduleid, $sche_data);
        Session::flash('success_mes', 'You have Successfully updated');
      } else {
        $sche_data = array(
          'st_code' => $ele_details->ST_CODE, 'district_no' => $d->dist_no, 'pc_no' => $ele_details->CONST_NO,
          'const_type' => $ele_details->CONST_TYPE, 'schedule_id' => $ele_details->ScheduleID, 'state_phase_no' => $m_election->StatePHASE_NO,
          'm_election_detail_ccode' => $m_election->CCODE, 'electionid' => $ele_details->ELECTION_ID, 'election_type_id' => $ele_details->ELECTION_TYPEID,
          'year' => $year, 'month' => $month, 'start_time' => $insstdate, 'end_time' => $inseddate, 'added_create_at' => date("Y-m-d"),
          'created_at' => date("Y-m-d H:m:s"), 'created_by' => $d->officername
        );

        $n = DB::table('pd_schedulemaster')->insert($sche_data);
        $pid = DB::getPdo()->lastInsertId();
        $listac = getallacbypcno($ele_details->ST_CODE, $ele_details->CONST_NO);

        foreach ($listac as $key => $v) {
          $rec = array(
            'pd_scheduleid' => $pid, 'st_code' => $ele_details->ST_CODE, 'pc_no' => $ele_details->CONST_NO,
            'ac_no' => $v->AC_NO, 'added_create_at' => date("Y-m-d"), 'created_at' => date("Y-m-d H:m:s"), 'created_by' => $d->officername
          );
          $check_ac = DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)
            ->where('ac_no', $v->AC_NO)->first();
          if (!isset($check_ac))
            $n = DB::table('pd_scheduledetail')->insert($rec);
        }

        Session::flash('success_mes', 'You have Successfully Added. ');
      }

      return Redirect::to('ropc/voting/list-schedule');
    } else {
      return redirect('/officer-login');
    }
  }
  public function list_schedule(Request $request)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
      $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
      if ($check_finalize == '') {
        $cand_finalize_ceo = 0;
        $cand_finalize_ro = 0;
      } else {
        $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
        $cand_finalize_ro = $check_finalize->finalized_ac;
      }
      $droplist = $this->commonModel->selectAll('pd_schedule_round', 'id', 'ASC');
      $seched = getschedulebyid($ele_details->ScheduleID);

      if ($d->st_code == "S09" and $d->pc_no == 3)
        $scheduleid = 5;
      else
        $scheduleid = $ele_details->ScheduleID;

      $master = DB::table('pd_schedulemaster')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)
        ->where('const_type', $ele_details->CONST_TYPE)->where('schedule_id', $scheduleid)->first();

      $lists = DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('scheduleid', $scheduleid)->get();

      $filter_election = [
        'st_code' => $ele_details->ST_CODE, 'const_no' => $ele_details->CONST_NO, 'scheduleid' => $scheduleid
      ];
      $total_round = $this->pollday->get_total_roundnew($filter_election);
      $total_electors = $this->pollday->get_elector_total($filter_election);

      if (isset($total_round)) {
        if ($total_electors->gen_t != 0) $totalturnout_per = (($total_round->total / $total_electors->gen_t) * 100);
        else  $totalturnout_per = 0;
        if ($total_electors->gen_m != 0) $maleturnout_per = (($total_round->total_voter_male / $total_electors->gen_m) * 100);
        else  $maleturnout_per = 0;
        if ($total_electors->gen_f != 0) $femaleturnout_per = (($total_round->total_voter_female / $total_electors->gen_f) * 100);
        else  $femaleturnout_per = 0;
        if ($total_electors->gen_o != 0) $othersturnout_per = (($total_round->total_voter_other / $total_electors->gen_o) * 100);
        else  $othersturnout_per = 0;
      } else {
        $totalturnout_per = 0;
        $maleturnout_per = 0;
        $femaleturnout_per = 0;
        $othersturnout_per = 0;
      }
      $totalturnout_per = round($totalturnout_per, 2);
      $maleturnout_per = round($maleturnout_per, 2);
      $femaleturnout_per = round($femaleturnout_per, 2);
      $othersturnout_per = round($othersturnout_per, 2);

      return view('admin.pc.ro.voting.list-schedule', ['user_data' => $d, 'ele_details' => $ele_details, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'lists' => $lists, 'droplist' => $droplist, 'totalturnout_per' => $totalturnout_per, 'maleturnout_per' => $maleturnout_per, 'femaleturnout_per' => $femaleturnout_per, 'othersturnout_per' => $othersturnout_per, 'master' => $master]);
    } else {
      return redirect('/officer-login');
    }
  }  // end  function   
  public function schedule_entry($round = '', Request $request)
  {

    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $cyear = date("Y");
      $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
      $round = base64_decode($round);
      $round1 = base64_encode($round);
      $droplist = $this->commonModel->selectAll('pd_schedule_round', 'id', 'ASC');

      $lists = DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $d->ac_no)->first();

      $master = DB::table('pd_schedulemaster')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $d->ac_no)
        ->where('const_type', $ele_details->CONST_TYPE)->first();
      $ele = getcdacelectorsdetails($ele_details->ST_CODE, $d->ac_no);
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


      if ($round != '') {
        $mr = $round . "_voter_male";
        $fr = $round . "_voter_female";
        $or = $round . "_voter_other";
        $tr = $round . "_voter_total";
        $m = $lists->$mr;
        $f = $lists->$fr;
        $o = $lists->$or;
        $t = $lists->$tr;
      } else {
        $m = '';
        $f = '';
        $o = '';
        $t = '';
      }
      return view('admin.pc.ro.voting.aro-schedule-entry', ['user_data' => $d, 'ele_details' => $ele_details, 'lists' => $lists, 'droplist' => $droplist, 'round' => $round, 'm' => $m, 'f' => $f, 'o' => $o, 't' => $t, 'ele' => $ele, 'totalturnout_per' => $totalturnout_per, 'maleturnout_per' => $maleturnout_per, 'femaleturnout_per' => $femaleturnout_per, 'othersturnout_per' => $othersturnout_per, 'master' => $master, 'round' => $round, 'round1' => $round1]);
    } else {
      return redirect('/officer-login');
    }
  }  // end   function



  public function aro_schedule_entry(Request $request)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      // $cyear=date("Y");
      $cyear = '2019';
      $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
      $validator = Validator::make(
        $request->all(),
        [
          'round' => 'required',
          'malevoter' => 'required|numeric|integer|digits_between:0,15000000',
          'femalevoter' => 'required|numeric|integer|digits_between:0,15000000',
          'othervoter' => 'required|numeric|integer|digits_between:0,15000000',
          'totalvoter' => 'required|numeric|integer|digits_between:0,15000000',
        ],
        [
          'round.required' => 'Please select round',
          'malevoter.numeric' => 'Please enter numeric value',
          'malevoter.required' => 'Please enter voter',
          'femalevoter.numeric' => 'Please enter numeric value',
          'femalevoter.required' => 'Please enter voter',
          'othervoter.numeric' => 'Please enter numeric value',
          'othervoter.required' => 'Please enter voter',
          'totalvoter.numeric' => 'Please enter numeric value',
          'totalvoter.required' => 'Please enter voter',

          'malevoter.integer' => 'Please enter numeric value',
          'femalevoter.integer' => 'Please enter numeric value',
          'othervoter.integer' => 'Please enter numeric value',
          'totalvoter.integer' => 'Please enter numeric value',
          'malevoter.digits_between' => 'Please enter valid value',
          'femalevoter.digits_between' => 'Please enter valid value',
          'othervoter.digits_between' => 'Please enter valid value',
          'totalvoter.digits_between' => 'Please enter valid value',
        ]
      );

      if ($validator->fails()) {
        return Redirect::back()->withInput($request->all())->withErrors($validator);
      }
      $ele = getcdacelectorsdetails($ele_details->ST_CODE, $d->ac_no);
      if (isset($ele)) {
        $elector_total = $ele->electors_total;
        $elector_male = $ele->electors_male;
        $elector_female = $ele->electors_female;
        $elector_others = $ele->electors_other;
      } else {
        $elector_total = 0;
        $elector_male = 0;
        $elector_female = 0;
        $elector_others = 0;
      }

      $id =  $request->input('id');
      $round = $this->xssClean->clean_input($request->input('round'));
      $newround = $this->xssClean->clean_input($request->input('newround'));
      $malevoter = $this->xssClean->clean_input($request->input('malevoter'));
      $femalevoter = $this->xssClean->clean_input($request->input('femalevoter'));
      $othervoter = $this->xssClean->clean_input($request->input('othervoter'));
      $totalvoter = $this->xssClean->clean_input($request->input('totalvoter'));

      $total1 = $malevoter + $femalevoter + $othervoter;

      if ($total1 != $totalvoter) {
        Session::flash('error_mes', 'male, frmale, others total sum mismatch . ');
        return Redirect::back()->withInput($request->all())->withErrors($validator);
        return Redirect::to('aro/voting/schedule-entry/' . $round);
      }
      if ($round != $newround) {

        Session::flash('error_mes', 'Voter turnout details mismatch. ');
        return Redirect::back()->withInput($request->all())->withErrors($validator);
        return Redirect::to('aro/voting/schedule-entry/' . $round);
      }

      if ($malevoter > $elector_male) {

        Session::flash('error_mes', 'Voter turnout details mismatch. ');
        return Redirect::back()->withInput($request->all())->withErrors($validator);
        return Redirect::to('aro/voting/schedule-entry/' . $round);
      }
      if ($femalevoter > $elector_female) {

        Session::flash('error_mes', 'Voter turnout details mismatch. ');
        return Redirect::back()->withInput($request->all())->withErrors($validator);
        return Redirect::to('aro/voting/schedule-entry/' . $round);
      }
      if ($othervoter > $elector_others) {

        Session::flash('error_mes', 'Voter turnout details mismatch. ');
        return Redirect::back()->withInput($request->all())->withErrors($validator);
        return Redirect::to('aro/voting/schedule-entry/' . $round);
      }
      if ($elector_total < $totalvoter) {

        Session::flash('error_mes', 'Voter more than Currents Electors. ');
        return Redirect::back()->withInput($request->all())->withErrors($validator);
        return Redirect::to('aro/voting/schedule-entry/' . $round);
      }
      $m = $round . "_voter_male";
      $f = $round . "_voter_female";
      $o = $round . "_voter_other";
      $t = $round . "_voter_total";

      $st = array($m => $malevoter, $f => $femalevoter, $o => $othervoter, $t => $totalvoter, 'updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => $d->officername, 'total_male' => $malevoter, 'total_female' => $femalevoter, 'total_other' => $othervoter, 'total' => $totalvoter);

      DB::beginTransaction();
      try {
        \App\models\Admin\ScheduleDetailLogModel::clone_record($id);
        $i = DB::table('pd_scheduledetail')->where('id', $id)->update($st);
        DB::commit();
      } catch (\Exception $e) {
        DB::rollback();
        Session::flash('error_mes', 'Please try again.');
        return Redirect::back();
      }

      Session::flash('success_mes', 'Voter Turnout successfully added');
      return Redirect::to('aro/voting/schedule-entry');
    } else {
      return redirect('/officer-login');
    }
  }
  public function estimate_turnout_entry()
  {

    //dd('hiii');
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);

      $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');

      $seched = getschedulebyid($ele_details->ScheduleID);

      $filter = [
        'st_code'       => $ele_details->ST_CODE,
        'ac_no'         => $user->ac_no,
        'election_id'   => $ele_details->ELECTION_ID,
        'const_type'    => $ele_details->CONST_TYPE,
        'phase_no'      => $ele_details->PHASE_NO,
        'pc_no'         => $user->pc_no,
      ];
      $exempted = $this->turnout->check_turnout_exempted($filter);
      //dd($exempted);
      if ($exempted == 0) {
        //$exempted=$this->turnout->check_turnout_entry_enable($filter);
      }

      $lists = DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $d->ac_no)->first();
      $master = DB::table('pd_schedulemaster')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $d->ac_no)
        ->where('const_type', $ele_details->CONST_TYPE)->first();

      // dd($lists);

      $total_total  = 0;

      $totalturnout_per = 0;

      if (isset($lists) && $lists) {
        $totalturnout_per = round(($lists->est_turnout_total), 2);
      }
      $data =  ['user_data' => $d, 'ele_details' => $ele_details, 'lists' => $lists, 'totalturnout_per' => $totalturnout_per, 'seched' => $seched, 'exempted' => $exempted];
      $data['timestamp'] = date('Y-m-d H:i:s');
      return view('admin.pc.ro.voting.estimate-turnout-entry', $data);
    } else {
      return redirect('/officer-login');
    }
  }

  public function estimated_entry(Request $request)
  {
    try {
      if (Auth::check()) {
        $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
        $lists1 = DB::table('pd_scheduledetail')->select('est_turnout_round1', 'est_turnout_round2', 'est_turnout_round3', 'est_turnout_round4', 'est_turnout_round5', 'est_turnout_total')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $d->ac_no)->first();
        $allow = false;
        if($request->has('ceorequest') && $request->input('ceorequest') == 1){
          $allow = true;
        }else if($request->has('ecirequest') && $request->input('ecirequest') == 1){
          $allow = true;
        }else if($request->input('roundno') == 1 && (int)date("H") == 9 && (int)date("i") >= 0 && (int)date("i") <= 30){
          $allow = true;
        }else if($request->input('roundno') == 2 && (int)date("H") == 11 && (int)date("i") >= 0 && (int)date("i") <= 30){
          $allow = true;
        }else if($request->input('roundno') == 3 && (int)date("H") == 13 && (int)date("i") >= 0 && (int)date("i") <= 30){
          $allow = true;
        }else if($request->input('roundno') == 4 && (int)date("H") == 15 && (int)date("i") >= 0 && (int)date("i") <= 30){
          $allow = true;
        }else if($request->input('roundno') == 5 && (int)date("H") == 17 && (int)date("i") >= 0 && (int)date("i") <= 30){
          $allow = true;
        }
        if ($request->input('roundno') == 1) {
          if($allow){
            $this->updateVtRoundOne($request, $lists1, $ele_details, $d);
          }else{
            Session::flash('error_mes', 'Time Slot is expired for this entry.');
            return Redirect::back()->withInput($request->all())->withErrors(['error'=>'Time Slot is expired for this entry.']);
          }
        } elseif ($request->input('roundno') == 2) {
          if($allow){
            $this->updateVtRoundTwo($request, $lists1, $ele_details, $d);
          }else{
            Session::flash('error_mes', 'Time Slot is expired for this entry.');
            return Redirect::back()->withInput($request->all())->withErrors(['error'=>'Time Slot is expired for this entry.']);
          }
        } elseif ($request->input('roundno') == 3) {
          if($allow){ 
            $this->updateVtRoundThree($request, $lists1, $ele_details, $d);
          }else{
            Session::flash('error_mes', 'Time Slot is expired for this entry.');
            return Redirect::back()->withInput($request->all())->withErrors(['error'=>'Time Slot is expired for this entry.']);
          }
        } elseif ($request->input('roundno') == 4) {
          if($allow){ 
            $this->updateVtRoundFour($request, $lists1, $ele_details, $d);
          }else{
            Session::flash('error_mes', 'Time Slot is expired for this entry.');
            return Redirect::back()->withInput($request->all())->withErrors(['error'=>'Time Slot is expired for this entry.']);
          }
        } elseif ($request->input('roundno') == 5) {
          if($allow){ 
            $this->updateVtRoundFive($request, $lists1, $ele_details, $d); 
          }else{
            Session::flash('error_mes', 'Time Slot is expired for this entry.');
            return Redirect::back()->withInput($request->all())->withErrors(['error'=>'Time Slot is expired for this entry.']);
          }
        } elseif ($request->input('roundno') == 6) {
          $this->updateVtRoundSix($request, $lists1, $ele_details, $d);
        }

        $lists = DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->first();
        $ele = getcdacelectorsdetails($ele_details->ST_CODE, $d->ac_no);
        $electors_total = 0;
        $est_voter = 0;
        if (isset($ele)) {
          $electors_total = $ele->electors_total;
        }
        $latest_updated = 0;
        if (isset($lists) and $lists->est_turnout_round1 != 0)
          $latest_updated = $lists->est_turnout_round1;
        if (isset($lists) and $lists->est_turnout_round2 != 0)
          $latest_updated = $lists->est_turnout_round2;
        if (isset($lists) and $lists->est_turnout_round3 != 0)
          $latest_updated = $lists->est_turnout_round3;
        if (isset($lists) and $lists->est_turnout_round4 != 0)
          $latest_updated = $lists->est_turnout_round4;
        if (isset($lists) and $lists->est_turnout_round5 != 0)
          $latest_updated = $lists->est_turnout_round5;
        if (isset($lists) and $lists->close_of_poll != 0)
          $latest_updated = $lists->close_of_poll;

        $est_voter = round(($electors_total * $latest_updated / 100), 0);

        $st1 = array(
          'updated_at' => date("Y-m-d H:i:s"),
          'added_update_at' => date("Y-m-d"),
          'updated_by' => $d->officername,
          'est_turnout_total' => $latest_updated,
          'electors_total' => $electors_total,
          'est_voters' => $est_voter
        );
        $id =  $request->input('id');
        DB::table('pd_scheduledetail')->where('id', $id)->update($st1);

        if ((!empty($request->input('ceorequest')) && $request->input('ceorequest') == 1) || (!empty($request->input('ecirequest')) && $request->input('ecirequest') == 1)) {
          DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update($st1);
        }
        Session::flash('success_mes', 'Voter Turnout successfully added');
        //* Rocky Code Ended For Round 6 *//
        if (config("public_config.vt_log")) {
          $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
          $ErrorMessage['applicationType'] = 'WebApp';
          $ErrorMessage['Module'] = 'ENCORE';
          $ErrorMessage['TransectionType'] = 'VoterTurnout';
          $modify = (!empty($request->input('ecirequest')) && $request->input('ecirequest') == 1) ? 'Modify' : 'Added';
          $ErrorMessage['TransectionAction'] = 'Estimated Turnout Entry ' . $modify;
          $ErrorMessage['TransectionStatus'] = 'SUCCESS';
          $ErrorMessage['LogDescription'] = 'Estimated Turnout Entry is ' . $modify . ' for round ' . $request->input('roundno') . ' AC NO ' . $d->ac_no . ' PC NO ' . $ele_details->CONST_NO . ' ST CODE ' . $ele_details->ST_CODE;
          LogNotification::LogInfo($ErrorMessage);
        }

        return Redirect::to('aro/voting/estimate-turnout-entry');
      } else {
        return redirect('/officer-login');
      }
    } catch (\Throwable $th) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Estimated Turnout Entry ';
        $ErrorMessage['TransectionStatus'] = 'FAILED';
        $modify = (!empty($request->input('ecirequest')) && $request->input('ecirequest') == 1) ? 'Modify' : 'Added';
        $ErrorMessage['LogDescription'] = 'Estimated Turnout Entry is ' . $modify . ' for round ' . $request->input('roundno') . " Failed";
        LogNotification::LogInfo($ErrorMessage);
        Session::flash('success_mes', 'Voter Turnout failed request');
        return Redirect::to('aro/voting/estimate-turnout-entry');
      }
    }
  }

  public function updateVtRoundOne($request, $lists1, $ele_details, $d){
    $validator = Validator::make(
      $request->all(),
      [
        'est_turnout_round1'          => 'required|numeric|between:0,99.99|required_with:est_turnout_round1_confrim|same:est_turnout_round1_confrim',
        'est_turnout_round1_confrim'  => 'required|numeric|between:0,99.99'
      ],
      [
        'est_turnout_round1.numeric' => 'Please enter numeric value',
        'est_turnout_round1.required' => 'Please enter voter',
        'est_turnout_round1.between' => 'Please enter valid value (99.99)',
        'est_turnout_round1_confrim.between' => 'Please enter valid value (99.99)',
        'est_turnout_round1.required_with' => 'Please enter valid value (99.99) Confirmation Estimated Poll Turnout%',
        'est_turnout_round1.same'   => 'The estimated Percentage entered does not match with the confirmation percentage entered.'
      ]
    );

    if ($validator->fails()) {
      return Redirect::back()->withInput($request->all())->withErrors($validator);
    }

    $id =  $request->input('id');
    $est_turnout_round1 = $this->xssClean->clean_input($request->input('est_turnout_round1'));
    if ($est_turnout_round1 < $lists1->est_turnout_total) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be less than the previous Percentage');
      return Redirect::to('aro/voting/estimate-turnout-entry');
    }
    $st = array('updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => $d->officername, 'est_turnout_round1' => $est_turnout_round1, 'update_device_round1' => 'web', 'update_at_round1' => date("Y-m-d H:i:s"), 'est_turnout_total' => $est_turnout_round1);
    /****************************** Publish data on CEO request *******************************************/
    if (!empty($request->input('ceorequest')) && $request->input('ceorequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'missed_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round1,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    /****************************** Publish data on ECI request *******************************************/

    /* Rocky Code Start here for round 1 */

    if (!empty($request->input('ecirequest')) && $request->input('ecirequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'modification_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round1,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }

    /* Rocky Code Ended Here for round 1 */

    $i = DB::table('pd_scheduledetail')->where('id', $id)->update($st);
    Session::flash('success_mes', 'Voter Turnout successfully added');
    //return Redirect::to('aro/voting/estimate-turnout-entry');
  }

  public function updateVtRoundTwo($request, $lists1, $ele_details, $d){
    $validator = Validator::make(
      $request->all(),
      [
        'est_turnout_round2'          => 'required|numeric|between:0,99.99|required_with:est_turnout_round2_confrim|same:est_turnout_round2_confrim',
        'est_turnout_round2_confrim'  => 'required|numeric|between:0,99.99'
      ],
      [
        'est_turnout_round2.numeric' => 'Please enter numeric value',
        'est_turnout_round2.required' => 'Please enter voter',
        'est_turnout_round2.between' => 'Please enter valid value (99.99)',
        'est_turnout_round2_confrim.between' => 'Please enter valid value (99.99)',
        'est_turnout_round2.required_with' => 'Please enter valid value (99.99) Confirmation Estimated Poll Turnout%',
        'est_turnout_round2.same'   => 'The estimated Percentage entered does not match with the confirmation percentage entered.'
      ]
    );

    if ($validator->fails()) {
      return Redirect::back()->withInput($request->all())->withErrors($validator);
    }
    $id =  $request->input('id');
    $est_turnout_round2 = $this->xssClean->clean_input($request->input('est_turnout_round2'));
    if ($est_turnout_round2 < $lists1->est_turnout_round1) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be less than the previous round Percentage');
      return Redirect::to('aro/voting/estimate-turnout-entry');
    }
    $st = array('updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => $d->officername, 'est_turnout_round2' => $est_turnout_round2, 'update_device_round2' => 'web', 'update_at_round2' => date("Y-m-d H:i:s"), 'est_turnout_total' => $est_turnout_round2);

    DB::table('pd_scheduledetail')->where('id', $id)->update($st);
    /****************************** Publish data on CEO request *******************************************/
    if (!empty($request->input('ceorequest')) && $request->input('ceorequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'missed_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round2,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    /****************************** Publish data on ECI request *******************************************/

    /* Rocky Code Start here for round 2 */

    if (!empty($request->input('ecirequest')) && $request->input('ecirequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'modification_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round2,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }

    /* Rocky Code Ended Here for round 2 */

    Session::flash('success_mes', 'Voter Turnout successfully added');
    //return Redirect::to('aro/voting/estimate-turnout-entry');
  }

  public function updateVtRoundThree($request, $lists1, $ele_details, $d){
    $validator = Validator::make(
      $request->all(),
      [
        'est_turnout_round3'          => 'required|numeric|between:0,99.99|required_with:est_turnout_round3_confrim|same:est_turnout_round3_confrim',
        'est_turnout_round3_confrim'  => 'required|numeric|between:0,99.99'
      ],
      [
        'est_turnout_round3.numeric' => 'Please enter numeric value',
        'est_turnout_round3.required' => 'Please enter voter',
        'est_turnout_round3.between' => 'Please enter valid value (99.99)',
        'est_turnout_round3_confrim.between' => 'Please enter valid value (99.99)',
        'est_turnout_round3.required_with' => 'Please enter valid value (99.99) Confirmation Estimated Poll Turnout%',
        'est_turnout_round3.same'   => 'The estimated Percentage entered does not match with the confirmation percentage entered.'
      ]
    );

    if ($validator->fails()) {
      return Redirect::back()->withInput($request->all())->withErrors($validator);
    }

    $id =  $request->input('id');
    $est_turnout_round3 = $this->xssClean->clean_input($request->input('est_turnout_round3'));
    if (($est_turnout_round3 < $lists1->est_turnout_round2) || ($est_turnout_round3 < $lists1->est_turnout_round1)) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be less than the previous round Percentage');
      return Redirect::to('aro/voting/estimate-turnout-entry');
    }
    $st = array('updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => $d->officername, 'est_turnout_round3' => $est_turnout_round3, 'update_device_round3' => 'web', 'update_at_round3' => date("Y-m-d H:i:s"), 'est_turnout_total' => $est_turnout_round3);
    DB::table('pd_scheduledetail')->where('id', $id)->update($st);

    /****************************** Publish data on CEO request *******************************************/
    if (!empty($request->input('ceorequest')) && $request->input('ceorequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'missed_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round3,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }

    /****************************** Publish data on ECI request *******************************************/

    /* Rocky Code Start here for round 3 */

    if (!empty($request->input('ecirequest')) && $request->input('ecirequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'modification_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round3,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }

    /* Rocky Code Ended Here for round 3 */

    Session::flash('success_mes', 'Voter Turnout successfully added');
    //return Redirect::to('aro/voting/estimate-turnout-entry');
  }

  public function updateVtRoundFour($request, $lists1, $ele_details, $d){
    $validator = Validator::make(
      $request->all(),
      [
        'est_turnout_round4'          => 'required|numeric|between:0,99.99|required_with:est_turnout_round4_confrim|same:est_turnout_round4_confrim',
        'est_turnout_round4_confrim'  => 'required|numeric|between:0,99.99'
      ],
      [
        'est_turnout_round4.numeric' => 'Please enter numeric value',
        'est_turnout_round4.required' => 'Please enter voter',
        'est_turnout_round4.between' => 'Please enter valid value (99.99)',
        'est_turnout_round4_confrim.between' => 'Please enter valid value (99.99)',
        'est_turnout_round4.required_with' => 'Please enter valid value (99.99) Confirmation Estimated Poll Turnout%',
        'est_turnout_round4.same'   => 'The estimated Percentage entered does not match with the confirmation percentage entered.'
      ]
    );

    if ($validator->fails()) {
      return Redirect::back()->withInput($request->all())->withErrors($validator);
    }

    $id =  $request->input('id');
    $est_turnout_round4 = $this->xssClean->clean_input($request->input('est_turnout_round4'));
    if (($est_turnout_round4 < $lists1->est_turnout_round3) || ($est_turnout_round4 < $lists1->est_turnout_round2) || ($est_turnout_round4 < $lists1->est_turnout_round1)) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be less than the previous round Percentage');
      return Redirect::to('aro/voting/estimate-turnout-entry');
    }
    $st = array('updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => $d->officername, 'est_turnout_round4' => $est_turnout_round4, 'update_device_round4' => 'web', 'update_at_round4' => date("Y-m-d H:i:s"), 'est_turnout_total' => $est_turnout_round4);

    DB::table('pd_scheduledetail')->where('id', $id)->update($st);
    /****************************** Publish data on CEO request *******************************************/
    if (!empty($request->input('ceorequest')) && $request->input('ceorequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'missed_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round4,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }

    /****************************** Publish data on ECI request *******************************************/

    /* Rocky Code Start here for round 4 */

    if (!empty($request->input('ecirequest')) && $request->input('ecirequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'modification_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round4,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }

    /* Rocky Code Ended Here for round 4 */

    Session::flash('success_mes', 'Voter Turnout successfully added');
    // return Redirect::to('aro/voting/estimate-turnout-entry');
  }

  public function updateVtRoundFive($request, $lists1, $ele_details, $d){
    $validator = Validator::make(
      $request->all(),
      [
        'est_turnout_round5'          => 'required|numeric|between:0,99.99|required_with:est_turnout_round5_confrim|same:est_turnout_round5_confrim',
        'est_turnout_round5_confrim'  => 'required|numeric|between:0,99.99'
      ],
      [
        'est_turnout_round5.numeric' => 'Please enter numeric value',
        'est_turnout_round5.required' => 'Please enter voter',
        'est_turnout_round5.between' => 'Please enter valid value (99.99)',
        'est_turnout_round5_confrim.between' => 'Please enter valid value (99.99)',
        'est_turnout_round5.required_with' => 'Please enter valid value (99.99) Confirmation Estimated Poll Turnout%',
        'est_turnout_round5.same'   => 'The estimated Percentage entered does not match with the confirmation percentage entered.'
      ]
    );

    if ($validator->fails()) {
      return Redirect::back()->withInput($request->all())->withErrors($validator);
    }

    $id =  $request->input('id');
    $est_turnout_round5 = $this->xssClean->clean_input($request->input('est_turnout_round5'));
    if (($est_turnout_round5 < $lists1->est_turnout_round4) || ($est_turnout_round5 < $lists1->est_turnout_round3) || ($est_turnout_round5 < $lists1->est_turnout_round2) || ($est_turnout_round5 < $lists1->est_turnout_round1)) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be less than the previous round Percentage');
      return Redirect::to('aro/voting/estimate-turnout-entry');
    }
    $st = array('updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => $d->officername, 'est_turnout_round5' => $est_turnout_round5, 'update_device_round5' => 'web', 'update_at_round5' => date("Y-m-d H:i:s"), 'est_turnout_total' => $est_turnout_round5);
    DB::table('pd_scheduledetail')->where('id', $id)->update($st);

    /****************************** Publish data on CEO request *******************************************/
    if (!empty($request->input('ceorequest')) && $request->input('ceorequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'missed_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round5,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }

    /****************************** Publish data on ECI request *******************************************/

    /* Rocky Code Start here for round 5 */

    if (!empty($request->input('ecirequest')) && $request->input('ecirequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'modification_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round5,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }

    /* Rocky Code Ended Here for round 5 */

    Session::flash('success_mes', 'Voter Turnout successfully added');
  }

  public function updateVtRoundSix($request, $lists1, $ele_details, $d){
    $validator = Validator::make(
      $request->all(),
      [
        'est_turnout_end'             => 'required|numeric|between:0,99.99|required_with:est_turnout_end_confrim|same:est_turnout_end_confrim',
        'est_turnout_end_confrim'     => 'required|numeric|between:0,99.99'
      ],
      [
        'est_turnout_end.numeric' => 'Please enter numeric value',
        'est_turnout_end.required' => 'Please enter voter',
        'est_turnout_end.between' => 'Please enter valid value (99.99)',
        'est_turnout_end_confrim.between' => 'Please enter valid value (99.99)',
        'est_turnout_end.required_with' => 'Please enter valid value (99.99) Confirmation Estimated Poll Turnout%',
        'est_turnout_end.same'   => 'The estimated Percentage entered does not match with the confirmation percentage entered.'
      ]
    );

    if ($validator->fails()) {
      return Redirect::back()->withInput($request->all())->withErrors($validator);
    }

    $id =  $request->input('id');
    $est_turnout_end = $this->xssClean->clean_input($request->input('est_turnout_end'));
    if (($est_turnout_end < $lists1->est_turnout_round5) || ($est_turnout_end < $lists1->est_turnout_round4) || ($est_turnout_end < $lists1->est_turnout_round3) || ($est_turnout_end < $lists1->est_turnout_round2) || ($est_turnout_end < $lists1->est_turnout_round1)) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be less than the previous round Percentage');
      return Redirect::to('aro/voting/estimate-turnout-entry');
    }
    $st = array(
      'updated_at' => date("Y-m-d H:i:s"),
      'added_update_at' => date("Y-m-d"),
      'updated_by' => $d->officername,
      'close_of_poll' => $est_turnout_end,
      'updated_device_close_of_poll' => 'web',
      'updated_at_close_of_poll' => date("Y-m-d H:i:s"),
      'est_turnout_total' => $est_turnout_end,
      'est_poll_close' => 1
    );
    DB::table('pd_scheduledetail')->where('id', $id)->update($st);

    /****************************** Publish data on CEO request *******************************************/
    //if(!empty($request->input('ceorequest')) && $request->input('ceorequest')==1){
    DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update($st);
    $upd_fld = 'missed_status_round' . $request->input('roundno');
    DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);

    //Create log upon publish

    $lists1 = DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->first();
    $latest_updated1 = 0;
    if (isset($lists1) and $lists1->est_turnout_round1 != 0)
      $latest_updated1 = $lists1->est_turnout_round1;
    if (isset($lists1) and $lists1->est_turnout_round2 != 0)
      $latest_updated1 = $lists1->est_turnout_round2;
    if (isset($lists1) and $lists1->est_turnout_round3 != 0)
      $latest_updated1 = $lists1->est_turnout_round3;
    if (isset($lists1) and $lists1->est_turnout_round4 != 0)
      $latest_updated1 = $lists1->est_turnout_round4;
    if (isset($lists1) and $lists1->est_turnout_round5 != 0)
      $latest_updated1 = $lists1->est_turnout_round5;
    if (isset($lists1) and $lists1->close_of_poll != 0)
      $latest_updated1 = $lists1->close_of_poll;

    DB::table('pd_voter_turnout_request_log')->insert([
      'request_from' => $ele_details->ST_CODE,
      'ac_no' => $ele_details->CONST_NO,
      'phase_no' => $ele_details->PHASE_NO,
      'round_no' => $request->input('roundno'),
      'updated_turnout' => $latest_updated1,
      'updated_by' => $d->officername,
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
    //}

    if (!empty($request->input('ecirequest')) && $request->input('ecirequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'modification_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('ac_no', $d->ac_no)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $latest_updated1,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
  }

  public function defreeze_round($id)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $rid = base64_decode($id);

      $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
      $st = array('updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => $d->officername, 'est_poll_close' => 0);

      $i = DB::table('pd_scheduledetail')->where('id', $rid)->update($st);
      Session::flash('success_mes', 'Defreeze successfully');
      return Redirect::to('aro/voting/estimate-turnout-entry');
    } else {
      return redirect('/officer-login');
    }
  }
  public function estimated_turnout()
  {
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
      $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
      if ($check_finalize == '') {
        $cand_finalize_ceo = 0;
        $cand_finalize_ro = 0;
      } else {
        $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
        $cand_finalize_ro = $check_finalize->finalized_ac;
      }
      $droplist = $this->commonModel->selectAll('pd_schedule_round', 'id', 'ASC');
      $seched = getschedulebyid($ele_details->ScheduleID);
      if ($d->st_code == "S09" and $d->pc_no == 3)
        $scheduleid = 5;
      else
        $scheduleid = $ele_details->ScheduleID;


      $master = DB::table('pd_schedulemaster')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)
        ->where('const_type', $ele_details->CONST_TYPE)->where('schedule_id', $scheduleid)->first();


      $lists = DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('scheduleid', $scheduleid)->get();

      $count = $lists->count();

      $filter_election1 = [
        'state' => $ele_details->ST_CODE, 'pc_no' => $ele_details->CONST_NO, 'phase' => $ele_details->ScheduleID, 'group_by' => 'ac_no', 'order_by' => 'ac_no'
      ];
      $filter_election = [
        'st_code' => $ele_details->ST_CODE, 'const_no' => $ele_details->CONST_NO
      ];
      $result1 = 0;
      $result = $this->pollday->get_reports($filter_election1);
      $result1 = $this->pollday->get_average_sum([
        'state' => $ele_details->ST_CODE,
        'pc_no' => $ele_details->CONST_NO,
        'phase' => $scheduleid,
        'group_by' => 'pc_no'
      ]);

      $total_round = $this->pollday->est_pcwiseturnout_total($filter_election);

      $totalturnout_per = round($result1, 2);


      return view('admin.pc.ro.voting.estimated-turnout', ['user_data' => $d, 'ele_details' => $ele_details, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'lists' => $lists, 'droplist' => $droplist, 'totalturnout_per' => $totalturnout_per, 'master' => $master, 'count' => $count, 'result' => $result]);
    } else {
      return redirect('/officer-login');
    }
  }
  public function estimated_turnout_change(Request $request)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');


      $validator = Validator::make(
        $request->all(),
        [
          'est_turnout' => 'required|numeric|between:0,99.99',
          'rounds' => 'required',
        ],
        [
          'est_turnout.numeric' => 'Please enter numeric value',
          'est_turnout.required' => 'Please enter voter',
          'est_turnout.between' => 'Please enter valid value (99.99)',
          'rounds' => 'Please select rounds',
        ]
      );

      if ($validator->fails()) {
        return Redirect::back()->withInput($request->all())->withErrors($validator);
      }

      $id =  $request->input('id');
      $acno =  $request->input('acno');
      $est_turnout = $this->xssClean->clean_input($request->input('est_turnout'));
      $rounds = $this->xssClean->clean_input($request->input('rounds'));


      if ($rounds == 1)
        $st = array('updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => $d->officername, 'est_turnout_round1' => $est_turnout, 'update_device_round1' => 'web', 'update_at_round1' => date("Y-m-d H:i:s"), 'est_turnout_total' => $est_turnout);
      elseif ($rounds == 2)
        $st = array('updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => $d->officername, 'est_turnout_round2' => $est_turnout, 'update_device_round2' => 'web', 'update_at_round2' => date("Y-m-d H:i:s"), 'est_turnout_total' => $est_turnout);
      elseif ($rounds == 3)
        $st = array('updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => $d->officername, 'est_turnout_round3' => $est_turnout, 'update_device_round3' => 'web', 'update_at_round3' => date("Y-m-d H:i:s"), 'est_turnout_total' => $est_turnout);
      elseif ($rounds == 4)
        $st = array('updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => $d->officername, 'est_turnout_round4' => $est_turnout, 'update_device_round4' => 'web', 'update_at_round4' => date("Y-m-d H:i:s"), 'est_turnout_total' => $est_turnout);
      elseif ($rounds == 5)
        $st = array('updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => $d->officername, 'est_turnout_round5' => $est_turnout, 'update_device_round5' => 'web', 'update_at_round5' => date("Y-m-d H:i:s"), 'est_turnout_total' => $est_turnout);
      elseif ($rounds == 6)
        $st = array('updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => $d->officername, 'close_of_poll' => $est_turnout, 'updated_device_close_of_poll' => 'web', 'updated_at_close_of_poll' => date("Y-m-d H:i:s"), 'est_turnout_total' => $est_turnout, 'est_poll_close' => 1);
      $i = DB::table('pd_scheduledetail')->where('id', $id)->update($st);



      $lists = DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('id', $id)->first();
      if (!isset($lists)) {
        $lists = DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $acno)->first();
      }
      $ele = getcdacelectorsdetails($ele_details->ST_CODE, $acno);
      $electors_total = 0;
      $est_voter = 0;
      if (isset($ele)) {
        $electors_total = $ele->electors_total;
      }


      if (isset($lists) and $lists->est_turnout_round1 != 0)
        $latest_updated = $lists->est_turnout_round1;
      if (isset($lists) and $lists->est_turnout_round2 != 0)
        $latest_updated = $lists->est_turnout_round2;
      if (isset($lists) and $lists->est_turnout_round3 != 0)
        $latest_updated = $lists->est_turnout_round3;
      if (isset($lists) and $lists->est_turnout_round4 != 0)
        $latest_updated = $lists->est_turnout_round4;
      if (isset($lists) and $lists->est_turnout_round5 != 0)
        $latest_updated = $lists->est_turnout_round5;
      if (isset($lists) and $lists->close_of_poll != 0)
        $latest_updated = $lists->close_of_poll;

      $est_voter = round(($electors_total * $latest_updated / 100), 0);
      $st1 = array('updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => $d->officername, 'est_turnout_total' => $latest_updated, 'electors_total' => $electors_total, 'est_voters' => $est_voter);

      $i = DB::table('pd_scheduledetail')->where('id', $id)->update($st1);
      Session::flash('success_mes', 'Voter Turnout successfully added');
      return Redirect::to('ropc/voting/estimated-turnout');
    } else {
      return redirect('/officer-login');
    }
  }
  public function end_of_poll_change(Request $request)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');

      $validator = Validator::make(
        $request->all(),
        [
          'voters_male' => 'required|numeric|integer|digits_between:0,15000000',
          'voters_female' => 'required|numeric|integer|digits_between:0,15000000',
          'voters_others' => 'required|numeric|integer|digits_between:0,15000000',
          'voters_total' => 'required|numeric|integer|digits_between:0,15000000',
        ],
        [

          'voters_male.numeric' => 'Please enter numeric value',
          'voters_female.numeric' => 'Please enter numeric value',
          'femalevoter.required' => 'Please enter voter',
          'voters_others.numeric' => 'Please enter numeric value',
          'voters_total.required' => 'Please enter voter',


          'voters_male.integer' => 'Please enter numeric value',
          'voters_female.integer' => 'Please enter numeric value',
          'voters_others.integer' => 'Please enter numeric value',
          'voters_total.integer' => 'Please enter numeric value',
          'voters_male.digits_between' => 'Please enter valid value',
          'voters_female.digits_between' => 'Please enter valid value',
          'voters_others.digits_between' => 'Please enter valid value',
          'voters_total.digits_between' => 'Please enter valid value',
        ]
      );

      if ($validator->fails()) {
        return Redirect::back()->withInput($request->all())->withErrors($validator);
      }
      $id =  $request->input('id');
      $acno = $this->xssClean->clean_input($request->input('acno'));
      $pcno = $this->xssClean->clean_input($request->input('pcno'));
      $voters_male = $this->xssClean->clean_input($request->input('voters_male'));
      $voters_female = $this->xssClean->clean_input($request->input('voters_female'));
      $voters_others = $this->xssClean->clean_input($request->input('voters_others'));
      $voters_total = $this->xssClean->clean_input($request->input('voters_total'));
      $ele = getcdacelectorsdetails($ele_details->ST_CODE, $acno);

      if (isset($ele)) {
        $elector_total = $ele->electors_total;
        $elector_male = $ele->electors_male;
        $elector_female = $ele->electors_female;
        $elector_others = $ele->electors_other;
      } else {
        $elector_total = 0;
        $elector_male = 0;
        $elector_female = 0;
        $elector_others = 0;
      }



      $total1 = $voters_male + $voters_female + $voters_others;

      if ($total1 != $voters_total) {
        Session::flash('error_mes', 'male, frmale, others total sum mismatch .1 ');
        return Redirect::back()->withInput($request->all())->withErrors($validator);
        return Redirect::to('ropc/voting/list-schedule');
      }

      if ($voters_male > $elector_male) {

        Session::flash('error_mes', 'Voter turnout details mismatch. 2');
        return Redirect::back()->withInput($request->all())->withErrors($validator);
        return Redirect::to('ropc/voting/list-schedule');
      }
      if ($voters_female > $elector_female) {

        Session::flash('error_mes', 'Voter turnout details mismatch. 3');
        return Redirect::back()->withInput($request->all())->withErrors($validator);
        return Redirect::to('ropc/voting/list-schedule');
      }
      if ($voters_others > $elector_others) {

        Session::flash('error_mes', 'Voter turnout details mismatch. 4');
        return Redirect::back()->withInput($request->all())->withErrors($validator);
        return Redirect::to('ropc/voting/list-schedule');
      }
      if ($elector_total < $voters_total) {

        Session::flash('error_mes', 'Voter more than Currents Electors. 5');
        return Redirect::back()->withInput($request->all())->withErrors($validator);
        return Redirect::to('ropc/voting/list-schedule');
      }

      $st = array('end_voter_male' => $voters_male, 'end_voter_female' => $voters_female, 'end_voter_other' => $voters_others, 'end_voter_total' => $voters_total, 'updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => $d->officername, 'total_male' => $voters_male, 'total_female' => $voters_female, 'total_other' => $voters_others, 'total' => $voters_total);

      DB::beginTransaction();
      try {
        // \App\models\Admin\ScheduleDetailLogModel::clone_record($id);
        $i = DB::table('pd_scheduledetail')->where('id', $id)->update($st);
        DB::commit();
      } catch (\Exception $e) {
        DB::rollback();
        Session::flash('error_mes', 'Please try again.');
        return Redirect::back();
      }


      Session::flash('success_mes', 'Voter Turnout successfully added');
      return Redirect::to('ropc/voting/list-schedule');
    } else {
      return redirect('/officer-login');
    }
  }

  public function finalize_turnout(Request $request)
  {
    $data  = [];
    $user = Auth::user();
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
    $data['user_data']      = $d;
    $data['ele_details']    = $ele_details;


    $sql_raw = "sum(electors_male) as electors_male,sum(electors_female) as electors_female,sum(electors_other) as electors_other,sum(electors_total) as electors_total,sum(voter_male) as voter_male,sum(voter_female) as voter_female,sum(voter_other) as voter_other,sum(voter_total) as voter_total";

    $sql = DB::table('polling_station as ps');

    $sql->selectRaw($sql_raw);


    //CHECKING STATE CODE
    if (!empty($d->st_code)) {
      $sql->where("ps.ST_CODE", $d->st_code);
    }

    //CHECKING AC CODE
    if (!empty($d->ac_no)) {
      $sql->where("ps.AC_NO", $d->ac_no);
    }


    //GROUP BY STARTS
    $sql->groupBy("ps.AC_NO");
    //GROUP BY ENDS

    $ac_data = $sql->first();

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

    $i = DB::table('pd_scheduledetail')->where('st_code', $d->st_code)->where('ac_no', $d->ac_no)->update($st);




    $update_fields = array(
      // 'ps_finalize_date'   => now(),
      //  'ps_finalize'        => 1,
    );

    $PsWiseDetailsWhere = ['st_code' => $d->st_code, 'ac_no' => $d->ac_no];

    // $Data = DB::table('polling_station')->where($PsWiseDetailsWhere)->update($update_fields);






    $total_voter = DB::table('pd_scheduledetail')->select('total', 'electors_total', 'est_turnout_total')
      ->where('st_code', $d->st_code)
      ->where('ac_no', $d->ac_no)
      ->first();
    //dd($total_voter);



    $ps_check = DB::table('polling_station')->where('voter_total', 0)
      ->where('st_code', $d->st_code)
      ->where('ac_no', $d->ac_no)
      ->first();


    if ($ps_check) {

      Session::flash('error_mes', 'Please Enter Voters in All Polling Stations!!!');
      return Redirect::back();
    }


    if (@$ac_data->electors_total != $total_voter->electors_total) {

      Session::flash('error_mes', 'Total Polling Station wise Electors entered did not matched with the ac wise Electors. Please verify.');
      return Redirect::back();
    }


    //dd($ac_data);

    if (@$ac_data->voter_total == 0) {
      Session::flash('error_mes', 'You Can not Published with 0 Voters!');
      return Redirect::back();
    }
    if (!empty($ac_data)) {

      //dd('aaaaaa');

      $est_voters = $total_voter->est_turnout_total;
      if ($est_voters > round(((@$ac_data->voter_total / @$ac_data->electors_total) * 100), 2)) {

        Session::flash('error_mes', 'Please verify, End of Poll percentage is showing less then estimated close of poll percentage.');
        return Redirect::back();
      }
    }

    $st = array(
      'updated_at' => date("Y-m-d h:m:s"),
      'added_update_at' => date("Y-m-d"),
      'updated_by' => $d->officername,
      'ceo_finalize' => '1'
    );
    $i = DB::table('pd_schedulemaster')
      ->where('st_code', $ele_details->ST_CODE)
      ->where('ac_no', $d->ac_no)
      ->where('election_id', $ele_details->ELECTION_ID)->update($st);
    $st1 = array(
      'updated_at' => date("Y-m-d h:m:s"),
      'updated_at_finalize' => date("Y-m-d h:m:s"),
      'added_update_at' => date("Y-m-d"),
      'updated_by' => $d->officername,
      'is_est_entry_allow' => '1',
      'est_poll_close' => 1,
      'est_voters' => @$total_voter->total,
      'est_turnout_total' => round(((@$ac_data->voter_total / @$ac_data->electors_total) * 100), 2),
      'close_of_poll' => round(((@$ac_data->voter_total / @$ac_data->electors_total) * 100), 2),
      'end_of_poll_finalize' => '1'
    );
    $i = DB::table('pd_scheduledetail')
      ->where('st_code', $ele_details->ST_CODE)
      ->where('ac_no', $d->ac_no)
      ->where('election_id', $ele_details->ELECTION_ID)->update($st1);
    Session::flash('success_mes', 'Turnout successfully Published');
    return Redirect::to('aro/voting/PsWiseDetails');
    //return Redirect::to('aro/voting/schedule-entry');
  }
}  // end class  //accepted_candidate  
