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
 padding: 4px;
    text-align: left;
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
    text-transform: uppercase;
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
                        <p style="font-size: 18px !important; text-transform: uppercase;"><b>23 - Participation Of Women Electors in Poll</b></p>
                  </td>
              </tr>
</table>
  <table>
  <?php  if (verifyreport(23) == 0){ ?>
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

                                <tr>
                                    <th class="blc" style="text-align: left;width: 20%;">State/UT</th>
                                    <th class="blc">No. of <br> Seats</th>
                                    <th class="blc">Total <br> Electors</th>
                                    <th class="blc" style="width: 9%">Women <br> Electors</th>
                                    <th class="blc" style="width: 10%;text-align: center;">% of Women <br> Electors <br> Over Total <br> Electors</th>
                                    <th class="blc">Total <br> Voters</th>
                                    <th class="blc">Women <br> Voters</th>
                                    <th class="blc">% of Women <br> Voters Over <br>Voters</th>
                                    <th class="blc">% of Women <br> Voters Over <br> Women <br>Electors</th>
                                    <th class="blc">Total <br>Poll% in <br> the <br> State/UT</th>
                                </tr>
                
								
								
								<?php 
								$totalNpOfSeats = $totalElectors = $totalWomenElectors = $totalWomenElectorsPer = $totalVoters = $totalWomenVoters = $totalWomenVotersPer = $totalWomenVotersOverElectorsPer = $totalVotersPer = 0;
								?>
								@foreach($data as $key => $row)
								
								
								<?php 
								
								if ($row->electors_total > 0){
									$perWomenElectors = ($row->electors_female*100)/$row->electors_total;
									$perWomenElectors = round($perWomenElectors,2);
								}else{
									$perWomenElectors = 0;
								}
								
								if ($row->voter_total > 0){
									$perWomenVoters = ($row->voter_female*100)/$row->voter_total;
									$perWomenVoters = round($perWomenVoters,2);
								}else{
									$perWomenVoters = 0;
								}
								
								if ($row->electors_female > 0){
									$perWomenVotersOverElectors = ($row->voter_female*100)/$row->electors_female;
									
									$perWomenVotersOverElectors = round($perWomenVotersOverElectors,2);
									
								}else{
									$perWomenVotersOverElectors = 0;
								}
								
								if ($row->electors_total > 0){
									$perTotalPoll = ($row->voter_total*100)/$row->electors_total;
									
									$perTotalPoll = round($perTotalPoll,2);
									
								}else{
									$perTotalPoll = 0;
								}
								
								
								$totalNpOfSeats += $row->no_of_seats;
								$totalElectors += $row->electors_total;
								$totalWomenElectors += $row->electors_female;								
								$totalWomenElectorsPer += $perWomenElectors;								
								$totalVoters += $row->voter_total;
								$totalWomenVoters += $row->voter_female;							
								$totalWomenVotersPer += $perWomenVoters;
								$totalWomenVotersOverElectorsPer += $perWomenVotersOverElectors;
								$totalVotersPer += $perTotalPoll;			
								?>
								
								
                                <tr>
                                    <td style="width: 20%;">{{$row->ST_NAME}}</td>
                                    <td>{{$row->no_of_seats}}</td>
                                    <td>{{($row->electors_total)?$row->electors_total:0}}</td>
                                    <td>{{($row->electors_female)?$row->electors_female:0}}</td>
                                    <td style="width: 10%;text-align: center;">{{$perWomenElectors}}</td>                                    
                                    <td>{{($row->voter_total)?$row->voter_total:0}}</td>
                                    <td>{{($row->voter_female)?$row->voter_female:0}}</td>
                                    <td style="width: 10%;text-align: center;">{{$perWomenVoters}}</td>
                                    <td style="text-align: center;width: 7%;">{{$perWomenVotersOverElectors}}</td>
                                    <td style="text-align: center;">{{$perTotalPoll}}</td>
                                </tr>
								
                                @endforeach
								
								<?php 
								if ($totalElectors > 0){
									$totalperWomenElectors = round((($totalWomenElectors*100)/$totalElectors),2);
								}else{
									$totalperWomenElectors = 0;
								}
								
								if ($totalVoters > 0){
									$totalperWomenVoters = round((($totalWomenVoters*100)/$totalVoters),2);
								}else{
									$totalperWomenVoters = 0;
								}
								
								if ($totalWomenElectors > 0){
									$totalperWomenVotersOverElectors = round((($totalWomenVoters*100)/$totalWomenElectors),2);
									
								}else{
									$totalperWomenVotersOverElectors = 0;
								}
								
								if ($totalElectors > 0){
									$totalperTotalPoll = round((($totalVoters*100)/$totalElectors),2);
									
								}else{
									$totalperTotalPoll = 0;
								}
								?>
								
								
								
								
                              <tr  style="font-weight:bold;">
                                    <td class="blcs"><b>TOTAL</b></td>
                                    <td class="blcs"><b>{{$totalNpOfSeats}}</b></td>
                                    <td class="blcs"><b>{{$totalElectors}}</b></td>
                                    <td class="blcs"><b>{{$totalWomenElectors}}</b></td>
                                    <td class="blcs" style="width: 10%;text-align: center;"><b>{{$totalperWomenElectors}}</b></td>
                                    <td class="blcs"><b>{{$totalVoters}}</b></td>
                                    <td class="blcs"><b>{{$totalWomenVoters}}</b></td>
                                    <td class="blcs" style="width: 10%;text-align: center;"><b>{{$totalperWomenVoters}}</b></td>
                                    <td class="blcs" style="text-align: center;width: 7%;"><b>{{$totalperWomenVotersOverElectors}}</b></td>
                                    <td class="blcs" style="text-align: center;"><b>{{$totalperTotalPoll}}</b></td>
                                </tr>
  

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
    
  
    if (verifyreport(23) == 0){
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