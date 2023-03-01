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
                  <li class="step active">Finalize</li>
                  <li class="step">Print Receipt</li>
                </ul>
    <div class="row">
     <div class="nomination-fieldset">
              <form method="POST"  action="{{url('ro/verify-otp') }}" >
                {{ csrf_field()}}   
                <input type="hidden" name="candidate_id" value="{{$nomination->candidate_id}}">
                <input type="hidden" name="nom_id" value="{{$nomination->nom_id}}">
            <div id="printTable">
                  
             <div class="nomination-form-heading"> <strong>PART VI </strong><br/><br/>
                <strong>Receipt for Nomination Paper and Notice of Scrutiny </strong>
                <br/>
                  (To be handed over to the person presenting the Nomination Paper) 
                </div>
                  
                <div class="nomination-detail">
                Serial No. of nomination paper &nbsp;&nbsp;&nbsp; <strong>{{$nomination->nomination_papersrno}} </strong> &nbsp;&nbsp;&nbsp; The nomination paper of &nbsp;&nbsp;&nbsp; <strong>{{strtoupper($caddata->cand_name)}} </strong> &nbsp;&nbsp;&nbsp;   a candidate for election from the 
                @if(!empty($pc))
                  &nbsp;&nbsp;&nbsp; <strong> {{strtoupper($pc->PC_NAME)}} </strong> &nbsp;&nbsp;&nbsp;  Parliamentary constituency  @endif  
                @if(!empty($ac)) &nbsp;&nbsp;&nbsp; <strong> &nbsp;&nbsp;&nbsp; <strong>{{strtoupper($ac->AC_NAME) }} </strong> &nbsp;&nbsp;&nbsp;  </strong> &nbsp;&nbsp;&nbsp; Assembly constituency @endif 

                 

                was delivered to me at my office  at &nbsp;&nbsp;&nbsp; <strong>{{$nomination->rosubmit_time}} </strong> &nbsp;&nbsp;&nbsp; (hour) on &nbsp;&nbsp;&nbsp; <strong>{{$nomination->rosubmit_date}} </strong> &nbsp;&nbsp;&nbsp; (date) by the *candidate/proposer. All nomination papers will be taken up for scrutiny at &nbsp;&nbsp;&nbsp; <strong>{{$nomination->scrutiny_time}} </strong> &nbsp;&nbsp;&nbsp;  (hour) on &nbsp;&nbsp;&nbsp; <strong>{{$nomination->scrutiny_date}} </strong> &nbsp;&nbsp;&nbsp; (date) at &nbsp;&nbsp;&nbsp; <strong>{{strtoupper($nomination->place)}} </strong> &nbsp;&nbsp;&nbsp; Place.  
                </div><!--Nomination Details-->
                
                <!--<div class="nomination-detail {{ $errors->has('otpvalue') ? ' has-error' : '' }}"> Verify OTP Number 
                <input type='text'  name="otpvalue" class="nomination-field-2" value="{{old('otpvalue')}}"/>
                   @if ($errors->has('otpvalue'))  <span style="color:red;"><strong>{{ $errors->first('otpvalue') }}</strong></span>  @endif
               
                  
                 </div>--> <?php  $url = URL::to("/");  ?>
                </div> <!--  printTable-->
                  <div class="btns-actn">
                            <input type="submit" value="Finalize"> 
                            <input type="button" value="Cancel" onclick="location.href = '{{$url}}/ro/applicant';">
                             
                    </div> 
              </form> 
              </div><!--Nomination Parts-->
            </div><!--Row-->
 
         
      </div>
     
  
     </div><!-- End Of nw-crte-usr Div -->
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
    
@endsection
