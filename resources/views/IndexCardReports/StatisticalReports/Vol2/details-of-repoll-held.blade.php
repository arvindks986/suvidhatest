@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Details of Repoll held - Phase General Elections')
@section('content')

<?php $st = getstatebystatecode($user_data->st_code); ?> 
<section class="">
    <div class="container">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(Details of repoll held)</h4></div> 
                        <div class="col">
                            <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
                            </p>
                            <p class="mb-0 text-right">
                                <a href="{{'Details-of-repoll-held-pdf'}}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
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
                                    <th>Name of State/UT</th>                  
                                    <th>Total No. of Polling Stationin state</th>
                                    <th>P.C No.</th>
                                    <th>P.C. Name </th>
                                    <th>Total No. of Polling Stationwhere repoll held</th>
                                    <th>Date of Re-Pol </th>                                                        
                                </tr> 
                            </thead>
                            <tbody>  
                                <?php
                                $totalpolling = 0;
//                    print_r($data['stcode']);
                                $ftotal = 0;
                                ?>

                                @forelse($data as $rows)


                                <?php $i = 0;

                                if ($i != 0 && $state != $rows['state_name']) {
                                    ?>

                                    <tr colspan='3'><td>Total</td><td> <?php echo $rows['totalrepoll'] ?></td></tr>";
                                <?php }
                                ?>


                                <tr>
                                    <td rowspan="<?php echo sizeof($rows['pcinfo']); ?>">{{$rows['state_name']}}</td>
                                    <td rowspan="<?php echo sizeof($rows['pcinfo']); ?>">{{($rows['total_no_polling_station'])?$rows['total_no_polling_station']:'NILL'}}</td>
                                              
                                    @foreach($rows['pcinfo'] as $subrows)

                                    <?php
                                    if ($i != 0) {
                                        ?>
                                    <tr>
                                    <?php } ?>

                                    <td>{{$subrows['PC_NO']}}</td>
                                    <td >{{$subrows['PC_NAME']}}</td>
                                    <td >{{($subrows['no_repoll'])?$subrows['no_repoll']:"NILL"}}</td>
                                    <td >{{($subrows['dt_repoll'])?$subrows['dt_repoll']:'NILL'}}</td>
                                </tr>  
                                <?php
                                $i++;

                                $ftotal += $subrows['no_repoll'];
                                ?>
                                @endforeach
                                </tr>
<tr>
                        <td colspan="4">Total</td>
                        <td>{{array_sum($rows['totalrepoll'])}}</td></tr>
                                @empty
                                <tr><td colspan="6">Data not Found</td></tr>
                                @endforelse  
                                <tr ><td colspan='3'></td><td >Total</td><td> <?php echo $ftotal; ?></td></tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">All INDIA</th>
                                    <th>Grand Total</th>
                                    <th colspan="2"><?php echo $ftotal; ?></th>
                                </tr>
                            </tfoot>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
