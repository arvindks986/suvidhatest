@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'State Wise Number Of Electors')
@section('content')

<style>
	th{
		text-align: center;
		text-transform: uppercase;
		font-weight: normal;
	}
</style>

<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(9 - State Wise Number Of Electors)</h4></div> 
              <div class="col">
			  <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
               </p>
			   <p class="mb-0 text-right">
				<a href="statewisenumberelectors_pdf" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
				<a href="statewisenumberelectors_xls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
			   </p>
              </div>
			  
			
            </div>
      </div>
  
 <div class="card-body">
 
	<div class="table-responsive">
                 <table class="table table-bordered" style="width:100%;white-space: nowrap;">
                    <thead class="table-primary">
                    	<tr>
                        <th>State/UT</th>
                        <th colspan="4">General <span style="text-transform: capitalize;">(Including NRIs)</span></th>
                        <th colspan="4">Service</th>
                        <th colspan="4">Grand</th>
						<th colspan="4">NRIs</th>
                    </tr>
                    </thead>

                    <tbody>

                    <tr>
                        <th></th>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Third Gender</th>
                        <th>Total</th>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Third Gender</th>
                        <th>Total</th>
                        <th>Male</th>
                        <th>Female</th>
						<th>Third Gender</th>
                        <th>Total</th>
                        <th>Male</th>
                        <th>Female</th>
						<th>Third Gender</th>
                        <th>Total</th>
                    </tr>


					<?php $total_gen_m = $total_gen_f = $total_gen_o = $total_gen_t =$total_ser_m = $total_ser_f = $total_ser_o = $total_ser_t = $total_grand_m = $total_grand_f = $total_grand_o = $total_grand_t = $total_nri_m = $total_nri_f = $total_nri_o = $total_nri_t =0; ?>



					@foreach($data as $key => $row)
					
					
					<?php 
					$grand_m = $grand_f = $grand_o = $grand_t =0; 
					
					$grand_m = $row->e_gen_m + $row->e_ser_m; 
					$grand_f = $row->e_gen_f + $row->e_ser_f; 
					$grand_o = $row->e_gen_o + $row->e_ser_o; 
					$grand_t = $row->e_gen_t + $row->e_ser_t; 
					
					
					$total_gen_m += $row->e_gen_m; 
					$total_gen_f += $row->e_gen_f; 
					$total_gen_o += $row->e_gen_o; 
					$total_gen_t += $row->e_gen_t; 
					
					$total_ser_m += $row->e_ser_m;
					$total_ser_f += $row->e_ser_f;
					$total_ser_o += $row->e_ser_o;
					$total_ser_t += $row->e_ser_t; 
					
					
					$total_nri_m += $row->e_nri_m;
					$total_nri_f += $row->e_nri_f;
					$total_nri_o += $row->e_nri_o;
					$total_nri_t += $row->e_nri_t; 
										
					$total_grand_m += $grand_m;
					$total_grand_f += $grand_f;
					$total_grand_o += $grand_o;
					$total_grand_t += $grand_t;
					
					?>
					
					
					<tr>
						<td class="">{{$key+1}}. {{$row->ST_NAME}}</td>
						<td>@if($row->e_gen_m) {{$row->e_gen_m}} @else 0 @endif</td>
						<td>@if($row->e_gen_f) {{$row->e_gen_f}} @else 0 @endif</td>
						<td>@if($row->e_gen_o) {{$row->e_gen_o}} @else 0 @endif</td>
						<td>@if($row->e_gen_t) {{$row->e_gen_t}} @else 0 @endif</td>
						<td>@if($row->e_ser_m) {{$row->e_ser_m}} @else 0 @endif</td>
						<td>@if($row->e_ser_f) {{$row->e_ser_f}} @else 0 @endif</td>
						<td>@if($row->e_ser_o) {{$row->e_ser_o}} @else 0 @endif</td>
						<td>@if($row->e_ser_t) {{$row->e_ser_t}} @else 0 @endif</td>
						<td>@if($grand_m) {{$grand_m}} @else 0 @endif</td>
						<td>@if($grand_f) {{$grand_f}} @else 0 @endif</td>
						<td>@if($grand_o) {{$grand_o}} @else 0 @endif</td>
						<td>@if($grand_t) {{$grand_t}} @else 0 @endif</td>
						<td>@if($row->e_nri_m) {{$row->e_nri_m}} @else 0 @endif</td>
						<td>@if($row->e_nri_f) {{$row->e_nri_f}} @else 0 @endif</td>
						<td>@if($row->e_nri_o) {{$row->e_nri_o}} @else 0 @endif</td>
						<td>@if($row->e_nri_t) {{$row->e_nri_t}} @else 0 @endif</td>
					</tr>
					
					@endforeach
					
					<tr>
						<th><b>Total</b></th>
						<td><b>{{$total_gen_m}}</b></td>
						<td><b>{{$total_gen_f}}</b></td>
						<td><b>{{$total_gen_o}}</b></td>
						<td><b>{{$total_gen_t}}</b></td>
						<td><b>{{$total_ser_m}}</b></td>
						<td><b>{{$total_ser_f}}</b></td>
						<td><b>{{$total_ser_o}}</b></td>
						<td><b>{{$total_ser_t}}</b></td>
						<td><b>{{$total_grand_m}}</b></td>
						<td><b>{{$total_grand_f}}</b></td>
						<td><b>{{$total_grand_o}}</b></td>
						<td><b>{{$total_grand_t}}</b></td>
						<td><b>{{$total_nri_m}}</b></td>
						<td><b>{{$total_nri_f}}</b></td>
						<td><b>{{$total_nri_o}}</b></td>
						<td><b>{{$total_nri_t}}</b></td>
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