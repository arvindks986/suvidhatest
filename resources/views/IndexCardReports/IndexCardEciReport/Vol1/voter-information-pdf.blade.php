<html>
  <head>
      <style>

        @page { sheet-size: A3-L; }
        @page bigger { sheet-size: 420mm 370mm; }
        @page toc { sheet-size: A4; }
        @page { size: a3 landscape; }
		
        td {
    font-size: 12px !important;
    font-weight: 500 !important;
    text-align: center;
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

    .blc{
  border-bottom: 1px solid #000 !important;
    border-collapse: collapse;

 } 
 .blcs{
  border-bottom: 1px solid #000 !important;
  border-top: 1px solid #000 !important;
  font-weight: bold !important;
    border-collapse: collapse;

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
                        <p style="font-size: 20px !important; text-transform: uppercase;"><b>10 - VOTERS INFORMATION </b></p>
                  </td>
              </tr>
  </table>
<br>
  <table>
  
 <?php  if (verifyreport(10) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php }  ?>
  </table> 

<br>
               <table class="table borders" style="width: 100%;">
              <thead>
                <tr class="table-primary">
                  <th scope="col" rowspan="2" class="blc">State/UT</th>
                  <th scope="col" rowspan="2" class="blc">Constituency Type</th>
                  <th scope="col" rowspan="2" class="blc">Seats</th>
                  <th colspan="6">Electors</th>
                  <th colspan="7">Voters</th>
                  <th scope="col">Rejected <br> Votes <br>(Postal)</th>
                  <th scope="col"> Votes Rejected <br> / Votes Not <br> Retrived <br> From EVM</th>
                  <th scope="col">NOTA <br> Votes </th>
                  <th scope="col">Valid <br> Votes <br> Polled</th>
                  <th scope="col">Tendered Votes</th>
                </tr>


                <tr>
                    <th class="blc">Male</th>
                    <th class="blc">Female</th>
                    <th class="blc">Third Gender</th>
                    <th class="blc">Total</th>
                    <th class="blc">NRIs</th>
                    <th class="blc">Service</th>
                
                    <th class="blc">Male</th>
                    <th class="blc">Female</th>
                    <th class="blc">Third Gender</th>
                    <th class="blc">Postal</th>
                    <th class="blc">Total</th>
                    <th class="blc">NRIs</th>
                    <th class="blc">Poll %</th>
                    <th class="blc"></th>
                    <th class="blc"></th>
                    <th class="blc"></th>
                    <th class="blc"></th>
                    <th class="blc"></th>


                </tr>
              </thead>

<tbody>
  
     <?php 

        $grandtotal= $grandseattotal= $grandemaletotal= $grandefemaletotal = $grandeothertotal = $grandestatetotal
        = $grandnrielectorstotal =$grandserviceelectorstotal = $grandgenmalevotertotal = $grandgenfemalevotertotal 
        = $grandgenothervotertotal = $grandpostaltotalstate = $grandtotalvotestate =$grandtotalnristate 
        = $grandpostalrejectedtotal = $grandvotesnotretrivedtotal = $grandnotavotetotal = $grandtendedvotetotal = $grandtestvote = $grandduetoother = 0; 

     ?>
  
  
      @foreach($voterarray as $row1 => $value1)
      <tr><td><b>{{$row1}}</b></td>

<td colspan="20"></td>

      </tr>

      <?php $seattotal = $emaletotal= $efemaletotal = $eothertotal = $estatetotal = $nrielectorstotal 
      = $serviceelectorstotal = $genmalevotertotal = $genfemalevotertotal = $genothervotertotal = $postaltotalstate 
      = $totalvotestate = $totalnristate = $postalrejectedtotal = $votesnotretrivedtotal = $notavotetotal = $tendedvotetotal =  $testtotal = $duetototal = 0; ?>

      @foreach($value1 as $row2 => $value2)

      
      

       <tr>
        
        <td></td> 
       

        <td>{{$value2['pc_type']}}</td>
        
        <td>{{$value2['seats']}}</td>
        <td>{{$value2['emale']}}</td>
        <td>{{$value2['efemale']}}</td>
        <td>{{$value2['eother']}}</td>
        <td>{{$value2['etotal']}}</td>
        <td>{{$value2['nrielectors']}}</td>
        <td>{{$value2['serviceelectors']}}</td>
        <td>{{$value2['general_male_voters']}}</td>
        <td>{{$value2['general_female_voters']}}</td>
        <td>{{$value2['general_other_voters']}}</td>
        <td>{{$value2['postaltotalvote']}}</td>
        <td>{{$value2['total_vote']}}</td>

        <td>{{$value2['voternri']}}</td>

        <td>{{round($value2['total_vote']/$value2['etotal']*100,2)}}</td>
        <td>{{$value2['postal_votes_rejected']}}</td>
        <td>{{$value2['votes_not_retreived_from_evm']+$value2['rejected_votes_due_2_other_reason']}}</td>
        <td>{{$value2['nota_vote']}}</td>
        
        <td>{{$value2['total_vote']-($value2['postal_votes_rejected']+$value2['votes_not_retreived_from_evm']+$value2['nota_vote']+$value2['rejected_votes_due_2_other_reason'])}}</td>
       <td>{{$value2['tended_votes']}}</td>
      

        

    </tr>

    <?php 

    $seattotal += $value2['seats']; 
    $emaletotal += $value2['emale'];
    $efemaletotal += $value2['efemale'];
    $eothertotal += $value2['eother'];
    $estatetotal += $value2['etotal'];

    $nrielectorstotal += $value2['nrielectors'];
    $serviceelectorstotal += $value2['serviceelectors'];
    $genmalevotertotal += $value2['general_male_voters'];
    $genfemalevotertotal += $value2['general_female_voters'];
    $genothervotertotal += $value2['general_other_voters'];

    $postaltotalstate += $value2['postaltotalvote'];
    $totalvotestate += $value2['total_vote'];
    $totalnristate += $value2['voternri'];
    $postalrejectedtotal += $value2['postal_votes_rejected'];
    $votesnotretrivedtotal  += $value2['votes_not_retreived_from_evm'];

    $notavotetotal += $value2['nota_vote'];
    $tendedvotetotal += $value2['tended_votes'];

    $testtotal += $value2['test_votes_49_ma'];
    $duetototal += $value2['rejected_votes_due_2_other_reason'];



    ?>

@endforeach

<tr>
    <td class="blcs"><b>State Total</b></td>
    <td class="blcs" style="border-bottom: 1px solid #000 !important;"></td>
        <td class="blcs"><b>{{$seattotal}}</b></td>
        <td class="blcs"><b>{{$emaletotal}}</b></td>
        <td class="blcs"><b>{{$efemaletotal}}</b></td>
        <td class="blcs"><b>{{$eothertotal}}</b></td>
        <td class="blcs"><b>{{$estatetotal}}</b></td>
        <td class="blcs"><b>{{$nrielectorstotal}}</b></td>
        <td class="blcs"><b>{{$serviceelectorstotal}}</b></td>

        <td class="blcs"><b>{{$genmalevotertotal}}</b></td>
        <td class="blcs"><b>{{$genfemalevotertotal}}</b></td>
        <td class="blcs"><b>{{$genothervotertotal}}</b></td>
        <td class="blcs"><b>{{$postaltotalstate}}</b></td>
        <td class="blcs"><b>{{$totalvotestate}}</b></td>
        <td class="blcs"><b>{{$totalnristate}}</b></td>


        <td class="blcs">{{round($totalvotestate/$estatetotal*100,2)}}</td>
        <td class="blcs"><b>{{$postalrejectedtotal}}</b></td>
        <td class="blcs">{{$votesnotretrivedtotal+$duetototal}}</td>
        <td class="blcs"><b>{{$notavotetotal}}</b></td>
        <td class="blcs"><b>{{$totalvotestate-($postalrejectedtotal+$votesnotretrivedtotal+$notavotetotal+$duetototal)}}</b></td>
        <td class="blcs"><b>{{$tendedvotetotal}}</b></td>
        

</tr>

<?php 

    $grandseattotal += $seattotal;
    $grandemaletotal += $emaletotal;
    $grandefemaletotal += $efemaletotal;
    $grandeothertotal += $eothertotal;
    $grandestatetotal += $estatetotal;

    $grandnrielectorstotal += $nrielectorstotal;
    $grandserviceelectorstotal += $serviceelectorstotal;

    $grandgenmalevotertotal += $genmalevotertotal;
    $grandgenfemalevotertotal += $genfemalevotertotal;
    $grandgenothervotertotal += $genothervotertotal;

    $grandpostaltotalstate += $postaltotalstate;
    $grandtotalvotestate += $totalvotestate;
    $grandtotalnristate += $totalnristate;

    $grandpostalrejectedtotal += $postalrejectedtotal;
    $grandvotesnotretrivedtotal += $votesnotretrivedtotal;

    $grandnotavotetotal += $notavotetotal;
    $grandtendedvotetotal += $tendedvotetotal;


    $grandtestvote += $testtotal;
    $grandduetoother += $duetototal;
    
    


    ?>


@endforeach

<tr>
    <td class="blcs"><b>Grand Total</b></td>
    <td class="blcs"></td>
        <td class="blcs"><b>{{$grandseattotal}}</b></td>
        <td class="blcs"><b>{{$grandemaletotal}}</b></td>
        <td class="blcs"><b>{{$grandefemaletotal}}</b></td>
        <td class="blcs"><b>{{$grandeothertotal}}</b></td>
        <td class="blcs"><b>{{$grandestatetotal}}</b></td>

       

        <td class="blcs"><b>{{$grandnrielectorstotal}}</b></td>
        <td class="blcs"><b>{{$grandserviceelectorstotal}}</b></td>
        <td class="blcs"><b>{{$grandgenmalevotertotal}}</b></td>
        <td class="blcs"><b>{{$grandgenfemalevotertotal}}</b></td>
        <td class="blcs"><b>{{$grandgenothervotertotal}}</b></td>
        <td class="blcs"><b>{{$grandpostaltotalstate}}</b></td>
        <td class="blcs"><b>{{$grandtotalvotestate}}</b></td>
        <td class="blcs"><b>{{$grandtotalnristate}}</b></td>


        <td class="blcs">{{round($grandtotalvotestate/$grandestatetotal*100,2)}}</td>

        <td class="blcs"><b>{{$grandpostalrejectedtotal}}</b></td>
         <td><b>{{$grandvotesnotretrivedtotal + $grandduetoother}}</b></td>
        <td class="blcs"><b>{{$grandnotavotetotal}}</b></td>

        <td class="blcs">{{$grandtotalvotestate-($grandpostalrejectedtotal+$grandvotesnotretrivedtotal+$grandnotavotetotal+$grandduetoother)}}</td>

        <td class="blcs"><b>{{$grandtendedvotetotal}}</b></td>
        

</tr>


</tbody>

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
    
  
    if (verifyreport(10) == 0){
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