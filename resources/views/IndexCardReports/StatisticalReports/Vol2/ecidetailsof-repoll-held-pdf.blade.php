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

     
        th {
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
    th {
    color: #000 !important;
    font-size: 12px;
    font-weight: bold !important;
    }
    td{
      border-bottom: none;
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
                        <p style="font-size: 20px !important; text-transform: uppercase;"><b>16 - Details of Re-poll held </b></p>
                  </td>
              </tr>

</table>
  <table>
  <?php  if (verifyreport(16) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>

  </table> 



<table class="table border" style="width: 100%;">

                             <tr>
                                    <th class="blcs" style="width: 20%;">Name of State/UT</th>
                                    <th class="blcs" style="width: 20%;">Total No. of Polling <br> Station in state</th>
                                    <th class="blcs">No. of P.C.</th>
                                    <th class="blcs">Name of P.C.</th>
                                    <th class="blcs">Total No. of <br> Polling Station <br> where repoll held</th>
                                    <th class="blcs">Date of <br> Re-Poll</th>
                                </tr>
                                <?php
                                $totalpolling = 0;
                                $ftotal = 0;?>
                                @forelse($data as $rows)
                                <?php    $i = 0;
                                if ($i != 0 && $state != $rows['state_name']) {
                                  ?>
                                    <tr colspan='3'><td>Total</td><td> <?php echo $rows['totalrepoll'] ?></td></tr>";
                                <?php }  ?>
                                <tr>
                                    <td rowspan="<?php echo sizeof($rows['pcinfo']); ?>">{{$rows['state_name']}}</td>
                                    <td rowspan="<?php echo sizeof($rows['pcinfo']);
                                     ?>">{{($rows['total_no_polling_station'])?$rows['total_no_polling_station']:'NILL'}}</td>
									 
									 <?php
                                    $total = 0;
                                        ?>
                                    @foreach($rows['pcinfo'] as $subrows)
                                    <?php
                                    if ($i != 0) {
                                        ?>
                                    <tr>
                                    <?php } ?>
                                    <td style="text-align: center;">{{$subrows['PC_NO']}}</td>
                                    <td>{{$subrows['PC_NAME']}}</td>
                                    <td style="text-align: center;">{{($subrows['no_repoll'])}}</td>
                                    <td style="text-align: center;">
									@if (trim($subrows['dt_repoll']) != 0 && $subrows['dt_repoll'])
													
												<?php 
													$repoll_dates 	= explode(',',$subrows['dt_repoll']);
													$dates_array 	= [];
													foreach($repoll_dates as $res_repoll){
														$dates_array[] = date('d-m-Y', strtotime(trim($res_repoll)));
													}	
												?>
												
												{!! implode(', ', $dates_array) !!}
												@endif
									</td>
                              
                                <?php
                                $total += $subrows['no_repoll'];
                                $ftotal += $subrows['no_repoll'];
                                $i++;
                                ?>
					
                                @endforeach
  
                        </tr>
						<tr>
                                    <td class="blcs"></td>
                                    <td class="blcs"></td>
                                    <td class="blcs"></td>
                                    <td class="blcs"><b>Total:</b></td>
                                    <td style="text-align: center;" class="blcs"><b>{{$total}}</b></td>
                                    <td class="blcs"></td>
                                    
                                </tr>
                                @empty
                                <tr>
                                  <td colspan="6">Data not Found</td></tr>
                                @endforelse

            
								<tr>
                                    <td><b>ALL INDIA</b></td>
                                    <td></td>
                                    <td></td>
                                    <td><b>Grand Total:</b></td>
                                    <td style="text-align: center;"><b>{{$ftotal}}</b></td>
                                    <td></td>
                                    
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
    
  
    if (verifyreport(16) == 0){
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