@extends('admin.layouts.theme')
@section('content') 
@include('admin.includes.script')       
<link href="{{ asset('admintheme/main.css') }}" rel="stylesheet">
<div class="container-fluid">
  <!-- Start Parent Wrap div -->  
    <div class="parent-wrap">
    <!-- Start Child Area Div --> 
    <div class="child-area">
     <!-- Start Page Content Div -->  
    <div class="page-contant">
    <div class="head-title">
        <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-010.png')}}" /></i>Final Receipt</h3>
      </div>
      <!-- Start Of Order History Here -->  
      <div class="ordr-histry-record">
      <div class="ordr-wrap">  
                <ul class="steps" id="progressbar">
                  <li class="step">QR SCAN</li>
                  <li class="step">Verify Nomination</li>
                  <li class="step">Decision by RO</li>
                  <li class="step active">Final Receipt</li>
                  <li class="step">Finalize</li>
                  <li class="step">Print Receipt</li>
                </ul>
      <div class="row">              
         <form method="POST" id="election_form" action="{{url('ro/finalized-application') }}" >
                {{ csrf_field() }} 
                
                <input type="hidden" name="qrcode" value="{{$caddata->qrcode}}">
                <input type="hidden" name="candidate_id" value="{{isset($caddata->candidate_id)?$caddata->candidate_id:old('candidate_id')}}">
                <input type="hidden" name="nom_id" value="{{isset($nom_id)?$nom_id:old('nom_id')}}">
            <div class="nomination-fieldset">
            
              <div class="nomination-form-heading">
                  <strong>PART VI </strong><br/>
                  <strong>Receipt for Nomination Paper and Notice of Scrutiny </strong> <br>
                  (To be handed over to the person presenting the Nomination Paper) 
                </div>
                             
              <div class="nomination-parts box recognised">
              <div class="nomination-detail">
                Serial No. of nomination paper &nbsp;&nbsp;&nbsp; <strong>{{$caddata->nomination_papersrno}} </strong> &nbsp;&nbsp;&nbsp; The nomination paper of &nbsp;&nbsp;&nbsp; <strong>{{$caddata->cand_name}} </strong> &nbsp;&nbsp;&nbsp; a candidate for election from the &nbsp;&nbsp;&nbsp; <strong>{{$con_name}} </strong> &nbsp;&nbsp;&nbsp; {{$ac}} constituency. 

                was delivered to me at my office  at &nbsp;&nbsp;&nbsp; <strong>{{$caddata->rosubmit_time}} </strong> &nbsp;&nbsp;&nbsp; (hour) on &nbsp;&nbsp;&nbsp; <strong>{{$caddata->rosubmit_date }} </strong> &nbsp;&nbsp;&nbsp; (date) by the *candidate/proposer. All nomination papers will be taken up for scrutiny at  <span class="pagespanred">*</span><input type="text" name="scrutiny_time" class="nomination-field-2" id="scrutiny_time" value="{{old('scrutiny_time')}}" />
                  <span id="err"  style="color:red;"></span>
                @if ($errors->has('scrutiny_time'))
                                <span style="color:red;"><strong>{{ $errors->first('scrutiny_time') }}</strong></span>
                            @endif    <!--id="scrutiny_date"-->
                            (hour) on 
              <input type="text" name="scrutiny_date" readonly="readonly" class="nomination-field-2" value="{{$scrutiny_date}}"/> 

                            @if ($errors->has('scrutiny_date'))
                                <span style="color:red;"><strong>{{ $errors->first('scrutiny_date') }}</strong></span>
                            @endif  
                            (date) at <input type="text" name="place" class="nomination-field-3" readonly="readonly" value="{{$con_name}}" />   Place.  
                
                </div><!--Nomination Details-->
                 <div class="nomination-signature">
                  <span class="nomination-date left">Date: <input type='text' name="fdate" class="nomination-field-4" value="{{ date('d-m-Y') }}" readonly="readonly" /></span>
                  <span class="nomination-sign right">Returning Officer </span>
                </div><!--Nomination Signature-->
                <div class="score">*Score out the word not applicable.</div>
                 <div class="btns-actn">
                            <input type="submit" value="Save">
                      <input type="button" value="Back" onclick="window.history.back();">
                    </div>
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
   
    if($("#scrutiny_time").val()=="")
    {
      $("#err").text("Please enter time");
      $("#scrutiny_time").focus();
      return false;
    }
     
    });
  });
</script>