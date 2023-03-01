<?php

namespace App\Http\Controllers\IndexCardReports\StatisticalReportPC;
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
use App\models\Admin\CandidateModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\StateModel;
use Excel;
use App;
use App\Classes\xssClean;

ini_set("memory_limit","1500M");
        set_time_limit('240');
        ini_set("pcre.backtrack_limit", "10000000");
		
class StatisticalReportController extends Controller
{


	public function __construct(){
       $this->middleware(['auth:admin', 'auth']);
       $this->middleware(function (Request $request, $next) {
           if (!\Auth::check()) {
               return redirect('login')->with(Auth::logout());
           }

           $user = Auth::user();
           switch ($user->role_id) {
               case '7':
                   $this->middleware('eci');
                   break;
               case '4':
                   $this->middleware('ceo');
                   break;
               case '18':
                   $this->middleware('ro');
                   break;
			  case '27':
                   $this->middleware('eci_index');
                   break;   
				   
               default:
                   $this->middleware('eci');
           }
           return $next($request);
       });
 
        $this->middleware('adminsession');
        $this->commonModel = new commonModel();
       // $this->ceomodel = new ACCEOModel();
       // $this->acceoreportModel = new ACCEOReportModel();
        $this->xssClean = new xssClean;
   }


    public function index(Request $request)
    {
         $dt = Carbon::now();
         $user = Auth::user();

		 $data = DB::select('SELECT a.partyname,contested,won,vote AS evm_vote,total_vote,
			(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fd FROM `counting_pcmaster` as cp
			 WHERE a.party_id = cp.party_id AND cp.party_id != 1180 AND  a.party_id != (select lead_cand_partyid from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code) group by cp.party_id) as fd
			FROM
			(SELECT party_id,partyname,COUNT(DISTINCT p.candidate_id)contested,
			 COUNT(DISTINCT w.`candidate_id`)won FROM `counting_pcmaster` p
			JOIN `winning_leading_candidate` w ON p.party_id=w.`lead_cand_partyid`
			JOIN m_party q ON p.party_id=q.ccode
			WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)a JOIN
			(SELECT partyname,SUM(evm_vote)vote,SUM(total_vote)total_vote FROM `counting_pcmaster` m
			JOIN m_party q ON m.party_id=q.ccode WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)b
			ON a.partyname=b.partyname');

		 $totalElectors  = DB::select("SELECT sum(`electors_total`) as total_electors FROM `electors_cdac`");

		 $totalVotes  = DB::select('SELECT sum(`total_vote`) as totalVotes FROM `counting_pcmaster`');

		 //echo '<pre>';print_r($data);die;

         return view('IndexCardReports/StatisticalReports/Vol1/performanceofnatiionalparties', compact('data','totalElectors','totalVotes','user_data'));
    }

  //
	// public function performanceofnatiionalpartiespdf()
  //   {
  //
	// 	$user = Auth::user();
  //          $uid=$user->id;
  //          $d=$this->commonModel->getunewserbyuserid($user->id);
  //          $d=$this->commonModel->getunewserbyuserid($uid);
  //          $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
  //
  //         $sched=''; $search='';
  //         $status=$this->commonModel->allstatus();
  //         if(isset($ele_details)) {  $i=0;
  //           foreach($ele_details as $ed) {
  //              $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
  //              $const_type=$ed->CONST_TYPE;
  //            }
  //         }
  //         $session['election_detail'] = (array)$ele_details[0];
  //         $session['election_detail']['st_code'] = $user->st_code;
  //         $session['election_detail']['st_name'] = $user->placename;
  //      // echo "<pre>"; print_r($session); die;
  //      $election_detail = $session['election_detail'];
  //      $user_data = $d;
  //
  //       $listofsuccessfullcondidate = array();
  //
  //
  //
  //
	// 	 $data = DB::select('SELECT a.partyname,contested,won,vote AS evm_vote,total_vote,
	// 		(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fd FROM `counting_pcmaster` as cp
	// 		 WHERE a.party_id = cp.party_id AND a.party_id != (select lead_cand_partyid from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code) group by cp.party_id) as fd
	// 		FROM
	// 		(SELECT party_id,partyname,COUNT(DISTINCT p.candidate_id)contested,
	// 		 COUNT(DISTINCT w.`candidate_id`)won FROM `counting_pcmaster` p
	// 		JOIN `winning_leading_candidate` w ON p.party_id=w.`lead_cand_partyid`
	// 		JOIN m_party q ON p.party_id=q.ccode
	// 		WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)a JOIN
	// 		(SELECT partyname,SUM(evm_vote)vote,SUM(total_vote)total_vote FROM `counting_pcmaster` m
	// 		JOIN m_party q ON m.party_id=q.ccode WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)b
	// 		ON a.partyname=b.partyname');
  //
	// 	 $totalElectors  = DB::select('SELECT sum(`electors_total`) as total_electors FROM `electors_cdac` WHERE year = 2019');
  //
  //
	// 	 $totalVotes  = DB::select('SELECT sum(`total_vote`) as totalVotes FROM `counting_pcmaster`');
  //
	// 	$pdf = PDF::loadView('IndexCardReports/StatisticalReports.Vol2.performanceofnationalparties-pdf', compact('data','totalElectors','totalVotes','user_data'));
  //       return $pdf->download('performance-of-national-parties-pdf.pdf');
  //
  //   }

    public function performanceSate()
    {
      $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $d=$this->commonModel->getunewserbyuserid($uid);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          $sched=''; $search='';
          $status=$this->commonModel->allstatus();
          if(isset($ele_details)) {  $i=0;
            foreach($ele_details as $ed) {
               $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               $const_type=$ed->CONST_TYPE;
             }
          }
          $session['election_detail'] = array();
       // echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;

     $select = array('m.PARTYNAME','m.PARTYABBRE' ,'ms.ST_NAME','ic.st_code','m.CCODE','ms.ST_CODE'
                ,DB::raw('sum(counting.candidate_id) as contested')
                //,DB::raw('count(cond.id) as won')
                ,DB::raw('sum(counting.evm_vote) as Securedvotes')
                ,DB::raw('sum(counting.postal_vote) as postal')
                ,DB::raw('sum(ic.electors_total) as electors')
                ,DB::raw('sum(counting.total_vote) as voters'));
                 $data = DB::table('candidate_nomination_detail as cnominated')
                ->select($select)
                ->join('m_party as m', 'cnominated.party_id', '=', 'm.CCODE')
                 ->join('electors_cdac as ic', function($query) {
                    $query->on('ic.st_code', 'cnominated.st_code');
                })
                 ->join('counting_pcmaster as counting', function($query) {
                    $query->on('counting.st_code', 'cnominated.st_code');
                })
                ->join('m_state as ms', 'ms.ST_CODE', 'ic.st_code')
                // ->join('cand_cont_ic as cond', function($querya) {
                //     $querya->on('cond.con_cand_id', 'cnominated.nom_id');
                // })
                ->where('m.PARTYTYPE', 'S')
                ->groupby('cnominated.party_id','cnominated.st_code')
                //->skip(0)->take(80)
                ->get()->toarray();








echo "<pre>"; print_r($data);die;
                $arraydata = array();
                $totalcontested = 0;
                $i=0;
                $stcode = '';

//
                foreach($data as $rowsdata){

                    $arraydata[$rowsdata->CCODE]['partyabbr'] = $rowsdata->PARTYABBRE;
                    $arraydata[$rowsdata->CCODE]['partyname'] = $rowsdata->PARTYNAME;

                    $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['statename'] = $rowsdata->ST_NAME;
                    $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['contested'] = $rowsdata->contested;
                    $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['won'] = $rowsdata->won;
                    $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['df'] = 'DF NA';
                    $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['Securedvotes'] = $rowsdata->Securedvotes;

                    if($rowsdata->electors !=0){
                  $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['poledvotespercent'] = round($rowsdata->Securedvotes/$rowsdata->electors*100,2);

                   }else{ $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['poledvotespercent'] = '0%';
                   }

                   if($rowsdata->voters !=0){
                   $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['totalelectors'] = round($rowsdata->Securedvotes/$rowsdata->electors*100,2);

                   }else{ $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['totalelectors'] = '0%'; }
                    $arraydata[$rowsdata->CCODE]['totalcontested'][] =  $rowsdata->contested;
                    $arraydata[$rowsdata->CCODE]['won'][] =  $rowsdata->won;
                    $arraydata[$rowsdata->CCODE]['Securedvotes'][] =  $rowsdata->Securedvotes;

                    if($rowsdata->electors !=0){
                  $arraydata[$rowsdata->CCODE]['totalpercentvote'][] = round($rowsdata->Securedvotes/$rowsdata->electors*100,2);

                   }else{ $arraydata[$rowsdata->CCODE]['totalpercentvote'][] = '0%';
                   }

                   if($rowsdata->voters !=0){
                   $arraydata[$rowsdata->CCODE]['totalpercentelectors'][] = round($rowsdata->Securedvotes/$rowsdata->electors*100,2);
                   }else{ $arraydata[$rowsdata->CCODE]['totalpercentelectors'][] = '0%'; }
                $i++;}
                return view('IndexCardReports/StatisticalReports/Vol2/performanceofstateparties',compact('arraydata','user_data'));

    }


    public function performanceSatepfd()
    {
     $select = array('m.PARTYNAME','m.PARTYABBRE' ,'ms.ST_NAME','ic.st_code','m.CCODE','ms.ST_CODE'
                ,DB::raw('sum(ic.c_nom_co_t) as contested')
                ,DB::raw('count(cond.id) as won')
              //,DB::raw('sum(cond.total_valid_vote) as securedvote')
                ,DB::raw('sum(cond.vote_count) as Securedvotes')
                ,DB::raw('sum(cond.postal_vote_count) as postal')
                ,DB::raw('sum(ic.e_all_t) as electors')
                ,DB::raw('sum(ic.total_valid_votes) as voters'));

        $data = DB::table('candidate_nomination_detail as cnominated')
                ->select($select)
                ->distinct()
                ->join('m_party as m', 'cnominated.party_id', '=', 'm.CCODE')
                 ->join('t_pc_ic as ic', function($query) {
                    $query->on('ic.st_code', 'cnominated.st_code');
                })
                ->join('m_state as ms', 'ms.ST_CODE', 'ic.st_code')
                ->join('cand_cont_ic as cond', function($querya) {
                    $querya->on('cond.con_cand_id', 'cnominated.nom_id');
                })
                ->where('m.PARTYTYPE', 'S')
                ->groupby('cnominated.party_id','cnominated.st_code')
                //->skip(0)->take(80)
                ->get()->toarray();

                $arraydata = array();
                $totalcontested = 0;
                $i=0;
                $stcode = '';

//
                foreach($data as $rowsdata){
                    $arraydata[$rowsdata->CCODE]['partyabbr'] = $rowsdata->PARTYABBRE;
                    $arraydata[$rowsdata->CCODE]['partyname'] = $rowsdata->PARTYNAME;

                    $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['statename'] = $rowsdata->ST_NAME;
                    $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['contested'] = $rowsdata->contested;
                    $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['won'] = $rowsdata->won;
                    $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['df'] = 'DF NA';
                    $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['Securedvotes'] = $rowsdata->Securedvotes;

                    if($rowsdata->electors !=0){
                  $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['poledvotespercent'] = round($rowsdata->Securedvotes/$rowsdata->electors*100,2);

                   }else{ $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['poledvotespercent'] = '0%';
                   }
                   if($rowsdata->voters !=0){
                   $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['totalelectors'] = round($rowsdata->Securedvotes/$rowsdata->electors*100,2);

                   }else{ $arraydata[$rowsdata->CCODE]['partydata'][$rowsdata->ST_CODE]['totalelectors'] = '0%'; }
                    $arraydata[$rowsdata->CCODE]['totalcontested'][] =  $rowsdata->contested;
                    $arraydata[$rowsdata->CCODE]['won'][] =  $rowsdata->won;
                    $arraydata[$rowsdata->CCODE]['Securedvotes'][] =  $rowsdata->Securedvotes;

                    if($rowsdata->electors !=0){
                  $arraydata[$rowsdata->CCODE]['totalpercentvote'][] = round($rowsdata->Securedvotes/$rowsdata->electors*100,2);

                   }else{ $arraydata[$rowsdata->CCODE]['totalpercentvote'][] = '0%';
                   }


                   if($rowsdata->voters !=0){
                   $arraydata[$rowsdata->CCODE]['totalpercentelectors'][] = round($rowsdata->Securedvotes/$rowsdata->electors*100,2);

                   }else{ $arraydata[$rowsdata->CCODE]['totalpercentelectors'][] = '0%'; }

                $i++;}

$pdf = PDF::loadView('IndexCardReports/StatisticalReports.Vol2.performanceofstateparties-pdf', compact('arraydata'));
        return $pdf->download('performance-of-state-parties-pdf.pdf');

                 //return view('StatisticalReports/Vol2/performanceofstateparties',compact('arraydata'));
    }


    public function indexpdf(){

        $select = array('m.PARTYNAME'
                ,DB::raw('sum(ic.c_nom_co_t) as contested')
                ,DB::raw('count(cond.id) as won')
                ,DB::raw('sum(cond.total_valid_vote) as securedvote')
                ,DB::raw('sum(cond.vote_count) as Securedvotes')
                ,DB::raw('sum(cond.postal_vote_count) as postal')
                ,DB::raw('sum(ic.e_all_t) as electors')
                ,DB::raw('sum(ic.total_valid_votes) as voters')
                );
        $data = DB::table('m_party as m')
                ->select($select)
                ->distinct()
               ->join('candidate_nomination_detail as cnominated', 'cnominated.party_id', '=', 'm.CCODE')
                ->join('t_pc_ic as ic',function($query){
                 $query->on('ic.st_code','cnominated.st_code');
                })
                ->join('cand_cont_ic as cond',function($querya){
                 $querya->on('cond.con_cand_id','cnominated.nom_id');
                })
                ->where('m.PARTYTYPE','N')
                ->groupby('m.PARTYNAME')
                //->skip(0)->take(80)
                ->get();
           //dd($data);

//                $db = DB::table('t_pc_ic')
//                ->select(DB::raw('sum(t_pc_ic.e_all_t) as electors'), DB::raw('sum(t_pc_ic.total_valid_votes) as voters'))
//               ->get();
//
$db = DB::table('m_party as m')
                 ->select(DB::raw('sum(ic.e_all_t) as electors'), DB::raw('sum(ic.total_valid_votes) as voters'))
               ->join('candidate_nomination_detail as cnominated', 'cnominated.party_id', '=', 'm.CCODE')
                ->join('t_pc_ic as ic',function($query){
                 $query->on('ic.st_code','cnominated.st_code');
                })
                ->where('m.PARTYTYPE','S')
                ->get();
            // return view('StatisticalReports/Vol1/performanceofnatiionalparties', compact('data','db'));
        $pdf = PDF::loadView('IndexCardReports/StatisticalReports.Vol1.performanceofnatiionalparties-pdf', compact('data','db'));
        return $pdf->download('performance-of-national-parties-pdf.pdf');
    }

       public function successfullcondidate(Request $request) {

        $user_data = Auth::user(); 

    $successfullcondidate = DB::select("SELECT m.st_code,`m`.`PC_TYPE`, `m`.`PC_NAME`, `m`.`PC_NO`, SUM(total_vote) as TotalVote, `winn`.`st_name`, `cpd`.`cand_name` as lead_cand_name, `cpd`.`cand_category` as cand_category, `winn`.`lead_party_abbre`, `symbol`.`SYMBOL_DES`, `winn`.`margin`, `winn`.`trail_total_vote`, `winn`.`st_code`
      FROM winning_leading_candidate AS winn INNER JOIN m_pc AS m ON m.st_code = winn.st_code AND m.pc_no = winn.pc_no INNER JOIN candidate_nomination_detail AS cond ON cond.candidate_id = winn.candidate_id JOIN candidate_personal_detail AS cpd ON cpd.candidate_id = winn.candidate_id AND cond.application_status = 6 AND finalaccepted = 1 INNER JOIN counting_pcmaster AS counting ON counting.st_code = winn.st_code AND winn.pc_no = counting.pc_no INNER JOIN m_symbol AS symbol ON cond.symbol_id = symbol.SYMBOL_NO GROUP BY m.st_code,m.pc_no order by `winn`.`st_name` asc");

                       //echo "<pre>";print_r($successfullcondidate);die;
     foreach($successfullcondidate as $key=>$listofsuccessfulldata){

     $arraydata[$listofsuccessfulldata->st_name]['state'] = $listofsuccessfulldata->st_name;
     $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Pc_Name'] = $listofsuccessfulldata->PC_NAME;
     $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['PC_TYPE'] = $listofsuccessfulldata->PC_TYPE;
     $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['cand_category'] = strtoupper($listofsuccessfulldata->cand_category);
     $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Cand_Name'] = $listofsuccessfulldata->lead_cand_name;
     $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Party_Abbre'] = $listofsuccessfulldata->lead_party_abbre;
     $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Party_symbol'] = $listofsuccessfulldata->SYMBOL_DES;
     $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Vote_Margin'] = $listofsuccessfulldata->trail_total_vote;
     $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['margin'] = $listofsuccessfulldata->margin;
     if($listofsuccessfulldata->trail_total_vote != 0){
      $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['percent'] = round($listofsuccessfulldata->margin/$listofsuccessfulldata->TotalVote*100,2);
     }else {
     $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['percent'] ='0%';
     }

     }
       return view('IndexCardReports/StatisticalReports/Vol2/ecisuccessfullcondidate',  compact('arraydata','user_data'));
     }

     public function successfullcondidatePDF(Request $request) {

           $successfullcondidate = DB::select("SELECT m.st_code,`m`.`PC_TYPE`, `m`.`PC_NAME`, `m`.`PC_NO`, SUM(total_vote) as TotalVote, `winn`.`st_name`, `cpd`.`cand_name` as lead_cand_name, `cpd`.`cand_category` as cand_category, `winn`.`lead_party_abbre`, `symbol`.`SYMBOL_DES`, `winn`.`margin`, `winn`.`trail_total_vote`, `winn`.`st_code`
      FROM winning_leading_candidate AS winn INNER JOIN m_pc AS m ON m.st_code = winn.st_code AND m.pc_no = winn.pc_no INNER JOIN candidate_nomination_detail AS cond ON cond.candidate_id = winn.candidate_id JOIN candidate_personal_detail AS cpd ON cpd.candidate_id = winn.candidate_id AND cond.application_status = 6 AND finalaccepted = 1 INNER JOIN counting_pcmaster AS counting ON counting.st_code = winn.st_code AND winn.pc_no = counting.pc_no INNER JOIN m_symbol AS symbol ON cond.symbol_id = symbol.SYMBOL_NO GROUP BY m.st_code,m.pc_no order by `winn`.`st_name` asc");


  foreach($successfullcondidate as $key=>$listofsuccessfulldata){

  $arraydata[$listofsuccessfulldata->st_name]['state'] = $listofsuccessfulldata->st_name;
   $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Pc_Name'] = $listofsuccessfulldata->PC_NAME;
   $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['PC_TYPE'] = $listofsuccessfulldata->PC_TYPE;
   $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['cand_category'] = strtoupper($listofsuccessfulldata->cand_category);
   $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Cand_Name'] = $listofsuccessfulldata->lead_cand_name;
   $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Party_Abbre'] = $listofsuccessfulldata->lead_party_abbre;
   $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Party_symbol'] = $listofsuccessfulldata->SYMBOL_DES;
   $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Vote_Margin'] = $listofsuccessfulldata->trail_total_vote;
   $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['margin'] = $listofsuccessfulldata->margin;
   if($listofsuccessfulldata->trail_total_vote != 0){
       $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['percent'] = round($listofsuccessfulldata->margin/$listofsuccessfulldata->TotalVote*100,2);

   }else {
   $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['percent'] ='0%';
   }
   }
        $pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports/StatisticalReports.Vol2.ecisuccessfull-candidate-pdf', compact('arraydata'));
			
			
         if(verifyreport(4)){
        
                  $file_name = 'List Of Successful Candidate'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/4/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '4',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
        return $pdf->download('List Of Successful Candidate.pdf');
    }

     public function successfullcondidateexcell(Request $request) {
         set_time_limit(6000);
       $successfullcondidate = DB::select("SELECT m.st_code,`m`.`PC_TYPE`, `m`.`PC_NAME`, `m`.`PC_NO`, SUM(total_vote) as TotalVote, `winn`.`st_name`, `cpd`.`cand_name` as lead_cand_name, `cpd`.`cand_category` as cand_category, `winn`.`lead_party_abbre`, `symbol`.`SYMBOL_DES`, `winn`.`margin`, `winn`.`trail_total_vote`, `winn`.`st_code`
      FROM winning_leading_candidate AS winn INNER JOIN m_pc AS m ON m.st_code = winn.st_code AND m.pc_no = winn.pc_no INNER JOIN candidate_nomination_detail AS cond ON cond.candidate_id = winn.candidate_id JOIN candidate_personal_detail AS cpd ON cpd.candidate_id = winn.candidate_id AND cond.application_status = 6 AND finalaccepted = 1 INNER JOIN counting_pcmaster AS counting ON counting.st_code = winn.st_code AND winn.pc_no = counting.pc_no INNER JOIN m_symbol AS symbol ON cond.symbol_id = symbol.SYMBOL_NO GROUP BY m.st_code,m.pc_no order by `winn`.`st_name` asc");

  foreach($successfullcondidate as $key=>$listofsuccessfulldata){

  $arraydata[$listofsuccessfulldata->st_name][$listofsuccessfulldata->PC_NO]['state'] = $listofsuccessfulldata->st_name;
   $arraydata[$listofsuccessfulldata->st_name][$listofsuccessfulldata->PC_NO]['Pc_Name'] = $listofsuccessfulldata->PC_NAME;
   $arraydata[$listofsuccessfulldata->st_name][$listofsuccessfulldata->PC_NO]['PC_TYPE'] = $listofsuccessfulldata->PC_TYPE;
   $arraydata[$listofsuccessfulldata->st_name][$listofsuccessfulldata->PC_NO]['cand_category'] = strtoupper($listofsuccessfulldata->cand_category);
   $arraydata[$listofsuccessfulldata->st_name][$listofsuccessfulldata->PC_NO]['Cand_Name'] = $listofsuccessfulldata->lead_cand_name;
   $arraydata[$listofsuccessfulldata->st_name][$listofsuccessfulldata->PC_NO]['Party_Abbre'] = $listofsuccessfulldata->lead_party_abbre;
   $arraydata[$listofsuccessfulldata->st_name][$listofsuccessfulldata->PC_NO]['Party_symbol'] = $listofsuccessfulldata->SYMBOL_DES;
   $arraydata[$listofsuccessfulldata->st_name][$listofsuccessfulldata->PC_NO]['Vote_Margin'] = $listofsuccessfulldata->trail_total_vote;
   $arraydata[$listofsuccessfulldata->st_name][$listofsuccessfulldata->PC_NO]['margin'] = $listofsuccessfulldata->margin;
   if($listofsuccessfulldata->trail_total_vote != 0){
       $arraydata[$listofsuccessfulldata->st_name][$listofsuccessfulldata->PC_NO]['percent'] = round($listofsuccessfulldata->margin/$listofsuccessfulldata->TotalVote*100,2);

   }else {
   $arraydata[$listofsuccessfulldata->st_name][$listofsuccessfulldata->PC_NO]['percent'] ='0%';
   }
   }

   return Excel::create('List Of Successful Candidate', function($excel) use ($arraydata) {
                       $excel->sheet('mySheet', function($sheet) use ($arraydata) {
                           $sheet->mergeCells('A1:I1');

                           $sheet->cells('A1', function($cells) {
							$cells->setValue('4 - List Of Successful Candidate');
                               $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));;
                               $cells->setAlignment('center');
                           });


							$sheet->getStyle('A3')->getAlignment()->setWrapText(true);
							$sheet->getStyle('B3')->getAlignment()->setWrapText(true);
							$sheet->getStyle('C3')->getAlignment()->setWrapText(true);
							$sheet->getStyle('D3')->getAlignment()->setWrapText(true);
							$sheet->getStyle('E3')->getAlignment()->setWrapText(true);
							$sheet->getStyle('F3')->getAlignment()->setWrapText(true);
							$sheet->getStyle('G3')->getAlignment()->setWrapText(true);
							$sheet->getStyle('H3')->getAlignment()->setWrapText(true);
							$sheet->getStyle('I3')->getAlignment()->setWrapText(true);
							$sheet->setSize('A3', 10,25);
							$sheet->setSize('B3', 25,25);
							$sheet->setSize('C3', 25,25);
							$sheet->setSize('D3', 60,25);
							$sheet->setSize('E3', 20,25);
							$sheet->setSize('F3', 25,25);
							$sheet->setSize('G3', 15,25);
							$sheet->setSize('H3', 25,25);
							$sheet->setSize('I3', 15,25);


                           $sheet->cell('A3', function($cell) {
                               $cell->setValue('SL. No.');
                                $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));
                           });
						   
						   $sheet->cell('B3', function($cell) {
                               $cell->setValue('STATE');
                                $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));
                           });

                           $sheet->cell('C3', function($cell) {
                               $cell->setValue('CONSTITUENCY');
                               $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));
                           });
                           
                           $sheet->cell('D3', function($cell) {
                               $cell->setValue('WINNER NAME');
                               $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));
                           });
						   $sheet->cell('E3', function($cell) {
                               $cell->setValue('CATEGORY');
                               $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));
                           });
						   $sheet->cell('F3', function($cell) {
                               $cell->setValue('SOCIAL CATEGORY');
                               $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));
                           });

                           $sheet->cell('G3', function($cell) {
                               $cell->setValue('PARTY');
                               $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));
                           });
                           $sheet->cell('H3', function($cell) {
                               $cell->setValue('PARTY SYMBOL');
                               $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));
                           });
                           $sheet->cell('I3', function($cell) {
                               $cell->setValue('MARGIN');
                               $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));
                           });
                          

                           $i = 4;
                           if (!empty($arraydata)) {
							   
							   $sn = 1;
                               foreach ($arraydata as $key => $values) {
									foreach ($values as $key => $value) {
                                   $sheet->cell('A' . $i,  $sn );
                                   $sheet->cell('B' . $i, $value['state']);
                                   $sheet->cell('C' . $i, $value['Pc_Name']);                                   
                                   $sheet->cell('D' . $i, $value['Cand_Name']);
								   $sheet->cell('E' . $i, $value['PC_TYPE']);
                                   $sheet->cell('F' . $i, $value['cand_category']);
                                   $sheet->cell('G' . $i, $value['Party_Abbre']);
                                   $sheet->cell('H' . $i, $value['Party_symbol']);
                                   $sheet->cell('I' . $i, $value['margin']);
                              $i++;$sn++; 

							  
                              
							  }
                               }
                           }


                           $i = $i+3;
                           $sheet->mergeCells("A$i:B$i");
                           $sheet->cell('A'.$i, function($cells) {
                           $cells->setValue('Disclaimer');
                           $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
                           });

                        $i = $i+1;

                        $sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
                        $sheet->setSize('A'.$i, 25,30);



                        $sheet->mergeCells("A$i:E$i");
                        $sheet->cell('A'.$i, function($cells) {
                        $cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
                        $cells->setFont(array('name' => 'Times New Roman','size' => 10));
                        });


                       });
                   })->export();



 }
