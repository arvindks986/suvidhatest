@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content')
@php
  $st_code=!empty($st_code) ? $st_code : '0';
  $cons_no=!empty($cons_no) ? $cons_no : '0';
  $st=getstatebystatecode($st_code);
  $distname=getdistrictbydistrictno($st_code,$user_data->dist_no);
  $pcdetails=getpcbypcno($st_code, $cons_no); 
  $pcName=!empty($pcdetails->PC_NAME) ? $pcdetails->PC_NAME : 'ALL';
  $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
  //echo $st_code.'cons_no=>'.$cons_no;
  // Get the full URL for the previous request...
 $routesegment=array_slice(explode('/', url()->previous()), -1, 1);

if($routesegment[0]=='mis-officer') {
	$backurl ='eci-expenditure/mis-officer';
}elseif($routesegment[0]=='mis-atr'){
	$backurl ='eci-expenditure/mis-atr';
}else {
	$backurl ='eci-expenditure/statusExpdashboard';
}

@endphp
<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">
                <div class="col-md-5 mt-2 mb-2"><h5 class="mr-auto">Reply By CEO On Notice: {{$count}}</h5></div> 
                <div class="col-md-7 mt-2 mb-2 text-right"><p class="mb-0">
                        <b>State Name:</b> 
                        <span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
                        <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                        <b>PC:</b> <span class="badge badge-info">{{ $pcName }}</span>
                        <!--<a href="{{url('/eci-expenditure/pendingatroPDF')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
                        <a href="{{url('/eci-expenditure/noticeatceoEXL')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;-->

                        <b></b><a href="{{url('/')}}/{{$backurl}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>

                    </p></div>
            </div> <!-- end row -->
        </div>

    </section>
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-8">
                    <div class="card text-left" style="width:100%;">
                        <!--SELECT CANDIDATE-->
		<div class="card-body" id="demo" class="collapse show">  
		<div class="table-responsive">
			<table id="example1" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
         <tr>
			<th>Action</th>
			<th>State</th>
			<th>PC No & Name</th>
			<th>Candidate Name</th>
			<th>Party Name</th>
			<th>Last Date of Submission</th>
			<th>Date of Scrutiny Report Submission</th>
			<th>Date of Lodging A/C By Candidate</th>
			<th>Date of Sending Notice to the CEO</th>
			<th>Date of Sending Notice to the DEO by CEO</th>
         </tr>
        </thead>
<?php $j=0;  ?>
    @if(!empty($replybyceo))
    @foreach($replybyceo as $candDetails)  
      <?php
       //dd($candDetails);
	    $stDetails=getstatebystatecode($candDetails->ST_CODE);
       $pcDetails=getpcbypcno($candDetails->ST_CODE,$candDetails->pc_no);
       $date = new DateTime($candDetails->created_at);
       //echo $date->format('d.m.Y'); // 31.07.2012
       $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
      // dd($candDetails);
	    $last_date_prescribed_acct_lodge = !empty($candDetails->last_date_prescribed_acct_lodge) && strtotime($candDetails->last_date_prescribed_acct_lodge) > 0 ?date('d-m-Y', strtotime($candDetails->last_date_prescribed_acct_lodge)) : "N/A";
        $j++; 
        ?>
<tr>

<td>  @if(!empty($candDetails->pc_no))
                <a href="{{url('/')}}/eci-expenditure/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-75" target="_blank">Report</a> 
                @endif <a href="javascript:void(0)" class="btn btn-info btn-sm width-75"
							 onclick="atrTracking({{($candDetails->candidate_id)}})" >Tracking</a> </td>
