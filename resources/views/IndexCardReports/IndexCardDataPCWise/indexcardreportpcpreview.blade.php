@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'The Schedule of GE to Lok Sabha 2019 - Phase General Elections')

@section('content')




<style>
#index_cus_ch ul li.active {
    background: #17a2b8;
}

.card-header h3 {
    font-size: 18px;
}

#index_cus_ch ul li {
    background: #4b4a4a;
    padding: 7px;
    margin-right: 2px;
}

#index_cus_ch ul.nav.nav-tabs {
    margin-bottom: 11px;
}
.card-header span {
    font-size: 13px;
}

table {
    font-size: .9em;
}


#index_cus_ch ul li:hover {
    background: #F0587E;
}

#index_cus_ch ul li:hover a {
color: #fff;
text-decoration: none;
}

#index_cus_ch ul li a {
color: #fff;
font-size: 14px;
}

select#partyList764 {
    width: 200px;
}

#menu2 select{
width: 148px;    
}


#index_cus_ch th {
    background: #F0587E;
    color: #fff;
    font-size: 14px;
    font-weight: 500;
}

#index_cus_ch td{
        font-size: 13px;
        vertical-align: middle;

}

.tab-content{
    width: 100%;
}
input.form-control{
        font-size: 13px;

}
#index_cus_ch ul li.active a {
    text-decoration: none;
    color: #fff;
}


</style>
<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h3> Index Card Parliamentary - {{$getIndexCardDataPCWise['election_detail']['YEAR']}}</h3></div> 
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
							</p>
						</div>
						
						
						
					</div>
					
					
					
					<div class="row" style="margin-top:2%;">
					
					<div class="col">
							<p class="mb-0 text-left"><b class="bolt">Type of Constituency:</b> <span >{{$getIndexCardDataPCWise['pcType']->PC_TYPE}}</span> &nbsp;&nbsp; 
							</p>
						</div>
						
						<div class="col">
							<p class="mb-0 text-center"><b class="bolt">Number & Name of PC:</b> <span >{{$getIndexCardDataPCWise['pcType']->PC_NO}} : {{$getIndexCardDataPCWise['pcType']->PC_NAME}} 
							</p>
						</div>
						
						<div class="col col" style="margin-right:20px;">
							<p class="mb-0 text-right"><b class="bolt">District :</b> <span >{{$getIndexCardDataPCWise['distict_name']}} 
							</p>
						</div>




        </div>
					
					
				</div>
				
				<div class="card-body">

<div class="wapper">
    <div class="grids"> 
    <div class="whole">
    

<?php //echo "<pre>"; print_r($getIndexCardDataPCWise); die; ?>

    <div class="container-fluid">
        









    

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
    <!--Start row-->
<div class="row" style="text-align: right;">
        <button type="button" class="btn btn-primary" style="position: absolute; right: 35px;">Print Preview</button>
