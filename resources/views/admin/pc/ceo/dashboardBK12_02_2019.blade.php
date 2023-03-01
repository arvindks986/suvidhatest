@extends('admin.layouts.pc.theme')
@section('content')
  <?php   $blackoutDays =""; $dataPoints = array(); $no='';
          $start = new \Carbon\Carbon($sched->DT_ISS_NOM);
          $end = new \Carbon\Carbon($sched->LDT_IS_NOM);
          $days = $start->diff($end)->days;
      for($i = 0; $i <= $days; $i++)
          { 
            $start = new \Carbon\Carbon($sched->DT_ISS_NOM);
            $date = $start->addDays($i);
            //echo $date."<br>";
            if($blackoutDays!="")
                $blackoutDays = $blackoutDays.', "'.$date->format('Y-m-j').'"';
            else
              $blackoutDays = $blackoutDays.'"'.$date->format('Y-m-j').'"';
           $tot=\app(App\adminmodel\CandidateNomination::class)->where(['date_of_submit' =>$date->format('Y-m-j')])->get()->count();
          // echo $tot;
           if($no!="")
                $no = $no.', '.$tot;
            else
              $no = $no.$tot;

            $dataPoints[]  = array("y" => $i, "label" =>$date->format('Y-m-j'));
         //echo $date."<br>".$blackoutDays;
    }
    
    $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$user_data->st_code])->get()->count();
    $totw=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$user_data->st_code])->where(['application_status' =>'5'])->get()->count();
    $totr=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$user_data->st_code])->where(['application_status' =>'4'])->get()->count();
    $totacc=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$user_data->st_code])->where(['application_status' =>'6'])->get()->count(); 
    $totfor=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$user_data->st_code])->where(['application_status' =>'2'])->get()->count();
    $tota=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$user_data->st_code])->where(['application_status' =>'1'])->get()->count();  
    $totrec=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$user_data->st_code])->where(['application_status' =>'3'])->get()->count();
    $cdate=date("Y-m-d");
     
 ?> 
 <div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
    <div class="intra-table">
    <div class="head-title">
        <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-002.png')}}" /></i>Activity Timeline</h3>
      </div>
     
      <div class="circle-report row" >
            <div class="four-circle"> 
              <div class="col-sm-2 col-xs-6" >
        <?php if($cdate>=$sched->DT_ISS_NOM) { $class="circle-contnt green-active"; } else { $class="circle-contnt"; }?>
         <div class="<?=$class;?>">
          hiiiiiiiiiiiiiiiiiiiiiiii
          <span>Notification Date</span>@if(!empty($sched->DT_ISS_NOM))<strong>{{date("d M Y",strtotime($sched->DT_ISS_NOM))}}</strong>
           @endif <span></span> 
        </div>
         </div>
         <div class="col-sm-2 col-xs-6">
         <?php if($cdate>=$sched->LDT_IS_NOM) { $class="circle-contnt green-active"; } else { $class="circle-contnt"; }?>
         <div class="<?=$class;?>">
        
          <span>Nomination LT DT</span>@if(!empty($sched->LDT_IS_NOM))<strong>{{date("d M Y",strtotime($sched->LDT_IS_NOM))}}</strong>@endif  <span></span> 
        </div>
         </div>  
        <div class="col-sm-2 col-xs-6">
        <?php if($cdate>=$sched->DT_SCR_NOM) { $class="circle-contnt green-active"; } else { $class="circle-contnt"; }?>
         <div class="<?=$class;?>">
          <span>Scrutiny Date</span> @if(!empty($sched->DT_SCR_NOM))<strong>{{date("d M Y",strtotime($sched->DT_SCR_NOM))}}</strong>@endif <span></span> 
        </div>
         </div> 
         <div class="col-sm-2 col-xs-6">
         <?php if($cdate>=$sched->LDT_WD_CAN) { $class="circle-contnt green-active"; } else { $class="circle-contnt"; }?>
         <div class="<?=$class;?>">
          <span>Withdrawan Date</span>@if(!empty($sched->LDT_WD_CAN))<strong>{{date("d M Y",strtotime($sched->LDT_WD_CAN))}}</strong>@endif <span></span> 
        </div>
         </div>  
         <div class="col-sm-2 col-xs-6">
       <?php if($cdate>=$sched->DATE_POLL) { $class="circle-contnt green-active"; } else { $class="circle-contnt"; }?>
         <div class="<?=$class;?>">
          <span>Poll Date</span>@if(!empty($sched->DATE_POLL))<strong>{{date("d M Y",strtotime($sched->DATE_POLL))}}</strong>@endif <span></span> 
        </div>
         </div>   
         <div class="col-sm-2 col-xs-6">
        <?php if($cdate>=$sched->DATE_COUNT) { $class="circle-contnt green-active"; } else { $class="circle-contnt"; }?>
         <div class="<?=$class;?>">
          <span>Counting Date</span>@if(!empty($sched->DATE_COUNT))<strong>{{date("d M Y",strtotime($sched->DATE_COUNT))}}</strong>@endif <span></span></div>

         </div> 
            </div> 
           </div>
        <br>
     <!--<div class="row"> 
    
     <div class="rcrd-list">
       <div class="rcrd">
         <h4>St. Date Nomination</h4>
         <span>@if(!empty($sched->DT_ISS_NOM)){{date("d-m-Y",strtotime($sched->DT_ISS_NOM))}} @endif</span>
        </div><!-- End Of rcrd div -->

       <!-- <div class="rcrd">
         <h4> Lt. Date Nomination</h4>
         <span>@if(!empty($sched->LDT_IS_NOM)){{date("d-m-Y",strtotime($sched->LDT_IS_NOM))}}@endif</span>
        </div><!-- End Of rcrd div -->

       <!-- <div class="rcrd">
         <h4>Date Of  Scrutiny </h4>
         <span>@if(!empty($sched->DT_SCR_NOM)){{date("d-m-Y",strtotime($sched->DT_SCR_NOM))}}@endif</span>
        </div><!-- End Of rcrd div -->
       <!-- <div class="rcrd">
         <h4>Date of Withdrawal </h4>
         <span>@if(!empty($sched->LDT_WD_CAN)){{date("d-m-Y",strtotime($sched->LDT_WD_CAN))}}@endif</span>
        </div><!-- End Of rcrd div -->
       <!-- <div class="rcrd">
         <h4>Date Of Poll </h4>
          <span>@if(!empty($sched->DATE_POLL)){{date("d-m-Y",strtotime($sched->DATE_POLL))}}@endif</span> 
        </div><!-- End Of rcrd div -->
         
       <!-- </div><!-- End Of rcrd-list Div -->
    
     <!-- </div><!-- End Of row -->
      
      <div class="row"> 
      <div class="col-sm-8" style="text-align:center; ">
        <h2 style="text-align:center;">Candidate Datewise Nomination</h2>
          <canvas id="graph" width="600" height="300" align="left"></canvas> 
        <p style="text-align:center;">Nomination Date</p>
        </div>
        <div class="col-sm-4" style="text-align:center; ">
           <h3>Nomination Information</h3><br>
        <div class="col-sm-10" style="text-align:left; ">
          <h4>Total  :</h4>
        </div><div class="col-sm-2"><h4>{{$total}}</h4></div>
        <div class="col-sm-10" style="text-align:left; ">
          <h4>Total Applied :</h4>
        </div><div class="col-sm-2"><h4> {{$tota}}</h4></div>
         <div class="col-sm-10" style="text-align:left; ">
          <h4>Total Verified :</h4>
        </div><div class="col-sm-2"><h4> {{$totfor}}</h4></div>
         <div class="col-sm-10" style="text-align:left; ">
          <h4>Total Receipt Print :</h4>
        </div><div class="col-sm-2"><h4> {{$totrec}}</h4></div>
         <div class="col-sm-10" style="text-align:left; ">
          <h4>Total  Withdrawn : </h4>
        </div><div class="col-sm-2"><h4>{{$totw}}</h4></div>
         <div class="col-sm-10" style="text-align:left; ">
          <h4>Total  Rejected : </h4>
        </div><div class="col-sm-2"><h4>{{$totr}}</h4></div>
         <div class="col-sm-10" style="text-align:left; ">
          <h4>Total  Accepted : </h4>
        </div><div class="col-sm-2"><h4>{{$totacc}}</h4></div>
         
        </div>
       
      </div><!-- End Of row-->
      <br>
    <div class="row">
      <div class="col-lg-9 col-md-9 col-sm-9">
       <form name="frmstatus" id="frmstatus" method="get"  action="{{url('ceo/dashboard') }}" > 
          <div class="table-responsive">
           <table>
            <tr><th>Status:- </th><th width="150">           
            <select name="cand_status" id="cand_status"  style="width:150px" onchange="filterdata(this.value);">
              <option value="" @if($status=='') selected="selected" @endif>All</option>  
               @foreach($status_list as $s)   
              <option value="{{$s->id}}" @if($status==$s->id) selected="selected" @endif >{{$s->status}}</option>
               
              @endforeach
            </select></th>
          <!--<th>&nbsp;&nbsp;&nbsp;&nbsp; Districts :- </th><th width="120">           
            <select name="districts " id="districts " onchange="this.form.submit()" style="width:120px">
              <option value="" >All</option>  
                onchange="this.form.submit()"
            </select></th>-->
            <th>&nbsp;&nbsp;&nbsp;&nbsp; Constituency :- </th><th width="150">            
            <select name="constituency" id="constituency"  style="width:150px" onchange="filterdata(this.value);">
              <option value="" selected="" >All</option>  
            @if(isset($ac))  
            @foreach($ac as $a)
          <?Php if($a->CONST_TYPE=='AC') {
            $const=\app(App\adminmodel\AcMaster::class)->where(['ST_CODE' =>$a->ST_CODE])->where(['AC_NO' =>$a->CONST_NO])->first();  
            $const_name=$const->AC_NAME;
            }
            if($a->CONST_TYPE=='PC') {
              $const=\app(App\adminmodel\AcMaster::class)->where(['ST_CODE' =>$a->ST_CODE])->where(['PC_NO' =>$a->CONST_NO])->first();
              $const_name=$const->PC_NAME;
            } 
                ?>
                <option value="{{$a->CONST_NO}}" >{{ $a->CONST_NO}}-{{$const_name}}</option> 
            @endforeach
            @endif  
            </select></th>

          </tr>      
          </table>
        </div>
          </form> 
      </div> 
       
    <div class="col-lg-3 col-md-3 col-sm-3">
                  <!--SEARCH FORM STARTS HEREonchange="filterdata(this.value);">-->
      <form name="frmsearch" id="frmsearch" method="get"  action="" onsubmit="return filterdata(this.value);"> 
         
       <div class="search-area">
      <div class="search-box">   
       <input type="text" placeholder="Enter QR Code" name="search" id="search">
       <button type="submit"><i class="glyphicon glyphicon-search"></i></button>    
        </div><!-- End Of search-box Div -->      
     </div>
   </form>
    </div>
    </div>  
    <br>
    <?php $i=0; ?>
         <div class="wrap-main">
        <div class="sub-scroll">   
           <div class="table-responsive">
           <table class="table table-bordered table-striped table-hover">
           <thead>
            <tr> <th>QR Code</th><th>Cand Name</th><th>Constituency Name</th><th>Status</th> </tr>
           </thead>
          <tbody id="oneTimetab"> 
      @foreach($list1 as $list)
<?php   
  if(!empty($list->ac_no) and empty($list->pc_no)) { 
     $const=\app(App\adminmodel\AcMaster::class)->where(['ST_CODE' =>$list->st_code])->where(['AC_NO' =>$list->ac_no])->first();
       
     $const_name=$const['AC_NAME'];
   }
  elseif(!empty($list->pc_no) and empty($list->ac_no)) {
    $const=\app(App\adminmodel\PCMaster::class)->where(['ST_CODE' =>$list->st_code])->where(['PC_NO' =>$list->pc_no])->first();
    $const_name=$const['PC_NAME'];  
  } 
  $s= \app(App\commonModel::class)->getnameBystatusid($list->application_status);
  ?> 
  <tr><td>{{$list->qrcode}} </td><td>{{$list->cand_name}} </td> <td>{{$const_name}}</td>
    <td>{{ucfirst($s)}}</td> </tr>
      @endforeach

            
          </tbody>   
           </table>
         </div>
        </div><!-- End Of sub-scroll Div -->   
         </div><!-- End Of wrap-main Div -->

      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
    </div>      
  </div><!-- End Of parent-wrap Div -->
  </div> 
  

@endsection
<script src="{{ asset('admintheme/js/jquery-1.11.2.min.js')}}"></script> 
<script src="{{ asset('admintheme/js/topup.js')}}" type="text/JavaScript"></script>
 <link href="{{ asset('admintheme/css/jquerysctipttop.css')}}" rel="stylesheet" type="text/css">
<script>    
    $( document ).ready(function() {
      var chartData = {
        node: "graph",
        dataset: [<?=$no;?>],
        labels: [<?=$blackoutDays;?>],
        pathcolor: "#288ed4",
        fillcolor: "#8e8e8e",
        xPadding: 0,
        yPadding: 0,
        ybreakperiod:2
      };
      drawlineChart(chartData);
    });
  
  </script>
   