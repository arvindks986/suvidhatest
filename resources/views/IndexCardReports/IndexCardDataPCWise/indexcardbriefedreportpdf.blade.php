<html>
    <head>
           <style>

    td {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #4a4646 !important;
    padding: 2.2px;
    font-family: "Times New Roman", Times, serif;
    }
    tr{
        border-bottom: 1px solid #000 !important;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
    }
    .table-bordered{
    border:1px solid #000;
    }
    .table-bordered td,
    .table-bordered th {
    border: 1px solid #000 !important
    }
    .table {
    width: 100%;
    border-collapse: collapse;
    font-size: .9em;
    color: #000;
    margin-bottom: 1rem;
    color: #212529;
    }

    .bordertestreport{
      border:1px solid #000;
    }
    .border{
    border-bottom: 1px solid #000;
    }
    th {
 
    text-align: center;
    font-size: 11px;
    font-weight: bold !important;
    }
    
    table{
    width: 100%;
    }
    
    </style>
    </head>
    <div class="bordertestreport">
       <table class="border">
          <tr>
                <td style="text-align: left;">
                    <p> <img src="<?php echo public_path('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/>  </p>
                </td>
              <td style="text-align: right;">

                

                <p style="float: right;width: 100%;font-size: 15px;"><b>SECRETARIAT OF THE <br>ELECTION COMMISSION OF INDIA
</b>
                 <br><b>Nirvachan Sadan, Ashoka Road, New Delhi-110001</b></p>
          </td>
      </tr>
  </table>





<?php  $st=getstatebystatecode($st_code);   ?> 
<section class="">
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class="row">


                        <table class="border">
                            
                            <tr><td><h4 style="font-size: 13px;"><b> Index Card Briefed Report - {{getElectionYear()}}</b></h4></td>
                                <td style="text-align: right;"><p class="mb-0 text-right" style="font-size: 13px;"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
                            </p></td>
                        </tr>

                           <tr>
    <td style="text-align: left;"><b style="font-size: 13px; ">User</b>: 
	@if(Auth::user()->designation == 'ECIINDEX')
		ECI
	@elseif(Auth::user()->designation == 'ECI')
		ECI
	@else {{Auth::user()->officername}}
	@endif</td>
    <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 13px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
  </tr>



                        </table>


<table class="border">
<tr><td><b>Type of Constituency:</b></td><td>{{$getIndexCardDataPCWise['pcType']->PC_TYPE}}</td></tr>
<tr><td><b>Number & Name of PC:</b></td><td>{{$getIndexCardDataPCWise['pcType']->PC_NO}} : {{$getIndexCardDataPCWise['pcType']->PC_NAME}} </td></tr>
<tr><td><b>District :</b></td><td>{{$getIndexCardDataPCWise['distict_name']}}</td></tr>

</table>




						
						
						
					</div>
									
					
				</div>
				
				<div class="card-body">

<div class="wapper">
    <div class="grids"> 
    <div class="whole">
    


    <div class="container-fluid">
        
        </div>
    



      <div class="row" id="index_cus_ch">
        <!--tabs starts-->


        <div class="tab-content" style="overflow: auto;">

            <div>
                <!--  <h3>Data For Election</h3> -->
                    <?php //echo "<pre>"; print_r($getIndexCardDataPCWise['t_pc_ic']); die; ?>

                
                        <div class="col-sm-12">
                            <div class="col-md-12 col-sm-12 col-xs-12 tab_card">
                              
                                    <div class="table-responsive">
                                    <table class="table table-bordered tableindexcard" style="width: 100%;">



                                             <tr>
                                                <th>I</th>
                                                <th>CANDIDATES</th>
                                                <th>MALE</th>
                                                <th>FEMALE</th>
                                                <th>THIRD GENDER</th>
                                                <th>TOTAL</th>
                                            </tr>
                                            
                                            @foreach($getIndexCardDataPCWise['indexCardData'] as $nominatedData)
                                            
                                            @if($nominatedData->status == 'nominated')
                                            <tr>
                                                <td>1.</td>
                                                <td>Nominated </td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_m_t}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_f_t}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_o_t}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_a_t}}</td>
                                            </tr>
                                            @endif
                                            @if($nominatedData->status == 'rejected')
                                            <tr>
                                                                                                <td>2.</td>

                                                <td>Nominations  Rejected</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_m}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_f}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_o}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_a}}</td>
                                            </tr>
                                            @endif
                                            @if($nominatedData->status == 'withdrawn')
                                            
                                            <tr>
                                                <td>3.</td>
                                                <td>Withdrawn</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_m}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_f}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_o}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_t}}</td>
                                            </tr>
                                            
                                            @endif
                                            @if($nominatedData->status == 'accepted')
                                            <tr>
                                                <td>4.</td>
                                                <td>Contested </td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_m}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_f}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_o}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_t}}</td>
                                            </tr>
                                            @endif
                                            @if($nominatedData->status == 'forfieted')
                                           <tr>
                                            <td>5.</td>
                                               <td>Deposit Forfeited </td>
                                               <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_m}}</td>
                                               <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_f}}</td>
                                               <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_o}}</td>
                                               <td>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_t}}</td>
                                           </tr>
                                            @endif
                                            @endforeach                   




