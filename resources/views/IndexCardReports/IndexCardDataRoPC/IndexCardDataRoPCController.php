<?php

namespace App\Http\Controllers\IndexCardReports\IndexCardDataRoPC;

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
		use App;
		use \PDF;
		use MPDF;
		use App\commonModel;
		use App\adminmodel\CandidateModel;
		use App\adminmodel\PartyMaster;
		use App\adminmodel\CandidateNomination;
		use App\Helpers\SmsgatewayHelper;
		use App\adminmodel\ROPCModel;
		use App\adminmodel\ROPCReportModel;
		use App\Classes\xssClean;
		use App\adminmodel\SymbolMaster;

class IndexCardDataRoPCController extends Controller
{
    public function __construct(Request $request){
        $this->middleware('adminsession');
			$this->middleware(['auth:admin','auth']);
			$this->middleware('ro');
			$this->commonModel = new commonModel();
			$this->CandidateModel = new CandidateModel();
			$this->romodel = new ROPCModel();
			$this->ropcreportmodel = new ROPCReportModel();
			$this->xssClean = new xssClean;
			$this->sym = new SymbolMaster();
    }
   
    public function getindexcarddata(Request $request){
        
    	$session = $request->session()->all();
      //dd($session);
        $user = Auth::user();
		
		
		
            $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
		    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');

$check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		    if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
           	$cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
           }

			$seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);

           /* $sched=''; $search='';
           $status=$this->commonModel->allstatus();
           if(isset($ele_details)) {  $i=0;
             foreach($ele_details as $ed) {
				 
				 //echo '<pre>'; print_r($ed); die;
				 
               // $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               // $const_type=$ed->CONST_TYPE;
              }
           }
		   
		   $sched=$ele_details->ScheduleID;
		   $const_type=$ele_details->CONST_TYPE;
		   
           $session['election_detail'] = (array)$ele_details; */
           $session['election_detail']['st_code'] = $user->st_code;
           $session['election_detail']['st_name'] = $user->placename;
    	// echo "<pre>"; print_r($session); die;
    	$st_code = $user->st_code;
    	$pc = $user->pc_no;
    	$election_detail = $session['election_detail'];
        $user_data = $d;
    	$getIndexCardDataCandidatesVotesACWise = $this->getIndexCardDataCandidatesVotesACWise($user->st_code, $user->pc_no);
    	 



    	$getIndexCardDataPCWise = $this->getIndexCardDataPCWise($user->st_code, $user->pc_no, $ele_details->ScheduleID);
    	
	//	echo "<pre>"; print_r($getIndexCardDataPCWise); die;
		
		
    	$getelectorsacwise = $this->getelectorsacwise($user->st_code, $user->pc_no);
		
		
if($request->path() == 'ropc/indexcardpcpdf'){
$pdf=PDF::loadView
('IndexCardReports.IndexCardDataPCWise.indexcardreportpcpdf',compact('getIndexCardDataCandidatesVotesACWise','session','getIndexCardDataPCWise','st_code','pc','getelectorsacwise','user_data', 'sched','ele_details','cand_finalize_ro'));
return $pdf->download('IndexCardReport.pdf');
}else{
	return view('IndexCardReports/IndexCardDataPCWise/indexcardreportpc',compact('getIndexCardDataCandidatesVotesACWise','session','getIndexCardDataPCWise','st_code','pc','getelectorsacwise','user_data', 'sched','ele_details','cand_finalize_ro'));
}
		
		
	
    }



   public function getindexcardbriefed(Request $request){
        
    	$session = $request->session()->all();
      //dd($session);
        $user = Auth::user();
		
		//echo '<pre>'; print_r($user); die;
		
            $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
		    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');

$check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		    if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
           	$cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
           }

			$seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);

          
           $session['election_detail']['st_code'] = $user->st_code;
           $session['election_detail']['st_name'] = $user->placename;
    	// echo "<pre>"; print_r($session); die;
    	$st_code = $user->st_code;
    	$pc = $user->pc_no;
    	$election_detail = $session['election_detail'];
        $user_data = $d;
    	$getIndexCardDataCandidatesVotesACWise = $this->getIndexCardDataCandidatesVotesACWise($user->st_code, $user->pc_no);
    	 
//echo "<pre>"; print_r($getIndexCardDataCandidatesVotesACWise); die;


    	$getIndexCardDataPCWise = $this->getIndexCardDataPCWise($user->st_code, $user->pc_no, $ele_details->ScheduleID);
    	
	//	echo "<pre>"; print_r($getIndexCardDataPCWise); die;
		
		
    	//$getelectorsacwise = $this->getelectorsacwise($user->st_code, $user->pc_no);
		
		
