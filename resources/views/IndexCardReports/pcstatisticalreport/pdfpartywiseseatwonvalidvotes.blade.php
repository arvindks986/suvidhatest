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
    padding: 10px;
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
    text-align: center;
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
                        <p style="font-size: 18px !important; text-transform: uppercase;"><b>18 - Party Wise Seat Won & Valid Votes Polled in Each State </b></p>
                  </td>
              </tr>
  </table>

  <table>
      <?php  if (verifyreport(18) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>

  </table> 

<br>  

@forelse($datanew as $partywiseseatwon)

                     <table class="table table-bordered table-striped" style="width: 100%;">

  <thead>
             
                <tr>
                  <th style="width: 15%;">Party Name</th>
                  <th style="width: 9%;">Party Type</th>
                  <th style="width: 10%;">State Name</th>
                  <th style="width: 10%;">Total Valid Votes <br>   Polled in the State</th>
                  <th style="width: 10%;">Total Electors <br>  in the State</th>
                  <th style="width: 10%;">Seats <br> Won</th>
                  <th style="width: 10%;">Total Valid Votes <br>  Polled by Party</th>
                  <th style="width: 10%;">% Valid Votes <br>Polled By Party</th>
                </tr>
                
                
         </thead>    
 

</table>
<table style="width: 100%;"> 

<tr style="width: 100%;">
  <td style="font-size: 14px;font-weight: bold;text-align: left;width: 19%;">{{$partywiseseatwon['partyname']}}</td>
  <td style="text-align: left;float: left;">{{$partywiseseatwon['leadtypename']}}</td> 
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
<td>  </td>
<td>  </td>
<td>  </td>
</tr>

</table>

<table class="table table-bordered" style="border:none;width: 100%;"> 
<tbody>
     @foreach($partywiseseatwon['partdetails'] as $rowdata)
                <tr style="width: 100%;">
                   

                   
                 <td style="visibility: hidden;border: none;"></td>
                 <td style="visibility: hidden;border: none;"></td>
                  <td style="width: 12%;">{{$rowdata['stname']}}</td>
                  <td style="width: 12%;">{{$rowdata['evmvote']}}</td>
                  <td style="width:12%;">{{$rowdata['electroll']}}</td>
                  <td style="width: 12%;">{{$rowdata['wonseat']}}</td>
                  <td style="width: 12%;">{{$rowdata['totalvotebyparty']}}</td>
                  <?php if($rowdata['evmvote']) { ?>
                  <td style="width: 12%;"><?php  echo round($rowdata['totalvotebyparty']/$rowdata['evmvote'] *100,2);?></td>
                  <?php } else{ ?>
                  <td style="width: 12%;">0</td>
                  <?php } ?>

                  
                </tr>

@endforeach

  
				
	</tbody>


            </table>			
@empty				
@endforelse





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
    
  
    if (verifyreport(18) == 0){
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
