@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'The Schedule of GE to Lok Sabha - Phase General Elections')
@section('content')

<style>
	
</style>
<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(1 - The Schedule of GE to Lok Sabha, {{getElectionYear()}})</h4></div> 
              <div class="col">
			  <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
               </p>
			   <p class="mb-0 text-right">
					  <a href="scheduleloksabhahighlights_pdf" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
        <a href="scheduleloksabhahighlights_xls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
			   </p>
              </div>
			  
			
            </div>
      </div>
  
 <div class="card-body">
 	 <h5 style="text-align: center;font-weight: bold;" class="pb-2" >PHASE GENERAL ELECTIONS-{{getElectionYear()}}</h5>
	<div class="table-responsive">
                 <table class="table table-bordered table-striped" style="width: 100%;">
				
                        <tr>
                      <th>PHASE</th>
                      <th>NUMBER OF STATE & UNION TERRITORIES</th>
                      <th>NUMBER OF PARLIAMENTARY CONSTITUENCIES</th>
                      <th>POLL DATES </th>
               

                           </tr>
                           <tbody>
           
						@foreach($data as $row)
		   
                          <tr>
                            <td>{{$row->SCHEDULEID}}</td>
                            <td>{{$row->no_state}}</td>
                            <td>{{$row->no_pc}}</td>
                            <td>{{date('d M Y', strtotime($row->DATE_POLL))}}</td>
                          </tr>

                        @endforeach   


                        </tbody>
                     </table>
					 
					 
					  <h5 style="font-weight: bold;text-align: center;" class="pb-2">NUMBER OF PHASES IN STATES AND UNION TERRITORIES</h5>

					 
					 <table class="table table-bordered table-striped" style="width: 100%;">
					<tr>
					                      <th>Sr No</th>
                      <th>NO. OF PHASES</th>
                      <th>STATES AND UNION TERRITORIES</th>
               </tr>


                           <tbody>
           
						@foreach($data2 as $key => $row)
		   
                          <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$row->no_phase}}</td>
                            <td>{{$row->ST_NAME}}</td>
                          </tr>

                        @endforeach   


                        </tbody>
                     </table>
					 
					 
					 
					 
					 
                </div>
 </div>
 </div>
 </div>
 </div>
 </section>

@endsection
