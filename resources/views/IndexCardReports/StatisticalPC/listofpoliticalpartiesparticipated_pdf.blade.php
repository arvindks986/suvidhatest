<html>
  <head>
      <style>

        td {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #000 !important;
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

    }

 .borders{
  border-collapse: collapse;

    border-bottom: 1px solid #000;
    border-top: 1px solid #000;
    }


    p{
      font-size: 13px;
    }
    .border{
    border: 1px solid #000;
    border-collapse: collapse;
    }
    th {
    color: #000 !important;
    font-size: 14px;
    border-collapse: collapse;
    font-weight: bold !important;
    text-transform: uppercase;
    text-align: left;
    }
    
    table{
    width: 100%;
    }
      </style>
  </head>
  <div class="bordertestreport">
  <table>
    <tr>
          <td style="text-align: center; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, Elections,2019 ( 17 LOK SABHA )</strong></p></td>
            </tr>
          
  </table>


<table class="border">
    
       <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 16px !important; text-transform: uppercase;"><b>3 - LIST OF POLITICAL PARTIES PARTICIPATED</b></p>
        </td>
    </tr>       

</table>
  <table>
      

      <?php  if (verifyreport(3) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>


  </table> 
  
<br>

&nbsp;



                <table style="width: 100%;">

                       
               <thead>
   <tr>
                            <th class="borders">Party Type</th>
                            <th class="borders">Abbreviation</th>
                            <th class="borders" style="text-align: center;">Party Symbol</th>
                            <th class="borders">Party</th>
         
                        </tr>
</thead>         



						<?php $i = 1; ?>
						@foreach ($dataArray as $key => $row)
												
						<tr>
							@if($key=='N')
                            <th colspan="">National Parties</th>
							@elseif($key=='S')
							<th colspan="">State Parties</th>
							@elseif($key=='U')
							 <th colspan="">Registered(Unrecognised) Parties</th>
							@elseif($key=='Z')
							<th colspan="">Independent</th>
							@endif
                        </tr>
											
						@foreach ($row as $keys => $rowData)
					
                        <tr>
                            <td>{{$i}} </td>
                            <td>{{$rowData['PARTYABBRE']}}</td>
                            <td style="text-align: center;">{{$rowData['SYMBOL_DES']}}</td>
                            <td>{{$rowData['PARTYNAME']}}</td>
                        </tr>
                        
						<?php $i++; ?>

						@endforeach
						@endforeach
						
						<tr>
						<th colspan="">NOTA</th>
                        </tr>
						<tr>
                            <td>{{$i}} </td>
                            <td>NOTA</td>
                            <td>NOTA</td>
                            <td>None of the Above</td>
                        </tr>

    
                </table>


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
        
    
        if (verifyreport(3) == 0){
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