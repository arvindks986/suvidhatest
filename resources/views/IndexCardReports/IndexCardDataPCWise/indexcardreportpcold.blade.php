@extends('admin.layouts.pc.dashboard-theme')
@section('content')
<style type="text/css">
    .modal-content {
        border-radius: 0px;
    }

table.table.table-bordered.table-responsive select {
    border-radius: 0;
    box-shadow: none;
    border: 1px solid #c5bebe;
    width: 190px !important;
    font-size: 12px !important;
}
.wrapper {
    padding: 15px;
    background: #f9f9f9 !important;
    position: relative;
}


    th {
font-size: 12px !important;
    white-space: nowrap;
}

div#index_cus_ch ul li.active{
background: #17a2b8;
}

div#index_cus_ch ul li.active a{
color: #fff !important;
}



div#index_cus_ch ul li:hover{
    background: #f0587e;
    text-decoration: none;
}

div#index_cus_ch ul li:hover a{
color: #fff !important;
text-decoration: none;
}



div#index_cus_ch ul li {
        background: #fff;
    padding: 10px;
    margin: 1px;
    border: 1px solid #eee;
}
div#index_cus_ch ul li a {
 color: #000 !important;
 }



    td {
font-size: 12px !important;
    vertical-align: middle !important;
    white-space: nowrap;
    text-align: center !important;
}
table.table.table-bordered.table-responsive.tableindexcard {
    table-layout: fixed;
    white-space: normal;
}

.main-content {
    margin-left: 240px;
    background: #f9f9f9;
}
h4.page-title {
    font-size: 23px;
     font-style: italic; 
    font-weight: 600;
    font-family: 'poppins';
}
</style>
<?php //echo "<pre>"; print_r($getIndexCardDataPCWise); die; ?>
<!--body wrapper start-->
<div class="wrapper">

    <!--Start Page Title-->
    <div class="container">
             <div class="row" style="position: relative;top: -20px;">
                    <div class="col-sm-9" style="text-align: center;position: relative;left: 28px;">
                    <h4 class="page-title">Index Card Parliamentary </h4>
                    </div>
                        <div class="col-sm-3" style="padding: 0px;">
                        <div class="col-sm-8 right_inde">
                       Type Of Constituency: 
                                              </div>
                                               <div class="col-sm-3">
                       <span id="pc_type">{{$getIndexCardDataPCWise['pcType']->PC_TYPE}}</span></div>
                        
<div class="col-sm-8 right_inde">
                        Year Of Election:
                      </div>           
<div class="col-sm-3" style="padding: 0px;">


                                   <span id="year">{{$getIndexCardDataPCWise['election_detail']['YEAR']}}</span>
</div>                     
                        </div>

