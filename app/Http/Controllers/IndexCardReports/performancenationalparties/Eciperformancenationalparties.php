<?php

namespace App\Http\Controllers\IndexCardReports\performancenationalparties;
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
class Eciperformancenationalparties extends Controller
{
  public function __construct(){
  $this->middleware('eci');
  $this->CandidateModel  = new CandidateModel();
  $this->report_model = new ReportModel();
  $this->voting_model = new PollDayModel();
  $this->voting_model = new PollDayModel();
  $this->commonModel  = new commonModel();
  if(!Auth::user()){
    return redirect('/officer-login');
  }
  }




    public function index(Request $request)
    {
         $dt = Carbon::now();
         $user = Auth::user();

		 $data = DB::select('SELECT a.partyname,contested,won,vote AS evm_vote,total_vote,
			(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),2) < .16 THEN 1 ELSE 0 END) as fd FROM `counting_pcmaster` as cp
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

		 $totalElectors  = DB::select('SELECT sum(`electors_total`) as total_electors FROM `electors_cdac` WHERE year = 2019');

		 $totalVotes  = DB::select('SELECT sum(`total_vote`) as totalVotes FROM `counting_pcmaster`');

		 echo '<pre>';print_r($data);die;

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
	// 		(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),2) < .16 THEN 1 ELSE 0 END) as fd FROM `counting_pcmaster` as cp
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

