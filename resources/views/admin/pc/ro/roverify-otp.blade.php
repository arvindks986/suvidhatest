@extends('admin.layouts.theme')
@section('content') 
       
<link href="{{ asset('admintheme/main.css') }}" rel="stylesheet">
<div class="container-fluid">
  <!-- Start Parent Wrap div -->  
    <div class="parent-wrap">
    <!-- Start Child Area Div --> 
    <div class="child-area">
     <!-- Start Page Content Div -->  
    <div class="page-contant">
      <div class="head-title">
        <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-010.png')}}" /></i>Print Receipt</h3>
      </div>
                <ul class="steps" id="progressbar">
                  <li class="step">QR SCAN</li>
                  <li class="step">Verify Nomination </li>
                  <li class="step">Decision by RO</li>
                  <li class="step">Final Receipt</li>
                  <li class="step active">Verify OTP</li>
                  <li class="step">Print Receipt</li>
                </ul>
    <div class="row">
     <div class="nomination-fieldset">
              <form method="POST" id="otp" action="{{url('ro/verify-finalize-otp') }}" autocomplete='off'>
                {{ csrf_field()}}    
                <input type="hidden" name="candidate_id" value="{{isset($candidate_id)?$candidate_id:old('candidate_id')}}">
                <input type="hidden" name="nom_id" value="{{isset($nom_id)?$nom_id:old('nom_id')}}">
                <input type="hidden" name="mobile_otp" value="{{$mobile_otp}}">
                
                          <span class="help-block">
                              <strong>{{ Session::get('opterror') }} </strong>
                          </span>
                    
                    <div class="nomination-detail {{ $errors->has('otpvalue') ? ' has-error' : '' }}">
                    <strong>  OTP hasbeen send on your (Login member) Mobile Number:- <span class="pagespanred">*</span></strong>
                <input type='text'  name="otpvalue" id="otpvalue" class="nomination-field-2" value="{{old('otpvalue')}}" placeholder="Enter OTP" />
                <span id="err"  style="color:red;"></span>  
                   @if ($errors->has('otpvalue'))  <span style="color:red;"><strong>{{ $errors->first('otpvalue') }}</strong></span>  @endif
                 <br>
                 <div id="clockdiv"></div>
                  
                 </div>
                  <?php  $url = URL::to("/");  ?>
                  <div class="btns-actn">
                            <input type="submit" value="Verify OTP"> 
                  <input type="button" value="Cancel" onclick="location.href = '{{$url}}/ro/applicant';">
                             
                    </div> 
              </form> 
              <form method="POST"  action="{{url('ro/resend-otp') }}">
                        {{ csrf_field()}}   
                     <input type="hidden" name="candidate_id" value="{{isset($candidate_id)?$candidate_id:old('candidate_id')}}">
                <input type="hidden" name="nom_id" value="{{isset($nom_id)?$nom_id:old('nom_id')}}">
                   
                  <?php  $url = URL::to("/");  ?>
                  <div class="btns-actn"> <input type="submit" value="Resend OTP"> </div> 
              </form> 
              </div><!--Nomination Parts-->
            </div><!--Row-->
 
         
      </div>
     
  
     </div><!-- End Of nw-crte-usr Div -->
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
  <script src="{{ asset('js/jquery.js')}}" type="text/JavaScript"></script> 
<script>
$(document).ready(function(){
 
  $("#otp").submit(function(){
   
    if($("#otpvalue").val()=="")
    {
      $("#err").text("Please enter OTP");
      $("#otpvalue").focus();
      return false;
    }
     
    });
  });
</script>
<script type="text/javascript">
  var time_in_minutes = 10;
var current_time = Date.parse(new Date());
var deadline = new Date(current_time + time_in_minutes*60*1000);


function time_remaining(endtime){
  var t = Date.parse(endtime) - Date.parse(new Date());
  var seconds = Math.floor( (t/1000) % 60 );
  var minutes = Math.floor( (t/1000/60) % 60 );
  var hours = Math.floor( (t/(1000*60*60)) % 24 );
  var days = Math.floor( t/(1000*60*60*24) );
  return {'total':t, 'days':days, 'hours':hours, 'minutes':minutes, 'seconds':seconds};
}
function run_clock(id,endtime){
  var clock = document.getElementById(id);
  function update_clock(){
    var t = time_remaining(endtime);
    clock.innerHTML = 'Left Time For OTP : '+t.minutes+' : '+t.seconds;
    if(t.total<=0){ clearInterval(timeinterval); }
  }
  update_clock(); // run function once at first to avoid delay
  var timeinterval = setInterval(update_clock,1000);
}
run_clock('clockdiv',deadline);
</script>    
@endsection
