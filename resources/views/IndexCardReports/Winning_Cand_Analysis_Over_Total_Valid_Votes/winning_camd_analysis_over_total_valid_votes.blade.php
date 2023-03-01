@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Winning Candidate Analysis Over Total Valide Votes-2019')
@section('content')


<style> 

th{
  text-align: center;
}
</style>
<?php  $st=getstatebystatecode($user_data->st_code);   ?>

<section class="">
 <div class="container-fluid">
 <div class="row">
 <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
     <div class=" card-header">
     <div class=" row">
           <div class="col"><h4>Winning Candidate Analysis Over Total Valid Votes, 2019<br>(Political Party Wise Deposits Forfeited-2019)</h4></div>
             <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
              </p>
               <p class="mb-0 text-right">
                      <a href="Winning-candidate-analysis-over-total-valid-votesPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
       <a href="" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
               </p>
             </div>


           </div>
     </div>

<div class="card-body">

    <div class="table-responsive">


                <?php //echo '<pre>'; print_r($statewisedata); die; ?>
                <div class="">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                          <tr class="table-primary">
                            <th scope="col">Name of State/UT</th>
                            <th scope="col">No. Of Seats</th>
                            <th colspan="8">No. Of Candidates Secured The % Of Votes Over The Total Electors In The Constituency</th>

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
        <tbody>
          @foreach($datanew as $value)

             <tr>
              <td>{{$value['ST_NAME']}}</td>
              <td>{{$value['TotalSeats']}}</td>
              <td><?php echo ($value['count']!=0)?$value['count']['10']:0; ?></td>
              <td><?php echo ($value['count']!=0)?$value['count']['20']:0; ?></td>
              <td><?php echo ($value['count']!=0)?$value['count']['30']:0; ?></td>
              <td><?php echo ($value['count']!=0)?$value['count']['40']:0; ?></td>
              <td><?php echo ($value['count']!=0)?$value['count']['50']:0; ?></td>
              <td><?php echo ($value['count']!=0)?$value['count']['60']:0; ?></td>
              <td><?php echo ($value['count']!=0)?$value['count']['70']:0; ?></td>
              <td><?php echo ($value['count']!=0)?$value['count']['80']:0; ?></td>
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
