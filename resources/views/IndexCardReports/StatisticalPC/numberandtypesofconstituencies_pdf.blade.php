<html>
  <head>
      <style>
        td {
    font-size: 14px !important;
    font-weight: 500 !important;
    color: #000 !important;
    text-align: center;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
    }
table td{
width: 17%;
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
    margin-bottom: 1rem;

    }

   
    .border{
    border: 1px solid #000;
    }   
    .borders{
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    }
    th {
    color: #000 !important;
    font-size: 15px;
    font-weight: bold !important;
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
                        <p style="font-size: 18px !important; text-transform: uppercase;"><b>5 - NUMBER AND TYPES OF CONSTITUENCIES</b></p>
                  </td>
              </tr>
  </table>

  <table class="">
      <?php  if (verifyreport(5) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>


  </table> 
&nbsp;
<table class="borders">
  <tr><td><p></p></td></tr>
  <tr><td><p></p></td></tr>
  <tr><td><p></p></td></tr>
  <tr><td><p></p></td></tr>
  <tr><td><p> </p></td></tr>
                        <tr style="">
                            <td style="width: 24%"><b>STATE/UT</b></td>
                            <td colspan="4" style="text-decoration: underline;"><b>Type Of Constituencies</b></td>
                            <td></td>                           
                        </tr>                      
                   
                        <tr class="">
                            <td></td>
                            <td><b>Gen</b></td>
                            <td><b>SC</b></td>
                            <td><b>ST</b></td>
                            <td><b>Total</b></td>
                            <td style="width: 24%"><b>No. of Constituencies <br>Where Election Completed</b></td>
                        </tr>

                            </table>



                <table class="" style="table-layout: fixed;width: 100%;">
                      <tr>  <td>  <p> </p></td></tr>
                      <tr>  <td>  <p> </p></td></tr>
                      <tr>  <td>  <p> </p></td></tr>

                      



					@foreach ($data as $row)

                                <tr>  <td>  <p> </p></td></tr>
                      <tr>  <td>  <p> </p></td></tr>
                      <tr>  <td>  <p> </p></td></tr>
						<tr>
                            <td style="width: 24%">{{$row->ST_NAME}}</td>
                            <td>{{$row->gen}}</td>
                            <td>{{$row->sc}}</td>
                            <td>{{$row->st}}</td>
                            <td>{{$row->total}}</td>
                            <td style="width: 24%">{{$row->completed}}</td>
                        </tr>
					@endforeach
<tr>  <td>  <p> </p></td></tr>
<tr>  <td>  <p> </p></td></tr>
<tr>  <td>  <p> </p></td></tr>
</table>
  <table class="borders">

                        <tr>
                           <td style="font-weight: bold !important;"><b>Total</b></td>
                            <td style="font-weight: bold !important;"><b>{{$dataSum['gen']}}</b></td>
                            <td style="font-weight: bold !important;"><b>{{$dataSum['sc']}}</b></td>
                            <td style="font-weight: bold !important;"><b>{{$dataSum['st']}}</b></td>
                            <td style="font-weight: bold !important;"><b>{{$dataSum['total']}}</b></td>
                            <td style="font-weight: bold !important;"><b>{{$dataSum['completed']}}</b></td>
                        </tr>
    
  
 
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
    
  
    if (verifyreport(5) == 0){
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