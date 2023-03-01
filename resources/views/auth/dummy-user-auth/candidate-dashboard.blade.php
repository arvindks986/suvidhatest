@extends('layouts.theme')
@section('content')

<main>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

   
	
	 <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-profile.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/fonts.css') }} " type="text/css">
	
   <title>Dashboard</title>
  </head>
  <body> 
   
   <main class="pt-3 pb-5 pl-5 pr-5">
	 <div class="container-fluid">
	 <div class="custom-tab-area">	 
	  <!-- Nav tabs -->
	  <ul class="nav nav-tabs">
		<li class="nav-item nav-md-item">
		  <a class="nav-link active" data-toggle="tab" href="#nomin">{{ __('dashboard.tag1') }}</a>
		</li>
		
		<li class="nav-item nav-md-item">
		  <a class="nav-link" href="{{ route('affidavit.e.file') }}" class="Affidavit">{{ __('dashboard.tag4') }}</a>
		</li>
		
		<li class="nav-item nav-md-item">
		  <a class="nav-link" data-toggle="tab" href="#permis">{{ __('dashboard.tag2') }}</a>
		</li>
		
	  </ul>	 
	  
	  
	  
	  
	  
	  <?php  $acs = 0;	
				 $std='';
				 $acd=0;
				 $std2='';
				 $acd2=0;
				
				$acs=app(App\Http\Controllers\Nomination\NominationController::class)->getAcs();
				if($acs!='0' && $acs!=''){ 
					$exp = explode('***', $acs); 
					$std=encrypt_string($exp[0]);
					$acd=encrypt_string($exp[1]);
					$std2=$exp[0];
					$acd2=$exp[1];
				} else {  
					$std='';
					$acd='';
					$std2='';
					$acd2='';
				}
				
				
				
				$md='';
				 $tststs = app(App\Http\Controllers\Nomination\NominationController::class)->getProfileD(); 
				if($tststs =='One' ){
				  $md = '/nomination/apply-nomination-step-2';
				} else {
				  $md ='/nomination/apply-nomination-step-1';
				}
				
				//echo $acd2.'--'.$std2; die;
				
		  ?>
	  
	  
	 
	   <div class="card card-shadow mt-4">
		<div class="card-body p-0">
	   <!-- Tab panes -->
	  <div class="tab-content">
		  <div id="nomin" class="tab-pane active">
		    <!-- From Here-->
			<div class="header-title">
			<div class="row">
			  <div class="col-6">
				<h5>{{ __('dashboard.tag1') }}	</h5> 
			  </div>  
			  <div class="col-6"><div class="text-right"></div></div>  
			</div>  
	      </div>	
			 <!--End From Here--> 
			 
		 <div style="display:block;"> 
		
		
		  
		  <div class="tab-body">
		  	 <div class="home">
			    <div class="row">
				  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn d-inline-flex">
						   <span class="apply-icon"></span><a href="<?php echo url('/'); ?>{{$md}}">{{ __('dashboard.Apply_New') }}<br/>{{ __('dashboard.tag1') }}</a>
						   <div class="help-txt">{{ __('dashboard.new_nomination_message') }}</div>
					   </div>
					 </div>	
				  </div>
                  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn my-apped-btn d-inline-flex">
						   <span class="my-apped-icon"></span><a href="{{'nomination/nominations?pcs='.$acd.'&std='.$std}}">{{ __('dashboard.my') }}<br/> {{ __('dashboard.tag1') }}</a>
						   <div class="help-txt">{{ __('dashboard.saved_and_submitted_nomination') }}</div>
					   </div>
					 </div>	
				  </div>				  
				 </div>
			  </div><!-- End Of home Div -->
			</div>
			 
		  </div><!-- End Of nomin Div -->  
		   	
		 </div> 
		<div id="permis" class="tab-pane">
		  <div class="header-title">
			<div class="row">
			  <div class="col-6">
				<h5>{{ __('dashboard.tag2') }}	</h5> 
			  </div>  
			</div>  
	      </div>	  
		 <!-- From Here-->		  
		  <div class="tab-body">
		   <div class="tab-body">
				<div class="home">
			    <div class="row">
				  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn d-inline-flex">
						   <span class="apply-icon"></span><a href="{{url('/create')}}">Apply New<br/>Permission</a>
						   <div class="help-txt">Here you can apply for new Permission</div>
					   </div>
					 </div>	
				  </div>
                  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn my-apped-btn d-inline-flex">
						   <span class="my-apped-icon"></span><a href="{{url('/permission')}}">My<br/> Permission</a>
						   <div class="help-txt">All your saved and submitted permission are listed here </div>
					   </div>
					 </div>	
				  </div>				  
				 </div>
			  </div><!-- End Of home Div -->
			</div>
			</div>
	 <!--End From Here-->
		  </div><!-- End Of permis Div -->
		<div id="adver" class="tab-pane fade">
		  <div class="header-title">
			<div class="row">
			  <div class="col-6">
				<h5>{{ __('dashboard.tag3') }}</h5> 
			  </div>  
			  <div class="col-6">
			  
			  </div>  
			</div>  
	      </div>
			<div>&nbsp;</div>
		@if(!empty($applicant_id))
	      <div class="appt-status tab-panel-bg p-4" id="one_div">
			<h4 class="text-center">Reference No  - @if(!empty($reference_no)) {{$reference_no}} @endif</h4>	
			<div class="approvl-wrap progess01">
			  @if($applied_date)
			  <div class="aprv-item">
				<div class="day-name"><strong>{{GetReadableDateFormat($applied_date) }}</strong></div> 
				 <div class="mark-tick class1"><span>&#10003;</span></div> 
				<div class="aprv-title">Application Applied</div>  
			  </div>
			  @endif

			  @if($application_status)
			  <div class="aprv-item">
				<div class="day-name"><strong>{{GetReadableDateFormat($certificate_generation) }}</strong></div> 
				 <div class="mark-tick class1"><span>&#10003;</span></div> 
				<div class="aprv-title">{{ucfirst($application_status)}}</div>  
			  </div>
			  @endif
			  
			  @if($ad_status=='6')
			  <div class="aprv-item">
				<div class="day-name"><strong>{{GetReadableDateFormat($certificate_generation) }}</strong></div> 
				 <div class="mark-tick class1"><span>&#10003;</span></div> 
				<div class="aprv-title">Certificate Generated</div>  
			  </div>
			  @endif
			  
			  </div>
			  
			  <!-- End Of approvl-wrap Div -->
			 <div class="text-right" style="font-size: 12px; cursor: pointer;"><a class="link-btn linktxt" data-val="1"><span class="headtext">View All</span> <i class="fa fa-caret-down" aria-hidden="true"></i></a></div>
			</div>
			<div class="clearfix"></div>
			<div style="display:none;" id="all_div">
			@if(!empty($allStatus))
			@if(count($allStatus)>0)
				  @php $j=2;@endphp
				  @foreach($allStatus as $k=>$v)
				  <div class="appt-status tab-panel-bg p-4">
				  <h4 class="text-center">Reference No  - @if(!empty($v['reference_no'])) {{$v['reference_no']}} @endif</h4>	
				  <div class="approvl-wrap progess{{$j}}">
				  @if($applied_date)
				  <div class="aprv-item">
					<div class="day-name"><strong>{{GetReadableDateFormat($v['applied_date']) }}</strong></div> 
					 <div class="mark-tick class{{$j}}"><span>&#10003;</span></div> 
					<div class="aprv-title">Application Applied</div>  
				  </div>
				  @endif

				  @if($application_status)
				  <div class="aprv-item">
					<div class="day-name"><strong>{{GetReadableDateFormat($v['certificate_generation']) }}</strong></div> 
					 <div class="mark-tick class{{$j}}"><span>&#10003;</span></div> 
					<div class="aprv-title">{{ucfirst($v['application_status'])}}</div>  
				  </div>
				  @endif
				  @if($v['ad_status']=='6')
				  <div class="aprv-item">
					<div class="day-name"><strong>{{GetReadableDateFormat($v['certificate_generation']) }}</strong></div> 
					 <div class="mark-tick class{{$j}}"><span>&#10003;</span></div> 
					<div class="aprv-title">Certificate Generated</div>  
				  </div>
				  @endif
				  </div>
				  </div>
				  @php $j++;@endphp
				  @endforeach
			  @endif
			  @endif
			  <div class="text-right" style="font-size: 12px; cursor: pointer;"><a class="link-btn linktxt" data-val="2"><span class="headtext">Show Latest</span> <i class="fa fa-caret-down" aria-hidden="true"></i></a></div>
			</div>
			
			<!-- View all list-->'
			
			@endif
		    <div class="tab-body">
				<div class="home">
			    <div class="row">
				  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn d-inline-flex">
						   <span class="apply-icon"></span><a href="{{url('/media/application')}}">Apply New<br/>Advertisement</a>
						   <div class="help-txt">Here you can apply for new Advertisement application</div>
					   </div>
					 </div>	
				  </div>
                  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn my-apped-btn d-inline-flex">
						   <span class="my-apped-icon"></span><a href="{{url('/media/my-applications')}}">My<br/> Advertisement</a>
						   <div class="help-txt">All your saved and submitted applications are listed here </div>
					   </div>
					 </div>	
				  </div>				  
				 </div>
			  </div><!-- End Of home Div -->
			</div>
		</div><!-- End Of adver Div -->
	  </div>
		</div>  
	  </div>
	  </div>	 
	 </div>
     
	<script src="{{ asset('appoinment/js/jQuery.min.v3.4.1.js') }}" type="text/javascript"></script>
	<script src="{{ asset('appoinment/js/bootstrap.min.js') }}" type="text/javascript"></script>
	
	
  </body>
</html>
</main>
@endsection

@section('script')
<script type="text/javascript">

function showAll(){
	$(".appt-status").show();
}
var appcount = <?php if(!empty($allStatus)){ ?> {!! count($allStatus) !!} <?php } ?>
appcount = parseInt(appcount) + parseInt(1);
$(document).ready(function(){	
$(".linktxt").click(function(){
	if($(this).attr("data-val")=='1'){
		$("#all_div").slideToggle('slow');
		$("#one_div").hide();
	}else{
		$("#all_div").hide();
		$("#one_div").slideToggle('slow');
	}
});
$(function(){	 
		 
		//This Function For aprv-item
		var noItem = $('.progess01 .aprv-item').length;
		if(noItem == 1){
          $('.class1').addClass('wdthOne');
		}else if(noItem == 2){
          $('.class1').addClass('wdthTwo');
			  $('.class1').last().removeClass('wdthTwo').addClass('last-progrss');
		}else if(noItem == 3){
		   $('.class1').addClass('wdthThree');
			  $('.class1').last().removeClass('wdthThree').addClass('last-progrss');
		}
		
		if(appcount > 0){
			for(var j=2;j<=appcount; j++){
				var noItem = $('.progess'+j+' .aprv-item').length;
				if(noItem == 1){
				  $('.class'+j).addClass('wdthOne');
				}else if(noItem == 2){
				  $('.class'+j).addClass('wdthTwo');
					  $('.class'+j).last().removeClass('wdthTwo').addClass('last-progrss');
				}else if(noItem == 3){
				   $('.class'+j).addClass('wdthThree');
					  $('.class'+j).last().removeClass('wdthThree').addClass('last-progrss');
				}
			}
		}
		
		
	  });
});
</script>
@endsection

