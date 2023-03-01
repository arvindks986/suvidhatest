@extends('admin.layouts.pc.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'State Wise Electors')
@section('content')
<style>
    .reportsection {
        text-align: center;
    }


    td {
        font-size: 14px !important;
    }

    .blc {
        background: #000;
        color: #fff;
    }

    .headerreport h2 {
        background: #005aab;
        color: #ffff;
        padding: 10px;
        text-transform: capitalize;
        border-radius: 10px;
        font-size: 22px;

    }

    .bordertestreport {
        text-align: center;
        border: 1px solid #ddd;
        padding: 30px;
        background-image: url(../images/grid.png);
        background: #005aab08;
        background-repeat: repeat;
    }

    .headerreport h4 {
        text-transform: capitalize;
        font-size: 18px;
        font-family: 'poppinsregular';

    }



    .headerreport {
        margin: auto;
    }

    .tablecenterreport td {
        font-size: 15px;
    }

    span.devil {
        float: right;
        padding: 11px;
        font-size: 17px;
        position: relative;
        right: 16%;
        border: 2px dotted #4da1ed;
        background: #4da1edab;
        color: #fff;
    }

    .gry {
        background: #666;
    }

    td {
        text-align: center;
    }

    div#example_wrapper {
        position: relative;
        top: 40px;
    }

    .pagination {
        margin: 10px 0px;
    }

    div#example_paginate {
        top: -16px;
        position: relative;
    }

    .rightfl {
        display: inline-block;
        float: right;
        word-spacing: -1px;
    }

    .contituency {
        text-align: left;
        padding: 4px;
        font-size: 17px;
        font-weight: bold;
        display: inline-block;
        padding: 0px 60px;
    }


    th {
        background: #4da1ed;
        color: #fff !important;
        text-align: center;
                font-size: 14px;

    }

    tr:nth-child(even) {
        background: #8e99ab29;

    }

    td.dev {
        text-align: center;
    }

    img.img-responsivesreport {
        height: 130px;
        margin: auto;
        position: relative;
        display: block;
    }


    .contituency span {
        padding: 6px 46px !important;
        background: #4da1ed;
        color: #fff;
        text-decoration: none !important;
    }
    </style>
    <div class="headerreport">
    	<div class="pull-right exportdiv"> 
	        <a href="StateWiseNoofElectorsPDF" class="btn show pdfbut"><img src="assets/images/pdficon.png"></a>
	        <a href="StateWiseNoofElectorsCSV" class="btn  show pdfbut"><img src="assets/images/excel.png"></a>
	   </div>
    <div class="container">
        <div class="bordertestreport">
            <img src="https://eci.gov.in/uploads/monthly_2018_11/cyber-security-logo.png.36764059053d6b18e50e5c809ed6caaa.png" class="img-responsivesreport" alt="">
            <h4>Election Commission of India, General Elections, 2014 (16th LOK SABHA )</h4>
            <h2>STATE WISE NUMBER OF ELECTORS</h2>
            <table class="table table-bordered table-responsive" style="width: 100%;table-layout: auto;">
                <thead>
                    <th>State/UT</th>
                    <th colspan="4">General</th>
                    <th colspan="4">Service</th>
                    <th colspan="3">NRIs</th>
                    <th colspan="3">Grand</th>
                </thead>
                <tbody>

                    <tr>
                        <th></th>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Third Gender</th>
                        <th>Total</th>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Third Gender</th>
                        <th>Total</th>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Total</th>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Total</th>
                    </tr>
                    @foreach($sResult as $value)
                    <tr>
                        <th class="gry">{{$value->st_name}}</th>
                        <td>{{$value->gen_male}}</td>
                        <td>{{$value->gen_female}}</td>
                        <td>{{$value->gen_other}}</td>
                        <td>{{$value->gen_total}}</td>
                        <td>{{$value->ser_male}}</td>
                        <td>{{$value->ser_female}}</td>
                        <td>{{$value->ser_other}}</td>
                        <td>{{$value->ser_total}}</td>
                        <td>{{$value->nri_male}}</td>
                        <td>{{$value->nri_female}}</td>
                        <td>{{$value->nri_total}}</td>
                        <td>{{$value->total_male}}</td>
                        <td>{{$value->total_female}}</td>
                        <td>{{$value->total_male_female}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection