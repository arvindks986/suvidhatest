@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Successfull candidate - Phase General Elections')
<?php $st = getstatebystatecode($user_data->st_code); ?> 
@section('content')


<style>
    
    tr:nth-child(even){
        background: #eee;
    }
</style>

<section class="">
    
<div class="container-fluid">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                
                 <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(List Of Successful Candidate)</h4></div> 
                        <div class="col">
                               <p class="mb-0 text-right"><b class="bolt">All India</b> <span class="badge badge-info">All State</span> &nbsp;&nbsp; <b></b> 
                                </p>
                            <p class="mb-0 text-right">
                                <a href="{{'Eci-list-of-successfull-candidate-pdf'}}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
<!--                                Eci-list-of-successfull-candidatexls-->
                                <a href="{{'#'}}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
                            </p>
                        </div>
                    </div>
                </div>
                
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" style="width: 100%;table-layout: fixed;">
                        <thead class="">
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">CONSTITUENCY</th>
                                <th scope="col">WINNER</th>
                                <th scope="col">PARTY</th>
                                <th scope="col">PARTY SYMBOL</th>
                                <th scope="col">MARGIN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($arraydata as $allsuccessfullcondidate)
                            <tr>
                                <th>{{$allsuccessfullcondidate['state']}}</th>
                            <tr>
                                @foreach($allsuccessfullcondidate['pc'] as  $catwise)
                            <tr>
                                <td>{{$catwise['Pc_Name']}}</td>
                                <td>{{$catwise['PC_TYPE']}}</td>
                                <td>{{$catwise['Cand_Name']}}</td>
                                <td>{{$catwise['Party_Abbre']}}</td>
                                <td>{{$catwise['Party_symbol']}}</td>
                                <td> {{$catwise['margin']}} {{$catwise['percent']}}</td>
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

</section>
@endsection
