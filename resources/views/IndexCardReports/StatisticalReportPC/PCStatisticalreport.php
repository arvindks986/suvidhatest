<?php

namespace App\Http\Controllers\IndexCardReports\StatisticalReportPC;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Session;
use App\Models\candidate_personal_detail;
use App\Helper;
use PDF;
use Excel;
use Auth;
use App\commonModel;



class PCStatisticalreport extends Controller
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
            //$this->xssClean = new xssClean;
       }

	
	public function pcwisevoterturnout(Request $request){
		DB::enableQueryLog();
		
			$session = $request->session()->all();
		
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
       // echo "<pre>"; print_r($session['election_detail']); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
		 $year = $session['election_detail']['YEAR'];
        $scheduleID = $session['election_detail']['ScheduleID']; 
		
		
		
		$pcwisevoterturnouts	=	DB::table("m_pc")
		->select('m_state.ST_NAME','m_pc.PC_NAME','m_pc.PC_NO',
		DB::raw("SUM(electors_male) AS electors_male"),
		DB::raw("SUM(electors_female) AS electors_female"),
		DB::raw("SUM(electors_other) AS electors_other"),
		DB::raw("SUM(electors_total) AS electors_total"),
		DB::raw("SUM(voter_male) AS voter_male"),
		DB::raw("SUM(voter_female) AS voter_female"),
		DB::raw("SUM(voter_other) AS voter_other"),
		DB::raw("SUM(voter_total) AS voter_total")
		
		)
		->join('m_state','m_state.st_code', '=', 'm_pc.ST_CODE')
		->join('electors_cdac', function($join){ 
			$join->on('electors_cdac.st_code', '=', 'm_pc.ST_CODE')
				->on('electors_cdac.pc_no', '=', 'm_pc.PC_NO');			
		})
		->groupBy('m_pc.ST_CODE','m_pc.PC_NO')
		->orderBy('m_state.ST_CODE','ASC')
		->orderBy('m_pc.PC_NAME','ASC')
		->get();
		
		$arrData = array();
		foreach($pcwisevoterturnouts as $key => $data){
			
			$arrData[$data->ST_NAME][] = array(
				'PC_NAME'           		=> $data->PC_NAME,
				'PC_NO'           			=> $data->PC_NO,
				'electors_male'           	=> $data->electors_male,
				'electors_female'           => $data->electors_female,
				'electors_other'           	=> $data->electors_other,
				'electors_total'           	=> $data->electors_total,
				'voter_male'           		=> $data->voter_male,
				'voter_female'           	=> $data->voter_female,
				'voter_other'           	=> $data->voter_other,
				'voter_total'           	=> $data->voter_total
			); 
			
		}
		


		//echo '<pre>'; print_r($arrData);	die;
		
	    //dd(DB::getQueryLog());
		return view('IndexCardReports.pcstatisticalreport.pcwisevoterturnout', ['pcwisevoterturnouts' => $arrData,'sched' => $sched,'election_detail' => $election_detail, 'user_data'=> $user_data])->with('no', 1);
	}
	
	
	public function downloadpcwisevoterturnout(Request $request){
		DB::enableQueryLog();
		
			$session = $request->session()->all();
		
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
       // echo "<pre>"; print_r($session['election_detail']); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
		 $year = $session['election_detail']['YEAR'];
        $scheduleID = $session['election_detail']['ScheduleID']; 
		
		
		
		$pcwisevoterturnout	=	DB::table("m_pc")
		->select('m_state.ST_NAME','m_pc.PC_NAME','m_pc.PC_NO',
		DB::raw("SUM(electors_male) AS electors_male"),
		DB::raw("SUM(electors_female) AS electors_female"),
		DB::raw("SUM(electors_other) AS electors_other"),
		DB::raw("SUM(electors_total) AS electors_total"),
		DB::raw("SUM(voter_male) AS voter_male"),
		DB::raw("SUM(voter_female) AS voter_female"),
		DB::raw("SUM(voter_other) AS voter_other"),
		DB::raw("SUM(voter_total) AS voter_total")
		
		)
		->join('m_state','m_state.st_code', '=', 'm_pc.ST_CODE')
		->join('electors_cdac', function($join){ 
			$join->on('electors_cdac.st_code', '=', 'm_pc.ST_CODE')
				->on('electors_cdac.pc_no', '=', 'm_pc.PC_NO');			
		})
		->groupBy('m_pc.ST_CODE','m_pc.PC_NO')
		->orderBy('m_state.ST_CODE','ASC')
		->orderBy('m_pc.PC_NAME','ASC')
		->get();
		
		$pcwisevoterturnouts = array();
		foreach($pcwisevoterturnout as $key => $data){
			
			$pcwisevoterturnouts[$data->ST_NAME][] = array(
				'PC_NAME'           		=> $data->PC_NAME,
				'PC_NO'           			=> $data->PC_NO,
				'electors_male'           	=> $data->electors_male,
				'electors_female'           => $data->electors_female,
				'electors_other'           	=> $data->electors_other,
				'electors_total'           	=> $data->electors_total,
				'voter_male'           		=> $data->voter_male,
				'voter_female'           	=> $data->voter_female,
				'voter_other'           	=> $data->voter_other,
				'voter_total'           	=> $data->voter_total
			); 
			
		}
		
	   
		$pdf=PDF::loadView('IndexCardReports.pcstatisticalreport.downloadpcwisevoterturnout',compact('pcwisevoterturnouts','year'));  
		return $pdf->download('downloadpcwisevoterturnout.pdf');
	}

	public function performanceofstateparties(){
		
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
        /*SELECT D.st_code,A.CCODE,A.PARTYNAME, A.PARTYTYPE,
		SUM(D.v_votes_evm_all) AS v_votes_evm_all, SUM(C.total_valid_vote) AS totalvotesparty, 
		SUM(D.e_all_t) AS TotalElectorsState, SUM(D.vt_all_t) AS TotalValidVotesState 
		FROM m_party AS A
		INNER JOIN candidate_nomination_detail AS B ON A.CCODE = B.party_id 
		INNER JOIN cand_cont_ic AS C ON C.con_cand_id = B.candidate_id 
		INNER JOIN t_pc_ic AS D ON D.st_code = B.st_code AND D.pc_no = B.pc_no 
		INNER JOIN (SELECT MAX(id) AS id, MAX(created_at) AS created_at FROM t_pc_ic GROUP BY  st_code ORDER BY created_at DESC) AS DD ON D.id = DD.id
		WHERE (B.cand_party_type = 'S' AND B.application_status = 6) 
		GROUP BY A.CCODE, D.st_code*/
        // DB::enableQueryLog();
            $sSelect = array(
                'D.st_code',
                'A.CCODE',
                'A.PARTYNAME',
                'A.PARTYTYPE',
                DB::raw('SUM(D.v_votes_evm_all) AS v_votes_evm_all'),
                DB::raw('SUM(C.total_valid_vote) AS totalvotesparty'),
                DB::raw('SUM(D.e_all_t) AS TotalElectorsState'),
                DB::raw('SUM(D.vt_all_t) AS TotalValidVotesState')
            );
            $sTable = 'm_party AS A';
            $sWhere = array(
                'B.cand_party_type' => 'S',
                'B.application_status' => 6,
            );
            $sGroup = array(
                'A.CCODE'
            );
            $result1 = DB::table($sTable)
                        ->select($sSelect)
                        ->join('candidate_nomination_detail AS B','A.CCODE','B.party_id')
                        ->join('cand_cont_ic AS C','C.con_cand_id','B.candidate_id')
                        ->join('t_pc_ic AS D', function($join){
                            $join->on('D.st_code', 'B.st_code')
                                ->on('D.pc_no', 'B.pc_no');
                        })
                        ->join(DB::raw('(SELECT MAX(id) as id, MAX(created_at) as created_at FROM t_pc_ic GROUP BY  st_code ORDER BY created_at DESC) AS DD'), 'D.id', 'DD.id')
                        ->where($sWhere)
                        ->groupBy($sGroup)
                        ->get()->toArray();
            /*SELECT G.st_code, H.CCODE, H.PARTYNAME, Counted  FROM candidate_nomination_detail AS G
			JOIN m_party AS H ON G.party_id = H.CCODE
			join (SELECT party_id,st_code, Count(Nom_id) AS Counted FROM candidate_nomination_detail GROUP by party_id, st_code) AS GG on G.party_id = GG.party_id AND G.st_code = GG.st_code
			GROUP BY H.CCODE*/
			$result2 = DB::table('candidate_nomination_detail AS G')
						->select(array('G.st_code', 'H.CCODE', 'H.PARTYNAME', 'Counted'))
						->join('m_party AS H', 'G.party_id', 'H.CCODE')
						->join(DB::raw('(SELECT party_id,st_code, Count(Nom_id) AS Counted FROM candidate_nomination_detail GROUP by party_id) AS GG'), function($gJoin){
							$gJoin->on('G.party_id','GG.party_id')
									->on('G.st_code','GG.st_code');
						})
						->where(array(
							'G.cand_party_type' => 'S',
                			'G.application_status' => 6,
						))
						->groupby('H.ccode')
						->get()->toArray();

        //     $queue = DB::getQueryLog();
       // echo "<pre>"; print_r($result1);
       // echo "<pre>"; print_r($result2); die;
        foreach ($result1 as $value1) {
        	foreach ($result2 as $value2) {
        		if(($value1->st_code == $value2->st_code) && ($value1->CCODE == $value2->CCODE)){
	        		$result[] = array(
	        			'PARTYNAME' => $value1->PARTYNAME,
	        			'c_nom_co_t' => $value2->Counted,
	        			'totalvotesparty' => $value1->totalvotesparty,
	        			'TotalElectorsState' => $value1->TotalElectorsState,
	        			'v_votes_evm_all' => $value1->v_votes_evm_all
	        		);
        		}
        	}
        }
        $result = json_decode(json_encode($result));
		
		
		return view('IndexCardReports.pcstatisticalreport.performanceofstateparties', compact('session','result','user_data','sched'));
            	
	}
	
	
	public function downloadperformanceofstateparties(){
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
        /*SELECT D.st_code,A.CCODE,A.PARTYNAME, A.PARTYTYPE,
		SUM(D.v_votes_evm_all) AS v_votes_evm_all, SUM(C.total_valid_vote) AS totalvotesparty, 
		SUM(D.e_all_t) AS TotalElectorsState, SUM(D.vt_all_t) AS TotalValidVotesState 
		FROM m_party AS A
		INNER JOIN candidate_nomination_detail AS B ON A.CCODE = B.party_id 
		INNER JOIN cand_cont_ic AS C ON C.con_cand_id = B.candidate_id 
		INNER JOIN t_pc_ic AS D ON D.st_code = B.st_code AND D.pc_no = B.pc_no 
		INNER JOIN (SELECT MAX(id) AS id, MAX(created_at) AS created_at FROM t_pc_ic GROUP BY  st_code ORDER BY created_at DESC) AS DD ON D.id = DD.id
		WHERE (B.cand_party_type = 'S' AND B.application_status = 6) 
		GROUP BY A.CCODE, D.st_code*/
        // DB::enableQueryLog();
            $sSelect = array(
                'D.st_code',
                'A.CCODE',
                'A.PARTYNAME',
                'A.PARTYTYPE',
                DB::raw('SUM(D.v_votes_evm_all) AS v_votes_evm_all'),
                DB::raw('SUM(C.total_valid_vote) AS totalvotesparty'),
                DB::raw('SUM(D.e_all_t) AS TotalElectorsState'),
                DB::raw('SUM(D.vt_all_t) AS TotalValidVotesState')
            );
            $sTable = 'm_party AS A';
            $sWhere = array(
                'B.cand_party_type' => 'S',
                'B.application_status' => 6,
            );
            $sGroup = array(
                'A.CCODE'
            );
            $result1 = DB::table($sTable)
                        ->select($sSelect)
                        ->join('candidate_nomination_detail AS B','A.CCODE','B.party_id')
                        ->join('cand_cont_ic AS C','C.con_cand_id','B.candidate_id')
                        ->join('t_pc_ic AS D', function($join){
                            $join->on('D.st_code', 'B.st_code')
                                ->on('D.pc_no', 'B.pc_no');
                        })
                        ->join(DB::raw('(SELECT MAX(id) as id, MAX(created_at) as created_at FROM t_pc_ic GROUP BY  st_code ORDER BY created_at DESC) AS DD'), 'D.id', 'DD.id')
                        ->where($sWhere)
                        ->groupBy($sGroup)
                        ->get()->toArray();
            /*SELECT G.st_code, H.CCODE, H.PARTYNAME, Counted  FROM candidate_nomination_detail AS G
			JOIN m_party AS H ON G.party_id = H.CCODE
			join (SELECT party_id,st_code, Count(Nom_id) AS Counted FROM candidate_nomination_detail GROUP by party_id, st_code) AS GG on G.party_id = GG.party_id AND G.st_code = GG.st_code
			GROUP BY H.CCODE*/
			$result2 = DB::table('candidate_nomination_detail AS G')
						->select(array('G.st_code', 'H.CCODE', 'H.PARTYNAME', 'Counted'))
						->join('m_party AS H', 'G.party_id', 'H.CCODE')
						->join(DB::raw('(SELECT party_id,st_code, Count(Nom_id) AS Counted FROM candidate_nomination_detail GROUP by party_id) AS GG'), function($gJoin){
							$gJoin->on('G.party_id','GG.party_id')
									->on('G.st_code','GG.st_code');
						})
						->where(array(
							'G.cand_party_type' => 'S',
                			'G.application_status' => 6,
						))
						->groupby('H.ccode')
						->get()->toArray();

        //     $queue = DB::getQueryLog();
       // echo "<pre>"; print_r($result1);
       // echo "<pre>"; print_r($result2); die;
        foreach ($result1 as $value1) {
        	foreach ($result2 as $value2) {
        		if(($value1->st_code == $value2->st_code) && ($value1->CCODE == $value2->CCODE)){
	        		$result[] = array(
	        			'PARTYNAME' => $value1->PARTYNAME,
	        			'c_nom_co_t' => $value2->Counted,
	        			'totalvotesparty' => $value1->totalvotesparty,
	        			'TotalElectorsState' => $value1->TotalElectorsState,
	        			'v_votes_evm_all' => $value1->v_votes_evm_all
	        		);
        		}
        	}
        }
        $result = json_decode(json_encode($result));
		
		
		$pdf=PDF::loadView('IndexCardReports.pcstatisticalreport.downloadperformanceofstateparties',compact('session','result','user_data','sched'));  
		return $pdf->download('downloadperformanceofstateparties.pdf');
		
		
	}
	
	/* public function statecount(Request $x){
		
		DB::enableQueryLog();
		
		$votes_sum=DB::table("t_pc_ic")
		->select(DB::raw('sum(total_voters_all_total) as gen_t_sum'))
		->where('st_code', '=', $x)
		->get();
		//print_r($votes_sum);
		echo $gen_t_sum;
		//dd(DB::getQueryLog());
	} */
	
	
	public function statewisevoterturnout(Request $request){
		//DB::enableQueryLog();
		 	$session = $request->session()->all();
		
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
       // echo "<pre>"; print_r($session['election_detail']); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
		 $year = $session['election_detail']['YEAR'];
        $scheduleID = $session['election_detail']['ScheduleID']; 
		
		$statewisevoterturnouts	=	DB::table("t_pc_ic")
		->select('m_state.ST_NAME',
		DB::raw("SUM(e_gen_t) AS e_gen_t"),
		DB::raw("SUM(e_ser_t) AS e_ser_t"),
		DB::raw("SUM(vt_all_t) AS vt_all_t"),
		DB::raw("SUM(postal_valid_votes) AS postal_valid_votes")
		)
        ->join('m_state', 'm_state.st_code', '=', 't_pc_ic.st_code')
		//->where('t_pc_ic.schedule_id', '=', $scheduleID)
		->groupBy('m_state.st_code')
		->orderBy('m_state.st_code','ASC')
		->get();
		
		//echo '<pre>'; print_r($statewisevoterturnouts); die;
		
		return view('IndexCardReports.pcstatisticalreport.statewisevoterturnout',compact('statewisevoterturnouts','year','sched','election_detail','user_data'));
	    //dd(DB::getQueryLog());
		
	}
	
	public function downloadstatewisevoterturnout(Request $request){
		DB::enableQueryLog();
		 	$session = $request->session()->all();
		
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
       // echo "<pre>"; print_r($session['election_detail']); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
		 $year = $session['election_detail']['YEAR'];
        $scheduleID = $session['election_detail']['ScheduleID']; 
		
		$statewisevoterturnouts	=	DB::table("t_pc_ic")
		->select('m_state.ST_NAME',
		DB::raw("SUM(e_gen_t) AS e_gen_t"),
		DB::raw("SUM(e_ser_t) AS e_ser_t"),
		DB::raw("SUM(vt_all_t) AS vt_all_t"),
		DB::raw("SUM(postal_valid_votes) AS postal_valid_votes")
		)
        ->join('m_state', 'm_state.st_code', '=', 't_pc_ic.st_code')
		//->where('t_pc_ic.schedule_id', '=', $scheduleID)
		->groupBy('m_state.st_code')
		->orderBy('m_state.st_code','ASC')
		->get();
		
		//return view('IndexCardReports.pcstatisticalreport.statewisevoterturnout',compact('statewisevoterturnouts','year'));
		//dd(DB::getQueryLog());
		$pdf=PDF::loadView('IndexCardReports.pcstatisticalreport.pdfstatewisevoterturnout',compact('statewisevoterturnouts','year'));  
		return $pdf->download('downloadstatewisevoterturnout.pdf');

	}
	
	 public function statewisevoterturnoutexcel(Request $request)
            {
        
        	$session = $request->session()->all();
		
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
       // echo "<pre>"; print_r($session['election_detail']); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
		 $year = $session['election_detail']['YEAR'];
        $scheduleID = $session['election_detail']['ScheduleID']; 
        
		$statewisevoterturnouts=DB::table("t_pc_ic")
		->select('m_state.ST_NAME','e_gen_t','e_ser_t','vt_all_t','postal_valid_votes')
        ->join('m_state', 'm_state.st_code', '=', 't_pc_ic.st_code')
		->groupBy('m_state.ST_NAME')
	    ->orderBy('t_pc_ic.created_at','desc')
	    ->orderBy('m_state.ST_NAME')
		->where('schedule_id', '=', $scheduleID)
		->get();
		//return view('IndexCardReports.pcstatisticalreport.statewisevoterturnout',compact('statewisevoterturnouts','year'));
		
       
        
        
                
        return Excel::create('state-wise-voter-turnouts', function($excel) use ($statewisevoterturnouts) {
            $excel->sheet('mySheet', function($sheet) use ($statewisevoterturnouts)
            {
                $sheet->mergeCells('A1:I1');
              
                $sheet->cells('A1', function($cells) {
                   
                            $cells->setValue('State Wise Voter Turn Out');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                            
                        });
                        
                        
                        
                $sheet->mergeCells('B4:D4');       
                $sheet->mergeCells('E4:H4');       
                //$sheet->mergeCells('A4');
              
                $sheet->cells('A4', function($cells) {
                   
                            $cells->setValue('State/UT');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                            
                        });
                        
                   $sheet->cells('B4', function($cells) {
                   
                            $cells->setValue('Electors');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                            
                        });
                        
                   $sheet->cells('D4', function($cells) {
                            $cells->setValue('Voters');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                            
                        });
					 $sheet->cells('F4', function($cells) {
                            $cells->setValue('Voters Turnout(%)');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                            
                        });
                        

                $sheet->cell('A5', function($cell) {$cell->setValue('');});
                $sheet->cell('B5', function($cell) {$cell->setValue('General');});
                $sheet->cell('D5', function($cell) {$cell->setValue('Service');});
                $sheet->cell('F5', function($cell) {$cell->setValue('EVM');});
                $sheet->cell('H5', function($cell) {$cell->setValue('Postal');});
               
                
                if (!empty($statewisevoterturnouts)) {

                            foreach ($statewisevoterturnouts as $key => $value) {
                                $i = $key + 6;

                                $sheet->cell('A' . $i, $value->ST_NAME);
                                $sheet->cell('B' . $i, $value->e_gen_t);
                                $sheet->cell('C' . $i, $value->e_ser_t);
                                $sheet->cell('D' . $i, $value->vt_all_t);
                                $sheet->cell('E' . $i, $value->ST_NAME);
                                
                            }
                        }
                    });
                })->export();
    }
	
	
	public function partywiseseatwonvalidvotes(Request $request){
		
		// DB::enableQueryLog();
		
			$session = $request->session()->all();
		
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
       // echo "<pre>"; print_r($session['election_detail']); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
		 
      

        DB::enableQueryLog();
		
		 $partywiseseatwons=DB::select("SELECT SUM(A.total_vote) AS totalvotebyparty ,B.* FROM counting_pcmaster A JOIN
				(SELECT wlc.`st_code`,wlc.`leading_id`,wlc.`st_name`,wlc.`lead_party_type`,
				wlc.`lead_cand_name`,wlc.`lead_cand_party`,wlc.`lead_cand_partyid`,wlc.`lead_total_vote`,
				COUNT(wlc.`lead_cand_partyid`) AS seatwon,a.v_votes_evm_all,a.e_all_t FROM m_party AS mp
				INNER JOIN winning_leading_candidate
				AS wlc ON wlc.`lead_cand_partyid` = mp.`CCODE` JOIN (SELECT TEMP.*,SUM(cpm.total_vote) AS v_votes_evm_all
				FROM (
				SELECT m.st_code, m.st_name,mpc.pc_no, mpc.PC_NAME,SUM(cda.gen_electors_male+cda.gen_electors_female+cda.gen_electors_other
				+cda.service_male_electors+cda.service_female_electors+cda.service_third_electors+cda.nri_male_electors+cda.nri_female_electors+cda.nri_third_electors) AS e_all_t
				FROM electors_cdac cda,m_pc mpc ,m_state m
				WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code AND cda.election_id = '1'
				GROUP BY mpc.st_code
				)TEMP,counting_pcmaster cpm
				WHERE TEMP.st_code=cpm.st_code
				GROUP BY TEMP.st_code) a ON a.st_code = wlc.st_code
				GROUP BY mp.`PARTYNAME`, wlc.`st_code` ORDER BY wlc.`st_code`) B ON A.st_code=B.st_code AND A.party_id=B.lead_cand_partyid 
				GROUP BY B.st_code,B.lead_cand_partyid ORDER BY B.lead_party_type, B.st_code;");




$datanew = array();
		foreach ($partywiseseatwons as  $value) {
		if($value->lead_cand_party){
        # code...

        $datanew[$value->lead_cand_party]['partyname'] = $value->lead_cand_party;
        $datanew[$value->lead_cand_party]['leadtypename'] = $value->lead_party_type;

        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['stcode'] = $value->st_code;
        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['stname'] = $value->st_name;
        //$datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['leadtypename'] = $value->lead_party_type;
        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['leadcondname'] = $value->lead_cand_name;

        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['leadcanparty'] = $value->lead_cand_party;

        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['wonseat'] = $value->seatwon;

        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['evmvote'] = $value->v_votes_evm_all; 

        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['totalvotebyparty'] = $value->totalvotebyparty;

        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['electroll'] = $value->e_all_t;
        
    }
  

        

      }

      //echo"<pre>";print_r($datanew); die;

      	if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
          }else if($user->designation == 'CEO'){
                    $prefix     = 'pcceo';
          }else if($user->role_id == '27'){
                  $prefix     = 'eci-index';
          }else if($user->role_id == '7'){
                  $prefix     = 'eci';
        }

		if($request->path() == "$prefix/partywiseseatwonvalidvotes"){
        	return view('IndexCardReports.pcstatisticalreport.partywiseseatwonvalidvotes',compact('datanew','user_data'));
        }

    // echo "<pre>"; print_r($datanew); die;
		
	// return view('IndexCardReports.pcstatisticalreport.partywiseseatwonvalidvotes',compact('datanew','user_data'));


	}


		public function downloadpartywiseseatwonvalidvotes(Request $request){

		$session = $request->session()->all();

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
       // echo "<pre>"; print_r($session['election_detail']); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
		

        DB::enableQueryLog();

				$partywiseseatwons=DB::select("SELECT SUM(A.total_vote) AS totalvotebyparty ,B.* FROM counting_pcmaster A JOIN
				(SELECT wlc.`st_code`,wlc.`leading_id`,wlc.`st_name`,wlc.`lead_party_type`,
				wlc.`lead_cand_name`,wlc.`lead_cand_party`,wlc.`lead_cand_partyid`,wlc.`lead_total_vote`,
				COUNT(wlc.`lead_cand_partyid`) AS seatwon,a.v_votes_evm_all,a.e_all_t FROM m_party AS mp
				INNER JOIN winning_leading_candidate
				AS wlc ON wlc.`lead_cand_partyid` = mp.`CCODE` JOIN (SELECT TEMP.*,SUM(cpm.total_vote) AS v_votes_evm_all
				FROM (
				SELECT m.st_code, m.st_name,mpc.pc_no, mpc.PC_NAME,SUM(cda.gen_electors_male+cda.gen_electors_female+cda.gen_electors_other
				+cda.service_male_electors+cda.service_female_electors+cda.service_third_electors+cda.nri_male_electors+cda.nri_female_electors+cda.nri_third_electors) AS e_all_t
				FROM electors_cdac cda,m_pc mpc ,m_state m
				WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code AND cda.year = 2019
				GROUP BY mpc.st_code
				)TEMP,counting_pcmaster cpm
				WHERE TEMP.st_code=cpm.st_code
				GROUP BY TEMP.st_code) a ON a.st_code = wlc.st_code
				GROUP BY mp.`PARTYNAME`, wlc.`st_code` ORDER BY wlc.`st_code`) B ON A.st_code=B.st_code AND A.party_id=B.lead_cand_partyid 
				GROUP BY B.st_code,B.lead_cand_partyid ORDER BY B.lead_party_type, B.st_code;");

		$query = DB::getQueryLog();
	
		$datanew = array();
		foreach ($partywiseseatwons as  $value) {
		if($value->lead_cand_party){
        # code...

        $datanew[$value->lead_cand_party]['partyname'] = $value->lead_cand_party;
        $datanew[$value->lead_cand_party]['leadtypename'] = $value->lead_party_type;

        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['stcode'] = $value->st_code;
        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['stname'] = $value->st_name;
        //$datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['leadtypename'] = $value->lead_party_type;
        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['leadcondname'] = $value->lead_cand_name;

        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['leadcanparty'] = $value->lead_cand_party;

        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['wonseat'] = $value->seatwon;

        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['evmvote'] = $value->v_votes_evm_all; 

        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['totalvotebyparty'] = $value->totalvotebyparty;

        $datanew[$value->lead_cand_party]['partdetails'][$value->st_code]['electroll'] = $value->e_all_t;
        
    }
  

        

      }

				if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
          }else if($user->designation == 'CEO'){
                    $prefix     = 'pcceo';
          }else if($user->role_id == '27'){
                  $prefix     = 'eci-index';
          }else if($user->role_id == '7'){
                  $prefix     = 'eci';
        }

				if($request->path() == "$prefix/downloadpartywiseseatwonvalidvotes"){
        $pdf=PDF::loadView('IndexCardReports.pcstatisticalreport.pdfpartywiseseatwonvalidvotes', ['datanew' => $datanew]);
        return $pdf->download('downloadpartywiseseatwonvalidvotes.pdf');
        }elseif($request->path() == "$prefix/downloadpartywiseseatwonvalidvotesXLS"){


  //echo "<pre>"; print_r($datanew); die;

  return Excel::create('partywiseseatwonvalidvotes'.'_'.date('d-m-Y').'_'.time(), function($excel) use ($datanew) {
                      $excel->sheet('mySheet', function($sheet) use ($datanew) {
												$sheet->getStyle('A')->getAlignment()->setWrapText(true);
                        $sheet->mergeCells('A1:H1');

                       $sheet->getStyle('D3')->getAlignment()->setWrapText(true);
                       $sheet->getStyle('E3')->getAlignment()->setWrapText(true);
											 $sheet->getStyle('G3')->getAlignment()->setWrapText(true);
                       $sheet->getStyle('H3')->getAlignment()->setWrapText(true);
                       // $sheet->getStyle('B')->getAlignment()->setWrapText(true);

                       $sheet->cells('A1', function($cells) {
                          $cells->setValue('18. Party Wise Seat Won And Valid Votes Polled In Each State');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
                       });

											 $sheet->cells('A3', function($cells) {
													$cells->setValue('Party Name');
													$cells->setAlignment('center');
													$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));

											 });

											 $sheet->setSize('A3', 25, 25);
                       $sheet->cells('B3', function($cells) {
                          $cells->setValue('Party Type');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));

                       });

                      $sheet->setSize('B3', 15, 25);


                       $sheet->cells('C3', function($cells) {
                          $cells->setValue('State Name');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });

											 $sheet->setSize('C3', 25, 25);


                       $sheet->cells('D3', function($cells) {
                          $cells->setValue('Total Valid Votes Polled In State');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });

											 $sheet->setSize('D3', 25, 35);

                       $sheet->cells('E3', function($cells) {
                          $cells->setValue('Total Electors In The State');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });

											 $sheet->setSize('E3', 25, 25);

											 $sheet->cells('F3', function($cells) {
                          $cells->setValue('Seat Won');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });
											 $sheet->setSize('F3', 10, 35);

											 $sheet->cells('G3', function($cells) {
                          $cells->setValue('Total Valid Votes Polled By Party');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });
											 $sheet->setSize('G3', 25, 35);

											 $sheet->cells('H3', function($cells) {
                          $cells->setValue('Percentage(%) Of Valid Votes Polled By Party');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });

											 $sheet->setSize('H3', 25, 35);


                          $i = 4;

											 foreach($datanew as $partywiseseatwon){
                        foreach($partywiseseatwon['partdetails'] as $rowdata) {
                                     $sheet->cell('A' . $i, $partywiseseatwon['partyname'] );
                                     $sheet->cell('B' . $i, $rowdata['leadtypename']);
                                     $sheet->cell('C' . $i, $rowdata['stname']);
                                     $sheet->cell('D' . $i, $rowdata['evmvote']);
                                     $sheet->cell('E' . $i, $rowdata['electroll']);
                                     $sheet->cell('F' . $i, $rowdata['wonseat']);
                                     $sheet->cell('G' . $i, $rowdata['totalvotebyparty']);
																		 $sheet->cell('H' . $i, round($rowdata['totalvotebyparty']/$rowdata['evmvote'] *100,2));

                              $i++; }
                           }
                       });

                   })->export();

				}  //elseif EXCEL ends here




	}  //  function downloads ends here
	
	
	
	
	
}