    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Counting Status</title>
       
     <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
      </style>
</head>
<body>
  <div class="bordertestreport">
      <table class="table-strip" style="width: 100%;" align="center">
           <tr>
              <td style="text-align: center;"><p style="font-size: 12px;font-weight: bold; text-align: center;">Election Commission of India, Elections,2019 ( 17 LOK SABHA )</p></td>
            </tr>
          
  </table>


      <table class="table-strip" style="width: 100%;" align="center">
       <tr><td style="text-align: center;">
                        <p style="font-size: 16px;text-transform: uppercase; text-align: center;border: 1px solid #000;height: 30px;"><b>24 - Participation of Women candidates in Poll</b></p>
                  </td>
              </tr>

  </table>
        <!--HEADER ENDS HERE-->
      <br>
        <table style="width:100%; border: 1px solid #000;" border="0" align="center">  
                <?php  if (verifyreport(24) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } else { ?>


    <?php } ?>
              
            </table>
        <table class="table-strip" style="width: 100%;" border="1" align="center">
           <thead>
       <tr>
          <th rowspan="2">State /UT </th>
          <th rowspan="2">Seats</th>
          <th rowspan="2">Catagory</th>
          <th colspan="3" class="text-center">No. Of Women</th>
          <th colspan="2" class="text-center">% of Elected Women</th>
        </tr>
        <tr>
         <th colspan="1">Contestants</th>
         <th colspan="1">Elected</th>
         <th colspan="1">Deposits Forfeited</th>

         <th colspan="1">Over Total Women Candidates in the State</th>
         <th colspan="1">Over total seats in State/UT</th>


       </tr>


    </thead>
         <tbody>
        @php  

        $count = 1;

        $TotalPcs             = 0;
        $TotalContested       = 0;
        $TotalElected         = 0;
        $TotalFd              = 0;
        $OvertTotalWomenState = 0;  
        $OvertTotalSeatsState = 0;   
        
        @endphp

        @forelse($results as $result)
       
        @php

        $TotalPcs               +=$result['seats'];

         if($result['is_state']==1){
     
         $TotalContested         +=$result['cont_female'];
         $TotalElected           +=$result['elected_women'];
         $TotalFd                +=$result['fdfemale'];
         $OvertTotalWomenState   +=$result['over_total_women'];
         $OvertTotalSeatsState   +=$result['over_total_seats'];
       

        }

       @endphp

 
         <tr class="<?php if($result['is_state']==1){ ?> state_row <?php } ?>">
          
            <td>{{ $result['st_name'] }} </td>
            <td> @php if($result['seats'] > 0){ @endphp {{ $result['seats'] }} @php } @endphp</td>
            <td>{{ $result['category'] }}</td>
            <td>{{ $result['cont_female'] }}</td>
            <td>{{ $result['elected_women'] }}</td>
            <td>{{ $result['fdfemale'] }}</td>
            <td> {{ $result['over_total_women'] }}</td>
            <td> {{ $result['over_total_seats'] }}</td>
            


          </tr>

     
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Participation of Women Candidates in Poll </td>                 
              </tr>

          @endforelse

          <tr><td><b>Total</b></td><td><b>{{$TotalPcs}}</b></td><td></td><td><b>{{$TotalContested}}</b></td><td><b>{{$TotalElected}}</b></td><td><b>{{$TotalFd}}</b></td><td><b>{{ ROUND($TotalElected/$TotalContested*100,2)}}</b></td><td><b>@if($TotalPcs)
		  {{ ROUND($TotalElected/$TotalPcs*100,2)}}
	      @endif</b></td></tr>

        
        </tbody>
        </table>
      


 <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>


    <script type="text/php">
    if (isset($pdf)) {
        $text = "{PAGE_NUM} / {PAGE_COUNT}";
        $size = 10;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width);
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size);
		
	
		if (verifyreport(24) == 0){
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