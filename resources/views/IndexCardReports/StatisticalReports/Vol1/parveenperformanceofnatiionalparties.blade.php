@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Performance of national parties - Phase General Elections')
@section('content')

<?php $st = getstatebystatecode($user_data->st_code); ?>
<section class="">
    <div class="container-fluid">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(20.Performance of national Parties)</h4></div>
                        <div class="col">
                                <!--<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
                                </p>-->
                            <p class="mb-0 text-right">
                                <a href="Performance-of-national-parties-pdf" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
                                <!--<a href="{{'#'}}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>-->
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="">
                            <tr>
                                <th scope="col">Party name</th>
                                <th scope="col" colspan="3">Candidate</th>
                                <th scope="col">Votes</th>
                                <th scope="col" colspan="2">% of Votes Secured</th>
                            </tr>

                            <tr>
                                <th scope="col"></th>
                                <th scope="col">Contested</th>
                                <th scope="col">Won</th>
                                <th scope="col">DF</th>
                                <th scope="col">Votes Secured by Party</th>
                                <th scope="col">Over total electors</th>
                                <th scope="col">Over total valid votes polled</th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php
                           $totalcontested = 0;
                           $won = 0;
                           $fd = 0;
                           $secure = 0;
                           $electorspercent = 0;
                           $overtotalvaliedpercent = 0;
                           ?>
                            @foreach($data as $rows)

							@php
								$peroverelectors = ($rows->total_vote/$totalElectors[0]->total_electors)*100;
								$perovervoter = ($rows->total_vote/$totalVotes[0]->totalVotes)*100;
							@endphp
							
                            <tr>
                                <td>{{$rows->partyname}}</td>
                                <td>{{$rows->contested}}</td>
                                <td>{{$rows->won}}</td>
                                <td>{{$rows->fd}}</td>
                                <td>{{$rows->total_vote}}</td>
                                <td>{{round($peroverelectors,2)}}</td>
                                <td>{{round($perovervoter,2)}}</td>
                                <?php

                                 $totalcontested += $rows->contested;
                                $won += $rows->won;
                                $fd += $rows->fd;
                                $secure += $rows->total_vote;
                                $electorspercent += $peroverelectors;
                                $overtotalvaliedpercent += $perovervoter;

                                        ?>
                            </tr>
                        @endforeach
                            <tr><td>Total</td>
                            <td>{{$totalcontested}}</td>
                             <td>{{$won}}</td>
                           <td>{{$fd}}</td>
                           <td>{{$secure}}</td>
                           <td>{{round($electorspercent,2)}}</td>
                           <td>{{round($overtotalvaliedpercent,2)}}</td>
                            </tr>

                        </tbody>

                    </table>

<tr>TOTAL ELECTORS IN THE COUNTRY (INCLUDING SERVICE - ELECTORS) -<span>{{$totalElectors[0]->total_electors}}</span></tr><br>
<tr>TOTAL VALID VOTES POLLED IN THE COUNTRY (INCLUDING SERVICE-VOTES -<span>{{$totalVotes[0]->totalVotes}}</span></tr>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
