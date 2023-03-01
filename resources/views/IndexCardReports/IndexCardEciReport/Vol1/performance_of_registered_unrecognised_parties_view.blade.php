@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Performance of registered unrecognised parties')
@section('content')
<style>
    @font-face {
        font-family: 'poppins';
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
        font-family: 'poppins';

    }

    tr {
        text-align: left;
    }

    td {
        font-size: 14px !important;
        font-weight: 500 !important;
        color: #4a4646 !important;
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
        font-family: 'poppins';

    }



    th {
        background: #959798;
        color: #fff !important;
        text-align: center;

        font-size: 14px;
    }

 
    </style>
<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
    <div class="container-fluid">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(Performance of registered unrecognised parties)</h4></div> 
                        <div class="col">
                            <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
                            </p>
                            <p class="mb-0 text-right">
                            <a href="perRegUnPartyPdf" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
                            <a href="perRegUnPartyCsv" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" style="width: 100%;">
                        
                            <thead>
                                <tr class="table-primary">
                                    <th>Party Name</th>
                                    <th colspan="3">Candidates</th>
                                    <th>Votes secured by Party</th>
                                    <th colspan="2">% of votes secured</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>Contested</th>
                                    <th>Won</th>
                                    <th>DF</th>
                                    <th></th>
                                    <th>Over total Electors in State</th>
                                    <th>Over total Valid Votes Polled in State</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($result as $value)
                                <?php 
                                    $overTotElec = ($value->TotalElectorsState)?round((($value->totalvotesparty/$value->TotalElectorsState)*100),2):0;
                                    $overTotValVotes = ($value->v_votes_evm_all)?round((($value->totalvotesparty/$value->v_votes_evm_all)*100),2):0;
                                 ?>
                                <tr>
                                    <td> {{$value->PARTYNAME}} </td>
                                    <td> {{$value->c_nom_co_t}} </td>
                                    <td> NA </td>
                                    <td> NA </td>
                                    <td> {{$value->totalvotesparty}} </td>
                                    <td> {{$overTotElec}} </td>
                                    <td> {{$overTotValVotes}} </td>
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


