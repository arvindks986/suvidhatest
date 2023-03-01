@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'The Schedule of GE to Lok Sabha 2019 - Phase General Elections')
@section('content')

<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(28.Participation Of Women In Registered Parties)</h4></div> 
						<div class="col">
							<!--<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
							</p>-->
							<p class="mb-0 text-right">
							<a href="ParticipationofWomenInRegisteredPartiesPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="#" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
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
                                    <th colspan="4">Candidates </th>
                                    <th colspan="2">Percentage </th>
                                    <th rowspan="2">Votes Secured By Party In State</th>
                                   <th colspan="3">% of votes secured</th>
                                   
                                </tr>
                             

                             <tr>
                                 
                                 <th>State</th>
                                 <th>Contested</th>
                                 <th>Won</th>
                                 <th>DF</th>
                                 <th>Won</th>
                                 <th>DF</th>
                                 <th>Over total electors in the State</th>
                                  <th>Over total valid votes in the State</th>
                                 <th>Over Votes secured by the party in State</th>
                             </tr>

                            
                            </thead>
                            <tbody>
                              <?php $totalcont = $totalallwon = $totalvsbp = $totalelectorsinstate = $totalvalidvotesinstate = $totalvv = $totalovervotessecuredbyparty = $totalwonpercent =$ttwonper  = 0;?>
                              @foreach($data as $row)
                              <?php 
                              $totalcont+= $row->totalcontested;
                              $totalallwon += ($row->totalwon)?$row->totalwon:0;
                              $totalvsbp+= $row->totalvotesecured;
                              $totalelectorsinstate+= $row->overtotalelectors;
                              $totalvv+= $row->overtotalvalidvotes;
                              $totalovervotessecuredbyparty+= $row->securedbyparties;
                               $totalwonpercent =round((($row->totalwon/$row->totalcontested)*100),2);
                               $ttwonper +=$totalwonpercent;
                              ?>
                                <tr>

                                   <td>{{$row->PARTYNAME}}</td>
                                   <td>{{$stname}} </td>
                                   <td>{{$row->totalcontested}}</td>
                                   <td>{{$row->totalwon}}</td>
                                   <td>N/A</td>
                                   <td>{{$totalwonpercent}}</td>
                                   <td>N/A</td> 
                                   <td>{{$row->totalvotesecured}}</td>
                                   <td>{{$row->overtotalelectors}}</td>
                                   <td>{{$row->overtotalvalidvotes}}</td>
                                   <td>{{$row->securedbyparties}}</td>
                                </tr>                                   
                                @endforeach
                                 <tr>
                                   <td><b>Grand Total</b></td>
                                   <td></td>
                                   <td>{{$totalcont}}</td>
                                   <td>{{$totalallwon}}</td>
                                   <td></td>
                                   <td>{{$ttwonper}}</td>
                                   <td></td>
                                   <td>{{$totalvsbp}}</td>
                                   <td>{{$totalelectorsinstate}}</td>
                                   <td>{{$totalvv}}</td>
                                   <td>{{$totalovervotessecuredbyparty}}</td>
                                   
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
