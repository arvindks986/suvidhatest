<html>
  <head>
      <style>

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

    .blc{
  border-collapse: collapse;
  border-bottom: 1px solid #000;
  border-spacing: 0px 8px;
 } 
 .blcs{
  border-collapse: collapse;
  border-bottom: 1px solid #000;
  border-top: 1px solid #000;
  font-weight: bold !important;
 }
   


    .border{
    border: 1px solid #000;
    }   
    .borders{
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    }
    th {
    font-size: 13px;
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
                        <p style="font-size: 20px !important; text-transform: uppercase;"><b>21 - PERFORMANCE OF STATE PARTIES</b></p>
                  </td>
              </tr>

</table>
  <table>
      <?php  if (verifyreport(21) == 0){ ?>
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
                                    <th>Party Name</th>
                                    <th style="">State in which <br> the party is recognised</th>
                                    <th colspan="3" style="text-decoration: underline;">Candidates</th>
                                    <th rowspan="2" class="blc">Votes <br> Secured By <br> Party</th>
                                    <th colspan="4" style="text-decoration: underline;">% of votes secured</th>
                                </tr>

                                <tr>
                                    <th class="blc"></th>
                                    <th class="blc"></th>
                                    <th class="blc">Contested</th>
                                    <th class="blc">Won</th>
                                    <th class="blc">DF</th>
                                    <th class="blc">Over total <br> elector in <br> the state</th>
                                    <th class="blc">Over total valid <br> votes polled  in <br> the state</th>
                                </tr>
                            </thead>

                <tbody>
							
								@php
								$grand_total_contested = 0;
								$grand_total_won = 0;
								$grand_total_df = 0;
								$grand_total_vote_secure = 0;
								$grand_total_vote = 0;
								$grand_total_electors = 0;
								@endphp
							
							
                                @foreach($arraydata as $rowdatas)

                                <tr>
                                    <td><b>{{$rowdatas['partyabbre']}}</b></td><td><b> ({{$rowdatas['partyname']}})</b></td>
                                    <td colspan="6"></td>
                                </tr>

								@php
								$total_vote_secure = 0;
								$total_vote = 0;
								$total_electors = 0;
								@endphp


                                @foreach($rowdatas['partydata'] as $rowdata)
                                <tr>
                                    <td colspan="2" style="text-align: center;">{{$rowdata['statename']}}</td>
                                    <td>{{$rowdata['contested']}} </td>
                                    <td>{{$rowdata['won']}} </td>
                                    <td>{{$rowdata['df']}} </td>
                                    <td>{{$rowdata['Securedvotes']}} </td>
									<td>{{$rowdata['totalelectors']}} </td>
                                    <td>{{$rowdata['poledvotespercent']}} </td>
                                    
                                </tr>
								
								@php
								$total_vote_secure += $rowdata['Securedvotes'];
								$total_vote += $rowdata['total_vote'];
								$total_electors += $rowdata['total_electorsdata'];
																
								$grand_total_contested += $rowdata['contested'];
								$grand_total_won += $rowdata['won'];
								$grand_total_df += $rowdata['df'];
								$grand_total_vote_secure += $rowdata['Securedvotes'];
								$grand_total_vote += $rowdata['total_vote'];
								$grand_total_electors += $rowdata['total_electorsdata'];
								
								@endphp
														
                                @endforeach
								<tr>
                                    <td  class="blcs">Party Total</td>
                                    <td class="blcs"></td>
                                    <td class="blcs">{{array_sum($rowdatas['totalcontested'])}}</td>
                                    <td class="blcs">{{array_sum($rowdatas['won'])}}</td>
                                    <td class="blcs">{{array_sum($rowdatas['DF'])}}</td>
                                    <td class="blcs">{{array_sum($rowdatas['Securedvotes'])}}</td>
									<td class="blcs">{{round(((($total_vote_secure)/$total_electors)*100),2)}}</td>
									<td class="blcs">{{round(((($total_vote_secure)/$total_vote)*100),2)}}</td>
                                    
                                </tr>

                                @endforeach


								<tr>
                                    <td  class="blcs">Grand Total</td>
                                    <td  class="blcs"></td>
                                    <td  class="blcs">{{$grand_total_contested}}</td>
                                    <td  class="blcs">{{$grand_total_won}}</td>
                                    <td  class="blcs">{{$grand_total_df}}</td>
                                    <td  class="blcs">{{$grand_total_vote_secure}}</td>
									 <td  class="blcs">{{round(((($grand_total_vote_secure)/$grand_total_electors)*100),2)}}</td>
									<td  class="blcs">{{round(((($grand_total_vote_secure)/$grand_total_vote)*100),2)}}</td>
                                   
                                </tr>

                 </tbody>


                    </table>
                </div>
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
    
  
    if (verifyreport(21) == 0){
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