@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Index Card Report')

@section('content')




<style>
#index_cus_ch ul li.active {
    background: #17a2b8;
}

.card-header h3 {
    font-size: 18px;
}

#table-scrol td p{
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    font-size: 13px;

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
    white-space: nowrap;
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



@media only screen and (max-width: 768px) {

#pdfbt a.btn.btn-primary {
     position: absolute;
    top: 106px;
}


 #div_botm3{
    padding-right: 4px !important;
    position: relative;
    left: -13px;
}
}


@media only screen and (max-width:375px) {
#div_botm3{
	position: relative;
	left: 0px;
}

}

@media only screen and (max-width: 480px) {


table#table-scrol{
    position: relative;
    left: -6px;
}
p.mb-0.text-center {
    position: relative;
    left: -36px;
}
#div_botm3{
    width: 67%;
}

#div_botm9{
    position: relative;
    width: 33%;
    padding: 0px;
    margin: 0px;
}
#index_cus_ch ul li{
    margin: 4px 1px;
}
}

@media only screen and (max-width: 320px) {

#pdfbt a.btn.btn-primary {
    position: absolute !important;
    top: 127px;
    font-size: 8px;
    right: 3px !important;
}

}

</style>
<?php  $st=getstatebystatecode($st_code);   ?> 

<?php if(Auth::user()->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if(Auth::user()->role_id == '7'){
			$prefix 	= 'eci';
		}   ?>
<section class="">
	<div class="container">
		<div class="row">
		
		@if(Auth::user()->role_id == '27' || Auth::user()->role_id == '7')
		<div class="card-body" style="border:3px solid #eee;">
 
	<form class="form form-inline" method="GET" action="{!! url(''.$prefix.'/indexcardpc') !!}" style="padding:0px 0px 0px;">
                            <h5 class="pb-3" style="text-decoration:underline;margin: auto;"><b>Select State And PC</b></h5>

	 <div class="col-sm-12 form-group">

		<div class="col-sm-5">
	      <select class="form-control" name="st_code" id="st_code" style="width:100%;" placeholder="Select State" onChange="getPC(this.value);" required>
	      	<option value="">Select State</option>
	      	@foreach($stateList as $stateLists)
	      		<option value="{{$stateLists->ST_CODE}}" @if(isset($_GET['st_code']) && ($_GET['st_code']== $stateLists->ST_CODE))    selected @endif >{{$stateLists->ST_NAME}}</option>
	      	@endforeach
	      </select>
	    </div>
 
	    <div class="col-sm-5">
	      <select class="form-control" name="pc" style="width:100%;" id="pc" placeholder="Select PC" required>
	      	<option value="">Select PC</option>
	      	@foreach($pcList as $pcLists)
	      		<option value="{{$pcLists->PC_NO}}" @if(isset($_GET['pc']) && ($_GET['pc']== $pcLists->PC_NO))    selected @endif >{{$pcLists->PC_NO.'-'.$pcLists->PC_NAME}}</option>
	      	@endforeach
	      </select>
	    </div>

			<div class="col-sm-2">

	    		<button type="submit" class="btn btn-info btn-lg pull-left" style="">Show</button>

           </div>

	</div>

</form>
 </div>
		
		@endif
		
			@if($st_code)
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h3> Index Card Parliamentary - {{getElectionYear()}}</h3></div> 
						@if($getIndexCardDataPCWise['t_pc_ic']->finalize_by_ro == 1)
							<div class="center" style="color:#F0587E;font-size:20px;"><b> Finalized By RO</b> ({{date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->finalize_by_ro_date))}})</div>
						@else
							<div class="center" style="color:#F0587E;font-size:20px;"><b> Not Finalized By RO</b></div>
						@endif
						
						@if($getIndexCardDataPCWise['t_pc_ic']->finalize_by_ceo == 1)
							<div class="center" style="color:#F0587E;font-size:20px;"><b> , Finalized By CEO</b> ({{date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->finalize_by_ceo_date))}})</div>
						@else
							<div class="center" style="color:#F0587E;font-size:20px;"><b> , Not Finalized By CEO</b></div>
						@endif
						
						
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
<div class="row" id="pdfbt" style="text-align: right;">

