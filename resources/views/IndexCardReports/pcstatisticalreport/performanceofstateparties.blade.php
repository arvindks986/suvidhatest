@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Performance of State Parties')
@section('content')

<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
    <div class="container">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(21.Performance of State Parties)</h4></div> 
                        <div class="col">
                            <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
                            </p>
                            <p class="mb-0 text-right">
                            <a href="downloadperformanceofstateparties" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
                            <a href="#" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
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