</div>


   
</div>
    <!--End Page Title-->
    <!----Success Message------>
    @if(session()->has('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      {{session()->get('success')}}
    </div>
    @endif

    <!--Basic Information-->



  
<div class="col-sm-12" style="border-top: 5px solid #2c3b48a6;padding: 44px 0px;">

<div class="col-sm-4">
           <div class="col-sm-2"> 
            <b>State:</b>  
            </div>
            <div class="col-sm-9">
            <span id="state_name">{{$getIndexCardDataPCWise['election_detail']['st_name']}}</span>
            </div>
       </div>
      
<div class="col-sm-8" style="float: right;">
           <div class="col-sm-8" style="text-align: right;"> 


 <b>Number & Name Of Parliamentary Constituency: </b>
</div>

           <div class="col-sm-4"> 

 <span id="pc_name">{{$getIndexCardDataPCWise['pcType']->PC_NO}} : {{$getIndexCardDataPCWise['pcType']->PC_NAME}}</span>

</div>
</div>


<div class="col-sm-12">
    <div class="col-sm-4" style="position: relative;left: -16px;top: 8px;">
      <div class="col-sm-2"> <b> District:</b>
       </div>
             
             <div class="col-sm-9">
                             <span id="district_name">{{$getIndexCardDataPCWise['distict_name']}}</span></div>

</div>

</div>
         

         




    <br>
    <br>
   
    <br>
    <br>
    <!--Basic Information-->
    <!--Start row-->
    <div class="row" id="index_cus_ch">
        <!--tabs starts-->

        <ul class="nav nav-tabs new_nav">
            <li class="active"><a data-toggle="tab" href="#menu1">Data For Election Card Index</a></li>
            <li><a data-toggle="tab" href="#menu2">Information About Candidate in PC</a></li>
            <li><a data-toggle="tab" href="#menu3">Data For Election AC Wise</a></li>
        </ul>

        <div class="tab-content" style="overflow: auto;">

            <div id="menu1" class="tab-pane fade in active">
                <!--  <h3>Data For Election</h3> -->
                <?php
                    if($getIndexCardDataPCWise['electorData'][0]->polling_total > 0)
                    {
                        
                    $avg_elec_polling_stn = $getIndexCardDataPCWise['electorData'][0]->total_t/$getIndexCardDataPCWise['electorData'][0]->polling_total;

                    }else{
                        $avg_elec_polling_stn =0;
                    } ?>
                        <div class="col-sm-12">
                            <div class="col-md-6 col-sm-12 col-xs-12 tab_card">
                                <h2>Part -A </h2>
                                <form action="updatepcwisedata" class="form-horizontal updatepcwisedata" method="post" enctype="multipart/form-data">
                                
                                @csrf
                                <input type="hidden" name="st_code" value="{{$getIndexCardDataPCWise['election_detail']['st_code']}}">
                                <input type="hidden" name="pc_no" value="{{$getIndexCardDataPCWise['pcType']->PC_NO}}">
                                <input type="hidden" name="schedule_id" value="{{$getIndexCardDataPCWise['election_detail']['ScheduleID']}}">
                                
                                    <div class="table-responsive">
                                    <table class="table table-bordered tableindexcard">
                                        <thead>
                                            <tr>
                                                <th>Total Elector's</th>
                                                <th>Male</th>
                                                <th>Female</th>
                                                <th>Third Gender</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr data-parent="e" data-category="nri">
                                                <td> 1.General (NRI)</td>
                                                <td><input class="form-control row_sum1 col_sum1" type="number" min="0" name="e_nri_m" value="<?php echo isset($getIndexCardDataPCWise['electorData'][0]->e_nri_m)?$getIndexCardDataPCWise['electorData'][0]->e_nri_m:0;?>"></td>
                                                <td><input class="form-control row_sum1 col_sum2" type="number" min="0" name="e_nri_f" value="<?php echo isset($getIndexCardDataPCWise['electorData'][0]->e_nri_f)?$getIndexCardDataPCWise['electorData'][0]->e_nri_f:0;?>"></td>
                                                <td><input class="form-control row_sum1 col_sum3" type="number" min="0" name="e_nri_o" value="<?php echo isset($getIndexCardDataPCWise['electorData'][0]->e_nri_o)?$getIndexCardDataPCWise['electorData'][0]->e_nri_o:0;?>"></td>
                                                <td><input class="form-control" id="row_sum1_total" type="number" min="0" name="e_nri_t"  value="<?php echo isset($getIndexCardDataPCWise['electorData'][0]->e_nri_t)?$getIndexCardDataPCWise['electorData'][0]->e_nri_t:0;?>" readonly=""></td>
                                            </tr>
                                            <tr>
                                                <td> 3.General</td>
                                                <td><input class="form-control row_sum2 col_sum1" type="number" min="0" name="e_gen_m" value="{{($getIndexCardDataPCWise['electorData'][0]->gen_m)}}"></td>
                                                <td><input class="form-control row_sum2 col_sum2" type="number" min="0" name="e_gen_f" value="{{$getIndexCardDataPCWise['electorData'][0]->gen_f}}"></td>
                                                <td><input class="form-control row_sum2 col_sum3" type="number" min="0" name="e_gen_o" value="{{$getIndexCardDataPCWise['electorData'][0]->gen_o}}"></td>
                                                <td><input class="form-control"  id="row_sum2_total"  type="number" min="0" name="e_gen_t" value="{{$getIndexCardDataPCWise['electorData'][0]->gen_t}}" readonly></td>
                                            </tr>
                                            <tr>
                                                <td> 3.Service</td>
                                                <td><input class="form-control row_sum3 col_sum1" type="number" min="0" name="e_ser_m" value="{{$getIndexCardDataPCWise['electorData'][0]->ser_m}}"></td>
                                                <td><input class="form-control row_sum3 col_sum2" type="number" min="0" name="e_ser_f" value="{{$getIndexCardDataPCWise['electorData'][0]->ser_f}}"></td>
                                                <td><input class="form-control row_sum3 col_sum3" type="number" min="0" name="e_ser_o" value="{{$getIndexCardDataPCWise['electorData'][0]->ser_o}}"></td>
                                                <td><input class="form-control" id="row_sum3_total" type="number" min="0" name="e_ser_t" value="{{$getIndexCardDataPCWise['electorData'][0]->ser_t}}" readonly></td>
                                            </tr>
                                            <tr>
                                                <td> 4.Total </td>
                                                <td><input class="form-control col_sum1_total" type="number" min="0" name="e_all_t_m" value="{{$getIndexCardDataPCWise['electorData'][0]->total_m}}" readonly></td>
                                                <td><input class="form-control col_sum2_total" type="number" min="0" name="e_all_t_f" value="{{$getIndexCardDataPCWise['electorData'][0]->total_f}}" readonly></td>
                                                <td><input class="form-control col_sum3_total" type="number" min="0" name="e_all_t_o" value="{{$getIndexCardDataPCWise['electorData'][0]->total_o}}" readonly></td>
                                                <td><input class="form-control col_gtotal" type="number" min="0" name="e_all_t" value="{{$getIndexCardDataPCWise['electorData'][0]->total_t}}" readonly></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">Tendered Votes</th>
                                                <td colspan="2"><input class="form-control" type="number" min="0" value="0" name="tendered_votes" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">Proxy Votes</th>
                                                <td colspan="2"><input class="form-control" type="number" min="0" value="0" name="proxy_votes" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">Total #of polling Staion</th>
                                                <td colspan="2"><input class="form-control" type="number" min="0" value="{{$getIndexCardDataPCWise['electorData'][0]->polling_total}}" name="total_no_polling_station" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">Averages # of Electors/Polling Stations</th>
                                                <td colspan="2"><input class="form-control" type="text" min="0" value="{{number_format((float)$avg_elec_polling_stn, 2, '.', '')}}" name="avg_elec_polling_stn" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">Date Of Poll</th>
                                                <td colspan="2"><input class="form-control" id="datepicker" type="text" value="{{$getIndexCardDataPCWise['ScheduleID']->DATE_POLL}}" name="dt_poll" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">Date Of Counting</th>
                                                <td colspan="2"><input class="form-control" id="datepicker" type="text" value="{{$getIndexCardDataPCWise['ScheduleID']->DATE_COUNT}}" name="dt_counting" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">Date Of Declaration Of result</th>
                                                <td colspan="2"><input class="form-control"  id="datepicker" type="text" value="{{$getIndexCardDataPCWise['ScheduleID']->DT_PRESS_ANNC}}" name="dt_declare" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">Whether this is Bye Election <br> or Countemented Election</th>
                                                <td class="dev" colspan="2">
                                                <select class="form-control" name="flag_bye_counter" id="">
                                                        <option value="1" <?php echo ($getIndexCardDataPCWise['electorData'][0]->flag_bye_counter==1)?'selected':''?>>Yes</option>
                                                        <option value="0" <?php echo ($getIndexCardDataPCWise['electorData'][0]->flag_bye_counter==0)?'selected':''?>>No</option>
                                                    </select></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">If Yes, Reason There Of</th>
                                                <td><textarea name="flag_bye_counter_reason" id="" cols="30" rows="3">{{$getIndexCardDataPCWise['electorData'][0]->flag_bye_counter_reason}}</textarea></td>
                                            </tr>
                                            <tr>
                                                <th style="background: #2c3b48 !important;text-align: center;" colspan="5">
                                                <input type="checkbox" class="toggle_check forRepolling" <?php echo isset($getIndexCardDataPCWise['electorData'][0]->repollData) ? 'checked'.' disabled':''; ?>> &nbsp;Select To Enter Polling Station Count</th>
                                            </tr>
                                            <tr>
                                                <th colspan="3">Enter Number Of Repolls</th>
                                                <td class="dev"><input class="form-control" type="number" min="0" value="<?php echo isset($getIndexCardDataPCWise['electorData'][0]->repollData) ? sizeof($getIndexCardDataPCWise['electorData'][0]->repollData):''; ?>" name="noRepoll" <?php echo isset($getIndexCardDataPCWise['electorData'][0]->repollData) ? 'readonly':''; ?>></td>
                                                <td> <a href="javascript:void(0);" class="btn btn-info forRepollingButton" <?php echo isset($getIndexCardDataPCWise['electorData'][0]->repollData) ? 'disabled':''; ?>> OK </a> </td>
                                            </tr>
                                            <?php $rpcount = 1; ?>
                                            @if(isset($getIndexCardDataPCWise['electorData'][0]->repollData))
                                            @foreach($getIndexCardDataPCWise['electorData'][0]->repollData as $repollData)
                                                <tr>
                                                    <th colspan="3">Date Of Repolls {{$rpcount}}</th>
                                                    <td colspan="2">
                                                        <input class="form-control" value="<?php echo date('Y-m-d', strtotime($repollData->dt_repoll));?>" name="dt_repoll[]" class="form-control">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th colspan="3">No. of Polling Station</th>
                                                    <td colspan="2">
                                                        <input class="form-control" type="number" min="0" value="{{$repollData->no_repoll}}" name="no_repoll[]" class="form-control">
                                                    </td>
                                                </tr>
                                                <?php $rpcount++; ?>
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    </div>
                             
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12 tab_card">
                                <h2>Part -B </h2>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Candidate:</th>
                                                <th>Male</th>
                                                <th>Female</th>
                                                <th>Third Gender</th>
                                                <th>Total</th>
                                            </tr>
                                            
                                            @foreach($getIndexCardDataPCWise['indexCardData'] as $nominatedData)
                                            
                                            @if($nominatedData->status == 'nominated')
                                            <tr>
                                                <td> 1. Nominated </td>
                                                <td><input class="form-control 1row_sum1" type="number" min="0" name="c_nom_m_t" value="{{$nominatedData->male}}"></td>
                                                <td><input class="form-control 1row_sum1" type="number" min="0" name="c_nom_f_t" value="{{$nominatedData->female}}"></td>
                                                <td><input class="form-control 1row_sum1" type="number" min="0" name="c_nom_o_t" value="{{$nominatedData->third}}"></td>
                                                <td><input class="form-control" id="1row_sum1_total" type="number" min="0" name="c_nom_a_t" value="{{$nominatedData->total}}" readonly></td>
                                            </tr>
                                            @endif
                                            @if($nominatedData->status == 'rejected')
                                            <tr>
                                                <td>2. Nominated <br> Rejected</td>
                                                <td><input class="form-control 1row_sum2" type="number" min="0" name="c_nom_r_m" value="{{$nominatedData->male}}"></td>
                                                <td><input class="form-control 1row_sum2" type="number" min="0" name="c_nom_r_m" value="{{$nominatedData->female}}"></td>
                                                <td><input class="form-control 1row_sum2" type="number" min="0" name="c_nom_r_o" value="{{$nominatedData->third}}"></td>
                                                <td><input class="form-control"  id="1row_sum2_total" type="number" min="0" name="c_nom_r_a" value="{{$nominatedData->total}}" readonly></td>
                                            </tr>
                                            @endif
                                            @if($nominatedData->status == 'withdrawn')
                                            
                                            <tr>
                                                <td>3. Withdrawn</td>
                                                <td><input class="form-control 1row_sum3" type="number" min="0" name="c_nom_w_m" value="{{$nominatedData->male}}"></td>
                                                <td><input class="form-control 1row_sum3" type="number" min="0" name="c_nom_w_f" value="{{$nominatedData->female}}"></td>
                                                <td><input class="form-control 1row_sum3" type="number" min="0" name="c_nom_w_o" value="{{$nominatedData->third}}"></td>
                                                <td><input class="form-control" id="1row_sum3_total" type="number" min="0" name="c_nom_w_t" value="{{$nominatedData->total}}" readonly></td>
                                            </tr>
                                            
                                            @endif
                                            @if($nominatedData->status == 'accepted')
                                            <tr>
                                                <td> 4. Contesting </td>
                                                <td><input class="form-control 1row_sum4" type="number" min="0" name="c_nom_co_m" value="{{$nominatedData->male}}"></td>
                                                <td><input class="form-control 1row_sum4" type="number" min="0" name="c_nom_co_f" value="{{$nominatedData->female}}"></td>
                                                <td><input class="form-control 1row_sum4" type="number" min="0" name="c_nom_co_o" value="{{$nominatedData->third}}"></td>
                                                <td><input class="form-control" id="1row_sum4_total" type="number" min="0" name="c_nom_co_t" value="{{$nominatedData->total}}" readonly></td>
                                            </tr>
                                            @endif
                                            @if($nominatedData->status == 'forfieted')
                                           <tr>
                                               <td>5. Forfeited <br> Deposits </td>
                                               <td><input class="form-control 1row_sum5" type="number" min="0" name="c_nom_fd_m" value="{{$nominatedData->male}}"></td>
                                               <td><input class="form-control 1row_sum5" type="number" min="0" name="c_nom_fd_f" value="{{$nominatedData->female}}"></td>
                                               <td><input class="form-control 1row_sum5" type="number" min="0" name="c_nom_fd_o" value="{{$nominatedData->third}}"></td>
                                               <td><input class="form-control" id="1row_sum5_total" type="number" min="0" name="c_nom_fd_t" value="{{$nominatedData->total}}" readonly></td>
                                           </tr>
                                            @endif
                                            @endforeach                                     
                                            
                                            <tr>
                                                <th style="background: #2c3b48 !important;text-align: center;" colspan="5">A. Details Of Vote Polled On EVM</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th>Male</th>
                                                <th>Female</th>
                                                <th>Third Gender</th>
                                                <th>Total</th>
                                            </tr>
                                            <tr>
                                                <td>1. NRI</td>
                                                <td><input class="form-control 2row_sum1 2col_sum1" type="number" min="0" name="vt_gen_m" value="0"></td>
                                                <td><input class="form-control 2row_sum1 2col_sum2" type="number" min="0" name="vt_gen_f" value="0"></td>
                                                <td><input class="form-control 2row_sum1 2col_sum3" type="number" min="0" name="vt_gen_o" value="0"></td>
                                                <td><input class="form-control" id="2row_sum1_total" type="number" min="0" name="vt_gen_t" value="0" readonly></td>
                                            </tr>
                                            <tr>
                                                <td> 2. Other Than <br> NRI </td>
                                                <td><input class="form-control 2row_sum2 2col_sum1" type="number" min="0" name="vt_nri_m" value="0"></td>
                                                <td><input class="form-control 2row_sum2 2col_sum2" type="number" min="0" name="vt_nri_f" value="0"></td>
                                                <td><input class="form-control 2row_sum2 2col_sum3" type="number" min="0" name="vt_nri_o" value="0"></td>
                                                <td><input class="form-control" id="2row_sum2_total" type="number" min="0" name="vt_nri_t" value="0" readonly></td>
                                            </tr>
                                            <tr>
                                                <td>3. Total<b>(1+2)</b></td>
                                                <td><input class="form-control 2col_sum1_total" type="number" min="0" name="vt_m_t" value="0" readonly></td>
                                                <td><input class="form-control 2col_sum2_total" type="number" min="0" name="vt_f_t" value="0" readonly></td>
                                                <td><input class="form-control 2col_sum3_total" type="number" min="0" name="vt_o_t" value="0" readonly></td>
                                                <td><input class="form-control 2col_gtotal" type="number" min="0" name="vt_all_t" value="0" readonly></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">4. Test Voted under 49 MA</th>
                                                <td colspan="2"><input class="form-control" type="number" min="0" value="0" name="mock_poll_evm" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">5. Rejected Voted due to other Reason</th>
                                                <td colspan="2"><input class="form-control" type="number" min="0" value="0" name="r_votes_evm" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">6. Votes Not Retrived From EVM</th>
                                                <td colspan="2"><input class="form-control" type="number" min="0" value="0" name="not_retrieved_vote_evm" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">7. Total Valid Votes Counted From EVM <br> <span style="color: #0a2467;margin-left: 23px;"> 3-(4+5+6+NOTA EVM Votes)</span></th>
                                                <td colspan="2"><input class="form-control" type="number" min="0" value="0" name="v_votes_evm_all" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="5" style="background: #2c3b48 !important;text-align: center;">B. Details Of Postal Votes</th>
                                            </tr>
                                            <tr>
                                                <th colspan="3">1. Postal Votes Counted <br> <span style="font-style: italic;font-size: 14px;color: #0a2467;font-size: 12px;">(For service Voter Under <br> Section 8a of Section 20)</span></th>
                                                <td colspan="2"><input class="form-control" type="number" min="0" value="0" name="postal_vote_ser_u" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">2. Postal Votes Counted <br> <span style="font-style: italic;font-size: 14px;color: #0a2467;font-size: 12px;">(For Govt Servants on Election Duty) including <br> all Police Personal , Driver, Cleaner</th>
                                                <td colspan="2"><input class="form-control" type="number" min="0" value="0" name="postal_vote_ser_o" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">3. Postal Votes Counted</th>
                                                <td colspan="2"><input class="form-control" type="number" min="0" value="0" name="postal_vote_ser_o" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">4. Postal Votes Rejected</th>
                                                <td class="dev" colspan="2"><input type="number" min="0" class="form-control" value="0" name="postal_vote_rejected"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">5. Total Valid Postal Votes <br><span style="color: #0a2467;margin-left: 23px;font-style: italic; "> 3-(4+NOTA Postal Votes) </span> </th>
                                                <td colspan="2"><input class="form-control" type="number" min="0" value="0" name="postal_valid_votes"></td>
                                            </tr>
                                            <tr>
                                                <th style="background: #2c3b48 !important;text-align: center;" colspan="5">C. Combined Details Of Postal and EVM Votes</th>
                                            </tr>
                                            <tr>
                                                <th colspan="3">1. Total Voter <br> <span style="color: #0a2467;font-style: italic; margin-left: 23px;"> A.3-B.1-A4</span></th>
                                                <td colspan="2" class="dev"><input class="form-control" type="number" min="0" value="0" name="total_votes_polled"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">2. Total Votes Not Retrived, Rejected and NOTA Votes</th>
                                                <td colspan="2"><input class="form-control" type="number" min="0" value="0" name="total_not_count_votes" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">3. Total Valid Voted Polled <br> <span style="color: #0a2467;font-style: italic; margin-left: 23px;"> 1-2</span></th>
                                                <td colspan="2"><input class="form-control" type="number" min="0" value="0" name="total_valid_votes" class="form-control"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <div class="col-sm-12">
                                        <input type="checkbox" name="isfinalised" class="isfinalisedCheck">Finalise Index Card
                                        <div style="float: right;" class="col-sm-1">
                                            <button type="submit" class="btn btn-info updatepcwisedata">Submit</button>
                                        </div>
                                    </div>
                                    
                                    
                                    
                                </form>
                            </div>
                        </div> 

            </div>

            <div id="menu2" class="tab-pane fade">
                <div class="table-responsive">
                    <table class="table table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th>Canditate
                                    <br> SL. No.</th>
                                <th>Name</th>
                                <th>Party Name</th>
                                <th>Party Symbol</th>
                                <th>Sex</th>
                                <th>Age</th>
                                <th>Category</th>
                                <th colspan="{{$getIndexCardDataCandidatesVotesACWise['colspanACs']}}">Valid Votes counted From Electronic Voting Machines </th>
                                <th>Total Postal Votes</th>
                                <th>Total Valid Votes</th>
                            </tr>

                            <tr color="acnamerow">
                                    <th colspan="7"></th>
                                @foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)
                                    <th color="{{$allACListsKey}}">{{$allACListsKey}} : {{$allACListsValue}}</th>
                                @endforeach
                                    <th colspan="2"></th>
                            </tr>
                        </thead>
                        <tbody color="CandidateBodyIDWise">
                            <?php $count=1; ?>
                            @foreach($getIndexCardDataCandidatesVotesACWise['dataArrayCandidate'] as $candKey => $candValue)
                                <form method="POST" action="updateCandiateAcWise">
                                @csrf
                                <input type="hidden" name="st_code" value="{{$st_code}}">
                                <input type="hidden" name="pc" value="{{$pc}}">
                                <tr color="CandidateRowIDWise" data-candid="{{$candValue['candidate_id']}}">
                                    <input type="hidden" name="candid[{{$candValue['candidate_id']}}][candidate_id]" value="{{$candValue['candidate_id']}}">
                                    <td>{{$count."."}} </td>
                                    <td color="CandName">{{$candValue['candidate_name']}}</td>
                                    <input type="hidden" name="candid[{{$candValue['candidate_id']}}][candidate_name]" value="{{$candValue['candidate_name']}}">
                                    <td color="party">
                                        <select name="candid[{{$candValue['candidate_id']}}][party]" id="partyList{{$candValue['candidate_id']}}" class="form-control">
                                            <option value="">Select Party</option>
                                            @foreach($getIndexCardDataCandidatesVotesACWise['allPartyArray'] as $allPartyKey => $allPartyValue)
                                                <option value="{{$allPartyKey}}" <?php echo ($allPartyKey==$candValue['party_abbre'])?'selected':'' ?>>{{$allPartyValue}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="candid[{{$candValue['candidate_id']}}][symbol]" id="symbolList{{$candValue['candidate_id']}}'" class="form-control">
                                        <option value="">Select Party Symbol</option>
                                            @foreach($getIndexCardDataCandidatesVotesACWise['allSymbolArray'] as $allSymbolKey =>$allSymbolValue)
                                                <option value="{{$allSymbolKey}}" <?php echo ($allSymbolKey==$candValue['symb_no'])?'selected':'' ?>>{{$allSymbolValue}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="candid[{{$candValue['candidate_id']}}][gender]" id="gender{{$candValue['candidate_id']}}'" class="form-control">
                                            <option value="male" <?php echo ($candValue['cand_gender']=='male')?'selected':'' ?>>Male</option>
                                            <option value="female" <?php echo ($candValue['cand_gender']=='female')?'selected':'' ?>>Female</option>
                                            <option value="other" <?php echo ($candValue['cand_gender']=='other')?'selected':'' ?>>Third Gender</option>
                                        </select>
                                    </td>
                                    <td color="candid[{{$candValue['candidate_id']}}][cand_age]">
                                    {{$candValue['cand_age']}}</td>
                                    <input type="hidden" name="candid[{{$candValue['candidate_id']}}][cand_age]" value="{{$candValue['cand_age']}}">
                                    <td color="candid[{{$candValue['candidate_id']}}][cand_category]">
                                    {{$candValue['cand_category']}}</td>
                                    <input type="hidden" name="candid[{{$candValue['candidate_id']}}][cand_category]" value="{{$candValue['cand_category']}}">
                                    <?php $totalValidVotesCandidateWise = 0; ?>
                                    @foreach($candValue['votescountacwise'] as $kkk => $vvv)
                                    <td contenteditable="true" color="candid[{{$candValue['candidate_id']}}][acwisevote][{{$kkk}}]" data-acid="{{$kkk}}" data-canid="{{$candValue['candidate_id']}}">{{$vvv}}</td>
                                    <input type="hidden" name="candid[{{$candValue['candidate_id']}}][acwisevote][{{$kkk}}]" value="{{$vvv}}"  class="subtotal{{$candValue['candidate_id']}}">
                                    <?php $totalValidVotesCandidateWise += $vvv; ?>
                                    @endforeach
                                    <td contenteditable="true" color="candid[{{$candValue['candidate_id']}}][postalVotes]">{{$candValue['postaltotalvote']}}</td>
                                    <input type="hidden" name="candid[{{$candValue['candidate_id']}}][postalVotes]" value="{{$candValue['postaltotalvote']}}">
                                    <td color="candid[{{$candValue['candidate_id']}}][totalValidVotes]">{{$totalValidVotesCandidateWise}}</td>
                                    <input type="hidden" name="candid[{{$candValue['candidate_id']}}][totalValidVotes]" value="{{$totalValidVotesCandidateWise}}" class="total{{$candValue['candidate_id']}}">
                                </tr>
                            <?php $count++; ?>

                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12">
                    <div style="float: right;" class="col-sm-1">
                        <button type="submit" class="btn btn-info updatepcwisedata" id="saveCandidateWise">Submit</button>
                    </div>
                </div>
            </form>
            </div>

            <div id="menu3" class="tab-pane fade">
                <table class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>AC</th>
                            <th>AC Name</th>
                            <th>Gen Male</th>
                            <th>Gen Female</th>
                            <th>Gen Third Gender</th>
                            <th class="highlit">Total</th>
                            <th>Serv Male </th>
                            <th>Serv Female</th>
                            <th>Serv Total</th>
                            <th>NRI Male</th>
                            <th>NRI Female</th>
                            <th>NRI Third Gender</th>
                            <th>NRI Total</th>
                            <th class="highlit">Total Male</th>
                            <th class="highlit">Total Female</th>
                            <th class="highlit">Total Third Gender</th>
                            <th class="highlit">Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        <form method="POST" action="updateDataForElectionAcWise">
                            @csrf
                            <?php

                                        foreach ($getelectorsacwise as $k => $value) {

                                             //{{$value['ac_name']}}
                                              //echo "<pre>"; print_r($session); die;
                                             ?>

                                <tr data-acid="{{$k}}">
                                    <td color="electorsac[{{$k}}][ac_no]">
                                        {{$value['ac_no']}}
                                    </td>
                                    <input type="hidden" class="" name="st_code" value="{{$value['st_code']}}">
                                    <input type="hidden" class="" name="pc_no" value="{{$value['pc_no']}}">
                                    <input type="hidden" class="" name="ScheduleID" value="{{$session['election_detail']['ScheduleID']}}">
                                    <input type="hidden" class="" name="electorsac[{{$k}}][ac_no]" value="{{$value['ac_no']}}">
                                    <td color="electorsac[{{$k}}]['ac_name']">
                                        {{$value['ac_name']}}
                                    </td>
                                    <input type="hidden" class="" name="electorsac[{{$k}}][ac_name]" value="{{$value['ac_name']}}">
                                    <!-- /////////////////////////Male/////////////////////// -->
                                    <td contenteditable="true" color="electorsac[{{$k}}][gen_m]" data-category="general" class="male">
                                        {{$value['gen_m']}}
                                    </td>
                                    <input type="hidden" name="electorsac[{{$k}}][gen_m]" value="{{$value['gen_m']}}" class="general{{$k}}">

                                    <td contenteditable="true" color="electorsac[{{$k}}][gen_f]" data-category="general" class="female">
                                        {{$value['gen_f']}}
                                    </td>

                                    <input type="hidden" name="electorsac[{{$k}}][gen_f]" value="{{$value['gen_f']}}" class="general{{$k}}">

                                    <td contenteditable="true" color="electorsac[{{$k}}][gen_o]" data-category="general" class="other">
                                        {{$value['gen_o']}}
                                    </td>
                                    <input type="hidden" name="electorsac[{{$k}}][gen_o]" value="{{$value['gen_o']}}" class="general{{$k}}">

                                    <td color="electorsac[{{$k}}][gen_t]" class="generalTotal{{$k}}" data-category="general">
                                        {{$value['gen_t']}}
                                    </td>
                                    <input type="hidden" name="electorsac[{{$k}}][gen_t]" id="generalTotal{{$k}}" value="{{$value['gen_t']}}">

                                    <!-- ////////////////////////////////////////////////////////// -->

                                    <td contenteditable="true" color="electorsac[{{$k}}][ser_m]" data-category="service" class="male">
                                        {{$value['ser_m']}}
                                    </td>
                                    <input type="hidden" name="electorsac[{{$k}}][ser_m]" class="service{{$k}}" value="{{$value['ser_m']}}">

                                    <td contenteditable="true" color="electorsac[{{$k}}][ser_f]" data-category="service" class="female">
                                        {{$value['ser_f']}}
                                    </td>
                                    <input type="hidden" name="electorsac[{{$k}}][ser_f]" class="service{{$k}}" value="{{$value['ser_f']}}">

                                    <td color="electorsac[{{$k}}][ser_t]" data-category="service" class="serviceTotal{{$k}}">
                                        {{$value['ser_t']}}
                                    </td>
                                    <input type="hidden" name="electorsac[{{$k}}][ser_t]" id="serviceTotal{{$k}}" value="{{$value['ser_t']}}">
                                    <!-- /////////////////////////////////////NRI//////////////////////////// -->

                                    <td contenteditable="true" color="electorsac[{{$k}}][nri_m]" data-category="nri" class="male">
                                        {{$value['nri_m']}}
                                    </td>
                                    <input type="hidden" class="nri{{$k}}" name="electorsac[{{$k}}][nri_m]" value="{{$value['nri_m']}}">

                                    <td contenteditable="true" color="electorsac[{{$k}}][nri_f]" data-category="nri" class="female">
                                        {{$value['nri_f']}}
                                    </td>
                                    <input type="hidden" class="nri{{$k}}" name="electorsac[{{$k}}][nri_f]" value="{{$value['nri_f']}}">

                                    <td contenteditable="true" color="electorsac[{{$k}}][nri_o]" data-category="nri" class="other">
                                        {{$value['nri_o']}}
                                    </td>
                                    <input type="hidden" class="nri{{$k}}" name="electorsac[{{$k}}][nri_o]" value="{{$value['nri_o']}}" >

                                    <b><td color="electorsac[{{$k}}][nri_t]" data-category="nri" class="nriTotal{{$k}} highlit">
                                        {{$value['nri_t']}} 
                                    </td></b>
                                    <input type="hidden" id="nriTotal{{$k}}" name="electorsac[{{$k}}][nri_t]" value="{{$value['nri_t']}}">
                                    <!-- //////////////////////////////////////////////////// -->
                </b>
                                    <td color="electorsac[{{$k}}][tot_m]" class="total-male subtotal highlit">
                                        {{$value['tot_m']}}
                                    </td></b>
                                    <input type="hidden" id="maletotal" name="electorsac[{{$k}}][tot_m]" value="{{$value['tot_m']}}">

                                    <td color="electorsac[{{$k}}][tot_f]" class="total-female subtotal highlit">
                                        {{$value['tot_f']}}
                                    </td>
                                    <input type="hidden" id="femaletotal" name="electorsac[{{$k}}][tot_f]" value="{{$value['tot_f']}}">

                                    <td color="electorsac[{{$k}}][tot_o]" class="total-other subtotal highlit">
                                        {{$value['tot_o']}}
                                    </td>
                                    <input type="hidden" id="totalother" name="electorsac[{{$k}}][tot_o]" value="{{$value['tot_o']}}">

                                    <td color="" class="grandtotal highlit">

                                    </td>
                                    <input type="hidden" class="inputgrandtotal" name="electorsac[{{$k}}][tot_all]" value="">

                                </tr>

                                <?php  } ?>
                    </tbody>

                </table>
                <div class="col-sm-12">
                    <div style="float: right;" class="col-sm-1">
                        <button type="submit" class="btn btn-info updatepcwisedata" id="savedataforelectionacwise">Submit</button>
                    </div>
                </div>
                </form>
                </div>

            </div>
        </div>

        <!--tabs ends-->
<div class="row">
    <div class="col-sm-10 col-offset-sm-1 pull-right">
        
    </div>
</div>
    </div>

</div>
<!-- End Wrapper-->
<!-- Modal for finalised -->
<div class="modal fade finalisedModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <p>Cannot Edit. The index card has been finalised!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Modal for finalised -->
<!-- Modal for finalised Cheack -->
<div class="modal fade finalisedModalCheck" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Alert</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to finalise index card for this PC!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default finalisedModalCheckFalse" data-dismiss="modal" value="0">Disagree</button>
        <button type="button" class="btn btn-info finalisedModalCheckTrue" data-dismiss="modal" value="1">Agree</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Modal for finalised -->
@endsection
@push('scripts')
<?php if(isset($getIndexCardDataPCWise['electorData'][0]->isfinalised)){
    if($getIndexCardDataPCWise['electorData'][0]->isfinalised == 1){ ?>
        <script>
            $(document).ready(function(){
                $('input').attr('readonly','readonly');
                $('select').attr('disabled','disabled');
                $('textarea').attr('disabled','disabled');
                $('.finalisedModal').modal('show');
                $('.updatepcwisedata').attr('disabled','disabled');
                $('td').removeAttr('contenteditable');
                $('.isfinalisedCheck').attr('checked','checked');
                $('.isfinalisedCheck').attr('disabled','disabled');
            })
        </script>
    <?php }
} ?>
<script type="text/javascript">
    /***********For Repoll by Mohd Saquib Siddiqui******************/
    $(document).on('click','.finalisedModalCheckTrue', function(){
        var agree = $(this).val();
        if(agree == '1'){
            $('.isfinalisedCheck').attr('checked','checked');
        }else{
            $('.isfinalisedCheck').removeAttr('checked');
        }
        $('.finalisedModalCheck').modal('hide');
    })
    $(document).on('click','.isfinalisedCheck',function(){
        var check = $(this).prop('checked');
        if(check){
            $('.finalisedModalCheck').modal({
                show: true,
                keyboard: false,
                backdrop: 'static'
            });
        }else{
            $('.isfinalisedFlag').removeAttr('checked');
        }
    })
    $(document).on('click','.forRepolling', function(){
        var check = $(this).prop('checked');
        if(!check){
            $('input[name="noRepoll"]').val(0);
            $('input[name="noRepoll"]').attr('disabled','disabled');
            $('.forRepollingButton').attr('disabled','disabled');
            $('.dynamicRepoll').remove();
        }else{
            $('input[name="noRepoll"]').removeAttr('disabled');
            $('.forRepollingButton').removeAttr('disabled');
        }
    });
    $(document).on('click','.forRepollingButton',function(){
        var count = parseInt($.trim($('input[name="noRepoll"]').val()));
        for(var i = 1; i<= count; i++){
            var appendRow = '<tr class="dynamicRepoll"> <th colspan="3">Date Of Repolls '+i+'</th> <td colspan="2"><input type="date" class="form-control" value="" name="dt_repoll[]" class="form-control"></td></tr><tr  class="dynamicRepoll"> <th colspan="3">No. of Polling Station</th> <td colspan="2"><input class="form-control" type="number" min="0" value="0" name="no_repoll[]" class="form-control"></td></tr>';
            $(this).parent().parent().parent().append(appendRow);
        }
    });
    /***********For Repoll by Mohd Saquib Siddiqui******************/
    /**************Praveen's Code****************/
    $(document).ready(function(){
   $('td[contenteditable="true"]').trigger('focusout');
});
// we used jQuery 'keyup' to trigger the computation as the user type
$(document).on('focusout', 'td[contenteditable="true"]',function(){

   //alert('sdfds');
   var key = $(this).parent().data('acid');
   // console.log(key);
   var sumOf = $(this).data('category');
   var loop = $('.'+sumOf+key);
   var sum = 0;
   $.each(loop, function(){
       sum += parseInt($(this).val());
   })
       $("."+sumOf+"Total"+key).text(sum);
       $("#"+sumOf+"Total"+key).val(sum);


    $('tr').each(function () {
        var sumcat = 0;
        $(this).find('.male').each(function () {
            var male = $(this).text();
            if (!isNaN(male) && male.length !== 0) {
                sumcat += parseFloat(male);
            }
        });
         $(this).find('.total-male').html(sumcat);

    });

   // var total_male = sumcat;

     $('tr').each(function () {
       var sumcat = 0;
        $(this).find('.female').each(function () {
            var female = $(this).text();
            if (!isNaN(female) && female.length !== 0) {
                sumcat += parseFloat(female);
            }
        });

        $(this).find('.total-female').html(sumcat);
    });

      $('tr').each(function () {
        var sumcat = 0;
        $(this).find('.other').each(function () {
            var other = $(this).text();
            if (!isNaN(other) && other.length !== 0) {
                sumcat += parseFloat(other);
            }
        });

        $(this).find('.total-other').html(sumcat);

    });

       $('tr').each(function () {
        var sumtotal = 0;
        $(this).find('.subtotal').each(function () {
            var subtotal = $(this).text();
            if (!isNaN(subtotal) && subtotal.length !== 0) {
                sumtotal += parseFloat(subtotal);
            }
        });
         $(this).find('.grandtotal').html(sumtotal);
         $(this).find('.inputgrandtotal').val(sumtotal);

    });



});
    /**************Praveen's Code****************/
    $(document).on('blur','td[contenteditable="true"]',function(){
        var id = $(this).attr('color');
        // console.log(id)
        var text = $.trim($(this).html());
        if(text!=''){
            $('input[name="'+id+'"]').val(text);
        }else{
            $(this).html(0);
            $('input[name="'+id+'"]').val(0);
        }
    });
    $(document).on('blur','td[contenteditable="true"]',function(){
        var candid = $(this).data('canid');
        if(candid!=undefined){
            var sum = 0;
            var cl = 'subtotal'+candid;
            var loop = $('.'+cl);
            $.each(loop,function(){
                sum += parseInt($.trim($(this).val()));
            });
            
            $('.total'+candid).val(sum);
            $('td[color="candid['+candid+'][totalValidVotes]"').text(sum);
        }
    });
</script>





<script>
   /* 
     $(document).ready(function () {
         $('.row_sum').on('keypress',function(){
             var sum = 0;
         

             $('td', $(this).parent('tr')).find('.row_sum').each(function() {

    // $(this).parent('tr').find('.row_sum').each(function () {
              alert(sum); 
         var row_value = $(this).val();
         if (!isNaN(row_value) && row_value.length !== 0) {
             sum += parseFloat(row_value);
         }
     });
         
        $('.row_total').val(sum);
         });

    
 });
*/


$("input").on( "keyup", function () {
    var sClass = $(this).prop("class");
   // alert(sClass);

      strClass = sClass.split(' ')[1];      
     var stdClass = sClass.split(' ')[2];      
//  alert(sClass.split(' ').length);        
    var sum = colsum = 0;

    $('.'+strClass).each(function(){

     sum += parseFloat($(this).val());  // Or this.innerHTML, this.innerText
});
       //alert(sum);

 $("#"+ strClass+"_total").val(sum);


if(sClass.split(' ').length>2){
    $('.'+stdClass).each(function(){

         colsum += parseFloat($(this).val());  // Or this.innerHTML, this.innerText
    });

 $("."+ stdClass+"_total").val(colsum);

        $(".col_gtotal").val(parseFloat($("#row_sum1_total").val())+parseFloat($("#row_sum2_total").val())+parseFloat($("#row_sum3_total").val()));

  $(".2col_gtotal").val(parseFloat($("#2row_sum1_total").val())+parseFloat($("#2row_sum2_total").val()));

}
       
   
       

       
});



</script>


@endpush