@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Index Card Select State And PC')
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
			  
			 
              </div>
			  
			
            </div>
      </div>
  
 <div class="card-body" style="">
 
	<form class="form form-inline" method="POST" action="getindexcarddata" style="padding:75px 0px;">
	
	 @csrf
	 <div class="col-sm-12 form-group">

		<div class="col-sm-4">
	    	
	      <select class="form-control" name="st_code" id="st_code" placeholder="Select State" onChange="getPC(this.value);" required>
	      	<option value="">Select State</option>
	      	@foreach($stateList as $stateLists)
	      		<option value="{{$stateLists->ST_CODE}}">{{$stateLists->ST_NAME}}</option>
	      	@endforeach
	      </select>
	    </div>
 
	    <div class="col-sm-4">
	      <select class="form-control" name="pc" id="pc" placeholder="Select PC" required>
	      	<option value="">Select PC</option>
	      	
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

<script src="jquery-3.2.1.min.js" type="text/javascript"></script>
<script>
function getPC(val) {
	$.ajax({
	type: "GET",
	url: "ajaxpccall",
	data:'st_code='+val,
	success: function(data){
		$("#pc").html(data);
		getCity();
	}
	});
}
</script>
@endsection