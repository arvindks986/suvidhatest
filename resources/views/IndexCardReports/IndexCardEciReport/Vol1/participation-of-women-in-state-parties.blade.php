@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'PARTICIPATION OF WOMEN IN STATE PARTIES')
@section('content')

<style>
  .table th{
    vertical-align: middle;
  }
</style>
<?php // $st=getstatebystatecode($user_data->st_code);   ?>
<section class="">
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(27 - PARTICIPATION OF WOMEN IN STATE PARTIES )</h4></div>
						<div class="col">

							<p class="mb-0 text-right">
							<a href="ParticipationofWomenInStatePartiesPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="ParticipationofWomenInStatePartiesXls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative;width: 61px !important;"></a>
							</p>
						</div>
					</div>
				</div>

				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped" style="width: 100%;table-layout: fixed;">

                               <tr>
                                    <th rowspan="2" class="blc" style="text-align: left;">State</th>
                                    <th colspan="3" style="text-decoration: underline;">Candidates </th>
                                    <th colspan="2" style="text-decoration: underline;">Percentage </th>
                                    <th rowspan="2" class="blc">Votes secured by <br> women<br> candidate</th>
                                    <th rowspan="2" class="blc">Votes <br>secured<br> by party<br> in state</th>
                                    <th colspan="3" style="text-decoration: underline;">% of Votes Secured </th>
                            </tr>
                               <tr>
                                <th class="blc">Contested</th>
                                <th class="blc">Won </th>
                                <th class="blc">DF</th>
                                <th class="blc">Won</th>
                                <th class="blc">DF</th>


                                <th class="blc">Over total <br>electors in<br>the state</th>
                                <th class="blc">Over total <br>valid votes<br> in the state</th>
                                <th class="blc">Over votes <br>secured by <br>the party in <br>state</th>
                            </tr>

                            <?php if($datanew) { ?>
                              <tbody>

                            @php
                            $grandtotalcontested = $grandtwon = $grandtfd = $grandtotalelectorsstate
                            = $grandOVER_ALL_TOTAL_VOTE_state = $grandsecure = $grandtotalVoteSecuredbyparty
                            = $grandperoverelectorstotal = $grandoverTotalValidVotestotal = $grandovsbptotal
                            = $grandtotalvalid_valid_vote = 0;
                            @endphp


                            @foreach($datanew as $key => $value)

                            @php
                             $totalcontested = $twon = $won= $fd =  $secure = $electorspercent = $overtotalvaliedpercent = $ovsbp= $tfd = $totalVoteSecuredbyparty = $totalElectors  = $tvv = $totalelectorsstate = $totalvalid_valid_vote = $OVER_ALL_TOTAL_VOTE_state = 0;
                            @endphp

                            <tr>
                                <td colspan="11"><b>{{$key}}</b></td>
                              </tr>
                            <tr>


                            @foreach($value as $key1 => $value1)




                              @php
                                $peroverelectors = ($value1['votes_secured_by_Women']/$value1['sum_of_total_eelctors'])*100;

                                $overTotalValidVotes = ($value1['votes_secured_by_Women']/$value1['OVER_ALL_TOTAL_VOTE_state'])*100;

                                $ovsbp = ($value1['votes_secured_by_Women']/$value1['totalvalid_valid_vote'])*100;
                              @endphp







                                <td>{{$key1}}</td>
                                <td>{{$value1['contested']}}</td>
                                <td>{{$value1['WON']}}</td>
                                <td>{{$value1['DF']}}</td>
                                <td>{{round((($value1['WON']/$value1['contested'])*100),2)}}</td>
                                <td>{{round((($value1['DF']/$value1['contested'])*100),2)}}</td>
                                <td>{{$value1['votes_secured_by_Women']}}</td>
                                <td>{{$value1['totalvalid_valid_vote']}}</td>
                                <td>{{round($peroverelectors,2)}}</td>
                                <td>{{round($overTotalValidVotes,2)}}</td>
                                <td>{{round($ovsbp,2)}}</td>

                              @php

                                $totalcontested += $value1['contested'];
                                $twon += $value1['WON'];
                                $tfd += $value1['DF'];
                                $secure += $value1['votes_secured_by_Women'];
                                $totalVoteSecuredbyparty += $value1['totalvalid_valid_vote'];

                                $totalelectorsstate += $value1['sum_of_total_eelctors'];
                                $totalvalid_valid_vote += $value1['totalvalid_valid_vote'];
                                $OVER_ALL_TOTAL_VOTE_state += $value1['OVER_ALL_TOTAL_VOTE_state'];


                              @endphp



                            </tr>





                        @endforeach

                              @php
                                $peroverelectorstotal = round(($secure/$totalelectorsstate)*100,2);

                                $overTotalValidVotestotal = round(($secure/$OVER_ALL_TOTAL_VOTE_state)*100,2);

                                $ovsbptotal = round(($secure/$totalvalid_valid_vote)*100,2);
                              @endphp

                        <tr>

                              <th><b>Party Total</b></th>
                              <td><b>{{$totalcontested}}</b></td>
                              <td><b>{{$twon}}</b></td>
                              <td><b>{{$tfd}}</td>
                              <td><b>{{round((($twon/$totalcontested)*100),2)}}</b></td>
                              <td><b>{{round((($tfd/$totalcontested)*100),2)}}</b></td>
                              <td><b>{{$secure}}</b></td>
                              <td><b>{{$totalVoteSecuredbyparty}}</b></td>
                              <td><b>{{$peroverelectorstotal}}</b></td>
                              <td><b>{{$overTotalValidVotestotal}}</b></td>
                              <td><b>{{$ovsbptotal}}</b></td>

                        </tr>



                        <?php

                            $grandtotalcontested += $totalcontested;
                            $grandtwon += $twon;
                            $grandtfd += $tfd;
                            $grandtotalelectorsstate += $totalelectorsstate;
                            $grandOVER_ALL_TOTAL_VOTE_state += $OVER_ALL_TOTAL_VOTE_state;
                            $grandsecure += $secure;
                            $grandtotalVoteSecuredbyparty += $totalVoteSecuredbyparty;
                            $grandperoverelectorstotal += $peroverelectorstotal;
                            $grandoverTotalValidVotestotal += $overTotalValidVotestotal;
                            $grandovsbptotal += $ovsbptotal;
                            $grandtotalvalid_valid_vote += $totalvalid_valid_vote;

                        ?>

                        @php
                          $grandperoverelectorstotal = round(($grandsecure/$grandtotalelectorsstate)*100,2);

                          $grandoverTotalValidVotestotal = round(($grandsecure/$grandOVER_ALL_TOTAL_VOTE_state)*100,2);

                          $grandovsbptotal = round(($grandsecure/$grandtotalvalid_valid_vote)*100,2);
                        @endphp

                        @endforeach


                        <tr>

                              <th><b>Grand Total</b></th>
                              <td><b>{{$grandtotalcontested}}</b></td>
                              <td><b>{{$grandtwon}}</b></td>
                              <td><b>{{$grandtfd}}</b></td>
                              <td><b>{{round((($grandtwon/$grandtotalcontested)*100),2)}}</b></td>
                              <td><b>{{round((($grandtfd/$grandtotalcontested)*100),2)}}</b></td>
                              <td><b>{{$grandsecure}}</b></td>
                              <td><b>{{$grandtotalVoteSecuredbyparty}}</b></td>
                              <td><b>{{$grandperoverelectorstotal}}</b></td>
                              <td><b>{{$grandoverTotalValidVotestotal}}</b></td>
                              <td><b>{{$grandovsbptotal}}</b></td>
                        </tr>



                        </tbody>

                      <?php } else { ?>

                        <tbody>
                          <td></td>
                          <td colspan="10" class="text-center"><b>No Data Found</b></td>
                        </tbody>

                      <?php } ?>
</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