if($request->path() == 'ropc/indexcardbriefedpdf'){
$pdf=PDF::loadView
('IndexCardReports.IndexCardDataPCWise.indexcardbriefedreportpdf',compact('getIndexCardDataCandidatesVotesACWise','session','getIndexCardDataPCWise','st_code','pc','user_data', 'sched','ele_details','cand_finalize_ro'));
return $pdf->download('IndexCardBriefedReport.pdf');
}else{
	return view('IndexCardReports/IndexCardDataPCWise/indexcardbriefedreport',compact('getIndexCardDataCandidatesVotesACWise','session','getIndexCardDataPCWise','st_code','pc','user_data', 'sched','ele_details','cand_finalize_ro'));
}
		
		
	
    }




    /***************************By Praveen***********************************/
    public function getelectorsacwise($st_code,$pc){
        //dd($pc);
        $data_pc_wise = array();
        $data_pc_wise_new = array();
        
        $ac_no = DB::table('m_ac')->select('m_ac.ac_no')
                ->where(['m_ac.st_code' => $st_code, 'm_ac.pc_no' =>$pc])
                ->get();
				
        $data = DB::table('m_ac AS mac')
                   ->select('*','ed.electors_male','ed.electors_female','ed.electors_other','ed.electors_service','ed.electors_total','ed.gen_electors_male','ed.gen_electors_female','ed.gen_electors_other','ed.nri_male_electors','ed.nri_female_electors','ed.nri_third_electors','ed.service_male_electors','ed.service_female_electors','ed.service_third_electors'
                     )

                   ->leftJoin('electors_cdac AS ed',function($query){
                          $query->on('mac.AC_NO','ed.ac_no')
                                  ->on('mac.ST_CODE','ed.st_code')
                                  ->on('mac.PC_NO','ed.pc_no');
                      })
                   ->where('mac.st_code', $st_code)
				   ->where('mac.PC_NO', $pc)
                   ->where('ed.year', 2019)
                     // ->where('ed.scheduledid', 1)

                   ->get()->toArray();
					
      
        foreach ($data as $key) {
                   $data_pc_wise[$key->ac_no] = array(
                   'st_code'    => $st_code,
                   'pc_no'      => $pc,
                   'ac_no'      => $key->ac_no,
                   'ac_name'    => $key->ac_name,
                   'gen_m'      => $key->gen_electors_male,
                   'gen_f'      => $key->gen_electors_female,
                   'gen_o'      => $key->gen_electors_other,

                   'gen_t'      => $key->gen_electors_male + $key->gen_electors_female + $key->gen_electors_other,

                   'ser_m'      => $key->service_male_electors,
                   'ser_f'      => $key->service_female_electors,
                   'ser_o'      => $key->service_third_electors,

                   'ser_t'      => $key->service_male_electors + $key->service_female_electors + $key->service_third_electors,

                   'tot_m'      => $key->gen_electors_male+$key->service_male_electors+$key->nri_male_electors,
                   'tot_f'      => $key->gen_electors_female+$key->service_female_electors+$key->nri_female_electors,
                   'tot_o'      => $key->gen_electors_other+$key->service_third_electors+$key->nri_third_electors,


                   'tot_all'    => $key->gen_electors_male+$key->service_male_electors+$key->nri_male_electors + $key->gen_electors_female+$key->service_female_electors+$key->nri_female_electors + $key->gen_electors_other+$key->service_third_electors+$key->nri_third_electors,

                   'nri_m'      => $key->nri_male_electors,
                   'nri_f'      => $key->nri_female_electors,
                   'nri_o'      => $key->nri_third_electors,
                   'nri_t'      => $key->nri_male_electors +  $key->nri_female_electors + $key->nri_third_electors
               );
               }

                return $data_pc_wise;
 
    }
    

