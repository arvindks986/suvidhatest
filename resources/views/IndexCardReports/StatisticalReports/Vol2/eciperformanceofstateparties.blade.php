@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'PERFORMANCE OF STATE PARTIES - Phase General Elections')
@section('content')
<?php  $st=getstatebystatecode($user_data->st_code);   ?>
<section class="">
	<div class="container">
		<div class="row">
		<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(21 - PERFORMANCE OF STATE PARTIES)</h4></div>
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All india</span> &nbsp;&nbsp; <b></b>
							</p>
							<p class="mb-0 text-right">
							<a href="{{'performance-of-state-partys-pdf'}}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>

							<a href="{{'performance-of-state-partys-excel'}}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important; display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>

				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped" style="width: 100%;">

							                            <thead>
                                <tr>
                                    <th>Party Name</th>
                                    <th>State in which <br> the party is recognised</th>
                                    <th colspan="3">Candidates</th>
                                    <th rowspan="2">Votes Secured By Party</th>
                                    <th colspan="4">% Of votes secured</th>
                                </tr>

                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>Contested</th>
                                    <th>Won</th>
                                    <th>DF</th>
                                    <th>Over total <br> elector in <br> the state</th>
                                    <th>Over total valid <br> votes polled  in <br> the state</th>
                                </tr>
                            </thead>
                            <tbody>
							
								@php
								$grand_total_contested = 0;
								$grand_total_won = 0;
								$grand_total_df = 0;
								$grand_total_vote_secure = 0;
								$grand_total_vote = 0;
								$grand_total_electors = 0;
								@endphp
							
							
                                @foreach($arraydata as $rowdatas)

                                <tr>
                                    <td>{{$rowdatas['partyabbre']}}</td><td> ({{$rowdatas['partyname']}})</td>
                                    <td colspan="6"></td>
                                </tr>

								@php
								$total_vote_secure = 0;
								$total_vote = 0;
								$total_electors = 0;
								@endphp


                                @foreach($rowdatas['partydata'] as $rowdata)
                                <tr>
                                    <td colspan="2" style="text-align: center;">{{$rowdata['statename']}}</td>
                                    <td>{{$rowdata['contested']}} </td>
                                    <td>{{$rowdata['won']}} </td>
                                    <td>{{$rowdata['df']}} </td>
                                    <td>{{$rowdata['Securedvotes']}} </td>
									 <td>{{$rowdata['totalelectors']}} </td>
                                    <td>{{$rowdata['poledvotespercent']}} </td>                                  
                                </tr>
								
								@php
								$total_vote_secure += $rowdata['Securedvotes'];
								$total_vote += $rowdata['total_vote'];
								$total_electors += $rowdata['total_electorsdata'];
																
								$grand_total_contested += $rowdata['contested'];
								$grand_total_won += $rowdata['won'];
								$grand_total_df += $rowdata['df'];
								$grand_total_vote_secure += $rowdata['Securedvotes'];
								$grand_total_vote += $rowdata['total_vote'];
								$grand_total_electors += $rowdata['total_electorsdata'];
								
								@endphp
														
                                @endforeach
								<tr>
                                    <th>Party Total</th>
                                    <th></th>
                                    <th>{{array_sum($rowdatas['totalcontested'])}}</th>
                                    <th>{{array_sum($rowdatas['won'])}}</th>
                                    <th>{{array_sum($rowdatas['DF'])}}</th>
                                    <th>{{array_sum($rowdatas['Securedvotes'])}}</th>
									<th>{{round(((($total_vote_secure)/$total_electors)*100),2)}}</th>
									<th>{{round(((($total_vote_secure)/$total_vote)*100),2)}}</th>
                                    
                                </tr>

                                @endforeach


								<tr>
                                    <th>Grand Total</th>
                                    <th></th>
                                    <th>{{$grand_total_contested}}</th>
                                    <th>{{$grand_total_won}}</th>
                                    <th>{{$grand_total_df}}</th>
                                    <th>{{$grand_total_vote_secure}}</th>
									<th>{{round(((($grand_total_vote_secure)/$grand_total_electors)*100),2)}}</th>
									<th>{{round(((($grand_total_vote_secure)/$grand_total_vote)*100),2)}}</th>
                                    
                                </tr>

                            </tbody>


						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
