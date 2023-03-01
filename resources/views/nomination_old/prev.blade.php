  @extends('layouts.theme')
  @section('title', 'Nomination')
  @section('content')
  <style type="text/css">
    .error{
      font-size: 12px; 
      color: red;
    }
  </style>
  
<style>

#faq-From-div ul li {
color: blue;
font-size: 15px;
font-weight: 700;
list-style: decimal;
padding-top: 10px;
width: 50%;
}

#faq-From-div ul li a {
color: blue;
font-size: 15px;
font-weight: 700;
list-style: decimal;
text-decoration: none;
}
#Answer {
width: 815px;
height: auto;
margin: 0 auto 0 0;
border: 1px #0F0F0A dotted;
color: #272727;
font-size: 15px;
font-weight: 400;
text-align: justify;
padding: 5px;
}

.pay-slct-pge{margin: 10rem 0 0 0;}
.pay-slct-pge .panel-heading{font-size: 21px; color: #1c6e91;}
.pay-slct-pge .panel-body{padding: 35px;}
table.Payment td {padding: 0 0 0 1rem;}
.pay-slct-pge .panel-default>.panel-heading {background-color: #ededed;}
.razorpay-payment-button {
    padding: 0.5rem 2rem;
    background-color: #06518b;
    border: 1px solid #06518b;
    color: #fff;
}
#resizeableDive .panel{box-shadow: none; -webkit-box-shadow: none; margin: 8.5rem 0 0 0;}
.Refresh h5{color: #12485e; text-transform: capitalize; text-align: center;}
.razorpay-payment-button:hover,
.razorpay-payment-button:focus,a.btn.bck-btn:hover,a.btn.bck-btn:focus {background-color: #046fc1; color: #fff; border-color: #046fc1;}
a.btn.bck-btn {border: 1px solid #d7d7d7; background-color: #f1f1f1; border-radius: 0; padding: 1rem 2.5rem; margin: 0 0 0 1rem; }
.no-pay {text-align: center; margin: 5rem 0 1.5rem 0; color: #1c6e91; line-height: 28px; font-weight: 400; font-size: 15px;}
.mchild {
 width: 50%;
}
</style>
  
   <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="{{ asset('appoinment/css/bootstrap.min.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-profile.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/custom-dark.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/css/font-awesome.min.css') }} " type="text/css">
	<link rel="stylesheet" href="{{ asset('appoinment/fonts.css') }} " type="text/css">
	
		
    <link rel="stylesheet" href="{{ asset('admintheme/css/jquery-ui.css') }}" id="theme-stylesheet">
	
	
   <title>Schedule Appointment</title>
   <script>
    var abc=[];
   </script>
  </head>
  <body>
   <main class="pt-3 pb-5 pl-5 pr-5">
	  <section>
	@if(count($errors->all())>0)
		  <div class="container" style="padding: 16px;">
          <div class="alert alert-danger">
            <ul>
              @foreach($errors->all() as $iterate_error)
              <li><p class="text-left">{!! $iterate_error !!}</p></li>
              @endforeach
            </ul>
          </div>
		  </div>    	
    @endif
	<!--	  
	@if (session('flash-message'))
		<div class="container">
			<div class="row" style="padding: 16px;">
           @if (session('flash-message'))
           <div class="alert alert-success"> {{session('flash-message') }}</div>
           @endif
		</div>    
    @endif -->
    </section>	
	<?php  
	 //Session::flash('is_payment',"yes");
	//Session::flash('is_payment',"yes"); //echo $is_appoinment_scheduled_for_one; die; ?>
	@if($is_appoinment_scheduled_for_one==1)
		@php 
		$disp='none';
		$disp2='block';
		@endphp
	@else 
		@php 
		$disp='block';
		$disp2='none';
		@endphp
	@endif
	
	
	
	 <div class="container-fluid" style="display:{{$disp}}" id="call">
	   <div class="card">
		 <div class="card-header">
		   <div class="row">
			<div class="col-md-6 col-12"><h4>{{ __('messages.Preference') }}</h4> </div> 
			<div class="col-md-6 col-12"><div id="dmy" class="text-right"></div></div> 
		   </div>
		    <span style="margin-left: 41em;margin-top: 16px; font-size: 13px; color: black;cursor:pointer;font-weight: bold;">
			@if($nomiantaion_end_date!=0)
			{{ __('messages.lastdate') }} 
			{{date("D d, M Y", strtotime($nomiantaion_end_date))}}  
			@endif
			</span>	

		   @if($is_appoinment_scheduled_for_one==1)
		   <span style="float: right; margin-top: 16px; font-size: 13px; color: gray;cursor:pointer;" onclick="return showhide2();">{{ __('messages.Preference_Details') }}dasdsads</span>	 
		   @endif	
		 </div> 
		 <div class="card-body">
		   <div class="row">
			<div class="col-md-4 col-12 pr-2">
			  <div class="nomin-list" style="height: 356px;">
			  <div class="owl-carousel owl-theme">
			  
			  <?php 
			    $d=1;
			    if(isset($_REQUEST['id'])){ 
				$ids = explode(",", $_REQUEST['id']);
				foreach($ids as $nomid){
				$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationBookedDetails($nomid);	
				
				if($datadd!="NA"){
				$str = explode("***", $datadd);
				$NOMNO=$str[0];
				$candidate_name=$str[1];
				$ACNO=$str[2];
				$ACname=$str[3];
				$appoinment_status=$str[4];
				$updated_at=$str[5];
				$view_href_cust=$str[6];
				$download_href_cust=$str[7];
				$election_name_one=$str[12];
				$state=$str[13];
				$party=$str[14];
				$std=$str[15];
				$cat=$str[16];
				
				
				$stst=app(App\Http\Controllers\Nomination\NominationController::class)->getAPSFromDetailsTB($NOMNO);
				
				$udurl='';
				$udurl='detail/'.encrypt_String($nomid);
				
				$dwdulr='';
				$dwdulr='download/'.encrypt_String($nomid);
				
				
				
				?>
				<div class="item">
				<div class="appnt-detail list-detail">
					<h4 class="text-center d-flex justify-content-between"><b>{{$d}}</b>{{$NOMNO}}</h4>
					<ul style="line-height: 1;">
					<li><strong> {{ __('nomination.Name') }}</strong> <span>{{$candidate_name}}</span></li>
					<li><strong>{{ __('nomination.ac') }} &amp; {{ __('nomination.Name') }}</strong> <span>{{$ACNO}} - {{$ACname}}</span></li>
					<!--<li><strong>{{ __('nomination.Status') }}</strong> <span>{{ __('csa.Pre_Scurtiny_Done') }}</span></li>-->
					<li><strong>{{ __('nomination.State') }}</strong> <span>{{$state}}</span></li>
					<li><strong>{{ __('nomination.Election') }}</strong> <span>{{$election_name_one}}</span></li>
					<li><strong>{{ __('nomination.Party') }}</strong> <span>{{$party}}</span></li>
					<li><strong>{{ __('step1.Category') }}</strong> <span> {{strtoupper($cat)}} </span></li>
				  </ul> 
					<div class="row m-0 p-3">
					  <div class="col-md-4 col-12 p-0"><strong>{{ __('nomination.Nomination') }}</strong></div>  
					  <div class="col-md-8 col-12 p-0 text-right">
						 <div class="apt-btn">
						  <a href="{{$udurl}}?pcs=<?php echo encrypt_String($ACNO); ?>&std=<?php echo encrypt_String($std); ?>" class="btn sm-btn dark-pink-btn">{{ __('nomination.View') }}</a>  
						  <a href="{{$dwdulr}}" class="btn sm-btn dark-purple-btn">{{ __('nomination.Download') }}</a>  
						</div> 
					  </div>  
					</div>
					</div><!-- End Of appnt-detail Div --> 
				</div><!-- End Of item Div -->
				<?php $d++; }}} ?>
			  </div>			   
		    </div><!-- End Of nomin-list Div --> 
			</div> 	
			<div class="col-md-8 col-12 pl-0">
			   <div class="appnt-calndr">
				   <div class="caldr-wrap frst-week m-3">
                         <div id="day-schedule"></div>
					   <div class="help-arrow">	 
						 <img src="<?php echo url('/'); ?>/appoinment/img/vendor/help-left-icon.png" height="60" width="70">
						 <span>{{ __('messages.click') }}</span>
					   </div>	 
						 <div class="d-flex justify-content-end trffic">
							 <div><span class="lowTrf"></span> Low Traffic</div>
							 <div><span class="highTrf"></span> High Traffic</div>
							 <div><span class="selectTrf"></span> Selected</div>
							 <div><span class="disableTrf"></span> Disabled</div>
						 </div>
					  </div><!-- End of frst week -->				 
			  </div><!-- End Of appnt-calndr Div -->   
			</div> 
		   </div>
		 </div>  
		 
		  <?php 
		$expd = explode(",", $_REQUEST['id']); 
		$stno='';
		$acnno='';
		$acst = app(App\Http\Controllers\Nomination\NominationController::class)->getAcStByNo($expd[0]); 
			 if(!empty($acst)){
				$entt = explode("***", $acst);
				$stno=encrypt_String($entt[0]);
				$acnno=encrypt_String($entt[1]);
			 } 
		?>	
		 
		 <div class="card-footer">
		    <div class="text-left col-md-3 col-3 pl-3" style="height:1px;">
			  <a href="{{'nominations?pcs='.$acnno.'&std='.$stno}}" class="btn btn-lg font-big dark-pink-btn" style="color: white;">{{ __('step1.Back') }}</a>  
			</div> 		
			@if($is_appoinment_scheduled_for_one==1)
		   <div style="font-size: 13px; color: gray; cursor: pointer; position: absolute; margin-top: 35px; margin-left: 50em;" onclick="return showhide2();">{{ __('messages.Preference_Details') }}</div>	 
		   @endif	
		   <div class="apt-btn text-right">
			  @if($appoinment_status!='Cancel')
			  <!-- <a href="schedule-appointment" class="btn btn-lg font-big dark-pink-btn">CANCEL</a>  -->
			  @endif
			  <a href="#" class="btn btn-lg font-big dark-purple-btn" onclick="return submitScheduled();" id="scheduleButton">{{ __('messages.SCHEDULE_APPOINTMENT') }}</a>  
			</div> 
		 </div>  
	   </div>
	 </div>
	 
	 
	  <div class="container-fluid" style="display:{{$disp2}}" id="slip">
	   <div class="card">
		 <div class="card-header">
		   <div class="row">
			<div class="col-md-12 col-12"><h4>{{ __('messages.predetails') }}</h4> </div> 
			<div class="col-md-6 col-12"></div> 
		   </div>	 
		 </div> 
		 <div class="card-body">
		   <div class="row">
			<div class="col-md-4 col-12 pr-2">
			  <div class="nomin-list" style="height: 356px;">
			  <div class="owl-carousel owl-theme">
			  
			  <?php 
			    $d=1; $std= 0; $ACNO=0;
			    if(isset($_REQUEST['id'])){ 
				$ids = explode(",", $_REQUEST['id']);
				foreach($ids as $nomid){
				$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationBookedDetails($nomid);	
				
				if($datadd!="NA"){
				$str = explode("***", $datadd);
				$NOMNO=$str[0];
				$candidate_name=$str[1];
				$ACNO=$str[2];
				$ACname=$str[3];
				$appoinment_status=$str[4];
				$updated_at=$str[5];
				$view_href_cust=$str[6];
				$download_href_cust=$str[7];
				$election_name_one=$str[12];
				$state=$str[13];
				$party=$str[14];
				$std=$str[15];
				$cat=$str[16];
				$stst=app(App\Http\Controllers\Nomination\NominationController::class)->getAPSFromDetailsTB($NOMNO); 
				
				//echo "<pre>";print_r($datadd);die;
				$udurl1='';
				$udurl1='detail/'.encrypt_String($nomid);
				
				$dwdulr2='';
				$dwdulr2='download/'.encrypt_String($nomid);
				
				
				?>
				
				<div class="item">
				<div class="appnt-detail list-detail">
					<h4 class="text-center d-flex justify-content-between"><b>{{$d}}</b>{{$NOMNO}}</h4>
					<ul>
					<li><strong> {{ __('nomination.Name') }}</strong> <span>{{$candidate_name}}</span></li>
					<li><strong>{{ __('nomination.ac') }} &amp; {{ __('nomination.Name') }}</strong> <span>{{$ACNO}} - {{$ACname}}</span></li>
					<!--<li><strong>{{ __('nomination.Status') }}</strong> <span>@if($stst!='0'){{$stst}}@else{{$appoinment_status}}@endif</span></li>-->
					<li><strong>{{ __('nomination.State') }}</strong> <span>{{$state}}</span></li>
					<li><strong>{{ __('nomination.Election') }}</strong> <span>{{$election_name_one}}</span></li>
					<li><strong>{{ __('nomination.Party') }}</strong> <span>{{$party}}</span></li>
					<li><strong>{{ __('step1.Category') }}</strong> <span> {{strtoupper($cat)}}</span></li>
				  </ul> 
					<div class="row m-0 p-3">
					  <div class="col-md-4 col-12 p-0"><strong>{{ __('nomination.Nomination') }}</strong></div>  
					  <div class="col-md-8 col-12 p-0 text-right">
						 <div class="apt-btn">
						  <a href="{{$udurl1}}?pcs=<?php echo encrypt_String($ACNO); ?>&std=<?php echo encrypt_String($std); ?>" class="btn sm-btn dark-pink-btn">{{ __('nomination.View') }}</a>  
						  <a href="{{$dwdulr2}}" class="btn sm-btn dark-purple-btn">{{ __('nomination.DownloadnPrint') }}</a>  
						</div> 
					  </div>  
					</div>
					</div><!-- End Of appnt-detail Div --> 
				</div><!-- End Of item Div -->
				<?php $d++; }}} ?>
			  </div>			   
		    </div><!-- End Of nomin-list Div --> 
			<?php 
			 $psta = app(App\Http\Controllers\Nomination\NominationController::class)->getpaymentStatus($_REQUEST['id']);
			 $isChallanSubmitted = app(App\Http\Controllers\Nomination\NominationController::class)->getChallan($std, $ACNO);
			  
					//echo count($psta) .'--'. count($isChallanSubmitted).'----'.$std.'---'.$ACNO; die;?>
					@if((count($psta)>0) or (count($isChallanSubmitted)>0))
					<?php $isAllFinalized = app(App\Http\Controllers\Nomination\NominationController::class)->isAllFinalized($std, $ACNO);	
					 //echo count($isAllFinalized); die;
					?>	
					@if(count($isAllFinalized)!=0)				
					<div class="text-center py-3"><a href="#" class="btn btn-success btn-big" id="online_pay2" onclick="return finalizeNomination('online_pay2');">{{ __('messages.fn') }}</a></div>	
			       <div class="animate-wrap" id="ttump">
				    <div class="animate-help-text-3">
					 <div class="animate-icon-3">
					      <div class="box bounce-3"><i class="fa fa-hand-o-up" aria-hidden="true"></i></div>
						</div>
					  </div><!-- End Of animate-help-text Div -->
					</div>	
				
					@else 
					<div style="text-align: center; margin-top: 21px; background: #caede3;" class="text-center" id="online_pay3">{{ __('messages.nmf') }}</div>		
				   @endif
				   @endif
		   <div style="text-align: center; margin-top: 21px; background: #caede3;display:none;"  id="NeedToShowSucceess" >{{ __('messages.nmf') }}</div>
			</div> 
			
				
			
			<div class="col-md-8 col-12 pl-0">
			   <div class="appnt-calndr" style="height:608px;">
				   <div class="caldr-wrap frst-week m-3">
                       <div class="booked-appoint">
					     <div class="book-header">
						  <div class="d-flex justify-content-between">
							<div>
							  <ul class="list-inline" style="background: #caede3; margin-right: 40px; width: 328%; height: 50px; margin-bottom: 36px; padding-bottom: 6px; margin-left: -17px; margin-top: -16px;">
								<li class="list-inline-item mr-4" style="margin-left: 30px; margin-top: -8px;">&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; <br>{{ __('messages.predetails2') }}</li> 
								</ul>
								
							 
							<?php 
							
							$ppArray = app(App\Http\Controllers\Nomination\NominationController::class)->getschedule_appoinment($ACNO, $std);	 
							if(count($ppArray) > 0 ) {
							foreach($ppArray as $dataaad){
							
							?>  
							  
							 <ul class="list-inline" style="font-size: 17px;margin-bottom:10px;width: 141%;margin-left: 20px;">
								<li class="list-inline-item" style="width: 250px;"><span class="list-bg"><b class="circle-icon"><i class="fa fa-calendar" aria-hidden="true"></i></b>&nbsp; {{date("D d, M Y", strtotime($dataaad->appointment_date))}}   </span></li>
								<li class="list-inline-item"><span class="list-bg"><b class="circle-icon"><i class="fa fa-clock-o" aria-hidden="true"></i></b>&nbsp; {{$dataaad->appointment_time}} O'clock</span></li>
								@if($dataaad->is_ro_acccept=='1')
								<!--<li class="list-inline-item"><span class="list-bg" style="background:yellowgreen;"><b></b>&nbsp; {{ __('messages.roacc') }} </span></li>-->
								@endif
								
							</ul> 
							<?php }} ?>  	
							</div>  
							<div class="down-print-link">
							  <ul class="list-inline" style="background: #caede3; margin-right: 40px; width: 108%; margin-bottom: 36px; padding-bottom: 8px;">
								<li class="list-inline-item"><a href="download-scheduled?id=<?php echo $_REQUEST['id'];?>">{{ __('nomination.Download') }} <i class="fa fa-download" aria-hidden="true"></i></a></li>
								<!--<li class="list-inline-item"><a href="#">Print <i class="fa fa-print" aria-hidden="true"></i></a></li>-->
							  </ul>  
							</div>  
							
							
						  </div>	 
						   
						 </div><!-- End Of book-header Div -->
						 <div class="book-body p-3">
						   <div class="row">
						     <?php 
							 $acnnod=0;
							 $stndn=0;
							 if(isset($_REQUEST['id'])){ 
							$ids = explode(",", $_REQUEST['id']);
							//foreach($ids as $nomid){
							$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationBookedDetails($ids[0]);	
						
							if($datadd!="NA"){
							$str = explode("***", $datadd);
							$NOMNO=$str[0];
							$candidate_name=$str[1];
							$ACNO=$str[2];
							$acnnod=$str[2];
							$ACname=$str[3];
							$appoinment_status=$str[4];
							$updated_at=$str[5];
							$view_href_cust=$str[6];
							$download_href_cust=$str[7];
							$ROname=$str[8];
							$ROaddress1=$str[9];
							$ROaddress2=$str[10];
							$DistName=$str[11];
							$stndn= $str[15];
							?>
							 <div class="col-md-4 col-12"><label>{{ __('csa.RO_Name') }}:</label> {{$ROname}} </div> 
							 <div class="col-md-4 col-12"><label>{{ __('nomination.ac') }} </label> {{$ACNO}} - {{$ACname}}</div> 
							 <div class="col-md-4 col-12"><label>{{ __('csa.District') }}:</label> {{$DistName}}</div> 
							 <!--<div class="col-md-12 col-12 pt-3"><label>{{ __('csa.Address') }}:</label> {{$ROaddress1}} {{$ROaddress2}}</div>
							 <div class="col-md-4 col-12"><label>Status:</label> {{$appoinment_status}}</div> -->
							<?php } } //} ?>	
							</div>	 
						   </div> 
						    <?php 
							 $expd = explode(",", $_REQUEST['id']);
							 $inr=   count($expd)*100;
							 $dmt=1000000;
							 
							
							 $psta = app(App\Http\Controllers\Nomination\NominationController::class)->getpaymentStatus($_REQUEST['id']);
							 $challanCnt = app(App\Http\Controllers\Nomination\NominationController::class)->getChallan($stndn, $acnnod);
							 
							 $bank = app(App\Http\Controllers\Nomination\NominationController::class)->bankDetails();
							
							 $cEdata = app(App\Http\Controllers\Nomination\NominationController::class)->getEmail();	
							 
							 $bnk='';	
							 if(count($bank)>0){
							  $bnk=__('csa.Edit');	 
							 } else {
							  $bnk=__('csa.Enter'); 
							 }
							 
							?>	
							
							
							
							<div class="d-flex justify-content-between align-items-center p-3 payment-bg">
							
							<div class="info-text">
							  @if((count($psta)>0) or (count($challanCnt)>0))
								<span style="color:black;font-size:15px;"><b>{{ __('messages.chnmesage') }}</b></span>
								<br><br>
								<span style="color:black;font-size:13px;"><b>{{ __('messages.challanPayment') }}:</b> 
								
										<?php 
										$expdText = '';
										$mydated = 'NA';
										if(!empty($psta[0]->pay_date_time)){ 
										$expdText = explode(" ", $psta[0]->pay_date_time);
											if(!empty($expdText[1])){
											$mydated  = $expdText[0] .' '. $expdText[1];
											}
										}
										$ost='NA';
										if(!empty($psta[0]->status)){ 
										    if($psta[0]->status==3){
										     $ost='NA';
										    }
											if($psta[0]->status==2){
										     $ost='Pending';
										    }
											if($psta[0]->status==1){
										     $ost='Success';
										    }
										} else {
											$ost='NA';
										}
								?>
										
										 @if((count($psta)>0))	
											<span >{{$ost}}
										   @endif		
										   @if(count($challanCnt)>0)
												   @if(!empty($challanCnt[0]->payByCash) && ($challanCnt[0]->payByCash > 0))
													Opt to be paid by Cash
												   @else
													Opt to be paid by Challan
												   @endif  	
										   @endif	
								
								</span>
								
								
								
								
								
								<span style="color:black;margin-left:100px;font-size:13px"><b>{{ __('messages.challanDteTime') }}:</b> 
											
										   @if((count($psta)>0))	
												{{$mydated}}
										   @endif		
										   @if(count($challanCnt)>0)
												 @if(!empty($challanCnt[0]->payByCash) && ($challanCnt[0]->payByCash > 0))
												  	@if(!empty($challanCnt[0]->createdAt)){{date('d-m-Y h:i:s', strtotime($challanCnt[0]->createdAt))}}@endif			
												 
												 @else	 
													@if(!empty($challanCnt[0]->createdAt)){{date('d-m-Y h:i:s', strtotime($challanCnt[0]->createdAt))}}@endif		
												 @endif 
										   
										   @endif	
											  
											  
											  
											  
								
								</span>
								<span style="color:black;margin-left:100px;font-size:13px"><b>{{ __('messages.Mode') }}:</b> 
											 @if((count($psta)>0))
												<span  onclick="return showPaymentDetails();" style="color:blue;cursor:pointer;">Online</span>
											   <!--<span>Online</span>-->
											  @endif	
											  
											 @if(count($challanCnt)>0)
												 @if(!empty($challanCnt[0]->payByCash) && ($challanCnt[0]->payByCash > 0))
													 <span>Pay By Cash</span>
												 @else		
											    	<span  onclick="return showPaymentDetails();" style="color:blue;cursor:pointer;">Challan</span>
											     @endif
											  @endif	
												</span>
								<br><br>
							@endif	
								
								
								
							  <div onclick="return showBankDetails();" class="btn btn-primary">{{$bnk}} {{ __('csa.Bank_Details') }}</div>
							</div>								   
							
  
                                        <div class="row" style="display:none;">
                                            <div class="col-md-4 col-md-offset-4">
                                            <form name="frmPayment" id="frmPayment" action="" method="post">
                                                <!-- if session found create new Token -->
                                                <!--end create new Token-->
                                                <br class="clearfix">
                                                <table width="100%" border="1" class="Payment" cellpadding="0" cellspacing="0">
                                                <tbody><tr>
                                                <td width="16%"><b>{{ __('nomination.Name') }}</b></td>
                                                <td width="30%"><spacer>{{$candidate_name}}	</spacer></td>
                                                <td width="30%"><strong>{{ __('nomination.Fee') }}</strong> :&nbsp; <img src="https://rtionline.gov.in/images/rsSymbol.png" width="8" height="10" style="border:none;"><cite>{{$dmt}}</cite></td>
                                                <!--<td width="24%">&nbsp;</td>-->
                                                </tr>
                                                </tbody></table>
                                                <br class="clearfix">
                                                <div id="SBIBankDiv">
                                                </div>
                                            </form>
                                        </div>
										<!--End Left SideBar--> 

                                       <br class="clearfix">
                                    </div>
								<?php //echo "<pre>"; print_r($psta); ?>
                                
						
							    @if((count($psta)<=0) && (count($challanCnt)<=0))
								<div>	
									<strong class="pr-2 btn font-big blink-effect"> {{ __('csa.Pay_Security_Deposit_Online') }} </strong>
                                    <div  onclick="red();" style="float: right;cursor:pointer;" class="razorpay-payment-button">
										PAY
									</div>								
                                </div>			
								@elseif( (count($challanCnt) > 0))	
					@if($finalize_after_payment!=1)				
				<!--	<div style="cursor:pointer;color:blue;" id="online_pay2" onclick="return finalizeNomination('online_pay2');">{{ __('messages.fn') }}-- pp</div>	-->
					@else
					<!--<div style="cursor:pointer;color:gray;" id="online_pay2" >{{ __('messages.nmf') }}</div>	-->
					@endif	
								<!--<div><strong class="pr-2">{{ __('messages.Challan') }}</strong>
							    <span style="color: blue; font-size: 13px; margin-right: 10px; cursor: pointer;" onclick="return showPaymentDetails();">{{ __('messages.Challan_Submitted') }} </span></div> -->
								<!-- Modal confirm schedule --> 
									<div class="modal fade modal-confirm" id="paymentPopUp">
										<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
										  <div class="modal-content">
										   <div class="pop-header pt-3 pb-1">
											  <div class="animte-tick"><span>&#10003;</span></div>	
											<h5 class="modal-title">{{ __('messages.Challan') }}</h5> 
											<div class="header-caption">
											</div>		
											</div>
											<?php 
											$acnamed = app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($stndn, $acnnod);
											$stated = app(App\Http\Controllers\Nomination\NominationController::class)->getState($stndn);
											?>
											<div class="modal-body">
											<ul>
											<li><label>{{ __('nomination.ac') }} &amp; {{ __('nomination.Name') }}:</label><span><?php echo $acnnod;  ?>-<?php echo $acnamed,', '.$stated; ?></span></li>
											
											<li><label>{{ __('messages.ChallanNO') }}</label><span> {{ $challanCnt[0]->challan_no }}</span></li> 
											
											<li><label>{{ __('messages.ChallanDate') }}</label><span> {{ $challanCnt[0]->createdAt	}}</span></li>
											
											<li><label>{{ __('messages.ViewChallan') }}</label><span><a href="<?php echo url('/'); ?>/{{$challanCnt[0]->challan_receipt}}" target="_blank">View</a></span></li> 
											
											 </ul> 
											  <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('step1.afterPayment') }}</p>	
											 <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('messages.rosubject') }}</p>	 
											 <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('csa.verification_doc') }} </p>	
											</div>
											<div class="confirm-footer">
											  <button type="button" class="btn dark-pink-btn font-big" data-dismiss="modal">{{ __('nomination.ok') }}</button>
											</div>
										  </div>
										</div> 
									  </div><!-- End Of confirm Modal popup Div -->
								@else 
					@if($finalize_after_payment!=1)				
					<!--<div style="cursor:pointer;color:blue;" id="online_pay2" onclick="return finalizeNomination('online_pay2');">{{ __('messages.fn') }}</div>	-->
					@else
					<!--<div style="cursor:pointer;color:gray;" id="online_pay2" >{{ __('messages.nmf') }}</div>-->	
					@endif		
								<!--<div><strong class="pr-2">{{ __('csa.Paid') }} </strong>
							    <span style="color: blue; font-size: 13px; margin-right: 10px; cursor: pointer;" onclick="return showPaymentDetails();">{{ __('csa.Details') }} </span></div>-->
									<!-- Modal confirm schedule -->
									<div class="modal fade modal-confirm" id="paymentPopUp">
										<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
										  <div class="modal-content">
										   <div class="pop-header pt-3 pb-1">
											  <div class="animte-tick"><span>&#10003;</span></div>	
											<h5 class="modal-title">{{ __('csa.Receipt') }} </h5> 
											<div class="header-caption">
											</div>		
											</div>  
										<?php 
										$expdText = '';
										$mydated = 'NA';
										$acnamed = app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($stndn, $acnnod);
										$stated = app(App\Http\Controllers\Nomination\NominationController::class)->getState($stndn);
										if(!empty($psta[0]->pay_date_time)){ 
										$expdText = explode(" ", $psta[0]->pay_date_time);
										   if(!empty($expdText[1])){
											$mydated  = $expdText[1];
											}
										}
										$ost='NA';
										if(!empty($psta[0]->status)){ 
										    if($psta[0]->status==3){
										     $ost='NA';
										    }
											if($psta[0]->status==2){
										     $ost='Pending';
										    }
											if($psta[0]->status==1){
										     $ost='Success';
										    }
										} else {
											$ost='NA';
										}
										
										//echo "<pre>"; print_r($psta); 
										
										?>	
											<div class="modal-body">
											  <ul>
												<!--<li><label>Transaction Id:</label><span><?php echo $psta[0]->pament_gateway_refrence_no_grn;  ?></span></li>-->
												<li><label>{{ __('nomination.ac') }} &amp; {{ __('nomination.Name') }}:</label><span><?php echo $psta[0]->ac_no;  ?>-<?php echo $acnamed,', '.$stated; ?></span></li>
												<!--<li><label>{{ __('csa.Amount') }}:</label> <span>Rs. <?php echo $psta[0]->amount1;  ?></span></li>
												<li><label>{{ __('csa.Payment_Status') }}:</label> <span>{{$ost}}</span></li>-->
												<!--<li><label>{{ __('csa.Amount') }}:</label> <span>INR <?php //echo $psta[0]->transaction_amount;  ?></span></li>-->
												<li><label>{{ __('csa.Payment_Date') }}:</label><span>@if(!empty($psta[0]->pay_date_time))<?php echo date('d-m-Y', strtotime($psta[0]->pay_date_time));  ?>@else{{'NA'}}@endif</span></li>
												@if($isPaymentConfig!="GUJ")		
												<li><label>{{ __('csa.Payment_Time') }}:</label><span><?php echo $mydated;  ?></span></li>
												@endif
												
												@if($isPaymentConfig=="GUJ")		
													@if(!empty($psta[0]->amount1))
												<li><label>{{ __('csa.Amount') }}:</label><span>Rs. <?php echo $psta[0]->amount1;  ?></span></li>
												<li><label>{{ __('csa.Payment_Status') }}:</label><span><?php echo $ost;  ?></span></li>
													@endif
												@endif
												
												
												@if(isset(($psta[0]->challan_url)) && !empty($psta[0]->challan_url))
												<li><label>Receipt:</label><span><a href="<?php echo $psta[0]->challan_url; ?>" target="_blank">Download</a></span></li>
												@endif
											 </ul> 
											  <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('step1.afterPayment') }}</p>	
											  <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('messages.rosubject') }}</p>	
											 <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('csa.verification_doc') }} </p>	
											</div>
											<div class="confirm-footer">
											  <button type="button" class="btn dark-pink-btn font-big" data-dismiss="modal">{{ __('nomination.ok') }}</button>
											</div>
										  </div>
										</div>
									  </div><!-- End Of confirm Modal popup Div -->
								
								@endif	
                             </div><!-- End Of d-flex Div -->	
							 
					   </div><!-- End Of booked-appoint Div -->
					  
					  </div><!-- End of frst week -->
						  
						  <p class="note-warn" style="margin-top: -50px; margin-left: 17px; font-size: 13px;"><strong><i>{{ __('csa.Instruction') }}<sup>*</sup></i></strong>{{ __('step1.afterPayment') }}</p>	
						  <br>
						  
						  <p class="note-warn" style="margin-top: -21px; margin-left: 17px; font-size: 13px;"><strong><i>{{ __('csa.Instruction') }}<sup>*</sup></i></strong>{{ __('messages.rosubject') }}</p>	
						  <br>
						 <p class="note-warn" style="margin-top: -21px; margin-left: 17px; font-size: 13px;"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('csa.verification_doc') }}</p>	
			  </div><!-- End Of appnt-calndr Div -->   
			</div> 
		   </div>
		 </div>  
		 <div class="card-footer">
		<div class="text-left col-md-3 col-3 pl-3" style="height:1px;">
		<?php 
		$stno='';
		$pcnno='';
		$acst = app(App\Http\Controllers\Nomination\NominationController::class)->getPcStByNo($expd[0]); 
			 if(!empty($acst)){
				$entt = explode("***", $acst);
				$stno=encrypt_String($entt[0]);
				$pcnno=encrypt_String($entt[1]);
			 }
		
		?>
				
			  <a href="{{'nominations?pcs='.$pcnno.'&std='.$stno}}" class="btn btn-lg font-big dark-pink-btn" style="color: white;">{{ __('step1.Back') }} </a>  
			</div> 	
		   <div class="apt-btn text-right"> 
				@if($appoinment_status!='Cancel')
			  <a href="#" class="btn btn-lg font-big dark-pink-btn"  data-target="#basicExampleModal" data-toggle="modal">{{ __('step1.Cancel') }}</a>  
			  @endif	
			  
			    <a href="#" class="btn btn-lg font-big dark-purple-btn pop-actn" onclick="return showhide();"> {{ __('messages.res') }}</a>  
			</div> 
		 </div>  
	   </div>
	 </div>

  
	 
	 
	 
	<!-- Modal For Cancel-->
	<div class="modal fade" id="basicExampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	  aria-hidden="true">
	  <form name="cancel_form" id="cancel_form" method="POST"  action="{{url('/nomination/cancel-nomination-prev') }}" autocomplete='off' enctype="x-www-urlencoded">
     {{ csrf_field() }}
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">{{ __('messages.canpre') }}</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		   <input type="hidden" name="nom_id" value="<?php echo $_REQUEST['id']; ?>">
		  <div class="modal-body" style="margin-left:-37px;">
          <ul>
          	<!--<li><label>Name:</label> <span> {{$candidate_name}}  </span></li>-->
          	<?php  if(isset($_REQUEST['id'])){ 
			$ids = explode(",", $_REQUEST['id']);
			foreach($ids as $nomid){
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationDetails($nomid);	
			$str = explode("***", $datadd);
			$NOMNO=$str[0];
			$ACNO=$str[2];
			$ACname=$str[3];
			?>
			<li><label>Nomination No.: </label> <span> {{$NOMNO}}</span></li>
          	<!--<li><label>AC No. &amp; Name: </label> <span> {{$ACNO}} - {{$ACname}}</span></li>-->
			<?php }}  ?>
			<li><label>{{ __('nomination.Status') }}:</label> <span> {{$appoinment_status}}</span></li>
		   </ul>
		</div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
			<button type="submit" class="btn btn-primary" onclick="return showLoader();" style="background: #bb4292; border: none;">{{ __('messages.Cancel_Preference') }}</button>
		  </div>
		  
		  <span style="text-align: center;display:none;" id="loader3">
			 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
		  </span>
		  
		  
		</div>
	  </div>
	  </form>
	</div>
	
	<!-- Modal For Schedule Appoinment-->
	<div class="modal fade" id="basicExampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	  aria-hidden="true">
	  <form name="appoinment_form" id="appoinment_form" method="POST"  action="{{url('/nomination/prev/post') }}"  autocomplete='off' enctype="x-www-urlencoded">
     {{ csrf_field() }}
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
		<!--	<h5 class="modal-title" id="areyu">{{ __('csa.Confirmation') }}</h5>		-->	
			<h5 class="modal-title" >Confirmation</h5>			
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <input type="hidden" name="daytime" id="daytime">
		  <input type="hidden" name="testing" id="testing">
		  <input type="hidden" name="id" value="{{$_REQUEST['id']}}">
		  <input type="hidden" name="slot" id="slot">
		<div class="modal-body" id="textmsg" style="border-bottom: 1px solid #e9ecef;">
			<div class="row">
							<div class="col-md-8 col-12">
							  <ul class="list-inline" style="width: 157%;">
							    <input type="hidden" name="nom_id" value="{{$nom_id}}">
								<li class="list-inline-item mr-4" style="font-size: 17px;"> 
								<?php echo __('messages.are_you_sure_p'); ?>
							  </ul>  
							</div>   
						  </div>
		  </div>
		 <!-- <div class="modal-body" style="margin-left:-37px;">
          <ul>
          	<li><label>Name:</label> <span> {{$candidate_name}}</span></li>
			
			<?php  if(isset($_REQUEST['id'])){ 
			$ids = explode(",", $_REQUEST['id']);
			foreach($ids as $nomid){
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationDetails($nomid);	
			if($datadd!="NA"){
			$str = explode("***", $datadd);
			$NOMNO=$str[0];
			$ACNO=$str[2];
			$ACname=$str[3];
			?>
			<li><label>Nomination No.: </label> <span> {{$NOMNO}}</span></li>
          	<li><label>AC No. &amp; Name: </label> <span> {{$ACNO}} - {{$ACname}}</span></li>
			  
			<?php } } } ?>  
			<li><label>Election</label> <span>GENERAL-2020</span></li>
			<li><label>Status:</label> <span> {{$appoinment_status}}</span></li>
		   </ul>-->
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
			<button type="submit" class="btn btn-primary" style="background: #bb4292; border: none;" id="reareyu" onclick="return showsc();">
			{{ __('messages.Yes') }}</button>
		  </div>
		  <span style="text-align: center;display:none;" id="loader">
		   <!--<span>{{ __('messages.emailsms') }} </span>-->
		  <br>
		 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('finalize.Please_Wait') }}
		</span>
		</div>
		</div>
	  </div>
	  </form>
	</div> 
	
	
	
	 
	 
	<!-- Modal confirm schedule -->
    <div class="modal fade modal-confirm" id="confirm">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}}</h5>
		<div class="header-caption">
		  <p><?php //echo  __('messages.succpre');  ?>			
			{{ __('messages.confpre') }} 
		  </p>			  
		</div>		
        </div>
        <!-- Modal body -->
        <div class="modal-body">
          <ul>
          	<?php if(isset($_REQUEST['id'])){ 
			$ids = explode(",", $_REQUEST['id']);
			foreach($ids as $nomid){
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationDetails($nomid);	
			$str = explode("***", $datadd);
			$NOMNO=$str[0];
			$ACNO=$str[2];
			$ACname=$str[3];
			?>
			<li><label>{{ __('nomination.Nomination_No') }}</label> <span>{{$NOMNO}}</span></li>
			<?php }} ?>
		 </ul>
		 <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('step1.afterPayment') }}</p>	
		 <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('messages.rosubject') }}</p>	
		 <p class="note-warn"><strong><i>{{ __('csa.Instruction') }} <sup>*</sup></i></strong>{{ __('csa.verification_doc') }}</p>	
        </div>
        
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
          <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
        </div>
        
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div -->
  
  
  
	
	<div class="modal fade modal-cancel" id="emptySlot">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>
		<div class="header-caption">
		  <p>{{ __('messages.pleasesel') }}</p>	
		</div>		
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
          <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
        </div>
      </div>
    </div>
  </div>
	
    <div class="modal fade modal-cancel" id="maxError">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>
		<div class="header-caption">
		  <p>{{ __('messages.3max') }}</p>	
		</div>		
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
          <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
        </div>
      </div>
    </div>
  </div>
	
	
	<div class="modal fade modal-cancel" id="red">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1 mb-2">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}}</h5>
        </div>
	   <div class="px-3">	
		<div style="padding: 0px; text-align: center; color:#f0587e; margin:1rem 0;">
          **{{ __('messages.rosubject') }}
		</div>
	  </div>	
		{{-- @if($isPaymentConfig=="YES")--}}
        <div id="paySec">
		<div class="modal-body p-0 text-center">
          <!--<ul>
			<li><label>{{ __('messages.payment') }} </label></li>
			<li><label>{{ __('messages.sdepam') }} : RS. {{--{{$amount1}} --}}</label></li>
		 </ul>
		-->
        <div class="confirm-footer p-0">  
			<!--<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>	
			&nbsp; 
		   <button type="button" class="btn dark-pink-btn" data-dismiss="modal" style="height: 30px; width: 12%;"  onclick="return redirect();">
		  {{ __('nomination.ok') }}</button>
		  &nbsp; &nbsp; &nbsp;-->
		  
		  
          <div class="foot-highlight">  
		    <div class="orSparate" style="width: 50px;">{{ __('messages.OR') }}</div>
			<i class="fa fa-check-square-o" aria-hidden="true"></i>
			  {{ __('messages.payBy') }} <span class="btn btn-outline-primary btn-sm" onclick="return showHidePay('1');">
			 {{ __('messages.SubBy') }} </span>
         </div>
        </div>
		 </div>
		 </div>
		{{--@endif --}}
		@if($isPaymentConfig=="GUJ")
        <div id="payGuj">
		<div class="modal-body p-0 text-center">
          <ul>
			<li><label><b>For Online payment support please call 07923257494</b></label></li>
			<li><label><b>10 am to 10 pm</b></label></li>
			<li><label>You will be redirected at Gujrat State Payment Gateway</label></li>
			<li><label>{{ __('messages.sdepam') }} : RS. {{$Total_amount}} </label></li>
		 </ul>
		
        <div class="confirm-footer p-0">  
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>	
			&nbsp; 
		   <button type="button" class="btn dark-pink-btn" data-dismiss="modal" style="height: 30px; width: 12%;"  onclick="return redirect_gujrat();">
		  {{ __('nomination.ok') }}</button>
		  &nbsp; &nbsp; &nbsp;
		  
		  
		  
		  
		  
		  
		  
          <div class="foot-highlight">   
		    <div class="orSparate" style="width: 50px;">{{ __('messages.OR') }}</div>
			<i class="fa fa-check-square-o" aria-hidden="true"></i>
			  {{ __('messages.payBy') }} <span class="btn btn-outline-primary btn-sm" onclick="return showHidePay('1');">
			 {{ __('messages.SubBy') }} </span>
         </div>
        </div>
		 </div>
		 </div>
		@endif
		
		
		<?php 
		$strdtdtd='';
		if($isPaymentConfig=='NO'){
			$strdtdtd='block';
		} else {
			$strdtdtd='none';
		}
		?>
		
		
		
		 <form name="submit_challan" id="submit_challan" method="POST"  action="{{url('/nomination/challan/post') }}"  autocomplete='off' enctype="multipart/form-data"  style="margin-top:-26px;display:{{$strdtdtd}};">
		  {{ csrf_field() }}
		 <div class="modal-body p-0">
          <!--<div style="text-align: center; font-size: 15px; font-weight: bold;">{{ __('messages.challanSubmit') }}</div> -->
		  <!-- @if(!empty($state_payment_url)) <?php //echo $state_payment_url; ?>
		    <div class="mx-5 mt-3 text-center">{{ __('messages.paylink') }}<span><a href="http://<?php echo $state_payment_url; ?>" target="_blank">&nbsp; Payment Link</a></span></div>
		   @endif	-->
		  <br> 
		    <ul style="text-align: center; background: aliceblue; font-size: 16px; font-weight: bold;opacity: .8;"> Pay By Challan </ul>
		  <input type="hidden" name="st" value="{{$stndn}}">
		  <input type="hidden" name="pc" value="{{$acnnod}}">
		  <ul>
			<li><label>{{ __('messages.chananNumber') }}</label><label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="challan_number" id="challan_number" style="margin-left: 14px; width: 167px;"  maxlength="15"></label></li>
			<li><label>{{ __('messages.challanDateSub') }}</label>&nbsp;&nbsp;&nbsp;<label><input type="text" name="challan_date"  id="challan_date"  readonly style="width: 167px;margin-left: 5px;"></label></li>
			<li><label>{{ __('messages.ChallanReceipt') }}</label><label>&nbsp;&nbsp;&nbsp;<input type="file" name="file" id="challan_receipt" style="margin-left: 60px; width: 97px;" ></label></li> 
			<li style="color:red;" id="preError"></li>    
		 </ul>
		 <div style="text-align:center;display:none;" id="previewId">
		     <iframe id="iframeid"  style="width: 360px; height: 218px;"></iframe>
		 </div>
		
		  <div class="confirm-footer p-0">  
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>	
			&nbsp; 
		   <button type="submit" class="btn dark-pink-btn" style="height: 30px;"  onclick="return submitChallan();">
		  {{ __('messages.Submit') }} </button><br> <br>
		  &nbsp; &nbsp; &nbsp;	
		  
		  
	  <div class="foot-highlight" style="background: white;"> 
		 <div class="orSparate" style="width: 50px;">  {{ __('messages.OR') }} </div>
		 
		    <ul style="text-align: center; background: aliceblue; font-size: 16px; font-weight: bold;margin-top: 11px;"> Pay By Cash </ul>
		 <ul style=" margin-top: 29px;">
			<input type="hidden" name="payByCash" id="payByCash">
			<li> <i class="fa fa-check-square-o" aria-hidden="true"></i> I will opt for cash payment of security deposit to the Returning officer</li>
			<button type="submit" class="btn dark-pink-btn" style="height: 30px;" onclick="return payByCashSubmit();">  {{ __('messages.Submit') }}</button>
		 </ul>
	  </div>
		
			
          @if($isPaymentConfig=='YES')
		
		<div class="foot-highlight"> 
         <div class="orSparate" style="width: 50px;">  {{ __('messages.OR') }} </div>
		 <i class="fa fa-check-square-o" aria-hidden="true"></i>
		{{ __('messages.payByGateway') }}  <span class="btn btn-outline-primary btn-sm" onclick="return showHidePay('2');">
		 {{ __('messages.SubBy') }} </span>
		 </div>
		 @endif		  
		 
		 
		  @if($isPaymentConfig=='GUJ')
		
		<div class="foot-highlight"> 
         <div class="orSparate" style="width: 50px;">  {{ __('messages.OR') }} </div>
		 <i class="fa fa-check-square-o" aria-hidden="true"></i>
		{{ __('messages.payByGateway') }}  <span class="btn btn-outline-primary btn-sm" onclick="return showHidePay('3');">
		 {{ __('messages.SubBy') }} </span>
		 </div>
		 @endif		  
		 
		 
        </div> 
		
		
		  <span style="text-align: center;display:none;margin-left: 9em;" id="afhbdsfbdjg">
			 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
		  </span>
		
		 </div>
		 </form>
      </div>
    </div>
  </div>
	
	
	
	
  <!-- Modal Cancel on success-->
    <div class="modal fade modal-cancel" id="cancel">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}}</h5>
		<div class="header-caption">
		  <p>{{ __('messages.Succcanpre') }}</p>	
		</div>		
        </div>
        <!-- Modal body -->
        <div class="modal-body">
          <ul>
          	<?php if(isset($_REQUEST['id'])){ 
			$ids = explode(",", $_REQUEST['id']);
			foreach($ids as $nomid){
			$datadd = app(App\Http\Controllers\Nomination\NominationController::class)->getNominationDetails($nomid);	
			$str = explode("***", $datadd);
			$NOMNO=$str[0];
			$ACNO=$str[2];
			$ACname=$str[3];
			?>
			<li><label>{{ __('nomination.Nomination_No') }}:</label> <span>{{$NOMNO}}</span></li>
          	<!--<li><label>AC No. &amp; Name:</label> <span>{{$ACNO}} - {{$ACname}}</span></li>-->
			<?php }} ?>
			
			<!--<li><label>{{ __('csa.Status') }}:</label> <span>{{$appoinment_status}}</span></li>-->
		 </ul>
		<!-- <p class="note-warn"><strong><i>Instruction <sup>*</sup></i></strong>Please carry all original and necessary documents for verification</p>-->
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
          <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
        </div>
      </div>
    </div>
  </div>
  <!-- Bank Details-->
	<div class="modal fade" id="bankDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	  aria-hidden="true">
	  <form name="cancel_form" id="cancel_form" method="POST"  action="{{url('/nomination/save-bank-prev') }}" autocomplete='off' enctype="x-www-urlencoded">
     {{ csrf_field() }}
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header pop-header">
			<h5 class="modal-title" id="exampleModalLabel">{{ __('csa.Bank_Details') }}</h5>
			<input type="hidden" name="nid" value="<?php echo $_REQUEST['id']; ?>">
			<input type="hidden" name="bank_id" value="<?php if(!empty($bank[0]->id)){ echo $bank[0]->id; } else { echo 0; }; ?>">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">  
          <table>
          	<tr><td>{{ __('csa.Name_as_account') }}:</td> <td><input type="text" name="candidate_name" id="candidate_name" value="<?php if(!empty($bank[0]->candidate_name)){ echo $bank[0]->candidate_name; }  ?>"></td></tr>
          	<tr><td>{{ __('csa.Bank_Name') }}:</td> <td><input type="text" name="bank_name" id="bank_name" value="<?php if(!empty($bank[0]->bank_name)){ echo $bank[0]->bank_name; }  ?>"></td></tr> 
          	<tr><td>{{ __('csa.Account_Number') }}:</td> <td><input type="number" name="account_number"  id="account_number" value="<?php if(!empty($bank[0]->account_number)){ echo $bank[0]->account_number; }  ?>" min="8"></td></tr>
          	<tr><td>{{ __('csa.Confirm_Account_Number') }}:</td> <td><input type="number" name="confirm_account_number"  id="confirm_account_number"  value="<?php if(!empty($bank[0]->confirm_account_number)){ echo $bank[0]->confirm_account_number; }  ?>" min="8"></td></tr>
          	<tr><td>{{ __('csa.IFSC') }}:</td> <td><input type="text" name="ifsc_code"  id="ifsc_code" value="<?php if(!empty($bank[0]->ifsc_code)){ echo $bank[0]->ifsc_code; }  ?>" min="5"></td></tr>
		  </table>	
		</div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
			<button type="submit" class="btn btn-primary" style="background: #bb4292; border: none;" onclick="return valBank();">{{ __('csa.Submit') }}</button>
		  </div>
		</div>
	  </div>
	  </form>
	</div>
	<div id=""></div> 
   </main>   
   
   <?php   
   
   //echo "PPPPP";
   
	if($isPaymentConfig=="GUJ"){
		
	

$fnlStrDAta="User_id=".$User_id."|Init_date=".$Init_date."|Transaction_id=".$Transaction_id."|Tax_type=".$Tax_type."|RegNo=".$RegNo."|Name=".$NameGuj."|Token_no=".$Token_no."|Total_amount=".$Total_amount."|Phone_no=".$Phone_no."|Tax_period_from=".$Tax_period_from."|Tax_period_to=".$Tax_period_to."|Purpose=".$Purpose.'~'.$Total_amount."|MerchantId=".$MerchantId;
		
		
		$key='q4UOLnbuVc0mP8Jf634f1zCGVy2pf9lj';
		$iv='q4UOLnbuVc0mP8Jf';
		
		$enc_method = "AES-128-CBC"; 	
		
		$trd=trim($fnlStrDAta);		
		
		
		
		$encdata=openssl_encrypt($trd, $enc_method, $key, $options=0, $iv);			
		$encryptedDU=openssl_encrypt($DU, $enc_method, $key, $options=0, $iv);	
		
		//$rd='https://cybertreasuryuat.gujarat.gov.in/CyberTreasury_UAT/connectDept?service=DeptPortalConnection'; //UAT
		$rd="https://cybertreasury.gujarat.gov.in/CyberTreasury/connectDept?service=DeptPortalConnection";  // Live
		
		
		?>
		
		<form method="POST" name="redGuj"  action="<?php echo $rd; ?>">
		<input type="hidden" name="_token" value="{{csrf_token()}}">
		<input type="hidden" name="CTP_DATA" value="<?php echo $encdata; ?>">
		<input type="hidden" name="Dept_call" value="first">
		<input type="text" name="RU" value="<?php echo $RU; ?>">
		<input type="text" name="DU" value="<?php echo $encryptedDU; ?>">
		<input type="hidden" name="MerchantId" value="<?php echo $MerchantId ?>">
		<input type="hidden" name="batchId" value="0810202000101">
		<input type="hidden" name="token" value="24343">
		</form>
		
		
		
		
	
	<?php } ?>
   
   
   
	
	
	<?php    
	if($isPaymentConfig=="YES"){
		$return_url=trim($return_url);
		$reff_no=trim($reff_no);
		$dep_code=trim($dep_code);
		$dist_code=trim($dist_code);
		$payment_head=trim($payment_head);
		$scheme_code=trim($scheme_code);
		$office_code=trim($office_code);
		$trs_code=trim($trs_code);
		$remitter_name=trim($remitter_name);
		//$pan=trim($pan);
		//$email=trim($email);
		$mobile=trim($mobile);
		$address=trim($address);
		$remarks=trim($remarks);
		$hd_ac1=trim($hd_ac1);
		$amount1=trim($amount1);
		$txn_amount=trim($txn_amount);
		
		$strtomd='return_url='.$return_url.'|'.'reff_no='.$reff_no.'|'.'dep_code='.$dep_code.'|'.'dist_code='.$dist_code.'|'.'office_code='.$office_code.'|'.'trs_code='.$trs_code.'|'.'remitter_name='.$remitter_name.'|'.'mobile='.$mobile.'|'.'address='.$address.'|'.'remarks='.$remarks.'|'.'payment_head='.$payment_head.'|'.'scheme_code='.$scheme_code.'|'.'hd_ac1='.$hd_ac1.'|'.'amount1='.$amount1.'|'.'txn_amount='.$txn_amount;
		
		
		$ttrimst=trim($strtomd);
		$checkSum=md5($ttrimst);

		$url='return_url='.$return_url.'|'.'reff_no='.$reff_no.'|'.'dep_code='.$dep_code.'|'.'dist_code='.$dist_code.'|'.'office_code='.$office_code.'|'.'trs_code='.$trs_code.'|'.'remitter_name='.$remitter_name.'|'.'mobile='.$mobile.'|'.'address='.$address.'|'.'remarks='.$remarks.'|'.'payment_head='.$payment_head.'|'.'scheme_code='.$scheme_code.'|'.'hd_ac1='.$hd_ac1.'|'.'amount1='.$amount1.'|'.'txn_amount='.$txn_amount;

		$key='E(*x5lcyam%$.9dx';
		$iv='E(*x5lcyam%$.9dx';
		$enc_method = "AES-128-CBC";		
		
		$trd=trim($url).'|'.'checkSum='.$checkSum;		
		//echo $trd; die;	
		//COMDEPT
		
		
		$encdata=openssl_encrypt($trd, $enc_method, $key, $options=0, $iv);			
		
		
		if(isset($_REQUEST['encdata'])){
			// Return Response
			// reff_no=asaxzx|grn=BHR202007507300E|status_code=Pending|status_desc=Pending Transaction|bank_reff_no=CPAAFJXAK8|bank_code=SBI|challan_url=https://e-receipt.bihar.gov.in/brcs/mainappservlet?action=gotoChallanPageForDepartment&uniqueId=AvNwK448oFlt6qv40l6VIrQPr4TpWipsDO6F3zp9gwM=|cin=null|pay_date=2020-07-29 20:51:31.0|checkSum=c1899f2792a39989a09e86addd966d1a
			
			$ru=$_REQUEST['encdata'];
			$decrypt=openssl_decrypt($ru, $enc_method, $key, $options=0, $iv);		
			echo "<pre>"; print_r($decrypt);
			die;
		}
		// echo $encdata; 
		$rd='https://e-receipt.bihar.gov.in/brcs/callpaygateway';		
		?>
		<form method="POST" name="paymentred" action="<?php echo $rd; ?>">
		<input type="hidden" name="_token" value="{{csrf_token()}}">
		<input type="hidden" name="encdata" value="<?php echo $encdata; ?>">
		<input type="hidden" name="merchant_code" value="{{$merchant_code}}">
		</form>
	
	<?php }  // Session::flash('is_payment',"yes"); ?>
	
	
	<!-- Modal confirm schedule --> 
    <div class="modal fade modal-confirm" id="payment">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}},</h5>
		<div class="header-caption">
		  <p id="challanMessage1">{{ __('csa.Payment_done') }}.</p>	
		</div>		
        </div> 
		<br>
		<span style="text-align: center;" id="txtchalan1">{{ __('step1.nowFina') }}</span> 
		<div class="text-center py-3"><a id="txt_final1" href="#" class="btn btn-success btn-big"  onclick="return moveLoader('<?php echo $stndn;  ?>','<?php echo $acnnod; ?>');">{{ __('messages.fn') }}</a>
		&nbsp; &nbsp; &nbsp;
		
		<span class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('step1.Cancel') }}</button>
        </span>
		</div>	
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div --> 
  
  <!-- Modal confirm schedule --> 
    <div class="modal fade modal-confirm" id="nopayment">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}},</h5>
		<div class="header-caption">
		  <p id="challanMessage1">Payment status not available.</p>	
		</div>		
        </div> 
		<br>
		&nbsp; &nbsp; &nbsp;
		
		<span class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
        </span>
		</div>	
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div --> 
	