//// performance of national party start
public function performanceofnationalparties(Request $request) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $user;
        $data = DB::select('SELECT a.partyname,contested,won,vote AS evm_vote,total_vote,
			(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fd FROM `counting_pcmaster` as cp
			 WHERE a.party_id = cp.party_id AND a.party_id != (select lead_cand_partyid from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code) group by cp.party_id) as fd
			FROM
			(SELECT party_id,partyname,COUNT(DISTINCT p.candidate_id)contested,
			 COUNT(DISTINCT w.`candidate_id`)won FROM `counting_pcmaster` p
			JOIN `winning_leading_candidate` w ON p.party_id=w.`lead_cand_partyid`
			JOIN m_party q ON p.party_id=q.ccode
			WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)a JOIN
			(SELECT partyname,SUM(evm_vote+migrate_votes)vote,SUM(total_vote)total_vote FROM `counting_pcmaster` m
			JOIN m_party q ON m.party_id=q.ccode WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)b
			ON a.partyname=b.partyname');

        $totalElectors = DB::select("SELECT sum(ec.gen_electors_male + ec.gen_electors_female + ec.gen_electors_other + ec.nri_male_electors + ec.nri_female_electors + ec.nri_third_electors + ec.service_male_electors + ec.service_female_electors + ec.service_third_electors) as total_electors FROM `electors_cdac` as ec join m_election_details as med on med.ST_CODE = ec.st_code AND med.CONST_NO = ec.pc_no  WHERE med.CONST_TYPE = 'PC' AND med.election_status = '1'");

       $totalVotes = 0;
		
		$totalVotes =  DB::select("SELECT SUM(ecoi.general_male_voters+ecoi.nri_male_voters + ecoi.general_female_voters+ecoi.nri_female_voters + ecoi.general_other_voters+ecoi.nri_other_voters + ecoi.service_postal_votes_under_section_8+ecoi.service_postal_votes_gov - ecoi.votes_not_retreived_from_evm - ecoi.rejected_votes_due_2_other_reason) AS totalVotes
                         FROM electors_cdac_other_information AS ecoi
                         INNER JOIN m_election_details AS med ON med.st_code = ecoi.ST_CODE AND med.CONST_NO = ecoi.PC_NO
                         WHERE   med.CONST_TYPE = 'PC' AND  med.election_status = '1'");
		
		$postalvotetotal = DB::select("SELECT
			SUM(rejectedpostalvote) AS rejected_postal_vote
			FROM
			 (SELECT cp.st_code,cp.pc_no, a.rejectedvote AS 'rejectedpostalvote'   FROM counting_pcmaster cp,m_election_details med,
			 (SELECT st_code,pc_no,rejectedvote FROM counting_pcmaster  GROUP BY st_code, pc_no) a
			  WHERE cp.st_code = a.st_code AND cp.pc_no = a.pc_no AND med.st_code = cp.ST_CODE AND med.CONST_NO = cp.PC_NO
			  AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1'  AND cp.party_id != '1180' GROUP BY cp.st_code,cp.pc_no) b");
				
		$notavote = DB::select("SELECT SUM(cp.total_vote) AS nota
                       FROM counting_pcmaster AS cp
                       INNER JOIN m_election_details AS med ON med.st_code = cp.ST_CODE AND med.CONST_NO = cp.PC_NO
                       WHERE cp.party_id = '1180' AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1'");
					
		$totalVotes = $totalVotes[0]->totalVotes - $notavote[0]->nota - $postalvotetotal[0]->rejected_postal_vote;
		$totalVotes = array((object)[
			'totalVotes' => $totalVotes
		]);

        return view('IndexCardReports/StatisticalReports/Vol1/eciperformanceofnatiionalparties', compact('data', 'totalElectors', 'totalVotes', 'user_data'));

    }


    public function performanceofnatiionalpartiespdf(Request $request) {
            $user = Auth::user();
            $user_data = $user;

            $data = DB::select('SELECT a.partyname,contested,won,vote AS evm_vote,total_vote,
			(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fd FROM `counting_pcmaster` as cp
			 WHERE a.party_id = cp.party_id AND a.party_id != (select lead_cand_partyid from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code) group by cp.party_id) as fd
			FROM
			(SELECT party_id,partyname,COUNT(DISTINCT p.candidate_id)contested,
			 COUNT(DISTINCT w.`candidate_id`)won FROM `counting_pcmaster` p
			JOIN `winning_leading_candidate` w ON p.party_id=w.`lead_cand_partyid`
			JOIN m_party q ON p.party_id=q.ccode
			WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)a JOIN
			(SELECT partyname,SUM(evm_vote+migrate_votes)vote,SUM(total_vote)total_vote FROM `counting_pcmaster` m
			JOIN m_party q ON m.party_id=q.ccode WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)b
			ON a.partyname=b.partyname');

        $totalElectors = DB::select("SELECT sum(ec.gen_electors_male + ec.gen_electors_female + ec.gen_electors_other + ec.nri_male_electors + ec.nri_female_electors + ec.nri_third_electors + ec.service_male_electors + ec.service_female_electors + ec.service_third_electors) as total_electors FROM `electors_cdac` as ec join m_election_details as med on med.ST_CODE = ec.st_code AND med.CONST_NO = ec.pc_no  WHERE  med.CONST_TYPE = 'PC' AND med.election_status = '1'");

        $totalVotes = 0;
		
		$totalVotes =  DB::select("SELECT SUM(ecoi.general_male_voters+ecoi.nri_male_voters + ecoi.general_female_voters+ecoi.nri_female_voters + ecoi.general_other_voters+ecoi.nri_other_voters + ecoi.service_postal_votes_under_section_8+ecoi.service_postal_votes_gov - ecoi.votes_not_retreived_from_evm - ecoi.rejected_votes_due_2_other_reason) AS totalVotes
                         FROM electors_cdac_other_information AS ecoi
                         INNER JOIN m_election_details AS med ON med.st_code = ecoi.ST_CODE AND med.CONST_NO = ecoi.PC_NO
                         WHERE   med.CONST_TYPE = 'PC' AND  med.election_status = '1'");
		
		$postalvotetotal = DB::select("SELECT
			SUM(rejectedpostalvote) AS rejected_postal_vote
			FROM
			 (SELECT cp.st_code,cp.pc_no, a.rejectedvote AS 'rejectedpostalvote'   FROM counting_pcmaster cp,m_election_details med,
			 (SELECT st_code,pc_no,rejectedvote FROM counting_pcmaster  GROUP BY st_code, pc_no) a
			  WHERE cp.st_code = a.st_code AND cp.pc_no = a.pc_no AND med.st_code = cp.ST_CODE AND med.CONST_NO = cp.PC_NO
			  AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1'  AND cp.party_id != '1180' GROUP BY cp.st_code,cp.pc_no) b");
				
		$notavote = DB::select("SELECT SUM(cp.total_vote) AS nota
                       FROM counting_pcmaster AS cp
                       INNER JOIN m_election_details AS med ON med.st_code = cp.ST_CODE AND med.CONST_NO = cp.PC_NO
                       WHERE cp.party_id = '1180' AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1'");
					
		$totalVotes = $totalVotes[0]->totalVotes - $notavote[0]->nota - $postalvotetotal[0]->rejected_postal_vote;
		$totalVotes = array((object)[
			'totalVotes' => $totalVotes
		]);
		
            $pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports/StatisticalReports.Vol1.eciperformanceofnationalparties-pdf', compact('data', 'totalElectors', 'totalVotes', 'user_data'));

             if(verifyreport(20)){
        
                  $file_name = 'Performance of National Parties'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/20/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '20',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
            return $pdf->download('Performance of National Parties.pdf');
        }


        public function winningcpndidateanalysisoverelectorxls(Request $request) {
                $user = Auth::user();
                $user_data = $user;


              $arrayData = DB::select('SELECT a.partyname,contested,won,vote AS evm_vote,total_vote,
			(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fd FROM `counting_pcmaster` as cp
			 WHERE a.party_id = cp.party_id AND a.party_id != (select lead_cand_partyid from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code) group by cp.party_id) as fd
			FROM
			(SELECT party_id,partyname,COUNT(DISTINCT p.candidate_id)contested,
			 COUNT(DISTINCT w.`candidate_id`)won FROM `counting_pcmaster` p
			JOIN `winning_leading_candidate` w ON p.party_id=w.`lead_cand_partyid`
			JOIN m_party q ON p.party_id=q.ccode
			WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)a JOIN
			(SELECT partyname,SUM(evm_vote+migrate_votes)vote,SUM(total_vote)total_vote FROM `counting_pcmaster` m
			JOIN m_party q ON m.party_id=q.ccode WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)b
			ON a.partyname=b.partyname');

        $totalElectors = DB::select("SELECT sum(ec.gen_electors_male + ec.gen_electors_female + ec.gen_electors_other + ec.nri_male_electors + ec.nri_female_electors + ec.nri_third_electors + ec.service_male_electors + ec.service_female_electors + ec.service_third_electors) as total_electors FROM `electors_cdac` as ec join m_election_details as med on med.ST_CODE = ec.st_code AND med.CONST_NO = ec.pc_no  WHERE med.CONST_TYPE = 'PC' AND med.election_status = '1'");

        $totalVotes = 0;
		
		$totalVotes =  DB::select("SELECT SUM(ecoi.general_male_voters+ecoi.nri_male_voters + ecoi.general_female_voters+ecoi.nri_female_voters + ecoi.general_other_voters+ecoi.nri_other_voters + ecoi.service_postal_votes_under_section_8+ecoi.service_postal_votes_gov - ecoi.votes_not_retreived_from_evm - ecoi.rejected_votes_due_2_other_reason) AS totalVotes
                         FROM electors_cdac_other_information AS ecoi
                         INNER JOIN m_election_details AS med ON med.st_code = ecoi.ST_CODE AND med.CONST_NO = ecoi.PC_NO
                         WHERE   med.CONST_TYPE = 'PC' AND  med.election_status = '1'");
		
		$postalvotetotal = DB::select("SELECT
			SUM(rejectedpostalvote) AS rejected_postal_vote
			FROM
			 (SELECT cp.st_code,cp.pc_no, a.rejectedvote AS 'rejectedpostalvote'   FROM counting_pcmaster cp,m_election_details med,
			 (SELECT st_code,pc_no,rejectedvote FROM counting_pcmaster  GROUP BY st_code, pc_no) a
			  WHERE cp.st_code = a.st_code AND cp.pc_no = a.pc_no AND med.st_code = cp.ST_CODE AND med.CONST_NO = cp.PC_NO
			  AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1'  AND cp.party_id != '1180' GROUP BY cp.st_code,cp.pc_no) b");
				
		$notavote = DB::select("SELECT SUM(cp.total_vote) AS nota
                       FROM counting_pcmaster AS cp
                       INNER JOIN m_election_details AS med ON med.st_code = cp.ST_CODE AND med.CONST_NO = cp.PC_NO
                       WHERE cp.party_id = '1180' AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1'");
					
		$totalVotes = $totalVotes[0]->totalVotes - $notavote[0]->nota - $postalvotetotal[0]->rejected_postal_vote;
		$totalVotes = array((object)[
			'totalVotes' => $totalVotes
		]);



                return Excel::create('Performance of National Parties', function($excel) use ($arrayData,$totalElectors,$totalVotes) {
                                    $excel->sheet('mySheet', function($sheet) use ($arrayData,$totalElectors,$totalVotes) {
                                        $sheet->mergeCells('A1:G1');
                                       // $sheet->mergeCells('B3:D3');
                                       // $sheet->mergeCells('F3:G3');
                                        $sheet->cells('A1', function($cells) {
                                            $cells->setValue('20 - PERFORMANCE OF NATIONAL PARTY');
                                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
                                            $cells->setAlignment('center');
                                        });

										$sheet->cells('B2', function($cells) {
                                            $cells->setValue(' CANDIDATES ');
                                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 13));
                                            $cells->setAlignment('center');
                                        });

										$sheet->cells('F2', function($cells) {
                                            $cells->setValue(' % OF VOTES SECURED ');
                                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 13));
                                            $cells->setAlignment('center');
                                        });
										
										$sheet->getStyle('A3')->getAlignment()->setWrapText(true);
										$sheet->setSize('A3', 40,50);
										
										$sheet->getStyle('E3')->getAlignment()->setWrapText(true);
										$sheet->setSize('E3', 30,50);
										
										$sheet->getStyle('F3')->getAlignment()->setWrapText(true);
										$sheet->setSize('F3', 25,50);
										
										$sheet->getStyle('G3')->getAlignment()->setWrapText(true);
										$sheet->setSize('G3', 25,50);

                                        $sheet->cells('A3', function($cells) {
                                            $cells->setValue(' PARTY NAME ');
                                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 13));
                                            $cells->setAlignment('center');
                                        });
                                        $sheet->cells('B3', function($cells) {
                                            $cells->setValue(' CONTESTED ');
                                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 13));
                                            $cells->setAlignment('center');
                                        });
										$sheet->cells('C3', function($cells) {
                                            $cells->setValue(' WON ');
                                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 13));
                                            $cells->setAlignment('center');
                                        });
										
										$sheet->cells('D3', function($cells) {
                                            $cells->setValue(' DF ');
                                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 13));
                                            $cells->setAlignment('center');
                                        });
                                        
                                        $sheet->cells('E3', function($cells) {
                                            $cells->setValue('VOTES SECURED BY PARTY');
											$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                                        });
                                        $sheet->cells('F3', function($cells) {
                                            $cells->setValue('OVER TOTAL ELECTORS');
											$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                                        });
                                        $sheet->cells('G3', function($cells) {
                                            $cells->setValue('OVER TOTAL VALID VOTES POLLED');
											$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                                        });

                                        $i = 4;
                                        $totalcontested = 0;
                                        $won = 0;
                                        $fd = 0;
                                        $secure = 0;
                                        $electorspercent = 0;
                                        $overtotalvaliedpercent = 0;
                                        if (!empty($arrayData)) {
                                            foreach ($arrayData as $key => $rows) {
                                              
		
                                              $peroverelectors = ($rows->total_vote/$totalElectors[0]->total_electors)*100;
                                              $perovervoter = ($rows->total_vote/$totalVotes[0]->totalVotes)*100;
                                                $sheet->cell('A' . $i, $rows->partyname);
                                                $sheet->cell('B' . $i, $rows->contested);
                                                $sheet->cell('C' . $i, $rows->won);
                                                $sheet->cell('D' . $i, $rows->fd);
                                                $sheet->cell('E' . $i, $rows->total_vote);
                                                $sheet->cell('F' . $i, $peroverelectors);
                                                $sheet->cell('G' . $i, $perovervoter);

                                                $totalcontested += $rows->contested;
                                                $won += $rows->won;
                                                $fd += $rows->fd;
                                                $secure += $rows->total_vote;
                                             

												$i++; 
										   }
										   $i++;
											$grand_peroverelectors = ($secure/$totalElectors[0]->total_electors)*100;
                                            $grand_perovervoter = ($secure/$totalVotes[0]->totalVotes)*100;
										   
                                                  $sheet->cell('A' . $i, 'Grand TOTAL');
                                                  $sheet->cell('B' . $i, $totalcontested);
                                                  $sheet->cell('C' . $i, $won);
                                                  $sheet->cell('D' . $i, $fd);
                                                  $sheet->cell('E' . $i, $secure);
                                                  $sheet->cell('F' . $i, $grand_peroverelectors);
                                                  $sheet->cell('G' . $i, $grand_perovervoter);
												  
												  
											$i++;
											$i++;
											
											$sheet->getStyle('B' . $i)->getAlignment()->setWrapText(true);
											$sheet->setSize('B' . $i, 25,50);
											
											
											$sheet->cell('B' . $i, 'TOTAL ELECTORS IN THE COUNTRY (INCLUDING SERVICE - ELECTORS)');
											$sheet->cell('D' . $i, $totalElectors[0]->total_electors);
											
											$i++;	

											$sheet->getStyle('B' . $i)->getAlignment()->setWrapText(true);
											$sheet->setSize('B' . $i, 25,70);
											$sheet->cell('B' . $i, 'TOTAL VALID VOTES POLLED IN THE COUNTRY (INCLUDING SERVICE-VOTES)');
											$sheet->cell('D' . $i, $totalVotes[0]->totalVotes);
											
												  
                                        }

                                        $i = $i+3;

          

                                    $sheet->mergeCells("A$i:B$i");
                                    $sheet->cell('A'.$i, function($cells) {
                                      $cells->setValue('Disclaimer');
                                      $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
                                    });

                                    $i = $i+1;

                                    $sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
                                    $sheet->setSize('A'.$i, 25,30);



                                    $sheet->mergeCells("A$i:F$i");
                                    $sheet->cell('A'.$i, function($cells) {
                                    $cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
                                    $cells->setFont(array('name' => 'Times New Roman','size' => 10));
                                    });
                                    });
                                })->export();
            }
//// performance of national party end

     public function allstatewiseoverseaselectorsvoter(){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $d=$this->commonModel->getunewserbyuserid($uid);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          $sched=''; $search='';
          $status=$this->commonModel->allstatus();
          if(isset($ele_details)) {  $i=0;
            foreach($ele_details as $ed) {
               $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               $const_type=$ed->CONST_TYPE;
             }
          }
          $session['election_detail'] = array();
       // echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
       $datanew = array();

$data = DB::select("SELECT TEMP.*
FROM(SELECT m.st_code, m.st_name,mpc.PC_TYPE,mpc.pc_no, mpc.PC_NAME,
SUM(cda.nri_male_electors) AS emale,
SUM(cda.nri_female_electors) AS efemale ,
SUM(cda.nri_third_electors)AS eother ,
SUM(cda.nri_third_electors + cda.nri_female_electors + cda.nri_male_electors) AS etotal
FROM electors_cdac cda,m_pc mpc ,m_state m
WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code
GROUP BY mpc.st_code,mpc.PC_TYPE
)TEMP

INNER JOIN m_election_details AS med ON med.`CONST_NO` = TEMP.pc_no AND med.`ST_CODE` = TEMP.st_code
WHERE   med.CONST_TYPE = 'PC' AND  med.election_status = '1'
GROUP BY TEMP.st_code,TEMP.PC_TYPE ORDER BY TEMP.st_name, TEMP.PC_TYPE");

//echo "<pre>"; print_r($data); die;

$datavoter = DB::select("SELECT m.st_code, m.st_name,mpc.PC_TYPE,
SUM(ecoi.nri_male_voters) AS vemale, SUM(ecoi.nri_female_voters) AS vefemale, SUM(ecoi.nri_other_voters) AS vother, SUM(ecoi.nri_male_voters + ecoi.nri_female_voters + ecoi.nri_other_voters) AS vtotals
FROM electors_cdac_other_information ecoi,m_pc mpc ,m_state m
WHERE mpc.ST_CODE = ecoi.st_code AND mpc.pc_no = ecoi.pc_no AND  m.st_code = ecoi.st_code
GROUP BY mpc.st_code,mpc.PC_TYPE ORDER BY m.st_name");




foreach ($data as $key => $value) {
foreach ($datavoter as $key1 => $value1) {
  if(($value->st_name == $value1->st_name) && ($value->PC_TYPE == $value1->PC_TYPE)){
   $datanew[$value->st_name][$value->PC_TYPE] = array(
      'st_code' =>$value->st_code,
      'PC_TYPE' =>$value->PC_TYPE,
      'pc_no' =>$value->pc_no,
      'PC_NAME' =>$value->PC_NAME,
      'emale' =>$value->emale,
      'efemale' =>$value->efemale,
      'eother' =>$value->eother,
      'etotal' =>$value->etotal,
      'vemale' =>$value1->vemale,
      'vefemale' =>$value1->vefemale,
      'vother' =>$value1->vother,
      'vtotals' =>$value1->vtotals,

   );
 }
}
}

$data = $datanew;

//echo "<pre>"; print_r($data); die;


 return view('IndexCardReports/StatisticalReports/Vol1/ecistateparticipationoofoverseaselectorsvoters',compact('data','user_data'));

    }

public function allstatewiseoverseaselectorsvoterpdf(){
  $data = DB::select("SELECT TEMP.*
FROM(SELECT m.st_code, m.st_name,mpc.PC_TYPE,mpc.pc_no, mpc.PC_NAME,
SUM(cda.nri_male_electors) AS emale,
SUM(cda.nri_female_electors) AS efemale ,
SUM(cda.nri_third_electors)AS eother ,
SUM(cda.nri_third_electors + cda.nri_female_electors + cda.nri_male_electors) AS etotal
FROM electors_cdac cda,m_pc mpc ,m_state m
WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code
GROUP BY mpc.st_code,mpc.PC_TYPE
)TEMP

INNER JOIN m_election_details AS med ON med.`CONST_NO` = TEMP.pc_no AND med.`ST_CODE` = TEMP.st_code
WHERE   med.CONST_TYPE = 'PC' AND  med.election_status = '1'
GROUP BY TEMP.st_code,TEMP.PC_TYPE ORDER BY TEMP.st_name, TEMP.PC_TYPE");

//echo "<pre>"; print_r($data); die;

$datavoter = DB::select("SELECT m.st_code, m.st_name,mpc.PC_TYPE,
SUM(ecoi.nri_male_voters) AS vemale, SUM(ecoi.nri_female_voters) AS vefemale, SUM(ecoi.nri_other_voters) AS vother, SUM(ecoi.nri_male_voters + ecoi.nri_female_voters + ecoi.nri_other_voters) AS vtotals
FROM electors_cdac_other_information ecoi,m_pc mpc ,m_state m
WHERE mpc.ST_CODE = ecoi.st_code AND mpc.pc_no = ecoi.pc_no AND  m.st_code = ecoi.st_code
GROUP BY mpc.st_code,mpc.PC_TYPE ORDER BY m.st_name");




foreach ($data as $key => $value) {
foreach ($datavoter as $key1 => $value1) {
  if(($value->st_name == $value1->st_name) && ($value->PC_TYPE == $value1->PC_TYPE)){
   $datanew[$value->st_name][$value->PC_TYPE] = array(
      'st_code' =>$value->st_code,
      'PC_TYPE' =>$value->PC_TYPE,
      'pc_no' =>$value->pc_no,
      'PC_NAME' =>$value->PC_NAME,
      'emale' =>$value->emale,
      'efemale' =>$value->efemale,
      'eother' =>$value->eother,
      'etotal' =>$value->etotal,
      'vemale' =>$value1->vemale,
      'vefemale' =>$value1->vefemale,
      'vother' =>$value1->vother,
      'vtotals' =>$value1->vtotals,

   );
 }
}
}

$data = $datanew;
$pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports/StatisticalReports.Vol1.ecistatestatewiseoverseaseelectors-pdf', compact('data'));

 if(verifyreport(11)){
        
                  $file_name = 'State Wise Participation of Overseas Electors Voters'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/11/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '11',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
        return $pdf->download('State Wise Participation of Overseas Electors Voters.pdf');

    }

public function allstatewiseoverseaselectorsvoterxls(Request $request){

$data = DB::select("SELECT TEMP.*
FROM(SELECT m.st_code, m.st_name,mpc.PC_TYPE,mpc.pc_no, mpc.PC_NAME,
SUM(cda.nri_male_electors) AS emale,
SUM(cda.nri_female_electors) AS efemale ,
SUM(cda.nri_third_electors)AS eother ,
SUM(cda.nri_third_electors + cda.nri_female_electors + cda.nri_male_electors) AS etotal
FROM electors_cdac cda,m_pc mpc ,m_state m
WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code
GROUP BY mpc.st_code,mpc.PC_TYPE
)TEMP

INNER JOIN m_election_details AS med ON med.`CONST_NO` = TEMP.pc_no AND med.`ST_CODE` = TEMP.st_code
WHERE   med.CONST_TYPE = 'PC' AND  med.election_status = '1'
GROUP BY TEMP.st_code,TEMP.PC_TYPE ORDER BY TEMP.st_name, TEMP.PC_TYPE");

//echo "<pre>"; print_r($data); die;

$datavoter = DB::select("SELECT m.st_code, m.st_name,mpc.PC_TYPE,
SUM(ecoi.nri_male_voters) AS vemale, SUM(ecoi.nri_female_voters) AS vefemale, SUM(ecoi.nri_other_voters) AS vother, SUM(ecoi.nri_male_voters + ecoi.nri_female_voters + ecoi.nri_other_voters) AS vtotals
FROM electors_cdac_other_information ecoi,m_pc mpc ,m_state m
WHERE mpc.ST_CODE = ecoi.st_code AND mpc.pc_no = ecoi.pc_no AND  m.st_code = ecoi.st_code
GROUP BY mpc.st_code,mpc.PC_TYPE ORDER BY m.st_name");




foreach ($data as $key => $value) {
foreach ($datavoter as $key1 => $value1) {
  if(($value->st_name == $value1->st_name) && ($value->PC_TYPE == $value1->PC_TYPE)){
   $datanew[$value->st_name][$value->PC_TYPE] = array(
      'st_code' =>$value->st_code,
      'PC_TYPE' =>$value->PC_TYPE,
      'pc_no' =>$value->pc_no,
      'PC_NAME' =>$value->PC_NAME,
      'emale' =>$value->emale,
      'efemale' =>$value->efemale,
      'eother' =>$value->eother,
      'etotal' =>$value->etotal,
      'vemale' =>$value1->vemale,
      'vefemale' =>$value1->vefemale,
      'vother' =>$value1->vother,
      'vtotals' =>$value1->vtotals,

   );
 }
}
}



$arrayData = $datanew;


  return Excel::create('State Wise Participation of Overseas Electors Voters', function($excel) use ($arrayData) {
                      $excel->sheet('mySheet', function($sheet) use ($arrayData) {
                          $sheet->mergeCells('A1:K1');
                          $sheet->mergeCells('C2:F2');
                          $sheet->mergeCells('G2:J2');

                          $sheet->cells('A1', function($cells) {
                              $cells->setValue('State Wise Participation of Overseas Electors Voters');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                              $cells->setFontColor("#ffffff");
                              $cells->setBackground("#042179");
                              $cells->setAlignment('center');
                          });

                          $sheet->cell('A2', function($cell) {
                              $cell->setValue('State');
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });
                          $sheet->cell('B2', function($cell) {
                              $cell->setValue('PC Type');
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });
                          $sheet->cell('C2', function($cell) {
                              $cell->setValue('Electors');
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });
                          $sheet->cell('G2', function($cell) {
                              $cell->setValue('Voters');
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });

                          $sheet->cell('C3', function($cell) {
                              $cell->setValue('Male');
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });
                            $sheet->cell('D3', function($cell) {
                              $cell->setValue('Female');
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });
                          $sheet->cell('E3', function($cell) {
                              $cell->setValue('THIRD GENDER');
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });
                          $sheet->cell('F3', function($cell) {
                              $cell->setValue('Total Electors');
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });


                          $sheet->cell('G3', function($cell) {
                              $cell->setValue('Male');
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });
                          $sheet->cell('H3', function($cell) {
                              $cell->setValue('Female');
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });
                          $sheet->cell('I3', function($cell) {
                              $cell->setValue('THIRD GENDER');
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });
                          $sheet->cell('J3', function($cell) {
                              $cell->setValue('Total Voters');
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });
                          $i = 4;
                          if (!empty($arrayData)) {

                            $grandemale = $grandfemale =$grandother = $grandtotal = $grandvemale
                            =$grandvefemale =$grandvother =$grandvtotals  = 0;

                            foreach ($arrayData as $key => $values) {
                              $totemale = $totefemale =$toteother = $totetotal = $totvemale
                              = $totvefemale = $totvother = $totvtotals = 0;
                              $sheet->cell('A' . $i, $key);
                            foreach($values as $key1 => $value1) {

                                  $sheet->cell('B' . $i, $key1);
                                  $sheet->cell('C' . $i, ($value1['emale']) ? $value1['emale'] : '=(0)');
                                  $sheet->cell('D' . $i, ($value1['efemale']) ? $value1['efemale'] : '=(0)');
                                  $sheet->cell('E' . $i, ($value1['eother']) ? $value1['eother'] : '=(0)');
                                  $sheet->cell('F' . $i, ($value1['etotal']) ? $value1['etotal'] : '=(0)');
                                  $sheet->cell('G' . $i, ($value1['vemale']) ? $value1['vemale'] : '=(0)');
                                  $sheet->cell('H' . $i, ($value1['vefemale']) ? $value1['vefemale'] : '=(0)');
                                  $sheet->cell('I' . $i, ($value1['vother']) ? $value1['vother'] : '=(0)');
                                  $sheet->cell('J' . $i, $value1['vtotals'] ? $value1['vtotals'] : '=(0)');

                                  $totemale += $value1['emale'];
                                  $totefemale += $value1['efemale'];
                                  $toteother += $value1['eother'];
                                  $totetotal += $value1['etotal'];
                                  $totvemale += $value1['vemale'];
                                  $totvefemale += $value1['vefemale'];
                                  $totvother += $value1['vother'];
                                  $totvtotals += $value1['vtotals'];

                             $i++; }

                             $sheet->cell('A'.$i, function($cell) {
                              $cell->setValue('State Total');
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                              });
                              $sheet->cell('C'.$i, function($cell)use($totemale) {
                              $cell->setValue($totemale);
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                              });
                              $sheet->cell('D'.$i, function($cell) use($totefemale){
                              $cell->setValue($totefemale);
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                              });
                              $sheet->cell('E'.$i, function($cell) use($toteother){
                              $cell->setValue($toteother);
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                              });
                              $sheet->cell('F'.$i, function($cell) use($totetotal) {
                              $cell->setValue($totetotal);
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                              });
                              $sheet->cell('G'.$i, function($cell) use($totvemale) {
                              $cell->setValue($totvemale);
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                              });
                              $sheet->cell('H'.$i, function($cell) use($totvefemale){
                              $cell->setValue($totvefemale);
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                              });
                              $sheet->cell('I'.$i, function($cell) use($totvother) {
                              $cell->setValue($totvother);
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                              });
                              $sheet->cell('J'.$i, function($cell) use($totvtotals){
                              $cell->setValue($totvtotals);
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                              });


                              $grandemale += $totemale;
                              $grandfemale += $totefemale;
                              $grandother += $toteother;
                              $grandtotal += $totetotal;
                              $grandvemale += $totvemale;
                              $grandvefemale += $totvefemale;
                              $grandvother += $totvother;
                              $grandvtotals += $totvtotals;

                              $i++;

                           }

                           $sheet->cell('A'.$i, function($cell) {
                            $cell->setValue('Grand Total');
                            $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            });
                            $sheet->cell('C'.$i, function($cell)use($grandemale) {
                            $cell->setValue($grandemale);
                            $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            });
                            $sheet->cell('D'.$i, function($cell) use($grandfemale){
                            $cell->setValue($grandfemale);
                            $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            });
                            $sheet->cell('E'.$i, function($cell) use($grandother){
                            $cell->setValue($grandother);
                            $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            });
                            $sheet->cell('F'.$i, function($cell) use($grandtotal) {
                            $cell->setValue($grandtotal);
                            $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            });
                            $sheet->cell('G'.$i, function($cell) use($grandvemale) {
                            $cell->setValue($grandvemale);
                            $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            });
                            $sheet->cell('H'.$i, function($cell) use($grandvefemale){
                            $cell->setValue($grandvefemale);
                            $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            });
                            $sheet->cell('I'.$i, function($cell) use($grandvother) {
                            $cell->setValue($grandvother);
                            $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            });
                            $sheet->cell('J'.$i, function($cell) use($grandvtotals){
                            $cell->setValue($grandvtotals);
                            $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            });
                          }

                          $i = $i+3;

          

                        $sheet->mergeCells("A$i:B$i");
                        $sheet->cell('A'.$i, function($cells) {
                          $cells->setValue('Disclaimer');
                          $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
                        });

                        $i = $i+1;

                        $sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
                        $sheet->setSize('A'.$i, 25,30);



                        $sheet->mergeCells("A$i:J$i");
                        $sheet->cell('A'.$i, function($cells) {
                        $cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
                        $cells->setFont(array('name' => 'Times New Roman','size' => 10));
                        });
                      });
                  })->export();

}
//// details of repollheld start.

public function detailsofrepollheld(Request $request){
$user = Auth::user();
$user_data = $user;
       /*$rowdatas = DB::table('repoll_pc_ic as ic')
                 ->select('pc.ST_CODE','state.ST_NAME','pc.PC_NO',
                        'pc.PC_NAME','ic.date_repoll as dt_repoll','ic.no_of_ps_repoll as no_repoll',
						DB::raw('(select sum(total_polling_station_s_i_t_c) from electors_cdac_other_information as ecoi where ecoi.st_code = ic.st_code) as tpolling'))
                      ->join('m_state as state', 'ic.st_code', '=', 'state.st_code')
                       ->join('m_pc as pc', function($query) {
                            $query->on('pc.PC_NO', '=', 'ic.pc_no')
                            ->on('pc.ST_CODE', '=', 'ic.st_code');
                        })

                       ->where('ic.no_of_ps_repoll','!=','0')
                    ->orderbyRaw('state.st_code','pc.PC_NO','asc')
                    ->get()->toArray();
					
			*/		
			
			$rowdatas = DB::table('electors_cdac_other_information as ic')
                 ->select('pc.PC_NO as t_pc_ic_id','ic.date_of_repoll as dt_repoll','ic.no_of_ps_repoll as no_repoll',
                          DB::raw('sum(ic.total_polling_station_s_i_t_c) as tpolling'),
                        'pc.PC_NAME','state.ST_NAME','pc.ST_CODE','pc.PC_NO')
                      ->join('m_state as state', 'ic.st_code', '=', 'state.st_code')
                       ->join('m_pc as pc', function($query) {
                            $query->on('pc.PC_NO', '=', 'ic.PC_NO')
                            ->on('pc.ST_CODE', '=', 'ic.st_code');
                        })
                    ->where('ic.no_of_ps_repoll','>','0')
                    ->groupby('state.st_code','pc.PC_NO')
                    ->get()->toarray();
					
					
				//echo "<pre>";print_r($rowdatas);die;
                     //dd($rowdatas);
					 
					 
                        $data=array();
                        $temp=array();
                        $polling=array();
                        $totalrepoll = 0;
                        $totalpolling = 0;
                        $stname = '';
                        $i=0;
                        $total_no_polling_station = 0;
                    foreach($rowdatas as $key=> $rowdata){
                      $i = ($rowdata->ST_NAME==$stname)?$i:0;
                         $totalpolling = ($rowdata->ST_NAME==$stname)?$totalpolling:0;
                         $data[$rowdata->ST_CODE]['state_name'] = $rowdata->ST_NAME;
                         $data[$rowdata->ST_CODE]['total_no_polling_station'] = $totalpolling + $rowdata->tpolling;
                         $totalpolling  = $rowdata->tpolling;
                      $data[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NO'] = $rowdata->PC_NO;
                      $data[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NAME'] = $rowdata->PC_NAME;
                      $data[$rowdata->ST_CODE]['pcinfo'][$i]['no_repoll'] = $rowdata->no_repoll;
                      $data[$rowdata->ST_CODE]['pcinfo'][$i]['dt_repoll'] = $rowdata->dt_repoll;
                      $data[$rowdata->ST_CODE]['totalrepoll'][] =  $rowdata->no_repoll;
                      $sumdata = @array_sum($data[$rowdata->ST_CODE]['totalrepoll']);
                      $i++;
                      $stname = $rowdata->ST_NAME;
                  }

			
					
				//echo "<pre>";print_r($rowdatas);die;
                     //dd($rowdatas);
					 
					 
                        $data=array();
                        $temp=array();
                        $polling=array();
                        $totalrepoll = 0;
                        $totalpolling = 0;
                        $stname = '';
                        $i=0;
                        $total_no_polling_station = 0;
                    foreach($rowdatas as $key=> $rowdata){
						
                      $i = ($rowdata->ST_NAME==$stname)?$i:0;
                         $totalpolling = ($rowdata->ST_NAME==$stname)?$totalpolling:0;
                         $data[$rowdata->ST_CODE]['state_name'] = $rowdata->ST_NAME;
                         $data[$rowdata->ST_CODE]['total_no_polling_station'] = $rowdata->tpolling;
						$data[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NO'] = $rowdata->PC_NO;
						$data[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NAME'] = $rowdata->PC_NAME;
						$data[$rowdata->ST_CODE]['pcinfo'][$i]['no_repoll'] = $rowdata->no_repoll;
						$data[$rowdata->ST_CODE]['pcinfo'][$i]['dt_repoll'] = $rowdata->dt_repoll;
                      //$data[$rowdata->ST_CODE]['totalrepoll'][] = $totalrepoll+=$rowdata->no_repoll;
                      $i++;
                      $stname = $rowdata->ST_NAME;
					  
                  }



			if($user->designation == 'ROPC'){
			$prefix 	= 'ropc';
			}else if($user->designation == 'CEO'){	
				$prefix 	= 'pcceo';
			}else if($user->role_id == '27'){
				$prefix 	= 'eci-index';
			}else if($user->role_id == '7'){
				$prefix 	= 'eci';
			}

			if($request->path() == "$prefix/details-of-repoll-held-pdf"){
				$pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports/StatisticalReports/Vol2/ecidetailsof-repoll-held-pdf', compact('data','user_data'));

         if(verifyreport(16)){
        
                  $file_name = 'Details of Re-poll Held'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/16/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '16',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
				  return $pdf->download('Details of Re-poll Held.pdf');
				  
				  
			}elseif($request->path() == "$prefix/details-of-repoll-held-xls"){	  
				  
				return Excel::create('Details of Re-poll Held', function($excel) use ($data) {
                              $excel->sheet('mySheet', function($sheet) use ($data) {
                                  $sheet->mergeCells('A1:F1');
                                  $sheet->cells('A1', function($cells) {
                                      $cells->setValue('16 - Details of Repoll Held');
                                      $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });

									
									$sheet->getStyle('A3')->getAlignment()->setWrapText(true);
									$sheet->getStyle('B3')->getAlignment()->setWrapText(true);
									$sheet->getStyle('E3')->getAlignment()->setWrapText(true);
									$sheet->getStyle('D3')->getAlignment()->setWrapText(true);
									$sheet->getStyle('F3')->getAlignment()->setWrapText(true);
									$sheet->setSize('A3', 25,50);
									$sheet->setSize('B3', 25,50);
									$sheet->setSize('D3', 25,50);
									$sheet->setSize('E3', 25,50);
									$sheet->setSize('F3', 25,50);


                                  $sheet->cells('A3', function($cells) {
                                      $cells->setValue(' Name of State/UT ');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  $sheet->cells('B3', function($cells) {
                                      $cells->setValue('Total No. of Polling Stationin in state');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  $sheet->cells('C3', function($cells) {
                                      $cells->setValue(' No. of P.C.');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });

                                  $sheet->cells('D3', function($cells) {
                                      $cells->setValue(' Name of P.C.');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  $sheet->cells('E3', function($cells) {
                                      $cells->setValue('Total No. of Polling Station where repoll held');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });

                                  $sheet->cells('F3', function($cells) {
                                      $cells->setValue('Date of Re-Poll');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });

                                  if (!empty($data)) {
                                     $i = 4;
									 
									 $ftotal = 0;
									 
                                      foreach ($data as $key => $values) {
										  
										  
									$sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);
									$sheet->getStyle('B' . $i)->getAlignment()->setWrapText(true);
									$sheet->getStyle('D' . $i)->getAlignment()->setWrapText(true);
									$sheet->getStyle('E' . $i)->getAlignment()->setWrapText(true);
									$sheet->getStyle('F' . $i)->getAlignment()->setWrapText(true);  
										  
									 $sheet->cell('A' . $i, $values['state_name']);
									 
									  $sheet->cells('B' . $i, function($cells) use($values) {
										  $cells->setValue($values['total_no_polling_station']);
										  $cells->setAlignment('center');
									  });
									  
									  $total = 0;
									  
                                      foreach ($values['pcinfo'] as $keys => $pcvalues) {
                                      
									  $sheet->cells('C' . $i, function($cells) use($pcvalues) {
										  $cells->setValue($pcvalues['PC_NO']);
										  $cells->setAlignment('center');
									  });
									  
									  
									  
                                      $sheet->cell('D' . $i, $pcvalues['PC_NAME']);
									  
									  $sheet->cells('E' . $i, function($cells) use($pcvalues) {
										  $cells->setValue($pcvalues['no_repoll']);
										  $cells->setAlignment('center');
									  });
									  
									  if (trim($pcvalues['dt_repoll']) != 0 && $pcvalues['dt_repoll']){
													
											
													$repoll_dates 	= explode(',',$pcvalues['dt_repoll']);
													$dates_array 	= [];
													foreach($repoll_dates as $res_repoll){
														$dates_array[] = date('d-m-Y', strtotime(trim($res_repoll)));
													}	
										$sheet->cells('F' . $i, function($cells) use($dates_array) {
											  $cells->setValue(implode(', ', $dates_array));
											  $cells->setAlignment('center');
										  }); 
									  }
									  
									  
                                     $total += $pcvalues['no_repoll'];
                                     $ftotal += $pcvalues['no_repoll'];
                                   
                                   $i++;
                                    }
                                   
									$sheet->cells('D' . $i, function($cells) {
										$cells->setValue('Total');
										$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
									});
									
									$sheet->cells('E' . $i, function($cells) use($total) {
										$cells->setValue($total);
										$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
										$cells->setAlignment('center');
									});
									
								   
								    $i++;
                                     }
									$i++;
									
									$sheet->cells('B' . $i, function($cells) {
										$cells->setValue('ALL INDIA');
										$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
									});
									
									
									$sheet->cells('D' . $i, function($cells) {
										$cells->setValue('Grand Total');
										$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
									});
									
									$sheet->cells('E' . $i, function($cells) use($ftotal) {
										$cells->setValue($ftotal);
										$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
										$cells->setAlignment('center');
									});
									
									 
                                  }

                                  $i = $i+3;

          

                          $sheet->mergeCells("A$i:B$i");
                          $sheet->cell('A'.$i, function($cells) {
                            $cells->setValue('Disclaimer');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
                          });

                          $i = $i+1;

                          $sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
                          $sheet->setSize('A'.$i, 25,30);



                          $sheet->mergeCells("A$i:F$i");
                          $sheet->cell('A'.$i, function($cells) {
                          $cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
                          $cells->setFont(array('name' => 'Times New Roman','size' => 10));
                          });
                              });
                          })->export();  
				  
				  
				  
				  
			}else{
			return view('IndexCardReports/StatisticalReports/Vol2/ecidetails-of-repoll-held',  compact('data','user_data'));
			}
    }

public function getcandidateDataSummary(Request $request) {
  //App\models\Admin\CandidateModel::get_count_nominated(\Auth::user()->st_code,\Auth::user()->pc_no);
  return $this->CandidateModel->get_count_nominated('S01',2);
}
//// details of repollheld end



/// performance of state parties
public function performanceofstateparties(Request $request){
    $user = Auth::user();
$user_data = $user;
$performanceofst = DB::select("SELECT a.party_id,a.partyname,a.partyabbre,a.st_code,a.st_name,a.contested, a.won, a.vote_secured_by_party,(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes
FROM `counting_pcmaster` AS cp1 WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fd FROM `counting_pcmaster` AS cp WHERE a.party_id = cp.party_id AND a.st_code = cp.st_code AND a.party_id != (SELECT lead_cand_partyid FROM winning_leading_candidate AS w1 WHERE w1.pc_no = cp.pc_no AND w1.st_code = cp.st_code) GROUP BY cp.party_id) AS fd,b.egeneral,b.total_vote

FROM(
SELECT p.party_id,q.partyname,q.partyabbre,m.st_code,m.st_name,COUNT(DISTINCT p.candidate_id)contested, COUNT(DISTINCT w.`candidate_id`)won, SUM(p.total_vote) vote_secured_by_party FROM `counting_pcmaster` p LEFT JOIN `winning_leading_candidate` w ON p.candidate_id=w.candidate_id JOIN m_party q ON p.party_id=q.ccode JOIN candidate_nomination_detail cnd ON p.candidate_id=cnd.candidate_id JOIN m_state m ON m.st_code = p.st_code WHERE cnd.cand_party_type = 'S' and cnd.application_status = '6' and cnd.finalaccepted = '1' GROUP BY p.party_id,m.st_code) a
JOIN
(SELECT TEMP.*,SUM(cpm.total_vote) AS 'total_vote' FROM (SELECT m.st_code, m.st_name,mpc.pc_no, mpc.PC_NAME,SUM(cda.gen_electors_male + cda.gen_electors_female +cda.gen_electors_other + cda.nri_male_electors + cda.nri_female_electors +cda.nri_third_electors + cda.service_male_electors + cda.service_female_electors +cda.service_third_electors) AS egeneral
FROM electors_cdac cda,m_pc mpc ,m_state m WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code GROUP BY mpc.st_code)TEMP,counting_pcmaster cpm WHERE TEMP.st_code=cpm.st_code and cpm.party_id != '1180' GROUP BY TEMP.st_code) b ON a.st_code = b.st_code ");
       $i =0;
	   
	//echo '<pre>'; print_r($performanceofst);  die;
	   
	   
				foreach($performanceofst as $rowsdata){
                    //$arraydata[$rowsdata->CCODE]['partyabbr'] = $rowsdata->PARTYABBRE;
                    $arraydata[$rowsdata->party_id]['partyname'] = $rowsdata->partyname;
                    $arraydata[$rowsdata->party_id]['partyabbre'] = $rowsdata->partyabbre;
					$arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['partyname'] = $rowsdata->partyname;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['partyabbre'] = $rowsdata->partyabbre;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['statename'] = $rowsdata->st_name;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['contested'] = $rowsdata->contested;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['won'] = $rowsdata->won;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['df'] = $rowsdata->fd;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['Securedvotes'] = $rowsdata->vote_secured_by_party;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['total_vote'] = $rowsdata->total_vote;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['total_electorsdata'] = $rowsdata->egeneral;

                    if($rowsdata->total_vote !=0){
						
					$arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['poledvotespercent'] = round($rowsdata->vote_secured_by_party/$rowsdata->total_vote*100,2);

                   }else{ $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['poledvotespercent'] = '0%';
                   }

                   if($rowsdata->egeneral !=0){
                   $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['totalelectors'] = round($rowsdata->vote_secured_by_party/$rowsdata->egeneral*100,2);

                   }else{ $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['totalelectors'] = '0%'; }
                   $arraydata[$rowsdata->party_id]['totalcontested'][] =  $rowsdata->contested;
                   $arraydata[$rowsdata->party_id]['won'][] =  $rowsdata->won;
                   $arraydata[$rowsdata->party_id]['DF'][] =  $rowsdata->fd;
                    $arraydata[$rowsdata->party_id]['Securedvotes'][] =  $rowsdata->vote_secured_by_party;
// votepercents
                    if($rowsdata->total_vote !=0){
                   $arraydata[$rowsdata->party_id]['totalpercentvote'][] = round($rowsdata->vote_secured_by_party/$rowsdata->total_vote*100,2);

                   }else{ $arraydata[$rowsdata->party_id]['totalpercentvote'][] = '0%';
                   }
//electors percents
                   if($rowsdata->egeneral !=0){
                   $arraydata[$rowsdata->party_id]['totalpercentelectors'][] = round(($rowsdata->vote_secured_by_party/$rowsdata->egeneral)*100,2);
                   }else{ $arraydata[$rowsdata->party_id]['totalpercentelectors'][] = '0%'; }

					


                   $i++;}
				   
				   
				if($user->designation == 'ROPC'){
					$prefix 	= 'ropc';
				}else if($user->designation == 'CEO'){	
					$prefix 	= 'pcceo';
				}else if($user->role_id == '27'){
					$prefix 	= 'eci-index';
				}else if($user->role_id == '7'){
					$prefix 	= 'eci';
				} 
				   
				   
			if($request->path() == "$prefix/performance-of-state-partys-pdf"){
				
				$pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports/StatisticalReports/Vol2/eciperformanceofstateparties-pdf', compact('arraydata'));

         if(verifyreport(21)){
        
                  $file_name = 'Performance of State Parties'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/21/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '21',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
                return $pdf->download('Performance of State Parties.pdf');
				
			}elseif($request->path() == "$prefix/performance-of-state-partys-excel"){
				
				return Excel::create('Performance of State Parties', function($excel) use ($arraydata) {
          $excel->sheet('mySheet', function($sheet) use ($arraydata) {
                                  $sheet->mergeCells('B1:J1');
                                  $sheet->mergeCells('E2:G2');
                                  $sheet->mergeCells('I2:J2');
                                  $sheet->cells('B1', function($cells) {
                                      $cells->setValue('21 - Performance of State Parties');
                                      $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });

                                  $sheet->cells('B2', function($cells) {
                                      $cells->setValue('Party Abbre');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  $sheet->cells('C2', function($cells) {
                                      $cells->setValue('Party Name');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  $sheet->cells('D2', function($cells) {
                                      $cells->setValue('State NAME');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });

                                  $sheet->cells('E2', function($cells) {
                                      $cells->setValue('Candidates');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });

                                  $sheet->cells('H2', function($cells) {
                                      $cells->setValue('Votes Secured By Party');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  $sheet->cells('I2', function($cells) {
                                      $cells->setValue('% Of Votes Secured');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });

                                  $sheet->cells('E3', function($cells) {
                                      $cells->setValue(' Contested ');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  $sheet->cells('F3', function($cells) {
                                      $cells->setValue(' Won ');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  $sheet->cells('G3', function($cells) {
                                      $cells->setValue(' DF ');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  
                                  $sheet->cells('I3', function($cells) {
                                      $cells->setValue('Over Total Elector in the State');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  $sheet->cells('J3', function($cells) {
                                      $cells->setValue('Over TotalVotes Polled in th State');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });

                                  if (!empty($arraydata)) {
                                     $i = 4;
                                     $ftotal =0;
                                     $total=0;
                                     $sum=0;
									 
									$grand_total_contested = 0;
									$grand_total_won = 0;
									$grand_total_df = 0;
									$grand_total_vote_secure = 0;
									$grand_total_vote = 0;
									$grand_total_electors = 0;
									 									 
                                    foreach ($arraydata as $key => $values) {
										
									$total_vote_secure = 0;
									$total_vote = 0;
									$total_electors = 0;	
										
                                    foreach ($values['partydata'] as $keys => $pcvalues) {
                                      $sheet->cell('B' . $i, $pcvalues['partyabbre']);
                                      $sheet->cell('C' . $i, $pcvalues['partyname']);
                                      $sheet->cell('D' . $i, $pcvalues['statename']);
                                      $sheet->cell('E' . $i, $pcvalues['contested']);
                                      $sheet->cell('F' . $i, ($pcvalues['won'] > 0 ? $pcvalues['won']:'=(0)'));
                                      $sheet->cell('G' . $i, ($pcvalues['df'] > 0 ? $pcvalues['df']:'=(0)'));
                                      $sheet->cell('H' . $i, $pcvalues['Securedvotes']);
                                      $sheet->cell('I' . $i, $pcvalues['totalelectors']);
                                      $sheet->cell('J' . $i, $pcvalues['poledvotespercent']);
                                       $i++;
									   
									    $total_vote_secure += $pcvalues['Securedvotes'];
										$total_vote += $pcvalues['total_vote'];
										$total_electors += $pcvalues['total_electorsdata'];
																		
										$grand_total_contested += $pcvalues['contested'];
										$grand_total_won += $pcvalues['won'];
										$grand_total_df += $pcvalues['df'];
										$grand_total_vote_secure += $pcvalues['Securedvotes'];
										$grand_total_vote += $pcvalues['total_vote'];
										$grand_total_electors += $pcvalues['total_electorsdata'];
										$partyabbre = $pcvalues['partyabbre'];
										$partyname = $pcvalues['partyname'];
									   
									   
                                    }
									
									$sheet->cell('B' . $i, $partyabbre);
                                    $sheet->cell('C' . $i, $partyname);
									$sheet->cells('D' . $i, function($cells) {
                                      $cells->setValue('Party Total');
									  $cells->setFont(array('name' => 'Times New Roman','bold' => true));;
                                    });
									

                                    $sheet->cell('E' . $i, (array_sum($values['totalcontested']))?array_sum($values['totalcontested']):'=(0)');
                                    $sheet->cell('F' . $i, (array_sum($values['won']))?array_sum($values['won']):'=(0)');
                                    $sheet->cell('G' . $i, (array_sum($values['DF']))?array_sum($values['DF']):'=(0)');

                                    $sheet->cell('H' . $i, (array_sum($values['Securedvotes']))?array_sum($values['Securedvotes']):'=(0)');

									 $sheet->cell('I' . $i, (((($total_vote_secure)/$total_electors)*100) > 0)?((($total_vote_secure)/$total_electors)*100):'=(0)');
                                    $sheet->cell('J' . $i, (((($total_vote_secure)/$total_vote)*100) > 0)?((($total_vote_secure)/$total_vote)*100):'=(0)');
                                   

                                   $i++;
                                   }
								   
								   $sheet->mergeCells("A$i:C$i");
									$sheet->cells('D' . $i, function($cells) {
                                      $cells->setValue('Grand Total');
									  $cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
                                    });
									

                                    $sheet->cell('E' . $i, ($grand_total_contested > 0)? $grand_total_contested:'=(0)');
                                    $sheet->cell('F' . $i, ($grand_total_won > 0)? $grand_total_won:'=(0)');
                                    $sheet->cell('G' . $i, ($grand_total_df > 0)? $grand_total_df:'=(0)');
                                    $sheet->cell('H' . $i, ($grand_total_vote_secure > 0)? $grand_total_vote_secure:'=(0)');

                                    $sheet->cell('I' . $i, (round(((($grand_total_vote_secure)/$grand_total_vote)*100),2) > 0)?round(((($grand_total_vote_secure)/$grand_total_vote)*100),2):'=(0)');
                                    $sheet->cell('J' . $i, (round(((($grand_total_vote_secure)/$grand_total_electors)*100),2) > 0)?round(((($grand_total_vote_secure)/$grand_total_electors)*100),2):'=(0)');

                                   $i++;
								   
								   
								   
                                  }

                                  $i = $i+3;

          

                              $sheet->mergeCells("A$i:B$i");
                              $sheet->cell('A'.$i, function($cells) {
                                $cells->setValue('Disclaimer');
                                $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
                              });

                              $i = $i+1;

                              $sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
                              $sheet->setSize('A'.$i, 25,30);



                              $sheet->mergeCells("A$i:J$i");
                              $sheet->cell('A'.$i, function($cells) {
                              $cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
                              $cells->setFont(array('name' => 'Times New Roman','size' => 10));
                              });
                              });
                          })->export();

				
			}else{
				return view('IndexCardReports/StatisticalReports/Vol2/eciperformanceofstateparties',compact('arraydata','user_data'));
			}  
              
		}
		
public function performanceofstatepartiespdf(Request $request){
    $user = Auth::user();
$user_data = $user;
$performanceofst = DB::select("SELECT a.party_id,a.partyname,a.partyabbre,a.st_code,a.st_name,a.contested, a.won, a.vote_secured_by_party,(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes
FROM `counting_pcmaster` AS cp1 WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fd FROM `counting_pcmaster` AS cp WHERE a.party_id = cp.party_id AND a.st_code = cp.st_code AND a.party_id != (SELECT lead_cand_partyid FROM winning_leading_candidate AS w1 WHERE w1.pc_no = cp.pc_no AND w1.st_code = cp.st_code) GROUP BY cp.party_id) AS fd,b.egeneral,b.total_vote

FROM(
SELECT p.party_id,q.partyname,q.partyabbre,m.st_code,m.st_name,COUNT(DISTINCT p.candidate_id)contested, COUNT(DISTINCT w.`candidate_id`)won, SUM(p.total_vote) vote_secured_by_party FROM `counting_pcmaster` p LEFT JOIN `winning_leading_candidate` w ON p.candidate_id=w.candidate_id JOIN m_party q ON p.party_id=q.ccode JOIN m_state m ON m.st_code = p.st_code WHERE q.PARTYTYPE = 'S' GROUP BY p.party_id,m.st_code) a
JOIN
(SELECT TEMP.*,SUM(cpm.total_vote) AS 'total_vote' FROM (SELECT m.st_code, m.st_name,mpc.pc_no, mpc.PC_NAME,SUM(cda.electors_total) AS egeneral
FROM electors_cdac cda,m_pc mpc ,m_state m WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code GROUP BY mpc.st_code)TEMP,counting_pcmaster cpm WHERE TEMP.st_code=cpm.st_code GROUP BY TEMP.st_code) b ON a.st_code = b.st_code");
       $i =0;
foreach($performanceofst as $rowsdata){
                    //$arraydata[$rowsdata->CCODE]['partyabbr'] = $rowsdata->PARTYABBRE;
                    $arraydata[$rowsdata->party_id]['partyname'] = $rowsdata->partyname;
                    $arraydata[$rowsdata->party_id]['partyabbre'] = $rowsdata->partyabbre;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['partyname'] = $rowsdata->partyname;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['partyabbre'] = $rowsdata->partyabbre;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['statename'] = $rowsdata->st_name;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['contested'] = $rowsdata->contested;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['won'] = $rowsdata->won;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['df'] = $rowsdata->fd;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['Securedvotes'] = $rowsdata->vote_secured_by_party;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['total_vote'] = $rowsdata->total_vote;
					$arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['total_electorsdata'] = $rowsdata->egeneral;
                    if($rowsdata->total_vote !=0){
                  $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['poledvotespercent'] = round($rowsdata->vote_secured_by_party/$rowsdata->total_vote*100,2);

                   }else{ $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['poledvotespercent'] = '0%';
                   }

                   if($rowsdata->egeneral !=0){
                   $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['totalelectors'] = round($rowsdata->vote_secured_by_party/$rowsdata->egeneral*100,2);

                   }else{ $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['totalelectors'] = '0%'; }
                  $arraydata[$rowsdata->party_id]['totalcontested'][] =  $rowsdata->contested;
                   $arraydata[$rowsdata->party_id]['won'][] =  $rowsdata->won;
                   $arraydata[$rowsdata->party_id]['DF'][] =  $rowsdata->fd;
                    $arraydata[$rowsdata->party_id]['Securedvotes'][] =  $rowsdata->vote_secured_by_party;
// votepercents
                    if($rowsdata->total_vote !=0){
                   $arraydata[$rowsdata->party_id]['totalpercentvote'][] = round($rowsdata->vote_secured_by_party/$rowsdata->total_vote*100,2);

                   }else{ $arraydata[$rowsdata->party_id]['totalpercentvote'][] = '0%';
                   }
//electors percents
                   if($rowsdata->egeneral !=0){
                   $arraydata[$rowsdata->party_id]['totalpercentelectors'][] = round(($rowsdata->vote_secured_by_party/$rowsdata->egeneral)*100,2);
                   }else{ $arraydata[$rowsdata->party_id]['totalpercentelectors'][] = '0%'; }

                   $i++;}
                //echo "<pre>";print_r($arraydata);die;
                $pdf = PDF::loadView('IndexCardReports/StatisticalReports/Vol2/eciperformanceofstateparties-pdf', compact('arraydata'));
                return $pdf->download('Performance of State Parties.pdf');
}


public function performanceofstatepartiesexcel(Request $request){
    $user = Auth::user();
$user_data = $user;
$performanceofst = DB::select("SELECT a.party_id,a.partyname,a.partyabbre,a.st_code,a.st_name,a.contested, a.won, a.vote_secured_by_party,(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes
FROM `counting_pcmaster` AS cp1 WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fd FROM `counting_pcmaster` AS cp WHERE a.party_id = cp.party_id AND a.st_code = cp.st_code AND a.party_id != (SELECT lead_cand_partyid FROM winning_leading_candidate AS w1 WHERE w1.pc_no = cp.pc_no AND w1.st_code = cp.st_code) GROUP BY cp.party_id) AS fd,b.egeneral,b.total_vote

FROM(
SELECT p.party_id,q.partyname,q.partyabbre,m.st_code,m.st_name,COUNT(DISTINCT p.candidate_id)contested, COUNT(DISTINCT w.`candidate_id`)won, SUM(p.total_vote) vote_secured_by_party FROM `counting_pcmaster` p LEFT JOIN `winning_leading_candidate` w ON p.candidate_id=w.candidate_id JOIN m_party q ON p.party_id=q.ccode JOIN m_state m ON m.st_code = p.st_code WHERE q.PARTYTYPE = 'S' GROUP BY p.party_id,m.st_code) a
JOIN
(SELECT TEMP.*,SUM(cpm.total_vote) AS 'total_vote' FROM (SELECT m.st_code, m.st_name,mpc.pc_no, mpc.PC_NAME,SUM(cda.electors_total) AS egeneral
FROM electors_cdac cda,m_pc mpc ,m_state m WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code GROUP BY mpc.st_code)TEMP,counting_pcmaster cpm WHERE TEMP.st_code=cpm.st_code GROUP BY TEMP.st_code) b ON a.st_code = b.st_code");
       $i =0;
foreach($performanceofst as $rowsdata){

                    $arraydata[$rowsdata->party_id]['total'] ='Total';

                    $arraydata[$rowsdata->party_id]['partyinfo'][$rowsdata->st_code]['partyname'] = $rowsdata->partyname;
                    $arraydata[$rowsdata->party_id]['partyinfo'][$rowsdata->st_code]['partyabbre'] = $rowsdata->partyabbre;
                    $arraydata[$rowsdata->party_id]['partyinfo'][$rowsdata->st_code]['statename'] = $rowsdata->st_name;
                    $arraydata[$rowsdata->party_id]['partyinfo'][$rowsdata->st_code]['contested'] = $rowsdata->contested;
                    $arraydata[$rowsdata->party_id]['partyinfo'][$rowsdata->st_code]['won'] = $rowsdata->won;
                    $arraydata[$rowsdata->party_id]['partyinfo'][$rowsdata->st_code]['df'] = $rowsdata->fd;
                    $arraydata[$rowsdata->party_id]['partyinfo'][$rowsdata->st_code]['Securedvotes'] = $rowsdata->vote_secured_by_party;
                    $arraydata[$rowsdata->party_id]['partyinfo'][$rowsdata->st_code]['total_vote'] = $rowsdata->total_vote;
					$arraydata[$rowsdata->party_id]['partyinfo'][$rowsdata->st_code]['total_electorsdata'] = $rowsdata->egeneral;
                  if($rowsdata->total_vote !=0){
                  $arraydata[$rowsdata->party_id]['partyinfo'][$rowsdata->st_code]['poledvotespercent'] = $rowsdata->vote_secured_by_party/($rowsdata->total_vote*100);

                   }else{ $arraydata[$rowsdata->party_id]['partyinfo'][$rowsdata->st_code]['poledvotespercent'] = '0%';
                   }

                   if($rowsdata->egeneral !=0){
                   $arraydata[$rowsdata->party_id]['partyinfo'][$rowsdata->st_code]['totalelectors'] = ($rowsdata->vote_secured_by_party/$rowsdata->egeneral*100);
                   }else{ $arraydata[$rowsdata->party_id]['partyinfo'][$rowsdata->st_code]['totalelectors'] = '0%'; }

                   $arraydata[$rowsdata->party_id]['totalcontested'][] =  $rowsdata->contested;

                   $arraydata[$rowsdata->party_id]['won'][] =  $rowsdata->won;
                   $arraydata[$rowsdata->party_id]['DF'][] =  $rowsdata->fd;
                    $arraydata[$rowsdata->party_id]['Securedvotes'][] =  $rowsdata->vote_secured_by_party;
// votepercents
                    if($rowsdata->total_vote !=0){
                   $arraydata[$rowsdata->party_id]['totalpercentvote'][] = ($rowsdata->vote_secured_by_party/$rowsdata->total_vote*100);

                   }else{ $arraydata[$rowsdata->party_id]['totalpercentvote'][] = '0%';
                   }
//electors percents
                   if($rowsdata->egeneral !=0){
                   $arraydata[$rowsdata->party_id]['totalpercentelectors'][] = (($rowsdata->vote_secured_by_party/$rowsdata->egeneral)*100);
                   }else{ $arraydata[$rowsdata->party_id]['totalpercentelectors'][] = '0%'; }
                   $i++;}

  //echo "<pre>";print_r($arraydata);die;

  return Excel::create('Performance of State Parties', function($excel) use ($arraydata) {
          $excel->sheet('mySheet', function($sheet) use ($arraydata) {
                                  $sheet->mergeCells('B1:J1');
                                  $sheet->mergeCells('E2:G2');
                                  $sheet->mergeCells('I2:J2');
                                  $sheet->cells('B1', function($cells) {
                                      $cells->setValue('21 - Performance of State Parties');
                                      $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });

                                  $sheet->cells('B2', function($cells) {
                                      $cells->setValue('Party Abbre');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  $sheet->cells('C2', function($cells) {
                                      $cells->setValue('Party Name');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  $sheet->cells('D2', function($cells) {
                                      $cells->setValue('State NAME');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });

                                  $sheet->cells('E2', function($cells) {
                                      $cells->setValue('Candidates');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });

                                  $sheet->cells('H2', function($cells) {
                                      $cells->setValue('Votes Secured By Party');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  $sheet->cells('I2', function($cells) {
                                      $cells->setValue('% Of Votes Secured');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });

                                  $sheet->cells('E3', function($cells) {
                                      $cells->setValue(' Contested ');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  $sheet->cells('F3', function($cells) {
                                      $cells->setValue(' Won ');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  $sheet->cells('G3', function($cells) {
                                      $cells->setValue(' DF ');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  
                                  $sheet->cells('I3', function($cells) {
                                      $cells->setValue('Over Total Elector in the State');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });
                                  $sheet->cells('J3', function($cells) {
                                      $cells->setValue('Over TotalVotes Polled in th State');
									  $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
                                      $cells->setAlignment('center');
                                  });

                                  if (!empty($arraydata)) {
                                     $i = 4;
                                     $ftotal =0;
                                     $total=0;
                                     $sum=0;
									 
									$grand_total_contested = 0;
									$grand_total_won = 0;
									$grand_total_df = 0;
									$grand_total_vote_secure = 0;
									$grand_total_vote = 0;
									$grand_total_electors = 0;
									 									 
                                    foreach ($arraydata as $key => $values) {
										
									$total_vote_secure = 0;
									$total_vote = 0;
									$total_electors = 0;	
										
                                    foreach ($values['partyinfo'] as $keys => $pcvalues) {
                                      $sheet->cell('B' . $i, $pcvalues['partyabbre']);
                                      $sheet->cell('C' . $i, $pcvalues['partyname']);
                                      $sheet->cell('D' . $i, $pcvalues['statename']);
                                      $sheet->cell('E' . $i, $pcvalues['contested']);
                                      $sheet->cell('F' . $i, ($pcvalues['won'] > 0 ? $pcvalues['won']:'=(0)'));
                                      $sheet->cell('G' . $i, ($pcvalues['df'] > 0 ? $pcvalues['df']:'=(0)'));
                                      $sheet->cell('H' . $i, $pcvalues['Securedvotes']);
                                      $sheet->cell('I' . $i, $pcvalues['poledvotespercent']);
                                      $sheet->cell('J' . $i, $pcvalues['totalelectors']);
                                       $i++;
									   
									    $total_vote_secure += $pcvalues['Securedvotes'];
										$total_vote += $pcvalues['total_vote'];
										$total_electors += $pcvalues['total_electorsdata'];
																		
										$grand_total_contested += $pcvalues['contested'];
										$grand_total_won += $pcvalues['won'];
										$grand_total_df += $pcvalues['df'];
										$grand_total_vote_secure += $pcvalues['Securedvotes'];
										$grand_total_vote += $pcvalues['total_vote'];
										$grand_total_electors += $pcvalues['total_electorsdata'];
										$partyabbre = $pcvalues['partyabbre'];
										$partyname = $pcvalues['partyname'];
									   
									   
                                    }
									
									$sheet->cell('B' . $i, $partyabbre);
                                    $sheet->cell('C' . $i, $partyname);
									$sheet->cells('D' . $i, function($cells) {
                                      $cells->setValue('Party Total');
									  $cells->setFont(array('name' => 'Times New Roman','bold' => true));;
                                    });
									

                                    $sheet->cell('E' . $i, (array_sum($values['totalcontested']))?array_sum($values['totalcontested']):'=(0)');
                                    $sheet->cell('F' . $i, (array_sum($values['won']))?array_sum($values['won']):'=(0)');
                                    $sheet->cell('G' . $i, (array_sum($values['DF']))?array_sum($values['DF']):'=(0)');

                                    $sheet->cell('H' . $i, (array_sum($values['Securedvotes']))?array_sum($values['Securedvotes']):'=(0)');


                                    $sheet->cell('I' . $i, (((($total_vote_secure)/$total_vote)*100) > 0)?((($total_vote_secure)/$total_vote)*100):'=(0)');
                                    $sheet->cell('J' . $i, (((($total_vote_secure)/$total_electors)*100) > 0)?((($total_vote_secure)/$total_electors)*100):'=(0)');

                                   $i++;
                                   }
								   
								   $sheet->mergeCells("A$i:C$i");
									$sheet->cells('D' . $i, function($cells) {
                                      $cells->setValue('Grand Total');
									  $cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
                                    });
									

                                    $sheet->cell('E' . $i, ($grand_total_contested > 0)? $grand_total_contested:'=(0)');
                                    $sheet->cell('F' . $i, ($grand_total_won > 0)? $grand_total_won:'=(0)');
                                    $sheet->cell('G' . $i, ($grand_total_df > 0)? $grand_total_df:'=(0)');
                                    $sheet->cell('H' . $i, ($grand_total_vote_secure > 0)? $grand_total_vote_secure:'=(0)');

                                    $sheet->cell('I' . $i, (round(((($grand_total_vote_secure)/$grand_total_vote)*100),2) > 0)?round(((($grand_total_vote_secure)/$grand_total_vote)*100),2):'=(0)');
                                    $sheet->cell('J' . $i, (round(((($grand_total_vote_secure)/$grand_total_electors)*100),2) > 0)?round(((($grand_total_vote_secure)/$grand_total_electors)*100),2):'=(0)');

                                   $i++;
								   
								   
								   
                                  }
                              });
                          })->export();

}


/// performance of state parties end





    }
