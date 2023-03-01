@extends('admin.layouts.pc.dashboard-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Dashboard')
@section('content')


    <section class="countdownTimer">
    <div class="container-fluid">
        <div class="row">
          <div class="col mr-auto"><h1 class="mt-2 display 1">RO PC Dashboard</h1></div>
        <div class="col-md-3 mt-2 mb-2 countdown">
        <span  id="demo"></span> LEFT FOR ELECTION
        </div>
        </div>
        </div>
    </section>
     @if (session('error_mes'))
          <div class="alert alert-success"> {{session('error_mes') }}</div>
        @endif
    <?php
		$totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'4'])->get()->count();
    $totalwith= \app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'5'])->get()->count() ;
    
    $totaccepted=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'6'])->where('party_id', '!=' ,'1180')->get()->count();
    $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where('application_status','!=','11')->where('party_id', '!=' ,'1180')->get()->count();
  
        

         try {
      
      $total_pen_verification = DB::table('nomination_application')
      ->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,
      'election_id' =>$ele_details->ELECTION_ID])
      ->where('finalize', '=', '1')
      ->where('finalize_after_payment', '=','1')
      ->where('is_physical_verification_done', '=', '0')
      ->get()->count();
      $appointment_pend = DB::table('nomination_application')->join('appointment_schedule_date_time', [
      ['nomination_application.candidate_id', '=', 'appointment_schedule_date_time.candidate_id'],
      ['nomination_application.st_code', '=', 'appointment_schedule_date_time.st_code'],
      ['nomination_application.pc_no', '=', 'appointment_schedule_date_time.pc_no']])
      ->where(['nomination_application.st_code' =>$ele_details->ST_CODE,'nomination_application.pc_no' =>$ele_details->CONST_NO])
      ->where('finalize', '=', '1')
      ->where('appointment_schedule_date_time.status', '=', '1')
      ->where('finalize_after_payment', '=','1')
      ->where('appointment_schedule_date_time.is_ro_acccept', '=', '0')
      ->groupBy('nomination_application.candidate_id')->get()->count();
      $prescrutiny_url = url('/ropc/listallapplicant_prescrutiny');
      $phyical_verification = url('/ropc/listallapplicant');
    } catch (\Throwable $th) {
      $prescrutiny_url = '#';
      $appointment_tot = 0;
      $phyical_verification = '#';
      $appointment_pend = 0;
      $total_prescrutiny = 0;
      $total_pen_verification = 0;
    }













     
        $blackoutDays =""; $dataPoints = array(); $no='';
              $start = new \Carbon\Carbon($sched['DT_ISS_NOM']);
              $end = new \Carbon\Carbon($sched['LDT_IS_NOM']);
              $days = $start->diff($end)->days;
          for($i = 0; $i <= $days; $i++)
              { 
                $start = new \Carbon\Carbon($sched['DT_ISS_NOM']);
                $date = $start->addDays($i);
                //echo $date."<br>";
                if($blackoutDays!="")
                    $blackoutDays = $blackoutDays.', "'.$date->format('Y-m-j').'"';
                else
                  $blackoutDays = $blackoutDays.'"'.$date->format('Y-m-j').'"';
                  // dd($user_data);
               $tot=\app(App\adminmodel\CandidateNomination::class)->where(['date_of_submit' =>$date->format('Y-m-j')])->where(['ST_CODE'=>$user_data->st_code],['district_no'=>$user_data->dist_no])->get()->count();
              // echo $tot;
              // dd($user_data);
               if($no!="")
                    $no = $no.', '.$tot;
                else
                  $no = $no.$tot;
    
                $dataPoints[]  = array("y" => $i, "label" =>$date->format('Y-m-j'));
             //echo $date."<br>".$blackoutDays;
        }
    
        $cdate=date("Y-m-d");
    
         ?>
   
    
    <section class="statistics color-grey pt-1 pb-5" style="border-bottom:1px solid #eee;">
            <div class="container-fluid">
              <div class="row d-flex">



               
  <div class="col-md-6 pb-3">
                  <!-- Income-->
                  <div class="card income text-center mt-3">
                    <div class="d-flex justify-content-between">
                       <div class="icon"><img src="{{ asset('theme/img/icon/online-nomination-icon-002.png') }}" alt="" /></div>
                    <div><p>Applications</p><strong class="text-primary"><a href="{{ url('ropc/appointment_request') }}">Request for Appointment</a></strong></div>
                    <div class="number orange">{{ $appointment_pend }}</div>
                        
                    </div>
                  </div>
                </div>
              <div class="col-md-6 pb-3">
                  <!-- Income-->
                  <div class="card income text-center mt-3">
                    <div class="d-flex justify-content-between">
                       <div class="icon"><img src="{{ asset('theme/img/icon/online-nomination-icon-002.png') }}" alt="" /></div>
                       <div><p>Applications</p><strong class="text-primary"><a href="{{ $phyical_verification }}">Pending Physical Verification</a></strong></div>
                    <div class="number orange">{{ $total_pen_verification }}</div>
                        
                    </div>
                  </div>
                </div>
                






                <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                    <div class="icon"><img src="{{ asset('admintheme/img/icon/applied.png') }}" alt="" /></div>
                    <div class="number yellow">{{$total}}</div><p>Application<strong class="text-primary">Open</strong></p>
                    
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                      <div class="icon"><img src="{{ asset('admintheme/img/icon/verified.png') }}" alt="" /></div>
                    <div class="number green">{{$totaccepted}}</div><p>Applications<strong class="text-primary">Accepted </strong></p>
                   
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                       <div class="icon"><img src="{{ asset('admintheme/img/icon/generate.png') }}" alt="" /></div>
                    <div class="number orange">{{$totrej}}</div><p>Total Receipt<strong class="text-primary">Rejected</strong></p>
                    
                  </div>
                </div> 
          <div class="col-md-3">
                  <!-- Income-->
                  <div class="card income text-center">
                       <div class="icon"><img src="{{ asset('admintheme/img/icon/notverified.png') }}" alt="" /></div>
                    <div class="number red">{{$totalwith}}</div><p>Applications<strong class="text-primary">Withdrawn</strong></p>
                  </div>
                </div>
              </div>
            </div>
    </section>
          <!-- Counts Section -->
          <section class="dashboard-counts  section-padding">
            <div class="container-fluid">
              <div class="row">
                <!-- Count item widget-->
                <div class="col-xl-2 col-md-4 col-6">
                  <div class="wrapper count-title d-flex">
                    <div class="icon"><i class="icon-user"></i></div>
                    <div class="name"><strong class="text-uppercase">Notification Date</strong><span>@if(!empty($sched['DT_ISS_NOM'])){{date("d M Y",strtotime($sched['DT_ISS_NOM']))}}
                      @endif</span>
                     <!--  <div class="count-number">25</div> -->
                    </div>
                  </div>
                </div>
                <!-- Count item widget-->
                <div class="col-xl-2 col-md-4 col-6">
                  <div class="wrapper count-title d-flex">
                    <div class="icon"><i class="icon-padnote"></i></div>
                    <div class="name"><strong class="text-uppercase">Nomination LT DT</strong><span>@if(!empty($sched['LDT_IS_NOM'])){{date("d M Y",strtotime($sched['LDT_IS_NOM']))}}@endif</span>
                    <!--   <div class="count-number">400</div> -->
                    </div>
                  </div>
                </div>
                <!-- Count item widget-->
                <div class="col-xl-2 col-md-4 col-6">
                  <div class="wrapper count-title d-flex">
                    <div class="icon"><i class="icon-check"></i></div>
                    <div class="name"><strong class="text-uppercase">Scrutiny Date</strong><span>@if(!empty($sched['DT_SCR_NOM'])){{date("d M Y",strtotime($sched['DT_SCR_NOM']))}}@endif</span>
                      <!-- <div class="count-number">342</div> -->
                    </div>
                  </div>
                </div>
                <!-- Count item widget-->
                <div class="col-xl-2 col-md-4 col-6">
                  <div class="wrapper count-title d-flex">
                    <div class="icon"><i class="icon-bill"></i></div>
                    <div class="name"><strong class="text-uppercase">Withdrawan Date</strong><span>@if(!empty($sched['LDT_WD_CAN'])){{date("d M Y",strtotime($sched['LDT_WD_CAN']))}}@endif</span>
                     <!--  <div class="count-number">123</div> -->
                    </div>
                  </div>
                </div>
                <!-- Count item widget-->
                <div class="col-xl-2 col-md-4 col-6">
                  <div class="wrapper count-title d-flex">
                    <div class="icon"><i class="icon-list"></i></div>
                    <div class="name"><strong class="text-uppercase">Poll Date</strong><span>@if(!empty($sched['DATE_POLL'])){{date("d M Y",strtotime($sched['DATE_POLL']))}}@endif</span>
                     <!--  <div class="count-number">92</div> -->
                    </div>
                  </div>
                </div>
                <!-- Count item widget-->
                <div class="col-xl-2 col-md-4 col-6">
                  <div class="wrapper count-title d-flex">
                    <div class="icon"><i class="icon-list-1"></i></div>
                    <div class="name"><strong class="text-uppercase">Counting Date</strong><span>@if(!empty($sched['DATE_COUNT'])){{date("d M Y",strtotime($sched['DATE_COUNT']))}}@endif</span>
                      <!-- <div class="count-number">70</div> -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
         
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
  
