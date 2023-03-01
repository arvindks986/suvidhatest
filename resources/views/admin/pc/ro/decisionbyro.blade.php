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
        <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-010.png')}}" /></i>Decision by RO </h3>
      </div>
      <!-- Start Of Order History Here -->  
      <div class="ordr-histry-record">
      <div class="ordr-wrap">  
                <ul class="steps" id="progressbar">
                  <li class="step">QR SCAN</li>
                  <li class="step">Verify Nomination</li>
                  <li class="step active">Decision by RO</li>
                  <li class="step">Final Receipt</li>
                  <li class="step">Finalize</li>
                  <li class="step">Print Receipt</li>
                </ul>
      <div class="row">              
         <form class="form-horizontal" id="election_form" method="get"  action="{{url('ro/decisionvalidate') }}" >
                {{ csrf_field() }}   
            <input type="hidden" name="candidate_id" value="{{isset($caddata) ?$caddata->candidate_id:old('candidate_id') }}">
            <input type="hidden" name="qrcode" value="{{isset($caddata) ?$caddata->qrcode:old('qrcode') }}">
            <div class="nomination-fieldset">
            
              <div class="nomination-form-heading">
                  <strong>PART IV </strong><br/> (To be filled by the Returning Officer)  
              </div>
             
              <div class="nomination-parts box recognised">
              <div class="nomination-detail">
                Serial No. of nomination paper <span class="pagespanred">*</span>
          <input type="text" name="nomination_srno" id="nomination_srno" class="nomination-field-3" value="{{isset($caddata) ?$caddata->nomination_papersrno:old('nomination_srno') }}" /> 
          <span id="err"  style="color:red;"></span>
          @if ($errors->has('nomination_srno'))
                                <span style="color:red;"><strong>{{ $errors->first('nomination_srno') }}</strong></span>
                           @endif This nomination was delivered to me at my office at 
                 
                <input type='text' readonly="readonly" name="nomination_hour" class="nomination-field-1" value="{{Carbon\Carbon::now()->format('H:i:s')  }}"/> (hour) on <input type='text' readonly="readonly" name="nomination_date" class="nomination-field-2" value="{{Carbon\Carbon::parse(now())->format('d-m-Y')  }}"/>(date) by the *candidate / proposer. (name of proposer)  <span class="pagespanred">*</span>
                <select name="nomination_submittedby" id="nomination_submittedby" class="nomination-field-2">
                  <option value="" selected="selected">Select One</option>
                   <option value="{{$caddata->cand_name}}" @if($caddata->cand_name==$caddata->nomination_submittedby) selected="selected" @endif>{{$caddata->cand_name}}</option>
                   <option value="{{$caddata->proposer_name}}">{{$caddata->proposer_name}}</option>
                </select>
                <span id="err1"  style="color:red;"></span>
                <!--<input type="text" name="nomination_submittedby" class="nomination-field-3" value="{{isset($caddata) ?$caddata->nomination_submittedby:old('nomination_submittedby') }}"/> --> 
                    @if ($errors->has('nomination_submittedby'))
                      <span style="color:red;"><strong>{{ $errors->first('nomination_submittedby') }}</strong></span>
                    @endif
                </div><!--Nomination Details-->
                 <div class="btns-actn"> <input type="submit" value="Save & Next"> 
                 <input type="button" value="Back" onclick="window.history.back();"></div>
             </div> 
           
        </div>
      </form>
      </div>
      </div> 
      </div>
     </div><!-- End OF page-contant Div -->
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
  </div> 
 
@endsection
<script src="{{ asset('js/jquery.js')}}" type="text/JavaScript"></script> 
<script>
$(document).ready(function(){
 
  $("#election_form").submit(function(){
   
    if($("#nomination_srno").val()=="")
    {
      $("#err").text("Please enter nomination serial number");
      $("#nomination_srno").focus();
      return false;
    }
    if($("#nomination_submittedby").val()=="")
    {  
      $("#err").text("");
      $("#err1").text("Please select submited by");
      $("#nomination_submittedby").focus();
      return false;
    } 
    });
  });
</script>