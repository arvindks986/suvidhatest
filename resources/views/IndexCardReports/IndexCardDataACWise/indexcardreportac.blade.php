@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Index Card Report')

@section('bradcome', 'Index Card Ac Wise')

@section('content')




<style>
#index_cus_ch ul li.active {
    background: #17a2b8;
}

.card-header h3 {
    font-size: 18px;
}

#index_cus_ch ul li {
    background: #4b4a4a;
    padding: 7px;
    margin-right: 2px;
}

#index_cus_ch ul.nav.nav-tabs {
    margin-bottom: 11px;
}
.card-header span {
    font-size: 13px;
}

table {
    font-size: .9em;
}


#index_cus_ch ul li:hover {
    background: #F0587E;
}

#index_cus_ch ul li:hover a {
color: #fff;
text-decoration: none;
}

#index_cus_ch ul li a {
color: #fff;
font-size: 14px;
}

select#partyList764 {
    width: 200px;
}

#menu2 select{
width: 148px;    
}


#index_cus_ch th {
    background: #F0587E;
    color: #fff;
    font-size: 14px;
    font-weight: 500;
}

#index_cus_ch td{
        font-size: 13px;
        vertical-align: middle;

}

.tab-content{
    width: 100%;
}
input.form-control{
        font-size: 13px;

}
#index_cus_ch ul li.active a {
    text-decoration: none;
    color: #fff;
}


