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
        <body>
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
         <td><h3>(State Wise Participation of Overseas Electors Voters - {{getElectionYear()}})</h3></td>
     </tr>
 </table>
            <table class="table table-bordered">
              <?php $sate = array();
              ?>
                @forelse($data as $rows)
                <?php $arrayq = (array)($rows);  if(!in_array($arrayq['st_name'], $sate)) { ?>
                    <tr>
                        <th style="font-size: 12px;">{{$rows->st_name}}</th>
                        <th colspan="8"></th>
                    </tr>

                    <tr class="table-primary">
                        <th scope="col">PC Type</th>
                        <th colspan="4">Electors</th>
                        <th colspan="4">Voters</th>
                    </tr>
<?php } $sate[] = $arrayq['st_name'];?>
<?php $arrayq = (array)($rows);  if(!in_array($arrayq['st_name'], $sate)) { ?>
                    <tr class="bold">
                        <td></td>
                        <td>Male</td>
                        <td>Female</td>
                        <td>Other</td>
                        <td>Total Electors</td>
                        <td>Male</td>
                        <td>Female</td>
                        <td>Other</td>
                        <td>Total Electors</td>
                    </tr>
<?php } $sate[] = $arrayq['st_name'];?>
                    <tr>
                        <td>{{$rows->PC_TYPE}}</td>
                        <td>{{$rows->emale}}</td>
                        <td>{{$rows->efemale}}</td>
                        <td>{{$rows->eother}}</td>
                        <td>{{$rows->etotal}}</td>
                        <td>{{$rows->nri_male_voters}}</td>
                        <td>{{$rows->nri_female_voters}}</td>
                        <td>{{$rows->evm_vote}}</td>
                        <td>{{$rows->total_vote}}</td>
                    </tr>
                @empty
                <tr>
                    <td>Data not found</td>
                </tr>
                @endforelse
            </table>
        </div>
        </body>
        </html>
