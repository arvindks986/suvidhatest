<html>
    <head>
           <style>

    td {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #4a4646 !important;
    padding: 2px;
    font-family: "Times New Roman", Times, serif;
    }
    tr{
        border-bottom: 1px solid #000 !important;
    }

    .new_mid_sec td{
        padding: 6px;
        font-size: 14px !important;
        text-align: center;
        vertical-align: middle;
    } 
    .new_mid_sec th{
        padding: 2px;
        text-align: center;
                font-size: 14px !important;

        vertical-align: middle;
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
    border-collapse: collapse;
    }

    .borders{
    border-top: 1px solid #000;
    } 

     .borderss{
    border-top: 1px solid #000;
    border-left: 1px solid #000;
    }
    .border{
    border-bottom: 1px solid #000;
    }
    th {
   
    text-align: center;
    font-size: 13px;
    font-weight: bold !important;
    }
    td{
        border-collapse: collapse;

    }
    table{
    width: 100%;
    }
    
    </style>
    </head>
	
	
	

    <div class="bordertestreport">
	
	<?php  $st=getstatebystatecode($st_code);   ?> 
	
	<?php  if (verifyreport_index($st_code,$getIndexCardDataPCWise['pcType']->PC_NO) == 0){ ?>
	
        <table class="table">
          <tr>
                <td style="text-align: left;border-left: 1px solid #000;border-top: 1px solid #000;border-bottom: 1px solid #000;">
                    <p> <img src="<?php echo public_path('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/>  </p>
                </td>
              <td style="text-align: right;border-right: 1px solid #000;border-bottom: 1px solid #000;border-top: 1px solid #000;">

                

                <p style="float: right;width: 100%;font-size: 15px;"><b>SECRETARIAT OF THE <br>ELECTION COMMISSION OF INDIA
</b>
                 <br><b>Nirvachan Sadan, Ashoka Road, New Delhi-110001</b></p>
          </td>
      </tr>
  </table>
        <table>
            
            <tr>
                <td>
                        <div class="col"><h3 style="font-size: 13px;"> <b>Index Card Parliamentary - {{getElectionYear()}}</b></h3></div> 
                </td>
				<td style="text-align: right;"><p style="font-size: 16px;"><b>Draft</td>
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
		
	<?php  } ?> 


<div class="border"></div>

<table>
<tr>
			<td style="width:100%;font-size:16px !important;"><strong></strong></td>
			<td style="width:100%;font-size:16px !important;"><strong></strong></td>
	</tr>
</table>


<section class="">
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col">
							<table>
							<tr>
							<td style="text-align:center; float: center; width: 95%;">
							<p style="float: center; text-align: center; width: 95%;font-size: 12px;"><b>ELECTION INDEX CARD<br/>FOR LOK SABHA ELECTIONS ONLY</b></p>
							</td>
							<td style="text-align: right; float: right; width:5%;">
							<p style="float: center; text-align: right; width: 5%;font-size: 11px;">{{getElectionType($st_code,$pc)}}&nbsp;ELECTION</p>
							</td>
							</tr>
							</table>
							
						</div>						
					</div>
<table>
	
    <tr>
			<td style="width:20%;font-size:15px!important;"><strong>State:</strong> {{$st->ST_NAME}}</td>

        <td style="width:60%;font-size:15px!important"><b>District:</b> {{$getIndexCardDataPCWise['distict_name']}}</td>
		<td style="width:20%;font-size:15px!important;"><b>Year of Election:</b> {{getElectionYear()}}</td>

    </tr>

    <tr>
        <td style="width:70%;font-size:15px!important"><b>Number & Name of Parliamentary Constituency: </b>{{$getIndexCardDataPCWise['pcType']->PC_NO}} : {{$getIndexCardDataPCWise['pcType']->PC_NAME}} </td>
		<td style=""></td>
        <td style="width:30%;font-size:15px!important;float:right;"><b>Type of Constituency:</b> {{$getIndexCardDataPCWise['pcType']->PC_TYPE}}</td>
    </tr>

