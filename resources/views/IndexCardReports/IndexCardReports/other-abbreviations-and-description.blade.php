@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Index Card Report')
@section('bradcome', 'Other Abbreviations And Descriptions')
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
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(1 - Other Abbreviations And Descriptions)<img id="theImg" src="/assets/images/img.png"></h4></div>
            <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
            </p>
            <p class="mb-0 text-right">
              <a href="{!! url('/'.$prefix.'/other-abbreviations-and-description-pdf/'.$st_code) !!}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
              <a href="{!! url('/'.$prefix.'/other-abbreviations-and-description-xls/'.$st_code) !!}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
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
                <th class="blcs">ABBREVIATIONS </th>
                <th class="blcs">DESCRIPTIONS</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>FD</td>
                <td>Forfeited Deposits</td>
              </tr>
              <tr>
                <td>GEN</td>
                <td>General Constituency
                </td>
              </tr>
              <tr>
                <td>SC</td>
                <td>Reserved for Scheduled Castes
                </td>
              </tr>
              <tr>
                <td>ST</td>
                <td>Reserved for Scheduled Tribes
                </td>
              </tr>
              <tr>
                <td>M</td>
                <td>Male</td>
              </tr>
              <tr>
                <td>F</td>
                <td>Female</td>
              </tr>
              <tr>
                <td>O</td>
                <td>Third Gender</td>
              </tr>
              <tr>
                <td>T</td>
                <td>Total</td>
              </tr>
              <tr>
                <td>N</td>
                <td>National Party</td>
              </tr>
              <tr>
                <td>S</td>
                <td>State Party</td>
              </tr>
              <tr>
                <td>U</td>
                <td>Registered (Unrecognised) Party</td>
              </tr>
              <tr>
                <td>Z</td>
                <td>Independent</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
@endsection