@if(Auth::user()->designation == 'CEO')

     <a href='{{url("pcceo/indexcardpcpdf/$pc")}}' target="_blank" class="btn btn-primary pdfbt" style="position: absolute; right: 140px;">Download PDF</a>  
    <!-- <a href='{{url("pcceo/indexcardpcexcel/$pc")}}' target="_blank" class="btn btn-primary pdfbt btn-success" style="position: absolute; right: 5px;">Download Excel</a>  -->

@elseif(Auth::user()->designation == 'DEO')

<a href='{{url("pcdeo/indexcardpcpdf/$pc")}}' target="_blank" class="btn btn-primary pdfbt" style="position: absolute; right: 140px;">Download PDF</a> 
<!--<a href='{{url("pcdeo/indexcardpcexcel/$pc")}}' target="_blank" class="btn btn-primary pdfbt btn-success" style="position: absolute; right: 5px;">Download Excel</a> -->

@elseif(Auth::user()->role_id == '7')

     <a href='{{url("eci/indexcardpcpdf/$st_code/$pc")}}' target="_blank" class="btn btn-primary" style="position: absolute; right: 140px;z-index:999;top:-42px;">Download PDF</a>  	
    <a href='{{url("eci/indexcardpcexcel/$st_code/$pc")}}' target="_blank" class="btn btn-primary btn-success" style="position: absolute; right: 5px;z-index:999;top:-42px;">Download Excel</a>  	
	 
@elseif(Auth::user()->role_id == '27')

     <a href='{{url("eci-index/indexcardpcpdf/$st_code/$pc")}}' target="_blank" class="btn btn-primary" style="position: absolute; right: 140px;z-index:999;top:-42px;">Download PDF</a>  	 
    <a href='{{url("eci-index/indexcardpcexcel/$st_code/$pc")}}' target="_blank" class="btn btn-primary btn-success" style="position: absolute; right: 5px;z-index:999;top:-42px;">Download Excel</a>  
		
@else
	 <a href="indexcardpcpdf" target="_blank" class="btn btn-primary" style="position:absolute; right: 140px;">Download PDF</a>
	<!-- <a href="indexcardpcexcel" target="_blank" class="btn btn-primary btn-success" style="position: absolute; right: 5px;">Download Excel</a>-->
