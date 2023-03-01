@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'PC Wise Voters Turn Out')
@section('content')

<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(PC Wise Voters Turn Out)</h4></div> 
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
							</p>
							<p class="mb-0 text-right">
							<a href="downloadpcwisevoterturnout" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="#" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>
				
				<div class="card-body">
					<div class="table-responsive">
						    <table class="table table-bordered table-striped" style="width: 100%;">
							
							@php 
							$sl_no = 1;
							@endphp
							
							@foreach($pcwisevoterturnouts as $key=>$row)
							
							
                            <thead>
                                <tr>
                                    <th colspan="2" style="font-size: 17px;">State : <span style="color: #fff; font-style: normal;font-weight: bold; text-decoration: underline;"> {{$key}}</span> </th>
                                </tr>
                                <tr class="table-primary">
                                    <th colspan="4"> </th>
                                   
                                    <th colspan="6">Voters </th>
                                    <th> Voter Turn Out%</th>
                                    <th colspan="3">Voter Turn Out (Excl. Postal) % </th>
                                </tr>
								
                                <tr>
                                    <th> </th>
                                    <th> </th>
                                    <th> </th>
                                    <th> </th>
                                    <th colspan="4"> EVM </th>
                                    <th> Postal Votes </th>
                                    <th> Total Votes </th>
                                    <th> </th>
                                    <th> Male </th>
                                    <th> Female </th>
                                    <th> Other</th>
                                </tr>
                                <tr>
                                    <th> SL. NO. </th>
                                    <th> PC Name </th>
                                    <th> PC No. </th>
                                    <th> Electors </th>
                                    <th> Male </th>
                                    <th> Female </th>
                                    <th> Other </th>
                                    <th> Total </th>
                                    <th colspan="2"> </th>
                                    <th> </th>
                                    <th colspan="3"> </th>
                                    
                                </tr>
                            </thead>
                            <tbody>
							
							@php
								$total_electors = 0;
								$total_voters_male = 0;
								$total_voters_female = 0;
								$total_voters_other = 0;
								$total_voters_all = 0;
								$total_voters_alltotal = 0;
								$total_voter_turn_all = 0;
								$total_voter_turn_male = 0;
								$total_voter_turn_female = 0;
								$total_voter_turn_other = 0;
							
							@endphp
							
								@foreach($row as $no => $value)
								
								@php 
								
								if($value['electors_total'] > 0)
									$voter_turn_all = round((($value['voter_total']/$value['electors_total'])*100),2);
								else
									$voter_turn_all = 0;
								
								
								if($value['electors_male'] > 0)
									$voter_turn_male = round((($value['voter_male']/$value['electors_male'])*100),2);
								else
									$voter_turn_all = 0;


								if($value['electors_female'] > 0)
									$voter_turn_female = round((($value['voter_female']/$value['electors_female'])*100),2);
								else
									$voter_turn_all = 0;
								
								
								if($value['electors_other'] > 0)
									$voter_turn_other = round((($value['voter_other']/$value['electors_other'])*100),2);
								else
									$voter_turn_all = 0;
								
								$total_electors += $value['electors_total'];
								$total_voters_male += $value['voter_male'];
								$total_voters_female += $value['voter_female'];
								$total_voters_other += $value['voter_other'];
								$total_voters_all += $value['voter_total'];
								$total_voters_alltotal += $value['voter_total'];
								$total_voter_turn_all += $voter_turn_all;
								$total_voter_turn_male += $voter_turn_male;
								$total_voter_turn_female += $voter_turn_female;
								$total_voter_turn_other += $voter_turn_other;
																
								@endphp

                                <tr>
                                    <td>{{$sl_no}}.</td>
                                    <td>{{$value['PC_NAME']}}</td>
                                    <td>{{$value['PC_NO']}}</td>
                                    <td>{{$value['electors_total']}}</td>
                                    <td>{{$value['voter_male']}}</td>
                                    <td>{{$value['voter_female']}}</td>
                                    <td>{{$value['voter_other']}}</td>
                                    <td>{{$value['voter_total']}}</td>
                                    <td>NA</td>
                                    <td>{{$value['voter_total']}}</td>
                                    <td>
										{{$voter_turn_all}}
									</td>
									<td>
										{{$voter_turn_male}}
									</td>
                                    <td>
										{{$voter_turn_female}}
									</td>
                                    <td>
										{{$voter_turn_other}}
									</td>
                                </tr>
								
									@php 
									$sl_no++;
									@endphp
								@endforeach
                                
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td>{{$total_electors}}</td>
                                    <td>{{$total_voters_male}}</td>
                                    <td>{{$total_voters_female}}</td>
                                    <td>{{$total_voters_other}}</td>
                                    <td>{{$total_voters_all}}</td>
									<td>NA</td>
                                    <td>{{$total_voters_alltotal}}</td>
                                    <td>{{round($total_voter_turn_all/($no+1),2)}}</td>
                                    <td>{{round($total_voter_turn_male/($no+1),2)}}</td>
                                    <td>{{round($total_voter_turn_female/($no+1),2)}}</td>
                                    <td>{{round($total_voter_turn_other/($no+1),2)}}</td>
                                </tr>
                                
								
                            </tbody>
							@endforeach
							
							
                        </table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection