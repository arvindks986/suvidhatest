<?php  $st=getstatebystatecode($st_code);   ?>	


<table>
			<tr><td colspan="6" align="center"><b>Election Commistion of India</b></td> </tr>
			<tr><td colspan="6" align="center"><b>{{getElectionType($st_code,$pc)}} Election,{{getElectionYear()}}</b></td> </tr>
			<tr><td colspan="6" align="center"></td> </tr>
			<tr><td colspan="6" align="center">Parliamentary Constituency of {{$st->ST_NAME}}, District {{$getIndexCardDataPCWise['distict_name']}}</td> </tr>
			<tr><td colspan="6" align="center">No. and Name of Parliamentary Constituancy {{$getIndexCardDataPCWise['pcType']->PC_NO}} ({{$getIndexCardDataPCWise['pcType']->PC_TYPE}}) {{$getIndexCardDataPCWise['pcType']->PC_NAME}}</td></tr>
			</table>


				
              <table>



                                             <tr>
                                            <th><b>I</b></th>
                                            <th><b>CANDIDATES</b></th>
                                            <th><b>MALE</b></th>
                                            <th><b>FEMALE</b></th>
                                            <th><b>THIRD GENDER</b></th>
                                            <th><b>TOTAL</b></th>
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
	<th><b>II</b></th>
	<th><b>ELECTORS</b></th>
	<th colspan="2"><b>GENERAL</b></th>
	<th><b>SERVICE</b></th>
	<th><b>TOTAL</b></th>
</tr>
<tr>
	<td></td>
	<td colspan=""></td>
	<td><b>Other than NRIs</b></td>
	<td><b>NRIs</b></td>
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
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_nri_o}}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_ser_o}}</td>
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
                                            <th><b>III</b></th>
                                            <th><b>VOTERS TURNED UP FOR VOTING</b></th>
                                            <th colspan="2"><b>GENERAL</b></th>
                                            <th colspan="2"><b>TOTAL</b></th>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td colspan=""></td>
                                            <td><b>Other than NRIs</b></td>
                                            <td><b>NRIs</b></td>
                                        </tr>


<tr>    
<td>1</td>
<td>Male</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_m }}</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_m ? :0 }}</td>
<td colspan="2">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_m +$getIndexCardDataPCWise['t_pc_ic']->vt_nri_m }}</td>


</tr>


<tr>    
<td>2</td>
<td>Female</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_f}}</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_f ? :0 }}</td>
<td colspan="2">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_f + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_f }}</td>


</tr>




<tr>    
<td>3</td>
<td>Third Gender</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_o}}</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_o ? :0}}</td>
<td colspan="2">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_o + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_o }}</td>


</tr>


<tr>    
<td>4</td>
<td>Total[Male+ Female+ Third Gender]</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_t}}</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_t }}</td>
<td colspan="2">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_t + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_t }}</td>


