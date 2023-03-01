<?php 
namespace App\Http\Controllers\Admin\BoothAppRevamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session;
use App\commonModel;  
use App\models\Admin\BoothAppRevamp\{PollingStation, PollingStationOfficerModel, TblPollSummaryModel, VoterInfoModel, VoterInfoPollStatusModel, TblBoothUserModel, StateModel, AcModel, DistrictModel, JsonFile};
use App\Http\Requests\Admin\BoothAppRevamp\OfficerRequest;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

//current

class PollingController extends Controller {

  public $folder        = 'booth-app-revamp';
  public $view          = "admin.booth-app-revamp";
  public $action        = "booth-app-revamp";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $dist_no       = NULL;
  public $role_id       = 0;
  public $base          = 'roac';
  public $restricted_ps = [];
  public $no_allowed_po   = 0;
  public $no_allowed_blo  = 2;
  public $no_allowed_pro  = 3;
  public $allowed_acs = ['133'];
  public $allowed_dist_no = ['19'];
  public $allowed_st_code = ['S04'];
  public $cache = true;

  public function __construct(Request $request){
    $this->commonModel  = new commonModel();
    $this->middleware(function ($request, $next) {
      if(in_array(Auth::user()->st_code,$this->allowed_st_code) && in_array(Auth::user()->ac_no,$this->allowed_acs) && in_array(Auth::user()->dist_no,$this->allowed_dist_no)){

      }
      $default_values = Common::get_request_filter($request);
      $this->ac_no    = $default_values['ac_no'];
      $this->st_code  = $default_values['st_code'];
      $this->dist_no  = $default_values['dist_no'];
      $this->role_id  = $default_values['role_id'];
      $this->base     = $default_values['base'];


     
        if(!in_array(Auth::user()->role_id,['4','5','18','19','20'])){
			
          return redirect("/officer-login");
        }

        if(!in_array($this->st_code,['S04'])){
          return redirect("/officer-login");
        }



        if(in_array($this->role_id,['5','19','20','18'])){
          if(!in_array($this->dist_no,['19'])){
            return redirect("/officer-login");
          }
        }



        if(in_array($this->role_id,['19','20'])){
          if(!in_array($this->ac_no,['133'])){
            return redirect("/officer-login");
          }
        }
      
      if($this->st_code == 'S04'){
        $this->allowed_st_code = ['S04'];
        $this->allowed_acs = ['133'];
        $this->allowed_dist_no = ['19'];
      }
      return $next($request);
    });
  }

public function get_polling_station(Request $request){

    $data = [];
    $request_array = [];

//set title
    $title_array  = [];
    $data['heading_title'] = 'BLO/Polling Party List';

    if($this->st_code){
      $state_object = StateModel::get_state_by_code($this->st_code);
      if($state_object){
        $title_array[]  = "State: ".$state_object['ST_NAME'];
      }
    }
    if($this->ac_no){
      $ac_object = AcModel::get_ac(['state' => $this->st_code, 'ac_no' => $this->ac_no]);
      if($ac_object){
        $title_array[]  = "AC: ".$ac_object['ac_name'];
      }
    }
    $data['filter_buttons'] = $title_array;


    $data['filter']   = implode('&', array_merge($request_array));
//end set title

//buttons
    $data['buttons']    = [];
    $data['action']     = url($this->action.'/officer-list');
    $data['reset_otp_link']  = Common::generate_url('booth-app-revamp/reset_otp');

    $results                = [];
    $filter_election = [
      'st_code'   => $this->st_code,
      'ac_no'     => $this->ac_no,
      'paginate'  => false,
      'restricted_ps' => NULL
    ];

    $data['add_new_url'] = Common::generate_url($this->action.'/officer-list/add/0');

    $max_po           = [];
    $max_blo          = [];
    $data['results']  = [];
    $results    =   PollingStation::get_polling_stations($filter_election);
    $data['pag_results'] = $results;
    foreach ($results as $result) {
      $blo  = [];
      $pro  = [];
      $po   = [];
      $i    = 1;
      $j    = 1;
      $officers = PollingStationOfficerModel::get_officers(array_merge($filter_election,['ps_no' => $result['PS_NO']]));
      foreach($officers as $officer){

        if($officer['role_id'] == '33'){
          $blo[$j] = [
            'name'    => $officer['name'],
            'mobile'  => $officer['mobile_number'],
            'href'    => Common::generate_url($this->action.'/officer-list/add/'.encrypt_string($officer['id'])),
            'is_active' => ($officer['is_active'])?'Enable':'Disable'
          ];
          $j++;
        }
        if($officer['role_id'] == '35'){
          $pro[$i] = [
            'name'    => $officer['name'],
            'mobile'  => $officer['mobile_number'],
            'href'    => Common::generate_url($this->action.'/officer-list/add/'.encrypt_string($officer['id'])),
            'is_active' => ($officer['is_active'])?'Enable':'Disable'
          ];
          $i++;
        }
        if($officer['role_id'] == '34'){
          $po[$i] = [
            'name'    => $officer['name'],
            'mobile'  => $officer['mobile_number'],
            'href'    => Common::generate_url($this->action.'/officer-list/add/'.encrypt_string($officer['id'])),
            'is_active' => ($officer['is_active'])?'Enable':'Disable'
          ];
          $i++;
        }
      }
      $max_po[]   = count($po);
      $max_blo[]  = count($blo);
      $max_pro[]   = count($pro);
      $data['results'][] = [
        'ps_no'   => $result['PS_NO'],
        'ps_name' => $result['PS_NAME_EN'],
        'blo'     => $blo,
        'pro'     => $pro,
        'po'      => $po,
      ];
    }

$data['max_po']     =  $this->no_allowed_po;//max($max_po);
$data['max_blo']    =  $this->no_allowed_blo;//max($max_po);
$data['max_pro']    =  $this->no_allowed_pro;//max($max_po);

$data['user_data']  =  Auth::user();

$data['heading_title_with_all'] = $data['heading_title'];

if($request->has('is_excel')){
  if(isset($title_array) && count($title_array)>0){
    $data['heading_title'] .= "- ".implode(', ', $title_array);
  }
  return $data;
}

return view($this->view.'.officer-list', $data);

try{}catch(\Exception $e){
  return Redirect::to($this->base.'/dashboard');
}

}

public function add_officer($id = 0, Request $request){

  $data = [];
  $request_array = [];

  $data['st_code']  = NULL;
  $data['ac_no']    = NULL;
  if($request->has('st_code')){
    $data['st_code'] = base64_decode($request->st_code);
    $request_array[] = 'state='.$request->st_code;
  }
  if($request->has('ac_no')){
    $data['ac_no'] = base64_decode($request->ac_no);
    $request_array[] = 'ac_no='.$request->ac_no;
  }


  if($this->role_id == '20'){
    $data['st_code'] = $this->st_code;
    $data['ac_no'] = $this->ac_no;
  }

//set title
  $title_array  = [];
  $data['heading_title'] = 'BLO/Polling Party List';

  if($data['st_code']){
    $state_object = StateModel::get_state_by_code($data['st_code']);
    if($state_object){
      $title_array[]  = "State: ".$state_object['ST_NAME'];
    }
  }
  if($data['ac_no']){
    $ac_object = AcModel::get_ac(['state' => $data['st_code'], 'ac_no' => $data['ac_no']]);
    if($ac_object){
      $title_array[]  = "AC: ".$ac_object['ac_name'];
    }
  }
  $data['filter_buttons'] = $title_array;

  if(Auth::user()->role_id == '4'){
    $data['state']  = Auth::user()->st_code;
  }

  $states = StateModel::get_states();
  $data['states'] = [];
  foreach($states as $result){
    if(Auth::user()->role_id == '4' && $result->ST_CODE == Auth::user()->st_code){
      $data['states'][] = [
        'code' => base64_encode($result->ST_CODE),
        'name' => $result->ST_NAME,
      ];
    }

    if(Auth::user()->role_id == '7' || Auth::user()->role_id == '27'){
      $data['states'][] = [
        'code' => base64_encode($result->ST_CODE),
        'name' => $result->ST_NAME,
      ];
    }
  }

  $data['filter']   = implode('&', array_merge($request_array));
//end set title

//buttons
  $data['buttons']    = [];
  $data['buttons'][]    = [
    'href' => Common::generate_url($this->action.'/officer-list'),
    'name' => 'List of BLO/Polling Party',
    'target' => false,
  ];
  $data['action']         = Common::generate_url($this->action.'/officer-list/post');
  $filter_election = [
    'st_code'   => $data['st_code'],
    'ac_no'     => $data['ac_no'],
    'paginate'  => true,
    'restricted_ps' => []
  ];


  $results   = [];
  $filter_ps = [
    'st_code'   => $this->st_code,
    'ac_no'     => $this->ac_no,
    'paginate'  => false,
    'restricted_ps' => []
  ];


  $data['polling_stations'] = [];
  $results    =   PollingStation::get_polling_stations($filter_ps);
  foreach ($results as $result) {
    $data['polling_stations'][] = [
      'ps_no'   => $result['PS_NO'],
      'ps_name' => $result['PS_NAME_EN'],
    ];
  }

  if($id != '0'){
    $data['encrpt_id'] = $id;
    $id     = decrypt_string($id);
    $object = PollingStationOfficerModel::get_officer(['id' => $id]);
  }

  $data['id'] = $id;

  if($request->old('ps_no')){
    $data['ps_no']  = $request->old('ps_no');
  }else if(isset($object) && $object){
    $data['ps_no']  = $object['ps_no'];
  }else{
    if($request->has('ps_no')){
      $data['ps_no']  = $request->ps_no;
    }else{
      $data['ps_no']  = '';
    }
  }

  if($request->old('name')){
    $data['name']  = $request->old('name');
  }else if(isset($object) && $object){
    $data['name']  = $object['name'];
  }else{
    $data['name']  = ''; 
  }

  if($request->old('mobile')){
    $data['mobile']  = $request->old('mobile');
  }else if(isset($object) && $object){
    $data['mobile']  = $object['mobile_number'];
  }else{
    $data['mobile']  = ''; 
  }

  if($request->old('status')){
    $data['status']  = $request->old('status');
  }else if(isset($object) && $object){
    $data['status']  = $object['is_active'];
  }else{
    $data['status']  = ''; 
  }

  if($request->old('role_id')){
    $data['role_id']  = $request->old('role_id');
  }else if(isset($object) && $object){
    $data['role_id']  = $object['role_id'];
  }else{
    if($request->has('role_id')){
      $data['role_id']  = $request->role_id;
    }else{
      $data['role_id']  = '';
    }
  }

  if($request->old('pin')){
    $data['pin']  = $request->old('pin');
  }else{
    $data['pin']  = ''; 
  }
  if($request->old('pin_confirmation')){
    $data['pin_confirmation']  = $request->old('name');
  }else{
    $data['pin_confirmation']  = ''; 
  }

  $data['roles']      = [
    [
      'role_id' => '',
      'name'    => 'Select',
    ],
    [
      'role_id' => '33',
      'name'    => 'BLO',
    ],
    [
      'role_id' => '34',
      'name'    => 'PO',
    ],
    [
      'role_id' => '35',
      'name'    => 'PRO',
    ]
  ];
  $data['user_data']  =  Auth::user();
  $data['heading_title_with_all'] = $data['heading_title'];

  return view($this->view.'.officer-list-form', $data);

  try{}catch(\Exception $e){
    return Redirect::to($this->base.'/dashboard');
  }

}

public function post_officer(OfficerRequest $request){

  if($request->has('id')){
    $id = decrypt_string($request->id);
    $is_unique_mobile = PollingStationOfficerModel::is_unique_mobile($request->mobile, $id);
  }else{
    $is_unique_mobile = PollingStationOfficerModel::is_unique_mobile($request->mobile);
  }

  if($is_unique_mobile){
    Session::flash('status',0);
    Session::flash('flash-message',"Please enter an unique mobile number.");
    return Redirect::back()->withInput($request->all());
  }

  $no_allowed_po = $this->no_allowed_po;
  $no_allowed_blo    = $this->no_allowed_blo;
  $no_allowed_pro    = $this->no_allowed_pro;
  $merge            = [];
  $merge['st_code'] = \Auth::user()->st_code;
  $merge['dist_no'] = \Auth::user()->dist_no;
  $merge['ac_no']   = \Auth::user()->ac_no;

  if(in_array($request->role_id,['33','35'])){
    $merge['pin'] = '';
  }
  $request->merge($merge);

  $no_active_officer = PollingStationOfficerModel::count_officer($request->all());

  if(in_array($request->role_id,['35']) && $no_active_officer > $no_allowed_pro){
    Session::flash('status',0);
    Session::flash('flash-message',"You can only add 3 Polling Party to a Polling Station.");
    return Redirect::back()->withInput($request->all());
  }

  if(in_array($request->role_id,['33']) && $no_active_officer > $no_allowed_blo){
    Session::flash('status',0);
    Session::flash('flash-message',"You can only add ".$this->no_allowed_blo." BLO to a Polling Station.");
    return Redirect::back()->withInput($request->all());
  }

  if($request->role_id == '34' && $no_active_officer > $no_allowed_po){
    Session::flash('status',0);
    Session::flash('flash-message',"You can only add ".$no_allowed_po." PO to a Polling Station.");
    return Redirect::back()->withInput($request->all());
  }

  DB::beginTransaction();
  try{  
    $result   = PollingStationOfficerModel::add_officer($request->all());
  }
  catch(\Exception $e){
    DB::rollback();
    Session::flash('status',0);
    Session::flash('flash-message',"Please Try Again.");
    return Redirect::back();
  }
  DB::commit();

  $role = '';
  $app_link = 'http://cvigil.eci.gov.in/BoothApp.apk';
  if($request->role_id == '33'){
    $role = 'BLO';
  }else if($request->role_id == '35'){
    $role = 'Polling Party';
  }
  $polling_name = '';
  $poll_station = PollingStation::get_polling_station([
    'st_code' => $this->st_code,
    'ac_no'   => $this->ac_no,
    'ps_no'   => $request->ps_no
  ]);
  if($poll_station){
    $polling_name              = $poll_station['PS_NAME_EN'];
  }

  try{
    $sms_message = "Your number has been registered for Booth App as a ".$role." for Polling station no. ".$request->ps_no.'-'.$polling_name." by the Returning officer.  Please download Booth App ".$app_link;
    $msgstatus = SmsgatewayHelper::gupshup($request->mobile, $sms_message);
  }catch(\Exception $e){

  }


  Session::flash('status',1);
  Session::flash('flash-message',"Profile has been updated successfully.");
  return redirect($this->base.'/booth-app-revamp/officer-list');
}


//dashboard for eci
  public function dashboard(Request $request){
    $data                         = [];
    $data['buttons']              = [];
    $data['role_id']              = $this->role_id;
    $data['heading_title']        = "Change Pin";
    $data['action']               = url($this->action);
    $dashboard_data               = $this->get_dashboard_data($request);
    $data                         = array_merge($dashboard_data, $data);
    $data['active_tab']           = 'before';
    if($request->has('tab') && $request->tab == 'after'){
      $data['active_tab'] = $request->tab;
    }
    $request_array = [];
    if($this->st_code){
      $request_array[] = "st_code=".$this->st_code;
    }
    if($this->ac_no){
      $request_array[] = "ac_no=".$this->ac_no;
    }

    $request_string = implode('&', $request_array);
    $data['request_string']       = $request_string;
    $data['get_voter_turnout']    = Common::generate_url('booth-app-revamp/get_voter_turnout').'?'.$request_string;
    $data['referesh_page_url']    = Common::generate_url('booth-app-revamp/get-dashboard-data').'?'.$request_string;
    $data['scan_page_url']        = Common::generate_url('booth-app-revamp/scan-data').'?'.$request_string;
    $data['user_data']            = Auth::user();
    $data['href_polling_station'] = Common::generate_url('booth-app-revamp/polling-station').'?'.$request_string;
    $data['href_officer']         = Common::generate_url('booth-app-revamp/officers').'?'.$request_string;
    $data['href_blo_officer']     = Common::generate_url('booth-app-revamp/officers').'?role_id=33'.'&'.$request_string;
    $data['href_pro_officer']     = Common::generate_url('booth-app-revamp/officers').'?role_id=35'.'&'.$request_string;
    $data['href_pro_activate']    = Common::generate_url('booth-app-revamp/officers').'?role_id=35&is_activated=no'.'&'.$request_string;
    $data['href_blo_activate']    = Common::generate_url('booth-app-revamp/officers').'?role_id=33&is_activated=no'.'&'.$request_string;
    $data['href_e_download']      = Common::generate_url('booth-app-revamp/e-roll-download').'?'.$request_string;
    $data['href_poll_detail']       = Common::generate_url('booth-app-revamp/poll-detail').'?'.$request_string;
    $data['href_connected_status']  = Common::generate_url('booth-app-revamp/poll-detail').'?'.$request_string;

    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/dashboard");
    $form_filter_array = [
      'st_code'     => true,
      'dist_no'     => false,
      'ac_no'       => true, 
      'ps_no'       => false, 
      'designation'     => false,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];
    $form_filters = Common::get_form_filters($form_filter_array, $request);      
    $data['form_filters'] = $form_filters;

    return view($this->view.'.dashboard', $data);
  }

