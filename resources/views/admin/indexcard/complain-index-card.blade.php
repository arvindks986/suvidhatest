@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Index Card Report')

@section('content')
<style type="text/css">
    .not_visible{
        visibility: hidden;
        width: 0px;
        height: 0px;
        float: left;
    }
    input:read-only, input:-moz-read-only{
        background: transparent;
        border: 0px;
    }
    .form-control[readonly],.form-control[readonly]:hover,.form-control[readonly]:focus{
        background: transparent;
        border: 0px;
    }
</style>



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


@if(Session::has('flash-message'))
      @if(Session::has('status'))
        <?php
        $status = Session::get('status');
        if($status==1){
          $class = 'alert-success';
        }
        else{
          $class = 'alert-danger';
        }
        ?>
      @endif
      <div class="alert <?php echo $class; ?>">
        {{ Session::get('flash-message') }}
      </div>
    @endif


<?php  $st=getstatebystatecode($st_code);   
    $pc_no = $getIndexCardDataPCWise['pcType']->PC_NO;
?> 

<section class="dashboard-header pt-3 pb-3">
  <div class="container-fluid">
  
        
      <form id="generate_report_id" class="row" method="get" onsubmit="return false;">
  

          <div class="form-group col-md-3"> <label>State</label> 
          
            <select name="st_code" id="st_code" class="form-control" onchange ="filter()">
              <option value="">Select State</option>
            @foreach($states as $iterate_state)
              @if($st_code == $iterate_state['st_code'])
                <option value="{{$iterate_state['st_code']}}" selected="selected" >{{$iterate_state['st_name']}}</option> 
              @else 
                <option value="{{$iterate_state['st_code']}}">{{$iterate_state['st_name']}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>

          <div class="form-group col-md-3"> <label>PC </label> 
          
            <select name="pc_no" id="pc_no" class="form-control" onchange ="filter()">
            <option value="">Select PC</option>
            @foreach($pcs as $result)
              @if($pc_no == $result['pc_no'])
                <option value="{{$result['pc_no']}}" selected="selected" >{{$result['pc_no']}}-{{$result['pc_name']}}</option> 
              @else 
                <option value="{{$result['pc_no']}}" >{{$result['pc_no']}}-{{$result['pc_name']}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>
         
        </form>   
  
    
  </div>
</section>


<section class="">
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h3> Index Card Parliamentary - 2019</h3></div> 
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


                      <div class="row" id="index_cus_ch">
                        <!--tabs starts-->

                        <ul class="nav nav-tabs ml-4" role="tablist">
                            <li role="presentation"><a data-toggle="tab" href="#menu1">Data For Election Card Index</a></li>
                            <li role="presentation"><a data-toggle="tab" href="#menu2">Information About Candidate in PC</a></li>
                            <li role="presentation"><a data-toggle="tab" href="#menu3">Data For Election AC Wise</a></li>
                        </ul>




                        <div class="tab-content" style="overflow: auto;">

                            <div id="menu1" role="tabpanel" class="tab-pane fade in active">
                
                <div class="col-sm-12">
                    <div class="col-md-12 col-sm-12 col-xs-12 tab_card">
                       <div class="table-responsive">

                
                        <table class="table table-bordered" id="nomination" style="width: 100%;">



                         <tr class="head_nominated">

                            <th>I</th>
                            <th>CANDIDATES</th>
                            <th>MALE</th>
                            <th>FEMALE</th>
                            <th>THIRD GENDER</th>
                            <th>TOTAL</th>
                           

                        </tr>

                        <tr>
                            <td colspan="6" align="right"><button class="btn btn-success btn-edit-request pull-right" request-type="1" request-heading="Nominated Candidate">Edit Nomination Request</button></td>
                        </tr>
                        @foreach($getIndexCardDataPCWise['indexCardData'] as $nominatedData)

                        @if($nominatedData->status == 'nominated')
                        <tr>

                            <td class="not_included">1.</td>
                            <td class="not_included label">Nominated </td>
                            <td><span class="not_visible">Male: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_m_t}}</td>
                            <td><span class="not_visible">Female: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_f_t}}</td>
                            <td><span class="not_visible">Third Gender: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_o_t}}</td>
                            <td><span class="not_visible">Total: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_a_t}}</td>
                            
                            </tr>
                            @endif
                            @if($nominatedData->status == 'rejected')
                            <tr>
                                
                                <td class="not_included">
                                2. </td>

                                <td class="not_included label">Nominations  Rejected</td>


                                <td><span class="not_visible">Male: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_m}}</td>
                                <td><span class="not_visible">Female: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_f}}</td>
                                <td><span class="not_visible">Third Gender: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_o}}</td>
                                <td><span class="not_visible">Total: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_r_a}}</td>
                               
                            </tr>
                            @endif
                            @if($nominatedData->status == 'withdrawn')

                            <tr>
                                
                                <td class="not_included">
                                3. </td>
                                <td class="not_included label">Withdrawn</td>
                                <td><span class="not_visible">Male: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_m}}</td>
                                <td><span class="not_visible">Female: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_f}}</td>
                                <td><span class="not_visible">Third Gender: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_o}}</td>
                                <td><span class="not_visible">Total: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_w_t}}</td>
                             
                            </tr>

                            @endif
                            @if($nominatedData->status == 'accepted')
                            <tr>
                                
                                <td class="not_included">
                                4. </td>
                                <td class="not_included label">Contested </td>
                                <td><span class="not_visible">Male: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_m}}</td>
                                <td><span class="not_visible">Female: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_f}}</td>
                                <td><span class="not_visible">Third Gender: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_o}}</td>
                                <td><span class="not_visible">Total: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_co_t}}</td>
                              
                            </tr>
                            @endif
                            @if($nominatedData->status == 'forfieted')
                            <tr>
                                
                                <td class="not_included">5. </td>
                                <td class="not_included label">Deposit Forfeited </td>
                                <td><span class="not_visible">Male: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_m}}</td>
                                <td><span class="not_visible">Female: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_f}}</td>
                                <td><span class="not_visible">Third Gender: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_o}}</td>
                                <td><span class="not_visible">Total: </span>{{$getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_t}}</td>
                              
                            </tr>
                            @endif
                            @endforeach                   
                        </table>


                        <table class="table table-bordered " id="electors" style="width: 100%;">
                            <tr class="head_nominated">    
                                <th>II</th>
                                <th>ELECTORS</th>
                                <th colspan="2" style="text-align: center;">GENERAL</th>
                                <th  rowspan="2">SERVICE</th>
                                <th rowspan="2">TOTAL</th>
                                <th></th>
                            </tr>


                            <tr class="head_nominated"> 
                                <th colspan="2"></th>
                                <th>Other than NRIs</th>
                                <th>NRIs</th>
                                <th></th>
                            </tr>


                            <tr>  
                                  
                                <td class="not_included">1</td>
                                <td>Male</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_m }}</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_nri_m }}</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_ser_m }}</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_all_t_m }}</td>
                                <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="Male">Edit Request</button></td>

                            </tr>


                            <tr>    
                                
                                <td>2</td>
                                <td>Female</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_f }}</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_nri_f }}</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_ser_f }}</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_all_t_f }}</td>
                                <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="Female">Edit Request</button></td>

                            </tr>




                            <tr>    
                                
                                <td>3</td>
                                <td>Third Gender (Not applicable to service electors)</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_o }}</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_nri_o }}</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_ser_o }}</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_all_t_o }}</td>
                                <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="Third Gender">Edit Request</button></td>

                            </tr>


                            <tr>   
                                 
                                <td>4</td>
                                <td>Total</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_gen_t }}</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_nri_t }}</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_ser_t }}</td>
                                <td>{{ $getIndexCardDataPCWise['t_pc_ic']->e_all_t }}</td>
                                <td></td>

                            </tr>

                        </table>




                        <table class="table table-bordered " id="voters" style="width: 100%;">
                            <tr>    
                                <th>III</th>
                                <th>VOTERS TURNED UP FOR VOTING</th>
                                <th colspan="2" style="text-align: center;">GENERAL</th>
                                <th colspan="2" style="text-align: center;">Total</th>
                                <th></th>
                            </tr>


                            <tr> 
                             <th colspan="2"></th>
                             <th>Other than NRIs</th>
                             <th>NRIs</th>
                             <th colspan="2" style="text-align: center;"></th>
                             <th></th>
                         </tr>


                         <tr>   
                             
                            <td>1</td>
                            <td>Male</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_m }}</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_m ? :0 }}</td>
                            <td colspan="2" style="text-align: center;">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_m +$getIndexCardDataPCWise['t_pc_ic']->vt_nri_m }}</td>
                            <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="Male">Edit Request</button></td>

                        </tr>


                        <tr>    
                            
                            <td>2</td>
                            <td>Female</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_f}}</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_f ? :0}}</td>
                            <td colspan="2" style="text-align: center;">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_f + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_f }}</td>

                            <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="Female">Edit Request</button></td>
                        </tr>




                        <tr> 
                               
                            <td>3</td>
                            <td>Third Gender</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_o}}</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_o ? :0}}</td>
                            <td colspan="2" style="text-align: center;">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_o + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_o }}</td>

                            <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="Third Gender">Edit Request</button></td>
                        </tr>


                        <tr> 
                               
                            <td>4</td>
                            <td>Total[Male+ Female+ Third Gender]</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_t}}</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->vt_nri_t }}</td>
                            <td colspan="2" style="text-align: center;">{{$getIndexCardDataPCWise['t_pc_ic']->vt_gen_t + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_t }}</td>
                            <td></td>

                        </tr>
                    </table>


                    <table class="table table-bordered " id="other_than" style="width: 100%;">

                        <tr>
                            <th>IV</th>
                            <th colspan="5">DETAILS OF VOTES POLLED ON EVM</th>
                            <th></th>
                        </tr>
                        <tr>
                            
                            <td>1</td>
                            <td colspan="4">Total votes polled on EVM</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->t_votes_evm}}</td>
                            <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="other">Edit Request</button></td>
                        </tr>
                        <tr>
                            
                            <td>2</td>
                            <td colspan="4">Test voted under Rule 49 MA</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->mock_poll_evm ? :0 }}</td>
                            <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="0" request-heading="other">Edit Request</button></td>
                        </tr>

                        <tr>
                            
                            <td>3</td>
                            <td colspan="4">Votes not retrieved From EVM</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->not_retrieved_vote_evm ? :0}}</td>
                            <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="0" request-heading="other">Edit Request</button></td>
                        </tr>

                        <tr>
                            
                            <td>4</td>
                            <td colspan="4">Rejected votes (due to other reasons)</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->r_votes_evm ?: 0}}</td>
                            <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="other">Edit Request</button></td>
                        </tr>


                        <tr>
                            
                            <td>5</td>
                            <td colspan="4">Votes polled for 'NOTA' on EVM</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota}}</td>
                            <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="1"  request-heading="other">Edit Request</button></td>
                        </tr>

                        <tr>
                            
                            <td>6</td>
                            <td colspan="4">Total of test votes + votes not retrieved + votes rejected (due to other reasons) + 'NOTA' [2+3+4+5]</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->v_r_evm_all}}</td>
                            <td></td>
                        </tr>


                        <tr>
                            
                            <td>7</td>
                            <td colspan="4">Total valid votes counted from EVM [1-6]</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->v_votes_evm_all}}</td>
                            <td></td>
                        </tr>



                        <tr>
                            <th>V</th>
                            <th colspan="5"> DETAILS OF POSTAL VOTES</th>
                            <th></th>
                        </tr>
                        <tr>
                            
                            <td>1</td>
                            <td colspan="4">Postal votes counted for service voters under sub-section (8) of Section 20 of R.P. Act, 1950</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_ser_u ? :0}}</td>
                            <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="other">Edit Request</button></td>
                        </tr>
                        <tr>
                            
                            <td>2</td>
                            <td colspan="4">Postal votes counted for Govt. servants on election duty (including all police personnel , drivers, conductors, cleaners).</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_ser_o ? :0}}</td>
                            <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="other">Edit Request</button></td>
                        </tr>
                        <tr>
                            
                            <td>3</td>
                            <td colspan="4">Postal votes rejected</td>
                            <td class="dev" colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_rejected ? :0}}</td>
                            <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="1"  request-heading="other">Edit Request</button></td>
                        </tr>



                        <tr>

                            <td>4</td>
                            <td colspan="4">Postal votes polled for 'NOTA'</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota}}</td>
                            <td><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="other">Edit Request</button></td>
                        </tr>


                        <tr>
                            <td>5</td>
                            <td colspan="4">Total of postal votes rejected + 'NOTA' [3+4]</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_vote_r_nota}}</td>
                            <td></td>
                        </tr>


                        <tr>
                            <td>6</td>
                            <td colspan="4">Total valid postal votes [1+2-5] </td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->postal_valid_votes}}</td>
                            <td></td>
                        </tr>





                        <tr>
                            <th>VI</th>
                            <th colspan="5">COMBINED DETAILS OF EVM & POSTAL VOTES</th>
                            <th></th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td colspan="4">Total votes polled [IV(1) + V(1+2)]</td>
                            <td class="dev">{{$getIndexCardDataPCWise['t_pc_ic']->total_votes_polled}}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td colspan="4">Total of test votes + votes not retrieved +votes rejected +'NOTA'[IV(6) + V(5)]</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->total_not_count_votes}}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td colspan="4">Total valid votes [IV(7) + V(6)]</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->total_valid_votes}}</td>
                            <td></td>
                        </tr>

                        <tr>
                            <td>4</td>
                            <td colspan="4">Total votes polled for 'NOTA' [IV(5) + V(4)]</td>
                            <td>{{$getIndexCardDataPCWise['t_pc_ic']->total_votes_nota}}</td>
                            <td></td>
                        </tr>







                        <tr>
                            <th>VII</th>
                            <th colspan="5">MISCELLANEOUS</th>
                            <th></th>
                        </tr>

                        <tr>
                            <td>1</td>
                            <td colspan="4">Proxy votes</td>
                            <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->proxy_votes ? :0}}</td>
                            <td><button class="btn btn-success btn-edit-request" request-type="1"  request-heading="other">Edit Request</button></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td colspan="4">Tendered votes</td>
                            <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->tendered_votes ? :0}}</td>
                            <td><button class="btn btn-success btn-edit-request" request-type="1"  request-heading="other">Edit Request</button></td>
                        </tr>

                        <tr>
                            <td>3</td>
                            <td colspan="4">Total number of polling stations set up in the Constituency</td>
                            <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->total_no_polling_station ? :0}}</td>
                            <td><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="other">Edit Request</button></td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td colspan="4">Average number of Electors per polling station in a Constituency</td>
                            <td colspan="1">{{$getIndexCardDataPCWise['t_pc_ic']->avg_elec_polling_stn}}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td colspan="4">Date(s) Of Poll</td>
                            <td colspan="1">{{date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->dt_poll))}}</td>
                            <td><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="other">Edit Request</button></td>
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
                          <td><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="other">Edit Request</button></td>
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
                        <td><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="other">Edit Request</button></td>
                    </tr>


                    <tr>
                        <td>8</td>
                        <td colspan="4">Date(s) Of counting</td>
                        <td colspan="1">{{date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->dt_counting))}}</td>
                        <td><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="other">Edit Request</button></td>
                    </tr>


                    <tr>
                        <td>9</td>
                        <td colspan="4">Date Of declaration Of result</td>
                        <td colspan="1">{{date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->dt_declare))}}</td>
                        <td><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="other">Edit Request</button></td>
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
                        <td><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="other">Edit Request</button></td>
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
                        <td><button class="btn btn-success btn-edit-request" request-type="0"  request-heading="other">Edit Request</button></td>
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
                    <table class="table table-bordered" id="candidates_list" style="width: 100%; text-align: center;">
                        <thead>
                            <tr class="not_included headers">
                                <th class="not_included">SL. No.</th>
                                <th >Name of Contesting Candidates <br>(in Block letters)</th>
                                <th >Sex <br> (Male/Female/Third Gender)</th>
                                <th >Age <br> (Years)</th>
                                <th >Category <br>(Gen/SC/ST)</th>
                                <th >Full Name of the Party</th>
                                <th >Election Symbol Allotted</th>


                                <th colspan="{{count($getIndexCardDataCandidatesVotesACWise['allACList'])}}">Valid Votes counted From Electronic Voting Machines </th>
                                <?php 
									if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){ ?>
									 <th>Migrant Votes</th>
								 <?php }
								 ?>

                             <th>Valid Postal Votes</th>
                             <th>Total Valid Votes</th>
                             <th></th>
                         </tr>

                         <tr color="acnamerow"  class="not_included sub_headers">
                            <th class="not_included"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            @foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue)
                            <th class="acs_headers">{{$allACListsKey}} : {{$allACListsValue}}</th>
                            @endforeach
                            <th></th>
                            <th></th>
                            <th></th>
                        
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
                            <td class="not_included">{{$count."."}} </td>
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
                        <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="1"  request-heading="{{$canddata['cand_name']}}">Edit Request</button></td>

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
                           <td class="not_included">{{$count}}</td>
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


                         <td class="not_included"><button class="btn btn-success btn-edit-request" request-type="1"  request-heading="Nota">Edit Request</button></td>

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
</div>
</div>
</div>
</section>