</style>
<?php  $st=getstatebystatecode($st_code);   ?> 
<?php if(Auth::user()->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if(Auth::user()->role_id == '7'){
			$prefix 	= 'eci';
		}else if(Auth::user()->designation == 'CEO'){
			$prefix 	= 'acceo';
		}   ?>
<section class="">
	<div class="container-fluid">
		<div class="row">
		
			@if(Auth::user()->role_id == '27' || Auth::user()->role_id == '7' || Auth::user()->designation == 'CEO')
			<div class="card-body" style="border:3px solid #eee;">
                    <div class="table-responsive">
                        <!-- Content goes Here -->
                        <div class="col-sm-12 text-center ac_sec">
                            <h5 class="p-1" style="text-decoration:underline;"><b>Select State And AC</b></h5>
                        </div>
						
					<form id="generate_report_id" class="form form-inline" method="GET" action="{!! url(''.$prefix.'/index-card') !!}">




					<div class="row" style="margin:0px;width:100%;">
							<div class="col-md-4"> 
						  
							<select class="form-control" style="width:100%;" name="st_code" id="st_code" placeholder="Select State" onChange="getAC(this.value);" required>
							<option value="">Select State</option>
							@foreach($stateList as $stateLists)
								<option value="{{$stateLists->ST_CODE}}" <?php if(($st_code != null) && ($st_code == $stateLists->ST_CODE)){ ?> selected <?php } ?>  >{{$stateLists->ST_NAME}}</option>
							@endforeach
						  </select>
						  </div>

						  <div class="col-md-4">						  
							<select class="form-control" style="width:100%;" name="ac_no" id="ac" placeholder="Select AC" required>
							 <option value="">Select AC</option>    
							 @foreach($acList as $acLists)
								<option value="{{$acLists->AC_NO}}" @if(isset($_GET['ac_no']) && ($_GET['ac_no']== $acLists->AC_NO)) selected @endif >{{$acLists->AC_NO.'-'.$acLists->AC_NAME}}</option>
							@endforeach
						  </select>
						  
						  </div>
						  
						  <div class="col-md-2" style="text-align:center;"> 
							<button type="submit" class="btn btn-info btn-lg" style="">Show</button>
							
						  </div>
						 </div>
						</form><br>
                       
                        <!-- Content ends Here -->
                    </div>
                </div>
			@endif
		
		@if($st_code && $ac)
		
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h3> Index Card Assembly - 2019</h3></div> 

						@if($getIndexCardDataACWise['finalize_by_ro']== 1)
							<div class="center" style="color:#F0587E;font-size:20px;"><b> Finalized By ARO</b> ({{date('d-m-Y', strtotime($getIndexCardDataACWise['finalize_by_ro_date']))}})</div>
						@else
							<div class="center" style="color:#F0587E;font-size:20px;"><b> Not Finalized By ARO</b></div>
						@endif

						@if($getIndexCardDataACWise['finalize_by_ceo']== 1)
							<div class="center" style="color:#F0587E;font-size:20px;"><b> , Finalized By CEO</b> ({{date('d-m-Y', strtotime($getIndexCardDataACWise['finalize_by_ceo_date']))}})</div>
						@else
							<div class="center" style="color:#F0587E;font-size:20px;"><b> , Not Finalized By CEO</b></div>
						@endif
						
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
							</p>
						</div>					
					</div>
										
					<div class="row" style="margin-top:2%;">
					
					<div class="col">
							<p class="mb-0 text-left"><b class="bolt">Type of Constituency:</b> <span > {{$acinfo->AC_TYPE}}</span> &nbsp;&nbsp; 
							</p>
						</div>
						
						<div class="col">
							<p class="mb-0 text-center"><b class="bolt">Number & Name of AC:</b> <span >{{$acinfo->AC_NO}}-{{$acinfo->AC_NAME}} 
							</p>
						</div>
						
						<div class="col col" style="margin-right:20px;">
							<p class="mb-0 text-right"><b class="bolt">District: {{$acinfo->DIST_NAME_EN}}</b> <span >
							</p>
						</div>




        </div>
					
					
				</div>
				
				<div class="card-body">

<div class="wapper">
    <div class="grids"> 
    <div class="whole">
    

<?php //echo "<pre>"; print_r($getIndexCardDataACWise); die; ?>

    <div class="container-fluid">

    

        </div>
    

@if(Auth::user()->designation == 'CEO')

     <a href='{{url("acceo/indexcardacpdf/$ac")}}' target="_blank" class="btn btn-primary pdfbt" style="position: absolute; right: 35px;">Download PDF</a>  


@elseif(Auth::user()->role_id == '7')

     <a href='{{url("eci/indexcardacpdf/$ac/$st_code")}}' target="_blank" class="btn btn-primary" style="position: absolute; right: 0px;z-index:999;top:-42px;">Download PDF</a>  	
	 
@elseif(Auth::user()->role_id == '27')

     <a href='{{url("eci-index/indexcardacpdf/$ac/$st_code")}}' target="_blank" class="btn btn-primary" style="position: absolute; right: 0px;z-index:999;top:-42px;">Download PDF</a>  	 
		
@else
	 <a href='{{url("roac/indexcardacpdf")}}' target="_blank" class="btn btn-primary" style="position: absolute; right: 35px;">Download PDF</a>
@endif


    <!--Basic Information-->
    <!--Start row-->
      <div class="row" id="index_cus_ch">
        <!--tabs starts-->

        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a data-toggle="tab" href="#menu1">Data For Election Card Index</a></li>
            <li role="presentation"><a data-toggle="tab" href="#menu2">Information About Candidate in AC</a></li>
            <!--<li><a href="changeRequest">Edit Request</a></li>
            <li><a href="finaliserequest">Finalise Request</a></li>-->
        </ul>
		
		
 <div class="tab-content" style="overflow: auto;">

            <div id="menu1" role="tabpanel" class="tab-pane fade in active">
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
                                        

										<tr>
											<td>1</td>
                                                <td>Nominated </td>
                                                <td>{{$getIndexCardDataACWise['c_nom_m_t']}}</td>
                                                <td>{{$getIndexCardDataACWise['c_nom_f_t']}}</td>
                                                <td>{{$getIndexCardDataACWise['c_nom_o_t']}}</td>
                                                <td>{{$getIndexCardDataACWise['c_nom_a_t']}}</td>
                                            </tr>
                                            
                                            <tr>
											<td>2</td>
                                                <td>Nominations  Rejected</td>
                                                <td>{{$getIndexCardDataACWise['c_nom_r_m']}}</td>
                                                <td>{{$getIndexCardDataACWise['c_nom_r_f']}}</td>
                                                <td>{{$getIndexCardDataACWise['c_nom_r_o']}}</td>
                                                <td>{{$getIndexCardDataACWise['c_nom_r_a']}}</td>
                                            </tr>
                                            
                                            
                                            <tr>
											<td>3</td>
                                                <td>Withdrawn</td>
                                                <td>{{$getIndexCardDataACWise['c_nom_w_m']}}</td>
                                                <td>{{$getIndexCardDataACWise['c_nom_w_f']}}</td>
                                                <td>{{$getIndexCardDataACWise['c_nom_w_o']}}</td>
                                                <td>{{$getIndexCardDataACWise['c_nom_w_t']}}</td>
                                            </tr>
                                            
                                            <tr>
											<td>4</td>
                                                <td> Contested </td>
                                                <td>{{$getIndexCardDataACWise['c_nom_co_m']}}</td>
                                                <td>{{$getIndexCardDataACWise['c_nom_co_f']}}</td>
                                                <td>{{$getIndexCardDataACWise['c_nom_co_o']}}</td>
                                                <td>{{$getIndexCardDataACWise['c_nom_co_t']}}</td>
                                            </tr>
                                            
                                           <tr>
										   <td>5</td>
                                               <td>Deposit Forfeited </td>
                                               <td>{{$getIndexCardDataACWise['c_nom_fd_m']}}</td>
                                               <td>{{$getIndexCardDataACWise['c_nom_fd_f']}}</td>
                                               <td>{{$getIndexCardDataACWise['c_nom_fd_o']}}</td>
                                               <td>{{$getIndexCardDataACWise['c_nom_fd_t']}}</td>
                                           </tr>
                                            
                                            <tr>
											<th>II</th>
                                                <th>ELECTORS</th>
                                                <th colspan="2">GENERAL</th>
                                                <th>SERVICE</th>
                                                <th>TOTAL</th>
                                                
                                            </tr>
											
											<tr>
											<td></td>
											<td colspan=""></td>
											
											<th>Other than NRIs</th>
											<th>NRIs</th>
											</tr>
                                            
                                            <tr>
											<td>1</td>
                                                <td>Male</td>
                                                <td>{{ $getIndexCardDataACWise['e_gen_m'] }}</td>
                                                <td>{{ $getIndexCardDataACWise['e_nri_m'] }}</td>
                                                <td>{{ $getIndexCardDataACWise['e_ser_m'] }}</td>
                                                <td>{{ $getIndexCardDataACWise['e_all_t_m'] }}</td>
                                            </tr>
                                            
                                            <tr data-parent="e" data-category="nri">
											<td>2</td>
                                                <td>Female</td>
                                                <td>{{$getIndexCardDataACWise['e_gen_f']}}</td>
                                                <td>{{$getIndexCardDataACWise['e_nri_f']}}</td>
                                                <td>{{$getIndexCardDataACWise['e_ser_f']}}</td>
                                                <td>{{$getIndexCardDataACWise['e_all_t_f']}}</td>
                                            </tr>
                                            
                                            
                                            <tr>
											<td>3</td>
                                                <td>Third Gender(Not applicable to Service electors)</td>
                                                <td>{{$getIndexCardDataACWise['e_gen_o']}}</td>
                                                <td>{{$getIndexCardDataACWise['e_nri_o']}}</td>
                                                <td>{{$getIndexCardDataACWise['e_ser_o']}}</td>
                                                <td>{{$getIndexCardDataACWise['e_all_t_o']}}</td>
                                            </tr>
                                            <tr>
											<td>4</td>
                                                <td>Total </td>
                                                <td>{{$getIndexCardDataACWise['e_gen_t']}}</td>
                                                <td>{{$getIndexCardDataACWise['e_nri_t']}}</td>
                                                <td>{{$getIndexCardDataACWise['e_ser_t']}}</td>
                                                <td>{{$getIndexCardDataACWise['e_all_t']}}</td>
                                            </tr>


                                            <tr>
											<th>III</th>
											
                                                <th>VOTERS TURNED UP FOR VOTING</th>
                                                <th colspan="2">GENERAL</th>
                                               
                                                <th colspan="2" style="text-align:center;">TOTAL</th>
                                                
                                            </tr>
											
											<tr>
											<td></td>
											<td colspan=""></td>
											<th>Other than NRIs</th>
											<th>NRIs</th>
											
											
											</tr>
                                            
                                            <tr>
											<td>1</td>
                                                <td>Male</td>
                                                <td>{{ $getIndexCardDataACWise['vt_gen_m'] }}</td>
												<td>{{$getIndexCardDataACWise['vt_nri_m']}}</td>
												<td  colspan="2" style="text-align:center;">{{$getIndexCardDataACWise['vt_m_t']}}</td>
                                            </tr>
                                            
                                            <tr data-parent="e" data-category="nri">
											<td>2</td>
                                                <td>Female</td>
                                                <td>{{$getIndexCardDataACWise['vt_gen_f']}}</td>
                                                <td>{{$getIndexCardDataACWise['vt_nri_f']}}</td>
                                                <td   colspan="2" style="text-align:center;">{{$getIndexCardDataACWise['vt_f_t']}}</td>
                                            </tr>
                                            
                                            
                                            <tr>
											<td>3</td>
                                                <td>Third Gender</td>
                                                <td>{{$getIndexCardDataACWise['vt_gen_o']}}</td>
                                                <td>{{$getIndexCardDataACWise['vt_nri_o']}}</td>
                                                <td   colspan="2" style="text-align:center;">{{$getIndexCardDataACWise['vt_o_t']}}</td>
                                            </tr>
                                            <tr>
											<td>4</td>
                                                <td>Total(Male + Female + Third Gender) </td>
                                                <td>{{$getIndexCardDataACWise['vt_gen_t']}}</td>
                                                <td>{{$getIndexCardDataACWise['vt_nri_t']}}</td>
                                                <td colspan="2" style="text-align:center;">{{$getIndexCardDataACWise['vt_all_t']}}</td>
                                            </tr>

                                        <tr>
												<th>IV</th>
                                                <th colspan="5">DETAILS OF VOTES POLLED ON EVM</th>
                                            </tr>
                                            <tr>
											<td>1</td>
                                                <td colspan="4"> Total votes polled on EVM</td>
                                                <td>{{$getIndexCardDataACWise['t_votes_evm']}}</td>
                                            </tr>
                                            <tr>
												<td>2</td>

                                                <td colspan="4">Test votes under Rule 49 MA</td>
                                                <td>{{$getIndexCardDataACWise['mock_poll_evm']}}</td>
                                            </tr>
                                            
                                            <tr>
											<td>3</td>
                                                <td colspan="4"> Votes not retrieved from EVM</td>
                                                <td>{{$getIndexCardDataACWise['not_retrieved_vote_evm']}}</td>
                                            </tr>
                                            
                                            <tr>
											<td>4</td>
                                                <td colspan="4">Rejected votes (due to other reasons)</td>
                                                <td>{{$getIndexCardDataACWise['r_votes_evm']}}</td>
                                            </tr>
                                            
                                            
                                             <tr>
											 <td>5</td>
                                                <td colspan="4">Votes polled for 'NOTA' on EVM</td>
                                                <td>{{$getIndexCardDataACWise['nota_vote_evm']}}</td>
                                            </tr>
                                            
                                            <tr>
											<td>6</td>
                                                <td colspan="4">Total of test votes + votes not retrieved + votes rejected (due to other reasons) + 'NOTA'[2+3+4+5]</td>
                                                <td>{{$getIndexCardDataACWise['all_reject_on_evm']}}</td>
                                            </tr>
                                            
                                            
                                            <tr>
											<td>7</td>
                                                <td colspan="4">Total valid votes counted from EVM [1-6]</span></td>
                                                <td>{{$getIndexCardDataACWise['v_votes_evm_all']}}</td>
                                            </tr>



                                      <tr>
									  <th>V</th>
                                                <th colspan="5">DETAILS OF POSTAL VOTES</th>
                                            </tr>
                                            <tr>
											<td>1</td>
                                                <td colspan="4">Postal votes counted for service voters under sub-section (8) of Section 20 of R.P.Act, 1950</span></td>
                                                <td>{{$getIndexCardDataACWise['postal_vote_ser_u']}}</td>
                                            </tr>
                                            <tr>
											<td>2</td>
                                                <td colspan="4">Postal votes counted for Govt. servants on election duty (including all police personnel, driver, conductors, cleaner</td>
                                                <td>{{$getIndexCardDataACWise['postal_vote_ser_o']}}</td>
                                            </tr>
                                             <tr>
											 <td>3</td>
                                                <td colspan="4"> Postal votes rejected</td>
                                                <td class="dev" colspan="1">{{$getIndexCardDataACWise['postal_vote_rejected']}}</td>
                                            </tr>
                                            
                                            
                                            
                                            <tr>
											<td>4</td>
                                                <td colspan="4">Postal votes polled for 'NOTA'</td>
                                                <td>{{$getIndexCardDataACWise['postal_vote_nota']}}</td>
                                            </tr>
                                           
                                           
                                           <tr>
										   <td>5</td>
                                                <td colspan="4">Total of postal votes rejected + NOTA [3+4]</td>
                                                <td>{{$getIndexCardDataACWise['postal_vote_r_nota']}}</td>
                                            </tr>
                                           
                                           
                                            <tr>
											<td>6</td>
                                                <td colspan="4">Total valid postal votes [1+2-5] </td>
                                                <td>{{$getIndexCardDataACWise['postal_valid_votes']}}</td>
                                            </tr>




          
                                             <tr>
											 <th>VI</th>
                                                <th colspan="5">COMBINED DETAILS OF EVM & POSTAL VOTES</th>
                                            </tr>
                                            <tr>
											<td>1</td>
                                                <td colspan="4"> Total votes polled [IV(1) + V(1+2)] </td>
                                                <td class="dev">{{$getIndexCardDataACWise['total_votes_polled']}}</td>
                                            </tr>
                                            <tr>
											<td>2</td>
                                                <td colspan="4">Total of test votes + votes not retrieved + votes rejected + 'NOTA'[IV(6) + V(5)]</td>
                                                <td>{{$getIndexCardDataACWise['total_not_count_votes']}}</td>
                                            </tr>
                                            <tr>
											<td>3</td>
                                                <td colspan="4">Total valid votes [IV(7)+ V(6)]</td>
                                                <td>{{$getIndexCardDataACWise['total_valid_votes']}}</td>
                                            </tr>
                                            
                                            <tr>
											<td>4</td>
                                                <td colspan="4"> Total votes polled for 'NOTA'[IV(5) + V(4)]</td>
                                                <td>{{$getIndexCardDataACWise['total_votes_nota']}}</td>
                                            </tr>


                                            <tr>
											<th>VII</th>
                                                <th colspan="5">MISCELLANEOUS</th>
                                            </tr>
                                            
                                            <tr>
											<td>1</td>
                                                <td colspan="4">Proxy votes</td>
                                                <td colspan="1">{{$getIndexCardDataACWise['proxy_votes']}}</td>
                                            </tr>
                                            <tr>
											<td>2</td>
                                                <td colspan="4">Tendered votes</td>
                                                <td colspan="1">{{$getIndexCardDataACWise['tendered_votes']}}</td>
                                            </tr>
                                            
                                            <tr>
											<td>3</td>
                                                <td colspan="4">Total number of polling station set up in a Constituency</td>
                                                <td colspan="1">{{$getIndexCardDataACWise['total_no_polling_station']}}</td>
                                            </tr>
                                            <tr>
											<td>4</td>
                                                <td colspan="4">Averages number of Electors assigned to a polling station</td>
                                                <td colspan="1">{{$getIndexCardDataACWise['avg_elec_polling_stn']}}</td>
                                            </tr>
                                            <tr>
											<td>5</td>
                                                <td colspan="4">Date(s) Of Poll</td>
                                                <td colspan="1">
													{{date('d-m-Y', strtotime($getIndexCardDataACWise['dt_poll']))}}
												</td>
                                            </tr>
                                          
											<tr>
											<td>6</td>
                                                <td colspan="4">Date(s) Of Re-Poll,(if any)</td>
                                                <td colspan="1">
												
												@if (trim($getIndexCardDataACWise['date_of_repoll']) != 0 && $getIndexCardDataACWise['date_of_repoll'])
													
												<?php 
													$repoll_dates 	= explode(',',$getIndexCardDataACWise['date_of_repoll']);
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
                                                <td colspan="4">Number of Polling stations where Re-poll was ordered(mention date of order also)</td>
                                                <td colspan="1">
													@if($getIndexCardDataACWise['dt_poll_reasion'])
													{{$getIndexCardDataACWise['dt_poll_reasion']}}
													@else
														NA
													@endif
												</td>
                                            </tr>
                                          
										  

										  <tr>
											<td>8</td>
                                                <td colspan="4">Date(s) Of counting</td>
                                                <td colspan="1">{{date('d-m-Y', strtotime($getIndexCardDataACWise['dt_counting']))}}</td>
                                            </tr>
                                            <tr>
											<td>9</td>
											
                                                <td colspan="4">Date Of Declaration Of result</td>
                                                <td colspan="1">{{date('d-m-Y', strtotime($getIndexCardDataACWise['dt_declare']))}}</td>
                                            </tr>
                                            <tr>
											<td>10</td>
                                                <td colspan="4">Whether this is Bye election or Countermanded election?</td>
                                                <td class="dev" colspan="1">
													@if ($getIndexCardDataACWise['flag_bye_counter'] == 1)
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
													@if ($getIndexCardDataACWise['flag_bye_counter_reason'])
														{{$getIndexCardDataACWise['flag_bye_counter_reason']}}
													@else
														NA
													@endif
                                                </td>
                                            </tr>
                                           
                                    </table>
                                    </div>
                             
                             
                            </div>
                        </div> 

            <!-- menu1 -->

            <div id="menu2" class="tab-pane fade">
                <div class="table-responsive">
                   
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>SL. No.</th>
                                <th>Name of the <br> Contesting Candidates <br/>(in Block Letters)</th>
                                <th>Sex <br>(Male/Female/<br>Third Gender)</th>
                                <th>Age(Years)</th>
                                 <th>Category <br>(Gen./SC/ST)</th>
                                <th>Full name of <br> the Party</th>
                                <th>Election Symbol <br>Alloted</th>
                                <th colspan="3" style="text-align:center;">Valid Votes Polled</th>
                               
                                                              
                                
                            </tr>
							
							
							<tr>
							<th colspan="7"></th>
							<th>Counted from EVM</th>
							<th>Postal</th>
							<th>Total</th>
							
							</tr>

                           
                        </thead>

                        <tbody color="CandidateBodyIDWise">
                            <?php $count=1; ?>
                            <?php $total_votes = $total_postel_votes = 0; ?>
                            @foreach($getIndexCardDataCandidatesVotesACWise as $canddata)
                            <?php //echo "<pre>"; print_r($canddata); die; ?>
                            <tr>
                                <td>{{$count."."}} </td>
                                <td >{{$canddata->cand_name}}</td>
                                <td style="text-transform:capitalize;">{{$canddata->cand_gender}}</td>
                                <td>{{$canddata->cand_age}}</td>
                                 <td>{{$canddata->cand_category}}</td>
                                <td>{{$canddata->PARTYNAME}}</td>
                                <td>{{$canddata->SYMBOL_DES}}</td>
								<td>{{$canddata->total_vote - $canddata->postalballot_vote}}</td>
                                 <td>{{$canddata->postalballot_vote}}</td>                                
                                <td>{{$canddata->total_vote}}</td>
                               <?php $total_votes += $canddata->total_vote;
							   $total_postel_votes += $canddata->postalballot_vote; ?>
                                
                            </tr>

                            <?php $count++; ?>
                            @endforeach
							
							<tr>
								<td colspan="7" style="text-align:right"><b>TOTAL</b></td>
								<td>{{$total_votes - $total_postel_votes}}</td>
								<td>{{$total_postel_votes}}</td>
								<td>{{$total_votes}}</td>
							
							</tr>
                        </tbody>
                    </table>
                </div>

            </div>


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
</div> <!-- end grids -->
</div>
<!-- End Wrapper-->

</div>

@endif
			</div>
		</div>
	</div>
</section>


@endsection
@section('script')
<script src="jquery-3.2.1.min.js" type="text/javascript"></script>
<script>
function getAC(val) {
	$.ajax({
	type: "GET",
	url: "ajaxpccall",
	data:'st_code='+val,
	success: function(data){
		$("#ac").html(data);
	}
	});
}
</script>
<script type="text/javascript">

$(document).ready(function(){
        $('input').each(function(){
            var value = $(this).val();
            if(value == '' || value == null || value == undefined){
                $(this).val(0);
            }
        })
    })
$(document).ready(function(){
    $('a[href="#menu1"]').trigger('click');
})
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  $(this).parent().attr('class','active show');
})
$('a[data-toggle="tab"]').on('hidden.bs.tab', function (e) {
  $(this).parent().removeAttr('class');
})

$(".updatepcwisedata input").removeClass("form-control");


$(".updatepcwisedata input").replaceWith(function () {
 return '<span class="'+this.className+'">'+this.value+'</span>';
});

</script>


@endsection