  public function get_dashboard_data(Request $request){

    $data                   = [];
    $filter = [
      'st_code'         => $this->st_code,
      'ac_no'           => $this->ac_no,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];

    $data['ac_no']    = $this->ac_no;
    $data['st_code']  = $this->st_code;
    $data['dist_no']  = $this->dist_no;

    $data['total_polling_booth']        = PollingStation::total_poll_station_count($filter);
    $data['total_blo_assign']           = PollingStationOfficerModel::total_officer_count(array_merge($filter,['role_id' => '33']));
    $data['total_pro_assign']           = PollingStationOfficerModel::total_officer_count(array_merge($filter,['role_id' => '35']));
    $data['total_blo_pro_assign']       = $data['total_pro_assign']+$data['total_blo_assign'];

    // $data['total_not_assign_officers']  = ($this->no_allowed_blo*$data['total_polling_booth'])+($this->no_allowed_pro*$data['total_polling_booth']) - $data['total_blo_pro_assign'];

    $data['total_not_assign_officers']  = $data['total_polling_booth']+$data['total_polling_booth'] - $data['total_blo_pro_assign'];


    $data['total_officer_count']        = $data['total_blo_assign']+$data['total_pro_assign'];
    $data['total_blo_pro_count']        = $data['total_blo_assign']+$data['total_pro_assign'];
    $data['total_app_downloaded']       = PollingStationOfficerModel::total_officer_count(array_merge($filter,['is_activated' => true]));

    $data['total_blo_not_activated']       = PollingStationOfficerModel::total_officer_count(array_merge($filter,['is_not_activated' => true, 'role_id' => 33]));
    $data['total_pro_not_activated']       = PollingStationOfficerModel::total_officer_count(array_merge($filter,['is_not_activated' => true, 'role_id' => 35]));
    $data['total_po_not_activated']       = PollingStationOfficerModel::total_officer_count(array_merge($filter,['is_not_activated' => true, 'role_id' => 34]));
    $data['total_e_download']   = TblBoothUserModel::total_e_download($filter);
    $data['total_poll_end']     = TblPollSummaryModel::total_statics_count(array_merge($filter,['is_end' => true]));
    $data['total_eroll_download_confirmed'] = 1;
    $data['total_poll_started']             = TblPollSummaryModel::total_statics_count(array_merge($filter,['is_started' => true]));
    $data['poll_percent']                   = 0;
    if($data['total_poll_started']>0 && $data['total_polling_booth']>0){
      $data['poll_percent'] = round($data['total_poll_started']/$data['total_polling_booth']*100);
    }
    $data['total_connected_status']         = TblPollSummaryModel::total_statics_count(array_merge($filter,['is_connected' => true]));
    $data['total_disconnected_status']      = TblPollSummaryModel::total_statics_count(array_merge($filter,['is_end' => true]));


    $data['last_disc_ps_name']            = '';
    $data['last_disc_ac_name']            = '';
    $data['poll_percent_for_css']         = 300;
    if($data['poll_percent'] < 100){
      $data['poll_percent_for_css']       = $data['poll_percent'] * 360/100;
    }

    $max_po                     = [];
    $highest_polling_name       = '';
    $max_percenatge             = [0];
    $data['voter_turnouts']     = [];
    $officers_data              = [];

    //voter turnout
    $voter_turn_out = $this->get_voter_turnout($request);

    //officers
    $officers = PollingStationOfficerModel::get_officers(array_merge($filter,['is_activated' => 'no']));
    foreach($officers as $officer){

      $role = '';
      if($officer['role_id'] == '33'){
        $role = 'BLO';
      }
      if($officer['role_id'] == '35'){
        $role = 'Polling Party';
      }
      if($officer['role_id'] == '34'){
        $role = 'PO';
      }

      $st_name = '';
      $state_object = StateModel::get_state_by_code($officer['st_code']);
      if($state_object){
        $st_name = $state_object['ST_NAME'];
      }

      $ac_name = '';
      $ac_object = AcModel::get_ac(['state' => $officer['st_code'], 'ac_no' => $officer['ac_no']]);
      if($ac_object){
        $ac_name = $ac_object['ac_name'];
      }

      $poll_station_name = '';
      $poll_station = PollingStation::get_polling_station([
        'st_code' => $officer['st_code'],
        'ac_no'   => $officer['ac_no'],
        'ps_no'   => $officer['ps_no']
      ]);
      if($poll_station){
        $poll_station_name = $poll_station['PS_NAME_EN'];
      }

      $officers_data[] =  [
        'st_code'       => $officer['st_code'],
        'st_name'     => $st_name,
        'ac_no'       => $officer['ac_no'],
        'ac_name'     => $ac_name,
        'ps_no'         => $officer['ps_no'],
        'ps_name'       => $poll_station_name,
        'name'          => $officer['name'],
        'mobile'        => $officer['mobile_number'],
        'designation'   => $role,
      ];
    }



    $stats_sum = TblPollSummaryModel::total_statics_sum($filter);


    $grand_male   = $stats_sum['male_voters'];
    $grand_female = $stats_sum['female_voters'];
    $grand_other  = $stats_sum['other_voters'];
    $grand_total  = $grand_male + $grand_female + $grand_other;

    $grand_e_male   = VoterInfoModel::get_elector_count(array_merge($filter,['gender' => 'M']));
    $grand_e_female = VoterInfoModel::get_elector_count(array_merge($filter,['gender' => 'F']));
    $grand_e_other  = VoterInfoModel::get_elector_count(array_merge($filter,['gender' => 'O']));
    $grand_e_total  = $grand_e_male+$grand_e_female+$grand_e_other;

    $total_male_percentage = 0;
    if($grand_e_male >= $grand_male && $grand_e_male>0){
      $total_male_percentage = ROUND($grand_male/$grand_e_male*100,2);
    }

    $total_female_percentage = 0;
    if($grand_e_female >= $grand_female && $grand_e_female>0){
      $total_female_percentage = ROUND($grand_female/$grand_e_female*100,2);
    }

    $total_other_percentage = 0;
    if($grand_e_other >= $grand_other && $grand_e_other>0){
      $total_other_percentage = ROUND($grand_other/$grand_e_other*100,2);
    }

    $total_percentage = 0;
    if($grand_e_total >= $grand_total && $grand_e_total>0){
      $total_percentage = ROUND($grand_total/$grand_e_total*100,2);
    }    

    $data['poll_turnout_percentage']  = $total_percentage;
    $data['total_male_percentage']    = $total_male_percentage.'%';
    $data['total_female_percentage']  = $total_female_percentage.'%';
    $data['total_other_percentage']   = $total_other_percentage.'%';
    $data['total_total_percentage']   = $total_percentage.'%';
  
    $data['total_male']    = $grand_male;
    $data['total_female']  = $grand_female;
    $data['total_other']   = $grand_other;
    $data['total_total']   = $grand_total;


    //bar graph
    $age_gap = ["18-25", "26-30", "31-40", "41-50", "51-60", "61-70", "71-80", "81-90", "91-100", "101-150"];
    $bar_graph  = [];
    foreach($age_gap as $iterate_age_gap){
      $bar_graph[] = VoterInfoPollStatusModel::get_elector_by_age(array_merge($filter,['age_between' => $iterate_age_gap]));
    }
    $data['age_gap']    = json_encode($age_gap);
    $data['bar_graph']  = json_encode($bar_graph);


    //gender wise bar chart 
    $gender_label_for_bar = ["Male", "Female", "Other"];
    $gender_data_for_bar  = [];
    $gender_data_for_bar[] = VoterInfoPollStatusModel::get_voter_count(array_merge($filter,['gender' => 'M']));
    $gender_data_for_bar[] = VoterInfoPollStatusModel::get_voter_count(array_merge($filter,['gender' => 'F']));
    $gender_data_for_bar[] = VoterInfoPollStatusModel::get_voter_count(array_merge($filter,['gender' => 'O']));
   
    $data['gender_label_for_bar'] = json_encode($gender_label_for_bar);
    $data['gender_data_for_bar']  = json_encode($gender_data_for_bar);
  
  //periodic line chart every 15 min
    $time_slot_label_for_line = VoterInfoModel::half_hour_times(); 
    foreach ($time_slot_label_for_line as $iterate_time_slot_label) {
      $time_slot_data_for_line[]  = VoterInfoPollStatusModel::get_voter_count(array_merge($filter,['time_between' => $iterate_time_slot_label]));
    }
    
    $data['time_slot_data_for_line'] =  json_encode($time_slot_data_for_line); 
    $data['time_slot_label_for_line'] = json_encode($time_slot_label_for_line);


    $data['officers'] = $officers_data;
    $data['highest_polling_name'] = $highest_polling_name;

    //doughnut_data
    $doughnut_data = [0,0,0,0,0];
    $doughnut_object = TblPollSummaryModel::total_statics_sum([
      'st_code' => $filter['st_code'],
      'ac_no'   => $filter['ac_no'],
    ]);
    if($doughnut_object){
      $doughnut_data = [$doughnut_object['scan_qr'],$doughnut_object['scan_epicno'],$doughnut_object['scan_srno'],$doughnut_object['scan_name'],$doughnut_object['scan_mobile']];
    }
    $data['doughnut_data'] = json_encode($doughnut_data);

    if($request->has('is_ajax')){
      return \Response::json($data);
    }

    return $data;

  }

