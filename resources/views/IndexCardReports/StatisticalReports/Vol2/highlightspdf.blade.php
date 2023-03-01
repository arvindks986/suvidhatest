<!DOCTYPE html>
<html lang="en">
  <head>
    <style>


      
    td {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #000 !important;
    text-align: left;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
    }

    .deviis td{
      width: 7%;
      text-align: center;
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

    p{
      font-size: 13px;
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
    background: #eff2f4;
    color: #000 !important;
    font-size: 12px;
    font-weight: bold !important;
    }
    table{
    width: 100%;
    }
    </style>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
  <div class="bordertestreport">
    <table>
            <tr>
              <td style="text-align: center; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, Elections,2019 ( 17 LOK SABHA )</strong></p></td>
            </tr>
            
    </table>

    <table class="border">
       <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 20px !important; text-transform: uppercase;"><b>2 - Highlights - {{getElectionYear()}}</b></p>
                  </td>
              </tr>
    </table>
    <table>
      
      <?php  if (verifyreport(2) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php }?>
    </table>
    
    <p>1. NO OF CONSTITUENCIES</p>
    <table class="table table-bordered deviis">
      <tr>
        <td>TYPE OF CONSTITUENCY</td>
        <td>GEN</td>
        <td>SC</td>
        <td>ST</td>
        <td>TOTAL</td>
      </tr>
      <tr>
        <td>NO OF CONSTITUENCIES</td>
        <td>{{$contestents['genpc']}}</td>
        <td>{{$contestents['scpc']}}</td>
        <td>{{$contestents['stpc']}}</td>
        <td colspan="">{{$contestents['genpc']+$contestents['scpc']+$contestents['stpc']}}</td>
      </tr>
    </table>
    <p>2. NO OF CONTESTANTS</p>
    <table class="table table-bordered deviis">
      <tr>
        <td>NO OF CONTESTANTS IN A CONSTITUENCY</td>
        <td>1</td>
        <td>2</td>
        <td>3</td>
        <td>4</td>
        <td>5</td>
        <td>6-10</td>
        <td>11-15</td>
        <td>ABOVE 15</td>
      </tr>
      <tr>
        <td>NO OF SUCH CONSTITUENCIES</td>
        <td>{{$contestents['one']}}</td>
        <td>{{$contestents['two']}}</td>
        <td>{{$contestents['three']}}</td>
        <td>{{$contestents['four']}}</td>
        <td>{{$contestents['five']}}</td>
        <td>{{$contestents['fiveten']}}</td>
        <td>{{$contestents['tenfifteen']}}</td>
        <td>{{$contestents['fifteen']}}</td>
      </tr>
    </table>
    <table>
      <tr>
        <td>TOTAL CONTESTANTS IN A FRAY : </td> <td style="text-align: right;">{{$contestents['Total_Candidates']}}</td></tr>
        <tr>
          <td>AVERAGE CONTESTANTS PER CONSTITUENCY  : </td><td style="text-align: right;">{{$contestents['Avg']}}</td>
        </tr>
        <tr>
          <td>MINIMUM CONTESTANTS IN A CONSTITUENCY : </td> <td style="text-align: right;">{{$contestents['maxcnd']}}</td>
        </tr>
        <tr>
          <td>MAXIMUM CONTESTANTS IN A CONSTITUENCY : </td> <td style="text-align: right;">{{$contestents['mincnd']}}</td>
        </tr>
      </table>
      <p>3. ELECTORS</p>
      <table class="table table-bordered deviis">
        <tr>
          <td colspan="2"></td>
          <td>MALE</td>
          <td>FEMALE</td>
          <td>OTHER</td>
          <td>TOTAL</td>
        </tr>
        <tr>
          <td>i.</td>
          <td>NO. OF ELECTORS(Including Service Electors)</td>
          <td colspan="">{{$contestents['maleElectors']}}</td>
          <td colspan="">{{$contestents['femaleElectors']}}</td>
          <td colspan="">{{$contestents['thirdElectors']}}</td>
          <td colspan="">{{$contestents['totalElectors']}}</td>
        </tr>
        <tr>
          <td>ii.</td>
          <td>NO. OF ELECTORS WHO VOTED AT POLLING STATIONS</td>
          <td colspan="">{{$contestents['totalMaleVoters']}}</td>
          <td colspan="">{{$contestents['totalFemaleVoters']}}</td>
          <td colspan="">{{$contestents['totalOtherVoters']}}</td>
          <td colspan="">{{$contestents['totalMaleVoters']+$contestents['totalFemaleVoters']+$contestents['totalOtherVoters']}}</td>
        </tr>
        <tr>
          <td>iii.</td>
          <td> POLLING PERCENTAGE (EXCLUDE POSTAL BALLOT)</td>
          <td colspan="">{{round($contestents['totalMaleVoters']/$contestents['maleElectors'] * 100,2)}}</td>
          <td colspan="">{{round($contestents['totalFemaleVoters']/$contestents['femaleElectors'] * 100,2)}}</td>
          <td colspan="">{{round($contestents['totalOtherVoters']/$contestents['thirdElectors'] * 100,2)}}</td>
          <td colspan="">{{round(($contestents['totalMaleVoters']+$contestents['totalFemaleVoters']+$contestents['totalOtherVoters'])/$contestents['totalElectors']*100,2)}}</td>
        </tr>
      </table>
      <p> 4. NO. OF SERVICE ELECTORS  </p>
      <table>
        <tr> <td>  &nbsp;&nbsp;&nbsp; MALE  </td><td style="text-align: right;"> {{$contestents['maleServiceElector']}}</td></tr>
        <tr> <td> &nbsp;&nbsp;&nbsp; FEMALE  </td><td style="text-align: right;">  {{$contestents['femaleServiceElector']}}</td></tr>
        </table>

        <table>

&nbsp;

        <tr><td><p>5. NO. OF POSTAL BALLOT RECEIVED   </p> </td><td style="text-align: right;">{{$contestents['total_postal_vote_received']}} </td></tr>

        <tr><td> <p> 6. POLL % (INCLUDING POSTAL BALLOT)</p></td>
        <?php $total = $contestents['totalMaleVoters']+$contestents['totalFemaleVoters']+$contestents['totalOtherVoters']+$contestents['total_postal_vote_received']; ?>
        <td style="text-align: right;">{{round($total/$contestents['totalElectors']*100,2)}}</td>
      </table>

      <p>7. NUMBER OF VALID VOTES
      </p>
      <table>
        <tr>
          <td>&nbsp;&nbsp; VALID VOTES POLLED ON EVM</td><td style="text-align: right;">{{($contestents['totalMaleVoters']+$contestents['totalFemaleVoters']+$contestents['totalOtherVoters'])-($contestents['votes_not_retreived_from_evm']+$contestents['rejected_votes_due_2_other_reason']+$contestents['evmnota'])}}</td></tr>
          <tr>
            <td>&nbsp;&nbsp;  VALID POSTAL VOTES  </td><td style="text-align: right;">{{($contestents['total_postal_vote_received'])-($contestents['postalnota']+$contestents['rejected_postal_vote'])}}</td>
          </tr>
        </table>

        <p>8. TOTAL NOTA VOTES </p>
        <table>
          <tr>
            <td>&nbsp;&nbsp;  'NOTA' VOTES ON EVM</td><td style="text-align: right;">{{$contestents['evmnota']}}</td></tr>
            <tr>
              <td>&nbsp;&nbsp;  'NOTA' VOTES ON POSTAL BALLOT</td><td style="text-align: right;">{{$contestents['postalnota']}}</td>
            </tr>
          </table>

          <p>9. NO. OF VOTES REJECTED</p>
          <table>
            <tr><td>&nbsp;&nbsp; POSTAL</td><td style="text-align: right;">{{$contestents['rejected_postal_vote']}}</td></tr>
            <tr><td>&nbsp;&nbsp; VOTES NOT RETRIEVED ON EVM </td><td style="text-align: right;">{{$contestents['votes_not_retreived_from_evm']}}</td></tr>
            <tr><td>&nbsp;&nbsp; REJECTED DUE TO OTHER REASON(AT POLLING STATION)</td><td style="text-align: right;">{{$contestents['rejected_votes_due_2_other_reason']}}</td></tr>
          </table>
&nbsp;&nbsp; <br>

          <table>
            <tr><td><p>10. TENDERED VOTES </p></td> <td style="text-align: right;">{{$contestents['tended_votes']}}</td></tr>
            <tr><td><p>11.  PROXY VOTES </p></td> <td style="text-align: right;">{{$contestents['proxy_votes']}}</td></tr>
            <tr><td><p>12.  NO. OF POLLING STATION  </p></td> <td style="text-align: right;">{{$contestents['totalpollingstation']}}</td></tr>
            <tr><td><p>13.  AVERAGE NO. OF ELECTORS PER POLLING STATION </p></td> <td style="text-align: right;">{{round(($contestents['totalElectors'])/$contestents['totalpollingstation'],0)}}</td></tr>
            <tr><td><p>14. NO. OF RE-POLLS HELD</p>  </td> <td style="text-align: right;">{{$contestents['total_repoll']}}</td></tr>
          </table>

          <p>15.  PERFORMANCE OF CONTESTING CANDIDATES </p>
          <table class="table table-bordered devi">
            <tr>
              <td colspan="6"></td>
              <td>MALE</td>
              <td>FEMALE</td>
              <td>OTHER</td>
              <td colspan="">TOTAL</td>
            </tr>
            <tr>
              <td>i.</td>
              <td colspan="5">NO. OF CONTESTANTS</td>
              <td>{{$contestents['totalnominatedmale']}}</td>
              <td>{{$contestents['totalnominatedfemale']}}</td>
              <td>{{$contestents['totalnominatedthird']}}</td>
              <td>{{$contestents['totalnominatedmale']+$contestents['totalnominatedfemale']+$contestents['totalnominatedthird']}}</td>
            </tr>
            <tr>
              <td>ii.</td>
              <td colspan="5"> ELECTED</td>
              <td>{{$contestents['totalwinnermale']}}</td>
              <td>{{$contestents['totalwinnerfemale']}}</td>
              <td>{{$contestents['totalwinnerthird']}}</td>
              <td colspan="">{{$contestents['totalwinnermale']+$contestents['totalwinnerfemale']+$contestents['totalwinnerthird']}}</td>
            </tr>
            <tr>
              <td>iii. </td>
              <td colspan="5">FORFEITED DEPOSITS</td>
              <td>{{$contestents['fdmale']}}</td>
              <td>{{$contestents['fdfemale']}}</td>
              <td>{{$contestents['fdthird']}}</td>
              <td colspan="">{{$contestents['fdtotal']}}</td>
            </tr>

           
          </table>
        </div>
        

 <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>


<script type="text/php">
    if (isset($pdf)) {
        $text = "Page {PAGE_NUM}";
        $size = 10;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width);
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size);
    
  
    if (verifyreport(2) == 0){
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
</body>
</html>