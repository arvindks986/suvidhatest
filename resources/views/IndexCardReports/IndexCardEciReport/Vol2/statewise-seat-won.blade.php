@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'State Wise Seat Won & Valid Votes Polled by Political Parties')
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
						<div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(17 - State Wise Seat Won & Valid Votes Polled  by Political Parties)</h4></div> 
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">All India</b> <span class="badge badge-info">{{--$st->ST_NAME--}}</span> &nbsp;&nbsp; <b></b> 
							</p>
							<p class="mb-0 text-right">
							<a href="StatewiseSeatWonPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="StatewiseSeatWonXls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
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
                  <th scope="col">PartyType</th>
                  <th scope="col">Party Name</th>
                  <th>Total Valid Votes <br> Polled in the State</th>
                  <th>Total Electors in the State</th>
                  <th>Seats Won</th>
                  <th>Total Valid Votes <br> Polled by Party</th>
                  <th>% Valid Votes <br> Polled by Party</th>
           
                </tr>
                <?php //echo'<pre>';print_r($data);die;?>
 			    @forelse($getuserrecord as $row)
               <?php
                $validvotepolledbyparty=0;
               
				if($row->totalvalid_st_vote!=0)
				{
                $validvotepolledbyparty= ROUND((($row->totalvalidvote/$row->totalvalid_st_vote)*100),2);
				}

                ?>
                <tr style="background: #fff !important;">
                     
                    <td>{{$row->ST_NAME}}</td>
                     <td>{{$row->PARTYTYPE}}</td>
                     <td>{{$row->PARTYNAME}}</td>
                     <td>{{$row->totalvalid_st_vote}}</td>
                     <td>{{$row->totaleelctors}}</td>                     
                     	<td>{{$row->win}}</td>                    
                     <td>{{$row->totalvalidvote}}</td>                     
                     <td>{{$validvotepolledbyparty}}</td>




</tr> 
@empty
<tr>
    <td colspan="8">Result No Found</td>
</tr>     
@endforelse
{{-- $getuserrecord->links()--}}
</tbody>
           </table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
