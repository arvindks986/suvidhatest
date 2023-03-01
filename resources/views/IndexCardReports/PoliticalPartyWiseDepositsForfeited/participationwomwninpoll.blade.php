@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Political Party Wise Deposits Fordeited-2019')
@section('content')
<?php  $st=getstatebystatecode($user_data->st_code);   ?>

<section class="">
 <div class="container-fluid">
 <div class="row">
 <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
     <div class=" card-header">
     <div class=" row">
           <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(19 - Participation of Women candidates in Poll)
</h4></div>
             <div class="col">
               <p class="mb-0 text-right">
                      <a href="#" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
                      <a href="#" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
               </p>
             </div>


           </div>
     </div>

<div class="card-body">

    <div class="table-responsive">
      <table class="table table-bordered table-striped" style="width: 100%;">
        <thead>
                        <tr>
                            <th colspan="3"> State / UT </th>
                            <th colspan="3" style="text-align:center">No. Of Women</th>
                            <th colspan="2" style="text-align:center">% of elected women</th>

                        </tr>
                        <tr>
                            <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th>Seats</th>
                            <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th>Contestants</th>
                            <th>Elected</th>
                            <th>Diposits Forfeited</th>
                            <th>over total women candidates in the state</th>
                            <th>over total seats in State/UT</th>
                        </tr>
                    </thead>
                  <tbody>
                    @foreach($datacandidate as $value)
                        <tr>
                            <td colspan="8">
                                <p class="state">{{$value['state']}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="8">
                                <span class="leftpad">42</span>
                            </td>
                        </tr>

                        @foreach ($value['pcinfo'] as $key => $valuetwo)

                          <?php //echo "<pre>"; print_r($valuetwo['category']);?>

                          <tr>
                            <td colspan="3" align="right" style="text-align:right "><strong>{{$valuetwo['category']}}</strong></td>
                            <td>{{$valuetwo['cont_female']}}</td>
                            <td>{{$valuetwo['fdfemale']}}</td>
                            <td>{{$valuetwo['fd']}}</td>
                            <td>{{$valuetwo['fdthird']}}</td>
                            <td>{{$valuetwo['fdmale']}}</td>


                          </tr>

                        @endforeach

                        @endforeach

         </tbody>

      </table>
    </div>
</div>
</div>
</div>
</div>
</section>
@endsection
