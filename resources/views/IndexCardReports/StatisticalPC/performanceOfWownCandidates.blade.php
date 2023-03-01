@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Individual Performance Of Woman Candidates')
@section('content')


<?php  //$st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(25 - INDIVIDUAL PERFORMANCE OF WOMEN CANDIDATES)</h4></div> 
              <div class="col">
			  <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
               </p>
			   <p class="mb-0 text-right">
					  <a href="individualperformanceofwomencandidates_pdf" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
        <a href="individualperformanceofwomencandidates_xls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
			   </p>
              </div>
			  
			
            </div>
      </div>
  
 <div class="card-body">
 
	<div class="table-responsive">
                 <table class="table table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Name of Constituency</th>
                        </tr>
                         <tr>
                            <th rowspan="2">Sl. No.</th>
                            <th style="text-align: left;" rowspan="2">Name of candidate</th>
                            <th rowspan="2">Party</th>
                            <th rowspan="2">Party <br> Type</th>
                            <th rowspan="2">Votes <br> Secured</th>
                            <th colspan="2" style="text-decoration: underline;">% of secured votes</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Total <br>Valid <br> Votes</th>
                        </tr>
                        <tr>
                            <th>Over total <br>electors in <br>constituency</th>
                            <th>Over total valid <br> votes in <br>constituency</th>
                            
                        </tr>
                    </thead>
                    <tbody>
					
                   
					<?php $i = 1; ?>
					@foreach ($dataArray as $keys => $rowArr)
					
					<tr>
                            <th colspan="">State/UT: {{$keys}} </th>
                            <td colspan="8">    </td>
                        </tr>
					
					@foreach ($rowArr as $key => $row)
												
						<tr>
                            <td colspan=""><b>{{$key}}</b></td>
                            <td colspan="8">    </td>
                        </tr>
											
						@foreach ($row as $keys => $rowData)
					<?php //$total_electors_votes = $rowData['total_electors_votes_gen'] + $rowData['total_electors_votes_ser']; 
					
					$total_electors_votes = $rowData['total_electors'];
					?>
					
                        
                        <tr>
                            <td style="text-align: center;">{{$rowData['srno']}}</td>
                            <td>{{$rowData['candidate_name']}}</td>
                            <td>{{$rowData['party_abbre']}}</td>
                            <td>{{$rowData['PARTYTYPE']}}</td>
                            <td>{{$rowData['candidate_votes']}}</td>
                            <td>@if($total_electors_votes > 0)
								{{number_format((float)($rowData['candidate_votes']*100)/$total_electors_votes, 2, '.', '')}}
								@else
									0
								@endif
							</td>
                            <td>@if($rowData['total_votes'])
							{{number_format((float)($rowData['candidate_votes']*100)/$rowData['total_votes'], 2, '.', '')}}
								@else
									0
								@endif</td>
                            <td>{{$rowData['status']}}
							</td>
                            <td>{{$rowData['total_votes']}}</td>
                        </tr>
						
						<?php $i++; ?>


                        <tr style="height: 10px;">    </tr>
						@endforeach
						@endforeach
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