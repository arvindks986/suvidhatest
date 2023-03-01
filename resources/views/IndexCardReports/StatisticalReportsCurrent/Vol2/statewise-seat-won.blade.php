@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'State Wise Seat Won & Valid Votes Polled by Political PartiesVotes')
@section('content')

<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<style>
	th{
		color: #fff;
	}
	td{
		color: #666;
	}
</style>
<section class="">
	<div class="container-fluid">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(17 - State Wise Seat Won & Valid Votes Polled by Political PartiesVotes)</h4></div> 
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
							</p>
							<p class="mb-0 text-right">
							<a href="StatewiseSeatWonPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="#" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>
				
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped" style="width: 100%;">
						
              <thead>
                <tr class="">
                  <th scope="col">State Name</th>
                  <th scope="col">Party Type</th>
                  <th scope="col">Party NAme</th>
                  <th>Total Valid Votes Polled in the State</th>
                  <th>Total Electors in the State</th>
                  <th>Seats Won</th>
                  <th>Total Valid Votes Polled by Party</th>
                  <th>% Valid Votes Polled by Party</th>
           
                </tr>
 <?php 
               //dd($totalelectors);
               ?>
                @forelse($data as $row)
               <?php
                $validvotepolledbyparty=0;
				if($totalelectors->totalvalidvotes!=0)
				{
                $validvotepolledbyparty=round(((($row->totalvotes/$totalelectors->totalvalidvotes)*100)),2);
				}

                ?>
                <tr style="background: #fff !important;">
                     
                    <td>{{$stname}}</td>
                     <td>{{$row->PARTYTYPE}}</td>
                     <td>{{$row->PARTYABBRE}}</td>
                     <td>{{$totalelectors->totalvalidvotes}}</td>
                     <td>{{$totalelectors->totalelectors}}</td>
                     <td>{{$row->wonseat}}</td>
                     <td>{{$row->totalvotes}}</td>                     
                     <td>{{$validvotepolledbyparty}}</td>




</tr> 
@empty
<tr>
    <td colspan="8">Result No Found</td>
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
