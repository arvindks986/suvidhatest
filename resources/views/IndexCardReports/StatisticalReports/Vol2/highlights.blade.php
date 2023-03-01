@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Highlights')

@section('content')

<style>
  th, td{
    text-transform: uppercase;
  }

  .dev2{
    text-transform: capitalize;
  }

</style>
<?php  $st=getstatebystatecode($user_data->st_code);   ?>
<section class="">
	<div class="container">
		<div class="row">
		<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>( 2 - Highlights )</h4></div>
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All india</span> &nbsp;&nbsp; <b></b>
							</p>
							<p class="mb-0 text-right">
							<a href="{{'highlights-pdf'}}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="{{'highlights-excel'}}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 63px !important; display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>

				<div class="card-body">
					<div class="table-responsive">


  <table class="table table-bordered" style="width: 100%;table-layout: fixed;">
  <tr>
    <th>1.</th>
      <th colspan="9">No. of Constituencies</th>
  </tr>
  <tr>
      <td colspan="6">Type Of Constituency</td>
      <td>GEN</td>
      <td>SC</td>
      <td>ST</td>
      <td colspan="">Total</td>
  </tr>

  <tr>
      <td colspan="6">No Of Constituencies</td>
      <td>{{$contestents['genpc']}}</td>
      <td>{{$contestents['scpc']}}</td>
      <td>{{$contestents['stpc']}}</td>
          <td colspan="">{{$contestents['genpc']+$contestents['scpc']+$contestents['stpc']}}</td>
      </tr>

      <tr>
        <th>2.</th>
          <th colspan="9">NO. of Contestants</th>
      </tr>
      <tr>
          <td colspan="2">NO. of Contestants in a Constituency</td>
          <td>1</td>
          <td>2</td>
          <td>3</td>
          <td>4</td>
          <td>5</td>
          <td>6-10</td>
          <td>11-15</td>
          <td>Above 15</td>
      </tr>
      <tr>
          <td colspan="2">NO Of Such Constituency</td>
          <td>{{$contestents['one']}}</td>
          <td>{{$contestents['two']}}</td>
          <td>{{$contestents['three']}}</td>
          <td>{{$contestents['four']}}</td>
          <td>{{$contestents['five']}}</td>
          <td>{{$contestents['fiveten']}}</td>
          <td>{{$contestents['tenfifteen']}}</td>
          <td>{{$contestents['fifteen']}}</td>
      </tr>
          <tr>
              <td colspan="8">Total Contestants in a Fray</td>
              <td colspan="2">{{$contestents['Total_Candidates']}}</td>
          </tr>

          <tr>

              <td colspan="8">Average Contestants Per Constituency</td>
              <td colspan="2">{{$contestents['Avg']}}</td>
          </tr>
          <tr>

              <td colspan="8">Minimum Contestants in a Constituency</td>
              <td colspan="2">{{$contestents['maxcnd']}}</td>
          </tr>
          <tr>

              <td colspan="8">Maximum Contestants in a Constituency</td>
              <td colspan="2">{{$contestents['mincnd']}}</td>
          </tr>
          <tr>
            <th>3.</th>
              <th colspan="9">Electors</th>
          </tr>
          <tr>
              <td colspan="6"></td>
              <td colspan="">Male</td>
              <td colspan="">Female</td>
              <td colspan="">Others </td>
              <td colspan="">Total</td>
          </tr>
          <tr>
            <td>i.</td>
              <td  class="dev2" colspan="5">NO. OF ELECTORS(including service electors)</td>
              <td colspan="">{{$contestents['maleElectors']}}</td>
              <td colspan="">{{$contestents['femaleElectors']}}</td>
              <td colspan="">{{$contestents['thirdElectors']}}</td>
              <td colspan="">{{$contestents['totalElectors']}}</td>
              </tr>
              <tr>
                <td>ii.</td>
                  <td colspan="5"> Number of Electors Who
                  Voted at polling stations</td>
                  <td colspan="">{{$contestents['totalMaleVoters']}}</td>
                  <td colspan="">{{$contestents['totalFemaleVoters']}}</td>
                  <td colspan="">{{$contestents['totalOtherVoters']}}</td>
                  <td colspan="">{{$contestents['totalMaleVoters']+$contestents['totalFemaleVoters']+$contestents['totalOtherVoters']}}</td>
                  </tr>
                  <tr>
                    <td>iii. </td>
                      <td colspan="5">Polling Percentage (EXCLUDE POSTAL BALLOT)</td>
                      <td colspan="">{{round($contestents['totalMaleVoters']/$contestents['maleElectors'] * 100,2)}}</td>
                      <td colspan="">{{round($contestents['totalFemaleVoters']/$contestents['femaleElectors'] * 100,2)}}</td>
                      <td colspan="">{{round($contestents['totalOtherVoters']/$contestents['thirdElectors'] * 100,2)}}</td>
                      <td colspan="">{{round(($contestents['totalMaleVoters']+$contestents['totalFemaleVoters']+$contestents['totalOtherVoters'])/$contestents['totalElectors']*100,2)}}</td>
                      </tr>
                      <tr>
                        <th>4.</th>
                          <th colspan="9"> No. Of  Service Electors</th>
                      </tr>
                      <tr>
                                                <td>i.</td>

                          <td colspan="7">Male</td>
                          <td colspan="2">{{$contestents['maleServiceElector']}}</td>
                      </tr>
                      <tr>
                                                <td>ii.</td>

                          <td colspan="7">Female</td>
                          <td colspan="2">{{$contestents['femaleServiceElector']}}</td>
                      </tr>
                      <tr>
                        <th>5.</th>
                          <td colspan="7"> No. of Postal Ballot Received</td>
                          <td colspan="2">{{$contestents['total_postal_vote_received']}}</td>
                      </tr>
                      <tr>
                        <th>6.</th>
                          <td colspan="7">Poll % (including Postal Ballot)</td>
                          <?php $total = $contestents['totalMaleVoters']+$contestents['totalFemaleVoters']+
                          $contestents['totalOtherVoters']+$contestents['total_postal_vote_received'];?>
                          <td colspan="2">{{round($total/$contestents['totalElectors']*100,2)}}</td>
                      </tr>
                      <tr>
                        <th>7.</th>
                          <th colspan="9">No. of Valid Votes</th>
                      </tr>
                      <tr>
                                                <td>i.</td>

                          <td colspan="7"> Valid Votes Polled on EVM</td>
                          <td colspan="2">{{($contestents['totalMaleVoters']+$contestents['totalFemaleVoters']+$contestents['totalOtherVoters'])-($contestents['votes_not_retreived_from_evm']+$contestents['rejected_votes_due_2_other_reason']+$contestents['evmnota'])}}</td>
                      </tr>
                      <tr>
                                                <td>ii.</td>

                          <td colspan="7"> Valid Postal Votes</td>
                          <td colspan="2">{{($contestents['total_postal_vote_received'])-($contestents['postalnota']+$contestents['rejected_postal_vote'])}}</td>
                      </tr>
                      <tr>
                        <th>8.</th>
                          <th colspan="9"> Total NOTA Votes
                          </th>
                      </tr>
                      <tr>
                                                <td>i.</td>

                          <td colspan="7"> 'NOTA' Votes on EVM
                          </td>
                          <td colspan="2">{{$contestents['evmnota']}}</td>
                      </tr>
                      <tr>
                                                <td>ii.</td>

                          <td colspan="7"> 'NOTA' Votes On Postal Ballot
                          </td>
                          <td colspan="2">{{$contestents['postalnota']}}</td>
                      </tr>
                      <tr>
                        <th>9.</th>
                          <th colspan="9">No. of Votes Rejected</th>
                      </tr>
                      <tr>
                        <td>i.</td>
                          <td colspan="7" style="">Postal</td>
                          <td colspan="2">{{$contestents['rejected_postal_vote']}}</td>
                      </tr>
                      <tr>
                        <td>ii.</td>
                          <td colspan="7" style=""> Votes Not Retrieved On EVM</td>
                          <td colspan="2">{{$contestents['votes_not_retreived_from_evm']}}</td>
                      </tr>
                      <tr>
                        <td>iii.</td>
                          <td colspan="7" style="">Votes Rejected Due to Other Reason(at Polling Station)</td>
                          <td colspan="2">{{$contestents['rejected_votes_due_2_other_reason']}}</td>
                      </tr>
                      <tr>
                        <th>10.</th>
                          <td colspan="7">Tendered Votes</td>
                          <td colspan="2">{{$contestents['tended_votes']}}</td>
                      </tr>
                      <tr>
                        <th>11.</th>
                          <td colspan="7">Proxy Votes</td>
                          <td colspan="2">{{$contestents['proxy_votes']}}</td>
                      </tr>
                      <tr>
                        <th>12.</th>
                          <td colspan="7">No. of Polling Station</td>
                          <td colspan="2">{{$contestents['totalpollingstation']}}</td>
                      </tr>
                      <tr>
                        <th>13.</th>
                          <td colspan="7"> Average No. of Electors Per Polling Station</td>
                          <td colspan="2"><p>{{round(($contestents['totalElectors'])/$contestents['totalpollingstation'],0)}}</p> </td>
                      </tr>
                      <tr>
                        <th>14.</th>
                          <td colspan="7">No. of Re-polls Held</td>
                          <td colspan="2">{{$contestents['total_repoll']}}</td>
                      </tr>
                      <tr>
                        <th>15.</th>
                          <th colspan="9">Performance of Contesting Candidates</th>
                      </tr>
                      <tr>
                          <td colspan="6"></td>
                          <td>Male</td>
                          <td>Female</td>
                          <td>Others</td>
                          <td colspan="">Total</td>
                      </tr>

                   
                      <tr>
                        <td colspan="">i. </td>
                          <td colspan="5">No. Of Contestants</td>
                          <td>{{$contestents['totalnominatedmale']}}</td>
                          <td>{{$contestents['totalnominatedfemale']}}</td>
                          <td>{{$contestents['totalnominatedthird']}}</td>
                          <td>{{$contestents['totalnominatedmale']+$contestents['totalnominatedfemale']+$contestents['totalnominatedthird']}}</td>
                      </tr>
                      <tr>
                        <td>ii. </td>
                          <td colspan="5">Elected</td>
                          <td>{{$contestents['totalwinnermale']}}</td>
                          <td>{{$contestents['totalwinnerfemale']}}</td>
                          <td>{{$contestents['totalwinnerthird']}}</td>
                          <td colspan="">{{$contestents['totalwinnermale']+$contestents['totalwinnerfemale']+$contestents['totalwinnerthird']}}</td>
                      </tr>
                      <tr>
                        <td>iii.</td>
                          <td colspan="5"> Forfeited Deposits</td>
                          <td>{{$contestents['fdmale']}}</td>
                          <td>{{$contestents['fdfemale']}}</td>
                          <td>{{$contestents['fdthird']}}</td>
                          <td colspan="">{{$contestents['fdtotal']}}</td>
                      </tr>
                  </table>


                </div>
              </div>




</div>

</div>

</div>





              </section>
              @endsection
