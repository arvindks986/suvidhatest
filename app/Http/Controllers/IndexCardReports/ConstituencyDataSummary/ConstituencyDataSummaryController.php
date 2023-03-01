<?php

namespace App\Http\Controllers\IndexCardReports\ConstituencyDataSummary;
use DB;
use Auth;
use Session;
use App;
use PDF;
use Excel;
use App\Http\Controllers\Controller;
use App\commonModel;
use Illuminate\Http\Request;

ini_set("memory_limit","850M");
set_time_limit('6000');
ini_set("pcre.backtrack_limit", "10000000");

class ConstituencyDataSummaryController extends Controller
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
      // $this->xssClean = new xssClean;
  }


 public function index(Request $request){

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



    $session = $request->session()->all();
  //  dd($session);
   $datanew = array();
    $st_code = $session['admin_login_details']['st_code'];

    $st_name = $session['admin_login_details']['placename'];

        $sSelect = array(
           'B.ST_NAME','B.st_code','A.pc_no','A.PC_TYPE','A.PC_NAME'

       );
       $sTable = 'm_pc AS A';
       $sGroup = array(
           'A.st_code','A.pc_no'
       );
       $countSeats = DB::table($sTable)
                       ->select($sSelect)
                       ->join('m_state AS B','B.ST_CODE','A.st_code')
                       ->join('m_election_details AS med',[['med.CONST_NO','A.PC_NO'],['med.ST_CODE','A.ST_CODE']])
                        //->where('B.ST_CODE','U05')
                        ->WHERE('med.CONST_TYPE', 'PC')
                       ->WHERE('med.election_status',  1)
                       //->WHERE('med.ELECTION_ID' ,1)
                       ->groupBy($sGroup)
                       ->get()->toArray();

         //echo "<pre>"; print_r($countSeats); die;

        foreach ($countSeats as  $value) {



				$st_code = $value->st_code;
				$st_name = $value->ST_NAME;
				$pc = $value->pc_no;

          




				$indexCardDatas = App\models\Admin\CandidateModel::get_count_nominated($st_code,$pc);





				$indexCardDatasDf = DB::select("SELECT cp.st_code,cp.pc_no,
				 SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
				where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'male' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdmale,

				SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
				where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'female' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdfemale,

				SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
				where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'third' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdthird,


				SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
				where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fd

				FROM `counting_pcmaster` as cp
				join candidate_personal_detail as C on C.candidate_id = cp.candidate_id
				WHERE cp.candidate_id != (select candidate_id from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code)
				AND cp.party_id != 1180 AND cp.st_code = '$st_code' and cp.pc_no = $pc");


			   $electorData = DB::table('electors_cdac AS ec')
					  ->select(array(
							  'ovi.general_male_voters AS male_voter',
							  'ovi.general_female_voters AS female_voter',
							  'ovi.general_other_voters AS other_voter',
							  'ovi.nri_male_voters AS nri_male_voters',
							  'ovi.nri_female_voters AS nri_female_voters',
							  'ovi.nri_other_voters AS nri_other_voters',
							  'ovi.test_votes_49_ma AS test_votes_49_ma',
							'ovi.votes_not_retreived_from_evm AS votes_not_retreived_from_evm',
							'ovi.rejected_votes_due_2_other_reason AS rejected_votes_due_2_other_reason',
							'ovi.service_postal_votes_under_section_8 AS service_postal_votes_under_section_8',

							'ovi.service_postal_votes_gov AS service_postal_votes_gov',
							'ovi.proxy_votes AS proxy_votes',
							'ovi.total_polling_station_s_i_t_c AS total_polling_station_s_i_t_c',
							'ovi.date_of_repoll AS date_of_repoll',
							'ovi.no_poll_station_where_repoll AS no_poll_station_where_repoll',

						DB::raw('SUM(ec.gen_electors_male) AS gen_m'),
						DB::raw("SUM(ec.service_male_electors) AS ser_m"),
						DB::raw("SUM(ec.nri_male_electors) AS nri_m"),

						DB::raw("SUM(ec.gen_electors_female) AS gen_f"),
						DB::raw("SUM(ec.service_female_electors) AS ser_f"),
						DB::raw("SUM(ec.nri_female_electors) AS nri_f"),

						DB::raw("SUM(ec.gen_electors_other) AS gen_o"),
						DB::raw("SUM(ec.nri_third_electors) AS nri_o"),
						DB::raw("SUM(ec.service_third_electors) AS ser_o"),

						DB::raw("SUM(ec.gen_electors_male + ec.gen_electors_female + ec.gen_electors_other) AS gen_t"),
						DB::raw("SUM(ec.service_male_electors+ec.service_female_electors+ec.service_third_electors) AS ser_t"),
						DB::raw("SUM(ec.nri_male_electors + ec.nri_female_electors+ ec.nri_third_electors) AS total_o"),

						DB::raw("SUM(ec.electors_male + ec.service_male_electors) AS total_m"),
						DB::raw("SUM(ec.electors_female + ec.service_female_electors) AS total_f"),
						DB::raw("SUM(ec.electors_other + ec.service_third_electors) AS total_other_electors"),

						DB::raw("SUM(ec.electors_total + ec.electors_service) AS total_all")

					))

					 ->leftJoin('electors_cdac_other_information as ovi',function($query){
					   $query->on('ovi.st_code','ec.st_code')
							   ->on('ovi.pc_no','ec.pc_no');

				   })

					->where(array(
						'ec.st_code' => $st_code,
						'ec.pc_no'   => $pc
						
					))

					->groupBy('ec.st_code','ec.pc_no')

					->first();

					//echo '<pre>'; print_r($electorData); echo '</pre>';

					$evmvotesfromcp = DB::table('counting_pcmaster')
                            ->select(array(
                            DB::raw('SUM(evm_vote) AS evm_votes'),
                            DB::raw("SUM(postal_vote) AS postal_votes"),
                            DB::raw("SUM(total_vote) AS total_votes"),
                            DB::raw("rejectedvote AS rej_votes_postal"),
                            DB::raw("tended_votes AS tended_votes")
                            ))
                            ->where(array(
                            'st_code' => $st_code,
                            'pc_no'   => $pc
                            ))
                            ->groupBy('st_code')
                            ->first();

					//echo '<pre>'; print_r($evmvotesfromcp); die;

					$datanotapcwise = DB::table('counting_pcmaster')
                                ->select('postal_vote','evm_vote','migrate_votes')
                                ->where(array(
                                        'st_code' => $st_code,
                                        'pc_no'    => $pc,
                                        'party_id'=>1180
                                 ))
                                 ->first();

					//echo '<pre>'; print_r($datanotapcwise); echo '</pre>';


					
					$pollDateInfoPcwise = DB::table('m_schedule as ms')
                                ->select('ms.DATE_POLL','ms.DATE_COUNT','wlc.result_declared_date',
                                'cpd.cand_name as lead_cand_name','wlc.lead_cand_party','wlc.lead_total_vote','wlc.trail_total_vote',
                                'wlc.trail_cand_party','cpdt.cand_name as trail_cand_name','wlc.margin')
                                ->join('m_election_details as mss','mss.ScheduleID','ms.SCHEDULEID')
                                ->join('winning_leading_candidate as wlc', function($join){
                                    $join->on('wlc.st_code', 'mss.st_code')
                                            ->on('wlc.pc_no', 'mss.CONST_NO');
                                })
								->join('candidate_personal_detail as cpd','cpd.candidate_id','wlc.candidate_id')
								->join('candidate_personal_detail as cpdt','cpdt.candidate_id','wlc.trail_candidate_id')
                                ->where(array(
                                        'mss.ST_CODE' => $st_code,
                                        'mss.CONST_NO'   => $pc,
                                        'mss.CONST_TYPE'   => 'PC',
                                        'mss.election_status'   => '1'
                                 ))
                                ->first();

					//echo '<pre>'; print_r($pollDateInfoPcwise); echo '</pre>';

					$finalArray = array();

					$finalArray = array(
                                'st_code' => $st_code,
                                'st_name' => $st_name,
                                'pc_no' => $pc,
                                'c_nom_m_t'=> $indexCardDatas['nom_male'],
                                'c_nom_f_t'=> $indexCardDatas['nom_female'],
                                'c_nom_o_t'=> $indexCardDatas['nom_third'],

                                'c_wd_m_t'=> $indexCardDatas['with_male'],
                                'c_wd_f_t'=> $indexCardDatas['with_female'],
                                'c_wd_o_t'=> $indexCardDatas['with_third'],

                                'c_rej_m_t'=> $indexCardDatas['rej_male'],
                                'c_rej_f_t'=> $indexCardDatas['rej_female'],
                                'c_rej_o_t'=> $indexCardDatas['rej_third'],

                                 'c_acp_m_t'=> $indexCardDatas['cont_male'],
                                'c_acp_f_t'=> $indexCardDatas['cont_female'],
                                'c_acp_o_t'=> $indexCardDatas['cont_third'],

								                'c_fd_m_t'=> $indexCardDatasDf[0]->fdmale,
                                'c_fd_f_t'=> $indexCardDatasDf[0]->fdfemale,
                                'c_fd_o_t'=> $indexCardDatasDf[0]->fdthird,
                                'c_fd_t'   => $indexCardDatasDf[0]->fd,

                								'male_voter' => @$electorData->male_voter,
                								'female_voter' => @$electorData->female_voter,
                								'other_voter' => @$electorData->other_voter,
                								'nri_male_voters' => @$electorData->nri_male_voters,
                								'nri_female_voters' => @$electorData->nri_female_voters,
                								'nri_other_voters' => @$electorData->nri_other_voters,
                								'test_votes_49_ma' => @$electorData->test_votes_49_ma,
                								'votes_not_retreived_from_evm' => @$electorData->votes_not_retreived_from_evm,
                								'rejected_votes_due_2_other_reason' => @$electorData->rejected_votes_due_2_other_reason,
                								'service_postal_votes_under_section_8' => @$electorData->service_postal_votes_under_section_8,
                								'service_postal_votes_gov' => @$electorData->service_postal_votes_gov,
                								'proxy_votes' => @$electorData->proxy_votes,
                								'total_polling_station_s_i_t_c' => @$electorData->total_polling_station_s_i_t_c,
                								'date_of_repoll' => @$electorData->date_of_repoll,
                								'no_poll_station_where_repoll' => @$electorData->no_poll_station_where_repoll,
                								'gen_m' => @$electorData->gen_m,
                								'ser_m' => @$electorData->ser_m,
                								'nri_m' => @$electorData->nri_m,
                								'gen_f' => @$electorData->gen_f,
                								'ser_f' => @$electorData->ser_f,
                								'nri_f' => @$electorData->nri_f,
                								'gen_o' => @$electorData->gen_o,
                								'nri_o' => @$electorData->nri_o,
                								'ser_o' => @$electorData->ser_o,
                								'gen_t' => @$electorData->gen_t,
                								'ser_t' => @$electorData->ser_t,
                								'total_o' => @$electorData->total_o,
                								'total_m' => @$electorData->total_m,
                								'total_f' => @$electorData->total_f,
                								'total_other_electors' => @$electorData->total_other_electors,
                								'total_all' => @$electorData->total_all,
                								'evm_votes'=> @$evmvotesfromcp->evm_votes,
                								'postal_votes'=> @$evmvotesfromcp->postal_votes,
                								'total_votes'=> @$evmvotesfromcp->total_votes,
                								'rej_votes_postal'=> @$evmvotesfromcp->rej_votes_postal,
                								'tended_votes'=> @$evmvotesfromcp->tended_votes,

                								'nota_postal_vote'=> @$datanotapcwise->postal_vote,
                								'nota_evm_vote'=> @$datanotapcwise->evm_vote,
                								'DATE_POLL'=> @$pollDateInfoPcwise->DATE_POLL,
                								'DATE_COUNT'=> @$pollDateInfoPcwise->DATE_COUNT,
                								'result_declared_date'=> @$pollDateInfoPcwise->result_declared_date,

                                'lead_cand_name'=> @$pollDateInfoPcwise->lead_cand_name,
                                'lead_cand_party'=> @$pollDateInfoPcwise->lead_cand_party,
                                'lead_total_vote'=> @$pollDateInfoPcwise->lead_total_vote,
                                'trail_cand_name'=> @$pollDateInfoPcwise->trail_cand_name,
                                'trail_cand_party'=> @$pollDateInfoPcwise->trail_cand_party,
                                'trail_total_vote'=> @$pollDateInfoPcwise->trail_total_vote,
                                'margin'=> @$pollDateInfoPcwise->margin,

                                );


                              $finalArraynew[$st_code][$pc] = array(
                                'st_code' => $st_code,
                                'st_name' => $st_name,
                                'PC_NAME' => $value->PC_NAME,
                                'pc_type' => $value->PC_TYPE,
                                'pc_no' => $pc,
                                'c_nom_m_t'=> $indexCardDatas['nom_male'],
                                'c_nom_f_t'=> $indexCardDatas['nom_female'],
                                'c_nom_o_t'=> $indexCardDatas['nom_third'],

                                'c_wd_m_t'=> $indexCardDatas['with_male'],
                                'c_wd_f_t'=> $indexCardDatas['with_female'],
                                'c_wd_o_t'=> $indexCardDatas['with_third'],

                                'c_rej_m_t'=> $indexCardDatas['rej_male'],
                                'c_rej_f_t'=> $indexCardDatas['rej_female'],
                                'c_rej_o_t'=> $indexCardDatas['rej_third'],

                                 'c_acp_m_t'=> $indexCardDatas['cont_male'],
                                'c_acp_f_t'=> $indexCardDatas['cont_female'],
                                'c_acp_o_t'=> $indexCardDatas['cont_third'],

                                'c_fd_m_t'=> $indexCardDatasDf[0]->fdmale,
                                'c_fd_f_t'=> $indexCardDatasDf[0]->fdfemale,
                                'c_fd_o_t'=> $indexCardDatasDf[0]->fdthird,
                                'c_fd_t'   => $indexCardDatasDf[0]->fd,

                                'male_voter' => @$electorData->male_voter,
                                'female_voter' => @$electorData->female_voter,
                                'other_voter' => @$electorData->other_voter,
                                'nri_male_voters' => @$electorData->nri_male_voters,
                                'nri_female_voters' => @$electorData->nri_female_voters,
                                'nri_other_voters' => @$electorData->nri_other_voters,
                                'test_votes_49_ma' => @$electorData->test_votes_49_ma,
                                'votes_not_retreived_from_evm' => @$electorData->votes_not_retreived_from_evm,
                                'rejected_votes_due_2_other_reason' => @$electorData->rejected_votes_due_2_other_reason,
                                'service_postal_votes_under_section_8' => @$electorData->service_postal_votes_under_section_8,
                                'service_postal_votes_gov' => @$electorData->service_postal_votes_gov,
                                'proxy_votes' => @$electorData->proxy_votes,
                                'total_polling_station_s_i_t_c' => @$electorData->total_polling_station_s_i_t_c,
                                'date_of_repoll' => @$electorData->date_of_repoll,
                                'no_poll_station_where_repoll' => @$electorData->no_poll_station_where_repoll,
                                'gen_m' => @$electorData->gen_m,
                                'ser_m' => @$electorData->ser_m,
                                'nri_m' => @$electorData->nri_m,
                                'gen_f' => @$electorData->gen_f,
                                'ser_f' => @$electorData->ser_f,
                                'nri_f' => @$electorData->nri_f,
                                'gen_o' => @$electorData->gen_o,
                                'nri_o' => @$electorData->nri_o,
                                'ser_o' => @$electorData->ser_o,
                                'gen_t' => @$electorData->gen_t,
                                'ser_t' => @$electorData->ser_t,
                                'total_o' => @$electorData->total_o,
                                'total_m' => @$electorData->total_m,
                                'total_f' => @$electorData->total_f,
                                'total_other_electors' => @$electorData->total_other_electors,
                                'total_all' => @$electorData->total_all,
                                'evm_votes'=> @$evmvotesfromcp->evm_votes,
                                'postal_votes'=> @$evmvotesfromcp->postal_votes,
                                'total_votes'=> @$evmvotesfromcp->total_votes,
                                'rej_votes_postal'=> @$evmvotesfromcp->rej_votes_postal,
                                'tended_votes'=> @$evmvotesfromcp->tended_votes,

                                'nota_postal_vote'=> @$datanotapcwise->postal_vote,
                                'nota_evm_vote'=> @$datanotapcwise->evm_vote,
                                'DATE_POLL'=> @$pollDateInfoPcwise->DATE_POLL,
                                'DATE_COUNT'=> @$pollDateInfoPcwise->DATE_COUNT,
                                'result_declared_date'=> @$pollDateInfoPcwise->result_declared_date,

                                'lead_cand_name'=> @$pollDateInfoPcwise->lead_cand_name,
                                'lead_cand_party'=> @$pollDateInfoPcwise->lead_cand_party,
                                'lead_total_vote'=> @$pollDateInfoPcwise->lead_total_vote,
                                'trail_cand_name'=> @$pollDateInfoPcwise->trail_cand_name,
                                'trail_cand_party'=> @$pollDateInfoPcwise->trail_cand_party,
                                'trail_total_vote'=> @$pollDateInfoPcwise->trail_total_vote,
                                'margin'=> @$pollDateInfoPcwise->margin,

                                );

              }


                //echo "<pre>"; print_r($finalArraynew); die;


                 if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
                }else if($user->designation == 'CEO'){
                    $prefix     = 'pcceo';
                }else if($user->role_id == '27'){
                  $prefix     = 'eci-index';
                }else if($user->role_id == '7'){
                  $prefix     = 'eci';
                }


                if($request->path() == "$prefix/constituencyDataSummaryReport"){
                return view('IndexCardReports.ConstituencyDataSummary.constituency-data-summary-report',compact('finalArraynew','user_data'));
                }elseif($request->path() == "$prefix/constituencyDataSummaryReportPDF"){

              //  $pdf = \App::make('dompdf.wrapper');
			//$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf = PDF::loadView('IndexCardReports.ConstituencyDataSummary.constituencyDataSummaryReportPDF',[
                    'session'=>$session,
                    'finalArraynew'=>$finalArraynew
                ]);

                           if(verifyreport(32)){
        
                  $file_name = 'Constituency_data_summery_report'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/32/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '32',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
                return $pdf->download('Constituency_data_summery_report.pdf');


            	}elseif($request->path() == "$prefix/constituencyDataSummaryReportXLS") {
                    $finalArraynew   = json_decode( json_encode($finalArraynew), true);
                    $date = date('Y-m-d');
                    return Excel::create('Constituency Data Summery Report', function($excel) use ($finalArraynew) {
						
					//echo '<pre>'; print_r($finalArraynew); die;
					

					foreach($finalArraynew as $key => $value){

        			foreach($value as $key2 => $val){
						
                    $excel->sheet($key.'-'.$key2, function($sheet) use ($val)
                    {

                    $sheet->mergeCells('A1:G1');
					
                    $sheet->cells('A1', function($cells) {
						$cells->setValue('CONSTITUENCY DATA  SUMMARY');
						$cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
						$cells->setAlignment('center');
                    });

                    $sheet->cells('A2', function($cells) use ($val) {
                        $cells->setValue(' State/UT & Code ');
                       });

					$sheet->cells('B2', function($cells) use ($val) {
                        $cells->setValue( $val['st_name'].'-'.$val['st_code'] );
                       });


					$sheet->cells('C2', function($cells) use ($val) {
                        $cells->setValue(' Constituency Name & Code ');
                       });

					$sheet->cells('D2', function($cells) use ($val) {
                        $cells->setValue( $val['PC_NAME'].'-'.$val['pc_type'] );
                    });

					
					$sheet->cell('A3', function($cell) {
						$cell->setValue(' CANDIDATES ');
                    });
					   
                       $sheet->cell('D3', function($cell) {
						   $cell->setValue('Men');
                       });
                       $sheet->cell('E3', function($cell) {
						   $cell->setValue('Women');
                       });
                       $sheet->cell('F3', function($cell) {
						   $cell->setValue('Third Gender');
                        });
                       $sheet->cell('G3', function($cell) {
						   $cell->setValue('Total');
                       });


						$sheet->cell('B4', ' Nominated ');
						$sheet->cell('D4', ($val['c_nom_m_t'] > 0) ? $val['c_nom_m_t']:'=(0)');
                        $sheet->cell('E4', ($val['c_nom_f_t']> 0) ? $val['c_nom_f_t']:'=(0)');
                        $sheet->cell('F4', ($val['c_nom_o_t']> 0) ? $val['c_nom_o_t']:'=(0)');
                        $sheet->cell('G4', (($val['c_nom_m_t']+$val['c_nom_f_t']+$val['c_nom_o_t']) > 0) ? ($val['c_nom_m_t']+$val['c_nom_f_t']+$val['c_nom_o_t']): '=(0)');
												
                        $sheet->cell('B5', ' Nomination Rejected ');
						$sheet->cell('D5', $val['c_rej_m_t']);
                        $sheet->cell('E5', $val['c_rej_f_t'] ? $val['c_rej_f_t'] :'=(0)');
                        $sheet->cell('F5', $val['c_rej_o_t'] ? $val['c_rej_o_t'] :'=(0)');
                        $sheet->cell('G5', ($val['c_rej_m_t']+$val['c_rej_f_t']+$val['c_rej_o_t']) ? ($val['c_rej_m_t']+$val['c_rej_f_t']+$val['c_rej_o_t']): '=(0)');
						
                        $sheet->cell('B6', ' Withdrawn ');
						$sheet->cell('D6', $val['c_wd_m_t'] ? $val['c_wd_m_t'] : '=(0)');
                        $sheet->cell('E6', $val['c_wd_f_t'] ? $val['c_wd_f_t'] : '=(0)' );
                        $sheet->cell('F6', $val['c_wd_o_t'] ?  $val['c_wd_o_t'] : '=(0)' );
                        $sheet->cell('G6', ($val['c_wd_m_t']+$val['c_wd_f_t']+$val['c_wd_o_t']) ? ($val['c_wd_m_t']+$val['c_wd_f_t']+$val['c_wd_o_t']): '=(0)' );
						
                        $sheet->cell('B7', ' Contested ');
						$sheet->cell('D7', $val['c_acp_m_t']);
                        $sheet->cell('E7', $val['c_acp_f_t'] ? $val['c_acp_f_t'] : '=(0)' );
                        $sheet->cell('F7', $val['c_acp_o_t'] ?  $val['c_acp_o_t'] : '=(0)');
                        $sheet->cell('G7', ($val['c_acp_m_t']+$val['c_acp_f_t']+$val['c_acp_o_t']) ? ($val['c_acp_m_t']+$val['c_acp_f_t']+$val['c_acp_o_t']) : '=(0)' );
						
                        $sheet->cell('B8',' Forfeited Deposit ');
                        $sheet->cell('D8', $val['c_fd_m_t'] ? $val['c_fd_m_t'] : '=(0)');
                        $sheet->cell('E8', $val['c_fd_f_t'] ? $val['c_fd_f_t'] : '=(0)' );
                        $sheet->cell('F8', $val['c_fd_o_t'] ? $val['c_fd_o_t']  : '=(0)');
                        $sheet->cell('G8', $val['c_fd_t']);


                    $sheet->cell('A9', function($cell) {
						$cell->setValue(' ELECTORS ');
                    });    
						
					$sheet->cell('B10', ' General ');
						$sheet->cell('D10', $val['gen_m']);
                        $sheet->cell('E10', $val['gen_f'] ? $val['gen_f'] : '=(0)');
                        $sheet->cell('F10', $val['gen_o'] ? $val['gen_o'] : '=(0)');
                        $sheet->cell('G10', $val['gen_t'] ? $val['gen_t'] : '=(0)');
						
                    $sheet->cell('B11', ' OverSeas ');
						$sheet->cell('D11', $val['nri_m']);
                        $sheet->cell('E11', $val['nri_f'] ? $val['nri_f'] : '=(0)');
                        $sheet->cell('F11', $val['nri_o'] ? $val['nri_o'] : '=(0)');
                        $sheet->cell('G11', ($val['nri_m']+$val['nri_f']+$val['nri_o']) ? ($val['nri_m']+$val['nri_f']+$val['nri_o']) : '=(0)');
					
                    $sheet->cell('B12', ' Service ');
						            $sheet->cell('D12', $val['ser_m'] ?  $val['ser_m'] : '=(0)');
                        $sheet->cell('E12', $val['ser_f'] ? $val['ser_f'] : '=(0)');
                        $sheet->cell('F12', $val['ser_o'] ? $val['ser_o'] : '=(0)');
                        $sheet->cell('G12', $val['ser_t'] ? $val['ser_t'] : '=(0)');
					
                    $sheet->cell('B13', ' Total ');	
						            $sheet->cell('D13', ($val['gen_m'] + $val['nri_m'] + $val['ser_m']) ? : '=(0)');
                        $sheet->cell('E13', ($val['gen_f'] + $val['nri_f'] + $val['ser_f']) ?  : '=(0)');
                        $sheet->cell('F13', ($val['gen_o'] + $val['nri_o'] + $val['ser_o']) ?  : '=(0)');
                        $sheet->cell('G13', ($val['gen_t'] + $val['nri_m']+$val['nri_f']+$val['nri_o'] + $val['ser_t']) ?  : '=(0)');
					
					$sheet->cell('A14', function($cell) {
						$cell->setValue(' VOTERS ');
                    });
						
                        $sheet->cell('B15', ' General ');
							$sheet->cell('D15', $val['male_voter'] ? $val['male_voter'] : '=(0)');
							$sheet->cell('E15', $val['female_voter'] ? $val['female_voter'] : '=(0)');
							$sheet->cell('F15', $val['other_voter'] ? $val['other_voter'] : '=(0)');
							$sheet->cell('G15', ($val['male_voter'] + $val['female_voter'] + $val['other_voter']) ? ($val['male_voter'] + $val['female_voter'] + $val['other_voter']) : '=(0)');
						
                        $sheet->cell('B16', ' Overseas ');
							$sheet->cell('D16', $val['nri_male_voters'] ? $val['nri_male_voters'] : '=(0)');
							$sheet->cell('E16', $val['nri_female_voters'] ? $val['nri_female_voters'] : '=(0)');
							$sheet->cell('F16', $val['nri_other_voters'] ? $val['nri_other_voters'] : '=(0)');
							$sheet->cell('G16', ($val['nri_male_voters'] + $val['nri_female_voters'] + $val['nri_other_voters']) ? ($val['nri_male_voters'] + $val['nri_female_voters'] + $val['nri_other_voters']): '=(0)');
						
                        $sheet->cell('B17', ' Proxy ');
							$sheet->cell('G17', $val['proxy_votes'] ? $val['proxy_votes'] : '=(0)' );
                        $sheet->cell('B18', ' Postal ');
							$sheet->cell('G18', ($val['service_postal_votes_under_section_8'] + $val['service_postal_votes_gov']) ? ($val['service_postal_votes_under_section_8'] + $val['service_postal_votes_gov']) : '=(0)');
                        $sheet->cell('B19', ' Total');
						    $sheet->cell('G19', ($val['male_voter'] + $val['female_voter'] + $val['other_voter'] + $val['nri_male_voters'] + $val['nri_female_voters'] + $val['nri_other_voters'] + $val['proxy_votes'] + $val['service_postal_votes_under_section_8'] + $val['service_postal_votes_gov']) ? ($val['male_voter'] + $val['female_voter'] + $val['other_voter'] + $val['nri_male_voters'] + $val['nri_female_voters'] + $val['nri_other_voters'] + $val['proxy_votes'] + $val['service_postal_votes_under_section_8'] + $val['service_postal_votes_gov']) : '=(0)');
						
						$sheet->mergeCells('B20:C20');
                        $sheet->cell('B20', ' Votes Rejected due to other Reason ');
							$sheet->cell('G20', $val['rejected_votes_due_2_other_reason'] ? $val['rejected_votes_due_2_other_reason'] : '=(0)');
						
						$sheet->mergeCells('B21:C21');
						$sheet->cell('B21', ' POLLING PERCENTAGE ');
						$sheet->cell('G21', round($val['total_votes']/($val['gen_t'] + $val['nri_m']+$val['nri_f']+$val['nri_o'] + $val['ser_t'])*100,2) );
						
						$sheet->cell('A22', ' VOTES ');

						$sheet->mergeCells('B23:C23');
                        $sheet->cell('B23', ' Total Votes Polled On EVM ');
							$sheet->cell('G23', ($val['male_voter']+$val['female_voter']+$val['other_voter']+$val['test_votes_49_ma']+$val['nri_male_voters']+$val['nri_female_voters']+$val['nri_other_voters']) ? : '=(0)');
						
						$sheet->mergeCells('B24:C24');
                        $sheet->cell('B24', ' Total Deducted Votes From EVM ');
							$sheet->cell('G24', ($val['test_votes_49_ma']+$val['votes_not_retreived_from_evm']+$val['rejected_votes_due_2_other_reason']+$val['nota_evm_vote']) ?  : '=(0)');
						
						$sheet->mergeCells('B25:C25');
                        $sheet->cell('B25', ' Total Valid Votes polled on EVM ');
							$sheet->cell('G25', ($val['male_voter']+$val['female_voter']+$val['other_voter']+$val['test_votes_49_ma']+$val['nri_male_voters']+$val['nri_female_voters']+$val['nri_other_voters'])-($val['test_votes_49_ma']+$val['votes_not_retreived_from_evm']+$val['rejected_votes_due_2_other_reason']+$val['nota_evm_vote']) ? : '=(0)');
						
						$sheet->mergeCells('B26:C26');
                        $sheet->cell('B26', ' Postal Votes Counted ');
							$sheet->cell('G26', ($val['service_postal_votes_under_section_8'] + $val['service_postal_votes_gov'] ) ?  : '=(0)');
						
						$sheet->mergeCells('B27:C27');
                        $sheet->cell('B27', ' Postal Votes Deducted ');
							$sheet->cell('G27', ($val['rej_votes_postal']+$val['nota_postal_vote']) ? : '=(0)');
						
						$sheet->mergeCells('B28:C28');
                        $sheet->cell('B28', ' Valid Postal Votes ');
							$sheet->cell('G28', (($val['service_postal_votes_under_section_8'] + $val['service_postal_votes_gov'])-($val['rej_votes_postal']+$val['nota_postal_vote'])) ?  : '=(0)');
						
						$sheet->mergeCells('B29:C29');
                        $sheet->cell('B29', ' Total Valid Votes Polled ');
							$sheet->cell('G29', ($val['male_voter']+$val['female_voter']+$val['other_voter']+$val['test_votes_49_ma']+$val['nri_male_voters']+$val['nri_female_voters']+$val['nri_other_voters'])-($val['test_votes_49_ma']+$val['votes_not_retreived_from_evm']+$val['rejected_votes_due_2_other_reason']+$val['nota_evm_vote'])+($val['service_postal_votes_under_section_8'] + $val['service_postal_votes_gov'])-($val['rej_votes_postal']+$val['nota_postal_vote']) ?  : '=(0)');
						
						$sheet->mergeCells('B30:C30');
                        $sheet->cell('B30', ' Test Votes polled On EVM ');
							$sheet->cell('G30', $val['test_votes_49_ma'] ? $val['test_votes_49_ma'] : '=(0)');
						
						$sheet->mergeCells('B31:C31');
                        $sheet->cell('B31', " Votes Polled for 'NOTA'(Including Postal) ");
							$sheet->cell('G31', ($val['nota_evm_vote'] + $val['nota_postal_vote']) ? ($val['nota_evm_vote'] + $val['nota_postal_vote']): '=(0)');
						
						$sheet->mergeCells('B32:C32');
                        $sheet->cell('B32', ' Tendered Votes ');
							$sheet->cell('G32', $val['tended_votes'] ?  $val['tended_votes'] : '=(0)' );
												
						$sheet->mergeCells('A33:B33');
                        $sheet->cell('A33', ' POLLING STATION ');
						
						$sheet->cell('B34', ' Number ');
							$sheet->cell('D34', $val['total_polling_station_s_i_t_c']);
						
						$sheet->mergeCells('E34:F34');
                        $sheet->cell('E34', ' Average Electors Per Polling ');
						
							if($val['total_polling_station_s_i_t_c'] > 0){
								$avg = round(($val['gen_t'] + $val['nri_m']+$val['nri_f']+$val['nri_o'] + $val['ser_t'])/$val['total_polling_station_s_i_t_c'],0);
							}else{
								$avg = 0;
							}
												
							$sheet->cell('G34', $avg);
						
						
						$sheet->mergeCells('B35:C35');
                        $sheet->cell('B35', ' Dates(s) of Re-Poll if Any ');
						
						
						if (trim($val['date_of_repoll']) != 0 && $val['date_of_repoll']){
																			
						$repoll_dates 	= explode(',',$val['date_of_repoll']);
						$dates_array 	= [];
						foreach($repoll_dates as $res_repoll){
							$dates_array[] = date('d/m/Y', strtotime(trim($res_repoll)));
						}	
						
						$sheet->cell('G35', implode(', ', $dates_array));						
						}
						
						$sheet->mergeCells('B36:D36');
                        $sheet->cell('B36', ' Numbers Of Polling Stations where Re-Poll was Order  ');
							$sheet->cell('G36', $val['no_poll_station_where_repoll']);
						
						$sheet->cell('A37', ' DATES  ');
						
						$sheet->cell('D37', ' Polling  ');
						
						$sheet->cell('E37', ' Counting  ');
						
						$sheet->mergeCells('F37:G37');
						$sheet->cell('F37', ' Declaration Of Result  ');
						
						$sheet->cell('D37', date('d/m/Y', strtotime($val['DATE_POLL'])));
						$sheet->cell('E37', date('d/m/Y', strtotime($val['DATE_COUNT'])));
						$sheet->cell('F37', date('d/m/Y', strtotime($val['result_declared_date'])));
												
						$sheet->cell('A39', ' RESULT  ');
						$sheet->cell('D39', ' Party  ');
						$sheet->cell('E39', ' Candidates  ');
						$sheet->cell('F39', ' Votes  ');
						
						$sheet->cell('B40', ' Winner ');
							$sheet->cell('D40', $val['lead_cand_party']);
							$sheet->cell('E40', $val['lead_cand_name']);
							$sheet->cell('F40', $val['lead_total_vote']);
						
						$sheet->cell('B41', ' Runner-Up ');
							$sheet->cell('D41', $val['trail_cand_party']);
							$sheet->cell('E41', $val['trail_cand_name']);
							$sheet->cell('F41', $val['trail_total_vote']);
						$sheet->cell('B42', ' Margin  ');
							$sheet->cell('D42', $val['margin']);



          

            $sheet->mergeCells("A45:B45");
            $sheet->cell('A45', function($cells) {
              $cells->setValue('Disclaimer');
              $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
            });

            

            $sheet->getStyle('A46')->getAlignment()->setWrapText(true);
            $sheet->setSize('A46', 25,40);



            $sheet->mergeCells("A46:G46");
            $sheet->cell('A46', function($cells) {
            $cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
            $cells->setFont(array('name' => 'Times New Roman','size' => 10));
            });
						
						
						});

              
					
					   }}
					  
					  
                	  })->download('xls');
					  
                }   //Constituency data summary excel ends here

      // return view('PoliticalPartyWiseDepositsForfeited.PoliticalpartyWiseDepositsForfeited',compact('statewisedata','electedcanddata'));
  }


