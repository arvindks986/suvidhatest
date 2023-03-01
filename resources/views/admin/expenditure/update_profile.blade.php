@extends('admin.layouts.pc.ecrp-theme')
@section('title', 'ECRP')
@section('bradcome', 'Candidate Profile')
@section('description', '')
@section('content') 
@php
 //dd($candidateData);
 $partyDetails=\app(App\commonModel::class)->getparty($candidateData->party_id);
 $stDetails=\app(App\commonModel::class)->getstatebystatecode($candidateData->st_code);
 $distDetails=\app(App\commonModel::class)->getdistrictbydistrictno($candidateData->st_code,$candidateData->district_no);
 //dd($distDetails);
@endphp 


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

<div class="col">  

    
<form class="form-horizontal" action="{{url('/profile_update')}}" method="post" autocomplete="off">
	{{ csrf_field() }}
	<input type="hidden" value="{{$candidateData->nom_ids}}" name="nom_id">
		<input type="hidden" value="{{$candidateData->candidate_ids}}" name="candidate_id">

<!--  -->
<div class="form-group row">
<label class="col-sm-2">Candidate Details <sup style="color:red">*</sup></label>
<div class="col">
	<input type="text" value="Candidate"  class="form-control" readonly/>	
</div>  
<label class="col-sm-2">Political Party/Independent <sup style="color:red">*</sup></label>
<div class="col">
	<select name="party_master" class="form-control" readonly disabled>
		<option value="{{$candidateData->party_id}}">{{$partyDetails->PARTYNAME}}</option>
	</select>
</div> 
<span class="text-danger"></span>
</div>
<!--  -->
<div class="form-group row">
<label class="col-sm-2">Name <sup style="color:red">*</sup></label>
<div class="col">

	<input type="hidden" value="" name="user_login_id" class="form-control" />
	<input type="text" value="{{$candidateData->cand_name}}" name="name" class="form-control" readonly/>

	<span class="text-danger">{{ $errors->first('cand_name') }}</span>
</div>  
<label class="col-sm-2">Father's / Husband's Name <sup style="color:red">*</sup></label>
<div class="col">

	<input type="text" value="{{$candidateData->candidate_father_name}}" name="father_name" class="form-control" readonly/>
	
</div> 
</div>

<div class="line"></div>
<div class="form-group row">
<label class="col-sm-2">Email <sup style="color:red">*</sup></label>

<div class="col">
	<input type="email" value="{{$candidateData->cand_email}}" name="email" class="form-control" readonly/>
	
</div>  
<label class="col-sm-2">Mobile No <sup style="color:red">*</sup></label>
<div class="col">
<input type="tel" value="{{$candidateData->cand_mobile }}" name="mobile" class="form-control" maxlength="10" readonly/>
</div>
</div>


<div class="form-group row">
	<label class="col-sm-2">Gender<sup style="color:red">*</sup></label>
<div class="col">
	<select name="radio_stacked" id="ac" class="form-control" readonly disabled>
	<option value="male">
		@if(($candidateData->cand_gender  == 'third'))
		Other
		@elseif(($candidateData->cand_gender == 'male'))
		Male
		@elseif(($candidateData->cand_gender == 'female'))
		Female
		@else
		{{$candidateData->cand_gender}}
		@endif
	</option>
	<option value="male">Male</option>
	<option value="female">Female</option>
	<option value="third">Other</option>
	</select>
	
<span class="text-danger">{{ $errors->first('cand_gender') }}</span>



</div>  

<label class="col-sm-2">Date of Birth <sup style="color:red">*</sup></label>

<div class="col">
	<input type="text" value="{{$candidateData->cand_dob}}" id="datetimepicker3s" name="dob" class="form-control" readonly/>

</div>  
</div>


<div class="line"></div>	

<div class="form-group row">
<label class="col-sm-2">Address <sup style="color:red">*</sup></label>

<div class="col">

<textarea readonly name="Address1" id="" cols="10" rows="4" class="form-control" placeholder="Enter current address" required>{{$candidateData->candidate_residence_address}}</textarea>
<br />	 
</div>  

</div>
<div class="line"></div>

<div class="form-group row">
<div class="col-sm-2"><label for="statename">State Name <sup style="color:red">*</sup></label></div>
<div class="col">
<input type="text" value="{{$stDetails->ST_NAME}}" id="" name="dob" class="form-control" readonly/>
</div>  
<div class="col-sm-2"><label for="statename">District <sup style="color:red">*</sup></label></div>
<div class="col">
<input type="text" value="{{$distDetails->DIST_NAME}}" id="datetimepicker3s" name="dob" class="form-control" readonly/>
</div> 
<div class="col-sm-2"><label for="statename">ECRP <sup style="color:red">*</sup></label></div>
<div class="col">
<input type="text" value="{{$candidateData->ecrp_reg_no}}" maxlength="6" id="" name="ecrp" class="form-control ackSch1input " required/>
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

 $(".ackSch1input").on("keypress keyup blur",function (event) {    
       $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
</script>
@endsection