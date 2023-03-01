<html>
  <head>
 <style>
    td {
    font-size: 12px !important;
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
    font-size: 12px;
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
                <p style="float: right;width: 100%;">ELECTIONs COMMISSION OF INDIA, <br>Nirvachan Sadan, Ashoka Road, New Delhi-110001
                 <br> General Elections,2019 </p>
          </td>
      </tr>
  </table>

  <table>
      <tr>
          <td style="text-align: left;">
             <h3>4.List of Successful  Candidate</h3>

          </td>
          <td style="text-align: right;">
          </td>
      </tr>
  </table>





                      <table class="table table-striped table-bordered">
                    <thead class="">
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">CONSTITUENCY</th>
                            <th scope="col">WINNER</th>
                            <th scope="col">PARTY</th>
                            <th scope="col">PARTY SYMBOL</th>
                            <th scope="col">MARGIN</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach($arraydata as $allsuccessfullcondidate)
                        <tr>
                            <th>{{$allsuccessfullcondidate['state']}}</th>
                        <tr>
                            @foreach($allsuccessfullcondidate['pc'] as  $catwise)
                           <tr>
                            <td>{{$catwise['Pc_Name']}}</td>
                            <td>{{$catwise['PC_TYPE']}}</td>
                            <td>{{$catwise['Cand_Name']}}</td>
                            <td>{{$catwise['Party_Abbre']}}</td>
                            <td>{{$catwise['Party_symbol']}}</td>
                           <td> {{$catwise['margin']}} ({{$catwise['percent']}} %)</td>
                        </tr>
                       @endforeach
                      @endforeach

                    </tbody>
                </table>
                </div>
    

    </html>