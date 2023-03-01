@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content') 
 <?php  
	$st=getstatebystatecode($user_data->st_code);
	$distname=getdistrictbydistrictno($user_data->st_code,$user_data->dist_no);
	$pcdetails=getpcbypcno($user_data->st_code,$user_data->pc_no); 
//	dd($pcdetails);
    ?>
<main role="main" class="inner cover mb-3">
	<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h2 class="mr-auto">Final Candidate List</h2></div> 
                   <div class="col"><p class="mb-0 text-right">
												<b>State Name:</b> 
												<span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
												<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
												<b>PC:</b> <span class="badge badge-info">{{ $pcdetails->PC_NAME}}</span>
                        <b></b> <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
									       
                    </p></div>
										</div><!-- end row-->
	              </div><!-- end card-header-->
<div class="card-body">  
  <div class="table-responsive">
      <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
          <th>Candidate Name</th>
					<th>Candidate Name In Hindi</th>
					<th>Candidate Father Name</th>
         <th>Action</th>
        </tr>
        </thead>
<?php $j=0;  ?>
		@if(!empty($finalCandList))
		@foreach($finalCandList as $candDetails)  
			<?php
			// dd($candDetails);
				$j++; 
				?>
<tr>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
  <td>
                                          <a href="javascript:void(0)" onclick="getProfile('{{$candDetails->candidate_id}}')">
                                        @if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</a>
                                    </td>
<td>@if(!empty($candDetails->candidate_father_name)) {{$candDetails->candidate_father_name}} @endif</td> 
<td >
                                            
                                    <a href="{{url('/')}}/ropc/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary" target="_blank">Scrutiny Report</a> 
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
  <script type="text/javascript">
     function getProfile(candidate_id){
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
  </script>
@endsection