@endif	
</div>

      <div class="row" id="index_cus_ch">
        <!--tabs starts-->

        <ul class="nav nav-tabs ml-4" role="tablist">
            <li role="presentation"><a data-toggle="tab" href="#menu1">Data For Election Card Index</a></li>
            <li role="presentation"><a data-toggle="tab" href="#menu2">Information About Candidate in PC</a></li>
            <li role="presentation"><a data-toggle="tab" href="#menu3">Data For Election AC Wise</a></li>
           <!-- <li><a href="changeRequest">Edit Request</a></li>
            <li><a href="finaliserequest">Finalise Request</a></li>-->
        </ul>
		

       

        <div class="tab-content" style="overflow: auto;">

            <div id="menu1" role="tabpanel" class="tab-pane fade in active">
                <!--  <h3>Data For Election</h3> -->
                    <?php //echo "<pre>"; print_r($getIndexCardDataPCWise['t_pc_ic']); die; ?>

                
                        <div class="col-sm-12">
                            <div class="col-md-12 col-sm-12 col-xs-12 tab_card">
                               
                                    <div class="table-responsive">
                                    <table class="table table-bordered tableindexcard" style="width: 100%;">



                                             <tr>
                                                <th>I</th>
                                                <th>CANDIDATES</th>
                                                <th>MALE</th>
                                                <th>FEMALE</th>
                                                <th>THIRD GENDER</th>
                                                <th>TOTAL</th>
                                            </tr>
                                            
                                            @foreach($getIndexCardDataPCWise['indexCardData'] as $nominatedData)
                                            
                                            @if($nominatedData->status == 'nominated')
                                            <tr>
                                                <td>1.</td>
                                                <td>Nominated </td>
                                                <td><input class="form-control 1row_sum1" type="number" min="0" name="c_nom_m_t" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_m_t}}"></td>
                                                <td><input class="form-control 1row_sum1" type="number" min="0" name="c_nom_f_t" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_f_t}}"></td>
                                                <td><input class="form-control 1row_sum1" type="number" min="0" name="c_nom_o_t" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_o_t}}"></td>
                                                <td><input class="form-control" id="1row_sum1_total" type="number" min="0" name="c_nom_a_t" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_a_t}}" readonly></td>
                                            </tr>
                                            @endif
                                            @if($nominatedData->status == 'rejected')
                                            <tr>
                                                <td>2.</td>

                                                <td>Nominations  Rejected</td>
                                                <td><input class="form-control 1row_sum2" type="number" min="0" name="c_nom_r_m" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_m}}"></td>
                                                <td><input class="form-control 1row_sum2" type="number" min="0" name="c_nom_r_f" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_f}}"></td>
                                                <td><input class="form-control 1row_sum2" type="number" min="0" name="c_nom_r_o" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_o}}"></td>
                                                <td><input class="form-control"  id="1row_sum2_total" type="number" min="0" name="c_nom_r_a" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_a}}" readonly></td>
                                            </tr>
                                            @endif
                                            @if($nominatedData->status == 'withdrawn')
                                            
                                            <tr>
                                                <td>3.</td>
                                                <td>Withdrawn</td>
                                                <td><input class="form-control 1row_sum3" type="number" min="0" name="c_nom_w_m" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_m}}"></td>
                                                <td><input class="form-control 1row_sum3" type="number" min="0" name="c_nom_w_f" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_f}}"></td>
                                                <td><input class="form-control 1row_sum3" type="number" min="0" name="c_nom_w_o" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_o}}"></td>
                                                <td><input class="form-control" id="1row_sum3_total" type="number" min="0" name="c_nom_w_t" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_t}}" readonly></td>
                                            </tr>
                                            
                                            @endif
                                            @if($nominatedData->status == 'accepted')
                                            <tr>
                                                <td>4.</td>
                                                <td>Contested </td>
                                                <td><input class="form-control 1row_sum4" type="number" min="0" name="c_nom_co_m" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_m}}"></td>
                                                <td><input class="form-control 1row_sum4" type="number" min="0" name="c_nom_co_f" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_f}}"></td>
                                                <td><input class="form-control 1row_sum4" type="number" min="0" name="c_nom_co_o" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_o}}"></td>
                                                <td><input class="form-control" id="1row_sum4_total" type="number" min="0" name="c_nom_co_t" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_t}}" readonly></td>
                                            </tr>
                                            @endif
                                            @if($nominatedData->status == 'forfieted')
                                           <tr>
                                            <td>5.</td>
                                               <td>Deposit Forfeited </td>
                                               <td><input class="form-control 1row_sum5" type="number" min="0" name="c_nom_fd_m" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_m}}"></td>
                                               <td><input class="form-control 1row_sum5" type="number" min="0" name="c_nom_fd_f" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_f}}"></td>
                                               <td><input class="form-control 1row_sum5" type="number" min="0" name="c_nom_fd_o" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_o}}"></td>
                                               <td><input class="form-control" id="1row_sum5_total" type="number" min="0" name="c_nom_fd_t" value="{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_t}}" readonly></td>
                                           </tr>
                                            @endif
                                            @endforeach                   




<tr>    
<th>II</th>
<th>ELECTORS</th>
<th colspan="2" style="text-align: center;">GENERAL</th>
<th  rowspan="2">SERVICE</th>
<th rowspan="2">TOTAL</th>
</tr>


<tr> 
    <th colspan="2"></th>
<th>Other than NRIs</th>
<th>NRIs</th>

</tr>


<tr>    
<td>1</td>
<td>Male</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_m }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_nri_m }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_ser_m }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_all_t_m }}</td>


</tr>


<tr>    
<td>2</td>
<td>Female</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_f }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_nri_f }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_ser_f }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_all_t_f }}</td>


</tr>




<tr>    
<td>3</td>
<td>Third Gender (Not applicable to service electors)</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_o }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_nri_o }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_ser_o }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_all_t_o }}</td>


</tr>


<tr>    
<td>4</td>
<td>Total</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_t }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_nri_t }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_ser_t }}</td>
<td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_all_t }}</td>


</tr>







<tr>    
<th>III</th>
<th>VOTERS TURNED UP FOR VOTING</th>
<th colspan="2" style="text-align: center;">GENERAL</th>
<th colspan="2" style="text-align: center;">Total</th>
</tr>


<tr> 
 <th colspan="2"></th>
<th>Other than NRIs</th>
<th>NRIs</th>
<th colspan="2" style="text-align: center;"></th>

</tr>


