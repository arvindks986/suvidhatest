@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Index Card Brief Report')
@section('content')

<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
      <?php //echo '<pre>'; print_r($tvdata); die;?>
      @foreach ($tvdata as $row)
   <div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(Index Card Brief Report)</h4></div> 
						<div class="col">
							<!--<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
							</p>-->
							<p class="mb-0 text-right">
							<a href="indexCardBriefReportPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="#" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>
				
				<div class="card-body">
				 <p>Type of Constituency &nbsp;&nbsp;{{$row->PC_TYPE}}</p>
          <p> Year Of Election &nbsp;&nbsp; 2019</p>
					<div class="table-responsive">
					 <div class="col">
          <p class="text-right"> <strong>Number & Name of Parliamentary Constituency</strong>&nbsp;&nbsp;&nbsp; {{$row->pc_no}}.{{$row->PC_NAME}} </p>
        </div>
						
            <table class="table table-bordered table-striped">
              <thead>
                <tr class="table-primary">
                  <th scope="col"> I</th>
                  <th scope="col">Candidates</th>
                  <th scope="col">Male</th>
                  <th scope="col">Female</th>
                  <th scope="col">Thrid Gender</th>
                  <th scope="col">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Nominated </td>
                  <td>{{$row->c_nom_m_t}} </td>
                  <td>{{$row->c_nom_f_t}} </td>
                  <td>{{$row->c_nom_o_t}} </td>
                  <td>{{$row->c_nom_a_t}} </td>
                
                </tr>
                <tr>
                  <td>2</td>
                  <td>Nomination Rejected</td>
                 <td>{{$row->c_nom_r_m}} </td>
                  <td>{{$row->c_nom_r_f}} </td>
                  <td>{{$row->c_nom_r_o}} </td>
                  <td>{{$row->c_nom_r_a}} </td>
                </tr>
                <tr>
                  <td>3</td>
                  <td>Withdrawn </td>
                  <td>{{$row->c_nom_w_m}} </td>
                  <td>{{$row->c_nom_w_f}} </td>
                  <td>{{$row->c_nom_w_o}} </td>
                  <td>{{$row->c_nom_w_t}} </td>
                </tr>
                <tr>
                  <td>4</td>
                  <td>Contested </td>
                   <td>{{$row->c_nom_co_m}} </td>
                  <td>{{$row->c_nom_co_f}} </td>
                  <td>{{$row->c_nom_co_o}} </td>
                  <td>{{$row->c_nom_co_t}} </td>
                </tr>
                <tr>
                  <td>5</td>
                  <td>Fortefied Deposits</td>
                  <td>{{$row->c_nom_fd_m}} </td>
                  <td>{{$row->c_nom_fd_f}} </td>
                  <td>{{$row->c_nom_fd_o}} </td>
                  <td>{{$row->c_nom_fd_t}} </td>
                </tr>
              </tbody>
              <thead>
                <tr class="table-primary">
                  <th scope="col"> II</th>
                  <th scope="col">Electors II</th>
                  <th scope="col">Other Then NRIs</th>
                  <th scope="col">NRIs</th>
                  <th scope="col">Service </th>
                  <th scope="col">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Male </td>
                   <td>{{$row->e_gen_m}} </td>
                  <td>{{$row->e_nri_m}} </td>
                  <td>{{$row->e_ser_m}} </td>
                  <td>{{$row->e_all_t_m}} </td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>Female</td>
                  <td>{{$row->e_gen_f}} </td>
                  <td>{{$row->e_nri_f}} </td>
                  <td>{{$row->e_ser_f}} </td>
                  <td>{{$row->e_all_t_f}} </td>
                </tr>
                <tr>
                  <td>3</td>
                  <td>Third Gender </td>
                  <td>0</td>
                  <td>0</td>
                  <td>0</td>
                  <td>0</td>
                </tr>
                <tr>
                  <td>4</td>
                  <td>Total</td>
                  <td>{{$row->e_gen_o}} </td>
                  <td>{{$row->e_nri_o}} </td>
                  <td>{{$row->e_ser_o}} </td>
                  <td>{{$row->e_all_t_o}} </td>
                </tr>
              </tbody>
              <thead>
                <tr class="table-primary bdr-top">
                  <th scope="col"> III</th>
                  <th scope="col" colspan="5">Details of Votes Polled on EVM </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td colspan="4"> Male </td>
                  <td>{{$row->vt_m_t}} </td>
                </tr>
                <tr>
                  <td>2</td>
                  <td colspan="4">Female</td>
                  <td>{{$row->vt_f_t}} </td>                 
                </tr>
                <tr>
                  <td>3</td>
                  <td colspan="4">Third Gender </td>
                  <td>{{$row->vt_o_t}}</td>
                </tr>
                <tr>
                  <td>4</td>
                  <td colspan="4">Total (Male + Female + Third Gender)</td>
                  </td>
                  <td>{{$row->vt_all_t}} </td>
                </tr>
                <tr>
                  <td>5</td>
                  <td colspan="4">Votes not Retrieved from EVM+ Test Votes Under 49A+Rejected Votes + NOTA Votes</td>
                  </td>
                  <td>{{$row->total_not_count_votes}} </td>
                </tr>
                <tr>
                  <td>6</td>
                  <td colspan="4">Total Valid Votes Counted from EVM (4-5) </td>
                  </td>
                  <td>{{$row->v_votes_evm_all}} </td>
                </tr>
              </tbody>
              <thead>
                <tr class="table-primary bdr-top">
                  <th scope="col"> IV</th>
                  <th scope="col" colspan="5">Details of Postal Vote</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td colspan="4">Postal Votes Counted </td>
                  <td>{{$row->postal_valid_votes}} </td>
                </tr>
                <tr>
                  <td>2</td>
                  <td colspan="4">Postal Votes Rejected + NOTA</td>
                  <td>{{$row->postal_vote_r_nota}}</td>
                </tr>
                <tr>
                  <td>3</td>
                  <td colspan="4">Total Valid Postal Votes (1-2)</td>
                  <td>{{$row->postal_valid_votes-$row->postal_vote_r_nota}}  </td>
                </tr>
              </tbody>
              <thead>
                <tr class="table-primary bdr-top">
                  <th scope="col"> V </th>
                  <th scope="col" colspan="5">Combined Details of EVM & Postal Votes</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td colspan="4">Total Voters (III-4 + IV-1) </td>
                  <td>{{$row->vt_all_t+$row->postal_valid_votes}}</td>
                </tr>
                <tr>
                  <td>2</td>
                  <td colspan="4"> Total of Votes not Retrived and Rejected(III-5 +IV-2) </td>
                  <td>
{{$row->total_not_count_votes+$row->postal_vote_r_nota}} </td>
                </tr>
                <tr>
                  <td>3</td>
                  <td colspan="4">Total Valid Votes (III-6 + IV-3)</td>
                  <td>{{$row->v_votes_evm_all+$row->postal_valid_votes}} </td>
                </tr>
              </tbody>
              <thead>
                <tr class="table-primary bdr-top">
                  <th scope="col"> VI </th>
                  <th scope="col" colspan="5">Miscellaneous</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td colspan="4">Proxy Votes</td>
                  <td>{{$row->proxy_votes}} </td>
                </tr>
                <tr>
                  <td>2</td>
                  <td colspan="4"> Tendered Votes </td>
                  <td>{{$row->tendered_votes}}</td>
                </tr>
                <tr>
                  <td>3</td>
                  <td colspan="4">Total Number number of polling station set up in the constituency </td>
                  <td>{{$row->total_no_polling_station}} </td>
                </tr>
                <tr>
                  <td>4</td>
                  <td colspan="4">Average Number of Electors assigned to polling station</td>
                  <td>{{$row->avg_elec_polling_stn}} </td>
                </tr>
                <tr>
                  <td>5</td>
                  <td colspan="4"> Date(s) of Poll </td>
                  <td>{{$row->dt_poll}}</td>
                </tr>
                <tr>
                  <td>6</td>
                  <td colspan="2">Date(s) of Repoll, if any </td>
                  <td></td>
                  <td></td>
                  <td>{{$row->dt_repoll}} </td>
                </tr>
                <tr>
                  <td>7</td>
                  <td colspan="2">Number of Polling Station where Repoll was ordered</td>
                  <td></td>
                  <td></td>
                  <td>{{$row->re_poll_station}} </td>
                </tr>
                <tr>
                  <td>8</td>
                  <td colspan="4">Date(s) of Counting</td>
                  <td>{{$row->dt_counting}}</td>
                </tr>
                <tr>
                  <td>9</td>
                  <td colspan="4"> Date of Decleration of Result </td>
                  <td>{{$row->dt_declare}}</td>
                </tr>
                <tr>
                  <td>10</td>
                  <td colspan="3"> Whether this is Bye-election or Countermanded Election? </td>
                  <td>Yes/No</td>
                  @if($row->flag_bye_counter==1 || $row->flag_bye_counter==2)
                  <td>Yes</td>
                  @else
                  <td>No</td>
                  @endif
                </tr>

            
                <tr>
                  <td>11</td>
                  <td colspan="4"> If Yes, Reason there of </td>
                  <td colspan="1">{{$row->flag_bye_counter_reason}} </td>
                </tr>
              </tbody>
            </table>
          </div>  

            <div class="table-responsive"></div>
            <table class="table table-bordered table-striped">
              <thead>
                <tr class="table-primary">
                  <th scope="col"> VII</th>
                  <th scope="col" colspan="9">Candidates</th>
                </tr>
                <tr class="table-primary">
                  <th scope="col"> SNo</th>
                  <th scope="col">Candidates Name</th>
                  <th scope="col">Gender</th>
                  <th scope="col">Age</th>
                  <th scope="col">Category </th>
                  <th scope="col">Party</th>
                  <th scope="col">Votes Postal </th>
                  <th scope="col">EVM Votes </th>
                  <th scope="col">Total Votes </th>
                  <th scope="col">Votes %</th>
                </tr>
              </thead>
              <tbody>
                <?php $count=1; $totalcount=0; ?>
                @foreach($row->candidate_data AS $candidateRow)
                <?php $totalPercent = ($candidateRow->total_valid_vote!=0)?((($candidateRow->postal_vote_count+$candidateRow->vote_count)/$candidateRow->total_valid_vote)*100):0;?>
                <?php 
                 $totalcount+=$candidateRow->total_valid_vote;?>
                <tr>
                  <td>{{$count}}.</td>
                  <td>{{$candidateRow->cand_name}}</td>
                  <td>{{$candidateRow->cand_gender}} </td>
                  <td>{{$candidateRow->cand_age}} </td>
                  <td>{{$candidateRow->cand_category}}</td>
                  <td>{{$candidateRow->PARTYABBRE}}</td>
                  <td>{{$candidateRow->postal_vote_count}}</td>
                  <td>{{$candidateRow->vote_count}}</td>
                  <td>{{$candidateRow->total_valid_vote}}</td>
                  <td>{{round($totalPercent,2)}}</td>                  
                </tr>
                 <?php $count++;?>
                @endforeach
				

                <tr>
                  <td colspan="8" scope="col" align="right"><strong> Grand Total Votes:</strong></td>
                  <td colspan="2" scope="col"><strong>{{$totalcount}} </strong></td>
                </tr>
              </tbody>
           </table>
					</div>
				</div>
			</div>
		</div>
		@break
    @endforeach
      </div>
</section>
@endsection