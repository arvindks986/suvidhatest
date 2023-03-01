
<html>
  <head>
      <style>
    td {
    font-size: 10px !important;
    font-weight: 500 !important;
    color: #4a4646 !important;
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
    font-size: 10px;
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
                 <br> General Elections,2019 </p>
          </td>
      </tr>
  </table>

  <table>
      <tr>
          <td>
             <h3>(22.Performance of registered unrecognised parties)</h3>

          </td>
          <td style="text-align: right;">
          </td>
      </tr>
  </table>




                        <table class="table table-bordered table-striped" style="table-layout: fixed;">
                            <thead>
                                <tr class="table-primary">
                                    <th>Party Name</th>
                                    <th colspan="3">Candidates</th>
                                    <th>Votes secured by Party</th>
                                    <th colspan="2">% of votes secured</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>Contested</th>
                                    <th>Won</th>
                                    <th>DF</th>
                                    <th></th>
                                    <th>Over total Electors in State</th>
                                    <th>Over total Valid Votes Polled in State</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($result as $value)
                                <?php 
                                    $overTotElec = ($value->TotalElectorsState)?round((($value->totalvotesparty/$value->TotalElectorsState)*100),2):0;
                                    $overTotValVotes = ($value->v_votes_evm_all)?round((($value->totalvotesparty/$value->v_votes_evm_all)*100),2):0;
                                 ?>
                                <tr>
                                    <td> {{$value->PARTYNAME}} </td>
                                    <td> {{$value->c_nom_co_t}} </td>
                                    <td> NA </td>
                                    <td> NA </td>
                                    <td> {{$value->totalvotesparty}} </td>
                                    <td> {{$overTotElec}} </td>
                                    <td> {{$overTotValVotes}} </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </html>
