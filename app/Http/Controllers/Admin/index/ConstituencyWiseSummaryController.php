<?php 
namespace App\Http\Controllers\Admin\index;
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
use App\models\Admin\ConstituencyWiseSummary;
use App\models\Admin\CandidateModel;

//current

class ConstituencyWiseSummaryController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/indexcardview/ConstituencyWiseSummary';
  public $action_pc     = 'eci/indexcardview/ConstituencyWiseSummary/state';
  public $view_path     = "admin.pc.eci";

  public function __construct(){
    $this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->middleware(function ($request, $next) {
        if(Auth::user() && Auth::user()->role_id=='26'){
          $this->action_state  = str_replace('eci','eci-agent',$this->action_state);
          $this->action_pc     = str_replace('eci','eci-agent',$this->action_pc);
          $this->action_ac     = str_replace('eci','eci-agent',$this->action_ac);
		  
        }
        return $next($request);
    });
  }

  public function ConstituencyWiseSummary(Request $request){

    if(Auth::user()->role_id == '27'){
      $this->action_state  = 'eci-index/indexcardview/ConstituencyWiseSummary';
      $this->action_pc     = 'eci-index/indexcardview/ConstituencyWiseSummary/state';
    }

    if(Auth::user()->role_id == '4'){
      $this->action_state  = 'pcceo/indexcardview/ConstituencyWiseSummary';
      $this->action_pc     = 'pcceo/indexcardview/ConstituencyWiseSummary/state';
    }
    
      $data = [];
      $request_array = [];

      $data['state'] = NULL;

      if($request->has('state')){
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;
      }

      //set title
      $title_array  = [];
      $data['heading_title'] = '7 - Constituency (PC) Wise Summary';

      
   
      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
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
              'st_code' => $result->ST_CODE,
              'name' => $result->ST_NAME,
          ];
        }


        if(Auth::user()->role_id == '7' || Auth::user()->role_id == '27'){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name' => $result->ST_NAME,
          ];
        }

      }
      


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
 
      $filter_election = [
        'state'         =>   $data['state'],
      ];



       foreach ($data['states'] as $state_result) {

         $statewise_results = [];
         $statewise_name_only =[];

          $filter_election = [
            'state'         => $state_result['st_code'],
          ];
       
        
        $state_ac_count          = 0;
        $state_ps_count          = 0;
        $state_electors          = 0;
        $state_avg_elector_in_ps = 0;
        $state_nominated         = 0;
        $state_contested         = 0;
        $state_forfieted         = 0;
        $state_voter             = 0;
        $state_voter_turn        = 0;

        $object           = ConstituencyWiseSummary::get_reports($filter_election);

        $statewise_name_only = [
                'is_state'              => 0,
                'st_name'               => $state_result['name'],
                'constno'               => '',
                'const_name'            => '',
                'total_const'           => '',
                'total_ps'              => '',
                'total_electors'        => '',
                'avg_elector_in_ps'     => '',
                'nominated'             => '',
                'contested'             => '',
                'forefeited'            => '',
                'total_voter'           => '',
                'voterturnout'          => '',
                
              ];

        $results[] = $statewise_name_only;

        foreach ($object as $result) {

          $individual_filter_array = [];
          $individual_filter_array['state'] = 'state='.base64_encode($result->st_code);
          $individual_filter    = implode('&', $individual_filter_array);

          $state_name = '';
          $state_object = StateModel::get_state_by_code($result->st_code);
          if($state_object){
            $state_name = $state_object['ST_NAME'];
          }





        

          $total_vote_in_pc       = '';
          $total_vote_in_pc       = ConstituencyWiseSummary::get_all_voter_in_pc($result->st_code,$result->constno);
          //GETTING TOTAL NOMINATED, CONTESTED AND FOREFITED DEPOSITE
          $total_candidate_values = CandidateModel::get_count_nominated($result->st_code,$result->constno);
          //forefeited CANDIDATE
          $forefeited_candidate   = ConstituencyWiseSummary::get_all_forefeited_cand($result->st_code,$result->constno);


          if($result->total_electors > 0 && $total_vote_in_pc->total_voter > 0){ 

              $state_ac_count          += $result->total_const;
              $state_ps_count          += $result->total_ps;
              $state_electors          += $result->total_electors;
              $state_avg_elector_in_ps += $result->avg_elector_in_ps;
              $state_nominated         += $total_candidate_values['nom_total'];
              $state_contested         += $total_candidate_values['cont_total'];
              $state_forfieted         += $forefeited_candidate['0']->forefeited_total;
              $state_voter             += $total_vote_in_pc->total_voter;
              


               $results[] = [
                'is_state'              => 0,
                'st_name'               => '',
                'constno'               => $result->constno,
                'const_name'            => $result->const_name,
                'total_const'           => $result->total_const,
                'total_ps'              => $result->total_ps,
                'total_electors'        => $result->total_electors,
                'avg_elector_in_ps'     => ($result->total_ps > 0) ? ROUND($result->total_electors/$result->total_ps,0): 0,
                'nominated'             => $total_candidate_values['nom_total'],
                'contested'             => $total_candidate_values['cont_total'],
                'forefeited'            => $forefeited_candidate['0']->forefeited_total,
                'total_voter'           => $total_vote_in_pc->total_voter,
                'voterturnout'          => ROUND($total_vote_in_pc->total_voter/$result->total_electors*100,2),
                
              ];     

            }
        }

       
         


        $object_state  = ConstituencyWiseSummary::get_reports([
            'state'             => $state_result['st_code'],
        ]);
        
        //STATE CODE STARTS
        if($data['state']){
            $subtitle = "Grand Total";
            $is_state = 0;
          }else{
            $subtitle = "State-Total";
            $is_state = 1;
          }
         


          if(count($object_state)>0){
            $statewise_results = [
                'st_name'               => '',
                'constno'               => $subtitle,
                'is_state'              => $is_state,
                'const_name'            => '',
                'total_const'           => $state_ac_count,
                'total_ps'              => $state_ps_count,
                'total_electors'        => $state_electors,
                'avg_elector_in_ps'     => ($state_ps_count > 0) ? ROUND($state_electors/$state_ps_count,0):0,
                'nominated'             => $state_nominated,
                'contested'             => $state_contested,
                'forefeited'            => $state_forfieted,
                'total_voter'           => $state_voter,
                'voterturnout'          => ROUND($state_voter/$state_electors*100,2)
                
            ];
          }

          $results[] = $statewise_results;
		  
     }


     $data['results']    =   $results;
     $data['user_data']  =   Auth::user();

       $data['heading_title_with_all'] = $data['heading_title'];
  /*    
       if(Auth::user()->designation == 'CEO' && !$request->has('is_excel')){
            return $data;
       }
*/
      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }

      return view($this->view_path.'.ConstituencyWiseSummary.ConstituencyWiseSummary', $data);

     try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }

 
  public function ConstituencyWiseSummaryExcel(Request $request){

    set_time_limit(6000);
    $data = $this->ConstituencyWiseSummary($request->merge(['is_excel' => 1]));

    $export_data = [];

     $TotalAc          = 0;
     $TotalPs          = 0;
     $TotalElector     = 0;
     $TotalAvgElector  = 0;
     $TotalNominated   = 0;
     $TotalContested   = 0;
     $TotalForefeited  = 0;
     $TotalVoter       = 0;


    $export_data[] = [$data['heading_title']];


    $export_data[] = ['PC No','PC Name','No Of AC Segments','No Of Polling Station','Electors','Avg. No. of Electors Per PS','Nominations','Contestants','Forefeited Deposits','Voters','Voters Turn Out (%)'];


    foreach ($data['results'] as $lis) {

       if($lis['is_state']==1){

         $TotalAc          +=$lis['total_const'];
         $TotalPs          +=$lis['total_ps'];
         $TotalElector     +=$lis['total_electors'];
         $TotalAvgElector  +=$lis['avg_elector_in_ps'];
         $TotalNominated   +=$lis['nominated'];
         $TotalContested   +=$lis['contested'];
         $TotalForefeited  +=$lis['forefeited'];
         $TotalVoter       +=$lis['total_voter'];

        }


        if($lis['is_state'] == 0 && empty($lis['constno'])){
                  
                $export_data[] = [
                                   $lis['st_name'],
                                  ];
        }else{


          $export_data[] = [
            $lis['constno'],
            $lis['const_name'],
            $lis['total_const'],
            $lis['total_ps'],
            $lis['total_electors'],
            $lis['avg_elector_in_ps'],
            $lis['nominated'],
            $lis['contested'], 
            $lis['forefeited'],
            $lis['total_voter'],
            $lis['voterturnout'],
          ];
       }

      
    }
     
     
    
    if(Auth::user()->role_id == '7' || Auth::user()->role_id =='27'){

      $TotalVote = ROUND($TotalVoter/$TotalElector*100,2);
	  $TotalAvgElector = ROUND($TotalElector/$TotalPs,0);

       $totalvalues = array('Total','',$TotalAc,$TotalPs,$TotalElector,$TotalAvgElector,$TotalNominated,$TotalContested,$TotalForefeited,$TotalVoter,$TotalVote);

      array_push($export_data,$totalvalues);

    }

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
        $excel->sheet('Sheet1', function($sheet) use($export_data) {
          $sheet->mergeCells('A1:K1');
          $sheet->cell('A1', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->fromArray($export_data,null,'A1',false,false);

          $sheet->cell('A619', function($cells) {
            $cells->setValue('Disclaimer');
            $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
          });

          

          $sheet->getStyle('A620')->getAlignment()->setWrapText(true);
          $sheet->setSize('A620', 25,30);



          $sheet->mergeCells("A620:K620");
          $sheet->cell('A620', function($cells) {
          $cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
          $cells->setFont(array('name' => 'Times New Roman','size' => 10));
          });
        });


          

          
          
    })->export('xls');

  }

  public function ConstituencyWiseSummaryPdf(Request $request){
    $data = $this->ConstituencyWiseSummary($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
	
	
   //  $pdf = \App::make('dompdf.wrapper');
			// $pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf = PDF::loadView($this->view_path.'.ConstituencyWiseSummary.ConstituencyWiseSummaryPdf',$data);


                 if(verifyreport(7)){
        
                  $file_name = 'ConstituencyWiseSummary'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/7/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '7',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }



  


}  // end class