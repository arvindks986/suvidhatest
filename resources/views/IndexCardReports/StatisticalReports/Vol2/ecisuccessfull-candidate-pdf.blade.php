<html>
  <head>
      <style>
        td {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #000 !important;
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
   
    .table {
    width: 100%;
    border-collapse: collapse;
    font-size: .9em;
    color: #000;
    margin-bottom: 1rem;

    }
.blcs{
  border-bottom: 1px solid #000;
  border-top: 1px solid #000;
  border-collapse: collapse;
}
.bordertestreport{
  margin: 0px;
  padding: 0px;
}

    .border{
    border: 1px solid #000;
    width: 100%;
    border-collapse: separate;
    }
    th {
    color: #000 !important;
    font-size: 13px;
border-collapse: collapse;
text-align: left;
    font-weight: bold !important;
    }

    table{
    width: 100%;
   
    }
      </style>
  </head>

  <body>
    
 
<div class="bordertestreport">
      <table>
           <tr>
              <td style="text-align: center !important; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, Elections,2019 ( 17 LOK SABHA )</strong></p></td>
            </tr>
           
  </table>

<table class="border">
    <tr><td style="text-align: center !important;; font-weight: bold !important;">
                        <p style="font-size: 16px !important; text-transform: uppercase;"><b>4 - LIST OF SUCCESSFUL CANDIDATE - {{getElectionYear()}}</b></p>
                  </td>
              </tr>

</table>
  <table>

    <?php  if (verifyreport(4) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>
      

  </table>

<br>
                    

                      <table>


                        <thead>
                          <tr>
                               <th scope="col" class="blcs" style="width: 10%;"></th>
                                <th scope="col" class="blcs">CONSTITUENCY</th>
                                <th scope="col" class="blcs" style="text-align: center;">Category</th>
                                <th scope="col" class="blcs">WINNER</th>
                                <th scope="col" class="blcs">Social Category</th>
                                <th scope="col" class="blcs">PARTY</th>
                                <th scope="col" class="blcs">PARTY SYMBOL</th>
                                <th scope="col" class="blcs">MARGIN</th>
                        </tr>



                        </thead>
                    <tbody>
					<?php $sn = 1; ?>
                       @foreach($arraydata as $allsuccessfullcondidate)
                        <tr>
                            <th colspan="3" style="text-align: left;font-size: 15px;" >{{$allsuccessfullcondidate['state']}}</th>
                           
                            <br>
                        </tr>
                                  
                            @foreach($allsuccessfullcondidate['pc'] as  $catwise)
                           <tr>
						   <td>{{$sn}}</td>
                            <td style="width: 20%;">{{$catwise['Pc_Name']}}</td>
                            <td style="width: 10%;text-align: center;">{{$catwise['PC_TYPE']}}</td>
                            <td style="width: 20%;">{{$catwise['Cand_Name']}}</td>
							<td style="width: 10%;">{{$catwise['PC_TYPE']}}</td>
							<td style="width: 15%;">{{$catwise['Party_Abbre']}}</td>
                            <td style="width: 20%;">{{$catwise['Party_symbol']}}</td>                            
                           <td style="width: 10%;"> {{$catwise['margin']}} <br> ({{$catwise['percent']}} %)</td>
                        </tr>
						<?php $sn++; ?>
                       @endforeach
                      @endforeach

     


                    </tbody>
                </table>

 

 <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>
</div>


<script type="text/php">
    if (isset($pdf)) {
        $text = "Page {PAGE_NUM}";
        $size = 10;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width);
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size);
    
  
    if (verifyreport(4) == 0){
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