</table>
					
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

            <div id="menu1" role="tabpanel" class="tab-pane fade in active">
              
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
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_m ? :0 }}</td>
<td colspan="2" style="text-align: center;">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_m +$getIndexCardDataPCWise['t_pc_ic']->vt_nri_m }}</td>


</tr>


<tr>    
<td>2</td>
<td>Female</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_f}}</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_f ? :0 }}</td>
<td colspan="2" style="text-align: center;">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_f + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_f }}</td>


</tr>




<tr>    
<td>3</td>
<td>Third Gender</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_o}}</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_o ? :0}}</td>
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
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->mock_poll_evm ? :0}}</td>
                                            </tr>
                                            
                                            <!--<tr>
                                                <td>3</td>
                                                <td colspan="4">Votes not retrieved from EVM</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->not_retrieved_vote_evm ? :0}}</td>
                                            </tr> -->
											
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
                                    </div>
                         
                             
                            </div>
                        </div> 

            </div>


         <!--    <div style="page-break-after: always;">

               </div>
            menu1 -->

            

            <div id="menu2" class="tab-pane fade">
                <div>

                    <h4 style="text-align: center;">VIII. DETAILS OF VOTES POLLED BY EACH CANDIDATES</h4>
					
					
					<?php $chunk_result = array_chunk($getIndexCardDataCandidatesVotesACWise['allACList'],8, true); 

					
						//echo '<pre>'; print_r($getIndexCardDataCandidatesVotesACWise['candidatedataarray']); die;

						$arrcount =  end($chunk_result);
						
						//echo sizeof($arrcount); die;

						
						foreach($chunk_result as $key => $allACList){			
								?>

                                <br>
                                <br>
					<table class="table new_mid_sec">				
                    
                        <thead>
                            <tr>
                                <th class="border borderss" style="vertical-align: middle;" rowspan="2">SL. No.</th>
                                <th class="border borderss" rowspan="2">Name of Contesting Candidates <br>(in Block letters)</th>
                                <th class="border borderss" rowspan="2">Sex <br> (Male/Female/Third Gender)</th>
                                <th class="border borderss" rowspan="2">Age <br> (Years)</th>
                                 <th class="border borderss" rowspan="2">Category <br>(Gen/SC/ST)</th>
                                <th class="border borderss" rowspan="2">Full Name of the Party</th>
                                <th class="border borderss" rowspan="2">Election Symbol Allotted</th>
                                <th class="border borderss" colspan="{{count($allACList)}}" style="border-right: 1px solid #000;">Valid Votes counted From Electronic Voting Machines </th>
                                <?php if( (count($allACList) <= 5) && ($st_code == 'S09') && ( in_array($getIndexCardDataPCWise['pcType']->PC_NO,[1,2,3]) ) ){ ?>
                                <th class="border borders" style="border-right: 1px solid #000;" colspan="3"></th>
								<?php } elseif(count($allACList) <= 6) { ?>
								<th class="border borders" style="border-right: 1px solid #000;" colspan="2"></th>
								<?php } ?>
							</tr>

                            <tr color="acnamerow">
                                    
									
                                @foreach($allACList as $allACListsKey => $allACListsValue)
								
                                    <th class="border borderss" style="min-width:100px !important;border-right: 1px solid #000; width:200px !important;">{{$allACListsKey}} : {{$allACListsValue}}</th>
                                @endforeach
								
								<?php if((count($allACList) <= 5) && ($st_code == 'S09') && ( in_array($getIndexCardDataPCWise['pcType']->PC_NO,[1,2,3]) ) ){ ?>
									<th class="border borderss">Migrant Votes</th>
									<th class="border borderss">Valid Postal Votes</th>
									<th class="border borderss" style="border-right: 1px solid #000;">Total Valid Votes</th>
								<?php }  else if(count($allACList) <= 6){ ?>
								<th class="border borderss">Valid Postal Votes</th>
                                <th class="border borderss" style="border-right: 1px solid #000;">Total Valid Votes</th> 
								<?php } ?>

                                
                            </tr>
                        </thead>

                        <tbody color="CandidateBodyIDWise">
												
                             <?php $count=1; 
							$dataSum  = array();
							
							$total_valid_postel_votes = 0;
							$total_valid_migrate_votes = 0;
							$total_valid_votes = 0;
							
							$i=0;
							?>
                            @foreach($getIndexCardDataCandidatesVotesACWise['candidatedataarray'] as $candpcdata)
							
                            @foreach($candpcdata as $canddata)
                           

                            <?php ?>
                            <tr>
                                <td class="borderss">{{$count."."}} </td>
                                <td class="borderss">{{$canddata['cand_name']}}</td>
                                <td class="borderss" style="text-align: center;text-transform: capitalize;">{{$canddata['cand_gender']}}</td>
                                <td class="borderss" style="text-transform: capitalize;text-align: center;">{{$canddata['cand_age']}}</td>
                                 <td class="borderss" style="text-align: center;text-transform: uppercase;">{{strtoupper($canddata['cand_category'])}}</td>
                                <td class="borderss">{{$canddata['partyname']}}</td>
                                                           
                                <td class="borderss" style="text-align: center;text-transform: uppercase;">{{$canddata['party_symbol']}}</td>

                                <?php 
								
								
								$sum = 0;
								
								$acdata = array_chunk($canddata['acdata'],8, true);
								
								
								foreach ($acdata[$key] as $key3 => $values) { 
								
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
                                  
                                <td class="borderss" style="border-right: 1px solid #000;">{{$values}}</td>
                                      
                                <?php } ?>
								
								
								
								<?php if((count($allACList) <= 5) && ($st_code == 'S09') && ( in_array($getIndexCardDataPCWise['pcType']->PC_NO,[1,2,3]) ) ){ 								
								$migrate_votes = 0;
								?>								
								<td class="borderss">{{$canddata['migrate_votes']}}</td>								
                                <td class="borderss">{{$canddata['valid_postal_votes']}}</td>
                                <td class="borderss">{{$canddata['total_valid_votes']}}</td>
                                <?php 
								$total_valid_postel_votes += $canddata['valid_postal_votes'];
								$total_valid_migrate_votes += $canddata['migrate_votes'];
								$total_valid_votes += $canddata['total_valid_votes'];
								} else if(count($allACList) <= 6 ){ ?>
                                <td class="borderss">{{$canddata['valid_postal_votes']}}</td>
                                <td class="borderss" style="border-right: 1px solid #000;">{{$canddata['total_valid_votes']}}</td>
								
                                <?php 
								$total_valid_postel_votes += $canddata['valid_postal_votes'];
								$total_valid_votes += $canddata['total_valid_votes'];
								 } ?>
								
								
                            </tr>

                            @endforeach
							<?php $count++; ?>
                            @endforeach
							
							
						<?php 
						$migrate_vote_nota = 0;
						$postal_vote_nota = 0;
						$total_nota = 0;
						if(($st_code == 'S09') && ( in_array($getIndexCardDataPCWise['pcType']->PC_NO,[1,2,3]) ) ){ ?>

							<tr>
							<td  class="borderss">{{$count}}</td>
							<td class="borderss"><b>Nota</b></td>
							<td class="borderss">-</td>
							<td class="borderss">-</td>
							<td class="borderss">-</td>
							<td class="borderss">-</td>
							<td class="borderss">-</td>
							
							<?php $migrate_nota = array_chunk($getIndexCardDataPCWise['migrate_nota'],8, true); ?>
							
							
							@foreach($migrate_nota[$key] as $key4 => $dataValue)
							<td class="borderss" style="border-right: 1px solid #000;">{{$dataValue['total_vote']}}</td>
							
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
							
							
							<?php if(count($allACList) <= 5) { ?>
							<td>{{$getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota}}</td>
							
							<td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota}}</td>
							<td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota}}</td>
						
						<?php

						$migrate_vote_nota = $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota;
						$postal_vote_nota = $getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota;
						$total_nota = $getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota;


						}}  ?>

							</tr>

						<?php// echo '<pre>'; print_r($dataSum); die; ?>

						<tr>
							<td class="border borderss" colspan="7" style="text-align:right;"><b>Total</b></td>
							@foreach($dataSum as $dataValue)
							<td class="border borderss" style="border-right: 1px solid #000;">{{$dataValue}} </td>
							@endforeach
							<?php if((count($allACList) <= 5) && ($st_code == 'S09') && ( in_array($getIndexCardDataPCWise['pcType']->PC_NO,[1,2,3]) ) ){ ?>
									<td class="border borderss">{{$total_valid_migrate_votes + $migrate_vote_nota}}</td>
									<td class="border borderss">{{$total_valid_postel_votes + $postal_vote_nota}}</td>
									<td class="border borderss" style="border-right: 1px solid #000;">{{$total_valid_votes + $total_nota}}</td>
								<?php }  else if(count($allACList) <= 6){ ?>
								<td class="border borderss">{{$total_valid_postel_votes + $postal_vote_nota}}</td>
									<td class="border borderss" style="border-right: 1px solid #000;">{{$total_valid_votes + $total_nota}}</td> 
								<?php } ?>
							</tr>


						
                        </tbody>
                   
					</table>
					<?php } ?>
					
					<?php if(sizeof($arrcount) >= 7) { ?>
					       <br>
                                <br>
					<table class="table new_mid_sec" style="width: 100%;">				
                    
                        <thead>
                            <tr>
                                <th class="border borderss" style="vertical-align: middle;">SL. No.</th>
                                <th class="border borderss">Name of Contesting Candidates <br>(in Block letters)</th>
                                <th class="border borderss">Sex <br> (Male/Female/Third Gender)</th>
                                <th class="border borderss">Age <br> (Years)</th>
                                 <th class="border borderss">Category <br>(Gen/SC/ST)</th>
                                <th class="border borderss">Full Name of the Party</th>
                                <th class="border borderss">Election Symbol Allotted</th>
								
						

                                    
																
								<?php if( ($st_code == 'S09') && ( in_array($getIndexCardDataPCWise['pcType']->PC_NO,[1,2,3]) ) ){ ?>
                           
									<th class="border borderss">Migrant Votes</th>
									<th class="border borderss">Valid Postal Votes</th>
									<th class="border borderss">Total Valid Votes</th>
								<?php }  else { ?>
								<th class="border borderss">Valid Postal Votes</th>
                                <th class="border borderss" style="border-right: 1px solid #000;">Total Valid Votes</th> 
                            
								<?php } ?>
                                
                           </tr>     
                        </thead>
<br>

                        <tbody color="CandidateBodyIDWise">
												
                             <?php $count=1; 
							$dataSum  = array();
							
							$total_valid_postel_votes = 0;
							$total_valid_migrate_votes = 0;
							$total_valid_votes = 0;
							
							$i=0;
							?>
                            @foreach($getIndexCardDataCandidatesVotesACWise['candidatedataarray'] as $candpcdata)
							
                            @foreach($candpcdata as $canddata)
                           

                          
                            <tr>
                                <td class="borderss">{{$count."."}} </td>
                                <td class="borderss" >{{$canddata['cand_name']}}</td>
                                <td class="borderss" style="text-align: center;text-transform: capitalize;">{{$canddata['cand_gender']}}</td>
                                <td class="borderss" style="text-transform: capitalize;text-align: center;">{{$canddata['cand_age']}}</td>
                                 <td class="borderss" style="text-align: center;text-transform: uppercase;">{{strtoupper($canddata['cand_category'])}}</td>
                                <td class="borderss">{{$canddata['partyname']}}</td>
                                                           
                                <td class="borderss" style="text-align: center;text-transform: uppercase;">{{$canddata['party_symbol']}}</td>

								<?php if( ($st_code == 'S09') && ( in_array($getIndexCardDataPCWise['pcType']->PC_NO,[1,2,3]) ) ){ 								
								$migrate_votes = 0;
								?>								
								<td class="borderss">{{$canddata['migrate_votes']}}</td>								
                                <td class="borderss">{{$canddata['valid_postal_votes']}}</td>
                                <td class="borderss">{{$canddata['total_valid_votes']}}</td>
                                <?php 
								$total_valid_postel_votes += $canddata['valid_postal_votes'];
								$total_valid_migrate_votes += $canddata['migrate_votes'];
								$total_valid_votes += $canddata['total_valid_votes'];
								} else { ?>
                                <td class="borderss">{{$canddata['valid_postal_votes']}}</td>
                                <td class="borderss" style="border-right: 1px solid #000;">{{$canddata['total_valid_votes']}}</td>
								
                                <?php 
								$total_valid_postel_votes += $canddata['valid_postal_votes'];
								$total_valid_votes += $canddata['total_valid_votes'];
								 } ?>
								
								
                            </tr>

                            @endforeach
							<?php $count++; ?>
                            @endforeach
							
							
						<?php 
						$migrate_vote_nota = 0;
						$postal_vote_nota = 0;
						$total_nota = 0;
											
						if(($st_code == 'S09') && ( in_array($getIndexCardDataPCWise['pcType']->PC_NO,[1,2,3]) ) ){ ?>

							<tr>
							<td class="borderss">{{$count}}</td>
							<td class="borderss"><b>Nota</b></td>
							<td class="borderss">-</td>
							<td class="borderss">-</td>
							<td class="borderss">-</td>
							<td class="borderss">-</td>
							<td class="borderss">-</td>
							
							<td class="borderss">{{$getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota}}</td>
							
							<td class="borderss">{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota}}</td>
							<td class="borderss" style="border-right: 1px solid #000;">{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota}}</td>
						
						<?php

						$migrate_vote_nota = $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota;
						$postal_vote_nota = $getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota;
						$total_nota = $getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota;


						}  ?>

							</tr>

						<tr>
							<td class="borderss border" colspan="7" style="text-align:right;"><b>Total</b></td>
							<?php if(($st_code == 'S09') && ( in_array($getIndexCardDataPCWise['pcType']->PC_NO,[1,2,3]))){ ?>
							<td class="borderss border">{{$total_valid_migrate_votes + $migrate_vote_nota}}</td>
							<?php } ?>
							<td class="borderss border">{{$total_valid_postel_votes + $postal_vote_nota}}</td>
							<td class="borderss border" style="border-right: 1px solid #000;">{{$total_valid_votes + $total_nota}}</td>
							</tr>


						
                        </tbody>
                   
					</table>
					 
					<?php } ?>
					
					<?php  if (verifyreport_index($st_code,$getIndexCardDataPCWise['pcType']->PC_NO) == 0){ ?>

                        <ul class="list-unstyled" style="list-style: none;text-decoration: none;">
                            <li style="font-size: 12px;">1.&nbsp; Arrange serially contesting candidates in desending order of valid votes polled.</li>
                            <li style="font-size: 12px;">2.&nbsp; If the number of Assembly Segments are more than 8, use additional Cards as per requirements.</li>
                            <li style="font-size: 12px;">3.&nbsp; Indicate names of Recognized and Un-recognized parties as registered with the Election Commission in full.</li>
                        </ul>
					<?php } ?>

                </div>
				
            </div>


            <!-- menu2 


            <div style="page-break-after: always;">

               </div>-->


            <div id="menu3" class="tab-pane fade">
			     <h4 style="text-align: center;">IX. DETAILS OF ELECTORS -ASSEMBLY SEGMENT WISE</h4>
			<div class="col-sm-12">
              
		
                <table class="table table-bordered">
    <thead>
        <tr>
            <th>AC</th>
            <th>AC Name</th>
            <th>Gen Male</th>
            <th>Gen Female</th>
            <th>Gen Third Gender</th>
            <th class="highlit">Total</th>
            <th>Ser Male </th>
            <th>Ser Female</th>
            <th>Ser Third Gender</th>
            <th>Ser Total</th>
            <th>NRI Male</th>
            <th>NRI Female</th>
            <th>NRI Third Gender</th>
            <th>NRI Total</th>
            <th class="highlit">Total Male</th>
            <th class="highlit">Total Female</th>
            <th class="highlit">Total Third Gender</th>
            <th class="highlit">Total</th>
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

                <tr data-acid="{{$k}}">
                    <td color="electorsac[{{$k}}][ac_no]">
                        {{$value['ac_no']}}
                    </td>
                    <td color="electorsac[{{$k}}]['ac_name']">
                        {{$value['ac_name']}}
                    </td>
                    <td contenteditable="true" color="electorsac[{{$k}}][gen_m]" data-category="general" class="male">
                        {{$value['gen_m']}}
                    </td>

                    <td contenteditable="true" color="electorsac[{{$k}}][gen_f]" data-category="general" class="female">
                        {{$value['gen_f']}}
                    </td>


                    <td contenteditable="true" color="electorsac[{{$k}}][gen_o]" data-category="general" class="other">
                        {{$value['gen_o']}}
                    </td>

                    <td color="electorsac[{{$k}}][gen_t]" class="generalTotal{{$k}}" data-category="general">
                        {{$value['gen_t']}}
                    </td>

                    <td contenteditable="true" color="electorsac[{{$k}}][ser_m]" data-category="service" class="male">
                        {{$value['ser_m']}}
                    </td>

                    <td contenteditable="true" color="electorsac[{{$k}}][ser_f]" data-category="service" class="female">
                        {{$value['ser_f']}}
                    </td>

					<td contenteditable="true" color="electorsac[{{$k}}][ser_f]" data-category="service" class="female">
                        {{$value['ser_o']}}
                    </td>
                    <td color="electorsac[{{$k}}][ser_t]" data-category="service" class="serviceTotal{{$k}}">
                        {{$value['ser_t']}}
                    </td>

                    <td contenteditable="true" color="electorsac[{{$k}}][nri_m]" data-category="nri" class="male">
                        {{$value['nri_m']}}
                    </td>

                    <td contenteditable="true" color="electorsac[{{$k}}][nri_f]" data-category="nri" class="female">
                        {{$value['nri_f']}}
                    </td>

                    <td contenteditable="true" color="electorsac[{{$k}}][nri_o]" data-category="nri" class="other">
                        {{$value['nri_o']}}
                    </td>

                    <b><td color="electorsac[{{$k}}][nri_t]" data-category="nri" class="nriTotal{{$k}} highlit">
                        {{$value['nri_t']}} 
                    </td></b>
