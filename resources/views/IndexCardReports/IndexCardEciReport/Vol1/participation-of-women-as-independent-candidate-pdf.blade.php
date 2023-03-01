<html>
  <head>
      <style>
        td {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #000 !important;
    padding: 3px;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
    }
b{
  font-weight: bold !important;
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
                        <p style="font-size: 20px !important; text-transform: uppercase;"><b>29 - PARTICIPATION OF WOMEN AS INDEPENDENT CANDIDATES</b></p>
                  </td>
              </tr>

</table>
  <table>
       <?php  if (verifyreport(29) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>

  </table>


<br>


                        <table class="table borders" style="width: 100%;">
                            <thead>
                                <tr>
                                  <th rowspan="2" class="blc">Party Name</th>
                                    <th colspan="3" style="text-decoration: underline;">Candidates </th>
                                    <th colspan="2" style="text-decoration: underline;">Percentage </th>
                                    <th rowspan="2" class="blc">Votes <br>secured by <br> women <br> candidates</th>
                                    <th colspan="3" style="text-decoration: underline;">% of Votes Secured </th>
                                  </tr>
                               <tr>
                                <th class="blc">Contested</th>
                                <th class="blc">Won </th>
                                <th class="blc">DF</th>
                                <th class="blc">Won</th>
                                <th class="blc">DF</th>

                                <th class="blc">Over total <br>electors in <br> country</th>
                                <th class="blc">Over total <br>valid votes <br> in country</th>

                            </tr>
                            </thead>
                            <?php if($data) { ?>
                              <tbody>
                             @php
                             $totalcontested = $twon = $won= $fd =  $secure = $electorspercent = $overtotalvaliedpercent = $ovsbp= $tfd = $totalVoteSecured = $totalElectors  = $tvv = 0;
                            @endphp
                            @foreach($data as $rows)
                              @php
                                $peroverelectors = ($rows->votes_secured_by_Women/$rows->sum_of_total_eelctors)*100;

                                $overTotalValidVotes = ($rows->votes_secured_by_Women/$rows->OVER_ALL_TOTAL_VOTE)*100;

                                $ovsbp = ($rows->votes_secured_by_Women/$rows->totalvalid_valid_vote)*100;
                              @endphp

                            <tr>
                                <td style="">{{$rows->partyabbre}}</td>
                                <td>{{$rows->contested}}</td>
                                <td>{{$rows->WON}}</td>
                                <td>{{$rows->DF}}</td>
                                <td>{{round((($rows->WON/$rows->contested)*100),2)}}</td>
                                <td>{{round((($rows->DF/$rows->contested)*100),2)}}</td>
                                <td>{{$rows->votes_secured_by_Women}}</td>
                                <td>{{round($peroverelectors,2)}}</td>
                                <td>{{round($overTotalValidVotes,2)}}</td>

                                @php
                                $totalcontested += $rows->contested;
                                $twon += $rows->WON;
                                $tfd += $rows->DF;
                                $twonper=($twon/$totalcontested)*100;
                                $tdfper=($tfd/$totalcontested)*100;
                                $totalVoteSecured+=$rows->votes_secured_by_Women;
                                $totalElectors+=$rows->sum_of_total_eelctors;
                                $ttotalElectors=($totalVoteSecured/$totalElectors)*100;
                                $totvv=($totalVoteSecured/$rows->OVER_ALL_TOTAL_VOTE)*100;
                                $tvv+=$rows->totalvalid_valid_vote;

                                @endphp
                            </tr>
                        @endforeach
                            <tr><th class="blcs"><b>TOTAL:</b></th>
                            <td class="blcs"><b>{{$totalcontested}}</b></td>
                            <td class="blcs"><b>{{$twon}}</b></td>
                            <td class="blcs"><b>{{$tfd}}</b></td>
                            <td class="blcs"><b>{{round($twonper,2)}}</b></td>
                           <td class="blcs"><b>{{round($tdfper,2)}}</b></td>
                           <td class="blcs"><b>{{$totalVoteSecured}}</b></td>
                           <td class="blcs"><b>{{round($ttotalElectors,2)}}</b></td>
                           <td class="blcs"><b>{{round($totvv,2)}}</b></td>

                            </tr>

                        </tbody>

                      <?php } else { ?>

                        <tbody>
                          <tr>
                            <td></td>
                            <td colspan="8" class="text-center"><b>No Data Found</b></td>
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
    
  
    if (verifyreport(29) == 0){
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