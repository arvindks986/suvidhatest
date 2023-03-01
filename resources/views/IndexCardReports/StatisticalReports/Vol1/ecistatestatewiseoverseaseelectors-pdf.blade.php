<html>
  <head>
      <style>
        td {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #000 !important;
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
                        <p style="font-size: 19px !important; text-transform: uppercase;"><b>11 - State Wise Participation of Overseas Electors Voters </b></p>
                  </td>
              </tr>

</table>
  <table>
       <?php  if (verifyreport(11) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>

  </table> 

  <div class="card-body">

                    <?php 

                         $grandemale = $grandfemale =$grandother = $grandtotal = $grandvemale 
                         =$grandvefemale =$grandvother =$grandvtotals  = 0;
                    ?>

                  
                        @foreach($data as $key => $value)

                            <p><b>{{$key}}</b></p>

                       



   <table class="table table-bordered" style="width: 100%;">
                         

                        

                        

                                <tr>
                                    <th scope="col">PC TYPE</th>
                                    <th colspan="4">Electors</th>
                                    <th colspan="4">Voters</th>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><b>Male</b></td>
                                    <td><b>Female</b></td>
                                    <td><b>Third Gender</b></td>
                                    <td><b>Total <br> Electors</b></td>
                                    <td><b>Male</b></td>
                                    <td><b>Female</b></td>
                                    <td><b>Third Gender</b></td>
                                    <td><b>Total <br>Voters</b></td>
                                </tr>
                                <?php 


                                            $totemale = $totefemale =$toteother = $totetotal = $totvemale
                                            = $totvefemale = $totvother = $totvtotals = 0;
                                ?>

                                 @foreach($value as $key1 => $value1)

                                        <tr>
                                            <td><b>{{$key1}}</b></td>
                                            <td>{{$value1['emale']}}</td>
                                            <td>{{$value1['efemale']}}</td>
                                            <td>{{$value1['eother']}}</td>
                                            <td>{{$value1['etotal']}}</td>
                                            <td>{{$value1['vemale']}}</td>
                                            <td>{{$value1['vefemale']}}</td>
                                            <td>{{$value1['vother']}}</td>
                                            <td>{{$value1['vtotals']}}</td>
                                        </tr>

                                        <?php 
                                            $totemale += $value1['emale'];
                                            $totefemale += $value1['efemale'];
                                            $toteother += $value1['eother'];
                                            $totetotal += $value1['etotal'];
                                            $totvemale += $value1['vemale'];
                                            $totvefemale += $value1['vefemale'];
                                            $totvother += $value1['vother'];
                                            $totvtotals += $value1['vtotals'];
                                        ?>

                                        
                                         

                                        @endforeach

                                          <tr>
                                            <td style="font-weight: 600 !important;"><b>STATE TOTAL:</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$totemale}}</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$totefemale}}</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$toteother}}</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$totetotal}}</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$totvemale}}</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$totvefemale}}</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$totvother}}</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$totvtotals}}</b></td>
                                        </tr>

                                      
                                      <?php 
                                            $grandemale += $totemale;
                                            $grandfemale += $totefemale;
                                            $grandother += $toteother;
                                            $grandtotal += $totetotal;
                                            $grandvemale += $totvemale;
                                            $grandvefemale += $totvefemale;
                                            $grandvother += $totvother;
                                            $grandvtotals += $totvtotals;
                                        ?>   



                        </table>

                        
                        @endforeach


<table class="table table-bordered">
                          <tr>
                                            <td style="font-weight: 600 !important;"><b>TOTAL:</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$grandemale}}</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$grandfemale}}</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$grandother}}</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$grandtotal}}</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$grandvemale}}</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$grandvefemale}}</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$grandvother}}</b></td>
                                            <td style="font-weight: 600 !important;"><b>{{$grandvtotals}}</b></td>
                                        </tr>
</table>





                    </div>




            
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
    
  
    if (verifyreport(11) == 0){
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