</b>
                    <td color="electorsac[{{$k}}][tot_m]" class="total-male subtotal highlit">
                        {{$value['tot_m']}}
                    </td></b>

                    <td color="electorsac[{{$k}}][tot_f]" class="total-female subtotal highlit">
                        {{$value['tot_f']}}
                    </td>

                    <td color="electorsac[{{$k}}][tot_o]" class="total-other subtotal highlit">
                        {{$value['tot_o']}}
                    </td>

					<td color="electorsac[{{$k}}][tot_o]" class="total-other subtotal highlit"> 
					{{$value['gen_t'] + $value['nri_t'] + $value['ser_t']}} </td>

                </tr>

                <?php  } ?>
				
				<tr style="text-align:justify;">
				<th colspan="2" class="total-other subtotal highlit">Total</th>
				<td class="total-other subtotal highlit"> {{$t_gen_m}}</td>
				<td class="total-other subtotal highlit"> {{$t_gen_f}}</td>
				<td class="total-other subtotal highlit"> {{$t_gen_o}}</td>
                <td> {{$t_gen_t}}</td>
                <td> {{$t_ser_m}}</td>
                <td> {{$t_ser_f}}</td>
                <td> {{$t_ser_o}}</td>
				<td> {{$t_ser_t}}</td>
				<td> {{$t_nri_m}}</td>
				<td> {{$t_nri_f}}</td>
				<td> {{$t_nri_o}}</td>
				<td> {{$t_nri_t}}</td>
                <td> {{$t_tot_m}}</td>
				<td> {{$t_tot_f}}</td>
				<td> {{$t_tot_o}}</td>
				<td> {{$t_gen_t + $t_nri_t + $t_ser_t}} </td>
				</tr>
    </tbody>

