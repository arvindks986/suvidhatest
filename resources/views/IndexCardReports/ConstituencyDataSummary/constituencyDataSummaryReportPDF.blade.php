<html>
  <head>
      <style>
	  @page {
            header: page-header;
            footer: page-footer;
        }

        td {
    font-size: 12px !important;
    font-weight: 500 !important;
    text-align: left;
    text-transform: uppercase;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
    }

    .left-al tr td{
text-align: left;
    }
    .table-bordered{
    border:1px solid #000;
    }
    .table-bordered td,
    .table-bordered th {
    border: 1px solid #000 !important
    }
    .table {
    width: 100%;
    border-collapse: collapse;
    font-size: .9em;
    color: #000;
    margin-bottom: 1rem;
    color: #212529;

    }

     
        td {
    font-size: 12px !important;
    text-align: left;
    padding: 9px;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
    }

    .left-al tr td{
text-align: left;
    }
    .table-bordered{
    border:1px solid #000;
    }
    .table-bordered td,
    .table-bordered th {
    border: 1px solid #000 !important
    }
    .table {
    width: 100%;
    border-collapse: collapse;
    font-size: .9em;
    color: #000;
    margin-bottom: 1rem;
    color: #212529;

    }
.bolds{
  font-weight: bold;
}
    .blc{
  border-collapse: collapse;
  border-bottom: 1px solid #000;
  border-spacing: 0px 8px;
 } 
 .blcs{
  border-collapse: collapse;
  border-bottom: 1px solid #000;
  border-top: 1px solid #000;
 }
   


    .border{
    border: 1px solid #000;
    }   
    .borders{
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    }
    th {
    font-size: 12px;
    font-weight: bold !important;
    }
    
    table{
    width: 100%;
    }
    th {
      text-align: left;
  text-transform: uppercase;
    font-size: 12px;
    font-weight: bold !important;
    }
    
    table{
    width: 100%;
    }
      </style>
  </head>

      <table class="">
           <tr>
              <td style="text-align: center; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, Elections,2019 ( 17 LOK SABHA )</strong></p></td>
            </tr>
         
  </table>
<table class="border">
      <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 20px !important; text-transform: uppercase;"><b>32 - CONSTITUENCY DATA SUMMARY</b></p>
                  </td>
              </tr>

</table>
<br>
  <table>
     <?php  if (verifyreport(32) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>

  </table> 


               @foreach($finalArraynew as  $key => $value)

                @foreach($value as  $val)

                <table>


        <tr>
          <td class="bolds" style="border-top: 1px solid #000;">State/UT: {{$key}}</td>
          <td class="bolds" style="border-top: 1px solid #000;">Code: {{$key}}</td>
        </tr>


          <tr>        
          <td> <p class="contituency"><b>Constituency</b> : <span>{{$val['PC_NAME'].' '.$val['pc_type']}}</span></p></td>
          <td>{{$val['pc_no']}}</td>
          </tr>
</table>

          <br>

                  <table id="" class="table table-striped table-bordered" style="width:100%;">

          <thead>
            



              <tr>
                  <th>&nbsp;I. Candidates</th>
                  <th>&nbsp;Men</th>
                  <th>&nbsp;Woman</th>
                  <th>&nbsp;Third Gender</th>
                  <th>&nbsp;Total</th>
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
                  <th>&nbsp;II. Electors</th>
                  <th>&nbsp;Men</th>
                  <th>&nbsp;Woman</th>
                  <th>&nbsp;Third Gender</th>
                  <th>&nbsp;Total</th>
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
                  <th>&nbsp;III. VOTERS</th>
                  <th>&nbsp;Men</th>
                  <th>&nbsp;Woman</th>
                  <th>&nbsp;Third Gender</th>
                  <th>&nbsp;Total</th>
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
                <td colspan="4">&nbsp;III(a). Polling Percentage</td>
                <td>{{round($val['total_votes']/($val['gen_t'] + $val['nri_m']+$val['nri_f']+$val['nri_o'] + $val['ser_t'])*100,2)}}</td>
              </tr>
<!-- new -->
              <tr>
                  <th colspan="5" style="">&nbsp; IV. Votes</th>
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
                  <th colspan="5" style=""> &nbsp;V. Polling Stations</th>
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
                  <th colspan="5" style="">&nbsp;VI. Dates</th>
              </tr>
              <tr>
                  <th colspan="2">&nbsp;Polling</th>
                  <th colspan="2">&nbsp;Counting</th>
                  <th colspan="1">&nbsp;Declaration Of Result</th>
              </tr>
              <tr>
                  <td colspan="2">{{$val['DATE_POLL']}}</td>
                  <td colspan="2">{{$val['DATE_COUNT']}}</td>
                  <td colspan="1">{{$val['result_declared_date']}}</td>
              </tr>
              <tr>
                  <th colspan="5" style="">&nbsp;VII. Result</th>
              </tr>
              <tr>
                  <th colspan="2"></th>
                  <th>&nbsp;Party</th>
                  <th>&nbsp;Candidate</th>
                  <th>&nbsp;Votes</th>
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

          <div style='page-break-after:always'></div>
            @endforeach
		 	
			
            @endforeach



 <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>



<htmlpagefooter name='page-footer'>
 <table>
 <tr>
 <?php if (verifyreport(32) == 1){ ?>
 <td align="left"><span style="float:left;color: #d3d3d3;">{{getreportsequence(777)}}</span></td>
    
    <?php } ?>
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>
</table>
 </htmlpagefooter>



</html>