     $successfullcondidate = DB::select("SELECT m.st_code,`m`.`PC_TYPE`, `m`.`PC_NAME`, `m`.`PC_NO`, SUM(total_vote) as TotalVote, `winn`.`st_name`, `winn`.`lead_cand_name`, `winn`.`lead_party_abbre`, `symbol`.`SYMBOL_DES`, `winn`.`margin`, `winn`.`trail_total_vote`, `winn`.`st_code`
      FROM winning_leading_candidate AS winn INNER JOIN m_pc AS m ON m.st_code = winn.st_code AND m.pc_no = winn.pc_no INNER JOIN candidate_nomination_detail AS cond ON cond.candidate_id = winn.candidate_id AND cond.application_status = 6 AND finalaccepted = 1 INNER JOIN counting_pcmaster AS counting ON counting.st_code = winn.st_code AND winn.pc_no = counting.pc_no INNER JOIN m_symbol AS symbol ON cond.symbol_id = symbol.SYMBOL_NO GROUP BY m.st_code,m.pc_no");

                       //echo "<pre>";print_r($successfullcondidate);die;
     foreach($successfullcondidate as $key=>$listofsuccessfulldata){

     $arraydata[$listofsuccessfulldata->st_name]['state'] = $listofsuccessfulldata->st_name;
     $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Pc_Name'] = $listofsuccessfulldata->PC_NAME;
     $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['PC_TYPE'] = $listofsuccessfulldata->PC_TYPE;
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

       $successfullcondidate = DB::select("SELECT m.st_code,`m`.`PC_TYPE`, `m`.`PC_NAME`, `m`.`PC_NO`, SUM(total_vote) as TotalVote, `winn`.`st_name`, `winn`.`lead_cand_name`, `winn`.`lead_party_abbre`, `symbol`.`SYMBOL_DES`, `winn`.`margin`, `winn`.`trail_total_vote`, `winn`.`st_code`
       FROM winning_leading_candidate AS winn INNER JOIN m_pc AS m ON m.st_code = winn.st_code AND m.pc_no = winn.pc_no INNER JOIN candidate_nomination_detail AS cond ON cond.candidate_id = winn.candidate_id AND cond.application_status = 6 AND finalaccepted = 1 INNER JOIN counting_pcmaster AS counting ON counting.st_code = winn.st_code AND winn.pc_no = counting.pc_no INNER JOIN m_symbol AS symbol ON cond.symbol_id = symbol.SYMBOL_NO GROUP BY m.st_code,m.pc_no");


  foreach($successfullcondidate as $key=>$listofsuccessfulldata){

  $arraydata[$listofsuccessfulldata->st_name]['state'] = $listofsuccessfulldata->st_name;
   $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Pc_Name'] = $listofsuccessfulldata->PC_NAME;
   $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['PC_TYPE'] = $listofsuccessfulldata->PC_TYPE;
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
        $pdf = PDF::loadView('IndexCardReports/StatisticalReports.Vol2.ecisuccessfull-candidate-pdf', compact('arraydata'));
        return $pdf->download('successfull-condidate.pdf');
    }

     public function successfullcondidateexcell(Request $request) {
         set_time_limit(6000);
       $successfullcondidate = DB::select("SELECT m.st_code,`m`.`PC_TYPE`, `m`.`PC_NAME`, `m`.`PC_NO`, SUM(total_vote) as TotalVote, `winn`.`st_name`, `winn`.`lead_cand_name`, `winn`.`lead_party_abbre`, `symbol`.`SYMBOL_DES`, `winn`.`margin`, `winn`.`trail_total_vote`, `winn`.`st_code`
       FROM winning_leading_candidate AS winn INNER JOIN m_pc AS m ON m.st_code = winn.st_code AND m.pc_no = winn.pc_no INNER JOIN candidate_nomination_detail AS cond ON cond.candidate_id = winn.candidate_id AND cond.application_status = 6 AND finalaccepted = 1 INNER JOIN counting_pcmaster AS counting ON counting.st_code = winn.st_code AND winn.pc_no = counting.pc_no INNER JOIN m_symbol AS symbol ON cond.symbol_id = symbol.SYMBOL_NO GROUP BY m.st_code,m.pc_no");

  foreach($successfullcondidate as $key=>$listofsuccessfulldata){

  $arraydata[$listofsuccessfulldata->st_name][$listofsuccessfulldata->PC_NO]['state'] = $listofsuccessfulldata->st_name;
   $arraydata[$listofsuccessfulldata->st_name][$listofsuccessfulldata->PC_NO]['Pc_Name'] = $listofsuccessfulldata->PC_NAME;
   $arraydata[$listofsuccessfulldata->st_name][$listofsuccessfulldata->PC_NO]['PC_TYPE'] = $listofsuccessfulldata->PC_TYPE;
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

   return Excel::create('successfull_candidate'.'_'.date('d-m-Y').'_'.time(), function($excel) use ($arraydata) {
                       $excel->sheet('mySheet', function($sheet) use ($arraydata) {
                           $sheet->mergeCells('A1:K1');

                           $sheet->cells('A1', function($cells) {
   $cells->setValue('Successful Candidate');
                               $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                               $cells->setFontColor("#ffffff");
                               $cells->setBackground("#042179");
                               $cells->setAlignment('center');
                           });


                           $sheet->cell('A2', function($cell) {
                               $cell->setValue('STATE');
                                $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });

                           $sheet->cell('B2', function($cell) {
                               $cell->setValue('CONSTITUENCY');
                               $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });
                           $sheet->cell('C2', function($cell) {
                               $cell->setValue('CATEGORY');
                               $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });
                           $sheet->cell('D2', function($cell) {
                               $cell->setValue('WINNER');
                               $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });

                           $sheet->cell('E2', function($cell) {
                               $cell->setValue('PARTY NAME');
                               $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });
                           $sheet->cell('F2', function($cell) {
                               $cell->setValue('PARTY SYMBOL');
                               $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });
                           $sheet->cell('G2', function($cell) {
                               $cell->setValue('MARGIN');
                               $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });
                           $sheet->cell('H2', function($cell) {
                               $cell->setValue('%');
                               $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });

                           $i = 3;
                           if (!empty($arraydata)) {
                               foreach ($arraydata as $key => $values) {
                        foreach ($values as $key => $value) {
                                   $sheet->cell('A' . $i, $value['state']);
                                   $sheet->cell('B' . $i, $value['Pc_Name']);
                                   $sheet->cell('C' . $i, $value['PC_TYPE']);
                                   $sheet->cell('D' . $i, $value['Cand_Name']);
                                   $sheet->cell('E' . $i, $value['Party_Abbre']);
                                   $sheet->cell('F' . $i, $value['Party_symbol']);
                                   $sheet->cell('G' . $i, $value['margin']);
                                   $sheet->cell('H' . $i, $value['percent']);
                              $i++; }
                               }
                           }
                       });
                   })->export();



 }
//// performance of national party start
public function performanceofnationalparties(Request $request) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $user;
        $data = DB::select('SELECT a.partyname,contested,won,vote AS evm_vote,total_vote,
			(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),2) < .16 THEN 1 ELSE 0 END) as fd FROM `counting_pcmaster` as cp
			 WHERE a.party_id = cp.party_id AND a.party_id != (select lead_cand_partyid from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code) group by cp.party_id) as fd
			FROM
			(SELECT party_id,partyname,COUNT(DISTINCT p.candidate_id)contested,
			 COUNT(DISTINCT w.`candidate_id`)won FROM `counting_pcmaster` p
			JOIN `winning_leading_candidate` w ON p.party_id=w.`lead_cand_partyid`
			JOIN m_party q ON p.party_id=q.ccode
			WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)a JOIN
			(SELECT partyname,SUM(evm_vote)vote,SUM(total_vote)total_vote FROM `counting_pcmaster` m
			JOIN m_party q ON m.party_id=q.ccode WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)b
			ON a.partyname=b.partyname');

        $totalElectors = DB::select('SELECT sum(`electors_total`) as total_electors FROM `electors_cdac` WHERE year = 2019');

        $totalVotes = DB::select('SELECT sum(`total_vote`) as totalVotes FROM `counting_pcmaster`');

        return view('IndexCardReports/StatisticalReports/Vol1/eciperformanceofnatiionalparties', compact('data', 'totalElectors', 'totalVotes', 'user_data'));

    }


    public function performanceofnatiionalpartiespdf(Request $request) {
            $user = Auth::user();
            $user_data = $user;

            $data = DB::select('SELECT a.partyname,contested,won,vote AS evm_vote,total_vote,
    			(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),2) < .16 THEN 1 ELSE 0 END) as fd FROM `counting_pcmaster` as cp
    			 WHERE a.party_id = cp.party_id AND a.party_id != (select lead_cand_partyid from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code) group by cp.party_id) as fd
    			FROM
    			(SELECT party_id,partyname,COUNT(DISTINCT p.candidate_id)contested,
    			 COUNT(DISTINCT w.`candidate_id`)won FROM `counting_pcmaster` p
    			JOIN `winning_leading_candidate` w ON p.party_id=w.`lead_cand_partyid`
    			JOIN m_party q ON p.party_id=q.ccode
    			WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)a JOIN
    			(SELECT partyname,SUM(evm_vote)vote,SUM(total_vote)total_vote FROM `counting_pcmaster` m
    			JOIN m_party q ON m.party_id=q.ccode WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)b
    			ON a.partyname=b.partyname');

            $totalElectors = DB::select('SELECT sum(`electors_total`) as total_electors FROM `electors_cdac` WHERE year = 2019');
            $totalVotes = DB::select('SELECT sum(`total_vote`) as totalVotes FROM `counting_pcmaster`');
            $pdf = PDF::loadView('IndexCardReports/StatisticalReports.Vol1.eciperformanceofnationalparties-pdf', compact('data', 'totalElectors', 'totalVotes', 'user_data'));
            return $pdf->download('performance-of-national-parties-pdf.pdf');
        }


