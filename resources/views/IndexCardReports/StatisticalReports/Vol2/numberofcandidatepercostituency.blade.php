@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Number of Candidates Per Constituency - Phase General Elections')
@section('content')
<?php  $st=getstatebystatecode($user_data->st_code);   ?>
<section class="">
	<div class="container">
		<div class="row">
		<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(8 - Number of Candidates Per Constituency)</h4></div>
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All india</span> &nbsp;&nbsp; <b></b>
							</p>
							<p class="mb-0 text-right">
							<a href="{{'No-of-candidate-per-consitituency-pdf'}}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="{{'No-of-candidate-per-consitituency-excel'}}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important; display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>

				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped" style="width: 100%;">

											<thead>
											<tr>
													<th colspan="2"></th>
													<th colspan="7" style="text-align: center; text-decoration: underline;">Constituencies with candidates numbering</th>
													<th colspan="3" style="text-align: center; text-decoration: underline;" >Candidates in a Constituency</th>
											</tr>
											<tr>
													<th>State/UT</th>
													<th>No. of Seats</th>
													<th>1 </th>
													<th><=15 </th>
													<th>>15  <=31</th>
													<th>>31  <=47</th>
													<th>>47 <=63</th>
													<th>>63</th>
													<th>Total Candidates</th>
													<th>Min</th>
													<th>Max</th>
													<th>Avg</th>
											</tr>
									</thead>
	         <tbody>
	         			<?php 

	         			 		$seartotal = $searonetotal = $searNotatotal = $searThreeOnetotal = $searFourSeventotal 

	         					= $searSixThreetotal = $searLessSixThreetotal = $totalcandidate = 0 ;

	         			?> 

						 @forelse ($pcCount as $key => $value)
							 <tr>
							 	<td>{{$value->st_name}}</td>
							 	<td>{{$value->No_of_Seats}}</td>
							 	<td>{{$value->one}}</td>
							 	<td>{{$value->Nota}}</td>
							 	<td>{{$value->threeone}}</td>
							 	<td>{{$value->fourseven}}</td>
							 	<td>{{$value->sixthree}}</td>
							 	<td>{{$value->lesssixthree}}</td>
							 	<td>{{$value->Total_Candidates}}</td>
							 	<td>{{$value->mincan}}</td>
							 	<td>{{$value->maxcan}}</td>
							 	<td>{{$value->Avg}}</td>
							 </tr>

							<?php  

									$seartotal += $value->No_of_Seats;
									$searonetotal += $value->one;
									$searNotatotal += $value->Nota;
									$searThreeOnetotal += $value->threeone;
									$searFourSeventotal += $value->fourseven;
									$searSixThreetotal += $value->sixthree;
									$searLessSixThreetotal += $value->lesssixthree;
									$totalcandidate	 += $value->Total_Candidates;
							?>

						 @empty
							 <tr>
							 	<td>Data Not available.</td></tr>
						 @endforelse

						 	<?php
								$minnumber = array_column($pcCount, 'mincan');
								$maxnumber = array_column($pcCount, 'maxcan');
								$min = min($minnumber);
								$max = max($maxnumber);

							?>

							 <tr>
							 	<td><b>Grand Total</b></td>
							 	<td><b>{{$seartotal}}</b></td>
							 	<td><b>{{$searonetotal}}</b></td>
							 	<td><b>{{$searNotatotal}}</b></td>
							 	<td><b>{{$searThreeOnetotal}}</b></td>
							 	<td><b>{{$searFourSeventotal}}</b></td>
							 	<td><b>{{$searSixThreetotal}}</b></td>
							 	<td><b>{{$searLessSixThreetotal}}</b></td>
							 	<td><b>{{$totalcandidate}}</b></td>
							 	<td><b>{{$min}}</b></td>
							 	<td><b>{{$max}}</b></td>
							 	<td><b>{{round($totalcandidate/$seartotal,2)}}</b></td>
							 	
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
