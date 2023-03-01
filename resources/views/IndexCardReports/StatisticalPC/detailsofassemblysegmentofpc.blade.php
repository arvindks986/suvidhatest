@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Details Of Assembly Segment Of PC')
@section('content')


<style>
</style>
<section class="">
	<div class="container-fluid">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(34 - Details of Assembly Segments of Parliamentary Constituencies)
</h4></div> 
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
							</p>
							<p class="mb-0 text-right">
							<a href="detailsofassemblysegmentofpc_pdf" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="detailsofassemblysegmentofpc_xls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>
				
				<div class="card-body">
					<div class="table-responsive">
						   <table class="table table-bordered table-striped" style="width: 100%;table-layout: fixed;">
                     <thead>
                        
                        <tr>
                           <th>Candidate No & Name </th>
                           <th>Party</th>
                           <th colspan="2">Votes Secured</th>
                        </tr>
                     </thead>
					 
					 
					 @foreach($arrData as $key => $arrData1)
                     <tbody>
                        <tr>
                           <td  colspan="4" style="text-align:center;vertical-align:middle;"><b>State/UT Code and Name: </b>   {{$key}}</td>
						</tr>   
						@foreach($arrData1 as $key1 => $arrData2)   
						<tr>  
                           <td colspan="4" style="text-align:center;vertical-align:middle;"><b>PC No. & Name: </b>{{$key1}}</td>
                        </tr>
                        
						<?php 
						$i = 1;
						?>
						@foreach($arrData2 as $key2 => $raw1)
						
						<?php $total_vote = 0; ?>
						
						@foreach($raw1 as $key => $raw)
						<?php if($key == 0) { ?>
                        <tr>
                           <td  colspan="2" style="text-align: center;"><b>AC Number and AC Name:</b>  {{$key2}}</td>
                           
                           <td colspan="2" style="text-align: center;"><b>Electors: </b> ({{$raw['ac_electors']}})</td>
                           
                        </tr>
						<?php } ?>
						
                     
						<?php 	$datarawc = \App\models\Admin\VoterModel::get_candedates_votes_by_ac_no($raw['st_code'],$raw['pc_no'],$ac_no   = $raw['ac_no']); ?>
						
						
							@foreach($datarawc as $keyy => $raww)
						 
						 
							<tr>
							   <td> {{$keyy +1}}. {{$raww->candidate_name}}</td>
							   <td>{{$raww->party_abbre}}</td>
							   <td colspan="2" style="text-align:center;">{{$raww->total_vote}}</td>
							</tr>
							
							<?php $total_vote += $raww->total_vote; ?>
							@endforeach
						
						
						<?php 
						$i++;
						?>
						
						<?php $st_code = $raw['st_code']; ?>
						<?php $pc_no   = $raw['pc_no']; ?>
						<?php $ac_no   = $raw['ac_no']; ?>
						@endforeach
						<tr>
                           
                           <td  colspan="2" style="text-align:center;"><b>Total Valid Votes for the AC :</b></td>
                           <td colspan="2" style="text-align:center;"><b>{{$total_vote}}</b></td>
                        </tr>
						
						<?php $dataraw = \App\models\Admin\VoterModel::get_nota_votes_by_ac_no($st_code,$pc_no,$ac_no);?>
						<tr>
                           
                           <td colspan="2" style="text-align:center;"><b>NOTA Votes Polled(Excld. Postal Votes):</b></td>
                           <td colspan="2" style="text-align:center;"><b>{{$dataraw[0]->total_vote}}</b></td>
                        </tr>
						
												
						@endforeach
                        <?php if($st_code == 'S09' && in_array($pc_no, [1,2,3]) ){ ?>
						
							<?php if($st_code == 'S09' && $pc_no == 1) { ?>
								<tr>
								   <td  colspan="2" style="text-align: center;"><b>AC Number and AC Name:</b>   88 - DelhiUdhampurJammu</td>
								   
								   <td colspan="2" style="text-align: center;"><b>Electors: </b> 0</td>
								   
								</tr>
							<?php }else if($st_code == 'S09' && $pc_no == 2){ ?>
									<tr>
									   <td  colspan="2" style="text-align: center;"><b>AC Number and AC Name:</b>   89 - DelhiUdhampurJammu</td>									   
									   <td colspan="2" style="text-align: center;"><b>Electors: </b> 0</td>	
									</tr>
							
							<?php }else if($st_code == 'S09' && $pc_no == 3){ ?>							
									<tr>
									   <td  colspan="2" style="text-align: center;"><b>AC Number and AC Name:</b>   90 - DelhiUdhampurJammu</td>									   
									   <td colspan="2" style="text-align: center;"><b>Electors: </b> 0</td>	
									</tr>							
							<?php } ?>
							
							
							<?php $dataraw2 = \App\models\Admin\VoterModel::get_migrante_by_pc_no($st_code,$pc_no); ?>
						
							@foreach($dataraw2 as $kk => $pdata)
								<tr>
								   <td> {{$kk +1}}. {{$pdata->candidate_name}}</td>
								   <td>{{$pdata->party_abbre}}</td>
								   <td colspan="2" style="text-align:center;">{{$pdata->migrate_votes}}</td>
								</tr>
								
							@endforeach
						
						
							<tr>							   
							   <td  colspan="2" style="text-align:center;"><b>Total Valid Votes for the AC :</b></td>
							   <td colspan="2" style="text-align:center;"><b></b></td>
							</tr>
							
							<tr>							   
							   <td colspan="2" style="text-align:center;"><b>NOTA Votes Polled(Excld. Postal Votes):</b></td>
							   <td colspan="2" style="text-align:center;"><b></b></td>
							</tr>
						
						<?php } ?>
						
						<tr>
                           
                           <td  colspan="4" ><b>Valid Postal Ballots for each candidate in the PC</b></td>
                        </tr>
						
							<?php $dataraw2 = \App\models\Admin\VoterModel::get_postal_by_pc_no($st_code,$pc_no);

								$post_count = 0

							?>
						
							@foreach($dataraw2 as $kk => $pdata)
								<tr>
								   <td> {{$kk +1}}. {{$pdata->candidate_name}}</td>
								   <td>{{$pdata->party_abbre}}</td>
								   <td colspan="2" style="text-align:center;">{{$pdata->postal_vote}}</td>
								</tr>
							<?php $post_count += $pdata->postal_vote;	?>
								
							@endforeach

							<tr>
							   
							   <td colspan="2"><b>Total Valid Postal Ballots for PC</b></td>
							   <td colspan="2" style="text-align:center;"><b>{{$post_count}}</b></td>
							</tr>
							
							<?php $dataraw3 = \App\models\Admin\VoterModel::get_total_valid_votes_by_pc_no($st_code,$pc_no); ?>

							<tr>                           
							   <td><b>Total Valid Votes for PC</b></td>
							   <td><b>{{$key1}}</b></td>
							   <td colspan="2" style="text-align:center;"><b>{{$dataraw3[0]->total_vote}}</b></td>
							</tr>
							<?php $dataraw4 = \App\models\Admin\VoterModel::get_nota_potal_votes_by_pc_no($st_code,$pc_no); ?>
							
							<tr>                           
							   <td colspan="2"><b>NOTA Postal Votes : </b></td>
							   <td colspan="2" style="text-align:center;"><b>{{$dataraw4[0]->postal_vote}}</b></td>
							</tr>
						
					@endforeach
						<?php $dataraw5 = \App\models\Admin\VoterModel::get_total_valid_votes_by_st_code($st_code); ?>
							
							<tr>                           
							   <td colspan="2"><b>Total Valid Votes for the State/UT : </b></td>
							   <td colspan="2" style="text-align:center;"><b>{{$dataraw5[0]->total_vote}}</b></td>
							</tr>
					
                     </tbody>
					 @endforeach
					 
					 <?php $dataraw6 = \App\models\Admin\VoterModel::get_total_valid_votes_by_all(); ?>
							
							<tr>                           
							   <td colspan="2"><b>Total Valid Votes for the Country : </b></td>
							   <td colspan="2" style="text-align:center;"><b>{{$dataraw6[0]->total_vote}}</b></td>
							</tr>
					 
					 
					 
                  </table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection