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

class WomenParticipationController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/indexcardview/WomenParticipation';
  public $action_pc     = 'eci/indexcardview/WomenParticipation/state';
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

  public function WomenParticipation(Request $request){


    if(Auth::user()->role_id == '27'){
      $this->action_state  = 'eci-index/indexcardview/WomenParticipation';
      $this->action_pc     = 'eci-index/indexcardview/WomenParticipation/state';
    }

    if(Auth::user()->role_id == '4'){
      $this->action_state  = 'pcceo/indexcardview/WomenParticipation';
      $this->action_pc     = 'pcceo/indexcardview/WomenParticipation/state';
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
      $data['heading_title'] = '24 - Participation of Women Candidates in Poll';

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

      $results = [];
 
      $filter_election = [
        'state'         =>   $data['state'],
      ];


      foreach ($data['states'] as $state_result) {

         $statewise_results = [];
         $results2[] = [];
         $results3[] = [];

          $filter_election = [
            'state'         => $state_result['st_code'],
          ];


        



          $state_data = "SELECT r.st_code, r.cand_category , q.st_name,(CASE WHEN q.fdfemale IS NULL THEN '0' ELSE q.fdfemale END) fdfemale, (CASE WHEN q.total_pcs IS NULL THEN '0' ELSE q.total_pcs END) total_pcs , (CASE WHEN q.total_const1 IS NULL THEN '0' ELSE q.total_const1 END) total_const1   FROM  st_code_category_mis r 
LEFT OUTER JOIN
(SELECT B.total_pcs,C.st_code,C.st_name,C.category,C.total_const1,C.fdfemale FROM (SELECT  st_code ,SUM(total_const) AS total_pcs,st_name FROM (SELECT
TEMP1.ST_CODE AS st_code,TEMP1.ST_NAME AS st_name,TEMP1.CATEGORY AS category,TEMP1.total_pc AS total_const,
TEMP1.fdfemale AS fdfemale
FROM
(
SELECT TEMP.*,
(SELECT COUNT(PC_TYPE) AS CC FROM m_pc MM , m_election_details med WHERE MM.ST_CODE=TEMP.ST_CODE AND MM.PC_TYPE=TEMP.category 
AND med.st_code = MM.`ST_CODE` AND med.`CONST_NO` = MM.`PC_NO` AND med.`CONST_TYPE` = 'PC' AND med.`election_status` != '0'
GROUP BY TEMP.ST_CODE,TEMP.category LIMIT 1) AS total_pc
FROM (
SELECT cp.st_code,M.PC_TYPE AS category,MP.ST_NAME,C.cand_gender,cp.pc_no,
SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'female'
GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdfemale,


SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'third'
GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdthird,

SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes
FROM `counting_pcmaster` AS cp1
WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code
GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fd

FROM  counting_pcmaster cp ,m_state  MP ,m_pc M,candidate_personal_detail  C
WHERE cp.candidate_id NOT IN(SELECT candidate_id FROM winning_leading_candidate AS w1
WHERE w1.pc_no = cp.pc_no AND w1.st_code = cp.st_code)
AND cp.party_id != 1180
AND cp.pc_no=M.PC_NO
AND MP.ST_CODE = cp.st_code
AND M.ST_CODE=MP.ST_CODE
AND C.cand_gender IN ('male','female','third')
AND C.candidate_id = cp.candidate_id
AND M.PC_NO=cp.pc_no
GROUP BY MP.ST_CODE,M.PC_TYPE
)TEMP

)TEMP1) A GROUP BY 1) B

JOIN

(SELECT
TEMP1.ST_CODE AS st_code,TEMP1.ST_NAME AS st_name,TEMP1.CATEGORY AS category,TEMP1.total_pc AS total_const1,
TEMP1.fdfemale AS fdfemale
FROM
(
SELECT TEMP.*,
(SELECT COUNT(PC_TYPE) AS CC FROM m_pc MM  WHERE MM.ST_CODE=TEMP.ST_CODE AND MM.PC_TYPE=TEMP.category
GROUP BY TEMP.ST_CODE,TEMP.category LIMIT 1) AS total_pc
FROM (
SELECT cp.st_code,M.PC_TYPE AS category,MP.ST_NAME,C.cand_gender,cp.pc_no,
SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'female'
GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdfemale,


SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'third'
GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdthird,

SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes
FROM `counting_pcmaster` AS cp1
WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code
GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fd
FROM  counting_pcmaster cp ,m_state  MP ,m_pc M,candidate_personal_detail  C
WHERE cp.candidate_id NOT IN(SELECT candidate_id FROM winning_leading_candidate AS w1
WHERE w1.pc_no = cp.pc_no AND w1.st_code = cp.st_code)
AND cp.party_id != 1180
AND cp.pc_no=M.PC_NO
AND MP.ST_CODE = cp.st_code
AND M.ST_CODE=MP.ST_CODE
AND C.cand_gender IN ('male','female','third')
AND C.candidate_id = cp.candidate_id
AND M.PC_NO=cp.pc_no
GROUP BY MP.ST_CODE,M.PC_TYPE
)TEMP

)TEMP1) C ON B.st_code=C.st_code GROUP BY C.st_code,C.category,C.total_const1,C.fdfemale) q  ON q.st_code=r.st_code AND q.category=r.cand_category WHERE r.st_code='".$state_result['st_code']."' ORDER BY r.st_code";

        $state_data_result = DB::select($state_data);


       
        /*$winning_women = "SELECT r.st_code,(CASE WHEN q.win_women IS NULL THEN '0' ELSE q.win_women END) win_women ,  r.cand_category FROM  st_code_category_mis r LEFT OUTER JOIN (SELECT w.st_code,c.cand_category,COUNT(*) AS win_women FROM winning_leading_candidate w RIGHT JOIN candidate_personal_detail c ON w.candidate_id=c.candidate_id WHERE w.status ='1' AND  c.cand_gender = 'female' GROUP BY w.st_code, c.cand_category  ORDER BY  w.st_code) q ON q.st_code=r.st_code AND q.cand_category=r.cand_category WHERE r.st_code='".$state_result['st_code']."' ORDER BY r.st_code";*/
		
		
		$winning_women = "SELECT s.st_code,s.cand_category,IFNULL(win_women,0) win_women FROM st_code_category_mis s  LEFT JOIN (SELECT w.st_code, pc_type, COUNT(IF(cand_gender='female',w.candidate_id,NULL)) win_women FROM winning_leading_candidate w JOIN candidate_personal_detail p ON w.candidate_id=p.candidate_id RIGHT JOIN m_pc q ON w.st_code=q.st_code AND w.pc_no=q.pc_no GROUP BY w.st_code,pc_type) m ON s.st_code=m.st_code AND s.cand_category=m.pc_type WHERE s.st_code='".$state_result['st_code']."'";

        $elected_women = DB::select($winning_women);

       $cand_data = CandidateModel::get_count_women_by_status_category($state_result['st_code']);


        $contestants_total  = @$cand_data[0]['cont_female'] + @$cand_data[1]['cont_female'] + @$cand_data[2]['cont_female'];

        $elected_total      = @$elected_women[0]->win_women + @$elected_women[1]->win_women + @$elected_women[2]->win_women;

        $forfieted_total     = @$state_data_result[0]->fdfemale + @$state_data_result[1]->fdfemale + @$state_data_result[2]->fdfemale;

      
 
                if(@$elected_women[0]->win_women > 0 && $contestants_total > 0){

                  $over_total_women1 = ROUND(@$elected_women[0]->win_women/$contestants_total*100,2);
                }else{
                    
                     $over_total_women1 = 0;
                }

                if(@$elected_women[0]->win_women > 0 && @$state_data_result[0]->total_pcs > 0){

                  $over_total_seats1 = ROUND(@$elected_women[0]->win_women/@$state_data_result[0]->total_pcs*100,2);
                }else{
                    
                     $over_total_seats1 = 0;
                }
                
                $results[] = [
                
                'is_state'           => 0,
                'st_name'            => $state_result['name'],
                'st_code'            => $state_result['st_code'],
                'seats'              => @$state_data_result[0]->total_pcs,
                'category'           => @$state_data_result[0]->cand_category,
                'total_const'        => @$state_data_result[0]->total_const1,
                'fdfemale'           => @$state_data_result[0]->fdfemale,
                'cont_female'        => @$cand_data[0]['cont_female'],
                'elected_women'      => @$elected_women[0]->win_women,
                'over_total_women'   => $over_total_women1,
                'over_total_seats'   => $over_total_seats1,
                
                ];

                if(@$elected_women[1]->win_women > 0 && $contestants_total > 0){

                  $over_total_women2 = ROUND(@$elected_women[1]->win_women/$contestants_total*100,2);
                }else{
                    
                     $over_total_women2 = 0;
                }

                if(@$elected_women[1]->win_women > 0 && @$state_data_result[0]->total_pcs > 0){

                  $over_total_seats2 = ROUND(@$elected_women[1]->win_women/@$state_data_result[0]->total_pcs*100,2);
                }else{
                    
                     $over_total_seats2 = 0;
                }

                $results2 = [
                
                'is_state'           => 0,
                'st_name'            => '',
                'st_code'            => '',
                'seats'              => 0,
                'category'           => @$state_data_result[1]->cand_category,
                'total_const'        => @$state_data_result[1]->total_const1,
                'fdfemale'           => @$state_data_result[1]->fdfemale,
                'cont_female'        => @$cand_data[1]['cont_female'],
                'elected_women'      => @$elected_women[1]->win_women,
                'over_total_women'   => $over_total_women2,
                'over_total_seats'   => $over_total_seats2,
                

                ];


                if(@$elected_women[2]->win_women > 0 && $contestants_total > 0){

                  $over_total_women3 = ROUND(@$elected_women[2]->win_women/$contestants_total*100,2);
                }else{
                    
                     $over_total_women3 = 0;
                }

                if(@$elected_women[2]->win_women > 0 && @$state_data_result[0]->total_pcs > 0){

                  $over_total_seats3 = ROUND(@$elected_women[2]->win_women/@$state_data_result[0]->total_pcs*100,2);
                }else{
                    
                     $over_total_seats3 = 0;
                }

                $results3 = [

                'is_state'           => 0,
                'st_name'            => '',
                'st_code'            => '',
                'seats'              => 0,
                'category'           => @$state_data_result[2]->cand_category,
                'total_const'        => @$state_data_result[2]->total_const1,
                'fdfemale'           => @$state_data_result[2]->fdfemale,
                'cont_female'        => @$cand_data[2]['cont_female'],
                'elected_women'      => @$elected_women[2]->win_women,
                'over_total_women'   => $over_total_women3,
                'over_total_seats'   => $over_total_seats3,
              
                ];

                if($elected_total > 0 && $contestants_total > 0){

                  $over_total_women_state = ROUND($elected_total/$contestants_total*100,2);
                }else{
                    
                     $over_total_women_state = 0;
                }

                if($elected_total > 0 && @$state_data_result[0]->total_pcs > 0){

                  $over_total_seats_state = ROUND($elected_total/@$state_data_result[0]->total_pcs*100,2);
                }else{
                    
                     $over_total_seats_state = 0;
                }


                $statewise_results = [

                'is_state'           => 1,
                'st_name'            => 'State Total',
                'st_code'            => '',
                'seats'              => 0,
                'category'           => '',
                'total_const'        => '',
                'fdfemale'           => $forfieted_total,
                'cont_female'        => $contestants_total,
                'elected_women'      => $elected_total,
                'over_total_women'   => $over_total_women_state,
                'over_total_seats'   => $over_total_seats_state,
                

                ];
             
             $results[] = $results2;
             $results[] = $results3;
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

      return view($this->view_path.'.WomenParticipation.WomenParticipation', $data);

     try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }

 
  public function WomenParticipationExcel(Request $request){

    set_time_limit(6000);
    $data = $this->WomenParticipation($request->merge(['is_excel' => 1]));

    $export_data = [];

      $TotalPcs             = 0;
        $TotalContested       = 0;
        $TotalElected         = 0;
        $TotalFd              = 0;
        $OvertTotalWomenState = 0;  
        $OvertTotalSeatsState = 0; 
 

    
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['', '' ,'', 'No. Of Women','','','% of Elected Women'];

    $export_data[] = ['State /UT ', 'Seats','Catagory','Contestants','Elected','Deposits Forfeited','Over Total Women Candidates in the State','Over total seats in State/UT'];


    foreach ($data['results'] as $result) {
      

       $TotalPcs                 +=$result['seats'];

       if($result['is_state']==1){
        
         $TotalContested         +=$result['cont_female'];
         $TotalElected           +=$result['elected_women'];
         $TotalFd                +=$result['fdfemale'];
         $OvertTotalWomenState   +=$result['over_total_women'];
         $OvertTotalSeatsState   +=$result['over_total_seats'];

        }


   
       $export_data[] = [
         $result['st_name'],
         ($result['seats'])?$result['seats']:'0',
         $result['category'] ,
         ($result['cont_female'] > 0) ? $result['cont_female'] : '=(0)',
         ($result['elected_women'] > 0) ? $result['elected_women'] : '=(0)',
         ($result['fdfemale'] > 0) ? (int)$result['fdfemale'] : '=(0)',
         ($result['over_total_women'] > 0) ? $result['over_total_women'] : '=(0)',
         ($result['over_total_seats'] > 0) ? $result['over_total_seats'] : '=(0)',
       ];
    }
     

		if($TotalContested > 0){
			$per1 = ROUND($TotalElected/$TotalContested*100,2);
		}else{
			$per1 = 0;
		}
		
		if($TotalPcs > 0){
			$per2 = ROUND($TotalElected/$TotalPcs*100,2);
		}else{
			$per2 = 0;
		}

       $totalvalues = array('Total',$TotalPcs,'',$TotalContested,$TotalElected,$TotalFd,$per1,$per2);

      array_push($export_data,$totalvalues);


    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
        $excel->sheet('Sheet1', function($sheet) use($export_data) {
          $sheet->mergeCells('A1:L1');
          $sheet->mergeCells('D2:F2');
           $sheet->mergeCells('G2:H2');
          $sheet->cell('A1', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->fromArray($export_data,null,'A1',false,false);



          

         
          $sheet->cell('A151', function($cells) {
            $cells->setValue('Disclaimer');
            $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
          });

          

          $sheet->getStyle('A152')->getAlignment()->setWrapText(true);
          $sheet->setSize('A152', 25,40);



          $sheet->mergeCells("A152:H152");
          $sheet->cell('A152', function($cells) {
          $cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
          $cells->setFont(array('name' => 'Times New Roman','size' => 10));
          });
        });
    })->export('xls');

  }

  public function WomenParticipationPdf(Request $request){
    $data = $this->WomenParticipation($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView($this->view_path.'.WomenParticipation.WomenParticipationPdf',$data);

    if(verifyreport(24)){
        
                  $file_name = 'WomenParticipation'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/24/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '24',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }



  


}  // end class