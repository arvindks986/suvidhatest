@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'PC Wise Voters Turn Out')
@section('content')

<section class="">
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>13 - PC Wise Voters Turn Out</h4></div> 
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
							</p>
							<p class="mb-0 text-right">
							<a href="pcwisevoterturnout_pdf" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="pcwisevoterturnout_xls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
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
							
							
							
							@php
								$grand_total_electors = 0;
								$grand_total_voters_male = 0;
								$grand_total_voters_female = 0;
								$grand_total_voters_other = 0;
								$grand_total_voters_all = 0;
								$grand_total_voters_nri = 0;
								$grand_total_postal_all = 0;
								$grand_total_voters_alltotal = 0;
								$grand_total_male_electors = 0;
								$grand_total_female_electors = 0;
								$grand_total_other_electors = 0;
							
							@endphp
							
							
							@foreach($pcwisevoterturnouts as $key=>$row)
							
							
                            <thead>
                                <tr>
                                	<tr style="height: 10px;"></tr>
                                    <th colspan="4" style="font-size: 17px;">State : <span style="color: #fff; font-style: normal;font-weight: bold; text-decoration: underline;"> {{$key}}</span> </th>
                                </tr>
                                <tr class="table-primary">
                                    <th colspan="4"> </th>
                                   
                                    <th colspan="7" style="text-align: center;">Voters </th>
                                    <th rowspan="3"> Voter <br> Turn Out <br> (%)</th>
                                    <th colspan="3">Voter Turn Out (Excl. Postal) % </th>
                                </tr>
								
                                <tr>
                                    <th colspan="4"> </th>
                                    <th colspan="5" style="text-align: center;"> EVM </th>
                                    <th rowspan="2"> Postal <br> Votes </th>
                                    <th rowspan="2"> Total <br> Votes </th>
                                    <th rowspan="2"> Male </th>
                                    <th rowspan="2"> Female </th>
                                    <th rowspan="2"> Third Gender</th>
                                </tr>
                                <tr>
                                    <th style="width: 20%;"> SL. NO. </th>
									<th> PC No. </th>
                                    <th> PC Name </th>                                    
                                    <th> Electors </th>
                                    <th> Male </th>
                                    <th> Female </th>
                                    <th> Third Gender </th>
                                    <th> Total </th>
                                    <th>NRI</th>
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
								$total_voters_nri = 0;
								$total_postal_all = 0;
								$total_voters_alltotal = 0;
								$total_male_electors = 0;
								$total_female_electors = 0;
								$total_other_electors = 0;
							
							@endphp
							
								@foreach($row as $no => $value)
								
								@php 
								
								if($value['electors_total'] > 0)
									$voter_turn_all = round((($value['total_vote']/$value['electors_total'])*100),2);
								else
									$voter_turn_all = 0;
								
								
								if($value['electors_male'] > 0)
									$voter_turn_male = round((($value['voter_male']/$value['electors_male'])*100),2);
								else
									$voter_turn_male = 0;


								if($value['electors_female'] > 0)
									$voter_turn_female = round((($value['voter_female']/$value['electors_female'])*100),2);
								else
									$voter_turn_female = 0;
								
								
								if($value['electors_other'] > 0)
									$voter_turn_other = round((($value['voter_other']/$value['electors_other'])*100),2);
								else
									$voter_turn_other = 0;
								
								$total_electors += $value['electors_total'];
								$total_voters_male += $value['voter_male'];
								$total_voters_female += $value['voter_female'];
								$total_voters_other += $value['voter_other'];
								$total_voters_all += $value['voter_total'];
								$total_voters_nri += $value['voter_nri'];
								$total_postal_all += $value['postal_vote'];
								$total_voters_alltotal += $value['total_vote'];
								$total_male_electors += $value['electors_male'];
								$total_female_electors += $value['electors_female'];
								$total_other_electors += $value['electors_other'];
								
								
								
								
								$grand_total_electors 			+= $value['electors_total'];
								$grand_total_voters_male 		+= $value['voter_male'];
								$grand_total_voters_female 		+= $value['voter_female'];
								$grand_total_voters_other 		+= $value['voter_other'];
								$grand_total_voters_all 		+= $value['voter_total'];
								$grand_total_voters_nri 		+= $value['voter_nri'];
								$grand_total_postal_all 		+= $value['postal_vote'];
								$grand_total_voters_alltotal 	+= $value['total_vote'];
								$grand_total_male_electors 		+= $value['electors_male'];
								$grand_total_female_electors 	+= $value['electors_female'];
								$grand_total_other_electors 	+= $value['electors_other'];
								
								
								
																
								@endphp

                                <tr>
                                    <td>{{$sl_no}}.</td>
									<td>{{$value['PC_NO']}}</td>
                                    <td>{{$value['PC_NAME']}}</td>                                 
                                    <td>{{$value['electors_total']}}</td>
                                    <td>{{$value['voter_male']}}</td>
                                    <td>{{$value['voter_female']}}</td>
                                    <td>{{$value['voter_other']}}</td>
                                    <td>{{$value['voter_total']}}</td>
									<td>{{$value['voter_nri']}}</td>
                                    <td>{{$value['postal_vote']}}</td>
                                    <td>{{$value['total_vote']}}</td>
                                    <td>{{$voter_turn_all}}</td>
									<td>{{$voter_turn_male}}</td>
                                    <td>{{$voter_turn_female}}</td>
                                    <td>{{$voter_turn_other}}</td>
                                </tr>
								
									@php 
									$sl_no++;
									@endphp
								@endforeach
                                
								@php
								if($total_electors > 0)
									$voter_turn_all_total = round((($total_voters_alltotal/$total_electors)*100),2);
								else
									$voter_turn_all_total = 0;
								
								
								if($total_male_electors > 0)
									$voter_turn_male_total = round((($total_voters_male/$total_male_electors)*100),2);
								else
									$voter_turn_male_total = 0;


								if($total_female_electors > 0)
									$voter_turn_female_total = round((($total_voters_female/$total_female_electors)*100),2);
								else
									$voter_turn_female_total = 0;
								
								
								if($total_other_electors > 0)
									$voter_turn_other_total = round((($total_voters_other/$total_other_electors)*100),2);
								else
									$voter_turn_other_total = 0;
								
								@endphp
								
                                <tr style="font-weight:bold;">
                                    <th style="width: 10%"><b>State Total:</b></th>
                                    <td></td>
                                    <td></td>
                                    <td><b>{{$total_electors}}</b></td>
                                    <td><b>{{$total_voters_male}}</b></td>
                                    <td><b>{{$total_voters_female}}</b></td>
                                    <td><b>{{$total_voters_other}}</b></td>
                                    <td><b>{{$total_voters_all}}</b></td>
                                    <td><b>{{$total_voters_nri}}</b></td>
									<td><b>{{$total_postal_all}}</b></td>

                                    <td><b>{{$total_voters_alltotal}}</b></td>
                                    <td><b>{{$voter_turn_all_total}}</b></td>
                                    <td><b>{{$voter_turn_male_total}}</b></td>
                                    <td><b>{{$voter_turn_female_total}}</b></td>
                                    <td><b>{{$voter_turn_other_total}}</b></td>
                                </tr>
                                

                            </tbody>
							@endforeach

							@php
								if($grand_total_electors > 0)
									$grand_voter_turn_all_total = round((($grand_total_voters_alltotal/$grand_total_electors)*100),2);
								else
									$grand_voter_turn_all_total = 0;
								
								
								if($grand_total_male_electors > 0)
									$grand_voter_turn_male_total = round((($grand_total_voters_male/$grand_total_male_electors)*100),2);
								else
									$grand_voter_turn_male_total = 0;


								if($grand_total_female_electors > 0)
									$grand_voter_turn_female_total = round((($grand_total_voters_female/$grand_total_female_electors)*100),2);
								else
									$grand_voter_turn_female_total = 0;
								
								
								if($grand_total_other_electors > 0)
									$grand_voter_turn_other_total = round((($grand_total_voters_other/$grand_total_other_electors)*100),2);
								else
									$grand_voter_turn_other_total = 0;
								
								@endphp
								
                                <tr style="font-weight:bold;">
                                    <th style="width: 50%;">All India Total:</th>
                                    <td></td>
                                    <td></td>
                                    <td>{{$grand_total_electors}}</td>
                                    <td>{{$grand_total_voters_male}}</td>
                                    <td>{{$grand_total_voters_female}}</td>
                                    <td>{{$grand_total_voters_other}}</td>
                                    <td>{{$grand_total_voters_all}}</td>
                                    <td>{{$grand_total_voters_nri}}</td>
									<td>{{$grand_total_postal_all}}</td>
                                    <td>{{$grand_total_voters_alltotal}}</td>
                                    <td>{{$grand_voter_turn_all_total}}</td>
                                    <td>{{$grand_voter_turn_male_total}}</td>
                                    <td>{{$grand_voter_turn_female_total}}</td>
                                    <td>{{$grand_voter_turn_other_total}}</td>
                                </tr>


							
                        </table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection