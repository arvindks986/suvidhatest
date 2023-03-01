@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'State Wise Voter Turnout')
@section('content')


<?php  //$st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
	<div class="container-fluid">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(12 - State Wise Voter Turnout)</h4></div> 
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
							</p>
							<p class="mb-0 text-right">
							<a href="statewisevoterturnout_pdf" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="statewisevoterturnout_xls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
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
					  <th scope="col">Voters Turn Out(%)</th>
					</tr>
				<tr>
					  <th scope="col"></th>
					  <th scope="col"></th>
					  <th scope="col">General (Including NRIs)</th>
					  <th scope="col">Service</th>
					  <th scope="col">EVM</th>
					  <th scope="col">Postal</th>
					  <th scope="col"></th>
					</tr>

				  </thead>
				  <tbody>
					<tr>
					</tr>
					
					<?php $e_gen_t = $e_ser_t = $vt_all_t = $postal_valid_votes =0;  ?>
					
				 @php
					$i=1
					@endphp  
				@foreach ($statewisevoterturnouts as $statewisevoterturnout) 
					<?php $votes = \App\models\Admin\VoterModel::get_total([
						'group_by' => 'st_code',
						'st_code' => $statewisevoterturnout->st_code
					]); ?>
					 <tr>
					  <td>{{$i}}</td>
					  <td>{{ $statewisevoterturnout->ST_NAME }}</td>
					  <td>{{ $statewisevoterturnout->e_gen_t }}</td>
					  <td>{{ $statewisevoterturnout->e_ser_t }}</td>
					  <td>{{ $votes['vt_all_t'] }}</td>
					  <td>{{ $votes['postal_valid_votes'] }}</td>
					  <td>@if(($statewisevoterturnout->e_gen_t+$statewisevoterturnout->e_ser_t) > 0)	  {{round(((($votes['vt_all_t']+$votes['postal_valid_votes'])/($statewisevoterturnout->e_gen_t+$statewisevoterturnout->e_ser_t))*100),2) }}
					  @else
						  0
					  @endif
					  </td>
					</tr>
					
					
					
					@php
					$e_gen_t += $statewisevoterturnout->e_gen_t;
					$e_ser_t += $statewisevoterturnout->e_ser_t;
					$vt_all_t += $votes['vt_all_t'];
					$postal_valid_votes += $votes['postal_valid_votes'];
													
					$i++
					@endphp
				@endforeach

								
					 <tr style="font-weight:bold;">
					  <td colspan="2">Total</td>
					  <td>{{ $e_gen_t }}</td>
					  <td>{{ $e_ser_t }}</td>
					  <td>{{ $vt_all_t }}</td>
					  <td>{{ $postal_valid_votes }}</td>
					  <td>
						@if(($e_gen_t+$e_ser_t) > 0)	  
						{{round(((($vt_all_t+$postal_valid_votes)/($e_gen_t+$e_ser_t))*100),2) }}
						@else
						0
						@endif
					  </td>
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
