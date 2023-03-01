@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'PERFORMANCE OF UNRECOGNISED PARTIES - Phase General Elections')
@section('content')


<?php  $st=getstatebystatecode($user_data->st_code);   ?> 



<style>
    
    td{
        font-weight: normal !important;
    }
</style>


<section class="">
	<div class="container">
		<div class="row">
		<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>22 - PERFORMANCE OF REGISTERED (UNRECOGNISED) PARTIES </h4></div> 
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All india</span> &nbsp;&nbsp; <b></b> 
							</p>
							<p class="mb-0 text-right">
							<a href="performance-of-unrecognised-partys-pdf" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="performance-of-unrecognised-partys_xls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important; display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>
				
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped" style="width: 100%;">
						
							                   
                                                        <thead>
                                <tr>
                                    <th rowspan="2" class="blc">Party Name</th>
                                    
                                    <th colspan="3" style="text-decoration: underline;">Candidates</th>
                                    <th class="blc" rowspan="2">Votes secured by party</th>
                                    <th colspan="2" style="text-decoration: underline;">% of votes secured</th>
                                </tr>

                                <tr>
                                    <th class="blc">Contested</th>
                                    <th class="blc">Won</th>
                                    <th class="blc">DF</th>
                                    <th class="blc">Over Total <br> Elector in  <br>the State</th>
                                    <th class="blc">Over Total valid  <br>Votes Polled in <br> the State</th>
                                </tr>
                            </thead>
                            <?php 
                                $grandtotalcon = $grandtotalwon = $grandtotalDf = $grandtotalvalid_vote_party 
                                = $grandtotalcon = $grandtotalelector = $grandtotalvotestate = 0;
                            ?>

                            

                            @foreach($performanceofst as $value)
                            
                            
                            <tbody>

                              
                            

                                <tr>
                                     <?php if($value->PARTYTYPE == 'S') { ?>
                                    <td>{{$value->PARTYNAME}}<span style="color: black"><b>*</b></span></td>
                                   <?php } else { ?>
                                    <td>{{$value->PARTYNAME}}</td>
                                <?php } ?>
                                    <td>{{$value->totalcontested}}</td>
                                    <td>{{$value->won}}</td>
                                    <td>{{$value->DF}}</td>
                                    <td>{{$value->totalvalid_valid_vote_party}}</td>
                                    <td>{{round($value->totalvalid_valid_vote_party/$value->TOTAL_ELECT_VOTE*100,4)}}</td>
                                    <td>{{round($value->totalvalid_valid_vote_party/$value->Total_Valid_Vote_State*100,4)}}</td>

                                </tr>

                                <?php 

                                    $grandtotalcon += $value->totalcontested;
                                    $grandtotalwon += $value->won;
                                    $grandtotalDf += $value->DF;
                                    $grandtotalvalid_vote_party += $value->totalvalid_valid_vote_party;
                                    $grandtotalelector +=  $value->TOTAL_ELECT_VOTE;
                                    $grandtotalvotestate +=  $value->Total_Valid_Vote_State;
                                    
                                ?>

                                @endforeach
                                

                               

                               
                                <tr>
                                    <th>Grand Total</th>
                                    <td><b>{{$grandtotalcon}}</b></td>

                                    <td><b>{{$grandtotalwon}}</b></td>
                                    <td><b>{{$grandtotalDf}}</b></td>
                                    <td><b>{{$grandtotalvalid_vote_party}}</b></td>
                                    <td><b>{{round($grandtotalvalid_vote_party/$grandtotalelector*100,4)}}</b></td>
                                    <td><b>{{round($grandtotalvalid_vote_party/$grandtotalvotestate*100,4)}}</b></td>
                                   

                                </tr>



                                <td colspan="6"><span><b>* State Party</b></span></td>

                            </tbody>

							
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
