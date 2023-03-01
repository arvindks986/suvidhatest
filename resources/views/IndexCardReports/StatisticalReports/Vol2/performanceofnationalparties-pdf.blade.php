<html>

<head>
<style>
   td {
   font-size: 12px !important;
   font-weight: 500 !important;
   color: #000 !important;
   font-family: "Times New Roman", Times, serif;
   text-align: center;
   }
   h3{
   font-size: 15px !important;
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


 <h3>20.PERFORMANCE OF NATIONAL PARTIES</h3>



        <table class="table table-striped table-bordered">
                        <thead class="">
                            <tr>
                                <th scope="col">Party name</th>
                                <th scope="col" colspan="3">Candidate</th>
                                <th scope="col">Votes</th>
                                <th scope="col" colspan="2">% of Votes Secured</th>
                            </tr>

                            <tr>
                                <th scope="col"></th>
                                <th scope="col">Contested</th>
                                <th scope="col">Won</th>
                                <th scope="col">DF</th>
                                <th scope="col">Votes Secured by Party</th>
                                <th scope="col">Over total electors</th>
                                <th scope="col">Over total valid votes polled</th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php
                           $totalcontested = 0;
                           $won = 0;
                           $fd = 0;
                           $secure = 0;
                           $electorspercent = 0;
                           $overtotalvaliedpercent = 0;
                           ?>
                            @foreach($data as $rows)

							@php
								$peroverelectors = ($rows->total_vote/$totalElectors[0]->total_electors)*100;
								$perovervoter = ($rows->total_vote/$totalVotes[0]->totalVotes)*100;
							@endphp

                            <tr>
                                <td>{{$rows->partyname}}</td>
                                <td>{{$rows->contested}}</td>
                                <td>{{$rows->won}}</td>
                                <td>{{$rows->fd}}</td>
                                <td>{{$rows->total_vote}}</td>
                                <td>{{round($peroverelectors,2)}}</td>
                                <td>{{round($perovervoter,2)}}</td>
                                <?php

                                 $totalcontested += $rows->contested;
                                $won += $rows->won;
                                $fd += $rows->fd;
                                $secure += $rows->total_vote;
                                $electorspercent += $peroverelectors;
                                $overtotalvaliedpercent += $perovervoter;

                                        ?>
                            </tr>
                        @endforeach
                            <tr><td>Total</td>
                            <td>{{$totalcontested}}</td>
                             <td>{{$won}}</td>
                           <td>{{$fd}}</td>
                           <td>{{$secure}}</td>
                           <td>{{round($electorspercent,2)}}</td>
                           <td>{{round($overtotalvaliedpercent,2)}}</td>
                            </tr>

                        </tbody>


                    </table>

<div style="font-size:8px;">TOTAL ELECTORS IN THE COUNTRY (INCLUDING SERVICE - ELECTORS) -<span>{{$totalElectors[0]->total_electors}}</span></div>
<div style="font-size:8px;">TOTAL VALID VOTES POLLED IN THE COUNTRY (INCLUDING SERVICE-VOTES -<span>{{$totalVotes[0]->totalVotes}}</span></div>



                </div>


</body>
