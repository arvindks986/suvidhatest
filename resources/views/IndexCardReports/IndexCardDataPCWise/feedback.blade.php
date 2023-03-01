@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Index Card Select PC')
@section('content')
<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/>

<style type="text/css">
	
	ul.multiselect-container.dropdown-menu.show {
    bottom: -46px;
    overflow: hidden;
    width: 100% !important;
    height: 250px;
    position: absolute;
    border: 4px solid #eee !important;
}

button.multiselect.dropdown-toggle.btn.btn-default {
    background: #ea5580;
    color: #fff;
    border: none;
    padding: 10px;
    font-size: 14px;
}
	
</style>


<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
	<!----Success Message------>
    @if(session()->has('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      {{session()->get('success')}}
    </div>
    @endif

    <!--Basic Information-->
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col">
							<h4> Election Index Card For Lok Sabha Elections Only 
								<br>(Correction Request Form)
							</h4>
						</div>
						<div class="col">
							<p class="mb-0 text-right">
								
							</p>
						</div>
					</div>
				</div>
				<div class="card-body">
					<form class="form-horizontal" action="feedbackSubmit" method="POST" enctype= multipart/form-data>
						@csrf
						<div class="row">
							<div class="col-sm-8 col-offset-sm-2" style="margin: auto;text-align: center;">
								Select Change Request for 
								<input type="radio" name="selectChange" value="AC"> AC 
								<input type="radio" name="selectChange" value="PC"> PC 
							</div>
						</div>
						<div class="mainContainerForm" data-const="AC">
						<div class="dynamicBox" data-count="0">
						<div class="form-group">
						    <label for="acno" class="col-sm-6 control-label">AC No.</label>
						    <div class="col-sm-10">
						      <select class="form-control acselectbox" id="acno" name="acno[]" multiple="multiple">
						      	@foreach($resultACs as $value)
						      	<option value="{{$value->AC_NO}}">{{$value->ac_name}}</option>
						      	@endforeach
						      </select>
						    </div>
						</div>
						<div class="row">
							<div class="col-sm-6">
							    <label for="rStartDate" class="col-sm-6 control-label">Request Start Date</label>
							    <div class="col-sm-8">
							      <input type="text" class="form-control" id="rStartDate" name="rStartDateAC">
							    </div>
							</div>
							<div class="col-sm-6">
								<label for="rEndDate" class="col-sm-6 control-label">Request End Date</label>
							    <div class="col-sm-8">
							      <input type="text" class="form-control" id="rEndDate" name="rEndDateAC">
							    </div>
							</div>
						</div>
						<div class="form-group">
							<label for="rField" class="col-sm-2 control-label">Request Fields</label>
							<div class="col-sm-10">
								<input type="text" name="rFieldAC" class="form-control" id="rField" Placeholder="Coma(,) Saprated Fields Required To Be Updated.">
							</div>
						</div>
						<div class="form-group">
							<label for="rFieldDesc" class="col-sm-2 control-label">
								Request Description								
							</label>
							<div class="col-sm-10">
								<textarea name="rFieldDescAC" class="form-control" id="rFieldDesc"></textarea>
							</div>
						</div>
						</div>
						</div>
						<!-- pc box -->
						<div class="mainContainerForm"  data-const="PC">
						<div class="dynamicBox" data-count="0">
						<div class="form-group">
						    <label for="pcno" class="col-sm-2 control-label">PC No.</label>
						    <div class="col-sm-10">
						      <select class="form-control acselectbox" id="pcno" name="pcno[]" multiple="multiple">
						      	@foreach($resultPCs as $value)
						      	<option value="{{$value->PC_NO}}">{{$value->pc_name}}</option>
						      	@endforeach
						      </select>
						    </div>
						</div>
						<div class="row">
							<div class="col-sm-6">
							    <label for="rStartDate" class="col-sm-6 control-label">Request Start Date</label>
							    <div class="col-sm-8">
							      <input type="text" class="form-control" id="rStartDate2" name="rStartDatePC">
							    </div>
							</div>
							<div class="col-sm-6">
								<label for="rEndDate" class="col-sm-6 control-label">Request End Date</label>
							    <div class="col-sm-8">
							      <input type="text" class="form-control" id="rEndDate2" name="rEndDatePC">
							    </div>
							</div>
						</div>
						<div class="form-group">
							<label for="rField" class="col-sm-2 control-label">Request Fields</label>
							<div class="col-sm-10">
								<input type="text" name="rFieldPC" class="form-control" id="rField"  Placeholder="Coma(,) Saprated Fields Required To Be Updated.">
							</div>
						</div>
						<div class="form-group">
							<label for="rFieldDesc" class="col-sm-2 control-label">
								Request Description								
							</label>
							<div class="col-sm-10">
								<textarea name="rFieldDescPC" class="form-control" id="rFieldDesc"></textarea>
							</div>
						</div>
						</div>
						</div>
						<!-- pc box -->
						<div class="form-group pull-right hide">
							<button type="submit" class="btn btn-primary">Submit</button>
							<button type="reset" class="btn btn-primary">Clear</button>
						</div>
					</form>					
				</div>
			</div>
		</div>
	</div>
</section>
@endsection

@section('script')


<script src="js/jquery.datetimepicker.js"></script>


<script>
$('#rStartDate').datetimepicker({
});

$('#rEndDate').datetimepicker({
});
$('#rStartDate2').datetimepicker({
});

$('#rEndDate2').datetimepicker({
});
</script>





<script type="text/javascript">
	$(document).ready(function(){
		$('.mainContainerForm').hide();
		$('.hide').hide();
	});
	$('.acselectbox').multiselect({
		includeSelectAllOption: true,
		buttonWidth: '381px',
		maxHeight: 350,
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		nonSelectedText: 'Select an Option',

	});
	$('.multiselect-clear-filter > i').removeAttr('class').attr('class','fa fa-search');
	$(document).on('change','input[name="selectChange"]',function(){
		var source = $.trim($(this).val());
		if(source == 'PC'){
			var reverseSource = 'AC';
		}else{
			var reverseSource = 'PC';
		}
		$('div[data-const="'+source+'"]').show();
		$('div[data-const="'+reverseSource+'"]').hide();
		$('.hide').show();
	});
</script>
@endsection