</tr>




                                        <tr>
                                            <th><b>IV</b></th>
                                                <th colspan="5"><b>DETAILS OF VOTES POLLED ON EVM</b></th>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td colspan="4">Total votes polled on EVM</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->t_votes_evm}}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td colspan="4">Test voted under Rule 49 MA</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->mock_poll_evm ? :0}}</td>
                                            </tr>

											
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
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->r_votes_evm ? :0 }}</td>
                                            </tr>
                                            
                                            
                                             <tr>
                                                <td>5</td>
                                                <td colspan="4">Votes polled for 'NOTA' on EVM</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota}}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td>6</td>
                                                <td colspan="4">Total of test votes + votes Rejected (due to Other Reasons) + NOTA [2+4+5]</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->v_r_evm_all}}</td>
                                            </tr>
                                            
                                            
                                            <tr>
                                                <td>7</td>
                                                <td colspan="4">Total valid votes counted from EVM [1-6]</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->v_votes_evm_all}}</td>
                                            </tr>



                                      <tr>
                                        <th><b>V</b></th>
                                                <th colspan="5"><b> DETAILS OF POSTAL VOTES</b></th>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td colspan="4">Postal votes counted for service voter under sub-section (8) of Section 20 of R.P. Act, 1950</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_ser_u ? :0}}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td colspan="4">Postal votes counted for Govt. servants on election duty (including all police personnel , drivers, conductors, cleaners)</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_ser_o ? :0}}</td>
                                            </tr>
                                             <tr>
                                                <td>3</td>
                                                <td colspan="4">Postal votes rejected</td>
                                                <td class="dev" colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_rejected ? :0}}</td>
                                            </tr>
                                            
                                            
                                            
                                            <tr>
                                                <td>4</td>
                                                <td colspan="4">Postal votes polled for 'NOTA'</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota}}</td>
                                            </tr>
                                           
                                           
                                           <tr>
                                            <td>5</td>
                                                <td colspan="4">Total of postal votes rejected + 'NOTA' [3+4] </td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_r_nota}}</td>
                                            </tr>
                                           
                                           
                                            <tr>
                                                <td>6</td>
                                                <td colspan="4">Total valid postal votes [1+2-5] </td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_valid_votes}}</td>
                                            </tr>




          
                                             <tr>
                                                <th><b>VI</b></th>
                                                <th colspan="5"><b>COMBINED DETAILS OF EVM and POSTAL VOTES</b></th>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td colspan="4">Total votes polled [IV(1) + V(1+2)]</td>
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
                                                <th><b>VII</b></th>
                                                <th colspan="5"><b>MISCELLANEOUS</b></th>
                                            </tr>
                                            
                                            <tr>
                                                <td>1</td>
                                                <td colspan="4">Proxy votes</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->proxy_votes ? :0}}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td colspan="4">Tendered votes</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->tendered_votes ? :0}}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td>3</td>
                                                <td colspan="4">Total number of polling Stations set up in the Constituency</td>
                                                @if($getIndexCardDataPCWise['t_pc_ic']->total_no_polling_station)
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->total_no_polling_station}}</td>
                                                @else 
                                                 <td colspan="1">0</td>
                                                @endif

                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td colspan="4">Average number of Electors per polling stations in  a Constituency</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->avg_elec_polling_stn ? :0}}</td>
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
                                                <td colspan="4">Date(s) Of Counting</td>
                                                <td colspan="1">{{date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->dt_counting))}}</td>
                                            </tr>
                                            
                                            
                                            <tr>
                                                <td>9</td>
                                                <td colspan="4">Date of declaration of result</td>
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
                                                <td colspan="4">If yes, reasons thereof</td>
                                                <td>
                                                @if ($getIndexCardDataPCWise['t_pc_ic']->flag_bye_counter_reason)
                                                {{$getIndexCardDataPCWise['t_pc_ic']->flag_bye_counter_reason}}
                                                @else
                                                Not Applicable
                                                @endif
                                                
                                                </td>
                                            </tr>
                                            
                                        
                                    </table>
									                        
                      <table>
                        <thead>
						
						<tr><th colspan="1"><b>VIII.</b></th><th colspan="5"><b>DETAILS OF VOTES POLLED BY EACH CANDIDATE</b></th></tr>
                            <tr>
                                <th rowspan="2"><b>SL. No.</b></th>
                                <th rowspan="2"><b>Name of Contesting Candidates(in Block letters)</b></th>
                                <th rowspan="2"><b>Sex(Male/Female/Third Gender)</b></th>
                                <th rowspan="2"><b>Age(Years)</b></th>
                                 <th rowspan="2"><b>Category(Gen/SC/ST)</b></th>
                                <th rowspan="2"><b>Full Name of the Party</b></th>
                                <th rowspan="2"><b>Election Symbol Allotted</b></th>
                                
                               
                                <th colspan="{{count($getIndexCardDataCandidatesVotesACWise['allACList'])}}"><b>Valid Votes counted From Electronic Voting Machines </b></th>
                                <?php 
								if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){ ?>
									<th><b>Migrant Votes</b></th>
								<?php }
								?>
                                
                                <th><b>Valid Postal Votes</b></th>
                                <th><b>Total Valid Votes</b></th>
                            </tr>

                            <tr>
                                    
                                @foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)
                                    <th><b>{{$allACListsKey}} : {{$allACListsValue}}</b></th>
                                    
                                @endforeach
                                    <th colspan="3"></th>
                            </tr>

                        </thead>

                        <tbody>
						

						
                            <?php $count=1; 
							$dataSum  = array();
							
							$total_valid_postel_votes = 0;
							$total_valid_migrate_votes = 0;
							$total_valid_votes = 0;
							
							$i=0;
							?>
                            @foreach($getIndexCardDataCandidatesVotesACWise['candidatedataarray'] as $key1 => $candpcdata)
							
							
                            @foreach($candpcdata as  $key2 => $canddata)
                            
                            <tr>
                                <td>{{$count."."}} </td>
                                <td >{{$canddata['cand_name']}}</td>
                                <td  style="text-transform: capitalize;">{{$canddata['cand_gender']}}</td>
                                <td>{{$canddata['cand_age']}}</td>
                                 <td style="text-transform: capitalize;">{{$canddata['cand_category']}}</td>
                                <td>{{$canddata['partyname']}}</td>
                                
                                
                               
                                <td>{{$canddata['party_symbol']}}</td>

                                <?php 
								
								
								$sum = 0;
								
								foreach ($canddata['acdata'] as $key3 => $values) { 
								
								$sum = $values;
								
								
								if (isset($dataSum[$key3]))
								{
									$dataSum[$key3] += $sum;
								}
								else
								{
									$dataSum[$key3] = $sum;
								}
								
								
								?>
                                  
								
								  
                                <td >{{$values}}</td>
                                      
                                <?php } ?>
								
								<?php 
								
								$migrate_votes = 0;
								
								if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){ 
								$migrate_votes = $canddata['migrate_votes'];
								?>
								
								
									<td>{{$canddata['migrate_votes']}}</td>
								<?php } ?>
								
								
								
                                 <td>{{$canddata['valid_postal_votes']}}</td>
                                <td>{{$canddata['valid_postal_votes']+$canddata['total_valid_vote'] + $migrate_votes}}</td>
                               
                                <?php 
								$total_valid_postel_votes += $canddata['valid_postal_votes'];
								$total_valid_migrate_votes += $canddata['migrate_votes'];
							$total_valid_votes += $canddata['valid_postal_votes']+$canddata['total_valid_vote'] + $migrate_votes;
								
								?>
                            </tr>



                              
                                    
                                 

                            @endforeach
							<?php $count++; ?>
                            @endforeach

						<?php 
						
						$migrate_vote_nota = 0;
						$postal_vote_nota = 0;
						$total_nota = 0;
						
						
						if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){ ?>

							<tr>
							<td>{{$count}}</td>
							<td><b>Nota</b></td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							@foreach($getIndexCardDataPCWise['migrate_nota'] as $key4 => $dataValue)
							<td>{{$dataValue['total_vote']}}
							</td>
							
							<?php 
							
							
							if (isset($dataSum[$key4]))
								{
									$dataSum[$key4] += $dataValue['total_vote'];
								}
								else
								{
									$dataSum[$key4] = $dataValue['total_vote'];
								}							
							?> 
							
							
							@endforeach
							
							<td>{{$getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota}}</td>
							
							<td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota}}</td>
							<td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota}}</td>
							</tr>

						<?php

						$migrate_vote_nota = $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota;
						$postal_vote_nota = $getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota;
						$total_nota = $getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota;


						} ?>



							<tr>
							<td colspan="7" style="text-align:right;"><b>Total</b></td>
							@foreach($dataSum as $dataValue)
							<td><b>{{$dataValue}} </b></td>
							@endforeach
							
							<?php if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){ ?>
							<td><b>{{$total_valid_migrate_votes + $migrate_vote_nota}}</b></td>
							<?php } ?>
							
							
							<td><b>{{$total_valid_postel_votes + $postal_vote_nota}}</b></td>
							<td><b>{{$total_valid_votes + $total_nota}}</b></td>
							</tr>




                        </tbody>
                    </table>
		
                <table>
    <thead>
        <tr><th colspan="1"><b>IX.</b></th><th colspan="5"><b>DETAILS OF ELECTORS -ASSEMBLY SEGMENT WISE</b></th></tr>
        <tr>
            <th><b>AC</b></th>
            <th><b>AC Name</b></th>
            <th><b>Gen Male</b></th>
            <th><b>Gen Female</b></th>
            <th><b>Gen Third Gender</b></th>
            <th><b>Total</b></th>
            <th><b>Ser Male </b></th>
            <th><b>Ser Female</b></th>
            <th><b>Ser Third Gender</b></th>
            <th><b>Ser Total</b></th>
            <th><b>NRI Male</b></th>
            <th><b>NRI Female</b></th>
            <th><b>NRI Third Gender</b></th>
            <th><b>NRI Total</b></th>
            <th><b>Total Male</b></th>
            <th><b>Total Female</b></th>
            <th><b>Total Third Gender</b></th>
            <th><b>Total</b></th>
        </tr>
    </thead>

    <tbody>
     
            <?php 
			
				$t_gen_m = 0;
				$t_gen_f = 0;
				$t_gen_o = 0;
                $t_gen_t = 0;
                $t_ser_m = 0;
                $t_ser_f = 0;
                $t_ser_o = 0;
				$t_ser_t = 0;
				$t_nri_m = 0;
				$t_nri_f = 0;
				$t_nri_o = 0;
				$t_nri_t = 0;
                $t_tot_m = 0;
				$t_tot_f = 0;
				$t_tot_o = 0;
			
			
			foreach ($getelectorsacwise as $k => $value) { 
			
				$t_gen_m += $value['gen_m'];
				$t_gen_f += $value['gen_f'];
				$t_gen_o += $value['gen_o'];
                $t_gen_t += $value['gen_t'];
                $t_ser_m += $value['ser_m'];
                $t_ser_f += $value['ser_f'];
                $t_ser_o += $value['ser_o'];
				$t_ser_t += $value['ser_t'];
				$t_nri_m += $value['nri_m'];
				$t_nri_f += $value['nri_f'];
				$t_nri_o += $value['nri_o'];
				$t_nri_t += $value['nri_t'];
                $t_tot_m += $value['tot_m'];
				$t_tot_f += $value['tot_f'];
				$t_tot_o += $value['tot_o'];
			
			?>

                <tr>
                    <td>
                        {{$value['ac_no']}}
                    </td>
                    <td>
                        {{$value['ac_name']}}
                    </td>
                    <td>
                        {{$value['gen_m']}}
                    </td>

                    <td>
                        {{$value['gen_f']}}
                    </td>


                    <td>
                        {{$value['gen_o']}}
                    </td>

                    <td>
                        {{$value['gen_t']}}
                    </td>

                    <td>
                        {{$value['ser_m']}}
                    </td>

                    <td>
                        {{$value['ser_f']}}
                    </td>

					<td>
                        {{$value['ser_o']}}
                    </td>
                    <td>
                        {{$value['ser_t']}}
                    </td>

                    <td>
                        {{$value['nri_m']}}
                    </td>

                    <td>
                        {{$value['nri_f']}}
                    </td>

                    <td>
                        {{$value['nri_o']}}
                    </td>

                    <td>
                        {{$value['nri_t']}} 
                    </td>

                    <td>
                        {{$value['tot_m']}}
                    </td>

                    <td>
                        {{$value['tot_f']}}
                    </td>

                    <td>
                        {{$value['tot_o']}}
                    </td>

					<td> 
					{{$value['gen_t'] + $value['nri_t'] + $value['ser_t']}} </td>

                </tr>

                <?php  } ?>
				
				<tr>
				<th colspan="2"><b>Total</b></th>
				<td><b> {{$t_gen_m}}</b></td>
				<td><b> {{$t_gen_f}}</b></td>
				<td><b> {{$t_gen_o}}</b></td>
                <td><b> {{$t_gen_t}}</b></td>
                <td><b> {{$t_ser_m}}</b></td>
                <td><b> {{$t_ser_f}}</b></td>
                <td><b> {{$t_ser_o}}</b></td>
				<td><b> {{$t_ser_t}}</b></td>
				<td><b> {{$t_nri_m}}</b></td>
				<td><b> {{$t_nri_f}}</b></td>
				<td><b> {{$t_nri_o}}</b></td>
				<td><b> {{$t_nri_t}}</b></td>
                <td><b> {{$t_tot_m}}</b></td>
				<td><b> {{$t_tot_f}}</b></td>
				<td><b> {{$t_tot_o}}</b></td>
				<td><b> {{$t_gen_t + $t_nri_t + $t_ser_t}} </b></td>
				</tr>
				<tr></tr>	  
<tr></tr>	  
	  <tr>
        <td colspan="2"><b>Disclaimer</b></td>
      </tr>
	  <tr>
        <td colspan="10">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</td>
      </tr>
    </tbody>

</table>
                                 