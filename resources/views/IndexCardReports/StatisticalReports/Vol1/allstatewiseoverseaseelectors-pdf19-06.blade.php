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
         <td><h3>(State Wise Participation of Overseas Electors Voters)</h3></td>
     </tr>
 </table>
            <table class="table table-bordered">
                @forelse($allstateelectors as $values)
                    <tr>
                        <th style="font-size: 12px;">{{$values->ST_NAME}}</th>
                        <th colspan="8"></th>
                    </tr>                                    
                    <tr class="table-primary">
                        <th scope="col">PC Type</th>
                        <th colspan="4">Electors</th>
                        <th colspan="4">Voters</th>
                    </tr>
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

                    <?php
                    $pcdata = DB::table('electors_cdac as pc')
                                                            ->select('pc.pc_no', 'mp.PC_TYPE', DB::raw('SUM(pc.electors_male) as emale'), DB::raw('SUM(pc.electors_female)as efemale'), DB::raw('SUM(pc.electors_other) as eother'), DB::raw('SUM(pc.electors_total)as etotal'), DB::raw('SUM(pc.voter_male)as votermale'), DB::raw('SUM(pc.voter_female)as voterfemale'), DB::raw('SUM(counting.evm_vote)as totalvotes'), DB::raw('SUM(counting.total_vote)as totalvalidvote')
                                                            )
                                                            ->join('m_pc as mp', 'mp.ST_CODE', '=', 'pc.st_code')
                                                            ->join('counting_pcmaster as counting',function($query){
                                                              $query->on('counting.st_code', '=', 'pc.st_code')
                                                              ->on('counting.pc_no', '=', 'pc.pc_no');
                                                            })
                                                            ->where('pc.st_code', $values->ST_CODE)
                                                            ->groupby('mp.PC_TYPE')
                                                            ->get();
                    ?>

                    @forelse($pcdata as $rows)
                    <tr>
                        <td>{{$rows->PC_TYPE}}</td>
                        <td>{{$rows->emale}}</td>
                        <td>{{$rows->efemale}}</td>
                        <td>{{$rows->eother}}</td>
                        <td>{{$rows->etotal}}</td>
                        <td>{{$rows->votermale}}</td>
                        <td>{{$rows->voterfemale}}</td>
                        <td>{{$rows->totalvotes}}</td>
                        <td>{{$rows->totalvalidvote}}</td>
                    </tr>
                    @empty

                    @endforelse
                
                @empty
                <tr>
                    <td>Data not found</td>
                </tr>
                @endforelse
            </table>
        </div>
        </body>
        </html>