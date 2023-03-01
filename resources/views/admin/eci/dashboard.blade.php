@extends('admin.layouts.theme')
@section('content')
  <?php    $total=\app(App\adminmodel\CandidateNomination::class)->get()->count();
            $totw=\app(App\adminmodel\CandidateNomination::class)->where(['application_status' =>'withdrawn'])->get()->count();
            $totr=\app(App\adminmodel\CandidateNomination::class)->where(['application_status' =>'rejected'])->get()->count();
            $totacc=\app(App\adminmodel\CandidateNomination::class)->where(['application_status' =>'accepted'])->get()->count(); 
            $totfor=\app(App\adminmodel\CandidateNomination::class)->where(['application_status' =>'formsubmited'])->orwhere(['application_status' =>'applied'])->orwhere(['application_status' =>'verified'])->orwhere(['application_status' =>'receipt_generated'])->get()->count(); 
     ?>
   
 <div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
    <div class="row"> 
     <div class="col-lg-12 col-md-12 col-sm-12">    
       <!-- Start rcrd-list div -->   
     <div class="rcrd-list">
       <div class="rcrd">
         <h4>Total Applied</h4>
         <div><span>{{$total}}</span></div>
        </div><!-- End Of rcrd div -->

        <div class="rcrd">
         <h4>Total Submitted </h4>
         <div><span>{{$totfor}}</span></div>
        </div><!-- End Of rcrd div -->

        <div class="rcrd">
         <h4>Total Withdrawn</h4>
         <div><span>{{$totw}}</span></div>
        </div><!-- End Of rcrd div -->
        <div class="rcrd">
         <h4>Total Rejected </h4>
         <div><span>{{$totr}}</span></div>
        </div><!-- End Of rcrd div -->
        <div class="rcrd">
         <h4>Total Contestant </h4>
         <div><span>{{$totacc}}</span></div>
        </div><!-- End Of rcrd div -->
         
        </div><!-- End Of rcrd-list Div -->
    </div>
      </div><!-- End Of child-area Div -->
      <br>
     <!--   <div class="row"> 
     <div class="col-lg-12 col-md-12 col-sm-12">    
       <!-- Start rcrd-list div -->   
     <!--  <div class="rcrd-list">
       <div class="rcrd">
         <h4>Affidavit Upload</h4>
         <div><span>20</span></div>
        </div><!-- End Of rcrd div -->
       <!--   <div class="rcrd">
         <h4>Counter Affidavit</h4>
         <div><span>2</span></div>
        </div><!-- End Of rcrd div -->
       <!--   <div class="rcrd">
         <h4>Duplicate Parties</h4>
         <div><span>5</span></div>
        </div><!-- End Of rcrd div -->
        <!--  <div class="rcrd">
         <h4>EPIC % Records</h4>
         <div><span>13</span></div>
        </div><!-- End Of rcrd div -->
        
       <!--  </div><!-- End Of rcrd-list Div -->
    <!--  </div>
      </div><!-- End Of child-area Div -->
    <div class="row">   
     <div class="col-lg-7 col-md-7 col-sm-7">
       <div class="box-area">
        <!-- Start Of Machine Table Div --> 
           <div class="machine-table">
         
        </div><!-- End Of machine-table Div --> 
     </div><!-- End Of box-area Div -->
     </div>
      <div class="col-lg-5 col-md-5 col-sm-5">
      <div class="box-area">
              <!-- Start Of Poll Status Div --> 
       <div class="poll-status">
         
        </div><!-- End Of poll-status Div --> 
      </div><!-- End Of box-area Div -->
     </div>    
      </div>
    </div>      
  </div><!-- End Of parent-wrap Div -->
  </div> 
  

@endsection
  <script type = "text/javascript">  
     window.onload = function () {  
         document.onkeydown = function (e) {  
              return (e.which || e.keyCode) != 116;  
          };  
      }  
 </script>  