<html>
  <head>
      <style>
        @page { sheet-size: A4-L; }
        @page bigger { sheet-size: 420mm 370mm; }
        @page toc { sheet-size: A4; }
		@page { size: a4 landscape; }
		
        td {
    font-size: 12px !important;
    font-weight: 500 !important;
    text-align: center;
    padding: 4px;
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


    .border{
    border: 1px solid #000;
    }
    th {

    font-size: 12px;
    font-weight: bold !important;
    }

    table{
    width: 100%;
    }
    .blcs{
      border-bottom: 1px solid #000;
 }

 .blc{
      border-bottom: 1px solid #000;
      border-top: 1px solid #000;
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
                        <p style="font-size: 16px !important; text-transform: uppercase;"><b>19 - Political Party Wise Deposit Forfeited </b></p>
                  </td>
              </tr>
</table>
  <table>
     <?php  if (verifyreport(19) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php }  ?>

  </table>


<br>


                <table class="table border">
                    <thead>
                           <tr class="table-primary">
                              <th style="text-align: left;" class=""> State/UT</th>
                              <th class="">No. Of Seats</th>
                              <th colspan="5" style="text-decoration: underline;">Total No. Of Candidates</th>
                              <th colspan="5" style="text-decoration: underline;">Total No. Of Elected Candidates</th>
                              <th colspan="5" style="text-decoration: underline;">Total No. Of Candidates with Forfeiture Of Deposit</th>
                           </tr>
                           <tr>
                              <td class="blcs"></td>
                              <td class="blcs"></td>
                              <th class="blcs">N</th>
                              <th class="blcs">S</th>
                              <th class="blcs">U</th>
                              <th class="blcs">I</th>
                              <th class="blcs">Tot</th>
                              <th class="blcs">N</th>
                              <th class="blcs">S</th>
                              <th class="blcs">U</th>
                              <th class="blcs">I</th>
                              <th class="blcs">Tot</th>
                              <th class="blcs">N</th>
                              <th class="blcs">S</th>
                              <th class="blcs">U</th>
                              <th class="blcs">I</th>
                              <th class="blcs">Tot</th>
                           </tr>
                        </thead>

                        <tbody>
                      <?php

                          $totalseats = $totalN = $totalS = $totalU = $totalZ = $totalCon = $totwinN
                            = $totwinS = $totwinU = $totwinZ = $totwinelected
                            = $totalfdN = $totalfdS = $totalfdU = $totalfdZ = $totalfdTOT = 0;

                      ?>
                     @foreach($statewisedata as  $value)

                           <tr>
                              <td style="text-align: left;">{{$value->ST_NAME}}</td>
                              <td style="text-align: left;">{{$value->TotalSeats}}</td>
                              <td style="text-align: left;">{{$value->N}}</td>
                              <td style="text-align: left;">{{$value->S}}</td>
                              <td style="text-align: left;">{{$value->U}}</td>
                              <td style="text-align: left;"><?php echo $value->Z+$value->Z1 ?></td>
                              <td style="text-align: left;"><?php echo $value->N+$value->S+$value->U+$value->Z+$value->Z1; ?></td>
                              <td style="text-align: left;">{{$value->totalwinner->N}}</td>
                              <td style="text-align: left;">{{$value->totalwinner->S}}</td>
                              <td style="text-align: left;">{{$value->totalwinner->U}}</td>
                              <td style="text-align: left;"><?php echo $value->totalwinner->Z+$value->totalwinner->Z1 ?></td>
                              <td style="text-align: left;"><?php echo $value->totalwinner->Z+$value->totalwinner->Z1+$value->totalwinner->U+$value->totalwinner->S+$value->totalwinner->N ?></td>
                              <td style="text-align: left;">{{$value->totalfd->N}}</td>
                              <td style="text-align: left;">{{$value->totalfd->S}}</td>
                              <td style="text-align: left;">{{$value->totalfd->U}}</td>
                              <td style="text-align: left;">{{$value->totalfd->Z}}</td>
                              <td style="text-align: left;">{{$value->totalfd->FDT}}</td>
                           </tr>

                           <?php
                           $totalseats += $value->TotalSeats;
                           $totalN += $value->N;
                           $totalS += $value->S;
                           $totalU += $value->U;
                           $totalZ += $value->Z+$value->Z1;
                           $totalCon += $value->N+$value->S+$value->U+$value->Z+$value->Z1;
                           $totwinN += $value->totalwinner->N;
                           $totwinS += $value->totalwinner->S;
                           $totwinU += $value->totalwinner->U;
                           $totwinZ += $value->totalwinner->Z+$value->totalwinner->Z1;
                           $totwinelected += $value->totalwinner->Z+$value->totalwinner->Z1+$value->totalwinner->U+$value->totalwinner->S+$value->totalwinner->N;
                           $totalfdN += $value->totalfd->N;
                           $totalfdS += $value->totalfd->S;
                           $totalfdU += $value->totalfd->U;
                           $totalfdZ += $value->totalfd->Z;
                           $totalfdTOT += $value->totalfd->FDT;
                           ?>

                        @endforeach

                        <tr>
                           <td class="blc"  style="text-align: left;font-weight: bold !important;"><b>Grand Total</b></td>
                           <td class="blc"  style="text-align: left;font-weight: bold !important;"><b>{{$totalseats}}</b></td>
                           <td class="blc" style="text-align: left;font-weight: bold !important;"><b>{{$totalN}}</b></td>
                           <td class="blc" style="text-align: left;font-weight: bold !important;"><b>{{$totalS}}</b></td>
                           <td class="blc" style="text-align: left;font-weight: bold !important;"><b>{{$totalU}}</b></td>
                           <td class="blc" style="text-align: left;font-weight: bold !important;"><b>{{$totalZ}}</b></td>
                           <td class="blc" style="text-align: left;font-weight: bold !important;"><b>{{$totalCon}}</b></td>
                           <td class="blc" style="text-align: left;font-weight: bold !important;"><b>{{$totwinN}}</b></td>
                           <td class="blc" style="text-align: left;font-weight: bold !important;"><b>{{$totwinS}}</b></td>
                           <td class="blc" style="text-align: left;font-weight: bold !important;"><b>{{$totwinU}}</b></td>
                           <td class="blc" style="text-align: left;font-weight: bold !important;"><b>{{$totwinZ}}</b></td>
                           <td class="blc" style="text-align: left;font-weight: bold !important;"><b>{{$totwinelected}}</b></td>
                           <td class="blc" style="text-align: left;font-weight: bold !important;"><b>{{$totalfdN}}</b></td>
                           <td class="blc" style="text-align: left;font-weight: bold !important;"><b>{{$totalfdS}}</b></td>
                           <td class="blc" style="text-align: left;font-weight: bold !important;"><b>{{$totalfdU}}</b></td>
                           <td class="blc" style="text-align: left;font-weight: bold !important;"><b>{{$totalfdZ}}</b></td>
                           <td class="blc" style="text-align: left;font-weight: bold !important;"><b>{{$totalfdTOT}}</b></td>
                        </tr>


                        </tbody>


            </table>



<div class="" style=></div>

<table style="text-align: left;width: 100%;" class="left-al">
    <tr><td><p style="font-weight: bold !important;">N : <b>National Parties</b></p>  </td></tr>
    <tr><td><p style="font-weight: bold !important;">S : <b>State Parties</b></p> </td></tr>
    <tr><td><p style="font-weight: bold !important;">U : <b>Registered (Unrecognised)</b></p>  </td></tr>
    <tr><td><p style="font-weight: bold !important;">I : <b>Independents</b></p> </td></tr>
    <tr><td><p style="font-weight: bold !important;">Tot : <b>Total</b></p>  </td></tr>
    <tr><td style="font-weight: bold !important;"><p>Remarks: <b>NOTA Votes not included in the total valid votes polled for candidates</b></p>
 </td></tr>



</table>





            </div>
        

 <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
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
    
  
    if (verifyreport(19) == 0){
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
