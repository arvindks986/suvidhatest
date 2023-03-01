@extends('admin.layouts.theme')
@section('content')
  <?php   $blackoutDays =array(); $dataPoints = array();
          $start = new \Carbon\Carbon($sched->DT_ISS_NOM);
          $end = new \Carbon\Carbon($sched->LDT_IS_NOM);
          $days = $start->diff($end)->days;
      for($i = 0; $i <= $days; $i++)
          { 
            $start = new \Carbon\Carbon($sched->DT_ISS_NOM);
            $date = $start->addDays($i);
            echo $date."<br>";
            $blackoutDays[]  = $date->format('Y-m-j');
            $dataPoints[]  = array("y" => $i, "label" =>$date->format('Y-m-j'));
         //echo $date."<br>".$blackoutDays;
    }
     print_r($dataPoints);
     //die;
  /* $dataPoints = array(
    array("y" => 0, "label" => "Sunday"),
    array("y" => 5, "label" => "Monday"),
    array("y" => 10, "label" => "Tuesday"),
    array("y" => 15, "label" => "Wednesday"),
    array("y" => 20, "label" => "Thursday"),
    array("y" => 25, "label" => "Friday"),
    
  ); */
 ?> 
 <div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
    <div class="intra-table">
    <div class="row"> 
    {{print_r($sched)}}
       <!-- Start rcrd-list div -->   
     <div class="rcrd-list">
       <div class="rcrd">
         <h4>Total State</h4>
         <div><span>0</span></div>
        </div><!-- End Of rcrd div -->
         <div class="rcrd">
         <h4>Total Phase</h4>
         <div><span>0</span></div>
        </div><!-- End Of rcrd div -->
        <div class="rcrd">
         <h4>Total Constituency </h4>
         <div><span>0</span></div>
        </div><!-- End Of rcrd div -->
 
        <div class="rcrd">
         <h4>Assigned Const.</h4>
         <div><span>0</span></div>
        </div><!-- End Of rcrd div -->
        <div class="rcrd">
         <h4>Unassigned Const. </h4>
         <div><span>0</span></div>
        </div><!-- End Of rcrd div -->
         
       
    </div>
      </div><!-- End Of row-->
      <br>
      <div class="row"> 
      <div id="chartContainer" style="height: 370px; width: 100%;"></div>
      </div><!-- End Of row-->
    
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
    </div>      
  </div><!-- End Of parent-wrap Div -->
  </div> 
  

@endsection
<script src="{{ asset('admintheme/js/canvasjs.min.js')}}" type="text/JavaScript"></script>

<!--<script> <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
  title: {
    text: "Candidate Nomination Graph"
  },
  axisY: {
    title: "Number of Candidate"
  },
  data: [{
    type: "line",
    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
  }]
});
chart.render();
 
}
</script>-->
<script>
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
  animationEnabled: true,
  title:{
    text: "Company Revenue by Year"
  },
  axisY: {
    title: "Revenue in USD",
    valueFormatString: "#0,,.",
    suffix: "mn",
    prefix: "$"
  },
  data: [{
    type: "spline",
    markerSize: 5,
    xValueFormatString: "YYYY",
    yValueFormatString: "$#,##0.##",
    xValueType: "dateTime",
    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
  }]
});
 
chart.render();
 
}
</script>
  