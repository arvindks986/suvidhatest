@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content') 
@php
  $st_code=!empty($st_code) ? $st_code : '';
  $cons_no=!empty($cons_no) ? $cons_no : '';
  $st=getstatebystatecode($st_code);
 
  $distname=getdistrictbydistrictno($st_code,$user_data->dist_no);
  $pcdetails=getpcbypcno($st_code, $cons_no); 
  $pcName=!empty($pcdetails->PC_NAME) ? $pcdetails->PC_NAME : 'ALL';
  $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
 //echo $st_code.'cons_no=>'.$cons_no;
@endphp
<main role="main" class="inner cover mb-3">
<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h2 class="mr-auto">Expense UnderStated</h2></div> 
                   <div class="col"><p class="mb-0 text-right">
						<b>State Name:</b> 
						<span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
						<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
						<b>PC:</b> <span class="badge badge-info">{{ $pcName}}</span>
                        <b></b> <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
									       
                    </p></div>
										</div><!-- end row-->
	              </div><!-- end card-header-->
<div class="card-body">  
  <div class="table-responsive">
      <table id="exampleexp" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
		 <th>State</th>
        <th>PC No & Name</th>
        <th>Candidate Name</th>
        <th>Party Name</th>
        <th>Date Of Lodging Srcutiny Form</th>
        <th>Action</th>
        </tr>
        </thead>
<?php $j=0;  ?>
@if(!empty($expenseunderstated))
@foreach($expenseunderstated as $candDetails)  
<?php
$date = new DateTime($candDetails->created_at);
//echo $date->format('d.m.Y'); // 31.07.2012
$lodgingDate=$date->format('d-m-Y'); // 31-07-2012
$j++; 
$pc=getpcbypcno($candDetails->st_code,$candDetails->pc_no); 
$stDetails=getstatebystatecode($candDetails->st_code);
        ?>
<tr>
<td>@if(!empty($stDetails->ST_NAME)) {{ $stDetails->ST_NAME}} @endif</td>
<td>@if(!empty($candDetails->pc_no)) {{ $pc->PC_NAME}} @endif</td>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
<td>@if(!empty($lodgingDate)) {{$lodgingDate}} @endif</td>
<td> @if($candDetails->final_by_ro=='1')
<a href="{{url('/')}}/eci-expenditure/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-75" target="_blank"> Srcutiny Report</a> 
@else  <a href="#" class="btn btn-primary btn-sm width-75" target="_blank">Not Started</a> @endif</td>
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
  <script  src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script>
$(document).ready(function() {
    var table = $('#exampleexp').DataTable({   
     dom: 'lBfrtip', 
     lengthMenu: [ [10, 50, 100, -1], [10, 50, 100, 'All'] ],
     pageLength: 10,
     buttons: [
            {
                extend: 'pdfHtml5',               
                pageSize: 'LEGAL',
               filename: function() {
                return 'expenseunderstated-report';    
              },
             title: function() {
                  return '<?php echo 'State Name:'.$stateName.'   PC:'.$pcName.''; ?>'
              },
            }],
           
         
      
    });
  })
  </script>
@endsection

