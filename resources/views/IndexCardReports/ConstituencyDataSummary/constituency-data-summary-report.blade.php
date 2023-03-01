@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'CONSTITUENCY DATA SUMMARY')
@section('content')
<?php  $st=getstatebystatecode($user_data->st_code);   ?>



<style>
  .contituency{
    text-align: center;
    font-weight: bold;
    padding: 17px;
    text-decoration: underline;
    font-size: 19px;

  }
  .bolds{
    font-weight: bold;
  }
  td{
    text-transform: uppercase;
  }
</style>
<section class="">
 <div class="container-fluid">
 <div class="row">
 <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
     <div class=" card-header">
     <div class=" row">
           <div class="col"><h4>Election Commission Of India, General Elections, {{getElectionYear()}}<br>(32 -CONSTITUENCY DATA SUMMARY)</h4></div>
             <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b>
              </p>
               <p class="mb-0 text-right">
                      <a href="constituencyDataSummaryReportPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
       <a href="constituencyDataSummaryReportXLS" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
               </p>
             </div>


           </div>
     </div>

<div class="card-body">
<?php //echo "<pre>"; print_r($finalArraynew); die; ?>
    <div class="table-responsive">
      @foreach($finalArraynew as $key => $value)

        @foreach($value as  $val)

        <?php //echo "<pre>"; print_r($val); die; ?>
<table style="width: 100%;">
  

            <tr>
          <td class="bolds">State/UT:   &nbsp;    {{$key}}</td>
          <td class="bolds" style="text-align: right;">Code:   {{$key}}</td>
        </tr>


          <tr>        
          <td> <p class=""><b>Constituency</b> : <span>   {{$val['PC_NAME'].' '.$val['pc_type']}}</span></p></td>
          <td style="text-align: right;"><b>  {{$val['pc_no']}}</b></td>
          </tr>


</table>
        <table id="" class="table table-striped table-bordered" style="width:100%; table-layout: fixed;overflow: hidden;">

          <thead>
            



              <tr>
                  <th>I. Candidates</th>
                  <th>Men</th>
                  <th>Woman</th>
                  <th>Third Gender</th>
                  <th>Total</th>
              </tr>
          </thead>
       
          <tbody>


              <tr>
                  <td>1. Nominated</td>
                  <td>{{$val['c_nom_m_t']}}</td>
                  <td>{{$val['c_nom_f_t']}}</td>
                  <td>{{$val['c_nom_o_t']}}</td>
                  <td>{{$val['c_nom_m_t']+$val['c_nom_f_t']+$val['c_nom_o_t']}}</td>
              </tr>

              <tr>
                  <td>2. Nomination Rejected</td>
                  <td>{{$val['c_rej_m_t']}}</td>
                  <td>{{$val['c_rej_f_t']}}</td>
                  <td>{{$val['c_rej_o_t']}}</td>
                  <td>{{$val['c_rej_m_t']+$val['c_rej_f_t']+$val['c_rej_o_t']}}</td>
              </tr>

              <tr>
                  <td>3. Withdrawn</td>
                  <td>{{$val['c_wd_m_t']}}</td>
                  <td>{{$val['c_wd_f_t']}}</td>
                  <td>{{$val['c_wd_o_t']}}</td>
                  <td>{{$val['c_wd_m_t']+$val['c_wd_f_t']+$val['c_wd_o_t']}}</td>
              </tr>

              <tr>
                  <td>4. Contested</td>
                  <td>{{$val['c_acp_m_t']}}</td>
                  <td>{{$val['c_acp_f_t']}}</td>
                  <td>{{$val['c_acp_o_t']}}</td>
                  <td>{{$val['c_acp_m_t']+$val['c_acp_f_t']+$val['c_acp_o_t']}}</td>
              </tr>

              <tr>
                  <td>5.Forfeited Deposit</td>
                  <td>{{$val['c_fd_m_t']}}</td>
                  <td>{{$val['c_fd_f_t']}}</td>
                  <td>{{$val['c_fd_o_t']}}</td>
                  <td>{{$val['c_fd_t']}}</td>
              </tr>



              <tr>
                  <th>II. Electors</th>
                  <th>Men</th>
                  <th>Woman</th>
                  <th>Third Gender</th>
                  <th>Total</th>
              </tr>
              </thead>
          <tbody>
              <tr>
                  <td>1. GENERAL</td>
                  <td>{{$val['gen_m']}}</td>
                  <td>{{$val['gen_f']}}</td>
                  <td>{{$val['gen_o']}}</td>
                  <td>{{$val['gen_t']}}</td>
              </tr>
              <tr>
                  <td>2. Overseas</td>
                  <td>{{$val['nri_m']}}</td>
                  <td>{{$val['nri_f']}}</td>
                  <td>{{$val['nri_o']}}</td>
                  <td>{{$val['nri_m']+$val['nri_f']+$val['nri_o']}}</td>
              </tr>
              <tr>
                  <td>3. Service</td>
                  <td>{{$val['ser_m']}}</td>
                  <td>{{$val['ser_f']}}</td>
                  <td>{{$val['ser_o']}}</td>
                  <td>{{$val['ser_t']}}</td>
              </tr>
              <tr>
                  <td>4. Total</td>
                  <td>{{$val['gen_m'] + $val['nri_m'] + $val['ser_m']}}</td>
                  <td>{{$val['gen_f'] + $val['nri_f'] + $val['ser_f']}}</td>
                  <td>{{$val['gen_o'] + $val['nri_o'] + $val['ser_o']}}</td>
                  <td>{{$val['gen_t'] + $val['nri_m']+$val['nri_f']+$val['nri_o'] + $val['ser_t']}}</td>
              </tr>
              <tr>
                  <th>III. VOTERS</th>
                  <th>Men</th>
                  <th>Woman</th>
                  <th>Third Gender</th>
                  <th>Total</th>
              </tr>
              </thead>
          <tbody>
              <tr>
                  <td>1. General</td>
                  <td>{{$val['male_voter']}}</td>
                  <td>{{$val['female_voter']}}</td>
                  <td>{{$val['other_voter']}}</td>
                  <td>{{$val['male_voter']+$val['female_voter']+$val['other_voter']}}</td>
              </tr>
              <tr>
                  <td>2. Overseas</td>
                  <td>{{$val['nri_male_voters']}}</td>
                  <td>{{$val['nri_female_voters']}}</td>
                  <td>{{$val['nri_other_voters']}}</td>
                  <td>{{$val['nri_male_voters']+$val['nri_female_voters']+$val['nri_other_voters']}}</td>
              </tr>
              <tr>
                  <td>3. Proxy</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>{{$val['proxy_votes']}}</td>
              </tr>
              <tr>
                  <td>4. Postal</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>{{$val['postal_votes']}}</td>
              </tr>
              <tr>
                  <td>5. Total</td>
                  <td>{{$val['male_voter']+$val['nri_male_voters']}}</td>
                  <td>{{$val['female_voter']+$val['nri_female_voters']}}</td>
                  <td>{{$val['other_voter']+$val['nri_other_voters']}}</td>
                  <td>{{$val['total_votes']}}</td>
              </tr> 

<!-- new -->

              <tr>
                  <td>6. Votes Rejected Due to Other Reason</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>{{$val['rejected_votes_due_2_other_reason']}}</td>
              </tr>


              <tr>
                <td colspan="4">III(a). Polling Percentage</td>
                <td>{{round($val['total_votes']/($val['gen_t'] + $val['nri_m']+$val['nri_f']+$val['nri_o'] + $val['ser_t'])*100,2)}}</td>
              </tr>
<!-- new -->
              <tr>
                  <th colspan="5" style=""> IV. Votes</th>
              </tr>
              <tr>
                  <td colspan="4">1. Total Votes Polled On EVM</td>
                  <td>{{$val['male_voter']+$val['female_voter']+$val['other_voter']+$val['test_votes_49_ma']+$val['nri_male_voters']+$val['nri_female_voters']+$val['nri_other_voters']}}</td>
              </tr>
              <tr>
                  <td colspan="4">2. Total deducted votes from evm(test
                      votes+votes not retrived+ NOTA)</td>
                  <td>{{$val['test_votes_49_ma']+$val['votes_not_retreived_from_evm']+$val['rejected_votes_due_2_other_reason']+$val['nota_evm_vote']}}</td>
              </tr>
              <tr>
                  <td colspan="4">3.Total valid votes polled on evm
                  <td>{{($val['male_voter']+$val['female_voter']+$val['other_voter']+$val['test_votes_49_ma']+$val['nri_male_voters']+$val['nri_female_voters']+$val['nri_other_voters'])-($val['test_votes_49_ma']+$val['votes_not_retreived_from_evm']+$val['rejected_votes_due_2_other_reason']+$val['nota_evm_vote'])}}</td>
              </tr>
              <tr>
                  <td colspan="4">4. Postal Votes Counted</td>
                  <td>{{$val['service_postal_votes_under_section_8'] + $val['service_postal_votes_gov'] }}</td>
              </tr>
              <tr>
                  <td colspan="4">5. Postal Votes Deducted (REJECTED POSTAL
                      VOTES + 'NOTA')</td>
                  <td>{{$val['rej_votes_postal']+$val['nota_postal_vote']}}</td>
              </tr>
              <tr>
                  <td colspan="4">6. Valid Postal Votes</td>
                  <td>{{($val['service_postal_votes_under_section_8'] + $val['service_postal_votes_gov'])-($val['rej_votes_postal']+$val['nota_postal_vote'])}}</td>
              </tr>
              <tr>
                  <td colspan="4">7. Total Valid Votes Polled</td>
                  <td>{{($val['male_voter']+$val['female_voter']+$val['other_voter']+$val['test_votes_49_ma']+$val['nri_male_voters']+$val['nri_female_voters']+$val['nri_other_voters'])-($val['test_votes_49_ma']+$val['votes_not_retreived_from_evm']+$val['rejected_votes_due_2_other_reason']+$val['nota_evm_vote'])+($val['service_postal_votes_under_section_8'] + $val['service_postal_votes_gov'])-($val['rej_votes_postal']+$val['nota_postal_vote'])}}</td>
              </tr>
              <tr>
                  <td colspan="4">8. Test Votes polled On EVM</td>
                  <td>{{$val['test_votes_49_ma']}}</td>
              </tr>

