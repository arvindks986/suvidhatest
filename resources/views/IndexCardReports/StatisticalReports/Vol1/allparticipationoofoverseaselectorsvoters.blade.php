@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'State Wise Participation of Overseas Electors Voters - Phase General Elections')
@section('content')

<?php $st = getstatebystatecode($user_data->st_code); ?>
<section class="">
    <div class="container">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(State Wise Participation of Overseas Electors Voters)</h4></div>
                        <div class="col">
                                <!--<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
                                </p>-->
                            <p class="mb-0 text-right">
                                <a href="{{'All-State-wise-overseas-electors-voters-pdf'}}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
                                <a href="{{'#'}}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important; display: table-row;"></a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" style="width: 100%;">
                          <?php $sate = array();

                          ?>
                            @forelse($data as $key=>$rows)

                            <thead>

                         <?php $arrayq = (array)($rows);  if(!in_array($arrayq['st_name'], $sate)) { ?>

                          <tr>
                              <th style="font-size: 17px;">State : <span style="color: #fff; font-style: normal;font-weight: bold; text-decoration: underline;"></span> {{$rows->st_name}} </th>
                          </tr>


                                <tr class="table-primary">
                                    <th scope="col">PC Type</th>
                                    <th colspan="4">Electors</th>
                                    <th colspan="4">Voters</th>
                                </tr>
<?php } $sate[] = $arrayq['st_name'];?>
                            </thead>

                            <tbody>
                              <?php $arrayq = (array)($rows);  if(!in_array($arrayq['st_name'], $sate)) { ?>
                                <tr>
                                    <td></td>
                                    <td>Male</td>
                                    <td>Female</td>
                                    <td>Other</td>
                                    <td>Total Electors</td>
                                    <td>Male</td>
                                    <td>Female</td>
                                    <td>Other</td>
                                    <td>Total Electors</td>
                                </tr>
                                
                           <?php } $sate[] = $arrayq['st_name'];?>
                                <tr>
                                    <td>{{$rows->PC_TYPE}}</td>
                                    <td>{{$rows->emale}}</td>
                                    <td>{{$rows->efemale}}</td>
                                    <td>{{$rows->eother}}</td>
                                    <td>{{$rows->etotal}}</td>

                                    <td>{{$rows->nri_male_voters}}</td>
                                    <td>{{$rows->nri_female_voters}}</td>
                                    <td>{{$rows->evm_vote}}</td>
                                    <td>{{$rows->total_vote}}</td>
                                </tr>



                                </tr>
                                @empty
                                <tr>
                                    <td>Data not found</td>
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
