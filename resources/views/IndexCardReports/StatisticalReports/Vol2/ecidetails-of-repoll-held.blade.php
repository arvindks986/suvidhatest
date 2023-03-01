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
                        <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(16 - Details of Re-poll Held)</h4></div>
                        <div class="col">
                            <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b>
                            </p>
                            <p class="mb-0 text-right">
                                <a href="{{'details-of-repoll-held-pdf'}}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
                                <a href="{{'details-of-repoll-held-xls'}}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" style="width: 100%;table-layout: fixed;">

                            <thead>
                                <tr>
                                    <th>Name of State/UT</th>
                                    <th>Total No. of Polling <br> Station in state</th>
                                    <th>No. of P.C.</th>
                                    <th>Name of P.C.</th>
                                    <th>Total No. of <br> Polling Station <br> where repoll held</th>
                                    <th>Date of <br> Re-Poll</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalpolling = 0;
                                $ftotal = 0;?>
                                @forelse($data as $rows)
                                <?php    $i = 0; ?>
                                <tr>
                                    <td rowspan="<?php echo sizeof($rows['pcinfo']); ?>">{{$rows['state_name']}}</td>
                                    <td rowspan="<?php echo sizeof($rows['pcinfo']);
                                     ?>">{{($rows['total_no_polling_station'])?$rows['total_no_polling_station']:'NILL'}}</td>
									 
									 
									 <?php
                                $total = 0;?> 
									 
									 
									 
                                    @foreach($rows['pcinfo'] as $subrows)
                                    <?php
                                    if ($i != 0) {
                                        ?>
                                    <tr>
                                    <?php } ?>
                                    <td>{{$subrows['PC_NO']}}</td>
                                    <td >{{$subrows['PC_NAME']}}</td>
                                    <td >{{($subrows['no_repoll'])}}</td>
                                    <td >
									@if (trim($subrows['dt_repoll']) != 0 && $subrows['dt_repoll'])
													
												<?php 
													$repoll_dates 	= explode(',',$subrows['dt_repoll']);
													$dates_array 	= [];
													foreach($repoll_dates as $res_repoll){
														$dates_array[] = date('d-m-Y', strtotime(trim($res_repoll)));
													}	
												?>
												
												{!! implode(', ', $dates_array) !!}
												@endif
									</td>
                                </tr>

                               
                                <?php
                                $ftotal += $subrows['no_repoll'];
                                $total += $subrows['no_repoll'];
                                $i++;
                                ?>
								 </tr>
                                @endforeach
                               
                      
								<tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><b>Total</b></td>
                                    <td><b>{{$total}}</b></td>
                                    <td></td>
                                    
                                </tr>
					  
					  
                                @empty


                                <tr>
                                  <td colspan="6">Data not Found</td></tr>
                                @endforelse

                                <tr>
                                    <td><b>ALL INDIA</b></td>
                                    <td></td>
                                    <td></td>
                                    <td><b>Grand Total</b></td>
                                    <td><b>{{$ftotal}}</b></td>
                                    <td></td>
                                    
                                </tr>


                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
