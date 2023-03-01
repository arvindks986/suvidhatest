@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'PC Wise Distribution Of Valid Votes Polled')
@section('content')

<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
	<div class="container-fluid">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(14.PC Wise Distribution Of Valid Votes Polled)</h4></div> 
						<div class="col">
							<!--<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
							</p>-->
							<p class="mb-0 text-right">
							<a href="PCWiseDistributionVotesPolledPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="#" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>
				
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th colspan="5" style="font-size: 17px;">State : <span style="color: #fff; font-style: normal;font-weight: bold; text-decoration: underline;"> {{$stname}}</span> </th>
                                </tr>
                                <tr class="table-primary">
                                  
                                <tr>
                                    <th rowspan="2">Sl.no. </th>
                                    <th rowspan="2">PC No. </th>
                                    <th rowspan="2">PC Name </th>
                                    <th colspan="2">Electors </th>
                                   <th colspan="2">Valid Votes Polled</th>
                                   <th>NOTA</th>
                                   <th colspan="2">Rejected/ Not Retrived Votes</th>
                                   <th rowspan="2">Total Voters</th>
                                   <th rowspan="2">Tendered Votes</th>
                                   <th rowspan="2">Test Votes</th>
                                   <th rowspan="2">Voter Turn Out (%)</th>
                                   <th rowspan="2">% Votes to Winner out of total Votes Polled</th>
                                   <th rowspan="2">% Votes to NOTA out of total Votes Polled</th>
                                </tr>
                             

                             <tr>
                                 
                             
                                 <th>Gereral</th>
                                 <th>Service</th>
                                 <th>EVM</th>
                                 <th>Postal</th>
                                 <th></th>
                                 <th>EVM</th>
                                 <th>POstal</th>
                             </tr>
                            </thead>
                            <tbody>
                                <?php $count=1; ?>
                                @forelse($data as $raw)
                                <?php
                                $totalelectors = $totalvoters = $voterturnout  = $notapercent = $candvote = $votertowinnerout = 0;
                                $totalelectors = $raw->e_all_t+$raw->e_ser_t;
                                $totalvoters=$raw->e_all_t;
                                if($totalelectors>0)
                                {
                                     $voterturnout=round((($totalvoters/$totalelectors)*100),2);
                                }
                                if($totalvoters>0)
                                {
                                    $notavote=$raw->total_votes_nota;
                                    $totalv=(($notavote/$totalvoters)*100);
                                    $notapercent=round($totalv,2);
                                }
                                if($totalvoters>0)
                                {
                                    $candvote=$raw->lead_total_vote;
                                    $votertowinnerout=(($candvote/$totalvoters)*100);
                                }
                               
                                ?>
                                <tr>
                                    <td>{{$count}}.</td>
                                    <td>{{$raw->PC_NO}}</td>
                                    <td>{{$raw->PC_NAME}}</td>
                                    <td>{{$raw->e_all_t}}</td>
                                    <td>{{$raw->e_ser_t}}</td>
                                    <td>{{$raw->v_votes_evm_all}}</td>
                                    <td>{{$raw->postal_valid_votes}}</td>
                                    <td>{{$raw->total_votes_nota}}</td>
                                    <td>{{$raw->r_votes_evm}}</td>
                                    <td>{{$raw->postal_vote_rejected}}</td>
                                    <td>{{$raw->e_all_t}}</td>
                                    <td>{{$raw->tendered_votes}}</td>
                                    <td>{{$raw->mock_poll_evm}}</td>
                                    <td>{{$voterturnout}}</td>
                                    <td>{{round($votertowinnerout,2)}}</td>
                                    <td>{{$notapercent}}</td>
                                </tr>
                                 <?php $count++; ?>
                                @empty
                               
                                <tr><td colspan="16">Result Not Found</td></tr>


 <tr>
    @endforelse

                            </tbody>
                       </table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
