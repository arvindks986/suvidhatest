<html>
  <head>
      <style>
        td {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #4a4646 !important;
	text-align:center;
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
    font-size: 13px;
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
                 <br> General Elections, 2019 </p>
          </td>
      </tr>
  </table>

  <table>
      <tr>
          <td>
             <h3>4 - POLLING STATION INFORMATION</h3>

          </td>
          <td style="text-align: right;">
              <p style="float: right;width: 100%;"><strong>State :</strong> All India </p>
          </td>
      </tr>
  </table>		
                <table class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
					<tr>
                        <th colspan="3"></th>
                        <th>Polling Station</th>
                        <th colspan="4">General Electors</th>
                        <th colspan="4">Service Electors</th>
                        
						<th colspan="4">Grand Total</th>
                    </tr>
					</thead>
                    <tbody>
                        <tr>
                            <th>State/UT</th>
                            <th>PC. No.</th>
                            <th>PC Name</th>
                            <th>Total <br>(Regular+Auxilary)</th>
                            <th>Male</th>
                            <th>Female</th>
                            <th>Third</th>
                            <th>Total</th>
                            <th>Male</th>
                            <th>Female</th>
                            <th>Third</th>
                            <th>Total</th>
                            <th>Male</th>
                            <th>Female</th>
                            <th>Third</th>
                            <th>Total</th>
                        </tr>
                       
						<?php 
						
						$gen_m_sum_tot  = $gen_f_sum_tot = $gen_o_sum_tot =  $ser_m_sum_tot = $ser_f_sum_tot =  $ser_o_sum_tot = $pollingregaux_tot = 0;
						$flag = $stcode = $gen_m_sum  = $gen_f_sum = $gen_o_sum =  $ser_m_sum = $ser_f_sum = $ser_o_sum = $pollingregaux = 0;
						?>
						@foreach ($pollingstations as $pollingstation) 

						<?php 
						
						// if($stcode=='')
						// $stcode=$pollingstation->st_code;
					
						
						
						
						$pollingregaux_tot +=$pollingstation->total_no_polling_station;
						$gen_m_sum_tot += $pollingstation->e_gen_m;
						$gen_f_sum_tot += $pollingstation->e_gen_f;						
						$gen_o_sum_tot += $pollingstation->e_gen_o;						
						$ser_m_sum_tot += $pollingstation->e_ser_m;
						$ser_f_sum_tot += $pollingstation->e_ser_f;
						$ser_o_sum_tot += $pollingstation->e_ser_o;
						
						?>
						@if($stcode!=0 || $stcode!=$pollingstation->st_code)
						
						<tr>
                            <th colspan="3">Total</th>
                            <th>{{$pollingregaux}}</th>
                            <th>{{$gen_m_sum}}</th>
                            <th>{{$gen_f_sum}}</th>
                            <th>{{$gen_o_sum}}</th>
							<th>{{$gen_m_sum + $gen_f_sum + $gen_o_sum}}</th>
                            <th>{{$ser_m_sum}}</th>
                            <th>{{$ser_f_sum}}</th>
                            <th>{{$ser_o_sum}}</th>
							<th>{{$ser_m_sum + $ser_f_sum + $ser_o_sum}}</th>
                            <th>{{$gen_m_sum + $ser_m_sum}}</th>
                            <th>{{$ser_f_sum + $ser_f_sum}}</th>
                            <th>{{$gen_o_sum + $ser_o_sum}}</th>
                           
                            <th>{{$gen_m_sum + $gen_f_sum + $ser_m_sum + $ser_f_sum + $gen_o_sum + $ser_o_sum}}</th>
                        </tr> 
							<?php 
							$gen_m_sum  = $gen_f_sum = $gen_o_sum = $ser_m_sum = $ser_f_sum = $ser_o_sum = $pollingregaux = 0;
						
						?>
						 @endif
						 <?php 
							
						$pollingregaux +=$pollingstation->total_no_polling_station;
						$gen_m_sum += $pollingstation->e_gen_m;
						$gen_f_sum += $pollingstation->e_gen_f;						
						$gen_o_sum += $pollingstation->e_gen_o;						
						$ser_m_sum += $pollingstation->e_ser_m;
						$ser_f_sum += $pollingstation->e_ser_f;
						$ser_o_sum += $pollingstation->e_ser_o;
						 ?>
						@if($stcode=='' || $stcode!=$pollingstation->st_code)
						 <tr style="width:100%;">
                            <th colspan="16" class="gry" style="text-align: left;">{{ $pollingstation->st_name }}</th>
                        </tr>
						<?php $stcode = $pollingstation->st_code; ?>
						@endif
						 <tr>
                            <td></td>
                            <td>{{$pollingstation->pc_no}}</td>
                            <td>{{$pollingstation->pc_name}}</td>
                            <td>{{$pollingstation->total_no_polling_station ? : 0}}</td>
                            <td>{{$pollingstation->e_gen_m}}</td>
                            <td>{{$pollingstation->e_gen_f}}</td>
                            <td>{{$pollingstation->e_gen_o}}</td>
							<td>{{$pollingstation->e_gen_m + $pollingstation->e_gen_f + $pollingstation->e_gen_o}}</td>
                            <td>{{$pollingstation->e_ser_m}}</td>
                            <td>{{$pollingstation->e_ser_f}}</td>
                            <td>{{$pollingstation->e_ser_o}}</td>
							<td>{{$pollingstation->e_ser_m + $pollingstation->e_ser_f + $pollingstation->e_ser_o}}</td>
                            <td>{{$pollingstation->e_gen_m + $pollingstation->e_ser_m}}</td>
                            <td>{{$pollingstation->e_gen_f + $pollingstation->e_ser_f}}</td>
                            <td>{{$pollingstation->e_gen_o + $pollingstation->e_ser_o}}</td>
                           
                            <td>{{ $pollingstation->e_gen_m + $pollingstation->e_ser_m + $pollingstation->e_gen_f + $pollingstation->e_ser_f + $pollingstation->e_gen_o + $pollingstation->e_ser_o}}</td>
                        </tr>
					
						<?php
				
						if($stcode!=$pollingstation->st_code){
						$stcode=$pollingstation->st_code;
						$flag=0;
					    
						?>
						
                          
                        
						<?php  }?>
						
                        @endforeach
                       <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>{{$pollingregaux}}</th>
                            <th>{{$gen_m_sum}}</th>
                            <th>{{$gen_f_sum}}</th>
                            <th>{{$gen_o_sum}}</th>
							<th>{{$gen_m_sum + $gen_f_sum + $gen_o_sum}}</th>
                            <th>{{$ser_m_sum}}</th>
                            <th>{{$ser_f_sum}}</th>
                            <th>{{$ser_o_sum}}</th>
							<th>{{$ser_m_sum + $ser_f_sum + $ser_o_sum}}</th>
                            <th>{{$gen_m_sum + $ser_m_sum}}</th>
                            <th>{{$gen_f_sum + $ser_f_sum}}</th>
                            <th>{{$ser_o_sum + $ser_o_sum}}</th>
                           
                            <th>{{$gen_m_sum + $gen_f_sum + $ser_m_sum + $ser_f_sum + $ser_o_sum + $ser_o_sum}}</th>
                        </tr> 
                       
                        
                        <tr>
                            <td colspan="2" class="blc"><b>All India Total</b></td>
                            <td></td>
                            <td><b>{{$pollingregaux_tot}}</b></td>
                            <td><b>{{$gen_m_sum_tot}}</b></td>
                            <td><b>{{$gen_f_sum_tot}}</b></td>
                            <td><b>{{$gen_o_sum_tot}}</b></td>
							<td><b>{{$gen_m_sum_tot + $gen_f_sum_tot + + $gen_o_sum_tot}}</b></td>
                            <td><b>{{$ser_m_sum_tot}}</b></td>
                            <td><b>{{$ser_f_sum_tot}}</b></td>
                            <td><b>{{$ser_o_sum_tot}}</b></td>
							<td><b>{{$ser_m_sum_tot + $ser_f_sum_tot + $ser_o_sum_tot}}</b></td>
							<td><b>{{$gen_m_sum_tot + $gen_m_sum_tot}}</b></td>
                            <td><b>{{$gen_f_sum_tot + $ser_f_sum_tot}}</b></td>
                            <td><b>{{$gen_o_sum_tot + $ser_o_sum_tot}}</b></td>
							<td><b>{{$gen_m_sum_tot + $gen_f_sum_tot + $ser_m_sum_tot + $ser_f_sum_tot + $gen_o_sum_tot + $ser_o_sum_tot}}</b></td>
                        </tr>
                    </tbody>
							
						</table>