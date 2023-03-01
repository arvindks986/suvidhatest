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
                 <td style="text-align: left;">
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
           <td style="text-align: left;">
              <h3>26.Participation of Women in National Parties</h3>

           </td>
           <td style="text-align: right;">
               <p style="float: right;width: 100%;"><strong></strong>All India</p>
           </td>
       </tr>
   </table>
   
                        <table class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                
                                <tr class="table-primary">
                                  
                                <tr>
                                    <th rowspan="2">Party Name </th>
                                    <th colspan="">Candidates </th>
                                    <th colspan="2">Percentage </th>
                                    <th rowspan="2">Votes Secured By Women Candidates </th>
                                   <th colspan="3">% of votes secured</th>
                                   
                                </tr>
                             

                             <tr>
                                 
                                 <th>Contested</th>
                                 <th>Won</th>
                                 <th>DF</th>
                                 <th>Over total electors in the State</th>
                                  <th>Over total valid votes in the State</th>
                                 <th>Over Votes secured by the party in State</th>
                             </tr>

                            
                            </thead>
                             <tbody>
                              <?php
                            $totalallcontested = $totalallwon = $totalwon = $totaldf = $totalvsecuredbyf = $totalelectors = $totalvalidvotes = $overvotessecuredbyparty = $tvsbp = 0;
                              ?>
                            @forelse($sData as $raw)
                            <?php 
                            $totalallcontested+=$raw->totalcontested;
                            $totalallwon+=$raw->totalwon;
                            $totalvsecuredbyf+=$raw->totalvote;
                            $totalelectors+=$raw->overtotalelectors;
                            $totalvalidvotes+=$raw->overtotalvalidvotes;
                            $tvsbp+=$raw->ovsbp;

                            ?>
                                <tr>
                                   <td>{{$raw->PARTYNAME}}</td>
                                   <td>{{$raw->totalcontested}}</td>
                                   <td>{{$raw->totalwon}}</td>
                                   <td>N/A</td>
                                   
                                   <td>{{$raw->totalvote}}</td>
                                   <td>{{$raw->overtotalelectors}}</td>
                                   <td>{{$raw->overtotalvalidvotes}}</td>
                                   <td>{{round($raw->ovsbp,2)}}</td>
                                   
                                </tr>

                              @empty
                              <tr>
                                   <td>Result Not Found</td>
                                </tr>
                              @endforelse
                              <tr>
                                <td>Total</td>
                                <td>{{$totalallcontested}}</td>
                                <td>{{$totalallwon}}</td>
                                <td></td>
                                <td>{{$totalvsecuredbyf}}</td>
                                <td>{{$totalelectors}}</td>
                                <td>{{$totalvalidvotes}}</td>
                                <td>{{round($tvsbp,2)}}</td>
                              </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>