<header class="header">
    
    <nav>
      <div class="nav-header">
    <div class="container-fluid d-flex flex-md-row align-items-md-center">
    <div class="float-left mr-auto"><a href="#" class="navbar-brand "><img style="max-width: 40px;" src="{{ asset('theme/img/logo/eci-logo.png') }}" alt="" />&nbsp;<span class="text" style="color:#fff;"> Election Commission of India</span> </a></div>
     <!-- ROPC Login Section-->
      
        <ul class="float-right">
          <li class="active"><a href="{{url('/permission')}}">Dashboard</a></li>
          <!-- <li><a href="{{url('/create')}}">Apply Permission</a></li> -->
          <!-- <li><a href="{{url('/update profile')}}">Profile</a></li> -->
           <li><a href="{{url('/nomination/apply-nomination-step-1')}}">Profile</a></li>
         
          <li><a href="{{url('/candidatelogout')}}" class="nav-link logout"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
      
        </ul>
          
      </div>
     <!--  <div class="nav-bg-header">
        <div class="navbar-header"> <span></span> <span></span> <span></span> </div>
        <a href="" class="title-mobile">Election Commission of India</a>
      </div> -->
    </nav>
   </header>