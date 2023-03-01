<?php

namespace App\Http\Controllers\IndexCardReports\performancenationalparties;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use PDF;
use Excel;
use App\commonModel;
use Auth;

class performancenationalparties extends Controller
{
    public function __construct(){
       $this->middleware('adminsession');
       $this->middleware(['auth:admin','auth']);
       $this->middleware('ceo');
       $this->commonModel = new commonModel();
   }




    public function index()
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

        $listofsuccessfullcondidate = array();




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

		 $totalElectors  = DB::select('SELECT sum(`electors_total`) as total_electors FROM `electors_cdac` WHERE year = 2019');


		 $totalVotes  = DB::select('SELECT sum(`total_vote`) as totalVotes FROM `counting_pcmaster`');

		// echo '<pre>';
		// print_r($totalVotes);

		// echo $totalElectors[0]->total_electors;
		// die;


         return view('IndexCardReports/StatisticalReports/Vol1/performanceofnatiionalparties', compact('data','totalElectors','totalVotes','user_data'));

    }




	public function performanceofnatiionalpartiespdf()
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

        $listofsuccessfullcondidate = array();




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

		 $totalElectors  = DB::select('SELECT sum(`electors_total`) as total_electors FROM `electors_cdac` WHERE year = 2019');


		 $totalVotes  = DB::select('SELECT sum(`total_vote`) as totalVotes FROM `counting_pcmaster`');

		$pdf = PDF::loadView('IndexCardReports/StatisticalReports.Vol2.performanceofnationalparties-pdf', compact('data','totalElectors','totalVotes','user_data'));
        return $pdf->download('performance-of-national-parties-pdf.pdf');

    }













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

$user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);
        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

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

         $rows = array('m.PC_TYPE','m.PC_NAME','m.PC_NO','winn.st_name','winn.lead_cand_name','winn.lead_party_abbre','symbol.SYMBOL_DES','winn.margin','winn.trail_total_vote','winn.st_code'
             //,DB::raw("sum(counting.total_vote) as totalleadvote")

                 );

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
        return view('IndexCardReports/StatisticalReports/Vol2/successfullcondidate',  compact('arraydata','user_data'));
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
        $pdf = PDF::loadView('IndexCardReports/StatisticalReports.Vol2.successfull-candidate-pdf', compact('arraydata'));
        return $pdf->download('successfull-condidate.pdf');
    }

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
       $data = DB::select("SELECT  TEMP1.*,ecoi.nri_female_voters,ecoi.nri_male_voters
FROM (SELECT TEMP.*,cpm.evm_vote,cpm.total_vote FROM ( SELECT m.st_code, m.st_name,mpc.PC_TYPE, mpc.PC_NAME, SUM(cda.electors_male) as emale, SUM(cda.electors_female) as efemale ,SUM(cda.electors_other)as eother , SUM(cda.electors_total) as etotal FROM electors_cdac cda,m_pc mpc ,m_state m WHERE mpc.ST_CODE = cda.st_code AND  m.st_code = cda.st_code GROUP BY mpc.st_code,mpc.PC_TYPE ) TEMP,counting_pcmaster cpm where TEMP.st_code=cpm.st_code group by TEMP.st_code,TEMP.PC_TYPE )TEMP1 LEFT JOIN electors_cdac_other_information ecoi ON TEMP1.st_code = ecoi.st_code group by TEMP1.st_code,TEMP1.PC_TYPE");

 return view('IndexCardReports/StatisticalReports/Vol1/allparticipationoofoverseaselectorsvoters',compact('data','user_data'));

    }


public function allstatewiseoverseaselectorsvoterpdf(){

$data = DB::select("SELECT  TEMP1.*,ecoi.nri_female_voters,ecoi.nri_male_voters
FROM (SELECT TEMP.*,cpm.evm_vote,cpm.total_vote FROM ( SELECT m.st_code, m.st_name,mpc.PC_TYPE, mpc.PC_NAME, SUM(cda.electors_male) as emale, SUM(cda.electors_female) as efemale ,SUM(cda.electors_other)as eother , SUM(cda.electors_total) as etotal FROM electors_cdac cda,m_pc mpc ,m_state m WHERE mpc.ST_CODE = cda.st_code AND  m.st_code = cda.st_code GROUP BY mpc.st_code,mpc.PC_TYPE ) TEMP,counting_pcmaster cpm where TEMP.st_code=cpm.st_code group by TEMP.st_code,TEMP.PC_TYPE )TEMP1 LEFT JOIN electors_cdac_other_information ecoi ON TEMP1.st_code = ecoi.st_code group by TEMP1.st_code,TEMP1.PC_TYPE");

  $pdf = PDF::loadView('IndexCardReports/StatisticalReports.Vol1.allstatewiseoverseaseelectors-pdf', compact('data'));
return $pdf->download('All-state-wise-oversease-electors.pdf');
    }





    }
