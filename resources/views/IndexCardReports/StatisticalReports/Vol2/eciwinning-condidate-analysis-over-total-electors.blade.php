@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Winning candidate analysis over total electors - Phase General Elections')
@section('content')


<style>

th{
    text-align: center;
}
</style>

<?php $st=getstatebystatecode($user_data->st_code);   ?>
<section class="">
    <div class="container">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(31 - WINNING CANDIDATES ANALYSIS OVER TOTAL ELECTORS)</h4></div>


                        <div class="col">
                                
                            <p class="mb-0 text-right">
                                <a href="{{'winning-condidate-analysisover-elector-pdf'}}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
                                <a href="{{'winning-condidate-analysisover-elector-xls'}}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" style="width: 100%;">
                             <thead>
                  <tr class="table-primary">
                  <th scope="col" rowspan="2">Name of State/UT</th>
                  <th scope="col" rowspan="2">No. Of <br> Seats</th>
                  <th colspan="8">No. Of Candidates Secured The % Of Votes Over The Total Electors In The Constituency
</th>

                </tr>
                <tr>
                                      <th>Winner with <= 10%</th>
                    <th>Winner with >10% to <= 20%</th>
                    <th>Winner with >20% to <=30%</th>
                    <th>Winner with >30% to <=40%</th>
                    <th>Winner with >40% to <=50%</th>
                    <th>Winner with >50% to <=60%</th>
                    <th>Winner with >60% to <=70%</th>
                    <th>Winner with > 70%</th>
</tr>
                </thead>
			@php
			$all_total_sheet = $zero_to_10 = $one_to_20 = $two_to_30 = $three_to_40 = $four_to_50 = $five_to_60 = $six_to_70 = $seven_to_80 = 0;
			@endphp

             @forelse($arrayData as $values)
            <tr>
                <td>{{$values->st_name}}</td>
                <td>{{$values->Total_Sheet}}</td>
                <td>{{$values->zero_to_10}}</td>
                <td>{{$values->one_to_20}}</td>
                <td>{{$values->two_to_30}}</td>
                <td>{{$values->three_to_40}}</td>
                <td>{{$values->four_to_50}}</td>
                <td>{{$values->five_to_60}}</td>
                <td>{{$values->six_to_70}}</td>
                <td>{{$values->seven_to_80}}</td>

            </tr>
			
			
			@php
			$all_total_sheet += $values->Total_Sheet;
			$zero_to_10 += $values->zero_to_10;
			$one_to_20 += $values->one_to_20;
			$two_to_30 += $values->two_to_30;
			$three_to_40 += $values->three_to_40;
			$four_to_50 += $values->four_to_50;
			$five_to_60 += $values->five_to_60;
			$six_to_70 += $values->six_to_70;
			$seven_to_80 += $values->seven_to_80;
			@endphp
						
            @empty
            <tr>
                <td>Data Not Found</td>
            </tr>
            @endforelse
				
			<tr>
                <td><b>Total Seats</b></td>
                <td><b>{{$all_total_sheet}}</b></td>
                <td><b>{{$zero_to_10}}</b></td>
                <td><b>{{$one_to_20}}</b></td>
                <td><b>{{$two_to_30}}</b></td>
                <td><b>{{$three_to_40}}</b></td>
                <td><b>{{$four_to_50}}</b></td>
                <td><b>{{$five_to_60}}</b></td>
                <td><b>{{$six_to_70}}</b></td>
                <td><b>{{$seven_to_80}}</b></td>
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