<!-- Modal for finalised Cheack -->
<div class="modal  fade change_request" id="change_request" tabindex="-1" role="dialog"  aria-hidden="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">

        <h4 class="modal-title">Please enter the new value</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-info" id="need_approval_form_button">Submit</button>
    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Modal for finalised -->

@endsection
@section('script')

<?php if(\Auth::user()->role_id == '18'){
    $post_url = url('/ropc/indexcard/post-complain-indexcard');
}else if(\Auth::user()->role_id == '4'){
    $post_url = url('/pcceo/indexcard/post-complain-indexcard');
}else{
    $post_url = url('/eci/indexcard/post-complain-indexcard');
} ?>

<script>
    $(document).ready(function(){
        $('a[href="#menu1"]').trigger('click');
    });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $(this).parent().attr('class','active show');
    });
    $('a[data-toggle="tab"]').on('hidden.bs.tab', function (e) {
        $(this).parent().removeAttr('class');
    });

    $(document).ready(function(e){
        
        $('#need_approval_form_button').click(function(e){
            $('#need_approval_form form').submit();
        });

        $('.btn-edit-request').click(function(e){
            var need_approval = $(this).attr('request-type');
            var sub_heading = $(this).attr('request-heading');
            var table_id = $(this).parents('table').attr('id');
            var html = "";
            html += "<div class='row' id='need_approval_form'>";
            html += "<form method='post' action='{!! $post_url; !!}'>";
            html += "<input type='hidden' name='_token' value='<?php echo csrf_token(); ?>'>";
            html += "<div class='col-lg-12'>";
            html += "<input type='hidden' name='need_approval' value='"+need_approval+"'>";
            html += "<input type='hidden' name='sub_heading' value='"+sub_heading+"'>";
            html += "<input type='hidden' name='st_code' value='<?php echo $st_code; ?>'>";
            html += "<input type='hidden' name='pc_no' value='<?php echo $pc_no; ?>'>";
            if($(this).attr('request-type') == '1'){
                html += "<div class='alert alert-warning'>Updation of this form requires approval of section.</div>";
                need_approval = 1;
            }
            html    += "<table class='table table-bordered'>";
            html    += "<tr>";
            if(table_id == 'nomination'){
                html    += "<td>Name</td><td>All Status</td><td>Current Status</td><td>Update Status</td><td>Comment</td>";
            }else{
                html    += "<td>Name</td><td>Current Value</td><td>Update Value</td><td>Comment</td>";
            }
            html    += "</tr>";
            if(table_id == 'nomination'){
                html += "<input type='hidden' name='type' value='nomination'>";
                var object = $(this).parent('td').parent('tr');  
                var label = $(object).find('td:nth-child(2)').text();
                $('#change_request .modal-title').text(label);
                var key = 0;
                var cadidates_list = <?php echo $cadidates_list; ?>;

                $(cadidates_list).each(function(index1,object1){
                    
                    var clone_count     = object1.final_status;
                    var label_for_form  = object1.name;
                    html += "<tr>";
                    html += "<td><input type='text' readonly='readonly' name='complain["+key+"][label]' class='form-control' value='"+label_for_form+"'></td>";
                    html += "<td>"+object1.status+"</td>";
                    html += "<td><input type='text' readonly='readonly' name='complain["+key+"][old_value]' class='form-control' value='"+clone_count+"'></td>";
                    html += "<td><input type='text' name='complain["+key+"][new_value]' value='' class='form-control'></td>";
                    html += "<td><input type='text' name='complain["+key+"][comment]' value='' class='form-control'></td>";
                    html += "</tr>";
                    key++;
                        
                });
                
            }else if(table_id == 'electors'){
                
                var object = $(this).parent('td').parent('tr');  
                var label = $(object).find('td:nth-child(2)').text();
                $('#change_request .modal-title').text("Electors");
                html += "<input type='hidden' name='type' value='electors'>";
                var key = 0;
                    $(object).find('td').each(function(index1,object1){
                        if($.inArray(index1,[2,3,4,5]) != -1){
                            var clone_value = $(object1).clone();
                            var clone_count = clone_value.children().remove().end().text();
                            var label_for_form = '';
                            if(index1==2){
                                label_for_form = 'General Other than NRIs '+$(object1).find(".not_visible").text();
                            }
                            if(index1==3){
                                label_for_form = 'General NRIs '+$(object1).find(".not_visible").text();
                            }
                            if(index1==4){
                                label_for_form = 'Service '+$(object1).find(".not_visible").text();
                            }
                            if(index1==5){
                                label_for_form = 'Total '+$(object1).find(".not_visible").text();
                            }
                            html += "<tr>";
                            html += "<td><input type='text' readonly='readonly' name='complain["+key+"][label]' class='form-control' value='"+label_for_form+"'></td>";
                            html += "<td><input type='text' readonly='readonly' name='complain["+key+"][old_value]' class='form-control' value='"+clone_count+"'></td>";
                            html += "<td><input type='text' name='complain["+key+"][new_value]' value='' class='form-control'></td>";
                            html += "<td><input type='text' name='complain["+key+"][comment]' value='' class='form-control'></td>";
                            html += "</tr>";
                            key++;
                        }
                    });
                
            }else if(table_id == 'voters'){
                
                    var object = $(this).parent('td').parent('tr');
                    var label = $(object).find('td:nth-child(2)').text();
                    $('#change_request .modal-title').text("VOTERS");
                    html += "<input type='hidden' name='type' value='voters'>";
                    var key = 0;
                    $(object).find('td').each(function(index1,object1){
                        if($.inArray(index1,[2,3,4]) != -1){
                            var clone_value = $(object1).clone();
                            var clone_count = clone_value.children().remove().end().text();
                            var label_for_form = '';
                            if(index1==2){
                                label_for_form = 'General Other than NRIs '+$(object1).find(".not_visible").text();
                            }
                            if(index1==3){
                                label_for_form = 'General NRIs '+$(object1).find(".not_visible").text();
                            }
                            if(index1==4){
                                label_for_form = 'Total '+$(object1).find(".not_visible").text();
                            }
                            html += "<tr>";
                            html += "<td><input type='text' readonly='readonly' name='complain["+key+"][label]' class='form-control' value='"+label_for_form+"'></td>";
                            html += "<td><input type='text' readonly='readonly' name='complain["+key+"][old_value]' class='form-control' value='"+clone_count+"'></td>";
                            html += "<td><input type='text' name='complain["+key+"][new_value]' value='' class='form-control'></td>";
                            html += "<td><input type='text' name='complain["+key+"][comment]' value='' class='form-control'></td>";
                            html += "</tr>";
                            key++;
                        }
                    });
                
            }else if(table_id == 'other_than'){
                var key = 0;
                var object = $(this).parent('td').parent('tr');  
                var label = $(object).find('td:nth-child(2)').text();
                html += "<input type='hidden' name='type' value='other'>";
                $('#change_request .modal-title').text("Update Request");
                html += "<tr>";
                html += "<td><input type='text' readonly='readonly' name='complain["+key+"][label]' class='form-control' value='"+$(object).find('td:nth-child(2)').text()+"'></td>";
                html += "<td><input type='text' readonly='readonly' name='complain["+key+"][old_value]' class='form-control' value='"+$(object).find('td:nth-child(3)').text()+"'></td>";
                html += "<td><input type='text' name='complain["+key+"][new_value]' value='' class='form-control'></td>";
                html += "<td><input type='text' name='complain["+key+"][comment]' value='' class='form-control'></td>";
                html += "</tr>";

            }else if(table_id == 'candidates_list'){

                var object = $(this).parent('td').parent('tr');
                var label = $(object).find('td:nth-child(2)').text();
                $('#change_request .modal-title').text("VOTERS");
                html += "<input type='hidden' name='type' value='Information About Candidate in PC'>";
                var length_sub_headers  = $('#candidates_list .acs_headers').length;
                var length_header       = $('#candidates_list .headers th').length;
                var key                 = 0;
                var i = 1;

                $(object).find('td').each(function(index1,object1){
                    if(!$(object1).hasClass("not_included")){
                        
                        if(index1 > 6 && index1 <= length_sub_headers+6){
                            label_for_form = $('#candidates_list .sub_headers').find('th:nth-child('+(index1+1)+')').text();
                        }else{

                            if(index1>6){
                                table_head_index = index1+1-length_sub_headers;
                            }else{
                                table_head_index = index1+1;  
                            }
                            label_for_form = $('#candidates_list .headers').find('th:nth-child('+table_head_index+')').text();
                        }

                        html += "<tr>";
                        html += "<td><input type='text' readonly='readonly' name='complain["+key+"][label]' class='form-control' value='"+label_for_form+"'></td>";
                        html += "<td><input type='text' readonly='readonly' name='complain["+key+"][old_value]' class='form-control' value='"+$(object1).text()+"'></td>";
                        html += "<td><input type='text' name='complain["+key+"][new_value]' value='' class='form-control'></td>";
                        html += "<td><input type='text' name='complain["+key+"][comment]' value='' class='form-control'></td>";
                        html += "</tr>";
                        key++;
                    }
                });

            }
            html += "</table>";
            html += "</div>";
            html += "</form>";
            html += "</div>";
            html += "";
            $('#change_request .modal-body').html(html);
            $('#change_request').modal('show');
        });
    });


function filter(){
  var url = "<?php echo $current_page ?>";
  var query = '';
  query += "&complain=1";
    if($("#pc_no").val() != ''){
      query += '&pc_no='+$("#pc_no").val();
    }
    if($("#st_code").val() != ''){
      query += '&st_code='+$("#st_code").val();
    }
    window.location.href = url+'?'+query.substring(1);
}
</script>
@endsection