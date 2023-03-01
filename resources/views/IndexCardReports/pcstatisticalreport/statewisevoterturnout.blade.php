@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'State Wise Voter Turnout')
@section('content')


<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
	<div class="container-fluid">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(12.State Wise Voter Turnout)</h4></div> 
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
							</p>
							<p class="mb-0 text-right">
							<a href="downloadstatewisevoterturnout" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="#" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>
				
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped table-bordered">
				  <thead class="">
					<tr>
					  <th scope="col">SL.No</th>
					  <th scope="col">State/UT</th>
					  <th scope="col" colspan="2">Electors</th>
					  <th scope="col" colspan="2">Voters</th>
					  <th scope="col">Voters Turnout(%)</th>
					</tr>
				<tr>
					  <th scope="col"></th>
					  <th scope="col"></th>
					  <th scope="col">General</th>
					  <th scope="col">Service</th>
					  <th scope="col">EVM</th>
					  <th scope="col">Postal</th>
					  <th scope="col"></th>
					</tr>

				  </thead>
				  <tbody>
					<tr>
					</tr>
				 @php
					$i=1
					@endphp  
				@foreach ($statewisevoterturnouts as $statewisevoterturnout) 
				<?php $voteper = 0;  ?>
					 <tr>
					  <td>{{$i}}</td>
					  <td>{{ $statewisevoterturnout->ST_NAME }}</td>
					  <td>{{ $statewisevoterturnout->e_gen_t }}</td>
					  <td>{{ $statewisevoterturnout->e_ser_t }}</td>
					  <td>{{ $statewisevoterturnout->vt_all_t }}</td>
					  <td>{{ $statewisevoterturnout->postal_valid_votes }}</td>
					  <td>@if(($statewisevoterturnout->e_gen_t+$statewisevoterturnout->e_ser_t) > 0)	  {{round(((($statewisevoterturnout->vt_all_t+$statewisevoterturnout->postal_valid_votes)/($statewisevoterturnout->e_gen_t+$statewisevoterturnout->e_ser_t))*100),2) }}
					  @else
						  0
					  @endif
					  </td>
					</tr>
					@php
					$i++
					@endphp
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