  public function get_voter_turnout(Request $request){
    if($this->ac_no && $this->st_code){
      return $this->turnout_ps_wise($request);
    }else if($this->st_code){
      return $this->turnout_ac_wise($request);
    }else{
      return $this->turnout_state_wise($request);
    }
  }

  public function turnout_ac_wise($request){
    $data                   = [];
    $data['voter_turnouts'] = [];
    $filter = [
      'st_code'         => $this->st_code,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
      'allowed_acs'     => $this->allowed_acs
    ];

    $grand_e_male   = 0;
    $grand_e_female = 0;
    $grand_e_other  = 0;
    $grand_e_total  = 0;
    $grand_male   = 0;
    $grand_female = 0;
    $grand_other  = 0;
    $grand_total  = 0;
    $grand_queue  = 0;

    $acs_results = AcModel::get_acs($filter);
 
    foreach ($acs_results as $key => $iterate_ac) {
      $filter_for_voters = [
        'st_code' => $iterate_ac['st_code'],
        'ac_no'   => $iterate_ac['ac_no'],
      ];
      $stats_sum = TblPollSummaryModel::total_statics_sum($filter_for_voters);

      $male   = $stats_sum['male_voters'];
      $female = $stats_sum['female_voters'];
      $other  = $stats_sum['other_voters'];
      $total  = $male+$female+$other;
      $queue_voters = $stats_sum['queue_voters'];

      $e_male   = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'M']));
      $e_female = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'F']));
      $e_other  = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'O']));
      $e_total  = $e_male+$e_female+$e_other;


      $grand_e_male   += $e_male;
      $grand_e_female += $e_female;
      $grand_e_other  += $e_other;
      $grand_e_total  += $e_total;
      $grand_male   += $male;
      $grand_female += $female;
      $grand_other  += $other;
      $grand_total  += $total;

      $is_ps_poll_end = TblPollSummaryModel::total_statics_count(array_merge($filter_for_voters,['is_end' => true]));
      if($is_ps_poll_end){
        $queue_voters = 'Poll End';
      }else{
        $grand_queue  += $queue_voters;
      }

      $percentage = 0;
      if($e_total >= $total && $e_total > 0){
        $percentage = round($total/$e_total*100,2);
      }

      $data['voter_turnouts'][] = [
        'name'            => $iterate_ac['ac_no'].'-'.$iterate_ac['ac_name'],
        'male'            => $male,
        'female'          => $female,
        'other'           => $other,
        'total'           => $total,
        'e_male'          => $e_male,
        'e_female'        => $e_female,
        'e_other'         => $e_other,
        'e_total'         => $e_total,
        'total_in_queue'  => $queue_voters,
        'percentage'      => $percentage
      ];

    }

    $grand_percentage = 0;
    if($grand_e_total >= $grand_total && $grand_e_total > 0){
      $grand_percentage = round($grand_total/$grand_e_total*100,2);
    }

    $data['voter_turnouts'][]    = [
      'name' => 'Total',
      'male'    => $grand_male,
      'female'  => $grand_female,
      'other'   => $grand_other,
      'total'   => $grand_total,
      'e_male'    => $grand_e_male,
      'e_female'  => $grand_e_female,
      'e_other'   => $grand_e_other,
      'e_total'   => $grand_e_total,
      'total_in_queue'  => $grand_queue,
      'percentage' => $grand_percentage
    ];

    $data['grand_percentage']         = $grand_percentage;
    $data['poll_turnout_percentage']  = $grand_percentage;

    return view($this->view.'.voter_turnout_ac_wise', $data);
  }

  public function turnout_state_wise($request){
    $data                   = [];
    $data['voter_turnouts'] = [];
    $filter = [
      'st_code'         => $this->st_code,
      'allowed_st_code' => $this->allowed_st_code
    ];

    $grand_e_male   = 0;
    $grand_e_female = 0;
    $grand_e_other  = 0;
    $grand_e_total  = 0;
    $grand_male   = 0;
    $grand_female = 0;
    $grand_other  = 0;
    $grand_total  = 0;
    $grand_queue  = 0;

    $states_results = StateModel::get_states($filter);
    foreach ($states_results as $key => $iterate_state) {

      $filter_for_voters = [
        'st_code' => $iterate_state->ST_CODE
      ];
      $stats_sum = TblPollSummaryModel::total_statics_sum($filter_for_voters);

      $male   = $stats_sum['male_voters'];
      $female = $stats_sum['female_voters'];
      $other  = $stats_sum['other_voters'];
      $total  = $male+$female+$other;
      $queue_voters = $stats_sum['queue_voters'];

      $e_male   = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'M']));
      $e_female = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'F']));
      $e_other  = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'O']));
      $e_total  = $e_male+$e_female+$e_other;


      $grand_e_male   += $e_male;
      $grand_e_female += $e_female;
      $grand_e_other  += $e_other;
      $grand_e_total  += $e_total;
      $grand_male   += $male;
      $grand_female += $female;
      $grand_other  += $other;
      $grand_total  += $total;

      $is_ps_poll_end = TblPollSummaryModel::total_statics_count(array_merge($filter_for_voters,['is_end' => true]));
      if($is_ps_poll_end){
        $queue_voters = 'Poll End';
      }else{
        $grand_queue  += $queue_voters;
      }

      $percentage = 0;
      if($e_total >= $total && $e_total > 0){
        $percentage = round($total/$e_total*100,2);
      }

      $data['voter_turnouts'][] = [
        'st_name'         => $iterate_state['ST_NAME'],
        'male'            => $male,
        'female'          => $female,
        'other'           => $other,
        'total'           => $total,
        'e_male'          => $e_male,
        'e_female'        => $e_female,
        'e_other'         => $e_other,
        'e_total'         => $e_total,
        'total_in_queue'  => $queue_voters,
        'percentage'      => $percentage
      ];

    }

    $grand_percentage = 0;
    if($grand_e_total >= $grand_total && $grand_e_total > 0){
      $grand_percentage = round($grand_total/$grand_e_total*100,2);
    }

    $data['voter_turnouts'][]    = [
      'st_name' => 'Total',
      'male'    => $grand_male,
      'female'  => $grand_female,
      'other'   => $grand_other,
      'total'   => $grand_total,
      'e_male'    => $grand_e_male,
      'e_female'  => $grand_e_female,
      'e_other'   => $grand_e_other,
      'e_total'   => $grand_e_total,
      'total_in_queue'  => $grand_queue,
      'percentage' => $grand_percentage
    ];

    $data['grand_percentage']         = $grand_percentage;
    $data['poll_turnout_percentage']  = $grand_percentage;

    return view($this->view.'.voter_turnout_state_wise', $data);
  }

  public function turnout_ps_wise(Request $request){
    $data                   = [];
    $data['voter_turnouts'] = [];
    $filter = [
      'st_code'         => $this->st_code,
      'ac_no'           => $this->ac_no,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];

    $grand_e_male   = 0;
    $grand_e_female = 0;
    $grand_e_other  = 0;
    $grand_e_total  = 0;
    $grand_male   = 0;
    $grand_female = 0;
    $grand_other  = 0;
    $grand_total  = 0;
    $grand_queue  = 0;

    $polling_stations = PollingStation::get_polling_stations($filter);

    foreach ($polling_stations as $key => $iterate_p_s) {
      $ps_no = $iterate_p_s['PS_NO'];
      $filter_for_voters = array_merge($filter,['ps_no' => $iterate_p_s['PS_NO']]);

      $stats_sum = TblPollSummaryModel::total_statics_sum($filter_for_voters);

      $male   = $stats_sum['male_voters'];
      $female = $stats_sum['female_voters'];
      $other  = $stats_sum['other_voters'];
      $total  = $male+$female+$other;
      $queue_voters = $stats_sum['queue_voters'];


      $e_male   = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'M']));
      $e_female = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'F']));
      $e_other  = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'O']));
      $e_total  = $e_male + $e_female + $e_other;


      $grand_e_male   += $e_male;
      $grand_e_female += $e_female;
      $grand_e_other  += $e_other;
      $grand_e_total  += $e_total;
      $grand_male   += $male;
      $grand_female += $female;
      $grand_other  += $other;
      $grand_total  += $total;

      $is_ps_poll_end = TblPollSummaryModel::total_statics_count(array_merge($filter_for_voters,['is_end' => true]));
      if($is_ps_poll_end){
        $queue_voters = 'Poll End';
      }else{
        $grand_queue  += $queue_voters;
      }

      $percentage = 0;
      if($e_total >= $total && $e_total > 0){
        $percentage = round($total/$e_total*100,2);
      }

      $poll_station_name = $iterate_p_s['PS_NAME_EN'];

      $data['voter_turnouts'][] = [
        'ps_name'         => $poll_station_name,
        'ps_no'           => $iterate_p_s['PS_NO'],
        'ps_name_and_no'  => $iterate_p_s['PS_NO'].'-'.$poll_station_name,
        'male'            => $male,
        'female'          => $female,
        'other'           => $other,
        'total'           => $total,
        'e_male'          => $e_male,
        'e_female'        => $e_female,
        'e_other'         => $e_other,
        'e_total'         => $e_total,
        'total_in_queue'  => $queue_voters,
        'percentage'      => $percentage
      ];

    }

    $grand_percentage = 0;
    if($grand_e_total >= $grand_total && $grand_e_total > 0){
      $grand_percentage = round($grand_total/$grand_e_total*100,2);
    }

    $data['voter_turnouts'][]    = [
      'ps_name' => 'Total',
      'ps_no'   => '',
      'ps_name_and_no' => 'Total',
      'male'    => $grand_male,
      'female'  => $grand_female,
      'other'   => $grand_other,
      'total'   => $grand_total,
      'e_male'    => $grand_e_male,
      'e_female'  => $grand_e_female,
      'e_other'   => $grand_e_other,
      'e_total'   => $grand_e_total,
      'total_in_queue'  => $grand_queue,
      'percentage' => $grand_percentage
    ];

    $data['grand_percentage'] = $grand_percentage;
    $data['poll_turnout_percentage']  = $grand_percentage;

    return view($this->view.'.voter_turnout_ps_wise', $data);
  }
  

  //get officers
  public function get_officers(Request $request){

    $data                   = [];
    $data['role_id']        = NULL;
    $is_activated           = NULL;
    if($request->has('is_activated')){
      $is_activated = $request->is_activated;
    }

  //buttons
    $data['buttons']    = [];
    $data['buttons'][]    = [
      'href' => Common::generate_url($this->action.'/dashboard'),
      'name' => 'Back',
      'target' => false,
    ];

    $data['heading_title']  = "List of Officers";
    $request_filter = Common::get_request_filter($request);
    $ac_no          = $request_filter['ac_no'];
    $st_code        = $request_filter['st_code'];
    $ps_no          = $request_filter['ps_no'];
    $role_id         = $request_filter['role_id'];

      $request_array = [];
      //set title
      $title_array  = [];
      if($st_code){
        $state_object = StateModel::get_state_by_code($st_code);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }
      if($ac_no && $st_code){
        $ac_object = AcModel::get_ac(['state' => $st_code, 'ac_no' => $ac_no]);
        if($ac_object){
          $title_array[]  = "AC: ".$ac_object['ac_name'];
        }
      }
      if($ac_no && $st_code && $ps_no){
        $poll_station_name = '';
        $poll_station = PollingStation::get_polling_station([
          'state' => $st_code, 
          'ac_no' => $ac_no,
          'ps_no'   => $ps_no
        ]);
        if($poll_station){
          $title_array[]  = "PS: ".$poll_station['PS_NAME_EN'];
        }
      }

      $data['filter']   = implode('&', array_merge($request_array));
      $filter_election = [
        'st_code'   => $st_code,
        'ac_no'     => $ac_no,
        'ps_no'     => $ps_no,
        'paginate'  => true,
        'restricted_ps' => ['99','128','129','91','189'],
        'role_id'       => $role_id,
        'not_po'        => true,
        'is_activated'   => $is_activated
      ];

      $data['results']        = [];
      $officers_data          = [];

    if($filter_election['st_code'] && $filter_election['ac_no']){

      $results                =   PollingStationOfficerModel::get_officers($filter_election);
      $data['pag_results']    = $results;
      foreach ($results as $officer) {

        $role = '';
        if($officer->role_id == '33'){
          $role = 'BLO';
        }
        if($officer->role_id == '35'){
          $role = 'PRO';
        }
        if($officer->role_id == '34'){
          $role = 'PO';
        }

        $st_name = '';
        $state_object = StateModel::get_state_by_code($officer->st_code);
        if($state_object){
          $st_name = $state_object['ST_NAME'];
        }

        $ac_name = '';
        $ac_object = AcModel::get_ac(['state' => $officer->st_code, 'ac_no' => $officer->ac_no]);
        if($ac_object){
          $ac_name = $ac_object['ac_name'];
        }

        $poll_station_name = '';
        $poll_station = PollingStation::get_polling_station([
          'st_code' => $officer->st_code,
          'ac_no'   => $officer->ac_no,
          'ps_no'   => $officer->ps_no
        ]);
        if($poll_station){
          $poll_station_name = $poll_station['PS_NAME_EN'];
        }

        $is_login = false;
        if(strtotime($officer->login_time)){
          $is_login = true;
        }

        $officers_data[] =  [
          'st_code'       => $officer->st_code,
          'st_name'     => $st_name,
          'ac_no'       => $officer->ac_no,
          'ac_name'     => $ac_name,
          'ps_no'         => $officer->ps_no,
          'ps_name'       => $poll_station_name,
          'name'          => $officer->name,
          'mobile'        => $officer->mobile_number,
          'designation'   => $role,
          'is_login'      =>  $is_login
        ];
      }
    }else{
      $data['no_record'] = "Please select a stata and ac.";
    }

      $data['user_data']  = Auth::user();
      $data['results']    = $officers_data;

      $data['heading_title_with_all'] = $data['heading_title'];

      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }
      
      //form filters
      $data['filter_action'] = Common::generate_url("booth-app-revamp/officers");
      $form_filter_array = [
        'st_code'     => true,
        'ac_no'       => true, 
        'ps_no'       => true, 
        'designation' => true,
        'allowed_acs'     => $this->allowed_acs,
        'allowed_st_code' => $this->allowed_st_code,
        'allowed_dist_no' => $this->allowed_dist_no,
      ];
      $form_filters = Common::get_form_filters($form_filter_array, $request);

      //activate filter
      $is_activated_value   = [];
      $is_activated_value[] = [
        'name'  => 'Activated',
        'id'    => 'yes',
      ];
      $is_activated_value[] = [
        'name'  => 'Not-Activated',
        'id'    => 'no',
      ];

      $activated_array = [];
      foreach ($is_activated_value as $iterate_activate) {
        $is_active = false;
        if($is_activated == $iterate_activate['id']){
          $is_active = true;
        }
        $activated_array[] = [
          'name'    => $iterate_activate['name'],
          'id'      => $iterate_activate['id'],
          'active'  => $is_active
        ];
      }

      $form_filters[] = [
        'id'      => 'is_activated',
        'name'    => 'Status',
        'results' => $activated_array
      ];

      
      $data['form_filters'] = $form_filters;
      return view($this->view.'.officers', $data);
 
}

