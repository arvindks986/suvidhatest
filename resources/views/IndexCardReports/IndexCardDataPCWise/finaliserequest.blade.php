@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Request')
@section('bradcome', 'Index Card Finalize Request Form')
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

.card-header:before
{
	visibility: hidden;
}

input.form-control.ddfrm {
    height: 45px;
}

button.multiselect.dropdown-toggle.btn.btn-default {
    background: #ea5580;
    color: #fff;
    border: none;
    padding: 10px;
    font-size: 14px;
}
	
</style>


<?php  $st=getstatebystatecode($user_data->st_code); 



  ?> 
<section class="">
	<!----Success Message------>
    @if(session()->has('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      	{{session()->get('success')}}
    </div>
    @endif
	
	<!-- @if(session()->has('errors'))
    <div class="alert alert-danger alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      	{{session()->get('errors')}}
    </div>
    @endif -->

    @if(session()->has('errors'))
    <div class="alert alert-danger alert-dismissible">
    	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <ul style="margin-top: 1%;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!--Basic Information-->
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col">
							<h4> Index Card Finalize Request Form
							</h4>
						</div>
						<div class="col">
							<p class="mb-0 text-right">
								
							</p>
						</div>
					</div>
				</div>
				<div class="card-body">
					<form class="form-horizontal" action="finalizerequestsubmit" method="POST" enctype= "multipart/form-data">
						@csrf
									
						<input type="hidden" name="st_code" value="">
									
						<!-- pc box -->
						<div class="mainContainerForm"  data-const="PC">
						<div class="dynamicBox" data-count="0">
						
						<div class="form-group">
						    <label for="pcno" class="col-sm-2 control-label" data-validation="email">PC No.</label>
						    <div class="col-sm-8">
						      <select class="form-control acselectbox" id="pcno" name="pcno" >
							  <label value=""> PC Name-</label>
								@foreach($resultPCs as $value)
						      	<option value="{{$value->PC_NO}}">{{$value->pc_name}}</option>
						      	@endforeach
						      </select>
						    </div>
						</div>
						
						<div class="form-group">
							<label for="rField" class="col-sm-2 control-label">Upload Index Card</label>
							<div class="col-sm-8">
								<input type="file" data-validation="mime size"  data-validation-allowing="pdf"
		 						    name="file_upload" class="form-control ddfrm"  Placeholder="" >
							</div>
						</div>
						
						</div>
						</div>
						<!-- pc box -->
						<div class="col-sm-3 form-group pull-right hide">
							<button type="submit" class="btn btn-primary" value="Validate!">Submit</button>
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



@endsection