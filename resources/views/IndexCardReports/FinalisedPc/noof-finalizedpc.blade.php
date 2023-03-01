@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'No. oF Finalized PC')
@section('content')

<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(No. oF Finalized PC)</h4></div> 
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
							</p>
							<p class="mb-0 text-right">
							<a href="NoofFinalizedPCPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="NoofFinalizedPCXls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>
				
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped" style="width: 100%;">
              <thead>
                <tr class="table-primary">
                  <th scope="col">Sr. No.</th>
                  <th scope="col">State Name </th>
                  <th scope="col">No Of PCs</th>
                  <th scope="col">Finalised </th>
                  <th scope="col">Not Finalised Yet</th>
                </tr>
              </thead>
              <tbody>
                <?php //echo '<pre>';print_r($data);die;
                $count=1; $totalpc=0; $totalfinalize=0; $totalnotfinalize=0;?>
                
                @forelse($data as $row)
				@php 
				$totalpc+=$row['totalpc'];
				$totalfinalize+=$row['finalize'];
				$totalnotfinalize+=$row['totalpc']-$row['finalize'];
				
				@endphp
                <tr>
                  <td>{{$count}}.</td>
                  <td>{{$row['state']}}</td>
                  <td>{{$row['totalpc']}}</td>
                  <td>{{$row['finalize']}}</td>
                 <td>{{$row['totalpc']-$row['finalize']}}</td>

                </tr>
                 <?php $count++;?>
				 
                @empty
				
                <tr>
                  <td colspan="3">No Record Found</td>
                </tr>
                
                @endforelse
                <tr><td></td>
				 <td>Total</td>
				 <td>{{$totalpc}}</td>
				 <td>{{$totalfinalize}}</td>
				 <td>{{$totalnotfinalize}}</td></tr>
              </tbody>
           </table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