public function getIndexCardDataCandidatesVotesACWise($st_code, $pc){

    	$sWhere = array(
    		'mac.PC_NO' 			=> $pc,
    		'mac.ST_CODE' 			=> $st_code
    	);


    	$responseFromIC = DB::table("counting_master_".strtolower($st_code)." AS  master")
				->select('mac.PC_NO as pc_no','B.candidate_id','B.new_srno','A.cand_name','A.cand_gender','A.cand_age','A.cand_category','C.PARTYNAME','C.PARTYABBRE','D.SYMBOL_DES','CP.migrate_votes','CP.postal_vote as postal_vote_count','CP.evm_vote as total_valid_vote','mac.AC_NO','master.total_vote as vote_count','mac.AC_NAME')
                ->join('m_ac as mac', function($query) {
                  $query->on('mac.PC_NO','master.pc_no')
                        ->on('mac.AC_NO','master.ac_no');
                       })
                   ->join('candidate_personal_detail AS A', 'A.candidate_id', 'master.candidate_id')
                   ->join('counting_pcmaster AS CP','CP.candidate_id','A.candidate_id')
                    ->join('candidate_nomination_detail AS B','A.candidate_id','B.candidate_id')
                    ->join('m_party AS C','B.party_id','C.CCODE')
                    ->join('m_symbol AS D','B.symbol_id','D.symbol_no')
    						->where($sWhere)
    						->where('B.application_status', '6')
							->where('B.finalaccepted', '1')
							->where('master.candidate_id','!=','4319')
    						->orderBy('mac.AC_NO','asc')
							->orderBy('B.new_srno','asc')
    						->get()->toArray();
    //  $queue = DB::getQueryLog();
 

          $candidatedataarray = array();
          $aclistforpc = array();
          $actotalvote = $pctotalvotes = $totalvalidpostal_votes = 0;

      foreach ($responseFromIC as $dataArraycandidatewise) {
          //echo "<pre>"; print_r($dataArraycandidatewise); die;
          $actotalvote += 0;
          $pctotalvotes += 0;
          $totalvalidpostal_votes += 0;

            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['new_srno'] = $dataArraycandidatewise->new_srno;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['cand_name'] = $dataArraycandidatewise->cand_name;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['cand_gender'] = $dataArraycandidatewise->cand_gender;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['cand_age'] = $dataArraycandidatewise->cand_age;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['cand_category'] = $dataArraycandidatewise->cand_category;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['partyname'] = $dataArraycandidatewise->PARTYNAME;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['PARTYABBRE'] = $dataArraycandidatewise->PARTYABBRE;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['party_symbol'] = $dataArraycandidatewise->SYMBOL_DES;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['valid_postal_votes'] = $dataArraycandidatewise->postal_vote_count;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['migrate_votes'] = $dataArraycandidatewise->migrate_votes;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['total_valid_vote'] = $dataArraycandidatewise->total_valid_vote;
			
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['total_valid_votes'] = $dataArraycandidatewise->total_valid_vote + $dataArraycandidatewise->postal_vote_count + $dataArraycandidatewise->migrate_votes;
 
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['acdata'][$dataArraycandidatewise->AC_NO]['vote_count'] = $dataArraycandidatewise->vote_count;

            $aclistforpc[$dataArraycandidatewise->AC_NO] = $dataArraycandidatewise->AC_NAME;

      }

//echo "<pre>"; print_r($candidatedataarray); die;


	  $cnt=0;
	  foreach ($candidatedataarray as $key => $row)
		{
			foreach ($row as $key1 => $row1){
				$cnt++;
				$pc_array_name[$cnt][$key1] = $row1['total_valid_votes'];
			}
		}
	  array_multisort($pc_array_name, SORT_DESC, $candidatedataarray);

    	//echo "<pre>"; print_r($candidatedataarray); die;



    	return $data = [
    		'candidatedataarray' 	=> $candidatedataarray,
    		'allACList'				=> $aclistforpc
    	];
    }
	
	
        public function getIndexCardDataPCWise($st_code, $pc, $election_detail){



	   		$st_code = $st_code;
        $pc = $pc;


		// $st_code = 'U07';
       //  $pc = 1;

        $dataarraypc = array(
            'st_code' => $st_code,
             'pc' => $pc,
            // 'ScheduleID'=> $pcrow->ScheduleID,
        );



//dd($dataarraypc);
        //$election_detail['ScheduleID'] = $pcrow->ScheduleID;


        $fWhere = array(
                        'st_code'   => $st_code,
                        'pc_no'     => $pc,
                        'ac_no'     => null
                    );

        DB::enableQueryLog();



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
                                'ovi.is_by_or_countermanded_election AS is_by_or_countermanded_election',
                                'ovi.reasons_for_by_or_countermanded_election AS reasons_for_by_or_countermanded_election',
                                'ovi.finalize_by_ceo AS finalize_by_ceo',
                                'ovi.finalize AS finalize_by_ro',
                                'ovi.finalize_by_eci AS finalize_by_eci',
                                'ovi.date_of_finalize_by_ro AS finalize_by_ro_date',
                                'ovi.date_of_finalize_by_ceo AS finalize_by_ceo_date',

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

                       //echo "<pre>"; print_r($electorData); die;

                          $queue = DB::getQueryLog();



                          // echo "<pre>"; print_r($queue); die;


                  /*  $voterdata = DB::table('pd_scheduledetail')
                                ->select(
                                    DB::raw("sum(total_male) AS male_voter"),
                                    DB::raw("sum(total_female) AS female_voter"),
                                    DB::raw("sum(total_other) AS other_voter"),
                                    DB::raw("sum(total) AS totel_voter")
                                )

                                ->where(array(
                                 'pd_scheduledetail.st_code' => $st_code,
                                 'pd_scheduledetail.pc_no'   => $pc


                                ))



                        ->first(); */



                                           $dataarraypc['e_gen_m']  = @$electorData->gen_m;

                                           $dataarraypc['e_nri_m']  = @$electorData->nri_m;
                                           $dataarraypc['e_ser_m']  = @$electorData->ser_m;
                                           $dataarraypc['e_all_t_m'] =@$electorData->gen_m+ @$electorData->nri_m+ @$electorData->ser_m;
                                           $dataarraypc['e_gen_f'] = @$electorData->gen_f;
                                           $dataarraypc['e_nri_f'] = @$electorData->nri_f;
                                           $dataarraypc['e_ser_f'] = @$electorData->ser_f;
                                           $dataarraypc['e_all_t_f'] =@$electorData->gen_f+ @$electorData->nri_f+@ $electorData->ser_f;
                                           $dataarraypc['e_gen_o'] = @$electorData->gen_o;
                                           $dataarraypc['e_nri_o'] = @$electorData->nri_o;
                                           $dataarraypc['e_ser_o'] = @$electorData->ser_o;
                                            $dataarraypc['e_all_t_o'] =@$electorData->gen_o+ @$electorData->nri_o;
                                           $dataarraypc['e_gen_t'] = @$electorData->gen_m+@$electorData->gen_f+@$electorData->gen_o;
                                           $dataarraypc['e_ser_t'] = @$electorData->ser_t;
                                           $dataarraypc['e_nri_t'] =  @$electorData->nri_m+ @$electorData->nri_f+@$electorData->nri_o;

										   $dataarraypc['total_t']  = $dataarraypc['e_gen_t'] + $dataarraypc['e_ser_t'] + $dataarraypc['e_nri_t'];
                                          $dataarraypc['total_t_ws']  = $dataarraypc['e_gen_t'] +  $dataarraypc['e_nri_t']  + $dataarraypc['e_ser_t'];
                                           $dataarraypc['proxy_votes'] =@$electorData->proxy_votes;
                                           $dataarraypc['test_votes_49_ma'] =@$electorData->test_votes_49_ma;
                                           //voter

                                           $dataarraypc[ 'voter_male'] =@$electorData->male_voter ? :0;
                                           $dataarraypc[ 'voter_female'] =@$electorData->female_voter ? :0;
                                           $dataarraypc[ 'voter_other'] =@$electorData->other_voter ? :0;
                                           $dataarraypc[ 'voters_service'] =@$electorData->voters_service;
                                           
                                           $dataarraypc[ 'nri_male_votes'] =@$electorData->nri_male_voters ? :0;
                                           $dataarraypc[ 'nri_female_votes'] =@$electorData->nri_female_voters ? :0;
                                           $dataarraypc[ 'nri_third_votes'] =@$electorData->nri_other_voters ? :0;
										   
										   $dataarraypc[ 'voter_total'] =@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter + @$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters;
										   
										   
										   
                                           $dataarraypc[ 'service_postal_votes'] =@$electorData->service_postal_votes_under_section_8 ? :0;
                                           $dataarraypc[ 'votes_not_retrieved_on_evm'] =@$electorData->votes_not_retreived_from_evm ? :0;
                                           $dataarraypc[ 'govt_servent_postal_votes'] =@$electorData->service_postal_votes_gov ? :0;
                                           $dataarraypc[ 'rejected_votes_evm'] =@$electorData->rejected_votes_due_2_other_reason ? :0;

                                           $dataarraypc[ 'date_of_repoll'] =@$electorData->date_of_repoll;
                                           $dataarraypc[ 'no_poll_station_where_repoll'] =@$electorData->no_poll_station_where_repoll;
                                           $dataarraypc[ 'is_by_countermanded_election'] =@$electorData->is_by_countermanded_election;
                                           $dataarraypc[ 'reasons_thereof'] =@$electorData->reasons_thereof;
                                           $dataarraypc[ 'total_polling_station_s_i_t_c'] =@$electorData->total_polling_station_s_i_t_c ? :0;





         /////////////////////////dataarray/////////////////////////////////////



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




        //$evmvotesfromcp = json_decode(json_encode($evmvotesfromcp));
       // echo "<pre>"; print_r($evmvotesfromcp); die;

        $dataarraypc = array_merge($dataarraypc,array(

                                //'v_votes_evm_all'=> @$evmvotesfromcp->evm_votes,
                                'v_votes_evm_all'=> $dataarraypc[ 'voter_total'],
                                'postal_valid_votes'=> @$evmvotesfromcp->postal_votes,
                                'total_valid_votes'=> @$evmvotesfromcp->total_votes,
                                'r_votes_evm'=> 0,
                                'r_votes_postal'=> @$evmvotesfromcp->rej_votes_postal,
                                'tendered_votes'=> @$evmvotesfromcp->tended_votes,

                                ));



    // echo "<pre>"; print_r($dataarraypc); die;

        DB::enableQueryLog();

		/* $indexCardDatas1 = DB::select("SELECT
		SUM(male) as male, SUM(female) as female,SUM(third) as third
		FROM (SELECT
		IF (B.cand_gender = 'male' ,COUNT(DISTINCT A.candidate_id),'')  AS male,
		IF (B.cand_gender = 'female' ,COUNT(DISTINCT A.candidate_id),'')  AS female,
		IF (B.cand_gender = 'third' ,COUNT(DISTINCT A.candidate_id),'')  AS third
		FROM `candidate_nomination_detail` AS `A` INNER JOIN `candidate_personal_detail` AS `B` ON `A`.`candidate_id` = `B`.`candidate_id`  WHERE `A`.`st_code` = '$st_code' AND `A`.`pc_no` = $pc AND `A`.`candidate_id` NOT IN (4319) GROUP BY  A.candidate_id)X limit 1"); */


        /* $indexCardDatas = DB::select("select (P.wdmale+P.rejmale+P.acpmale) as male , (P.wdfemale+P.rejfemale+P.acpfemale) as female, (P.wdthird+P.rejthird+P.acpthird) as third, P.* from (select         SUM(CASE WHEN A.application_status = '5' AND B.cand_gender = 'male' THEN 1 ELSE 0 END) AS wdmale,         SUM(CASE WHEN A.application_status = '5' AND B.cand_gender = 'female' THEN 1 ELSE 0 END) AS wdfemale,         SUM(CASE WHEN A.application_status = '5' AND B.cand_gender = 'third' THEN 1 ELSE 0 END) AS wdthird,         SUM(CASE WHEN A.application_status = '4' AND B.cand_gender = 'male' THEN 1 ELSE 0 END) AS rejmale,         SUM(CASE WHEN A.application_status = '4' AND B.cand_gender = 'female' THEN 1 ELSE 0 END) AS rejfemale,         SUM(CASE WHEN A.application_status = '4' AND B.cand_gender = 'third' THEN 1 ELSE 0 END) AS rejthird ,         SUM(CASE WHEN A.application_status = '6' and A.finalaccepted='1' AND B.cand_gender = 'male' THEN 1 ELSE 0 END) AS acpmale,         SUM(CASE WHEN A.application_status = '6' and A.finalaccepted='1' AND B.cand_gender = 'female' THEN 1 ELSE 0 END) AS acpfemale, SUM(CASE WHEN A.application_status = '6' and A.finalaccepted='1' AND B.cand_gender = 'third' THEN 1 ELSE 0 END)  AS acpthird from (select Y.candidate_id,Y.finalaccepted,Y.application_status from candidate_nomination_detail Y INNER JOIN( select candidate_id ,max(application_status) as application_status from candidate_nomination_detail  where `st_code` = '$st_code' AND `pc_no` = '$pc' and application_status<> '11'  and `candidate_id` NOT IN (4319) group by candidate_id) X on X.candidate_id=Y.candidate_id and X.application_status=Y.application_status group by Y.candidate_id,Y.finalaccepted,Y.application_status) A INNER JOIN candidate_personal_detail B on  B.candidate_id=A.candidate_id) P"); */


			$indexCardDatas = App\models\Admin\CandidateModel::get_count_nominated($st_code,$pc);

//echo '<pre>'; print_r($dataaa); die;



                $datanotapcwise = DB::table('counting_pcmaster')
                                ->select('postal_vote','evm_vote','migrate_votes')
                                ->where(array(
                                        'st_code' => $st_code,
                                        'pc_no'    => $pc,
                                        'candidate_id'=>4319
                                 ))
                                 ->first();


            $dataarraypc = array_merge($dataarraypc,array(

                                'c_nom_m_t'=> $indexCardDatas['nom_male'],
                                'c_nom_f_t'=> $indexCardDatas['nom_female'],
                                'c_nom_o_t'=> $indexCardDatas['nom_third'],
                                'c_nom_all_t'=> $indexCardDatas['nom_male'] + $indexCardDatas['nom_female'] + $indexCardDatas['nom_third'],

                                'c_wd_m_t'=> $indexCardDatas['with_male'],
                                'c_wd_f_t'=> $indexCardDatas['with_female'],
                                'c_wd_o_t'=> $indexCardDatas['with_third'],

                                 'c_rej_m_t'=> $indexCardDatas['rej_male'],
                                'c_rej_f_t'=> $indexCardDatas['rej_female'],
                                'c_rej_o_t'=> $indexCardDatas['rej_third'],

                                 'c_acp_m_t'=> $indexCardDatas['cont_male'],
                                'c_acp_f_t'=> $indexCardDatas['cont_female'],
                                'c_acp_o_t'=> $indexCardDatas['cont_third'],



                                ));









   //echo "<pre>"; print_r($dataarraypc); die;
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
            AND cp.candidate_id != 4319 AND cp.st_code = '$st_code' and cp.pc_no = $pc");


		//	echo '<pre>'; print_r($indexCardDatasDf); die;



            $dataarraypc = array_merge($dataarraypc,array(

                                'c_fd_m_t'=> $indexCardDatasDf[0]->fdmale,
                                'c_fd_f_t'=> $indexCardDatasDf[0]->fdfemale,
                                'c_fd_o_t'=> $indexCardDatasDf[0]->fdthird,
                                'c_fd_t'=> $indexCardDatasDf[0]->fd,


                                ));

            $pollDateInfoPcwise = DB::table('m_schedule as ms')
                                ->select('ms.DATE_POLL','ms.DATE_COUNT','wlc.result_declared_date')
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

		   if($dataarraypc['total_polling_station_s_i_t_c'] > 0){
			   $avg = round(($dataarraypc['total_t_ws'])/$dataarraypc['total_polling_station_s_i_t_c']);
		   }else{
			  $avg = 0;
		   }


  $data=array(

             'st_code'                        =>  $dataarraypc['st_code'],
             'pc_no'                          => $dataarraypc['pc'],
            // 'schedule_id'                    => $dataarraypc['ScheduleID'],
             'e_nri_m'                        => $dataarraypc['e_nri_m'],
             'e_nri_f'                        => $dataarraypc['e_nri_f'],
             'e_nri_o'                        => $dataarraypc['e_nri_o'],
             'e_nri_t'                        => $dataarraypc['e_nri_m'] + $dataarraypc['e_nri_f'] + $dataarraypc['e_nri_o'],
             'e_gen_m'                        => $dataarraypc['e_gen_m'],
             'e_gen_f'                        => $dataarraypc['e_gen_f'],
             'e_gen_o'                        => $dataarraypc['e_gen_o'],
             'e_gen_t'                        => $dataarraypc['e_gen_m'] +  $dataarraypc['e_gen_f'] + $dataarraypc['e_gen_o'],
             'e_ser_m'                        => $dataarraypc['e_ser_m'],
             'e_ser_f'                        => $dataarraypc['e_ser_f'],
              "e_ser_o"                        => $dataarraypc['e_ser_o'],
              'e_ser_t'                        => $dataarraypc['e_ser_t'],
             'e_all_t_m'                     =>   $dataarraypc['e_nri_m'] + $dataarraypc['e_gen_m'] + $dataarraypc['e_ser_m'],
             'e_all_t_f'                     =>   $dataarraypc['e_nri_f'] + $dataarraypc['e_gen_f'] + $dataarraypc['e_ser_f'],
             'e_all_t_o'                     =>  $dataarraypc['e_nri_o'] + $dataarraypc['e_gen_o'],
              "e_all_t"                      =>  $dataarraypc['total_t'],

             'proxy_votes'                   => $dataarraypc['proxy_votes'],
             'tendered_votes'                 => $dataarraypc['tendered_votes'],

           "total_no_polling_station"         => $dataarraypc['total_polling_station_s_i_t_c'],

            "avg_elec_polling_stn"             => $avg,
               'dt_poll'                         => @$pollDateInfoPcwise->DATE_POLL,
               "dt_counting"                     => @$pollDateInfoPcwise->DATE_COUNT,
               "dt_declare"                     =>  @$pollDateInfoPcwise->result_declared_date,
           //  "flag_bye_counter"                 => ($flag_bye_counter)?$flag_bye_counter:0,
           //  "flag_bye_counter_reason"         => ($flag_bye_counter_reason)?$flag_bye_counter_reason:0,

    "c_nom_m_t"                     =>$dataarraypc['c_nom_m_t'],
   "c_nom_f_t"                     => $dataarraypc['c_nom_f_t'],
    "c_nom_o_t"                     =>$dataarraypc['c_nom_o_t'],
"c_nom_a_t"                     =>$dataarraypc['c_nom_all_t'],

      "c_nom_w_m"                     =>$dataarraypc['c_wd_m_t'],
    "c_nom_w_f"                     =>$dataarraypc['c_wd_f_t'],
    "c_nom_w_o"                     =>$dataarraypc['c_wd_o_t'],
    "c_nom_w_t"                     =>$dataarraypc['c_wd_m_t']  + $dataarraypc['c_wd_f_t'] +$dataarraypc['c_wd_o_t'],

    "c_nom_r_m"                     =>$dataarraypc['c_rej_m_t'],
    "c_nom_r_f"                     =>$dataarraypc['c_rej_f_t'],
    "c_nom_r_o"                     =>$dataarraypc['c_rej_o_t'],
    "c_nom_r_a"                     =>$dataarraypc['c_rej_m_t'] + $dataarraypc['c_rej_f_t'] + $dataarraypc['c_rej_o_t'],

    "c_nom_co_m"                     =>$dataarraypc['c_acp_m_t'],
    "c_nom_co_f"                     =>$dataarraypc['c_acp_f_t'],
    "c_nom_co_o"                     =>$dataarraypc['c_acp_o_t'],
     'c_nom_co_t'                      =>$dataarraypc['c_acp_m_t'] + $dataarraypc['c_acp_f_t'] +$dataarraypc['c_acp_o_t'],


    "c_nom_fd_m"                     =>$dataarraypc['c_fd_m_t'],
    "c_nom_fd_f"                     =>$dataarraypc['c_fd_f_t'],
    "c_nom_fd_o"                     =>$dataarraypc['c_fd_o_t'],
    "c_nom_fd_t"                     =>$dataarraypc['c_fd_t'],


    "vt_gen_m"                     =>$dataarraypc['voter_male'],
    "vt_gen_f"                     =>$dataarraypc['voter_female'],
    "vt_gen_o"                     =>$dataarraypc['voter_other'],
    "vt_gen_t"                     =>$dataarraypc['voter_male'] + $dataarraypc['voter_female'] +$dataarraypc['voter_other'],

    //                           =>$dataarraypc['voter_total'],
    "vt_nri_m"                     =>$dataarraypc['nri_male_votes'],
    "vt_nri_f"                     =>$dataarraypc['nri_female_votes'],
    "vt_nri_o"                     =>$dataarraypc['nri_third_votes'],
    "vt_nri_t"                     =>$dataarraypc['nri_male_votes'] + $dataarraypc['nri_female_votes'] + $dataarraypc['nri_third_votes'],

    "vt_m_t"                     =>$dataarraypc['voter_male']+$dataarraypc['nri_male_votes'],
    "vt_f_t"                     =>$dataarraypc['voter_female']+$dataarraypc['nri_female_votes'],
    "vt_o_t"                     =>$dataarraypc['voter_other']+$dataarraypc['nri_third_votes'],
    "vt_all_t"                     =>$dataarraypc['voter_male']+$dataarraypc['nri_male_votes']+$dataarraypc['voter_female']+$dataarraypc['nri_female_votes']+$dataarraypc['voter_other']+$dataarraypc['nri_third_votes'],



    "t_votes_evm"               => $dataarraypc['v_votes_evm_all'],
    "mock_poll_evm"       =>   $dataarraypc['test_votes_49_ma'],
    "not_retrieved_vote_evm"                     =>$dataarraypc['votes_not_retrieved_on_evm'],
    "r_votes_evm"                     => $dataarraypc[ 'rejected_votes_evm'],
    "nota_vote_evm"       =>   @$datanotapcwise->evm_vote,
    "v_r_evm_all"       =>      $dataarraypc['test_votes_49_ma'] + $dataarraypc['votes_not_retrieved_on_evm'] + $dataarraypc[ 'rejected_votes_evm'] +@$datanotapcwise->evm_vote+@$datanotapcwise->migrate_votes,
     "v_votes_evm_all"                     => $dataarraypc['v_votes_evm_all'] - ($dataarraypc['test_votes_49_ma'] + $dataarraypc['votes_not_retrieved_on_evm'] + $dataarraypc[ 'rejected_votes_evm'] +@$datanotapcwise->evm_vote +@$datanotapcwise->migrate_votes),

    "postal_vote_ser_u"                     =>$dataarraypc['service_postal_votes'],
    "postal_vote_ser_o"                     =>$dataarraypc['govt_servent_postal_votes'],
    "postal_vote_rejected"                     =>$dataarraypc['r_votes_postal'],
    "postal_vote_nota"       			=>   @$datanotapcwise->postal_vote,
    "migrate_vote_nota"       			=>   @$datanotapcwise->migrate_votes,
    "postal_vote_r_nota"  =>    @$datanotapcwise->postal_vote+@$dataarraypc['r_votes_postal'],
    "postal_valid_votes"                     =>$dataarraypc['service_postal_votes']+$dataarraypc['govt_servent_postal_votes']-(@$datanotapcwise->postal_vote+@$dataarraypc['r_votes_postal']),

    "total_votes_polled"                       =>$dataarraypc['v_votes_evm_all']+ $dataarraypc['service_postal_votes']+$dataarraypc['govt_servent_postal_votes'],
   // "v_r_votes_evm"                     => $dataarraypc['test_votes_49_ma'] + $dataarraypc['votes_not_retrieved_on_evm'] + $dataarraypc['r_votes_evm'] +  @$datanotapcwise->evm_vote,
"total_not_count_votes"       =>  $dataarraypc['test_votes_49_ma'] + $dataarraypc['votes_not_retrieved_on_evm'] + $dataarraypc[ 'rejected_votes_evm'] +@$datanotapcwise->evm_vote + @$datanotapcwise->postal_vote + @$datanotapcwise->migrate_votes +@$evmvotesfromcp->rej_votes_postal ,

    "total_valid_votes"                     => $dataarraypc['v_votes_evm_all'] - ($dataarraypc['test_votes_49_ma'] + $dataarraypc['votes_not_retrieved_on_evm'] + $dataarraypc[ 'rejected_votes_evm'] +@$datanotapcwise->evm_vote + @$datanotapcwise->migrate_votes) + $dataarraypc['service_postal_votes']+$dataarraypc['govt_servent_postal_votes']-(@$datanotapcwise->postal_vote+@$evmvotesfromcp->rej_votes_postal),
     "total_votes_nota"       =>    @$datanotapcwise->evm_vote + @$datanotapcwise->migrate_votes + @$datanotapcwise->postal_vote,

     "dt_repoll"               =>   @$electorData->date_of_repoll,
     "re_poll_station"         =>   @$electorData->no_poll_station_where_repoll,
     "flag_bye_counter"        =>   @$electorData->is_by_or_countermanded_election ? : 0,
     "flag_bye_counter_reason" =>   @$electorData->reasons_for_by_or_countermanded_election,
	 "finalize_by_ceo" 		   =>   @$electorData->finalize_by_ceo ? : 0,
     "finalize_by_ro" 		   =>   @$electorData->finalize_by_ro ? : 0,
     "finalize_by_eci" 		   =>   @$electorData->finalize_by_eci ? : 0,
     "finalize_by_ro_date" 	   =>   @$electorData->finalize_by_ro_date,
     "finalize_by_ceo_date" 	   =>   @$electorData->finalize_by_ceo_date

 );






	   $indexCardData[0] = (object)array(
							'status'=> 'nominated',
							'male'=> $indexCardDatas['nom_male'],
							'female'=> $indexCardDatas['nom_female'],
							'third'=> $indexCardDatas['nom_third'],
							'total'=> $indexCardDatas['nom_male'] + $indexCardDatas['nom_female'] + $indexCardDatas['nom_third']
							);


		$indexCardData[1] = (object)array(
								'status'=> 'rejected',
                                'male'=> $indexCardDatas['rej_male'],
                                'female'=> $indexCardDatas['rej_female'],
                                'third'=> $indexCardDatas['rej_third'],
                                'total'=> $indexCardDatas['rej_male'] + $indexCardDatas['rej_female'] + $indexCardDatas['rej_third'],
							);

		$indexCardData[2] = (object)array(
								'status'=> 'withdrawn',
                                'male'=> $indexCardDatas['with_male'],
                                'female'=> $indexCardDatas['with_female'],
                                'third'=> $indexCardDatas['with_third'],
                                'total'=> $indexCardDatas['with_male'] + $indexCardDatas['with_female'] + $indexCardDatas['with_third'],
							);

		$indexCardData[3] = (object)array(
								'status'=> 'accepted',
                                'male'=> $indexCardDatas['cont_male'],
                                'female'=> $indexCardDatas['cont_female'],
                                'third'=> $indexCardDatas['cont_third'],
                                'total'=> $indexCardDatas['cont_male'] + $indexCardDatas['cont_female'] + $indexCardDatas['cont_third'],
                            );


		$indexCardData[4] = (object)array(
								'status'=> 'forfieted',
                                'male'=> $indexCardDatasDf[0]->fdmale,
                                'female'=> $indexCardDatasDf[0]->fdfemale,
                                'third'=> $indexCardDatasDf[0]->fdthird,
                                'total'=> $indexCardDatasDf[0]->fd,
                                );




        #distict_name
         $distictData = DB::select("SELECT GROUP_CONCAT(DISTINCT DIST_NAME) as distict_name FROM `m_district` join m_ac on m_ac.DIST_NO_HDQTR = m_district.DIST_NO and m_ac.ST_CODE = m_district.ST_CODE where 
m_ac.ST_CODE = '$st_code' and m_ac.PC_NO = $pc ORDER BY m_district.DIST_NAME ASC");

		//echo '<pre>'; print_r($distictData); die;

        #PC_TYPE
        $pcType = DB::table('m_pc')
                    ->select([
                        'PC_NO','PC_NAME','PC_TYPE'
                    ])
                    ->where(array(
                        'ST_CODE' => $st_code,
                        'PC_NO'   => $pc
                    ))
                    ->first();



         // echo "<pre>"; print_r($election_detail); die;


        #Schedule ID
        // $electionSession = $request->session()->all();

        $ScheduleID = DB::table('m_schedule')
                    ->select([
                        'DATE_POLL',
                        'DATE_COUNT',
                        'DT_PRESS_ANNC'
                    ])
					->join('m_election_details','m_schedule.SCHEDULEID','m_election_details.ScheduleID' )
                    ->where(array(
                        'm_election_details.ST_CODE' => $st_code,
                        'm_election_details.CONST_NO'   => $pc,
                        'm_election_details.CONST_TYPE'   => 'PC',
                        'm_election_details.YEAR'   => '2019'
                    ))
                    ->first();
        // echo "<pre>"; print_r($election_detail['ScheduleID']); die;

        $migrate_notadb = array();
		$migrate_nota = array();
		
		if($st_code == 'S09'){
			$migrate_notadb = DB::select("SELECT ac_no, total_vote FROM `counting_master_s09` where pc_no = $pc and candidate_id = '4319' ORDER BY ac_no ASC");
			
			foreach($migrate_notadb as $datam){
			$migrate_nota[$datam->ac_no] = array(
				'total_vote' =>$datam->total_vote
				);
			}
			
		}
				
		//echo '<pre>'; print_r($migrate_nota); die;
		
        return $data = [
            'indexCardData'         =>    json_decode(json_encode($indexCardData)),
            'distict_name'          =>    $distictData[0]->distict_name,
            'pcType' 				=>    $pcType,
            't_pc_ic'               =>    (object)$data,
            'migrate_nota'          =>    $migrate_nota,
			
        ];
    }

    function callAPI($method, $url, $data)
    {

        // echo '<pre>'; print_r($data); die;

        $curl = curl_init();
        switch ($method)
            {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;

        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;

        default:
            if ($data) $url = sprintf("%s?%s", $url, http_build_query($data));
            }

        // OPTIONS:

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:multipart/form-data',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        // EXECUTE:

        $result = curl_exec($curl);
        if (!$result)
            {
            die("Connection Failure");
            }

        curl_close($curl);
        return $result;
        }


public function finaliserequest(Request $request){
      $user = Auth::user();
      $uid=$user->id;
      $d=$this->commonModel->getunewserbyuserid($user->id);
      $d=$this->commonModel->getunewserbyuserid($uid);
      $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

      $sched=''; $search='';
      $status=$this->commonModel->allstatus();
      if(isset($ele_details)) {  $i=0;
       foreach($ele_details as $ed) {
         // $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
          //$const_type=$ed->CONST_TYPE;
        }
      }
 
      $user_data = $d;
    
    $resultPCs = DB::table('m_pc')
      ->select('PC_NO','pc_name')
      ->where('st_code',$user->st_code)
      ->where('pc_no',$user->pc_no)
      ->get()->toArray();
    
    
    //echo '<pre>';
    //print_r($data);die;
    
                    
      return view('IndexCardReports.IndexCardDataPCWise.finaliserequest', compact('user_data','resultPCs'));
    }



public function finalizerequestsubmit(Request $request){
    
    
    
      $validator = Validator::make($request->all(), [ 
                'file_upload' => 'required|mimes:pdf'
                
            ]);


            if ($validator->fails()) {
               return Redirect::back()
               ->withErrors($validator)
               ->withInput();          
            }
    
    
    
    
    $user = Auth::user();
     
      
    $photo = $request->file('file_upload')->getClientOriginalName();
    $photo =   time().'-'.$photo;
    
    $destination = base_path() . '/public/indexcard';
    
    $request->file('file_upload')->move($destination, $photo);
   
    
          $insertData[] = array(
            'st_code'                   => $user->st_code,
            'pc_no'           => $request->pcno,
            'file_name'           => $photo,
            'submitted_by'            => $user->officername,
            'submitted_at'          => date('Y-m-d H:i:s')
          );
 
      foreach ($insertData as $key => $value) {
      $insertId = DB::table('finalize_request_ic')->insertGetId($value);
      }
     
    $user_data = $user;
                    
      return Redirect::to('ropc/myrequestindexcard');
    }



public function myrequestindexcard(Request $request){
  //dd("Hello");
  $user_data = Auth::user();
     
    $data = DB::table('finalize_request_ic')
          ->select('finalize_request_ic.*','m_pc.PC_NAME')
        ->join('m_pc',function($join){
          $join->on('finalize_request_ic.st_code','=','m_pc.st_code')
            ->on('finalize_request_ic.pc_no','=','m_pc.pc_no');
        })
        ->where('submitted_by',$user_data->officername)
        ->where('finalize_request_ic.st_code',$user_data->st_code)
        ->where('finalize_request_ic.pc_no',$user_data->pc_no)
        ->orderBy('finalize_request_ic.id','DESC')
        ->get();
        
    //echo '<pre>';
    //print_r($data); die;

      return view('IndexCardReports.IndexCardDataPCWise.myrequestindexcard', compact('user_data','data'));
    }
   	
}