<tr>    
<td>1</td>
<td>Male</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_m }}</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_m ? :0 }}</td>
<td colspan="2" style="text-align: center;">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_m +$getIndexCardDataPCWise['t_pc_ic']->vt_nri_m }}</td>


</tr>


<tr>    
<td>2</td>
<td>Female</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_f}}</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_f ? :0}}</td>
<td colspan="2" style="text-align: center;">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_f + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_f }}</td>


</tr>




<tr>    
<td>3</td>
<td>Third Gender</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_o}}</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_o ? :0}}</td>
<td colspan="2" style="text-align: center;">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_o + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_o }}</td>


</tr>


<tr>    
<td>4</td>
<td>Total[Male+ Female+ Third Gender]</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_t}}</td>
<td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_t }}</td>
<td colspan="2" style="text-align: center;">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_t + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_t }}</td>


</tr>




                                        <tr>
                                            <th>IV</th>
                                                <th colspan="5">DETAILS OF VOTES POLLED ON EVM</th>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td colspan="4">Total votes polled on EVM</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->t_votes_evm}}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td colspan="4">Test voted under Rule 49 MA</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->mock_poll_evm ? :0 }}</td>
                                            </tr>
                                            
                                            <!--<tr>
                                                <td>3</td>
                                                <td colspan="4">Votes not retrieved From EVM</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->not_retrieved_vote_evm ? :0}}</td>
                                            </tr>-->
											
											
											<tr>
											<td>3A</td>
                                                <td colspan="4">Votes Counted From CU Of EVM</td>
												<td>{{$getIndexCardDataPCWise['t_pc_ic']->votes_counted_from_evm ? :0}}</td>
                                            </tr>
											
											<tr>
											<td>3B</td>
                                                <td colspan="4">Votes Counted From VVPAT (Whenever Votes Not Retrieved From CU)</td>
												<td>{{$getIndexCardDataPCWise['t_pc_ic']->votes_counted_from_vvpat ? :0}}</td>
                                            </tr>
											
											
											
                                            
                                            <tr>
                                                <td>4</td>
                                                <td colspan="4">Rejected votes (due to other reasons)</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->r_votes_evm ?: 0}}</td>
                                            </tr>
                                            
                                            
                                             <tr>
                                                <td>5</td>
                                                <td colspan="4">Votes polled for 'NOTA' on EVM</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota}}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td>6</td>
                                                <td colspan="4">Total of test votes + votes rejected (due to other reasons) + 'NOTA' [2+4+5]</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->v_r_evm_all}}</td>
                                            </tr>
                                            
                                            
                                            <tr>
                                                <td>7</td>
                                                <td colspan="4">Total valid votes counted from EVM [1-6]</span></td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->v_votes_evm_all}}</td>
                                            </tr>



                                      <tr>
                                        <th>V</th>
                                                <th colspan="5"> DETAILS OF POSTAL VOTES</th>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td colspan="4">Postal votes counted for service voters under sub-section (8) of Section 20 of R.P. Act, 1950</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_ser_u ? :0}}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td colspan="4">Postal votes counted for Govt. servants on election duty (including all police personnel , drivers, conductors, cleaners).</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_ser_o ? :0}}</td>
                                            </tr>
                                             <tr>
                                                <td>3</td>
                                                <td colspan="4">Postal votes rejected</td>
                                                <td class="dev" colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_rejected ? :0}}</td>
                                            </tr>
                                            
                                            
                                            
                                            <tr>
                                                <td>4</td>
                                                <td colspan="4">Postal votes polled for 'NOTA'</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota}}</td>
                                            </tr>
                                           
                                           
                                           <tr>
                                            <td>5</td>
                                                <td colspan="4">Total of postal votes rejected + 'NOTA' [3+4]</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_r_nota}}</td>
                                            </tr>
                                           
                                           
                                            <tr>
                                                <td>6</td>
                                                <td colspan="4">Total valid postal votes [1+2-5] </td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_valid_votes}}</td>
                                            </tr>




          
                                             <tr>
                                                <th>VI</th>
                                                <th colspan="5">COMBINED DETAILS OF EVM & POSTAL VOTES</th>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td colspan="4">Total votes polled [IV(1) + V(1+2)]</span></td>
                                                <td class="dev">{{$getIndexCardDataPCWise['t_pc_ic']->total_votes_polled}}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td colspan="4">Total of test votes + votes rejected +'NOTA'[IV(6) + V(5)]</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->total_not_count_votes}}</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td colspan="4">Total valid votes [IV(7) + V(6)]</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->total_valid_votes}}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td>4</td>
                                                <td colspan="4">Total votes polled for 'NOTA' [IV(5) + V(4)]</td>
                                                <td>{{$getIndexCardDataPCWise['t_pc_ic']->total_votes_nota}}</td>
                                            </tr>

                     
            




											<tr>
                                                <th>VII</th>
                                                <th colspan="5">MISCELLANEOUS</th>
                                            </tr>
											
											<tr>
                                                <td>1</td>
                                                <td colspan="4">Proxy votes</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->proxy_votes ? :0}}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td colspan="4">Tendered votes</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->tendered_votes ? :0}}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td>3</td>
                                                <td colspan="4">Total number of polling stations set up in the Constituency</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->total_no_polling_station ? :0}}</td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td colspan="4">Average number of Electors per polling station in a Constituency</td>
                                                <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->avg_elec_polling_stn}}</td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td colspan="4">Date(s) Of Poll</td>
                                                <td colspan="1">{{date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->dt_poll))}}</td>
                                            </tr>
											
											<tr>
                                                <td>6</td>
                                                <td colspan="4">Date(s) Of Re-poll,if any</td>
                                                <td colspan="1">
												@if (trim($getIndexCardDataPCWise['t_pc_ic']->dt_repoll) != 0 && $getIndexCardDataPCWise['t_pc_ic']->dt_repoll)
													
												<?php 
													$repoll_dates 	= explode(',',$getIndexCardDataPCWise['t_pc_ic']->dt_repoll);
													$dates_array 	= [];
													foreach($repoll_dates as $res_repoll){
														$dates_array[] = date('d-m-Y', strtotime(trim($res_repoll)));
													}	
												?>
												
												{!! implode(', ', $dates_array) !!}
                                                @else{{'NA'}}
												@endif
												
												</td>
                                            </tr>
											
											<tr>
                                                <td>7</td>
                                                <td colspan="4">Number Of Polling Stations where Re-poll was ordered (mention date of Order also)</td>
                                                <td colspan="1">
												@if ($getIndexCardDataPCWise['t_pc_ic']->re_poll_station)
												{{$getIndexCardDataPCWise['t_pc_ic']->re_poll_station}}
                                                @else{{'NA'}}
												@endif
												
												</td>
                                            </tr>
											
											
                                            <tr>
                                                <td>8</td>
                                                <td colspan="4">Date(s) Of counting</td>
                                                <td colspan="1">{{date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->dt_counting))}}</td>
                                            </tr>
											
											
                                            <tr>
                                                <td>9</td>
                                                <td colspan="4">Date Of declaration Of result</td>
                                                <td colspan="1">{{date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->dt_declare))}}</td>
                                            </tr>
                                            <tr>
                                                <td>10</td>
                                                <td colspan="4">Whether this is Bye election <br> or Countermanded election? &nbsp; &nbsp; &nbsp;   Yes/No</td>
                                                <td class="dev" colspan="1">
												@if ($getIndexCardDataPCWise['t_pc_ic']->flag_bye_counter == 1)
												Yes
												@else
												No
												@endif
													
													</td>
                                            </tr>
                                            <tr>
                                                <td>11</td>
                                                <td colspan="4">If yes, reasons thereof</td>
                                                <td>
												@if ($getIndexCardDataPCWise['t_pc_ic']->flag_bye_counter_reason)
												{{$getIndexCardDataPCWise['t_pc_ic']->flag_bye_counter_reason}}
												@else
												Not Applicable
												@endif
												
												</td>
                                            </tr>
											
										
                                    </table>
                                    </div>
                         
                             
                            </div>
                        </div> 

            </div>

            <!-- menu1 -->

            <div id="menu2" class="tab-pane fade">
                <div class="table-responsive" style="position:relative;">
                    <!--<form name="menu2" method="POST" action="updateCandiateAcWise">
                    @csrf
                    <input type="hidden" name="st_code" value="{{$st_code}}">
                    <input type="hidden" name="pc" value="{{$pc}}">-->
                    <table class="table table-bordered" style="width: 100%; text-align: center;">
                        <thead>
                            <tr>
                                <th rowspan="2">SL. No.</th>
                                <th rowspan="2">Name of Contesting Candidates <br>(in Block letters)</th>
                                <th rowspan="2">Sex <br> (Male/Female/Third Gender)</th>
                                <th rowspan="2">Age <br> (Years)</th>
                                 <th rowspan="2">Category <br>(Gen/SC/ST)</th>
                                <th rowspan="2">Full Name of the Party</th>
                                <th rowspan="2">Election Symbol Allotted</th>
                                
                               
                                <th colspan="{{count($getIndexCardDataCandidatesVotesACWise['allACList'])}}">Valid Votes counted From Electronic Voting Machines </th>
                                <?php 
								if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){ ?>
									<th>Migrant Votes</th>
								<?php }
								?>
                                
                                <th>Valid Postal Votes</th>
                                <th>Total Valid Votes</th>
                            </tr>

                            <tr color="acnamerow">
                                    
                                @foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)
                                    <th>{{$allACListsKey}} : {{$allACListsValue}}</th>
                                    
                                @endforeach
                                    <th colspan="3"></th>
                            </tr>

                        </thead>

                        <tbody color="CandidateBodyIDWise">
						

						
                            <?php $count=1; 
							$dataSum  = array();
							
							$total_valid_postel_votes = 0;
							$total_valid_migrate_votes = 0;
							$total_valid_votes = 0;
							
							$i=0;
							?>
                            @foreach($getIndexCardDataCandidatesVotesACWise['candidatedataarray'] as $key1 => $candpcdata)
							
							
                            @foreach($candpcdata as  $key2 => $canddata)
                            
                            <tr>
                                <td>{{$count."."}} </td>
                                <td >{{$canddata['cand_name']}}</td>
                                <td  style="text-transform: capitalize;">{{$canddata['cand_gender']}}</td>
                                <td>{{$canddata['cand_age']}}</td>
                                 <td style="text-transform: capitalize;">{{$canddata['cand_category']}}</td>
                                <td>{{$canddata['partyname']}}</td>
                                
                                
                               
                                <td>{{$canddata['party_symbol']}}</td>

                                <?php 
								
								
								$sum = 0;
								
								foreach ($canddata['acdata'] as $key3 => $values) { 
								
								$sum = $values;
								
								
								if (isset($dataSum[$key3]))
								{
									$dataSum[$key3] += $sum;
								}
								else
								{
									$dataSum[$key3] = $sum;
								}
								
								
								?>
                                  
								
								  
                                <td >{{$values}}</td>
                                      
                                <?php } ?>
								
								<?php 
								
								$migrate_votes = 0;
								
								if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){ 
								$migrate_votes = $canddata['migrate_votes'];
								?>
								
								
									<td>{{$canddata['migrate_votes']}}</td>
								<?php } ?>
								
								
								
                                 <td>{{$canddata['valid_postal_votes']}}</td>
                                <td>{{$canddata['valid_postal_votes']+$canddata['total_valid_vote'] + $migrate_votes}}</td>
                               
                                <?php 
								$total_valid_postel_votes += $canddata['valid_postal_votes'];
								$total_valid_migrate_votes += $canddata['migrate_votes'];
							$total_valid_votes += $canddata['valid_postal_votes']+$canddata['total_valid_vote'] + $migrate_votes;
								
								?>
                            </tr>



                              
                                    
                                 

                            @endforeach
							<?php $count++; ?>
                            @endforeach

						<?php 
						
						$migrate_vote_nota = 0;
						$postal_vote_nota = 0;
						$total_nota = 0;
						
						
						if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){ ?>

							<tr>
							<td>{{$count}}</td>
							<td><b>Nota</b></td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							@foreach($getIndexCardDataPCWise['migrate_nota'] as $key4 => $dataValue)
							<td>{{$dataValue['total_vote']}}
							</td>
							
							<?php 
							
							
							if (isset($dataSum[$key4]))
								{
									$dataSum[$key4] += $dataValue['total_vote'];
								}
								else
								{
									$dataSum[$key4] = $dataValue['total_vote'];
								}							
							?> 
							
							
							@endforeach
							
							<td>{{$getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota}}</td>
							
							<td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota}}</td>
							<td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota}}</td>
							</tr>

						<?php

						$migrate_vote_nota = $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota;
						$postal_vote_nota = $getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota;
						$total_nota = $getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota;


						} ?>



							<tr>
							<td colspan="7" style="text-align:right;"><b>Total</b></td>
							@foreach($dataSum as $dataValue)
							<td>{{$dataValue}} </td>
							@endforeach
							
							<?php if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){ ?>
							<td>{{$total_valid_migrate_votes + $migrate_vote_nota}}</td>
							<?php } ?>
							
							
							<td>{{$total_valid_postel_votes + $postal_vote_nota}}</td>
							<td>{{$total_valid_votes + $total_nota}}</td>
							</tr>




                        </tbody>
                    </table>
                </div>
				
				<!--
                <div class="col-sm-12">
                    <div style="float: right;" class="col-sm-1">
                        <button type="submit" class="btn btn-info updatepcwisedata" id="saveCandidateWise">Submit</button>
                    </div>
                </div>
				
				
            </form>-->
            </div>


            <!-- menu2 -->

            <div id="menu3" class="tab-pane fade" style="overflow:hidden;">
			
			<div class="row">
                <div class="col-sm-3" id="div_botm3" style="padding-right: 15px";>
       <!-- <form method="POST" action="updateDataForElectionAcWise">-->
                <table class="table table-bordered" id="table-scrol" style="">
   


