<!DOCTYPE html>
<html lang="en">
<head>
    <style>
    td {
    font-size: 10px !important;
    font-weight: 500 !important;
    color: #000 !important;
    text-align: left;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
    }
    .new{
    border-collapse: separate;
    border-spacing:0px 12px;
    }
    .new tr{
    padding: 10px;
    margin: 10px;
    }

    p{
      font-size: 13px;
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
    }
    .borders{
    border-collapse: collapse;
    font-size: 13px !important;
    }
    .borders td{
    text-align: left;
    font-size: 13px !important;
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    }
    .border{
    border: 1px solid #000;
    }
    .new22 th {
    color: #000 !important;
    font-size: 12px;
    font-weight: bold !important;
    padding: 10px;
    position: relative;
    left: 10px !important;
    text-align: center;
    }
    .new22{
    border:1px solid #000;
    }
    .new td{
    border:1px solid #000;
    padding: 10px;
    font-size: 12px;
    vertical-align: middle;
    text-align: center;
    border-collapse: separate;
    border-spacing: 12px;
    }
    table{
    width: 100%;
    }
    </style>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
  
  <div class="bordertestreport">
    <table>
            <tr>
              <td style="text-align: center; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, Elections,2019 ( 17 LOK SABHA )</strong></p></td>
            </tr>
            
    </table>


    <table class="border">
         <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 20px !important; text-transform: uppercase;"><b>1 - The Schedule of GE to Lok Sabha {{getElectionYear()}}</b></p>
                  </td>
              </tr>

    </table>
    <table class="">
 
      <?php  if (verifyreport(1) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } else { ?>

    <?php } ?>
    </table>
    <h5 style="text-align: center;font-weight: bold;font-size: 16px;" class="pb-2" >PHASE GENERAL ELECTIONS-{{getElectionYear()}}</h5>
    <table class="new22" style="width: 100%;">
      <tr>
        <th>PHASE</th>
        <th>NUMBER OF STATE & <br>   UNION TERRITORIES</th>
        <th>NUMBER OF PARLIAMENTARY <br>   CONSTITUENCIES</th>
        <th>POLL DATES </th>
      </tr>
    </table>
    <table class="new">
      @foreach($data as $row)
      <tr>
        <td style="width: 10%;">{{$row->SCHEDULEID}}</td>
        <td style="width: 30%;">{{$row->no_state}}</td>
        <td style="width: 30%;">{{$row->no_pc}}</td>
        <td style="width: 10%;">{{date('d M Y', strtotime($row->DATE_POLL))}}</td>
      </tr>
      @endforeach
    </table>
	
	<div style="page-break-after: always;" > </div>
	
    <div class="border">
      <h5 style="font-weight: bold;text-align: center;font-size: 16px;" class="pb-2">NUMBER OF PHASES IN STATES AND UNION TERRITORIES</h5>
      <table>
        <tr>
          <th style="text-align: center;">Sr No</th>
          <th style="text-align: center;">NO. OF PHASES</th>
          <th style="text-align: center;">STATES AND UNION TERRITORIES</th>
        </tr>
      </table>
      <table class="borders">
        @foreach($data2 as $key => $row)
        <tr class="borders">
          <td style="text-align: center;width: 14%;">{{$key+1}}</td>
          <td style="width: 30%;text-align: center;">{{$row->no_phase}}</td>
          <td style="width: 50%;">{{$row->ST_NAME}}</td>
        </tr>
        @endforeach
        
      </table>
     
        
    
          
        
      
    </div>

  </div>


 <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>


<script type="text/php">
    if (isset($pdf)) {
        $text = "Page {PAGE_NUM}";
        $size = 10;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width);
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size);
		
	
		if (verifyreport(1) == 0){
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