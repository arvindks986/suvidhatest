@extends('layouts.login')
 
@section('content')
<?php  $url = URL::to("/");  ?>
<main>
   <section class="main-box">
     <div class="circle peach-gradient">
            <img src="{{ asset('theme/img/vendor/background.png') }}" alt=""></div>
    <div class="container-fluid h-100">
   
         
         
    <div class="row justify-content-center align-items-center h-100" style="width:100%; margin:0 auto;">
  
  <div class="col-md-6 login-page "> 
        <figure class="evm-logo officerlogin">
          <span style="margin: auto;">@if($url=="https://suvidha.eci.gov.in" || $url=="http://suvidha.eci.gov.in")
 
                <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo1.png') }}" alt="" />
               @else
                 <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo.png') }}" alt="" />
              @endif
                  <p>Election Commission of India </p> </span></figure> </div>
	

 <div class="col-md-6 loginDiv">
    <div class="login-right">
	
	<fieldset>
		<legend class="text-center">OTP Verification</legend>
	
	
	<p class="text-center">Please enter your one time password sent to your phone number</p>
	
               
  <form class="log-frm-area" method="POST" action="{{ url('customlogin') }}" autocomplete='off' enctype="x-www-urlencoded" id="loginval">
      {{ csrf_field() }}

      <span class="help-block"><strong>{{ Session::get('opterror') }} </strong></span>
        
        <!--MOBILE NUMBERT FIELD STARTS-->
        <div class=" form-inline{{ $errors->has('mobile') ? ' has-error' : '' }}">
          
          <input id="mobile" type="text" class="form-control col-md-12" name="mobile" value="{{$mobile}}"  placeholder="Mobile Number" maxlength="10" minlength="10" readonly="readonly">
          
          @if ($errors->has('mobile'))
                <span class="invalid-feedback"> <strong>{{ $errors->first('mobile') }}</strong>   </span>
          @endif
        </div>
        <!--MOBILE NUMBER FIELD ENDS-->
      
       <!--OTP FIELD STARTS-->
	   <div class="form-group   flex-column flex-md-row  mt-3">
	   
	   <div class="d-flex align-items-center m-inline{{ $errors->has('otp') ? ' has-error' : '' }}">
            
			<input id="otp" type="password" class="form-control " name="otp" value="{{ old('otp') }}"  placeholder="Mobile Otp" maxlength="6" minlength="6"/>&nbsp; &nbsp;
			<input type="submit" class="btn btn-primary ml-auto" value="Verify OTP"> 
			</div>
	
	   
	   </div>
        @if ($errors->has('otp'))
              <span class="invalid-feedback"> <strong>{{ $errors->first('otp') }}</strong>   </span>
         @endif
        <!--OTP FIELD ENDS-->

        <div class="form-inline">
         
        
          <a href="{{url('/login')}}" class="btn btn-link ">Back </a>
		   <a href="" class="btn btn-link ml-auto ">Resend OTP</a>
        </div>


      <!-- <div id="clockdiv" class="clockdiv"></div> -->
                      
    </form><br>

     <div class="form-inline">
     @if (session('error'))
           <div class="alert alert-info">{{ session('error') }}</div>
      @endif
      
      @if($errors->any())
        <div class="alert alert-info">{{$errors->first()}}</div>
      @endif

      @if (session('success'))
           <div class="alert alert-info success">{{ session('success') }}</div>
      @endif

    </div>
    
    <div  id="otpsend"></div> 
     
     <div id="attempts"></div>     
              
    </fieldset>
    </div>    
    </div>    
    </div>
  </div>
</section>
  
</main>
 

<script src="{{ asset('theme/vendor/jquery/jquery.min.js') }}"></script>
<!-- Validation  JavaScript -->
<!--**********DCO FORM VALIDATION STARTS**********-->
    <script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
    <script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
    <!--**********DCO FORM VALIDATIONS SCRIPT**********-->
    <script src="{{ asset('formvalidations/loginformvalidations.js') }}"></script>
    <!--**********DCO FORM VALIDATION ENDS*************-->
<script type="text/javascript">
//RESEND OTP LOGIN STARTS


$(document).on("click", ".resendotpform", function () {    

    var mobile = $("#mobile").val();

        $.ajax({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: APP_URL + '/resendotp',                
            type: 'POST',
            data: 'mobile='+mobile,
            success: function (data) {
                if(data == 1){
                    $('#otpsend').hide();
                    $('#attempts').addClass('alert text-danger').text('Reached maximum otp attempts. Request for new OTP.');
                }else if(data == 3){
                    $('#otpsend').hide();
                    $('.success').hide();
                    $('#attempts').addClass('alert text-info').text('Can Send only 1 OTP per minute.');
                }else{
                  $('#attempts').hide();
                  $('#otpsend').addClass('alert text-success').text('OTP successfully Send.');
                         //$('#attempts').hide();
                }
                
            }
        });
    
});
//RESEND OTP LOGIN ENDS  
</script>

@endsection
