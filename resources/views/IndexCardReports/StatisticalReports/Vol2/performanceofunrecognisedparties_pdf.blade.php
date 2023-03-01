<html>
  <head>
      <style>
b{
    font-weight: 600 !important;
}

        td {
    font-size: 12px !important;
    font-weight: 500 !important;
    text-align: center;
    padding: 2px;
    width: 15%;
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
                        <p style="font-size: 18px !important; text-transform: uppercase;"><b>22 - PERFORMANCE OF REGISTERED (UNRECOGNISED) PARTIES</b></p>
                  </td>
              </tr>
</table>
  <table>
     <?php  if (verifyreport(22) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>

  </table> 


<br>



						<table class="table" style="width: 100%;">
            
                                 
                             <thead>
                                <tr>
                                    <th rowspan="2" class="blc" style="border-top: 1px solid #000;">Party Name</th>
                                    
                                    <th colspan="3" class="" style="text-decoration: underline;border-top: 1px solid #000;">Candidates</th>
                                    <th class="blc" rowspan="2" style="border-top: 1px solid #000;">Votes <br>secured by <br> party</th>
                                    <th colspan="2" style="text-decoration: underline;border-top: 1px solid #000;">% of votes secured</th>
                                </tr>

                                <tr>
                                    <th class="blc">Contested</th>
                                    <th class="blc">Won</th>
                                    <th class="blc">DF</th>
                                    <th class="blc">Over Total <br> Electors in  <br> State</th>
                                    <th class="blc">Over Total valid  <br>Votes Polled in <br> State</th>
                                </tr>
                            </thead>
                            <?php 
                                $grandtotalcon = $grandtotalwon = $grandtotalDf = $grandtotalvalid_vote_party 
                                = $grandtotalcon = $grandtotalelector = $grandtotalvotestate = 0;
                            ?>

                            

                            @foreach($performanceofst as $value)
                            
                            
                            <tbody>

                              
                            

                                <tr>
                                     <?php if($value->PARTYTYPE == 'S') { ?>
                                    <td style="text-align: left;">{{$value->PARTYNAME}}<span style="color: black"><b>*</b></span></td>
                                   <?php } else { ?>
                                    <td style="text-align: left;">{{$value->PARTYNAME}}</td>
                                <?php } ?>
                                    <td>{{$value->totalcontested}}</td>
                                    <td>{{$value->won}}</td>
                                    <td>{{$value->DF}}</td>
                                    <td>{{$value->totalvalid_valid_vote_party}}</td>
                                    <td>{{round($value->totalvalid_valid_vote_party/$value->TOTAL_ELECT_VOTE*100,4)}}</td>
                                    <td>{{round($value->totalvalid_valid_vote_party/$value->Total_Valid_Vote_State*100,4)}}</td>

                                </tr>

                                <?php 

                                    $grandtotalcon += $value->totalcontested;
                                    $grandtotalwon += $value->won;
                                    $grandtotalDf += $value->DF;
                                    $grandtotalvalid_vote_party += $value->totalvalid_valid_vote_party;
                                    $grandtotalelector +=  $value->TOTAL_ELECT_VOTE;
                                    $grandtotalvotestate +=  $value->Total_Valid_Vote_State;
                                    
                                ?>

                                @endforeach
                                

                               

                               
                                <tr>
                                    <th class="blcs" style="text-align: left;">Grand Total</th>
                                    <td class="blcs"><b>{{$grandtotalcon}}</b></td>

                                    <td class="blcs"><b>{{$grandtotalwon}}</b></td>
                                    <td class="blcs"><b>{{$grandtotalDf}}</b></td>
                                    <td class="blcs"><b>{{$grandtotalvalid_vote_party}}</b></td>
                                    <td class="blcs"><b>{{round($grandtotalvalid_vote_party/$grandtotalelector*100,4)}}</b></td>
                                    <td class="blcs"><b>{{round($grandtotalvalid_vote_party/$grandtotalvotestate*100,4)}}</b></td>
                                   

                                </tr>



                                <tr>
                                  <td colspan="7" style="text-align: left;"><span><b>* State Party</b></span></td>
                                </tr>

                            </tbody>

              
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
    
  
    if (verifyreport(22) == 0){
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