</div>

      <div class="row" id="index_cus_ch">
        <!--tabs starts-->

        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a data-toggle="tab" href="#menu1">Data For Election Card Index</a></li>
            <li role="presentation"><a data-toggle="tab" href="#menu2">Information About Candidate in PC</a></li>
            <li role="presentation"><a data-toggle="tab" href="#menu3">Data For Election AC Wise</a></li>
            <li><a href="changeRequest">Edit Request</a></li>
            <li><a href="finaliserequest">Finalise Request</a></li>
        </ul>
		

       

        <div class="tab-content" style="overflow: auto;">

            <div id="menu1" role="tabpanel" class="tab-pane fade in active">
                <!--  <h3>Data For Election</h3> -->


                <?php
                     // echo "<pre>"; print_r($getIndexCardDataPCWise['t_pc_ic']); die;

                    if($getIndexCardDataPCWise['electorData'][0]->polling_total > 0)
                    {
                        
                    $avg_elec_polling_stn = $getIndexCardDataPCWise['electorData'][0]->total_t/$getIndexCardDataPCWise['electorData'][0]->polling_total;

                    }else{
                        $avg_elec_polling_stn =0;
                    } ?>
                        <div class="col-sm-12">
                            <div class="col-md-12 col-sm-12 col-xs-12 tab_card">
                                <!-- <h2>Part -A </h2> -->
                                <form action="updatepcwisedata" class="form-horizontal updatepcwisedata" method="post" enctype="multipart/form-data">
                                
                              <!--  @csrf
                                <input type="hidden" name="st_code" value="{{$getIndexCardDataPCWise['election_detail']['st_code']}}">
                                <input type="hidden" name="pc_no" value="{{$getIndexCardDataPCWise['pcType']->PC_NO}}">
                                <input type="hidden" name="schedule_id" value="{{$getIndexCardDataPCWise['election_detail']['ScheduleID']}}">
                                -->
                                    <div class="table-responsive">
                                    <table class="table table-bordered tableindexcard" style="width: 100%;table-layout: fixed;">



                                             <tr>
                                                <th>Candidates:</th>
                                                <th>Male</th>
                                                <th>Female</th>
                                                <th>Third Gender</th>
                                                <th>Total</th>
                                            </tr>
                                            
                                            @foreach($getIndexCardDataPCWise['indexCardData'] as $nominatedData)
                                            
                                            @if($nominatedData->status == 'nominated')
                                            <tr>
                                                <td> 1. Nominated </td>
                                                <td><input class="form-control 1row_sum1" type="number" min="0" name="c_nom_m_t" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_m_t}}"></td>
                                                <td><input class="form-control 1row_sum1" type="number" min="0" name="c_nom_f_t" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_f_t}}"></td>
                                                <td><input class="form-control 1row_sum1" type="number" min="0" name="c_nom_o_t" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_o_t}}"></td>
                                                <td><input class="form-control" id="1row_sum1_total" type="number" min="0" name="c_nom_a_t" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_a_t}}" readonly></td>
                                            </tr>
                                            @endif
                                            @if($nominatedData->status == 'rejected')
                                            <tr>
                                                <td>2. Nominations  Rejected</td>
                                                <td><input class="form-control 1row_sum2" type="number" min="0" name="c_nom_r_m" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_m}}"></td>
                                                <td><input class="form-control 1row_sum2" type="number" min="0" name="c_nom_r_f" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_f}}"></td>
                                                <td><input class="form-control 1row_sum2" type="number" min="0" name="c_nom_r_o" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_o}}"></td>
                                                <td><input class="form-control"  id="1row_sum2_total" type="number" min="0" name="c_nom_r_a" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_a}}" readonly></td>
                                            </tr>
                                            @endif
                                            @if($nominatedData->status == 'withdrawn')
                                            
                                            <tr>
                                                <td>3. Withdrawn</td>
                                                <td><input class="form-control 1row_sum3" type="number" min="0" name="c_nom_w_m" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_m}}"></td>
                                                <td><input class="form-control 1row_sum3" type="number" min="0" name="c_nom_w_f" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_f}}"></td>
                                                <td><input class="form-control 1row_sum3" type="number" min="0" name="c_nom_w_o" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_o}}"></td>
                                                <td><input class="form-control" id="1row_sum3_total" type="number" min="0" name="c_nom_w_t" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_t}}" readonly></td>
                                            </tr>
                                            
                                            @endif
                                            @if($nominatedData->status == 'accepted')
                                            <tr>
                                                <td> 4. Contested </td>
                                                <td><input class="form-control 1row_sum4" type="number" min="0" name="c_nom_co_m" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_m}}"></td>
                                                <td><input class="form-control 1row_sum4" type="number" min="0" name="c_nom_co_f" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_f}}"></td>
                                                <td><input class="form-control 1row_sum4" type="number" min="0" name="c_nom_co_o" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_o}}"></td>
                                                <td><input class="form-control" id="1row_sum4_total" type="number" min="0" name="c_nom_co_t" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_t}}" readonly></td>
                                            </tr>
                                            @endif
                                            @if($nominatedData->status == 'forfieted')
                                           <tr>
                                               <td>5. Deposit Forfeited </td>
                                               <td><input class="form-control 1row_sum5" type="number" min="0" name="c_nom_fd_m" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_m}}"></td>
                                               <td><input class="form-control 1row_sum5" type="number" min="0" name="c_nom_fd_f" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_f}}"></td>
                                               <td><input class="form-control 1row_sum5" type="number" min="0" name="c_nom_fd_o" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_o}}"></td>
                                               <td><input class="form-control" id="1row_sum5_total" type="number" min="0" name="c_nom_fd_t" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_t}}" readonly></td>
                                           </tr>
                                            @endif
                                            @endforeach                   




                                            <tr>
                                                <th>Elector's</th>
                                                <th>Male</th>
                                                <th>Female</th>
                                                <th>Third Gender</th>
                                                <th>Total</th>
                                            </tr>
                                            
                                            <tr>
                                                <td> 1. General</td>
                                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_m }}</td>
                                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_f }}</td>
                                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_o }}</td>
                                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_t }}</td>
                                            </tr>
											
											<tr data-parent="e" data-category="nri">
                                                <td> 2. General (NRI)</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->e_nri_m}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->e_nri_f}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->e_nri_o}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->e_nri_t}}</td>
                                            </tr>
											
											
                                            <tr>
                                                <td> 3. Service</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->e_ser_m}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->e_ser_f}}</td>
                                                <td>NA</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->e_ser_t}}</td>
                                            </tr>
                                            <tr>
                                                <td> 4. Total </td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->e_all_t_m}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->e_all_t_f}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->e_all_t_o}}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->e_all_t}}</td>
                                            </tr>



                                              <tr>
                                                <th style="background: #2c3b48 !important;text-align: center;" colspan="5">Details Of Vote Polled On EVM</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th>Male</th>
                                                <th>Female</th>
                                                <th>Third Gender</th>
                                                <th>Total</th>
                                            </tr>
                                            
                                             <tr>
                                                <td> 1. Other Than  NRI </td>           
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_m }}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_f }}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_o }}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_t }}</td>

                                            </tr>
                                            
                                            <tr>
                                                <td>2. NRI</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_m }}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_f }}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_o }}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_t }}</td>
                                            </tr>
                                           
                                            <tr>
                                                <td>3. Total<b>(1+2)</b></td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_m +$getIndexCardDataPCWise['t_pc_ic']->vt_nri_m }}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_f + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_f }}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_o +$getIndexCardDataPCWise['t_pc_ic']->vt_nri_o }}</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_t + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_t }}</td>
                                            </tr>
                                            



                                        <tr>
                                                <th style="background: #2c3b48 !important;text-align: center;" colspan="5">A. Details of votes polled on EVM</th>
                                            </tr>
                                            <tr>
                                                <td colspan="4">1. Total Votes on EVM</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->t_votes_evm}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">2. Test Voted under 49 MA</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->mock_poll_evm}}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td colspan="4">3. Votes Not Retrived From EVM</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->not_retrieved_vote_evm}}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td colspan="4">4. Rejected Voted (due to other Reason)</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->r_votes_evm}}</td>
                                            </tr>
                                            
                                            
                                             <tr>
                                                <td colspan="4">5. Votes polled for 'NOTA' on EVM</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm}}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td colspan="4">6. Total of test votes + Not retrieved + Votes Rejects (Due to Other  Reason) + Nota [2+3+4+5]</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->v_r_evm_all}}</td>
                                            </tr>
                                            
                                            
                                            <tr>
                                                <td colspan="4">7. Total Valid Votes Counted From EVM <span style="color: #0a2467;margin-left: 23px;">[1-6]</span></td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->v_votes_evm_all}}</td>
                                            </tr>



                                      <tr>
                                                <th colspan="5" style="background: #2c3b48 !important;text-align: center;">B. Details Of Postal Votes</th>
                                            </tr>
                                            <tr>
                                                <td colspan="4">1. Postal Votes Counted  <span style="font-size: 14px;color: #0a2467;font-size: 14px;"> &nbsp; (For service Voter Under  Section 8a of Section 20)</span></td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_ser_u}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">2. Postal Votes Counted  <span style="font-size: 14px;color: #0a2467;font-size: 14px;"> &nbsp; (For Govt Servants on Election Duty) including all Police Personal , Driver, Cleaner</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_ser_o}}</td>
                                            </tr>
                                             <tr>
                                                <td colspan="4">3. Postal Votes Rejected</td>
                                                <td class="dev" colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_rejected}}</td>
                                            </tr>
                                            
                                            
                                            
                                            <tr>
                                                <td colspan="4">4. Postal Votes Polled for 'NOTA'</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota}}</td>
                                            </tr>
                                           
                                           
                                           <tr>
                                                <td colspan="4">5. Total of Postal Votes Rejected +Nota <span style="font-size: 16px;color: #0a2467;margin-left: 23px; "> 3+4 </span> </td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_r_nota}}</td>
                                            </tr>
                                           
                                           
                                            <tr>
                                                <td colspan="4">6. Total Valid postal votes <span style="color: #0a2467;margin-left: 23px; "> 1+2-5 </span> </td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_valid_votes}}</td>
                                            </tr>




          
                                             <tr>
                                                <th style="background: #2c3b48 !important;text-align: center;" colspan="5">C. Combined Details Of Postal and EVM Votes</th>
                                            </tr>
                                            <tr>
                                                <td colspan="4">1. Total Votes Polled<span style="color: #0a2467; margin-left: 23px;"> A.1+B.1</span></td>
                                                <td class="dev">{{$getIndexCardDataPCWise['t_pc_ic']->total_votes_polled}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">2. Total Votes Not Retrived, Rejected and NOTA Votes <span style="color: #0a2467; margin-left: 23px;"> A.6+B.5</span></td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->total_not_count_votes}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">3. Total Valid Votes <span style="color: #0a2467; margin-left: 23px;"> A.7+B.6</span></td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->total_valid_votes}}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td colspan="4">4. Total Votes Polled for NOTA <span style="color: #0a2467; margin-left: 23px;"> A.5+B.4</span></td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->total_votes_nota}}</td>
                                            </tr>

                     
            




											<tr>
                                                <th style="background: #2c3b48 !important;text-align: center;" colspan="5">Miscellaneous</th>
                                            </tr>
											
											<tr>
                                                <td colspan="4">Proxy Votes</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->proxy_votes}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">Tendered Votes</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->tendered_votes}}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td colspan="4">Total No of polling Staion</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->total_no_polling_station}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">Averages of Electors/Polling Stations</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->avg_elec_polling_stn}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">Date Of Poll</td>
                                                <td colspan="1">NA</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">Date Of Counting</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->dt_counting}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">Date Of Declaration Of result</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->dt_declare}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">Whether this is Bye Election <br> or Countemented Election</td>
                                                <td class="dev" colspan="1">
                                                <select class="form-control" name="flag_bye_counter" id="">
                                                        <option value="1" <?php echo ($getIndexCardDataPCWise['electorData'][0]->flag_bye_counter==1)?'selected':''?>>Yes</option>
                                                        <option value="0" <?php echo ($getIndexCardDataPCWise['electorData'][0]->flag_bye_counter==0)?'selected':''?>>No</option>
                                                    </select></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">If Yes, Reason There Of</td>
                                                <td><textarea name="flag_bye_counter_reason" id="" cols="30" rows="3">{{$getIndexCardDataPCWise['electorData'][0]->flag_bye_counter_reason}}</textarea></td>
                                            </tr>
                                            <tr>
                                                <th style="background: #2c3b48 !important;text-align: center;" colspan="5">
                                                <input type="checkbox" class="toggle_check forRepolling" <?php echo isset($getIndexCardDataPCWise['electorData'][0]->repollData) ? 'checked'.' disabled':''; ?>> &nbsp;Select To Enter Polling Station Count</th>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Enter Number Of Repolls</td>
                                                <td class="dev"><input class="form-control" type="number" min="0" value="<?php echo isset($getIndexCardDataPCWise['electorData'][0]->repollData) ? sizeof($getIndexCardDataPCWise['electorData'][0]->repollData):''; ?>" name="noRepoll" <?php echo isset($getIndexCardDataPCWise['electorData'][0]->repollData) ? 'readonly':''; ?>></td>
                                                <td> <a href="javascript:void(0);" class="btn btn-info forRepollingButton" <?php echo isset($getIndexCardDataPCWise['electorData'][0]->repollData) ? 'disabled':''; ?>> OK </a> </td>
                                            </tr>
                                            <?php $rpcount = 1; ?>
                                            @if(isset($getIndexCardDataPCWise['electorData'][0]->repollData))
                                            @foreach($getIndexCardDataPCWise['electorData'][0]->repollData as $repollData)
                                                <tr>
                                                    <th colspan="4">Date Of Repolls {{$rpcount}}</th>
                                                    <td colspan="1">
                                                        <input class="form-control" value="<?php echo date('Y-m-d', strtotime($repollData->dt_repoll));?>" name="dt_repoll[]" class="form-control">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th colspan="4">No. of Polling Station</th>
                                                    <td colspan="1">
                                                        <input class="form-control" type="number" min="0" value="{{$repollData->no_repoll}}" name="no_repoll[]" class="form-control">
                                                    </td>
                                                </tr>
                                                <?php $rpcount++; ?>
                                            @endforeach
                                            @endif
                                        
                                    </table>
                                    </div>
                             
                         
                                <!-- <h2>Part -B </h2> -->
                                   <!--  <table class="table table-bordered tableindexcard">
                                        <tbody>
                                                             
                                            
                                           
											
                                           
                                           
											
                                        </tbody>
                                    </table> -->
                                    
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

            <!-- menu1 -->

            <div id="menu2" class="tab-pane fade">
                <div class="table-responsive">
                    <form name="menu2" method="POST" action="updateCandiateAcWise">
                    @csrf
                    <input type="hidden" name="st_code" value="{{$st_code}}">
                    <input type="hidden" name="pc" value="{{$pc}}">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="2">SL. No.</th>
                                <th rowspan="2">Candidate Name</th>
                                <th rowspan="2">Sex</th>
                                <th rowspan="2">Age</th>
                                 <th rowspan="2">Category</th>
                                <th rowspan="2">Party Name</th>
                                <th rowspan="2">Party Symbol</th>
                                
                               
                                <th colspan="{{count($getIndexCardDataCandidatesVotesACWise['allACList'])}}">Valid Votes counted From Electronic Voting Machines </th>
                                
                                <th>Total Postal Votes</th>
                                <th>Total Votes</th>
                            </tr>

                            <tr color="acnamerow">
                                    
                                @foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)
                                    <th>{{$allACListsKey}} : {{$allACListsValue}}</th>
                                @endforeach
                                    <th colspan="3"></th>
                            </tr>
                        </thead>

                        <tbody color="CandidateBodyIDWise">
                            <?php $count=1; ?>
                            @foreach($getIndexCardDataCandidatesVotesACWise['candidatedataarray'] as $candpcdata)
                            @foreach($candpcdata as $canddata)
                            <?php //echo "<pre>"; print_r($canddata); die; ?>
                            <tr>
                                <td>{{$count."."}} </td>
                                <td >{{$canddata['cand_name']}}</td>
                                <td>{{$canddata['cand_gender']}}</td>
                                <td>{{$canddata['cand_age']}}</td>
                                 <td>{{$canddata['cand_category']}}</td>
                                <td>{{$canddata['partyname']}}</td>
                                
                                
                               
                                <td>{{$canddata['party_symbol']}}</td>

                                <?php foreach ($canddata['acdata'] as  $values) { ?>
                                <?php foreach ($values as  $value) { ?>

                                     <?php //echo "<pre>"; print_r($value); die; ?>
                                    <td >{{$value}}</td>
                                   
                                    
                                    
                                    
                                <?php }} ?>
                                 <td>{{$canddata['valid_postal_votes']}}</td>
                                <td>{{$canddata['total_valid_vote']}}</td>
                               
                                
                            </tr>



                               
                                    
                                 

                            @endforeach
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


            <!-- menu2 -->

            <div id="menu3" class="tab-pane fade">
                <div class="table-reponsive">
        <form method="POST" action="updateDataForElectionAcWise">
                <table class="table table-bordered">
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
</div>
<div class="col-sm-12">
    <div style="float: right;" class="col-sm-1">
        <button type="submit" class="btn btn-info updatepcwisedata" id="savedataforelectionacwise">Submit</button>
    </div>