//polling station
public function polling_station(Request $request){
    $data                   = [];
    $data['buttons']        = [];
    $data['role_id']        = $this->role_id;
    $data['heading_title']  = "List of Polling Station";

    $request_filter = Common::get_request_filter($request);
    $ac_no          = $request_filter['ac_no'];
    $st_code        = $request_filter['st_code'];

    $filter = [
      'st_code' => $st_code,
      'ac_no'   => $ac_no,
      'restricted_ps' => $this->restricted_ps
    ];

    //buttons
    $data['buttons']    = [];
    $data['buttons'][]    = [
      'href' => Common::generate_url($this->action.'/dashboard'),
      'name' => 'Back',
      'target' => false,
    ];
    
    $data['results']     = [];

    if($filter['st_code'] && $filter['ac_no']){
      $ps_results = PollingStation::get_polling_stations($filter);

      foreach ($ps_results as $key => $iterate_ps) {
        $iterate_restricted = $iterate_ps['PS_NO'];

          $filter_for_voters = array_merge($filter,['ps_no' => $iterate_restricted]);

          $poll_station_name = '';
          $poll_station = PollingStation::get_polling_station($filter_for_voters);
          if($poll_station){
            $poll_station_name = $poll_station['PS_NAME_EN'];
          }

          $ac_name = '';
          $ac_no = '';
          $ac_object = AcModel::get_ac([
            'state' => $poll_station['ST_CODE'],
            'ac_no' => $poll_station['AC_NO']
          ]);
          if($ac_object){
            $ac_name = $ac_object['ac_name'];
            $ac_no = $ac_object['ac_no'];
          }

          $st_name = '';
          $st_object = StateModel::get_state_by_code($poll_station['ST_CODE']);
          if($st_object){
            $st_name = $st_object['ST_NAME'];
          }

          $data['results'][]    = [
            'st_name' => $st_name,
            'ac_no'   => $ac_no,
            'ac_name' => $ac_name,
            'ps_no'   => $iterate_restricted,
            'ps_name' => $poll_station_name,
          ];
      }
    }else{
      $data['no_record'] = "Please select a state & ac";
    }
    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/polling-station");
    $form_filter_array = [
      'st_code'     => true,
      'ac_no'       => true, 
      'ps_no'       => false, 
      'designation' => false,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters']   = $form_filters;
    $data['user_data']      = Auth::user();

    return view($this->view.'.no-of-polling-station', $data);   
}


//e-roll download
public function get_e_roll_download(Request $request){

    $data                   = [];   
    //buttons
    $data['buttons']    = [];
    $data['buttons'][]    = [
      'href' => Common::generate_url($this->action.'/dashboard'),
      'name' => 'Back',
      'target' => false,
    ];

    $data['heading_title']  = "List of E-Download";
    
    $request_filter = Common::get_request_filter($request);
    $ac_no          = $request_filter['ac_no'];
    $st_code        = $request_filter['st_code'];
    $ps_no          = $request_filter['ps_no'];
    $role_id         = $request_filter['role_id'];

      $request_array = [];
      //set title
      $title_array  = [];
      if($st_code){
        $state_object = StateModel::get_state_by_code($st_code);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }
      if($ac_no && $st_code){
        $ac_object = AcModel::get_ac(['state' => $st_code, 'ac_no' => $ac_no]);
        if($ac_object){
          $title_array[]  = "AC: ".$ac_object['ac_name'];
        }
      }
      if($ac_no && $st_code && $ps_no){
        $poll_station_name = '';
        $poll_station = PollingStation::get_polling_station([
          'state' => $st_code, 
          'ac_no' => $ac_no,
          'ps_no'   => $ps_no
        ]);
        if($poll_station){
          $title_array[]  = "PS: ".$poll_station['PS_NAME_EN'];
        }
      }
      $data['filter_buttons'] = $title_array;

      $data['filter']   = implode('&', array_merge($request_array));
      $filter_election = [
        'st_code'   => $st_code,
        'ac_no'     => $ac_no,
        'ps_no'     => $ps_no,
        'paginate'  => false,
        'role_id'       => $role_id
      ];
  
      $data['results']        = [];
      $officers_data          = [];

      if($filter_election['st_code'] && $filter_election['ac_no']){
        $results                = TblBoothUserModel::get_download_time($filter_election);
        foreach ($results as $officer) {

          $role = '';
          if($officer->user_type == '33'){
            $role = 'BLO';
          }
          if($officer->user_type == '35'){
            $role = 'Polling Party';
          }
          if($officer->user_type == '34'){
            $role = 'PO';
          }

          $st_name = '';
          $state_object = StateModel::get_state_by_code($officer->st_code);
          if($state_object){
            $st_name = $state_object['ST_NAME'];
          }

          $ac_name = '';
          $ac_object = AcModel::get_ac(['state' => $officer->st_code, 'ac_no' => $officer->ac_no]);
          if($ac_object){
            $ac_name = $ac_object['ac_name'];
          }

          $poll_station_name = '';
          $poll_station = PollingStation::get_polling_station([
            'st_code' => $officer->st_code,
            'ac_no'   => $officer->ac_no,
            'ps_no'   => $officer->ps_no
          ]);
          if($poll_station){
            $poll_station_name = $poll_station['PS_NAME_EN'];
          }

          $name = '';
          $mobile = '';
          $officer_detail = PollingStationOfficerModel::get_officer(['id' => $officer->officer_id]);

          if($officer_detail){
            $name   = $officer_detail['name'];
            $mobile = $officer_detail['mobile_number'];
          }

          $download_time = '';
          if($officer->download_time){
            $download_time = round($officer->download_time/60,2).' minute';
          }

          $officers_data[] =  [
            'st_code'       => $officer->st_code,
            'st_name'     => $st_name,
            'ac_no'       => $officer->ac_no,
            'ac_name'     => $ac_name,
            'ps_no'         => $officer->ps_no,
            'ps_name'       => $poll_station_name,
            'name'          => $name,
            'mobile'        => $mobile,
            'designation'   => $role,
            'download_time' =>  $download_time
          ];
        }

      }else{
        $data['no_record'] = "Please select state and ac.";
      }

      $data['user_data']  = Auth::user();
      $data['results']    = $officers_data;

      $data['heading_title_with_all'] = $data['heading_title'];

      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }
      
      //form filters
      $data['filter_action'] = Common::generate_url("booth-app-revamp/e-roll-download");
      $form_filter_array = [
        'st_code'     => true,
        'ac_no'       => true, 
        'ps_no'       => true, 
        'designation' => true,
        'allowed_acs'     => $this->allowed_acs,
        'allowed_st_code' => $this->allowed_st_code,
        'allowed_dist_no' => $this->allowed_dist_no,
      ];
      $form_filters = Common::get_form_filters($form_filter_array, $request);
      
      $data['form_filters'] = $form_filters;
      return view($this->view.'.e-roll-download', $data);
}

