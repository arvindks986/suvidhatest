@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'State Wise Candidate Data Summary-2019')
@section('content')
<?php  $st=getstatebystatecode($user_data->st_code);   ?>

<section class="">
 <div class="container-fluid">
 <div class="row">
 <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
     <div class="card-header">
     <div class="row">

        <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(6.State Wise Candidate Data Summary-2019)</h4></div> 

             <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
              </p>
               <p class="mb-0 text-right">
                      <a href="StateWiseCandidateDataSummaryPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
       <a href="" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important; display: table-row;"></a>
               </p>
             </div>


           </div>
     </div>

<div class="card-body">

    <div class="table-responsive">
      @foreach($statewisedata as  $key => $value)
                 
                <?php //echo '<pre>'; print_r($statewisedata); die; ?>
                <div class="">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" style="width: 100%;margin: 8px 0px 5px 0px;">
                            <thead>

                                <tr>

                                    <th colspan="5" style="font-size: 17px;">State : <span style="color: #fff; font-style: normal;font-weight: bold; text-decoration: underline;">{{$key}}</span> </th>
                                </tr>
                                <tr class="table-primary">
                                    <th scope="col">Constituency Type</th>
                                    <th scope="col">No Of Seats</th>
                                    <th colspan="4">Nominations Filled</th>
                                    <th colspan="4">Nominations Rejected</th>
                                    <th colspan="4">Nominations Withdrawn</th>
                                    <th colspan="4">Contesting Candidates</th>
                                    <th colspan="4">Deposits Forfeited</th>

                                </tr>

                            </thead>

                            <tbody>
                                <tr>

                                    <td></td>
                                    <td></td>
                                    <td>Men</td>
                                    <td>Women</td>
                                    <td>Other</td>
                                    <td>Total</td>

                                    <td>Men</td>
                                    <td>Women</td>
                                    <td>Other</td>
                                    <td>Total</td>

                                    <td>Men</td>
                                    <td>Women</td>
                                    <td>Other</td>
                                    <td>Total</td>

                                    <td>Men</td>
                                    <td>Women</td>
                                    <td>Other</td>
                                    <td>Total</td>

                                    <td>Men</td>
                                    <td>Women</td>
                                    <td>Other</td>
                                    <td>Total</td>

                                </tr>
                                <?php $cnomfdtotal = $cnomfdother = $cnomfdfemale = $cnomfdmale = $cnomcototal = $cnomcother = $cnomcofemale = $cnomcomale = $cnomwtotal = $cnomwother = $cnomwfemale = $cnomwmale = $cnomrall = $cnomrother = 
                                $cnomrfemale = $cnomrmale = $CandNomall = $CandNomOther = $CandNomFemale = $candNomMale = $totSeat = 0; ?>
                                @foreach($value as $k)
                                <tr>
                                    <?php if($k['GENSEATS'] != 0)
                                            $seats = $k['GENSEATS'];
                                        else if($k['SCSEATS'] != 0)
                                            $seats = $k['SCSEATS'];
                                        else $seats = $k['STSEATS'];
                                    ?>
                                    <td>{{$k['pc_type']}}</td>
                                    <td>{{$seats}}</td>
                                    <td>{{$k['CandNommale']}}</td>
                                    <td>{{$k['CandNomFemale']}}</td>
                                    <td>{{$k['CandNomOther']}}</td>
                                    <td>{{$k['CandNomall']}}</td>
                                    <td>{{$k['cnomrmale']}}</td>
                                    <td>{{$k['cnomrfemale']}}</td>
                                    <td>{{$k['cnomrother']}}</td>
                                    <td>{{$k['cnomrall']}}</td>
                                    <td>{{$k['cnomwmale']}}</td>
                                    <td>{{$k['cnomwfemale']}}</td>
                                    <td>{{$k['cnomwother']}}</td>
                                    <td>{{$k['cnomwtotal']}}</td>
                                    <td>{{$k['cnomcomale']}}</td>
                                    <td>{{$k['cnomcofemale']}}</td>
                                    <td>{{$k['cnomcother']}}</td>
                                    <td>{{$k['cnomcototal']}}</td>
                                    <td>{{$k['cnomfdmale']}}</td>
                                    <td>{{$k['cnomfdfemale']}}</td>
                                    <td>{{$k['cnomfdother']}}</td>
                                    <td>{{$k['cnomfdtotal']}}</td>
                                </tr>

                                <?php $totSeat += $seats;
                                    $candNomMale += $k['CandNommale'];
                                    $CandNomFemale += $k['CandNomFemale'];
                                    $CandNomOther  += $k['CandNomOther'];
                                    $CandNomall   += $k['CandNomall'];
                                    $cnomrmale += $k['cnomrmale'];
                                    $cnomrfemale += $k['cnomrfemale'];
                                    $cnomrother  += $k['cnomrother'];
                                    $cnomrall   += $k['cnomrall'];
                                    $cnomwmale  += $k['cnomwmale'];
                                    $cnomwfemale += $k['cnomwfemale'];
                                    $cnomwother += $k['cnomwother'];
                                    $cnomwtotal += $k['cnomwtotal'];
                                    $cnomcomale += $k['cnomcomale'];
                                    $cnomcofemale += $k['cnomcofemale'];
                                    $cnomcother += $k['cnomcother'];
                                    $cnomcototal += $k['cnomcototal'];
                                    $cnomfdmale += $k['cnomfdmale'];
                                    $cnomfdfemale += $k['cnomfdfemale'];
                                    $cnomfdother += $k['cnomfdother'];
                                    $cnomfdtotal += $k['cnomfdtotal'];
                                    ?>
                                @endforeach

                                <tr style="font-weight:bold;">

                                    <td>Total</td>
                                    <td>{{$totSeat}}</td>
                                    <td>{{$candNomMale}}</td>
                                    <td>{{$CandNomFemale}}</td>
                                    <td>{{$CandNomOther}}</td>
                                    <td>{{$CandNomall}}</td>
                                    <td>{{$cnomrmale}}</td>
                                    <td>{{$cnomrfemale}}</td>
                                    <td>{{$cnomrother}}</td>
                                    <td>{{$cnomrall}}</td>
                                    <td>{{$cnomwmale}}</td>
                                    <td>{{$cnomwfemale}}</td>
                                    <td>{{$cnomwother}}</td>
                                    <td>{{$cnomwtotal}}</td>
                                    <td>{{$cnomcomale}}</td>
                                    <td>{{$cnomcofemale}}</td>
                                    <td>{{$cnomcother}}</td>
                                    <td>{{$cnomcototal}}</td>
                                    <td>{{$cnomfdmale}}</td>
                                    <td>{{$cnomfdfemale}}</td>
                                    <td>{{$cnomfdother}}</td>
                                    <td>{{$cnomfdtotal}}</td>
                                </tr>
                               
                                    </tbody>
                        </table>
                       
                         @endforeach
    </div>
</div>
</div>
</div>
</div>
</section>


@endsection




