<?php

namespace App\Http\Controllers\IndexCardReports\StateWiseOverseasElectorsVoters;
use DB;
use Auth;
use Session;
use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\commonModel;


ini_set("memory_limit","850M");
set_time_limit('120');
ini_set("pcre.backtrack_limit", "5000000");



class State_Wise_Electors_votersController extends Controller

{
   public function __construct()
      {
         $this->middleware('adminsession');
         $this->middleware(['auth:admin','auth']);
         $this->middleware('ceo');
         $this->commonModel = new commonModel();
      }

		public function index( Request $request){
			

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
      //  echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;


			$stcode = DB::table('m_state')->select('m_state.st_code')
				->get();
			$vSelect=array('B.pc_type','A.ST_NAME','C.c_nom_m_t as totmale',
				DB::raw("SUM(CASE WHEN B.`PC_TYPE` = 'GEN' THEN 1 ELSE 0 END) AS GENSEATS"),
				DB::raw("SUM(CASE WHEN B.`PC_TYPE` = 'SC' THEN 1 ELSE 0 END) AS SCSEATS"),
				DB::raw("SUM(CASE WHEN B.`PC_TYPE` = 'ST' THEN 1 ELSE 0 END) AS STSEATS"),
				DB::raw('SUM(C.e_nri_m) as maletotalnrielector'),
				DB::raw('SUM(C.e_nri_f) as femaletotalnrielector'),
				DB::raw('SUM(C.e_nri_o) as othertotalnrielector'),
				DB::raw('SUM(C.e_nri_t) as totalnrielector'),
				DB::raw('SUM(C.vt_nri_m) as votermalenritotal'),
				DB::raw('SUM(C.vt_nri_f) as voterfemalenritotal'),
				DB::raw('SUM(C.vt_nri_o) as voterothernritotal'),
				DB::raw('SUM(C.vt_nri_t) as voterallnritotal'),
				);
					//DB::enableQueryLog();c_nom_a_t
				$voterquery = DB::table('m_state AS A')
					->select($vSelect)
					->JOIN ('m_pc AS B', 'A.ST_CODE', 'B.ST_CODE')
					->join('t_pc_ic AS C', function($join){
				$join->on('A.st_code','C.ST_CODE')
					->on('B.pc_no','C.PC_NO');
					})
					->groupby ('A.ST_CODE', 'B.PC_TYPE')
					->get()->toArray();
					// dd($voterquery);
				foreach ($voterquery as  $value) {
					# code...
					$statewisedata[$value->ST_NAME][] = array(
					'pc_type' => $value->pc_type,
					'GENSEATS'=> $value->GENSEATS,
					'SCSEATS' => $value->SCSEATS,
					'STSEATS' => $value->STSEATS,
					'maletotalnrielector'=>$value->maletotalnrielector,
					'femaletotalnrielector'=>$value->femaletotalnrielector,
					'othertotalnrielector'=>$value->othertotalnrielector,
					'totalnrielector'=>$value->totalnrielector,
					'votermalenritotal'=>$value->votermalenritotal,
					'voterfemalenritotal'=>$value->voterfemalenritotal,
					'voterothernritotal'=>$value->voterothernritotal,
					'voterallnritotal' => $value->voterallnritotal
					);
				}
					//dd($statewisedata);

				if($request->path() == 'pcceo/State-Wise-Overseas-Electors-Voters'){

					return view('IndexCardReports.StateWiseOverseasElectorsVoters.State_wise_elector_voter', compact('statewisedata','user_data'));

					        }
				elseif($request->path() == 'pcceo/State-Wise-Overseas-Electors-VotersPDF'){

					    $pdf=PDF::loadView
					        ('IndexCardReports.StateWiseOverseasElectorsVoters.State_wise_elector_voterPDF',[

					            'statewisedata'=>$statewisedata
					        ]);
				return $pdf->download('state-wise-overseas-electors-votersReport.pdf');
					        }

		}
}
