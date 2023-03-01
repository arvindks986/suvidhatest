@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'POLLING STATION INFORMATION')
@section('content')


<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
	<div class="container-fluid">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(4 - POLLING STATION INFORMATION)</h4></div> 
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
							</p>
							<p class="mb-0 text-right">
							<a href="pollingstationinformation_pdf" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="pollingstationinformation_xls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>
				
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <th colspan="3"></th>
                        <th>Polling Station</th>
                        <th colspan="4">General Electors</th>
                        <th colspan="4">Service Electors</th>
                        <th colspan="4">Grand Total</th>
                    </thead>
                    <tbody>
                        <tr>
                            <th>State/UT</th>
                            <th>PC. No.</th>
                            <th>PC Name</th>
                            <th>Total <br><span style="font-style: italic;font-size: 12px;">(Regular+Auxilary)</span></th>
                            <th>Male</th>
                            <th>Female</th>
                            <th>Third</th>
                            <th>Total</th>
                            <th>Male</th>
                            <th>Female</th>
                            <th>Third</th>
                            <th>Total</th>
                            <th>Male</th>
                            <th>Female</th>
                            <th>Third</th>
                            <th>Total</th>
                        </tr>
                       
						<?php 
						
						$gen_m_sum_tot  = $gen_f_sum_tot = $gen_o_sum_tot =  $ser_m_sum_tot = $ser_f_sum_tot =  $ser_o_sum_tot = $pollingregaux_tot = 0;
						$flag = $stcode = $gen_m_sum  = $gen_f_sum = $gen_o_sum =  $ser_m_sum = $ser_f_sum = $ser_o_sum = $pollingregaux = 0;
						?>
						@foreach ($pollingstations as $pollingstation) 

						<?php 
						
						// if($stcode=='')
						// $stcode=$pollingstation->st_code;
					
						
						
						
						$pollingregaux_tot +=$pollingstation->total_no_polling_station;
						$gen_m_sum_tot += $pollingstation->e_gen_m;
						$gen_f_sum_tot += $pollingstation->e_gen_f;						
						$gen_o_sum_tot += $pollingstation->e_gen_o;						
						$ser_m_sum_tot += $pollingstation->e_ser_m;
						$ser_f_sum_tot += $pollingstation->e_ser_f;
						$ser_o_sum_tot += $pollingstation->e_ser_o;
						
						?>
						@if($stcode!=0 || $stcode!=$pollingstation->st_code)
						
						<tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>{{ $pollingregaux ? : 0 }}</th>
                            <th>{{ $gen_m_sum }}</th>
                            <th>{{ $gen_f_sum }}</th>
                            <th>{{ $gen_o_sum }}</th>
							<th>{{ $gen_m_sum + $gen_f_sum + $gen_o_sum}}</th>
                            <th>{{ $ser_m_sum }}</th>
                            <th>{{ $ser_f_sum }}</th>
                            <th>{{ $ser_o_sum }}</th>
							<th>{{ $ser_m_sum + $ser_f_sum + $ser_o_sum }}</th>
                            <th>{{ $gen_m_sum + $ser_m_sum }}</th>
                            <th>{{ $ser_f_sum + $ser_f_sum }}</th>
                            <th>{{ $gen_o_sum + $ser_o_sum }}</th>
                           
                            <th>{{ $gen_m_sum + $gen_f_sum + $ser_m_sum + $ser_f_sum + $gen_o_sum + $ser_o_sum }}</th>
                        </tr> 
							<?php 
							$gen_m_sum  = $gen_f_sum = $gen_o_sum = $ser_m_sum = $ser_f_sum = $ser_o_sum = $pollingregaux = 0;
						
						?>
						 @endif
						 <?php 
							
						$pollingregaux +=$pollingstation->total_no_polling_station;
						$gen_m_sum += $pollingstation->e_gen_m;
						$gen_f_sum += $pollingstation->e_gen_f;						
						$gen_o_sum += $pollingstation->e_gen_o;						
						$ser_m_sum += $pollingstation->e_ser_m;
						$ser_f_sum += $pollingstation->e_ser_f;
						$ser_o_sum += $pollingstation->e_ser_o;
						 ?>
						@if($stcode=='' || $stcode!=$pollingstation->st_code)
						 <tr>
                            <th colspan="16" class="gry">{{ $pollingstation->st_name }}</th>
                        </tr>
						<?php $stcode = $pollingstation->st_code; ?>
						@endif
						 <tr>
                            <td></td>
                            <td>{{ $pollingstation->pc_no }}</td>
                            <td>{{ $pollingstation->pc_name }}</td>
                            <td>{{ $pollingstation->total_no_polling_station ? : 0 }}</td>
                            <td>{{ $pollingstation->e_gen_m }}</td>
                            <td>{{ $pollingstation->e_gen_f }}</td>
                            <td>{{ $pollingstation->e_gen_o }}</td>
							<td>{{ $pollingstation->e_gen_m + $pollingstation->e_gen_f + $pollingstation->e_gen_o}}</td>
                            <td>{{ $pollingstation->e_ser_m }}</td>
                            <td>{{ $pollingstation->e_ser_f }}</td>
                            <td>{{ $pollingstation->e_ser_o }}</td>
							<td>{{ $pollingstation->e_ser_m + $pollingstation->e_ser_f + $pollingstation->e_ser_o }}</td>
                            <td>{{ $pollingstation->e_gen_m + $pollingstation->e_ser_m }}</td>
                            <td>{{ $pollingstation->e_gen_f + $pollingstation->e_ser_f }}</td>
                            <td>{{ $pollingstation->e_gen_o + $pollingstation->e_ser_o }}</td>
                           
                            <td>{{ $pollingstation->e_gen_m + $pollingstation->e_ser_m + $pollingstation->e_gen_f + $pollingstation->e_ser_f + $pollingstation->e_gen_o + $pollingstation->e_ser_o }}</td>
                        </tr>
					
						<?php
				
						if($stcode!=$pollingstation->st_code){
						$stcode=$pollingstation->st_code;
						$flag=0;
					    
						?>
						
                          
                        
						<?php  }?>
						
                        @endforeach
                       <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>{{ $pollingregaux }}</th>
                            <th>{{ $gen_m_sum }}</th>
                            <th>{{ $gen_f_sum }}</th>
                            <th>{{ $gen_o_sum }}</th>
							<th>{{ $gen_m_sum + $gen_f_sum + $gen_o_sum}}</th>
                            <th>{{ $ser_m_sum }}</th>
                            <th>{{ $ser_f_sum }}</th>
                            <th>{{ $ser_o_sum }}</th>
							<th>{{ $ser_m_sum + $ser_f_sum + $ser_o_sum}}</th>
                            <th>{{ $gen_m_sum + $ser_m_sum }}</th>
                            <th>{{ $gen_f_sum + $ser_f_sum }}</th>
                            <th>{{ $ser_o_sum + $ser_o_sum }}</th>
                           
                            <th>{{ $gen_m_sum + $gen_f_sum + $ser_m_sum + $ser_f_sum + $ser_o_sum + $ser_o_sum}}</th>
                        </tr> 
                       
                        
                        <tr>
                            <td colspan="2" class="blc"><b>All India Total</b></td>
                            <td></td>
                            <td><b>{{$pollingregaux_tot}}</b></td>
                            <td><b>{{$gen_m_sum_tot}}</b></td>
                            <td><b>{{$gen_f_sum_tot}}</b></td>
                            <td><b>{{$gen_o_sum_tot}}</b></td>
							<td><b>{{$gen_m_sum_tot + $gen_f_sum_tot + + $gen_o_sum_tot}}</b></td>
                            <td><b>{{$ser_m_sum_tot}}</b></td>
                            <td><b>{{$ser_f_sum_tot}}</b></td>
                            <td><b>{{$ser_o_sum_tot}}</b></td>
							<td><b>{{$ser_m_sum_tot + $ser_f_sum_tot + $ser_o_sum_tot}}</b></td>
							<td><b>{{$gen_m_sum_tot + $gen_m_sum_tot}}</b></td>
                            <td><b>{{$gen_f_sum_tot + $ser_f_sum_tot}}</b></td>
                            <td><b>{{$gen_o_sum_tot + $ser_o_sum_tot}}</b></td>
							<td><b>{{$gen_m_sum_tot + $gen_f_sum_tot + $ser_m_sum_tot + $ser_f_sum_tot + $gen_o_sum_tot + $ser_o_sum_tot}}</b></td>
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
