@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content') 
 <?php  
	$st=getstatebystatecode($user_data->st_code);
	$distname=getdistrictbydistrictno($user_data->st_code,$user_data->dist_no);
	$pcdetails=getpcbypcno($user_data->st_code,$cons_no); 
  $cons_no = !empty($pcdetails) ? $cons_no : '0';
//	dd($pcdetails);
    ?>
 


<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">
                <div class="col"><h2 class="mr-auto">Defects in format Candidate List: {{$count}}</h2></div> 
                <div class="col"><p class="mb-0 text-right">
                        <b>State Name:</b> 
                        <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
                        <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                        <b>PC:</b> <span class="badge badge-info">{{ $pcName }}</span>
                        <b></b> <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>

                    </p></div>
            </div> <!-- end row -->
        </div>

    </section>
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="card text-left" style="width:100%;">
                        <!--SELECT CANDIDATE-->
                        <div class="card-body" id="demo" class="collapse show">  
                            <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
        <th>PC No & Name</th>
        <th>Candidate Name</th>
        <th>Party Name</th>
        <th>Date Of Lodging</th>
         
        </tr>
        </thead>
<?php $j=0;  ?>
    @if(!empty($DataentryStartCandList))
    @foreach($DataentryStartCandList as $candDetails)  
      <?php
      // dd($candDetails);
        $j++; 
        $date = new DateTime($candDetails->created_at);
        //echo $date->format('d.m.Y'); // 31.07.2012
        $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
        $pc=getpcbypcno($candDetails->ST_CODE,$candDetails->constituency_no); 
        ?>
<tr>
<td>@if(!empty($candDetails->pc_no)) {{ $pc->PC_NO}} - {{ $pc->PC_NAME}} @endif</td>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
<td>@if(!empty($lodgingDate)) {{$lodgingDate}} @endif</td>
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
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="card text-left" style="width:100%;">

                        <div class="card-body"  class="collapse show">
                            @if($count>0)
                             <div id="barchart" style="height: 682px;"></div>
                            @else
                            No data for graph. 
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
            url: "{{url('/')}}/pcceo/candidateListByformatedefectsgraph/{{$cons_no}}",
            dataType: "json",
            success: function (response) {
                var data = google.visualization.arrayToDataTable(response);
                var options = {
                    chart: {
                        title: 'Overall summary of Defects in format',                         
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
@endsection


