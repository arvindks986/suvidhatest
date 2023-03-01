@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Performance OF Women Elector In Poll')
@section('content')

<style>
	td{
		background: #fff;
	}
	th{
		text-transform: uppercase;
	}

	td{
		font-weight: normal !important;
	}
</style>
<?php // $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(23 - PARTICIPATION OF WOMEN ELECTORS IN POLL )</h4></div> 
              <div class="col">
			  <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
               </p>
			   <p class="mb-0 text-right">
					  <a href="participationofWomeneletorsinPoll_pdf" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
        <a href="participationofWomeneletorsinPoll_xls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
			   </p>
              </div>
			  
			
            </div>
      </div>
  
 <div class="card-body">
 
	<div class="table-responsive">
                <table class="table table-bordered table-striped" style="width: 100%;table-layout: fixed;">
                            <thead>
                                <tr class="table-primary">
                                    <th>State/UT</th>
                                    <th>No. of <br> Seats</th>
                                    <th>Total <br> Electors</th>
                                    <th>Women <br> Electors</th>
                                    <th>% of Women <br> Electors <br> Over Total <br> Electors</th>
                                    <th>Total <br> Voters</th>
                                    <th>Women <br> Voters</th>
                                    <th>% of Women <br> Voters Over <br>Voters</th>
                                    <th>% of Women <br> Voters Over <br> Women <br>Electors</th>
                                    <th>Total <br>Poll% in <br> the <br> State/UT</th>
                                </tr>
								
								
								
								<?php 
								$totalNpOfSeats = $totalElectors = $totalWomenElectors = $totalWomenElectorsPer = $totalVoters = $totalWomenVoters = $totalWomenVotersPer = $totalWomenVotersOverElectorsPer = $totalVotersPer = 0;
								?>
								@foreach($data as $key => $row)
								
								
								<?php 
								
								if ($row->electors_total > 0){
									$perWomenElectors = ($row->electors_female*100)/$row->electors_total;
									$perWomenElectors = round($perWomenElectors,2);
								}else{
									$perWomenElectors = 0;
								}
								
								if ($row->voter_total > 0){
									$perWomenVoters = ($row->voter_female*100)/$row->voter_total;
									$perWomenVoters = round($perWomenVoters,2);
								}else{
									$perWomenVoters = 0;
								}
								
								if ($row->electors_female > 0){
									$perWomenVotersOverElectors = ($row->voter_female*100)/$row->electors_female;
									
									$perWomenVotersOverElectors = round($perWomenVotersOverElectors,2);
									
								}else{
									$perWomenVotersOverElectors = 0;
								}
								
								if ($row->electors_total > 0){
									$perTotalPoll = ($row->voter_total*100)/$row->electors_total;
									
									$perTotalPoll = round($perTotalPoll,2);
									
								}else{
									$perTotalPoll = 0;
								}
								
								
								$totalNpOfSeats += $row->no_of_seats;
								$totalElectors += $row->electors_total;
								$totalWomenElectors += $row->electors_female;								
								$totalWomenElectorsPer += $perWomenElectors;								
								$totalVoters += $row->voter_total;
								$totalWomenVoters += $row->voter_female;							
								$totalWomenVotersPer += $perWomenVoters;
								$totalWomenVotersOverElectorsPer += $perWomenVotersOverElectors;
								$totalVotersPer += $perTotalPoll;			
								?>
								
								
                                <tr>
                                    <td>{{$row->ST_NAME}}</td>
                                    <td>{{$row->no_of_seats}}</td>
                                    <td>{{($row->electors_total)?$row->electors_total:0}}</td>
                                    <td>{{($row->electors_female)?$row->electors_female:0}}</td>
                                    <td>{{$perWomenElectors}}</td>                                    
                                    <td>{{($row->voter_total)?$row->voter_total:0}}</td>
                                    <td>{{($row->voter_female)?$row->voter_female:0}}</td>
                                    <td>{{$perWomenVoters}}</td>
                                    <td>{{$perWomenVotersOverElectors}}</td>
                                    <td>{{$perTotalPoll}}</td>
                                </tr>
								
                                @endforeach
								
								<?php 
								if ($totalElectors > 0){
									$totalperWomenElectors = round((($totalWomenElectors*100)/$totalElectors),2);
								}else{
									$totalperWomenElectors = 0;
								}
								
								if ($totalVoters > 0){
									$totalperWomenVoters = round((($totalWomenVoters*100)/$totalVoters),2);
								}else{
									$totalperWomenVoters = 0;
								}
								
								if ($totalWomenElectors > 0){
									$totalperWomenVotersOverElectors = round((($totalWomenVoters*100)/$totalWomenElectors),2);
									
								}else{
									$totalperWomenVotersOverElectors = 0;
								}
								
								if ($totalElectors > 0){
									$totalperTotalPoll = round((($totalVoters*100)/$totalElectors),2);
									
								}else{
									$totalperTotalPoll = 0;
								}
								?>
								
								
								
								
                                <tr  style="font-weight:bold;">
                                    <td><b>TOTAL</b></td>
                                    <td><b>{{$totalNpOfSeats}}</b></td>
                                    <td><b>{{$totalElectors}}</b></td>
                                    <td><b>{{$totalWomenElectors}}</b></td>
                                    <td><b>{{$totalperWomenElectors}}</b></td>
                                    <td><b>{{$totalVoters}}</b></td>
                                    <td><b>{{$totalWomenVoters}}</b></td>
                                    <td><b>{{$totalperWomenVoters}}</b></td>
                                    <td><b>{{$totalperWomenVotersOverElectors}}</b></td>
                                    <td><b>{{$totalperTotalPoll}}</b></td>
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