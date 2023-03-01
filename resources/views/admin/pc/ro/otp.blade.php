@extends('layouts.theme')
@section('content')
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
        
   <div class="nw-crte-usr">
         <div class="head-title">
          <h3><i><img src="{{ asset('theme/images/icons/tab-icon-010.png')}}" /></i>Verify OTP</h3>
         </div>
              <form class="form-horizontal" id="otp_form" method="POST" action="{{url('ro/verifycandotp') }}" >
                          {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('mobile_otp') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">OTP Verification </label>

                            <div class="col-md-5">
                                <input id="mobile_otp" type="text" class="form-control" name="mobile_otp" value="{{ old('mobile_otp') }}">

                                @if ($errors->has('mobile_otp'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mobile_otp') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                          
                         
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                 <button class="btn btn-primary" type="submit"><< Verify OTP</button>
                                  <a class="btn btn-link" href=""><button class="btn btn-primary" type="button">Resend OTP</button></a>
                            </div>
                        </div>        
           
                  </form>
                </div><!-- End Of nw-crte-usr Div -->
   
      
    
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
  </div> 

@endsection
 