<!-- Modal confirm schedule -->
    <div class="modal fade modal-confirm" id="nopdf_model">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}},</h5>
		<div class="header-caption">
		  <p style="color:red;font-size:20px;">{{ __('messages.pleasePDF') }}</p>	
		</div>		
        </div>
        
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
          <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
        </div>
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div -->		
  
  
  
 <!-- Modal confirm schedule -->
    <div class="modal fade modal-confirm" id="pay_by_cash_message">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}},</h5>
		<div class="header-caption">  
		  <p id="challanMessage3">{{ __('messages.payByCashSucc') }}</p>	
		</div>		
        </div>
		<br>
		<span style="margin-left:60px;text-align:center;" id="txtchalan3">{{ __('step1.nowFina') }}</span> 
		
		 <span style="text-align: center;display:none;margin-left: 9em;" id="L123">
			 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
		  </span>
		<div class="text-center py-3"><a id="txt_final3" href="#" class="btn btn-success btn-big"  onclick="return moveLoader('<?php echo $stndn;  ?>','<?php echo $acnnod; ?>');">{{ __('messages.fn') }}</a>
		&nbsp; &nbsp; &nbsp;
		
		<span class="confirm-footer">
		  <button type="button" id="c123" class="btn dark-pink-btn" data-dismiss="modal">{{ __('step1.Cancel') }}</button>
        </span>
		
		</div>	
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div -->	
	<!-- bank_model-->
    <div class="modal fade modal-confirm" id="bank_model">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}},</h5>
		<div class="header-caption">
		  <p>{{ __('csa.success') }}.</p>	
		</div>		
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
          <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
        </div>
        
      </div>
    </div>
  </div><!-- End Of bank_model -->
	
 <!-- Modal confirm schedule -->
    <div class="modal fade modal-confirm" id="challan_model">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}},</h5>
		<div class="header-caption">
		  <p id="challanMessage">{{ __('messages.challanSUcc') }}</p>	
		</div>		
        </div>
		<br>
		<span style="margin-left:60px;text-align:center;" id="txtchalan">{{ __('step1.nowFina') }}</span> 
		
		<span style="text-align: center;display:none;margin-left: 9em;" id="L3333">
			 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
		  </span>
		
		<div class="text-center py-3"><a id="txt_final" href="#" class="btn btn-success btn-big"  onclick="return moveLoader('<?php echo $stndn;  ?>','<?php echo $acnnod; ?>');">{{ __('messages.fn') }}</a>
		&nbsp; &nbsp; &nbsp;
		
		  <button type="button" id="c333" class="btn dark-pink-btn" data-dismiss="modal">{{ __('step1.Cancel') }}</button>
		<span class="confirm-footer">
        </span>
		
		</div>	
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div -->	
	<!-- bank_model-->
    <div class="modal fade modal-confirm" id="bank_model">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title">{{$candidate_name}},</h5>
		<div class="header-caption">
		  <p>{{ __('csa.success') }}.</p>	
		</div>		
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal">{{ __('nomination.ok') }}</button>
          <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
        </div>
        
      </div>
    </div>
  </div><!-- End Of bank_model -->

  		<div class="modal fade modal-confirm" id="finalizeNomination">
		 <form name="cancel_form" id="cancel_form" method="POST"  action="{{url('/nomination/finalize-nomination-payment') }}" autocomplete='off' enctype="x-www-urlencoded">
		{{ csrf_field() }}
		<div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
		  <div class="modal-content">
		   <div class="pop-header pt-3 pb-1">
			  <div class="animte-tick"><span>&#10003;</span></div>	
			<h5 class="modal-title">{{ __('messages.fn') }}</h5> 
			<div class="header-caption">
			</div>		
			</div>
			
			<?php 
			$acnamed = app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($stndn, $acnnod);
			$stated = app(App\Http\Controllers\Nomination\NominationController::class)->getState($stndn);
			?>
			<div class="modal-body" style="text-align: center;">
			<ul>
			<li><label  id="oneqqq">{{ __('messages.fnsure') }}</li>
			 </ul> 
			</div>
			<div class="confirm-footer" id="oneqqq2">
			<input type="hidden" id="messageNeedToShow">
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;">{{ __('step1.Cancel') }}</button>
			<button type="button" class="btn btn-primary" style="background: #bb4292; border: none;" id="reareyu" onclick="return showLoaderP('<?php echo $stndn;  ?>','<?php echo $acnnod; ?>');">
			{{ __('messages.Yes') }}</button>
			</div>
			<button id="oneqqq3" type="button" class="btn btn-secondary" data-dismiss="modal" style="background:#f0587e; border: none;display:none;">{{ __('nomination.ok') }}</button>
			
			  <span style="text-align: center;display:none;" id="ppppppppppppp">
			 <img src="{{ asset('appoinment/loader.gif') }}" height="70" width="70"></img> &nbsp; {{ __('nomination.Please_Wait') }}
			</span>
		  
			
		  </div>
		</div>
		</form>
	  </div><!-- End Of confirm Modal popup Div -->
								




  
	<!-- Optional JavaScript -->
    <!-- jQuery Bootstrap JS -->
    <script src="{{ asset('appoinment/js/jQuery.min.v3.4.1.js') }} " type="text/javascript"></script>
	<?php include('appoinment/js/week-scheduale.php'); ?>
	<script src="{{ asset('appoinment/js/week-scheduale.php') }} " ></script>	
    <script src="{{ asset('appoinment/js/bootstrap.min.js') }}" type="text/javascript"></script>
	
	<script src="{{ asset('appoinment/js/jQuery.min.v3.4.1.js') }}" type="text/javascript"></script>
    <script src="{{ asset('appoinment/js/bootstrap.min.js') }}" type="text/javascript"></script>	
	<script src="{{ asset('appoinment/js/owl.carousel.js') }}"></script>  
	<script type="text/javascript" src="{{ asset('admintheme/js/jquery-ui.js') }}"></script>
	 <script type="text/javascript">  
		
		function moveLoader(st, ac){     
						var fnp = jQuery.noConflict();	
						fnp("#L333").show();
						fnp("#L123").show();
						
		
						
						$.ajax({
						type: "POST",
						url: "<?php echo url('/'); ?>/nomination/finalize-nomination-payment", 
						data: {
							"_token": "{{ csrf_token() }}",
							"st_code": st,
							"pc_no": ac,
							},
						dataType: "html",
						success: function(msg){         
						if(msg==1){	  
							fnp('#txtchalan').html('<?php echo __('step1.finalizedMessage'); ?>');	
							fnp('#txtchalan1').html('<?php echo __('step1.finalizedMessage'); ?>');	
							fnp('#txtchalan3').html('<?php echo __('step1.finalizedMessage'); ?>');	
							fnp('#c123').html('<?php echo __('nomination.ok'); ?>');	
							fnp('#c333').html('<?php echo __('nomination.ok'); ?>');	
							fnp('#challanMessage').html('');	 
							fnp('#challanMessage1').html('');	 
							fnp('#challanMessage3').html('');	
							fnp("#L123").hide();	
							fnp("#L333").hide();	
							fnp('#txt_final').hide();	
							fnp('#txt_final1').hide();	
							fnp('#txt_final3').hide();	
							fnp('#online_pay2').hide();	
							fnp('#ttump').hide();	
							fnp('#NeedToShowSucceess').show();	
						} else {
							fnp('#challanMessage').html('<?php echo __('step1.someissue'); ?>');	
							fnp('#challanMessage1').html('<?php echo __('step1.someissue'); ?>');	
							fnp('#challanMessage3').html('<?php echo __('step1.someissue'); ?>');	
							fnp('#c123').html('<?php echo __('nomination.ok'); ?>');	
							fnp('#c333').html('<?php echo __('nomination.ok'); ?>');	
							fnp("#L333").hide();	
							fnp("#L123").hide();	
							fnp('#txtchalan').html('');	
							fnp('#txtchalan1').html('');	
							fnp('#txtchalan3').hide();	
							fnp('#txt_final').hide();	
							fnp('#txt_final1').hide();	
							fnp('#txt_final3').hide();	
						}	
						 
						},
						error: function(error){
							fnp("#fnp").hide();
							console.log(error);
							console.log(error.responseText);				
							var obj =  $.parseJSON(error.responseText);
						}
					  });
		}
		
		
		function showLoaderP(st, ac){  
						var fnp = jQuery.noConflict();	
						fnp("#fnp").show();
						fnp("#ppppppppppppp").show();
						$.ajax({
						type: "POST",
						url: "<?php echo url('/'); ?>/nomination/finalize-nomination-payment", 
						data: {
							"_token": "{{ csrf_token() }}",
							"st_code": st,
							"pc_no": ac,
							},
						dataType: "html",
						success: function(msg){ 
						if(msg==1){	
							var messageNeedToShow=fnp("#messageNeedToShow").val(); 
							fnp('#challan_model').modal('hide');
							fnp("#ppppppppppppp").hide();
							fnp("#fnp").hide();
							fnp("#oneqqq3").show();
							fnp("#online_pay3").show();
							fnp("#NeedToShowSucceess").show();
							fnp("#oneqqq2").hide();
							fnp("#ttump").hide();
							fnp("#online_pay2").hide();
							fnp("#oneqqq").html("<?php echo __('messages.nmf'); ?>"); 
							fnp("#"+messageNeedToShow).html("<?php echo __('messages.nmf'); ?>");
							
						} else {
							fnp('#challan_model').modal('hide');
							fnp("#ppppppppppppp").hide();
							fnp("#fnp").hide();
							fnp("#oneqqq").html("<?php echo __('messages.delIssue'); ?>"); 
							fnp("#oneqqq3").show();
							fnp("#oneqqq2").hide();
						}	
						 
						},
						error: function(error){
							fnp("#ppppppppppppp").hide();
							fnp("#fnp").hide();
							console.log(error);
							console.log(error.responseText);				
							var obj =  $.parseJSON(error.responseText);
						}
					  });
		}
	 
		function showHidePay(nu){ 
			if(nu==1){
				var pay = jQuery.noConflict();	
						  pay("#submit_challan").show();
						  pay("#paySec").hide();
						  pay("#payGuj").hide();
			}
			if(nu==2){
				var pay = jQuery.noConflict();	
						  pay("#submit_challan").hide();
						  pay("#paySec").show();
						  pay("#payGuj").hide();
			}
			if(nu==3){
				var pay = jQuery.noConflict();	
						  pay("#submit_challan").hide();
						  pay("#paySec").hide();
						  pay("#payGuj").show();
			}
			
		}
	 
	 	
		var j = jQuery.noConflict();	
	   j(document).ready(function() {
              var owl = j('.owl-carousel');
              owl.owlCarousel({
                margin: 2,
                nav: true,
                loop: true,
                responsive: {
                  0: {
                    items: 1
                  },
                  600: {
                    items: 1
                  },
                  1000: {
                    items: 1
                  }
                }
              });
		  }); 
		
		
		<?php if(session('nopdf')!==null){ 
		if(session('nopdf') == 'nopdf'){ ?>
		var sd = jQuery.noConflict();	
		sd('#nopdf_model').modal('show');
		<?php } } ?> 
		
		<?php if(session('challan_message')!==null){ 
		if(session('challan_message') == 'challan_message'){ ?>
		var sd = jQuery.noConflict();	
		sd('#challan_model').modal('show');
		<?php } } ?> 	
		
		<?php if(session('pay_by_cash_message')!==null){ 
		if(session('pay_by_cash_message') == 'pay_by_cash_message'){ ?>
		var sd = jQuery.noConflict();	
		sd('#pay_by_cash_message').modal('show');
		<?php } } ?> 	
		
		
		 var dt = jQuery.noConflict();	
		 dt( function() {
			dt( "#challan_date" ).datepicker({
				dateFormat: 'dd-mm-yy',
                maxDate: 0,
                changeYear: true
			});
			  
		  } );
		  
		  
		
		
		 challan_date.max = new Date().toISOString().split("T")[0];
		 
		    var zz = jQuery.noConflict();	
		 zz(document).ready(function(){ 
		 zz('#challan_receipt').bind('change', function() {
					var a=(this.files[0].size);
					//alert(a);
					if(a > 4000000) {
						zz("#preError").html("<?php echo __('messages.fsize'); ?>");
						zz("#challan_receipt").val("");
						zz("#challan_receipt").focus();
						zz('#previewId').hide();
						return false;
					};
				});
			});	
		 
		 $(document).ready(function(){
          $("#challan_receipt").change(function(){
            var pre = jQuery.noConflict();	
			var challan_receipt= pre("#challan_receipt").val();
			var file_type = challan_receipt.substr(challan_receipt.lastIndexOf('.')).toLowerCase();
			
			if (file_type  != '.pdf' && file_type  != '.jpg' && file_type  != '.JPEG' && file_type  != '.PNG' && file_type  != '.png') {
					pre("#preError").html("<?php echo __('messages.chalaanPDF'); ?>");
					pre("#challan_receipt").val("");
					pre("#challan_receipt").focus();
					pre('#previewId').hide();
					return false;
			}
			
			
				
				
			pre("#preError").html("");
		    var file =document.getElementById("challan_receipt").files[0];
		    var url =URL.createObjectURL(file );
		    pre('#previewId').show();
		    pre('#iframeid').attr('src',url);
        });
		});
		
		function payByCashSubmit(){
			var aaaa = jQuery.noConflict();
			aaaa("#payByCash").val('payByCash');
		}	
		 
		  
		function submitChallan(){
			var f = jQuery.noConflict();
			
			
			
			var challan_number = f("#challan_number").val();
			var challan_date =   f("#challan_date").val();
			var challan_receipt= f("#challan_receipt").val();
			var file_type = challan_receipt.substr(challan_receipt.lastIndexOf('.')).toLowerCase();
			
			
			
			
			if(challan_number=='' || challan_number.length<3){ 
				alert("<?php echo __('messages.correctChalan'); ?>");
				f("#challan_number").focus();
				return false;
			}
			
			if(challan_date=='' ){
				alert("<?php echo __('messages.challanDateError'); ?>");
				f("#challan_date").focus();
				return false;
			}
			
			if (file_type  != '.pdf' && file_type  != '.jpg' && file_type  != '.JPEG' && file_type  != '.PNG' && file_type  != '.png') {
					f("#preError").html("<?php echo __('messages.chalaanPDF'); ?>");
					f("#challan_receipt").val("");
					f("#challan_receipt").focus();
					f('#previewId').hide();
					return false;
			}
			
			f("#afhbdsfbdjg").show();
		}	
		  
		  
		 
		 function redirect_gujrat(){
			var j = jQuery.noConflict();	
			<?php if($isPaymentConfig=='GUJ'){ ?>
			$.ajax({
				type: "POST",
				url: "<?php echo url('/'); ?>/nomination/save-payment-details-gujrat_our_end", 
				data: {
					"_token": "{{ csrf_token() }}",
					"sId": "{{$Guj_state}}",
					"pc":  "{{$Guj_PC}}",
					"amount1": "{{$Total_amount}}",
					"txn_amount": "{{$Total_amount}}",
					"reff_no": "{{$Transaction_id}}"
					},
				dataType: "html",
				success: function(msg){ 
				  if(msg==1){
					document.redGuj.submit();
				  } 
				},
				error: function(error){
					console.log(error);
					console.log(error.responseText);				
					var obj =  $.parseJSON(error.responseText);
				}
			  });
			<?php } ?> 
		}   
		 
		 
		 
		function redirect(){
			var j = jQuery.noConflict();	
			<?php if($isPaymentConfig=='YES'){ ?>
			$.ajax({
				type: "POST",
				url: "<?php echo url('/'); ?>/nomination/save-payment-details-our-end", 
				data: {
					"_token": "{{ csrf_token() }}",
					"sId": "{{$st_save}}",
					"dist_code": "{{$dist_code}}",
					"pc":  "{{$pc_save}}",
					"amount1": "{{$amount1}}",
					"txn_amount": "{{$txn_amount}}",
					"reff_no": "{{$reff_no}}",
					"checkSum": "{{$checkSum}}"
					},
				dataType: "html",
				success: function(msg){ 
				  if(msg==1){
					document.paymentred.submit();
				  } 
				},
				error: function(error){
					console.log(error);
					console.log(error.responseText);				
					var obj =  $.parseJSON(error.responseText);
				}
			  });
			<?php } ?> 
		}    
		
		function finalizeNomination(id){ 
			var dsd = jQuery.noConflict();	
			dsd("#messageNeedToShow").val(id);
			dsd('#finalizeNomination').modal('show');
		}
		
		function finalizeNominationTwo(id){ 
			var pppnnbb = jQuery.noConflict();	
			pppnnbb('#challan_model').modal('hide');
			pppnnbb('#finalizeNomination').modal('show');
		}
		
		
		function showPaymentDetails(){
			var j = jQuery.noConflict();	
			j('#paymentPopUp').modal('show');
		}  
		
		function red(){
		var j = jQuery.noConflict();	
		 j('#red').modal('show');	
			
		}
		  
		function showLoader(){
		 $("#loader3").show();	
		}  
		function showsc(){
		 $("#loader").show();	
		} 
		
		function showBankDetails(){
			var j = jQuery.noConflict();	
			j('#bankDetails').modal('show');
		}
		
		<?php if(session('is_payment')!==null){ 
		if(session('is_payment') == 'yes'){ ?>
		var j = jQuery.noConflict();	
		j('#payment').modal('show');
		<?php } } ?> 	
		
		<?php if(session('is_payment')!==null){ 
		if(session('is_payment') == 'no'){ ?>
		var j = jQuery.noConflict();	
		j('#nopayment').modal('show');
		<?php } } ?>
		
		<?php if(session('is_scheduled')!==null){ 
		if(session('is_scheduled') == 'yes'){ ?>
		$('#confirm').modal('show');
		<?php } } ?> 
		<?php if(session('is_scheduled')!==null){ 
		if(session('is_scheduled') == 'cancel'){ ?>
		$('#cancel').modal('show');
		<?php } } ?> 
		<?php if(session('bank')!==null){ 
		if(session('bank') == 'bank'){ ?>
		var j = jQuery.noConflict();	
		j('#bank_model').modal('show');
		<?php } } ?> 
		
		function valBank(){
			var j = jQuery.noConflict();	
			var candidate_name = j("#candidate_name").val();
			var bank_name = j("#bank_name").val();
			var account_number = j("#account_number").val();
			var confirm_account_number = j("#confirm_account_number").val();
			var ifsc_code = j("#ifsc_code").val();
			
			if(candidate_name==''){
				alert("<?php echo __('csa.cand_error'); ?>");
				j("#candidate_name").focus();
				return false; 
			}
			if(bank_name==''){ 
				alert("<?php echo __('csa.bank_error'); ?>");
				j("#bank_name").focus();
				return false; 
			}
			if(account_number==''){ 
				alert("<?php echo __('csa.accoun_error'); ?>");
				j("#account_number").focus();
				return false; 
			}
			if(confirm_account_number==''){ 
				alert("<?php echo __('csa.con_accoun_error'); ?>");
				j("#confirm_account_number").focus();
				return false; 
			}
			
			if(confirm_account_number!=account_number){ 
				alert("<?php echo __('csa.same_error'); ?>");
				j("#account_number").focus();
				return false; 
			}
			
			if(ifsc_code==''){ 
				alert("<?php echo __('csa.IFSC_Code_error'); ?>");
				j("#ifsc_code").focus();
				return false; 
			}
			
		}
		
		function showhide(){
			$("#slip").hide();
			//$("#textmsg").hide();
			$("#call").show();
			$("#scheduleButton").text("<?php echo __('messages.res'); ?>");
			$("#areyu").text("<?php echo __('messages.are_you_sure_p'); ?>"); 
			$("#reareyu").text("<?php echo __('messages.Yes'); ?>");
		}
		function showhide2(){
			$("#slip").show();
			$("#call").hide();
		}
		
		function submitScheduled(){
			
		    var datad = $("#daytime").val();
			if(datad == ''){ 
			  //alert("<?php echo __('messages.pleasesel'); ?>");	srjkgdfn
			  $('#emptySlot').modal('show');
			return false;
			}	
			//alert($("#daytime").val());
			$('#basicExampleModal2').modal('show');
			var sch = $("#daytime").val();
			var darr = sch.split('___');
			var dated = darr[1].split('***');
			
			var exacttme = darr[0];
			$("#ampm").text(exacttme);
			var exactdate = dated[1];
			
			var dates = exactdate.split('-');
			var cdd = dates[2]+'-'+dates['1']+'-'+dates['0']	
			$("#datea").text(cdd);	
	
			var myDate = new Date(dated[1]);
			var dayname = myDate.toString().split(' ')[0];
			$("#dayname").text(dayname);
			
			
			 //if(confirm("Are you sure to schedule appoinment?")==true){
			 //document.appoinment_form.submit();	
			//}
		}
		
		 
		function getval(daytime, i){ //alert(i);
			
			
		
			if ($.inArray(daytime, abc)!='-1') {
				if(abc.length>=3){	
				   var index = abc.indexOf(daytime);
					abc.splice(index, 1);
					$("#testing").val(abc);
					$("#"+i).css('background-color', 'white');
				return false;	
			}
			}	
			
			
			if(abc.length>=3){
			//alert("<?php echo __('messages.3max'); ?>");	
			 $('#maxError').modal('show');
			return false;	
			}
			
			
			var getabc = $("#testing").val();
				if ($.inArray(daytime, abc)!='-1') {
				   var index = abc.indexOf(daytime);
					abc.splice(index, 1);
					$("#testing").val(abc);
					$("#"+i).css('background-color', 'white');
				} else {
					    abc.push(daytime);
						$("#testing").val(abc);
						$("#"+i).css("opacity", 1);
						$("#"+i).css("background-color", 'yellow');
				}
			
			
			//alert(getabc);
			
			
			
			$("#daytime").val(daytime);
			var mdaat = daytime.replace(":", "");
			var mdaat = mdaat.replace("___", "");
			var mdaat = mdaat.replace("***", "");
			//$(".time-slot").css('background', '');
			//$("."+mdaat).css('background', '#f0587e');
		}

	  //Current Date-Month-Year	 
	    var today = new Date();
		var date = today.getDate(); 
		var dmy = today.getDate()+'-'+(today.getMonth()+1)+'-'+today.getFullYear(); 
		document.getElementById("dmy").innerHTML = dmy;
		 //Calendar Schedule  
		(function ($) {
		  $("#day-schedule").dayScheduleSelector({
			/*
			days: [1, 2, 3, 5, 6],
			interval: 15,
			startTime: '09:50',
			endTime: '21:06'
			*/
		  });
		  $("#day-schedule").on('selected.artsy.dayScheduleSelector', function (e, selected) {
			console.log(selected);
		  })
		  $("#day-schedule").data('artsy.dayScheduleSelector').deserialize({
			'0': [['09:30', '11:00'], ['13:00', '16:30']]
		  });
		})($);
	  
	 </script> 
  </body>
  
  
  
@endsection