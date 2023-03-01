<html>
  <head>
      <style>
	  
	  @page { sheet-size: A3-L; }
@page bigger { sheet-size: 420mm 370mm; }
@page toc { sheet-size: A4; }
      @page {
            header: page-header;
            footer: page-footer;
        }


        td {
    font-size: 15px !important;
    font-weight: 500 !important;
    text-align: center;
    font-family: "Times New Roman", Times, serif;
    padding: 10px;
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


    .border{
    border: 1px solid #000;
    }
    th {
    font-size: 14px;
    font-weight: bold !important;
    }

    table{
    width: 100%;
    }
      </style>
  </head>
  <div class="bordertestreport">
      <table class="">
           <tr>
              <td style="text-align: center; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, Elections,2019 ( 17 LOK SABHA )</strong></p></td>
            </tr>
           
  </table>


<table class="border">
    <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 20px !important; text-transform: uppercase;"><b>14 - PC WISE DISTRIBUTION OF VOTES POLLED </b></p>
                  </td>
              </tr>

</table>
  <table>
       <?php  if (verifyreport(14) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php }  ?>


  </table>




      <table class="table table-bordered">
                      <thead>
                          <tr>
                              <th rowspan="2">Sl.no. </th>
                              <th rowspan="2">PC No. </th>
                              <th rowspan="2">PC Name </th>
                              <th colspan="2">Electors </th>
                             <th colspan="2">Valid Votes Polled</th>
                             <th rowspan="2">NOTA</th>
                             <th colspan="2">Rejected/ Not <br>Retrived Votes</th>
                             <th rowspan="2">Total Voters</th>
                             <th rowspan="2">Tendered <br> Votes</th>
                             <th rowspan="2">Test <br>Votes</th>
                             <th rowspan="2">Voter <br> Turn Out <br> (%)</th>
                             <th rowspan="2">% Votes to <br> Winner out <br> of  total <br> Votes <br> Polled</th>
                             <th rowspan="2">% Votes to  <br>NOTA out <br> of total <br> Votes <br>Polled</th>
                          </tr>


                       <tr>


                           <th>General</th>
                           <th>Service</th>
                           <th>EVM</th>
                           <th>Postal</th>
                           <th>EVM</th>
                           <th>Postal</th>
                       </tr>
                      </thead>
                      <tbody>

                          <?php $count=1; ?>

                          <?php

                              $grandegeneral = $grandeservice = $grandevm_vote = $grandpostal_vote = $grandnota_vote
                              = $grandvotes_not_retreived_from_evm  = $grandpostal_votes_rejected = $grandvoters = $grandtended_votes
                              = $grandtest_votes_49_ma = $grandtotal1 = $grandtotal2 = $grandtotal3 = $grandwinnervote =0;
                          ?>


                          @foreach($pcwisedata as $key => $value)

                              <tr>
                                  <td colspan="16" style="text-align: left;"><p><b>State : </b><b>{{$key}}</b></p></td>
                              </tr>

                          <?php

                               $totalegeneral = $totaleservice = $totalevm_vote = $totalpostal_vote = $totalnota_vote
                               = $totalvotes_not_retreived_from_evm = $totalpostal_votes_rejected = $totalvoters =
                              $totaltended_votes = $totaltest_votes_49_ma = $totalwinnervote = 0;
                          ?>
                          @foreach($value as $key1 => $value1)


                          <tr>
                              <td>{{$count}}</td>
                              <td>{{$value1['pc_no']}}</td>
                              <td>{{$value1['PC_NAME']}}</td>
                              <td>{{$value1['egeneral']}}</td>
                              <td>{{$value1['eservice']}}</td>
                              <td>{{$value1['evm_vote']}}</td>
                              <td>{{$value1['postal_vote']}}</td>
                              <td>{{$value1['nota_vote']}}</td>
                              <td>{{$value1['votes_not_retreived_from_evm']+$value1['rejected_votes_due_2_other_reason']}}</td>
                              <td>{{$value1['postal_votes_rejected']}}</td>

                              <td>{{$value1['voters']}}</td>
                              <td>{{$value1['tended_votes']}}</td>
                              <td>{{$value1['test_votes_49_ma']}}</td>

                              <td>{{round($value1['voters']/($value1['egeneral']+$value1['eservice'])*100,2)}}</td>
                              <td>{{round($value1['lead_total_vote']/$value1['voters']*100,2)}}</td>
                              <td>{{round($value1['nota_vote']/$value1['voters']*100,2)}}</td>

                          </tr>



                         <?php $count++; ?>

                         <?php

                          $totalegeneral += $value1['egeneral'];
                          $totaleservice += $value1['eservice'];
                          $totalevm_vote += $value1['evm_vote'];
                          $totalpostal_vote += $value1['postal_vote'];
                          $totalnota_vote += $value1['nota_vote'];
                          $totalvotes_not_retreived_from_evm += $value1['votes_not_retreived_from_evm']+$value1['rejected_votes_due_2_other_reason'];
                          $totalpostal_votes_rejected += $value1['postal_votes_rejected'];
                          $totalvoters += $value1['voters'];
                          $totaltended_votes += $value1['tended_votes'];
                          $totaltest_votes_49_ma += $value1['test_votes_49_ma'];
                          $totalwinnervote += $value1['lead_total_vote'];

                          $total1 = round($totalvoters/($totalegeneral+$totaleservice)*100,2);
                          $total2 = round($totalwinnervote/($totalvoters)*100,2);
                          $total3 = round($totalnota_vote/($totalvoters)*100,2);

                         ?>

                          @endforeach

                          <tr>
                              <td colspan="3"><b>State Total</b></td>

                              <td><b>{{$totalegeneral}}</b></td>
                              <td><b>{{$totaleservice}}</b></td>
                              <td><b>{{$totalevm_vote}}</b></td>
                              <td><b>{{$totalpostal_vote}}</b></td>
                              <td><b>{{$totalnota_vote}}</b></td>
                              <td><b>{{$totalvotes_not_retreived_from_evm}}</b></td>
                              <td><b>{{$totalpostal_votes_rejected}}</b></td>
                              <td><b>{{$totalvoters}}</b></td>
                              <td><b>{{$totaltended_votes}}</b></td>
                              <td><b>{{$totaltest_votes_49_ma}}</b></td>
                              <td><b>{{$total1}}</b></td>
                              <td><b>{{$total2}}</b></td>
                              <td><b>{{$total3}}</b></td>
                          </tr>

                           <?php

                          $grandegeneral += $totalegeneral;
                          $grandeservice += $totaleservice;
                          $grandevm_vote += $totalevm_vote;
                          $grandpostal_vote += $totalpostal_vote;
                          $grandnota_vote += $totalnota_vote;
                          $grandvotes_not_retreived_from_evm += $totalvotes_not_retreived_from_evm;
                          $grandpostal_votes_rejected += $totalpostal_votes_rejected;
                          $grandvoters += $totalvoters;
                          $grandtended_votes += $totaltended_votes;
                          $grandtest_votes_49_ma += $totaltest_votes_49_ma;

                          $grandwinnervote += $totalwinnervote;

                          $grandtotal1 = round($grandvoters/($grandegeneral+$grandeservice)*100,2);
                          $grandtotal2 = round($grandwinnervote/($grandvoters)*100,2);
                          $grandtotal3 = round($grandnota_vote/($grandvoters)*100,2);


                         ?>

                          @endforeach


                          <tr>
                              <td colspan="3"><b>All India Total</b></td>

                              <td><b>{{$grandegeneral}}</b></td>
                              <td><b>{{$grandeservice}}</b></td>
                              <td><b>{{$grandevm_vote}}</b></td>
                              <td><b>{{$grandpostal_vote}}</b></td>
                              <td><b>{{$grandnota_vote}}</b></td>
                              <td><b>{{$grandvotes_not_retreived_from_evm}}</b></td>
                              <td><b>{{$grandpostal_votes_rejected}}</b></td>
                              <td><b>{{$grandvoters}}</b></td>
                              <td><b>{{$grandtended_votes}}</b></td>
                              <td><b>{{$grandtest_votes_49_ma}}</b></td>
                              <td><b>{{$grandtotal1}}</b></td>
                              <td><b>{{$grandtotal2}}</b></td>
                              <td><b>{{$grandtotal3}}</b></td>
                          </tr>


                          </tbody>
                 </table>



  </div>
  


 <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>


<htmlpagefooter name='page-footer'>
 <table>
 <tr>
 <?php if (verifyreport(14) == 1){ ?>
 <td align="left"><span style="float:left;color: #d3d3d3;">{{getreportsequence(777)}}</span></td>
    
    <?php } ?>
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>
</table>
 </htmlpagefooter>



  </html>
