@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Participation of Women in National Parties')
@section('content')

<?php // $st=getstatebystatecode($user_data->st_code);   ?>
<section class="">
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(26 - PARTICIPATION OF WOMEN IN NATIONAL PARTIES
)</h4></div>
						<div class="col">

							<p class="mb-0 text-right">
							<a href="ParticipationofWomenInNationalPartiesPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="ParticipationofWomenInNationalPartiesXls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>

				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped" style="width: 100%;">
                            <thead>

                                <tr class="table-primary">

                                <tr>
                                    <th rowspan="2">Party Name </th>
                                    <th colspan="3">Candidates </th>
                                    <th colspan="2">Percentage </th>
                                    <th rowspan="2">Votes Secured By Women Candidates </th>
                                   <th colspan="3">% of votes secured</th>

                                </tr>


                             <tr>

                                 <th>Contested</th>
								 <th>Won</th>
                                 <th>DF</th>
                                 <th>Won</th>
                                 <th>DF</th>
                                 <th>Over total electors</th>
                                  <th>Over total valid votes</th>
                                 <th>Over Votes secured by the party</th>
                             </tr>


                            </thead>
															<?php if($data) { ?>
                              <tbody>
                             @php
                             $totalcontested = $twon = $won= $fd =  $secure = $electorspercent = $overtotalvaliedpercent = $ovsbp= $tfd = $totalVoteSecured = $totalElectors  = $tvv = 0;
                            @endphp
                            @foreach($data as $rows)
                              @php
                                $peroverelectors = ($rows->votes_secured_by_Women/$rows->electrols_Total)*100;

                                $overTotalValidVotes = ($rows->votes_secured_by_Women/$rows->OVER_ALL_TOTAL_VOTE)*100;

                                $ovsbp = ($rows->votes_secured_by_Women/$rows->totalvalid_valid_vote)*100;
                              @endphp

                            <tr>
                                <td>{{$rows->partyabbre}}</td>
                                <td>{{$rows->contested}}</td>
                                <td>{{$rows->WON}}</td>
                                <td>{{$rows->DF}}</td>
                                <td>{{round((($rows->WON/$rows->contested)*100),2)}}</td>
                                <td>{{round((($rows->DF/$rows->contested)*100),2)}}</td>
                                <td>{{$rows->votes_secured_by_Women}}</td>
                                <td>{{round($peroverelectors,2)}}</td>
                                <td>{{round($overTotalValidVotes,2)}}</td>
                                <td>{{round($ovsbp,2)}}</td>
                                @php
                                $totalcontested += $rows->contested;
                                $twon += $rows->WON;
                                $tfd += $rows->DF;
                                $twonper=($twon/$totalcontested)*100;
                                $tdfper=($tfd/$totalcontested)*100;
                                $totalVoteSecured+=$rows->votes_secured_by_Women;
                                $totalElectors+=$rows->electrols_Total;
                                $ttotalElectors=($totalVoteSecured/$totalElectors)*100;
                                $totvv=($totalVoteSecured/$rows->OVER_ALL_TOTAL_VOTE)*100;
                                $tvv+=$rows->totalvalid_valid_vote;
                                $totvsp=($totalVoteSecured/$tvv)*100;
                                @endphp
                            </tr>
                        @endforeach
                            <tr><td style="background:#f0587e;color:#fff !important;">Total</td>
                            <td><b>{{$totalcontested}}</b></td>
                            <td><b>{{$twon}}</b></td>
                            <td><b>{{$tfd}}</b></td>
                             <td><b>{{round($twonper,2)}}</b></td>
                           <td><b>{{round($tdfper,2)}}</b></td>
                           <td><b>{{$totalVoteSecured}}</b></td>
                           <td><b>{{round($ttotalElectors,2)}}</b></td>
                           <td><b>{{round($totvv,2)}}</b></td>
                           <td><b>{{round($totvsp,2)}}</b></td>
                            </tr>

                        </tbody>
											<?php } else { ?>

												<tbody>
													<td></td>
													<td colspan="7" class="text-center"><b>No Data Found</b></td>
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
