@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content') 
<?php
$st = getstatebystatecode($user_data->st_code);
$distname = getdistrictbydistrictno($user_data->st_code, $user_data->dist_no);
$pcdetails = getpcbypcno($user_data->st_code, $user_data->pc_no);


$pcName = !empty($pcdetails->PC_NAME) ? $pcdetails->PC_NAME : 'ALL';
$cons_no = !empty($pcdetails) ? $cons_no : '0';
//dd($pcdetails);
?>






<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">
                <div class="col"><h6 class="mr-auto mt-2">Scrutiny Candidate List : </h6>


                </div> 
                <!--
<div class="col"><p class="mb-0 text-right">
        <b>State Name:</b> 
        <span class="badge badge-info"><?php //$st->ST_NAME    ?></span> &nbsp;&nbsp; 
        <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
        <b>PC:</b> <span class="badge badge-info"><?php //$pcName    ?></span>
        <b></b> <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>

    </p></div>-->
            </div> <!-- end row -->
        </div>

    </section>
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row">

                @if (Session::has('message'))
                <div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{{ Session::get('message') }} </div> 
                @php Session::forget('message'); @endphp
                @elseif (Session::has('error'))
                <div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{ Session::get('error') }} <br/>

                </div>
                @php Session::forget('error'); @endphp
                @endif

                <?php
                if (session()->get('success')) {
                    ?>
                    <div class="alert alert-success">
                        <?php echo session()->get('success'); ?>    
                    </div>
                <?php } ?>

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
                                        <th>Date Of Lodging</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <?php $j = 0; ?>
                                @if(!empty($scrutinycandidate))
                                @foreach($scrutinycandidate as $candDetails)  
                                <?php
                                $pcDetails = getpcbypcno($candDetails->st_code, $candDetails->pc_no);
                                $date = new DateTime($candDetails->created_at);
                                //echo $date->format('d.m.Y'); // 31.07.2012
                                $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
                                // dd($candDetails);
                                $j++;
                                ?>
                                <tr>
                                    <td>@if(!empty($candDetails->pc_no)) {{ $pcDetails->PC_NAME}} @endif</td>
                                    <td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
                                    <td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
                                    <td>@if(!empty($lodgingDate)) {{$lodgingDate}} @endif</td>                                     
                                    <td >
                                        @if($candDetails->final_by_ceo !='1')
                                        <a href="{{url('/pcceo/editExpenditureReport?candidate_id=')}}{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary" 
                                           target="_blank">Proceed</a>
                                           @endif

                                        <!--  <button type="button"  class="btn btn-primary profileByceo" id="{{$candDetails->candidate_id}}"  >Proceed</button> -->
                                        <!-- <a href="{{url('/')}}/pcceo/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary" 
                                           target="_blank">Print</a> -->

                                        &nbsp;&nbsp;
                                        <a href="javascript:void(0)"  class="btn btn-primary" onclick="getProfile('{{$candDetails->candidate_id}}')">
                                            view</a>
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
            <?php //print_r($PreviewData);die;     ?>
            <div class="modal-body">
                <div class="col"><center><h4>Scrutiny Report Filed By RO</h4></center></div>
                <br>
                <div class="scruitData"></div>
            </div>


        </div>

    </div>
</div>
<!---/////////////////modal for profile preview /////////////////-->
<!-- Modal -->
<div class="modal fade" id="ModalProfile" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <?php //print_r($PreviewData);die;     ?>
            <div class="modal-body">
                <div class="col"><center><h4>Candidate profile</h4></center></div>
                <br>
                <div class="profileData"></div>
            </div>

            <!--            <button id='cmd' ids="">generate PDF</button>-->
        </div>

    </div>
</div>
<!-- ProfileCEO-->
<div class="modal fade" id="ModalProfileCEO" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <?php //print_r($PreviewData);die;     ?>
            <div class="modal-body">
                <div class="col"><center><h4>Candidate Status</h4></center></div>
                <br>
                <div class="profileDataCEO"></div>
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
    $(".profileByceo").click(function () {

    var candidate_id = $(this).attr('id');
    jQuery.ajax({
    url: "{{url('/pcceo/proceed')}}",
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
    // // end profile ECI pop up
    function getProfile(candidate_id){
    //var candidate_id = $(this).attr('id');
    jQuery.ajax({
    url: "{{url('/pcceo/GetProfileCEO')}}",
            type: 'GET',
            data: {candidate_id: candidate_id},
            dataType: 'html',
            success: function (result) {

            $('.profileDataCEO').html(result);
            $('#ModalProfileCEO').modal('show');
            }
    });
    }
    // end profile CEO

</script>
<!--graph implementation start here-Manoj -->
@endsection

