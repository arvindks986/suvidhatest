<section class="breadcrumb-section">
<div class="container-fluid">
<div class="row">
  <div class="col">
    <ul id="breadcrumb" class="pt-1 mr-auto">
      <li><a href="{{url('/ropc/dashboard')}}"><span class="icon icon-home"> </span></a></li>
      <li><a href="{{url('/dashboard-nomination-new')}}"><span class="icon icon-beaker"> </span> Nomination</a></li>
      <!-- <li><span class="icon icon-double-angle-right"></span> @yield('bradcome')</li>   -->
    </ul>
	<div class="nav-header float-right welcome">
	
	<ul class="float-right">
         
          <li><a href="javascript:void(0)" >Welcome:- <b>
            @if(Session::has('Applicant_type'))
                           {{ Session::get('Applicant_type') }}
                   @else
                    0
                   @endif
          <!-- {{$users=Session::get('Applicant_type')}} -->
          </b></a></li>
        </ul>
	



</div>
  </div>
</div>
</div>
</section> 