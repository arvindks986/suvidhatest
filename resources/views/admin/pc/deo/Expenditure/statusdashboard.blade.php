@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Current Dashboard')
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
              <form method="post" action="{{url('/pcdeo/statusExpdashboard')}}" id="ceodashboardFilter">           
                 <div class="row justify-content-center">
                    {{ csrf_field() }}
					       	<div class="col-sm-3">
                  <label for="" class="mr-3">Select PC</label>    
                  <select name="pc" id="pc" class="consttype form-control" >
                    <option value="">-- All PC --</option>
                    @php $all_pc = getpcbystate($user_data->st_code); @endphp
                    @foreach($all_pc as $getPc)
                    @if ($cons_no == $getPc->PC_NO)
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
              <div class="card cardNew income reportBox text-center mt-5">
				<a href="{{url('/')}}/pcdeo/pendingdataentry/{{base64_encode($cons_no)}}" target="">
				<div class="feature"><img src="{{ asset('admintheme/img/icon/dataEntry-s.png') }}" alt="" /> </div>
                <div class="number text-danger mb-1 mt-4">{{ $Percent_pendingdatacount }} %</div>
                <p><strong class="text-primary">Not filed ({{$pendingdatacount}})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
			     	</a>
              </div>
            </div>
		
           
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			      	<!-- <a href="{{url('/')}}/pcdeo/filedData/{{base64_encode($cons_no)}}" target=""> -->
                <a href="#" target="">
			    	<div class="feature">
			     	<img src="{{ asset('admintheme/img/icon/exp-icon-s.png') }}" alt="" />            
                </div>
                <div class="number text-info mb-1 mt-4">{{ $Percent_startdatacount }} %</div>
                <p><strong class="text-primary">Filed Data ({{$startdatacount}})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
               </a>
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			        <!--  <a href="{{url('/')}}/pcdeo/defaulter/{{base64_encode($cons_no)}}" target=""> -->
                 <a href="#" target="">
			      	<div class="feature"><img src="{{ asset('admintheme/img/icon/noTime-s.png') }}" alt="" /> </div>
                <div class="number text-success mb-1 mt-4">{{ $Percent_defaultercount }} %</div>
			        	<p><strong class="text-primary">Default Account ({{$defaultercount}})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div>   
           <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			      	 <a href="{{url('/')}}/pcdeo/partiallypending/{{base64_encode($cons_no)}}" target=""> 
                 <!-- <a href="#" target=""> -->
			        	<div class="feature"><img src="{{ asset('admintheme/img/icon/accLodged-s.png') }}" alt="" /></div>
                <div class="number text-warning mb-1 mt-4">{{ $Percent_partiallypendingcount }} %</div>
			         	<p><strong class="text-primary">Pending At DEO ({{$partiallypendingcount}})</strong></p>
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
				<!-- <a href="{{url('/')}}/pcdeo/finalbyceo/{{base64_encode($cons_no)}}" target=""> -->
           <a href="#" target="">
				<div class="feature">
				<img src="{{ asset('admintheme/img/icon/defectFormat-s.png') }}" alt="" />            
                </div>
                <div class="number text-danger mb-1 mt-4">{{ $Percent_finalbyceocount }}%</div>
				<p><strong class="text-primary">Pending At CEO ({{ $finalbyceocount }})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div>
              <!-- RO Not Agreee start--
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
            </div> 
 <!-- RO Not Agreee start--
            <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			        	<a href="{{url('/')}}/pcdeo/finalbyeci/{{base64_encode($cons_no)}}" target="">
			        	<div class="feature"><img src="{{ asset('admintheme/img/icon/expUnder-s.png') }}" alt="" /></div>
                <div class="number text-warning mb-1 mt-4">{{ $Percent_finalbyecicount}} %</div>
				       <p><strong class="text-primary">Pending At ECI ({{ $finalbyecicount }})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> 
			 <!--- Notice At CEO-->
          <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			        	<a href="{{url('/')}}/pcdeo/noticeatceo/{{base64_encode($cons_no)}}" target="">
			        	<div class="feature"><img src="{{ asset('admintheme/img/icon/expUnder-s.png') }}" alt="" /></div>
                <div class="number text-warning mb-1 mt-4">{{ $Percent_noticeatceocount}} %</div>
				       <p><strong class="text-primary">Notice At CEO({{ $noticeatceocount }})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div>
			<!--- Start Notice At CEO-->
            <!---Data entry defect--
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
            </div> -->
            <!----Data entry defects end--->
              <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			        	<a href="{{url('/')}}/pcdeo/return/{{base64_encode($cons_no)}}" target="">
			        	<div class="feature"><img src="{{ asset('admintheme/img/icon/expUnder-s.png') }}" alt="" /></div>
                <div class="number text-warning mb-1 mt-4">{{ $Percent_returncount}} %</div>
                 
				       <p><strong class="text-primary">Return({{ $returncount }})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div> 
            <!--- Data return end -->
              
    <!-- End of EEMS box Row 2 -->
	  <!-- Start of EEMS box Row 3 -->
	 <!--- Data non return -->
          <div class="col-lg-3 col-md-4 col-sm-3 mt-5">
              <div class="card cardNew income reportBox text-center mt-5">
			        	<a href="{{url('/')}}/pcdeo/non-return/{{base64_encode($cons_no)}}" target="">
			        	<div class="feature"><img src="{{ asset('admintheme/img/icon/expUnder-s.png') }}" alt="" /></div>
                <div class="number text-warning mb-1 mt-4">{{ $Percent_nonreturncount}} %</div>
                
				       <p><strong class="text-primary">Non-Return({{ $nonreturncount }})</strong></p>
                <p class="mb-2 mt-4">
                <button type="button" id="Back" class="btn btn-primary">View Detail</button>
                </p>
                </a>
              </div>
            </div>
			  <!--- Data non return end -->
              
       <!-- End of EEMS box Row 3 -->
     </div> 
    </div>
 </section>
</main>
@endsection