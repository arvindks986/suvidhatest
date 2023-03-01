@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content') 
 <?php  
	$st=getstatebystatecode($user_data->st_code);
  $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
	$distname=getdistrictbydistrictno($user_data->st_code,$user_data->dist_no);
  $cons_no = !empty($cons_no) ? $cons_no : '0';
  $pcdetails=getpcbypcno($user_data->st_code,$cons_no); 
  $pcName=!empty($pcdetails->PC_NAME) ? $pcdetails->PC_NAME : 'ALL';
    ?>
<main role="main" class="inner cover mb-3">
	<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h2 class="mr-auto">Pending At CEO : {{$count}}</h2></div> 
                   <div class="col"><p class="mb-0 text-right">
												<b>State Name:</b> 
												<span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
												<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
												<b>PC:</b> <span class="badge badge-info">{{ $pcName }}</span>
                                               <b></b><a href="{{url('/pcceo/statusExpdashboard')}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>

									       
                    </p></div>
										</div><!-- end row-->
	              </div><!-- end card-header-->
<div class="card-body">  
  <div class="table-responsive">
      <table id="example1" class="table table-striped table-bordered table-hover" style="width:100%">
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
		@if(!empty($finalbyceoCandList))
		@foreach($finalbyceoCandList as $candDetails)  
			<?php
      //dd($candDetails);
      $pc=getpcbypcno($candDetails->ST_CODE,$candDetails->constituency_no); 
		 $date = new DateTime($candDetails->created_at);
     //echo $date->format('d.m.Y'); // 31.07.2012
     $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
				$j++; 
				?>
<tbody>
<tr>
<td>@if(!empty($candDetails->constituency_no)) {{ $candDetails->constituency_no}}-{{ $pc->PC_NAME}} @endif</td>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
<td>@if(!empty($candDetails->last_date_prescribed_acct_lodge)) {{ date('d-m-Y',strtotime($candDetails->last_date_prescribed_acct_lodge))}}  @else {{ '22-06-2019'}} @endif</td>
<td>  @if($candDetails->final_by_ro==1)
                <a href="{{url('/')}}/pcceo/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-75" target="_blank">Report</a> 
                @endif</td>
</tr>
@endforeach 
@endif 

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
                return 'finalbyceo-report';    
              },
             title: function() {
                  return '<?php echo 'State Name:'.$stateName.'   PC:'.$pcName.''; ?>'
              },
            }],
           
         
      
    });
  })
  </script>
@endsection

