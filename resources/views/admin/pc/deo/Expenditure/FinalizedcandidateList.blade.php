@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Finalized Candidate List')
@section('description', '')
@section('content') 
 
 <?php 
	$st=getstatebystatecode($user_data->st_code);
	$distname=getdistrictbydistrictno($user_data->st_code,$user_data->dist_no);

  $pc =!empty($_GET['pc'])?$_GET['pc']:""; 
  $pcdetails=getpcbypcno($user_data->st_code,$pc); 
  $pcName=!empty($pcdetails->PC_NAME) ? $pcdetails->PC_NAME : 'ALL';
  $all_pc="";
  $last_date_prescribed_acct_lodge = !empty($candList[0]->last_date_prescribed_acct_lodge) && strtotime($candList[0]->last_date_prescribed_acct_lodge) > 0 ?date('d-m-Y', strtotime($candList[0]->last_date_prescribed_acct_lodge)) : "22-06-2019";
    ?>
    <style type="text/css">
    	.mt-5, .my-5{margin-top: 1rem!important;}
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
  	<div class="col-sm-12 mt-3">
              <!--FILTER STARTS FROM HERE-->
              <form method="get" action="{{url('/pcceo/FinalizedcandidateList')}}" id="EcidashboardFilter">           
                       <div class="row justify-content-center">
                      <!--STATE LIST DROPDOWN STARTS-->
                          <div class="col-sm-3">
                  <label for="" class="mr-3">Select PC</label>    
                  <select name="pc" id="pc" class="consttype form-control" >
                    <option value="">-- All PC --</option>
                    @php $all_pc = getpcbystate($user_data->st_code); @endphp
                    @foreach($all_pc as $getPc)
                    @if ($pc == $getPc->PC_NO)
                      <option value="{{ $getPc->PC_NO }}" selected>{{$getPc->PC_NAME}} - {{$getPc->PC_NAME_HI}}</option>
                      @else
                      <option value="{{ $getPc->PC_NO }}">{{$getPc->PC_NAME}} - {{$getPc->PC_NAME_HI}}</option>
                    @endif
                @endforeach 
              </select>
              @if ($errors->has('pc'))
                        <span style="color:red;">{{ $errors->first('pc') }}</span>
                    @endif
                     
              <div class="acerrormsg errormsg errorred"></div>
                        </div>
					  	<div class="col-sm-1 mt-2">
							<p class="mt-4 text-left">
							<!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
						  <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
            	</p>
                        </div>
                    </div>
                </form> 
                 <!--FILTER ENDS HERE-->
				</div> 
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
                 <div class="col"><h2 class="mr-auto">Finalized Candidate List</h2></div> 
                   <div class="col"><p class="mb-0 text-right">
												<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
												<b>PC:</b> <span class="badge badge-info">{{$pcName}}</span>
									  </p></div>
										</div><!-- end row-->
	              </div><!-- end card-header-->
<div class="card-body">  
  <div class="table-responsive">
      <table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
          <th>Candidate Name</th>
          <th>State</th>
          <th>PC No & PC Name</th>
          <th>Election Year</th>
		  <th>Election Type</th>
 		  <th>Last Date of Submission</th>
  		  <th>Date of Submission</th>
		  <th><div class="width-260">Status</div></th>
        </tr>
        </thead>
<?php $j=0;  ?>
		@if(!empty($candList))
		@foreach($candList as $candDetails)  
			<?php
				$pcdetails=getpcbypcno($candDetails->st_code,$candDetails->pc_no); 
				$st=getstatebystatecode($candDetails->st_code);

			//dd($st);
				$j++; 

				?>
<tr>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif - @if(!empty($candDetails->cand_hname)) {{$candDetails->cand_hname}} @endif</td>
<td>{{$st->ST_NAME}}</td>
<td>{{$pcdetails->PC_NO}} - {{$pcdetails->PC_NAME}}</td>
<td>@if(!empty($candDetails->YEAR)) {{$candDetails->YEAR}} @endif</td>
<td>@if(!empty($candDetails->ELECTION_TYPE)) {{$candDetails->ELECTION_TYPE}} @endif</td>
<td>{{$last_date_prescribed_acct_lodge}}</td>
<td>
<?php echo !empty($candDetails->finalized_date)?date('d-m-Y h:i A',strtotime($candDetails->finalized_date)):'N/A' ?>
</td>
  
<td>
  <?php if($candDetails->count_by_ceo=="2"){?>
      <button value="" id="count_by_ceo" class="btn btn-info">Permission Of Scrutiny Report</button>
  <?php }else{ ?>
  <button value="{{$candDetails->candidate_id}}" id="changeStatus" class="btn btn-info">Permission Of Scrutiny Report</button>
<?php } ?>
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
            <?php //print_r($PreviewData);die;     ?>
            <div class="modal-body">
                <div class="col"><center><h4>Candidate Status</h4></center></div>
                <br>
                <div class="profileData"></div>
            </div>

            <!--            <button id='cmd' ids="">generate PDF</button>-->
        </div>

    </div>
</div>
<!-- ProfileRO-->
 
<!-- end pop up -->

<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>

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
                    <h6 class="modal-title" id="myModalLabel"><center>Scrutiny Report Definalization limit reached at CEO level. </center></h6>
                </div>
                <div class="modal-footer mb-2">
                    <input type="button" value="Ok" id="" class="btn btn-primary mt-2" data-dismiss="modal">
                </div>
            </div>
        </div>
    </div>



<script type="text/javascript">
$(document).on('click', '#fianlform', function () {
                var candidate_id = $('#candidate_id').val();
                var candID = $('#candidate_id_base').val();
                 
                $('#myModalErr').modal('hide');

                //  var retVal = confirm("Kindly make sure, do you want to finalized all entries?");
                //   if( retVal == true ) {
                $.ajax({
                    data: {"_token": "{{ csrf_token() }}", candidate_id: candidate_id},
                    type: "post",
                    url: "{{url('/ropc/FinalizedData')}}",
                    success: function (data) {
                        response = data.trim();
                        if (response == 1) {
                            //alert("Finalized");
                            $('#myModalSucc').modal('show');
                            $('#getlist').attr('ids',candID);
                        }
                        if (response == 0) {
                            $('#myModalErr').modal('show');
                        }
                    }
                });
                // }
                // else{
                //    return false;
                // }  
            });


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
    url: "{{url('/pcceo/updateStatusReport')}}",
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
    // end profile ECI pop up
$(document).on('click', '#count_by_ceo', function (e) {
    $('#count_by_ceo_count_modal').modal('show');
  });

</script>
<!--graph implementation start here-Manoj -->
@endsection
