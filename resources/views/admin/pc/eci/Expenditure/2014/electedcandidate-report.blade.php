@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content') 
@php
  $pc = !empty($_GET['pc'])?$_GET['pc']:""; 
  $st_code=!empty($st_code) ? $st_code : '0';
  $cons_no=!empty($cons_no) ? $cons_no : '0';
  $st=getstatebystatecode($st_code);
  $distname=getdistrictbydistrictno($st_code,$user_data->dist_no);
  $pcdetails=getpcbypcno($st_code, $cons_no); 
  $pcName=!empty($pcdetails->PC_NAME) ? $pcdetails->PC_NAME : 'ALL';
  $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
// echo $st_code.'cons_no=>'.$cons_no; 
@endphp
<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">
                <div class="col"><h2 class="mr-auto">Elected Candidate List</h2></div> 
                <div class="col"><p class="mb-0 text-right">
                        <b>State Name:</b> 
                        <span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
                        <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                        <b>PC:</b> <span class="badge badge-info">{{ $pcName}}</span>
              <span class="badge badge-info"></span>&nbsp;&nbsp;
            <!--  <a href="{{url('/eci-expenditure/EciOfficerReportPDF')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a>-->
			&nbsp;&nbsp;
			   <a href="{{url('/eci-expenditure/electedcand2014')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}?pc={{$pc}}&state={{$st_code}}&exl=yes" class="btn btn-info" role="button">State Wise Excel Report </a> &nbsp;&nbsp;
                        <b></b> <a href="{{url('/eci-expenditure/mis-officer2014')}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>

                    </p></div>
            </div> <!-- end row -->
			<p class="mb-0 text-right"><a href="{{url('/eci-expenditure/electedcand2014')}}/{{base64_encode('0')}}/{{base64_encode('0')}}?pc={{'0'}}&state={{'0'}}&exl=yes" class="btn btn-info" role="button">All India Excel Report </a></p>
        </div>
    </section>
<section class="mt-5">
<div class="container-fluid">
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12">
<div class="card text-left" style="width:100%;">
<!--SELECT CANDIDATE-->
<div class="card-body" id="demo" class="collapse show">  
<table id="examplereturn" class="table table-striped table-bordered table-hover" style="width:100%">
	<thead>
		<tr>
		   <th>State</th>
			<th>PC No & Name</th>
			<th>Candidate Name</th>
			<th>Party Name</th>
			<th>Lodging Date</th>
			<th>Total Received Fund</th>
			<th>Total Expenditure <br /> Declared By Candidate</th>

		</tr>
	</thead>
	<?php $j = 0; ?>
	@if(!empty($electedCandList))
	@foreach($electedCandList as $candDetails)  
	<?php
	//dd($candDetails);
	$pc = getpcbypcno($candDetails->st_code, $candDetails->pc_no);
	$j++;
	$stDetails=getstatebystatecode($candDetails->st_code);
	$totalexpen= !empty($candDetails->candidate_total_expense) ? $candDetails->candidate_total_expense : '0';

	 $candreceieved= !empty($candDetails->source_amount) ? $candDetails->source_amount : 0;
?>
<tr>
<td>@if(!empty($stDetails->ST_NAME)) {{ $stDetails->ST_NAME}} @endif</td>
		<td>@if(!empty($pc->PC_NO))  {{ $pc->PC_NO}}-{{ $pc->PC_NAME}} @endif</td>
		<td>
			 <!-- <a href="javascript:void(0)" onclick="getProfile('{{$candDetails->C_CODE}}')">-->
			@if(!empty($candDetails->CAND_NAME)) {{$candDetails->CAND_NAME}} @endif<!--</a>-->
		</td>
		
		<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
<td>@if(!empty($candDetails->DATE_OF_LODGING)) {{ date('d-m-Y',strtotime(str_replace('/', '-', $candDetails->DATE_OF_LODGING)))}}  @else {{ 'N/A'}} @endif</td>

<td>@if(!empty($candreceieved))  {{ $candreceieved }} @else 0 @endif</td>
<td>@if(!empty( $totalexpen))  {{  $totalexpen }} @else 0 @endif</td>
		
	</tr>
	@endforeach 
	@endif 
	<tbody>
	</tbody>
</table>
</div>


</div>
<!--END OF SELECT CANDIDATE-->
</div>
<!--  <div class="col-lg-6 col-md-12 col-sm-12">
<div class="card text-left" style="width:100%;">

<div class="card-body"  class="collapse show">
@if($count>0)
<div id="barchart"></div>
@else
No data for graph. 
@endif
</div>
</div>
</div> -->
</div>
</div>
</section>
</main>
<!-- Modal -->
<div class="modal fade" id="ModalProfile" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
<?php //print_r($PreviewData);die;    ?>
            <div class="modal-body">
                <div class="col"><center><h4>Candidate Profile</h4></center></div>
                <br>
                <div class="profileData"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
<!--            <button id='cmd' ids="">generate PDF</button>-->
        </div>

    </div>
</div>

  <!-- end pop up -->
<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
<!-- <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> -->
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
 /*   google.charts.load('current', {'packages': ['bar']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        var id = 1;
        $.ajax({
            type: "get",
            url: "{{url('/')}}/ropc/candidateListByfinalizeDatagraph",
            dataType: "json",
            success: function (response) {
                var data = google.visualization.arrayToDataTable(response);
                var options = {
                    chart: {
                        title: 'Overall summary of Finalized',                         
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


    } */
          function getProfile(candidate_id){
        //var candidate_id = $(this).attr('id');
        jQuery.ajax({
            url: "{{url('/eci-expenditure/getprofile')}}",
            type: 'GET',
            data: {candidate_id: candidate_id},
            dataType: 'html',
            success: function (result) {
                 
                $('.profileData').html(result);
                $('#ModalProfile').modal('show');
                 

            }
        });
    }
</script>
<!--graph implementation start here-Manoj -->
  
<script>
$(document).ready(function() {
    var table = $('#examplereturn').DataTable({   
     dom: 'lBfrtip', 
     lengthMenu: [ [10, 50, 100, -1], [10, 50, 100, 'All'] ],
     pageLength: 10,
     buttons: [
            {
                extend: 'pdfHtml5',               
                pageSize: 'LEGAL',
               filename: function() {
                return 'return-report';    
              },
             title: function() {
                  return '<?php echo 'State Name:'.$stateName.'   PC:'.$pcName.''; ?>'
              },
            }],
           
         
      
    });
  })
  </script>
@endsection


