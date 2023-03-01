@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content')
@php 
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($st_code);
$pcdetails=getpcbypcno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$pcName=!empty($pcdetails) ? $pcdetails->PC_NAME : 'ALL';
$all_pc=getpcbystate($st_code);
 //echo $st_code.'cons_no'.$cons_no; die;
@endphp
<main role="main" class="inner cover mb-3">
	<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col-md-5"><h2 class="mr-auto">Filed Data List</h2></div> 
                   <div class="col-md-7"><p class="mb-0 text-right">
						<b>State Name:</b> 
						<span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
						<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
						<b>PC:</b> <span class="badge badge-info">{{ $pcName }}</span>
                        <a href="{{url('/eci-expenditure/EcifiledCandidateMISPDF')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
                        <a href="{{url('/eci-expenditure/EcifiledCandidateMISEXL')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
                        <b></b><a href="{{url('/eci-expenditure/mis-candidate')}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>
                        </p></div>
						</div><!-- end row-->
	              </div><!-- end card-header-->
<div class="card-body">  
  <div class="table-responsive">
      <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
		 <th>State</th>
        <th>PC No & Name</th>
          <th>Candidate Name</th>
			<th>Party Name</th>
			<th>Last Date Of Lodging</th>
      <th>DEO Scruitny Report</th>
        </tr>
        </thead>
<?php $j=0;  ?>
		@if(!empty($filedData))
		@foreach($filedData as $candDetails)  
			<?php
      //dd($candDetails);
		 $date = new DateTime($candDetails->created_at);
     //echo $date->format('d.m.Y'); // 31.07.2012
     $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
     $pc=getpcbypcno($candDetails->ST_CODE,$candDetails->constituency_no); 
				$j++;
				 $stDetails=getstatebystatecode($candDetails->ST_CODE);
		 ?>

<tr>

 <td>@if(!empty($stDetails->ST_NAME)) {{ $stDetails->ST_NAME}} @endif</td>
<td>@if(!empty($pc->PC_NO)) {{ $pc->PC_NO}}-{{ $pc->PC_NAME}} @endif</td>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
 <td>@if(!empty($candDetails->last_date_prescribed_acct_lodge)) {{ date('d-m-Y',strtotime($candDetails->last_date_prescribed_acct_lodge))}}  @else {{ '22-06-2019'}} @endif</td>
 <td>  @if($candDetails->final_by_ro=='1')
<a href="{{url('/')}}/eci-expenditure/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-75" target="_blank">Report</a>@else <a href="#" class="btn btn-primary btn-sm width-75">Not Start</a> @endif</td>  
</tr>
@endforeach 
@endif 

<tbody>   </tbody>
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