<tr>    
<th>II</th>
<th>ELECTORS</th>
<th colspan="2" style="text-align: center;">GENERAL</th>
<th  rowspan="2">SERVICE</th>
<th rowspan="2">TOTAL</th>
</tr>


<tr> 
    <th colspan="2"></th>
<th>Other than NRIs</th>
<th>NRIs</th>

</tr>


<tr>    
<td>1</td>
<td>Male</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_m }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_nri_m }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_ser_m }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_all_t_m }}</td>


</tr>


<tr>    
<td>2</td>
<td>Female</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_f }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_nri_f }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_ser_f }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_all_t_f }}</td>


</tr>




<tr>    
<td>3</td>
<td>Third Gender (Not applicable to service electors)</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_o }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_nri_o }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_ser_o }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_all_t_o }}</td>


</tr>


<tr>    
<td>4</td>
<td>Total</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_t }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_nri_t }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_ser_t }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_all_t }}</td>


</tr>







<tr>    
<th>III</th>
<th>VOTERS TURNED UP FOR VOTING</th>
<th colspan="2" style="text-align: center;">GENERAL</th>
<th colspan="2" style="text-align: center;">Total</th>
</tr>


<tr> 
 <th colspan="2"></th>
<th>Other than NRIs</th>
<th>NRIs</th>
<th colspan="2" style="text-align: center;"></th>

</tr>


<tr>    
<td>1</td>
<td>Male</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_m }}</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_m ?:0 }}</td>
<td colspan="2" style="text-align: center;">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_m +$getIndexCardDataPCWise['t_pc_ic']->vt_nri_m }}</td>


</tr>


<tr>    
<td>2</td>
<td>Female</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_f}}</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_f ?:0 }}</td>
<td colspan="2" style="text-align: center;">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_f + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_f }}</td>


</tr>




<tr>    
<td>3</td>
<td>Third Gender</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_o}}</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_o ?:0 }}</td>
<td colspan="2" style="text-align: center;">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_o + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_o }}</td>


</tr>


<tr>    
<td>4</td>
<td>Total[Male+ Female+ Third Gender]</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_t}}</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_t }}</td>
<td colspan="2" style="text-align: center;">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_t + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_t }}</td>


