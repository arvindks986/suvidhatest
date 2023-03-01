<html>

<head>
    <style>
    td {
        font-size: 14px !important;
        font-weight: 500 !important;
        color: #4a4646 !important;
        font-family: "Times New Roman", Times, serif;
    }


    .bordertestreport {
        border: 1px solid #666;
        padding: 30px 0px;
        background-image: url(../images/grid.png);
        background: #fff;
        background-repeat: repeat;
    }






    .tablecenterreport td {
        font-size: 16px;
    }

    .bothe {
        position: relative;
        top: -42px;
    }

    th {
        background: #959798;
        color: #fff !important;
        text-align: center;

        font-size: 14px;
    }

    tr:nth-child(even) {
        background: #8e99ab29;

    }
    </style>
</head>


        <div class="bordertestreport">
            <div class="bothe">
                <p> <img src="assets/images/Cyber-Security-Logo.png" class="img-responsive" style="width:100px !important;" alt=""></p>
                <div class="rwed" style="display: block;">
                    <p style="float: right;text-align: right; position: relative;top: -103px;font-size: 16px;font-weight: 600;">ELECTION COMMISSION OF INDIA, <br>Nirvachan Sadan, Ashoka Road, New Delhi-110001 <br> General Elections, 2019
                    </p>
                </div>
            </div>
            <hr style="color: #666;position: relative;top:-40px;">
            <div style="display: block;position: relative;top: -54px;">
                <p style="font-size: 14px;visibility: hidden;"><strong>State :</strong>  </p>
                <p style="font-size: 14px;float: right;position: relative;visibility: hidden; top: -34px;"> <strong>(Year</strong>&nbsp;&nbsp;&nbsp;) </p>
            </div>
            <div style="">
                <hr style="color: #666;position: relative;top:-150px;">
                <p style="position:absolute;top: 105px;right: 240px;">11 - Details of re-poll Held</p>
            </div>
                <table class="table table-bordered table-striped" style="width: 100%;">

                            <thead>
                                <tr>
                                    <th>Name of State/UT</th>
                                    <th>Total No. of Polling <br> Station in State</th>
                                    <th>P.C No.</th>
                                    <th>P.C. Name </th>
                                    <th>Total No. of Polling Station <br> where Re-poll Held</th>
                                    <th>Date of Re-Poll</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalpolling = 0;
                                $ftotal = 0;?>
                                @forelse($data as $rows)
                                <?php    $i = 0;
                                if ($i != 0 && $state != $rows['state_name']) {
                                  ?>
                                    <tr colspan='3'><td>Total</td><td> <?php echo $rows['totalrepoll'] ?></td></tr>";
                                <?php }  ?>
                                <tr>
                                    <td rowspan="<?php echo sizeof($rows['pcinfo']); ?>">{{$rows['state_name']}}</td>
                                    <td rowspan="<?php echo sizeof($rows['pcinfo']);
                                     ?>">{{($rows['total_no_polling_station'])?$rows['total_no_polling_station']:'NILL'}}</td>
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
                                //$ftotal += $subrows['no_repoll'];
                                $i++;
                                ?>
								 </tr>
                                @endforeach
                               
                      
                                @empty
                                <tr>
                                  <td colspan="6">Data not Found</td></tr>
                                @endforelse
                            </tbody>

                        </table>
                </div>
            </div>
        </div>