</table>
                        
 
</div>


<?php  if (verifyreport_index($st_code,$getIndexCardDataPCWise['pcType']->PC_NO) == 0){ ?>

<p class="" style="font-size: 12px;position: relative;margin-bottom: 23px;">&nbsp;Certified that the Election Index Card has been checked with Forms 3A, 4, 7A, 20 and 21C or 21D or 21E and R.O.'s &nbsp;Reports etc. and that there is no discrepancy.<br>&nbsp;Further it is certified that the Party Affiliations and symbols alloted have been verified from the list of contesting &nbsp;candidates in Forms 7 A.</p>


</div>




                 <table class="table">
                        <tr>
                            <td class="borders" style="border-right: 1px solid #000;border-left: 1px solid #000;">Date : </td>
                            <td class="borders" style="border-right: 1px solid #000;">Date of Press Note : {{date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->DT_PRESS_ANNC))}} </td>
                            <td class="borders" style="border-right: 1px solid #000;">Signature : </td>
                        </tr>
<tr>
    <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
    <td style="border-right: 1px solid #000;"></td>
    <td style="border-right: 1px solid #000;"></td>
</tr>
<tr>
    <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
    <td style="border-right: 1px solid #000;"></td>
    <td style="border-right: 1px solid #000;"></td>
</tr>
<tr>
    <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
    <td style="border-right: 1px solid #000;"></td>
    <td style="border-right: 1px solid #000;"></td>
