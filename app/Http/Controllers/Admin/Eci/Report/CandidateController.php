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
use App\models\Admin\EndOfPollModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\PcModel;
use App\models\Admin\AcModel;
use App\models\Admin\CandidateModel;
use App\models\Admin\CandidateNominationModel;

//current

class CandidateController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/report/candidate';
  public $action_pc     = 'eci/report/voting/candidate-pc';
  public $view_path     = "admin.pc.eci";

  public function get_candidates(Request $request){
    
      $data = [];
      $request_array = []; 

      $data['state'] = NULL;
      if($request->has('state')){
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;
      }

      $data['pc_no'] = NULL;
      if($request->has('pc_no')){
        $data['pc_no'] = $request->pc_no;
        $request_array[] = 'pc_no='.$request->pc_no;
      }

      if(\Auth::user()->role_id == '4'){
        $data['state']    = \Auth::user()->st_code;
        $request_array[]  = 'state='.\Auth::user()->st_code;
        $this->action_state  = 'pcceo/report/candidate';
        $this->action_pc     = 'pcceo/report/voting/candidate-pc';
      }

      if(\Auth::user()->role_id == '18'){
        $data['state']    = \Auth::user()->st_code;
        $request_array[]  = 'state='.\Auth::user()->st_code;
        $data['pc_no']    = \Auth::user()->pc_no;
        $request_array[]  = 'pc_no='.\Auth::user()->pc_no;
        $this->action_state  = 'ropc/report/candidate';
        $this->action_pc     = 'ropc/report/voting/candidate-pc';
      }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'List of Nominated Candidates';
      if(isset($from_date) && isset($from_to)){
        $data['heading_title'] .= ' between '.date('d-M-Y',strtotime($from_date)).' to '.date('d-M-Y',strtotime($from_to));
      }

      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }

      $data['filter_buttons'] = $title_array;

      $states = StateModel::get_states(); 
      $data['states'] = [];
      foreach($states as $result){
        if(\Auth::user()->role_id=='4' || \Auth::user()->role_id=='18' ){
          if(\Auth::user()->st_code == $result->ST_CODE){
            $data['states'][] = [
              'code' => base64_encode($result->ST_CODE),
              'name' => $result->ST_NAME,
            ];
          }
        }else{
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


      $data['action']         = url($this->action_state);

      $results                = [];
      $filter_election = [
        'state'         => $data['state'],
        'pc_no'         => $data['pc_no']
      ];

      $data['pcs']      = [];
      $pcs = PcModel::get_pcs();
      foreach ($pcs as $key => $pc) {

        if(\Auth::user()->role_id=='4' || \Auth::user()->role_id == '18'){
          if(\Auth::user()->st_code == $pc->st_code && \Auth::user()->role_id == '18' && \Auth::user()->pc_no == $pc->pc_no){
            $data['pcs'][] = [
              'pc_no' => $pc->pc_no,
              'pc_name' => $pc->pc_name,
              'st_code' => $pc->st_code
            ];
          }else if(\Auth::user()->st_code == $pc->st_code && \Auth::user()->role_id == '4'){
            $data['pcs'][] = [
              'pc_no' => $pc->pc_no,
              'pc_name' => $pc->pc_name,
              'st_code' => $pc->st_code
            ];
          }
        }else{
          $data['pcs'][] = [
              'pc_no' => $pc->pc_no,
              'pc_name' => $pc->pc_name,
              'st_code' => $pc->st_code
          ];
        }
      }

      if($data['state'] && $data['pc_no']){
        $results_object = CandidateModel::get_candidates($filter_election);
        foreach ($results_object as $result) {
          $text_status    = '';
          $status_array   = [];
          $status_results = CandidateNominationModel::get_nomination_status([
            'candidate_id'  => $result['candidate_id'],
            'pc_no'         => $data['pc_no'],
            'state'         => $data['state'],
          ]);



          $status_result  = [];
          foreach ($status_results as $status_res) {
            if($status_res['application_status'] == '6' && $status_res['finalaccepted'] == '1'){
              $status_result[] = 'final';
            }else{
              $status_result[] = $status_res->application_status;
            }
          }
        
          if(in_array('final',$status_result)){
            $text_status = 'contesting';
          }else if(in_array('5',$status_result)){
            $text_status = 'Withdrawn';
          }else if(in_array('6',$status_result)){
            $text_status = 'Accepted';
          }else if(in_array('4',$status_result)){
            $text_status = 'Rejected';
          }else{

          }
          foreach ($status_result as $status_key => $status_r) {
            if(in_array('final',$status_result)){
              $text_status = 'contesting';
            }else if(in_array('5',$status_result)){
              $text_status = 'Withdrawn';
            }else if(in_array('4',$status_result)){
              $text_status = 'Rejected';
            }else if(in_array('6',$status_result)){
              $text_status = 'Accepted';
            }else{

            }
            $status_array[]     = $text_status;
          }

          if($text_status == 'contesting'){
            $status_array = ['contesting'];
          }
          
          $results[] = [
            'candidate_id'      => $result->candidate_id,
            'new_srno'          => $result->new_srno,
            'name'              => $result->cand_name,
			'gender'              => $result->cand_gender,
            'total_nomination'  => count($status_results),
            'status'            => implode(', ', $status_array),
            'final_status'      => $text_status,
          ];
        }   
      }

      $data['results']    =   $results;
      $data['user_data']  =   Auth::user();
      $data['heading_title_with_all'] = $data['heading_title'];

      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }

      return view($this->view_path.'.report.candidate.candidates', $data);

     try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }

 

}  // end class