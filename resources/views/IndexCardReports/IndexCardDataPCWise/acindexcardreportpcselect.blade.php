@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Index Card Select PC')
@section('content')


<style>	
.card-header:before{
    visibility: hidden;
}
img#theImg{
	display: none;
}

#pc{
	width: 100%;
}

</style>
<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
  <div class="container">
  <div class="row">
  <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> Election Index Card For Lok Sabha Elections Only <br>(At Election where Electronics Voting Machines are used)</h4></div> 
              <div class="col">
			  <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
               </p>
			 
              </div>
			  
			
            </div>
      </div>
  
 <div class="card-body" style="">
 
	<form class="form form-inline" method="POST" action="getindexcarddata" style="padding:75px 0px;">
	
	 
	 <div class="col-sm-12 form-group">	   
	    <div class="col-sm-8">
	    	@csrf
	    	<input type="hidden" name="st_code" value="{{$st_code}}">
	      <select class="form-control" name="pc" id="pc" placeholder="Select PC" required>
	      	<option value="">Select PC</option>
	      	@foreach($pcList as $pcLists)
	      		<option value="{{$pcLists->PC_NO}}">{{$pcLists->PC_NAME}}</option>
	      	@endforeach
	      </select>
	    </div>

			<div class="col-sm-2">

	    		<button type="submit" class="btn btn-primary btn-lg pull-right">Show</button>

           </div>


	</div>

</form>
 </div>
 </div>
 </div>
 </div>
 </section>

@endsection