</div>
</form>
</div>

<!-- menu3 -->

            </div>
        </div>

        <!--tabs ends-->
<div class="row">
    <div class="col-sm-10 col-offset-sm-1 pull-right">
        
    </div>
</div>
    </div>
</div>
</div> <!-- end grids -->
</div>
<!-- End Wrapper-->

</div>
			</div>
		</div>
	</div>
</section>





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
@section('script')



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
    $(document).on('keyup','.subpollevm, .totalevmvotes, .postalvote, .rejectvote,.totalvotesonevm, .totalvotesonpostal', function(){
        var sum = 0;
		var totalreject  = 0;
		var postalvote = 0;
        var rejectvote = 0;
		var totalvotesonevm = 0;
        var totalvotesonpostal = 0;
		var notaonevm = 0;
        var notaonpostal = 0;
		
		//A
		
        $('.subpollevm').each(function(){
            if($(this).val() != '' || (!Number.isNaN($(this).val())) || $(this).val() != undefined){
                sum += parseInt($.trim($(this).val()));
            }else{
                sum += 0;
            }
        })
        $('.totalpollevm').val(sum);
        var minusOne = $('.totalevmvotes').val();
        $('.validvotesevm').val(minusOne-sum);
		
		
		
		
		$('.postalvote').each(function(){
            if($(this).val() != '' || (!Number.isNaN($(this).val())) || $(this).val() != undefined){
                postalvote += parseInt($.trim($(this).val()));
            }else{
                postalvote += 0;
            }
        })
		
		
		$('.rejectvote').each(function(){
            if($(this).val() != '' || (!Number.isNaN($(this).val())) || $(this).val() != undefined){
                rejectvote += parseInt($.trim($(this).val()));
            }else{
                rejectvote += 0;
            }
        })
		
		
        $('.totalreject').val(rejectvote);
        $('.totalvalid').val(postalvote-rejectvote);
		
		var totalvotesonevm = parseInt($('.totalvotesonevm').val());
        //var totalvotesonpostal = parseInt($('.totalvotesonpostal').val());
		
        $('.totalpolledvotes').val(totalvotesonevm+postalvote);
		
        $('.totalrejectvotes').val(sum+rejectvote);
		
        $('.totalvalidvotes').val((minusOne-sum) + (postalvote-rejectvote));
		
		var notaonevm = parseInt($('.notaonevm').val());
        var notaonpostal = parseInt($('.notaonpostal').val());
		
        $('.totalnotavotes').val(notaonevm+notaonpostal);
		
		
    })


	


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
        // console.log(text)
        //$('input[name="'+id+'"]').val(text);
		if(text!=''){
           $('input[name="'+id+'"]').val(text);
       }else{
           $(this).html(0);
           $('input[name="'+id+'"]').val(0);
       }
    });
    $(document).on('blur','td[contenteditable="true"]',function(){
        var id = $(this).data('canid');
        if(id!=undefined){
            var sum = 0;
            var cl = 'subtotal'+id;
            var loop = $('.'+cl);
            $.each(loop,function(){
                sum += parseInt($.trim($(this).val()));
            });
            $('.total'+id).val(sum);
            $('td[color="candid['+id+'][totalValidVotes]"').text(sum);
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
		
		var nopollingstation = 0;
		
		var nopollingstation = parseInt($('.nopollingstation').val());
		
		var gTotal = $(".col_gtotal").val();
		
		if(nopollingstation > 0){
		var avgpollingstation = (gTotal)/nopollingstation;
		}else{
			var avgpollingstation = 0;
		}
		
		$(".avgpollingstation").val(Math.round(avgpollingstation * 100) / 100);
		
  $(".2col_gtotal").val(parseFloat($("#2row_sum1_total").val())+parseFloat($("#2row_sum2_total").val()));

}
           
});

$(document).on('keyup','.nopollingstation', function(){
		var totalreject  = 0;
		
		var nopollingstation = 0;
		
		var nopollingstation = parseInt($('.nopollingstation').val());
		
		var gTotal = $(".col_gtotal").val();
		
		if(nopollingstation > 0){
		var avgpollingstation = (gTotal)/nopollingstation;
		}else{
			var avgpollingstation = 0;
		}
		
		$(".avgpollingstation").val(Math.round(avgpollingstation * 100) / 100);
				
    })


$(document).ready(function(){
        $('input').each(function(){
            var value = $(this).val();
            if(value == '' || value == null || value == undefined){
                $(this).val(0);
            }
        })
    })
$(document).ready(function(){
    $('a[href="#menu1"]').trigger('click');
})
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  $(this).parent().attr('class','active show');
})
$('a[data-toggle="tab"]').on('hidden.bs.tab', function (e) {
  $(this).parent().removeAttr('class');
})




$(".updatepcwisedata input").removeClass("form-control");


$(".updatepcwisedata input").replaceWith(function () {
 return '<span class="'+this.className+'">'+this.value+'</span>';
});

</script>


@endsection