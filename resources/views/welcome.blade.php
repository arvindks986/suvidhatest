<!DOCTYPE HTML>
      <html lang="{{ app()->getLocale() }}">
 <head>
    <?php $url=url('/'); ?> 
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-9" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="poppins" content="all,follow">
    <input type="hidden" name="base_url" id="base_url" value="<?php echo url('/'); ?>" />
    <title>Candidate & Counting  Management System</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="{{ asset('admintheme/vendor/bootstrap/css/bootstrap.min.css')}}">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="{{ asset('admintheme/vendor/font-awesome/css/font-awesome.min.css')}}">
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="{{ asset('admintheme/css/fontastic.css')}}">
    <!-- Google fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <!-- jQuery Circle-->
   <link rel="stylesheet" href="{{ asset('admintheme/css/grasp_mobile_progress_circle-1.0.0.min.css')}}">
    <!-- Custom Scrollbar-->
    <link rel="stylesheet" href="{{ asset('admintheme/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css')}}">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="{{ asset('admintheme/css/style.red.css')}}" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="{{ asset('admintheme/css/custom.css')}}">
    <!-- Favicon-->
    <link rel="shortcut icon" href="{{ asset('admintheme/img/favicon.ico')}}">
    
  <!-- Scripts -->
 <style type="text/css">
	@media (min-width: 768px) and (max-width: 991px) { .card{box-shadow:none;}}
 </style>
</head>

<body>
 <main>
   <section class="main-box">
    <div class="container-fluid h-100">
     <div class="circle peach-gradient">
	 <img src="{{ asset('admintheme/img/vendor/background.png') }}" alt="" />
     </div>
			
			 
    <div class="row justify-content-center align-items-center h-100" style="">
  
  
  <div class="col-md-6 login-page "> 
        <figure class="evm-logo officerlogin">
          <span style="margin: auto;">
           @if($url=="https://suvidha.eci.gov.in" || $url=="http://suvidha.eci.gov.in")
 
                <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo1.png') }}" alt="" />
               @else
                 <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo.png') }}" alt="" />
              @endif
                  <p>Election Commission of India </p> </span></figure> </div>
	  
	  
    <div class="col-md-6 loginDiv">
    <div class="login-right">
    
      <div class="row">		

	<div class="col">
			<div class="card">
		
			<div class="card-body">

<br />	
@if($url=="https://suvidha.eci.gov.in" || $url=="http://suvidha.eci.gov.in")		
		<p class="col-md-12 mb-0"><a class="d-flex align-items-center" href="{{url('login') }}">General Election to the House of People &nbsp;<i class=" badge-custom">(All India)</i> <small class="btn btn-danger ml-auto"><i class="fa fa-angle-right"></i></small></a></p>	
@else
    <p class="col-md-12 mb-0"><a class="d-flex align-items-center" href="{{url('/officer-login') }}">General Election to the House of People &nbsp;<i class=" badge-custom">(All India)</i> <small class="btn btn-danger ml-auto"><i class="fa fa-angle-right"></i></small></a></p>  
@endif
<br />	
	<hr />
	<br />	
	@if($url=="https://suvidha.eci.gov.in" || $url=="http://suvidha.eci.gov.in")		
		<p class="col-md-12 mb-0"><a class="d-flex align-items-center" href="{{$url}}/suvidhaac/public/login">General Elections to the Legislative Assembly <small class="btn btn-danger ml-auto"><i class="fa fa-angle-right"></i></small></a> </p>	
@else
    <p class="col-md-12 mb-0"><a class="d-flex align-items-center" href="{{$url}}/suvidhaac/public/officer-login">General Elections to the Legislative Assembly <small class="btn btn-danger ml-auto"><i class="fa fa-angle-right"></i></small></a> </p>  
@endif		
	
		
<br />	
		</div>
			</div>
</div>

	
			
			</div>
          
              
    
    </div>    
    </div>    
    </div>
        </div>
   </section>
  <input type="hidden" value="{{$_SERVER['SERVER_ADDR']}}" readonly>
  </main>
    <script src="{{ asset('admintheme/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admintheme/vendor/popper.js/umd/popper.min.js') }}"> </script>
    <script src="{{ asset('admintheme/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admintheme/js/grasp_mobile_progress_circle-1.0.0.min.js') }}"></script>
    <script src="{{ asset('admintheme/vendor/jquery.cookie/jquery.cookie.js') }}"> </script>
    <script src="{{ asset('admintheme/vendor/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('admintheme/vendor/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admintheme/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <!-- Main File-->
    <script src="{{ asset('admintheme/js/front.js') }}"></script>
    @yield('script');
  </body>
</html>
