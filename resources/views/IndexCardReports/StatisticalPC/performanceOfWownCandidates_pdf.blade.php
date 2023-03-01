<html>
  <head>
      <style>
@page {
            header: page-header;
            footer: page-footer;
        }
        td {
    font-size: 11px !important;
    font-weight: 500 !important;
    text-align: center;
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
                        <p style="font-size: 20px !important; text-transform: uppercase;"><b>25 - INDIVIDUAL PERFORMANCE OF WOMEN CANDIDATES</b></p>
                  </td>
              </tr>

</table>
  <table>
      <?php  if (verifyreport(25) == 0){ ?>
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



                <table class="table borders" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Name of Constituency</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                         <tr>
                            <th rowspan="2" class="blc">Sl. No.</th>
                            <th style="text-align: left;" rowspan="2" class="blc">Name of candidate</th>
                            <th rowspan="2" class="blc">Party</th>
                            <th rowspan="2" class="blc">Party <br> Type</th>
                            <th rowspan="2" class="blc">Votes <br> Secured</th>
                            <th colspan="2" style="text-decoration: underline;">% of secured votes</th>
                            <th rowspan="2" class="blc">Status</th>
                            <th rowspan="2" class="blc">Total <br>Valid <br> Votes</th>
                        </tr>
                        <tr>
                            <th class="blc">Over total <br>electors in <br>constituency</th>
                            <th class="blc">Over total valid <br> votes in <br>constituency</th>
                            
                        </tr>
                    </thead>
                    <tbody>
					
                  
					<?php $i = 1; ?>
					@foreach ($dataArray as $keys => $rowArr)
					
					<tr>
                            <td colspan="9" style="text-align: left;">
							<b>State/UT: {{$keys}}</b>
                            </td>
                        </tr>
					
					
					@foreach ($rowArr as $key => $row)
												
						<tr>
                            <th colspan="9" style="text-align: left;">
							{{$key}}
                            </th>
                        </tr>
											
						@foreach ($row as $keys => $rowData)
					<?php 
					$total_electors = $rowData['total_electors'];
					?>
					
                        
                        <tr>
                            <td style="text-align: center;">{{$rowData['srno']}}</td>
                            <td style="text-align: left;">{{$rowData['candidate_name']}}</td>
                            <td>{{$rowData['party_abbre']}}</td>
                            <td>{{$rowData['PARTYTYPE']}}</td>
                            <td>{{$rowData['candidate_votes']}}</td>
                            <td>@if($total_electors)
								{{number_format((float)($rowData['candidate_votes']*100)/$total_electors, 2, '.', '')}}
								@else
									0
								@endif
							</td>
                            <td>@if($rowData['total_votes'])
							{{number_format((float)($rowData['candidate_votes']*100)/$rowData['total_votes'], 2, '.', '')}}
								@else
									0
								@endif</td>
                            <td>{{$rowData['status']}}</td>
                            <td>{{$rowData['total_votes']}}</td>
                        </tr>
						
						<?php $i++; ?>
						@endforeach
						@endforeach
						@endforeach


         


                    </tbody>
                </table>

<table style="text-align: left;">
     <tr style="text-align: left;"><td><p> W - WINNER</p></td></tr>
            <tr style="text-align: left;"><td><p>L - LOOSER</p></td></tr>
            <tr style="text-align: left;"><td><p>DP - DEPOSIT FORFEITED</p></td></tr>
            <tr style="text-align: left;"><td><p>N- NATIONAL PARTY</p></td></tr>
            <tr style="text-align: left;"><td><p>S - STATE PARTY</p></td></tr>
            <tr style="text-align: left;"><td><p>U - UNRECOGNIZED (REGISTERED)</p></td></tr>
            <tr style="text-align: left;"><td><p>I - INDEPENDENT</p></td></tr>


</table>
                
            </div>

 <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>


<htmlpagefooter name='page-footer'>
 <table>
 <tr>
 <?php if (verifyreport(25) == 1){ ?>
 <td align="left"><span style="float:left;color: #d3d3d3;">{{getreportsequence(777)}}</span></td>
    
    <?php } ?>
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>
</table>
 </htmlpagefooter>
 
</html>