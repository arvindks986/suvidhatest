@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Participation of Women as Individual Candidates')
@section('content')

<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(29.Participation of Women as Independent Candidates)</h4></div> 
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
							</p>
							<p class="mb-0 text-right">
							<a href="ParticipationofWomenAsIndependentCandidatesPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="ParticipationofWomenAsIndependentCandidatesXls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row; "></a>
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
                                  <th>Party Name</th>
                                    <th colspan="3">Candidates </th>
                                    <th colspan="2">Percentage </th>
                                    <th>Votes Secured By Women Candidates </th>
                                    <th colspan="2">% of Votes Secured </th>
                                   
                                </tr>
                             

                            <tr>
                                <th></th>
                                <th>Contested</th>
                                <th>Won </th>
                                <th>DF</th>
                                <th>Won</th>
                                <th>DF</th>
                                <th></th>
                                <th>Over Total Electors in Country</th>
                                <th>Over Total Valid Votes in Country</th>
                            </tr>
                            
                            </thead>
                            <tbody>
                               <?php $count = 1;
                            $totalc = $totalallwon = $totalvs = $totaloe = $totalvv = $totalwonpercent = $ttwonper = 0;?>
                              @foreach($data as $row)
                            <tr>
            <?php
                $totalc += ($row->totalcontested)?$row->totalcontested:0;
                $totalallwon += ($row->totalwon)?$row->totalwon:0;
                $totalvs += ($row->totalvotesecured)?$row->totalvotesecured:0;
                $totaloe += ($row->overtotalelectors)?$row->overtotalelectors:0;
                $totalvv += ($row->overtotalvalidvotes)?$row->overtotalvalidvotes:0;
                $totalwonpercent =round((($row->totalwon/$row->totalcontested)*100),2);
                $ttwonper +=$totalwonpercent;
               ?>
                                   
                              <td>{{$row->PARTYNAME}}</td>
                              <td>{{$row->totalcontested}}</td>
                              <td>{{$row->totalwon}}</td>
                              <td>N/A</td>
                              <td>{{$totalwonpercent}}</td>
                              <td>N/A</td>                              
                              <td>{{$row->totalvotesecured}}</td>
                              <td>{{$row->overtotalelectors}}</td>
                              <td>{{$row->overtotalvalidvotes}}</td>

                                </tr>
                            @endforeach



<tr>
    <td><b>Total</b></td>
                              <td>{{$totalc}}</td>
                              <td>{{$totalallwon}}</td>
                              <td></td>
                              <td>{{$ttwonper}}</td>
                              <td></td>                              
                              <td>{{$totalvs}}</td>
                              <td>{{$totaloe}}</td>
                              <td>{{$totalvv}}</td>
                              
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
