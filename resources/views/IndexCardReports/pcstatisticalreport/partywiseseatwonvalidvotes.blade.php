@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Party Wise Seats Won And Valid Polled - 2019')
@section('content')
<?php  $st=getstatebystatecode($user_data->st_code);   ?>




<style>
	
	th{
		font-size: 13px !important;
    color: #fff;
	}
</style>
<section class="">
 <div class="container">
 <div class="row">
 <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
     <div class=" card-header">
     <div class=" row">
           <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(18 - Party Wise Seat Won & Valid Votes Polled  in Each State )</h4></div>
             <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All State</span> &nbsp;&nbsp; <b></b>
              </p>
               <p class="mb-0 text-right">
                      <a href="downloadpartywiseseatwonvalidvotes" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
                      <a href="downloadpartywiseseatwonvalidvotesXLS" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
               </p>
             </div>


           </div>
     </div>

<div class="card-body">

    <div class="table-responsive">
      <table class="table table-bordered table-striped" style="width: 100%;table-layout: fixed;">
              <thead>
@forelse($datanew as $partywiseseatwon)

                <tr>
                    
                    <th colspan="2" style="font-size: 14px !important;">Party Name : <span style=" font-style: normal;"> {{$partywiseseatwon['partyname']}}</span> </th>
                    <th>Party Type: {{$partywiseseatwon['leadtypename']}}</th>
                </tr>
                   <tr class="">
                  
                  <th>State Name</th>
                  <th>Total Valid Votes <br>   Polled in the State</th>
                  <th>Total Electors <br>  in the State</th>
                  <th>Seats <br> Won</th>
                  <th>Total Valid Votes <br>  Polled by Party</th>
                  <th>% Valid Votes <br>Polled By Party</th>
                </tr>
                
             
 
</thead>

<tbody>
     @foreach($partywiseseatwon['partdetails'] as $rowdata)
                <tr>
                   

                  
                  <td>{{$rowdata['stname']}}</td>
                  <td>{{$rowdata['evmvote']}}</td>
                  <td>{{$rowdata['electroll']}}</td>
                  <td>{{$rowdata['wonseat']}}</td>
                  <td>{{$rowdata['totalvotebyparty']}}</td>
                  <?php if($rowdata['evmvote']) { ?>
                  <td><?php  echo round($rowdata['totalvotebyparty']/$rowdata['evmvote'] *100,2);?></td>
                  <?php } else{ ?>
                  <td>0</td>
                  <?php } ?>

                  
                </tr>

                <tr style="height: 10px;"></tr>
@endforeach
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