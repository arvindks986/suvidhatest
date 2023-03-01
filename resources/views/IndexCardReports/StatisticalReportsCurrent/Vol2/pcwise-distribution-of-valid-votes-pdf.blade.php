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
                <td style="text-align:left;">
                    <p> <img src="img/Cyber-Security-Logo.png" class="img-responsive" style="width:100px;" alt="">  </p>
                </td>
              <td style="text-align: right;">
                <p style="float: right;width: 100%;">ELECTION COMMISSION OF INDIA, <br>Nirvachan Sadan, Ashoka Road, New Delhi-110001
                 <br> General Elections,2019 </p>
          </td>
      </tr>
  </table>

  <table>
      <tr>
          <td>
             <h3></h3>

          </td>
          <td style="text-align: right;">
          </td>
      </tr>
  </table>

<table> 

<tr>  

<td style="font-size: 17px;">  (14.PC Wise Distribution Of Valid Votes Polled)
</td>
</tr>
</table>

                          <table class="table table-bordered table-striped" style="width: 100%;table-layout: fixed;">
                            <thead>
                                <tr>
                                    <th colspan="5" style="font-size: 13px;">State : <span style="color: #000; font-style: normal;font-weight: bold; text-decoration: underline;"> {{$stname}}</span> </th>
                                </tr>
                                <tr class="table-primary">
                                  
                                <tr>
                                    <th rowspan="2">Sl.no. </th>
                                    <th rowspan="2">PC No. </th>
                                    <th rowspan="2">PC Name </th>
                                    <th colspan="2">Electors </th>
                                   <th colspan="2">Valid Votes Polled</th>
                                   <th>NOTA</th>
                                   <th colspan="2">Rejected/ Not Retrived Votes</th>
                                   <th rowspan="2">Total Voters</th>
                                   <th rowspan="2">Tendered Votes</th>
                                   <th rowspan="2">Test Votes</th>
                                   <th rowspan="2">Voter Turn Out (%)</th>
                                   <th rowspan="2">% Votes to Winner out of total Votes Polled</th>
                                   <th rowspan="2">% Votes to NOTA out of total Votes Polled</th>
                                </tr>
                             

                             <tr>
                                 
                             
                                 <th>Gereral</th>
                                 <th>Service</th>
                                 <th>EVM</th>
                                 <th>Postal</th>
                                 <th></th>
                                 <th>EVM</th>
                                 <th>POstal</th>
                             </tr>
                            </thead>
                            <tbody>
                                <?php $count=1; ?>
                                @forelse($data as $raw)
                                <?php
                                $totalelectors = $totalvoters = $voterturnout  = $notapercent = $candvote = $votertowinnerout = 0;
                                $totalelectors = $raw->e_all_t+$raw->e_ser_t;
                                $totalvoters=$raw->e_all_t;
                                if($totalelectors>0)
                                {
                                     $voterturnout=round((($totalvoters/$totalelectors)*100),2);
                                }
                                if($totalvoters>0)
                                {
                                    $notavote=$raw->total_votes_nota;
                                    $totalv=(($notavote/$totalvoters)*100);
                                    $notapercent=round($totalv,2);
                                }
                                if($totalvoters>0)
                                {
                                    $candvote=$raw->lead_total_vote;
                                    $votertowinnerout=(($candvote/$totalvoters)*100);
                                }
                               
                                ?>
                                <tr>
                                    <td>{{$count}}.</td>
                                    <td>{{$raw->PC_NO}}</td>
                                    <td>{{$raw->PC_NAME}}</td>
                                    <td>{{$raw->e_all_t}}</td>
                                    <td>{{$raw->e_ser_t}}</td>
                                    <td>{{$raw->v_votes_evm_all}}</td>
                                    <td>{{$raw->postal_valid_votes}}</td>
                                    <td>{{$raw->total_votes_nota}}</td>
                                    <td>{{$raw->r_votes_evm}}</td>
                                    <td>{{$raw->postal_vote_rejected}}</td>
                                    <td>{{$raw->e_all_t}}</td>
                                    <td>{{$raw->tendered_votes}}</td>
                                    <td>{{$raw->mock_poll_evm}}</td>
                                    <td>{{$voterturnout}}</td>
                                    <td>{{round($votertowinnerout,2)}}</td>
                                    <td>{{$notapercent}}</td>
                                </tr>
                                 <?php $count++; ?>
                                @empty
                               
                                <tr><td>Result Not Found</td></tr>


 <tr>
    @endforelse

                            </tbody>
                        </table>
                    
  
  </div>
  </html>