@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Index Card Report')
@section('bradcome', 'List Of Political Parties Participated')
@section('content')
@php
	if(Auth::user()->designation == 'ROAC'){
		$prefix 	= 'roac';
	}else if(Auth::user()->designation == 'CEO'){	
		$prefix 	= 'acceo';
	}else if(Auth::user()->role_id == '27'){
		$prefix 	= 'eci-index';
	}else if(Auth::user()->role_id == '7'){
		$prefix 	= 'eci';
	}
@endphp


<?php  $st=getstatebystatecode($st_code);   ?>
<section class="">
  <div class="container-fluid">
    <div class="row">
      <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
        <div class=" card-header">
          <div class=" row">
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(3 - List Of Political Parties Participated)<img id="theImg" src="/assets/images/img.png"></h4></div>
            <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
            </p>
            <p class="mb-0 text-right">
              <a href="{!! url('/'.$prefix.'/list-of-political-parties-participated-pdf/'.$st_code) !!}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
              <a href="{!! url('/'.$prefix.'/list-of-political-parties-participated-xls/'.$st_code) !!}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
            </p>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive" style="width: 100%;">
          <!-- Content goes Here -->
          <table class="table table-bordered table-striped" style="width: 100%;">
			<thead>
				<tr>
				  <th class="blcs">PARTY TYPE</th>
				  <th class="blcs">ABBREVIATION</th>
				  <th class="blcs">PARTY</th>
				</tr>
			  </thead>
			  <tbody>
				@php $i = 1; @endphp
			  @foreach($dataArray as $key=>$data)
				@if($key == 'N-N')
					<tr><th>NATIONAL PARTIES</th></tr>
				@elseif($key == 'S-U')
					<tr><th>STATE PARTIES - OTHER STATES</th></tr>
				@elseif($key == 'S-S')
					<tr><th>STATE PARTIES</th></tr>
				@elseif($key == 'U-U')
					<tr><th>REGISTERED(Unrecognised) PARTIES </th></tr>
				@elseif($key == 'Z-Z')
					<tr><th>INDEPENDENTS  </th></tr>
				@endif
				
				  @foreach($data as $raw)
						<tr>
						  <td>{{$i}}.</td>
						  <td>{{$raw['PARTYABBRE']}}</td>
						  <td>{{$raw['PARTYNAME']}}</td>
						</tr>	
					@php $i++; @endphp
				  @endforeach				  
			  @endforeach
			  
				<tr style="width: 100%;"><td colspan="3"><p style="border-top: 1px solid #000;"></p></td></tr>
			  </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
@endsection