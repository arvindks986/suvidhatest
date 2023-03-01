<html>
  <head>
      <style>

        @page {
            header: page-header;
            footer: page-footer;
        }


        td {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #000 !important;
padding: 5px;
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
                        <p style="font-size: 16px !important; text-transform: uppercase;"><b>30 -WINNING CANDIDATES ANALYSIS OVER TOTAL VALID VOTES</b></p>
                  </td>
              </tr>

</table>
<br>
  <table>
     <?php  if (verifyreport(30) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>


  </table>




              <table class="table table-bordered table-striped" style="width: 100%;">
             <thead>
                  <tr class="table-primary">
                  <th scope="col" rowspan="2">Name of State/UT</th>
                  <th scope="col" rowspan="2">No. Of Seats</th>
                  <th colspan="8">No. Of Candidates Secured The % Of Votes Over The Total Valid Votes In The Constituency
 </th>

                </tr>
                <tr>

                    <th>Winner with <= 10%</th>
                    <th>Winner with >10% to <= 20%</th>
                    <th>Winner with >20% to <=30%</th>
                    <th>Winner with >30% to <=40%</th>
                    <th>Winner with >40% to <=50%</th>
                    <th>Winner with >50% to <=60%</th>
                    <th>Winner with >60% to <=70%</th>
                    <th>Winner with > 70%</th>
</tr>
                </thead>
                        <tbody>

  <?php

      $totalsheet = $totalzero_to_10 = $totalone_to_20 = $totaltwo_to_30 = $totalthree_to_40
      = $totalfour_to_50 = $totalfive_to_60 = $totalsix_to_70 = $totalseven_to_80 = 0;

  ?>

@forelse($arrayData as $values)
<tr>
   <td>{{$values->st_name}}</td>
   <td><b>{{$values->Total_Sheet}}</b></td>
   <td>{{$values->zero_to_10}}</td>
   <td>{{$values->one_to_20}}</td>
   <td>{{$values->two_to_30}}</td>
   <td>{{$values->three_to_40}}</td>
   <td>{{$values->four_to_50}}</td>
   <td>{{$values->five_to_60}}</td>
   <td>{{$values->six_to_70}}</td>
   <td>{{$values->seven_to_80}}</td>

</tr>

<?php

  $totalsheet += $values->Total_Sheet;
  $totalzero_to_10 += $values->zero_to_10;
  $totalone_to_20 += $values->one_to_20;
  $totaltwo_to_30 += $values->two_to_30;
  $totalthree_to_40 += $values->three_to_40;
  $totalfour_to_50 += $values->four_to_50;
  $totalfive_to_60 += $values->five_to_60;
  $totalsix_to_70 += $values->six_to_70;
  $totalseven_to_80 += $values->seven_to_80;

?>
@empty
<tr>
   <td>Data Not Found</td>

</tr>
@endforelse

<tr>
    <td style="font-weight: bold !important;"><b style="font-weight: bold !important;">Total Seats</b></td>
    <td style="font-weight: bold !important;"><b>{{$totalsheet}}</b></td>
    <td style="font-weight: bold !important;"><b>{{$totalzero_to_10}}</b></td>
    <td style="font-weight: bold !important;"><b>{{$totalone_to_20}}</b></td>
    <td style="font-weight: bold !important;"><b>{{$totaltwo_to_30}}</b></td>
    <td style="font-weight: bold !important;"><b>{{$totalthree_to_40}}</b></td>
    <td style="font-weight: bold !important;"><b>{{$totalfour_to_50}}</b></td>
    <td style="font-weight: bold !important;"><b>{{$totalfive_to_60}}</b></td>
    <td style="font-weight: bold !important;"><b>{{$totalsix_to_70}}</b></td>
    <td style="font-weight: bold !important;"><b>{{$totalseven_to_80}}</b></td>

</tr>
</tbody>



            </table>
                </div>

 <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>

<htmlpagefooter name='page-footer'>
 <table>
 <tr>
 <?php if (verifyreport(30) == 1){ ?>
 <td align="left"><span style="float:left;color: #d3d3d3;">{{getreportsequence(777)}}</span></td>
    
    <?php } ?>
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>
</table>
 </htmlpagefooter>


                </html>