        public function winningcpndidateanalysisoverelectorxls(Request $request) {
                $user = Auth::user();
                $user_data = $user;


              $arrayData = DB::select('SELECT a.partyname,contested,won,vote AS evm_vote,total_vote,
              (SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),2) < .16 THEN 1 ELSE 0 END) as fd FROM `counting_pcmaster` as cp
               WHERE a.party_id = cp.party_id AND a.party_id != (select lead_cand_partyid from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code) group by cp.party_id) as fd
              FROM
              (SELECT party_id,partyname,COUNT(DISTINCT p.candidate_id)contested,
               COUNT(DISTINCT w.`candidate_id`)won FROM `counting_pcmaster` p
              JOIN `winning_leading_candidate` w ON p.party_id=w.`lead_cand_partyid`
              JOIN m_party q ON p.party_id=q.ccode
              WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)a JOIN
              (SELECT partyname,SUM(evm_vote)vote,SUM(total_vote)total_vote FROM `counting_pcmaster` m
              JOIN m_party q ON m.party_id=q.ccode WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)b
              ON a.partyname=b.partyname');

                return Excel::create('20Performance_of_national_Parties'.'_'.date('d-m-Y').'_'.time(), function($excel) use ($arrayData) {
                                    $excel->sheet('mySheet', function($sheet) use ($arrayData) {
                                        $sheet->mergeCells('A1:H1');
                                        $sheet->mergeCells('B2:D2');
                                        $sheet->mergeCells('F2:G2');
                                        $sheet->cells('A1', function($cells) {
                                            $cells->setValue('Performance of national party');
                                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                                            $cells->setFontColor("#ffffff");
                                            $cells->setBackground("#042179");
                                            $cells->setAlignment('center');
                                        });

                                        $sheet->cells('A2', function($cells) {
                                            $cells->setValue('Party name');
                                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                                            $cells->setFontColor("#ffffff");
                                            $cells->setBackground("#042179");
                                            $cells->setAlignment('center');
                                        });
                                        $sheet->cells('B2', function($cells) {
                                            $cells->setValue('Candidate');
                                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                                            $cells->setFontColor("#ffffff");
                                            $cells->setBackground("#042179");
                                            $cells->setAlignment('center');
                                        });
                                        $sheet->cells('E2', function($cells) {
                                            $cells->setValue('Votes');
                                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                                            $cells->setFontColor("#ffffff");
                                            $cells->setBackground("#042179");
                                            $cells->setAlignment('center');
                                        });

                                        $sheet->cells('F2', function($cells) {
                                            $cells->setValue('% of Votes Secured');
                                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                                            $cells->setFontColor("#ffffff");
                                            $cells->setBackground("#042179");
                                            $cells->setAlignment('center');
                                        });

                                        $sheet->cell('A3', function($cell) {
                                            $cell->setValue('');
                                        });
                                        $sheet->cell('B3', function($cell) {
                                            $cell->setValue('Contested');
                                        });
                                        $sheet->cell('C3', function($cell) {
                                            $cell->setValue('Won');
                                        });

                                        $sheet->cell('D3', function($cell) {
                                            $cell->setValue('DF');
                                        });
                                        $sheet->cell('E3', function($cell) {
                                            $cell->setValue('Votes Secured by Party');
                                        });
                                        $sheet->cell('F3', function($cell) {
                                            $cell->setValue('Over total electors	');
                                        });
                                        $sheet->cell('G3', function($cell) {
                                            $cell->setValue('Over total valid votes polled');
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
                                              $totalElectors = DB::select('SELECT sum(`electors_total`) as total_electors FROM `electors_cdac` WHERE year = 2019');
                                              $totalVotes = DB::select('SELECT sum(`total_vote`) as totalVotes FROM `counting_pcmaster`');
                                              $peroverelectors = ($rows->total_vote/$totalElectors[0]->total_electors)*100;
                                              $perovervoter = ($rows->total_vote/$totalVotes[0]->totalVotes)*100;
                                                $sheet->cell('A' . $i, $rows->partyname);
                                                $sheet->cell('C' . $i, $rows->contested);
                                                $sheet->cell('B' . $i, $rows->won);
                                                $sheet->cell('D' . $i, $rows->fd);
                                                $sheet->cell('E' . $i, $rows->total_vote);
                                                $sheet->cell('F' . $i, round($peroverelectors,2));
                                                $sheet->cell('G' . $i, round($perovervoter,2));

                                                $totalcontested += $rows->contested;
                                                $won += $rows->won;
                                                $fd += $rows->fd;
                                                $secure += $rows->total_vote;
                                                $electorspercent += $peroverelectors;
                                                $overtotalvaliedpercent += $perovervoter;

                                                if( $key == ( count( $arrayData ) - 1 ) ){
                                                  $sheet->cell('A' . $i, 'TOTAL');
                                                  $sheet->cell('B' . $i, $totalcontested);
                                                  $sheet->cell('C' . $i, $won);
                                                  $sheet->cell('D' . $i, $fd);
                                                  $sheet->cell('E' . $i, $secure);
                                                  $sheet->cell('F' . $i, round($electorspercent,2));
                                                  $sheet->cell('G' . $i, round($overtotalvaliedpercent,2));
                                                }

                                           $i++; }
                                        }
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

$data = DB::select("SELECT TEMP.*, SUM(ecoi.nri_male_voters) as vemale, SUM(ecoi.nri_female_voters) as vefemale, SUM(ecoi.nri_other_voters) as vother, SUM(ecoi.nri_male_voters + ecoi.nri_female_voters + ecoi.nri_other_voters) AS vtotals
FROM(SELECT m.st_code, m.st_name,mpc.PC_TYPE,mpc.pc_no, mpc.PC_NAME,
SUM(cda.nri_male_electors) AS emale,
SUM(cda.nri_female_electors) AS efemale ,
SUM(cda.nri_third_electors)AS eother ,
SUM(cda.nri_third_electors + cda.nri_female_electors + cda.nri_male_electors) AS etotal
FROM electors_cdac cda,m_pc mpc ,m_state m
WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code AND cda.year = 2019
GROUP BY mpc.st_code,mpc.PC_TYPE
)TEMP
LEFT JOIN electors_cdac_other_information ecoi
ON TEMP.st_code = ecoi.st_code AND TEMP.pc_no = ecoi.pc_no
GROUP BY TEMP.st_code,TEMP.PC_TYPE");


 return view('IndexCardReports/StatisticalReports/Vol1/ecistateparticipationoofoverseaselectorsvoters',compact('data','user_data'));

    }

public function allstatewiseoverseaselectorsvoterpdf(){
  $data = DB::select("SELECT TEMP.*, SUM(ecoi.nri_male_voters) as vemale, SUM(ecoi.nri_female_voters) as vefemale, SUM(ecoi.nri_other_voters) as vother, SUM(ecoi.nri_male_voters + ecoi.nri_female_voters + ecoi.nri_other_voters) AS vtotals
  FROM(SELECT m.st_code, m.st_name,mpc.PC_TYPE,mpc.pc_no, mpc.PC_NAME,
  SUM(cda.nri_male_electors) AS emale,
  SUM(cda.nri_female_electors) AS efemale ,
  SUM(cda.nri_third_electors)AS eother ,
  SUM(cda.nri_third_electors + cda.nri_female_electors + cda.nri_male_electors) AS etotal
  FROM electors_cdac cda,m_pc mpc ,m_state m
  WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code AND cda.year = 2019
  GROUP BY mpc.st_code,mpc.PC_TYPE
  )TEMP
  LEFT JOIN electors_cdac_other_information ecoi
  ON TEMP.st_code = ecoi.st_code AND TEMP.pc_no = ecoi.pc_no
  GROUP BY TEMP.st_code,TEMP.PC_TYPE");
            $pdf = PDF::loadView('IndexCardReports/StatisticalReports.Vol1.ecistatestatewiseoverseaseelectors-pdf', compact('data'));
        return $pdf->download('All-state-wise-oversease-electors.pdf');

    }

public function allstatewiseoverseaselectorsvoterxls(Request $request){

  $arrayData = DB::select("SELECT TEMP.*, SUM(ecoi.nri_male_voters) as vemale, SUM(ecoi.nri_female_voters) as vefemale, SUM(ecoi.nri_other_voters) as vother, SUM(ecoi.nri_male_voters + ecoi.nri_female_voters + ecoi.nri_other_voters) AS vtotals
  FROM(SELECT m.st_code, m.st_name,mpc.PC_TYPE,mpc.pc_no, mpc.PC_NAME,
  SUM(cda.nri_male_electors) AS emale,
  SUM(cda.nri_female_electors) AS efemale ,
  SUM(cda.nri_third_electors)AS eother ,
  SUM(cda.nri_third_electors + cda.nri_female_electors + cda.nri_male_electors) AS etotal
  FROM electors_cdac cda,m_pc mpc ,m_state m
  WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code AND cda.year = 2019
  GROUP BY mpc.st_code,mpc.PC_TYPE
  )TEMP
  LEFT JOIN electors_cdac_other_information ecoi
  ON TEMP.st_code = ecoi.st_code AND TEMP.pc_no = ecoi.pc_no
  GROUP BY TEMP.st_code,TEMP.PC_TYPE");


  return Excel::create('State_wise_overseas_electors_voters'.'_'.date('d-m-Y').'_'.time(), function($excel) use ($arrayData) {
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
                              $cell->setValue('Other');
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
                              $cell->setValue('Other');
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });
                          $sheet->cell('J3', function($cell) {
                              $cell->setValue('Total Voters');
                              $cell->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });
                          $i = 4;
                          if (!empty($arrayData)) {
                              foreach ($arrayData as $key => $values) {
                                  $sheet->cell('A' . $i, $values->st_name);
                                  $sheet->cell('B' . $i, $values->PC_TYPE);
                                  $sheet->cell('C' . $i, $values->emale);
                                  $sheet->cell('D' . $i, $values->efemale);
                                  $sheet->cell('E' . $i, $values->eother);
                                  $sheet->cell('F' . $i, $values->etotal);
                                  $sheet->cell('G' . $i, $values->vemale);
                                  $sheet->cell('H' . $i, $values->vefemale);
                                  $sheet->cell('I' . $i, $values->vother);
                                  $sheet->cell('J' . $i, $values->vtotals);

                             $i++; }
                          }
                      });
                  })->export();

}
//// details of repollheld start.

public function detailsofrepollheld(){
$user = Auth::user();
$user_data = $user;
       $rowdatas = DB::table('electors_cdac_other_information as ic')
                 ->select('r.t_pc_ic_id','r.dt_repoll','r.no_repoll',
                          DB::raw('sum(ic.total_polling_station_s_i_t_c) as tpolling'),
                        'pc.PC_NAME','state.ST_NAME','pc.ST_CODE','pc.PC_NO')
                      ->join('m_state as state', 'ic.st_code', '=', 'state.st_code')
                       ->join('m_pc as pc', function($query) {
                            $query->on('pc.PC_NO', '=', 'ic.PC_NO')
                            ->on('pc.ST_CODE', '=', 'ic.st_code');
                        })
                         ->leftjoin('repoll_pc_ic as r', 'r.t_pc_ic_id', '=', 'ic.id')
                    //->groupby('r.t_pc_ic_id')
                                ->where('ic.year','2019')
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
                        //$polling[] =$rowdata->ST_NAME;
                      //$dataArray['stcode'][$rowdata->PC_NO] = $rowdata->PC_NAME;
                      $data[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NO'] = $rowdata->PC_NO;
                      $data[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NAME'] = $rowdata->PC_NAME;
                      $data[$rowdata->ST_CODE]['pcinfo'][$i]['no_repoll'] = $rowdata->no_repoll;
                      $data[$rowdata->ST_CODE]['pcinfo'][$i]['dt_repoll'] = $rowdata->dt_repoll;
                      //$data[$rowdata->ST_CODE]['totalrepoll'][] = $totalrepoll+=$rowdata->no_repoll;
                      $data[$rowdata->ST_CODE]['totalrepoll'][] =  $rowdata->no_repoll;
                      $sumdata = @array_sum($data[$rowdata->ST_CODE]['totalrepoll']);
                      $i++;
                      $stname = $rowdata->ST_NAME;
                      //$dataArray['stcode']['totalrepoll']  = $rowdata->dt_repoll;
                  }
//echo "<pre>";print_r($data);die;

        return view('IndexCardReports/StatisticalReports/Vol2/ecidetails-of-repoll-held',  compact('data','user_data'));
    }

    public function Detailsofrepollheldpdf(Request $request){

           $rowdatas = DB::table('electors_cdac_other_information as ic')
                     ->select('r.t_pc_ic_id','r.dt_repoll','r.no_repoll',
                            DB::raw('sum(ic.total_polling_station_s_i_t_c) as tpolling'),
                            'pc.PC_NAME','state.ST_NAME','pc.ST_CODE','pc.PC_NO')
                          ->join('m_state as state', 'ic.st_code', '=', 'state.st_code')
                           ->join('m_pc as pc', function($query) {
                                $query->on('pc.PC_NO', '=', 'ic.PC_NO')
                                ->on('pc.ST_CODE', '=', 'ic.st_code');
                            })
                             ->leftjoin('repoll_pc_ic as r', 'r.t_pc_ic_id', '=', 'ic.id')
                        ->where('ic.year','2019')
                        ->groupby('state.st_code','pc.PC_NO')
                        ->get()->toarray();


                         //dd($data);
                            $dataArray=array();
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
                             $dataArray[$rowdata->ST_CODE]['state_name'] = $rowdata->ST_NAME;
                             $dataArray[$rowdata->ST_CODE]['total_no_polling_station'] = $totalpolling + $rowdata->tpolling;
                             $totalpolling  = $rowdata->tpolling;
                            //$polling[] =$rowdata->ST_NAME;

                          //$dataArray['stcode'][$rowdata->PC_NO] = $rowdata->PC_NAME;
                          $dataArray[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NO'] = $rowdata->PC_NO;
                          $dataArray[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NAME'] = $rowdata->PC_NAME;
                          $dataArray[$rowdata->ST_CODE]['pcinfo'][$i]['no_repoll'] = $rowdata->no_repoll;
                          $dataArray[$rowdata->ST_CODE]['pcinfo'][$i]['dt_repoll'] = $rowdata->dt_repoll;
                          //$dataArray[$rowdata->ST_CODE]['totalrepoll'] = $totalrepoll+=$rowdata->no_repoll;
                         $dataArray[$rowdata->ST_CODE]['totalrepoll'][] =  $rowdata->no_repoll;
                          $i++;
                          $stname = $rowdata->ST_NAME;
                          //$dataArray['stcode']['totalrepoll']  = $rowdata->dt_repoll;
                      }
                  $pdf = PDF::loadView('IndexCardReports/StatisticalReports/Vol2/ecidetailsof-repoll-held-pdf', compact('dataArray'));
            return $pdf->download('details-of-repoll-held.pdf');
        }



        public function detailsofrepollheldxls(Request $request){

        $rowdatas = DB::table('electors_cdac_other_information as ic')
                  ->select('r.t_pc_ic_id','r.dt_repoll','r.no_repoll',
                         DB::raw('sum(ic.total_polling_station_s_i_t_c) as tpolling'),
                         'pc.PC_NAME','state.ST_NAME','pc.ST_CODE','pc.PC_NO')
                       ->join('m_state as state', 'ic.st_code', '=', 'state.st_code')
                        ->join('m_pc as pc', function($query) {
                             $query->on('pc.PC_NO', '=', 'ic.PC_NO')
                             ->on('pc.ST_CODE', '=', 'ic.st_code');
                         })
                          ->leftjoin('repoll_pc_ic as r', 'r.t_pc_ic_id', '=', 'ic.id')
                     ->where('ic.year','2019')
                     ->groupby('state.st_code','pc.PC_NO')
                     ->get()->toarray();

                         $dataArray=array();
                         $polling=array();
                         $totalrepoll = 0;
                         $totalpolling = 0;
                         $stname = '';
                         $i=0;
                         $total_no_polling_station = 0;

                     foreach($rowdatas as $key=> $rowdata){
                        //$dataArray[$rowdata->ST_CODE]['state_name'] = $rowdata->ST_NAME;
                        $dataArray[$rowdata->ST_CODE]['allindia'] = 'All India';
                        $dataArray[$rowdata->ST_CODE]['gtotal'] = 'Grand Total';
                        $dataArray[$rowdata->ST_CODE]['total_no_polling_station'] = $totalpolling + $rowdata->tpolling;
                        $totalpolling  = $rowdata->tpolling;
                        $dataArray[$rowdata->ST_CODE]['pcinfo'][$i]['sate'] = $rowdata->ST_NAME;
                        $dataArray[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NO'] = $rowdata->PC_NO;
                        $dataArray[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NAME'] = $rowdata->PC_NAME;
                        $dataArray[$rowdata->ST_CODE]['pcinfo'][$i]['no_repoll'] = $rowdata->no_repoll;
                        $dataArray[$rowdata->ST_CODE]['dt_repoll'] = $rowdata->dt_repoll;
                        $dataArray[$rowdata->ST_CODE]['totalrepoll'][] =  $rowdata->no_repoll;

                       $i++;
                   }

      //  echo "<pre>"; print_r($dataArray);die;

        return Excel::create('details_of_repoll_held'.'_'.date('d-m-Y').'_'.time(), function($excel) use ($dataArray) {
                              $excel->sheet('mySheet', function($sheet) use ($dataArray) {
                                  $sheet->mergeCells('A1:G1');
                                  $sheet->cells('A1', function($cells) {
                                      $cells->setValue('11.Details of Repoll Held');
                                      $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                                      $cells->setFontColor("#ffffff");
                                      $cells->setBackground("#042179");
                                      $cells->setAlignment('center');
                                  });

                                   // $last_key = 0;
                                   // $last = $last_key + 10;
                                   // $col = 'B' . $last . ':' . 'G' . $last;
                                   //
                                   // $sheet->cells($col, function($cells) {
                                   //     $cells->setFont(array(
                                   //         'name' => 'Times New Roman',
                                   //         'size' => 12,
                                   //         'bold' => true
                                   //     ));
                                   //     $cells->setAlignment('center');
                                   // });

                                  $sheet->cell('A2', function($cell) {
                                      $cell->setValue('STATE');
                                  });
                                  $sheet->cell('B2', function($cell) {
                                      $cell->setValue('Total No. of Polling Stationin state');
                                  });
                                  $sheet->cell('C2', function($cell) {
                                      $cell->setValue('P.C No.');
                                  });

                                  $sheet->cell('D2', function($cell) {
                                      $cell->setValue('P.C. Name');
                                  });
                                  $sheet->cell('E2', function($cell) {
                                      $cell->setValue('Total No. of Polling Stationwhere repoll held');
                                  });

                                  $sheet->cell('F2', function($cell) {
                                      $cell->setValue('Date of Re-Poll');
                                  });

                                  if (!empty($dataArray)) {
                                     $i = 3;
                                     $ftotal =0;
                                     $total=0;
                                     $sum=0;
                                      foreach ($dataArray as $key => $values) {
                                      foreach ($values['pcinfo'] as $keys => $pcvalues) {
                                      $sheet->cell('A' . $i, $pcvalues['sate']);
                                      $sheet->cell('B' . $i, $values['total_no_polling_station']);
                                      $sheet->cell('C' . $i, $pcvalues['PC_NO']);
                                      $sheet->cell('D' . $i, $pcvalues['PC_NAME']);
                                      $sheet->cell('E' . $i, ($pcvalues['no_repoll'])?$pcvalues['no_repoll']:'N/A');
                                      $sheet->cell('F' . $i, ($pcvalues['no_repoll'])?$pcvalues['no_repoll']:'N/A');
                                     //($keys == (count( $values['pcinfo'] )-1));
                                    //  if( $values['ST_CODE']) ){
                                      //  }
                                     // $ftotal += $pcvalues['no_repoll'];
                                   $i++;
                                    }
                                    $total=$i;
                                    $sheet->cell('A' . $total,'Total');
                                    $alltotal =array_sum($values['totalrepoll']);
                                    $sheet->cell('E' . $total, (array_sum($values['totalrepoll']))?array_sum($values['totalrepoll']):'N/A');
                                   $i=$total+1;
                                //    echo "Total==>".$total;
                                     }
                                       $gTotal=$total+1;
                                      $sheet->cell('A' .$gTotal , $values['allindia']);
                                      $sheet->cell('D' . $gTotal, $values['gtotal']);
                                      $sheet->cell('F' . $gTotal, ($ftotal)?$ftotal:'00');
                                  }
                              });
                          })->export();
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
FROM `counting_pcmaster` AS cp1 WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),2) < .16 THEN 1 ELSE 0 END) AS fd FROM `counting_pcmaster` AS cp WHERE a.party_id = cp.party_id AND a.st_code = cp.st_code AND a.party_id != (SELECT lead_cand_partyid FROM winning_leading_candidate AS w1 WHERE w1.pc_no = cp.pc_no AND w1.st_code = cp.st_code) GROUP BY cp.party_id) AS fd,b.egeneral,b.total_vote

FROM(
SELECT p.party_id,q.partyname,q.partyabbre,m.st_code,m.st_name,COUNT(DISTINCT p.candidate_id)contested, COUNT(DISTINCT w.`candidate_id`)won, SUM(p.total_vote) vote_secured_by_party FROM `counting_pcmaster` p LEFT JOIN `winning_leading_candidate` w ON p.candidate_id=w.candidate_id JOIN m_party q ON p.party_id=q.ccode JOIN m_state m ON m.st_code = p.st_code WHERE q.PARTYTYPE = 'S' GROUP BY p.party_id,m.st_code) a
JOIN
(SELECT TEMP.*,SUM(cpm.total_vote) AS 'total_vote' FROM (SELECT m.st_code, m.st_name,mpc.pc_no, mpc.PC_NAME,SUM(cda.electors_total) AS egeneral
FROM electors_cdac cda,m_pc mpc ,m_state m WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code AND cda.year = 2019 GROUP BY mpc.st_code)TEMP,counting_pcmaster cpm WHERE TEMP.st_code=cpm.st_code GROUP BY TEMP.st_code) b ON a.st_code = b.st_code");
       $i =0;
foreach($performanceofst as $rowsdata){
                    //$arraydata[$rowsdata->CCODE]['partyabbr'] = $rowsdata->PARTYABBRE;
                    $arraydata[$rowsdata->party_id]['partyname'] = $rowsdata->partyname;
                    $arraydata[$rowsdata->party_id]['partyabbre'] = $rowsdata->partyabbre;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['statename'] = $rowsdata->st_name;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['contested'] = $rowsdata->contested;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['won'] = $rowsdata->won;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['df'] = $rowsdata->fd;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['Securedvotes'] = $rowsdata->vote_secured_by_party;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['total_vote'] = $rowsdata->total_vote;

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

  return view('IndexCardReports/StatisticalReports/Vol2/eciperformanceofstateparties',compact('arraydata','user_data'));



}
public function performanceofstatepartiespdf(Request $request){
    $user = Auth::user();
$user_data = $user;
$performanceofst = DB::select("SELECT a.party_id,a.partyname,a.partyabbre,a.st_code,a.st_name,a.contested, a.won, a.vote_secured_by_party,(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes
FROM `counting_pcmaster` AS cp1 WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),2) < .16 THEN 1 ELSE 0 END) AS fd FROM `counting_pcmaster` AS cp WHERE a.party_id = cp.party_id AND a.st_code = cp.st_code AND a.party_id != (SELECT lead_cand_partyid FROM winning_leading_candidate AS w1 WHERE w1.pc_no = cp.pc_no AND w1.st_code = cp.st_code) GROUP BY cp.party_id) AS fd,b.egeneral,b.total_vote

FROM(
SELECT p.party_id,q.partyname,q.partyabbre,m.st_code,m.st_name,COUNT(DISTINCT p.candidate_id)contested, COUNT(DISTINCT w.`candidate_id`)won, SUM(p.total_vote) vote_secured_by_party FROM `counting_pcmaster` p LEFT JOIN `winning_leading_candidate` w ON p.candidate_id=w.candidate_id JOIN m_party q ON p.party_id=q.ccode JOIN m_state m ON m.st_code = p.st_code WHERE q.PARTYTYPE = 'S' GROUP BY p.party_id,m.st_code) a
JOIN
(SELECT TEMP.*,SUM(cpm.total_vote) AS 'total_vote' FROM (SELECT m.st_code, m.st_name,mpc.pc_no, mpc.PC_NAME,SUM(cda.electors_total) AS egeneral
FROM electors_cdac cda,m_pc mpc ,m_state m WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code AND cda.year = 2019 GROUP BY mpc.st_code)TEMP,counting_pcmaster cpm WHERE TEMP.st_code=cpm.st_code GROUP BY TEMP.st_code) b ON a.st_code = b.st_code");
       $i =0;
foreach($performanceofst as $rowsdata){
                    //$arraydata[$rowsdata->CCODE]['partyabbr'] = $rowsdata->PARTYABBRE;
                    $arraydata[$rowsdata->party_id]['partyname'] = $rowsdata->partyname;
                    $arraydata[$rowsdata->party_id]['partyabbre'] = $rowsdata->partyabbre;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['statename'] = $rowsdata->st_name;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['contested'] = $rowsdata->contested;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['won'] = $rowsdata->won;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['df'] = $rowsdata->fd;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['Securedvotes'] = $rowsdata->vote_secured_by_party;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['total_vote'] = $rowsdata->total_vote;

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
                return $pdf->download('performance_of_state_parties.pdf');




}



/// performance of state parties end

public function performanceofunrecognisedparties(Request $request){
    $user = Auth::user();
    $user_data = $user;
    $performanceofst = DB::select("SELECT a.party_id,a.partyname,a.partyabbre,a.st_code,a.st_name,a.contested, a.won, a.vote_secured_by_party,(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes
FROM `counting_pcmaster` AS cp1 WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),2) < .16 THEN 1 ELSE 0 END) AS fd FROM `counting_pcmaster` AS cp WHERE a.party_id = cp.party_id AND a.st_code = cp.st_code AND a.party_id != (SELECT lead_cand_partyid FROM winning_leading_candidate AS w1 WHERE w1.pc_no = cp.pc_no AND w1.st_code = cp.st_code) GROUP BY cp.party_id) AS fd,b.egeneral,b.total_vote

