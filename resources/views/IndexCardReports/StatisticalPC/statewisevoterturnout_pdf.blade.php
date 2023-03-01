<html>
  <head>
      <style>
        td {
    font-size: 12px !important;
    font-weight: 500 !important;
    text-align: center;
    font-family: "Times New Roman", Times, serif;
    }
    b{
      font-weight: 600 !important;
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
    border-collapse: collapse;
    }

.border td{
  border-collapse: collapse;
  border-top: 1px solid #000;
  border-spacing: 2px;
  text-align: left;
  padding: 10px;
}

.border td:last-child {
  border: none;

}
      .borders{
    border-bottom: 1px solid #000;
    border-top: 1px solid #000;
    }



    th {
    color: #000 !important;
    font-size: 13px;
    font-weight: bold !important;
    vertical-align: middle;
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
                        <p style="font-size: 18px !important; text-transform: uppercase;"><b>12 - State wise Voter TurnOut </b></p>
                  </td>
              </tr>
</table>
<br>
  <table>
     <?php  if (verifyreport(12) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>


  </table> 


                <table class="border">
  <thead class="thead-light">
   <tr>
            <th scope="col">SL.No</th>
            <th scope="col">State/UT</th>
            <th scope="col" colspan="2" style="border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;text-align: center;"> Electors</th>
            <th scope="col" colspan="2" style="border-right: 1px solid #000;border-bottom: 1px solid #000;text-align: center;"> Voters</th>
            <th scope="col" style="text-align: center;"> Voters <br> Turn <br>Out (%)</th>
          </tr>
        <tr style="border-bottom: 1px solid #000;">
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col" style="border-left: 1px solid #000;text-align: center; border-right: 1px solid #000;"> General <br>(Including <br>NRIs)</th>
            <th scope="col" style="text-align: center;"> Service</th>
            <th scope="col" style="border-left: 1px solid #000;border-right: 1px solid #000;text-align: center;"> EVM</th>
            <th scope="col" style="border-right: 1px solid #000;text-align: center;"> Postal</th>
            <th scope="col" style="border-bottom: 1px solid #000;"></th>
          </tr>

  </thead>
    
	<?php $e_gen_t = $e_ser_t = $vt_all_t = $postal_valid_votes =0;  ?>
					
				 @php
					$i=1
					@endphp  
				@foreach ($statewisevoterturnouts as $statewisevoterturnout) 
					<?php $votes = \App\models\Admin\VoterModel::get_total([
						'group_by' => 'st_code',
						'st_code' => $statewisevoterturnout->st_code
					]); ?>
					 <tr>
					  <td>{{$i}}</td>
					  <td style="width: 23%;">{{ $statewisevoterturnout->ST_NAME }}</td>
					  <td>{{ $statewisevoterturnout->e_gen_t }}</td>
					  <td>{{ $statewisevoterturnout->e_ser_t }}</td>
					  <td>{{ $votes['vt_all_t'] }}</td>
					  <td>{{ $votes['postal_valid_votes'] }}</td>
					  <td style="border-bottom: 1px solid #000;">@if(($statewisevoterturnout->e_gen_t+$statewisevoterturnout->e_ser_t) > 0)	  {{round(((($votes['vt_all_t']+$votes['postal_valid_votes'])/($statewisevoterturnout->e_gen_t+$statewisevoterturnout->e_ser_t))*100),2) }}
					  @else
						  0
					  @endif
					  </td>
					</tr>
					
					
					
					@php
					$e_gen_t += $statewisevoterturnout->e_gen_t;
					$e_ser_t += $statewisevoterturnout->e_ser_t;
					$vt_all_t += $votes['vt_all_t'];
					$postal_valid_votes += $votes['postal_valid_votes'];
													
					$i++
					@endphp
				@endforeach

								
					 <tr style="font-weight:bold;">
					  <td colspan="2" style="text-align: left"><b>TOTAL</b></td>
					  <td><b>{{ $e_gen_t }}<b></td>
					  <td><b>{{ $e_ser_t }}</b></td>
					  <td><b>{{ $vt_all_t }}</b></td>
					  <td><b>{{ $postal_valid_votes }}</b></td>
					  <td><b>
						@if(($e_gen_t+$e_ser_t) > 0)	  
						{{round(((($vt_all_t+$postal_valid_votes)/($e_gen_t+$e_ser_t))*100),2) }}</b>
						@else
						0
						@endif
					  </td>
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
    
  
    if (verifyreport(12) == 0){
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