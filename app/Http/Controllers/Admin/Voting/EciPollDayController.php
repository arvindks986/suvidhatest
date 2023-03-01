<?php namespace App\Http\Controllers\Admin\Voting;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Session;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Redirect;
    use Carbon\Carbon;
    use DB;
    use Illuminate\Support\Facades\Hash;
    use Validator;
    use Config;
    use \PDF;
    use App\commonModel;  
    use App\models\Admin\ReportModel;
    use App\models\Admin\PollDayModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\models\Admin\StateModel;

class EciPollDayController extends Controller {
  
  public $base    = 'ro';
  public $folder  = 'eci';
  public $action    = 'eci/voting/list-schedule';
  public $view_path = "admin.pc.eci";

  public function __construct(){
    $this->middleware('eci');
    $this->commonModel  = new commonModel();
    $this->report_model = new ReportModel();
    $this->voting_model = new PollDayModel();
    if(!Auth::user()){
      return redirect('/officer-login');
    }
  }

  public function index(Request $request){
    try{
      $data = [];
      $from_date  = NULL;
      $from_to    = NULL;
      
      //first argument must be string, second must be $request object if you want to verify base 64, send that variable in ccode parameter in request object using $request->merge(['ccode' => $somevalue]);
      $request_status = validate_request('',$request);
      if(!$request_status){
        return Redirect::to('logout');
      }
      //end validate request

      $request_array = [];
  
      $data['states'] = [];
      foreach(StateModel::get_pc_states() as $result){
        $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
        ];
      }

      $data['phases'] = $this->report_model->get_phases();
      $data['phase'] = NULL;
      if($request->has('phase')){
        $data['phase'] = $request->phase;
        $request_array[] =  'phase='.$request->phase;
      }

      $data['state'] = NULL;
      if($request->has('state')){
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;
      }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Poll Day Turnout Details';
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
      //end set title

      $data['action']         = url($this->action);
      $data['redirect_href']  = url($this->action);

      $results              = [];
      $total_round_1_m= 0;
            $total_round_1_f= 0;
            $total_round_1_o= 0;
            $total_round_1_t= 0;
            $total_round_2_m= 0;
            $total_round_2_f= 0;
            $total_round_2_o= 0;
            $total_round_2_t= 0;
            $total_round_3_m= 0;
            $total_round_3_f= 0;
            $total_round_3_o= 0;
            $total_round_3_t= 0;
            $total_round_4_m= 0;
            $total_round_4_f= 0;
            $total_round_4_o= 0;
            $total_round_4_t= 0;
            $total_round_5_m= 0;
            $total_round_5_f= 0;
            $total_round_5_o= 0;
            $total_round_5_t= 0;
            $total_round_end_m= 0;
            $total_round_end_f= 0;
            $total_round_end_o= 0;
            $total_round_end_t= 0;
      
          $total_round_1_total   = 0;
          $total_round_2_total   = 0;
          $total_round_3_total   = 0;
          $total_round_4_total   = 0;
          $total_round_5_total    = 0;
          $total_round_end_total  = 0;

          $total_e_male     = 0;
          $total_e_female   = 0;
          $total_e_other    = 0;
          $total_e_total    = 0;

          $total_male     = 0;
          $total_female   = 0;
          $total_other    = 0;
          $total_total    = 0;

      $total_voter_male   = 0;
      $total_voter_female    = 0;
      $total_voter_other    = 0;



      $filter_election = [
        'state_code'    => $data['state'],
        'const_type'    => 'PC',
      ];

      foreach ($data['states'] as $lis) {   

          $filter_data = [
            'st_code'       => base64_decode($lis['code']),
            'const_type'    => NULL,
            'phase'         => $data['phase']
          ];

           //total
          $total_round        = $this->voting_model->get_total_round($filter_data);
          $total_round_1_total   += $total_round->round_1_total;
          $total_round_2_total   += $total_round->round_2_total;
          $total_round_3_total   += $total_round->round_3_total;
          $total_round_4_total   += $total_round->round_4_total;
          $total_round_5_total   += $total_round->round_5_total;
          $total_round_end_total   += $total_round->round_end_total;

          $total_voter_male     += $total_round->total_voter_male;
          $total_voter_female   += $total_round->total_voter_female;
          $total_voter_other    += $total_round->total_voter_other;

          $total_elector     = $this->voting_model->get_elector_total($filter_data);
          $total_e_male     += $total_elector->gen_m;
          $total_e_female   += $total_elector->gen_f;
          $total_e_other    += $total_elector->gen_o;
          $total_e_total    += $total_elector->gen_t;



          $count_result       = $this->voting_model->get_total_by_state($filter_data);

          $total_round_1_m += $count_result['round_1_m'];
          $total_round_1_f += $count_result['round_1_f'];
          $total_round_1_o += $count_result['round_1_o'];
          $total_round_1_t += $count_result['round_1_t'];
          $total_round_2_m += $count_result['round_2_m'];
          $total_round_2_f += $count_result['round_2_f'];
          $total_round_2_o += $count_result['round_2_o'];
          $total_round_2_t += $count_result['round_2_t'];
          $total_round_3_m += $count_result['round_3_m'];
          $total_round_3_f += $count_result['round_3_f'];
          $total_round_3_o += $count_result['round_3_o'];
          $total_round_3_t += $count_result['round_3_t'];
          $total_round_4_m += $count_result['round_4_m'];
          $total_round_4_f += $count_result['round_4_f'];
          $total_round_4_o += $count_result['round_4_o'];
          $total_round_4_t += $count_result['round_4_t'];
          $total_round_5_m += $count_result['round_5_m'];
          $total_round_5_f += $count_result['round_5_f'];
          $total_round_5_o += $count_result['round_5_o'];
          $total_round_5_t += $count_result['round_5_t'];
          $total_round_end_m += $count_result['round_end_m'];
          $total_round_end_f += $count_result['round_end_f'];
          $total_round_end_o += $count_result['round_end_o'];
          $total_round_end_t += $count_result['round_end_t']; 

          $total_male   += $count_result['total_male'];
          $total_female += $count_result['total_female'];
          $total_other  += $count_result['total_other'];
          $total_total  += $count_result['total']; 

          $results[] = [
            'label'              => $lis['name'],
            'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            'const_no'           => $lis['code'],
            'const_name'         => $lis['name'],
            'round_1_m'              => $count_result['round_1_m'],
            'round_1_f'              => $count_result['round_1_f'],
            'round_1_o'              => $count_result['round_1_o'],
            'round_1_t'              => $count_result['round_1_t'],
            'round_2_m'              => $count_result['round_2_m'],
            'round_2_f'              => $count_result['round_2_f'],
            'round_2_o'              => $count_result['round_2_o'],
            'round_2_t'              => $count_result['round_2_t'],
            'round_3_m'              => $count_result['round_3_m'],
            'round_3_f'              => $count_result['round_3_f'],
            'round_3_o'              => $count_result['round_3_o'],
            'round_3_t'              => $count_result['round_3_t'],
            'round_4_m'              => $count_result['round_4_m'],
            'round_4_f'              => $count_result['round_4_f'],
            'round_4_o'              => $count_result['round_4_o'],
            'round_4_t'              => $count_result['round_4_t'],
            'round_5_m'              => $count_result['round_5_m'],
            'round_5_f'              => $count_result['round_5_f'],
            'round_5_o'              => $count_result['round_5_o'],
            'round_5_t'              => $count_result['round_5_t'],
            'round_end_m'            => $count_result['round_end_m'],
            'round_end_f'            => $count_result['round_end_f'],
            'round_end_o'            => $count_result['round_end_o'],
            'round_end_t'            => $count_result['round_end_t'], 

            'total_male'             => $count_result['total_male'],
            'total_female'           => $count_result['total_female'],
            'total_other'            => $count_result['total_other'],
            'total'                  => $count_result['total'], 

            'href'                   => url($this->action.'/state'),
            'gen_m'             => $total_elector->gen_m,
            'gen_f'             => $total_elector->gen_f,
            'gen_o'             => $total_elector->gen_o,
            'gen_t'             => $total_elector->gen_t,
          ];                        
    }   


    $total_aggregate_elector = $total_e_total;
    $total_aggregate_voter   = $total_round_1_total + $total_round_2_total + $total_round_3_total + $total_round_4_total + $total_round_5_total + $total_round_end_total;


    //total voting
    $data['number_of_voting'] = 0;
    if($total_aggregate_elector > 0){
      $data['number_of_voting'] = round((($total_total*100)/$total_aggregate_elector),1);
    }


    //male percentage
    $data['male_percentage'] = 0;
    if($total_e_male > 0){
      $data['male_percentage'] = round((($total_voter_male*100)/$total_e_male),1);
    }

    $data['female_percentage'] = 0;
    if($total_e_female > 0){
      $data['female_percentage'] = round((($total_voter_female*100)/$total_e_female),1);
    }



    $data['other_percentage'] = 0;
    if($total_e_other > 0){
      $data['other_percentage'] = round((($total_voter_other*100)/$total_e_other),1);
    }





    $data['results']    =  $results;
    $data['user_data']  = Auth::user();
    $data['from']       = $from_date;
    $data['to']         = $from_to;


    $data['buttons']    = [];
    $data['buttons'][]  = [
      'name' => 'Export Excel',
      'href' =>  url($this->action.'/export_excel').'?'.implode('&', $request_array),
      'target' => true
    ];
    

    if($request->has('is_excel')){
      if(isset($title_array) && count($title_array)>0){
        $data['heading_title'] .= "- ".implode(', ', $title_array);
      }
      return $data;
    }

    return view($this->view_path.'.voting.list', $data);

    }catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }


  public function state(Request $request){
    
      $data = [];
      $from_date  = NULL;
      $from_to    = NULL;
      
      //first argument must be string, second must be $request object if you want to verify base 64, send that variable in ccode parameter in request object using $request->merge(['ccode' => $somevalue]);
      $request_status = validate_request('',$request);
      if(!$request_status){
        return Redirect::to('logout');
      }
      //end validate request

      $request_array = [];
  
      $data['states'] = [];
      foreach(StateModel::get_pc_states() as $result){
        $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
        ];
      }

      $data['phases'] = $this->report_model->get_phases();
      $data['phase'] = NULL;
      if($request->has('phase')){
        $data['phase'] = $request->phase;
        $request_array[] =  'phase='.$request->phase;
      }

      $data['constituency'] = NULL;
      if($request->has('constituency')){
        $data['constituency'] = $request->constituency;
        $request_array[] =  'constituency='.$request->constituency;
      }

      $data['state'] = NULL;
      if($request->has('state')){
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;
      }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Poll Day Turnout Details';
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

      if($data['state'] && $data['constituency']){
        $get_pc = $this->voting_model->get_pc_detail([
          'const_no' => $data['constituency'],
          'st_code' => $data['state']
        ]);
        if($get_pc){
          $title_array[]  = "PC: ".$get_pc->PC_NAME;
        }
      }

      $data['filter_buttons'] = $title_array;
      //end set title

      $data['action']         = url($this->action).'/state';
      $data['redirect_href']  = url($this->action).'/state';

      $results              = [];
      $total_round_1_m= 0;
            $total_round_1_f= 0;
            $total_round_1_o= 0;
            $total_round_1_t= 0;
            $total_round_2_m= 0;
            $total_round_2_f= 0;
            $total_round_2_o= 0;
            $total_round_2_t= 0;
            $total_round_3_m= 0;
            $total_round_3_f= 0;
            $total_round_3_o= 0;
            $total_round_3_t= 0;
            $total_round_4_m= 0;
            $total_round_4_f= 0;
            $total_round_4_o= 0;
            $total_round_4_t= 0;
            $total_round_5_m= 0;
            $total_round_5_f= 0;
            $total_round_5_o= 0;
            $total_round_5_t= 0;
            $total_round_end_m= 0;
            $total_round_end_f= 0;
            $total_round_end_o= 0;
            $total_round_end_t= 0;
      
          $total_round_1_total   = 0;
          $total_round_2_total   = 0;
          $total_round_3_total   = 0;
          $total_round_4_total   = 0;
          $total_round_5_total    = 0;
          $total_round_end_total  = 0;

          $total_e_male     = 0;
          $total_e_female   = 0;
          $total_e_other    = 0;
          $total_e_total    = 0;

          $total_male     = 0;
          $total_female   = 0;
          $total_other    = 0;
          $total_total    = 0;

      $total_voter_male   = 0;
      $total_voter_female    = 0;
      $total_voter_other    = 0;


      $filter_election = [
        'state_code'    => $data['state'],
        'const_type'    => 'PC'
      ];

      $filter_report = array_merge($filter_election,[
        'pc_no'         => $data['constituency'],
        'phase_id'      => $data['phase'],
      ]);


      $lists_all    = $this->voting_model->get_scrutny_report_ceo($filter_election);
      $lists        = $this->voting_model->get_scrutny_report_ceo($filter_report);

      
      foreach ($lists as $lis) {   

        $const_name   = NULL;
        $const        = $this->commonModel->getpcname($lis->ST_CODE,$lis->PC_NO);
        $filter_data = [
            'st_code'       => $lis->ST_CODE,
            'pc_no'         => $lis->PC_NO,
            'phase'         => $data['phase'],
        ];


        $schedule_object  = $this->voting_model->get_schedule_detail($filter_data);

        
        //total
          $total_round            = $this->voting_model->get_total_round($filter_data);
          $total_round_1_total   += $total_round->round_1_total;
          $total_round_2_total   += $total_round->round_2_total;
          $total_round_3_total   += $total_round->round_3_total;
          $total_round_4_total   += $total_round->round_4_total;
          $total_round_5_total   += $total_round->round_5_total;
          $total_round_end_total   += $total_round->round_end_total;

          $total_voter_male     += $total_round->total_voter_male;
          $total_voter_female   += $total_round->total_voter_female;
          $total_voter_other    += $total_round->total_voter_other;

          $total_elector     = $this->voting_model->get_elector_total($filter_data);
          $total_e_male     += $total_elector->gen_m;
          $total_e_female   += $total_elector->gen_f;
          $total_e_other    += $total_elector->gen_o;
          $total_e_total    += $total_elector->gen_t;
        

          $total_round_1_m += $schedule_object['round_1_m'];
          $total_round_1_f += $schedule_object['round_1_f'];
          $total_round_1_o += $schedule_object['round_1_o'];
          $total_round_1_t += $schedule_object['round_1_t'];
          $total_round_2_m += $schedule_object['round_2_m'];
          $total_round_2_f += $schedule_object['round_2_f'];
          $total_round_2_o += $schedule_object['round_2_o'];
          $total_round_2_t += $schedule_object['round_2_t'];
          $total_round_3_m += $schedule_object['round_3_m'];
          $total_round_3_f += $schedule_object['round_3_f'];
          $total_round_3_o += $schedule_object['round_3_o'];
          $total_round_3_t += $schedule_object['round_3_t'];
          $total_round_4_m += $schedule_object['round_4_m'];
          $total_round_4_f += $schedule_object['round_4_f'];
          $total_round_4_o += $schedule_object['round_4_o'];
          $total_round_4_t += $schedule_object['round_4_t'];
          $total_round_5_m += $schedule_object['round_5_m'];
          $total_round_5_f += $schedule_object['round_5_f'];
          $total_round_5_o += $schedule_object['round_5_o'];
          $total_round_5_t += $schedule_object['round_5_t'];
          $total_round_end_m += $schedule_object['round_end_m'];
          $total_round_end_f += $schedule_object['round_end_f'];
          $total_round_end_o += $schedule_object['round_end_o'];
          $total_round_end_t += $schedule_object['round_end_t']; 

          $total_male   += $schedule_object['total_male'];
          $total_female += $schedule_object['total_female'];
          $total_other  += $schedule_object['total_other'];
          $total_total  += $schedule_object['total']; 

          $results[] = [
            'label'              => $lis->PC_NO.'-'.$lis->PC_NAME,
            'filter'             => implode('&', $request_array),
            'const_no'           => $lis->PC_NO,
            'const_name'         => $lis->PC_NAME,
            'round_1_m'              => $schedule_object['round_1_m'],
            'round_1_f'              => $schedule_object['round_1_f'],
            'round_1_o'              => $schedule_object['round_1_o'],
            'round_1_t'              => $schedule_object['round_1_t'],
            'round_2_m'              => $schedule_object['round_2_m'],
            'round_2_f'              => $schedule_object['round_2_f'],
            'round_2_o'              => $schedule_object['round_2_o'],
            'round_2_t'              => $schedule_object['round_2_t'],
            'round_3_m'              => $schedule_object['round_3_m'],
            'round_3_f'              => $schedule_object['round_3_f'],
            'round_3_o'              => $schedule_object['round_3_o'],
            'round_3_t'              => $schedule_object['round_3_t'],
            'round_4_m'              => $schedule_object['round_4_m'],
            'round_4_f'              => $schedule_object['round_4_f'],
            'round_4_o'              => $schedule_object['round_4_o'],
            'round_4_t'              => $schedule_object['round_4_t'],
            'round_5_m'              => $schedule_object['round_5_m'],
            'round_5_f'              => $schedule_object['round_5_f'],
            'round_5_o'              => $schedule_object['round_5_o'],
            'round_5_t'              => $schedule_object['round_5_t'],
            'round_end_m'            => $schedule_object['round_end_m'],
            'round_end_f'            => $schedule_object['round_end_f'],
            'round_end_o'            => $schedule_object['round_end_o'],
            'round_end_t'            => $schedule_object['round_end_t'], 

            'total_male'             => $schedule_object['total_male'],
            'total_female'           => $schedule_object['total_female'],
            'total_other'            => $schedule_object['total_other'],
            'total'                  => $schedule_object['total'], 

            'href'                   => 'javascript:void(0)',
            'gen_m'             => $total_elector->gen_m,
            'gen_f'             => $total_elector->gen_f,
            'gen_o'             => $total_elector->gen_o,
            'gen_t'             => $total_elector->gen_t,
          ];                      
    }   


    $total_aggregate_elector = $total_e_total;
    $total_aggregate_voter   = $total_round_1_total + $total_round_2_total + $total_round_3_total + $total_round_4_total + $total_round_5_total + $total_round_end_total;

    //total voting
    $data['number_of_voting'] = 0;
    if($total_aggregate_elector > 0){
      $data['number_of_voting'] = round((($total_total*100)/$total_aggregate_elector),1);
    }


    //male percentage
    $data['male_percentage'] = 0;
    if($total_e_male > 0){
      $data['male_percentage'] = round((($total_voter_male*100)/$total_e_male),1);
    }

    $data['female_percentage'] = 0;
    if($total_e_female > 0){
      $data['female_percentage'] = round((($total_voter_female*100)/$total_e_female),1);
    }

    $data['other_percentage'] = 0;
    if($total_e_other > 0){
      $data['other_percentage'] = round((($total_voter_other*100)/$total_e_other),1);
    }

    $data['list_const'] = $lists_all;
    $data['results']    = $results;
    $data['user_data']  = Auth::user();
    $data['from']       = $from_date;
    $data['to']         = $from_to;

    // $data['downlaod_to_excel'] = url($this->action.'/excel').'?'.implode('&', $request_array);
    // $data['downlaod_to_pdf']   = url($this->action.'/pdf').'?'.implode('&', $request_array);

    $data['buttons']    = [];
    $data['buttons'][]  = [
      'name' => 'Export Excel',
      'href' =>  url($this->action.'/export_state_excel').'?'.implode('&', $request_array),
      'target' => true
    ];
    $data['buttons'][]  = [
      'name' => 'Back',
      'href' =>  url($this->action),
      'target' => false
    ];

  

    if($request->has('is_excel')){
      if(isset($title_array) && count($title_array)>0){
        $data['heading_title'] .= "- ".implode(', ', $title_array);
      }
      return $data;
    }

    return view($this->view_path.'.voting.state_list', $data);
    try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }
  }

  public function get_ac_by_pc($id, Request $request){
    
      $data = [];
      $from_date  = NULL;
      $from_to    = NULL;
      
      //first argument must be string, second must be $request object if you want to verify base 64, send that variable in ccode parameter in request object using $request->merge(['ccode' => $somevalue]);
      $request_status = validate_request('',$request);
      if(!$request_status){
        return Redirect::to('logout');
      }
      //end validate request

      $request_array = [];
  
      $data['states'] = [];
      foreach(StateModel::get_pc_states() as $result){
        $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
        ];
      }

      $data['phases'] = $this->report_model->get_phases();
      $data['phase'] = NULL;
      if($request->has('phase')){
        $data['phase'] = $request->phase;
        $request_array[] =  'phase='.$request->phase;
      }

      $data['constituency'] = NULL;
      if($request->has('constituency')){
        $data['constituency'] = $request->constituency;
        $request_array[] =  'constituency='.$request->constituency;
      }

      $data['state'] = NULL;
      if($request->has('state')){
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;
      }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Poll Day Turnout Details';
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
      if($data['state'] && $id){
        $get_pc = $this->voting_model->get_pc_detail([
          'const_no' => $id,
          'st_code' => $data['state']
        ]);
        if($get_pc){
          $title_array[]  = "PC: ".$get_pc->PC_NAME;
        }
      }
      $data['filter_buttons'] = $title_array;
      //end set title

      $data['action']         = url($this->action).'/state/'.$id;
      $data['redirect_href']  = url($this->action).'/state/'.$id;

      $results              = [];
      $total_round_1_m= 0;
            $total_round_1_f= 0;
            $total_round_1_o= 0;
            $total_round_1_t= 0;
            $total_round_2_m= 0;
            $total_round_2_f= 0;
            $total_round_2_o= 0;
            $total_round_2_t= 0;
            $total_round_3_m= 0;
            $total_round_3_f= 0;
            $total_round_3_o= 0;
            $total_round_3_t= 0;
            $total_round_4_m= 0;
            $total_round_4_f= 0;
            $total_round_4_o= 0;
            $total_round_4_t= 0;
            $total_round_5_m= 0;
            $total_round_5_f= 0;
            $total_round_5_o= 0;
            $total_round_5_t= 0;
            $total_round_end_m= 0;
            $total_round_end_f= 0;
            $total_round_end_o= 0;
            $total_round_end_t= 0;
      
          $total_round_1_total   = 0;
          $total_round_2_total   = 0;
          $total_round_3_total   = 0;
          $total_round_4_total   = 0;
          $total_round_5_total    = 0;
          $total_round_end_total  = 0;

          $total_e_male     = 0;
          $total_e_female   = 0;
          $total_e_other    = 0;
          $total_e_total    = 0;

          $total_male     = 0;
          $total_female   = 0;
          $total_other    = 0;
          $total_total    = 0;

      $total_voter_male   = 0;
      $total_voter_female    = 0;
      $total_voter_other    = 0;


      $filter_election = [
        'state_code'    => $data['state'],
        'const_type'    => 'PC'
      ];

      $filter_report = array_merge($filter_election,[
        'pc_no'         => $data['constituency'],
        'phase_id'      => $data['phase'],
      ]);

      $const        = $this->commonModel->getpcname($data['state'],$id);
      if(!$const){
        return Redirect::to('/eci/dashboard');
      }
    
      $lists = $this->voting_model->get_ac_by_pc($filter_report);
      
      foreach ($lists as $lis) { 

        $const_name   = NULL;
        $const        = $this->commonModel->getacname($lis->ST_CODE,$lis->AC_NO);

        $filter_data = [
            'st_code'       => $lis->ST_CODE,
            'ac_no'         => $lis->AC_NO,
            'pc_no'         => $id,
            'phase'         => $data['phase'],
        ];

        $schedule_object  = $this->voting_model->get_schedule_detail_for_ac($filter_data);

        //total
          $total_round        = $this->voting_model->get_total_round_for_ac($filter_data);


          $total_round_1_total   += $total_round->round_1_total;
          $total_round_2_total   += $total_round->round_2_total;
          $total_round_3_total   += $total_round->round_3_total;
          $total_round_4_total   += $total_round->round_4_total;
          $total_round_5_total   += $total_round->round_5_total;
          $total_round_end_total   += $total_round->round_end_total;

          $total_voter_male     += $total_round->total_voter_male;
          $total_voter_female   += $total_round->total_voter_female;
          $total_voter_other    += $total_round->total_voter_other;

          $total_elector     = $this->voting_model->get_elector_total_for_ac($filter_data);
          $total_e_male     += $total_elector->gen_m;
          $total_e_female   += $total_elector->gen_f;
          $total_e_other    += $total_elector->gen_o;
          $total_e_total    += $total_elector->gen_t;
        

          $total_round_1_m += $schedule_object['round_1_m'];
          $total_round_1_f += $schedule_object['round_1_f'];
          $total_round_1_o += $schedule_object['round_1_o'];
          $total_round_1_t += $schedule_object['round_1_t'];
          $total_round_2_m += $schedule_object['round_2_m'];
          $total_round_2_f += $schedule_object['round_2_f'];
          $total_round_2_o += $schedule_object['round_2_o'];
          $total_round_2_t += $schedule_object['round_2_t'];
          $total_round_3_m += $schedule_object['round_3_m'];
          $total_round_3_f += $schedule_object['round_3_f'];
          $total_round_3_o += $schedule_object['round_3_o'];
          $total_round_3_t += $schedule_object['round_3_t'];
          $total_round_4_m += $schedule_object['round_4_m'];
          $total_round_4_f += $schedule_object['round_4_f'];
          $total_round_4_o += $schedule_object['round_4_o'];
          $total_round_4_t += $schedule_object['round_4_t'];
          $total_round_5_m += $schedule_object['round_5_m'];
          $total_round_5_f += $schedule_object['round_5_f'];
          $total_round_5_o += $schedule_object['round_5_o'];
          $total_round_5_t += $schedule_object['round_5_t'];
          $total_round_end_m += $schedule_object['round_end_m'];
          $total_round_end_f += $schedule_object['round_end_f'];
          $total_round_end_o += $schedule_object['round_end_o'];
          $total_round_end_t += $schedule_object['round_end_t']; 

          $total_male   += $schedule_object['total_male'];
          $total_female += $schedule_object['total_female'];
          $total_other  += $schedule_object['total_other'];
          $total_total  += $schedule_object['total']; 

          $results[] = [
            'label'              => $lis->AC_NO.'-'.$lis->AC_NAME,
            'filter'             => implode('&', array_merge($request_array,['ccode' => 'ccode='.base64_encode($lis->CCODE)])),
            'const_no'           => $lis->AC_NO,
            'const_name'         => $lis->AC_NAME,
            'round_1_m'              => $schedule_object['round_1_m'],
            'round_1_f'              => $schedule_object['round_1_f'],
            'round_1_o'              => $schedule_object['round_1_o'],
            'round_1_t'              => $schedule_object['round_1_t'],
            'round_2_m'              => $schedule_object['round_2_m'],
            'round_2_f'              => $schedule_object['round_2_f'],
            'round_2_o'              => $schedule_object['round_2_o'],
            'round_2_t'              => $schedule_object['round_2_t'],
            'round_3_m'              => $schedule_object['round_3_m'],
            'round_3_f'              => $schedule_object['round_3_f'],
            'round_3_o'              => $schedule_object['round_3_o'],
            'round_3_t'              => $schedule_object['round_3_t'],
            'round_4_m'              => $schedule_object['round_4_m'],
            'round_4_f'              => $schedule_object['round_4_f'],
            'round_4_o'              => $schedule_object['round_4_o'],
            'round_4_t'              => $schedule_object['round_4_t'],
            'round_5_m'              => $schedule_object['round_5_m'],
            'round_5_f'              => $schedule_object['round_5_f'],
            'round_5_o'              => $schedule_object['round_5_o'],
            'round_5_t'              => $schedule_object['round_5_t'],
            'round_end_m'            => $schedule_object['round_end_m'],
            'round_end_f'            => $schedule_object['round_end_f'],
            'round_end_o'            => $schedule_object['round_end_o'],
            'round_end_t'            => $schedule_object['round_end_t'], 

            'total_male'             => $schedule_object['total_male'],
            'total_female'           => $schedule_object['total_female'],
            'total_other'            => $schedule_object['total_other'],
            'total'                  => $schedule_object['total'], 

            'href'                   => 'javascript:void(0)',
            'gen_m'             => @$total_elector->gen_m,
            'gen_f'             => @$total_elector->gen_f,
            'gen_o'             => @$total_elector->gen_o,
            'gen_t'             => @$total_elector->gen_t,
          ];                      
    }   



    $total_aggregate_elector = $total_e_total;
    $total_aggregate_voter   = $total_round_1_total + $total_round_2_total + $total_round_3_total + $total_round_4_total + $total_round_5_total + $total_round_end_total;

    //total voting
    $data['number_of_voting'] = 0;
    if($total_aggregate_elector > 0){
      $data['number_of_voting'] = round((($total_total*100)/$total_aggregate_elector),1);
    }


    //male percentage
    $data['male_percentage'] = 0;
    if($total_e_male > 0){
      $data['male_percentage'] = round((($total_voter_male*100)/$total_e_male),1);
    }

    $data['female_percentage'] = 0;
    if($total_e_female > 0){
      $data['female_percentage'] = round((($total_voter_female*100)/$total_e_female),1);
    }

    $data['other_percentage'] = 0;
    if($total_e_other > 0){
      $data['other_percentage'] = round((($total_voter_other*100)/$total_e_other),1);
    }

    $data['list_const'] = [];
    $data['results']    = $results;
    $data['user_data']  = Auth::user();
    $data['from']       = $from_date;
    $data['to']         = $from_to;

    // $data['downlaod_to_excel'] = url($this->action.'/excel').'?'.implode('&', $request_array);
    // $data['downlaod_to_pdf']   = url($this->action.'/pdf').'?'.implode('&', $request_array);

    $data['back_href']         = url($this->action.'/state').'?'.implode('&', array_merge($request_array));

    if($request->has('is_excel')){
      if(isset($title_array) && count($title_array)>0){
        $data['heading_title'] .= "- ".implode(', ', $title_array);
      }
      return $data;
    }

    return view($this->view_path.'.voting.get_ac_by_pc', $data);

    try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }


  public function export_excel(Request $request){
    $export_data = $this->get_data_for_excel('index', $request);

    \Excel::create('poll_turn_out'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
        $excel->sheet('Sheet1', function($sheet) use($export_data) {
          $sheet->mergeCells('A1:AH1');
          $sheet->mergeCells('C2:F2');
          $sheet->mergeCells('G2:J2');
          $sheet->mergeCells('K2:N2');
          $sheet->mergeCells('O2:R2');
          $sheet->mergeCells('S2:V2');
          $sheet->mergeCells('W2:Z2');
          $sheet->mergeCells('AA2:AD2');
          $sheet->mergeCells('AE2:AH2');

          $sheet->cell('A1', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->cell('A2:AH2', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->fromArray($export_data,null,'A1',false,false);
        });
    })->export('xls');

  }

  public function export_state_excel(Request $request){
    $export_data = $this->get_data_for_excel('state', $request);

    \Excel::create('poll_turn_out'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
        $excel->sheet('Sheet1', function($sheet) use($export_data) {
          $sheet->mergeCells('A1:AH1');
          $sheet->mergeCells('C2:F2');
          $sheet->mergeCells('G2:J2');
          $sheet->mergeCells('K2:N2');
          $sheet->mergeCells('O2:R2');
          $sheet->mergeCells('S2:V2');
          $sheet->mergeCells('W2:Z2');
          $sheet->mergeCells('AA2:AD2');
          $sheet->mergeCells('AE2:AH2');

          $sheet->cell('A1', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->cell('A2:AH2', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->fromArray($export_data,null,'A1',false,false);
        });
    })->export('xls');
  }

  public function export_state_ac_excel(Request $request){
    
  }


  public function get_data_for_excel($type = '', $request){
    set_time_limit(6000);
    if($type == 'index'){
      $data = $this->index($request->merge(['is_excel' => 1]));
    }else if($type == 'state'){
      $data = $this->state($request->merge(['is_excel' => 1]));
    }else if($type=='ac'){
      $data = $this->export_state_ac_excel($request->merge(['is_excel' => 1]));
    }else{
      return [];
    }

    $export_data   = [];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['S.No','Name', 'Total Elector','','','','Latest Updated Value','','','','Round1 (Poll Start to 9:00 AM)','','','','Round2 (Poll Start to 11:00 AM)','','','','Round3 (Poll Start to 1:00 PM)','','','','Round4 (Poll Start to 3:00 PM)','','','','Round5 (Poll Start to 5:00 PM)','','','','End of Poll(Poll Start to End)','','','',];
    $export_data[] = ['','' ,'Male','Female','Other', 'Total',   'Male','Female','Other', 'Total',  'Male','Female','Other', 'Total', 'Male','Female','Other', 'Total',   'Male','Female','Other', 'Total',  'Male','Female','Other', 'Total', 'Male','Female','Other', 'Total','Male','Female','Other', 'Total'];
    foreach ($data['results'] as $result) {
      $export_data[] = [
            'const_no'           => $result['const_no'],
            'label'              => $result['label'],

            'gen_m'             => $result['gen_m'],
            'gen_f'             => $result['gen_f'],
            'gen_o'             => $result['gen_o'],
            'gen_t'             => $result['gen_t'],
      
            'total_male'             => $result['total_male'],
            'total_female'           => $result['total_female'],
            'total_other'            => $result['total_other'],
            'total'                  => $result['total'], 

            'round_1_m'              => $result['round_1_m'],
            'round_1_f'              => $result['round_1_f'],
            'round_1_o'              => $result['round_1_o'],
            'round_1_t'              => $result['round_1_t'],

            'round_2_m'              => $result['round_2_m'],
            'round_2_f'              => $result['round_2_f'],
            'round_2_o'              => $result['round_2_o'],
            'round_2_t'              => $result['round_2_t'],

            'round_3_m'              => $result['round_3_m'],
            'round_3_f'              => $result['round_3_f'],
            'round_3_o'              => $result['round_3_o'],
            'round_3_t'              => $result['round_3_t'],

            'round_4_m'              => $result['round_4_m'],
            'round_4_f'              => $result['round_4_f'],
            'round_4_o'              => $result['round_4_o'],
            'round_4_t'              => $result['round_4_t'],

            'round_5_m'              => $result['round_5_m'],
            'round_5_f'              => $result['round_5_f'],
            'round_5_o'              => $result['round_5_o'],
            'round_5_t'              => $result['round_5_t'],

            'round_end_m'            => $result['round_end_m'],
            'round_end_f'            => $result['round_end_f'],
            'round_end_o'            => $result['round_end_o'],
            'round_end_t'            => $result['round_end_t'], 



            
      ];
    }

    return $export_data;

  }

}  // end class