@extends('admin.layouts.pc.theme')
@section('title', 'Officer-details')
@section('bradcome', 'Officer-Profile')
@section('description', '')
@section('content') 
 <?php   
		    $st=getstatebystatecode($user_data->st_code); 
       
  ?>
<main role="main" class="inner cover mb-3">
<section>	 
	 <form enctype="multipart/form-data" id="election_form" method="POST"  action="{{url('pcdeo/updateuser') }}" >
	  {{ csrf_field() }}
  <div class="container">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class="card-header">
                <div class="row">
                 <div class="col"><h4>Profile Details</h4></div> 
                <div class="col"><p class="mb-0 text-right"><b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
								 <b></b> <span class="badge badge-info"></span>&nbsp;&nbsp;  
								 </p></div>
                </div> <!--End row-->
                </div><!--End card-header-->
								@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
   
		<div class="container p-0">
			<div class="row">
				<div class="col-md-12">
				<div class="card">
                <div class="card-body">
								<div class="row">
				              	<div class="col"> 
                    <div class="form-group row">
                      <label class="col-sm-3">Name<sup>*</sup></label>
											     <div class="col">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ $getofficerdetails[0]->name }}">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                        <strong><span style="color:red;">{{ $errors->has('name') }}</span></strong>
                                    </span>
                                    @endif
                                </div>
                       </div>
											 <div class="nameerrormsg errormsg errorred"></div>
											 
											 <div class="line"></div>
											 <div class="row">
															<div class="col"> 
																	<div class="form-group row">
																		<label class="col-sm-3">Designation<sup>*</sup></label>
																				<div class="col">
																									<input id="designation" type="text" class="form-control" name="designation" value="{{ $getofficerdetails[0]->designation }}" readonly>
																									@if ($errors->has('designation'))
																											<span class="help-block">
																											<strong><span style="color:red;">{{ $errors->has('designation') }}</span></strong>
																									</span>
																									@endif
																							</div>
																		</div>
																		<div class="currenterrormsg errormsg errorred"></div>

																		<div class="line"></div>

																		<div class="row">
				       	<div class="col"> 
                    <div class="form-group row">
                      <label class="col-sm-3">Email<sup>*</sup></label>
											     <div class="col">
                                    <input id="email" type="text" class="form-control" name="email" value="{{ $getofficerdetails[0]->email }}">
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong><span style="color:red;">{{ $errors->has('email') }}</span></strong>
                                    </span>
                                    @endif
                                </div>
                             </div>
														 <div class="emailerrormsg errormsg errorred"></div>

												<div class="line"></div>
												
								<div class="row">
				            <div class="col"> 
                    <div class="form-group row">
                      <label class="col-sm-3">Phone<sup>*</sup></label>
											     <div class="col">
                                    <input id="Phone_no" type="text" class="form-control" name="Phone_no" value="{{ $getofficerdetails[0]->Phone_no }}">
                                    @if ($errors->has('Phone_no'))
                                        <span class="help-block">
                                        <strong><span style="color:red;"></span></strong>
                                    </span>
                                    @endif
                                </div>
                             </div>
														 <div class="Phone_noerrormsg errormsg errorred"></div>

												<div class="line"></div>
														
				 
				 <div class="form-group row float-right">       
					  <div class="col">
						<button type="submit" id="profileUpdate" name="profileUpdate" value={{ $getofficerdetails[0]->id}} class="btn btn-primary">Submit</button>
					  </div>
				 </div>
                
				  </div>
				</div>
                </div>
              </div>
				</div>
			</div>
		</div>	
	  </form>
		</section>
	</main>
@endsection

@section('script')

<script>


jQuery(document).ready(function(){  
	
	//Check Validation
    jQuery('#profileUpdate').click(function(){
		var name = jQuery('input[name="name"]').val();
		var email = jQuery('input[name="email"]').val();
		var mobile = jQuery('input[name="Phone_no"]').val();
		
		if(name == ''){
      jQuery('.errormsg').html('');
			jQuery('.nameerrormsg').html('Please enter name in english');
			jQuery( "input[name='name']" ).focus();
			return false;
		}
		
	
	
		if(email == ''){
			jQuery('.errormsg').html('');
			jQuery('.emailerrormsg').html('Please enter email');
			jQuery( "input[name='email']" ).focus();
			return false;
		}
		if(IsEmail(email)==false){
          jQuery('.errormsg').html('');
		  jQuery('.emailerrormsg').html('Please enter valid email');
		  jQuery( "input[name='email']" ).focus();
          return false;
        }
		if(mobile == ''){
			jQuery('.errormsg').html('');
			jQuery('.Phone_noerrormsg').html('Please enter valid mobile number');
			jQuery( "input[name='Phone_no']" ).focus();
			return false;
		}
	/*	if(mobile.length!=10){
			jQuery('.errormsg').html('');
			jQuery('.Phone_noerrormsg').html('Mobile number must be 10 digits');
			jQuery( "input[name='Phone_no']" ).focus();
			return false;
		}
	*/
	
		
	});
	jQuery("#Phone_no").keypress(function (e) {
		//if the letter is not digit then display error and don't type anything
		   var length = jQuery(this).val().length;
		   if(length > 9) {
				return false;
		   } else if(e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
				jQuery('.errormsg').html('');
				jQuery('.Phone_noerrormsg').html('Digits Only').show().fadeOut("slow");
				jQuery( "input[name='Phone_no']" ).focus();
				return false;
		   } else if((length == 0) && (e.which == 48)) {
				return false;
		   }
    });
});
function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if(!regex.test(email)) {
    return false;
  }else{
    return true;
  }
}
</script>
@endsection