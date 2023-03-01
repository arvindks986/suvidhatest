@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content') 
<?php
$st = getstatebystatecode($user_data->st_code);
$distname = getdistrictbydistrictno($user_data->st_code, $user_data->dist_no);
$pcdetails = getpcbypcno($user_data->st_code, $user_data->pc_no);
$last_date_prescribed_acct_lodgenot = !empty($resultDeclarationDate['start_result_declared_date']) ? 
date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' + 30 days ')):'';
 
$last_date_prescribed_acct_lodge = !empty($candList[0]->last_date_prescribed_acct_lodge) && strtotime($candList[0]->last_date_prescribed_acct_lodge) > 0 ?date('d-m-Y', strtotime($candList[0]->last_date_prescribed_acct_lodge)) : 
   $last_date_prescribed_acct_lodgenot;
?>
<style type="text/css">
    .definalizeForm{width: 87%;
    margin: 0 auto;}
      textarea#definalization_reason {
    border: 1px solid #6666;
    border-radius: 2px;
    height: 100px;
}
#definalized_error{    color: red;
    font-size: 15px;}
</style>
<main role="main" class="inner cover mb-3">
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="card text-left" style="width:100%; margin:0 auto;">
                    <div class=" card-header">
                        @if (Session::has('message'))
                        <div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{{ Session::get('message') }} </div> 
                        @php Session::forget('message'); @endphp
                        @elseif (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            {{ Session::get('error') }} <br/>

                        </div>
                        @php Session::forget('error'); @endphp
                        @endif
                        <div class=" row">
                            <div class="col"><h2 class="mr-auto">Candidate List</h2></div> 
                            <div class="col"><p class="mb-0 text-right">
                            <!-- <a href="javascript:window.print()"> 
                           <i class="fa fa-print"></i>  </a>&nbsp;&nbsp;&nbsp;&nbsp; -->
                                    <b>State Name:</b> 
                                    <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
                                    <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                                    <b>PC:</b> <span class="badge badge-info">{{ $pcdetails->PC_NAME}}</span>
                                </p></div>
                        </div><!-- end row-->
                    </div><!-- end card-header-->
                    <div class="card-body">  
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Candidate Name</th>

<!--          <th>Candidate Name In Hindi</th>
--><!--          <th>Candidate Father Name</th>
                                        -->          <th>Election Year</th>
                                        <th>Election Type</th>
                              <!--           <th>View</th> 
                                        -->        
                                        <th>Last Date of Submission</th>
                                        <th>Date of Scrutiny Report Submission</th>
                                        <th>Date of Lodging A/C By Candidate</th>
                                        <th>Date of Sending to the CEO</th>
                                        <th>Date of Receipt By CEO</th>
                                        
                                        <th class="width-200"> Action </th>   
                                        <th> Status </th>
                                    </tr>
                                </thead>
                                <?php $j = 0; ?>
                                @if(!empty($candList))
                                @foreach($candList as $candDetails)  
                                <?php
                                // dd($candDetails);
                                $j++;
                                ?>
                                <tr>
                                    <td><a href="javascript:void(0)" onclick="getProfile('{{$candDetails->candidate_id}}')" >@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif - @if(!empty($candDetails->cand_hname)) {{$candDetails->cand_hname}} @endif </a></td>
                                    <td>@if(!empty($candDetails->YEAR)) {{$candDetails->YEAR}} @endif</td>
                                    <td>@if(!empty($candDetails->ELECTION_TYPE)) {{$candDetails->ELECTION_TYPE}} @endif</td>
                                    <td>
                                    {{$last_date_prescribed_acct_lodge}}
                                    </td>
                                    <td>
                                        <?php echo!empty($candDetails->finalized_date) ? date('d-m-Y', strtotime($candDetails->finalized_date)) : 'N/A' ?>
                                    </td>
                                    <td><?php echo !empty($candDetails->date_orginal_acct) && strtotime($candDetails->date_orginal_acct)>0  ? date('d-m-Y', strtotime($candDetails->date_orginal_acct)) : 'N/A' ?></td>
                                    <td><?php echo!empty($candDetails->date_of_sending_deo) ? date('d-m-Y', strtotime($candDetails->date_of_sending_deo)) : 'N/A' ?></td>
                                    <td><?php echo!empty($candDetails->date_of_receipt) ? date('d-m-Y', strtotime($candDetails->date_of_receipt)) : 'N/A' ?></td>
                                  
                              
                                    <td>
                                         
                                        @if(($candDetails->final_by_ro=="1" && $candDetails->finalized_status=="1")  || (strtotime($candDetails->report_submitted_date)>0 && $candDetails->finalized_status=="1"))
                                        
                                        <a href="{{url('/')}}/ropc/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-60" target="_blank">Report</a> 
                                        @endif

                                       @if((!empty($candDetails->form_fill_start) && strtotime($candDetails->form_fill_start)>0 && $candDetails->finalized_status=="0") || $candDetails->finalized_status=="1")
                                              <a href="{{url('/')}}/ropc/view/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-secondary btn-sm width-60" >View</a>
                                        @endif
                                        @if($candDetails->final_by_ro !="1" && $candDetails->finalized_status !="1" && empty($candDetails->form_fill_start))
                                            N/A
                                        @endif
                                          <?php /* //hide by niraj instructed by Manish Sir
                                          @if($candDetails->finalized_status=="1" && empty($candDetails->final_action))
                                             @if($candDetails->count_by_ro=="1")
                                            <button value="" id="count_by_ceo" style="border-color:red;background-color: red;" class="btn btn-info btn-secondary">Definalized</button>
                                            @else
                                            <button value="{{$candDetails->candidate_id}}" id="changeStatus" class="btn btn-info">Definalize</button>
                                            @endif
                                          @endif
                                          */ ?>
                                    </td>
                                          <?php 
                                           $issueslist=array("Hearing Done","Reply Issued","Notice Issued");  
                                  
                                         ?>
                                    <td>
                                        @if(empty($candDetails->date_of_declaration))
                                        <a href="{{url('/')}}/ropc/deoformview/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-warning btn-sm width-90 text-white ">Not-Started</a>
                                        @elseif(!empty($candDetails->form_fill_start) && strtotime($candDetails->form_fill_start)>0 && $candDetails->finalized_status=="0")
                                        <a href="{{url('/')}}/ropc/deoformview/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-success btn-sm width-90 text-white "  data-placement="left" title="Scrutiny form partially filled but not completed.">In progress</a>
                                        @elseif(!empty($candDetails->final_action) && in_array($candDetails->final_action, $issueslist))
                                        <a href="{{url('/ropc/editExpenditureReport?candidate_id=')}}{{base64_encode($candDetails->candidate_id)}}" class="btn btn-info btn-sm width-130 text-white" title="Scrutiny report has been finailised so push this form while clicking on Update Info to CEO">{{!empty($candDetails->final_action)? $candDetails->final_action:'Partially Finalized'}}</a>
                                        @elseif($candDetails->final_by_ro=="0" && $candDetails->finalized_status=="1")
                                        <a href="{{url('/ropc/editExpenditureReport?candidate_id=')}}{{base64_encode($candDetails->candidate_id)}}" class="btn btn-info btn-sm width-130 text-white" title="Scrutiny report has been finailised so push this form while clicking on Update Info to CEO">Partially Finalized</a>
                                        @elseif($candDetails->final_by_ro=="1")
                                        <a href="javascript:void(0)" class="btn btn-secondary btn-sm width-90 text-white ">Finalized</a>
                                        @else
                                        N/A
                                        @endif


 

                                        <!--                                        @if(empty($candDetails->date_of_declaration))
                                                                                <a href="javascript:void(0)" class="btn btn-warning btn-sm width-90 text-white ">Not-Started</a>
                                                                                @else                      
                                                                                <a href="{{url('/')}}/ropc/deoformview/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-secondary btn-sm width-90 text-white disabled" role="button">Started</a>
                                                                                @endif
                                                                                @if(!empty($candDetails->date_of_declaration) && $candDetails->final_by_ro=="0")
                                                                                <a class="btn btn-info btn-sm width-90 text-white" style="" href="{{url('/ropc/editExpenditureReport?candidate_id=')}}{{base64_encode($candDetails->candidate_id)}}">
                                                                                    Update Info</a> 
                                                                                @else 
                                                                                <a class="btn btn-secondary btn-sm width-90 text-white disabled"  href="javascript:void(0)">
                                                                                    Update Info</a> 
                                                                                @endif
                                                                                @if($candDetails->final_by_ro=="1")
                                                                                <a href="javascript:void(0)" class="btn btn-success btn-sm width-90" target="_blank">Finalized</a> 
                                        
                                                                                @else   
                                                                                <a href="javascript:void(0)" class="btn btn-secondary btn-sm width-90 text-white disabled" target="_blank">Finalized</a> 
                                                                                @endif
                                        
                                                                                @if(!empty($candDetails->date_of_declaration))
                                                                                <a href="{{url('/')}}/ropc/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-90 mt-2 " target="_blank">Report</a> 
                                                                                @else
                                                                                <a href="javascript:void(0)" class="btn btn-secondary btn-sm width-90 text-white disabled" target="_blank">Report</a> 
                                                                                @endif
                                                                                 @if($candDetails->final_by_ro=="1")
                                                                                  <a href="javascript:void(0)" class="btn btn-info btn-sm width-75" onclick="getProfile('{{$candDetails->candidate_id}}')">
                                                                                                            Status</a> 
                                                                                 @else
                                                                                       <a href="javascript:void(0)"  class="btn btn-info btn-sm width-75 disablestatus">
                                                                                                            Status</a> 
                                                                                 @endif 
                                                                                <a href="javascript:void(0)" class="btn btn-warning btn-sm width-90 text-white mt-2" onclick="getProfile('{{$candDetails->candidate_id}}')">Status</a> -->
                                    </td>

                                </tr>
                                @endforeach 
                                @endif 
                                <tbody>
                                </tbody>
                            </table>
                        </div> <!-- end responcive-->
                    </div> <!-- end card-body-->
                </div>
            </div>
        </div>
        </div>
    </section>

</main>
<!-- Modal -->
<div class="modal fade" id="ModalProfile" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <?php //print_r($PreviewData);die;      ?>
            <div class="modal-body">
                <div class="col"><center><h4>Candidate Profile</h4></center></div>
                <br>
                <div class="profileData"></div>
            </div>

            <!--            <button id='cmd' ids="">generate PDF</button>-->
        </div>

    </div>
</div>
<div class="modal fade" id="ModalCurrentStatus" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <?php //print_r($PreviewData);die;      ?>
            <div class="modal-body">
                <div class="col"><center><h4>Current Status</h4></center></div>
                <br>
                <div class="statusData"></div>
            </div>

            <!--            <button id='cmd' ids="">generate PDF</button>-->
        </div>

    </div>
</div>


<!-- Modal -->
    <div class="modal fade" id="myModalcheck" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel" style="text-align: -webkit-center;">Are you sure give permission to update scrutiny report?<Br>IF YES GIVE REASON</h6><br>

                </div>
                 <div class="form-group definalizeForm">
                    <textarea name="definalization_reason" class="form-control" id="definalization_reason"></textarea>
                    <span id="definalized_error"></span>
                  </div>
                <div class="modal-footer mb-2">
                     <input type="hidden" value="" id="definalizedreport">
                     <input type="button" value="Submit" id="definalized" class="btn btn-primary mt-2">
                    <input type="button" value="Cancel" id="" class="btn btn-default mt-2" data-dismiss="modal">
                   <!--  <input type="button" value="" id="definalizedreport"  class="btn btn-primary btncl mt-2" data-dismiss="modal"> -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModaldefi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel"><center>Scrutiny Report is successfully definalized.</center></h6>
                </div>
                <div class="modal-footer mb-2">
                    <input type="button" value="Ok" id="" class="btn btn-primary mt-2" data-dismiss="modal">
                </div>
            </div>
        </div>
    </div>


<div class="modal fade" id="count_by_ceo_count_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel"><center>Scrutiny Report Definalization limit reached at DEO level.</center></h6>
                </div>
                <div class="modal-footer mb-2">
                    <input type="button" value="Ok" id="" class="btn btn-primary mt-2" data-dismiss="modal">
                </div>
            </div>
        </div>
    </div>

<!-- end pop up -->

<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
<script type="text/javascript">

                                            $(document).ready(function () {

                                            $('.disablestatus').attr('disabled', true);
                                            });
                                            function getProfile(candidate_id) {
                                            //var candidate_id = $(this).attr('id');
                                            jQuery.ajax({
                                            url: "{{url('/ropc/getprofile')}}",
                                                    type: 'GET',
                                                    data: {candidate_id: candidate_id},
                                                    dataType: 'html',
                                                    success: function (result) {

                                                    $('.profileData').html(result);
                                                    $('#ModalProfile').modal('show');
                                                    }
                                            });
                                            }
                                            function getStatus(candidate_id) {

                                            jQuery.ajax({
                                            url: "{{url('/ropc/GetProfileRO')}}",
                                                    type: 'GET',
                                                    data: {candidate_id: candidate_id},
                                                    dataType: 'html',
                                                    success: function (result) {

                                                    $('.statusData').html(result);
                                                    $('#ModalCurrentStatus').modal('show');
                                                    }
                                            });
                                            }
// end profile ECI pop up


 $(document).on('click', '#changeStatus', function (e) {
    var candidate_id = $(this).val();
    $('#definalizedreport').val(candidate_id)
    $('#myModalcheck').modal('show');
  });

  
 $(document).on('click', '#definalized', function (e) {
    var candidate_id = $('#definalizedreport').val();
    var reason = $("#definalization_reason").val();
    if($.trim(reason).length>0){
    jQuery.ajax({
    url: "{{url('/ropc/updateStatusReport')}}",
            type: 'GET',
            data: {candidate_id: candidate_id,reason:reason},
            success: function (result) {
                result = result.trim();
                if(result=="1")
                {
          $('#myModalcheck').modal('hide');
          $('#definalized_error').css('display','none');
                  $('#myModaldefi').modal('show');
                  setTimeout(function() {
              location.reload();
          }, 5000);
                }
            }
    });
  }
  else
  {
    $("#definalized_error").text("Please give reason for definalization of candidate.");
  }


     
    
    });
$(document).on('click', '#count_by_ceo', function (e) {
    $('#count_by_ceo_count_modal').modal('show');
  });
</script>
<!--graph implementation start here-Manoj -->
@endsection
