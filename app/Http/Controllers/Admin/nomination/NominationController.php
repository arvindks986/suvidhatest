<?php namespace App\Http\Controllers\Admin\Nomination;
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
use App\models\Admin\{StateModel,AcModel,PhaseModel, DistrictModel,PcModel};
use App\models\Admin\CandidateModel;
use App\models\Admin\CandidateNominationModel;
use App\Http\Controllers\Admin\Common\CommonController;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

//current

class NominationController extends Controller {
  
  public $base          = '';
  public $folder        = '';
  public $view_path     = "admin.nomination";

  public function get_candidates(Request $request){

     $user_detail=Auth::user();

      $data                   = [];
      $data['heading_title']  = "List of Nominated Candidate";
      $request_filter         = CommonController::get_request_filter($request);

      //dd($request);
      $ac_no                = $request_filter['ac_no'];
      $st_code              = $request_filter['st_code'];
      $dist_no              = $request_filter['dist_no'];
      $ps_no                = $request_filter['ps_no'];
     // $phase_no             = $request_filter['phase_no'];


  $is_activated           = NULL;
  $is_activated2           = NULL;
  if($request->has('election_type_id')){
  $is_activated = $request->election_type_id;
  }
  
  if($request->has('election_phase')){
  $is_activated2 = $request->election_phase;
  }


      $filter = [
        'st_code' => $st_code,
        'ac_no'   => $ac_no,
        'dist_no' => $dist_no,
        'ps_no'   => $ps_no,
        //'phase_no' => $phase_no
  ];
  
   $title_array = [
        'st_code' => $st_code,
  ];
  
  
  

      $request_array = []; 
      $title_array = [];
      


      $data['filter_buttons'] = $title_array;
      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

//dd($request->election_type_id);


      //buttons
      $data['buttons']    = [];
     $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  CommonController::generate_url('list-of-nomination').'?st_code='.@$request->st_code.'&dist_no='.@$request->dist_no.'&ac_no='.@$request->ac_no.'&election_type_id='.@$request->election_type_id.'&election_phase='.@$request->election_phase.'&is_excel=yes',
        'target' => true
      ];
    
    
       $data['buttons'][]  = [
        'name' => 'Contesting Candidates Export Excel',
        'href' =>  CommonController::generate_url('list-of-nomination').'?st_code='.@$request->st_code.'&dist_no='.@$request->dist_no.'&ac_no='.@$request->ac_no.'&election_type_id='.@$request->election_type_id.'&election_phase='.@$request->election_phase.'&is_excel_contesting=yes',
        'target' => true
      ];
    
    
    
      $results            = [];
      $filter_election = [
        'state'         => $filter['st_code'],
        'dist_no'       => $filter['dist_no'],
        'ac_no'         => $filter['ac_no'],
       // 'phase_no'      => $filter['phase_no'],
        'election_type_id' => $request->election_type_id,
        'election_phase'   => $request->election_phase,
      ];
    
    if($request->has('is_excel_contesting')){
      $filter_election['status'] =  '6';      
    }
    

     // if($filter['st_code']){
        $results_object = CandidateModel::get_candidates($filter_election);
    
    
    