<!-- new2 --><tr>
                  <td colspan="4">9.  VOTES POLLED FOR 'NOTA' (INCLUDING POSTAL)</td>
                  <td>{{$val['nota_evm_vote']+$val['nota_postal_vote']}}</td>
              </tr>

<!-- new2 -->


              <tr>
                  <td colspan="4">10. Tendered Votes</td>
                  <td>{{$val['tended_votes']}}</td>
              </tr>


           

              <tr>
                  <th colspan="5" style=""> V. Polling Stations</th>
              </tr>
              <tr>
                  <td colspan="2">Number</td>
                  <td>{{$val['total_polling_station_s_i_t_c']}}</td>
                  <td>Average Electors Per Polling Station</td>
                  <?php if($val['total_polling_station_s_i_t_c'] > 0) { ?>
                  <td>{{round(($val['gen_t'] + $val['nri_m']+$val['nri_f']+$val['nri_o'] + $val['ser_t'])/$val['total_polling_station_s_i_t_c'],0)}}</td>

                  
                  <?php } else { ?>
                  <td>0</td>
                <?php } ?>
              </tr>
              <tr>
                  <td colspan="4">Date(s) of Re-poll, If Any:</td>
                  <td>
                    @if (trim($val['date_of_repoll']) != 0 && $val['date_of_repoll'])
                                                    
                                                <?php
                                                    $repoll_dates     = explode(',',$val['date_of_repoll']);
                                                    $dates_array     = [];
                                                    foreach($repoll_dates as $res_repoll){
                                                        $dates_array[] = date('d-m-Y', strtotime(trim($res_repoll)));
                                                    }    
                                                ?>
                                                
                                                {!! implode(', ', $dates_array) !!}
                                               @else{{'NA'}}
                                                @endif
                                       
                  </td>
                  
              </tr>
              <tr>
                  <td colspan="4">Number Of Polling Stations where Re-Polls Was Ordered</td>
                  <td>{{$val['no_poll_station_where_repoll']}}</td>
              </tr>
              <tr>
                  <th colspan="5" style="">VI. Dates</th>
              </tr>
              <tr>
                  <th colspan="2">Polling</th>
                  <th colspan="2">Counting</th>
                  <th colspan="1">Declaration Of Result</th>
              </tr>
              <tr>
                  <td colspan="2">{{$val['DATE_POLL']}}</td>
                  <td colspan="2">{{$val['DATE_COUNT']}}</td>
                  <td colspan="1">{{$val['result_declared_date']}}</td>
              </tr>
              <tr>
                  <th colspan="5" style="">VII. Result</th>
              </tr>
              <tr>
                  <th colspan="2"></th>
                  <th>Party</th>
                  <th>Candidate</th>
                  <th>Votes</th>
              </tr>
              <tr>
                  <td colspan="2">Winner</td>
                  <td>{{$val['lead_cand_party']}}</td>
                  <td>{{$val['lead_cand_name']}}</td>
                  <td>{{$val['lead_total_vote']}}</td>
              </tr>
              <tr>
                  <td colspan="2">Runer-Up</td>
                  <td>{{$val['trail_cand_party']}}</td>
                  <td>{{$val['trail_cand_name']}}</td>
                  <td>{{$val['trail_total_vote']}}</td>
              </tr>
              <tr>
                  <td colspan="2">Margin</td>
                  <td></td>
                  <td></td>
                  <td>{{$val['margin']}}</td>
              </tr>
          </tbody>

      </table>

  
  @endforeach
  @endforeach
    </div>
</div>
</div>
</div>
</div>
</section>


@endsection
