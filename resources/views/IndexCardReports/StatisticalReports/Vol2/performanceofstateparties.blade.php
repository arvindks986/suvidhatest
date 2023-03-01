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
						<div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(PERFORMANCE OF STATE PARTIES)</h4></div> 
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All india</span> &nbsp;&nbsp; <b></b> 
							</p>
							<p class="mb-0 text-right">
							<a href="{{'Performance-state-parties-pdf'}}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="#" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important; display: table-row;"></a>
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
                                    <th>State in which the Party is Recognised</th>
                                    <th colspan="3">Candidates</th>
                                    <th>Votes Secured By Party</th>
                                    <th colspan="4">% Of Votes Secured</th>
                                </tr>

                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>Contested</th>
                                    <th>Won</th>
                                    <th>DF</th>
                                    <th></th>
                                    <th>Over Total Elector in the State</th>
                                    <th>Over TotalVotes Polled in th State</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($arraydata as $rowdatas)
                                <tr>
                                    <td>{{$rowdatas['partyabbr']}}</td><td> {{$rowdatas['partyname']}})</td>
                                    <td colspan="6"></td>
                                </tr>    


                                @foreach($rowdatas['partydata'] as $rowdata) 
                                <tr>
                                    <td colspan="2">{{$rowdata['statename']}}</td>                            
                                    <td>{{$rowdata['contested']}} </td>
                                    <td>{{$rowdata['won']}} </td>
                                    <td>{{$rowdata['df']}} </td>
                                    <td>{{$rowdata['Securedvotes']}} </td>
                                    <td>{{$rowdata['poledvotespercent']}} </td>
                                    <td>{{$rowdata['totalelectors']}} </td>
                                </tr>
                                @endforeach

                                <tr>
                                    <th>Total</th>
                                    <th></th>
                                    <th>{{array_sum($rowdatas['totalcontested'])}}</th>
                                    <th>{{array_sum($rowdatas['won'])}}</th>
                                    <th>NA</th>
                                    <th>{{array_sum($rowdatas['Securedvotes'])}}</th>
                                    <th>{{array_sum($rowdatas['totalpercentvote'])}}</th>
                                    <th>{{array_sum($rowdatas['totalpercentelectors'])}}</th>

                                </tr>
                                <?Php $aber = $rowdatas['partyabbr']; ?>
                                @endforeach

                            </tbody>

							
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