public function get_voter_list(Request $request){

  $data = [];
  $request_array = [];

  $data['st_code']  = NULL;
  $data['ac_no']    = NULL;
  $data['ps_no']    = NULL;

  $request_filter = Common::get_request_filter($request);
  $ac_no          = $request_filter['ac_no'];
  $st_code        = $request_filter['st_code'];
  $ps_no          = $request_filter['ps_no'];
  $role_id        = $request_filter['role_id'];

//set title
  $title_array  = [];
  $data['heading_title'] = 'Electoral List';

  if($st_code){
    $state_object = StateModel::get_state_by_code($st_code);
    if($state_object){
      $title_array[]  = "State: ".$state_object['ST_NAME'];
    }
  }
  if($ac_no){
    $ac_object = AcModel::get_ac(['state' => $st_code, 'ac_no' => $ac_no]);
    if($ac_object){
      $title_array[]  = "AC: ".$ac_object['ac_name'];
    }
  }
  if($ps_no){
    $ps_object = PollingStation::get_polling_station(['st_code' => $st_code, 'ac_no' => $ac_no, 'ps_no' => $ps_no]);
    if($ps_object){
      $title_array[]  = "PS: ".$ps_object['PS_NAME_EN'];
    }
  }

  $data['filter_buttons'] = $title_array;
  $data['filter']   = implode('&', array_merge($request_array));
  //end set title
  $data['action']     = Common::generate_url($this->action.'/voter-list');
  $data['add_download_log'] = Common::generate_url('booth-app-revamp/add_download_log/0');

  $results                = [];
  $filter_for_ps = [
    'st_code'  => $st_code,
    'ac_no'    => $ac_no,
    'ps_no'    => $ps_no,
    'paginate'  => false
  ];


  //buttons
  $data['buttons']    = [];
  $button_filter = [];


  $data['results']      = [];
  if($st_code && $ac_no){
    $results              = PollingStation::get_polling_stations($filter_for_ps);
    foreach ($results as $ierate_res) {
      $data['results'][] = [
        'ps_no'   => $ierate_res['PS_NO'],
        'ps_name' => $ierate_res['PS_NAME_EN'],
        'href'    => $ierate_res['slip_path'],
      ];
    }
  }

  //form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/voter-list");
  $form_filter_array = [
    'st_code'     => true,
    'ac_no'       => true, 
    'ps_no'       => true, 
    'designation' => false,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);
  
  $data['form_filters'] = $form_filters;

  $data['user_data']  =   Auth::user();
  $data['heading_title_with_all'] = $data['heading_title'];

  return view($this->view.'.voting-list', $data);

  try{}catch(\Exception $e){
    return Redirect::to($this->base.'/dashboard');
  }

}

