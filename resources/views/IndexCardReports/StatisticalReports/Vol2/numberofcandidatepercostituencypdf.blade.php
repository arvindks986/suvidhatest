<html>
  <head>
  
  
      <style>


        @page { sheet-size: A3-L; }
@page bigger { sheet-size: 420mm 370mm; }
@page toc { sheet-size: A4; }
@page {
            header: page-header;
            footer: page-footer;
        }


        td {
    font-size: 14px;
    font-weight: 500 !important;
    text-align: left;
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
    border-bottom: 1px solid #000;
    border-top: 1px solid #000;
    font-stretch: 16px;
    }

     .border{
    border: 1px solid #000;

    }
    th {
    color: #000 !important;
    font-size: 15px;
    text-align: center;
    font-weight: bold !important;
    }
    
    table td{
      font-size: 16px;
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
                        <p style="font-size: 21px !important; text-transform: uppercase;"><b>8 - NUMBER OF CANDIDATES PER CONSTITUENCY </b></p>
                  </td>
              </tr>

               </table>
  <table class="">
      <?php  if (verifyreport(8) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php }  ?>


  </table> 


<br>
&nbsp;


             <table class="borders" style="width: 100%;">

                      <thead>
                      <tr>
                          <th colspan="2"></th>
                          <th colspan="7" style="text-align: center;text-decoration: underline;">Constituencies with candidates numbering</th>
                          <th colspan="3" style="text-align: center;text-decoration: underline;" >Candidates in a Constituency</th>
                      </tr>
                      <tr>
                          <th>State/UT</th>
                          <th>No. of Seats</th>
                          <th style="width: 8%;">1 </th>
                          <th><=15 </th>
                          <th>>15  <=31</th>
                          <th>>31  <=47</th>
                          <th>>47 <=63</th>
                          <th>>63</th>
                          <th>Total Candidates</th>
                          <th>Min</th>
                          <th>Max</th>
                          <th>Avg</th>
                      </tr>
                  </thead>

                </table>

                <table>
                  <?php $seartotal = $searonetotal = $searNotatotal = $searThreeOnetotal = $searFourSeventotal 

                = $searSixThreetotal = $searLessSixThreetotal = $totalcandidate = 0 ; ?> 
         						 @forelse ($pcCount as $key => $value)

         							 <tr>
         							 	<td style="width: 15%;">{{$value->st_name}}</td>
         							 	<td style="width: 7%;">{{$value->No_of_Seats}}</td>
         							 	<td style="width: 10%;">{{$value->one}}</td>
         							 	<td style="width: 10%;">{{$value->Nota}}</td>
         							 	<td style="width: 10%;">{{$value->threeone}}</td>
         							 	<td style="width: 10%;">{{$value->fourseven}}</td>
         							 	<td style="width: 10%;">{{$value->sixthree}}</td>
         							 	<td style="width: 10%;">{{$value->lesssixthree}}</td>
         							 	<td style="width: 10%;">{{$value->Total_Candidates}}</td>
         							  <td style="width: 8%;">{{$value->mincan}}</td>
                        <td style="width: 8%;">{{$value->maxcan}}</td>
                        <td style="width: 7%;">{{$value->Avg}}</td>
         							 </tr>

                       <?php  

                  $seartotal += $value->No_of_Seats;
                  $searonetotal += $value->one;
                  $searNotatotal += $value->Nota;
                  $searThreeOnetotal += $value->threeone;
                  $searFourSeventotal += $value->fourseven;
                  $searSixThreetotal += $value->sixthree;
                  $searLessSixThreetotal += $value->lesssixthree;
                  $totalcandidate  += $value->Total_Candidates;
              ?>

         						 @empty
         							 <tr>
         							 	<td>Data Not available.</td></tr>
         						 @endforelse
                    </table>

                    <?php

                      $minnumber = array_column($pcCount, 'mincan');
                      $maxnumber = array_column($pcCount, 'maxcan');
                      $min = min($minnumber);
                      $max = max($maxnumber);

                    ?>


<table class="borders">
               <tr>
                <td style="width: 15%;font-weight: 600 !important;"><b>Grand Total</b></td>
                <td style="width: 7%;font-weight: 600 !important;"><b>{{$seartotal}}</b></td>
                <td style="width: 10%;font-weight: 600 !important;"><b>{{$searonetotal}}</b></td>
                <td style="width: 10%;font-weight: 600 !important;"><b>{{$searNotatotal}}</b></td>
                <td style="width: 10%;font-weight: 600 !important;"><b>{{$searThreeOnetotal}}</b></td>
                <td style="width: 10%;font-weight: 600 !important;"><b>{{$searFourSeventotal}}</b></td>
                <td style="width: 10%;font-weight: 600 !important;"><b>{{$searSixThreetotal}}</b></td>
                <td style="width: 10%;font-weight: 600 !important;"><b>{{$searLessSixThreetotal}}</b></td>
                <td style="width: 10%;font-weight: 600 !important;"><b>{{$totalcandidate}}</b></td>
                <td style="width: 8%;font-weight: 600 !important;"><b>{{$min}}</b></td>
                <td style="width: 8%;font-weight: 600 !important;"><b>{{$max}}</b></td>
                <td style="width: 7%;font-weight: 600 !important;"><b>{{round($totalcandidate/$seartotal,2)}}</b></td>
                
               </tr>



            </table>






                </div>
            </div>
            
        </div>



 <h4 style="padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>


<htmlpagefooter name='page-footer'>
 <table>
 <tr>
 <?php if (verifyreport(8) == 1){ ?>
 <td align="left"><span style="float:left;color: #d3d3d3;">{{getreportsequence(777)}}</span></td>
    
    <?php } ?>
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>
</table>
 </htmlpagefooter>

</html>