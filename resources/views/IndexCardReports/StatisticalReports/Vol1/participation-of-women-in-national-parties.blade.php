@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Participation of Women in National Parties')
@section('content')

<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(26.Participation of Women in National Parties)</h4></div> 
						<div class="col">
							<!--<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
							</p>-->
							<p class="mb-0 text-right">
							<a href="ParticipationofWomenInNationalPartiesPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="ParticipationofWomenInNationalPartiesXls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>
				
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                
                                <tr class="table-primary">
                                  
                                <tr>
                                    <th rowspan="2">Party Name </th>
                                    <th colspan="">Candidates </th>
                                    <th colspan="2">Percentage </th>
                                    <th rowspan="2">Votes Secured By Women Candidates </th>
                                   <th colspan="3">% of votes secured</th>
                                   
                                </tr>
                             

                             <tr>
                                 
                                 <th>Contested</th>
                                 <th>Won</th>
                                 <th>DF</th>
                                 <th>Over total electors in the State</th>
                                  <th>Over total valid votes in the State</th>
                                 <th>Over Votes secured by the party in State</th>
                             </tr>

                            
                            </thead>
                            <tbody>
                              <?php
                            $totalallcontested = $totalallwon = $totalwon = $totaldf = $totalvsecuredbyf = $totalelectors = $totalvalidvotes = $overvotessecuredbyparty = $tvsbp = 0;
                              ?>
                            @forelse($sData as $raw)
                            <?php 
                            $totalallcontested+=$raw->totalcontested;
                            $totalallwon+=$raw->totalwon;
                            $totalvsecuredbyf+=$raw->totalvote;
                            $totalelectors+=$raw->overtotalelectors;
                            $totalvalidvotes+=$raw->overtotalvalidvotes;
                            $tvsbp+=$raw->ovsbp;

                            ?>
                                <tr>
                                   <td>{{$raw->PARTYNAME}}</td>
                                   <td>{{$raw->totalcontested}}</td>
                                   <td>{{$raw->totalwon}}</td>
                                   <td>N/A</td>
                                   
                                   <td>{{$raw->totalvote}}</td>
                                   <td>{{$raw->overtotalelectors}}</td>
                                   <td>{{$raw->overtotalvalidvotes}}</td>
                                   <td>{{round($raw->ovsbp,2)}}</td>
                                   
                                </tr>

                              @empty
                              <tr>
                                   <td>Result Not Found</td>
                                </tr>
                              @endforelse
                              <tr>
                                <td>Total</td>
                                <td>{{$totalallcontested}}</td>
                                <td>{{$totalallwon}}</td>
                                <td></td>
                                <td>{{$totalvsecuredbyf}}</td>
                                <td>{{$totalelectors}}</td>
                                <td>{{$totalvalidvotes}}</td>
                                <td>{{round($tvsbp,2)}}</td>
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