FROM(
SELECT p.party_id,q.partyname,q.partyabbre,m.st_code,m.st_name,COUNT(DISTINCT p.candidate_id)contested, COUNT(DISTINCT w.`candidate_id`)won, SUM(p.total_vote) vote_secured_by_party FROM `counting_pcmaster` p LEFT JOIN `winning_leading_candidate` w ON p.candidate_id=w.candidate_id JOIN m_party q ON p.party_id=q.ccode JOIN m_state m ON m.st_code = p.st_code WHERE q.PARTYTYPE = 'U' GROUP BY p.party_id,m.st_code) a
JOIN
(SELECT TEMP.*,SUM(cpm.total_vote) AS 'total_vote' FROM (SELECT m.st_code, m.st_name,mpc.pc_no, mpc.PC_NAME,SUM(cda.electors_total) AS egeneral
FROM electors_cdac cda,m_pc mpc ,m_state m WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code AND cda.year = 2019 GROUP BY mpc.st_code)TEMP,counting_pcmaster cpm WHERE TEMP.st_code=cpm.st_code GROUP BY TEMP.st_code) b ON a.st_code = b.st_code");
       $i =0;
foreach($performanceofst as $rowsdata){
                    //$arraydata[$rowsdata->CCODE]['partyabbr'] = $rowsdata->PARTYABBRE;
                    $arraydata[$rowsdata->party_id]['partyname'] = $rowsdata->partyname;
                    $arraydata[$rowsdata->party_id]['partyabbre'] = $rowsdata->partyabbre;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['statename'] = $rowsdata->st_name;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['contested'] = $rowsdata->contested;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['won'] = $rowsdata->won;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['df'] = $rowsdata->fd;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['Securedvotes'] = $rowsdata->vote_secured_by_party;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['total_vote'] = $rowsdata->total_vote;

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

  return view('IndexCardReports/StatisticalReports/Vol2/eciperformanceofstateparties',compact('arraydata','user_data'));



}



    }
