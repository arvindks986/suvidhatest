@extends('admin.layouts.theme')
@section('content') 

<link href="{{ asset('admintheme/main.css') }}" rel="stylesheet">
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
   <div class="nw-crte-usr">
       
        <div class="head-title">
              <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-010.png')}}" /></i>QR  Code Scan </h3>
        </div>
              <ul class="steps" id="progressbar">
                  <li class="step active">QR SCAN</li>
                  <li class="step">Verify Nomination </li>
                  <li class="step">Decision by RO</li>
                  <li class="step">Final Receipt</li>
                  <li class="step">Finalize</li>
                  <li class="step">Print Receipt</li>
                </ul> <br>
            @if (session('error_mes'))
                  <div class="alert alert-danger"> {{session('error_mes') }}</div>
       @endif   <!---->
            <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ro/candidateinformation') }}" >
                {{ csrf_field() }}  
                 
               <label>QR Code <span class="pagespanred">*</span> </lable><br>
               <textarea name="qrcode" id="qrcode" rows="5" cols="25" readonly="readonly">{{isset($qr)?$qr:old('qrcode')}}</textarea>
               <span id="err"  style="color:red;"></span>
               @if ($errors->has('qrcode'))
                  <span style="color:red;"><strong>{{ $errors->first('qrcode') }}</strong></span>
               @endif 
                 

              <div class="btns-actn">
                 <input type="submit" value="Next">
                 <input type="button" value="Back" onclick="window.history.back();">
              </div>
            </form>

              
      </div><!-- End Of nw-crte-usr Div --> 
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
  </div> 

@endsection
<script src="{{ asset('js/jquery.js')}}" type="text/JavaScript"></script> 
<script>
$(document).ready(function(){
 
  $("#election_form").submit(function(){
   
    if($("#qrcode").val()=="")
    {
      $("#err").text("Please enter your name");
      $("#qrcode").focus();
      return false;
    }
     
    });
  });
</script>