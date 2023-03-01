<html>
  <head>
      <style>
	  @page { sheet-size: A3-L; }
	  @page bigger { sheet-size: 420mm 370mm; }
	  @page toc { sheet-size: A4; }

	@page { size: a4 landscape; }

        td {
    font-size: 12px !important;
    font-weight: 500 !important;
    text-align: center;
    padding: 7px 0px;
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
  font-weight: 600 !important;
 } 
 .blcs{
  border-collapse: collapse;
  border-bottom: 1px solid #000;
  border-top: 1px solid #000;
    font-weight: 600 !important;

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
    padding: 4px;
    font-weight: bold !important;
    }
table .dev{
    border-bottom: 1px solod #000;<b>
}
    
    table{
    width: 100%;
    border-collapse: collapse;

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
                        <p style="font-size: 18px !important; text-transform: uppercase;"><b>6 - CANDIDATE DATA SUMMARY ON NOMINATIONS , REJECTIONS,WITHDRAWALS AND DEPOSITS FORFEITED</b></p>
                  </td>
              </tr>
</table>
  <table class="">
     <?php  if (verifyreport(6) == 0){ ?>
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
                        <th colspan="2"></th>
                        <th colspan="4" style="text-decoration: underline;">NOMINATIONS FILED</th>
                        <th colspan="4" style="text-decoration: underline;">NOMINATIONS REJECTED</th>
                        <th colspan="4" style="text-decoration: underline;">NOMINATIONS WITHDRAWN</th>
                        <th colspan="4" style="text-decoration: underline;">CONTESTING CANDIDATES</th>
                        <th colspan="4" style="text-decoration: underline;">DEPOSIT FORFEITED </th>
                    </tr>
                    <tr>

                        <td class="" style="text-align: left;" colspan="2"><b>State/UT</b></td>
                        <td class=""><b>Male</b></td>
                        <td class=""><b>Women</b></td>
                        <td class=""><b>Third Gender</b></td>
                        <td class=""><b>Total</b></td>
                        <td class=""><b>Male</b></td>
                        <td class=""><b>Women</b></td>
						<td class=""><b>Third Gender</b></td>
                        <td class=""><b>Total</b></td>
                        <td class=""><b>Male</b></td>
                        <td class=""><b>Women</b></td>
						<td class=""><b>Third Gender</b></td>
                        <td class=""><b>Total</b></td>
                        <td class=""><b>Male</b></td>
                        <td class=""><b>Women</b></td>
						<td class=""><b>Third Gender</b></td>
                        <td class=""><b>Total</b></td>
                        <td class=""><b>Male</b></td>
                        <td class=""><b>Women</b></td>
						<td class=""><b>Third Gender</b></td>
                        <td class=""><b>Total</b></td>
                    </tr>
                    
                   
                   <!--  <tr>
                        <td colspan="6"><b>Constituency Type/No. Of Seats</b></td>
                        <td colspan="16"></td>
                    </tr> 
					 -->
`						
					 <?php $allcnomfdtotal = $allcnomfdother = $allcnomfdfemale = $allcnomfdmale = $allcnomcototal = $allcnomcother = $allcnomcofemale = $allcnomcomale = $allcnomwtotal = $allcnomwother = $allcnomwfemale = $allcnomwmale = $allcnomrall = $allcnomrother = 
                            $allcnomrfemale = $allcnomrmale = $allCandNomall = $allCandNomOther = $allCandNomFemale = $allcandNomMale = $alltotSeat = 0;  ?>
					
                    
					@foreach($dataArray as $key => $data)
										<tr style="white-space: nowrap;width: 100%;">
                        <td colspan="" class="blc"><b> Constituency Type</b></td>
                        <td colspan="" class="blc"><b>No. of Seats</b></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                        <td class="blc"></td>
                    </tr>

                  </thead>

                                  <tbody>



					 <tr style="width: 100%;">
                        <th colspan="22" style="text-align: left;float: left;"><b>{{$key}}</b></th>
                    </tr> 


					<?php $cnomfdtotal = $cnomfdother = $cnomfdfemale = $cnomfdmale = $cnomcototal = $cnomcother = $cnomcofemale = $cnomcomale = $cnomwtotal = $cnomwother = $cnomwfemale = $cnomwmale = $cnomrall = $cnomrother = 
                                $cnomrfemale = $cnomrmale = $CandNomall = $CandNomOther = $CandNomFemale = $candNomMale = $totSeat = 0; 

						?>
					
					
					@foreach($data as $key => $raw)
                    <tr>
                        <td colspan="">{{$raw['category']}}</td>
                         <td>{{$raw['total_pc']}}</td> 
                        <td>{{$raw['nom_male']}}</td>
                        <td>{{$raw['nom_female']}}</td>
                        <td>{{$raw['nom_third']}}</td>
                        <td>{{$raw['nom_total']}}</td>
                        <td>{{$raw['rej_male']}}</td>
                        <td>{{$raw['rej_female']}}</td>
                        <td>{{$raw['rej_third']}}</td>
                        <td>{{$raw['rej_total']}}</td>
                        <td>{{$raw['with_male']}}</td>
                        <td>{{$raw['with_female']}}</td>
                        <td>{{$raw['with_third']}}</td>
                        <td>{{$raw['with_total']}}</td>
                        <td>{{$raw['cont_male']}}</td>
                        <td>{{$raw['cont_female']}}</td>
                        <td>{{$raw['cont_third']}}</td>
                        <td>{{$raw['cont_total']}}</td>
                        <td>{{$raw['fdmale']}}</td>
                        <td>{{$raw['fdfemale']}}</td>
                        <td>{{$raw['fdthird']}}</td>
                        <td>{{$raw['fd']}}</td>
                    </tr>
					
					<?php $totSeat 		+= $raw['total_pc'];
					$candNomMale 		+= $raw['nom_male'];
					$CandNomFemale 		+= $raw['nom_female'];
					$CandNomOther  		+= $raw['nom_third'];
					$CandNomall   		+= $raw['nom_total'];
					$cnomrmale 			+= $raw['rej_male'];
					$cnomrfemale 		+= $raw['rej_female'];
					$cnomrother  		+= $raw['rej_third'];
					$cnomrall   		+= $raw['rej_total'];
					$cnomwmale  		+= $raw['with_male'];
					$cnomwfemale 		+= $raw['with_female'];
					$cnomwother 		+= $raw['with_third'];
					$cnomwtotal 		+= $raw['with_total'];
					$cnomcomale 		+= $raw['cont_male'];
					$cnomcofemale 		+= $raw['cont_female'];
					$cnomcother 		+= $raw['cont_third'];
					$cnomcototal 		+= $raw['cont_total'];
					$cnomfdmale			+= $raw['fdmale'];
					$cnomfdfemale 		+= $raw['fdfemale'];
					$cnomfdother 		+= $raw['fdthird'];
					$cnomfdtotal 		+= $raw['fd'];
					?>
					
					
					
				@endforeach
				
				<tr style="font-weight:bold;">
					<td class="blcs"><b>Total</td>
					<td class="blcs"><b>{{$totSeat}}</b></td>
					<td class="blcs"><b>{{$candNomMale}}</b></td>
					<td class="blcs"><b>{{$CandNomFemale}}</b></td>
					<td class="blcs"><b>{{$CandNomOther}}</b></td>
					<td class="blcs"><b>{{$CandNomall}}</b></td>
					<td class="blcs"><b>{{$cnomrmale}}</b></td>
					<td class="blcs"><b>{{$cnomrfemale}}</b></td>
					<td class="blcs"><b>{{$cnomrother}}</b></td>
					<td class="blcs"><b>{{$cnomrall}}</b></td>
					<td class="blcs"><b>{{$cnomwmale}}</b></td>
					<td class="blcs"><b>{{$cnomwfemale}}</b></td>
					<td class="blcs"><b>{{$cnomwother}}</b></td>
					<td class="blcs"><b>{{$cnomwtotal}}</b></td>
					<td class="blcs"><b>{{$cnomcomale}}</b></td>
					<td class="blcs"><b>{{$cnomcofemale}}</b></td>
					<td class="blcs"><b>{{$cnomcother}}</b></td>
					<td class="blcs"><b>{{$cnomcototal}}</b></td>
					<td class="blcs"><b>{{$cnomfdmale}}</b></td>
					<td class="blcs"><b>{{$cnomfdfemale}}</b></td>
					<td class="blcs"><b>{{$cnomfdother}}</b></td>
					<td class="blcs"><b>{{$cnomfdtotal}}</b></td>
				</tr>
				
				<?php 
								$alltotSeat 			+= $totSeat;
								$allcandNomMale 		+= $candNomMale;
								$allCandNomFemale 		+= $CandNomFemale;
								$allCandNomOther  		+= $CandNomOther;
								$allCandNomall   		+= $CandNomall;
								$allcnomrmale 			+= $cnomrmale;
								$allcnomrfemale 		+= $cnomrfemale;
								$allcnomrother  		+= $cnomrother;
								$allcnomrall   			+= $cnomrall;
								$allcnomwmale  			+= $cnomwmale;
								$allcnomwfemale 		+= $cnomwfemale;
								$allcnomwother 			+= $cnomwother;
								$allcnomwtotal 			+= $cnomwtotal;
								$allcnomcomale 			+= $cnomcomale;
								$allcnomcofemale 		+= $cnomcofemale;
								$allcnomcother 			+= $cnomcother;
								$allcnomcototal 		+= $cnomcototal;
								$allcnomfdmale			+= $cnomfdmale;
								$allcnomfdfemale 		+= $cnomfdfemale;
								$allcnomfdother 		+= $cnomfdother;
								$allcnomfdtotal 		+= $cnomfdtotal;
				?>
				
				
				
				@endforeach
         
                <tr style="font-weight:bold;">
					<td class="blc"><b>Grand Total</b></td>
					<td class="blc"><b>{{$alltotSeat}}</b></td>
					<td class="blc"><b>{{$allcandNomMale}}</b></td>
					<td class="blc"><b>{{$allCandNomFemale}}</b></td>
					<td class="blc"><b>{{$allCandNomOther}}</b></td>
					<td class="blc"><b>{{$allCandNomall}}</b></td>
					<td class="blc"><b>{{$allcnomrmale}}</b></td>
					<td class="blc"><b>{{$allcnomrfemale}}</b></td>
					<td class="blc"><b>{{$allcnomrother}}</td>
					<td class="blc"><b>{{$allcnomrall}}</b></td>
					<td class="blc"><b>{{$allcnomwmale}}</b></td>
					<td class="blc"><b>{{$allcnomwfemale}}</b></td>
					<td class="blc"><b>{{$allcnomwother}}</b></td><b>
					<td class="blc"><b>{{$allcnomwtotal}}</b></td>
					<td class="blc"><b>{{$allcnomcomale}}</b></td>
					<td class="blc"><b>{{$allcnomcofemale}}</b></td>
					<td class="blc"><b>{{$allcnomcother}}</b></td>
					<td class="blc"><b>{{$allcnomcototal}}</b></td>
					<td class="blc"><b>{{$allcnomfdmale}}</b></td>
					<td class="blc"><b>{{$allcnomfdfemale}}</b></td>
					<td class="blc"><b>{{$allcnomfdother}}</b></td>
					<td class="blc"><b>{{$allcnomfdtotal}}</b></td>
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
    
  
    if (verifyreport(6) == 0){
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