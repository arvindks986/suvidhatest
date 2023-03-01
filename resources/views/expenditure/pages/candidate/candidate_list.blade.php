@extends('admin.layouts.pc.theme')
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
                 <div class="col"><h2 class="mr-auto">Candidate List</h2></div> 
                   <div class="col"><p class="mb-0 text-right">
												<b>State Name:</b> 
												<span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
												<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
												<b>PC:</b> <span class="badge badge-info">{{ $pcdetails->PC_NAME}}</span>
									  </p></div>
										</div><!-- end row-->
	              </div><!-- end card-header-->
<div class="card-body">  
  <div class="table-responsive">
      <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
          <th>Candidate Name</th>
<!--           <th>Candidate Name In Hindi</th>
 --><!-- 					<th>Candidate Father Name</th>
 -->          <th>Election Year</th>
          <th>Election Type</th>
          <th>Status</th>
        </tr>
        </thead>
<?php $j=0;  ?>
		@if(!empty($candList))
		@foreach($candList as $candDetails)  
			<?php
			// dd($candDetails);
				$j++; 
				?>
<tr>
<td><a href="{{url('/')}}/ropc/annuxure/{{base64_encode($candDetails->candidate_id)}}" >@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif - @if(!empty($candDetails->cand_hname)) {{$candDetails->cand_hname}} @endif </a></td>

<!-- <td>@if(!empty($candDetails->cand_hname)) {{$candDetails->cand_hname}} @endif</td>
 --><!-- <td>@if(!empty($candDetails->candidate_father_name)) {{$candDetails->candidate_father_name}} @endif</td>
 --><td>@if(!empty($candDetails->YEAR)) {{$candDetails->YEAR}} @endif</td>
<td>@if(!empty($candDetails->ELECTION_TYPE)) {{$candDetails->ELECTION_TYPE}} @endif</td>

<td>
  

    <?php if($candDetails->finalized_status=="0") {
        ?>
                <span class="bg-danger text-white btn btn-sm">Pending</span>   
    <?php 
      }
      elseif(empty($candDetails->finalized_status))
      {?>
                <span class="bg-warning text-white btn btn-sm">Not filed</span>    

     <?php  }
     else{?>
         <span class="bg-success text-white btn btn-sm">Filed</span>         

     <?php }
      ?>
  
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
@endsection

