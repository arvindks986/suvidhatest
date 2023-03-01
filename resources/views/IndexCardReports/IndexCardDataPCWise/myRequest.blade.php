@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'My Request')
@section('content')
<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
  <div class="container">
  <div class="row">
  <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> My Requests</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
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
                  <th scope="col">Constituency Type</th>
                  <th scope="col">Constituency Name</th>
                  <th scope="col">Request Date</th>
                  <th scope="col">Approved Date</th>
                  <th scope="col">Approved Status</th>
                  <!--<th scope="col">Status</th>-->
                </tr>
              </thead>
              <tbody>
                <?php //echo '<pre>';print_r($data);die;
                $count=1;?>
                
				@foreach($data as $key => $row)
                <tr>
                  <td>{{$key+1}}.</td>
                  <td>@if($row->for_ac_no) AC @endif @if($row->for_pc_no) PC @endif</td>
                  <td>{{$row->ac_name}}{{$row->PC_NAME}}</td>
				  <td>{{date('d/m/Y h:i A', strtotime($row->request_start_datetime))}}   --   {{date('d/m/Y  h:i A', strtotime($row->request_end_datetime))}}</td>
                  <td>@if($row->approve_start_date)
						{{date('d/m/Y h:i A', strtotime($row->approve_start_date))}}   --   {{date('d/m/Y  h:i A', strtotime($row->approve_end_date))}}
					@else
						--
					@endif
				</td>
                  <td>@if($row->approval_status == 0)
						Pending
					@elseif($row->approval_status == 1)
						Approved
					@else
						Rejects
					@endif</td>
                  <!--<td>Active</td>-->
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