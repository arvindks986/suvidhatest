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
    padding: 6px; class="blcs"
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
                        <p style="font-size: 18px !important; text-transform: uppercase;"><b>33 - CONSTITUENCY WISE DETAILED RESULTS</b></p>
                  </td>
              </tr>

</table>
  <table>
  <?php  if (verifyreport(33) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>

  </table> 

<br>
   
               
                 @foreach($dataArr as $key => $data)
				
				<p class="contituency" style="font-size: 13px;"> <span> <b>{{$key}}</b></span></p>

                @foreach($data as $key1 => $raw)
				
				<p class="contituency" style="font-size: 13px;"><b>Constituency:</b> <span> {!!$key1!!}</span></p>
				

                <table id="example" class="table borders" style="width:100%;">
                    <thead>
                        <tr>
                            <th rowspan="2" class="blc">SL NO</th>
                            <th rowspan="2" class="blc">CANDIDATE <br> NAME</th>
                            <th rowspan="2" class="blc">SEX</th>
                            <th rowspan="2" class="blc">AGE</th>
                            <th rowspan="2" class="blc">CATEGORY</th>
                            <th rowspan="2" class="blc">PARTY</th>
                            <th rowspan="2" class="blc">Symbol</th>
                           <th style="text-decoration: underline;" colspan="3">Votes Secured</th>
                           <th style="text-decoration: underline;" colspan="2">% of votes secured</th>
                        </tr>


                        <tr>
                             <th class="blc">GENERAL</th>
                            <th class="blc">POSTAL</th>
                            <th class="blc">TOTAL</th>
 <th class="blc">Over total elctors in constituency</th>
                            <th class="blc">Over total votes polled in constituency</th>

                        </tr>


                    </thead>
                    <tbody><?php $count=1;$totalgeneral_vote=0;$totalpostal_vote=0;$grandtotal=0; $totalelectorspercent =0; $grandelector=0; $grandpolled=0; ?>
                        @foreach($raw as $row)
                 <?php
				$electors = $row['total_electors'];
				 $totalvotespolled = $row['total_votes'];

                  
                  $totalelectorPercent = ($electors!=0)?((($row['general_vote']+$row['postal_vote'])/$electors)*100):0;
                  $grandelector+=$totalelectorPercent;


                 $totalvotespolled=($totalvotespolled!=0)?((($row['general_vote']+$row['postal_vote'])/$totalvotespolled)*100):0;
                 $grandpolled+=$totalvotespolled;

                 ?>
                        <tr>
                            <td>{{$count}}</td>
                            <td style="text-transform: capitalize;">{{$row['cand_name']}}</td>
                            <td style="text-transform: capitalize;">{{$row['cand_gender']}}</td>
                            <td>{{$row['cand_age']}}</td>
                            <td>{{$row['cand_category']}}</td>
                            <td>{{$row['party_abbre']}}</td>
                            <td>{{$row['SYMBOL_DES']}}</td>
                            <td>{{$row['general_vote']}}</td>
                            <td>{{$row['postal_vote']}}</td>
                            <td>{{$row['general_vote']+$row['postal_vote']}}</td>
                            <td>{{round($totalelectorPercent,2)}}</td>
                            <td>{{round($totalvotespolled,2)}}</td>
                            
                        </tr>
						<?php $totalgeneral_vote+=$row['general_vote'];
						$totalpostal_vote+=$row['postal_vote'];
						$grandtotal+=$row['general_vote']+$row['postal_vote'];
						$count++;?>
                     
                        @endforeach
                        <tr>
                           <td colspan="5" class="blcs"></td>
                            <td colspan="2" class="blcs"><b>TOTAL</b></td>                          
                            <td class="blcs"><b>{{$totalgeneral_vote}}</b></td>
                            <td class="blcs"><b>{{$totalpostal_vote}}</b></td>
                            <td class="blcs"><b>{{$grandtotal}}</b></td>
                            <td class="blcs"><b>{{round($grandelector,2)}}</b></td>
                            <td class="blcs"><b>{{round($grandpolled,2)}}</b></td>
                        </tr>
                    </tbody>



                </table>


         
<div style="page-break-after: always;">
  
</div>

               
                @endforeach
                @endforeach
  
			
			
            
				
                <table class="table table-bordered">
        <tr>
                           
                  
                            <td colspan="5" style="width: 42%;" class="blc"></td>
              <td style="width: 12%;" colspan="2" class="blc">INDIA TOTAL:</td>
                            <td style="width: 10%;" class="blc"><b>{{$all_india_Data[0]->all_india_evm}}</b></td>
                            <td style="width: 10%; " class="blc"><b>{{$all_india_Data[0]->all_india_postal}}</b></td>
                            <td style="" class="blc"><b>{{$all_india_Data[0]->all_india_total}}</b></td>
                           
                        </tr>
        </table>


            </div>



 <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>
 

<htmlpagefooter name='page-footer'>
 <table>
 <tr>
 <?php if (verifyreport(33) == 1){ ?>
 <td align="left"><span style="float:left;color: #d3d3d3;">{{getreportsequence(777)}}</span></td>
    
    <?php } ?>
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>
</table>
 </htmlpagefooter>



       </html>