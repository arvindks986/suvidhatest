@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'State Wise Participation of Overseas Electors Voters - Phase General Elections')
@section('content')


<style>
    
    td{

    }
</style>
<?php $st = getstatebystatecode($user_data->st_code); ?>
<section class="">
    <div class="container">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(11 - State Wise Participation of Overseas Electors Voters)</h4></div>
                        <div class="col">
                               
                            <p class="mb-0 text-right">
                                <a href="{{'State-wise-overseas-electors-voters-pdf'}}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
                                <a href="{{'State-wise-overseas-electors-voters-xls'}}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important; display: table-row;"></a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <?php 

                         $grandemale = $grandfemale =$grandother = $grandtotal = $grandvemale 
                         =$grandvefemale =$grandvother =$grandvtotals  = 0;
                    ?>

                  
                        @foreach($data as $key => $value)

                            <p><b>{{$key}}</b></p>

                       



   <table class="table table-bordered" style="width: 100%;">
                         

                        

                        

                                <tr>
                                    <th scope="col">PC Type</th>
                                    <th colspan="4">Electors</th>
                                    <th colspan="4">Voters</th>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Male</td>
                                    <td>Female</td>
                                    <td>Third Gender</td>
                                    <td>Total Electors</td>
                                    <td>Male</td>
                                    <td>Female</td>
                                    <td>Third Gender</td>
                                    <td>Total Voters</td>
                                </tr>
                                <?php 


                                            $totemale = $totefemale =$toteother = $totetotal = $totvemale
                                            = $totvefemale = $totvother = $totvtotals = 0;
                                ?>

                                 @foreach($value as $key1 => $value1)

                                        <tr>
                                            <td>{{$key1}}</td>
                                            <td>{{$value1['emale']}}</td>
                                            <td>{{$value1['efemale']}}</td>
                                            <td>{{$value1['eother']}}</td>
                                            <td>{{$value1['etotal']}}</td>
                                            <td>{{$value1['vemale']}}</td>
                                            <td>{{$value1['vefemale']}}</td>
                                            <td>{{$value1['vother']}}</td>
                                            <td>{{$value1['vtotals']}}</td>
                                        </tr>

                                        <?php 
                                            $totemale += $value1['emale'];
                                            $totefemale += $value1['efemale'];
                                            $toteother += $value1['eother'];
                                            $totetotal += $value1['etotal'];
                                            $totvemale += $value1['vemale'];
                                            $totvefemale += $value1['vefemale'];
                                            $totvother += $value1['vother'];
                                            $totvtotals += $value1['vtotals'];
                                        ?>

                                        
                                         

                                        @endforeach

                                          <tr>
                                            <td><b>STATE TOTAL</b></td>
                                            <td><b>{{$totemale}}</b></td>
                                            <td><b>{{$totefemale}}</b></td>
                                            <td><b>{{$toteother}}</b></td>
                                            <td><b>{{$totetotal}}</b></td>
                                            <td><b>{{$totvemale}}</b></td>
                                            <td><b>{{$totvefemale}}</b></td>
                                            <td><b>{{$totvother}}</b></td>
                                            <td><b>{{$totvtotals}}</b></td>
                                        </tr>

                                      
                                      <?php 
                                            $grandemale += $totemale;
                                            $grandfemale += $totefemale;
                                            $grandother += $toteother;
                                            $grandtotal += $totetotal;
                                            $grandvemale += $totvemale;
                                            $grandvefemale += $totvefemale;
                                            $grandvother += $totvother;
                                            $grandvtotals += $totvtotals;
                                        ?>   



                        </table>

                        
                        @endforeach


<table class="table table-bordered">
                          <tr>
                                            <td><b>TOTAL</b></td>
                                            <td><b>{{$grandemale}}</b></td>
                                            <td><b>{{$grandfemale}}</b></td>
                                            <td><b>{{$grandother}}</b></td>
                                            <td><b>{{$grandtotal}}</b></td>
                                            <td><b>{{$grandvemale}}</b></td>
                                            <td><b>{{$grandvefemale}}</b></td>
                                            <td><b>{{$grandvother}}</b></td>
                                            <td><b>{{$grandvtotals}}</b></td>
                                        </tr>
</table>





                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