<tbody>

<tr>
<th colspan="2" style="height: 107px;vertical-align:middle;text-align:center;overflow: hidden;">Total Electors</th>
</tr>



<tr>
<td rowspan="4">1. General [Other than NRIs]</td>

<td>Male</td>
</tr>

<tr>

<td>Female</td>
</tr>



<tr>

<td><p>Third Gender</p></td>
</tr>

<tr>

<td>Total</td>
</tr>




<tr>
<td rowspan="4">2. General [NRIs]</td>

<td>Male</td>
</tr>

<tr>

<td>Female</td>
</tr>



<tr>

<td><p>Third Gender</p></td>
</tr>

<tr>

<td>Total</td>
</tr>




<tr>
<td rowspan="4">3. Service</td>

<td>Male</td>
</tr>

<tr>

<td>Female</td>
</tr>



<tr>

<td><p>Third Gender</p></td>
</tr>

<tr>

<td>Total</td>
</tr>





<tr>
<td rowspan="4">4. Total</td>

<td>Male</td>
</tr>

<tr>

<td>Female</td>
</tr>



<tr>

<td><p>Third Gender</p></td>
</tr>

<tr>

<td>Total</td>
</tr>
</tbody>
</table>








</div>


                <div class="col-sm-9" id="div_botm9" style="position: relative;padding: 0px; left: -16px;">
    <table class="table table-bordered">
   


