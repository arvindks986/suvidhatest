@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'My Index Card Requests')
@section('content')
<?php  //$st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
  <div class="container">
  <div class="row">
  <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> My Index Card Requests</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">Andhra Predesh</span> &nbsp;&nbsp; <b></b> 
               </p>
			   
              </div>
			  
			
            </div>
      </div>
  
 <div class="card-body">
 
	<div class="table-responsive">
		
	
<table class="table table-bordered table-striped">
              <thead>
                <tr class="table-primary">
                  <th scope="col">SI No</th>
                  <th scope="col">Constituency Name</th>
                  <th scope="col">Request Date</th>
                  <th scope="col">Approved/Rejected Date</th>
                  <th scope="col">Status</th>
                  <th scope="col">Reason (If Rejected)</th>
                </tr>
              </thead>
              <tbody>
                
				@foreach($data as $kay => $row)
                <tr>
                  <td>{{$kay+1}}.</td>
                  <td>{{$row->PC_NAME}}</td>
                  <td>{{date('d-m-Y', strtotime($row->submitted_at))}}</td>
				  <td>
					@if($row->review_status == '0')
							--
						@else
						  {{date('d-m-Y',strtotime($row->review_at))}}
						@endif
				  </td>
                  <td>
					@if($row->review_status == '1')
							Approved
						@elseif($row->review_status == '2')
							Rejected
						@else
							Pending
						@endif
				  </td>
				  <td>
						@if($row->review_status == '2')							
							{{implode(", ",unserialize($row->issue))}}							
						@else
						  --
						@endif
					</td>
                </tr>
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