</tr>
<tr>
    <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
    <td style="border-right: 1px solid #000;"></td>
    <td style="border-right: 1px solid #000;"></td>
</tr>
<tr>
    <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
    <td style="border-right: 1px solid #000;"></td>
    <td style="border-right: 1px solid #000;"></td>
</tr>
<tr>
    <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
    <td style="border-right: 1px solid #000;"></td>
    <td style="border-right: 1px solid #000;"></td>
</tr>

<tr>
    <td style="border-right: 1px solid #000;border-left: 1px solid #000;"></td>
    <td style="border-right: 1px solid #000;"></td>
    <td style="border-right: 1px solid #000;"></td>
</tr>



    <tr>
        <td class="border" style="border-right: 1px solid #000;border-left: 1px solid #000;">(Signature & Seal) Cheif Electoral Officer</td>
        <td class="border" style="border-right: 1px solid #000;">Date of Notification : {{date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->DT_ISS_NOM))}}</td>
        <td class="border" style="border-right: 1px solid #000;">(Seal)Returning Officer</td>
    </tr>
                    </table>       
<!-- menu3 -->

            </div>
        </div>

        <!--tabs ends-->
<div class="row">
    <div class="col-sm-10 col-offset-sm-1 pull-right">
        
    </div>
</div>
    </div>
</div>

<table>
      <tr style="width: 100%;">
  
  <td colspan="6" style="text-align: center;"><p><b style="font-size: 15px;">Nirvachan Sadan, Ashoka Road, New Delhi- 110001</b></p></td>
</tr>


</table>
<?php } ?>

<p style="border-top: 2px solid #000;padding-top: 8px;">
<span style="font-weight: bold;">Disclaimer</span><br/><br/>
<span style="font-size: 12px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</span></p>

</div> <!-- end grids -->
</div>
<!-- End Wrapper-->

</div>
			</div>
		</div>
	</div>
</section>