        foreach ($results_object as $result) {

         // dd($result);
          $text_status    = '';
          $status_array   = [];
          $status_results = CandidateNominationModel::get_nomination_status([
            'candidate_id'  => $result['candidate_id'],
            'ac_no'         => $filter['ac_no'],
            'state'         => $filter['st_code'],
            //'phase_no'      => $filter['phase_no'],
      'election_type_id' => $request->election_type_id,
      'election_phase'   => $request->election_phase,
          ]);


      $sql = DB::table('candidate_nomination_detail as cnd')
      ->join('m_party as mp','cnd.party_id','mp.CCODE')
      ->where('candidate_id',$result['candidate_id'])
      ->where('application_status', '!=','11');
      if($request->has('is_excel_contesting')){
        $sql->select('mp.PARTYNAME as party');
        $sql->where("cnd.application_status",'6');
        $sql->where("cnd.finalaccepted",'1');
        $sql->where("cnd.symbol_id",'!=','200');
        $sql->where("cnd.party_id",'!=','1180');
        $sql->groupBy("cnd.party_id");
      }else{
        $sql->select(DB::raw('group_concat(mp.PARTYNAME SEPARATOR " , ") as party'));
      }
      $sql = $sql->first();



          $status_result  = [];
          $status_text    = [];
          foreach ($status_results as $status_res) {
            if($status_res['application_status'] == '6' && $status_res['finalaccepted'] == '1'){
              $status_result[]  = 'final';
              $status_text[]    = 'Contesting';
            }else{
              $status_result[] = $status_res->application_status;
        
        
        
        

              /* if(in_array('5',$status_result)){
                $status_text[] = 'Withdrawn';
              }else if(in_array('6',$status_result)){
                $status_text[] = 'Accepted';
              }else if(in_array('4',$status_result)){
                $status_text[] = 'Rejected';
              }else{

              } */
        
        if($status_res->application_status == '5'){
                $status_text[] = 'Withdrawn';
              }else if($status_res->application_status == '6'){
                $status_text[] = 'Accepted';
              }else if($status_res->application_status == '4'){
                $status_text[] = 'Rejected';
              }else{

              }
        
        
            }
          }

          if(in_array('final',$status_result)){
            $text_status = 'Contesting';
          }else if(in_array('5',$status_result)){
            $text_status = 'Withdrawn';
          }else if(in_array('6',$status_result)){
            $text_status = 'Accepted';
          }else if(in_array('4',$status_result)){
            $text_status = 'Rejected';
          }else{

          }
          //dd($result['st_code']);

          $state_name = '';
          $state_object = StateModel::get_state_by_code($result['st_code']);
          if($state_object){
            $state_name = $state_object['ST_NAME'];
          }


          $dist_name = '';
          $dist_object = DistrictModel::get_district([
            'st_code' => $result['st_code'],
            'dist_no' => $result['dist_no']
          ]);
          if($dist_object){
            $dist_name = $dist_object['dist_name'];
          }

          
//dd($result);
          $ac_name = '';
          $ac_object = PcModel::get_record([
            'state'   => $result['st_code'],
            'pc_no'   => $result['pc_no'],
          ]);
          //dd($ac_object);
          if($ac_object){
            $ac_name = $ac_object['pc_name'];
          }

          // $phase_name = '';
          // $phase_object = getallschedule_ceofilter([
          // 'state'   => $result['st_code']

          // ]);

          // if($phase_object){
          //  // dd($phase_object);
          //   $phase_name = $phase_object[0]->SCHEDULEID;
          // }
        // dd($dist_name);

          $results[] = [
            'st_name'           => $state_name,
            'dist_name'         => $result['dist_no'].'-'.$dist_name,
            'ac_name'           => $result['pc_no'].'-'.$ac_name,
            //'phase_no'          => $phase_name,
            'candidate_id'      => $result->candidate_id,
            'new_srno'          => $result->new_srno,
            'gender'          => $result->cand_gender,
            'name'              => $result->cand_name,
            'total_nomination'  => count($status_results),
            'status'            => implode(', ', $status_text),
            'final_status'      => $text_status,
            'party'      => $sql->party,
            'symbol'      => $result['SYMBOL_DES'],
            'criminal_inced'      => $result->is_criminal,
          ];
        }   
      //}

//dd($results);


      $data['results']    =   $results;
    
   // dd($results);

      //form filters
      $data['filter_action'] = CommonController::generate_url("list-of-nomination");
      $form_filter_array = [
        'st_code'     => true,
        'dist_no'     => true,
        'ac_no'       => true, 
        'ps_no'       => false, 
        'designation' => false,
        //'phase_no'    => true
      ];
      $form_filters = CommonController::get_form_filters($form_filter_array, $request);
    
     //activate filter
     
     
      
$is_activated_value   = [];
  
  $is_activated_value[] = [
    'election_phase'  => 'All Phase',
    'type_filter'    => '',
  ];
  
  if($user_detail['role_id']=='4')
  {
      $getallsche = getallschedule_ceofilter($user_detail['st_code']);
  }else{

     $getallsche = getallschedule();
  }
 
