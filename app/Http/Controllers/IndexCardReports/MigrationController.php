<?php

namespace App\Http\Controllers\IndexCardReports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Session;
use DB;

class MigrationController extends Controller
{
    
  
    public function indexcardMigration(){


       //DB::enableQueryLog();

        
        
        $pcData = DB::table('m_pc')->select('m_pc.ST_CODE','m_pc.PC_NO', 'm_election_details.ScheduleID')
        
        ->leftjoin('m_election_details', function($join){
            $join->on('m_pc.ST_CODE','=','m_election_details.ST_CODE')
                ->on('m_pc.PC_NO','=','m_election_details.CONST_NO');
        })
        ->where('m_election_details.CONST_TYPE', "PC")
        ->orderBy('m_pc.ST_CODE','ASC')
        ->orderBy('m_pc.PC_NO','ASC')
        ->get()->toArray();


        // $queue = DB::getQueryLog();


      
       // echo "<pre>"; print_r($queue); die;

        $dataarraypc = array();
        
        //echo '<pre>'; print_r($pcData); die;

        // loop through all the pc and get data in order to 
        // get all the data required from query
        
        foreach($pcData as $pcrow){
        
        
         $st_code = $pcrow->ST_CODE;
        $pc = $pcrow->PC_NO; 


		 $st_code = 'U07';
         $pc = 1;

        $dataarraypc = array(
            'st_code' => $pcrow->ST_CODE,
             'pc' => $pcrow->PC_NO,
             'ScheduleID'=> $pcrow->ScheduleID,
        ); 

        

//dd($dataarraypc);
        $election_detail['ScheduleID'] = $pcrow->ScheduleID; 
        

        $fWhere = array(
                        'st_code'   => $st_code,
                        'pc_no'     => $pc,
                        'ac_no'     => null
                    );

        DB::enableQueryLog();

       

        $electorData = DB::table('electors_cdac AS ec')
                          ->select(array(
                            	  'ovi.nri_male_voters AS nri_male_voters',
                            	  'ovi.nri_female_voters AS nri_female_voters',
                            	  'ovi.nri_other_voters AS nri_other_voters',
                            	  'ovi.test_votes_49_ma AS test_votes_49_ma',
                                'ovi.votes_not_retreived_from_evm AS votes_not_retreived_from_evm',
                                'ovi.rejected_votes_due_2_other_reason AS rejected_votes_due_2_other_reason',
                                'ovi.service_postal_votes_under_section_8 AS service_postal_votes_under_section_8',

                                'ovi.service_postal_votes_gov AS service_postal_votes_gov',
                                'ovi.postal_votes_rejected AS postal_votes_rejected',
                                'ovi.proxy_votes AS proxy_votes',
                                'ovi.tendered_votes AS tendered_votes',

                                'ovi.total_polling_station_s_i_t_c AS total_polling_station_s_i_t_c',
                                'ovi.date_of_repoll AS date_of_repoll',
                                'ovi.no_poll_station_where_repoll AS no_poll_station_where_repoll',
                                'ovi.is_by_or_countermanded_election AS is_by_or_countermanded_election',
                                'ovi.reasons_for_by_or_countermanded_election AS reasons_for_by_or_countermanded_election',

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

                     /*   ->Join('pd_scheduledetail as psd',function($aa){
                            $aa->on('ec.st_code','psd.st_code')
                                    ->on('ec.pc_no','psd.pc_no');
                                   
                        }) */

                        //->join('other_votes_information as ovi', 'ovi.st_code','ec.st_code')
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

                       // echo "<pre>"; print_r($electorData); die;

                          $queue = DB::getQueryLog();


      
                          // echo "<pre>"; print_r($queue); die;

       
                   $voterdata = DB::table('pd_scheduledetail')
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

                        
                        
                        ->first();




                   //echo "<pre>"; print_r($electorData); die;    

         /////////////////////////dataarray/////////////////////////////////////

         
                    

                                           $dataarraypc['e_gen_m']  = @$electorData->gen_m;
                                           $dataarraypc['total_t']  = @$electorData->total_t;
                                           $dataarraypc['e_nri_m']  = @$electorData->nri_m; 
                                           $dataarraypc['e_ser_m']  = @$electorData->ser_m;        
                                           $dataarraypc['e_all_t_m'] =@$electorData->gen_m+ @$electorData->nri_m+ @$electorData->ser_m;
                                           $dataarraypc['e_gen_f'] = @$electorData->gen_f;
                                           $dataarraypc['e_nri_f'] = @$electorData->nri_f;
                                           $dataarraypc['e_ser_f'] = @$electorData->ser_f; 
                                           $dataarraypc['e_all_t_f'] =@$electorData->gen_f+ @$electorData->nri_f+@ $electorData->ser_f; 
                                           $dataarraypc['e_gen_o'] = @$electorData->gen_o;   
                                           $dataarraypc['e_nri_o'] = @$electorData->nri_o;
                                            $dataarraypc['e_all_t_o'] =@$electorData->gen_o+ @$electorData->nri_o; 
                                           $dataarraypc['e_gen_t'] = @$electorData->gen_m+@$electorData->gen_f+@$electorData->gen_o;
                                           $dataarraypc['e_ser_t'] = @$electorData->ser_t;
                                           $dataarraypc['e_nri_t'] =  @$electorData->nri_m+ @$electorData->nri_f+@$electorData->nri_o;
                                           $dataarraypc['proxy_votes'] =@$electorData->proxy_votes;
                                           $dataarraypc['test_votes_49_ma'] =@$electorData->test_votes_49;
                                           //voter

                                           $dataarraypc[ 'voter_male'] =@$voterdata->male_voter;
                                           $dataarraypc[ 'voter_female'] =@$voterdata->female_voter;
                                           $dataarraypc[ 'voter_other'] =@$voterdata->other_voter;
                                           $dataarraypc[ 'voters_service'] =@$electorData->voters_service;
                                           $dataarraypc[ 'voter_total'] =@$voterdata->totel_voter;
                                           $dataarraypc[ 'nri_male_votes'] =@$electorData->nri_male_votes;
                                           $dataarraypc[ 'nri_female_votes'] =@$electorData->nri_female_votes;
                                           $dataarraypc[ 'nri_third_votes'] =@$electorData->nri_third_votes;
                                           $dataarraypc[ 'service_postal_votes'] =@$electorData->service_postal_votes;
                                           $dataarraypc[ 'votes_not_retrieved_on_evm'] =@$electorData->votes_not_retrieved_on_evm;
                                           $dataarraypc[ 'govt_servent_postal_votes'] =@$electorData->govt_servent_postal_votes;
                                           $dataarraypc[ 'rejected_votes_evm'] =@$electorData->rejected_votes_evm;

                                           $dataarraypc[ 'date_of_repoll'] =@$electorData->date_of_repoll;
                                           $dataarraypc[ 'no_poll_station_where_repoll'] =@$electorData->no_poll_station_where_repoll;
                                           $dataarraypc[ 'is_by_countermanded_election'] =@$electorData->is_by_countermanded_election;
                                           $dataarraypc[ 'reasons_thereof'] =@$electorData->reasons_thereof;
                                           $dataarraypc[ 'total_polling_station_s_i_t_c'] =@$electorData->total_polling_station_s_i_t_c;
                                           
                                    //    echo "<pre>"; print_r($dataarraypc); die;


                                 
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

                                'v_votes_evm_all'=> @$evmvotesfromcp->evm_votes,
                                'postal_valid_votes'=> @$evmvotesfromcp->postal_votes,
                                'total_valid_votes'=> @$evmvotesfromcp->total_votes,
                                'r_votes_evm'=> 0,
                                'r_votes_postal'=> @$evmvotesfromcp->rej_votes_postal,
                                'tendered_votes'=> @$evmvotesfromcp->tended_votes,

                                ));



    // echo "<pre>"; print_r($dataarraypc); die;

        DB::enableQueryLog();

        $indexCardDatas = DB::select("SELECT `status`,SUM(male) as male,SUM(third) as third,SUM(female) as female,SUM(female) as female,SUM(total)as total,sum(wdmale) as wdmale,sum(wdfemale) as wdfemale ,sum(wdthird) as wdthird ,sum(rejmale) as rejmale,sum(rejfemale) as rejfemale,sum(rejthird) as rejthird,sum(acpmale) as acpmale
,sum(acpfemale) as acpfemale,sum(acpthird) as acpthird FROM (SELECT `D`.`status`, IF (B.cand_gender = 'male' ,COUNT(DISTINCT A.candidate_id),'')  AS male, IF (B.cand_gender = 'female' ,COUNT(DISTINCT A.candidate_id),'')   AS female, SUM(CASE WHEN B.cand_gender = 'third' THEN 1 ELSE 0 END) AS third, COUNT(DISTINCT A.candidate_id) AS total, SUM(CASE WHEN A.application_status = '5'  AND B.cand_gender = 'male' THEN 1 ELSE 0 END) AS wdmale, SUM(CASE WHEN A.application_status = '5' AND B.cand_gender = 'female' THEN 1 ELSE 0 END) AS wdfemale, SUM(CASE WHEN A.application_status = '5' AND B.cand_gender = 'other' THEN 1 ELSE 0 END) AS wdthird, SUM(CASE WHEN A.application_status = '4'  AND B.cand_gender = 'male' THEN 1 ELSE 0 END) AS rejmale, SUM(CASE WHEN A.application_status = '4' AND B.cand_gender = 'female' THEN 1 ELSE 0 END) AS rejfemale, SUM(CASE WHEN A.application_status = '4' AND B.cand_gender = 'other' THEN 1 ELSE 0 END) AS rejthird, SUM(CASE WHEN A.application_status = '6'  AND B.cand_gender = 'male' AND A.finalaccepted = '1' THEN 1 ELSE 0 END) AS acpmale, SUM(CASE WHEN A.application_status = '6' AND B.cand_gender = 'female' AND A.finalaccepted = '1' THEN 1 ELSE 0 END) AS acpfemale, SUM(CASE WHEN A.application_status = '6' AND B.cand_gender = 'other' AND A.finalaccepted = '1' THEN 1 ELSE 0 END) AS acpthird FROM `candidate_nomination_detail` AS `A` INNER JOIN `candidate_personal_detail` AS `B` ON `A`.`candidate_id` = `B`.`candidate_id` INNER JOIN `m_status` AS `D` ON `D`.`id` = `A`.`application_status` WHERE (`A`.`st_code` = '$st_code' AND `A`.`pc_no` = $pc) AND `A`.`candidate_id` NOT IN (4319) GROUP BY  A.candidate_id)X limit 1");


$indexCardDatas =$indexCardDatas[0];
//echo '<pre>'; print_r($indexCardDatas);
 //die();


                $datanotapcwise = DB::table('counting_pcmaster')
                                ->select('postal_vote','evm_vote')
                                ->where(array(
                                        'st_code' => $st_code,
                                        'pc_no'    => $pc,
                                        'candidate_id'=>4319
                                 ))
                                 ->first();

                /* $datapollingStation = DB::table('polling_station AS ps')
                                    ->select([
                                        DB::raw("COUNT(ps.PS_NO) AS totalpollingstationinPC")
                                    ])
                                     ->where(array(
                                        'st_code' => $st_code,
                                        'pc_no'    => $pc
                                        
                                 ))
                                
                                 ->first(); */


              /*   $dataschedulepoll = DB::table('polling_station AS ps')
                                    ->select([
                                        DB::raw("COUNT(ps.PS_NO) AS totalpollingstationinPC")
                                    ])
                                     ->where(array(
                                        'st_code' => $st_code,
                                        'pc_no'    => $pc
                                        
                                 ))
                                
                                 ->first(); */

          
                
              //  echo "<pre>"; print_r($datapollingStation); die;
            $dataarraypc = array_merge($dataarraypc,array(

                                'c_nom_m_t'=> $indexCardDatas->male,
                                'c_nom_f_t'=> $indexCardDatas->female,
                                'c_nom_o_t'=> $indexCardDatas->third,
                                'c_nom_all_t'=> $indexCardDatas->total,

                                'c_wd_m_t'=> $indexCardDatas->wdmale,
                                'c_wd_f_t'=> $indexCardDatas->wdfemale,
                                'c_wd_o_t'=> $indexCardDatas->wdthird,

                                 'c_rej_m_t'=> $indexCardDatas->rejmale,
                                'c_rej_f_t'=> $indexCardDatas->rejfemale,
                                'c_rej_o_t'=> $indexCardDatas->rejthird,

                                 'c_acp_m_t'=> $indexCardDatas->acpmale,
                                'c_acp_f_t'=> $indexCardDatas->acpfemale,
                                'c_acp_o_t'=> $indexCardDatas->acpthird,
                                
                                

                                ));


   //echo "<pre>"; print_r($dataarraypc); die;
            $indexCardDatasDf = DB::select("SELECT cp.st_code,cp.pc_no,
            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 
            where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and cp.candidate_id != 4319 and  C.cand_gender = 'male' GROUP BY cp1.`pc_no` ),2) < .16 THEN 1 ELSE 0 END) as fdmale,

            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 
            where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and cp.candidate_id != 4319 and  C.cand_gender = 'female' GROUP BY cp1.`pc_no` ),2) < .16 THEN 1 ELSE 0 END) as fdfemale, 

            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 
            where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and cp.candidate_id != 4319 and  C.cand_gender = 'third' GROUP BY cp1.`pc_no` ),2) < .16 THEN 1 ELSE 0 END) as fdthird,


            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 
            where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and cp.candidate_id != 4319 GROUP BY cp1.`pc_no` ),2) < .16 THEN 1 ELSE 0 END) as fd


            FROM `counting_pcmaster` as cp
            join candidate_personal_detail as C on C.candidate_id = cp.candidate_id
            WHERE cp.candidate_id != (select candidate_id from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code) 
            AND cp.candidate_id != 4319 AND cp.st_code = '$st_code' and cp.pc_no = $pc");


            $dataarraypc = array_merge($dataarraypc,array(

                                'c_fd_m_t'=> $indexCardDatasDf[0]->fdmale,
                                'c_fd_f_t'=> $indexCardDatasDf[0]->fdfemale,
                                'c_fd_o_t'=> $indexCardDatasDf[0]->fdthird,
                                'c_fd_t'=> $indexCardDatasDf[0]->fd,
                                

                                ));

            $pollDateInfoPcwise = DB::table('m_schedule as ms')
                                ->select('ms.DATE_POLL','ms.DATE_COUNT','wlc.result_declared_date')
                                ->join('m_st_schedule as mss','mss.SCHEDULEID','ms.SCHEDULEID')
                                ->join('winning_leading_candidate as wlc', function($join){
                                    $join->on('wlc.st_code', 'mss.st_code')
                                            ->on('wlc.pc_no', 'mss.CONST_NO');
                                })
                                ->where(array(
                                        'mss.st_code' => $st_code,
                                        'mss.CONST_NO'    => $pc
                                        
                                 ))
                                ->first();


          // echo "<pre>"; print_r($pollDateInfoPcwise); die;
 //echo "<pre>"; print_r($dataarraypc); die;


		   if($dataarraypc['total_polling_station_s_i_t_c'] > 0){
			   $avg = (int)(($dataarraypc['total_t'])/$dataarraypc['total_polling_station_s_i_t_c']); 
		   }else{
			  $avg = 0; 
		   }
             

 $data=array(

             'st_code'                        =>  $dataarraypc['st_code'],
             'pc_no'                          => $dataarraypc['pc'],
             'schedule_id'                    => $dataarraypc['ScheduleID'],
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
           //    "e_ser_o"                        => ($e_ser_o)?$e_ser_o:0,   $dataarraypc['e_ser_t'] = @$electorData->ser_t;
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
    "v_r_evm_all"       =>      $dataarraypc['test_votes_49_ma'] + $dataarraypc['votes_not_retrieved_on_evm'] + $dataarraypc[ 'rejected_votes_evm'] +@$datanotapcwise->evm_vote,
     "v_votes_evm_all"                     => $dataarraypc['v_votes_evm_all'] - ($dataarraypc['test_votes_49_ma'] + $dataarraypc['votes_not_retrieved_on_evm'] + $dataarraypc[ 'rejected_votes_evm'] +@$datanotapcwise->evm_vote),

    "postal_vote_ser_u"                     =>$dataarraypc['service_postal_votes'],
    "postal_vote_ser_o"                     =>$dataarraypc['govt_servent_postal_votes'],
    "postal_vote_rejected"                     =>@$evmvotesfromcp->rej_votes_postal,
    "postal_vote_nota"       =>   @$datanotapcwise->postal_vote,
    "postal_vote_r_nota"  =>    @$datanotapcwise->postal_vote+@$evmvotesfromcp->rej_votes_postal,
    "postal_valid_votes"                     =>$dataarraypc['service_postal_votes']+$dataarraypc['govt_servent_postal_votes']-(@$datanotapcwise->postal_vote+@$evmvotesfromcp->rej_votes_postal),

    "total_votes_polled"                       =>$dataarraypc['v_votes_evm_all']+ $dataarraypc['service_postal_votes']+$dataarraypc['govt_servent_postal_votes'],
   // "v_r_votes_evm"                     => $dataarraypc['test_votes_49_ma'] + $dataarraypc['votes_not_retrieved_on_evm'] + $dataarraypc['r_votes_evm'] +  @$datanotapcwise->evm_vote,
 "total_not_count_votes"       =>  $dataarraypc['test_votes_49_ma'] + $dataarraypc['votes_not_retrieved_on_evm'] + $dataarraypc[ 'rejected_votes_evm'] +@$datanotapcwise->evm_vote + @$datanotapcwise->postal_vote+@$evmvotesfromcp->rej_votes_postal ,

    "total_valid_votes"                     => $dataarraypc['v_votes_evm_all'] - ($dataarraypc['test_votes_49_ma'] + $dataarraypc['votes_not_retrieved_on_evm'] + $dataarraypc[ 'rejected_votes_evm'] +@$datanotapcwise->evm_vote) + $dataarraypc['service_postal_votes']+$dataarraypc['govt_servent_postal_votes']-(@$datanotapcwise->postal_vote+@$evmvotesfromcp->rej_votes_postal),
     "total_votes_nota"       =>    @$datanotapcwise->evm_vote+ @$datanotapcwise->postal_vote,

     "dt_repoll"               =>   @$electorData->date_of_repoll,
     "re_poll_station"         =>   @$electorData->no_poll_station_where_repoll,
     "flag_bye_counter"        =>   @$electorData->is_by_countermanded_election,
     "flag_bye_counter_reason" =>   @$electorData->reasons_thereof
     
 );


     


  $data = array_map(
                function($val){
                 return ($val)?$val:0;  
                },

                $data
            );  



 echo "<pre>"; print_r($data); die;
//$data = json_decode(json_encode($data));


                           

echo $dataarraypc['st_code'] .'------'.$dataarraypc['pc'].'<br>';
     DB::table('t_pc_ic')->insert($data);
            

            
        }

        
        die('done');
        
    }
        
}

