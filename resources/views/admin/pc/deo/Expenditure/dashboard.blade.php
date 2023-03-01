@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Dashboard')
@section('description', '')
@section('content')
<?php

$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($user_data->st_code);

$pcdetails=getpcbypcno($user_data->st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$pcName=!empty($pcdetails) ? $pcdetails->PC_NAME : 'ALL';
?>
<main role="main" class="inner cover mb-3">
    <div class="card-header pt-3" id="expenditure_section">
        <div class="container-fluid">
            <div class="row text-center pt-2 pb-1">
                <div class="col-sm-12"><h4><b> CEO ELECTION EXPENDITURE MONITORING SYSTEM GENERAL PC ELECTION-2019</b></h4></div>
				<div class="col-sm-12 mt-3">
                <!--FILTER STARTS FROM HERE-->
              <form method="post" action="{{url('/pcceo/CeoExpdashboard')}}" id="EcidashboardFilter">           
                 <div class="row justify-content-center">
                    {{ csrf_field() }}
					       	<div class="col-sm-3">
                  <label for="" class="mr-3">Select PC</label>    
                  <select name="pc" id="pc" class="consttype form-control" >
                    <option value="">-- All PC --</option>
                    @php $all_pc = getpcbystate($user_data->st_code); @endphp
                    @foreach($all_pc as $getPc)
                    @if (old('pc') == $getPc->PC_NO)
                      <option value="{{ $getPc->PC_NO }}" selected>{{$getPc->PC_NAME}} - {{$getPc->PC_NAME_HI}}</option>
                      @else
                      <option value="{{ $getPc->PC_NO }}">{{$getPc->PC_NAME}} - {{$getPc->PC_NAME_HI}}</option>
                    @endif
								@endforeach 
							</select>
					    @if ($errors->has('pc'))
                  		  <span style="color:red;">{{ $errors->first('pc') }}</span>
               			@endif
                     
							<div class="acerrormsg errormsg errorred"></div>
                        </div>
					  	<div class="col-sm-1 mt-2">
							<p class="mt-4 text-left">
							<!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
						  <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
            	</p>
                        </div>
                    </div>
                </form> 
                 <!--FILTER ENDS HERE-->
				</div> 
            </div> 
        </div>
    </div>
   <section class="breadcrumb-section">
	<div class="container-fluid">
		<div class="row">
		 <div class="col">
		  <ul id="breadcrumb" class="pt-1">
			<li><a href="#">EEMS-Election Expenditure Monitoring System (Displayed in %)</a></li>
		  </ul>
		 </div>
     <div class="col"><p class="mb-0 text-right">
												<b>State Name:</b> 
												<span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
												<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
												<b>PC:</b> <span class="badge badge-info">{{$pcName}}</span>
                        <!-- <b></b> <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button> -->
									       
                    </p></div>
		 </div>
	</div>
  </section>
<section class="statistics color-grey pt-2 pb-5">
        <div class="container-fluid">
          <!-- EEMS box Row 1 -->
          <div class="row d-flex mb-2">
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <!-- Income-->
              <div class="card cardNew income reportBox text-center mt-5">
				      <div class="feature">
				      <img src="{{ asset('admintheme/img/icon/dataEntry-s.png') }}" alt="" />  
			
                </div>
                <div class="number mb-4 mt-4">
                    <a href="{{url('/')}}/pcceo/dataentryStart/{{base64_encode($cons_no)}}" target="" style="font-size: 17px;" class="text-danger">{{ $Percent_startdataentry }} % Data entry started ({{$startdatacount}})</a><br>
                    <a href="{{url('/')}}/pcceo/finalizeData/{{base64_encode($cons_no)}}" target="" style="font-size: 17px;" class="text-info">{{ $Percent_finaldatacount }} % Report Finalised ({{$finaldatacount}})</a>
                </div>
              </div>
            </div>
			 <!--<div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <!-- Income--
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/pcceo/finalizeData/{{$cons_no}}" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/exp-icon-s.png') }}" alt="" />            
                </div>
                <div class="number text-info mb-1 mt-4">{{ $Percent_finaldatacount }} %</div>
                <p><strong class="text-primary">Report Finalised ({{$finaldatacount}})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
               </a>
              </div>
            </div>-->
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <!-- Income-->
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/pcceo/logedaccount/{{base64_encode($cons_no)}}" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/accLodged-s.png') }}" alt="" />            
                </div>
                <div class="number text-warning mb-1 mt-4">{{ $Percent_logedaccount }} %</div>
				<p><strong class="text-primary">Account Lodged ({{$logedaccount}})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> 
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <!-- Income-->
              <div class="card cardNew income reportBox text-center mt-5">
			   <a href="{{url('/')}}/pcceo/notintime/{{base64_encode($cons_no)}}" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/noTime-s.png') }}" alt="" />            
                </div>
                <div class="number text-success mb-1 mt-4">{{ $Percent_notintimeaccount }} %</div>
				<p><strong class="text-primary">Not in Time ({{$notintimeaccount}})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div>       
    <!-- End of EEMS box Row 1 -->
     <!-- EEMS box Row 2 -->
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <!-- Income-->
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/pcceo/formatedefects/{{base64_encode($cons_no)}}" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/defectFormat-s.png') }}" alt="" />            
                </div>
                <div class="number text-danger mb-1 mt-4">{{ $Percent_formateDefectscount }}%</div>
				<p><strong class="text-primary">Defect in account ({{$formateDefectscount}})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div>

            <!--Ro not agree---
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
            
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="#" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/notAgree-s.png') }}" alt="" />            
                </div>
                <div class="number text-info mb-1 mt-4">23.5%</div>
			        	<p><strong class="text-primary">RO not Agree</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> --->
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <!-- Income-->
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/pcceo/understatedexpense/{{base64_encode($cons_no)}}" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/expUnder-s.png') }}" alt="" />            
                </div>
                <div class="number text-warning mb-1 mt-4">{{ $Percent_expenseunderstated}} %</div>
				<p><strong class="text-primary">Expenses understated ({{ $expenseunderstated}})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> 

            <!---data entry defects---
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="#" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/dataDefect-s.png') }}" alt="" />            
                </div>
                <div class="number text-success mb-1 mt-4">43.7%</div>
				<p><strong class="text-primary">Data entry defects</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> --->
    <!-- End of EEMS box Row 2 -->
     <!-- EEMS box Row 3 -->
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <!-- Income-->
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/pcceo/partyfund/{{base64_encode($cons_no)}}" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/fundParty-s.png') }}" alt="" />            
                </div>
                <div class="number text-danger mb-1 mt-4">{{ $Percent_partyFund}} %</div>
				<p><strong class="text-primary">Taken funds from party</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <!-- Income-->
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/pcceo/othersfund/{{base64_encode($cons_no)}}" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/fundOther-s.png') }}" alt="" />            
                </div>
                <div class="number text-info mb-1 mt-4">{{ $Percent_OthersourcesFund}} %</div>
                <p><strong class="text-primary">Taken funds from other sources</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
              </div>
            </div> 
            <!----Exceed ceiling amount--
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
            
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="#" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/ceilingAmount-s.png') }}" alt="" />            
                </div>
                <div class="number text-warning mb-1 mt-4">11.9%</div>
				<p><strong class="text-primary">Exceed the Ceiling amount</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> --->
    <!-- End of EEMS box Row 3 -->
     </div> 
    </div>
 </section>
</main>

<!-- Validation  JavaScript -->
<script src="{{ asset('admintheme/js/front.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/charts-home.js') }}"></script>
<script type="text/javascript">
  // Set the date we're counting down to
  var po = "@if(!empty($sched->DATE_POLL)){{date("M d, Y 12:00:0",strtotime($sched->DATE_POLL))}}@endif" ;

  var countDownDate = new Date(po).getTime();
  
  // Update the count down every 1 second
  var x = setInterval(function() {

    // Get todays date and time
    var now = new Date().getTime();
  
    // Find the distance between now and the count down date
    var distance = countDownDate - now;
    // console.log(distance);
    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
  
    // Display the result in the element with id="demo"
    document.getElementById("demo").innerHTML = days + " DAYS";
  
    // If the count down is finished, write some text 
    if (distance < 0) {
      clearInterval(x);
      document.getElementById("demo").innerHTML = "EXPIRED";
    }
  }, 1000);
  </script>
@endsection