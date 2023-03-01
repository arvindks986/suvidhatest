@extends('layouts.theme')
@section('title', 'Permission')
@section('content')
<main role="main" class="inner cover mb-3">

<section class="mt-5"></section>
<section class="">
<div class="container">
<div class="row">

<div class="col-md-12">
<div class="card">
<div class="card-header d-flex align-items-center">
<h4>Applicant Personal Details</h4>
</div>
<div class="card-body">
	@if(session()->has('message'))
	    <div class="alert alert-success">
	        {{ session()->get('message') }}
	    </div>
	@endif
<div class="row">


	<?php $permission=$value->permission_request_status;?>

<div class="col">  

    
<form class="form-horizontal" action="{{url('/update')}}" method="post" autocomplete="off">
	{{ csrf_field() }}
<!--  -->
<div class="form-group row">
<label class="col-sm-2">Applicant Type <sup style="color:red">*</sup></label>
<div class="col">
	<input type="text" value="{{$users=Session::get('Applicant_type')}}"  class="form-control" readonly/>	
</div>  
<label class="col-sm-2">Political Party/Independent <sup style="color:red">*</sup></label>
<div class="col">
	<select name="party_master" class="form-control">
		<option value="{{$value->CCODE}}">{{$value->PARTYNAME}}</option>
	</select>
</div> 
<span class="text-danger">{{ $errors->first('district') }}</span>
</div>
<!--  -->
<div class="form-group row">
<label class="col-sm-2">Name <sup style="color:red">*</sup></label>
<div class="col">

	<input type="hidden" value="{{$value->user_login_id}}" name="user_login_id" class="form-control" required/>
	@if($permission>0)
	<input type="text" value="{{$value->name}}" name="name" class="form-control" readonly/>
	@else
	<input type="text" value="{{$value->name}}" name="name" class="form-control" required/>
	@endif
	<span class="text-danger">{{ $errors->first('name') }}</span>
</div>  
<label class="col-sm-2">Father's / Husband's Name <sup style="color:red">*</sup></label>
<div class="col">
	@if($permission>0)
	<input type="text" value="{{$value->fathers_name}}" name="father_name" class="form-control" readonly/>
	@else
	<input type="text" value="{{$value->fathers_name}}" name="father_name" class="form-control" required/>
	@endif
	<span class="text-danger">{{ $errors->first('father_name') }}</span>
</div> 
</div>

<div class="line"></div>
<div class="form-group row">
<label class="col-sm-2">Email <sup style="color:red">*</sup></label>

<div class="col">
@if($permission>0)
	<input type="email" value="{{$value->email}}" name="email" class="form-control" required/>
	@else
	<input type="email" value="{{$value->email}}" name="email" class="form-control" required/>
	@endif
	<span class="text-danger">{{ $errors->first('email') }}</span>	
</div>  
<label class="col-sm-2">Mobile No <sup style="color:red">*</sup></label>
<div class="col">
<input type="tel" value="{{$value->mobileno}}" name="mobile" class="form-control" maxlength="10" readonly/>
</div>
</div>


<div class="form-group row">
	<label class="col-sm-2">Gender<sup style="color:red">*</sup></label>
<div class="col">
	<select name="radio_stacked" id="ac" class="form-control">
	<option value="male">
		@if(($value->gender == 'third'))
		Other
		@elseif(($value->gender == 'male'))
		Male
		@elseif(($value->gender == 'female'))
		Female
		@else
		{{$value->gender}}
		@endif
	</option>
	<option value="male">Male</option>
	<option value="female">Female</option>
	<option value="third">Other</option>
	</select>
	
<span class="text-danger">{{ $errors->first('email') }}</span>



</div>  

<label class="col-sm-2">Date of Birth <sup style="color:red">*</sup></label>

<div class="col">
	@if($permission>0)
	<input type="text" value="{{$value->dob}}" id="datetimepicker3" name="dob" class="form-control" required/>
	@else
	<input type="text" value="{{$value->dob}}" id="datetimepicker3" name="dob" class="form-control" required/>
	@endif

<span class="text-danger">{{ $errors->first('dob') }}</span>
</div>  
</div>


<div class="line"></div>	

<div class="form-group row">
<label class="col-sm-2">Address <sup style="color:red">*</sup></label>

<div class="col">

<textarea name="Address1" id="" cols="10" rows="4" class="form-control" placeholder="Enter current address" required>{{$value->address}}</textarea>
<br />	 
</div>  

</div>
<div class="line"></div>