 // dd($getallsche);exit;
   $k=1;
  foreach($getallsche as $each_data){
   
    $incrment=$k++;
   $is_activated_value[] = [
    'election_phase'  => $incrment.'-'.'Phase',
    'type_filter'    => $each_data->SCHEDULEID,
  ]; 
  }
    
  
  $election_type = [];
  foreach ($is_activated_value as $iterate_activate) {
    $is_active = false;
    if($is_activated2 == $iterate_activate['type_filter']){
      $is_active = true;
    }
    $election_type[] = [
      'id'    => $iterate_activate['type_filter'],
      'name'      => $iterate_activate['election_phase'],
      'active'  => $is_active
    ];
  }
  
  
  array_unshift($form_filters,[
    'id'      => 'election_phase',
    'name'    => 'Election Phase',
    'results' => $election_type
  ]);
    

  $is_activated_value   = [];
  
  
  
if($user_detail['role_id']=='4')
  {
      $getelection_type = getelection_type($user_detail['st_code']);
  
 $is_activated_value[] = [
    'election_type_id'  => 'All Election Type',
    'type_filter'    => '',
  ];
  
  foreach($getelection_type as $each_type){
     if($each_type->ELECTION_TYPEID=='1'){
     $is_activated_value[] = [
    'election_type_id'  => 'PC-GENERAL',
    'type_filter'    => $each_type->ELECTION_TYPEID,
  ];
}
  if($each_type->ELECTION_TYPEID=='2'){
     $is_activated_value[] = [
    'election_type_id'  => 'PC-BYE',
    'type_filter'    => $each_type->ELECTION_TYPEID,
  ];
}
 
  }
} else{



$is_activated_value[] = [
    'election_type_id'  => 'All Election Type',
    'type_filter'    => '',
  ];
  $is_activated_value[] = [
    'election_type_id'  => 'PC-GENERAL',
    'type_filter'    => '1',
  ];
  $is_activated_value[] = [
    'election_type_id'  => 'PC-BYE',
    'type_filter'    => '2',
  ];






  
}
  
  
  $election_type = [];
  foreach ($is_activated_value as $iterate_activate) {
    $is_active = false;
    if($is_activated == $iterate_activate['type_filter']){
      $is_active = true;
    }
    $election_type[] = [
      'id'    => $iterate_activate['type_filter'],
      'name'      => $iterate_activate['election_type_id'],
      'active'  => $is_active
    ];
  }
  
  array_unshift($form_filters,[
    'id'      => 'election_type_id',
    'name'    => 'Election Type',
    'results' => $election_type
  ]);
    

    
      $data['form_filters']   = $form_filters;

      $data['user_data']              = Auth::user();
      $data['heading_title_with_all'] = $data['heading_title'];

      if($request->has('is_excel')){
      
     
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
    

    $export_data = [];
    $headings[] = [$data['heading_title']];
    
    $export_data[] = ['State' ,'PC Name', 'Candidate Nmae','Gender','Total Nomination','Party','Symbol','Is_Criminal', 'Status','Final Status'];

     foreach ($data['results'] as $lis) {
      $export_data[] = [
      $lis['st_name'],
      //$lis['dist_name'],
      $lis['ac_name'],
      $lis['name'],
      $lis['gender'],
      $lis['total_nomination'],
      $lis['party'],
      $lis['symbol'],
       $lis['criminal_inced'],
      $lis['status'],
      $lis['final_status'],
     
      ];
    }



    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
    
    
    //  \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //  $excel->sheet('Sheet1', function($sheet) use($export_data) {       
    //    $sheet->fromArray($export_data,null,'A1',false,false);
    //  });
    // })->export('xls');
    
    
      }
    
    
      if($request->has('is_excel_contesting')){
      
     $data['heading_title'] = 'List Of Contesting Candidates';
       
      
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
    
    $export_data = [];
    $headings[] = [$data['heading_title']];
    
    $export_data[] = ['State' ,'PC Name', 'Candidate Nmae','Gender','Party','Symbol','Is Criminal' ,'Final Status'];

     foreach ($data['results'] as $lis) {
      $export_data[] = [
      $lis['st_name'],
     // $lis['dist_name'],
      $lis['ac_name'],
      $lis['name'],
      $lis['gender'],
      $lis['party'],
      $lis['symbol'],
      $lis['criminal_inced'],
      $lis['final_status'],
        
      ];
    }


    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
    
      }

      return view($this->view_path.'.list-of-nomination', $data);

      try{
     
   }catch(\Exception $e){
      return Redirect::to('/officer-login');
    }

  }

 

}  // end class