<html>
  <head>
      <style>
        @page { sheet-size: A4-L; }
@page bigger { sheet-size: 420mm 370mm; }
@page toc { sheet-size: A4; }
@page { size: a4 landscape; }

b{
  font-weight: 600 !important;
}
        td {
    font-size: 12px !important;
    font-weight: 500 !important;
  padding: 4px;
    text-align: center;
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
    vertical-align: bottom;
    font-weight: bold !important;
    text-align: center;
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
                        <p style="font-size: 18px !important; text-transform: uppercase;"><b>27 - PARTICIPATION OF WOMEN IN STATE PARTIES</b></p>
                  </td>
              </tr>

</table>
  <table>
      <?php  if (verifyreport(27) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>

  </table>


<br>


                <table class="table borders">
                           <tr>
                                    <th rowspan="2" class="blc" style="text-align: left;">State</th>
                                    <th colspan="3" style="text-decoration: underline;">Candidates </th>
                                    <th colspan="2" style="text-decoration: underline;">Percentage </th>
                                    <th rowspan="2" class="blc">Votes secured by <br> women<br> candidates</th>
                                    <th rowspan="2" class="blc">Votes <br>secured<br> by party<br> in State</th>
                                    <th colspan="3" style="text-decoration: underline;">% of Votes Secured </th>
                            </tr>
                               <tr>
                                <th class="blc">Contested</th>
                                <th class="blc">Won </th>
                                <th class="blc">DF</th>
                                <th class="blc">Won</th>
                                <th class="blc">DF</th>
                                <th class="blc">Over total <br>electors in<br>the State</th>
                                <th class="blc">Over total <br>valid votes<br> in the State</th>
                                <th class="blc">Over votes <br>secured by <br>the party in <br>State</th>
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
                                <td style="text-align: left;" colspan="11"><b>{{$key}} </b></td>
                              </tr>





                            @foreach($value as $key1 => $value1)


                                                        <tr>


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




                            <tr style="height: 5px;"><td class="aaa" colspan="11"></td></tr>

                        @endforeach

                              @php
                                $peroverelectorstotal = round(($secure/$totalelectorsstate)*100,2);

                                $overTotalValidVotestotal = round(($secure/$OVER_ALL_TOTAL_VOTE_state)*100,2);

                                $ovsbptotal = round(($secure/$totalvalid_valid_vote)*100,2);
                              @endphp

                        <tr>

                              <th class="blcs"><b>Party Total</b></th>
                              <td class="blcs"><b>{{$totalcontested}}</b></td>
                              <td class="blcs"><b>{{$twon}}</b></td>
                              <td class="blcs"><b>{{$tfd}}</td>
                              <td class="blcs"><b>{{round((($twon/$totalcontested)*100),2)}}</b></td>
                              <td class="blcs"><b>{{round((($tfd/$totalcontested)*100),2)}}</b></td>
                              <td class="blcs"><b>{{$secure}}</b></td>
                              <td class="blcs"><b>{{$totalVoteSecuredbyparty}}</b></td>
                              <td class="blcs"><b>{{$peroverelectorstotal}}</b></td>
                              <td class="blcs"><b>{{$overTotalValidVotestotal}}</b></td>
                              <td class="blcs"><b>{{$ovsbptotal}}</b></td>
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

                              <th class="blc"><b>Grand Total</b></th>
                              <td class="blc"><b>{{$grandtotalcontested}}</b></td>
                              <td class="blc"><b>{{$grandtwon}}</b></td>
                              <td class="blc"><b>{{$grandtfd}}</b></td>
                              <td class="blc"><b>{{round((($grandtwon/$grandtotalcontested)*100),2)}}</b></td>
                              <td class="blc"><b>{{round((($grandtfd/$grandtotalcontested)*100),2)}}</b></td>
                              <td class="blc"><b>{{$grandsecure}}</b></td>
                              <td class="blc"><b>{{$grandtotalVoteSecuredbyparty}}</b></td>
                              <td class="blc"><b>{{$grandperoverelectorstotal}}</b></td>
                              <td class="blc"><b>{{$grandoverTotalValidVotestotal}}</b></td>
                              <td class="blc"><b>{{$grandovsbptotal}}</b></td>
                        </tr>
                      </tbody>

                    <?php } ?>

   
</table>


   </div>
 
 <h4 style="padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>


<script type="text/php">
    if (isset($pdf)) {
        $text = "Page {PAGE_NUM}";
        $size = 10;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width);
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size);
    
  
    if (verifyreport(27) == 0){
      $text2 = '';
        } else { 
    $text2 =  getreportsequence(777);  
    
      }

    $x2 = $width -20;
        $y2 = $pdf->get_height() - 35;
    
        $color = array(0.5,0.5,0.5);
    
    $size2 = 8;
    
        $pdf->page_text($x2, $y2, $text2, $font, $size2, $color);
    }
</script>
   
  </html>