<tbody>

<tr style="height:107px;">
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)
                                    <th style="vertical-align:middle;text-align: center;"><p>    {{$allACListsKey.". ".$allACListsValue}} </p></th>
                                @endforeach

<th style="vertical-align:middle;">Total</th>
</tr>
<?php $total = 0; ?>
<tr>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)



                                    <td>{{$getelectorsacwise[$allACListsKey]['gen_m']}}</td>
							<?php  $total +=	$getelectorsacwise[$allACListsKey]['gen_m'];?>								
								
@endforeach
                                    <td>{{$total}}</td>	
</tr>

<tr>
<?php $total = 0; ?>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)



                                    <td>{{$getelectorsacwise[$allACListsKey]['gen_f']}}</td>									
								<?php  $total +=	$getelectorsacwise[$allACListsKey]['gen_f'];?>
                                @endforeach

                                    <td>{{$total}}</td>
</tr>
<?php $total = 0; ?>
<tr>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)


                                    <td>{{$getelectorsacwise[$allACListsKey]['gen_o']}}</td>									
 								<?php  $total +=	$getelectorsacwise[$allACListsKey]['gen_o'];?>
                                @endforeach

                                   <td>{{$total}}</td>	
</tr>
<?php $total = 0; ?>
<tr>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)


                                    <td>{{$getelectorsacwise[$allACListsKey]['gen_t']}}</td>									
								<?php  $total +=	$getelectorsacwise[$allACListsKey]['gen_t'];?>
                                @endforeach

                                    <td>{{$total}}</td>