</tr>




                                        <tr>
                                            <th>IV</th>
                                                <th colspan="5">DETAILS OF VOTES POLLED ON EVM</th>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td colspan="4">Total votes polled on EVM</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->t_votes_evm}}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td colspan="4">Test voted under Rule 49 MA</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->mock_poll_evm ?:0}}</td>
                                            </tr>
                                            
                                             <!--<tr>
                                                <td>3</td>
                                                <td colspan="4">Votes not retrieved From EVM</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->not_retrieved_vote_evm ? :0}}</td>
                                            </tr>-->
											
											
											<tr>
											<td>3A</td>
                                                <td colspan="4">Votes Counted From CU Of EVM</td>
												<td>{{$getIndexCardDataPCWise['t_pc_ic']->votes_counted_from_evm ? :0}}</td>
                                            </tr>
											
											<tr>
											<td>3B</td>
                                                <td colspan="4">Votes Counted From VVPAT (Whenever Votes Not Retrieved From CU)</td>
												<td>{{$getIndexCardDataPCWise['t_pc_ic']->votes_counted_from_vvpat ? :0}}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td>4</td>
                                                <td colspan="4">Rejected votes (due to other reasons)</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->r_votes_evm ?:0}}</td>
                                            </tr>
                                            
                                            
                                             <tr>
                                                <td>5</td>
                                                <td colspan="4">Votes polled for 'NOTA' on EVM</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota}}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td>6</td>
                                                <td colspan="4">Total of test votes + votes Rejected (due to other reasons) + NOTA [2+4+5]</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->v_r_evm_all}}</td>
                                            </tr>
                                            
                                            
                                            <tr>
                                                <td>7</td>
                                                <td colspan="4">Total valid votes counted from EVM [1-6]</span></td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->v_votes_evm_all}}</td>
                                            </tr>



                                      <tr>
                                        <th>V</th>
                                                <th colspan="5"> DETAILS OF POSTAL VOTES</th>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td colspan="4">Postal votes counted for service voter under sub-section (8) of Section 20 of R.P. Act, 1950</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_ser_u ?:0}}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td colspan="4">Postal votes counted for Govt. servants on election duty (including all police personnel , drivers, conductors, cleaners)</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_ser_o ?:0}}</td>
                                            </tr>
                                             <tr>
                                                <td>3</td>
                                                <td colspan="4">Postal votes rejected</td>
                                                <td class="dev" colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_rejected ?:0}}</td>
                                            </tr>
                                            
                                            
                                            
                                            <tr>
                                                <td>4</td>
                                                <td colspan="4">Postal votes polled for 'NOTA'</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota}}</td>
                                            </tr>
                                           
                                           
                                           <tr>
                                            <td>5</td>
                                                <td colspan="4">Total of postal votes rejected + 'NOTA' [3+4] </span> </td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_r_nota}}</td>
                                            </tr>
                                           
                                           
                                            <tr>
                                                <td>6</td>
                                                <td colspan="4">Total valid postal votes [1+2-5] </td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_valid_votes}}</td>
                                            </tr>




          
                                             <tr>
                                                <th>VI</th>
                                                <th colspan="5">COMBINED DETAILS OF EVM & POSTAL VOTES</th>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td colspan="4">Total votes polled [IV(1) + V(1+2)]</span></td>
                                                <td class="dev">{{$getIndexCardDataPCWise['t_pc_ic']->total_votes_polled}}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td colspan="4">Total of test votes + votes rejected +'NOTA'[IV(6) + V(5)]</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->total_not_count_votes}}</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td colspan="4">Total valid votes [IV(7) + V(6)]</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->total_valid_votes}}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td>4</td>
                                                <td colspan="4">Total votes polled for 'NOTA' [IV(5) + V(4)]</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->total_votes_nota}}</td>
                                            </tr>

                     
            


											<tr>
                                                <th>VII</th>
                                                <th colspan="5">MISCELLANEOUS</th>
                                            </tr>
											
											<tr>
                                                <td>1</td>
                                                <td colspan="4">Proxy votes</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->proxy_votes ?:0}}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td colspan="4">Tendered votes</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->tendered_votes ?:0}}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td>3</td>
                                                <td colspan="4">Total number of polling stations set up in the Constituency</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->total_no_polling_station ?:0}}</td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td colspan="4">Average number of Electors per polling stations in  a Constituency</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->avg_elec_polling_stn ?:0}}</td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td colspan="4">Date(s) Of Poll</td>
                                                <td colspan="1">{{date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->dt_poll))}}</td>
                                            </tr>
											
											<tr>
                                                <td>6</td>
                                                <td colspan="4">Date(s) Of Re-poll,if any</td>
                                                <td colspan="1">
												@if (trim($getIndexCardDataPCWise['t_pc_ic']->dt_repoll) != 0 && $getIndexCardDataPCWise['t_pc_ic']->dt_repoll)
													
												<?php 
													$repoll_dates 	= explode(',',$getIndexCardDataPCWise['t_pc_ic']->dt_repoll);
													$dates_array 	= [];
													foreach($repoll_dates as $res_repoll){
														$dates_array[] = date('d-m-Y', strtotime(trim($res_repoll)));
													}	
												?>
												
												{!! implode(', ', $dates_array) !!}
                                                @else{{'NA'}}
												@endif
												
												</td>
                                            </tr>
											
											<tr>
                                                <td>7</td>
                                                <td colspan="4">Number Of polling stations where Re-poll was ordered (mention date of Order also)</td>
                                                <td colspan="1">
												@if ($getIndexCardDataPCWise['t_pc_ic']->re_poll_station)
												{{$getIndexCardDataPCWise['t_pc_ic']->re_poll_station}}
                                                @else{{'NA'}}
												@endif
												
												</td>
                                            </tr>
											
											
                                            <tr>
                                                <td>8</td>
                                                <td colspan="4">Date(s) Of counting</td>
                                                <td colspan="1">{{date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->dt_counting))}}</td>
                                            </tr>
											
											
                                            <tr>
                                                <td>9</td>
                                                <td colspan="4">Date Of declaration Of result</td>
                                                <td colspan="1">{{date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->dt_declare))}}</td>
                                            </tr>
                                            <tr>
                                                <td>10</td>
                                                <td colspan="4">Whether this is Bye election <br> or Countermanded election? &nbsp; &nbsp; &nbsp;   Yes/No</td>
                                                <td class="dev" colspan="1">
												@if ($getIndexCardDataPCWise['t_pc_ic']->flag_bye_counter == 1)
												Yes
												@else
												No
												@endif
													
													</td>
                                            </tr>
                                            <tr>
                                                <td>11</td>
                                                <td colspan="4">If yes, reason thereof</td>
                                                <td>
												@if ($getIndexCardDataPCWise['t_pc_ic']->flag_bye_counter_reason)
												{{$getIndexCardDataPCWise['t_pc_ic']->flag_bye_counter_reason}}
												@else
												Not Applicable
												@endif
												
												</td>
                                            </tr>
											
										
                                    </table>
                                    </div>
                         
                             
                            </div>
                        </div> 

            </div>

            <!-- menu1 -->

            <div id="" class="">
                <div class="table-responsive" style="position:relative;">
                 
                    <table class="table table-bordered" style="width: 100%; text-align: center;">
                        <thead>

                            <tr><th>VIII.</th><th colspan="9"> Detailed Result</th></tr>
                            <tr>
                                <th>SL. No.</th>
                                <th>Candidate Name</th>
                                <th>Sex</th>
                                <th>Age</th>
                                 <th>Category</th>
                                <th>Party</th>
								<?php 
								if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){ ?>
									<th>Migrant Votes</th>
								<?php }
								?>
                                <th>Postal Votes</th>
                                <th>EVM Votes</th>
                                <th>Total Votes</th>
                                <th>Votes %</th>
                            </tr>

                            
                        </thead>

                        <tbody color="CandidateBodyIDWise">
						

						
                            <?php $total_votes_evm_postal = 0; ?>
							
							@foreach($getIndexCardDataCandidatesVotesACWise['candidatedataarray'] as $candpcdata)


							@foreach($candpcdata as $canddata)
							
							<?php $total_votes_evm_postal += $canddata['migrate_votes'] + $canddata['valid_postal_votes']+$canddata['total_valid_vote']; ?>
							
							@endforeach
							@endforeach
							
                            <?php $count=1; ?>
                            
								@foreach($getIndexCardDataCandidatesVotesACWise['candidatedataarray'] as $candpcdata)
                            @foreach($candpcdata as $canddata)
                            <?php //echo "<pre>"; print_r($canddata); die; ?>
                            <tr>
                                <td>{{$count."."}} </td>
                                <td >{{$canddata['cand_name']}}</td>
                                <td>@if($canddata['cand_gender'] == 'male')
									Male
									@elseif($canddata['cand_gender'] == 'female')
									Female
									@elseif($canddata['cand_gender'] == 'third')
									Third Gender
									@endif
								</td>
                                <td>{{$canddata['cand_age']}}</td>
                                 <td>{{strtoupper($canddata['cand_category'])}}</td>
                                <td>{{$canddata['PARTYABBRE']}}</td>
                                <?php 
								
								$migrate_votes = 0;
								
								if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){ 
								$migrate_votes = $canddata['migrate_votes'];
								?>
								
									<td>{{$canddata['migrate_votes']}}</td>
								<?php } ?>
                               
                                <td>{{$canddata['valid_postal_votes']}}</td>
                                 <td>{{$canddata['total_valid_vote']}}</td>
                                <td>{{$canddata['valid_postal_votes']+$canddata['total_valid_vote'] + $migrate_votes}}</td>
								
								
                                <td>@if(($total_votes_evm_postal+$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm) > 0)
								{{round((((($canddata['valid_postal_votes']+$canddata['total_valid_vote'] + $migrate_votes)*100)/($total_votes_evm_postal+$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm))),2)}}
                               @else
								   0
							   @endif </td>
                            </tr>

 

                            @endforeach
							<?php $count++; ?>
                            @endforeach
							<tr>
                                <td>{{$count."."}} </td>
                                <td >None of the Above</td>
                                <td>
                                <td></td>
                                 <td></td>
                                <td>Nota</td>
                                <?php if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){ ?>
                               
                                 <td>{{$getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota}}</td>
								 
								<?php } ?>
                                 <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota}}</td>
                                 <td>{{$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm}}</td>
                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota}}</td>
                                <td>@if(($total_votes_evm_postal+$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota) > 0)
								{{round((((($getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm+$getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota)*100)/($total_votes_evm_postal+$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm+$getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota))),2)}}</td>
                               @else
								   0
							   @endif
                            </tr>
							
							
							
							<tr>
							<?php if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){ ?>
								<td colspan="9" style="text-align:right;"><b>Grand Total Votes: </b></td>
								 
								<?php } else{ ?>
								<td colspan="8" style="text-align:right;"><b>Grand Total Votes: </b></td>
								<?php } ?>
								<td>{{$total_votes_evm_postal+$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm +$getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota}}</td>
								<td></td>
							</tr>
                        </tbody>
                    </table>
                </div>
				
		    </div>


</div>

            </div>
        </div>


    </div>


    <table>
        
          <tr style="width: 100%;">
  
  <td colspan="10" style="text-align: center;"><p><b style="font-size: 15px;">Nirvachan Sadan, Ashoka Road, New Delhi- 110001</b></p></td>
</tr>



    </table>
</div>
</div> <!-- end grids -->
</div>
<!-- End Wrapper-->

</div>
			</div>
		</div>
	</div>
</section>

</html>