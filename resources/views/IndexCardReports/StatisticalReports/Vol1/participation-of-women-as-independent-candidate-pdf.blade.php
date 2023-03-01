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
              <h3>29.Participation of Women as Independent Candidates</h3>

           </td>
           <td style="text-align: right;">
               <p style="float: right;width: 100%;"><strong>State :</strong> All India </p>
           </td>
       </tr>
   </table>
   
   
                        <table class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                              
                                <tr class="table-primary">
                                  
                                <tr>
                                  <th>Party Name</th>
                                    <th colspan="3">Candidates </th>
                                    <th colspan="2">Percentage </th>
                                    <th>Votes Secured By Women Candidates </th>
                                    <th colspan="2">% of Votes Secured </th>
                                   
                                </tr>
                             

                            <tr>
                                <th></th>
                                <th>Contested</th>
                                <th>Won </th>
                                <th>DF</th>
                                <th>Won</th>
                                <th>DF</th>
                                <th></th>
                                <th>OVer Total Electors in Country</th>
                                <th>Over Total Valid Votes in Country</th>
                            </tr>
                            
                            </thead>
                             <tbody>
                               <?php $count = 1;
                            $totalc = $totalallwon = $totalvs = $totaloe = $totalvv = $totalwonpercent = $ttwonper = 0;?>
                              @foreach($data as $row)
                            <tr>
            <?php
                $totalc += ($row->totalcontested)?$row->totalcontested:0;
                $totalallwon += ($row->totalwon)?$row->totalwon:0;
                $totalvs += ($row->totalvotesecured)?$row->totalvotesecured:0;
                $totaloe += ($row->overtotalelectors)?$row->overtotalelectors:0;
                $totalvv += ($row->overtotalvalidvotes)?$row->overtotalvalidvotes:0;
                $totalwonpercent =round((($row->totalwon/$row->totalcontested)*100),2);
                $ttwonper +=$totalwonpercent;
               ?>
                                   
                              <td>{{$row->PARTYNAME}}</td>
                              <td>{{$row->totalcontested}}</td>
                              <td>{{$row->totalwon}}</td>
                              <td>N/A</td>
                              <td>{{$totalwonpercent}}</td>
                              <td>N/A</td>                              
                              <td>{{$row->totalvotesecured}}</td>
                              <td>{{$row->overtotalelectors}}</td>
                              <td>{{$row->overtotalvalidvotes}}</td>

                                </tr>
                            @endforeach



<tr>
    <td><b>Total</b></td>
                              <td>{{$totalc}}</td>
                              <td>{{$totalallwon}}</td>
                              <td></td>
                              <td>{{$ttwonper}}</td>
                              <td></td>                              
                              <td>{{$totalvs}}</td>
                              <td>{{$totaloe}}</td>
                              <td>{{$totalvv}}</td>
                              
</tr>




                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
