<?php 
    namespace App\Http\Controllers\turnout;
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
    use App\models\TurnoutModel; 

class VoterturnoutController extends Controller {
  
  public $base    = 'turnout';
  public $folder  = 'turnout';
  public $action_state    = 'turnout/state-wise-details';
 // public $action_state  = 'eci/report/voting/end-of-poll';
  public $view_path = "turnout";

  public function __construct(){
      $this->turnoutModel=new TurnoutModel;
  }

  public function index(Request $request){
      
      $data = [];
      $request_array = []; 
      
      $data['phase'] = NULL;
      $data['election_id']=NULL;

      $data['state'] = NULL;
      $data['election_id']=1;

      if($request->has('state')){
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;

        $filter_data = [
            'state'         => base64_encode($result->ST_CODE),
            'phase'         =>NULL,
            'election_id'   => 1,
            'group_by'      => 'pc_no'
          ];
      }
    else{
       $filter_data = [
            'state'         =>NULL,
            'phase'         => NULL,
            'election_id'   => 1,
            'group_by'      => 'state'
          ];
    }
      //set title
      $title_array  = [];
      $data['heading_title'] = 'Voter turnout Details';
       
       
      
      $data['filter_buttons'] = $title_array;

      
      $data['states'] = [];
      

      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
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
     
      $object_states = TurnoutModel::get_turnout_report($filter_data);
   // dd($object_states);
          $total_electors_male=0;
          $total_electors_female=0;
          $total_electors_other=0;
          $total_electors_total=0;
          $total_electors_service=0;
          $total_grand_total=0;
          $total_voter_male=0;
          $total_voter_female=0;
          $total_voter_other=0;
          $total_voter_total=0;
          $total_evm_vote=0;
          $total_postal_vote=0;
          $total_migrate_votes=0;
          $total_total_vote=0;
     

      foreach ($object_states as $result) {
  
          $newfilter_data = [
            'state'         => $result->st_code,
            'phase'         => NULL,
            'election_id'   => 1,
            'group_by'      => 'state',
            'order_by'      => 'state'
          ];

          $individual_filter_array = [];
          if($data['election_id']){
             $individual_filter_array['election_id'] = 'election_id='.$data['election_id'];
            }
          $individual_filter_array['state'] = 'state='.base64_encode($result->st_code);
          $individual_filter    = implode('&', $individual_filter_array);

          //get total electors
          // $object_elector  = TurnoutModel::get_total_elector($newfilter_data);
          $object_vote  = TurnoutModel::get_turnout_votes($newfilter_data);
         //  dd($object_vote);
          
          $results[] = [
            'label'               => $result->st_name,
            'st_name'             => $result->st_name,
            'filter'              => $individual_filter,
            "pc_no"               => $result->pc_no,
            "pc_name"             => $result->pc_name,
            "st_code"             => $result->st_code,
            "electors_male"       => $result->electors_male,
            "electors_female"     => $result->electors_female,
            "electors_other"      => $result->electors_other,
            "electors_total"      => $result->electors_total,
            "electors_service"    => $result->electors_service,
            "grand_total"         => $result->grand_total,
            "voter_male"          => $result->voter_male,
            "voter_female"        => $result->voter_female,
            "voter_other"         => $result->voter_other,
            "voter_total"         => $result->total,
            "evm_votes"            => $object_vote->evm_vote,
            "postal_vote"         => $object_vote->postal_vote,
            "migrate_votes"       => $object_vote->migrate_votes,
            "total_votes"          => $object_vote->total_vote,
            "href"                => url($this->action_state)."?".$individual_filter
          ];   
          
       

      }   // end of foreach 
    // dd( $results);
      $total_filter = [
        'election_id'  =>1,
        'group_by'   => 'national',
        'order_by'   => NULL,
      ];

      //calculate total
      $object_votes =  TurnoutModel::get_turnout_votes($total_filter);
      $object_elector   = TurnoutModel::get_total_elector($total_filter);
      
      $object_voter    = TurnoutModel::get_total_voters($total_filter);
      

  
      if(count($object_elector)>0){
       // $result           = $total_object[0];
        

        $data['totals'] = [
            'label'               => 'Total',
            'filter'              => '',
            "pc_no"               => $result->pc_no,
            "pc_name"             => $result->pc_name,
            "st_code"             => $result->st_code,
            
            "total_electors_male"       => $object_elector->total_male,
            "total_electors_female"     => $object_elector->total_female,
            "total_electors_other"      => $object_elector->total_other,
            "total_electors_total"      => $object_elector->total,
            "total_electors_service"   => $object_elector->total_service,
            "total_grand_total"         => $object_elector->grand_total,
            "total_voter_male"          => $object_voter->voter_male,
            "total_voter_female"        => $object_voter->voter_female,
            "total_voter_other"         => $object_voter->voter_other,
            "total_voter_total"         => $object_voter->total,
            "total_evm_votes"           =>  $object_votes->evm_vote,
            "total_postal_vote"         =>  $object_votes->postal_vote,
            "total_migrate_votes"       =>  $object_votes->migrate_votes,
            "total_total_votes"         =>  $object_votes->total_vote,
            "href"                => ''
        ]; 

         
      }
  
      $data['results']    =   $results;
    
 
       $data['heading_title_with_all'] = $data['heading_title'];
       

      // if($request->has('is_excel')){
      //   if(isset($title_array) && count($title_array)>0){
      //     $data['heading_title'] .= "- ".implode(', ', $title_array);
      //   }
      //   return $data;
      // }

      // if($request->has('is_excel')){
      //   if(isset($title_array) && count($title_array)>0){
      //     $data['heading_title'] .= "- ".implode(', ', $title_array);
      //   }
      //   return $data;
      // }

      return view($this->view_path.'.display-turnout', $data);

    
  }

public function report_pc(Request $request){
       //dd($request);
       //-----------------------------------------------
       $data = [];
       $request_array = []; 
       $data['phase'] = NULL;
       $data['election_id']=NULL;
       $data['state'] = NULL;

      if($request->has('state')){
         $data['state'] = base64_decode($request->state);   //valid a state is exist in the current filter state
         $request_array[] = 'state='.$request->state;
      }
     if($request->has('election_id')){
         $data['election_id'] = $request->election_id;   //valid a state is exist in the current filter election id
         $request_array[] = 'election_id='.$request->election_id;
      }
      
 
        $filter_data = [
            'state'         => base64_decode($request->state),
            'phase'         =>NULL,
            'election_id'   =>$data['election_id'],
            'group_by'      => 'pc_no',
            'order_by'      => 'pc_no'
          ];
     
      //set title
      $title_array  = [];
      $data['heading_title'] = 'Voter turnout State Wise Details ';
       
       
      
      $data['filter_buttons'] = $title_array;

      
      $data['states'] = [];
       
      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
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
     
      $object_states = TurnoutModel::get_turnout_report($filter_data);
     //dd($object_states);
          $total_electors_male=0;
          $total_electors_female=0;
          $total_electors_other=0;
          $total_electors_total=0;
          $total_electors_service=0;
          $total_grand_total=0;
          $total_voter_male=0;
          $total_voter_female=0;
          $total_voter_other=0;
          $total_voter_total=0;
          $total_evm_vote=0;
          $total_postal_vote=0;
          $total_migrate_votes=0;
          $total_total_vote=0;
        //==================================================================================================
      
        foreach ($object_states as $result) {
  
          
          $individual_filter_array = [];
          if($data['election_id']){
             $individual_filter_array['election_id'] = 'election_id='.$data['election_id'];
            }
          $individual_filter_array['state'] = 'state='.base64_encode($result->st_code);
          $individual_filter    = implode('&', $individual_filter_array);

           $object_votes =  TurnoutModel::get_turnout_votes($filter_data);
          
          //$object_elector   = TurnoutModel::get_total_elector($total_filter);
          
          ///$object_voter    = TurnoutModel::get_total_voters($total_filter);
          
          $results[] = [
            'label'               => $result->st_name,
            'st_name'             => $result->st_name,
            'filter'              => $individual_filter,
            "pc_no"               => $result->pc_no,
            "pc_name"             => $result->pc_name,
            "st_code"             => $result->st_code,
            "electors_male"       => $result->electors_male,
            "electors_female"     => $result->electors_female,
            "electors_other"      => $result->electors_other,
            "electors_total"      => $result->electors_total,
            "electors_service"    => $result->electors_service,
            "grand_total"         => $result->grand_total,
            "voter_male"          => $result->voter_male,
            "voter_female"        => $result->voter_female,
            "voter_other"         => $result->voter_other,
            "voter_total"         => $result->total,
            "evm_votes"            => $object_votes->evm_vote,
            "postal_vote"         => $object_votes->postal_vote,
             "migrate_votes"       => $object_votes->migrate_votes,
            "total_votes"          => $object_votes->total_vote,
            "href"                => url($this->action_state)."?".$individual_filter
          ];   
     
      }   // end of foreach 

       //dd( $results);
       
      $total_filter = [
          'state'         => base64_decode($request->state),
          'phase'         =>  NULL,
          'election_id'   => $data['election_id'],
          'group_by'      => 'state',
          'order_by'      => 'state'
        ];

      //calculate total
      $object_votes =  TurnoutModel::get_turnout_votes($total_filter);
      $object_elector   = TurnoutModel::get_total_elector($total_filter);
      
      $object_voter    = TurnoutModel::get_total_voters($total_filter);
      

  
      if(count($object_elector)>0){
       // $result           = $total_object[0];
        

        $data['totals'] = [
            'label'               => 'Total',
            'filter'              => '',
            "pc_no"               => $result->pc_no,
            "pc_name"             => $result->pc_name,
            "st_code"             => $result->st_code,
            
            "total_electors_male"       => $object_elector->total_male,
            "total_electors_female"     => $object_elector->total_female,
            "total_electors_other"      => $object_elector->total_other,
            "total_electors_total"      => $object_elector->total,
            "total_electors_service"   => $object_elector->total_service,
            "total_grand_total"         => $object_elector->grand_total,
            "total_voter_male"          => $object_voter->voter_male,
            "total_voter_female"        => $object_voter->voter_female,
            "total_voter_other"         => $object_voter->voter_other,
            "total_voter_total"         => $object_voter->total,
            "total_evm_votes"           =>  $object_votes->evm_vote,
            "total_postal_vote"         =>  $object_votes->postal_vote,
            "total_migrate_votes"       =>  $object_votes->migrate_votes,
            "total_total_votes"         =>  $object_votes->total_vote,
            "href"                      => ''
        ]; 

         
      }

      $data['results']    =   $results;
      $data['heading_title_with_all'] = $data['heading_title'];

      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }
      return view($this->view_path.'.display-pcwise-turnout', $data);
     
  }   // report pc

  //done her

   

}  // end class