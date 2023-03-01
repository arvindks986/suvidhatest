@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Finalized PC')
@section('content')
<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
  <div class="container">
  <div class="row">
  <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(Finalized PC)</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
               </p>
			   <p class="mb-0 text-right">
					 <a href="FinalisedPCReportPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
        <a href="FinalisedPCReportXls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
			   </p>
              </div>
			  
			
            </div>
      </div>
  
 <div class="card-body">
 
	<div class="table-responsive">
	
	
	
<table class="table table-bordered table-striped">
              <thead>
                <tr class="table-primary">
                  <th scope="col">SI No</th>
                  <th scope="col">PC. NO.</th>
                  <th scope="col">PC NMAE</th>
                </tr>
              </thead>
              <tbody>
                <?php //echo '<pre>';print_r($data);die;
                $count=1;?>
                
                @forelse($data as $row)
                 @if($row->finalize ==1)
                    <tr>
                  <td>{{$count}}.</td>
                  <td>{{$row->pc_no}}</td>
                  <td>{{$row->PC_NAME}}</td>

                </tr>
                 <?php $count++;?>
                @else
                
                 <tr>
                  <td colspan="3">No Record Found</td>
                </tr>
				@break
				@endif
                @empty
                <tr>
                  <td colspan="3">No Record Found</td>
                </tr>
                
                @endforelse
              </tbody>
            </table>
	
	
	
	</div>
 </div>
 </div>
 </div>
 </div>
 </section>

@endsection