</tr>


<?php $total = 0; ?>
<tr>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)


                                    <td>{{$getelectorsacwise[$allACListsKey]['nri_m']}}</td>									
 								<?php  $total +=	$getelectorsacwise[$allACListsKey]['nri_m'];?>
                                @endforeach
                                   <td>{{$total}}</td>	

</tr>
<?php $total = 0; ?>
<tr>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)


                                    <td>{{$getelectorsacwise[$allACListsKey]['nri_f']}}</td>	
									<?php  $total +=	$getelectorsacwise[$allACListsKey]['nri_f'];?>							
                             									
                                @endforeach

        <td>{{$total}}</td>
</tr>
<?php $total = 0; ?>
<tr>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)


                                    <td>{{$getelectorsacwise[$allACListsKey]['nri_o']}}</td>									
 								<?php  $total +=	$getelectorsacwise[$allACListsKey]['nri_o'];?>
                                @endforeach

                                   <td>{{$total}}</td>
</tr>
<?php $total = 0; ?>
<tr>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)

                                    <td>{{$getelectorsacwise[$allACListsKey]['nri_t']}}</td>								
								<?php  $total +=	$getelectorsacwise[$allACListsKey]['nri_t'];?>
                                @endforeach

                                    <td>{{$total}}</td>
</tr>



