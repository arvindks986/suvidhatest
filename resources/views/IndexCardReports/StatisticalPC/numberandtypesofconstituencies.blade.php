@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Number And Type Of Constituency')
@section('content')


<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(5 - NUMBER AND TYPES OF CONSTITUENCIES )</h4></div> 
              <div class="col">
			  <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
               </p>
			   <p class="mb-0 text-right">
				<a href="numberandtypesofconstituencies_pdf" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
        <a href="numberandtypesofconstituencies_xls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
			   </p>
              </div>
			  
			
            </div>
      </div>
  
 <div class="card-body">
 
	<div class="table-responsive">
                 <table class="table table-bordered" style="width: 100%;table-layout: fixed;">
                    <thead>
                        <tr>
                            <th>State/UT</th>
                            <th colspan="4">Type Of Constituencies</th>
                            <th></th>                           
                        </tr>                      
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td>Gen</td>
                            <td>SC</td>
                            <td>ST</td>
                            <td>Total</td>
                            <td>No. of Constituencies Where Election Completed</td>
                        </tr>
					@foreach ($data as $row)
						<tr>
                            <td>{{$row->ST_NAME}}</td>
                            <td>{{$row->gen}}</td>
                            <td>{{$row->sc}}</td>
                            <td>{{$row->st}}</td>
                            <td>{{$row->total}}</td>
                            <td>{{$row->completed}}</td>
                        </tr>
					@endforeach

                        <tr>
                            <th><b>Total</b></th>
                            <td><b>{{$dataSum['gen']}}</b></td>
                            <td><b>{{$dataSum['sc']}}</b></td>
                            <td><b>{{$dataSum['st']}}</b></td>
                            <td><b>{{$dataSum['total']}}</b></td>
                            <td><b>{{$dataSum['completed']}}</b></td>
                        </tr>
    
                    <tbody>
                </table>
                </div>
 </div>
 </div>
 </div>
 </div>
 </section>

@endsection