<div class="form-group row">
<div class="col-sm-2"><label for="statename">State Name <sup style="color:red">*</sup></label></div>
<div class="col">
<div class="custom-select1" style="width:100%;">

	<select name="state" id="state" class="form-control" @if($permission>0) disabled="disabled" @endif>
	<option value="">Select</option>
	@foreach($states as $result)  
		@if($value->state_id == $result['code'])
		<option value="{{ $result['code'] }}" selected="selected"> {{ $result['name'] }}</option>
		@else
		<option value="{{ $result['code'] }}"> {{ $result['name'] }}</option>
		@endif
	@endforeach 
</select>

<span class="text-danger">{{ $errors->first('state') }}</span>
</div>
</div>  
<div class="col-sm-2"><label for="statename">District <sup style="color:red">*</sup></label></div>
<div class="col"><div class="custom-select1" style="width:100%;">
	<select name="district" id="district" class="form-control" @if($permission>0) disabled="disabled" @endif>
	<option value="">Select</option>
	@foreach($districts as $result)  
		@if($value->district_id == $result['code'] && $result['st_code'] == $value->state_id)
		<option value="{{ $result['code'] }}" data-state="{{ $result['st_code'] }}" selected="selected"> {{ $result['name'] }}</option>
		@else
		<option value="{{ $result['code'] }}" data-state="{{ $result['st_code'] }}"> {{ $result['name'] }}</option>
		@endif
	@endforeach 
</select>
<span class="text-danger">{{ $errors->first('district') }}</span>

</div>
</div> 
</div> 
<div class="form-group row">
<div class="col-sm-2"><label for="statename">AC <sup style="color:red">*</sup></label></div>
<div class="col"><div class="custom-select1" style="width:100%;">
<select name="ac" id="ac" class="form-control" @if($permission>0) @endif>
	<option value="">Select</option>
	@foreach($acs as $result)  
		@if($value->ac_id == $result['code']&& $result['st_code'] == $value->state_id && $value->district_id)
		<option value="{{ $result['code'] }}" data-state="{{ $result['st_code'] }}" selected="selected"> {{ $result['name'] }}</option>
		@elseif($result['st_code'] == $value->state_id && $result['dist_code'] == $value->district_id)
		<option value="{{ $result['code'] }}" data-state="{{ $result['st_code'] }}" data-district="{{ $result['dist_code'] }}"> {{ $result['name'] }}</option>
		@endif
	@endforeach 
</select>
<span class="text-danger">{{ $errors->first('ac') }}</span>

</div>
</div> 


</div> 

<div class="form-group row float-right">       
<div class="col">
<input type="submit" value="Submit" class="btn btn-primary">
</div>
</div>
</form>
</div>


</div>
</div>
</div>
</div>
</div>
</div>	  
</section>

</main>
@endsection
@section('script')


<script>
jQuery(function(){
jQuery('#datetimepicker3').datetimepicker({
format: 'YYYY-MM-DD',
useCurrent: false, 
maxDate: new Date()	 
 });
       
});

 
jQuery(document).ready(function(){
	//alert("hello");
	jQuery("select[name='state']").change(function()
	{
		var stcode = jQuery(this).val();
		//alert(stcode);
		jQuery.ajax({
		url: "{{url('/getDistrictsval')}}",
		type: 'GET',
		dataType: 'json',
		data: {stcode:stcode},
		success: function(result){
		//alert(result);
		var distselect = jQuery('form select[name=district]');
		distselect.empty();
		var statehtml = '';
		statehtml = statehtml + '<option value=""> Select District</option> ';
		jQuery.each(result,function(key, value) {
		statehtml = statehtml + '<option value="'+value.DIST_NO+'">'+value.DIST_NAME+'</option>'; 
		jQuery("select[name='district']").html(statehtml);
		});
		var statehtml_end = '';
		jQuery("select[name='district']").append(statehtml_end)
		}
		});
	
	});

	jQuery("#district").change(function()
	{
		///alert('test');
		var stcode = jQuery("select[name='state']").val();
		var district = jQuery(this).val();
		//alert(stcode);
		jQuery.ajax({
		url: "{{url('/getACListsval')}}",
		type: 'GET',
		dataType: 'json',
		data: {stcode:stcode,district:district},
		success: function(result){
		//alert(result);
		var acselect = jQuery('select[name="ac"]');
		acselect.empty();
		var achtml = '';
		achtml = achtml + '<option value=""> Select AC</option> ';
		jQuery.each(result,function(key, value) {
		achtml = achtml + '<option value=' + value.AC_NO + '>' + value.AC_NAME + '</option>';
		jQuery("select[name='ac']").html(achtml);		
		});
		var achtmlend = '';
		jQuery("select[name='ac']").append(achtmlend)
		}
		});
	});

});
</script>
@endsection