<?php $total = 0; ?>

<tr>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)

                                    <td>{{$getelectorsacwise[$allACListsKey]['ser_m']}}</td>	
									<?php  $total +=	$getelectorsacwise[$allACListsKey]['ser_m'];?>								
 								
                                @endforeach
                                   <td>{{$total}}</td>	

</tr>
<?php $total = 0; ?>
<tr>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)

                                    <td>{{$getelectorsacwise[$allACListsKey]['ser_f']}}</td>									
                             				<?php  $total +=	$getelectorsacwise[$allACListsKey]['ser_f'];?>					
                                @endforeach

       <td>{{$total}}</td>
</tr>
<?php $total = 0; ?>
<tr>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)
                                    <td>{{$getelectorsacwise[$allACListsKey]['ser_o']}}</td>		<?php  $total +=	$getelectorsacwise[$allACListsKey]['ser_o'];?>							
 								
                                @endforeach

                                   <td>{{$total}}</td>	
</tr>
<?php $total = 0; ?>
<tr>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)

                                    <td>{{$getelectorsacwise[$allACListsKey]['ser_t']}}</td>								
								<?php  $total +=	$getelectorsacwise[$allACListsKey]['ser_t'];?>
                                @endforeach

                                    <td>{{$total}}</td>
</tr>



<?php $total = 0; ?>
<tr>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)

                                    <td>{{$getelectorsacwise[$allACListsKey]['tot_m']}}</td>									
 								<?php  $total +=	$getelectorsacwise[$allACListsKey]['tot_m'];?>
                                @endforeach
                                   <td>{{$total}}</td>	

</tr>
<?php $total = 0; ?>
<tr>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)

                                    <td>{{$getelectorsacwise[$allACListsKey]['tot_f']}}</td>									
                             				<?php  $total +=	$getelectorsacwise[$allACListsKey]['tot_f'];?>					
                                @endforeach

       <td>{{$total}}</td>
</tr>
<?php $total = 0; ?>
<tr>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)

                                    <td>{{$getelectorsacwise[$allACListsKey]['tot_o']}}</td>	
									<?php  $total +=	$getelectorsacwise[$allACListsKey]['tot_o'];?>								
 								
                                @endforeach

                                   <td>{{$total}}</td>	
</tr>

<tr>
<?php $total = 0; ?>
@foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)

                                    <td>{{$getelectorsacwise[$allACListsKey]['tot_all']}}</td>								
								<?php  $total +=	$getelectorsacwise[$allACListsKey]['tot_all'];?>
                                @endforeach

                                    <td>{{$total}}</td>
</tr>



</table>
</div>
</div>
<!--
<div class="col-sm-12">
    <div style="float: right;" class="col-sm-1">
        <button type="submit" class="btn btn-info updatepcwisedata" id="savedataforelectionacwise">Submit</button>
    </div>
</div>


</form>-->
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
@endif

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
<script src="jquery-3.2.1.min.js" type="text/javascript"></script>
<script>
function getPC(val) {
	$.ajax({
	type: "GET",
	url: "ajaxpccall",
	data:'st_code='+val,
	success: function(data){
		$("#pc").html(data);
		getCity();
	}
	});
}
</script>

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




$("input").removeClass("form-control");


$("input").replaceWith(function () {
 return '<span class="'+this.className+'">'+this.value+'</span>';
});

</script>


@endsection