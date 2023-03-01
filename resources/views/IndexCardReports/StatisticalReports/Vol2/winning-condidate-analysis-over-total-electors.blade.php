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
                                                <div class="col"><h4> Elections Commission Of India, General Elections, {{getElectionYear()}}<br>(31 - Winning candidate analysis over total electors)</h4></div> 


                        <div class="col">
                                <!--<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
                                </p>-->
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
                                    <th scope="col">Name of State/UT</th>
                                    <th scope="col">No. Of Seats</th>
                                    <th colspan="8">No. Of Candidates Secured The  % Of <br>Votes Over The Total Electors In The Constituency</th>

                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>

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
                                @forelse($arrayData as $values)


                                <tr>
                                    <td>{{$values['stname']}}</td>
                                    <td>{{$values['totalseat']}}</td>
                                    <td><?php echo ($values['count'] != 0) ? $values['count']['10'] : 0; ?></td>
                                    <td><?php echo ($values['count'] != 0) ? $values['count']['20'] : 0; ?></td>
                                    <td><?php echo ($values['count'] != 0) ? $values['count']['30'] : 0; ?></td>
                                    <td><?php echo ($values['count'] != 0) ? $values['count']['40'] : 0; ?></td>
                                    <td><?php echo ($values['count'] != 0) ? $values['count']['50'] : 0; ?></td>
                                    <td><?php echo ($values['count'] != 0) ? $values['count']['60'] : 0; ?></td>
                                    <td><?php echo ($values['count'] != 0) ? $values['count']['70'] : 0; ?></td>
                                    <td><?php echo ($values['count'] != 0) ? $values['count']['80'] : 0; ?></td>


                                </tr>
                                @empty
                                <tr>
                                    <td>Data Not Found</td>

                                </tr>
                                @endforelse
                                </tbody>


                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
