@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Party Wise Seats Won And Valid Polled - 2019')
@section('content')
<?php  $st=getstatebystatecode($user_data->st_code);   ?>
<section class="">
 <div class="container">
 <div class="row">
 <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
     <div class=" card-header">
     <div class=" row">
           <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>((Party Wise Seats Won And Valid Polled ))</h4></div>
             <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
              </p>
               <p class="mb-0 text-right">
                      <a href="downloadstatewiseseatwonvalidvotes" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
                      <a href="downloadstatewiseseatwonvalidvotesXLS" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
               </p>
             </div>


           </div>
     </div>

<div class="card-body">

    <div class="table-responsive">
      <table class="table table-bordered table-striped" style="width: 100%;">
              <thead>


                <tr class="table-primary">

                  <th>State Name </th>
                  <th>Party Type</th>
                  <th>Party Name</th>
                  <th>Total Valid Votes Polled in the State</th>
                  <th>Total Electors in the State</th>
                  <th>Seats Won</th>
                  <th>Total Valid Votes Polled by Party</th>
                  <th>% Valid Votes Polled By Party</th>
                </tr>



              </thead>

      <tbody>
             @foreach($datanew as $value)

                <tr>

                  <th>{{$value->st_name}}</th>
                  <th>{{$value->lead_party_type}}</th>
                  <th>{{$value->lead_cand_party}}</th>
                  <th>{{$value->v_votes_evm_all}}</th>
                  <th>{{$value->e_all_t}}</th>
                  <th>{{$value->seatwon}}</th>
                  <th>{{$value->totalvotebyparty}}</th>

                  <th><?php  echo round($value->totalvotebyparty/$value->v_votes_evm_all *100,2);?></th>



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
