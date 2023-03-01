@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Political Party Wise Deposits Fordeited-2019')
@section('content')
<?php  $st=getstatebystatecode($user_data->st_code);   ?>

<section class="">
 <div class="container-fluid">
 <div class="row">
 <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
     <div class=" card-header">
     <div class=" row">
           <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(19 - Political Party Wise Deposit Forfeited)
</h4></div>
             <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All State</span> &nbsp;&nbsp; <b></b>
              </p>
               <p class="mb-0 text-right">
                      <a href="Political_party_Wise_Deposits_ForfeitedPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
                      <a href="Political_party_Wise_Deposits_ForfeitedXLS" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
               </p>
             </div>


           </div>
     </div>

<div class="card-body">

    <div class="table-responsive">
      <table class="table table-bordered table-striped" style="width: 100%;">
         <thead>

            <tr class="table-primary">
               <th>State/UT</th>
               <th>No. Of Seats</th>
               <th colspan="5">Total No. Of Candidates</th>
               <th colspan="5">Total No. Of Elected Candidates</th>
               <th colspan="5">Total No. Of Candidates with Forfeiture Of Deposit</th>
            </tr>
            <tr>
               <td></td>
               <td></td>
               <th>N</th>
               <th>S</th>
               <th>U</th>
               <th>I</th>
               <th>TOT</th>
               <th>N</th>
               <th>S</th>
               <th>U</th>
               <th>I</th>
               <th>TOT</th>
               <th>N</th>
               <th>S</th>
               <th>U</th>
               <th>I</th>
               <th>TOT</th>
            </tr>
         </thead>

         <tbody>
      <?php

          $totalseats = $totalN = $totalS = $totalU = $totalZ = $totalCon = $totwinN
            = $totwinS = $totwinU = $totwinZ = $totwinelected
            = $totalfdN = $totalfdS = $totalfdU = $totalfdZ = $totalfdTOT = 0;

      ?>
      @foreach($statewisedata as  $value)

            <tr>
               <td>{{$value->ST_NAME}}</td>
               <td>{{$value->TotalSeats}}</td>
               <td>{{$value->N}}</td>
               <td>{{$value->S}}</td>
               <td>{{$value->U}}</td>
               <td><?php echo $value->Z+$value->Z1 ?></td>
               <td><?php echo $value->N+$value->S+$value->U+$value->Z+$value->Z1; ?></td>
               <td>{{$value->totalwinner->N}}</td>
               <td>{{$value->totalwinner->S}}</td>
               <td>{{$value->totalwinner->U}}</td>
               <td><?php echo $value->totalwinner->Z+$value->totalwinner->Z1 ?></td>
               <td><?php echo $value->totalwinner->Z+$value->totalwinner->Z1+$value->totalwinner->U+$value->totalwinner->S+$value->totalwinner->N ?></td>
               <td>{{$value->totalfd->N}}</td>
               <td>{{$value->totalfd->S}}</td>
               <td>{{$value->totalfd->U}}</td>
               <td>{{$value->totalfd->Z}}</td>
               <td>{{$value->totalfd->FDT}}</td>
            </tr>

            <?php
            $totalseats += $value->TotalSeats;
            $totalN += $value->N;
            $totalS += $value->S;
            $totalU += $value->U;
            $totalZ += $value->Z+$value->Z1;
            $totalCon += $value->N+$value->S+$value->U+$value->Z+$value->Z1;
            $totwinN += $value->totalwinner->N;
            $totwinS += $value->totalwinner->S;
            $totwinU += $value->totalwinner->U;
            $totwinZ += $value->totalwinner->Z+$value->totalwinner->Z1;
            $totwinelected += $value->totalwinner->Z+$value->totalwinner->Z1+$value->totalwinner->U+$value->totalwinner->S+$value->totalwinner->N;
            $totalfdN += $value->totalfd->N;
            $totalfdS += $value->totalfd->S;
            $totalfdU += $value->totalfd->U;
            $totalfdZ += $value->totalfd->Z;
            $totalfdTOT += $value->totalfd->FDT;
            ?>

         @endforeach

         <tr>
            <td><b>Grand Total</b></td>
            <td><b>{{$totalseats}}</b></td>
            <td><b>{{$totalN}}</b></td>
            <td><b>{{$totalS}}</b></td>
            <td><b>{{$totalU}}</b></td>
            <td><b>{{$totalZ}}</b></td>
            <td><b>{{$totalCon}}</b></td>
            <td><b>{{$totwinN}}</b></td>
            <td><b>{{$totwinS}}</b></td>
            <td><b>{{$totwinU}}</b></td>
            <td><b>{{$totwinZ}}</b></td>
            <td><b>{{$totwinelected}}</b></td>
            <td><b>{{$totalfdN}}</b></td>
            <td><b>{{$totalfdS}}</b></td>
            <td><b>{{$totalfdU}}</b></td>
            <td><b>{{$totalfdZ}}</b></td>
            <td><b>{{$totalfdTOT}}</b></td>
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
