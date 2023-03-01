@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content') 
 <?php  
	$st=getstatebystatecode($user_data->st_code);
	$distname=getdistrictbydistrictno($user_data->st_code,$user_data->dist_no);
    $pcdetails=getpcbypcno($user_data->st_code,$user_data->pc_no); 

     
    $pcName=!empty($pcdetails->PC_NAME) ? $pcdetails->PC_NAME : 'ALL';
    $cons_no = !empty($pcdetails) ? $cons_no : '0';
	//dd($pcdetails);
    ?>

<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">
                <div class="col"><h2 class="mr-auto">Data Entry Start: {{$count}}</h2></div> 
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
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card text-left" style="width:100%;">
                        <!--SELECT CANDIDATE-->
                        <div class="card-body" id="demo" class="collapse show">  
                             <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
          <th>PC No & Name</th>
          <th>Candidate Name</th>
          <th>Party Name</th>
          <th>Last Date Of Lodging</th>
          <th>Action</th>
        </tr>
        </thead>
<?php $j=0;  ?>
    @if(!empty($DataentryStartCandList))
    @foreach($DataentryStartCandList as $candDetails)  
      <?php
      
       $pcDetails=getpcbypcno($candDetails->st_code,$candDetails->pc_no);
      // $date = new DateTime($candDetails->created_at);
       //echo $date->format('d.m.Y'); // 31.07.2012
      // $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
      // dd($candDetails);
        $j++; 
        ?>
<tr>
<td>@if(!empty($pcDetails->PC_NO)) {{ $pcDetails->PC_NO}}-{{ $pcDetails->PC_NAME}} @endif</td>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
<td>@if(!empty($candDetails->last_date_prescribed_acct_lodge)) {{ date('d-m-Y',strtotime($candDetails->last_date_prescribed_acct_lodge))}}  @else {{ '22-06-2019'}} @endif</td>
 <td >
                                            <button type="button"  class="btn btn-primary profileByro" id="{{$candDetails->candidate_id}}"  >Profile View</button>
                                    <a href="{{url('/')}}/pcceo/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary" target="_blank">Print</a> 
                                        </td>

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
               <!--  <div class="col-lg-6">
                    <div class="card text-left" style="width:100%;">

                        <div class="card-body"  class="collapse show">
                            @if($count>0)
                            <div id="barchart" style="height: 1200px;"></div>
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
<!---/////////////////modal for data preview /////////////////-->
<!-- Modal -->
<div class="modal fade" id="ModalReply" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
<?php //print_r($PreviewData);die;    ?>
            <div class="modal-body">
                <div class="col"><center><h4>Scrutiny Report Filed By RO</h4></center></div>
                <br>
                <div class="scruitData"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            <button id='cmd' ids="">generate PDF</button>
        </div>

    </div>
</div>
<!---/////////////////modal for profile preview /////////////////-->
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
  /*  google.charts.load('current', {'packages': ['bar']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        var id = 1;
        $.ajax({
            type: "get",
            url: "{{url('/')}}/pcceo/candidateListBydataentryStartgraph/{{$cons_no}}",
            dataType: "json",
            success: function (response) {
                var data = google.visualization.arrayToDataTable(response);
                var options = {
                    chart: {
                        title: 'Overall summary of Data Entry Start',                         
                    },
                    bars: 'horizontal' // Required for Material Bar Charts.
                };

                var chart = new google.charts.Bar(document.getElementById('barchart'));
                chart.draw(data, google.charts.Bar.convertOptions(options));

            },
            errors: function (errors) {
                console.log(errors);
            }
        });


    } */
</script>
<script type="text/javascript">   
    // profile
       $(".profileByro").click(function () {
        var candidate_id = $(this).attr('id');
        jQuery.ajax({
            url: "{{url('/pcceo/getprofile')}}",
            type: 'GET',
            data: {candidate_id: candidate_id},
            dataType: 'html',
            success: function (result) {
                 
                $('.profileData').html(result);
                $('#ModalProfile').modal('show');
                 

            }
        });

    });
    // end profile pop up
</script>
<!--graph implementation start here-Manoj -->
@endsection
