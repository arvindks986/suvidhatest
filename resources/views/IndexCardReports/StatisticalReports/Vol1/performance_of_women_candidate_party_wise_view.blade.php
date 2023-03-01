@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'The Schedule of GE to Lok Sabha 2019 - Phase General Elections')
@section('content')
<?php  $st=getstatebystatecode($user_data->st_code);?>
<style>
    @font-face {
        font-family: 'poppinsregular';
        src: url('../fonts/poppins-regular-webfont.woff2') format('woff2'),
            url('../fonts/poppins-regular-webfont.woff') format('woff');
        font-weight: normal;
        font-style: normal;

    }


    .text-centerdev {
        position: relative;
        left: 20px;
        top: 61px;

    }

    body {
        padding-bottom: 20px;
        background: url(./assets/images/grid.png);
        font-family: 'poppinsregular';

    }

    tr {
        text-align: center;
    }

    td {
        font-size: 14px !important;
        font-weight: 500 !important;
        color: #4a4646 !important;
    }

th{
    vertical-align: bottom !important;
}

    .headerreport h2 {
        background: #959798;
        color: #ffff;
        padding: 5px;
        text-transform: capitalize;
        border-radius: 10px;
        font-size: 19px;

    }

    .bordertestreport {
        border: 1px solid #ddd;
        padding: 30px 0px;
        background-image: url(../images/grid.png);
        background: #fff;
        background-repeat: repeat;
    }

    .headerreport h4 {
        text-transform: capitalize;
        font-size: 18px;
        font-family: 'poppinsregular';

    }



    th {
        background: #959798;
        color: #fff !important;
        text-align: center;

        font-size: 14px;
    }

    tr:nth-child(even) {
        background: #8e99ab29;

    }

    </style>
<section class="">
    <div class="container">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(Performance OF Women Elector In Poll)</h4></div> 
                        <div class="col">
                            <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
                            </p>
                            <p class="mb-0 text-right">
                            <a href="perWomenStatePartPdf" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
                            <a href="perWomenStatePartCsv" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 53px !important;"></a>
                            </p>
                        </div>
                    </div>
                </div>
<div class="headerreport">
    <div class="container-fluid">
        <div class="bordertestreport">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-3" style="position: relative;top: -17px;">
                        <img src="https://suvidha.eci.gov.in/theme/img/logo/eci-logo.png" class="img-responsive" style="width:100px !important;" alt="">
                    </div>
                    <div class="col-sm-9" style="display: grid;justify-content: right;">
                        <h4 style="font-size: 21px;text-align: center; font-family: 'poppinsregular';">Election Commission Of India, General Elections, 2019</h4>
                        <h5 style="font-family: 'poppinsregular'; font-weight: bold;text-align: center; font-size: 17px;text-decoration: underline;">( Participation of Women in State Parties)</h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <hr>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-6">
                        <!--                 <strong>GENERAL ELECTIONS - INDIA, 2019</p> 
 -->
                    </div>
                    <div class="col-sm-6" style="display: grid;justify-content: right;">
                        <p>(Year : 2019)</p>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="">
                    <table class="table table-bordered table-striped" style="width: 100%;table-layout: fixed;">
                        <thead>
                            <tr>
                                <!--                                     <th colspan="2" style="font-size: 17px;">State : <span style="color: #fff; font-style: normal;font-weight: bold; text-decoration: underline;"> Chandigarh</span> </th>
 -->
                            </tr>
                            <tr class="table-primary">

                                <tr>
                                    <th colspan="5">Candidates </th>
                                    <th colspan="2">Percentage </th>
                                    <th rowspan="2">Votes Secured By Women Candidates </th>
                                    <th rowspan="2">Votes Secured By Party In State</th>
                                    <th colspan="3">% of votes secured</th>

                                </tr>

                                <tr>

                                    <th>Party</th>
                                    <th>State</th>
                                    <th>Contested</th>
                                    <th>Won</th>
                                    <th>DF</th>
                                    <th>Won</th>
                                    <th>DF</th>
                                    <th>Over total electors in the State</th>
                                    <th>Over total valid votes in the State</th>
                                    <th>Over Votes secured by the party in State</th>
                                </tr>

                        </thead>
                        <tbody>
                            @foreach($aaData as $k => $key)
                            <?php
                                $contestedTot = $votes_securedTot = $totalvoteTot = $OverTotElecTot = $OverTotVotTot = $OverStVotesTot = 0;
                            ?>
                            @foreach($key as $v => $value)
                                <tr>
                                    <td>{{$k}}</td>
                                    <td>{{$v}}</td>
                                    <td>{{$value->contested}}</td>
                                    <td>NA</td>
                                    <td>NA</td>
                                    <td>NA</td>
                                    <td>NA</td>
                                    <td>{{$value->votes_secured}}</td>
                                    <td>{{$value->totalvote}}</td>
                                    <td>{{$value->OverTotElec}}</td>
                                    <td>{{$value->OverTotVot}}</td>
                                    <td>{{$value->OverStVotes}}</td>
                                </tr>
                                <?php
                                    $contestedTot += $value->contested;
                                    $votes_securedTot += $value->votes_secured;
                                    $totalvoteTot += $value->totalvote;
                                    $OverTotElecTot += $value->OverTotElec;
                                    $OverTotVotTot += $value->OverTotVot;
                                    $OverStVotesTot += $value->OverStVotes;
                                ?>
                                <?php //echo "<pre>"; print_r($value); die; ?>
                            @endforeach
                                <tr>
                                    <td colspan="2"><b>Grand Total</b></td>
                                    <td>{{$contestedTot}}</td>
                                    <td>NA</td>
                                    <td>NA</td>
                                    <td>NA</td>
                                    <td>NA</td>
                                    <td>{{$votes_securedTot}}</td>
                                    <td>{{$totalvoteTot}}</td>
                                    <td>{{$OverTotElecTot}}</td>
                                    <td>{{$OverTotVotTot}}</td>
                                    <td>{{$OverStVotesTot}}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection