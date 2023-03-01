@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Number of Candidates Per Constituency - Phase General Elections')

@section('content')
<?php  $st=getstatebystatecode($user_data->st_code);   ?>
<section class="">
	<div class="container">
		<div class="row">
		<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(1. No. OF Constituency)</h4></div>
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All india</span> &nbsp;&nbsp; <b></b>
							</p>
							<p class="mb-0 text-right">
							<a href="{{'highlights-pdf'}}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="{{'highlights-excel'}}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important; display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>

				<div class="card-body">
					<div class="table-responsive">


  <table class="table table-bordered table-responsive tablecenterreport" style="width: 100%;">
  <tr>
      <th colspan="10" style="background: #4da1ed;">1. No. OF Constituency</th>
  </tr>
  <tr>
      <th colspan="4">Type Of Constituency</th>
      <th>GEN</th>
      <th>SC</th>
      <th>ST</th>
      <th colspan="2">Total</th>
  </tr>

  <tr>
      <th colspan="4">Number Of Constituency</th>
      <td>{{$contestents['genpc']}}</td>
      <td>{{$contestents['scpc']}}</td>
      <td>{{$contestents['stpc']}}</t>
          <td colspan="2">{{$contestents['totalwinnermale']}}</td>
      </tr>

      <tr>
          <th colspan="9" style="background:#4da1ed;">2. No. OF Contestants</th>
      </tr>
      <tr>
          <th>No of Contstants in a Constituency</th>
          <th>1</th>
          <th>2</th>
          <th>3</th>
          <th>4</th>
          <th>5</th>
          <th>6-10</th>
          <th>11-15</th>
          <th>Above 15</th>
      </tr>
      <tr>
          <th>Number Of Such Constituency</th>
          <th>{{$contestents['one']}}</th>
          <th>{{$contestents['two']}}</th>
          <th>{{$contestents['three']}}</th>
          <th>{{$contestents['four']}}</th>
          <th>{{$contestents['five']}}</th>
          <th>{{$contestents['fiveten']}}</th>
          <th>{{$contestents['tenfifteen']}}</th>
          <th>{{$contestents['fifteen']}}</th>
      </tr>
          <tr>
              <th colspan="6">Total Contestants in a Fray</th>
              <td colspan="3">{{$contestents['Total_Candidates']}}</td>
          </tr>

          <tr>
              <th colspan="6">Average contestants per constituency</th>
              <td colspan="3">{{$contestents['Avg']}}</td>
          </tr>
          <tr>
              <th colspan="6">Minimum contestants in a constituency</th>
              <td colspan="3">{{$contestents['mincnd']}}</td>
          </tr>
          <tr>
              <th colspan="6">Maximum contestants in a constituency</th>
              <td colspan="3">{{$contestents['maxcnd']}}</td>
          </tr>
          <tr>
              <th colspan="9" style="background:#4da1ed;">3. Electors</th>
          </tr>
          <tr>
              <th colspan="4"></th>
              <th>Male</th>
              <th>Female</th>
              <th>Third Gender </th>
              <th colspan="2">Total</th>
          </tr>
          <tr>
              <th colspan="4">i. Number of Electors</th>
              <td>{{$contestents['maleElectors']}}</td>
              <td>{{$contestents['femaleElectors']}}</td>
              <td>{{$contestents['thirdElectors']}}</td>
              <td colspan="2">{{$contestents['totalElectors']}}</td>
              </tr>
              <tr>
                  <th colspan="4">ii. No. of electors who
                  voted</th>
                  <td>{{$contestents['totalMaleVoters']}}</td>
                  <td>{{$contestents['totalFemaleVoters']}}</td>
                  <td>{{$contestents['totalOtherVoters']}}</td>
                  <td colspan="2">{{$contestents['totalMaleVoters']+$contestents['totalFemaleVoters']+$contestents['totalOtherVoters']}}</td>
                  </tr>
                  <tr>
                      <th colspan="4">iii. Polling Percentage</th>
                      <td>{{round($contestents['totalMaleVoters']/$contestents['maleElectors'] * 100,2)}}</td>
                      <td>{{round($contestents['totalFemaleVoters']/$contestents['femaleElectors'] * 100,2)}}</td>
                      <td>{{round($contestents['totalOtherVoters']/$contestents['thirdElectors'] * 100,2)}}</t>
                      <td colspan="2">{{round($contestents['totalMaleVoters']/$contestents['maleElectors'] * 100,2) +round($contestents['totalFemaleVoters']/$contestents['femaleElectors'] * 100,2) + round($contestents['totalOtherVoters']/$contestents['thirdElectors'] * 100,2)}}</td>
                      </tr>
                      <tr>
                          <th colspan="6" style="background:#4da1ed;">4. No. Of  Service Electors</th>
                          <td colspan="3"></td>
                      </tr>
                      <tr>
                          <th colspan="6">Male</th>
                          <td colspan="3">{{$contestents['maleServiceElector']}}</td>
                      </tr>
                      <tr>
                          <th colspan="6">Female</th>
                          <td colspan="3">{{$contestents['femaleServiceElector']}}</td>
                      </tr>
                      <tr>
                          <th colspan="6" style="background:#4da1ed;">5. Number of Postal Ballot Received</th>
                          <td colspan="3">{{$contestents['total_postal_vote_received']}}</td>
                      </tr>
                      <tr>
                          <th colspan="6" style="background:#4da1ed;">6. Poll %(including postal Ballot)</th>
                          <?php $total = $contestents['total_evm_vote']+$contestents['total_postal_vote_received'];?>
                          <td colspan="3">{{round($total/$contestents['totalElectors']*100,2)}}</td>
                      </tr>
                      <tr>
                          <th colspan="6" style="background:#4da1ed;">7. Number of valid votes</th>
                          <td colspan="3"></td>
                      </tr>
                      <tr>
                          <th colspan="6">Valid votes polled on EVM</th>
                          <td colspan="3">{{$contestents['total_evm_vote']}}</td>
                      </tr>
                      <tr>
                          <th colspan="6">Valid Postal Votes</th>
                          <td colspan="3">{{$contestents['total_valid_postal_vote']}}</td>
                      </tr>
                      <tr>
                          <th colspan="6" style="background:#4da1ed;">8. Total NOTA Votes
                          </th>
                          <td colspan="3"></td>
                      </tr>
                      <tr>
                          <th colspan="6">NOTA Votes on EVM
                          </th>
                          <td colspan="3">{{$contestents['evmnota']}}</td>
                      </tr>
                      <tr>
                          <th colspan="6">NOTA Votes on postal Ballot
                          </th>
                          <td colspan="3">{{$contestents['postalnota']}}</td>
                      </tr>
                      <tr>
                          <th colspan="9" style="background:#4da1ed;">9. No. of votes rejected</th>
                          <td colspan="3"></td>
                      </tr>
                      <tr>
                          <th colspan="6" style="">postal</th>
                          <td colspan="3">{{$contestents['rejected_postal_vote']}}</td>
                      </tr>
                      <tr>
                          <th colspan="6" style="">Votes not retrieved on evm</th>
                          <td colspan="3">{{$contestents['votes_not_retreived_from_evm']}}</td>
                      </tr>
                      <tr>
                          <th colspan="6" style="">Votes rejected due to other reason(at polling station)</th>
                          <td colspan="3">{{$contestents['rejected_votes_due_2_other_reason']}}</td>
                      </tr>
                      <tr>
                          <th colspan="6" style="background:#4da1ed;">10.Tendered Votes</th>
                          <td colspan="3">{{$contestents['tended_votes']}}</td>
                      </tr>
                      <tr>
                          <th colspan="6" style="background:#4da1ed;">11. proxy Votes</th>
                          <td colspan="3">{{$contestents['proxy_votes']}}</td>
                      </tr>
                      <tr>
                          <th colspan="6" style="background:#4da1ed;">12. no of polling Station</th>
                          <td colspan="3">{{$contestents['totalpollingstation']}}</td>
                      </tr>
                      <tr>
                          <th colspan="6" style="background:#4da1ed;">13. Average no. of electors per polling station</th>
                          <td colspan="3">{{round($contestents['totalElectors']/$contestents['totalpollingstation']*100,2)}}</td>
                      </tr>
                      <tr>
                          <th colspan="6" style="background:#4da1ed;">14. No. of repolls held</th>
                          <td colspan="3">{{$contestents['rejected_votes_due_2_other_reason']}}</td>
                      </tr>
                      <tr>
                          <th colspan="9" style="background:#4da1ed;">15. performance of contesting candidates</th>
                      </tr>
                      <tr>
                          <th colspan="4"></th>
                          <th>Male</th>
                          <th>Female</th>
                          <th>Third Gender</th>
                          <th colspan="2">Total</th>
                      </tr>
                      <tr>
                          <th colspan="4">i. No. Of Constenstents</th>
                          <td>12</td>
                          <td>12</td>
                          <td>12</td>
                          <td colspan="2">12</td>
                      </tr>
                      <tr>
                          <th colspan="4">ii. Elected</th>
                          <td>{{$contestents['totalwinnermale']}}</td>
                          <td>{{$contestents['totalwinnerfemale']}}</td>
                          <td>{{$contestents['totalwinnerthird']}}</td>
                          <td colspan="2">{{$contestents['totalwinnermale']+$contestents['totalwinnerfemale']+$contestents['totalwinnerthird']}}</td>
                      </tr>
                      <tr>
                          <th colspan="4">iii. Forfeighted Deposits</th>
                          <td>{{$contestents['fdmale']}}</td>
                          <td>{{$contestents['fdfemale']}}</td>
                          <td>{{$contestents['fdthird']}}</td>
                          <td colspan="2">{{$contestents['fdtotal']}}</td>
                      </tr>
                  </table>
                </table>
              </section>
              @endsection
