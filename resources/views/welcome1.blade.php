@extends('admin.layouts.login')

@section('content')
<?php  $url = URL::to("/");  ?>
 
<main>
   <section class="main-box">
     <div class="circle peach-gradient">
            <img src="{{ asset('admintheme/img/vendor/background.png') }}" alt=""></div>
    <div class="container-fluid h-100">
   
         
         
    <div class="row justify-content-center align-items-center h-100" style="width:100%; margin:0 auto;">
  
  <div class="col-md-6 login-page "> 
        <figure class="evm-logo officerlogin">
          <span style="margin: auto;">@if($url=="https://suvidha.eci.gov.in" || $url=="http://suvidha.eci.gov.in")
 
                <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo1.png') }}" alt="" />
               @else
                 <img class="logoSize" src="{{ asset('theme/img/logo/eci-logo.png') }}" alt="" />
              @endif
                  <p>Election Commission of India </p> </span></figure> </div>
	
	
	
  
    <div class="col-md-6 loginDiv">
    <div class="login-right">
   
   <fieldset>
   <legend class="text-center mb-2"> 
   
 
   <div class=" btn-group main-nav">
          <input type="button" class="btn btn-link" onclick="location.href = '{{$url}}';" value="Home"/> 
          <input type="button" class="btn btn-link" onclick="location.href = '{{$url}}/login';" value="Candidate Login"/> 
          <input type="button" class="btn btn-link active"  value="Officer Login"/>
		  
        </div>
 

        
		</legend>
  <legend class="text-center">Login For Officer</legend>
       <!--    <h3 class="display 1">Officer Login</h3>   -->
               
 <form class="log-frm-area" method="POST" action="{{ url('/admin-postlogin') }}" autocomplete='off' enctype="x-www-urlencoded">
                        {{ csrf_field() }}
    @if (session('data_username'))
        <div class="alert alert-danger"> {{session('data_username') }}</div>
    @endif
    <span class="help-block"> 
        <strong>{{ Session::get('log_message') }}</strong>
    </span>
@if(Session::has('flash-message'))
      @if(Session::has('status'))
        <?php
        $status = Session::get('status');
        if($status==1){
          $class = 'alert-success';
        }
        else{
          $class = 'alert-danger';
        }
        ?>
      @endif
      <div class="alert <?php echo $class; ?>">
        {{ Session::get('flash-message') }}
      </div>
    @endif
    <div class="form-group">
              <input type="radio" name="database" value="1" >  <span id=""> Current Election  ( BYE) </span>
              <input type="radio" name="database" value="0" checked="checked"> <span id=""> Previous Election </span>
      </div>
      <div class="form-group">
        <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{old('username')}}"  autofocus placeholder="User Name"  autocomplete="off" >

        @if ($errors->has('username'))
          <span class="invalid-feedback"><strong>{{ $errors->first('username') }}</strong>
                      </span>
        @endif 
      </div>
      <div class="form-group"> 
            <input id="password" type="password" class="form-control{{$errors->has('password') ? ' is-invalid' : '' }}" name="password"  placeholder="Password" autocomplete="new-password" autocomplete="off" >

           @if ($errors->has('password'))
              <span class="invalid-feedback">
                  <strong>{{ $errors->first('password') }}</strong>
              </span>
           @endif
          </div>
     
	 
	 <div class="form-group  d-flex flex-column flex-md-row align-items-center mb-3">
                    <div class="col col-xs-12 m-0 p-0"> 
						<div class="captcha">
								<span id="captcha"><img id="refresh" src="{{ captcha_src() }}" alt="captcha" class="captcha-img" data-refresh-config="default"></span>
                    <button type="button" data-refresh-config="default" id="btn-refresh" class="btn btn-success btn-refresh captcha-img refresh"><i class="fa fa-refresh"></i> Refresh</button>
								  
								    
						</div>
					</div>
						 
                        <div class="col col-xs-12 pr-0 d-flex align-items-center capchtainpyt">  	  <input id="lcaptcha" type="text" class="form-control{{$errors->has('lcaptcha') ? ' is-invalid' : '' }}" name="lcaptcha"  placeholder="captcha"   autocomplete="off"/>&nbsp;
						 <input type="submit" class="btn btn-primary" value="Login"/></div>
						                      
                  </div>
	 		 <div class="row">
					
						  
						 </div>
   <div class="row">
   	 <div class="col">
						  @if ($errors->has('lcaptcha'))
              <span class="invalid-feedback">
                  <strong>{{ $errors->first('lcaptcha') }}</strong>
              </span>
           @endif
						 </div>
                  <div class="col form-group"> 
                    <small><a href="{!! url('/forgot') !!}" class="pull-right">Forgot Password</a></small>
                  </div>
                </div>
	 
 </form>
               
               </fieldset>
    
    </div>    
    </div>    
    </div>
        </div>
   </section>
   <!--  <footer class="main-footer">
        <div class="container-fluid">
          <div class="row">
      <div class="col"></div>
            <div class="col">
              <figure class="foot-lft"><img src="{{ asset('admintheme/img/vendor/footer-img.png')}}"></figure>
            </div>
            <div class="col text-right">
      
       
       <nav>
       <a href="#">Privacy Policy</a> &nbsp; | &nbsp; 
       <a href="#">Term &amp; Conditions</a> &nbsp; | &nbsp;   
       <a href="#">About ECI</a>
       </nav>
       
      
              <div class="copyright small">Copyright @2019  Election Commission of India. All rights reserved.</div>
               
            </div>
          </div>
        </div>
      </footer>  -->
  </main>
@endsection
@section('script')
<script>

 /*function refereshcaptch(){    
    jQuery.ajax({
                  type:'GET',
                  url: APP_URL+"/refresh_captcha",           
       success: function (data) {
         jQuery("#captcha").html(data.captcha);
       },
       error: function (data, textStatus, errorThrown) {
             //do something

       }
           });
    }*/

   $('.captcha-img').on('click', function () {
   var captcha = $(this);
   var config = captcha.data('refresh-config');
   $.ajax({
       method: 'GET',
       url: APP_URL +'/get_captcha/' + config,
   }).done(function (response) {
       $('#refresh').prop('src', response);
   });
}); 
</script>
@endsection