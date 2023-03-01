<html>
  <head>
      <style>

   @page { sheet-size: A4-L; }
@page bigger { sheet-size: 420mm 370mm; }
@page toc { sheet-size: A4; }
@page {
            header: page-header;
            footer: page-footer;
        }


        td {
    font-size: 11px !important;
    font-weight: 500 !important;
    text-align: center;
    padding: 4px;
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

   .borders{
   	border-top: 1px solid #000;
   	border-bottom: 1px solid #000;
   	font-weight: 600 !important;
   }

   .borders2{
   	border-top: 1px solid #000;
   font-weight: bold;
   }
    .border{
    border: 1px solid #000;
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
  <body>
  
  <div class="bordertestreport">
      <table class="">
           <tr>
              <td style="text-align: center; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, Elections,2019 ( 17 LOK SABHA )</strong></p></td>
            </tr>
           
  </table>


<table class="border">
	
	  <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 20px !important; text-transform: uppercase;"><b>13 - PC WISE VOTERS TURN OUT </b></p>
                  </td>
              </tr>


</table>
<br>
  <table>
      <?php  if (verifyreport(13) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>

  </table> 

	
                     
							@php 
							$sl_no = 1;
							
								$grand_total_electors = 0;
								$grand_total_voters_male = 0;
								$grand_total_voters_female = 0;
								$grand_total_voters_other = 0;
								$grand_total_voters_all = 0;
								$grand_total_voters_nri = 0;
								$grand_total_postal_all = 0;
								$grand_total_voters_alltotal = 0;
								$grand_total_male_electors = 0;
								$grand_total_female_electors = 0;
								$grand_total_other_electors = 0;
							
							@endphp
							
							
							@foreach($pcwisevoterturnouts as $key=>$row)


                      <table class="table table-bordered">
								<thead>
                                <tr>
                                    <th rowspan="3"> SL. NO. </th>
									<th rowspan="3"> PC No. </th>
                                    <th rowspan="3"> PC Name </th>                                    
                                    <th rowspan="3"> Electors </th>
                                    <th colspan="7" style="text-align: center;">Voters </th>
                                    <th rowspan="3"> Voter <br> Turn Out <br> (%)</th>
                                    <th colspan="3">Voter Turn Out (Excl. Postal) % </th>
                                </tr>
								
                                <tr>
                                    <th colspan="5" style="text-align: center;"> EVM </th>
                                    <th rowspan="2"> Postal <br> Votes </th>
                                    <th rowspan="2"> Total <br> Votes </th>
                                    <th rowspan="2"> Male </th>
                                    <th rowspan="2"> Female </th>
                                    <th rowspan="2"> Third Gender</th>
                                </tr>
                                <tr>
                                   
                                    <th> Male </th>
                                    <th> Female </th>
                                    <th> Third Gender </th>
                                    <th> Total </th>
                                    <th>NRI</th>
                                   
                                </tr>
                            
                           </thead>
							@php
								$total_electors = 0;
								$total_voters_male = 0;
								$total_voters_female = 0;
								$total_voters_other = 0;
								$total_voters_all = 0;
								$total_voters_nri = 0;
								$total_postal_all = 0;
								$total_voters_alltotal = 0;
								$total_male_electors = 0;
								$total_female_electors = 0;
								$total_other_electors = 0;
							
							@endphp
							
							<tbody>
							
							 <tr>
                                    <th colspan="15" style="font-size: 14px;border-right: none !important; border-left: none !important; text-align: left;"><span style="color: #000;"> {{$key}}</span> </th>
                                </tr>
								
								@foreach($row as $no => $value)
								
								@php 
								
								if($value['electors_total'] > 0)
									$voter_turn_all = round((($value['total_vote']/$value['electors_total'])*100),2);
								else
									$voter_turn_all = 0;
								
								
								if($value['electors_male'] > 0)
									$voter_turn_male = round((($value['voter_male']/$value['electors_male'])*100),2);
								else
									$voter_turn_male = 0;


								if($value['electors_female'] > 0)
									$voter_turn_female = round((($value['voter_female']/$value['electors_female'])*100),2);
								else
									$voter_turn_female = 0;
								
								
								if($value['electors_other'] > 0)
									$voter_turn_other = round((($value['voter_other']/$value['electors_other'])*100),2);
								else
									$voter_turn_other = 0;
								
								$total_electors += $value['electors_total'];
								$total_voters_male += $value['voter_male'];
								$total_voters_female += $value['voter_female'];
								$total_voters_other += $value['voter_other'];
								$total_voters_all += $value['voter_total'];
								$total_voters_nri += $value['voter_nri'];
								$total_postal_all += $value['postal_vote'];
								$total_voters_alltotal += $value['total_vote'];
								$total_male_electors += $value['electors_male'];
								$total_female_electors += $value['electors_female'];
								$total_other_electors += $value['electors_other'];
								
								
								
								
								$grand_total_electors 			+= $value['electors_total'];
								$grand_total_voters_male 		+= $value['voter_male'];
								$grand_total_voters_female 		+= $value['voter_female'];
								$grand_total_voters_other 		+= $value['voter_other'];
								$grand_total_voters_all 		+= $value['voter_total'];
								$grand_total_voters_nri 		+= $value['voter_nri'];
								$grand_total_postal_all 		+= $value['postal_vote'];
								$grand_total_voters_alltotal 	+= $value['total_vote'];
								$grand_total_male_electors 		+= $value['electors_male'];
								$grand_total_female_electors 	+= $value['electors_female'];
								$grand_total_other_electors 	+= $value['electors_other'];
								
								
								
																
								@endphp

                               <tr>
                                    <td>{{$sl_no}}.</td>
									<td>{{$value['PC_NO']}}</td>
                                    <td>{{$value['PC_NAME']}}</td>                                 
                                    <td>{{$value['electors_total']}}</td>
                                    <td>{{$value['voter_male']}}</td>
                                    <td>{{$value['voter_female']}}</td>
                                    <td>{{$value['voter_other']}}</td>
                                    <td>{{$value['voter_total']}}</td>
                                    <td>{{$value['voter_nri']}}</td>
                                    <td>{{$value['postal_vote']}}</td>
                                    <td>{{$value['total_vote']}}</td>
                                    <td>{{$voter_turn_all}}</td>
									<td>{{$voter_turn_male}}</td>
                                    <td>{{$voter_turn_female}}</td>
                                    <td>{{$voter_turn_other}}</td>
                                </tr>
								
								
									@php 
									$sl_no++;
									@endphp
																
								@endforeach
                                
                                @php
								if($total_electors > 0)
									$voter_turn_all_total = round((($total_voters_alltotal/$total_electors)*100),2);
								else
									$voter_turn_all_total = 0;
								
								
								if($total_male_electors > 0)
									$voter_turn_male_total = round((($total_voters_male/$total_male_electors)*100),2);
								else
									$voter_turn_male_total = 0;


								if($total_female_electors > 0)
									$voter_turn_female_total = round((($total_voters_female/$total_female_electors)*100),2);
								else
									$voter_turn_female_total = 0;
								
								
								if($total_other_electors > 0)
									$voter_turn_other_total = round((($total_voters_other/$total_other_electors)*100),2);
								else
									$voter_turn_other_total = 0;
								
								@endphp
								
                                   <tr style="font-weight:bold;">
                                    <th class="borders2"><b>State Total:</b></th>
                                    <td class="borders2"></td>
                                    <td class="borders2"></td>
                                    <td class="borders2"><b>{{$total_electors}}</b></td>
                                    <td class="borders2"><b>{{$total_voters_male}}</b></td>
                                    <td class="borders2"><b>{{$total_voters_female}}</b></td>
                                    <td class="borders2"><b>{{$total_voters_other}}</b></td>
                                    <td class="borders2"><b>{{$total_voters_all}}</b></td>
                                    <td class="borders2"><b>{{$total_voters_nri}}</b></td>
									<td class="borders2"><b>{{$total_postal_all}}</b></td>

                                    <td class="borders2"><b>{{$total_voters_alltotal}}</b></td>
                                    <td class="borders2"><b>{{$voter_turn_all_total}}</b></td>
                                    <td class="borders2"><b>{{$voter_turn_male_total}}</b></td>
                                    <td class="borders2"><b>{{$voter_turn_female_total}}</b></td>
                                    <td class="borders2"><b>{{$voter_turn_other_total}}</b></td>
                                </tr>
                              </tbody>  
						 </table>
							@endforeach
							
							
							
							@php
								if($grand_total_electors > 0)
									$grand_voter_turn_all_total = round((($grand_total_voters_alltotal/$grand_total_electors)*100),2);
								else
									$grand_voter_turn_all_total = 0;
								
								
								if($grand_total_male_electors > 0)
									$grand_voter_turn_male_total = round((($grand_total_voters_male/$grand_total_male_electors)*100),2);
								else
									$grand_voter_turn_male_total = 0;


								if($grand_total_female_electors > 0)
									$grand_voter_turn_female_total = round((($grand_total_voters_female/$grand_total_female_electors)*100),2);
								else
									$grand_voter_turn_female_total = 0;
								
								
								if($grand_total_other_electors > 0)
									$grand_voter_turn_other_total = round((($grand_total_voters_other/$grand_total_other_electors)*100),2);
								else
									$grand_voter_turn_other_total = 0;
								
								@endphp
							 <table class="table table-bordered">	
                                <tr style="font-weight:bold;">
                                    <th colspan="3">All India Total:</th>
                                 
                                    <td class="borders">{{$grand_total_electors}}</td>
                                    <td>{{$grand_total_voters_male}}</td>
                                    <td>{{$grand_total_voters_female}}</td>
                                    <td>{{$grand_total_voters_other}}</td>
                                    <td>{{$grand_total_voters_all}}</td>
                                    <td>{{$grand_total_voters_nri}}</td>
									<td>{{$grand_total_postal_all}}</td>
                                    <td>{{$grand_total_voters_alltotal}}</td>
                                    <td>{{$grand_voter_turn_all_total}}</td>
                                    <td>{{$grand_voter_turn_male_total}}</td>
                                    <td>{{$grand_voter_turn_female_total}}</td>
                                    <td>{{$grand_voter_turn_other_total}}</td>
                                </tr>
							
							
                        </table>
						
					 </div>	


 <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>


<htmlpagefooter name='page-footer'>
 <table>
 <tr>


 <?php if (verifyreport(13) == 1){ ?>
 <td align="left"><span style="float:left;color: #d3d3d3;">{{getreportsequence(777)}}</span></td>
    
    <?php } ?>
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>
</table>
 </htmlpagefooter>

</body>	
</html>