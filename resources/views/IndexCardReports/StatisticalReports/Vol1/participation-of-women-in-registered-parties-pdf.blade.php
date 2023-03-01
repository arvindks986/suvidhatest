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
  
  <td style="text-align: left;"><h3>28.Participation of Women in Registered Parties</h3></td>
</tr>
</table>

                <table class="table table-bordered table-striped">
                    <thead>
                                  
                                <tr>
                                    <th rowspan="2">Party Name </th>
                                    <th colspan="4">Candidates </th>
                                    <th colspan="2">Percentage </th>
                                    <th rowspan="2">Votes Secured By Party In State</th>
                                   <th colspan="3">% of votes secured</th>
                                   
                                </tr>
                             

                             <tr>
                                 
                                 <th>State</th>
                                 <th>Contested</th>
                                 <th>Won</th>
                                 <th>DF</th>
                                 <th>Won</th>
                                 <th>DF</th>
                                 <th>Over total electors in the State</th>
                                  <th>Over total valid votes in the State</th>
                                 <th>Over Votes secured by the party in State</th>
                             </tr>

                            
                            </thead>
                             <tbody>
                              <?php $totalcont = $totalallwon = $totalvsbp = $totalelectorsinstate = $totalvalidvotesinstate = $totalvv = $totalovervotessecuredbyparty = $totalwonpercent =$ttwonper  = 0;?>
                              @foreach($data as $row)
                              <?php 
                              $totalcont+= $row->totalcontested;
                              $totalallwon += ($row->totalwon)?$row->totalwon:0;
                              $totalvsbp+= $row->totalvotesecured;
                              $totalelectorsinstate+= $row->overtotalelectors;
                              $totalvv+= $row->overtotalvalidvotes;
                              $totalovervotessecuredbyparty+= $row->securedbyparties;
                               $totalwonpercent =round((($row->totalwon/$row->totalcontested)*100),2);
                               $ttwonper +=$totalwonpercent;
                              ?>
                                <tr>

                                   <td>{{$row->PARTYNAME}}</td>
                                   <td>{{$stname}} </td>
                                   <td>{{$row->totalcontested}}</td>
                                   <td>{{$row->totalwon}}</td>
                                   <td>N/A</td>
                                   <td>{{$totalwonpercent}}</td>
                                   <td>N/A</td> 
                                   <td>{{$row->totalvotesecured}}</td>
                                   <td>{{$row->overtotalelectors}}</td>
                                   <td>{{$row->overtotalvalidvotes}}</td>
                                   <td>{{$row->securedbyparties}}</td>
                                </tr>                                   
                                @endforeach
                                 <tr>
                                   <td><b>Grand Total</b></td>
                                   <td></td>
                                   <td>{{$totalcont}}</td>
                                   <td>{{$totalallwon}}</td>
                                   <td></td>
                                   <td>{{$ttwonper}}</td>
                                   <td></td>
                                   <td>{{$totalvsbp}}</td>
                                   <td>{{$totalelectorsinstate}}</td>
                                   <td>{{$totalvv}}</td>
                                   <td>{{$totalovervotessecuredbyparty}}</td>
                                   
                                </tr>
                 
                            </tbody>
                        </table>
                    </div>
                </html>