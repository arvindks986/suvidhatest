@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'State Wise Overseas Electors Voters-2019')
@section('content')
<?php  $st=getstatebystatecode($user_data->st_code);   ?>

<section class="">
 <div class="container">
 <div class="row">
 <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
     <div class=" card-header">
     <div class=" row">
           <div class="col"><h4>24.State Wisse Overseas Electors Voters, 2019</h4></div>
             <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
              </p>
               <p class="mb-0 text-right">
                      <a href="State-Wise-Overseas-Electors-VotersPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
       <a href="" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
               </p>
             </div>


           </div>
     </div>

<div class="card-body">

    <div class="table-responsive">
      @foreach($statewisedata as  $key => $value)
     <div class="">
        <div class="table-responsive">
           <table class="table table-bordered table-striped" style="width: 100%;">
              <thead>
                 <tr>
                    <th style="font-size: 17px;">State : <span style="color: #fff; font-style: normal;font-weight: bold; text-decoration: underline;"> {{$key}}</span> </th>
                 </tr>
                 <tr class="table-primary">
                    <th scope="col">PC Type</th>
                    <th colspan="4">Electors</th>
                    <th colspan="4">Voters</th>
                 </tr>
              </thead>
              <tbody>

                    <td></td>
                    <td>Male</td>
                    <td>Female</td>
                    <td>Other</td>
                    <td>Total Electors</td>
                    <td>Male</td>
                    <td>Female</td>
                    <td>Other</td>
                    <td>Total Voters</td>
                 </tr>
                 <tr>
                     @foreach($value as $k)
                 <tr>
                    <?php if($k['GENSEATS'] != 0)
                                  $seats = $k['GENSEATS'];
                              else if($k['SCSEATS'] != 0)
                                  $seats = $k['SCSEATS'];
                              else $seats = $k['STSEATS'];
                    ?>
                    <td>{{$k['pc_type']}}</td>
                    <td>{{$k['maletotalnrielector']}}</td>
                    <td>{{$k['femaletotalnrielector']}}</td>
                    <td>{{$k['othertotalnrielector']}}</td>
                    <td>{{$k['totalnrielector']}}</td>
                    <td>{{$k['votermalenritotal']}}</td>
                    <td>{{$k['voterfemalenritotal']}}</td>
                    <td>{{$k['voterothernritotal']}}</td>
                    <td>{{$k['voterallnritotal']}}</td>
                 </tr>

              </tbody>
              @endforeach
           </table>
           @endforeach
    </div>
</div>
</div>
</div>
</div>
</section>


@endsection
