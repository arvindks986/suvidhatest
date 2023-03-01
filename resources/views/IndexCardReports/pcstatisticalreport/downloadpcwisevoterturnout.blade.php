<html>
  <head>
       <style>
    td {
    font-size: 11px !important;
    font-weight: 500 !important;
    color: #4a4646 !important;
    text-align: center;
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

    .bordertestreport{
      border:1px solid #000;
    }
    .border{
    border-bottom: 1px solid #000;
    }
    th {
    background: #eff2f4;
    color: #000 !important;
    text-align: center;
    font-size: 11px;
    font-weight: bold !important;
    }
    
    table{
    width: 100%;
    }
    
    </style>
  </head>
  <div class="bordertestreport">
      <table class="border">
          <tr>
                <td>
                    <p> <img src="img/Cyber-Security-Logo.png" class="img-responsive" style="width:100px;" alt="">  </p>
                </td>
              <td style="text-align: right;">
                <p style="float: right;width: 100%;">ELECTION COMMISSION OF INDIA, <br>Nirvachan Sadan, Ashoka Road, New Delhi-110001
                 <br> General Elections, {{$year}} </p>
          </td>
      </tr>
  </table>

  <table>
      <tr style="text-align: center;">
          <td>
             <h3>State Wise Voter Turnout</h3>

          </td>
          <td style="text-align: right;">
              <p style="float: right;width: 100%;"><strong>State :</strong> All India </p>
          </td>
      </tr>
  </table>		
                       <table class="table table-bordered table-striped" style="width: 100%;table-layout: fixed;">
							@php 
							$sl_no = 1;
							@endphp
							@foreach($pcwisevoterturnouts as $key=>$row)
					
                                <tr>
                                    <th colspan="2" style="font-size: 12px;">State : <span style="color: #000; font-style: normal;font-weight: bold; text-decoration: underline;"> {{$key}}</span> </th>
                                </tr>
                                <tr class="table-primary">
                                    <th colspan="4"> </th>
                                   
                                    <th colspan="6">Voters </th>
                                    <th> Voter Turn Out%</th>
                                    <th colspan="3">Voter Turn Out (Excl. Postal) % </th>
                                </tr>
								
                                <tr>
                                    <th> </th>
                                    <th> </th>
                                    <th> </th>
                                    <th> </th>
                                    <th colspan="4"> EVM </th>
                                    <th> Postal Votes </th>
                                    <th> Total Votes </th>
                                    <th> </th>
                                    <th> Male </th>
                                    <th> Female </th>
                                    <th> Other</th>
                                </tr>
                                <tr>
                                    <th> SL. NO. </th>
                                    <th> PC Name </th>
                                    <th> PC No. </th>
                                    <th> Electors </th>
                                    <th> Male </th>
                                    <th> Female </th>
                                    <th> Other </th>
                                    <th> Total </th>
                                    <th colspan="2"> </th>
                                    <th> </th>
                                    <th colspan="3"> </th>
                                    
                                </tr>
                           
							@php
								$total_electors = 0;
								$total_voters_male = 0;
								$total_voters_female = 0;
								$total_voters_other = 0;
								$total_voters_all = 0;
								$total_voters_alltotal = 0;
								$total_voter_turn_all = 0;
								$total_voter_turn_male = 0;
								$total_voter_turn_female = 0;
								$total_voter_turn_other = 0;
							
							@endphp
							
								@foreach($row as $no => $value)
								
								@php 
								
								if($value['electors_total'] > 0)
									$voter_turn_all = round((($value['voter_total']/$value['electors_total'])*100),2);
								else
									$voter_turn_all = 0;
								
								
								if($value['electors_male'] > 0)
									$voter_turn_male = round((($value['voter_male']/$value['electors_male'])*100),2);
								else
									$voter_turn_all = 0;


								if($value['electors_female'] > 0)
									$voter_turn_female = round((($value['voter_female']/$value['electors_female'])*100),2);
								else
									$voter_turn_all = 0;
								
								
								if($value['electors_other'] > 0)
									$voter_turn_other = round((($value['voter_other']/$value['electors_other'])*100),2);
								else
									$voter_turn_all = 0;
								
								$total_electors += $value['electors_total'];
								$total_voters_male += $value['voter_male'];
								$total_voters_female += $value['voter_female'];
								$total_voters_other += $value['voter_other'];
								$total_voters_all += $value['voter_total'];
								$total_voters_alltotal += $value['voter_total'];
								$total_voter_turn_all += $voter_turn_all;
								$total_voter_turn_male += $voter_turn_male;
								$total_voter_turn_female += $voter_turn_female;
								$total_voter_turn_other += $voter_turn_other;
																
								@endphp

                                <tr>
                                    <td>{{$sl_no}}.</td>
                                    <td>{{$value['PC_NAME']}}</td>
                                    <td>{{$value['PC_NO']}}</td>
                                    <td>{{$value['electors_total']}}</td>
                                    <td>{{$value['voter_male']}}</td>
                                    <td>{{$value['voter_female']}}</td>
                                    <td>{{$value['voter_other']}}</td>
                                    <td>{{$value['voter_total']}}</td>
                                    <td>NA</td>
                                    <td>{{$value['voter_total']}}</td>
                                    <td>
										{{$voter_turn_all}}
									</td>
									<td>
										{{$voter_turn_male}}
									</td>
                                    <td>
										{{$voter_turn_female}}
									</td>
                                    <td>
										{{$voter_turn_other}}
									</td>
                                </tr>
								@php 
									$sl_no++;
								@endphp
																
								@endforeach
                                
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td>{{$total_electors}}</td>
                                    <td>{{$total_voters_male}}</td>
                                    <td>{{$total_voters_female}}</td>
                                    <td>{{$total_voters_other}}</td>
                                    <td>{{$total_voters_all}}</td>
									<td>NA</td>
                                    <td>{{$total_voters_alltotal}}</td>
                                    <td>{{round($total_voter_turn_all/($no+1),2)}}</td>
                                    <td>{{round($total_voter_turn_male/($no+1),2)}}</td>
                                    <td>{{round($total_voter_turn_female/($no+1),2)}}</td>
                                    <td>{{round($total_voter_turn_other/($no+1),2)}}</td>
                                </tr>
                                
						
							@endforeach
							
							
                        </table>