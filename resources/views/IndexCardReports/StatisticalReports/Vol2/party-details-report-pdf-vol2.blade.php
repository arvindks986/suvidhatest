<html>
   <head>
     <style>
    td {
    font-size: 12px !important;
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
            <h3>Party Details Report</h3>

           </td>
           <td style="text-align: right;">
               <p style="float: right;width: 100%;">All India </p>
           </td>
       </tr>
   </table>
                <table class="table table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Party Type</th>
                            <th>Abbreviation</th>
                            <th>Party Symbol</th>
                            <th>Party</th>
                        </tr>

                       
                    </thead>


                    <tbody>

                        <?php $i = 1; ?>
                        @foreach ($partyDetailData as $key => $row)
                                                
                        <tr>
                            @if($key=='N')
                            <th colspan="4">National Parties</th>
                            @elseif($key=='S')
                            <th colspan="4">State Parties</th>
                            @elseif($key=='U')
                             <th colspan="4">Registered(unrecognised) Parties</th>
                            @elseif($key=='Z1')
                            <th colspan="4">Nota</th>
                            @endif
                        </tr>
                       
                        @foreach ($row as $keys => $rowData)
                    
                        <tr>
                            <td>{{$i}} </td>
                            <td>{{$rowData['PARTYABBRE']}}</td>
                            <td>{{$rowData['SYMBOL_DES']}}</td>
                            <td>{{$rowData['PARTYNAME']}}</td>
                        </tr>
                        
                        <?php $i++; ?>

                        @endforeach
                        @endforeach

                    </tbody>
                </table>
                </div>
            </div>
        </div>