public function indexxls(Request $request){

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



   $session = $request->session()->all();
 //  dd($session);
  $datanew = array();
   $st_code = $session['admin_login_details']['st_code'];

   $st_name = $session['admin_login_details']['placename'];

       $sSelect = array(
          'B.ST_NAME','B.st_code','A.pc_no','A.PC_TYPE','A.PC_NAME'

      );
      $sTable = 'm_pc AS A';
      $sGroup = array(
          'A.st_code','A.pc_no'
      );
       $countSeats = DB::table($sTable)
                       ->select($sSelect)
                       ->join('m_state AS B','B.ST_CODE','A.st_code')
                       // ->where('B.ST_CODE','S01')
                       ->groupBy($sGroup)
                       ->get()->toArray();
        //echo "<pre>"; print_r($countSeats); die;

       foreach ($countSeats as  $value) {

       $st_code = $value->st_code;
        $pc = $value->pc_no;

       $indexCardDatas = App\models\Admin\CandidateModel::get_count_nominated($st_code,$pc);
       $indexCardDatasDf = DB::select("SELECT cp.st_code,cp.pc_no,
        SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
       where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'male' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdmale,

       SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
       where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'female' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdfemale,

       SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
       where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'third' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdthird,


       SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
       where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fd

       FROM `counting_pcmaster` as cp
       join candidate_personal_detail as C on C.candidate_id = cp.candidate_id
       WHERE cp.candidate_id != (select candidate_id from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code)
       AND cp.party_id != 1180 AND cp.st_code = '$st_code' and cp.pc_no = $pc");


        $electorData = DB::table('electors_cdac AS ec')
           ->select(array(
               'ovi.general_male_voters AS male_voter',
               'ovi.general_female_voters AS female_voter',
               'ovi.general_other_voters AS other_voter',
               'ovi.nri_male_voters AS nri_male_voters',
               'ovi.nri_female_voters AS nri_female_voters',
               'ovi.nri_other_voters AS nri_other_voters',
               'ovi.test_votes_49_ma AS test_votes_49_ma',
             'ovi.votes_not_retreived_from_evm AS votes_not_retreived_from_evm',
             'ovi.rejected_votes_due_2_other_reason AS rejected_votes_due_2_other_reason',
             'ovi.service_postal_votes_under_section_8 AS service_postal_votes_under_section_8',

             'ovi.service_postal_votes_gov AS service_postal_votes_gov',
             'ovi.proxy_votes AS proxy_votes',
             'ovi.total_polling_station_s_i_t_c AS total_polling_station_s_i_t_c',
             'ovi.date_of_repoll AS date_of_repoll',
             'ovi.no_poll_station_where_repoll AS no_poll_station_where_repoll',

           DB::raw('SUM(ec.gen_electors_male) AS gen_m'),
           DB::raw("SUM(ec.service_male_electors) AS ser_m"),
           DB::raw("SUM(ec.nri_male_electors) AS nri_m"),

           DB::raw("SUM(ec.gen_electors_female) AS gen_f"),
           DB::raw("SUM(ec.service_female_electors) AS ser_f"),
           DB::raw("SUM(ec.nri_female_electors) AS nri_f"),

           DB::raw("SUM(ec.gen_electors_other) AS gen_o"),
           DB::raw("SUM(ec.nri_third_electors) AS nri_o"),
           DB::raw("SUM(ec.service_third_electors) AS ser_o"),

           DB::raw("SUM(ec.gen_electors_male + ec.gen_electors_female + ec.gen_electors_other) AS gen_t"),
           DB::raw("SUM(ec.service_male_electors+ec.service_female_electors+ec.service_third_electors) AS ser_t"),
           DB::raw("SUM(ec.nri_male_electors + ec.nri_female_electors+ ec.nri_third_electors) AS total_o"),

           DB::raw("SUM(ec.electors_male + ec.service_male_electors) AS total_m"),
           DB::raw("SUM(ec.electors_female + ec.service_female_electors) AS total_f"),
           DB::raw("SUM(ec.electors_other + ec.service_third_electors) AS total_other_electors"),
           DB::raw("SUM(ec.electors_total + ec.electors_service) AS total_all")

         ))

          ->leftJoin('electors_cdac_other_information as ovi',function($query){
            $query->on('ovi.st_code','ec.st_code')
                ->on('ovi.pc_no','ec.pc_no');

          })

         ->where(array(
           'ec.st_code' => $st_code,
           'ec.pc_no'   => $pc,
           'ec.year'    => '2019'
         ))

         ->groupBy('ec.st_code','ec.pc_no')

         ->first();

         //echo '<pre>'; print_r($electorData); echo '</pre>';

         $evmvotesfromcp = DB::table('counting_pcmaster')
                           ->select(array(
                           DB::raw('SUM(evm_vote) AS evm_votes'),
                           DB::raw("SUM(postal_vote) AS postal_votes"),
                           DB::raw("SUM(total_vote) AS total_votes"),
                           DB::raw("rejectedvote AS rej_votes_postal"),
                           DB::raw("tended_votes AS tended_votes")
                           ))
                           ->where(array(
                           'st_code' => $st_code,
                           'pc_no'   => $pc
                           ))
                           ->groupBy('st_code')
                           ->first();

         //echo '<pre>'; print_r($evmvotesfromcp); die;

         $datanotapcwise = DB::table('counting_pcmaster')
                               ->select('postal_vote','evm_vote','migrate_votes')
                               ->where(array(
                                       'st_code' => $st_code,
                                       'pc_no'    => $pc,
                                       'party_id'=>1180
                                ))
                                ->first();

         //echo '<pre>'; print_r($datanotapcwise); echo '</pre>';


         $pollDateInfoPcwise = DB::table('m_schedule as ms')
                               ->select('ms.DATE_POLL','ms.DATE_COUNT','wlc.result_declared_date',
                               'wlc.lead_cand_name','wlc.lead_cand_party','wlc.lead_total_vote','wlc.trail_total_vote',
                               'wlc.trail_cand_party','wlc.trail_cand_name','wlc.margin')
                               ->join('m_election_details as mss','mss.ScheduleID','ms.SCHEDULEID')
                               ->join('winning_leading_candidate as wlc', function($join){
                                   $join->on('wlc.st_code', 'mss.st_code')
                                           ->on('wlc.pc_no', 'mss.CONST_NO');
                               })
                               ->where(array(
                                       'mss.ST_CODE' => $st_code,
                                       'mss.CONST_NO'   => $pc,
                                       'mss.CONST_TYPE'   => 'PC',
                                       'mss.YEAR'   => '2019'
                                ))
                               ->first();

         //echo '<pre>'; print_r($pollDateInfoPcwise); echo '</pre>';

         $finalArray = array();

         $finalArray = array(
                               'st_code' => $st_code,
                               'pc_no' => $pc,
                               'c_nom_m_t'=> $indexCardDatas['nom_male'],
                               'c_nom_f_t'=> $indexCardDatas['nom_female'],
                               'c_nom_o_t'=> $indexCardDatas['nom_third'],

                               'c_wd_m_t'=> $indexCardDatas['with_male'],
                               'c_wd_f_t'=> $indexCardDatas['with_female'],
                               'c_wd_o_t'=> $indexCardDatas['with_third'],

                               'c_rej_m_t'=> $indexCardDatas['rej_male'],
                               'c_rej_f_t'=> $indexCardDatas['rej_female'],
                               'c_rej_o_t'=> $indexCardDatas['rej_third'],

                                'c_acp_m_t'=> $indexCardDatas['cont_male'],
                               'c_acp_f_t'=> $indexCardDatas['cont_female'],
                               'c_acp_o_t'=> $indexCardDatas['cont_third'],

                               'c_fd_m_t'=> $indexCardDatasDf[0]->fdmale,
                               'c_fd_f_t'=> $indexCardDatasDf[0]->fdfemale,
                               'c_fd_o_t'=> $indexCardDatasDf[0]->fdthird,
                               'c_fd_t'   => $indexCardDatasDf[0]->fd,

                               'male_voter' => @$electorData->male_voter,
                               'female_voter' => @$electorData->female_voter,
                               'other_voter' => @$electorData->other_voter,
                               'nri_male_voters' => @$electorData->nri_male_voters,
                               'nri_female_voters' => @$electorData->nri_female_voters,
                               'nri_other_voters' => @$electorData->nri_other_voters,
                               'test_votes_49_ma' => @$electorData->test_votes_49_ma,
                               'votes_not_retreived_from_evm' => @$electorData->votes_not_retreived_from_evm,
                               'rejected_votes_due_2_other_reason' => @$electorData->rejected_votes_due_2_other_reason,
                               'service_postal_votes_under_section_8' => @$electorData->service_postal_votes_under_section_8,
                               'service_postal_votes_gov' => @$electorData->service_postal_votes_gov,
                               'proxy_votes' => @$electorData->proxy_votes,
                               'total_polling_station_s_i_t_c' => @$electorData->total_polling_station_s_i_t_c,
                               'date_of_repoll' => @$electorData->date_of_repoll,
                               'no_poll_station_where_repoll' => @$electorData->no_poll_station_where_repoll,
                               'gen_m' => @$electorData->gen_m,
                               'ser_m' => @$electorData->ser_m,
                               'nri_m' => @$electorData->nri_m,
                               'gen_f' => @$electorData->gen_f,
                               'ser_f' => @$electorData->ser_f,
                               'nri_f' => @$electorData->nri_f,
                               'gen_o' => @$electorData->gen_o,
                               'nri_o' => @$electorData->nri_o,
                               'ser_o' => @$electorData->ser_o,
                               'gen_t' => @$electorData->gen_t,
                               'ser_t' => @$electorData->ser_t,
                               'total_o' => @$electorData->total_o,
                               'total_m' => @$electorData->total_m,
                               'total_f' => @$electorData->total_f,
                               'total_other_electors' => @$electorData->total_other_electors,
                               'total_all' => @$electorData->total_all,
                               'evm_votes'=> @$evmvotesfromcp->evm_votes,
                               'postal_votes'=> @$evmvotesfromcp->postal_votes,
                               'total_votes'=> @$evmvotesfromcp->total_votes,
                               'rej_votes_postal'=> @$evmvotesfromcp->rej_votes_postal,
                               'tended_votes'=> @$evmvotesfromcp->tended_votes,

                               'nota_postal_vote'=> @$datanotapcwise->postal_vote,
                               'nota_evm_vote'=> @$datanotapcwise->evm_vote,
                               'DATE_POLL'=> @$pollDateInfoPcwise->DATE_POLL,
                               'DATE_COUNT'=> @$pollDateInfoPcwise->DATE_COUNT,
                               'result_declared_date'=> @$pollDateInfoPcwise->result_declared_date,

                               'lead_cand_name'=> @$pollDateInfoPcwise->lead_cand_name,
                               'lead_cand_party'=> @$pollDateInfoPcwise->lead_cand_party,
                               'lead_total_vote'=> @$pollDateInfoPcwise->lead_total_vote,
                               'trail_cand_name'=> @$pollDateInfoPcwise->trail_cand_name,
                               'trail_cand_party'=> @$pollDateInfoPcwise->trail_cand_party,
                               'trail_total_vote'=> @$pollDateInfoPcwise->trail_total_vote,
                               'margin'=> @$pollDateInfoPcwise->margin,

                               );


                             $finalArraynew[$st_code][$pc] = array(
                               'st_code' => $st_code,
                               'PC_NAME' => $value->PC_NAME,
                               'pc_type' => $value->PC_TYPE,
                               'pc_no' => $pc,
                               'c_nom_m_t'=> $indexCardDatas['nom_male'],
                               'c_nom_f_t'=> $indexCardDatas['nom_female'],
                               'c_nom_o_t'=> $indexCardDatas['nom_third'],

                               'c_wd_m_t'=> $indexCardDatas['with_male'],
                               'c_wd_f_t'=> $indexCardDatas['with_female'],
                               'c_wd_o_t'=> $indexCardDatas['with_third'],

                               'c_rej_m_t'=> $indexCardDatas['rej_male'],
                               'c_rej_f_t'=> $indexCardDatas['rej_female'],
                               'c_rej_o_t'=> $indexCardDatas['rej_third'],

                                'c_acp_m_t'=> $indexCardDatas['cont_male'],
                               'c_acp_f_t'=> $indexCardDatas['cont_female'],
                               'c_acp_o_t'=> $indexCardDatas['cont_third'],

                               'c_fd_m_t'=> $indexCardDatasDf[0]->fdmale,
                               'c_fd_f_t'=> $indexCardDatasDf[0]->fdfemale,
                               'c_fd_o_t'=> $indexCardDatasDf[0]->fdthird,
                               'c_fd_t'   => $indexCardDatasDf[0]->fd,

                               'male_voter' => @$electorData->male_voter,
                               'female_voter' => @$electorData->female_voter,
                               'other_voter' => @$electorData->other_voter,
                               'nri_male_voters' => @$electorData->nri_male_voters,
                               'nri_female_voters' => @$electorData->nri_female_voters,
                               'nri_other_voters' => @$electorData->nri_other_voters,
                               'test_votes_49_ma' => @$electorData->test_votes_49_ma,
                               'votes_not_retreived_from_evm' => @$electorData->votes_not_retreived_from_evm,
                               'rejected_votes_due_2_other_reason' => @$electorData->rejected_votes_due_2_other_reason,
                               'service_postal_votes_under_section_8' => @$electorData->service_postal_votes_under_section_8,
                               'service_postal_votes_gov' => @$electorData->service_postal_votes_gov,
                               'proxy_votes' => @$electorData->proxy_votes,
                               'total_polling_station_s_i_t_c' => @$electorData->total_polling_station_s_i_t_c,
                               'date_of_repoll' => @$electorData->date_of_repoll,
                               'no_poll_station_where_repoll' => @$electorData->no_poll_station_where_repoll,
                               'gen_m' => @$electorData->gen_m,
                               'ser_m' => @$electorData->ser_m,
                               'nri_m' => @$electorData->nri_m,
                               'gen_f' => @$electorData->gen_f,
                               'ser_f' => @$electorData->ser_f,
                               'nri_f' => @$electorData->nri_f,
                               'gen_o' => @$electorData->gen_o,
                               'nri_o' => @$electorData->nri_o,
                               'ser_o' => @$electorData->ser_o,
                               'gen_t' => @$electorData->gen_t,
                               'ser_t' => @$electorData->ser_t,
                               'total_o' => @$electorData->total_o,
                               'total_m' => @$electorData->total_m,
                               'total_f' => @$electorData->total_f,
                               'total_other_electors' => @$electorData->total_other_electors,
                               'total_all' => @$electorData->total_all,
                               'evm_votes'=> @$evmvotesfromcp->evm_votes,
                               'postal_votes'=> @$evmvotesfromcp->postal_votes,
                               'total_votes'=> @$evmvotesfromcp->total_votes,
                               'rej_votes_postal'=> @$evmvotesfromcp->rej_votes_postal,
                               'tended_votes'=> @$evmvotesfromcp->tended_votes,

                               'nota_postal_vote'=> @$datanotapcwise->postal_vote,
                               'nota_evm_vote'=> @$datanotapcwise->evm_vote,
                               'DATE_POLL'=> @$pollDateInfoPcwise->DATE_POLL,
                               'DATE_COUNT'=> @$pollDateInfoPcwise->DATE_COUNT,
                               'result_declared_date'=> @$pollDateInfoPcwise->result_declared_date,

                               'lead_cand_name'=> @$pollDateInfoPcwise->lead_cand_name,
                               'lead_cand_party'=> @$pollDateInfoPcwise->lead_cand_party,
                               'lead_total_vote'=> @$pollDateInfoPcwise->lead_total_vote,
                               'trail_cand_name'=> @$pollDateInfoPcwise->trail_cand_name,
                               'trail_cand_party'=> @$pollDateInfoPcwise->trail_cand_party,
                               'trail_total_vote'=> @$pollDateInfoPcwise->trail_total_vote,
                               'margin'=> @$pollDateInfoPcwise->margin,

                               );
                             }


               //echo "<pre>"; print_r($finalArraynew); die;

//               echo "<pre>"; print_r( $user);die;

                if($user->designation == 'ROPC'){
                   $prefix     = 'ropc';
               }else if($user->designation == 'CEO'){
                   $prefix     = 'pcceo';
               }else if($user->role_id == '27'){
                 $prefix     = 'eci-index';
               }else if($user->role_id == '7'){
                 $prefix     = 'eci';
               }


$arrayData = array();
$i=1;
foreach($finalArraynew as $values){
foreach($values as  $val){

$arrayData[$val['pc_no']]['statecode'] = $val['st_code'];
$arrayData[$val['pc_no']]['pcno'] = $val['pc_no'];
$arrayData[$val['pc_no']]['pcname'] = $val['PC_NAME'];
$arrayData[$val['pc_no']]['pctype'] = $val['pc_type'];
//Nominated Filed
$arrayData[$val['pc_no']]['pcnodetals'][$i]['candidate'] = 'I. Candidates';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['Menc'] = 'Men';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['Womanc'] = 'Woman';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['ThirdGenderc'] = 'Third Gender';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['Nominatedtotal'] = 'Total';
//Nominated Rejected
$arrayData[$val['pc_no']]['pcnodetals'][$i]['NominatedFiled'] = '1. Nominated Filed';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['c_nom_m_t'] = $val['c_nom_m_t'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['c_nom_f_t'] = $val['c_nom_f_t'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['c_nom_o_t'] = $val['c_nom_o_t'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['Nominatedrtotal'] = $val['c_nom_m_t']+$val['c_nom_f_t']+$val['c_nom_o_t'];
//Nominated Withdraw
$arrayData[$val['pc_no']]['pcnodetals'][$i]['NominatedRejected'] = '2. Nominated Rejected';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['c_rej_m_t'] = $val['c_rej_m_t'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['c_rej_f_t'] = $val['c_rej_f_t'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['c_rej_o_t'] = $val['c_rej_o_t'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['NominatedRejectedtotal'] = $val['c_rej_m_t']+$val['c_rej_f_t']+$val['c_rej_o_t'];
//Contested
$arrayData[$val['pc_no']]['pcnodetals'][$i]['NominatedWithdraw'] = '3. Nominated Withdraw';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['c_wd_m_t'] = $val['c_wd_m_t'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['c_wd_f_t'] = $val['c_wd_f_t'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['c_wd_o_t'] = $val['c_wd_o_t'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['NominatedWithdrawtotal'] = $val['c_wd_m_t']+$val['c_wd_f_t']+$val['c_wd_o_t'];
//Forfeighted
$arrayData[$val['pc_no']]['pcnodetals'][$i]['Contested'] = '4. Contested';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['c_acp_m_t'] = $val['c_acp_m_t'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['c_acp_f_t'] = $val['c_acp_f_t'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['c_acp_o_t'] = $val['c_acp_o_t'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['Contestedtotal'] = $val['c_acp_m_t']+$val['c_acp_f_t']+$val['c_acp_o_t'];

$arrayData[$val['pc_no']]['pcnodetals'][$i]['ForfeightedDeposits'] = '5.Forfeighted Deposits';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['c_fd_m_t'] = $val['c_fd_m_t'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['c_fd_f_t'] = $val['c_fd_f_t'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['c_fd_o_t'] = $val['c_fd_o_t'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['totalForfeightedtotal'] = $val['c_fd_m_t']+$val['c_fd_f_t']+$val['c_fd_o_t'];
// end candidate


// electors

$arrayData[$val['pc_no']]['pcnodetals'][$i]['electors'] = 'II. Electors';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['Mene'] = 'Men';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['Womane'] = 'Woman';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['ThirdGendere'] = 'Third Gender';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['electorstotal'] = 'Total';
///GENERAL(Other than OVERSEAS.
$arrayData[$val['pc_no']]['pcnodetals'][$i]['GENERALoversel'] = '1. GENERAL(Other than OVERSEAS';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['gen_m'] = $val['gen_m'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['gen_f'] = $val['gen_f'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['gen_o'] = $val['gen_o'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['totalgeneraloverse'] = $val['gen_t'];
///Overseas.
$arrayData[$val['pc_no']]['pcnodetals'][$i]['Overseas'] = '2. Overseas';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['nri_m'] = $val['nri_m'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['nri_f'] = $val['nri_f'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['nri_o'] = $val['nri_o'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['totaloverseasContested'] = $val['nri_m']+$val['nri_f']+$val['nri_o'];
///Services.
$arrayData[$val['pc_no']]['pcnodetals'][$i]['Services'] = '3. Services';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['ser_m'] = $val['ser_m'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['ser_f'] = $val['ser_f'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['ser_o'] = $val['ser_o'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['totalservicesContested'] = $val['ser_t'];
// electors Total.
$arrayData[$val['pc_no']]['pcnodetals'][$i]['total'] = '4. Total';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['total_m'] = $val['total_m'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['total_f'] = $val['total_f'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['total_other_electors'] = $val['total_other_electors'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['total_all'] = $val['total_all'];
// end Electors

// votes.
$arrayData[$val['pc_no']]['pcnodetals'][$i]['VOTES'] = 'III VOTES';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vMen'] = 'Men';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vWoman'] = 'Woman';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vThirdGender'] = 'Third Gender';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vTotal'] = 'Total';
//General
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vGeneral'] = '1. General';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['male_voter'] = $val['male_voter'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['female_voter'] = $val['female_voter'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['other_voter'] = $val['other_voter'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vtotalgeneral'] = $val['male_voter']+$val['female_voter']+$val['other_voter'];
//Overseas
$arrayData[$val['pc_no']]['pcnodetals'][$i]['voverseas'] = '2. Overseas';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vnri_male_voters'] = $val['nri_male_voters'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vnri_female_voters'] = $val['nri_female_voters'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vnri_other_voters'] = $val['nri_other_voters'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['voverseastotal'] = $val['nri_male_voters']+$val['nri_female_voters']+$val['nri_other_voters'];
//Proxy
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vProxy'] = '3. Proxy';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vnri_male_Proxy'] = '';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vnri_female_Proxy'] = '';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vnri_other_Proxy'] = '';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['totalProxy'] = $val['proxy_votes'];
/// postal.
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vpostal'] = '4. Postal';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vnri_male_postal'] = '';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vnri_female_postal'] = '';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vnri_other_postal'] = '';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['totalProxyvote'] = $val['postal_votes'];

/// 5. vTotal.
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vtotaltitle'] = '5. Total';
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vnmvoters'] = $val['male_voter']+$val['nri_male_voters'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vnfvoters'] = $val['female_voter']+$val['nri_female_voters'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vngvoters'] = $val['other_voter']+$val['nri_other_voters'];
$arrayData[$val['pc_no']]['pcnodetals'][$i]['vtotal'] = $val['total_votes'];
//end votes

$arrayData[$val['pc_no']]['fourVotes'] = 'IV Votes';
$arrayData[$val['pc_no']]['TotalVotesPolledonevm'] = '1. Total Votes Polled On EVM';
$arrayData[$val['pc_no']]['TotalVotesPolledonevmd'] = $val['evm_votes']+$val['test_votes_49_ma']+$val['votes_not_retreived_from_evm']+$val['rejected_votes_due_2_other_reason']+$val['nota_evm_vote'];

$arrayData[$val['pc_no']]['Totaldeductedvotesevm'] = '2.Total deducted votes from evm(test votes+votes not retrived+votes rejected due to other reasons + "Nota")';
$arrayData[$val['pc_no']]['TotalVotesrejected'] =$val['test_votes_49_ma']+$val['votes_not_retreived_from_evm']+$val['rejected_votes_due_2_other_reason']+$val['nota_evm_vote'];

$arrayData[$val['pc_no']]['Totalpllledevm'] = '3.Total valid votes polled on evm';
$arrayData[$val['pc_no']]['Totalpllledevmd'] =$val['evm_votes'];

$arrayData[$val['pc_no']]['postalvotecount'] = '4. Postal Votes Counted';
$arrayData[$val['pc_no']]['postalvotecountd'] =$val['service_postal_votes_under_section_8'] + $val['service_postal_votes_gov'];




$arrayData[$val['pc_no']]['postalvotenota'] = '5. Postal Votes Deducted REJECTED POSTAL
    VOTES + POSTAL VOTES POLLED FOR NOTA)';
$arrayData[$val['pc_no']]['postalvotenotad'] =$val['rej_votes_postal']+$val['nota_postal_vote'];

$arrayData[$val['pc_no']]['valiedpostalvote'] = '6. Valid Postal Votes';
$arrayData[$val['pc_no']]['valiedpostalvoted'] =$val['postal_votes'];

$arrayData[$val['pc_no']]['totalvalidpolled'] = '7. Total Valid Votes Polled';
$arrayData[$val['pc_no']]['totalvalidpolledd'] =$val['total_votes'];

$arrayData[$val['pc_no']]['votespolledonevm'] = '8. Test Votes polled On EVM';
$arrayData[$val['pc_no']]['votespolledonevmd'] =$val['test_votes_49_ma'];

$arrayData[$val['pc_no']]['tendredvotes'] = '9. Tendered Votes';
$arrayData[$val['pc_no']]['tendredvotesd'] =$val['tended_votes'];

$arrayData[$val['pc_no']]['pollingstation'] = 'V. Polling Stations';
$arrayData[$val['pc_no']]['number'] = 'Number';
$arrayData[$val['pc_no']]['average'] = 'Average Electors Per Polling Stations';
$arrayData[$val['pc_no']]['na'] = 'N/A';

$arrayData[$val['pc_no']]['dateofRepollsifany'] = 'Date of Repolls If Any';
$arrayData[$val['pc_no']]['dateofroll'] = $val['date_of_repoll'];

$arrayData[$val['pc_no']]['numberofpollingstations'] = 'Number Of Polling Stations where Re Polls Was Ordere';

$arrayData[$val['pc_no']]['numberofpollingstationd'] = $val['no_poll_station_where_repoll'];

$arrayData[$val['pc_no']]['viDates'] = 'VI. Dates';

$arrayData[$val['pc_no']]['Polling'] = 'Polling';
$arrayData[$val['pc_no']]['Counting'] = 'Counting';
$arrayData[$val['pc_no']]['declarationofresults'] = 'Declaration Of Results';

$arrayData[$val['pc_no']]['datePolling'] = $val['DATE_POLL'];
$arrayData[$val['pc_no']]['datecount'] = $val['DATE_COUNT'];
$arrayData[$val['pc_no']]['resuladeclarationdate'] = $val['result_declared_date'];

$arrayData[$val['pc_no']]['eightResults'] = 'VII. Results';
$arrayData[$val['pc_no']]['Party'] = 'Party';
$arrayData[$val['pc_no']]['Candidates'] = 'Candidates';
$arrayData[$val['pc_no']]['Votes'] = 'Votes';

$arrayData[$val['pc_no']]['winner'] = 'Winner';
$arrayData[$val['pc_no']]['lead_cand_party'] = $val['lead_cand_party'];
$arrayData[$val['pc_no']]['lead_cand_name'] = $val['lead_cand_name'];
$arrayData[$val['pc_no']]['lead_total_vote'] = $val['lead_total_vote'];

$arrayData[$val['pc_no']]['runnerup'] = 'Runner Up';
$arrayData[$val['pc_no']]['trail_cand_party'] = $val['trail_cand_party'];
$arrayData[$val['pc_no']]['trail_cand_name'] = $val['trail_cand_name'];
$arrayData[$val['pc_no']]['trail_total_vote'] = $val['trail_total_vote'];

$arrayData[$val['pc_no']]['Margin'] = 'Margin';
$arrayData[$val['pc_no']]['Marginone'] = '';
$arrayData[$val['pc_no']]['Margintwo'] = '';
$arrayData[$val['pc_no']]['Margind'] = $val['margin'];


//echo "<pre>";  print_r($val);
//$arrayData[$values->pc_no]['pcno'] = $values->pc_no;

}
$i++;}
//echo "<pre>";print_r($arrayData[8]);die;
    $date = date('Y-m-d');
    return Excel::create($date.'-Report', function($excel) use ($arrayData) {
     $excel->sheet('mySheet', function($sheet) use ($arrayData)
     {
      $sheet->mergeCells('A1:E1');
      //$sheet->mergeCells('B3:C3');
      $sheet->mergeCells('K3:M3');
      $sheet->setWidth('B', 10);
      $sheet->setWidth('A', 30);
      $sheet->setWidth('C', 10);
      $sheet->setWidth('D', 20);
      $sheet->cells('A1', function($cells) {
      $cells->setValue('CONSTITUENCY DATA - SUMMARY');
      $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
      $cells->setFontColor("#ffffff");
      $cells->setBackground("#042179");
      $cells->setAlignment('center');

      });

       $r=2;
       foreach($arrayData as  $value){
       foreach($value['pcnodetals'] as  $val){

        $sheet->cell('A'. $r , ($value['statecode']));
        $sheet->cell('B'. $r , $value['pcname']." ".($value['pctype']));
        $sheet->cell('C'. $r , $val['candidate']);
        $sheet->cell('D'. $r , $val['Menc']);
        $sheet->cell('E'. $r , $val['Womanc']);
        $sheet->cell('F'. $r , $val['ThirdGenderc']);
        $sheet->cell('G'. $r , $val['Nominatedtotal']);

      if(!empty($val['NominatedFiled'] == '1. Nominated Filed')){
        $tottal = $r+1;
        $sheet->cell('C'. $tottal , $val['NominatedFiled']);
        $sheet->cell('D'. $tottal , $val['c_nom_m_t']);
        $sheet->cell('E'. $tottal , $val['c_nom_f_t']);
        $sheet->cell('F'. $tottal , ($val['c_nom_o_t'])?$val['c_nom_o_t'] :'=(0)');
        $sheet->cell('G'. $tottal , $val['c_nom_m_t']+$val['c_nom_f_t']+$val['c_nom_o_t']);
       }
       $r= $tottal;

       if(!empty($val['NominatedRejected'] == '2. Nominated Rejected')){
         $tottal = $r+1;
         $sheet->cell('C'. $tottal , $val['NominatedRejected']);
         $sheet->cell('D'. $tottal , $val['c_rej_m_t']);
         $sheet->cell('E'. $tottal , $val['c_rej_f_t']);
         $sheet->cell('F'. $tottal , ($val['c_rej_o_t'])?$val['c_rej_o_t']:'0');
         $sheet->cell('G'. $tottal , $val['c_rej_m_t']+$val['c_rej_f_t']+$val['c_rej_o_t']);
        }
        $r= $tottal;

       if(!empty($val['NominatedWithdraw'] == '3. Nominated Withdraw')){
         $tottal = $r+1;
         $sheet->cell('C'. $tottal , $val['NominatedWithdraw']);
         $sheet->cell('D'. $tottal , $val['c_wd_m_t']);
         $sheet->cell('E'. $tottal , $val['c_wd_f_t']);
         $sheet->cell('F'. $tottal , ($val['c_wd_o_t'])?$val['c_wd_o_t']:'0');
         $sheet->cell('G'. $tottal , $val['c_wd_m_t']+$val['c_wd_f_t']+$val['c_wd_o_t']);
        }
        $r= $tottal;
       if(!empty($val['Contested'] == '4. Contested')){
         $tottal = $r+1;
         $sheet->cell('C'. $tottal , $val['Contested']);
         $sheet->cell('D'. $tottal , $val['c_acp_m_t']);
         $sheet->cell('E'. $tottal , $val['c_acp_f_t']);
         $sheet->cell('F'. $tottal , ($val['c_acp_o_t'])?$val['c_acp_o_t']:'0');
         $sheet->cell('G'. $tottal , $val['c_acp_o_t']+$val['c_acp_f_t']+$val['c_acp_m_t']);
        }
        $r= $tottal;

       if(!empty($val['ForfeightedDeposits'] == '5.Forfeighted Deposits')){
         $tottal = $r+1;
         $sheet->cell('C'. $tottal , $val['ForfeightedDeposits']);
         $sheet->cell('D'. $tottal , $val['c_fd_m_t']);
         $sheet->cell('E'. $tottal , $val['c_fd_f_t']);
         $sheet->cell('F'. $tottal , ($val['c_fd_o_t'])?$val['c_fd_o_t']:'0');
         $sheet->cell('G'. $tottal , $val['totalForfeightedtotal']);
        }
        $r= $tottal;

       if(!empty($val['electors'] == 'II. Electors')){
         $tottal = $r+1;
         $sheet->cell('C'. $tottal , $val['electors']);
         $sheet->cell('D'. $tottal , $val['Mene']);
         $sheet->cell('E'. $tottal , $val['Womane']);
         $sheet->cell('F'. $tottal , ($val['ThirdGendere']));
         $sheet->cell('G'. $tottal , $val['electorstotal']);
        }
        $r= $tottal;


        if(!empty($val['GENERALoversel'] == '1. GENERAL(Other than OVERSEAS')){
          $tottal = $r+1;
          $sheet->cell('C'. $tottal , $val['GENERALoversel']);
          $sheet->cell('D'. $tottal , $val['gen_m']);
          $sheet->cell('E'. $tottal , $val['gen_f']);
          $sheet->cell('F'. $tottal , ($val['gen_o'])?$val['gen_o'] :'=(0)');
          $sheet->cell('G'. $tottal , $val['totalgeneraloverse']);
         }
         $r= $tottal;

        if(!empty($val['Overseas'] == '2. Overseas')){
          $tottal = $r+1;
          $sheet->cell('C'. $tottal , $val['Overseas']);
          $sheet->cell('D'. $tottal , $val['nri_m']);
          $sheet->cell('E'. $tottal , $val['nri_f']);
          $sheet->cell('F'. $tottal , ($val['nri_o'])?$val['nri_o']:'0');
          $sheet->cell('G'. $tottal , $val['totaloverseasContested']);
         }
         $r= $tottal;
         if(!empty($val['Services'] == '3. Services')){
           $tottal = $r+1;
           $sheet->cell('C'. $tottal , $val['Services']);
           $sheet->cell('D'. $tottal , $val['ser_m']);
           $sheet->cell('E'. $tottal , $val['ser_f']);
           $sheet->cell('F'. $tottal , ($val['ser_o'])?$val['ser_o']:'0');
           $sheet->cell('G'. $tottal , $val['totalservicesContested']);
          }
          $r= $tottal;

        if(!empty($val['total'] == '4. Total')){
          $tottal = $r+1;
          $sheet->cell('C'. $tottal , $val['total']);
          $sheet->cell('D'. $tottal , $val['total_m']);
          $sheet->cell('E'. $tottal , $val['total_f']);
          $sheet->cell('F'. $tottal , ($val['total_other_electors'])?$val['total_other_electors']:'0');
          $sheet->cell('G'. $tottal , $val['total_all']);
         }
         $r= $tottal;

         if(!empty($val['VOTES'] == 'III VOTES')){
           $tottal = $r+1;
           $sheet->cell('C'. $tottal , $val['VOTES']);
           $sheet->cell('D'. $tottal , $val['vMen']);
           $sheet->cell('E'. $tottal , $val['vWoman']);
           $sheet->cell('F'. $tottal , ($val['vThirdGender']));
           $sheet->cell('G'. $tottal , $val['vTotal']);
          }
          $r= $tottal;

         if(!empty($val['vGeneral'] == '1. General')){
           $tottal = $r+1;
           $sheet->cell('C'. $tottal , $val['vGeneral']);
           $sheet->cell('D'. $tottal , $val['male_voter']);
           $sheet->cell('E'. $tottal , $val['female_voter']);
           $sheet->cell('F'. $tottal , ($val['other_voter']));
           $sheet->cell('G'. $tottal , $val['vtotalgeneral']);
          }
          $r= $tottal;

         if(!empty($val['voverseas'] == '2. Overseas')){
           $tottal = $r+1;
           $sheet->cell('C'. $tottal , $val['voverseas']);
           $sheet->cell('D'. $tottal , $val['vnri_male_voters']);
           $sheet->cell('E'. $tottal , $val['vnri_female_voters']);
           $sheet->cell('F'. $tottal , ($val['vnri_other_voters']));
           $sheet->cell('G'. $tottal , $val['voverseastotal']);
          }
          $r= $tottal;

         if(!empty($val['vProxy'] == '3. Proxy')){
           $tottal = $r+1;
           $sheet->cell('C'. $tottal , $val['vProxy']);
           $sheet->cell('D'. $tottal , $val['vnri_male_Proxy']);
           $sheet->cell('E'. $tottal , $val['vnri_female_Proxy']);
           $sheet->cell('F'. $tottal , ($val['vnri_other_Proxy']));
           $sheet->cell('G'. $tottal , $val['totalProxy']);
          }
          $r= $tottal;

         if(!empty($val['vpostal'] == '4. Postal')){
           $tottal = $r+1;
           $sheet->cell('C'. $tottal , $val['vpostal']);
           $sheet->cell('D'. $tottal , $val['vnri_male_postal']);
           $sheet->cell('E'. $tottal , $val['vnri_female_postal']);
           $sheet->cell('F'. $tottal , ($val['vnri_other_postal']));
           $sheet->cell('G'. $tottal , $val['totalProxyvote']);
          }
          $r= $tottal;

         if(!empty($val['vtotaltitle'] == '5. Total')){
           $tottal = $r+1;
           $sheet->cell('C'. $tottal , $val['vtotaltitle']);
           $sheet->cell('D'. $tottal , $val['vnmvoters']);
           $sheet->cell('E'. $tottal , $val['vnfvoters']);
           $sheet->cell('F'. $tottal , ($val['vngvoters']));
           $sheet->cell('G'. $tottal , $val['vtotal']);
          }
          $r= $tottal;

         if(!empty($value['fourVotes'] == 'IV Votes')){
           $tottal = $r+1;
           $sheet->cell('C'. $tottal , $value['fourVotes']);

          }
          $r= $tottal;

         if(!empty($value['TotalVotesPolledonevm'] == '1. Total Votes Polled On EVM')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['TotalVotesPolledonevm']);
           $sheet->cell('G'. $tottal , $value['TotalVotesPolledonevmd']);
          }
          $r= $tottal;

         if(!empty($value['Totaldeductedvotesevm'] == '2.Total deducted votes from evm(test votes+votes not retrived+votes rejected due to other reasons + "Nota")')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['Totaldeductedvotesevm']);
           $sheet->cell('G'. $tottal , $value['TotalVotesrejected']);
          }
          $r= $tottal;
         if(!empty($value['Totalpllledevm'] == '3.Total valid votes polled on evm')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['Totalpllledevm']);
           $sheet->cell('G'. $tottal , $value['Totalpllledevmd']);
          }
          $r= $tottal;
         if(!empty($value['postalvotecount'] == '3.Total valid votes polled on evm')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['postalvotecount']);
           $sheet->cell('G'. $tottal , $value['Totalpllledevmd']);
          }
          $r= $tottal;

         if(!empty($value['postalvotenota'] == '5. Postal Votes Deducted REJECTED POSTAL VOTES + POSTAL VOTES POLLED FOR NOTA)')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['postalvotenota']);
           $sheet->cell('G'. $tottal , $value['postalvotenotad']);
          }
          $r= $tottal;

         if(!empty($value['valiedpostalvote'] == '6. Valid Postal Votes')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['valiedpostalvote']);
           $sheet->cell('G'. $tottal , $value['valiedpostalvoted']);
          }
          $r= $tottal;

         if(!empty($value['totalvalidpolled'] == '7. Total Valid Votes Polled')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['totalvalidpolled']);
           $sheet->cell('G'. $tottal , $value['totalvalidpolledd']);
          }
          $r= $tottal;
         if(!empty($value['votespolledonevm'] == '8. Test Votes polled On EVM')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['votespolledonevm']);
           $sheet->cell('G'. $tottal , $value['votespolledonevmd']);
          }
          $r= $tottal;

         if(!empty($value['tendredvotes'] == '9. Tendered Votes')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['tendredvotes']);
           $sheet->cell('G'. $tottal , $value['tendredvotesd']);
          }
          $r= $tottal;

         if(!empty($value['pollingstation'] == 'V. Polling Stations')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['pollingstation']);
          }
          $r= $tottal;

         if(!empty($value['number'] == 'Number')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['number']);
           $sheet->cell('F'. $tottal , $value['average']);
           $sheet->cell('G'. $tottal , $value['na']);
          }
          $r= $tottal;

         if(!empty($value['dateofRepollsifany'] == 'Date of Repolls If Any')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['dateofRepollsifany']);
           $sheet->cell('G'. $tottal , $value['dateofroll']);

          }
          $r= $tottal;

         if(!empty($value['numberofpollingstations'] == 'Number Of Polling Stations where Re Polls Was Ordere')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['numberofpollingstations']);
           $sheet->cell('G'. $tottal , $value['numberofpollingstationd']);
          }
          $r= $tottal;

         if(!empty($value['viDates'] == 'VI. Dates')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['viDates']);
          }
          $r= $tottal;


         if(!empty($value['Polling'] == 'Polling')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['Polling']);
           $sheet->cell('D'. $tottal , $value['Counting']);
           $sheet->cell('G'. $tottal , $value['declarationofresults']);
          }
          $r= $tottal;

         if(!empty($value['Counting'] == 'Counting')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['datePolling']);
           $sheet->cell('D'. $tottal , $value['datecount']);
           $sheet->cell('G'. $tottal , $value['resuladeclarationdate']);
          }
          $r= $tottal;

         if(!empty($value['eightResults'] == 'VII. Results')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['eightResults']);
          }
          $r= $tottal;

         if(!empty($value['winner'] == 'Winner')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['winner']);
           $sheet->cell('E'. $tottal , $value['lead_cand_party']);
           $sheet->cell('F'. $tottal , $value['lead_cand_name']);
           $sheet->cell('G'. $tottal , $value['lead_total_vote']);
          }
          $r= $tottal;
         if(!empty($value['runnerup'] == 'Runner Up')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['runnerup']);
           $sheet->cell('E'. $tottal , $value['trail_cand_party']);
           $sheet->cell('F'. $tottal , $value['trail_cand_name']);
           $sheet->cell('G'. $tottal , $value['trail_total_vote']);
          }
          $r= $tottal;
         if(!empty($value['Margin'] == 'Margin')){
           $tottal = $r+1;
           $sheet->cell('A'. $tottal , $value['Margin']);
           $sheet->cell('E'. $tottal , $value['Marginone']);
           $sheet->cell('F'. $tottal , $value['Margintwo']);
           $sheet->cell('G'. $tottal , $value['Margind']);
          }
          $r= $tottal;
  $r++;   }
    }
        });
      })->download('xls');


}


}
