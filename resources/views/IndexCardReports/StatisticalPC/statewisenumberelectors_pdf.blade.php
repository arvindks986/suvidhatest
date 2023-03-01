<html>
  <head>
      <style>

        @page { sheet-size: A3-L; }
@page bigger { sheet-size: 420mm 370mm; }
@page toc { sheet-size: A4; }
@page { size: a3 landscape; }
        td {
    font-size: 12px !important;
    font-weight: 500 !important;
    text-align: left;
	border-collapse:collapse;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
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

     .borders{
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
	border-collapse:collapse;
    font-weight: 600 !important;
    }
    .border{
    border: 1px solid #000;
    }
    th {
        text-align: center;
    font-size: 14px;
    font-weight: bold !important;
    text-align: left;
    }
    
    table{
    width: 100%;
	border-collapse:collapse;
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
                        <p style="font-size: 20px !important; text-transform: uppercase;"><b>9 - STATE WISE NUMBER OF ELECTORS</b></p>
                  </td>
              </tr>

              </table>
  <table>
    <?php  if (verifyreport(9) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php }  ?>


  </table> 

&nbsp;
<br>
<table class="border">
    
     <tr>
                        <th style="width: 10%;">STATE/UT</th>
                        <th colspan="4" style="text-align: center;border-right:1px solid #000; border-bottom: 1px solid #000;border-left: 1px solid #000;">GENERAL <span style="text-transform: ">(including NRIs)</span></th>
                        <th colspan="4" style="text-align: center;border-right: 1px solid #000;border-bottom: 1px solid #000;">SERVICE</th>
                        <th colspan="4" style="text-align: center;border-right: 1px solid #000;border-bottom: 1px solid #000;">GRAND</th>
                        <th colspan="5" style="text-align: center;border-bottom: 1px solid #000;">NRIs</th>
                        
                        
                    </tr>

                    <?php $total_gen_m = $total_gen_f = $total_gen_o = $total_gen_t =$total_ser_m = $total_ser_f = $total_ser_o = $total_ser_t = $total_grand_m = $total_grand_f = $total_grand_o = $total_grand_t = $total_nri_m = $total_nri_f = $total_nri_o = $total_nri_t =0; ?>



                    <tr>
                        <th></th>
                        <th style="border-left: 1px solid #000;">MALE</th>
                        <th>FEMALE</th>
                        <th>THIRD GENDER</th>
                        <th style="width: 5%;border-right: 1px solid #000;">TOTAL</th>
                        <th>    </th>
                        <th>MALE</th>
                        <th>FEMALE</th>
                        <th style="width: 5%;border-right: 1px solid #000;">TOTAL</th>
                        <th>MALE</th>
                        <th>FEMALE</th>
                        <th>THIRD GENDER</th>
                        <th style="width: 5%;border-right: 1px solid #000;">TOTAL</th>
                        <th>    </th>
                        <th>MALE</th>
                        <th>FEMALE</th>
                        <th>THIRD GENDER</th>
                        <th>TOTAL</th>
                    </tr>



</table>

              <table class="" style="white-space: nowrap;">
                    
                   
                   
                    

                @foreach($data as $key => $row)
                    
                    
                    <?php 
                    $grand_m = $grand_f = $grand_o = $grand_t =0; 
                    
                    $grand_m = $row->e_gen_m + $row->e_ser_m; 
                    $grand_f = $row->e_gen_f + $row->e_ser_f; 
                    $grand_o = $row->e_gen_o + $row->e_ser_o; 
                    $grand_t = $row->e_gen_t + $row->e_ser_t; 
                    
                    
                    $total_gen_m += $row->e_gen_m; 
                    $total_gen_f += $row->e_gen_f; 
                    $total_gen_o += $row->e_gen_o; 
                    $total_gen_t += $row->e_gen_t; 
                    
                    $total_ser_m += $row->e_ser_m;
                    $total_ser_f += $row->e_ser_f;
                    $total_ser_o += $row->e_ser_o;
                    $total_ser_t += $row->e_ser_t; 
                    
                    
                    $total_nri_m += $row->e_nri_m;
                    $total_nri_f += $row->e_nri_f;
                    $total_nri_o += $row->e_nri_o;
                    $total_nri_t += $row->e_nri_t; 
                                        
                    $total_grand_m += $grand_m;
                    $total_grand_f += $grand_f;
                    $total_grand_o += $grand_o;
                    $total_grand_t += $grand_t;
                    
                    ?>
                    
                    
                    <tr>
                        <td class="gry" style="width: 10%;"> {{$row->ST_NAME}}</td>
                        <td>@if($row->e_gen_m) {{$row->e_gen_m}} @else 0 @endif</td>
                        <td>@if($row->e_gen_f) {{$row->e_gen_f}} @else 0 @endif</td>
                        <td>@if($row->e_gen_o) {{$row->e_gen_o}} @else 0 @endif</td>
                        <td>@if($row->e_gen_t) {{$row->e_gen_t}} @else 0 @endif</td>
                        <td>@if($row->e_ser_m) {{$row->e_ser_m}} @else 0 @endif</td>
                        <td>@if($row->e_ser_f) {{$row->e_ser_f}} @else 0 @endif</td>
<!--                         <td style="width: 6%;">@if($row->e_ser_o) {{$row->e_ser_o}} @else 0 @endif</td>
 -->                        <td>@if($row->e_ser_t) {{$row->e_ser_t}} @else 0 @endif</td>
                        <td>@if($grand_m) {{$grand_m}} @else 0 @endif</td>
                        <td>@if($grand_f) {{$grand_f}} @else 0 @endif</td>
                        <td>@if($grand_o) {{$grand_o}} @else 0 @endif</td>
                        <td style="width: 8%;">@if($grand_t) {{$grand_t}} @else 0 @endif</td>
                        <td>@if($row->e_nri_m) {{$row->e_nri_m}} @else 0 @endif</td>
                        <td>@if($row->e_nri_f) {{$row->e_nri_f}} @else 0 @endif</td>
                        <td>@if($row->e_nri_o) {{$row->e_nri_o}} @else 0 @endif</td>
                        <td>@if($row->e_nri_t) {{$row->e_nri_t}} @else 0 @endif</td>
                    </tr>
                    
                    @endforeach
                    
    
	
	<tr>
                        <th style="width: 9%;" class="borders"><b>TOTAL:</b></th>
                        <td style="width:%;" class="borders"><b>{{$total_gen_m}}</b></td>
                        <td class="borders"><b>{{$total_gen_f}}</b></td>
                        <td class="borders"><b>{{$total_gen_o}}</b></td>
                        <td class="borders"><b>{{$total_gen_t}}</b></td>
                        <td class="borders"><b>{{$total_ser_m}}</b></td>
                        <td class="borders"><b>{{$total_ser_f}}</b></td>
                        <!-- <td><b>{{$total_ser_o}}</b></td> -->
                        <td class="borders"><b>{{$total_ser_t}}</b></td>
                        <td class="borders"><b>{{$total_grand_m}}</b></td>
                        <td class="borders"><b>{{$total_grand_f}}</b></td>
                        <td class="borders"><b>{{$total_grand_o}}</b></td>
                        <td class="borders"><b>{{$total_grand_t}}</b></td>
                        <td class="borders"><b>{{$total_nri_m}}</b></td>
                        <td class="borders"><b>{{$total_nri_f}}</b></td>
                        <td class="borders"><b>{{$total_nri_o}}</b></td>
                        <td class="borders"><b>{{$total_nri_t}}</b></td>
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
    
  
    if (verifyreport(9) == 0){
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