public function add_download_log($elector_id = 0, Request $request){
  try{
    $data = [];
    $data['st_code']  = $this->st_code;
    $data['ac_no']    = $this->ac_no;
    $data['ps_no']    = 0;
    if($request->has('ps_no')){
      $data['ps_no'] = $request->ps_no;
    }
    $data['created_by'] = Auth::id();
    $data['updated_by'] = Auth::id();
    $data['role_id']    = Auth::user()->role_id;
    $data['created_at'] = date("Y:m:d h:s:i");
    $data['updated_at'] = date("Y:m:d h:s:i");
    $data['name']           = Auth::user()->officername;
    DB::connection("mysql_for_ac")->table("download_booth_sliplogs")->insert($data);
  }catch(\Exception $e){

  }
}

//get scan data

public function get_sacn_data(Request $request){
    $data                   = [];
    $data['role_id']        = $this->role_id;
    $data['heading_title']  = "Scan Electoral Report";

    $request_filter = Common::get_request_filter($request);
    $ac_no          = $request_filter['ac_no'];
    $st_code        = $request_filter['st_code'];

    $filter = [
      'st_code' => $st_code,
      'ac_no'   => $ac_no,
      'allowed_acs' => $this->allowed_acs,
      'allowed_dist_no' => $this->allowed_dist_no,
      'allowed_st_code' => $this->allowed_st_code
    ];

    
    $data['results']     = [];

    $ps_results = PollingStation::get_polling_stations($filter);

    foreach ($ps_results as $key => $iterate_ps) {

      $iterate_restricted = $iterate_ps['PS_NO'];

      $doughnut_object = TblPollSummaryModel::total_statics_sum(array_merge($filter,[
        'ps_no' => $iterate_ps['PS_NO']
      ]));

      $iterate_restricted = $iterate_ps['PS_NO'];

        $filter_for_voters = array_merge($filter,['ps_no' => $iterate_restricted]);

        $poll_station_name = '';
        $poll_station = PollingStation::get_polling_station($filter_for_voters);
        if($poll_station){
          $poll_station_name = $poll_station['PS_NAME_EN'];
        }

        $ac_name = '';
        $ac_no = '';
        $ac_object = AcModel::get_ac([
          'state' => $poll_station['ST_CODE'],
          'ac_no' => $poll_station['AC_NO']
        ]);
        if($ac_object){
          $ac_name = $ac_object['ac_name'];
          $ac_no = $ac_object['ac_no'];
        }

        $st_name = '';
        $st_object = StateModel::get_state_by_code($poll_station['ST_CODE']);
        if($st_object){
          $st_name = $st_object['ST_NAME'];
        }

        //get data ps wise
        $total_qr   = 0;
        $total_epic = 0;
        $total_bs   = 0;
        $total_name = 0;
        $total_mobile = 0;
       
        
        if($doughnut_object){
          $total_qr   = $doughnut_object['scan_qr'];
          $total_epic = $doughnut_object['scan_epicno'];
          $total_bs   = $doughnut_object['scan_srno'];
          $total_name = $doughnut_object['scan_name'];
          $total_mobile = $doughnut_object['scan_mobile'];
        }

        $data['results'][] = [
          'st_name' => $st_name,
          'ac_no'   => $ac_no,
          'ac_name' => $ac_name,
          'ps_no'   => $iterate_restricted,
          'ps_name'   => $poll_station_name,
          'total_qr'     => $total_qr,
          'total_epic'   => $total_epic,
          'total_name'   => $total_name,
          'total_booth_id'   => $total_bs,
          'total_mobile'    => $total_mobile
        ];
    }
    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/polling-station");
    $form_filter_array = [
      'st_code'     => true,
      'ac_no'       => true, 
      'ps_no'       => false, 
      'designation' => false,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters']   = $form_filters;

    return \Response::json($data);  

}

//poll details
public function get_poll_detail(Request $request){
    $data                   = [];
    $data['buttons']        = [];
    $data['role_id']        = $this->role_id;
    $data['heading_title']  = "List of Polling Station";

    $request_filter = Common::get_request_filter($request);
    $ac_no          = $request_filter['ac_no'];
    $st_code        = $request_filter['st_code'];

    $filter = [
      'st_code' => $st_code,
      'ac_no'   => $ac_no,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_acs' => $this->allowed_acs,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];

    //buttons
    $data['buttons']    = [];
    $data['buttons'][]    = [
      'href' => Common::generate_url($this->action.'/dashboard'),
      'name' => 'Back',
      'target' => false,
    ];
    
    $data['results']     = [];

    if($filter['st_code'] && $filter['ac_no']){
      $ps_results = PollingStation::get_polling_stations($filter);

      foreach ($ps_results as $key => $iterate_ps) {

        $iterate_restricted = $iterate_ps['PS_NO'];

        $poll_summary_object = TblPollSummaryModel::get_poll_summary(array_merge($filter,[
          'ps_no' => $iterate_ps['PS_NO'], 
          'is_started' => true
        ]));

        $iterate_restricted = $iterate_ps['PS_NO'];

          $filter_for_voters = array_merge($filter,['ps_no' => $iterate_restricted]);

          $poll_station_name = '';
          $poll_station = PollingStation::get_polling_station($filter_for_voters);
          if($poll_station){
            $poll_station_name = $poll_station['PS_NAME_EN'];
          }

          $ac_name = '';
          $ac_no = '';
          $ac_object = AcModel::get_ac([
            'state' => $poll_station['ST_CODE'],
            'ac_no' => $poll_station['AC_NO']
          ]);
          if($ac_object){
            $ac_name = $ac_object['ac_name'];
            $ac_no = $ac_object['ac_no'];
          }

          $st_name = '';
          $st_object = StateModel::get_state_by_code($poll_station['ST_CODE']);
          if($st_object){
            $st_name = $st_object['ST_NAME'];
          }

          $data['results'][]    = [
            'st_name' => $st_name,
            'ac_no'   => $ac_no,
            'ac_name' => $ac_name,
            'ps_no'   => $iterate_restricted,
            'ps_name'   => $poll_station_name,
            'is_start'  => ($poll_summary_object['poll_start_datetime'])?$poll_summary_object['poll_start_datetime']:'No',
         'is_end'    => ($poll_summary_object['poll_end_datetime'])?$poll_summary_object['poll_end_datetime']:'No',
          ];
      }
    }else{
      $data['no_record'] = "Please select a state and ac.";
    }
    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/polling-station");
    $form_filter_array = [
      'st_code'     => true,
      'ac_no'       => true, 
      'ps_no'       => false, 
      'designation' => false,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters']   = $form_filters;
    $data['user_data']      = Auth::user();

    return view($this->view.'.poll-detail', $data);   
  }

  public function generate_polling_station(Request $request){
    $data                   = [];
    $filter = [
      'st_code'         => $this->st_code,
      'ac_no'           => $this->ac_no,
      'dist_no'         => $this->dist_no,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];

    $i = 0;
    foreach($this->allowed_st_code as $st_code){
      $filter = [];
      $allowed_filter = Common::get_allowed_acs($st_code);
      $acs = AcModel::get_acs($allowed_filter);
      foreach($acs as $iterate_ac){
        $filter = array_merge($allowed_filter,[
          'ac_no'           => $iterate_ac['ac_no'],
          'st_code'         => $iterate_ac['st_code'],
          'dist_no'         => $iterate_ac['dist_no']
        ]);
        JsonFile::generate_polling_station_file($filter);
        echo $i."<br>";
        $i++;
      }
      
    }
  }


  public function generate_electors(Request $request){
    $data                   = [];
    $filter = [
      'st_code'         => $this->st_code,
      'ac_no'           => $this->ac_no,
      'dist_no'         => $this->dist_no,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];
    $i = 0;
    foreach($this->allowed_st_code as $st_code){

      $filter = [];

      $allowed_filter = Common::get_allowed_acs($st_code);

      $st_code_filter = array_merge($allowed_filter,['st_code' => $st_code]);

      JsonFile::generate_electors_for_state($st_code_filter);

      $districts = DistrictModel::get_districts($st_code_filter);

      foreach($districts as $iterate_distract){

        JsonFile::generate_electors_for_district($iterate_distract);
    
        $acs = AcModel::get_acs(array_merge($st_code_filter,[
          'dist_no'         => $iterate_distract['dist_no'],
        ]));

        foreach($acs as $iterate_ac){
          JsonFile::generate_electors_for_ps($iterate_ac);
          echo $i."<br>";
          $i++;
        }

      }

    }

  }


  //send sms to user
  public function send_sms_to_boothapp(Request $request){

    $app_link = 'http://cvigil.eci.gov.in/BoothApp.apk';
    $officers = PollingStationOfficerModel::get_officers();
    $i = 0;
    foreach($officers as $officer){

      $role = '';
      if($officer['role_id'] == '33'){
        $role = 'BLO';
      }
      if($officer['role_id'] == '35'){
        $role = 'Polling Party';
      }
      if($officer['role_id'] == '34'){
        $role = 'PO';
      }

      $poll_station_name = '';
      $poll_station = PollingStation::get_polling_station([
        'st_code' => $officer['st_code'],
        'ac_no'   => $officer['ac_no'],
        'ps_no'   => $officer['ps_no']
      ]);
      if($poll_station){
        $poll_station_name = $poll_station['PS_NAME_EN'];
      }
      try{
        $sms_message = "Your number has been registered for Booth App as a ".$role." for Polling station no. ".$officer['ps_no'].'-'.$poll_station_name." by the Returning officer.  Please download Booth App ".$app_link;
        $msgstatus = SmsgatewayHelper::gupshup($officer['mobile_number'], $sms_message);
        $i++;
      }catch(\Exception $e){

      }
    }

    echo "Link sent to ". $i. " officers";
  }

  public function reset_otp(Request $request){

  $data   = [
    'otp'     => $request->otp,
    'mobile'  => $request->mobile
  ];
  $rules = [
    "mobile"  => "required|mobile",
    "otp"     => "required|regex:/^[0-9]{6}$/"
  ];

  $messages = [
      'mobile'  => 'Please enter valid a valid mobile number',
      'otp'     => 'Please enter a valid 6 digit number.',
      'regex'   => 'Please enter a valid 6 digit number.',
  ];

  $validator = Validator::make($data, $rules, $messages);
  if ($validator->fails())
  {
    return \Response::json([
        'status' => false,
        'errors' => $validator->errors()->getMessageBag()->first()
    ]);
  }

  $is_valid_mobile = PollingStationOfficerModel::get_officer(['mobile' => $request->mobile]);
  if(!$is_valid_mobile){
    return \Response::json([
        'status' => false,
        'errors' => "Please enter a valid mobile number"
    ]);
  }

  try{
    PollingStationOfficerModel::update_otp([
      'mobile'  => $request->mobile,
      'otp'     => $request->otp,
    ]);
  }catch(\Exception $e){
    return \Response::json([
        'status' => false,
        'errors' => "Please try again."
    ]);
  }

  return \Response::json([
    'status'  => true,
    'message' => "OTP has been updated."
  ]);
}


}  // end class