<td>@if(!empty($stDetails->ST_NAME)) {{ $stDetails->ST_NAME}} @endif</td>
<td>@if(!empty($candDetails->pc_no)) {{ $candDetails->pc_no}} - {{ $pcDetails->PC_NAME}} @endif</td>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
<td>{{$last_date_prescribed_acct_lodge}}</td>
<td>@if(strtotime($candDetails->finalized_date)>0 && $candDetails->finalized_date !='0000-00-00') {{ date('d-m-Y',strtotime($candDetails->finalized_date))}}  @else {{ 'N/A'}} @endif</td>
<td>@if(strtotime($candDetails->date_orginal_acct)>0 && $candDetails->date_orginal_acct !='0000-00-00') {{ date('d-m-Y',strtotime($candDetails->date_orginal_acct))}} @else {{ 'N/A'}} @endif</td>
<td>@if(strtotime($candDetails->date_of_issuance_notice)>0 && $candDetails->date_of_issuance_notice !='0000-00-00') {{  date('d-m-Y',strtotime($candDetails->date_of_issuance_notice))}} @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($candDetails->date_sending_notice_service_to_deo) && ($candDetails->date_sending_notice_service_to_deo !='0000-00-00')) {{ date('d-m-Y',strtotime($candDetails->date_sending_notice_service_to_deo))}}  @else {{ 'N/A'}} @endif</td>

</tr>
@endforeach 
@endif 
<tbody></tbody>
            </table>
           			 </div>
                     </div>
                    </div>
                    <!--END OF SELECT CANDIDATE-->
                </div>
               
<!--Start Of Tracking Div-->	
<div class="col-lg-4 col-md-4 col-sm-4 menu1" style="">
	<div class="card" id="atrTracking" style="display: none;"></div>
</div>
	</div>
    </div>
  </div>
 </div>
<!--End Of Tracking Div-->
    </section>
</main>

<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<!--**********FORM VALIDATIONS SCRIPT**********-->
<script type="text/javascript">


//*******************EXTRA VALIDATION METHODS STARTS********************//
//maxsize
$.validator.addMethod('maxSize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param)
});
//minsize
$.validator.addMethod('minSize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size >= param)
});
//alphanumeric
$.validator.addMethod("alphnumericregex", function (value, element) {
    return this.optional(element) || /^[a-z0-9\._\s]+$/i.test(value);
});
//alphaonly
$.validator.addMethod("onlyalphregex", function (value, element) {
    return this.optional(element) || /^[a-z\.\s]+$/i.test(value);
});
//without space
$.validator.addMethod("noSpace", function (value, element) {
    return value.indexOf(" ") < 0 && value != "";
}, "No space please and don't leave it empty");
//*******************EXTRA VALIDATION METHODS ENDS********************//
 
</script> 
<!--**********FORM VALIDATION ENDS*************-->
<!-- graph start here manoj-->
<script type="text/javascript">
    google.charts.load('current', {'packages': ['bar']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        var id = 1;
        $.ajax({
            type: "get",
            url: "{{url('/')}}/eci-expenditure/candidateListBydataentryStartgraph/{{$st_code}}/{{$cons_no}}",
            dataType: "json",
            success: function (response) {
                var data = google.visualization.arrayToDataTable(response);
                var options = {
                    chart: {
                        title: 'Overall summary of Data entry started',                         
                    },
                    bars: 'vertical' // Required for Material Bar Charts.
                };

                var chart = new google.charts.Bar(document.getElementById('barchart'));
                chart.draw(data, google.charts.Bar.convertOptions(options));

            },
            errors: function (errors) {
                console.log(errors);
            }
        });


    }
</script>
<!--graph implementation start here-Manoj -->
 <script  src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script>
$(document).ready(function() {
    var table = $('#example1').DataTable({   
     dom: 'lBfrtip', 
     lengthMenu: [ [10, 50, 100, -1], [10, 50, 100, 'All'] ],
     pageLength: 10,
     buttons: [
            {
                extend: 'pdfHtml5',               
                pageSize: 'LEGAL',
               filename: function() {
                return 'noticeatCEO-report';    
              },
             title: function() {
                  return '<?php echo 'State Name:'.$stateName.'   PC:'.$pcName.''; ?>'
              },
            }],
           
         
      
    });
  })
  
   function atrTracking(candidate_id){
		 $('#atrTracking').css('display','block');
		var candidate_id = candidate_id;
		//alert(candidate_id);
		 $.ajax({
			url: '<?php echo url('/') ?>/eci-expenditure/getCandATRTracking/'+candidate_id,
            type: 'GET',
           // data: { _token: '{{csrf_token()}}' },
		    success: function(response){
			// Code
			var html = '';
			console.log(response);
			$('#atrTracking').html(response);
		 }
		});
	}
  </script>

@endsection